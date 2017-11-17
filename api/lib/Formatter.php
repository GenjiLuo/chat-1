<?php
namespace api\lib;
/**
 * zjw
 * 2017/11/17
 */
class Formatter{
    public function format($data){
        if (is_array($data)){
            return json_encode($data);
        }
        return $data;
    }
}