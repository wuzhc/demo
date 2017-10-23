<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2016/9/13
 * Time: 8:39
 */

namespace mongo;

defined('MONGO_DB') or define('MONGO_DB', 'test');
defined('MONGO_HOST') or define('MONGO_HOST', '127.0.0.1');
defined('MONGO_PORT') or define('MONGO_PORT', '27017');

/**
 * Class CMongo
 */
class CMongo
{
    /** @var \MongoClient */
    private static $conn;
    /** @var \MongoDB */
    private static $db;
    /** @var CMongo */
    private static $instance;
    
    private function __construct()
    {
        try {
            self::$conn = new \MongoClient(sprintf('mongodb://%s:%s', MONGO_HOST, MONGO_PORT));
        } catch (\MongoConnectionException $e) {
            exit($e->getMessage());
        }

        $this->selectMongoDB(MONGO_DB);
    }

    /**
     * @return CMongo|\MongoClient
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new CMongo();
        }
        return self::$instance;
    }

    public static function reset()
    {
        self::$instance = null;
    }
    
    /**
     * 显示数据库
     * @return array
     */
    public function listDBs()
    {
        $result = [];

        $dbs = self::$conn->listDBs();
        foreach ((array)$dbs['databases'] as $db) {
            $result[] = $db;
        }

        return $result;
    }

    /**
     * 返回连接信息
     * @return array
     */
    public function getConnections()
    {
        return self::$conn->getConnections();
    }

    /**
     * 删除数据库
     * @param string $db
     * @return bool
     */
    public function dropDB($db = '')
    {
        $db and $this->selectMongoDB($db);
        $result = self::$db->drop();
        return $result['ok'] == 1 ? true : false;
    }

    /**
     * mongo版本
     * @return string
     */
    public function getVersion()
    {
        return \MongoClient::VERSION;
    }

    /**
     * 选择mongo数据库
     * @param $db
     * @since 2016-09-13
     */
    public function selectMongoDB($db)
    {
        if (!empty($db)) {
            exit('DB can not empty');
        }
        self::$db = self::$conn->selectDB($db);
    }

