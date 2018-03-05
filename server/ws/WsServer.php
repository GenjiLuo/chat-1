<?php

namespace server\ws;

use common\interfaces\ServerInterface;
use server\ws\action\Action;
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
        $server = new Server(SERVER_HOST, WS_SERVER_PORT);

        $configFile = BASE_ROOT . "/server/ws/config/server.php";
        if (is_file($configFile)) {
            $config = require BASE_ROOT . "/server/ws/config/server.php";
        } else {
            throw new FileNotExistException("server config file");
        }

        $server->set($config);
        // 连接建立回调函数
        $server->on("open", function (Server $server, Request $request) {
            App::$DI->router->dispatch(['server' => $server, "request" => $request], "open");
        });
        // 接受消息回调函数
        $server->on("message", function (Server $server, $frame) {
            App::$DI->router->dispatch(['server' => $server, "frame" => $frame], "message");
        });
        // 接受请求回调函数
        $server->on("request", function (Server $server, $response) {
//            App::$DI->router->dispatch(['server' => $server, "response" => $response], "request");
        });
        // 连接关闭回调函数
        $server->on("close", function (Server $server, $fd, $reactorId) {
            App::$DI->router->dispatch(['server' => $server, "fd" => $fd, 'reactorId' => $reactorId], "close");
        });

        // 投递task 回调函数
        $server->on("task", function (Server $server, int $taskId, int $workerId, $data) {
            App::$DI->router->dispatch(['server'=>$server,'taskId'=>$taskId,'workerId'=>$workerId,'data'=>$data],'task');
        });
        // task任务完成回调
        $server->on("finish", function (Server $server, int $taskId, string $data) {
            echo $data;
        });
        App::notice("webSocket now is running on " . SERVER_HOST . ":" . WS_SERVER_PORT);
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