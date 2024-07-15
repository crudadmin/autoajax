<?php

namespace AutoAjax;

trait EventsTrait
{
    /*
     * AutoAjax events
     */
    static $events = [
        'onResponse' => [], // fn($response, $autoAjax) => {}
        'onMessage' => [], // fn($autoAjax) => {}
        'onSuccess' => [], // fn($autoAjax, $message) => {}
        'onError' => [], // fn($autoAjax, $message, $code) => {}
    ];

    /**
     * Set eventn
     *
     * @param  string  $type
     * @param  callable  $callback
     */
    static function setEvent($type, callable $callback)
    {
        self::$events[$type][] = $callback;
    }

    public function getEvents()
    {
        return self::$events;
    }

    public function runEvent($eventName, array $arguments)
    {
        $events = $this->getEvents()[$eventName] ?? [];;

        foreach ($events as $event) {
            $event(...$arguments);
        }
    }
}
