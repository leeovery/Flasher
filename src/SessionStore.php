<?php

namespace LeeOvery\Flasher;

interface SessionStore
{

    /**
     * Flash a message to the session.
     *
     * @param $name
     * @param $data
     */
    public function flash($name, $data);

    /**
     * Flash a message to the session for use in this request.
     *
     * @param $name
     * @param $data
     */
    public function now($name, $data);

    /**
     * Check if a key exists in the session.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Get data from the session.
     *
     * @param string $key
     *
     * @param array  $default
     * @return mixed
     */
    public function get($key, $default = []);

    /**
     * Push array onto key in session.
     *
     * @param $key
     * @param $data
     * @return void
     */
    public function push($key, $data);

    /**
     * Retrieve & delete item from session.
     *
     * @param $key
     * @param $default
     * @return mixed
     */
    public function pull($key, $default = null);
} 