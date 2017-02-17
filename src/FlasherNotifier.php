<?php

namespace LeeOvery\Flasher;

use BadMethodCallException;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FlasherNotifier
{
    /**
     * @var SessionStore
     */
    private $session;

    /**
     * @var CacheStore
     */
    private $cache;

    /**
     * Session key under which the flash
     * notifications are stored.
     *
     * @var string
     */
    private $sessionKey;

    private $title               = null;

    private $now                 = false;

    private $override            = false;

    private $overrideAllTypes    = false;

    private $blockFutureMessages = false;

    private $config;

    /**
     * @var Collection
     */
    private $existingMessages;

    private $payload;

    private $frequencyCap = null;

    private $frequencyKey;

    private $frequencies  = [
        'session',
        'minute',
        'hour',
        'day',
        'week',
        'month',
        'year',
    ];

    /**
     * Create a new flash notifier instance.
     *
     * @param SessionStore $session
     * @param CacheStore   $cache
     * @param              $config
     */
    function __construct(SessionStore $session, CacheStore $cache, $config)
    {
        $this->session = $session;
        $this->cache = $cache;
        $this->config = $config;
        $this->sessionKey = $config['session_key'];
        $this->frequencyKey = $config['frequency_key'];
    }

    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Flash an information message.
     *
     * @param string $message
     * @return $this
     */
    public function info($message)
    {
        $this->message($message, 'info');

        return $this;
    }

    /**
     * Flash a general message.
     *
     * @param  string $message
     * @param  string $level
     */
    public function message($message, $level = 'info')
    {
        $this->existingMessages = $this->pullExistingMessages();

        if ($this->canFlash($level)) {

            $this->makeFlashPayload($message, $level);

            $this->flashPayload();
        }

        // add back existing
        $this->existingMessages->each(function ($message) {
            $this->session->push($this->sessionKey, $message);
        });
    }

    /**
     * Get messages that have already been flashed.
     *
     * @return Collection
     */
    private function pullExistingMessages()
    {
        return collect($this->session->pull($this->sessionKey));
    }

    /**
     * Is this new message permitted based on previous message
     * override settings.
     *
     * Returns true if can flash message based on previous
     * override settings in other flash messages.
     *
     * @param $level
     * @return bool
     */
    private function canFlash($level)
    {
        return $this->existingMessages->filter(function ($item) use ($level) {
            return
                array_get($item, 'override') &&
                array_get($item, 'blockFutureMessages') &&
                (array_get($item, 'overrideAllTypes') || array_get($item, 'level') === $level);
        })->isEmpty();
    }

    /**
     * Creates flasher payload based on setters used.
     *
     * @param $message
     * @param $level
     */
    private function makeFlashPayload($message, $level)
    {
        $this->payload = [
            'message' => $message,
            'level'   => $level,
        ];

        if (! is_null($this->title)) {
            $this->payload += ['title' => $this->title];
        }

        if (! is_null($this->frequencyCap)) {
            $this->payload += ['frequency_cap' => $this->frequencyCap];
        }

        if ($this->override) {
            $this->payload += [
                'override'            => true,
                'overrideAllTypes'    => $this->overrideAllTypes,
                'blockFutureMessages' => $this->blockFutureMessages,
            ];
        }

        $this->payload += ['hash' => md5(serialize($this->payload))];
    }

    /**
     * Flashes payload to session based on settings.
     */
    private function flashPayload()
    {
        if ($this->isDuplicate() || $this->hasFrequencyCapping()) {
            return;
        }

        $this->setOverrideIfApplicable();

        if ($this->now) {
            $this->session->now($this->sessionKey, [$this->payload]);
        } else {
            $this->session->flash($this->sessionKey, [$this->payload]);
        }
    }

    private function isDuplicate()
    {
        return $this->existingMessages->contains('hash', $this->payload['hash']);
    }

    private function hasFrequencyCapping()
    {
        if (is_null($this->frequencyCap)) {
            return false;
        }

        $key = $this->frequencyKey . ':' . $this->payload['hash'];

        if (! $capped = $this->isFrequencyCapped($key)) {
            $this->cap($key);
        }

        return $capped;
    }

    /**
     * @param $key
     * @return bool
     */
    private function isFrequencyCapped($key)
    {
        if ($this->frequencyCap['frequency'] == 'session') {
            return $this->session->has($key);
        }

        return $this->cache->has($key);
    }

    private function cap($key)
    {
        if ($this->frequencyCap['frequency'] == 'session') {
            $this->session->push($key, $this->payload['hash']);
        }

        $this->cache->push(
            $key,
            $this->payload['hash'],
            (new DateTime)->setTimestamp(
                strtotime(sprintf('+%d %s', $this->frequencyCap['number'], $this->frequencyCap['frequency']))
            )
        );
    }

    private function setOverrideIfApplicable()
    {
        // is this message an override?
        if ($this->override) {

            // If fullOverride is true then wipe and block all other messages
            // of all types. If fullOverride is false then just effect this
            // message type.
            if ($this->overrideAllTypes === true) {

                // Bin off all existing
                $this->session->flash($this->sessionKey, []);
                $this->session->now($this->sessionKey, []);

                $this->existingMessages = collect();

            } else {

                // Reject any previous messages of this type
                $this->existingMessages = $this->existingMessages->reject(
                    function ($item) {
                        return $item['level'] == $this->payload['level'];
                    }
                );

            }
        }
    }

    /**
     * Flash a success message.
     *
     * @param  string $message
     * @return $this
     */
    public function success($message)
    {
        $this->message($message, 'success');

        return $this;
    }

    /**
     * Flash an error message.
     *
     * @param  string $message
     * @return $this
     */
    public function error($message)
    {
        $this->message($message, 'error');

        return $this;
    }

    /**
     * Flash a warning message.
     *
     * @param  string $message
     * @return $this
     */
    public function warning($message)
    {
        $this->message($message, 'warning');

        return $this;
    }

    public function now()
    {
        $this->now = true;

        return $this;
    }

    /**
     * This will override all other messages. By default it'll block any
     * future messages added, and will also remove anything previously
     * set. Can pass false as first param to override only messages
     * of this type (i.e. success, info etc). Can also pass false
     * as second param to prevent blocking any future messages.
     *
     * @param bool $overrideAllTypes
     * @param bool $blockFutureMessages
     * @return $this
     */
    public function override($overrideAllTypes = true, $blockFutureMessages = true)
    {
        $this->override = true;
        $this->overrideAllTypes = $overrideAllTypes;
        $this->blockFutureMessages = $blockFutureMessages;

        return $this;
    }

    /**
     * @param $method
     * @param $parameters
     * @return $this
     * @throws BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'oncePer')) {
            return $this->oncePer(strtolower(substr($method, 7)), $parameters[0] ?? null);
        }

        throw new BadMethodCallException("Call to undefined method FlasherNotifier::{$method}()");
    }

    /**
     * Allows you to limit the number of times this flash message
     * shows to the user. You can also set the number which will
     * be relative to the frequency used. i.e. if freq is set
     * to 'day' and number is set to 2, then the cap will be
     * 2 days. Number if ignored when session is set as the
     * frequency value.
     *
     * Frequency caps available:
     *  - session, minute, hour, day, week, month, year
     *
     * Use-cases:
     * flasher()->oncePerDay(5)
     * flasher()->oncePer('day', 3)
     * flasher()->oncePerSession()
     * flasher()->oncePerWeek(2)
     *
     * @param string $frequency
     * @param int    $number
     * @return $this
     */
    public function oncePer($frequency, $number = null)
    {
        if (in_array($frequency, $this->frequencies)) {
            $this->frequencyCap = [
                'frequency' => $frequency,
                'number'    => $number ?? 1,
            ];
        }

        return $this;
    }
}
