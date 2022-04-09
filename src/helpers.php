<?php

if ( ! function_exists('autoAjax') ) {
    function autoAjax()
    {
        $provider = config('autoajax.provider', AutoAjax\AutoAjax::class);
        $provider = new $provider;

        $provider->boot();

        return $provider;
    }
}