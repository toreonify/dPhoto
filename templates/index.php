<?php
	// nuPhoto
	// UI for index page

	$user_logged = false;
	$albums_exist = false;
			
	function index_calculate_values() {
		global $user_logged, $albums_exist;

		if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
			$user_logged = true;
			$albums_exist = libdb_check_albums();
		}
		
	}
	
	function index_render_ui() {
		global $user_logged, $albums_exist;

		if ($user_logged) {
			print '<script src="javascript/index.js"></script>';
			print '<script src="javascript/analyze.js"></script>';
			
			print '			
			<div class="ui left vertical inverted labeled blue sidebar menu" id="albums_list">
  			<span class="item header">
    			Albums
    		<a class="menu-button" href="javascript:lib_add_album()">
    			<div class="menu-button">
    				<i class="plus icon" style="margin-right: 0px; width: 23px;"></i>
    			</div>
    		</a>
  			</span>

  			</div>';

		print '<div class="ui inverted dimmer" id="loader">
						<div class="ui text loader">Loading</div>
				   </div>';

		print '<div class="ui basic modal" id="viewer">
  <div class="content">
	<img src="" class="ui rounded image" id="viewer-img"></img>
  </div>
  <div class="actions">
    <div class="two fluid ui inverted buttons">
      <a id="viewer_link" class="ui basic inverted button" href="" target="_blank">

        <i class="angle expand icon"></i>Open full image

      </a>
	  <div class="ui basic inverted button">
	  <i class="angle close icon"></i>
        Close preview
      </div>
    </div>
  </div>
</div>';


		print '
		<div class="pusher">
    		<div class="ui white big launch right attached fixed button" style="top: 55px !important;" onclick="lib_toggle_sidebar();">
  				<i class="ellipsis vertical icon" onclick="lib_toggle_sidebar();"></i>
  			</div>';

  		print '<div id="album_content">';
	  			
  		if (!$albums_exist) {	
	  		print '<div id="content-no-album">
	  				<h2 class="ui icon header">
	  					<i class="photo icon"></i>
	  				
	  					<div class="content">
	  					No albums
	  					<div class="sub header">Create new albums by pressing <i class="plus icon" style="font-size: 1em; display: inline;"></i> in side menu.</div>
	  					</div>
	  				</h2>
	  			</div>';
  		} else {
	  		
  		}	
  		
  		print '</div>';
  			
 		print '</div>';

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