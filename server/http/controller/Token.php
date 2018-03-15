<?php

namespace server\http\controller;

use common\lib\MyRedis;
use common\model\UserModel;
use core\App;

class Token extends Controller
{
    public function update()
    {
        $redis = App::createObject(MyRedis::class);
        $userModel = App::createObject(UserModel::class);
        if (!isset($this->request->post['token'])) {  //账号密码登陆
            $username = $this->request->post['username'];
            $password = $this->request->post['password'];
            $user = $userModel->findOne(['username' => $username, "password" => md5($password)]);
            if ($user) {
                // 如果该用户已经登陆在线,获取fd加入待关闭的队列中
                if ($redis->sIsMember("onlineList", $user['id'])) {
                    $fd = $redis->hGet("userId:userFd", $user['id']);
                    $redis->lPush("closeQueue", $fd);
                }
                $token = md5(time() + rand(1000, 9999));
                $redis->set($token, $user["id"]);
                $redis->close();
                return ['status' => 1, "token" => $token, "user" => $user];
            } else {
                return ['status' => 0];
            }
        } else {  //token登陆
            $token = $this->request->post['token'];
            $userId = $redis->get($token);
            if ($userId) {
                $user = $userModel->findOne(['id' => $userId]);
                return ['status' => 1, "token" => $token, "user" => $user];
            } else {
                return ['status' => 0];
            }
        }
    }
}