<?php
	// nuPhoto
	// Library for using Dropbox

	error_reporting(E_ALL);
	ini_set('display_errors', 'on');

	require_once "Dropbox/autoload.php";
	use \Dropbox as dbx;

	$appInfo = dbx\AppInfo::loadFromJsonFile("/var/config_dropbox.json");
	
	if (isset($_GET['get_file'])) {
		include_once("lib-db.php");
	
		print(lib_get_file_link($_GET['get_file'])[0]);
	}
		
		function lib_get_web_auth()
		{
			session_start();
		
			global $appInfo;
			$clientIdentifier = "nuPhoto/0001";
			$redirectUri = "https://bootefi.no-ip.org/dbx-auth.php";
			$csrfTokenStore = new dbx\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
			
			return new dbx\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore);
		}

		function lib_ls($path) {
			try {
				$dbxClient = new dbx\Client(libdb_get_user_token(0), "nuPhoto/0001");
		
				return $dbxClient->getMetadataWithChildren($path);
			}
			catch (dbx\WebAuthException_BadRequest $ex) {
			   print("/dropbox-auth-finish: bad request: " . $ex->getMessage());
			   // Respond with an HTTP 400 and display error page...
			}
			catch (dbx\WebAuthException_BadState $ex) {
			   // Auth session expired.  Restart the auth process.
			   header('Location: /index.php');
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
			catch (dbx\Exception_NetworkIO $ex) {
			   print("Connection timeout, retrying: " . $ex->getMessage());
			}
		}
		
		function lib_get_user_info() {
			try {
				$dbxClient = new dbx\Client(libdb_get_user_token(0), "nuPhoto/0001");

				return $dbxClient->getAccountInfo();		
			}
			catch (dbx\Exception_NetworkIO $ex) {
				print("Connection timeout, retry: " . $ex->getMessage());
			}
		}
		
		function lib_get_file_link($path){
			try{
				$dbxClient = new dbx\Client(libdb_get_user_token(0), "nuPhoto/0001");
			
				$link = $dbxClient->createTemporaryDirectLink($path);
				
				return $link;
			}
			catch(Dropbox\Exception $e){
				return array("error"=>1, "message"=>"There was a problem accessing file download URL from dropbox");
			}
		}

?>
