<?php 
	// Get order translations
	$orderTranslations = MTLang::instance()->getGroup('order');
	
	// Get cart translations
	$translations = MTLang::instance()->getGroup('cart');
	
	// Get album translations
	$albumTranslations = MTLang::instance()->getGroup('album');
	
	// Get song translations
	$songTranslations = MTLang::instance()->getGroup('song');
?>
<h2><?php echo $orderTranslations['songs_caption']; ?></h2>

<?php if (empty($songs) || empty($albums)) { ?>
	<b><?php $orderTranslations['not_found_songs']; ?></b>
<?php } else { ?>
	<div class="singer-album-list">
		<table width="100%">
		<tr>
			<td width="20%"><?php $this->renderPartial('application.views.user._profile.menu', array('currentKey' => 'orders')); ?></td>
			<td>
	
				<table border=1 cellspacing=0 width="100%" class="album-list">
					<tr class="title_th">
						<th width="80px"><?php echo $albumTranslations['cover']; ?></th>
						<th colspan="8"><?php echo $albumTranslations['title']; ?></th>
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
						<tr>
							<td>
								<img title="<?php echo $album['title'];?>" 
									alt="<?php echo $album['title'];?>" width=60px" height="60px"
									src="<?php $this->widget('application.components.widgets.images.WAlbum', array('singerId' => $album['singer_id'], 'albumId' => $album['id'], 'type' => 'tcover')); ?>"
								/>
							</td>
							<td class="singer-album-title" colspan="8">
								<a href="<?php echo $this->createUrl('/album/', $params); ?>" title="<?php echo $album['title'];?>"><?php echo $album['title'];?></a>
							</td>
						</tr>
						<tr class="title_th">
							<th width="20px">&nbsp;</th>
							<th width="20px">#</th>
							<th width="340px"><?php echo $songTranslations['track_title'];?></th>
							<th width="80px"><?php echo $songTranslations['mode'];?></th>
							<th width="60px"><?php echo $songTranslations['size'];?></th>
							<th width="60px"><?php echo $songTranslations['length'];?></th>
							<th width="60px"><?php echo $songTranslations['bitrate'];?></th>
							<th width="60px"><?php echo $songTranslations['price'];?></th>
							<th width="20px">&nbsp;</th>
						</tr>
						<?php 
							foreach ($songs as $song) {
								if ($song['album_id'] != $album['id']) {
									continue;
								}
								
								// Get order song
								if (empty($orderSongs[$song['id']])) {
									continue;
								}
								$orderSong = $orderSongs[$song['id']];
								
								// Get price
								$price = $orderSong['price'];
								
								// Calculate size
								$size = round($song['size'] / (1024 * 1024), 2);
								
								// Calculate length
								$length = round($song['length'] * 0.0166666667, 2);
								
								// Calculate total
								$total['size'] += $size;
								$total['length'] += $length;
								$total['price'] += $price;
						?>
							<tr>
								<td>&nbsp;</td>
								<td><?php echo $song['track'];?></td>
								<td><?php echo $song['name'];?></td>
								<td><?php echo $song['mode'];?></td>
								<td><?php echo $size; ?> Mb</td>
								<td><?php echo $length; ?></td>
								<td><?php echo $song['bitrate']; ?></td>
								<td><?php echo $price;?></td>
								<td>
									<a href="<?php echo $this->createUrl('/song/download/', array('orderSongId' => $orderSong['id'])); ?>"><?php echo $orderTranslations['download']; ?></a>
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
					<div class="cart_total"><span class="catr_txt"><?php echo $translations['total_time'];?>:</span><span id="total_time"><?php echo $total['length']; ?></span><b> <?php echo $songTranslations['min'];?></b></div>
					<div class="cart_total"><span class="catr_txt"><?php echo $translations['total_size'];?>:</span><span id="total_size"><?php echo $total['size']; ?></span><b> Mb</b></div>
					<div class="cart_total"><span class="catr_txt"><?php echo $translations['total_price'];?>:</span><span id="total_price"><?php echo $total['price']; ?></span><b> $</b></div>
				</div>
			</td>
		</tr>
		</table>
	</div>
<?php } ?>