<?php
	// nuPhoto
	// UI for standart header
	
	$user_logged = false;
	$display_name = NULL;		
	$user_avatar = NULL;
	$user_link = NULL;
	$user_needforcloud = false;
		
	function header_calculate_values() {
		global $user_logged, $display_name, $user_avatar, $user_link, $user_needforcloud;
		include_once('lib-db.php');
		
		// Get user info
		if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
			$user_logged = true;
			
			// Get info from cloud services
			$user_token_drop = libdb_get_user_token(0);
			$user_token_oned = libdb_get_user_token(1);
			
			// Get user profile link
			$user_link = "user.php";
			
			if ($user_token_drop != '') {
				include_once('lib-dropbox.php');
				
				$info = lib_get_user_info();
			
				$display_name = $info['display_name'];
			} elseif ($user_token_oned != '') {
				// To be written
			} else {
				$user_needforcloud = true;
				
				$display_name = libdb_exec_query_assoc("SELECT nu_login FROM `users` WHERE `id` = '".$_COOKIE['id']."' LIMIT 1")['nu_login'];
			}
			// End
			
			// Get avatar image
			$user_avatar = libdb_get_user_photo();
			// End
		} else {
			$display_name = "Log in";
			$user_link = "login.php";
		}
	}
	
	function header_render_ui() {
		include_once('text/templates.php');
		global $user_logged, $display_name, $user_avatar, $user_link, $user_needforcloud;

		
		print '
		<div class="ui fixed menu borderless" id="header-menu">
			<div class="active item">
				nuPhoto
  			</div>
  			<a class="item" onclick="lib_folder_back();" id="back-button"><i class="angle left icon"></i> Back</a>
			
			<a class="item title disabled"></a>
			
			<div class="right menu">';
			
		if ($user_needforcloud) {
			print '<a href="add_cloud.php" class="item">Add cloud</a>';
		}
		
		if ($user_logged) {
		print '
		<div class="ui dropdown item">
			<i class="user icon"></i>
  			<div class="text">'.$display_name.'</div>
  			
  			<div class="menu">
    			<div class="header">Account</div>
    			<div class="item"><a href="settings.php"><i class="setting icon"></i>Settings</a></div>
    			<div class="item"><a href="logout.php"><i class="sign out icon"></i>Log out</a></div>
  			</div>
		</div>';
		} else {
			print '<a href="'.$user_link.'" class="item">'
				.$display_name.
			'</a>';
		}

		print '</div>
		
		</div>';
	}

	function header_add_cloud_calculate_values() {
		header_calculate_values();
	}
	
	function header_add_cloud_render_ui() {
		global $user_logged, $display_name, $user_avatar, $user_link, $user_needforcloud;
		
		print '
		<div class="ui fixed menu borderless" id="header-menu">
			<a href="index.php" class="item">nuPhoto</a>
  		
			
			<div class="right menu">';
			
		if ($user_needforcloud) {
			print '<a href="add_cloud.php" class="active item">Add cloud</a>';
		}
		
		if ($user_logged) {
		print '
		<div class="ui dropdown item">
			<i class="user icon"></i>
  			<div class="text">'.$display_name.'</div>
  			
  			<div class="menu">
    			<div class="header">Account</div>
    			<div class="item"><a href="settings.php"><i class="setting icon"></i>Settings</a></div>
    			<div class="item"><a href="logout.php"><i class="sign out icon"></i>Log out</a></div>
  			</div>
		</div>';
		} else {
			print '<a href="'.$user_link.'" class="item">'
				.$display_name.
			'</a>';
		}

		print '</div>
		
		</div>';		
	}
	
?>
