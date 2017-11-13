<?php
namespace lib;
use \Redis;
class MyRedis extends Redis{

    public function __construct($config)
    {
        parent::__construct();
        $this->connect($config['host'],$config['port']);
    }

}