<?php
	// nuPhoto
	// Text for pages
	
	$text_template['en']['title_index'] = "Home page";
	
	if (isset($_GET['text']) && isset($_GET['lang'])) {
		print $text_template[$_GET['lang']][$_GET['text']];
	}
	
?>