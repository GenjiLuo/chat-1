<?php

namespace server\http\controller;

use common\model\UserModel;

class User extends Controller
{
    public function add()
    {
        $post = $this->request->post;
        $username = $post['username'];
        if (UserModel::findOne(["username" => $username])) {
            return ['status' => 0, 'errMsg' => "用户名已存在"];
        } else {
            if (UserModel::add($post)) {
                return ['status' => 1];
            }
        }
        return ['status' => 0, 'errMsg' => "发生未知错误"];
    }
}