<?php
	// nuPhoto
	// Library for using Dropbox

	error_reporting(E_ALL);
	ini_set('display_errors', 'on');

	require_once "Dropbox/autoload.php";
	use \Dropbox as dbx;

	$appInfo = dbx\AppInfo::loadFromJsonFile("/var/config_dropbox.json");
	
	if (isset($_GET['test'])) {
		include_once("lib-db.php");

			try {
				$dbxClient = new dbx\Client(libdb_get_user_token(0), "nuPhoto/0001");

				print_r($dbxClient->getAccountInfo());		
			}
			catch (dbx\Exception_NetworkIO $ex) {
				print_r($ex);
			}
	
	}
	
	if (isset($_GET['get_file'])) {
		include_once("lib-db.php");
	
		print lib_get_file_link($_GET['get_file'])[0];
	}
	
	if (isset($_GET['watch_changes'])) {
		include_once("lib-db.php");
		
		$result = lib_get_changed($_GET['watch_changes']);
		
		if ($result == false) {
			print "0";
		} else {
			print "1";	
		}

	}
		
	function lib_get_web_auth()	{
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
		
				$ls = $dbxClient->getMetadataWithChildren($path);
				
				unset($ls['thumb_exists']);
				unset($ls['icon']);
				unset($ls['read_only']);
				unset($ls['modifier']);
				unset($ls['bytes']);
				unset($ls['size']);
				unset($ls['root']);
				
				for ($i = 0; $i < count($ls['contents']); $i++) {
					
					$fileext = explode('.', $ls['contents'][$i]['path']);
					$fileext = mb_strtolower($fileext[count($fileext) - 1]);
				
					if ($ls['contents'][$i]['is_dir'] == 1) {
						unset($ls['contents'][$i]['mime_type']); unset($ls['contents'][$i]['thumb_exists']);
						unset($ls['contents'][$i]['icon']);	unset($ls['contents'][$i]['read_only']);
						unset($ls['contents'][$i]['modifier']);	unset($ls['contents'][$i]['bytes']);
						unset($ls['contents'][$i]['size']);	unset($ls['contents'][$i]['root']);
					} else {
						if (in_array($fileext, array('jpg', 'jpeg', 'png', 'bmp'))) {
							unset($ls['contents'][$i]['thumb_exists']);	unset($ls['contents'][$i]['icon']);
							unset($ls['contents'][$i]['read_only']); unset($ls['contents'][$i]['modifier']);
							unset($ls['contents'][$i]['bytes']); unset($ls['contents'][$i]['size']);
							unset($ls['contents'][$i]['root']);
						} else {
							unset($ls['contents'][$i]);
						}	
					}

				}
		
				return $ls;
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

	function lib_get_hash($path) {	
		return lib_ls($path)['hash'];
	}

	function lib_get_info($path) {

		$dbxClient = new dbx\Client(libdb_get_user_token(0), "nuPhoto/0001");

		return $dbxClient->getMetadata($path);
		
	}
		
	function lib_get_changed($id) {		
		include_once("lib-db.php");
		
		$dbxClient = new dbx\Client(libdb_get_user_token(0), "nuPhoto/0001");
		$user_id = $_COOKIE['id'];		
		$mysql = NULL;

		if (isset($_GET['no_changes'])) {
			$no_changes = $_GET['no_changes'];
		}
		
		connect($mysql);	
		
		$path = urldecode(libdb_exec_query_assoc("SELECT nu_path FROM watchlist WHERE id=".$id." AND nu_user = ".$user_id)['nu_path']);
		$hash = libdb_exec_query_assoc("SELECT nu_hash FROM watchlist WHERE id=".$id." AND nu_user = ".$user_id)['nu_hash'];
	
		if (($hash == NULL) || ($hash == "")) {
			$changed = true;
		} else {
			list($changed, $new) = $dbxClient->getMetadataWithChildrenIfChanged($path, $hash);
		}
		
		if ($changed) {			
			return true;
		} else {
			return false;
		}	
				
	}
	
?>
