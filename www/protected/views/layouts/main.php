<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/main.js"></script>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/tools.js"></script>
	<?php 
		Yii::app()->clientScript->scriptMap=array(
			'jquery.js'=>false,
			'jquery.min.js'=>false,
		);
	?>

	<?php
	// TODO: remove after tests

	MTLang::instance()->cleareCache();

	?>

 	<!-- Include superfish plugin -->
	<link rel="stylesheet" media="screen" href="<?php echo Yii::app()->request->baseUrl; ?>/js/superfish/css/superfish.css" /> 
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/superfish/js/hoverIntent.js"></script> 
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/superfish/js/superfish.js"></script> 

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	
	<script>
		var global_language_code = '<?php echo Yii::app()->params['currentLanguage']; ?>';
	</script>
</head>

<body>

<div class="container" id="page" style='width: 90%;'>

	<div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div><!-- header -->
	<div class="content">
		<!-- mainmenu -->
		<div class="mainmenu">
		<?php
			if (!Yii::app()->user->isGuest) {
				$this->renderPartial('application.views.layouts.top_menu');
			}
		?>
		</div>
		<!-- / mainmenu -->
<br>

		<?php ;if(isset($this->breadcrumbs)):?>
			<?php $this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>$this->breadcrumbs,
			)); ?><!-- breadcrumbs -->
		<?php endif?>

		<?php echo $content; ?>

		<div class="clear"></div>
	</div>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by TDG.<br/>
		All Rights Reserved.<br/>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
