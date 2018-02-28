<?php

namespace server\ws;

use common\interfaces\ServerInterface;
use Swoole\Http\Request;
use Swoole\WebSocket\Server;
use App;

class WsServer implements ServerInterface
{
    public function run()
    {
        $server = new Server(SERVER_HOST, WS_SERVER_PORT);

        $server->on("open", function (Server $server,Request $request) {
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

        App::notice("webSocket now is running on 0.0.0.0:" . WS_SERVER_PORT);

        $server->start();
    }


}