<?php

namespace App\Core;

use Illuminate\Contracts\Support\Renderable;

class AutoAjax implements Renderable
{
    private $title = null;

    private $message = null;

    private $callback = null;

    private $redirect = null;

    private $modal = false;

    private $error = false;

    private $code = 200;

    private $data = [];

    /*
     * Set title response
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /*
     * Set success message response
     */
    public function message($message)
    {
        $this->message = $message;
        $this->error = false;

        return $this;
    }

    public function success($message = null)
    {
        if ( ! $message )
            $message = _('Changes has been successfully saved.');

        return $this->message($message);
    }

    /*
     * Set error message response
     */
    public function error($message)
    {
        $this->message = $message;
        $this->error = true;

        return $this;
    }

    /*
     * Set callback after response, or modal close
     */
    public function callback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /*
     * Set redirect after response
     */
    public function redirect($redirect)
    {
        $this->redirect = $redirect;

        return $this;
    }

    /*
     * Set additional data
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /*
     * Set modal
     */
    public function modal($message = null)
    {
        if ( $message )
            $this->message = $message;

        $this->modal = true;

        return $this;
    }

    /*
     * Reload afer callback close
     */
    public function reload()
    {
        $this->callback .= ';window.location.reload()';

        return $this;
    }

    public function render()
    {
        return $this->throw();
    }

    public function save()
    {
        return $this->message(_('Změny byly úspěšně uloženy.'));
    }

    public function getResponse()
    {
        $response = [
            'title' => $this->title ?: ($this->error ? _('Whoopsie :( !') : _('Success')),
            'redirect' => $this->redirect,
            'callback' => $this->callback,
            'type' => $this->modal ? 'modal' : 'normal',
            'error' => $this->error,
            'message' => $this->message,
            'data' => $this->data,
        ];

        return $response;
    }

    public function throw()
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException( response()->json($this->getResponse(), $this->code), $this->code );
    }
}

?>