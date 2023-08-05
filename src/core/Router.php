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

    public static function get($page, $action, $callback) {
        self::add($page, 'GET', $action, $callback);
    }

    public static function post($page, $action, $callback) {
        self::add($page, 'POST', $action, $callback);
    }

    public static function all($page, $action, $callback) {
        self::add($page, '*', $action, $callback);
    }

    public static function add($page, $method,  $action, $callback) {
        self::$routes[] = [
            'method' => strtoupper($method),
            'page' => $page,
            'action' => $action,
            'callback' => $callback
        ];
    }

    public static function resolve($page) {
        $action = $_GET['action'] ?? $_POST['action'] ?? null;
        $method = self::getMethod();
        foreach (self::$routes as $route) {
            if (self::match($route, $method, $page, $action)) {
                self::call($route['callback']);
                break;
            }
        }
    }

    static function match($route, $method, $page, $action) {
        if (
            ($route['method'] === $method && $route['action'] === $action && $route['page'] === $page) ||
            ($route['method'] === '*' && $route['action'] === $action && $route['page'] === $page) ||
            ($route['method'] === $method && $route['action'] === '*' && $route['page'] === $page) ||
            ($route['method'] === '*' && $route['action'] === '*' && $route['page'] === $page) ||
            ($route['method'] === '*' && $route['action'] === '*' && $route['page'] === '*')
        ) {
            return true;
        }
        return false;
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
