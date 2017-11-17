<?php
namespace api\controllers;
/**
 * zjw
 * 2017/11/17
 */
class Base{

    public $formatter = "\api\lib\Formatter";

    public function afterAction($result){
        return (new $this->formatter)->format($result);
    }

    public function beforeAction(){
        //todo
        return true;
    }


}