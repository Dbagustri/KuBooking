<?php
namespace App\Core;

class Router
{
    public function dispatch()
    {
        $controller = $_GET['controller'] ?? 'auth';
        $action     = $_GET['action'] ?? 'landing';

        $controllerName = "App\\Controllers\\" . ucfirst($controller) . "Controller";

        if (!class_exists($controllerName)) {
            http_response_code(404);
            echo "Controller {$controllerName} tidak ditemukan";
            exit;
        }

        $controllerObject = new $controllerName();

        if (!method_exists($controllerObject, $action)) {
            http_response_code(404);
            echo "Action {$action} tidak ditemukan di controller {$controllerName}";
            exit;
        }

        call_user_func([$controllerObject, $action]);
    }
}
