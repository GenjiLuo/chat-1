<?php

namespace server\http\controller;

use Swoole\Http\Request;
use Swoole\Http\Response;
use common\lib\CResponse;
abstract class Controller
{

    protected $request;

    protected $response;

    protected $responseType;

    protected $responseContent;

    public function __construct(Request $request, Response $response)
    {
        $this->responseType = CResponse::HTML;
        $this->request = $request;
        $this->response = $response;
    }

    public function run()
    {
        if($this->beforeAction()){
            switch ($this->request->server['request_method']) {
                case "GET":
                    $this->responseContent =  $this->view();
                    break;
                case 'POST':
                    $this->responseContent =  $this->add();
                    break;
                case 'PUT':
                    $this->responseContent = $this->update();
                    break;
                case 'DELETE':
                    $this->responseContent = $this->delete();
                    break;
                case 'OPTION':
                    $this->responseContent = $this->option();
            }
            $this->formatter();
            $this->afterAction();
            return $this->responseContent;
        }
    }

    public function option()
    {
        return "";
    }

    abstract function view();

    abstract function update();

    abstract function add();

    abstract function delete();

    public function formatter(){
        $this->response->header("Content-Type",$this->responseType.";charset=UTF-8");
        if($this->responseType === CResponse::JSON ){
           $this->responseContent = json_encode($this->responseContent);
        }
    }

    public function afterAction(){
        //do something here
    }

    public function beforeAction() : bool {
        return true;
    }
}