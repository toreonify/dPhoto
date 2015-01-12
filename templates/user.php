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
			
		print '<div class="ui basic modal" id="viewer">
  <div class="content">
	<img src="" class="ui rounded image" id="viewer-img"></img>
  </div>
  <div class="actions">
    <div class="two fluid ui inverted buttons">
      <div class="ui basic inverted button">
      <a id="viewer_link" href="" target="_blank"><i class="angle expand icon"></i>
        Open full image
	  </a>
      </div>
	  <div class="ui basic inverted button">
	  <i class="angle close icon"></i>
        Close preview
      </div>
    </div>
  </div>
</div>';
				
		
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
							<span class="time">%s</span>
						</div>
					</div>
				 </div>';
		
		$card_photo = '
				 <div class="ui card">
					<div class="content">
						<a class="header" onclick=\'lib_get_file("%s");\'>%s</a>
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
					$time = explode(' ', $file['modified']);
					$time = $time[1]." ".$time[2]." ".$time[3];
				
					$is_watching = "plus";
					$is_watching_text = "Add folder to watchlist";

					if (libdb_check_watch($file['path'], $json_album_id)) {
						$is_watching = "checkmark";
						$is_watching_text = "Remove folder from watchlist";
					}

					print(sprintf($card, $is_watching, $file['path'], $is_watching_text, $file['path'], $file['path'], "#009fda", "Folder", $time));
				} else {
					$time = explode(' ', $file['modified']);
					$time = $time[1]." ".$time[2]." ".$time[3];
				
					$filename = explode('/', $file['path']);
					$filename = $filename[count($filename) - 1];
				
					$fileext = explode('.', $file['path']);
					$fileext = $fileext[count($fileext) - 1];
				
					if (in_array($fileext, array('jpg', 'jpeg', 'png', 'bmp'))) {
						print(sprintf($card_photo, $file['path'], $filename, "#da9f00", "Photo", $time));
					}
				}
			}
		}
		
		print '</div>';
		
	}

?>