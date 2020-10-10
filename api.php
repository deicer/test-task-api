<?php

declare(strict_types=1);


use App\Event;
use App\Rabbit;

require __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$rabbit = new Rabbit();

$generatedEventsCount = 0;

while (true) {
    for ($accountId = 0; $accountId < $_ENV['ACCOUNT_COUNT']; ++$accountId) {
        $events = [];
        $randomEventsCount = random_int(
            (int)$_ENV['EVENT_BATCH_MIN_COUNT'],
            (int)$_ENV['EVENT_BATCH_MAX_COUNT']
        );
        $generatedEventsCount += $randomEventsCount;
        if($generatedEventsCount>$_ENV['EVENT_TOTAL_COUNT']) {
            break 2;
        }

        for ($i = 0; $i < $randomEventsCount; ++$i) {
            $events[] = new Event($i, $accountId);
        }

        $rabbit->publishAccountEvents($accountId, $events);
    }
}

echo $generatedEventsCount.' events published';





