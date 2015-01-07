<?php
	// nuPhoto
	// UI for index page

	$user_logged = false;
			
	function index_calculate_values() {
		global $user_logged;

		if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
			$user_logged = true;
		}
	}
	
	function index_render_ui() {
		global $user_logged;

		if ($user_logged) {
			print '
			<div class="ui left vertical inverted labeled blue sidebar menu">
  			<a class="item">
    			<i class="home icon"></i>	
    			Home
  			</a>
</div>';

		print '
		<div class="pusher">
    		<div class="ui white big launch right attached fixed button" onclick="lib_toggle_sidebar();">
  				<i class="ellipsis vertical icon" onclick="lib_toggle_sidebar();"></i>
		</div>
 		</div>';

		} else {

		print '
		<div class="header-image">
			<div></div>
			<img src="images/index_header.jpg"></img>
		</div>
		
		<div>
		
			<div class="ui piled segment" id="main-land">
		
				<h1 style="margin-top: 0px;">Manage all your photos in one place!</h1>
		
				<p>
				Connect favorite cloud storages and share photos by smart albums with simple click.
				No need to search your photos again.
				</p>
		
			</div>
		
		</div>
		';
		}
	}
?>