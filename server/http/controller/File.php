<?php
namespace server\http\controller;

class File extends Auth {
    public function create()
    {
        var_dump($this->request->post);
        var_dump($this->request->files);
    }
}