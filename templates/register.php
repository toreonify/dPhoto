<?php
	// nuPhoto
	// UI for register page
			
	function register_calculate_values() {
		return false;
	}
	
	function register_render_ui() {
		global $err;
	
		print '
		<div class="ui piled segment" id="login-land">';
	
		if (count($err) != 0) {
			print '
			<div class="ui error message">
				<div class="header">
					Произошли следующие ошибки при регистрации:
				</div>
				<ul class="list">';

			foreach ($err as $error) {
				print '<li>'.$error.'</li>';
			}

			print '
				</ul>
			</div>';
		}
	
		print '
			<div class="ui form">
				<form method="POST">
					<div class="field">
						<label>Имя пользователя</label>
						<input type="text" name="login">
					</div>
					<div class="field">
						<label>Пароль</label>
						<input name="password" type="password">
					</div>
					<button name="submit" class="ui submit button">Подтвердить</button>
				</form>
			</div>
					
		</div>';
	
	}
?>