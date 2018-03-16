<?php
namespace server\http\controller;

use common\lib\exception\ForbiddenException;
use common\model\UserModel;
use core\App;

class Auth extends Controller{
    /**
     * @return bool
     * @throws ForbiddenException
     */
    public function beforeAction(): bool
    {
        if($this->request->server['request_method'] != 'OPTIONS'){
            $token = substr($this->request->header["authorization"],7);
            $user = App::createObject(UserModel::class)->findOne(['access_token'=>$token]);
            if(!$user){
                throw new ForbiddenException("token已失效");
            }
            $this->user = $user;
        }
        return true;
    }
}