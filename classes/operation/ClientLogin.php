<?php
namespace classes\operation;
use ChatServer;
/**
 * zjw
 * Date: 2017/11/10
 */
class ClientLogin extends Base {
    /**
     * zjw
     * @param $server
     * @param $frame
     * 登录
     */
    public static function run($server,$frame){
        ChatServer::$app->redis->sadd(CLIENT_ONLINE, $frame->fd);
    }
}
