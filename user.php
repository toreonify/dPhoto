<?php
	// nuPhoto
	// User profile page
	
	// Connect Dropbox library
	include_once("lib-dropbox.php");
	// Connect Database library
	include_once("lib-db.php");
	// Connect Render library
	include_once("lib-render.php");
	
	$page = 'user';
	
	lib_render_template("begin");	
	lib_render_template("header", "user");
	lib_render_template("user");
	lib_render_template("end");	
?>