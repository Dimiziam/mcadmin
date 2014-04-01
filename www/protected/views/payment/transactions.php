<?php
	// Get order translations
	$translations = MTLang::instance()->getGroup('transaction');
?>

<h2><?php echo $translations['payments']; ?></h2>

<?php if (empty($transactions)) { ?>
	<h1><?php echo $translations['payments_not_found']; ?></h1>
<?php } else { ?>

	<h3><?php 
		// Get user info
		$userInfo = Yii::app ()->user->getInfo();
		
		echo sprintf($translations['current_amount'], ($userInfo['amount'] . '$')); 
	?></h3>

	<table width="100%">
	<tr>
		<td width="20%"><?php $this->renderPartial('application.views.user._profile.menu', array('currentKey' => 'transactions')); ?></td>
		<td>
		
			<table border=1 cellspacing=0 class="track-list">
				<tr class="title_th">
					<th width="20px">#</th>
					<th width="150px"><?php echo $translations['status'];?></th>
					<th width="60px"><?php echo $translations['amount'];?>&nbsp;$</th>
					<th width="150px"><?php echo $translations['date'];?></th>
					<th width="60px">&nbsp;</th>
				</tr>
				<tr>
					<td colspan="5" class="td_height"></td>
				</tr>
				<?php
					// Prepare index
					$index = 0;
				
					foreach($transactions as $transaction) {
						$index ++;
				?>
					<tr class="<?php if ($index % 2 != 0) {echo 'bg';}?>">
						<td><?php echo $transaction['id'];?></td>
						<td><?php echo $translations['status_' . $transaction['status']];?></td>
						<td class="transaction_<?php echo $transaction['type']; ?>">
							<?php echo ($transaction['type'] == 'add' ? '+' : '-') . $transaction['amount'];?>
						</td>
						<td><?php echo $transaction['date_start'];?></td>
						<td>
							<?php if (!empty($transaction['order_id'])) { ?>
								<a href="<?php echo $this->createUrl('/order/songs/', array('id' => $transaction['order_id'])); ?>"><?php echo $translations['go_to_order']; ?></a>
							<?php } else { ?>
								&nbsp;
							<?php } ?>
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