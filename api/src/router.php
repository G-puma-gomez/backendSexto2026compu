<?php

namespace App;
class router{
    protected $router=[];
    public function add($method,$route,$handler)
    {
        $this->router[]=[
            'method'=>$method,
            'route'=>$route,
            'handler'=>$handler,
        ];
    }

    public function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        foreach ($this->router as $route)
            {
                $pattern = str_replace(['{id}','/'], ['([0-9]+)','\/'], $route['route']);
                $pattern = '/^'.$pattern.'$/';
                if ($method === $route['method'] && preg_match($pattern, $uri, $matches))
                    {  array_shift($matches);
                        list($controllerName, $methodName) = explode('@', $route['handler']);
                        $controller = new $controllerName();
                        return call_user_func_array([$controller, $methodName], $matches);

                    }
            }
            // si no encontro
            http_response_code(404);
            echo json_encode(["error"=>'Ruta no encontrada']);
    }
}