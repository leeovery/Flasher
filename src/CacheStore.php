<?php

namespace LeeOvery\Flasher;

interface CacheStore
{

    /**
     * Check if a key exists in the session.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Push array onto key in cache.
     *
     * @param                     $key
     * @param                     $data
     * @param \DateTime|float|int $minutes
     */
    public function push($key, $data, $minutes = null);
} 