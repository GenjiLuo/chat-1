<?php
namespace api\controllers;
use App;
use common\model\UserModel;

/**
 * zjw
 * 2017/11/16
 */
class User extends Base {

    /**
     * zjw
     * @return mixed
     */
    public function login(){

        $username = App::$DI->request->post('username');
        $password = App::$DI->request->post('password');
        $user = UserModel::findOne(["username"=>$username,"password"=>md5($password)]);
        if($user){
            $ip = App::$DI->request->getIp();
            $expired = time()+7*24*3600;
            $token = md5($ip.$expired);
            UserModel::update(["access_token"=>$token,"token_expired"=>$expired,"access_ip"=>$ip],['id'=>$user['id']]);
            return ['status'=>1,'token'=>$token,"user"=>$user];
        }else{
            return ['status'=>0];
        }
    }

    /**
     * zjw
     * @return array
     */
    public function create(){
        $data = [
            'username'=>App::$DI->request->post('username'),
            'password'=>App::$DI->request->post('password'),
            'created_at'=>time(),
            'avatar'=>BASE_URL."/static/avatar/default"
        ];
        if(UserModel::add($data)){
            return [
                'status'=>1,
            ];
        }
        return ['status'=>0];
    }

    /**
     * zjw
     * @return array
     */
    public function check(){
        $username = $_GET['username'];
        $user = UserModel::findOne(['username'=>$username]);
        if($user){
            return [
                'status'=>0
            ];
        }
        return ['status'=>1];
    }

    /**
     * zjw
     */
    public function add(){

    }


}