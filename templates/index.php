<?php
	// nuPhoto
	// UI for index page

	$user_logged = false;
	$albums_exist = false;
	$no_cloud = false;
			
	function index_calculate_values() {
		global $user_logged, $albums_exist, $no_cloud;

		if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
			$user_logged = true;
			$albums_exist = libdb_check_albums();
		
			if (libdb_get_user_token(0) == '') {
				$no_cloud = true;
			}
		}
		
	}
	
	function index_render_ui() {
		global $user_logged, $albums_exist, $no_cloud;

		if ($user_logged) {
			
			if (!$no_cloud) {
			
				print '<script src="javascript/index.js"></script>';
				print '<script src="javascript/analyze.js"></script>';
			
				print '			
				<div class="ui left vertical inverted labeled blue sidebar menu" id="albums_list">
	  			<span class="item header">
	    			Альбомы
	    		<a class="menu-button" href="javascript:lib_add_album()">
	    			<div class="menu-button">
	    				<i class="plus icon" style="margin-right: 0px; width: 23px;"></i>
	    			</div>
	    		</a>
	  			</span>
	
	  			</div>';
	
				print '<div class="ui inverted dimmer" id="loader">
								<div class="ui text loader">Загрузка</div>
						   </div>';
		
				print '<div class="ui basic modal" id="viewer">
		    <div class="ui left basic inverted button prevnext_viewer" style="left: -28px; box-shadow: 0 0 0 0px #fff !important; " onclick="lib_viewer_prev()" id="viewer_prev"><i class="chevron left icon"></i></div>
		    <div class="ui right basic inverted button prevnext_viewer" style="right: -28px;box-shadow: 0 0 0 0px #fff !important;" onclick="lib_viewer_next()" id="viewer_next"><i class="chevron right icon"></i></div>
		  <div class="content">
		  	<div style="z-index:999; position: fixed; border: 3px solid rgba(255, 255, 255, 0.58); visibility: hidden;" id="viewer_highlight"></div>
			<img src="" class="ui rounded image" id="viewer-img"></img>

		  </div>
		  <div class="actions">
		    <div class="two fluid ui inverted buttons">
		      <a id="viewer_link" class="ui blue basic inverted button" style="width:200px;" href="" target="_blank">
		
		        <i class="angle expand icon"></i>Открыть оригинал
		
		      </a>
			  <div class="ui basic inverted button" onclick="active_viewer = -1;" style="width:200px;">
			  <i class="close icon"></i>
		        Закрыть
		      </div>
			  <div class="ui basic red inverted button" style="visibility: hidden; display: none; width: 350px;" id="delete_from_album" onclick="lib_photo_delete()">
			  <i class="trash icon"></i>
		        Удалить из альбома
		      </div>
			  <div class="ui basic red inverted button" style="visibility: hidden; display: none; width:350px" id="remove_from_photo">
			  
		        
		      </div>
		    </div>
		  </div>
		  <div class="ui animated horizontal inverted list" id="viewer_faces">
		  	
		  			
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
			  					Нет альбомов<div class="sub header">Создайте новый альбом нажав <i class="plus icon" style="font-size: 1em; display: inline;"></i> в боковом меню.</div>
			  					</div>
			  				</h2>
			  			</div>';
		  		} 
		  		
		  		print '</div>';
		  			
		 		print '</div>';
 		
	 		} else {
		 		print '<div id="album_content">';
	
			  		print '<div id="content-no-album">
			  				<h2 class="ui icon header">
			  					<i class="settings icon"></i>
			  				
			  					<div class="content">
			  					Облако не присоединено
			  					<div class="sub header">Присоедините облако в настройках пользователя, чтобы начать работу.</div>
			  					</div>
			  				</h2>
			  			</div>';
		  		
		  		print '</div>';
	 		}

		} else {

		print '
		<div class="header-image">
			<div></div>
			<img src="images/index_header.jpg"></img>
		</div>
		
		<div>
		
			<div class="ui piled segment" id="main-land">
		
				<h1 style="margin-top: 0px;">Управляйте всеми фотографиями в одном месте!</h1>
		
				<p>
				Пприсоедините ваш любимый облачный сервис и отсортируйте фотографии с умными альбомами.
				Теперь не нужно искать фотографии.
				</p>
				<a class="big blue ui button" style="z-index: 999; position: relative;" href="register.php">Начать работу</a>
			</div>
		
		</div>
		';
		}
	}
?>