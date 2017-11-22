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
            'password'=>md5(App::$DI->request->post('password')),
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
     * @return array
     */
    public function LoginByToken(){
        $token = App::$DI->request->get("token");
        $user = UserModel::findOne(["access_token"=>$token,'access_ip'=>App::$DI->request->getIp(),"token_expired[>]"=>time()]);
        if($user){
            return ['status'=>1,'user'=>$user];
        }
        return ['status'=>0];
    }

    /**
     * zjw
     */
    public function avatar(){
        $file = $_FILES['file'];
        if (!$file['error']){
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $name = md5($file['name'].time()).".".$ext;
            $savePath = UserModel::$defaultPath.$name;
            move_uploaded_file($file['tmp_name'],BASE_ROOT.$savePath);
            $token = App::$DI->request->get("token");
            $query  = UserModel::update(["avatar"=>$savePath],['access_token'=>$token]);
            if($query->rowCount()){
                return ["status"=>1,'avatar'=>BASE_URL.$savePath];
            }
            return ["status"=>0];
        }
        return ["status"=>0];
    }

}