    /**
     * 新建集合
     * @param $name
     * @return bool
     */
    public function createCollection($name)
    {
        try {
            self::$db->createCollection($name);
            return true;
        } catch (\MongoException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 选择集合
     * @param $name
     * @return \MongoCollection
     */
    public function selectCollection($name)
    {
        try {
            return self::$db->selectCollection($name);
        } catch (\MongoException $e) {
            exit($e->getMessage());
        }
    }

    //如果没有集合，插入数据会怎么样
    /**
     * 插入数据
     * @param string $collection 集合名称
     * @param array $document 文档
     * @return bool
     * @since 2016-09-13
     */
    public function insert($collection, $document)
    {
        try {
            self::$db->selectCollection($collection)->insert($document);
            return true;
        } catch (\MongoException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @param string $collection
     * @param array $documents
     */
    public function batchInsert($collection, array $documents)
    {
        foreach ((array)$documents as $document) {
            $this->insert($collection, $document);
        }
    }

    /**
     * 删除文档
     * @param $name
     * @param $condition
     * @param array $option 选项 e.g.
     * justOne : （可选）如果设为 true 或 1，则只删除一个文档。
     * writeConcern :（可选）抛出异常的级别。
     * @return bool
     */
    public function remove($name, $condition = [], $option = [])
    {
        try {
            self::$db->$name->remove($condition, $option);
            return true;
        } catch (\MongoException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 查询单挑数据
     * @param string $name 集合名称
     * @param array $condition
     * @param array $field 需要返回的字段
     * @return array|bool|null
     */
    public function findOne($name, $condition = [], $field = [])
    {
        try {
            return self::$db->$name->findOne($condition, $field);
        } catch (\MongoException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 查询集合文档条件
     * @param string $name 集合名称
     * @param array $condition e.g.
     * <pre>
     *      ['id' => ['$gt' => 1, '$lt' => 2]], //范围查询，如果id是个数组，这种方式并不适用
     *      ['id' => ['$eleMatch' => ['$gt' => 1, '$lt' => 2]]], //只支持数组范围查询,min(),max()
     *      ['id' => ['$where' => 'function(){return this.id == 1}']], //尽量避免这种使用方式
     *      ['id' => ['$in' => [1,2,3]]],
     *      ['$or' => [['id' => 1], ['id' => 2]],
     *      ['$and' => [['id' => 1], ['id' => 2]],
     *      ['name' => ['$in' => [null], '$exists' => true]], //查询null字段
     *      ['name' => ['$regex' => new MongoRegex("/^$search/")]], //正则查找
     *      ['name' => ['$all' => ['wuzhc', 'heheda']]], //数组查询，同时匹配多个元素，顺序无关
     *      ['comments' => ['$eleMatch' => ['author'=>'joe','score'=>['$gte'=>5]]]], //内嵌文档查询，$eleMatch将限定条件分组
     * </pre>
     * @param array $options 其他选项
     * <pre>
     *      ['select' => ['username' => 1, 'age' => 1]], //需要返回字段, PS: '_id' => 0不返回_id
     *      ['limit' => 10], //查询条数
     *      ['sort' => ['time' => -1]], //排序
     *      ['skip' => '10'], //跳过数量过多的话会有性能问题，不建议用于数量较多的分页
     * </pre>
     * @return \MongoCursor
     */
    public function findCriteria($name, $condition = [], $options = [])
    {
        try {
            $cursor =  self::$db->$name->find($condition, (array)$options['select']);
            if (is_numeric($options['limit']) && $options['limit'] > 0) {
                $cursor->limit($options['limit']);
            }
            if (is_numeric($options['skip']) && $options['skip'] > 0) {
                $cursor->skip($options['skip']);
            }
            if (isset($options['sort']) && is_array($options['sort'])) {
                $cursor->sort($options['sort']);
            }
            return $cursor;
        } catch (\MongoException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 查询集合
     * @see findCriteria()
     * @param $name
     * @param array $condition
     * @param array $options
     * @return array
     * @since 2016-09-17
     */
    public function find($name, $condition = [], $options = [])
    {
        $cursor = $this->findCriteria($name, $condition, $options);
        return iterator_to_array($cursor);
    }

    /**
     * @deprecated
     * 高级查询
     * @see findCriteria()
     * @param $name
     * @param array $condition
     * @param array $options
     * @param array $specialOpt
     * <pre>
     *      ['$maxscan' => 10], //指定扫描文档上限
     *      ['$showDiskLoc' => true], //显示结果在磁盘的位置
     * </pre>
     * @return array
     */
    public function specialFind($name, $condition = [], $options = [], $specialOpt = [])
    {
        $cursor = $this->findCriteria($name, $condition, $options);
        if ($specialOpt) {
            while (list($opt, $value) = each($specialOpt)) {
                $cursor->_addSpecial($opt, $value);
            }
        }
        return iterator_to_array($cursor);
    }

    /**
     * 为查询添加快照，以保证数据一致性
     * 数据处理过程（查询修改再保存），如果修改后的文档体积增大
     * 原有的预留空间不够，mongoDB会将体积增大后的文档往末尾挪动，这样游标可能会返回
     * 体积增大后的文档，导致数据不一致
     *
     * Note:使用快照会使查询变慢
     * @see findCriteria()
     * @param $name
     * @param array $condition
     * @param array $options
     * @return \MongoCursor
     */
    public function snapshot($name, $condition = [], $options = [])
    {
        $cursor = $this->findCriteria($name, $condition, $options);
        return $cursor->snapshot();
    }

    /**
     * @param $name
     * @param array $newData
     *      ['$inc' => ['num' => 1]],           //自增加一
     *      ['$set' => ['name' => 'wuzhc']],    //更新指定字段，如果没有这个，整个文档将被替换
     *      ['name' => 'wuzhc']
     *
     * @param array $condition 查询条件
     * @param array $options 选项 e.g.
     *
     * upsert : 可选，这个参数的意思是，如果不存在update的记录，是否插入objNew,true为插入，默认是false，不插入。
     * multi : 可选，mongodb 默认是false,只更新找到的第一条记录，如果这个参数为true,就把按条件查出来多条记录全部更新。
     * writeConcern :可选，抛出异常的级别。
     * @return bool
     */
    public function update($name, $newData = [], $condition = [], $options = [])
    {
        try {
            return self::$db->$name->update($condition, $newData, $options);
        } catch (\MongoException $e) {
            exit($e->getMessage());
        }
    }

    public function command($args = [])
    {
        return self::$db->command(
            array(
                'text' => 'goods', //this is the name of the collection where we are searching
                'search' => '呵呵', //the string to search
                'limit' => 5, //the number of results, by default is 1000
                /*'project' => Array( //the fields to retrieve from db
                    'title' => 1
                )*/
            )
        );
    }

    /**
     * hasNext 查看游标是否还有其他结果
     * next 迭代结果
     */
    public function cursor()
    {

    }
}