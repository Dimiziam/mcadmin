<?php
/**
 * Config for DB
 *
 * @package MediaClub
 * @subpackage configs
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
return array(
	// DB with all main info
	'info_db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=mclub',
			'emulatePrepare' => true,
			'username' => 'mclub',
			'password' => 'mclub',
			'attributes' => array(
				'charset' => 'utf8',
				'enableProfiling' => true,
				'tablePrefix' => null
			)
	),
	
	// Old MediaClub DB
	'old_db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=mclub_old',
			'emulatePrepare' => true,
			'username' => 'mclub_old',
			'password' => 'mclub_old',
			'attributes' => array(
				'charset' => 'utf8',
				'enableProfiling' => true,
				'tablePrefix' => null
			)
	),
);