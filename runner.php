<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


for ($accountId = 1; $accountId <= $_ENV['ACCOUNT_COUNT']; ++$accountId) {
    echo 'run worker for account #'.$accountId.PHP_EOL;
    exec('php worker.php -o '.$accountId.' > /dev/null &');
}

echo 'Слушаем и обрабатываем очередь:';

//.' > /dev/null &')