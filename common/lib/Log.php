<?php
namespace common\lib;
class Log {

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

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        if(!is_dir($this->filePath)){
            mkdir($this->filePath,0777,true);
        }
    }

    public function notice(string $str){
        $this->content .=  "[".date("Y-m-d H:i:s")."] NOTICE:".$str.PHP_EOL;
    }

    public function warning(string $str){
        $this->content .= "[".date("Y-m-d H:i:s")."] WARNING:".$str.PHP_EOL;
    }

    /**
     * @throws \Exception
     * 日志写入
     */
    public function write(){
        $path  = $this->filePath;
        $fileName = date("Y-m-d");
        $file = $path."/".$fileName;
        if(!$handle = fopen($file,'a+')){
            throw new \Exception('无法打开日志文件');
        };
        if(flock($handle,LOCK_EX)){
            fwrite($handle,$this->content);
            flock($handle,LOCK_UN);
        }
        fclose($handle);
        $this->content = '';
    }

    /**
     * @param string $str
     * @throws \Exception
     */
    public function noticeNow(string $str){
        $this->notice($str);
        $this->write();
        $this->content = '';
    }


}
