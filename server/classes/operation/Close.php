<?php
namespace server\classes\operation;
use App;
/**
 * zjw
 * 2017/11/21
 */
class Close{
    public static function run($server,$fd,$reactorId){
        $redis = App::$DI->redis;
        $userId = $redis->get("user:".$fd.":id");
        if($userId){
            $redis->del("user:".$userId.":fd");
            $redis->del("user:".$fd.":id");
            $redis->srem(USER_ONLINE,$userId);
        }
        foreach ($server->connections as $fd){
            $data = ['type'=>"goOff","userId"=>$userId];
            $server->push($fd,json_encode($data));
        }
    }
}