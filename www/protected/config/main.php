<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// Set default language
$currentLanguage = 'en';

// Allowed languages
$i18n = array(
	'cz' => 'cz',
	'ru' => 'ru',
	'en' => 'en'
);

// Define current language
if (!empty($_SERVER['REQUEST_URI'])) {
	// Get URI parts
	$uri = explode('/', $_SERVER['REQUEST_URI']);
	
	// Get current language alias
	$currentLanguage = !strstr($_SERVER['REQUEST_URI'], '/static/') ? $uri[1] : $uri[2];
	
	// Check is first of URI part is language alias
	if (!isset($i18n[$currentLanguage])) {
		$currentLanguage = 'en';
	}
}

// Custom Includes
require_once(dirname(__FILE__) . '/../includes/localization.php');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
$settings = array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'MediaClub',

	// preloading 'log' component
	'preload' => array('log'),

	// Autoloading model and component classes
	'import' => array(
		'application.models.*',
		'application.models.form.*',
		'application.components.*',
		'application.extensions.yii-mail.YiiMailMessage',

		// Import tools
		'application.includes.tools.*',

		// Import billings
		'application.includes.billings.*',
	),
	'language' => 'en',
	'sourceLanguage' => '',
	'defaultController' => 'index',

	// Modules
	'modules' => array(),


	// application components
	'components' => array(
		'curl' => array(
			'class' => 'application.extensions.curl.Curl',
		),
		'mail' => array(
	 		'class' => 'application.extensions.yii-mail.YiiMail',
			'transportType' => 'php',
	 		'viewPath' => 'application.views.mail',
			'logging' => true
		),
		"messages" => array(
			//"class" => "CGettextMessageSource",
			//"useMoFile" => FALSE,
		),
		'request' => array(
			'enableCookieValidation' => true,
			'enableCsrfValidation' => true,
		),
		'user' => array(
			// Disable cookie-based authentication
			'allowAutoLogin' => false,
			
			// Extending class CWebUser
			'class' => 'MCWebUser',
		
			// Singin url
			'loginUrl'=>array('/user/signin/')
		),
		'urlManager'=>array(
			'urlFormat' => 'path',
			'class' => 'application.components.UrlManager',
			'showScriptName' => false,
			'rules' => array(
				// Routes index page
				'<language:(' . implode('|', $i18n) . ')>' => '/index',
		
				// Routes index page
				'/captcha/display/<key:([^\_]+)>_<rand:([^\.]+)>.jpg' => '/captcha/display/',
		
				// Routes search
				'<language:(' . implode('|', $i18n) . ')>/search/letters/<letter>.html' => '/search/letters/',

				// Routes singes
				'/<language:(' . implode('|', $i18n) . ')>/singer/<letter>/<alias:([^\.]+)>.<id>.html' => '/singer/',
		
				// Routes albums
				'/<language:(' . implode('|', $i18n) . ')>/album/<singerLetter>/<singerAlias>/<alias:([^\.]+)>.<id>.html' => '/album/',
				'/<language:(' . implode('|', $i18n) . ')>/albums/history/<date:([^\.]+)>.<genreId>.html' => '/album/history/',
			
				// Songs albums
				'/song/download/<orderSongId>.html' => '/song/download/',
				'/static/<language:(' . implode('|', $i18n) . ')>/lyrics/<singerLetter>/<singerAlias>/<albumAlias>/<alias:([^\.]+)>.<id>.html' => '/song/lyrics/',
				'/static/<language:(' . implode('|', $i18n) . ')>/lyrics_ajax/<singerLetter>/<singerAlias>/<albumAlias>/<alias:([^\.]+)>.<id:([0-9]+)>.html' => '/song/lyricsAjax/',
		
				// Routes genres
				'/static/<language:(' . implode('|', $i18n) . ')>/genres/<alias:([^\.]+)>.<id>.html' => '/genre/',
				'/static/<language:(' . implode('|', $i18n) . ')>/genres.html' => '/genre/',
		
				// Routes styles
				'/static/<language:(' . implode('|', $i18n) . ')>/styles/<genreId>/<alias:([^\.]+)>.<id>.html' => '/style/',
		
				// Routes countries
				'/static/<language:(' . implode('|', $i18n) . ')>/countries/<alias:([^\.]+)>.<id>.html' => '/country/',
				'/static/<language:(' . implode('|', $i18n) . ')>/countries.html' => '/country/',
		
				// User routes
				'/<language:(' . implode('|', $i18n) . ')>/user/signup/' => '/user/signup/',
				'/<language:(' . implode('|', $i18n) . ')>/user/signin/' => '/user/signin/',
				'/<language:(' . implode('|', $i18n) . ')>/user/logout/' => '/user/logout/',
				'/<language:(' . implode('|', $i18n) . ')>/user/profile/' => '/user/profile/',
				'/<language:(' . implode('|', $i18n) . ')>/user/confirm_email/<id:([0-9]+)>_<token:([^\.]+)>.html' => '/user/confirmEmail/',
				'/<language:(' . implode('|', $i18n) . ')>/user/end_signup/<id:([0-9]+)>_<mailResult:([^\.]+)>.html' => '/user/endSignup/',
				'/<language:(' . implode('|', $i18n) . ')>/user/end_signup/<id:([0-9]+)>.html' => '/user/endSignup/',
				'/<language:(' . implode('|', $i18n) . ')>/user/remember_password.html' => '/user/rememberPassword/',
				'/<language:(' . implode('|', $i18n) . ')>/user/change_password/<id:([0-9]+)>_<token:([^\.]+)>.html' => '/user/changePassword/',
		
				// Order routes
				'/order/add_to_cart/<type:([^\.]+)>.html' => '/order/addToCart/',
				'/order/remove_from_cart/<type:([^\.]+)>.<id:([0-9]+)>.html' => '/order/removeFromCart/',
				'/<language:(' . implode('|', $i18n) . ')>/order/cart/' => '/order/cart/',
				'/order/cart_confirm/' => '/order/cartConfirm/',
				'/<language:(' . implode('|', $i18n) . ')>/order/list/' => '/order/list/',
				'/<language:(' . implode('|', $i18n) . ')>/order/songs/<id:([0-9]+)>.html' => '/order/songs/',
		
				// Payments routs
				'/<language:(' . implode('|', $i18n) . ')>/payment/topup/' => '/payment/topup/',
				'/<language:(' . implode('|', $i18n) . ')>/payment/topup_return/<billing:([^\.]+)>.<status:([^\.]+)>.<transactionId:([0-9]+)>.html' => '/payment/topupReturn/',
				'/<language:(' . implode('|', $i18n) . ')>/payment/transactions/' => '/payment/transactions/',
			)
		),
		'errorHandler' => array(
			// use 'site/error' action to display errors
			'errorAction' => 'index/error',
		),
		'log' => array(
			'class' => 'CLogRouter',
			'enabled' => YII_DEBUG,
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning',
				),
				array(
					'class' => 'CWebLogRoute',
				),
				//off locale problem
				// TODO: fix it;
				array(
						'class'=>'application.extensions.yii-debug-toolbar.YiiDebugToolbarRoute',
						'ipFilters'=>array('192.168.70.25','195.138.70.86'),
				)
			)
		),
		// Sphinx component
		'sphinx' => array(
			'class' => 'system.db.CDbConnection',
			'connectionString' => 'mysql:host=127.0.0.1;port=9306',
			'charset' => 'utf8',
			'enableProfiling' => true
		)
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => array(
		// Current language
		'currentLanguage' => $currentLanguage,
	
		// this is used in contact page
		'adminEmail' => 'ramzes@te.net.ua',
	
		// application-level parameters
		'languages' => array('ru' => 'Russain', 'en' => 'English', 'cz' => 'Czech'),
		
		// Static file cache: true - enabled, false or nothing - disabled
		'static_cache' => true,
	
		// Part of seacret key to create hashes
		'secret_key' => 'MediaClub_0343742374-fjfnsdjkfn=jdnfjksdfn',
	
		'RangeSearchAutocomplite' => '30',
		'singer_cover' => '/images/art/',
		'albumr_cover' => '/images/alb/',
		'static_images_url' => 'http://mclub.com.ua',
		'mailer' =>	'noreply@mediaclub.com',

		// Pager settings
		'pager' => array(
			'variable' => 'page',
			'rpp' => array(
				'singer_letters' => 200,
				'albums' => 15
			)
		),
	
		// royalty for track price
		'royalty'=> 0.12,
	
		// traf price
		'track_price' => array(
			"0" => array("price" => 0.2, 'geo' => "World"),
			"1" => array("price" => 0.2, 'geo' => "Ukraine"),
			"2" => array("price" => 0.2, 'geo' => "Odessa"),
			"3" => array("price" => 0.7, 'geo' => "Reserved"),
			"4" => array("price" => 0.2, 'geo' => "Tenet"),
			"5" => array("price" => 0,   'geo' => "Free"),
		),

		// Album types
		'album_types' => array(
			1 => 'albums',
			2 => 'bootlegs',
			3 => 'singles',
			4 => 'live',
			5 => 'compilations'
		),
		
		// Auth cookies settings
		'cookies' => array(
			'prefix' => 'mclub_',
			'life_time' => 3600,
			'hash_rebuild_period' => 600,
		
			// One year life time for cookie with tmp user token
			'tmp_token_life_time' => 31536000,
		),
		
		// Lists settings
		'lists' => array(
			'albums' => array(
				'index_latest_number' => 5
			)
		)
	)
);

// Add DB settings to app params
$settings['params']['db'] = require_once 'main_db.php';

// Add paths to app params
$settings['params']['paths'] = require_once 'paths.php';

// Add billings settings to app params
$settings['params']['billings'] = require_once 'billings.php';

return $settings;