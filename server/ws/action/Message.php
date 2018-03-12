<?php
namespace server\ws\action;
use common\model\FriendApplyModel;
use common\model\MessageModel;
use common\model\UserFriendModel;
use common\model\UserModel;


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

        switch ($data['type']){
            case 'msg':
                $this->msg($data);
                break;
            case  'userList':
                $this->useList($data);
                break;
            case 'addFriend':
                $this->addFriend($data);
                break;
            case 'friendList':
                $this->friendList($data);
                break;
        }
    }

    /**
     * @param $data
     * 发消息
     */
    private function msg($data){
        $redis = $this->server->redis;
        $messageModel = new MessageModel($this->server->db);
        $to = $data['to'];
        $message = [
            "from" => $data['from'],
            "to" => $data['to'],
            "avatar" => $data['avatar'],
            "msg" => $data['msg'],
            "time" => $data['time'],
        ];
        $messageModel->add($message);
        if ($redis->sIsMember("onlineList", $to)) { //目标用户在线
            $data['owner'] = false;
            $toFd = $redis->hGet("userId:userFd",$to);
            $this->pushMessage($toFd, $data);
        }
    }

    /**
     * @param $data
     * 获取用户列表
     */
    private function useList($data){
        $userModel = new UserModel($this->server->db);
        $userId = $this->server->redis->hGet("userFd:userId",$this->frame->fd);
        $userList = $userModel->findAll(
            [
                'username[~]'=>'%'.$data['search'].'%',
                'id[!]'=>$userId,
                'LIMIT'=>20
            ]
        );
        $friendModel = new UserFriendModel($this->server->db);
        $applyModel = new FriendApplyModel($this->server->db);
        foreach ($userList as $key => &$user) {
            //检查是否已经是朋友
            if($friendModel->isFriend($userId,$user['id'])){
                $user['can_apply'] =false;
                continue;
            }
            //检查是否申请中
            if($applyModel->isApplying($userId,$user['id'])){
                $user['can_apply'] =false;
            }
        }
        $this->pushUserList($this->frame->fd,['userList'=>$userList]);
    }

    /**
     * @param $data
     * 申请添加好友
     */
    private function addFriend($data){
        $sponsorId = $this->server->redis->hGet("userFd:userId",$this->frame->fd);
        $targetId = $data['targetId'];
        $model = new FriendApplyModel($this->server->db);
        $model->add($sponsorId,$targetId);
    }

    /**
     * @param $data
     */
    private function friendList($data){
        $userId = $this->server->redis->hGet("userFd:userId",$this->frame->fd);
        $model = new UserFriendModel($this->server->db);
        $friendList = $model->find($userId);
        $this->pushFriendList($this->frame->fd,["friendList"=>$friendList]);
    }

}