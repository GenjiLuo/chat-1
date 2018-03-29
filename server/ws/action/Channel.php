<?php
namespace server\ws\action;
use common\model\ChatModel;
use common\model\FriendApplyModel;
use common\model\GroupUserModel;
use common\model\UserModel;
use common\model\GroupModel;

/**
 * Class Channel
 * @package server\ws\action
 * redis 订阅处理类
 */
class Channel extends Action{
    
    public function handle()
    {
        if($this->data[0] === "message"){
            $method = $this->data[1];
            $this->$method($this->data[2]);
        }
    }

    /**
     * @param $data
     */
    private function applyCH($data){
        $applyId = $data;
        $applyModel = new FriendApplyModel($this->server->db);
        $apply = $applyModel->selectOne(['id' => $applyId]);
        //如果申请目标在线,推送有新还有申请标识
        if ($this->server->redis->sIsMember("onlineList", $apply['target_id'])) {
            $targetFd = $this->server->redis->hGet('userId:userFd', $apply['target_id']);
            $this->pushNewApply($targetFd);
        }
    }

    /**
     * @param $data
     */
    private function agreeCH($data){
        $applyId = $data;
        $applyModel = new FriendApplyModel($this->server->db);
        $apply = $applyModel->selectOne(['id' => $applyId]);
        //如果申请人在线,推送好友申请被同意消息
        if ($this->server->redis->sIsMember("onlineList", $apply['sponsor_id'])) {
            $sponsorFd = $this->server->redis->hGet('userId:userFd',  $apply['sponsor_id']);
            $friend = (new UserModel($this->server->db))->findOne(['id'=>$apply['target_id']]);
            $this->pushAgreeSucc($sponsorFd, ['friend' => $friend]);
        }
    }

    /**
     * @param $data
     */
    private function closeFD($data){
        $closeFd = $data;
        if ($this->server->exist($closeFd)) {
            //此处有可能消息没发送就关闭了连接
            //todo
            $this->server->push($closeFd, json_encode(['type' => 'repeat']));
            $this->server->close($closeFd);
        }
    }

    /**
     * @param $data
     * 创建群组
     */
    private function createGroup($data){
        $groupId = $data;
        $groupModel = new GroupModel($this->server->db);
        $group = $groupModel->findOneWithUser($groupId);
        $time = date("Y-m-d H:i:s");
        $redis = $this->server->redis;
        foreach ($group['userIds'] as $key =>$val ){
            $chatDate = [                      //创建聊天
                'user_id'=>$val['user_id'],
                'target_id'=>$groupId,
                'type'=>ChatModel::TYPE_GROUP,
                'last_chat_time'=>$time
            ];
            $chatModel = new ChatModel($this->server->db);
            $chatId = $chatModel->insert($chatDate);
            if($redis->sIsMember("onlineList",$val['user_id'])){
                $fd  = $redis->hGet("userId:userFd",$val['user_id']);
                $group = $chatModel->findOneWithGroup(['chat_id'=>$chatId])[0];
                $group['msgList'] = [];
                $group['notReadNum'] = 0;
                $group['online'] = true;
                $group['userList'] = (new UserModel($this->server->db))->findByGroup($group['group_id']);
                $this->pushNewGroup($fd,['group'=>$group]);
            }
        }
    }

    private function quitGroup($data){
        $chat = json_decode($data,true);
        $groupUserList = (new GroupUserModel($this->server->db))->selectAll(['group_id'=>$chat['target_id']]);
        $redis = $this->server->redis;
        $chatModel = new ChatModel($this->server->db);
        foreach ($groupUserList as $k => $user){
            if($redis->sIsMember('onlineList',$user['user_id'])){
                $userChat = $chatModel->selectOne(['target_id'=>$chat['target_id'],"user_id"=>$user['user_id']]);
                $fd  = $redis->hGet("userId:userFd",$user['user_id']);
                $this->push($fd,['userId'=>$chat['user_id'],'chatId'=>$userChat['chat_id']],"quitGroup");
            }
        }
    }
}