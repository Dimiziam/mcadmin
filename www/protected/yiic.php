<?php
// Set default timezone
date_default_timezone_set('Europe/Moscow');

// Enable to store all php error into log file
error_reporting(E_ALL);
ini_set('display_errors', 'off');
ini_set('log_errors', 'on');
ini_set('ignore_repeated_errors', 'on');
ini_set('log_errors_max_len', 10240);
ini_set('error_log', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'php_error_' . date('Y_m_d') . '.log');

// Include TRedis
require_once(dirname(__FILE__) . '/includes/tredis.php');

// Create TRedis inctance
TRedis::create(require_once(dirname(__FILE__) . '/config/redis.php'));

// change the following paths if necessary
$yiic=dirname(__FILE__).'/../../yii/framework/yiic.php';
$config=dirname(__FILE__).'/config/console.php';

require_once($yiic);
