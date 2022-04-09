<?php

namespace AutoAjax;

trait MessagesTrait
{
    /*
     * Defined messages
     */
    static $messages = [
        'error' => 'Something went wrong. Try again later.',
        'success' => 'Changes have been successfully saved.',
    ];

    /**
     * Set global messages for autoAjax
     *
     * @param  string  $type
     * @param  string  $message
     */
    public function setMessage($type, $message)
    {
        self::$messages[$type] = $message;
    }

    public function getMessage($type)
    {
        return self::$messages[$type] ?? null;
    }
}
