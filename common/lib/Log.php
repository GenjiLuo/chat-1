<?php
namespace common\lib;

class Log{
    public function notice(string $str){
        echo "[".date("Y-m-d H:i:s")."] NOTICE ".$str.PHP_EOL;
    }
}
