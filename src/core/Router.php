<?php

namespace verifywoo\core;

defined('ABSPATH') || exit;

class Router {
    static $routes = [];

    public static function redirect($how, $value, $params = []) {
        if ($how === 'url') {
            wp_redirect($value);
            exit;
        }

        $permalink = self::get_page_permalink($value, $params);
        wp_redirect($permalink);
        exit;
    }

    public static function get_page_permalink($post_name, $search_params = []) {
        $permalink = get_permalink(get_page_by_path($post_name));
        return add_query_arg($search_params, $permalink);
    }

    public static function get_query_string($request = null) {
        $request = $request ?? $_REQUEST;
        $str = '?';
        foreach ($request as $key => $val) {
            $str = $str . $key . '=' . $val . '&';
        }

        return rtrim($str, '&');
    }

    public static function getMethod() {
        return $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
    }

    public static function get($page, $view, $callback) {
        self::add($page, 'GET', $view, $callback);
    }

    public static function post($page, $view, $callback) {
        self::add($page, 'POST', $view, $callback);
    }

    public static function add($page, $method,  $view, $callback) {
        self::$routes[] = [
            'method' => strtoupper($method),
            'page' => $page,
            'view' => $view,
            'callback' => $callback
        ];
    }

    public static function resolve($page) {
        $view = $_GET['view'] ?? $_POST['view'] ?? null;
        $method = self::getMethod();
        foreach (self::$routes as $route) {
            if (self::match($route, $method, $page, $view)) {
                self::call($route['callback']);
                break;
            }
        }
    }

    static function match($route, $method, $page, $view) {
        if (
            ($route['method'] === $method && $route['view'] === $view && $route['page'] === $page) ||
            ($route['method'] === '*' && $route['view'] === $view && $route['page'] === $page) ||
            ($route['method'] === $method && $route['view'] === '*' && $route['page'] === $page) ||
            ($route['method'] === '*' && $route['view'] === '*' && $route['page'] === $page) ||
            ($route['method'] === '*' && $route['view'] === '*' && $route['page'] === '*')
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
