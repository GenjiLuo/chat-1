<?php
namespace common\lib;

class Log{

    /**
     * @var string
     * 待写入的内容
     */
    public $content;
    /**
     * @var mixed
     * 日志文件路径
     */
    public $filePath;

    public function __construct(array $config)
    {
        $this->filePath = $config['filePath'];
    }

    public function notice(string $str){
        $this->content .= "[".date("Y-m-d H:i:s")."] NOTICE:".$str.PHP_EOL;
    }

    public function warning(string $str){
        $this->content .= "[".date("Y-m-d H:i:s")."] WARNING:".$str.PHP_EOL;
    }

    public function handle(){
        $file = $this->filePath;
        if(!is_dir($file)){
            mkdir($file,0777,true);
        }
        file_put_contents($file."/".date("Y-m-d").".log",$this->content,FILE_APPEND);
        $this->content = "";
    }


}
