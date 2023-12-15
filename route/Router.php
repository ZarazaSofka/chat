<?php

class Router {
    private $routes = [];

    public function addRoute($pattern, $call) {
        $this->routes[$pattern] = $call;
    }

    public function route() {
        global $vars;
        $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        foreach ($this->routes as $pattern => $call) {
            $vars = [];
            if ($this->matchPattern($pattern, $url, $vars)) {
                array_shift($vars);
                $this->call($call);
                return;
            }
        }
        http_response_code(404);
        view("404.php");
    }

    private function matchPattern($pattern, $url, &$vars) {
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        return preg_match($pattern, $url, $vars);
    }

    private function call($call) {
        list($controller, $func, $methods, $roles) = explode("@", $call);
        $access = $this->getAccess(explode('|', $roles));
        switch ($access) {
            case -1:
                return http_response_code(403);
            case 0:
                if (!$controller instanceof UserController) {
                    return header('Location: /login');
                }
            case 1:
                if ($this->checkMethod(explode('|', $methods))) {
                    return (new $controller())->$func();
                }
                return http_response_code(405);
        }
    }

    private function getAccess($roles) {
        if ((!isset($_SESSION['user_id']) || !isset($_SESSION['role']))) {
            if (in_array("ANON", $roles)) {
                return 1;
            }
            return 0;
        }
        global $user_id, $role;
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        
        if (in_array($role, $roles)) {
            return 1;
        }
        return -1;
    }

    private function checkMethod($methods) {
        if (in_array($_SERVER["REQUEST_METHOD"], $methods)) {
            return true;
        }
        return false;
    }
}