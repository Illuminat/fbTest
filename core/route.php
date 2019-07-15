<?php

namespace core;


class Route
{
    static function start()
    {
        $controllerName = 'Main';
        $actionName = 'index';

        $routes = explode('/', $_SERVER['REQUEST_URI']);

        // get the name of the controller
        if (!empty($routes[1])) {
            $controllerName = ucfirst($routes[1]);
        }

        // get the name of the action
        if (!empty($routes[2])) {
            $actionName = $routes[2];
        }

        $controllerName = $controllerName . 'Controller';

        // include controller
        $controllerFile = $controllerName . '.php';
        $controllerPath = "app/controllers/" . $controllerFile;
        if (!file_exists($controllerPath)) {
            self::ErrorPage404();
        }

        include $controllerPath;

        $controllerName = "\\app\\controllers\\" . $controllerName;
        $controller = new $controllerName;
        $action = $actionName;

        if (!method_exists($controller, $action)) {
            self::ErrorPage404();
        }
        $controller->$action();
    }

    static function errorPage404()
    {
        header('HTTP/1.1 404 Not Found');
        die();
    }
}
