<?php

namespace server\classes\operation;

use App;

/**
 * zjw
 * 2017/11/21
 */
class Close
{
    public static function run($server, $fd, $reactorId)
    {
        $redis = App::$DI->redis;
        $userId = $redis->get("user:" . $fd . ":id");
        if ($userId) {
            $redis->del("user:" . $userId . ":fd");
            $redis->del("user:" . $fd . ":id");
            $redis->srem(USER_ONLINE, $userId);
        }
        $data = ['type' => "goOff", "userId" => $userId];
        foreach ($server->connections as $onlineFd) {
            if ($fd == $onlineFd) continue; //此时$server->connections还未移除当前关掉的fd,需要自己排除掉
            $server->push($onlineFd, json_encode($data));
        }
    }
}