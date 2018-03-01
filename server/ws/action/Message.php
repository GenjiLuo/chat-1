<?php
namespace server\ws\action;
use common\model\MessageModel;
use common\model\UserModel;
use App;
use Swoole\Mysql;
class Message extends Action{
    /**
     * @throws Mysql\Exception
     */
    public function handle()
    {
        $data = json_decode($this->frame->data, true);
        $redis = App::$DI->redis;
        if ($data['type'] === 'msg') {
            $to = $data['to'];
            $message = [
                "from" => $data['from'],
                "to" => $data['to'],
                "avatar" => $data['avatar'],
                "msg" => $data['msg'],
                "time" => $data['time'],
            ];
            MessageModel::add($message);
            // 异步保存消息
            $swooleDB = new Mysql();
            $swooleDB->connect($this->db,function (Mysql $db,$r) use ($message){
                $sql = 'insert into ';
                $db->query($sql, function(Mysql $db, $r) {
                    if ($r === false)
                    {
                        var_dump($db->error, $db->errno);
                    } elseif ($r === true )
                    {
                        var_dump($db->affected_rows, $db->insert_id);
                    }
                    $db->close();
                });
            });
            if ($redis->sIsMember("onlineList", $to)) {
                $data['owner'] = false;
                $toFd = $redis->hGet("userId:userFd",$to);
                $this->pushMessage($toFd, $data);
            } else {
                //todo 不在线
            }
        }
        if ($data['type'] === 'userList') {
            $userList = UserModel::findAll(['nickname[~]'=>'%'.$data['search'].'%']);
            $this->push($this->frame->fd,['users'=>$userList],self::TYPE_FRIEND_LIST);
        }
    }

}