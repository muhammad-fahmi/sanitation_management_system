<?php

namespace App\Contracts;

interface DataPoolInterface
{
    public function remember(string $pool, string $signature, callable $resolver, int $ttl = 30): mixed;

    public function bump(string $pool): void;
}
