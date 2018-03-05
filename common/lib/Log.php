<?php
namespace common\lib;

class Log{

    public $content;

    public function __construct()
    {
        $logFile  = BASE_ROOT."/runtime/log/".date("Y-m-d");
        if(!is_file($logFile)){
            mkdir($logFile,0777,true);
        }
    }

    public function notice(string $str){
        echo "[".date("Y-m-d H:i:s")."] NOTICE:".$str.PHP_EOL;
    }


}
