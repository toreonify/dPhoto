<?php
	// nuPhoto
	// Settings managing page
	
	// Connect Dropbox library
	include_once("lib-dropbox.php");
	// Connect Database library
	include_once("lib-db.php");
	// Connect Render library
	include_once("lib-render.php");
	
	$page = 'settings';
	
	lib_render_template("begin", $page);	
	lib_render_template("header");	
	lib_render_template("settings");
	lib_render_template("end");	
?>