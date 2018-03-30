<?php

namespace server\http\controller;

use common\model\ChatModel;
use common\model\MessageModel;
use core\App;
use Medoo\Medoo;

class Message extends Controller
{
    /**
     * @return array|mixed
     */
    public function view()
    {
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
        $pageSize = MessageModel::PAGE_SIZE;
        $chatId = $this->request->get['id'];
        $time = $this->request->get['time'];
        $db = App::createObject(Medoo::class);
        $chatModel = new ChatModel($db);
        $chat = $chatModel->selectOne(['chat_id' => $chatId]);
        if ($chat) {
            if ($chat['type'] == ChatModel::TYPE_FRIEND) {
                $where = [
                    "OR #1" => [
                        "AND #1" => ["from_id" => $chat["target_id"], "to_id" => $chat['user_id']],
                        "AND #2" => ["from_id" => $chat['user_id'], "to_id" => $chat["target_id"]],
                    ],
                    'time[<=]' => $time,
                    'chat_id' => $chat['chat_id'],
                    "LIMIT" => [$pageSize * ($page - 1), 20],
                    "ORDER" => ['time' => "DESC"]
                ];
            } else {
                $where = [
                    "to_id" => $chat["target_id"],
                    'chat_id' => $chat['chat_id'],
                    'time[<=]' => $time,
                    "LIMIT" => [$pageSize * ($page - 1), 20],
                    "ORDER" => ['time' => "DESC"]
                ];
            }
            $msgList = (new MessageModel($db))->findWithAvatar($where);
            return ['status' => 1, 'msgList' => array_reverse($msgList)];
        }
        return ['status' => 0];

    }
}