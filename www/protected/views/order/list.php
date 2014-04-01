<?php
	// Get order translations
	$translations = MTLang::instance()->getGroup('order');
?>

<h2><?php echo $translations['orders']; ?></h2>

<?php if (empty($orders)) { ?>
	<h1><?php echo $translations['not_found']; ?></h1>
<?php } else { ?>

	<table width="100%">
	<tr>
		<td width="20%"><?php $this->renderPartial('application.views.user._profile.menu', array('currentKey' => 'orders')); ?></td>
		<td>
		
			<table border=1 cellspacing=0 class="track-list">
				<tr class="title_th">
					<th width="20px">#</th>
					<th width="100px"><?php echo $translations['songs_number'];?></th>
					<th width="60px"><?php echo $translations['price'];?>&nbsp;$</th>
					<th width="150px"><?php echo $translations['created'];?></th>
					<th width="60px">&nbsp;</th>
				</tr>
				<tr>
					<td colspan="5" class="td_height"></td>
				</tr>
				<?php
					// Prepare index
					$index = 0;
				
					foreach($orders as $order) {
						$index ++;
				?>
					<tr class="<?php if ($index % 2 != 0) {echo 'bg';}?>">
						<td><?php echo $order['id'];?></td>
						<td><?php echo $order['songs_number'];?></td>
						<td><?php echo $order['price'];?></td>
						<td><?php echo $order['created'];?></td>
						<td>
							<a href="<?php echo $this->createUrl('/order/songs/', array('id' => $order['id'])); ?>"><?php echo $translations['songs']; ?></a>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<td colspan="10" class="td_height"></td>
				</tr>
			</table>
			
		</td>
	</tr>
	</table>
	
<?php } ?>