<?php

namespace VerifyWoo\Core;

defined('ABSPATH') || exit;

class Router {
    static $routes = [];

    public static function redirect($url) {
        wp_redirect($url);
        exit;
    }

    public static function redirect_to_permalink($post_name, $search_params = []) {
        self::redirect(self::get_page_permalink($post_name, $search_params));
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

    public static function get($page, $action, $callback) {
        self::add($page, 'GET', $action, $callback);
    }

    public static function post($page, $action, $callback) {
        self::add($page, 'POST', $action, $callback);
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
