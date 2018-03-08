<?php
namespace common\lib;
/**
 * Class MyPDO
 * @package common\lib
 * 如果mysql采用长连接，长时间没操作mysql服务器会自动断开连接
 * 此后再查询会发生 mysql server gone away
 * 为了方便，采用短连接的方式，每次使用完后需要关闭连接
 * 这样会带来额外每次连接mysql server的开销
 */
class Model extends \Mysqli{

    public static $tableName;

    public $sql;

    public $whereString;

    public $whereValues;

    public function __construct($host=DB_HOST,$user= DB_USER,$pwd = DB_PWD,$dbName= DB_NAME,$port= DB_PORT)
    {
        parent::__construct($host,$user,$pwd,$dbName,$port);
    }

    public function field(array $fields){

    }

    public function where(array  $where){
        $this->whereKey = array_keys($where);
    }

    public function update(array $where,array $data){

    }


}