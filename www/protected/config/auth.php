<?php
/**
 *
 * @packageadmin.mediaclub.com.
 * Date: 14.08.2015
 * @author Roman Shtempeliuk <deRamzes@gmail.com>
 */


return array(
	'guest' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Guest',
		'bizRule' => null,
		'data' => null
	),
	'seo' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Seo User',
		'children' => array(
			'guest', // унаследуемся от гостя
		),
		'bizRule' => null,
		'data' => null
	),

	'admin' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Administrator',
		'children' => array(
			'seo',         // позволим админу всё, что позволено модератору
		),
		'bizRule' => null,
		'data' => null
	),
);