<?php
namespace server\http\controller;

use common\lib\CResponse;

class Test extends Controller{

    public function view()
    {
        $this->responseType = CResponse::JSON;
        return [1,2,3,4,5,6];
    }

    function update()
    {
        // TODO: Implement update() method.
    }

    function add()
    {
        // TODO: Implement add() method.
    }

    function delete()
    {
        // TODO: Implement delete() method.
    }


}