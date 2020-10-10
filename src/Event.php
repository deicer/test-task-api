<?php

declare(strict_types=1);


namespace App;


class Event
{
    public int $id;
    public int $accountId;

    public function __construct(int $id,int $accountId)
    {
        $this->id = $id;
        $this->accountId = $accountId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }
}