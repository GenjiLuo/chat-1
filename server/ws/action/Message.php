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
            if ($chatInfo['type'] == ChatModel::TYPE_FRIEND) {
                $date = date('Y-m-d H:i:s');
                $msg = [
                    'from_id' => $userId,
                    'to_id' => $chatInfo['target_id'],
                    'chat_id' => $chatInfo['chat_id'],
                    'time' => $date,
                    'msg' => $data['msg']
                ];
                $messageModel->insert($msg);
                $chatModel->update(['last_chat_time'=>$date],['chat_id' => $chatId, 'user_id' => $userId]);
                // 查看对面是否有与该好友的聊天
                $targetChat = $chatModel->selectOne(['target_id' => $userId, 'user_id' => $chatInfo['target_id']]);
                if (sizeof($targetChat) == 0) { //没有
                    $chatId = $chatModel->add($chatInfo['target_id'], $userId); // 新增一个聊天
                    $newChat = $chatModel->findOneWithUser(['chat_id'=>$chatId]);  // 获取聊天对象（包括friend info)
                } else { // 有
                    $chatId = $targetChat['chat_id'];
                    $chatModel->update(['last_chat_time'=>$date],['chat_id' => $chatId]);
                }
                $reverseMsg = [
                    'from_id' => $userId,
                    'to_id' => $chatInfo['target_id'],
                    'chat_id' => $chatId,
                    'time' => $date,
                    'msg' => $data['msg']
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
        }
        $this->pushUserList($this->frame->fd, ['userList' => $userList]);
    }

    /**
     * @param $data
     * 拒绝好友申请
     */
    private function reject($data)
    {
        $applyId = $data['applyId'];
        $model = new FriendApplyModel($this->server->db);
        $model->reject($applyId);
    }

    /**
     * @param $data
     * @throws \Exception
     * 同意好友申请
     */
    private function agree($data)
    {
        $applyId = $data['applyId'];
        $model = new FriendApplyModel($this->server->db);
        if ($apply = $model->agree($applyId)) {
            $userModel = new UserModel($this->server->db);
            $target = $userModel->findOne(['id' => $apply['target_id']]);
            $sponsor = $userModel->findOne(['id' => $apply['sponsor_id']]);
            $this->pushAgreeSucc($this->frame->fd, ['friend' => $sponsor, 'applyId' => $applyId]);
            $redis = $this->server->redis;
            if ($redis->sIsMember("onlineList", $sponsor['id'])) { //如果申请人在线
                $sponsorFd = $redis->hGet('userId:userFd', $sponsor['id']);
                $this->pushApplySucc($sponsorFd, ['friend' => $target, 'applyId' => $applyId]);
            }
        }
    }

}