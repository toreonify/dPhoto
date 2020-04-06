<?php
	// nuPhoto
	// Text for pages
	
	$text_template['en']['title_index'] = "dPhoto";
	$text_template['en']['title_settings'] = "dPhoto &mdash; Настройки";
	$text_template['en']['title_login'] = "dPhoto &mdash; Вход";	
	$text_template['en']['title_register'] = "dPhoto &mdash; Регистрация";	
	
	if (isset($_GET['text']) && isset($_GET['lang'])) {
		print $text_template[$_GET['lang']][$_GET['text']];
	}
	
?>