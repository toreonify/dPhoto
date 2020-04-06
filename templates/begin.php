<?php
	// nuPhoto
	// HTML begining for UI
			
	$title = NULL;
			
	function begin_calculate_values($page) {
		global $title;
		include_once('text/templates.php');
		
		$title = $text_template['en']['title_'.$page];
	}
	
	function begin_render_ui() {
		global $title;
	
		print '
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset="UTF-8">
				<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
				<style type="text/css">
  					{# set default font for website #}
				    *:not(.icon) {
				        font-family: \'Open Sans\', sans-serif !important;
				    }
				    
				    @font-face {
						font-family: \'Basic Icons\';
						src: url(\'../fonts/basic.icons.eot\');
							src: url(\'../fonts/basic.icons.eot?#iefix\') format("embedded-opentype"), url(\'../fonts/semantic-ui/basic.icons.svg#basic.icons\') format("svg"), url(\'../fonts/semantic-ui/basic.icons.woff\') format("woff"), url(\'../fonts/basic.icons.ttf\') format("truetype");
  						font-style: normal;
						font-weight: normal;
						font-variant: normal;
  						text-decoration: inherit;
						text-transform: none;
					}
				</style>
				<script src="javascript/jquery.js"></script>
				<script src="javascript/jquery-ui.js"></script>
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<meta name="mobile-web-app-capable" content="yes">
				
				<link rel="stylesheet" type="text/css" href="/components/card.css">
				<link rel="stylesheet" type="text/css" class="ui" href="css/semantic.min.css">
				<link rel="stylesheet" type="text/css" href="css/main.css">
				<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">
				<link rel="stylesheet" type="text/css" href="css/jquery-ui.structure.min.css">
				<link rel="stylesheet" type="text/css" href="css/jquery-ui.theme.min.css">
				<script src="javascript/semantic.min.js"></script>	
				<script src="javascript/main.js"></script>
				<title>'.$title.'</title>
			</head>
		<body>';
	}
?>
