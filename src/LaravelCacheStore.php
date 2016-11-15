<?php

namespace LeeOvery\Flasher;

use Illuminate\Contracts\Cache\Repository;

class LaravelCacheStore implements CacheStore
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Repository $repository
     */
    function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Check if a key exists in the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->repository->has($key);
    }

    /**
     * Push value onto key in cache.
     *
     * @param                     $key
     * @param                     $data
     * @param \DateTime|float|int $minutes
     */
    public function push($key, $data, $minutes = null)
    {
        $this->repository->put($key, $data, $minutes);
    }
}