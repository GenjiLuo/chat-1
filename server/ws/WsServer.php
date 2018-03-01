<?php

namespace server\ws;

use common\interfaces\ServerInterface;
use Swoole\Http\Request;
use Swoole\WebSocket\Server;
use App;
use common\lib\exception\FileNotExistException;
class WsServer implements ServerInterface
{
    /**
     * @return mixed|void
     * @throws FileNotExistException
     */
    public function run()
    {
        $server = new Server(SERVER_HOST, WS_SERVER_PORT);

        $configFile = BASE_ROOT . "/server/http/config/server.php";
        if(is_file($configFile)){
            $config = require BASE_ROOT . "/server/ws/config/server.php";
        }else {
            throw new FileNotExistException("server config file");
        }
        $server->set($config);

        $server->on("open", function (Server $server,Request $request) {
            echo "open";
            App::$DI->router->dispatch(['server' => $server, "request" => $request], "open");
        });

        $server->on("message", function (Server $server, $frame) {
            App::$DI->router->dispatch(['server' => $server, "frame" => $frame], "message");
        });

        $server->on("request", function (Server $server, $response) {
//            App::$DI->router->dispatch(['server' => $server, "response" => $response], "request");
        });

        $server->on("close", function (Server $server, $fd, $reactorId) {
            App::$DI->router->dispatch(['server' => $server, "fd" => $fd, 'reactorId' => $reactorId], "close");
        });


        $server->on("task",function(){

        });
        $server->on("finish",function (Server $server, int $taskId, string $data){
            echo "task finish";
        });

        App::notice("webSocket now is running on ".SERVER_HOST.":" . WS_SERVER_PORT);
        $server->start();
    }


}