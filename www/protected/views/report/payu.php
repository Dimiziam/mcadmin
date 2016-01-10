<?php
/**
 *
 * @var ReportController $this
 * @var PsiWhiteSpace $paypalReportData
 * @var PsiWhiteSpace $dateFrom
 * @var PsiWhiteSpace $dateTo
 */

// Get report translations
$translations = MTLang::instance()->getGroup('report');
?>

<?php echo $this->renderPartial('_form',
	array(
		'translations' => $translations,
		'ReportData' =>$payUReportData,
		'title' =>$translations['title_payu']
	)
);
?>


<?php

if (!empty($payUReportData)) {
	$total = 0;

	$ReportData = $payUReportData->getData()
	?>


	<table class="table table-striped">
		<tr>
			<th ">#</th>
			<th "><?php echo $translations['created']; ?></th>
			<th <?php echo $translations['email']; ?></th>
			<th><?php echo $translations['name']; ?></th>
			<th><?php echo $translations['county_code']; ?></th>
			<td><?php echo $translations['amount']; ?></td>

		</tr>

		<?php foreach ($ReportData as $item) {
			$total += round($item['amount']);
			?>
			<tr>
				<td><?php echo $item['id']; ?></td>
				<td nowrap><?php echo date("Y-m-d", strtotime($item['date_end'])); ?></td>
				<td nowrap><?php echo $item['email']; ?></td>
				<td nowrap><?php echo $item['name']; ?>
					<?php echo $item['last_name']; ?></td>
				<td nowrap><?php echo $item['country_code']; ?></td>
				<td nowrap><?php echo $item['amount']; ?></td>



			</tr>
		<?php } ?>
		<tr>
			<td nowrap colspan="10"><?php echo $translations['total_on_this_page'] . ":" . $total ?> </td>
		</tr>
	</table>
<?php } ?>

</div>






