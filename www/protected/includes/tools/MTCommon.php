<?php
/**
 * MTCommon
 * 
 * Class with common tools functions
 * 
 * @package MediaClub
 * @subpackage tools
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MTCommon
{
	/**
	 * Method create alias from string
	 * 
	 * @param string $string
	 * @return string
	 */
	public static function createAlias($string)
	{
		// Get alphabet
		$translit = self::getTranslit();
		
		// Prepare alias
		$alias = strtolower(str_replace(array_keys($translit), array_values($translit), $string));
		$alias = preg_replace('/[^A-Za-z0-9_]/is', '_', $alias);
		$alias = trim(preg_replace('/_{2,10}+/is', '_', $alias), '_');
		
		return $alias;
	}
	
	/**
	 * Method to get translit aphabet
	 * 
	 * @return array
	 */
	public static function getTranslit()
	{
		$eng["А"] = "A";
		$eng["Б"] = "B";
		$eng["В"] = "V";
		$eng["Г"] = "G";
		$eng["Д"] = "D";
		$eng["Е"] = "E";
		$eng["Ё"] = "YO";
		$eng["Ж"] = "ZH";
		$eng["З"] = "Z";
		$eng["И"] = "I";
		$eng["Й"] = "J";
		$eng["К"] = "K";
		$eng["Л"] = "L";
		$eng["М"] = "M";
		$eng["Н"] = "N";
		$eng["О"] = "O";
		$eng["П"] = "P";
		$eng["Р"] = "R";
		$eng["С"] = "S";
		$eng["Т"] = "T";
		$eng["У"] = "U";
		$eng["Ф"] = "F";
		$eng["Х"] = "H";
		$eng["Ц"] = "C";
		$eng["Ч"] = "CH";
		$eng["Ш"] = "SH";
		$eng["Щ"] = "SH";
		$eng["Ъ"] = "'";
		$eng["Ы"] = "Y";
		$eng["Ь"] = "'";
		$eng["Э"] = "E'";
		$eng["Ю"] = "YU";
		$eng["Я"] = "YA";
		$eng["а"] = "a";
		$eng["б"] = "b";
		$eng["в"] = "v";
		$eng["г"] = "g";
		$eng["д"] = "d";
		$eng["е"] = "e";
		$eng["ё"] = "e";
		$eng["ж"] = "zh";
		$eng["з"] = "z";
		$eng["и"] = "i";
		$eng["й"] = "j";
		$eng["к"] = "k";
		$eng["л"] = "l";
		$eng["м"] = "m";
		$eng["н"] = "n";
		$eng["о"] = "o";
		$eng["п"] = "p";
		$eng["р"] = "r";
		$eng["с"] = "s";
		$eng["т"] = "t";
		$eng["у"] = "u";
		$eng["ф"] = "f";
		$eng["х"] = "h";
		$eng["ц"] = "c";
		$eng["ч"] = "ch";
		$eng["ш"] = "sh";
		$eng["щ"] = "sh";
		$eng["ъ"] = "'";
		$eng["ы"] = "y";
		$eng["ь"] = "'";
		$eng["э"] = "e";
		$eng["ю"] = "yu";
		$eng["я"] = "ya";
	
		return $eng;
	}
}