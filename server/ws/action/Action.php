<?php
namespace server\ws\action;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

abstract class Action{
    /**
     * @var Server
     */
    public $server;
    /**
     * @var Frame
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
    /**
     * @var Response
     */
    public $response;
    /**
     * @var int
     */
    public $reactorId;


    const TYPE_FRIEND_LIST = "friendList";
    const TYPE_GO_ONLINE = "goOnline";
    const TYPE_MESSAGE ="msg";

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

    /**
     * @param int $fd
     * @param array $data
     * @param string $type
     */
    public function push(int $fd,array $data,string $type){
        $data['type'] = $type;
        $this->server->push($fd,json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    public function pushMessage(int $fd,array $data){
        $this->push($fd,$data,self::TYPE_MESSAGE);
    }
    abstract function handle();

}