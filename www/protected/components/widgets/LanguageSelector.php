<?php
/**
*
* @author Ramzes
 * date  18.04.2013
 * @version
 */

class LanguageSelector extends CWidget
{
	public function run()
	{
		$currentLang = Yii::app()->language;
		$languages = Yii::app()->params->languages;
		$this->render('languageSelector', array('currentLang' => $currentLang, 'languages'=>$languages));
	}
}
