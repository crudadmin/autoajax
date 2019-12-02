<?php

namespace AutoAjax;

use Symfony\Component\HttpFoundation\JsonResponse;

class AutoAjax
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
    public function save($save = null)
    {
        return $this->message($save ?: self::$messages['success']);
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

        return $response;
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
     * Throw response for laravel controller response
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->throw();
    }
}

?>