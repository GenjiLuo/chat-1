<?php

namespace server\http\controller;

use common\lib\MyRedis;
use common\model\ChatModel;
use common\model\MessageModel;
use core\App;
use Medoo\Medoo;

class Chat extends Auth
{
    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function delete()
    {
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        if ($id) {
            $db = App::createObject(Medoo::class);
            $result = (new ChatModel($db))->deleteChat($id,$this->user['id']);
            if ($result) {
                return ['status' => 1, 'chatId' => $id];
            }
        }
        return ['status' => 0];
    }

    /**
     * @return array|mixed
     * 创建聊天
     */
    public function create()
    {
        $targetId = isset($this->request->post['targetId']) ? $this->request->post['targetId'] : null;
        if ($targetId) {
            $chatModel = App::createObject(ChatModel::class);
            $id = $chatModel->add($this->user['id'], $targetId);
            if ($id) {
                $newChat = $chatModel->findOneWithUser(['chat_id' => $id]);
                $redis = App::createObject(MyRedis::class);
                if($redis->sIsMember("onlineList",$targetId)){
                    $newChat['online'] = true ;
                }else{
                    $newChat['online'] = false ;
                }
                $redis->close();
                return ['status' => 1, 'chat' => $newChat];
            }
        }
        return ['status' => 0];
    }

    /**
     * @return array|mixed
     * 更新聊天所有消息为已读
     */
    public function update()
    {
        $messageModel = App::createObject(MessageModel::class);
        if ($messageModel->update(['is_read' => 1], ['chat_id' => $this->params['id']])) {
            return ['status' => 1];
        };
        return ['status' => 0];
    }


}