<?php

function custom_autoloader($class) {
    $directories = array(
        '/controllers/',
        '/services/',
        '/models/',
        '/route/',
        '/repositories/'
    );

    foreach ($directories as $directory) {
        $file = ROOT_DIR . $directory . $class . '.php';
        if (file_exists($file)) {
            include $file;
            return;
        }
    }
}

spl_autoload_register('custom_autoloader');


function view($page) {
    require ROOT_DIR .'/views/'. $page;
}

$db = new MySQL("localhost:3306", "root", "1111", "chat");