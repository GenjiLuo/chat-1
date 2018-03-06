<?php

namespace server\http;

use common\lib\exception\FileNotExistException;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use common\interfaces\ServerInterface;
use App;

class HttpServer implements ServerInterface
{
    public $config;

    /**
     * @return mixed|void
     * @throws FileNotExistException
     */
    public function run()
    {
        cli_set_process_title("swoole http server");
        $server = new Server(SERVER_HOST, HTTP_SERVER_PORT);
        $configFile = BASE_ROOT . "/server/http/config/server.php";
        if (is_file($configFile)) {
            $this->config = require BASE_ROOT . "/server/http/config/server.php";
        } else {
            throw new FileNotExistException("server config file");
        }

        $server->set($this->config);
        $server->on('request', function (Request $request, Response $response) {
            try {
                App::$router->dispatch($request, $response);
            } catch (\Exception $e) {
                $response->status($e->getCode());
                if (DEBUG) {
                    $response->end($e->getMessage());
                } else {
                    $response->end();
                }
            }
        });
        App::notice("HttpServer now is running on 127.0.0.1:" . HTTP_SERVER_PORT);
        $server->start();
    }

    /**
     * @param null $key
     * @return mixed
     * 获取服务器配置参数
     */
    public function get($key = null)
    {
        if($key===null){
            return $this->config;
        }
        return isset($this->config[$key])?$this->config[$key]:"";
    }

}