<?php
// Get report translations
$translations = MTLang::instance()->getGroup('report');
?>

<?php echo $this->renderPartial('_form',
		array(
			'translations' => $translations,
			'ReportData' =>$paypalReportData,
			'title' =>$translations['title_paypal']
		)
	);
?>


	<?php

	if (!empty($paypalReportData)) {
		$total = 0;

		$ReportData = $paypalReportData->getData()
		?>


		<table class="table table-striped">
			<tr>
				<th rowspan="2">#</th>
				<th rowspan="2"><?php echo $translations['created']; ?></th>
				<th rowspan="2"<?php echo $translations['email']; ?></th>
				<th rowspan="2"><?php echo $translations['name']; ?></th>
				<th rowspan="2"><?php echo $translations['county_code']; ?></th>
				<th colspan="5" style="text-align: center;"><?php echo $translations['paypal']; ?></th>
			</tr>
			<tr>
				<td><?php echo $translations['paymentid']; ?></td>
				<td><?php echo $translations['email']; ?></td>
				<td><?php echo $translations['name']; ?></td>
				<td><?php echo $translations['county_code']; ?></td>
				<td><?php echo $translations['amount']; ?></td>
			</tr>
			<?php foreach ($ReportData as $item) {
				$total += round($item['amount']);
				?>
				<tr>
					<td><?php echo $item['id']; ?></td>
					<td nowrap><?php echo date("Y-m-d", strtotime($item['date_end'])); ?></td>
					<td nowrap><?php echo $item['email']; ?></td>
					<td nowrap><?php echo $item['name']; ?><br>
					<?php echo $item['last_name']; ?></td>
					<td nowrap><?php echo $item['country_code']; ?></td>

					<?php
					$paypaInfo = unserialize($item['info']);

					if (!empty($paypaInfo['detailPayment'])) {
						echo '<td>'.$paypaInfo['paymentId'].'</td>';
						echo '<td>'.$paypaInfo['detailPayment']['email'].'</td>';
						echo '<td>'.$paypaInfo['detailPayment']['first_name'].'<br>';
						echo $paypaInfo['detailPayment']['last_name'].'</td>';
						echo '<td>'.$paypaInfo['detailPayment']['county_code'].'</td>';
						echo '<td>'.$paypaInfo['detailPayment']['amount'].'</td>';
					}
					else {?>
						<td><?php echo $paypaInfo['paymentId'];?></td>
						<td colspan="4">&nbsp;</td>
					<?php }

					?>

				</tr>
			<?php } ?>
			<tr>
				<td nowrap colspan="10"><?php echo $translations['total_on_this_page'] . ":" . $total ?> </td>
			</tr>
		</table>
	<?php } ?>

</div>






