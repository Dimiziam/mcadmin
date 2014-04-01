<?php 
	// Get album translations
	$translations = MTLang::instance()->getGroup('album');
	
	// Get song translations
	$songTranslations = MTLang::instance()->getGroup('song');
?>

<table width="100%">
	<tr>
		<td>
			<?php 
				$this->widget('application.components.widgets.WCalendar', 
					array(
						'currentDate' => $currentDate,
						'settings' => array(
							'url' => array(
								'rout' => '/album/history/',
								'params' => array(
									'genreId' => $curentGenreId
								)
							),
							'marks' => $calendarInfo
						)
					)
				); 
			?>
		</td>
		<td>
			
			<?php 
				// Get album translations
				$translations = MTLang::instance()->getGroup('album');
			?>
				<div class="discography_detail">
					<div class="grid_12">
						<div class="singer-album_header">
							<?php 
								if (!empty($albums)) {
									// Get albums items
									$items = $albums->getData();
							?>
								<?php echo $translations['history_page_caption'] . ': ' . $currentDate; ?>&nbsp;<span class="total">(<?php echo sprintf($translations['total_albums'], $albums->totalItemCount); ?>)</span>
							<?php } ?>
							&nbsp;
							<?php 
								// Get all genres
								$genres = Genre::model()->getAll();
								
								// Prepare options
								$options = array();
								foreach ($genres as $genre) {
									// Create URL
									$url = $this->createUrl('/album/history/', array('date' => $currentDate, 'genreId' => $genre['id']));
									
									$options[$url] = $genre['name'];
								}
							
								// Prepare current genre option
								$curentUrl = $this->createUrl('/album/history/', array('date' => $currentDate, 'genreId' => $curentGenreId));
								
								// Display genres selecte box
								echo CHtml::dropDownList('genres', $curentUrl, $options, 
									array(
										'empty' => array($this->createUrl('/album/history/', array('date' => $currentDate, 'genreId' => 0)) => MTLang::t('genres', 'top_menu')),
										'onchange' => 'window.location = this.value;'
									)
								); 
							?>
						</div>
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
									if (!empty($albums)) {
										$this->widget('application.components.widgets.Paginator',
											array(
												'pages' => $albums->getPagination(),
											)
										);
									
										// Prepare current index
										$index = 0;
										
										foreach($items as $album) {
											$index ++;
											
											// Calculate price
											$location = 0;
											$price = round(Yii::app()->params['royalty'] * 100 * $album['songs_number'] + (($album['size'] / (1024 * 1024)) * Yii::app()->params['track_price'][$location]['price'])) / 100;
								?>
									<tr class="album <?php if ($index % 2 != 0) {echo 'bg';}?>">
										<td>
											<img title="<?php echo $album['title'];?>" 
												alt="<?php echo $album['title'];?>" width=60px" height="60px"
												src="<?php $this->widget('application.components.widgets.images.WAlbum', array('singerId' => $album['singer']['id'], 'albumId' => $album['id'], 'type' => 'tcover')); ?>"
											/>
										</td>
										<td class="singet-album-list"><?php echo $album['year'];?></td>
										<td>
											<?php 
												// Prepare url params
												$params = array(
													'id' => $album['singer']['id'],
													'alias' => $album['singer']['alias'],
													'letter' => substr($album['singer']['alias'], 0, 1)
												);
											?>
											<a href="<?php echo $this->createUrl('/singer/', $params); ?>"><?php echo $album['singer']['name']; ?></a><br />
											<?php 
												// Prepare url params
												$params = array(
													'id' => $album['id'],
													'alias' => $album['alias'],
													'singerLetter' => substr($album['singer']['alias'], 0, 1),
													'singerAlias' => $album['singer']['alias']
												);
											?>
											<a href="<?php echo $this->createUrl('/album/', $params); ?>" title="<?php echo $album['title'];?>"><?php echo $album['title'];?></a>
											<?php if (strtotime($album['updated']) + 864000 > time()) {echo '<span class="new">(new)</span>';}?>
											<?php if (strtotime($album['updated']) + 864000 * 10 > time()) {echo '<span class="fresh">(fresh)</span>';}?>
											<br>
											<?php if (!empty($album['genres'])) { ?>
												<div class="song_style">
													<?php 
														$index = 0;
														foreach ($album['genres'] as $genre) {
															$index ++;
													?>
														<a href="<?php echo Yii::app()->getController()->createUrl('/genre/', array('alias' => $genre['alias'], 'id' => $genre['id'])); ?>"><?php echo $genre['name']; ?></a>
														<?php if ($index > 0 && $index < sizeof($album['genres'])) {echo '&nbsp;/&nbsp;';} ?>
													<?php } ?>
												</div>
											<?php } ?>
											
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
								<?php }} ?>
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
			
		</td>
	</tr>
</table>