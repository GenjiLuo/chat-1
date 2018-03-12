<?php
class test {
    public $a;
    public function __construct()
    {
        var_dump($this->a);
    }
}
$a = [1,2,3];
$b = &$a;
$b[] = 4;
var_dump($a);
