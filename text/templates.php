<?php
	// nuPhoto
	// Text for pages
	
	$text_template['en']['title_index'] = "nuPhoto";
	$text_template['en']['title_settings'] = "nuPhoto &mdash; Settings";
	$text_template['en']['title_login'] = "nuPhoto &mdash; Log in";	
	$text_template['en']['title_register'] = "nuPhoto &mdash; Registration";	
	
	if (isset($_GET['text']) && isset($_GET['lang'])) {
		print $text_template[$_GET['lang']][$_GET['text']];
	}
	
?>