<?php
namespace server\ws\action;
use Swoole\WebSocket\Server;

abstract class Action{
    /**
     * @var Server
     */
    public $server;
    /**
     * @var array
     */
    public $frame;
    /**
     * @var int
     */
    public $fd;

    public $response;
    public $reactorId;

    public function __construct(array $params)
    {
        if(isset($params['server'])){
            $this->server = $params['server'];
        }
        if(isset($params['frame'])){
            $this->frame = $params['frame'];
        }
        if(isset($params['fd'])){
            $this->fd = $params['fd'];
        }
        if(isset($params['response'])){
            $this->response = $params['response'];
        }
        if(isset($params['reactorId'])){
            $this->reactorId = $params['reactorId'];
        }
    }

    abstract function handle();

}