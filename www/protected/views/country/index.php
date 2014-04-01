<table width="100%">
	<tr>
		<td>
			<ul>
				<?php foreach ($countries as $id => $country) { ?>
					<li>
						<a href="<?php echo $this->createUrl('/country/', array('alias' => $country['alias'], 'id' => $id)); ?>" 
							title="<?php echo $country['name']; ?>"
							<?php if ($id == $currentCountryId) {echo 'style="font-weight: bold;"';} ?>
						><?php echo $country['name']; ?></a>
					</li>
				<?php } ?>
			</ul>
		</td>
		<td>
			<?php if (!empty($singers)) { ?>
				<ul>
					<?php 
						foreach ($singers as $singer) {
							// Prepare url params
							$params = array(
								'id' => $singer['id'],
								'alias' => $singer['alias'],
								'letter' => substr($singer['alias'], 0, 1)
							);
					?>
						<li>
							<a href="<?php echo $this->createUrl('/singer/', $params); ?>" 
								title="<?php echo $singer['name']; ?>"
							><?php echo $singer['name'] . (!empty($singer['pseudonym']) ? ' (<i>' . $singer['pseudonym'] . '</i>)' : ''); ?></a>
						</li>
					<?php } ?>
				</ul>
			<?php } ?>
		</td>
	</tr>
</table>