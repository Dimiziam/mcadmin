<?php
/**
 * Template for WPager widget
 * 
 * @package MediaClub
 * @subpackage views
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */

// Get pager translations
$translations = MTLang::instance()->getGroup('pager');

// Get current page
$page = !empty($_POST[Yii::app()->params['pager']['variable']['current']]) ? (int) $_POST[Yii::app()->params['pager']['variable']['current']] : 1;

// Get rows per page
if (!empty($_POST[Yii::app()->params['pager']['variable']['rpp']])) { 
	$rpp = (int) $_POST[Yii::app()->params['pager']['variable']['rpp']];
} else {
	$rpp = !empty(Yii::app()->params['pager']['rpp']) ? Yii::app()->params['pager']['rpp'] : 20;
}

// Get total rows number
$totalRowsNumber = !empty($provider->totalItemCount) ? $provider->totalItemCount : 0;

// Calculate total pages
$totalPages = ceil($totalRowsNumber / $rpp);
?>

<table>
	<tr>
		<td align='left'>
			<nobr>
			<input 
				type='submit'
				value='<?php echo $translations['to_first_page']; ?>'
				<?php if ($page == 1) {echo 'disabled';} ?>
				onClick='$("#pager_currentPageField").attr("value", 1);'
			/>
			<input 
				type='submit'
				value='< '
				<?php if ($page == 1) {echo 'disabled';} ?>
				onClick='$("#pager_currentPageField").attr("value", parseInt($("#pager_currentPageField").attr("value")) - 1)'
			/>
			</nobr>
		</td>
		<td align='center' style='vertical-align: middle;'>
			<nobr><?php echo $translations['found_rows']; ?>: <b><?php echo $totalRowsNumber;?></b></nobr>
		</td>
		<td align='center'>
			<nobr>
			<b><?php echo $translations['page']; ?>:</b>
			<input 
				type='text' 
				name='<?php echo Yii::app()->params['pager']['variable']['current']; ?>'
				id='pager_currentPageField'
				value='<?php echo $page;?>'
				style='width: 40px;'
			/>
			&nbsp;<b><?php echo $translations['from'] . ' ' . $totalPages;?></b>
			&nbsp;<input type='submit' value='<?php echo $translations['goto']; ?>' />
			</nobr>
		</td>
		<td align='center'>
			<nobr>
			<input 
				type='text' 
				name='<?php echo Yii::app()->params['pager']['variable']['rpp']; ?>' 
				value='<?php echo $rpp;?>'
				size='3'
			/>
			&nbsp;<?php echo $translations['rpp']; ?>&nbsp;
			<input 
				type='submit' 
				value='<?php echo $translations['found']; ?>'
				onClick='$("#pager_currentPageField").attr("value", 1);'
			/>
			</nobr>
		</td>
		<td align='right'>
			<nobr>
			<input 
				type='submit' 
				value='> '
				<?php if ($page == $totalPages) {echo 'disabled';} ?>
				onClick='$("#pager_currentPageField").attr("value", parseInt($("#pager_currentPageField").attr("value")) + 1)'
			/>
			<input 
				type='submit' 
				value='<?php echo $translations['to_end']; ?>'
				<?php if ($page == $totalPages) {echo 'disabled';} ?>
				onClick='$("#pager_currentPageField").attr("value", <?php echo $totalPages;?>);'
			/>
			</nobr>
		</td>
	</tr>
</table>