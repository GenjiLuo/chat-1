<?php

namespace server\ws;

use common\interfaces\ServerInterface;
use Swoole\Http\Request;
use Swoole\WebSocket\Server;
use App;
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
        cli_set_process_title("swoole websocket server");
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

            App::$router->dispatch(['server' => $server, "request" => $request], "open");
        });
        // 接受消息回调函数
        $server->on("message", function (Server $server, $frame) {
            App::$router->dispatch(['server' => $server, "frame" => $frame], "message");
        });
        // 接受请求回调函数
        $server->on("request", function (Server $server, $response) {
//            App::$router->dispatch(['server' => $server, "response" => $response], "request");
        });
        // 连接关闭回调函数
        $server->on("close", function (Server $server, $fd, $reactorId) {
            App::$router->dispatch(['server' => $server, "fd" => $fd, 'reactorId' => $reactorId], "close");
        });
        // 投递task回调函数
        $server->on("task", function (Server $server, int $taskId, int $workerId, $data) {
            App::$router->dispatch(['server'=>$server,'taskId'=>$taskId,'workerId'=>$workerId,'data'=>$data],'task');


        });
        // task任务完成回调
        $server->on("finish", function (Server $server, int $taskId, string $data) {
            echo $data;
        });
        // worker start 回调
        $server->on("WorkerStart",function (Server $server,int $workId){

            // 不同的进程不能共用同一个redis/mysql连接，否则数据会发现错乱
            $server->redis = new \Redis();
            $server->redis->connect(REDIS_HOST,REDIS_PORT);
            // 设置用户重复登陆时自动断开发送消息通知，每个work都有自己独立的定时器
            // 所以设置有多少个worker就会生成多少个定时器
           $server->tick(500,function () use ($server){
              $closeFd  = $server->redis->rPop("closeQueue");

              if($closeFd && $server->exist($closeFd)){
                  $server->push($closeFd,json_encode(['type'=>'repeat']));
                  $server->close($closeFd);
              }
           });
        });
        $server->start();
    }

    /**
     * @param null $key
     * @return mixed
     * 获取服务器配置参数
     */
    public function get($key = null)
    {
        if ($key === null) {
            return $this->config;
        }
        return isset($this->config[$key]) ? $this->config[$key] : "";
    }


}