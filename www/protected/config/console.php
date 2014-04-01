<?php
/**
 * Config to store main settings fro console application
 * 
 * @package MediaClub
 * @subpackage configs
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */

// Get settings from main config
$settings = require_once 'main.php';

// Set console custom settings
$settings['name'] = 'Mclub Console Application';

// Unset no need settings
unset($settings['defaultController']);

return $settings;