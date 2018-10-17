<?php
declare(strict_types=1);

use Tomaj\Hermes\Driver\RabbitMqDriver;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Tomaj\Hermes\Dispatcher;
use Tomaj\Hermes\Handler\EchoHandler;

require_once __DIR__.'/../../vendor/autoload.php';

$queueName = 'hermes_queue';
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest', '/guest');
$channel = $connection->channel();
$channel->queue_declare($queueName, false, false, false, false);
$driver = new RabbitMqDriver($channel, $queueName);


$dispatcher = new Dispatcher($driver);

$dispatcher->registerHandler('type1', new EchoHandler());

$dispatcher->handle();
