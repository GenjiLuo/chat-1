<?php
namespace server\ws\action;
class Task extends Action{

    const TASK_ONLINE = 'online';
    const TASK_OFFLINE = 'offline';

    public function handle()
    {
        switch ($this->data['type']){
            case self::TASK_ONLINE :
                $this->handleOnline();
                break;
            case self::TASK_OFFLINE:
                $this->handleOffLine();
                break;
        }
    }

    /**
     * 上线task任务处理函数
     */
    private function handleOnline(){
        foreach ($this->server->connections as $userFd) {
            if ($userFd == $this->data['fd']) continue; // 排除掉自己
            $this->push($userFd, ['user' => $this->data['user']],Action::TYPE_GO_ONLINE );
        }
        $this->server->finish("task");
    }

    /**
     * 下线task任务处理函数
     */
    private function handleOffLine(){
        foreach ($this->server->connections as $onlineFd) {
            if ($this->data['fd'] == $onlineFd) continue; //此时$server->connections还未移除当前关掉的fd,需要自己排除掉
            $this->push($onlineFd,['userId'=>$this->data['userId']],'goOff');
        }
        $this->server->finish("task");
    }

}