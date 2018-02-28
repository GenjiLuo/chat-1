<?php
namespace server\ws\action;
use common\model\UserModel;
use App;
use common\model\MessageModel;
class Open extends Action {

    public function handle()
    {
        $token = $this->request->get['token'];
        $user = UserModel::findOne(["access_token"=>$token]);
        $fd = $this->request->fd;
        if($user){
            // 在线用户列表
            App::$DI->redis->sAdd("onlineList",$user['id']);
            // 用户id:$fd 关联哈希表
            App::$DI->redis->hSet("userId:userFd",$user['id'],$fd);
            // 用户$fd:id 关联哈希表
            App::$DI->redis->hSet("userFd:userId",$fd,$user['id']);
            // 用户列表
            $userList = UserModel::findAll(['id[!]'=>$user['id']]);
            foreach ($userList as $key => &$val ){
                // 过滤掉自己
                if($val['id'] == $user['id']) {
                    continue;
                }
                if (App::$DI->redis->sIsMember('onlineList', $val['id'])) {
                    $val['online'] = true;
                } else {
                    $val['online'] = false;
                }
                $msgList = MessageModel::find([
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
            $data = ["friends" => $userList];
            $this->push($fd,$data,self::TYPE_FRIEND_LIST);
            //发送用户上线信息
            foreach ($this->server->connections as $userFd) {
                if ($userFd == $fd) continue; //排除掉自己
                $this->push($userFd, [ "user" => $user],self::TYPE_GO_ONLINE);
            }
        }else{
            $this->push($fd,[],"forbidden");
        }
    }
}