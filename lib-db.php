<?php
	// nuPhoto
	// Library for using database

	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	
	if (isset($_GET['watch_hash']) && isset($_COOKIE['id'])) {
		print libdb_get_watch_hash($_GET['watch_hash']);
	}
	
	if (isset($_GET['delete_album']) && isset($_COOKIE['id'])) {
		$album_id = urlencode($_GET['delete_album']);

		$result = NULL;
		$mysql = NULL;
		$user_id = $_COOKIE['id'];

		connect($mysql);

		$query = "DELETE FROM albums WHERE nu_user = ".$user_id." AND id = '".$album_id."'";
		
		libdb_exec_query($query);
		
		$mysql->close();
	}

	if (isset($_GET['get_album_watchlist']) && isset($_COOKIE['id'])) {
		$result = NULL;
		$mysql = NULL;
		$user_id = $_COOKIE['id'];	
		$album_id = $_GET['get_album_watchlist'];
	
		connect($mysql);
		
		$query = "SELECT id, nu_path FROM watchlist WHERE nu_album='".$album_id."' AND nu_user='".$user_id."'";
		
		$result = $mysql->query($query);
		
		print_r(json_encode($result->fetch_all(MYSQLI_ASSOC)));
		
		$mysql->close();
	}

	if (isset($_GET['get_albums']) && isset($_COOKIE['id'])) {
		$result = NULL;
		$mysql = NULL;
		$user_id = $_COOKIE['id'];	
	
		connect($mysql);
		
		$query = "SELECT id, nu_name FROM albums WHERE nu_user='".$user_id."'";
		
		$result = $mysql->query($query);
		
		print_r(json_encode($result->fetch_all(MYSQLI_ASSOC)));
		
		$mysql->close();
	}
	
	if (isset($_GET['get_albums_count']) && isset($_COOKIE['id'])) {
		$result = NULL;
		$mysql = NULL;
		$user_id = $_COOKIE['id'];	
	
		connect($mysql);
		
		$query = "SELECT COUNT(id) FROM albums WHERE nu_user='".$user_id."'";
		
		$result = libdb_exec_query_assoc($query);
		
		$mysql->close();
		
		print $result['COUNT(id)'];
	}

	if (isset($_GET['album_id']) && isset($_GET['rename_album']) && isset($_COOKIE['id'])) {
		$result = NULL;
		$mysql = NULL;
		$name = $_GET['rename_album'];
		$user_id = $_COOKIE['id'];	
		$id = urlencode($_GET['album_id']);
	
		connect($mysql);
		
		print $name;
		
		$query = "UPDATE albums SET nu_name='".$name."' WHERE id='".$id."' AND nu_user='".$user_id."'";
		
		libdb_exec_query($query);
		
		$mysql->close();
	}

	if (isset($_GET['add_album']) && isset($_COOKIE['id'])) {
		$result = NULL;
		$mysql = NULL;
		$user_id = $_COOKIE['id'];	
	
		connect($mysql);
		
		$query = "INSERT INTO albums (id, nu_user, nu_name) VALUES (NULL, ".$user_id.", '')";
		
		$result = $mysql->query($query);
		
		$id = $mysql->insert_id;
		
		$mysql->close();
		
		print $id;
	}
	
	if (isset($_GET['set_watch']) && isset($_GET['album']) && isset($_COOKIE['id'])) {
		$path = urlencode($_GET['set_watch']);
		$album_id = $_GET['album'];
		$watch_id = NULL;
		
		if (isset($_GET['watch_id'])) {
			$watch_id = $_GET['watch_id'];
		}

		$result = NULL;
		$mysql = NULL;
		$return_new_id = false;
		$user_id = $_COOKIE['id'];
		
		include_once("lib-dropbox.php");
		
		$metadata = NULL;

		connect($mysql);

		if (libdb_check_watch($_GET['set_watch'], $_GET['album'])) {
			$query = "DELETE FROM watchlist WHERE nu_user = ".$user_id." AND nu_path = '".$path."'";
		} else {
			if ($watch_id != NULL) {
				$query = "INSERT INTO watchlist (id, nu_user, nu_path, nu_album) VALUES (".$watch_id.",".$user_id.", '".$path."', '".$album_id."')";
				$return_new_id = false;
			} else {
				$query = "INSERT INTO watchlist (nu_user, nu_path, nu_album) VALUES (".$user_id.", '".$path."', '".$album_id."')";
				$return_new_id = true;
			}
		}

		$result = libdb_exec_query($query);

		if ($return_new_id) {
			$id = $mysql->insert_id;

			print $id;
		}

		$mysql->close();
	}

	function libdb_check_albums() {
		$result = NULL;
		$mysql = NULL;
		$user_id = $_COOKIE['id'];

		connect($mysql);

		$query = "SELECT EXISTS(SELECT 1 FROM albums WHERE nu_user = ".$user_id.") AS `albums_exist`";

		$result = libdb_exec_query_assoc($query);

		$mysql->close();

		if (isset($result) && is_array($result)) {
			if ($result['albums_exist'] == 1) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function libdb_check_watch($path, $album_id) {
		$path = urlencode($path);

		$result = NULL;
		$result2 = NULL;
		$mysql = NULL;
		$user_id = $_COOKIE['id'];

		connect($mysql);

		$query = "SELECT EXISTS(SELECT 1 FROM watchlist WHERE nu_path = '".$path."' AND nu_user = ".$user_id." AND nu_album='".$album_id."') AS `path_exist`";

		$result = libdb_exec_query_assoc($query);

		$query = "SELECT EXISTS(SELECT 1 FROM albums WHERE id = '".$album_id."' AND nu_user = ".$user_id.") AS `album_exist`";

		$result2 = libdb_exec_query_assoc($query);

		$mysql->close();

		if (isset($result) && is_array($result) && isset($result2) && is_array($result2)) {
			if (($result['path_exist'] == 1) && ($result2['album_exist'] == 1)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function libdb_get_watch_hash($id) {
		$result = NULL;
		$mysql = NULL;
		$user_id = $_COOKIE['id'];

		connect($mysql);

		$query = "SELECT nu_hash FROM watchlist WHERE id = '".$id."' AND nu_user = ".$user_id;

		$result = libdb_exec_query_assoc($query);
		
		if (isset($result) && is_array($result)) {
			$mysql->close();
			
			return $result['nu_hash'];
		}
		
		return false;
	}

	// Connect MySQL
	function connect(&$mysql) {
		include('/var/config_mysql.php');
		
		$mysql->select_db("nuPhoto");
		$mysql->query("SET NAMES utf8");

		if ($mysql->connect_error) {
			die("[lib-db] mysqli connection error\n");	
		}
	}
	
	function libdb_exec_query($query) {
		$result = NULL;
		$mysql = NULL;

		connect($mysql);
		
		$result = $mysql->query($query);
		
		if ($result) {
			
			$mysql->close();

		} else {
			die($mysql->error);
		}		
	}
	
	function libdb_exec_query_assoc($query) {
		$result = NULL;
		$mysql = NULL;

		connect($mysql);
		
		$result = $mysql->query($query);
		
		if ($result) {
			
			$rows = $result->fetch_assoc();
			
			$mysql->close();
			
			return $rows;
		} else {
			die($mysql->error);
		}		
	}
	
	function libdb_get_user_photo() {
		$mysql = NULL;

		connect($mysql);
	
		if (isset($_COOKIE['id'])) {
	
			$user_id = $_COOKIE['id'];

			$query = "SELECT nu_photo FROM users WHERE id='".$user_id."'";

			$result = $mysql->query($query);

			if ($result) {
				$result = $result->fetch_assoc();
				
				$mysql->close();
				
				if ($result['nu_photo'] == '' or $result['nu_photo'] == NULL) {
					return "images/default.png";
				}
				
				return "images/".$result['nu_photo'];
			} else {
				die($mysql->error);
			}		
		}
	}

	function libdb_get_user_token($service_id) {
		$mysql = NULL;

		connect($mysql);
	
		if (isset($_COOKIE['id'])) {
		
			$user_id = $_COOKIE['id'];

			$query = "SELECT nu_authcode FROM users WHERE id='".$user_id."'";

			$result = $mysql->query($query);

			if ($result) {
				$result = $result->fetch_assoc();

				$result = $result['nu_authcode'];
				
				if ($result != '') {
					$result = explode(';', $result)[$service_id];	
				}
				
				$mysql->close();

				return $result;
			} else {
				die($mysql->error);
			}
		}
	}
?>
