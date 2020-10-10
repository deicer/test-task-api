<?php

declare(strict_types=1);


namespace App;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Worker
{


    /**
     * @var Logger
     */
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger('WorkersLog');
        $this->logger->pushHandler(
            new StreamHandler($_ENV['LOG_FILE_PATH'], Logger::INFO)
        );
    }


    public function run(array $event): void
    {
        sleep(1);
        $this->logger->info(
            'Account - '.$event['accountId'].' | Event '.$event['id']
        );
    }
}