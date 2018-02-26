<?php

return [
    "/"=>\server\http\controller\TestController::class,
    "/test"=> function($request,$response){
        return "hahahahha";
    },
    "/user" => \server\http\controller\User::class
];