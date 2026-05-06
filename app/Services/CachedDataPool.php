<?php

namespace App\Services;

use App\Contracts\DataPoolInterface;

class CachedDataPool implements DataPoolInterface
{
    public function remember(string $pool, string $signature, callable $resolver, int $ttl = 30): mixed
    {
        try {
            $cache = cache();
            $version = (int) ($cache->get($this->versionKey($pool)) ?? 1);
            $cacheKey = $this->dataKey($pool, $version, $signature);

            $cached = $cache->get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }

            $value = $resolver();
            $cache->save($cacheKey, $value, $ttl);

            return $value;
        } catch (\Throwable $e) {
            return $resolver();
        }
    }

    public function bump(string $pool): void
    {
        try {
            $cache = cache();
            $versionKey = $this->versionKey($pool);
            $current = (int) ($cache->get($versionKey) ?? 1);
            $cache->save($versionKey, $current + 1, 0);
        } catch (\Throwable $e) {
            // Cache invalidation should never break request flow.
        }
    }

    private function versionKey(string $pool): string
    {
        return 'dbpool:version:' . $pool;
    }

    private function dataKey(string $pool, int $version, string $signature): string
    {
        return 'dbpool:data:' . $pool . ':v' . $version . ':' . sha1($signature);
    }
}
