<?php
	// nuPhoto
	// UI for user page

	$ls = NULL;
	$json = (isset($_GET['json'])) ? $_GET['json'] : false;
	$json_album_id = (isset($_GET['album'])) ? $_GET['album'] : false;
	
	if ($json != false) {	
		if ($_SERVER["REMOTE_ADDR"] == "10.0.2.2") {
			include_once('../lib-db.php');
			include_once('../lib-dropbox.php');
			
			user_calculate_values();
			user_render_ui();
		}
	}
	
	function user_calculate_values() {
		global $ls, $json;
		
		if ($json == false) {
			$ls = lib_ls("/");
		} else {
			$ls = lib_ls($json);
		}
	}
	
	function user_render_ui() {
		global $ls, $json, $json_album_id;			
		
		print '
				<div class="ui cards">';

		$card = '
				 <div class="ui card">
					<div class="content">
						<a><i class="right floated %s icon" onclick=\'lib_folder_watch(this, "%s")\' data-content="%s" data-variation="small inverted"></i></a>
						<a class="header" onclick=\'lib_folder_ls("%s");\'>%s</a>
						<div class="meta">
							<span class="group" style="color: %s;">%s</span>
						</div>
						<div class="description">
						</div>
					</div>
				 </div>';
		
		$card_photo = '
				 <div class="ui card">
					<div class="content">
						<a class="header" onclick=\'lib_get_file("%s",0);\'>%s</a>
						<div class="meta">
							<span class="group" style="color: %s;">%s</span>
						</div>
						<div class="description">
							<span class="time">%s</span>
						</div>
					</div>
				 </div>';
		
		//#da9f00
		
		if (isset($ls['is_dir'])) {
			if ($ls['is_dir'] == 1) {
				$ls = $ls['contents'];
			}
		}
		
		if (is_array($ls)) {
		
			foreach ($ls as $file) {
				if ($file['is_dir'] == 1) {
				
					$is_watching = "plus";
					$is_watching_text = "Добавить папку в наблюдаемые";

					if (libdb_check_watch($file['path'], $json_album_id)) {
						$is_watching = "checkmark";
						$is_watching_text = "Убрать папку из наблюдаемых";
					}

					print(sprintf($card, $is_watching, $file['path'], $is_watching_text, $file['path'], $file['path'], "#009fda", "Папка"));
				} else {
					if (isset($file['time_taken'])) {
						$time = explode(' ', $file['time_taken']);
						$time = $time[1]." ".$time[2]." ".$time[3];
					} else {
						$time = "";
					}
					
					$filename = explode('/', $file['path']);
					$filename = $filename[count($filename) - 1];
				
					$fileext = explode('.', $file['path']);
					$fileext = mb_strtolower($fileext[count($fileext) - 1]);
				
					if (in_array($fileext, array('jpg', 'jpeg', 'png', 'bmp'))) {
						print(sprintf($card_photo, $file['path'], $filename, "#da9f00", "Фотография", $time));
					}
				}
			}
		}
		
		print '</div>';
		
	}

?>