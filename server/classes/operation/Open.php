<?php

namespace server\classes\operation;

use App;
use common\model\MessageModel;
use common\model\UserModel;

/**
 * zjw
 * 2017/11/6
 */
class Open
{

    public static function run($server, $frame)
    {
        $serverInfo = $frame->server;
        $queryStr = $serverInfo['query_string'];
        $query = explode("=", $queryStr);
        $token = $query[1];
        $user = UserModel::findOne(['access_token' => $token]);
        $redis = App::$DI->redis;

        $redis->sAdd(USER_ONLINE, $user["id"]);
        $redis->set("user:" . $user["id"] . ":fd", $frame->fd);
        $redis->set("user:" . $frame->fd . ":id", $user['id']);

        $users = UserModel::findAll();
        $friend = [];

        foreach ($users as $key => $val) {
            if ($val["id"] == $user['id']) continue; //排除掉自己
            if ($redis->sIsMember(USER_ONLINE, $val['id'])) {
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
            $friend[] = $val;
        }

        $data = ["type" => "userList", "friend" => $friend];
        $server->push($frame->fd, json_encode($data));
        //发送用户上线信息
        foreach ($server->connections as $fd) {
            if ($fd == $frame->fd) continue; //排除掉自己
            $server->push($fd, json_encode(["type" => "goOnline", "user" => $user]));
        }
    }
}