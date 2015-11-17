<?php
// Enable to store all php error into log file
error_reporting(E_ALL);
ini_set('display_errors', 'on');
ini_set('log_errors', 'on');
ini_set('ignore_repeated_errors', 'on');
ini_set('log_errors_max_len', 10240);
ini_set('error_log', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'protected' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'php_error_' . date('Y_m_d') . '.log');

// change the following paths if necessary
$yii = dirname(__FILE__) . '/../yii/framework/yii.php';
$config = dirname(__FILE__) . '/protected/config/main.php';

// Include TRedis
require_once(dirname(__FILE__) . '/protected/includes/tredis_new.php');

// Create TRedis inctance
TRedis::create(require_once(dirname(__FILE__) . '/protected/config/redis.php'));

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', true);

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

// Include yii
require_once($yii);

// Create web application
$app = Yii::createWebApplication($config);

// Run app
$app->run();
