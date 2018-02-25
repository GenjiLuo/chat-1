<?php

namespace server\http\controller;

use Swoole\Http\Request;
use Swoole\Http\Response;
use common\lib\CResponse;
abstract class Controller
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Response
     */
    protected $response;
    /**
     * @var string
     */
    protected $responseType;
    /**
     * @var string
     */
    protected $responseContent;

    /**
     * Controller constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->responseType = CResponse::HTML;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return string
     */
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

    /**
     * @return mixed
     * get method
     */
    abstract function view();

    /**
     * @return mixed
     * put method
     */
    abstract function update();

    /**
     * @return mixed
     * post method
     */
    abstract function add();

    /**
     * @return mixed
     * delete method
     */
    abstract function delete();

    /**
     * content formatter
     */
    public function formatter(){
        $this->response->header("Content-Type",$this->responseType.";charset=UTF-8");
        if($this->responseType === CResponse::JSON ){
           $this->responseContent = json_encode($this->responseContent);
        }
    }

    /**
     * afterAction
     */
    public function afterAction(){
        //do something here
    }

    /**
     * @return bool
     * beforeAction
     * if return false,the action will not be executed
     */
    public function beforeAction() : bool {
        return true;
    }
}