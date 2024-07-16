<?php

namespace AutoAjax;

use AutoAjax\EventsTrait;
use AutoAjax\MessagesTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AutoAjax extends Response
{
    use EventsTrait,
        MessagesTrait;

    /*
     * Title
     */
    public $title = null;

    /*
     * Message
     */
    public $message = null;

    /*
     * Js callback
     */
    public $callback = null;

    /*
     * Redirect url
     */
    public $redirect = null;

    /*
     * Is modal
     */
    public $modal = false;

    /*
     * Error type
     */
    public $error = false;

    /*
     * Http code
     */
    public $code = 200;

    /*
     * Request data
     */
    public $data = [];

    /*
     * Store data
     */
    public $store = [];

    /*
     * Response type
     */
    public $type;

    /**
     * On auto ajax boot
     */
    public function boot(){}

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

        $this->runEvent('onMessage', [$this, $message]);

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
        $this->runEvent('onSuccess', [$this, $message]);

        $this->message($message);

        return $this;
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
        $this->runEvent('onError', [$this, $message, $code]);

        $this->message($message ?: $this->getMessage('error'));

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
     * Set store store
     *
     * @param  mixed/array  $data
     * @return this
     */
    public function store($store)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Set response type
     *
     * @param  null|string  $type
     *
     * @return this
     */
    public function type($type = null)
    {
        if ( $type ) {
            $this->type = $type;
        }

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
        return $this->message($message ?: $this->getMessage('success'));
    }

    /**
     * Build JSON response
     *
     * @return  array
     */
    public function getResponse()
    {
        $response = [];

        $addKeys = [
            'type', 'title', 'message', 'redirect', 'callback', 'error', 'data'
        ];

        foreach ($addKeys as $key) {
            $value = $this->{$key} ?? null;

            if ( is_null($value) === false ){
                $response[$key] = $value;
            }
        }

        $response = $this->addStoreIntoResponse($response);

        $response = $this->runEvent('onResponse', [&$response, $this], 1);

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
    public function prepare(Request $request) :static
    {
        $this->setContent(json_encode($this->getResponse()));

        $this->setStatusCode($this->code);

        $this->headers->set('Content-Type', 'application/json');

        return parent::prepare($request);
    }

    private function addStoreIntoResponse($response)
    {
        $storePath = config('autoajax.store_path', 'store');

        //Store support added
        if ( config('autoajax.store', false) ){
            Arr::set($response, $storePath, $this->store);
        } else if ( is_array($this->store) && count($this->store) ) {
            $response['data'] = array_merge($this->store, $response['data']);
        }

        return $response;
    }
}

?>