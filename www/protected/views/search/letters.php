<?php 
	// Get search_letters translations
	$translations = MTLang::instance()->getGroup('search_letters');
?>
<div class="search_letter">
	<?php echo $translations['letter']; ?>: <span class="litera"><?php echo $letter; ?></span>
</div>

<?php 
	// Get found items
	$items = $model->getData();
?>

<div class="singers-search-names-list">
<div class="search_title_row">
	<div class="title"><?php echo $translations['band']; ?></div>
		<div class="paginator_search">
		<?php
			$this->widget('application.components.widgets.Paginator',
				array(
						'pages' => $model->getPagination(),
				)
			);
		?>
		</div>
<div class="clearfix"></div>
</div>


<ol class="column4">

<?php 
	// Display found items
	foreach ($items as $item) {
		// Prepare url params
		$params = array(
			'id' => $item['id'],
			'alias' => $item['alias'],
			'letter' => substr($item['alias'], 0, 1)
		);
		
		?>
			<li>
				<a href="<?php echo $this->createUrl('/singer/', $params); ?>">
					<?php echo $item['name'] . (!empty($item['pseudonym']) ? ' <i>(' . $item['pseudonym'] . '</i>)' : ''); ?>
				</a>
			</li>
		<?php
	}
?>
</ol>
</div>