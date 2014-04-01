<?php
/**
 * Template for WCalendar widget
 * 
 * @package MediaClub
 * @subpackage views
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */

// Month names
$months = MTLang::instance()->getGroup('month_names'); 

// Get names for days of week
$daysOfWeek = MTLang::instance()->getGroup('days_of_week');

// Create entity of current date
$current = new DateTime($currentDate);
$firmstOfMonth = new DateTime($current->format('Y-m-01'));

// Prepare number of days in month
$dayNumbers = $firmstOfMonth->format('t');

// Prepare day of week
$weekDay = $firmstOfMonth->format('w');
$weekDay = $weekDay != 0 ? $weekDay : 7;

// Prepare start, end and last
$start = -($weekDay - 2);
$last = ($dayNumbers + $weekDay - 1) % 7;
if ($last == 0) {
	$end = $dayNumbers;
} else {
	$end = $dayNumbers + 7 - $last;
}

// Get previous month date
$previous = new DateTime($currentDate);
$previous->modify('-1 month');

// Prepare url params for previous month date
$params = $settings['url']['params'];
$params['date'] = $previous->format('Y-m-t');

// Prepare url for previous month date
$previousUrl = Yii::app()->getController()->createUrl($settings['url']['rout'], $params);

// Get next month date
$next = new DateTime($currentDate);
$next->modify('first day of next month');

// Prepare url params for next month date
$params = $settings['url']['params'];
$params['date'] = $next->format('Y-m-01');

// Prepare url for next month date
$nextUrl = Yii::app()->getController()->createUrl($settings['url']['rout'], $params);
?>

<table border=1 cellspacing=0 cellpadding=2 class="calendar"> 
	<tr>
		<td colspan=7> 
			<table width="100%" border=0 cellspacing=0 cellpadding=0> 
				<tr> 
					<td align="left"><a href="<? echo $previousUrl; ?>" title="<?php echo $previous->format('Y-m-t'); ?>">&lt;&lt;&lt;</a></td> 
					<td align="center"><? echo $months[$firmstOfMonth->format('m')] . '&nbsp;' . $firmstOfMonth->format('Y') ?></td> 
					<td align="right"><a href="<? echo $nextUrl; ?>" title="<?php echo $next->format('Y-m-01'); ?>">&gt;&gt;&gt;</a></td> 
				</tr> 
			</table> 
		</td> 
	</tr> 
	<tr>
		<?php foreach ($daysOfWeek as $number => $name) { ?>
			<th><?php echo $name; ?></th>
		<?php } ?>
	</tr>
	<?php 
		$i = 0;
		
		// Build days
		for ($day = $start; $day <= $end; $day++) {
			// Get full day date
			$dayDate = $firmstOfMonth->format('Y-m-' . ($day <= 9 ? '0' . $day : $day));
			
			// Prepare url params
			$params = $settings['url']['params'];
			$params['date'] = $dayDate;
			
			// Prepare day url
			$url = Yii::app()->getController()->createUrl($settings['url']['rout'], $params);
			
			// Start new line
			if (!($i++ % 7)) {
				echo "<tr>";
			}
			
			echo '<td ' . ($current->format('Y-m-d') == $dayDate ? 'class="calendar_selected_day"' : '') . '>';
			
			if ($day < 1 || $day > $dayNumbers) {
				echo "&nbsp";
			} else {
				if (!empty($settings['marks']) && !empty($settings['marks'][$dayDate])) {
					echo '<a href="' . $url . '" title="' . $settings['marks'][$dayDate] . '">' . $day . '</a>';
				} else {
					echo $day;
				}
			}
			
			echo '</td>';
			
			// End new line
			if (!($i % 7)) {
				echo "</tr>";
			}
		}
	
	?>
</table>