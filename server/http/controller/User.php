<?php

namespace server\http\controller;

use common\model\UserModel;
use core\App;
use common\model\FriendApplyModel;
use Medoo\Medoo;
use common\model\UserFriendModel;


/**
 * Class User
 * @package server\http\controller
 */
class User extends Controller
{

    /**
     * @return array|mixed
     */
    public function view()
    {
//        $medoo = App::createObject(Medoo::class);
//        $userModel = new UserModel($medoo);
//        $userId = $this->user['id'];
//        $userList = $userModel->findAll(
//            [
//                'username[~]' => '%' . $this->request->get['search'] . '%',
//                'id[!]' => $this->user['id'],
//                'LIMIT' => 20
//            ]
//        );
//        $friendModel = new UserFriendModel($medoo);
//        $applyModel = new FriendApplyModel($medoo);
//        foreach ($userList as $key => &$user) {
//            //检查是否已经是朋友
//            if ($friendModel->isFriend($userId, $user['id'])) {
//                $user['can_apply'] = false;
//                continue;
//            }
//            //检查是否申请中
//            if ($applyModel->isApplying($userId, $user['id'])) {
//                $user['can_apply'] = false;
//            }
//        }
//        return ['status'=>1,'userList'=>$userList];
    }

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

    /**
     * @return array|mixed
     */
    public function update(){
        $userId = isset($this->params['id']) ? $this->params['id'] : null;
        if ($userId) {
            $type = $this->request->post['type'];
            if($type === 'applyRead'){
                $model = App::createObject(FriendApplyModel::class);
                if($model->update(['is_read'=>1],['target_id'=>$userId])){
                    return ['status'=>1];
                }
            }
        }
        return ['status'=>0];
    }

}