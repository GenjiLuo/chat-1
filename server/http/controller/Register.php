<?php
namespace server\http\controller;
use core\App;
use common\model\UserModel;
class Register extends Controller{
    /**
     * @return array|mixed
     */
    public function create()
    {
        $post = $this->request->post;
        $username = $post['username'];
        $userModel = App::createObject(UserModel::class);
        if ($userModel->findOne(['username' => $username])) {
            return ['status' => 0, 'errMsg' => "用户名已存在"];
        } else {
            if ($userModel->add($post)) {
                return ['status' => 1];
            }
        }
        return ['status' => 0, 'errMsg' => "发生未知错误"];
    }
}