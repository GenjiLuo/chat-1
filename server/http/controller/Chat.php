<?php

namespace server\http\controller;

use common\lib\DB;
use common\model\ChatModel;
use common\model\MessageModel;
use core\App;

class Chat extends Controller
{
    /**
     * @return array|mixed
     * 删除聊天
     */
    public function delete()
    {
        $chatId = isset($this->request->get['ChatId']) ? $this->request->get['ChatId'] : null;
        if ($chatId) {
            $db = App::createObject(DB::class);
            $result = (new ChatModel($db))->delete(['chat_id' => $chatId]);
            if ($result) {
                (new MessageModel($db))->delete(['chat_id' => $chatId]);
                return ['status' => 1, 'chatId' => $chatId];
            }
        }
        return ['status' => 0];
    }

}