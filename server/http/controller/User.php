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
class User extends Auth
{

    /**
     * @return array|mixed
     */
    public function view()
    {
        $medoo = App::createObject(Medoo::class);
        $userModel = new UserModel($medoo);
        $userId = $this->user['id'];
        $userList = $userModel->findAll(
            [
                'username[~]' => '%' . $this->request->get['search'] . '%',
                'id[!]' => $this->user['id'],
                'LIMIT' => 20
            ]
        );
        $friendModel = new UserFriendModel($medoo);
        $applyModel = new FriendApplyModel($medoo);
        foreach ($userList as $key => &$user) {
            //检查是否已经是朋友
            if ($friendModel->isFriend($userId, $user['id'])) {
                $user['can_apply'] = false;
                continue;
            }
            //检查是否申请中
            if ($applyModel->isApplying($userId, $user['id'])) {
                $user['can_apply'] = false;
            }
        }
        return ['status' => 1, 'userList' => $userList];
    }


    /**
     * @return array|mixed
     */
    public function update()
    {
        $userId = $this->user['id'];
        $type = $this->request->post['type'];
        if ($type === 'applyRead') {
            $model = App::createObject(FriendApplyModel::class);
            if ($model->update(['is_read' => 1], ['target_id' => $userId])) {
                return ['status' => 1];
            }
        }
    }

}