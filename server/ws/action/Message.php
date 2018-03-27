<?php

namespace server\ws\action;

use common\model\ChatModel;
use common\model\FriendApplyModel;
use common\model\GroupModel;
use common\model\MessageModel;
use common\model\UserFriendModel;
use common\model\UserModel;
use core\App;


/**
 * Class Message
 * @package server\ws\action
 * 接受消息处理函数
 */
class Message extends Action
{
    /**
     * @return mixed|void
     */
    public function handle()
    {
        $data = json_decode($this->frame->data, true);
        $type = $data['type'];
        if (is_callable([$this, $type])) {
            $this->$type($data);
        }
    }

    /**
     * @param $data
     * 发消息
     */
    private function msg($data)
    {
        $redis = $this->server->redis;
        $userId = $this->server->redis->hGet("userFd:userId", $this->frame->fd);
        $messageModel = new MessageModel($this->server->db);
        $chatId = $data['chat_id'];
        $chatModel = new ChatModel($this->server->db);
        $chatInfo = $chatModel->selectOne(['chat_id' => $chatId, 'user_id' => $userId]);
        // 存在聊天对象才处理
        if ($chatInfo) {
            if ($chatInfo['type'] == ChatModel::TYPE_FRIEND) {  // 聊天对象是朋友
                $date = date('Y-m-d H:i:s');
                $msg = [
                    'from_id' => $userId,
                    'to_id' => $chatInfo['target_id'],
                    'chat_id' => $chatInfo['chat_id'],
                    'time' => $date,
                    'msg' => $data['msg'],
                    'is_read'=>1
                ];
                $messageModel->insert($msg);
                $chatModel->update(['last_chat_time'=>$date],['chat_id' => $chatId]);
                // 查看对面是否有与该好友的聊天
                $targetChat = $chatModel->selectOne(['target_id' => $userId, 'user_id' => $chatInfo['target_id']]);
                if (sizeof($targetChat) == 0) { //没有
                    $targetChatId = $chatModel->add($chatInfo['target_id'], $userId); // 新增一个聊天
                    $newChat = $chatModel->findOneWithUser(['chat_id'=>$targetChatId]);  // 获取聊天对象（包括friend info)
                    $newChat['notReadNum'] = 0;
                } else { // 有
                    $targetChatId = $targetChat['chat_id'];
                    $chatModel->update(['last_chat_time'=>$date],['chat_id' => $targetChatId]);
                }
                $reverseMsg = [
                    'from_id' => $userId,
                    'to_id' => $chatInfo['target_id'],
                    'chat_id' => $targetChatId,
                    'time' => $date,
                    'msg' => $data['msg'],
                    'is_read'=>0
                ];
                $messageModel->insert($reverseMsg);
                if ($redis->sIsMember("onlineList", $chatInfo['target_id'])) { //目标用户在线
                    $data['owner'] = false;
                    $toFd = $redis->hGet("userId:userFd", $chatInfo['target_id']);
                    if(isset($newChat)){
                        $this->pushMessage($toFd, ['msg'=>$reverseMsg,'chat'=>$newChat]);
                    }else{
                        $this->pushMessage($toFd, ['msg'=>$reverseMsg]);
                    }
                }
            } else {   // 聊天对象是群组
                $date = date('Y-m-d H:i:s');
                $groupModel = new GroupModel($this->server->db);
                $group = $groupModel->findOneWithUser($chatInfo['target_id']);
                $chatModel->update(['last_chat_time'=>$date],['target_id' =>$chatInfo['target_id']]);
                foreach ($group['userIds'] as $key => $val){
                    $groupUserChat = $chatModel->findOneWithGroup(
                        [
                            'user_id'=>$val['user_id'],
                            'target_id'=>$chatInfo['target_id']
                        ]
                    )[0];
                    $reverseMsg = [
                        'from_id' => $userId,
                        'to_id' => $chatInfo['target_id'],
                        'chat_id' => $groupUserChat['chat_id'],
                        'time' => $date,
                        'msg' => $data['msg'],
                        'is_read'=> $userId == $val['user_id'] ? 1: 0
                    ];
                    $messageModel->insert($reverseMsg);
                    if ($val['user_id'] != $userId && $redis->sIsMember("onlineList", $val['user_id'])) { //目标用户在线
                        $data['owner'] = false;
                        $toFd = $redis->hGet("userId:userFd", $val['user_id']);
                        $this->pushMessage($toFd, ['msg'=>$reverseMsg]);
                    }
                }
            }
        }
    }


    /**
     * @param $data
     * 获取用户列表
     */
    private function userList($data)
    {
        $userModel = new UserModel($this->server->db);
        $userId = $this->server->redis->hGet("userFd:userId", $this->frame->fd);
        $userList = $userModel->findAll(
            [
                'username[~]' => '%' . $data['search'] . '%',
                'id[!]' => $userId,
                'LIMIT' => 20
            ]
        );
        $friendModel = new UserFriendModel($this->server->db);
        $applyModel = new FriendApplyModel($this->server->db);
        foreach ($userList as $key => &$user) {
            //检查是否已经是朋友
            if ($friendModel->isFriend($userId, $user['id'])) {
                $user['can_apply'] = false;
                continue;
            }
            //检查是否申请中
            if ($applyModel->isApplying($userId, $user['id'])) {
                $user['can_apply'] = false;
            }
            $this->pushUserList($this->frame->fd, ['userList' => $userList]);
        }
    }
}