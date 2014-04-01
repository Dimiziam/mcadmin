<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/css/reset.css" />
<link rel="stylesheet" type="text/css" href="/css/text.css" />
<link rel="stylesheet" type="text/css" href="/css/960_12_col.css" />
<link rel="stylesheet" type="text/css" href="/css/main.css" />

<!-- for dropdown select menu -->

<link type="text/css" href="/js/themes/base/jquery.ui.core.css"
	rel="stylesheet" />
<link type="text/css" href="/js/themes/base/jquery.ui.theme.css"
	rel="stylesheet" />
<link type="text/css" href="/js/themes/base/jquery.ui.selectmenu.css"
	rel="stylesheet" />
<link type="text/css" href="/js/themes/base/jquery.ui.replace.css"
	rel="stylesheet" />
<link type="text/css" href="/js/themes/base/jquery.ui.autocomplete.css"
	rel="stylesheet" />

<link rel="stylesheet" type="text/css" href="/css/tinyscroll.css" />


<script type="text/javascript" src="/js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="/js/ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/ui/jquery.ui.selectmenu.js"></script>
<script type="text/javascript" src="/js/tools.js"></script>
<script type="text/javascript" src="/js/order.js"></script>
<script type="text/javascript" src="/js/main.js"></script>
<script type="text/javascript" src="/js/jquery.tinyscrollbar.js"></script>


<!-- <script type="text/javascript" src="/js/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="/js/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="/js/ui/jquery.ui.selectmenu.js"></script>
<script type="text/javascript" src="/js/ui/jquery.ui.autocomplete.js"></script>
-->
<script type="text/javascript">
	(function() {
		var config = {
			kitId: 'igz4mrf',
			scriptTimeout: 3000
		};
		var h=document.getElementsByTagName("html")[0];h.className+=" wf-loading";var t=setTimeout(function(){h.className=h.className.replace(/(\s|^)wf-loading(\s|$)/g," ");h.className+=" wf-inactive"},config.scriptTimeout);var tk=document.createElement("script"),d=false;tk.src='//use.typekit.net/'+config.kitId+'.js';tk.type="text/javascript";tk.async="true";tk.onload=tk.onreadystatechange=function(){var a=this.readyState;if(d||a&&a!="complete"&&a!="loaded")return;d=true;clearTimeout(t);try{Typekit.load(config)}catch(b){}};var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(tk,s)
	})();

	// Init global variables
	<?php 
		// Get top menu translations
		$cartTranslations = MTLang::instance()->getGroup('cart');
	?>
	var cart_added_message = "<?php echo $cartTranslations['message_1']; ?>";
	var cart_error_add_message = "<?php echo $cartTranslations['message_2']; ?>";
	var cart_confirm_message = "<?php echo $cartTranslations['message_3']; ?>";
	var cart_confirmed_message = "<?php echo $cartTranslations['message_4']; ?>";
	var cart_error_confirm_message = "<?php echo $cartTranslations['message_5']; ?>";
	var cart_is_empty = "<?php echo $cartTranslations['empty_cart']; ?>";
	
</script>
<title>mediaclub</title>
</head>
<body>
	<?php 
		// Cleare language cache
		// TODO: remove after tests
		MTLang::instance()->cleareCache();
	
		// Get top menu translations
		$topMenuTranslations = MTLang::instance()->getGroup('top_menu');
	?>
	<div id="super_main_div">
		<div class="container_12">
			<div class="grid_1 top_menu"></div>
			<div class="grid_6 top_menu">
				<ul>
					<li><a href="#"><?php echo $topMenuTranslations['about']; ?></a></li>
					<li><a href="#"><?php echo $topMenuTranslations['faq']; ?></a></li>
					<li><a href="#"><?php echo $topMenuTranslations['dir']; ?></a></li>
					<li><a href="#"><?php echo $topMenuTranslations['charts']; ?></a></li>
				</ul>
			</div>
			<div class="grid_5 top_menu">
			<?php
				if (!Yii::app()->user->isGuest) {
					$this->renderPartial('application.views.include.user_header');
				} else {
					$this->renderPartial('application.views.include.guest_header');
				}
			?>
			</div>
		</div>

		<div class="wrapper">
			<div class="container_12">
				<div class="grid_12 header">
					<div id="logo">
						<img src="/images/logo_mclub.png" width="230" height="92" />
					</div>
					<div class="menu">
						<div class="menu_cart left">
							<?php 
								// Get current cart info
								$cartInfo = MTCart::instance()->getInfo();
							?>
							<a href="<?php echo $this->createUrl('/order/cart'); ?>" title="<?php echo MTLang::t('link_title', 'cart'); ?>">
								<img src="/images/shopping_cart.gif" width="32" height="20" border="0"/>
							</a>
							(<span id="cart_songs_number"><?php echo !empty($cartInfo['songs_number']) ? $cartInfo['songs_number'] : 0; ?></span>)
							
						</div>
						<div class="menu_selectors">
							<ul>
								<?php $this->widget('application.components.widgets.lists.WGenres'); ?>
								<?php $this->widget('application.components.widgets.lists.WCountries'); ?>
								<li><a href="#"><?php echo $topMenuTranslations['soundtraks']; ?></a></li>
								<li><a href="#"><?php echo $topMenuTranslations['dj']; ?></a></li>
							</ul>
						</div>
					</div>
			<?php $this->widget('application.components.widgets.search.WLetters'); ?>
			<div class="clear"></div>

			<?php $this->widget('application.components.widgets.search.WPanel'); ?>

		</div>
			</div>
		</div>
		<div class="clear"></div>
		<!-- center content -->
		<div id="content-wrap" class="clearfix">
			<div class="container_12">
				<div class="grid_12 spacer30"></div>
				<div class="clear"></div>
			<?php //echo $this->pageTitle=Yii::app()->name; ?>

					<?php echo $content; ?>

			</div>
			<!--//container_12-->
		</div>

	</div>

	<div class="footer">
		<div class="footer_content">

			<div class="footer_menu">
				<ul>
					<li><a href="#">About Media Club</a></li>
					<li><a href="#">FAQ</a></li>
					<li><a href="#">a-z Directory</a></li>
					<li><a href="#">Charts</a></li>
				</ul>

			</div>

			<div class="copyright">
				Copyright by <a href="#">Media Club</a>, 2012
			</div>




		</div>
	</div>

	<!-- //user top-->


</body>
</html>
