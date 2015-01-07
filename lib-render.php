<?php
	// nuPhoto
	// Library for rendering UI
	
	function lib_render_template($name, $page = NULL) {
		include_once("templates/".$name.".php");
		
		if ($page == NULL) {
			$calculate_values = $name."_calculate_values";
			$render_ui = $name."_render_ui";
		} else {
			$calculate_values = $name."_".$page."_calculate_values";
			$render_ui = $name."_".$page."_render_ui";
		}
		
		$calculate_values();
		$render_ui();
	}

?>
