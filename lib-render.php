<?php
	// nuPhoto
	// Library for rendering UI
	
	function lib_render_template($name, $page = NULL) {
		include_once("templates/".$name.".php");
		
		$calculate_values = $name."_calculate_values";
		$render_ui = $name."_render_ui";
		
		$calculate_values($page);
		$render_ui();
	}

?>
