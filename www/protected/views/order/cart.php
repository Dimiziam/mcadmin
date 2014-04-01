<?php 
	// Get cart translations
	$translations = MTLang::instance()->getGroup('cart');
	
	// Get album translations
	$albumTranslations = MTLang::instance()->getGroup('album');
	
	// Get song translations
	$songTranslations = MTLang::instance()->getGroup('song');
?>
<h2><?php echo $translations['cart_caption']; ?></h2>
<?php if (empty($songs) || empty($albums)) { ?>
	<b><?php $translations['empty_cart']; ?></b>
<?php } else { ?>
	<div class="singer-album-list" id='cart_container'>
		<table border=1 cellspacing=0 width="100%" class="album-list">
			<tr class="title_th">
				<th width="80px"><?php echo $albumTranslations['cover']; ?></th>
				<th colspan="7"><?php echo $albumTranslations['title']; ?></th>
				<th width="80px">&nbsp;</th>
			</tr>
			<?php 
				// Prepare total
				$total = array(
					'size' => 0,
					'length' => 0,
					'price' => 0,
				);
			
				foreach ($albums as $album) {
					// Prepare url params
					$params = array(
						'id' => $album['id'],
						'alias' => $album['alias'],
						'singerLetter' => substr($album['singer']['alias'], 0, 1),
						'singerAlias' => $album['singer']['alias']
					);
			?>
				<tr id="album_<?php echo $album['id'];?>">
					<td>
						<img title="<?php echo $album['title'];?>" 
							alt="<?php echo $album['title'];?>" width=60px" height="60px"
							src="<?php $this->widget('application.components.widgets.images.WAlbum', array('singerId' => $album['singer_id'], 'albumId' => $album['id'], 'type' => 'tcover')); ?>"
						/>
					</td>
					<td class="singer-album-title" colspan="7">
						<a href="<?php echo $this->createUrl('/album/', $params); ?>" title="<?php echo $album['title'];?>"><?php echo $album['title'];?></a>
					</td>
					<td>
						<a href="JavaScript: void(0); " title="<?php echo $translations['remove_album'];?>" class="album_remove" albumId="<?php echo $album['id'];?>"><?php echo $translations['remove'];?></a>
					</td>
				</tr>
				<tr class="title_th" id="songs_album_caption_<?php echo $album['id'];?>">
					<th width="20px">&nbsp;</th>
					<th width="20px">#</th>
					<th width="340px"><?php echo $songTranslations['track_title'];?></th>
					<th width="80px"><?php echo $songTranslations['mode'];?></th>
					<th width="60px"><?php echo $songTranslations['size'];?></th>
					<th width="60px"><?php echo $songTranslations['length'];?></th>
					<th width="60px"><?php echo $songTranslations['bitrate'];?></th>
					<th width="60px"><?php echo $songTranslations['price'];?></th>
					<th width="60px">&nbsp;</th>
				</tr>
				<?php 
					foreach ($songs as $song) {
						if ($song['album_id'] != $album['id']) {
								continue;
						}
						
						// Calculate price
						$location = 0;
						$price = round(Yii::app()->params['royalty'] * 100 + ($song['size'] / (1024 * 1024)) * Yii::app()->params['track_price'][$location]['price']) / 100;
						
						// Calculate size
						$size = round($song['size'] / (1024 * 1024), 2);
						
						// Calculate length
						$length = round($song['length'] * 0.0166666667, 2);
						
						// Calculate total
						$total['size'] += $size;
						$total['length'] += $length;
						$total['price'] += $price;
				?>
					<tr id="song_<?php echo $song['id'];?>" class="songs_album_<?php echo $album['id'];?>">
						<td>&nbsp;</td>
						<td><?php echo $song['track'];?></td>
						<td><?php echo $song['name'];?></td>
						<td><?php echo $song['mode'];?></td>
						<td><?php echo $size; ?> Mb</td>
						<td><?php echo $length; ?></td>
						<td><?php echo $song['bitrate']; ?></td>
						<td><?php echo $price;?></td>
						<td>
							<a href="JavaScript: void(0); " title="<?php echo $translations['remove_song'];?>" class="song_remove" songId="<?php echo $song['id'];?>"><?php echo $translations['remove'];?></a>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</table>
		<div class="clear"></div>
		<div class="grid_8">
			<div class="track_list_info">
				<?php echo $translations['pay_info'];?>
			</div>
		</div>
		<div class="grid_3">
			<?php 
				if (!Yii::app()->user->isGuest) {
					// Get current user info
					$userInfo = Yii::app()->user->getInfo();
					
					// Check is user has enought money
					if (empty($userInfo['amount']) || $userInfo['amount'] < $total['price']) {
			?>
					<div id="topup_dialog" title="<?php echo $translations['topup_dialog_caption'];?>" style='display: none;'>
						<?php echo $this->renderPartial('application.views.order._cart.topup_dialog', array('translations' => $translations)); ?>
					</div>
				
					<a href="JavaScript: void(0);" class="buy_track" id="topup_button">
						<button name="buy_button" class="buy_button" type="submit">
							<img src="/images/cart_ico.png"><?php echo $translations['buy_button'];?>
						</button>
					</a>
				<?php } else { ?>
					<a href="JavaScript: void(0);" class="buy_track" id="cart_confirm">
						<button name="buy_button" class="buy_button" type="submit">
							<img src="/images/cart_ico.png"><?php echo $translations['buy_button'];?>
						</button>
					</a>
				<?php } ?>
			<?php } else { ?>
				<div id="login_dialog" title="<?php echo $translations['login_dialog_caption'];?>" style='display: none;'>
					<?php echo $this->renderPartial('application.views.order._cart.login_dialog'); ?>
				</div>
			
				<a href="JavaScript: void(0);" class="buy_track" id="login_button">
					<button name="buy_button" class="buy_button" type="submit">
						<img src="/images/cart_ico.png"><?php echo $translations['login_button'];?>
					</button>
				</a>
			<?php } ?>
			<br><br>
			<div class="cart_total"><span class="catr_txt"><?php echo $translations['total_time'];?>:</span><span id="total_time"><?php echo $total['length']; ?></span><b> <?php echo $songTranslations['min'];?></b></div>
			<div class="cart_total"><span class="catr_txt"><?php echo $translations['total_size'];?>:</span><span id="total_size"><?php echo $total['size']; ?></span><b> Mb</b></div>
			<div class="cart_total"><span class="catr_txt"><?php echo $translations['total_price'];?>:</span><span id="total_price"><?php echo $total['price']; ?></span><b> $</b></div>
		</div>
	</div>
<?php } ?>