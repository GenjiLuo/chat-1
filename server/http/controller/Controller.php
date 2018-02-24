<?php
namespace server\http\controller;

abstract class Controller{
    public function run($request){
        switch ($request->server['request_method']){
            case "GET":
                return $this->view($request);
                break;
            case 'POST':
                return $this->add($request);
                break;
            case 'PUT':
                return $this->update($request);
                break;
            case 'DELETE':
                return $this->delete($request);
                break;
            case 'OPTION':
                return $this->option($request);
        }
    }

    public function option($request){
        return "";
    }

    abstract  function view($request);

    abstract function update($request);

    abstract function add($request);

    abstract function delete($request);
}