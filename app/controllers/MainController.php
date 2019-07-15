<?php

namespace app\controllers;

use core\Controller;

class MainController extends Controller
{
    public function index()
    {
        $this->view->render('/main/index.php');
    }
}
