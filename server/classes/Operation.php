<?php

namespace server\classes;

use ChatServer;
use server\classes\operation\Close;
use server\classes\operation\Login;
use App;
use server\classes\operation\Open;

/**
 * zjw
 * 2017/11/6
 */
class Operation
{

    const CLIENT_LOGIN = "clientLogin";
    const MSG_MESSAGE = "message";



    public static function open($server,$frame){
        Open::run($server,$frame);
    }



    public static function message($server,$frame)
    {
        $data = json_decode($frame->data, true);
        $type = $data['type'];
        switch ($type) {
            case self::CLIENT_LOGIN :
                Login::run($server,$frame);
                break;
            case self::MSG_MESSAGE :
                $fd = $frame->fd;
                App::$DI->redis->rpush("member:" . $fd . ":message", $data["data"]);
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
    public static function close($server,$fd,$reactorId){
        Close::run($server,$fd,$reactorId);
    }

}