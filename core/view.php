<?php

namespace core;

class View
{
    function render($contentView, $data = null)
    {
        include 'public/template.php';
    }
}