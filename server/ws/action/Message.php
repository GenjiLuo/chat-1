<?php
namespace server\ws\action;
use common\model\MessageModel;
use common\model\UserModel;
use App;
use Swoole\Mysql;
class Message extends Action{
    /**
     * @return mixed|void
     */
    public function handle()
    {
        $data = json_decode($this->frame->data, true);
        $redis = $this->server->redis;
        $messageModel = new MessageModel($this->server->db);
        if ($data['type'] === 'msg') {
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
        }
        if ($data['type'] === 'userList') {
            $userList = $messageModel->find(['nickname[~]'=>'%'.$data['search'].'%']);
            $this->push($this->frame->fd,['users'=>$userList],self::TYPE_FRIEND_LIST);
        }
    }

}