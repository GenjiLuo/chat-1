<?php

namespace server\http\controller;


use core\App;
use common\model\UserFriendModel;


class Friend extends Auth
{
    /**
     * @return array|mixed
     */
    public function view()
    {
        $userId = $this->user['id'];
        $model = App::createObject(UserFriendModel::class);
        $friendList = $model->find($userId);
        return ['status' => 1, 'friendList' => $friendList];
    }

    /**
     * @return array|mixed
     */
    public function delete()
    {
        $friendId = $this->params['id'];
        $userId = $this->user['id'];
        $model = App::createObject(UserFriendModel::class);
        if($model->deleteFriend($userId,$friendId)){
            return ['status'=>1,'friendId'=>$friendId];
        }
        return ['status'=>0];
    }


}