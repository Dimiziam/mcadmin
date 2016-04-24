<?php 
	// Get song translations
	$translations = MTLang::instance()->getGroup('song');

	$this->breadcrumbs = array(
		$translations['orders_list'] => $this->createUrl('/order/search/'),
		'#' . $orderId
	);




	if (!empty($songs)) {



?>



		<table class="table table-striped">
		<?php 
			$currentAlbum = '';
			$singerInfo='';
			foreach ($songs as $item) {
				if (empty($currentAlbum) || $currentAlbum != $item['title']) {
					$singerInfo = Singer::model()->getById($item['singer_id']);
					$currentAlbum = $item['title'];

		?>
				<tr>
					<th colspan="2">
					Album name: <a target="_blank"
									href="<?php echo  Yii::app()->params['url_main_site'];?>en/album/<?php echo $item['album_id'];?>">

									<?php echo $item['title']; ?>
								</a>&nbsp;
							Singer name:
								<a target="_blank"
								   	href="<?php echo  Yii::app()->params['url_main_site'];?>en/singer/<?php echo $singerInfo['id'];?>">
									<?php echo $singerInfo['name']; ?>
								</a>&nbsp;
						<a href="<?php echo $this->createUrl('/user/edit/', array('id' => $songs[0]['user_id'])); ?>">
							Edit User
						</a>
					</th>
				</tr>
				<tr>
					<th><?php echo $translations['name']; ?></th>
					<th><?php echo $translations['price']. ' '. MTLang::t('price_sign','user')?></th>
				</tr>
					<tr><td colspan="2"></td></tr>
			<?php } ?>
			<tr>
				<td><?php echo $item['name']; ?></td>
				<td><?php echo $item['price']; ?> <?php echo MTLang::t('price_sign','user');?></td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['songs_not_found']; ?></h2>
<?php } ?>