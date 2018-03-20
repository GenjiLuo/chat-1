<?php

namespace server\ws;

use common\model\FriendApplyModel;
use common\model\UserModel;
use core\interfaces\ServerInterface;
use common\lib\MyRedis;
use Medoo\Medoo;
use Swoole\Http\Request;
use Swoole\Redis;
use Swoole\WebSocket\Server;
use core\App;
use common\lib\exception\FileNotExistException;

class WsServer implements ServerInterface
{
    public $config;

    /**
     * @return mixed|void
     * @throws FileNotExistException
     */
    public function run()
    {
        cli_set_process_title("WebSocket");
        $server = new Server(SERVER_HOST, WS_SERVER_PORT);
        $configFile = BASE_ROOT . "/server/ws/config/server.php";
        if (is_file($configFile)) {
            $this->config = require BASE_ROOT . "/server/ws/config/server.php";
        } else {
            throw new FileNotExistException("server config file");
        }
        $server->set($this->config);
        // 连接建立回调函数
        $server->on("open", function (Server $server, Request $request) {
            App::$comp->router->dispatch(['server' => $server, "request" => $request], "open");
        });
        // 接受消息回调函数
        $server->on("message", function (Server $server, $frame) {
            App::$comp->router->dispatch(['server' => $server, "frame" => $frame], "message");
        });
        // http接受请求回调函数
        $server->on("request", function (Server $server, $response) {
//           App::$comp->router->dispatch(['server' => $server, "response" => $response], "request");
        });
        // 连接关闭回调函数
        $server->on("close", function (Server $server, $fd, $reactorId) {
            App::$comp->router->dispatch(['server' => $server, "fd" => $fd, 'reactorId' => $reactorId], "close");
        });
        // 投递task回调函数
        $server->on("task", function (Server $server, int $taskId, int $workerId, $data) {
            App::$comp->router->dispatch(['server' => $server, 'taskId' => $taskId, 'workerId' => $workerId, 'data' => $data], 'task');

        });
        // task任务完成回调
        $server->on("finish", function (Server $server, int $taskId, string $data) {

        });
        $server->on("managerStart", function (Server $server) {
        });
        // worker start 回调
        $server->on("WorkerStart", function (Server $server, int $workId) {
            // 每个worker各自拥有自己的redis/mysql 连接,在action中通过$this->server->db/redis调用
            $server->redis = App::createObject(MyRedis::class);
            $server->db = App::createObject(Medoo::class);
            // 设置用户重复登陆时自动断开发送消息通知，每个worker都有自己独立的定时器
            // 所以设置有多少个worker就会生成多少个定时器并发执行
            // 只有worker才设置定时器，taskWorker不设置
            if (!$server->taskworker) {
                if ($workId == 0) { // 异步redis 订阅频道，消费http产生的内容
                    $redis = new Redis();
                    $redis->on("message", function (Redis $redis, $message) use ($server) {
                        if ($message[0]=='message' && $message[1] == 'applyCH') { // 好友申请
                            //如果申请目标在线,推送全新的好友申请列表
                            $applyId = $message[2];
                            $applyModel = new FriendApplyModel($server->db);
                            $apply = $applyModel->selectOne(['id' => $applyId]);
                            if ($server->redis->sIsMember("onlineList", $apply['target_id'])) {
                                $targetFd = $server->redis->hGet('userId:userFd', $apply['target_id']);
                                $applyList = $applyModel->findWithUser(['target_id' => $apply['target_id']]);
                                $server->push($targetFd, json_encode(['applyList' => $applyList,'type'=>'applyList']));
                            }
                        }
                        if ($message[0]=='message' && $message[1] == 'agreeCH') { // 同意好友申请
                            //如果申请人在线,推送好友申请被同意消息
                            $applyId = $message[2];
                            $applyModel = new FriendApplyModel($server->db);
                            $apply = $applyModel->selectOne(['id' => $applyId]);
                            if ($server->redis->sIsMember("onlineList", $apply['sponsor_id'])) {
                                $sponsorFd = $server->redis->hGet('userId:userFd',  $apply['sponsor_id']);
                                $friend = (new UserModel($server->db))->findOne(['id'=>$apply['target_id']]);
                                $server->push($sponsorFd, json_encode(['friend' => $friend,'type'=>'applySucc']));
                            }
                        }
                        if ($message[0]=='message' && $message[1] == 'closeFD') { //重复连接关闭已连接
                            $closeFd = $message[2];
                            if ($server->exist($closeFd)) {
                                //此处有可能消息没发送就关闭了连接
                                //todo
                                $server->push($closeFd, json_encode(['type' => 'repeat']));
                                $server->close($closeFd);
                            }
                        }
                    });
                    $redis->connect(REDIS_HOST, REDIS_PORT, function (Redis $redis, $result) {
                        if ($result) {
                            $redis->subscribe("applyCH");
                            $redis->subscribe("agreeCH");
                            $redis->subscribe("closeFD");
                        }
                    });
                }
            }
        });
        $server->start();
    }
}
