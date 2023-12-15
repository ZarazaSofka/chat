<?php

define('ROOT_DIR', __DIR__);

require_once ROOT_DIR . '/config/config.php';
require_once ROOT_DIR . '/config/session.php';
require_once ROOT_DIR . '/config/route.php';

$router->route();

$db->close();