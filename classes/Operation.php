<?php

namespace classes;

use ChatServer;
use classes\operation\ClientLogin;

/**
 * zjw
 * 2017/11/6
 */
class Operation
{

    const CLIENT_LOGIN = "clientLogin";
    const MSG_MESSAGE = "message";



    public static function open($server,$frame){

    }



    public static function message($server, $frame)
    {
        $data = json_decode($frame->data, true);
        $type = $data['type'];
        switch ($type) {
            case self::CLIENT_LOGIN :
                ClientLogin::run($server,$frame);
                break;
            case self::MSG_MESSAGE :
                $fd = $frame->fd;
                ChatServer::$app->redis->rpush("member:" . $fd . ":message", $data["data"]);
        }
    }

    /**
     * zjw
     * @param $server
     * @param null $expect
     * 发送client端在线人数
     */
    public function sendOnline($server,$expect=null){
        $redis =  ChatServer::$app->redis;
        $data = [
            "type"=>"count",
            "num"=> $redis->scard(CLIENT_ONLINE)
        ];
        $servers = $redis->smember(CLIENT_ONLINE);
        foreach ($servers as $val){
            $server->push($val,json_encode($data));
        }
    }

    /**
     * zjw
     * @param $server
     * @param $fd
     * @param $reactorId
     * 关闭处理
     */
    public function close($server,$fd,$reactorId){

        $redis =  ChatServer::$app->redis;
        if($redis->sIsMember(CLIENT_ONLINE,$fd)){
            $redis->sRem(CLIENT_ONLINE,$fd);
        }
        if($redis->sIsMember(SERVER_ONLINE,$fd)){
            $redis->sRem(SERVER_ONLINE,$fd);
            $servers = $redis->sMembers(CLIENT_ONLINE);
            foreach ($servers as $val){

            }
        }

    }

}