<?php
namespace server\classes\operation;
/**
 * zjw
 * 2017/11/22
 */
use App;
use common\model\MessageModel;

class Message{
    public static function run($server,$frame){
        $data = json_decode($frame->data,true);
        $redis =App::$DI->redis;
        if($data['type']==='msg'){
            $to = $data['to'];
            $message = [
                "from" =>$data['from'],
                "to" =>$data['to'],
                "avatar" =>$data['avatar'],
                "msg" =>$data['msg'],
                "time" =>$data['time'],
            ];
            MessageModel::add($message);
            if($redis->sIsMember(USER_ONLINE,$to)){
                $data['owner'] = false;
                $toFd = $redis->get("user:{$to}:fd");
                $server->push($toFd,json_encode($data));
            }else{
                //todo 不在线
            }
        }
    }
}