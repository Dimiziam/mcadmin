<?php 
	// Get singer translations
	$translations = MTLang::instance()->getGroup('singer');

	// Get album translations
	$albumTranslations = MTLang::instance()->getGroup('album');
	
	// Get song translations
	$songTranslations = MTLang::instance()->getGroup('song');
	
	if (empty($singer)) {
?>
	<h1><?php echo $translations['not_found']; ?></h1>
<?php } else { ?>
	<div class="grid_12" id="singerinfo">
		<h1><?php echo !empty($singer['name']) ? $singer['name'] : $singer['pseudonym'];?></h1>
		<div class="singer_social_block">
			<?php $this->renderPartial('../include/social', array()); ?>
		</div>
		<div class="clear"></div>
	</div>
	<div class="singer_detail">
		<div class="grid_4 singer_img">
			<img class="shadow" 
				alt="<?php echo !empty($singer['name']) ? $singer['name'] : $singer['pseudonym'];?>" 
				src="<?php $this->widget('application.components.widgets.images.WAlbum', array('singerId' => $singer['id'], 'albumId' => $album['id'], 'type' => 'cover')); ?>"
			/>
		</div>
		<div class="grid_8 ">
			<div class="singer_data">
				<div class="dataline nopadding">
					<div class="singer_caption"><?php echo $translations['band']; ?>:</div>
					<div class="singer_detail_info"><span class="band"><?php echo !empty($singer['name']) ? $singer['name'] : $singer['pseudonym']; ?></span></div>
				</div>
				
				<div class="dataline">
					<div class="singer_caption"><?php echo $albumTranslations['type']; ?>:</div>
					<div class="singer_detail_info"><?php
						// Get type alias 
						$typeAlias = Yii::app()->params['album_types'][$album['type']];
						
						// Display type
						echo $albumTranslations[$typeAlias];
					?></div>
				</div>
				
				<?php if (!empty($album['genres'])) { ?>
					<div class="dataline">
						<div  class="singer_caption"><?php echo $translations['genre']; ?>:</div>
							<div class="singer_detail_info">
								<?php 
									$index = 0;
									foreach ($album['genres'] as $id => $genre) {
										$index ++;
								?>
									<a href="<?php echo $this->createUrl('/genre/', array('alias' => $genre['alias'], 'id' => $id)); ?>"><?php echo $genre['name']; ?></a>
									<?php if ($index > 0 && $index < sizeof($album['genres'])) {echo '&nbsp;/&nbsp;';} ?>
								<?php } ?>
						</div>
					</div>
				<?php } ?>
				
				<?php if (!empty($album['styles'])) { ?>
					<div class="dataline">
						<div  class="singer_caption"><?php echo $translations['style']; ?>:</div>
							<div class="singer_detail_info">
								<?php 
									$index = 0;
									foreach ($album['styles'] as $style) {
										$index ++;
								?>
									<a href="<?php echo $this->createUrl('/style/', array('alias' => $style['alias'], 'id' => $style['id'], 'genreId' => $style['genre_id'])); ?>"><?php echo $style['name']; ?></a>
									<?php if ($index > 0 && $index < sizeof($album['styles'])) {echo '&nbsp;/&nbsp;';} ?>
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

	<div class="track_detail">
		<div class="grid_12">
			<h2><?php echo $album['title'];?></h2>
			<div class="clear"></div>
			<div class="album-track">
				<?php if (empty($songs)) { ?>
					<h2><?php echo $songTranslations['not_found'];?></h2>
				<?php } else { ?>
					<!-- track list -->
					<table border=1 cellspacing=0 width="100%" class="track-list" id="track-list">
						<tr class="title_th">
							<th width="20px">#</th>
							<th width="340px"><?php echo $songTranslations['track_title'];?></th>
							<th width="80px"><?php echo $songTranslations['mode'];?></th>
							<th width="60px"><?php echo $songTranslations['size'];?></th>
							<th width="60px"><?php echo $songTranslations['length'];?></th>
							<th width="60px"><?php echo $songTranslations['bitrate'];?></th>
							<th width="60px"><?php echo $songTranslations['lyrics'];?></th>
							<th width="60px"><?php echo $songTranslations['check'];?> <a href="#" class="track_check_all"><img src="/images/check_box_check.png"></a></th>
							<th width="60px"><?php echo $songTranslations['price'];?></th>
							<th width="60px"><?php echo $songTranslations['listen'];?></th>
						</tr>
						<tr>
							<td colspan="10" class="td_height"></td>
						</tr>
						<?php
							// Prepare index
							$index = 0;
						
							foreach($songs as $song) {
								$index ++;
								
								// Calculate price
								$location = 0;
								$price = round(Yii::app()->params['royalty'] * 100 + ($song['size'] / (1024 * 1024)) * Yii::app()->params['track_price'][$location]['price']) / 100;
						?>
							<tr class="track <?php if ($index % 2 != 0) {echo 'bg';}?>">
								<td><?php echo $song['track'];?></td>
								<td class="title_left"><?php echo $song['name'];?></td>
								<td><?php echo $song['mode'];?></td>
								<td><?php echo round($song['size'] / (1024 * 1024), 2); ?> Mb</td>
								<td><?php echo round($song['length'] * 0.0166666667, 2); ?></td>
								<td><?php echo $song['bitrate']; ?></td>
								<td>
									<?php 
										if (!empty($song['text'])) {
											// Prepare url params
											$params = array(
												'id' => $song['id'],
												'alias' => $song['alias'],
												'albumAlias' => $album['alias'],
												'singerLetter' => substr($singer['alias'], 0, 1),
												'singerAlias' => $singer['alias']
											);
									?>
										<a href="<?php echo $this->createUrl('/song/lyrics/', $params); ?>" title="<?php echo $songTranslations['lyrics'] . ': ' . $song['name'];?>" class="song_text_link">
											<img src="/images/lyrics.png">
										</a>
									<?php } ?>
								</td>
								<td>
									<input type="hidden" name="size_<?php echo $song['id'];?>" value="<? echo $song['size'];?>">
									<input type="hidden" name="time_<?php echo $song['id'];?>" value="<? echo $song['length'];?>">
									<input type="hidden" name="price_<?php echo $song['id'];?>" value="<? echo $price;?>">
									<input id="box_<?php echo $song['id'];?>" class="css-checkbox" type="checkbox" name="song_id_<?php echo $song['id'];?>" value="<?php echo $song['id'];?>">
									<label for="box_<?php echo $song['id'];?>" name="song_id_<?php echo $song['id'];?>" class="css-label"></label>
								</td>
								<td><?php echo $price;?></td>
								<td><a href=""><img src="/images/play.png"></a></td>
							</tr>
						<?php } ?>
						<tr>
							<td colspan="10" class="td_height"></td>
						</tr>
					</table>
					<div class="clear"></div>
					<div class="grid_8">
						<div class="track_list_info">
							<?php echo $songTranslations['to_download'];?>
						</div>
					</div>
					<div class="grid_3">
						<a href="JavaScript: void(0);" class="buy_track" id="buy_tracks">
							<button name="buy_button" class="buy_button" type="submit">
								<img src="/images/cart_ico.png"><?php echo $songTranslations['add_to_cart'];?>
							</button>
						</a><br><br>
						<div class="cart_total"><span class="catr_txt"><?php echo $songTranslations['total_time'];?>:</span><span id="total_time">0</span><b> <?php echo $songTranslations['min'];?></b></div>
						<div class="cart_total"><span class="catr_txt"><?php echo $songTranslations['total_size'];?>:</span><span id="total_size">0</span><b> Mb</b></div>
						<div class="cart_total"><span class="catr_txt"><?php echo $songTranslations['total_price'];?>:</span><span id="total_price">0</span><b> $</b></div>
					</div>
					
					<div id="song_text_dialog" title="Text not found" style='display: none;'></div>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>