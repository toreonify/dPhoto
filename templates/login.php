<?php
	// nuPhoto
	// UI for login page
			
	function login_calculate_values() {
		return false;
	}
	
	function login_render_ui() {
	
		print '
		<div class="ui piled segment" id="login-land">

			<div class="ui form">
				
				<form method="POST">
				
				<div class="two fields">
					<div class="required field">
						<label>Имя пользователя</label>
						
						<div class="ui icon input">
							<input type="text" name="login">
							<i class="user icon"></i>
						</div>
					</div>
					
					<div class="required field">
						<label>Пароль</label>
						
							<div class="ui icon input">
								<input type="password" name="password">
								<i class="lock icon"></i>
							</div>
					</div>
				</div>
  
				<input class="ui submit button" type="submit" name="submit" value="Войти"/>
			
				</form>
			
			</div>
			
		</div>';
	
	}

?>