<?php
namespace server\lib;
use \Redis as oRedis;
class Redis extends oRedis{

    public function __construct($config)
    {
        parent::__construct();
        $this->connect($config['host'],$config['port']);
    }

}