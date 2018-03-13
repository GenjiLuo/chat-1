<?php
namespace server\ws\action;
use common\model\ChatModel;
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
        $type = $data['type'];
        $this->$type($data);
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
    private function userList($data){
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
                $user['can_apply'] = false;
                continue;
            }
            //检查是否申请中
            if($applyModel->isApplying($userId,$user['id'])){
                $user['can_apply'] = false;
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
     * 好友列表
     */
    private function friendList($data){
        $userId = $this->server->redis->hGet("userFd:userId",$this->frame->fd);
        $model = new UserFriendModel($this->server->db);
        $friendList = $model->find($userId);
        $this->pushFriendList($this->frame->fd,["friendList"=>$friendList]);
    }

    /**
     *  好友申请标记已读
     */
    private function applyRead($data){
        $userId = $this->server->redis->hGet("userFd:userId",$this->frame->fd);
        $model = new FriendApplyModel($this->server->db);
        $model->handleRead($userId);
    }

    /**
     * @param $data
     * 拒绝好友申请
     */
    private function reject($data){
        $applyId = $data['applyId'];
        $model = new FriendApplyModel($this->server->db);
        $model->reject($applyId);
    }

    /**
     * @param $data
     * @throws \Exception
     * 同意好友申请
     */
    private function agree($data){
        $applyId = $data['applyId'];
        $model = new FriendApplyModel($this->server->db);
        if($apply = $model->agree($applyId)){
            $userModel = new UserModel($this->server->db);
            $target = $userModel->findOne(['id'=>$apply['target_id']]);
            $sponsor = $userModel->findOne(['id'=>$apply['sponsor_id']]);
            $this->pushAgreeSucc($this->frame->fd,['friend'=>$sponsor,'applyId'=>$applyId]);
            $redis = $this->server->redis;
            if($redis->sIsMember("onlineList", $sponsor['id'])){ //如果申请人在线
                $sponsorFd = $redis->hGet('userId:userF',$sponsor['id']);
                $this->pushApplySucc($sponsorFd,['friend'=>$target,'applyId'=>$applyId]);
            }
        }
    }

    /**
     * @param $data
     * 创建普通聊天
     */
    private function createChat($data){
        $targetId = $data['targetId'];
        $userId = $this->server->redis->hGet("userFd:userId",$this->frame->fd);
        $chatModel = new ChatModel($this->server->db);
        $chatModel->add($userId,$targetId);
    }

}