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
        $online =[];
        $offline=[];
        foreach ($users as $key=>$val ){
            if ($redis->sIsMember(USER_ONLINE,$val['id'])){
                $online[] =$val;
            }else{
                $offline[] =$val;
            }
        }
        $data = ["type"=>"userList","online"=>$online,"offline"=>$offline];
        $server->push($frame->fd,json_encode($data));
        //发送用户上线信息
        foreach ($server->connections as $fd){
            $server->push($fd,json_encode(["type"=>"goOnline","userId"=>$user['id']]));
        }
    }
}