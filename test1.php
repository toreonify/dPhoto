<?php
	// nuPhoto
	// Test script for API
	// Places text document in folder
	
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');

	// Connecting library
	require_once "Dropbox/autoload.php";
	use \Dropbox as dbx;
	
	// Authorizing app
	$appInfo = dbx\AppInfo::loadFromJsonFile("/var/config_dropbox.json");
	$webAuth = new dbx\WebAuthNoRedirect($appInfo, "nuPhoto/0001");
	
	// Send approve dialog
	$authorizeUrl = $webAuth->start();
	
	echo "Go to: <a href=\"".$authorizeUrl."\">Authorize</a> ";
	
	//$authCode = "_8BvgoXPi5MAAAAAAAADbrGQE5iujGfxgfdmE37fl8k";

	list($accessToken, $dropboxUserId) = $webAuth->finish($authCode);

	print($accessToken);
	
	//$accessToken = "_8BvgoXPi5MAAAAAAAADbCe7bFVabUW_692Kphj2Zjp8sFOub6q_w8pdPoTFzubx";
?>
