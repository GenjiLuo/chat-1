<?php

namespace server\ws\action;

use common\model\ChatModel;

class Task extends Action
{

    const TASK_ONLINE = 'online';
    const TASK_OFFLINE = 'offline';

    public function handle()
    {
        switch ($this->data['type']) {
            case self::TASK_ONLINE :
                $this->handleOnline();
                break;
            case self::TASK_OFFLINE:
                $this->handleOffLine();
                break;
            default:
                $this->server->finish("no task type was found" . PHP_EOL);
        }
    }

    /**
     * 上线广播task任务处理函数
     */
    private function handleOnline()
    {
        $userId = $this->data['userId'];
        // 查找所有跟上线用户有聊天关系的人
        $chatModel = new ChatModel($this->server->db);
        $chatList = $chatModel->selectAll(['target_id'=>$userId,'type'=>ChatModel::TYPE_FRIEND]);
        foreach ($chatList as $k => $chat){
            $redis = $this->server->redis;
            // 如果在线则发送好友上线通知
            if($redis->sIsMember("onlineList",$chat['user_id'])){
                $fd = $redis->hGet("userId:userFd",$chat['user_id']);
                $this->push($fd,['chatId'=>$chat['chat_id']],Action::TYPE_GO_ONLINE);
            }
        }
        $this->server->finish("online task finish " . PHP_EOL);
    }

    /**
     * 下线广播task任务处理函数
     */
    private function handleOffLine()
    {
        $userId = $this->data['userId'];
        // 查找所有跟下线用户有聊天关系的人
        $chatModel = new ChatModel($this->server->db);
        $chatList = $chatModel->selectAll(['target_id'=>$userId,'type'=>ChatModel::TYPE_FRIEND]);
        foreach ($chatList as $k => $chat){
            $redis = $this->server->redis;
            // 如果在线则发送好友上线通知
            if($redis->sIsMember("onlineList",$chat['user_id'])){
                $fd = $redis->hGet("userId:userFd",$chat['user_id']);
                $this->push($fd,['chatId'=>$chat['chat_id']],Action::TYPE_GO_OFFLINE);
            }
        }
        $this->server->finish("offline task finish " . PHP_EOL);
    }

}