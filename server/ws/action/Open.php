<?php

namespace server\ws\action;

use common\model\ChatModel;
use common\model\MessageModel;
use common\model\UserModel;

class Open extends Action
{
    public function handle()
    {
        $redis = $this->server->redis;
        $token = $this->request->get['token'];
        $user = (new UserModel($this->server->db))->findOne(['access_token' => $token]);
        $fd = $this->request->fd;
        if ($user) {
            $currentTime = date('Y-m-d H:i:s');
            $userId = $user['id'];
            // 如果已经登陆且连接ws,即同一浏览器打开多个界面
            if($redis->sIsMember("onlineList",$userId)){
                $oldFd = $redis->hGet("userId:userFd",$userId);
                $this->pushRepectConnect($oldFd);
                $this->server->close($oldFd);
            }
            // 在线用户列表
            $redis->sAdd("onlineList", $userId);
            // 用户id:$fd 关联哈希表
            $redis->hSet("userId:userFd", $userId, $fd);
            // 用户$fd:id 关联哈希表
            $redis->hSet("userFd:userId", $fd, $userId);

            $chatModel = new ChatModel($this->server->db);
            // 用户聊天对象
            $userChatList = $chatModel->findAllWithUser($userId);
            foreach ($userChatList as $key => $val) {
                // 判断是否在线
                if ($redis->sIsMember('onlineList', $val['id'])) {
                    $userChatList[$key]['online'] = true;
                } else {
                    $userChatList[$key]['online'] = false;
                }
                $msgList = (new MessageModel($this->server->db))->findWithAvatar([
                        "OR #1" => [
                            "AND #1" => ["from_id" => $val["target_id"], "to_id" => $userId],
                            "AND #2" => ["from_id" => $userId, "to_id" => $val["target_id"]],
                        ],
                        'chat_id' => $val['chat_id'],
                        "LIMIT" => MessageModel::PAGE_SIZE,
                        "ORDER" => ['time' => "DESC"]
                    ]
                );
                // 计算未读消息数量
                $userChatList[$key]['notReadNum'] = 0;
                foreach ( $msgList as $k => $v ){
                    if($v['is_read'] == 0){
                        $userChatList[$key]['notReadNum'] += 1;
                    }
                }
                $userChatList[$key]['msgList'] = array_reverse($msgList);
                $userChatList[$key]['getMsgTime'] = $currentTime;
                $userChatList[$key]['page'] = 1;
            }
            // 群组聊天对象
            $groupChatList = $chatModel->findOneWithGroup(['user_id' => $userId]);
            foreach ($groupChatList as $key => $val) {
                $groupChatList[$key]['online'] = true;
                $msgList = (new MessageModel($this->server->db))->findWithAvatar([
                        "to_id" => $val["target_id"],
                        'chat_id' => $val['chat_id'],
                        "LIMIT" => 20,
                        "ORDER" => ['time' => "DESC"]
                    ]
                );
                // 计算未读消息数量
                $groupChatList[$key]['notReadNum'] = 0;
                foreach ( $msgList as $k => $v ){
                    if($v['is_read'] == 0){
                        $groupChatList[$key]['notReadNum'] += 1;
                    }
                }
                $userModel = new UserModel($this->server->db);
                $groupChatList[$key]['userList'] = $userModel->findByGroup($val['group_id']);
                $groupChatList[$key]['msgList'] = array_reverse($msgList);
                $groupChatList[$key]['getMsgTime'] = $currentTime;
                $groupChatList[$key]['page'] = 1;
            }
            $chatList = array_merge($groupChatList, $userChatList);
            $this->pushChatList($fd, ["chatList" => $chatList]);
            // 调用task进程广播用户上线信息
            $this->pushTask(['userId' => $user['id']], Task::TASK_ONLINE);
        } else {
            $this->push($fd, [], "forbidden");
        }
    }
}