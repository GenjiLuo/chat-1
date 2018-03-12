<?php
namespace server\ws\action;
use common\model\MessageModel;
use common\model\UserModel;
use App;
use Swoole\Mysql;

/**
 * Class Message
 * @package server\ws\action
 * 接受消息处理函数
 */
class Message extends Action{
    /**
     * @return mixed|void
     */
    public function handle()
    {
        $data = json_decode($this->frame->data, true);
        $redis = $this->server->redis;
        $messageModel = new MessageModel($this->server->db);
        switch ($data['type']){
            case 'msg':
                $to = $data['to'];
                $message = [
                    "from" => $data['from'],
                    "to" => $data['to'],
                    "avatar" => $data['avatar'],
                    "msg" => $data['msg'],
                    "time" => $data['time'],
                ];
                $messageModel->add($message);
                if ($redis->sIsMember("onlineList", $to)) {
                    $data['owner'] = false;
                    $toFd = $redis->hGet("userId:userFd",$to);
                    $this->pushMessage($toFd, $data);
                } else {
                    //todo 不在线
                }
                break;
            case  'userList':
                $userModel = new UserModel($this->server->db);
                $userList = $userModel->findAll(['username[~]'=>'%'.$data['search'].'%']);
                $this->pushUserList($this->frame->fd,['userList'=>$userList]);
        }
    }

}