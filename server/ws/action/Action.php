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

    const TYPE_FRIEND_LIST = "friendList";
    const TYPE_USER_LIST = 'userList';
    const TYPE_CHAT_LIST = 'chatList';
    const TYPE_GO_ONLINE = "goOnline";
    const TYPE_MESSAGE = "msg";
    const TYPE_APPLY_LIST = "applyList";
    const TYPE_APPLY_SUCC = 'applySucc';
    const TYPE_AGREE_SUCC = 'agreeSucc';
    const TYPE_NEW_GROUP ='newGroup';

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
     * @param array $userList
     * 发送用户列表
     */
    public function pushUserList(int $fd, array $userList)
    {
        $this->push($fd, $userList, self::TYPE_USER_LIST);
    }


    /**
     * @param int $fd
     * @param array $applyList
     * 发送好友申请列表
     */
    public function pushApplyList(int $fd, array $applyList)
    {
        $this->push($fd, $applyList, self::TYPE_APPLY_LIST);
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