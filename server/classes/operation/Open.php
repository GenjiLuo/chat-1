<?php
namespace server\classes\operation;
use common\model\UserModel;
use App;
/**
 * zjw
 * 2017/11/6
 */
class Open{

    public static function run($server,$frame){
        $serverInfo  = $frame->server;
        $queryStr = $serverInfo['query_string'];
        $query = explode("=",$queryStr);
        $token = $query[1];
        $user = UserModel::findOne(['access_token'=>$token]);
        $redis =  App::$DI->redis;
        $redis->sAdd(USER_ONLINE,$user["id"]);
        $redis->set("user:".$user["id"].":fd",$frame->fd);
        $redis->set("user:".$frame->fd.":id",$user['id']);
        $users = UserModel::findAll();
        $userKeyId = [];
        //生成按id索引的数组
        foreach ($users as $key=>$val ){
            echo $val['id'];
            $userKeyId[$val['id']] = $val;
            if ($redis->sIsMember(USER_ONLINE,$val['id'])){
                $userKeyId[$val['id']]['online'] = 1;
            }else{
                $userKeyId[$val['id']]['online'] = 0;
            }
        }

        //在线排序在前
        usort($userKeyId,function($a,$b){
            if($a['online']==$b['online']) return 0;
            return $a['online']>$b['online']? -1:1;
        });
        //推送人员列表
        var_dump($userKeyId);
        $data = ["type"=>"userList","users"=>$userKeyId];
        $server->push($frame->fd,json_encode($data));
        //发送用户上线信息
        foreach ($server->connections as $fd){
            $server->push($fd,json_encode(["type"=>"goOnline","userId"=>$user['id']]));
        }
    }
}