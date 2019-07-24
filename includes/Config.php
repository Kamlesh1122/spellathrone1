<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'piano');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('ADMIN_SLUG', '');
define('ADMIN_DIR', 'admin');
define('SUBFOLDER', '/public');
define('SALT_STR', '$2y$10$WXHXicWqQLS');
define('DATE_DEFAULT', 'Y-m-d H:i:s');

DEFINE('DS', DIRECTORY_SEPARATOR);
define('SITE_DIR', dirname(dirname(__FILE__)));
define('ASSET_DIR', SITE_DIR . DS . 'public' . DS . 'asset');
define('FILE_UPLOAD_DIR', ASSET_DIR . DS . 'images');

define('VIEW_DIR', SITE_DIR . '\App\Views');
define('CONTROLLER_DIR', SITE_DIR . '\App\Controllers');

session_start();
if (empty($_SESSION['checkpoint_token'])) {
	$_SESSION['checkpoint_token'] = md5(mt_rand());
}
if (empty($_SESSION['old'])) {
	$_SESSION['old'] = [];
}
if (empty($_SESSION['flash'])) {
	$_SESSION['flash'] = [];
}

define('PROCESS_FAIL', 'Oops! Something went wrong');
