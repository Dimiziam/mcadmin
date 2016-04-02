<?php
/**
 *
 * @packageadmin.mediaclub.com.
 * Date: 02.04.2016
 * @author Roman Shtempeliuk <deRamzes@gmail.com>
 */

// Get user translations
$translations = MTLang::instance()->getGroup('album');



//$this->breadcrumbs = array(
//	$translations['singer_list'] => $this->createUrl('/singerSeo/search/'),
//	MTLang::t('add_new', 'persistence')
//);

//$this->menu = array(
//	array('label' => $translations['singer_list'], 'url' => $this->createUrl('/singerSeo/search/'))
//);
?>

<h1><?php echo $translations['permit_album']; ?></h1>
<?php echo $this->renderPartial('_form', array('action'=>$action,'translations' => $translations)); ?>