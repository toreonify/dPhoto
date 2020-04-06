<?php
	// nuPhoto
	// UI for clouds page
	
	$authorize_link = NULL;
	$dropbox_added = NULL;
	
	function add_cloud_calculate_values() {
		include_once('lib-db.php');
		include_once('lib-dropbox.php');
	
		global $authorize_link, $dropbox_added;
		
		$authorize_link = lib_get_web_auth()->start();
		
		$user_token_drop = libdb_get_user_token(0);
		
		if ($user_token_drop != "") {
			$dropbox_added = "Добавлен";
		} else {
			$dropbox_added = '<a href="'.$authorize_link.'">Присоединить к профилю</a>';
		}
	}
	
	function add_cloud_render_ui() {
		global $authorize_link, $dropbox_added;
	
		print '
		<div id="content">
		<div class="ui card">
			<div class="image">
				<img src="/images/dropbox-logo.png">
			</div>
			
			<div class="content">
				<a class="header">Dropbox</a>
				<div class="meta">
				</div>
			</div>
			
			<div class="extra content">
				<a>'.$dropbox_added.'</a>
			</div>
		</div>
		</div>';
	}

?>