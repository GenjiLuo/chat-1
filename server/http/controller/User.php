<?php
namespace server\http\controller;

use common\lib\CResponse;

class User extends Controller{

    protected $responseType = CResponse::JSON;

    public function view()
    {
        // TODO: Implement view() method.
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function add()
    {
        $this->response->header("Access-Control-Allow-Origin","*");
        return $this->request->post;
    }

    public function delete()
    {
        return false;
    }

}