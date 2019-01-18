<?php
/**
 * Author: Sergey Ponomarev
 * Version: 1.0
 */

namespace generator_OpenGraph;

if (!defined('EXTENSION_FOLDER_PATH')) {
    define('EXTENSION_FOLDER_PATH', dirname(__FILE__));
}

$settings_path = EXTENSION_FOLDER_PATH . '/settings.ini';
$settings = parse_ini_file($settings_path, true);

foreach ($settings as $section => $setting){
    foreach ($setting as $name => $v) {
        define($section . '_' . $name, $v);
    }
}

require_once EXTENSION_FOLDER_PATH . '/class.php';





