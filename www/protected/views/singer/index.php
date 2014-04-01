<?php 
	// Get singer translations
	$translations = MTLang::instance()->getGroup('singer');
	
	// Get song translations
	$songTranslations = MTLang::instance()->getGroup('song');

	if (empty($singer)) {
?>
	<h1><?php echo $translations['not_found']; ?></h1>
<?php } else { ?>
	<div  class="grid_12" id="singerinfo">
		<h1><?php echo !empty($singer['name']) ? $singer['name'] : $singer['pseudonym'];?></h1>
		<div class="singer_social_block">
			<?php $this->renderPartial('../include/social',array()); ?>
		</div>
		<div class="clear"></div>
	</div>
	<div class="singer_detail">
		<div class="grid_4 singer_img">
			<img class="shadow" 
				alt="<?php echo !empty($singer['name']) ? $singer['name'] : $singer['pseudonym'];?>"
				src="<?php $this->widget('application.components.widgets.images.WSinger', array('singerId' => $singer['id'])); ?>"
			/>
		</div>
		<div class="grid_8 ">
			<div class="singer_data">
				<div class="dataline nopadding">
					<div class="singer_caption"><?php echo $translations['band']; ?>:</div>
					<div class="singer_detail_info"><span class="band"><?php echo !empty($singer['name']) ? $singer['name'] : $singer['pseudonym']; ?></span></div>
				</div>
	
				<?php if (!empty($singer['country_id'])) { ?>
					<div class="dataline">
						<div  class="singer_caption"><?php echo $translations['country']; ?>:</div>
						<div class="singer_detail_info">
							<a href="<?php echo $this->createUrl('/country/', array('alias' => $singer['country']['alias'], 'id' => $singer['country_id'])); ?>"><?php echo !empty($singer['country']['name']) ? $singer['country']['name'] : $singer['country']['alias'];?></a>
						</div>
					</div>
				<?php } ?>

				<?php if (!empty($singer['genres'])) { ?>
					<div class="dataline">
						<div  class="singer_caption"><?php echo $translations['genre']; ?>:</div>
							<div class="singer_detail_info">
								<?php 
									$index = 0;
									foreach ($singer['genres'] as $id => $genre) {
										$index ++;
								?>
									<a href="<?php echo $this->createUrl('/genre/', array('alias' => $genre['alias'], 'id' => $id)); ?>"><?php echo $genre['name']; ?></a>
									<?php if ($index > 0 && $index < sizeof($singer['genres'])) {echo '&nbsp;/&nbsp;';} ?>
								<?php } ?>
						</div>
					</div>
				<?php } ?>

				<?php if (!empty($singer['styles'])) { ?>
					<div class="dataline">
						<div  class="singer_caption"><?php echo $translations['style']; ?>:</div>
							<div class="singer_detail_info">
								<?php 
									$index = 0;
									foreach ($singer['styles'] as $style) {
										$index ++;
								?>
									<a href="<?php echo $this->createUrl('/style/', array('alias' => $style['alias'], 'id' => $style['id'], 'genreId' => $style['genre_id'])); ?>"><?php echo $style['name']; ?></a>
									<?php if ($index > 0 && $index < sizeof($singer['styles'])) {echo '&nbsp;/&nbsp;';} ?>
								<?php } ?>
						</div>
					</div>
				<?php } ?>

				<?php if (!empty($singer['description'])) {?>
					<div class="dataline">
						<div class="singer_caption"><?php echo $translations['info']; ?>:</div>
						<div class="singer_detail_info"><?php echo mb_substr(stripcslashes($singer['description']), 0, 400, 'UTF-8'); ?></div>
					</div>
				<?php }?>

			</div>
		</div>
	</div>
	
	<div class="grid_12 spacer30"></div>
	<?php 
		if (!empty($albums)) {
			// Get albums items
			$items = $albums->getData();
			
			// Get album translations
			$translations = MTLang::instance()->getGroup('album');
	?>
		<div class="discography_detail">
			<div class="grid_12">
				<div class="singer-album_header"><?php echo $translations['discography']; ?>&nbsp;<span class="total">(<?php echo sprintf($translations['total_albums'], $albums->totalItemCount); ?>)</span></div>
				<div class="clear"></div>
				<div class="singer-album-list">
					<!-- album list -->
					<table border=1 cellspacing=0 width="100%" class="album-list" id="album-list">
						<tr class="title_th">
							<th width="80px"><?php echo $translations['cover']; ?></th>
							<th width="60px"><?php echo $translations['year']; ?></th>
							<th width="360px"><?php echo $translations['title']; ?></th>
							<th width="60px"><?php echo $translations['track']; ?></th>
							<th width="60px"><?php echo $translations['bitrate']; ?></th>
							<th width="60px"><?php echo $translations['size'];?></th>
							<th width="60px"><?php echo $translations['length'];?></th>
							<th width="60px"><?php echo $translations['check'];?> <a href="#" class="album_check_all"><img src="/images/check_box_check.png"></a></th>
							<th width="60px"><?php echo $translations['price'];?></th>
							<th width="100px"><?php echo $translations['dates']; ?></th>
						</tr>
						<?php
							$this->widget('application.components.widgets.Paginator',
								array(
									'pages' => $albums->getPagination(),
								)
							);
						?>
						<?php 
							// Prepare current type
							$currentType = 0;
						
							// Prepare current index
							$index = 0;
							
							foreach($items as $album) {
								$index ++;
								
								// Calculate price
								$location = 0;
								$price = round(Yii::app()->params['royalty'] * 100 * $album['songs_number'] + (($album['size'] / (1024 * 1024)) * Yii::app()->params['track_price'][$location]['price'])) / 100;
						?>
							<?php 
								if (empty($currentType) || $currentType != $album['type']) {
									// Change current album type
									$currentType = $album['type'];
							?>
								<tr class="singer-album-title">
									<td colspan="6"><?php 
										// Get translation alias
										$translationAlias = Yii::app()->params['album_types'][$currentType];
										
										// Display caption
										echo !empty($translations[$translationAlias]) ? $translations[$translationAlias] : ucfirst($translationAlias);
									?></td>
								</tr>
							<?php } ?>
							<tr class="album <?php if ($index % 2 != 0) {echo 'bg';}?>">
								<td>
									<img title="<?php echo $album['title'];?>" 
										alt="<?php echo $album['title'];?>" width=60px" height="60px"
										src="<?php $this->widget('application.components.widgets.images.WAlbum', array('singerId' => $singer['id'], 'albumId' => $album['id'], 'type' => 'tcover')); ?>"
									/>
								</td>
								<td class="singet-album-list"><?php echo $album['year'];?></td>
								<td>
									<?php 
										// Prepare url params
										$params = array(
											'id' => $album['id'],
											'alias' => $album['alias'],
											'singerLetter' => substr($singer['alias'], 0, 1),
											'singerAlias' => $singer['alias']
										);
									?>
									<a href="<?php echo $this->createUrl('/album/', $params); ?>" title="<?php echo $album['title'];?>"><?php echo $album['title'];?></a>
									<?php if (strtotime($album['updated']) + 864000 > time()) {echo '<span class="new">(new)</span>';}?>
									<?php if (strtotime($album['updated']) + 864000 * 10 > time()) {echo '<span class="fresh">(fresh)</span>';}?>
								</td>
								<td class="singet-album-list"><?php echo !empty($album['songs_number']) ? $album['songs_number'] : 0;?></td>
								<td class="singet-album-list"><?php echo !empty($album['bitrate']) ? $album['bitrate'] : '---';?></td>
								<td class="singet-album-list"><?php echo round($album['size'] / (1024 * 1024), 2); ?>&nbsp;Mb</td>
								<td class="singet-album-list"><?php echo round($album['length'] * 0.0166666667, 2); ?></td>
								<td>
									<input type="hidden" name="size_<?php echo $album['id'];?>" value="<? echo $album['size'];?>">
									<input type="hidden" name="time_<?php echo $album['id'];?>" value="<? echo $album['length'];?>">
									<input type="hidden" name="price_<?php echo $album['id'];?>" value="<? echo $price;?>">
									<input id="box_<?php echo $album['id'];?>" class="css-checkbox" type="checkbox" name="album_id_<?php echo $album['id'];?>" value="<?php echo $album['id'];?>">
									<label for="box_<?php echo $album['id'];?>" name="album_id_<?php echo $album['id'];?>" class="css-label"></label>
								</td>
								<td class="singet-album-list"><?php echo $price;?></td>
								<td class="singet-album-list-date">
									<?php echo date('m.d.Y', strtotime($album['updated']));?>
									<?php  if ($album['created'] != $album['updated']) { echo " / " . date('m.d.Y',strtotime($album['updated']));}?>
								</td>
							</tr>
						<?php }?>
					</table>
					<div class="clear"></div>
					<div class="grid_8">
						<div class="track_list_info">
							<?php echo $translations['to_download'];?>
						</div>
					</div>
					<div class="grid_3">
						<a href="#" class="buy_track" id="buy_albums">
							<button name="buy_button" class="buy_button" type="submit">
								<img src="/images/cart_ico.png"><?php echo $songTranslations['add_to_cart'];?>
							</button>
						</a><br><br>
						<div class="cart_total"><span class="catr_txt"><?php echo $songTranslations['total_time'];?>:</span><span id="total_time">0</span><b> <?php echo $songTranslations['min'];?></b></div>
						<div class="cart_total"><span class="catr_txt"><?php echo $songTranslations['total_size'];?>:</span><span id="total_size">0</span><b> Mb</b></div>
						<div class="cart_total"><span class="catr_txt"><?php echo $songTranslations['total_price'];?>:</span><span id="total_price">0</span><b> $</b></div>
					</div>
					<!-- // album list -->
				</div>
			</div>
		</div>
	<?php } ?>
<?php }?>