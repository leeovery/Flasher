<?php

if (! function_exists('flasher')) {

    /**
     * Arrange for a flash message.
     *
     * @param  string|null $message
     * @return \LeeOvery\Flasher\FlasherNotifier
     */
    function flasher($message = null)
    {
        $flasher = resolve('flasher');

        if (! is_null($message)) {
            return $flasher->info($message);
        }

        return $flasher;
    }

}