<?php

if ( ! function_exists('autoAjax') ) {
    function autoAjax()
    {
        //Return laravel or symfony bootstraped depending on used framework
        if ( class_exists(Illuminate\Http\Response::class) ) {
            return new AutoAjax\LaravelBuilder;
        } else {
            return new AutoAjax\SymfonyBuilder;
        }
    }
}