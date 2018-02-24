<?php

namespace server\classes;

use WsServer;
use server\classes\operation\Close;
use server\classes\operation\Login;
use App;
use server\classes\operation\Message;
use server\classes\operation\Open;

/**
 * zjw
 * 2017/11/6
 */
class Operation
{

    /**
     * zjw
     * @param $server
     * @param $frame
     */
    public static function open($server,$frame){
        Open::run($server,$frame);
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

    /**
     * zjw
     * @param $server
     * @param $frame
     */
    public static function message($server,$frame){
        Message::run($server,$frame);
    }

}