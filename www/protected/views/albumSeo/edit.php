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
	$translations['singer_list'] => $this->createUrl('/singerSeo/search/'),
	MTLang::t('add_new', 'persistence')
);

$this->menu = array(
	array('label' => $translations['singer_list'], 'url' => $this->createUrl('/singerSeo/search/'))
);
?>

<h1><?php echo $translations['edit_new_album_seo']; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'translations' => $translations)); ?>