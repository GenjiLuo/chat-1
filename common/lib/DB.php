<?php
namespace common\lib;
use Medoo\Medoo;

/**
 * Class db;
 * @package common\lib
 * 如果mysql采用长连接，长时间没操作mysql服务器会自动断开连接
 * 此后再查询会发生 mysql server gone away
 * 为了方便，采用短连接的方式，每次使用完后需要关闭连接
 * 这样会带来额外每次连接mysql server的开销
 */
class DB {

    public $medoo;

    public static $tableName ;

    /**
     * DB constructor.
     * @param Medoo $medoo
     */
    public function __construct(Medoo $medoo)
    {
        $this->medoo = $medoo;
    }

    /**
     * @param $where
     * @param $fields
     * @return mixed
     */
    public function selectOne($where,$fields = '*'){
        $class = get_called_class();
        $result = $this->medoo->select($class::$tableName,$fields,$where);
        return $result ? $result[0] :[] ;
    }

    /**
     * @param $where
     * @param $fields
     * @return mixed
     */
    public function selectAll($where,$fields = '*'){
        $class = get_called_class();
        $result = $this->medoo->select($class::$tableName,$fields,$where);
        return $result ? $result :[] ;
    }




    /**
     * @param $data
     * @return bool|\PDOStatement
     */
    public function insert($data){
        $class = get_called_class();
        $this->medoo->insert($class::$tableName,$data);
        return $this->medoo->id();
    }

    /**
     * @param $data
     * @param $where
     * @return bool|\PDOStatement
     */
    public function update($data,$where){
        $class = get_called_class();
        return $this->medoo->update($class::$tableName,$data,$where);
    }

    /**
     * @param $where
     * @return bool|\PDOStatement
     */
    public function delete($where){
        $class = get_called_class();
        $pdo =  $this->medoo->delete($class::$tableName,$where);
        return $pdo->rowCount();
    }

    public function __destruct()
    {
        $this->medoo->close();
    }

}