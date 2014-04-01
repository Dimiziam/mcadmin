<?php
/**
*
* @author Roman A. Shtemepliuk (deRamzes@gmail.com)
 * date  27.06.2013
 * @version  1.0
 *
 */


//used in
/*

$this->widget('zii.widgets.CListView', array(
		'dataProvider' => $model,
		'itemView' => 'search_viewList',
		'enablePagination' => false,
));
*/

// in this case we print real name

if  ($data['print_name']==$data['realname']) {
	 $singer = $data['realname'].'&nbsp;';
	 if (!empty($data['alias'])) {
	 	$singer .='<i>('.$data['alias'].')</i>';
	 }
}

elseif ($data['print_name']==$data['alias']) {

	$singer = '<i>'.$data['alias'].'</i>&nbsp;';

	if (!empty($data['realname'])) {
		$singer .=$data['relaname'];
	}
}

//print_name $singer

echo '<li><a href="'.$this->createUrl('/singer/',array('singer_name'=>$data['print_name'])).'">'.$singer.'</a></li>';



