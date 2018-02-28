<?php

use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * 路由设置
 * 可以是可调用函数（闭包）或者controller
 * 如果是controller则默认按照restful规则,详情参考 server\http\controller\Controller
 * 函数的参数分别是 \Swoole\Http\Request,\Swoole\Http\Response
 * 如果在router配置没有找到路由，则默认会去查找如下类是否存在
 *  /user => server\http\controller\User()
 *  /user-add => server\http\controller\UserAdd()
 */
return [
    "/" => \server\http\controller\Test::class,
    "user"=>\server\http\controller\User::class,
    "/checkUsername" => function (Request $request, Response $response) {
        $response->header("Content-Type", "application/json;charset=UTF-8");
        $user = \common\model\UserModel::findOne(["username" => $request->get['username']]);
        if ($user) {
            return json_encode(['status' => 0]);
        } else {
            return json_encode(['status' => 1]);
        }
    }

];