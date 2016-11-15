<?php

namespace LeeOvery\Flasher;

use Illuminate\Session\Store;

class LaravelSessionStore implements SessionStore {

    /**
     * @var Store
     */
    private $session;

    /**
     * @param Store $session
     */
    function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Flash a message to the session for the next request.
     *
     * @param $name
     * @param $data
     */
    public function flash($name, $data)
    {
        $this->session->flash($name, $data);
    }

    /**
     * Flash a message to the session for use in this request.
     * 
     * @param $name
     * @param $data
     */
    public function now($name, $data)
    {
        $this->session->now($name, $data);
    }

    /**
     * Check if a key exists in the session.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->session->has($key);
    }

    /**
     * Get data from the session.
     *
     * @param string $key
     *
     * @param array  $default
     * @return mixed
     */
    public function get($key, $default = [])
    {
        return $this->session->get($key, $default);
    }

    /**
     * Push array onto key in session.
     *
     * @param $key
     * @param $data
     * @return void
     */
    public function push($key, $data)
    {
        $this->session->push($key, $data);
    }

    /**
     * Retrieve & delete item from session.
     *
     * @param $key
     * @param $default
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        return $this->session->pull($key, $default);
    }
}