<?php
namespace api\lib;
/**
 * zjw
 * Date: 2017/11/17
 */
class Response{
    public function send($data){
        header('Access-Control-Allow-Origin:*');
//        header('Content-Type: application/json; charset=utf-8');
//        header('HTTP/1.1 200 OK');
//        header('Content-language: cn');
        echo $data;
    }
}