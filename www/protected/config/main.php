<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// Set default language
$currentLanguage = 'en';

// Allowed languages
$i18n = array(
	'cs' => 'cs',
	'de' => 'de',
  	'pl' => 'pl',
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

		// Import tools
		'application.includes.tools.*',
	),
	'language' => 'en',
	'sourceLanguage' => '',
	'defaultController' => 'index',

	// Modules
	'modules' => array(),


	// application components
	'components' => array(
		'request' => array(
			'enableCookieValidation' => true,
			'enableCsrfValidation' => true,
		),
		'authManager' => array(
			// Будем использовать свой менеджер авторизации
			'class' => 'MCPhpAuthManager',
			// Роль по умолчанию. Все, кто не админы, модераторы и юзеры — гости.
			'defaultRoles' => array('guest'),
		),
		'user' => array(
			// Disable cookie-based authentication
			'allowAutoLogin' => false,
			
			// Extending class CWebUser
			'class' => 'MCWebUser',
		
			// Singin url
			'loginUrl' => array('/admin/signin/')
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
		
				// Admin routes
				'/<language:(' . implode('|', $i18n) . ')>/admin/search/' => '/admin/search/',
				'/<language:(' . implode('|', $i18n) . ')>/admin/signin/' => '/admin/signin/',
				'/<language:(' . implode('|', $i18n) . ')>/admin/logout/' => '/admin/logout/',
				'/<language:(' . implode('|', $i18n) . ')>/admin/add/' => '/admin/add/',
				'/<language:(' . implode('|', $i18n) . ')>/admin/edit/<id:([^\.]+)>.html' => '/admin/edit/',
				'/<language:(' . implode('|', $i18n) . ')>/admin/delete/<id:([^\.]+)>.html' => '/admin/delete/',
				'/<language:(' . implode('|', $i18n) . ')>/admin/autocomplete/' => '/admin/autocomplete/',
		
				// User routes
				'/<language:(' . implode('|', $i18n) . ')>/user/search/' => '/user/search/',
				'/<language:(' . implode('|', $i18n) . ')>/user/add/' => '/user/add/',
				'/<language:(' . implode('|', $i18n) . ')>/user/edit/<id:([^\.]+)>.html' => '/user/edit/',
				'/<language:(' . implode('|', $i18n) . ')>/user/delete/<id:([^\.]+)>.html' => '/user/delete/',
				'/<language:(' . implode('|', $i18n) . ')>/user/payments/<id:([^\.]+)>.html' => '/user/payments/',
				'/<language:(' . implode('|', $i18n) . ')>/user/autocomplete/' => '/user/autocomplete/',


				// Affiliate routes
				'/<language:(' . implode('|', $i18n) . ')>/user/affiliate/' => '/user/affiliate/',
				'/<language:(' . implode('|', $i18n) . ')>/user/affiliatelist/<id:\d+>' => '/user/affiliatelist/',

				//Reports
				'/<language:(' . implode('|', $i18n) . ')>/report/osa/' => '/report/osa/',
				'/<language:(' . implode('|', $i18n) . ')>/report/paypal/' => '/report/paypal/',
				'/<language:(' . implode('|', $i18n) . ')>/report/payu/' => '/report/payu/',
				'/<language:(' . implode('|', $i18n) . ')>/report/chart/' => '/report/chart/',

				//SEO singer
				'/<language:(' . implode('|', $i18n) . ')>/singerSeo/' => '/singerSeo/',
				'/<language:(' . implode('|', $i18n) . ')>/singerSeo/add' => '/singerSeo/add',
				'/<language:(' . implode('|', $i18n) . ')>/singerSeo/search' => '/singerSeo/search',
				'/<language:(' . implode('|', $i18n) . ')>/singerSeo/autocomplete/' => '/singerSeo/autocomplete/',
				'/<language:(' . implode('|', $i18n) . ')>/singerSeo/edit/<id:([^\.]+)>.html' => '/singerSeo/edit/',
				'/<language:(' . implode('|', $i18n) . ')>/singerSeo/delete/<id:([^\.]+)>.html' => '/singerSeo/delete/',

				//SEO album
				'/<language:(' . implode('|', $i18n) . ')>/albumSeo/' => '/albumSeo/',
				'/<language:(' . implode('|', $i18n) . ')>/albumSeo/add' => '/albumSeo/add',
				'/<language:(' . implode('|', $i18n) . ')>/albumSeo/search' => '/albumSeo/search',
				'/<language:(' . implode('|', $i18n) . ')>/albumSeo/autocomplete/' => '/albumSeo/autocomplete/',
				'/<language:(' . implode('|', $i18n) . ')>/albumSeo/edit/<id:([^\.]+)>.html' => '/albumSeo/edit/',
				'/<language:(' . implode('|', $i18n) . ')>/albumSeo/delete/<id:([^\.]+)>.html' => '/albumSeo/delete/',

				//Album
				'/<language:(' . implode('|', $i18n) . ')>/album/' => '/album/',
				'/<language:(' . implode('|', $i18n) . ')>/album/search' => '/album/search',
				'/<language:(' . implode('|', $i18n) . ')>/album/save' => '/album/save',

				// Order routes
				'/<language:(' . implode('|', $i18n) . ')>/order/search/' => '/order/search/',
				'/<language:(' . implode('|', $i18n) . ')>/order/songs/<id:([^\.]+)>.html' => '/order/songs/',
		
				// Payments routes
				'/<language:(' . implode('|', $i18n) . ')>/transaction/search/' => '/transaction/search/',
				'/<language:(' . implode('|', $i18n) . ')>/transaction/add/<userId:([^\.]+)>.html' => '/transaction/add/',
				'/<language:(' . implode('|', $i18n) . ')>/transaction/affiliate/<id:\d+>' => '/transaction/affiliate/',
				'/<language:(' . implode('|', $i18n) . ')>/transaction/canceled/<transaction_id:\d+>' => '/transaction/canceled/',
		
				// Chart routes
				'/<language:(' . implode('|', $i18n) . ')>/chart/add/' => '/chart/add/',
				'/<language:(' . implode('|', $i18n) . ')>/chart/edit/<id:([^\.]+)>.html' => '/chart/edit/',
				'/<language:(' . implode('|', $i18n) . ')>/chart/delete/<id:([^\.]+)>.html' => '/chart/delete/',
				'/<language:(' . implode('|', $i18n) . ')>/chart/autocomplete/' => '/chart/autocomplete/',
				'/<language:(' . implode('|', $i18n) . ')>/chart/items/<id:([^\.]+)>.html' => '/chart/items/',
				'/<language:(' . implode('|', $i18n) . ')>/chart/item_add/<created:([^\_]+)>_<sourceId:([^\.]+)>.html' => '/chart/itemAdd/',
				'/<language:(' . implode('|', $i18n) . ')>/chart/item_edit/<id:([^\.]+)>.html' => '/chart/itemEdit/',
				'/<language:(' . implode('|', $i18n) . ')>/chart/item_delete/<id:([^\.]+)>.html' => '/chart/itemDelete/',
				
				// Singer routs
				'/<language:(' . implode('|', $i18n) . ')>/singer/autocomplete/' => '/singer/autocomplete/',
		
				// Album routs
				'/<language:(' . implode('|', $i18n) . ')>/album/autocomplete/' => '/album/autocomplete/',
				'/<language:(' . implode('|', $i18n) . ')>/album/autocomplete/' => '/album/permit/',
		
				// Page routes
				'/<language:(' . implode('|', $i18n) . ')>/page/search/<type:([^\.]+)>.html' => '/page/search/',
				'/<language:(' . implode('|', $i18n) . ')>/page/add/<type:([^\.]+)>.html' => '/page/add/',
				'/<language:(' . implode('|', $i18n) . ')>/page/edit/<lang:([^\.]+)>_<type:([^\_]+)>_<id:([^\.]+)>.html' => '/page/edit/',
				'/<language:(' . implode('|', $i18n) . ')>/page/delete/<id:([^\.]+)>.html' => '/page/delete/',
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

				'class' => 'ext.phpconsole.PhpConsoleLogRoute',

				array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning',
				),
				array(
					'class' => 'CWebLogRoute',
				),
				//off locale problem
				// TODO: fix it;
//				array(
//						'class'=>'application.extensions.yii-debug-toolbar.YiiDebugToolbarRoute',
//						'ipFilters'=>array('192.168.70.25','195.138.70.86'),
//				)
			)
		),
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => array(
		// Current language
		'currentLanguage' => $currentLanguage,
	
		// this is used in contact page
		'adminEmail' => 'ramzes@te.net.ua',
	
		// application-level parameters
		'languages' => array('de' => 'Deutsch', 'en' => 'English', 'cs' => 'Čeština','pl'=>'Polski'),
	
		// Part of seacret key to create hashes
		'secret_key' => 'MediaClub_0343742374-fjfnsdjkfn=jdnfjksdfn',
	
		'RangeSearchAutocomplite' => '30',
		'singer_cover' => '/images/art/',
		//  path to News images
		'news_image_path'=>'/images/newsimages/',
		'albumr_cover' => '/images/alb/',
		'static_images_url' => 'http://mclub.com.ua',
		'mailer' =>	'noreply@mediaclub.com',
		'url_main_site' =>'http://www.mediaclub.com/',

		// Pager settings
		'pager' => array(
			'variable' => array(
				'current' => '_pager_current_page',
				'rpp' => '_pager_rpp'
			),
			'rpp' => 20
		),
	
		// royalty for track price
		'royalty'=> 0.12,
		//default billing in Payment paypal,payu,direct
		'default_view_billing' =>'paypal',
        'default_view_status' =>'done',

		// traf price
		'track_price' => array(
			"0" => array("price" => 0.2, 'geo' => "World"),
			"1" => array("price" => 0.2, 'geo' => "Ukraine"),
			"2" => array("price" => 0.2, 'geo' => "Odessa"),
			"3" => array("price" => 0.7, 'geo' => "Reserved"),
			"4" => array("price" => 0.2, 'geo' => "Tenet"),
			"5" => array("price" => 0,   'geo' => "Free"),
		),
		// minimum payment CKZ
		'AO_minimup'=>2,
		// payment persent from one track
		'AO_ednotka'=>0.08,


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
			'prefix' => 'mclubadmin_',
			'life_time' => 3600,
			'hash_rebuild_period' => 600,
		
			// One year life time for cookie with tmp user token
			'tmp_token_life_time' => 31536000,
		),
		'EU_codes' =>array (
			'BE','BG','CZ','DK','EL','ES','FR',
			'HR','IT','CY','LV','LT','LU',
			'HU','MT','NL',	'AT','PL','PT',
			'RO','SI','SK','FI',
			'SE','UK','DE','EE','IE'
		),

	)

);

// Add DB settings to app params
$settings['params']['db'] = require_once 'main_db.php';

// Add paths to app params
$settings['params']['paths'] = require_once 'paths.php';

return $settings;