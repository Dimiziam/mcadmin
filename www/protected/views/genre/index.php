<table width="100%">
	<tr>
		<td>
			<ul>
				<?php foreach ($genres as $id => $genre) { ?>
					<li>
						<a href="<?php echo $this->createUrl('/genre/', array('alias' => $genre['alias'], 'id' => $id)); ?>" 
							title="<?php echo $genre['name']; ?>"
							<?php if ($id == $currentGenreId) {echo 'style="font-weight: bold;"';} ?>
						><?php echo $genre['name']; ?></a>
					</li>
				<?php } ?>
			</ul>
		</td>
		<td>
			<?php if (!empty($styles)) { ?>
				<ul>
					<?php foreach ($styles as $style) { ?>
						<li>
							<a href="<?php echo $this->createUrl('/style/', array('alias' => $style['alias'], 'id' => $style['id'], 'genreId' => $style['genre_id'])); ?>" title="<?php echo $style['name']; ?>"><?php echo $style['name']; ?></a>
						</li>
					<?php } ?>
				</ul>
			<?php } ?>
		</td>
	</tr>
</table>