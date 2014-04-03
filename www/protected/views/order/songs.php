<?php 
	// Get song translations
	$translations = MTLang::instance()->getGroup('song');

	$this->breadcrumbs = array(
		$translations['orders_list'] => $this->createUrl('/order/search/'),
		'#' . $orderId
	);
	
	if (!empty($songs)) {
?>
	<table class="list_table">
		<?php 
			$currentAlbum = '';
			foreach ($songs as $item) {
				if (empty($currentAlbum) || $currentAlbum != $item['title']) {
					$currentAlbum = $item['title'];
		?>
				<tr>
					<th colspan="2"><?php echo $item['title']; ?></th>
				</tr>
				<tr>
					<th><?php echo $translations['name']; ?></th>
					<th><?php echo $translations['price']; ?></th>
				</tr>
			<?php } ?>
			<tr>
				<td><?php echo $item['name']; ?></td>
				<td><?php echo $item['price']; ?></td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<h2><?php echo $translations['songs_not_found']; ?></h2>
<?php } ?>