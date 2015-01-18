<?php
	// nuPhoto
	// Functions to import and analyze content
	
	include 'lib-db.php';
	
	if ( (isset($_GET['set_metadata'])) && (isset($_GET['set_photos'])) && (isset($_COOKIE['id'])) ) {
		liba_set_imported($_GET['set_metadata'], $_GET['set_photos']);
	}
	
	if ( (isset($_GET['get_list'])) && (isset($_COOKIE['id'])) ) {
		print liba_get_json_list($_GET['get_list']);
	}

	if ( (isset($_GET['get_photos'])) && (isset($_COOKIE['id'])) ) {
		print liba_get_json_photos($_GET['get_photos']);
	}
	
	function liba_set_imported($id, $imported) {
		
		$user_id = $_COOKIE['id'];
		$imported = json_decode($imported);
		
		$query = "UPDATE metadata SET nu_imported=1 WHERE nu_user=%s AND nu_watch=%s AND nu_path='%s'";
			
		foreach ($imported as $ley => &$value) {
			libdb_exec_query(sprintf($query, $user_id, $id, urlencode($value)));
		}	
	}
	
	function liba_get_metadata($id) {		
		$user_id = $_COOKIE['id'];
		
		$query = "SELECT nu_path, nu_modified, nu_client_mtime, nu_imported FROM metadata WHERE nu_watch=".$id." AND nu_user = ".$user_id;
		
		$mysql = NULL;
		connect($mysql);
		
		$metadata = $mysql->query($query);
		$metadata = $metadata->fetch_all(MYSQLI_ASSOC);
		
		$metadata = array_map(function($tag) {
			return array(
				'path' => urldecode($tag['nu_path']),
				'modified' => $tag['nu_modified'],
				'client_mtime' => $tag['nu_client_mtime'],
				'imported' => $tag['nu_imported']
			);
		}, $metadata);
		
		return $metadata;
	}
	
	function liba_update_metadata($metadata, $data, $watch_id) {		
		$user_id = $_COOKIE['id'];
		/*
		foreach ($data['contents'] as $index => &$file) {
			$fileext = explode('.', $file['path']);
			$fileext = mb_strtolower($fileext[count($fileext) - 1]);
			
			if (in_array($fileext, array('jpg', 'jpeg', 'png', 'bmp'))) {
					
				unset($file['thumb_exists']); unset($file['icon']);
				unset($file['read_only']); unset($file['modifier']);
				unset($file['bytes']); unset($file['rev']);
				unset($file['revision']); unset($file['size']);
				unset($file['root']);
						
				if ($file['is_dir'] != 1) {
					unset($file['mime_type']);
				}
					
				unset($file['is_dir']);
					
			} else {
				unset($data['contents'][$index]);
			}
		}*/
																    
	    foreach ($data['contents'] as $key => &$value) {
		    if (in_array($value, $metadata)) {  
			    unset($data['contents'][$key]);
			}
	    }
	    
	    $data['contents'] = array_values($data['contents']);
	        
	    liba_fill_metadata($data, $watch_id);
	    
		/*
		foreach ($data['contents'] as $index => &$file) {
			$fileext = explode('.', $file['path']);
			$fileext = mb_strtolower($fileext[count($fileext) - 1]);
					
			if (in_array($fileext, array('jpg', 'jpeg', 'png', 'bmp'))) {
				
				$query = "INSERT INTO metadata (id, nu_user, nu_watch, nu_path, nu_modified, nu_client_mtime, nu_imported) VALUES (NULL ,%s, %s, '%s', '%s', '%s', 0) ON DUPLICATE KEY UPDATE nu_modified='%s', nu_client_mtime='%s'";
				libdb_exec_query(sprintf($query, $user_id, $watch_id, urlencode($file['path']), $file['modified'], $file['client_mtime'], 0, $file['modified'], $file['client_mtime']));							
						
			} 
			
		}*/
		
	}

	function liba_fill_metadata($data, $watch_id) {		
		$user_id = $_COOKIE['id'];
				
		foreach ($data['contents'] as $index => &$file) {
			$fileext = explode('.', $file['path']);
			$fileext = mb_strtolower($fileext[count($fileext) - 1]);
					
			if (in_array($fileext, array('jpg', 'jpeg', 'png', 'bmp'))) {
				
				$query = "INSERT INTO metadata (id, nu_user, nu_watch, nu_path, nu_modified, nu_client_mtime, nu_imported) VALUES (NULL ,%s, %s, '%s', '%s', '%s', %s) ON DUPLICATE KEY UPDATE nu_modified='%s', nu_client_mtime='%s'";
				
				libdb_exec_query(sprintf($query, $user_id, $watch_id, urlencode($file['path']), $file['modified'], $file['client_mtime'], 0, $file['modified'], $file['client_mtime']));							
						
			} 
			
		}
		
		$hash = $data['hash'];		
		$query = "UPDATE watchlist SET nu_hash='".$hash."' WHERE id=".$watch_id." AND nu_user=".$user_id;
		libdb_exec_query($query);	
	}

	function liba_get_metadata_count($id) {
		$user_id = $_COOKIE['id'];
		
		$query = "SELECT COUNT('id') FROM metadata WHERE nu_watch=".$id." AND nu_user = ".$user_id;
		
		$metadata = libdb_exec_query_assoc($query);
				
		return $metadata["COUNT('id')"];
	}

	function liba_get_import_list($id) {
		
		$user_id = $_COOKIE['id'];
		$path = urldecode(libdb_exec_query_assoc("SELECT nu_path FROM watchlist WHERE id=".$id)['nu_path']);	
		$metadata = NULL;
					
		if (liba_get_metadata_count($id) == 0) {
			$data = lib_ls($path);
			liba_fill_metadata($data, $id);
			
			$metadata = liba_get_metadata($id);
		} else {
			
			$watch_changed = lib_get_changed($id);
			
			if ($watch_changed) {
				
				$metadata = liba_get_metadata($id);
				$data = lib_ls($path);
				liba_update_metadata($metadata, $data, $id);
			}
			
			$metadata = liba_get_metadata($id);
			
		}
					
		return $metadata;
	}
		
	function liba_get_json_list($id) {
		include_once('lib-dropbox.php');

		$changes = [];
				
		$metadata = liba_get_import_list($id);
		$dbxClient = new Dropbox\Client(libdb_get_user_token(0), "nuPhoto/0001");
				
		foreach ($metadata as $file) {	
			if ($file['imported'] == 0) {
				$fileext = explode('.', $file['path']);
				$fileext = mb_strtolower($fileext[count($fileext) - 1]);
					
				if (in_array($fileext, array('jpg', 'jpeg', 'png', 'bmp'))) {
					$new_file = [];
							
					$new_file['path'] = $file['path'];
					$new_file['thumbnail'] = base64_encode($dbxClient->getThumbnail($file['path'], "png", "m")[1]);
							
					$changes[] = $new_file;
				}
			}
		}
				
		return json_encode($changes);		
	}

	function liba_get_json_photos($id) {
		include_once('lib-dropbox.php');

		$changes = [];
				
		$metadata = liba_get_metadata($id);
		$dbxClient = new Dropbox\Client(libdb_get_user_token(0), "nuPhoto/0001");
				
		foreach ($metadata as $file) {	
			if ($file['imported'] == 1) {
				$fileext = explode('.', $file['path']);
				$fileext = mb_strtolower($fileext[count($fileext) - 1]);
					
				if (in_array($fileext, array('jpg', 'jpeg', 'png', 'bmp'))) {
					$new_file = [];
							
					$new_file['path'] = $file['path'];
					$new_file['thumbnail'] = base64_encode($dbxClient->getThumbnail($file['path'], "png", "l")[1]);
							
					$changes[] = $new_file;
				}
			}
		}
				
		return json_encode($changes);		
	}				
?>