<?php
namespace server\ws\action;
use App;
class Close extends Action{
    function handle()
    {
        $redis = App::$DI->redis;
        $userId = $redis->hGet("userFd:userId" , $this->fd);
        if ($userId) {
            $redis->hDel("userId:userFd" , $userId );
            $redis->del("userFd:userId" ,$this->fd );
            $redis->srem("onlineList", $userId);
        }
        //发送用户下线消息
        $data = ['type' => "goOff", "userId" => $userId];
        foreach ($this->server->connections as $onlineFd) {
            if ($this->fd == $onlineFd) continue; //此时$server->connections还未移除当前关掉的fd,需要自己排除掉
            $this->server->push($onlineFd, json_encode($data));
        }
    }

}