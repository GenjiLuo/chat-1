<?php
namespace server\ws\action;
use common\lib\MyRedis;
use common\model\UserModel;
use core\App;
use common\model\MessageModel;

class Open extends Action {

    public function handle()
    {
        $redis = App::createObject(MyRedis::class);
        $token = $this->request->get['token'];
        $userId = $redis->get($token);
        $fd = $this->request->fd;
        $redis = $this->server->redis;
        if($userId){
            $userModel = new UserModel($this->server->db);
            $user = $userModel->findOne(['id'=>$userId ]);
            // 在线用户列表
            $redis->sAdd("onlineList",$user['id']);
            // 用户id:$fd 关联哈希表
            $redis->hSet("userId:userFd",$user['id'],$fd);
            // 用户$fd:id 关联哈希表
            $redis->hSet("userFd:userId",$fd,$user['id']);
            // 用户列表

            $userList = $userModel->findAll(['id[!]'=>$user['id']]);
            foreach ($userList as $key => &$val ){
                if ($redis->sIsMember('onlineList', $val['id'])) {
                    $val['online'] = true;
                } else {
                    $val['online'] = false;
                }
                $msgList = (new MessageModel($this->server->db))->find([
                        "OR #1" => [
                            "AND #1" => ["from" => $val["id"], "to" => $user['id']],
                            "AND #2" => ["from" => $user['id'], "to" => $val["id"]],
                        ],
                        "LIMIT" => 20,
                        "ORDER" => ['time' => "DESC"]
                    ]
                );
                $val['msgList'] = array_reverse($msgList);
            }
            $this->pushFriendList($fd,["friends" => $userList]);
            // 调用task进程广播用户上线信息
            $this->pushTask(['fd'=>$fd,'user'=>$user],Task::TASK_ONLINE);
        }else{
            $this->push($fd,[],"forbidden");
        }
    }
}