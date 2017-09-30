<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月30日
 * Time: 11:50
 */

require_once '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class FibonacciRpcClient
{
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            'localhost', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
        list($this->callback_queue, ,) = $this->channel->queue_declare(
            "", false, false, true, false);
        $this->channel->basic_consume(
            $this->callback_queue, '', false, false, false, false,
            array($this, 'on_response'));
    }

    public function on_response($rep)
    {
        if ($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }

    public function call($n)
    {
        $this->response = null;
        $this->corr_id = uniqid();

        $msg = new AMQPMessage(
            json_encode(['num' => $n]),
            array(
                'correlation_id' => $this->corr_id,
                'reply_to' => $this->callback_queue,
                'content_type' => 'application/json',
            )
        );
        $this->channel->basic_publish($msg, '', 'rpc_queue');
        while (!$this->response) {
            $this->channel->wait();
        }
        return intval($this->response);
    }
}


if (!isset($argv[1]) || empty($argv[1]) || !is_numeric($argv[1])) {
    file_put_contents('php://stderr', "Usage: $argv[0] [num]");
    exit(1);
}

$fibonacci_rpc = new FibonacciRpcClient();
$response = $fibonacci_rpc->call($argv[1]);
echo " [.] Got ", $response, "\n";
