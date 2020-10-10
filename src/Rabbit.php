<?php

declare(strict_types=1);

namespace App;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class Rabbit
{


    private AMQPStreamConnection $AMQPStreamConnection;
    private AMQPChannel $channel;
    private ?array $declaredQueues = [];

    public function __construct()
    {
        $this->AMQPStreamConnection = new AMQPStreamConnection(
            $_ENV['RABBIT_HOST'],
            $_ENV['RABBIT_PORT'],
            $_ENV['RABBIT_LOGIN'],
            $_ENV['RABBIT_PASSWORD']
        );
        $this->channel = $this->AMQPStreamConnection->channel();
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->AMQPStreamConnection->close();
    }

    public function publishAccountEvents(int $accountId,array $events): void
    {
        $queueName = 'account-'.$accountId;
        $this->queueDeclare($queueName);

        foreach ($events as $event) {
            $message = new AMQPMessage(
                json_encode($event, JSON_THROW_ON_ERROR),
                ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
            );

            $this->channel->basic_publish($message, '', $queueName);
        }
    }

    private function queueDeclare(string $queueName): void
    {
        if (!in_array($queueName, $this->declaredQueues, true)) {
            $this->channel->queue_declare(
                $queueName,
                false,
                true,
                false,
                false,
                false
            );

            $this->declaredQueues[] = $queueName;
        }
    }

    public function consume(int $accountId, callable $callback): void
    {
        $queueName = 'account-'.$accountId;

        $this->queueDeclare($queueName);

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume(
            $queueName,
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        while (count($this->channel->callbacks)) {
           $this->channel->wait();
        }
    }

}