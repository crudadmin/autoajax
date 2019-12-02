<?php

use AutoAjax\AutoAjax;

if ( ! function_exists('autoAjax') ) {
    function autoAjax()
    {
        return (new AutoAjax);
    }
}