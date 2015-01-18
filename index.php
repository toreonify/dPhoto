<?php
	// nuPhoto
	// Index page
	
	// Connect Dropbox library
	include_once("lib-dropbox.php");
	// Connect Database library
	include_once("lib-db.php");
	// Connect Render library
	include_once("lib-render.php");
	
	$page = 'index';
	
	lib_render_template("begin", $page);	
	lib_render_template("header");	
	lib_render_template("index");
	lib_render_template("end");	
?>
