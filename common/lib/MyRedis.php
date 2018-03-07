<?php
namespace common\lib;
use \Redis;
class MyRedis extends Redis{

    public function __construct($host,$port)
    {
        parent::__construct();
        $this->connect($host,$port);
    }

    public function __destruct()
    {
        $this->close();
    }

}