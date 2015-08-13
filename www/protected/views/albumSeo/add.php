<?php
/**
 *
 * @packageadmin.mediaclub.com.
 * Date: 31.07.2015
 * @author Roman Shtempeliuk <deRamzes@gmail.com>
 */


// Get user translations
$translations = MTLang::instance()->getGroup('seo');



$this->breadcrumbs = array(
	$translations['album_list'] => $this->createUrl('/albumSeo/search/'),
	MTLang::t('add_new', 'persistence')
);

$this->menu = array(
	array('label' => $translations['album_list'], 'url' => $this->createUrl('/singerSeo/search/'))
);
?>

<h1><?php echo $translations['add_new_album_seo']; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'translations' => $translations)); ?>