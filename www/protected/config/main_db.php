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
        'connectionString' => 'mysql:host=127.0.0.1;dbname=mediaclub',
        'emulatePrepare' => true,
        'username' => 'ramzesdb',
        'password' => 'qwepoi',
        'attributes' => array(
            'charset' => 'utf8',
            'enableProfiling' => false,
            'tablePrefix' => null
        )
    ),

    // Old MediaClub DB
    'old_db' => array(
        'connectionString' => 'mysql:host=mclub.com.ua;dbname=tdg_mclub',
        'emulatePrepare' => true,
        'username' => 'mclub_reader',
        'password' => '22323mclub_reader12212',
        'attributes' => array(
            'charset' => 'utf8',
            'enableProfiling' => false,
            'tablePrefix' => null
        )
    ),


);