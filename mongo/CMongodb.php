<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2016/9/13
 * Time: 8:39
 */

namespace mongo;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\Exception;
use MongoDB\Driver\Manager;
use MongoDB\Driver\WriteConcern;
use stdClass;

defined('MONGO_DB') or define('MONGO_DB', 'wuzhc');
defined('MONGO_HOST') or define('MONGO_HOST', '127.0.0.1');
defined('MONGO_PORT') or define('MONGO_PORT', '27017');

/**
 * Class CMongo
 */
class CMongodb
{
    /** @var Manager */
    private static $manager;
    /** @var \MongoDB */
    private static $db;
    /** @var CMongodb */
    private static $instance;
    
    private function __construct()
    {
        try {
            self::$manager = new Manager(sprintf('mongodb://%s:%s', MONGO_HOST, MONGO_PORT));
        } catch (\MongoConnectionException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @return CMongodb
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new CMongodb();
        }
        return self::$instance;
    }

    /**
     * @param array $records 需要插入的数据
     * @param string $namespace db.collection
     * @link http://php.net/manual/en/mongodb-driver-manager.executebulkwrite.php
     * @since 2017-10-23
     */
    public function insert(array $records, $namespace = 'wuzhc.sms')
    {
        $bulk = new BulkWrite(['ordered' => true]);
        foreach ((array)$records as $record) {
            $bulk->insert($record);
        }
        $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);
        try {
            $result = self::$manager->executeBulkWrite($namespace, $bulk, $writeConcern);
        } catch (BulkWriteException $e) {
            $result = $e->getWriteResult();

            // Check if the write concern could not be fulfilled
            if ($writeConcernError = $result->getWriteConcernError()) {
                printf("%s (%d): %s\n",
                    $writeConcernError->getMessage(),
                    $writeConcernError->getCode(),
                    var_export($writeConcernError->getInfo(), true)
                );
            }

            // Check if any write operations did not complete at all
            foreach ($result->getWriteErrors() as $writeError) {
                printf("Operation#%d: %s (%d)\n",
                    $writeError->getIndex(),
                    $writeError->getMessage(),
                    $writeError->getCode()
                );
            }
        } catch (Exception $e) {
            printf("Other error: %s\n", $e->getMessage());
            exit;
        }

        printf("Inserted %d document(s)\n", $result->getInsertedCount());
        printf("Updated  %d document(s)\n", $result->getModifiedCount());
    }

    /**
     * @param $params
     * @param $db
     * @return \MongoDB\Driver\Cursor
     * @link http://php.net/manual/en/mongodb-driver-manager.executecommand.php
     */
    public function command($params, $db)
    {
        $command = new Command($params);
        return self::$manager->executeCommand($db, $command);
    }

    /**
     * 聚合管道
     *
     *   $pipeline = [
     *       ['$group' => ['_id' => '$phone', 'count' => ['$sum' => 1]]],
     *       ['$sort' => ['count' => -1]],
     *   ];
     *   $cursor = \mongo\CMongodb::instance()->aggregate('wuzhc', 'sms', $pipeline);
     *   foreach ($cursor as $document) {
     *       echo $document->_id . ' : ' . $document->count . PHP_EOL;
     *   }
     *
     * @param string $db
     * @param $collection
     * @param array $pipeline
     * [
     *     ['$group' => ['_id' => '$y', 'sum' => ['$sum' => '$x']]],
     * ],
     * @return \MongoDB\Driver\Cursor
     */
    public function aggregate($db, $collection, array $pipeline)
    {
        $params = [
            'aggregate' => $collection,
            'pipeline' => $pipeline,
            'cursor' => new stdClass,
        ];
        return $this->command($params, $db);
    }
}