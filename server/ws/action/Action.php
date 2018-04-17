<?php

namespace server\ws\action;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

abstract class Action
{
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
    /**
     * @var int
     */
    public $taskId;
    /**
     * @var int
     */
    public $workerId;
    /**
     * @var array
     * taskHandle/channel(message) 参数
     */
    public $data;

    const TYPE_CHAT_LIST = 'chatList';
    const TYPE_GO_ONLINE = 'goOnline';
    const TYPE_GO_OFFLINE ='goOffLine';
    const TYPE_MESSAGE = 'msg';
    const TYPE_NEW_APPLY = 'newApply';
    const TYPE_AGREE_SUCC = 'agreeSucc';
    const TYPE_NEW_GROUP ='newGroup';
    const TYPE_REPEAT_CONNECT = 'repeatConnect';

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->server->$name;
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        call_user_func_array([$this->server,$name],$arguments);
    }

    /**
     * Action constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        if (isset($params['server'])) {
            $this->server = $params['server'];
        }
        if (isset($params['frame'])) {
            $this->frame = $params['frame'];
        }
        if (isset($params['fd'])) {
            $this->fd = $params['fd'];
        }
        if (isset($params['response'])) {
            $this->response = $params['response'];
        }
        if (isset($params['reactorId'])) {
            $this->reactorId = $params['reactorId'];
        }
        if (isset($params['request'])) {
            $this->request = $params['request'];
        }
        if (isset($params['taskId'])) {
            $this->taskId = $params['taskId'];
        }
        if (isset($params['workerId'])) {
            $this->workerId = $params['workerId'];
        }
        if (isset($params['data'])) {
            $this->data = $params['data'];
        }
    }

    /**
     * @param int $fd
     * @param array $data
     * @param string $type
     */
    public function push(int $fd, array $data, string $type)
    {
        if ($this->server->exist($fd)) {
            $data['type'] = $type;
            $this->server->push($fd, json_encode($data, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * @param int $fd
     * @param array $data
     * 用户发送消息
     */
    public function pushMessage(int $fd, array $data)
    {
        $this->push($fd, $data, self::TYPE_MESSAGE);
    }

    /**
     * @param int $fd
     * @param array $chatList
     * 发送用户聊天列表
     */
    public function pushChatList(int $fd, array $chatList)
    {
        $this->push($fd, $chatList, self::TYPE_CHAT_LIST);
    }


    /**
     * @param int $fd
     */
    public function pushNewApply(int $fd)
    {
        $this->push($fd, [], self::TYPE_NEW_APPLY);
    }

    /**
     * @param int $fd
     * @param array $data
     * 同意好友申请成功
     */
    public function pushAgreeSucc(int $fd,array $data){
        $this->push($fd,$data,self::TYPE_AGREE_SUCC);
    }

    /**
     * @param int $fd
     * @param array $data
     * 同意好友申请成功
     */
    public function pushNewGroup(int $fd,array $data){
        $this->push($fd,$data,self::TYPE_NEW_GROUP);
    }


    public function pushRepectConnect(int $fd){
        $this->push($fd,[],self::TYPE_REPEAT_CONNECT);
    }

    /**
     * @param array $data
     * @param string $type task任务类型
     * @param int $taskId
     * 投递task任务
     */
    public function pushTask(array $data, string $type, $taskId = -1)
    {
        $data['type'] = $type;
        $this->server->task($data, $taskId);
    }

    /**
     * @return mixed
     */
    abstract function handle();

    /**
     * @return bool
     */
    public function beforeAction()
    {
        return true;
    }

    public function afterAction()
    {
        //todo
    }
}