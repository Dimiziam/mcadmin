<?php
/**
 * Config for TRedis class
 *
 * @package MediaClub
 * @subpackage configs
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
return array(
	'users' => array(
		'namespace' => 'MCAUsers_',
		'servers' => array(
			array('host' => '127.0.0.1', 'port' => 6379, 'db' => 1)
		)
	),
	// in this namespace store all info albums,tracks,singers,genre,style
	// cleanup then base converting
	'data' => array(
		'namespace' => 'MCAData_',
		'servers' => array(
			array('host' => '127.0.0.1', 'port' => 6379, 'db' => 2)
		)
	),
	'charts' => array(
		'namespace' => 'MCACharst_',
		'servers' => array(
			array('host' => '127.0.0.1', 'port' => 6379, 'db' => 3)
		)
	),
	'autocomplite' => array(
		'namespace' => 'MCAAutocomplite_',
		'servers' => array(
			array('host' => '127.0.0.1', 'port' => 6379, 'db' => 4)
		)
	),
	'cart' => array(
		'namespace' => 'MCACart_',
		'servers' => array(
			array('host' => '127.0.0.1', 'port' => 6379, 'db' => 5)
		)
	),
);
