<?php

namespace AutoAjax;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

trait AutoAjax
{
    /*
     * Title
     */
    private $title = null;

    /*
     * Message
     */
    private $message = null;

    /*
     * Js callback
     */
    private $callback = null;

    /*
     * Redirect url
     */
    private $redirect = null;

    /*
     * Is modal
     */
    private $modal = false;

    /*
     * Error type
     */
    private $error = false;

    /*
     * Http code
     */
    private $code = 200;

    /*
     * Request data
     */
    private $data = [];

    /*
     * Defined messages
     */
    static $messages = [
        'error' => 'Something went wrong. Try again later.',
        'success' => 'Changes has been successfully saved.',
    ];

    /*
     * AutoAjax events
     */
    static $events = [
        'onResponse' => null,
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

    /**
     * Set global messages for autoAjax
     *
     * @param  string  $type
     * @param  string  $message
     */
    public function setGlobalMessage($type, $message)
    {
        self::$messages[$type] = $message;
    }

    /**
     * Set title response
     *
     * @param  string  $title
     * @return this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set success message response
     *
     * @param  string  $message
     * @return this
     */
    public function message($message)
    {
        $this->message = $message;
        $this->error = false;

        return $this;
    }

    /**
     * Set success message response
     *
     * @param  string  $message
     * @return this
     */
    public function success($message = null)
    {
        return $this->message($message);
    }

    /**
     * Set error message response
     *
     * @param  string  $message
     * @param  integer $code
     * @return this
     */
    public function error($message = null, $code = null)
    {
        $this->message($message ?: self::$messages['error']);

        $this->error = true;

        if ( $code ) {
            $this->code = $code;
        }

        return $this;
    }

    /**
     * Set code of response
     *
     * @param  integer $code
     * @return this
     */
    public function code($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Set javascript callback after response
     *
     * @param  string  $callback
     * @return this
     */
    public function callback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Set redirect after response
     *
     * @param  string  $redirect
     * @return this
     */
    public function redirect($redirect)
    {
        $this->redirect = $redirect;

        return $this;
    }

    /**
     * Set additional data
     *
     * @param  mixed/array  $data
     * @return this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Return message with modal type
     *
     * @param  null|string  $message
     * @return this
     */
    public function modal($message = null)
    {
        if ( $message )
            $this->message = $message;

        $this->modal = true;

        return $this;
    }

    /**
     * Reload webpage after response
     *
     * @return this
     */
    public function reload()
    {
        $this->callback .= ';window.location.reload()';

        return $this;
    }

    /**
     * Send success save message
     *
     * @param  null|string  $save
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function save($message = null)
    {
        return $this->message($message ?: self::$messages['success']);
    }

    /**
     * Alias
     *
     * @param  string  $message
     * @return  Symfony\Component\HttpFoundation\Response
     */
    public function saved($message = null)
    {
        return $this->save($message);
    }

    /**
     * Build JSON response
     *
     * @return  array
     */
    public function getResponse()
    {
        $response = [
            'title' => $this->title ?: ($this->error ? _('Whoopsie :( !') : _('Success')),
            'redirect' => $this->redirect,
            'callback' => $this->callback,
            'type' => $this->modal ? 'modal' : 'message',
            'error' => $this->error,
            'message' => $this->message,
            'data' => $this->data,
        ];

        if ( is_callable(self::$events['onResponse']) ){
            $response = self::$events['onResponse']($response);
        }

        return $response;
    }


    /*
     * Alias for JSON response
     */
    public function render()
    {
        return $this->throw();
    }

    /*
     * Throw JSON response
     */
    public function throw()
    {
        $response = new JsonResponse($this->getResponse(), $this->code);

        die($response->send());
    }

    /**
     * Throw validation error response
     *
     * @param  array  $errors
     * @return void
     */
    public function throwValidation(array $errors = [])
    {
        $response = new JsonResponse($errors, 422);

        die($response->send());
    }

    /**
     * Prepare request for laravel
     *
     * @param  Request  $request
     * @return
     */
    public function prepare(Request $request)
    {
        $this->setContent(json_encode($this->getResponse()));

        $this->setStatusCode($this->code);

        $this->headers->set('Content-Type', 'application/json');

        return parent::prepare($request);
    }
}

?>