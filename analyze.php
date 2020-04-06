<?php
	// nuPhoto
	// Functions to import and analyze content
	
	include 'lib-db.php';
	
	if ( (isset($_GET['merge'])) && (isset($_GET['merge_with'])) && (isset($_COOKIE['id'])) ) {
		$id1 = $_GET['merge_with'];
		$id2 = $_GET['merge'];
		$user_id = $_COOKIE['id'];	
				
		chdir("/var/opencv");
		
		$fi = new FilesystemIterator("/var/opencv/users/".$user_id."/".$id2."/", FilesystemIterator::SKIP_DOTS);
		$id2_count = iterator_count($fi);
				
		$fi = new FilesystemIterator("/var/opencv/users/".$user_id."/".$id1."/", FilesystemIterator::SKIP_DOTS);
		$id1_count = iterator_count($fi);
		
		output($id1_count);
		output($id2_count);
		
		for ($i = 0; $i < $id2_count; $i++) {
			rename("/var/opencv/users/".$user_id."/".$id2."/".$i.".jpg", "/var/opencv/users/".$user_id."/".$id1."/".($id1_count + $i).".jpg");
			output("   /var/opencv/users/".$user_id."/".$id2."/".$i.".jpg", "/var/opencv/users/".$user_id."/".$id1."/".($id1_count + $i).".jpg");
		}
		
		
		rmdir("/var/opencv/users/".$user_id."/".$id2."/");
		
		$query = "UPDATE faces_data SET nu_face_id=".$id1." WHERE nu_face_id=".$id2;
		libdb_exec_query($query);
		
		$query = "DELETE FROM faces WHERE id=".$id2;
		libdb_exec_query($query);
		
	}
	
	if ( (isset($_GET['get_sort'])) && (isset($_COOKIE['id']))  ) {
		print json_encode(liba_get_sort($_GET['get_sort']));
	}
	
	if ( (isset($_GET['set_sort'])) && (isset($_GET['album_id'])) && (isset($_COOKIE['id'])) ) {
		if ($_GET['set_sdate'] != "") {
			$_GET['set_sdate'] = strtotime($_GET['set_sdate']);
		} else {
			$_GET['set_sdate'] = -1;
		}
		if ($_GET['set_edate'] != "") {
			$_GET['set_edate'] = strtotime($_GET['set_edate']);
		} else {
			$_GET['set_edate'] = -1;
		}
		$query = "UPDATE sort SET nu_sort=%s, nu_sdate=%s, nu_edate=%s WHERE nu_album=".$_GET['album_id'];
		
		libdb_exec_query(sprintf($query, $_GET['set_sort'], $_GET['set_sdate'], $_GET['set_edate']));
	} 
	
	if ( (isset($_GET['set_metadata'])) && (isset($_GET['set_photos'])) && (isset($_COOKIE['id'])) ) {
		liba_set_imported($_GET['set_metadata'], $_GET['set_photos']);
	}
	
	if ( (isset($_GET['get_list'])) && (isset($_COOKIE['id'])) ) {
		print liba_get_json_list($_GET['get_list']);
	}

	if ( (isset($_GET['album'])) && (isset($_GET['get_photos'])) && (isset($_COOKIE['id'])) ) {
		print liba_get_json_photos($_GET['get_photos'], $_GET['album']);
	}
	
	if ( (isset($_GET['get_thumbnail'])) && (isset($_COOKIE['id'])) ) {
		include_once('lib-dropbox.php');

		$dbxClient = new Dropbox\Client(libdb_get_user_token(0), "nuPhoto/0001");
		print base64_encode($dbxClient->getThumbnail($_GET['get_thumbnail'], "png", "m")[1]);
	}
	
	function liba_send_to_opencv($path, $watch_id) {
		include_once('lib-dropbox.php');
		include_once('lib-db.php');

		$user_id = $_COOKIE['id'];

		$dbxClient = new Dropbox\Client(libdb_get_user_token(0), "nuPhoto/0001");

		$query = "SELECT id FROM metadata WHERE nu_path='".$path."' AND nu_watch=".$watch_id;
		
		$photo_id = libdb_exec_query_assoc($query)['id'];
																									
		$fd = fopen("/var/opencv/temp/".$photo_id.".jpg", "wb");
		$metadata = $dbxClient->getFile($path, $fd);
		fclose($fd);
		
		chmod("/var/opencv/temp/".$photo_id.".jpg", 0777);
		chdir("/var/opencv");
		
		echo system('python /var/opencv/opencv.py '.$photo_id);
		echo system('/var/opencv/./mysql '.$user_id.' '.$photo_id.' > /var/opencv/log/'.$photo_id.' 2>&1');
	}
	
	function liba_set_imported($id, $imported) {
		
		$user_id = $_COOKIE['id'];
		$imported = json_decode($imported);
		
		$query = "UPDATE metadata SET nu_imported=1 WHERE nu_user=%s AND nu_watch=%s AND nu_path='%s'";
			
		foreach ($imported as $key => &$value) {
			libdb_exec_query(sprintf($query, $user_id, $id, $value));
			liba_send_to_opencv($value, $id);
		}	
	}
	
	function liba_get_sort($album) {
		$query = "SELECT nu_sort, nu_sdate, nu_edate FROM sort WHERE nu_album=".$album;
		
		$result = libdb_exec_query_assoc($query);
		
		if ($result['nu_sdate'] != -1) {
			$result['nu_sdate'] = date("d.m.Y", $result['nu_sdate']);
		}
		
		if ($result['nu_edate'] != -1) {
			$result['nu_edate'] = date("d.m.Y", $result['nu_edate']);
		}
				
		return $result;		
	}
	
	function liba_get_metadata($id, $album = NULL) {		
		$user_id = $_COOKIE['id'];
						
		$query = "SELECT nu_path, nu_time_taken, nu_imported FROM metadata WHERE nu_watch=".$id." AND nu_user = ".$user_id;
		
		$mysql = NULL;
		connect($mysql);
		
		$metadata = $mysql->query($query);
		$metadata = $metadata->fetch_all(MYSQLI_ASSOC);
				
		$metadata = array_map(function($tag) {
			return array(
				'path' => $tag['nu_path'],
				'time_taken' => $tag['nu_time_taken'],
				'imported' => $tag['nu_imported']
			);
		}, $metadata);


		if (isset($album)) {
			$settings = liba_get_sort($album);
			
			output($settings);
			
			usort($metadata, 'do_desc');
						
			if ($settings['nu_sort'] == 1) {
				$metadata = array_reverse($metadata, true);
			} 		
			
			
			if ($settings['nu_sdate'] != -1) {
				foreach ($metadata as $key=>&$value) {
					if (strtotime($value['time_taken']) < strtotime($settings['nu_sdate'])) {
						unset($metadata[$key]);
					}	
				}
			}
			
			if ($settings['nu_edate'] != -1) {
				foreach ($metadata as $key=>&$value) {
					if (strtotime($value['time_taken']) > (strtotime($settings['nu_edate']) + 24*60*60) ) {
						unset($metadata[$key]);
					}					
				}			
			}

		}
		
		return $metadata;
	}
	
	function liba_update_metadata($metadata, $data, $watch_id) {		
		$user_id = $_COOKIE['id'];
																    
	    foreach ($data['contents'] as $key => &$value) {
		    if (in_array($value, $metadata)) {  
			    unset($data['contents'][$key]);
			}
	    }
	    
	    $data['contents'] = array_values($data['contents']);
	        
	    liba_fill_metadata($data, $watch_id);		
	}

	function liba_fill_metadata($data, $watch_id) {		
		$user_id = $_COOKIE['id'];
				
		foreach ($data['contents'] as $index => &$file) {
			$fileext = explode('.', $file['path']);
			$fileext = mb_strtolower($fileext[count($fileext) - 1]);
					
			if (in_array($fileext, array('jpg', 'jpeg', 'png', 'bmp'))) {
				
				$query = "INSERT INTO metadata (id, nu_user, nu_watch, nu_path, nu_time_taken, nu_imported) SELECT * FROM (SELECT NULL ,%s, %s, '%s', '%s', %s) AS tmp WHERE NOT EXISTS (SELECT nu_path FROM metadata WHERE nu_path = '".$file['path']."')";
				
				
				libdb_exec_query(sprintf($query, $user_id, $watch_id, $file['path'], $file['time_taken'], 0, $file['time_taken']));							
						
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

	function do_desc($item1, $item2) {
    	$ts1 = strtotime($item1['time_taken']);
		$ts2 = strtotime($item2['time_taken']);
	
		return $ts2 - $ts1;
	}

	function liba_get_import_list($id) {
		
		$user_id = $_COOKIE['id'];
		$path = libdb_exec_query_assoc("SELECT nu_path FROM watchlist WHERE id=".$id)['nu_path'];	
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

		$dbxClient = new Dropbox\Client(libdb_get_user_token(0), "nuPhoto/0001");				
		$metadata = liba_get_import_list($id);
				
		foreach ($metadata as $file) {	
			if ($file['imported'] == 0) {
				$fileext = explode('.', $file['path']);
				$fileext = mb_strtolower($fileext[count($fileext) - 1]);
					
				if (in_array($fileext, array('jpg', 'jpeg', 'png', 'bmp'))) {
					$new_file = [];
							
					$new_file['path'] = $file['path'];
					$new_file['time_taken'] = $file['time_taken'];
					$new_file['thumbnail'] = NULL;
							
					$changes[] = $new_file;
				}
			}
		}
				
		return json_encode($changes);		
	}

	function liba_get_json_photos($id, $album) {
		include_once('lib-dropbox.php');

		$changes = [];
				
		$metadata = liba_get_metadata($id, $album);
		$dbxClient = new Dropbox\Client(libdb_get_user_token(0), "nuPhoto/0001");
				
		output($metadata);
				
		foreach ($metadata as $file) {	
			if ($file['imported'] == 1) {
				$fileext = explode('.', $file['path']);
				$fileext = mb_strtolower($fileext[count($fileext) - 1]);
					
				if (in_array($fileext, array('jpg', 'jpeg', 'png', 'bmp'))) {
					$new_file = [];
							
					$new_file['path'] = $file['path'];
					$new_file['time_taken'] = $file['time_taken'];
					$new_file['thumbnail'] = NULL;
							
					$changes[] = $new_file;
				}
			}
		}
				
		return json_encode($changes);		
	}				
?>