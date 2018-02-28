<?php
namespace server\ws\action;
use Swoole\Http\Request;
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
    /**
     * @var Request
     */
    public $request;

    public $response;
    public $reactorId;


    const TYPE_FRIEND_LIST = "friendList";
    const TYPE_GO_ONLINE = "goOnline";


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
        if(isset($params['request'])){
            $this->request = $params['request'];
        }
    }

    public function push(int $fd,array $data,string $type){
        $data['type'] = $type;
        $this->server->push($fd,json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    abstract function handle();

}