<?php

use Swoole\Http\Request;
use Swoole\Http\Response;
use common\model\UserModel;

/**
 * 路由设置
 * 可以是可调用函数（闭包）或者类的方法
 * 函数的参数分别是 \Swoole\Http\Request,\Swoole\Http\Response
 * 如果在router配置没有找到路由，则会按照如下规则去查找controller
 *  /user => server\http\controller\User()
 *  /user-add => server\http\controller\UserAdd()
 */
return [
    "/" => \server\http\controller\Test::class,
    "/checkUsername" => function (Request $request, Response $response) {
        $response->header("Content-Type", "application/json;charset=UTF-8");
        $user = \core\App::createObject(UserModel::class)->findOne(["username" => $request->get['username']]);
        if ($user) {
            return json_encode(['status' => 0]);
        } else {
            return json_encode(['status' => 1]);
        }
    }

];