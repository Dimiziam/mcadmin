<?php
/**
 * Template for lists.WLatestAlbums widget
 * 
 * @package MediaClub
 * @subpackage views
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */

	// Get album translations
	$translations = MTLang::instance()->getGroup('album');
?>

<div class="grid_4">
	<div class="block_header"><?php echo $translations['latest_block_caption']; ?></div>
	<div class="clear"></div>
	<div class="body_block_latest">
		<div class="block_content_latest">
			<ul>
				<?php 
					$index = 0;
					$created = null;
					foreach ($albums as $id => $album) {
						$index ++;
						$created = !empty($created) ? $created : $album['created'];
				?>
					<li> 
						<span><?php echo $index; ?></span>
						<div class="tumb">
							<img title="<?php echo $album['title'];?>" 
								alt="<?php echo $album['title'];?>" width=60px" height="60px"
								src="<?php $this->widget('application.components.widgets.images.WAlbum', array('singerId' => $album['singer']['id'], 'albumId' => $album['id'], 'type' => 'tcover')); ?>"
							/>
						</div>
						<div class="song_info">
							<?php 
								// Prepare url params
								$params = array(
									'id' => $album['singer']['id'],
									'alias' => $album['singer']['alias'],
									'letter' => substr($album['singer']['alias'], 0, 1)
								);
							?>
							<a href="<?php echo Yii::app()->getController()->createUrl('/singer/', $params); ?>"><span class="song_artist"><?php echo $album['singer']['name']; ?></span></a><br />
							<?php 
								// Prepare url params
								$params = array(
									'id' => $album['id'],
									'alias' => $album['alias'],
									'singerLetter' => substr($album['singer']['alias'], 0, 1),
									'singerAlias' => $album['singer']['alias']
								);
							?>
							<a href="<?php echo Yii::app()->getController()->createUrl('/album/', $params); ?>" title="<?php echo $album['title'];?>"><?php echo $album['title'];?></a>
						</div>
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
						<div class="clear"></div>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="more">
			<a href="<?php echo Yii::app()->getController()->createUrl('/album/history/', array('date' => $created, 'genreId' => 0)); ?>"><?php echo $translations['more']; ?> <img src="/images/more_arrow_right.png" /></a>
		</div>
	</div>
</div>


