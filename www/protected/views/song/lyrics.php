<?php 
	// Get song translations
	$translations = MTLang::instance()->getGroup('song');
	
	if (empty($song)) {
?>
	<h1><?php echo $translations['text_not_found']; ?></h1>
<?php } else { ?>
	<?php if (empty($ajax)) { ?>
		<h1><?php echo sprintf($translations['text_page_caption'], $song['name']); ?></h1>
		<h2><?php echo $translations['text_album'] . ': ' . $album['title']; ?></h2>
		<h2><?php echo $translations['text_singer'] . ': ' . $singer['name']; ?></h2>
	<?php } ?>
	<p><?php echo nl2br($text['text']); ?></p>
<?php } ?>