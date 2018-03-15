<?php

namespace server\http\controller;
use common\lib\MyRedis;
use common\model\UserModel;
use core\App;

/**
 * Class Avatar
 * @package server\http\controller
 */
class Avatar extends Controller
{
    public function add()
    {
        $file = $this->request->files['file'];
        if ($file["type"] != "image/jpeg") {
            return ['status' => 0];
        }
        $avatarPath = BASE_ROOT."/".STATIC_DIR."/avatar/";
        $fileName = md5($file['name'].time())."jpg";
        $newFile = $avatarPath.$fileName;
        copy($file['tmp_name'],$newFile);
        $userId = (App::createObject(MyRedis::class))->get($this->request->get["token"]);
        $userModel = App::createObject(UserModel::class);
        $userModel->update(['avatar'=>$fileName],["id"=>$userId]);
        return [
            'status'=>1,
            'url'=>BASE_URL."/".STATIC_DIR."/avatar/".$fileName
        ];
    }


}