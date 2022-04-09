<?php

namespace AutoAjax;

trait EventsTrait
{
    /*
     * AutoAjax events
     */
    static $events = [
        'onResponse' => null, // fn($response, $autoAjax) => {}
        'onMessage' => null, // fn($autoAjax) => {}
        'onSuccess' => null, // fn($autoAjax, $message) => {}
        'onError' => null, // fn($autoAjax, $message, $code) => {}
    ];

    /**
     * Set eventn
     *
     * @param  string  $type
     * @param  callable  $callback
     */
    static function setEvent($type, callable $callback)
    {
        self::$events[$type] = $callback;
    }

    public function getEvents()
    {
        return self::$events;
    }

    public function getEvent($eventName)
    {
        $event = $this->getEvents()[$eventName] ?? null;

        return $event && is_callable($event) ? $event : null;
    }

    public function runEvent($eventName, array $arguments)
    {
        if ( $event = $this->getEvent($eventName) ){
            return $event(...$arguments);
        }
    }
}
