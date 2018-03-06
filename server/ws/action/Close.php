<?php
namespace server\ws\action;
use App;

class Close extends Action{

    public function handle()
    {
        $redis =$this->server->redis;
        $userId = $redis->hGet("userFd:userId" , $this->fd);
        if ($userId) {
            $redis->hDel("userId:userFd" , $userId );
            $redis->del("userFd:userId" ,$this->fd );
            $redis->srem("onlineList", $userId);
        }
        // 调用task进程广播用户下线消息
        $this->pushTask(['fd'=>$this->fd,'userId'=>$userId],Task::TASK_OFFLINE);
    }

}