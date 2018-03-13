<?php
namespace server\ws\action;
use common\lib\MyRedis;
use common\model\ChatModel;
use common\model\FriendApplyModel;
use common\model\UserModel;
use core\App;
use common\model\MessageModel;

class Open extends Action {

    public function handle()
    {
        $redis = $this->server->redis;
        $token = $this->request->get['token'];
        $userId = $redis->get($token);
        $fd = $this->request->fd;
        if($userId){
            $userModel = new UserModel($this->server->db);
            $user = $userModel->findOne(['id'=>$userId ]);
            // 在线用户列表
            $redis->sAdd("onlineList",$userId);
            // 用户id:$fd 关联哈希表
            $redis->hSet("userId:userFd",$userId,$fd);
            // 用户$fd:id 关联哈希表
            $redis->hSet("userFd:userId",$fd,$userId);
            // 聊天列表
            $chatModel = new ChatModel($this->server->db);
            $chatList = $chatModel->findAll($userId);
            // 判断是否在线
            if(sizeof($chatList) > 0 ){
                foreach ($chatList as $key => &$val ) {
                    if ($redis->sIsMember('onlineList', $val['id'])) {
                        $val['online'] = true;
                    } else {
                        $val['online'] = false;
                    }
                }
                // 排序,先按照上下线排序，再按最后聊天排序
                foreach ( $chatList as $key =>$val ){
                    $sortArrOne[$key] = $val['last_chat_time'];
                    $sortArrTwo[$key] = $val['online'];
                }
                array_multisort($sortArrTwo,SORT_DESC,$sortArrOne,SORT_DESC,$chatList);
            }

//                $msgList = (new MessageModel($this->server->db))->find([
//                        "OR #1" => [
//                            "AND #1" => ["from" => $val["id"], "to" => $user['id']],
//                            "AND #2" => ["from" => $user['id'], "to" => $val["id"]],
//                        ],
//                        "LIMIT" => 20,
//                        "ORDER" => ['time' => "DESC"]
//                    ]
//                );
//                $val['msgList'] = array_reverse($msgList);
//            }
            $this->pushChatList($fd,["chatList" => $chatList]);
            // 调用task进程广播用户上线信息
            $this->pushTask(['fd'=>$fd,'user'=>$user],Task::TASK_ONLINE);
            // 好友申请列表
            $applyModel = new FriendApplyModel($this->server->db);
            $applyList = $applyModel->find(['target_id'=>$userId]);
            $this->pushApplyList($fd,['applyList'=>$applyList]);
        }else{
            $this->push($fd,[],"forbidden");
        }
    }
}