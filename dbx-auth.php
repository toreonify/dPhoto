<?php
	// nuPhoto
	// Dropbox authorization finisher
	
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	
	print "Finishing...";
	
	include_once("lib-db.php");
	include_once("lib-dropbox.php");
	session_start();
	
	try {
		$accessToken = NULL;
		$userId = NULL;
		$urlState = NULL;
		
		list($accessToken, $userId, $urlState) = lib_get_web_auth()->finish($_GET);
		assert($urlState === null);  // Since we didn't pass anything in start()

		$user_id = $_COOKIE['id'];
		
		$temp = $accessToken.";".libdb_get_user_token(1);
		
		$query = "UPDATE `users` SET `nu_authcode`='".$temp."' WHERE `id`='".$user_id."'";
		
		libdb_exec_query($query);
		
		header('Location: add_cloud.php');
	}
	catch (dbx\WebAuthException_BadRequest $ex) {
	   print("/dropbox-auth-finish: bad request: " . $ex->getMessage());
	   // Respond with an HTTP 400 and display error page...
	}
	catch (dbx\WebAuthException_BadState $ex) {
	   // Auth session expired.  Restart the auth process.
	   header('Location: /add_cloud.php');
	}
	catch (dbx\WebAuthException_Csrf $ex) {
	   print("/dropbox-auth-finish: CSRF mismatch: " . $ex->getMessage());
	   // Respond with HTTP 403 and display error page...
	}
	catch (dbx\WebAuthException_NotApproved $ex) {
	   print("/dropbox-auth-finish: not approved: " . $ex->getMessage());
	}
	catch (dbx\WebAuthException_Provider $ex) {
	   print("/dropbox-auth-finish: error redirect from Dropbox: " . $ex->getMessage());
	}
	catch (dbx\Exception $ex) {
	   print("/dropbox-auth-finish: error communicating with Dropbox API: " . $ex->getMessage());
	}

?>