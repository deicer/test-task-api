<?php

declare(strict_types=1);


use App\Rabbit;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PhpAmqpLib\Message\AMQPMessage;

require __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$options = getopt('o:');

$accountId = $options['o'];

$rabbit = new Rabbit();
$worker = new \App\Worker();

$logger = new Logger('test');
$logger->pushHandler(new StreamHandler('log.log', Logger::INFO));

$rabbit->consume(
    (int)$accountId,
    static function (AMQPMessage $message) use ($rabbit, $worker, $logger) {
        $event = json_decode($message->body, true, 512, JSON_THROW_ON_ERROR);

        $worker->run($event);

        $message->delivery_info['channel']->basic_ack(
            $message->delivery_info['delivery_tag']
        );
    }
);



