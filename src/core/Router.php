<?php

namespace VerifyWoo\Core;

defined('ABSPATH') || exit;

class Router {
    static $routes = [];

    public static function redirect($path) {
        wp_redirect(home_url() . $path);
        exit;
    }

    public static function getMethod() {
        return $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
    }

    public static function get($action, $callback) {
        self::add('GET', $action, $callback);
    }

    public static function post($action, $callback) {
        self::add('POST', $action, $callback);
    }

    public static function add($method, $action, $callback) {
        self::$routes[] = [
            'method' => strtoupper($method),
            'action' => $action,
            'callback' => $callback
        ];
    }

    public static function resolve() {
        $action = $_GET['action'] ?? $_POST['action'] ?? null;
        $method = self::getMethod();
        $matched = false;
        foreach (self::$routes as $route) {
            if ($route['method'] === $method && $route['action'] === $action) {
                self::call($route['callback']);
                $matched = true;
                break;
            }
        }

        if (!$matched) {
            Template::include('actions/message', [
                'type' => 'info',
                'msg' => 'Nothing to do here yet.'
            ]);
        }
    }

    private static function call($controller) {
        if (is_callable($controller)) {
            $controller();
            return;
        }

        if (is_array($controller)) {
            call_user_func([new $controller[0], $controller[1]]);
        }
    }
}
