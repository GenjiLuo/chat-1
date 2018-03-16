<?php

namespace server\http\controller;

use core\App;
use common\model\UserFriendModel;
class Friend extends Auth
{
    public function view()
    {
        $userId = $this->user['id'];
        $model = App::createObject(UserFriendModel::class);
        $friendList = $model->find($userId);
        return ['status'=>1,'friendList'=>$friendList];
    }

}