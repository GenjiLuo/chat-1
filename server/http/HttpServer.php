<?php

namespace server\http;

use common\lib\exception\FileNotExistException;
use core\App;
use core\interfaces\ServerInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class HttpServer implements ServerInterface
{
    public $config;

    /**
     * @return mixed|void
     * @throws FileNotExistException
     */
    public function run()
    {
        cli_set_process_title("SHTTP");
        $server = new Server(SERVER_HOST, HTTP_SERVER_PORT);
        $configFile = BASE_ROOT . "/server/http/config/server.php";
        if (is_file($configFile)) {
            $this->config = require BASE_ROOT . "/server/http/config/server.php";
        } else {
            throw new FileNotExistException("server config file");
        }
        $server->set($this->config);
        $server->on('request', function (Request $request, Response $response) {
            // swoole不支持set_exception_handler,set_error_handler
            try {
                App::$comp->router->dispatch($request, $response);
            } catch (\Exception $e) {
                $response->status($e->getCode());
                if (DEBUG) {
                    $response->end($e->getMessage());
                } else {
                    $response->end();
                }
            }
        });
        $server->start();
    }
}