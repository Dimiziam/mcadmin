<?php

// change the following paths if necessary

$yii=dirname(__FILE__).'/../../yii/framework/yii.php';
$config=dirname(__FILE__).'/../protected/config/console.php';


$global=dirname(__FILE__).'/../protected/config/globals.php';
require($global);

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);


// Include TRedis
require_once(dirname(__FILE__) . '/..//protected/includes/tredis.php');

// Create TRedis inctance
TRedis::create(require_once(dirname(__FILE__) . '/../protected/config/redis.php'));



require_once($yii);
$_yii = Yii::createConsoleApplication($config);

// Include Mediclub
require_once(dirname(__FILE__) . '/../protected/includes/MediaClub.php');


//Create Mclub instance
MediaClub::create();


$_yii->run();
