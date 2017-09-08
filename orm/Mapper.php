<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月31日
 * Time: 14:09
 */

abstract class Mapper {

    public static $pdo;

    public function __construct($dsn)
    {
        if (!$dsn) {
            throw new Exception('dsn can not empty');
        } else {
            self::$pdo = new PDO($dsn);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    /**
     * @param $id
     * @return mixed|null
     * @throws Exception
     */
    public function find($id)
    {
        if (!$id) {
            throw new Exception('param error');
        } else {
            $this->selectStmt()->execute(array($id));
            $res = $this->selectStmt()->fetch();
            $this->selectStmt()->closeCursor();
            if (!$res) {
                return null;
            } else {
                return $this->createObject($res);
            }
        }
    }

    /**
     * 插入数据，具体插入动作委托各个子类完成
     * @param $sql
     * @return mixed
     */
    public function insert($sql)
    {
        return $this->doInsert($sql);
    }

    /**
     * 将数据库查询结果集转换为对象，具体实现过程委托给各个子类来完成
     * @param $array
     * @return mixed
     */
    public function createObject($array)
    {
        return $this->doCreateObject($array);
    }

    /**
     * @return PDOStatement
     */
    protected abstract function selectStmt();

    /**
     * @param $sql
     * @return mixed
     */
    protected abstract function doInsert($sql);

    /**
     * @param array $array
     * @return mixed
     */
    protected abstract function doCreateObject(array $array);
}