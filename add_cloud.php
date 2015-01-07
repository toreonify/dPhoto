<?php
	// nuPhoto
	// Cloud managing page
	
	// Connect Dropbox library
	include_once("lib-dropbox.php");
	// Connect Database library
	include_once("lib-db.php");
	// Connect Render library
	include_once("lib-render.php");
	
	$page = 'add_cloud';
	
	lib_render_template("begin");	
	lib_render_template("header", "add_cloud");	
	lib_render_template("add_cloud");
	lib_render_template("end");	
?>