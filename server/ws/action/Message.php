<?php
namespace server\ws\action;
use common\model\MessageModel;
use common\model\UserModel;
use App;
class Message extends Action{

    public function handle()
    {
        $data = json_decode($this->frame->data, true);
        $redis = App::$DI->redis;
        if ($data['type'] === 'msg') {
            $to = $data['to'];
            $message = [
                "from" => $data['from'],
                "to" => $data['to'],
                "avatar" => $data['avatar'],
                "msg" => $data['msg'],
                "time" => $data['time'],
            ];
            MessageModel::add($message);
            if ($redis->sIsMember("onlineList", $to)) {
                $data['owner'] = false;
                $toFd = $redis->hGet("userId:userFd",$to);
                $this->server->push($toFd, json_encode($data));
            } else {
                //todo 不在线
            }
        }
        if ($data['type'] === 'userList') {
            $userList = UserModel::findAll(['nickname[~]'=>'%'.$data['search'].'%']);
            $this->server->push($this->frame->fd,json_encode(['type'=>'userList','users'=>$userList]));
        }
    }

}