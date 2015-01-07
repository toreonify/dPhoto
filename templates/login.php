<?php
	// nuPhoto
	// UI for index page
			
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
						<label>Username</label>
						
						<div class="ui icon input">
							<input type="text" name="login" placeholder="Username">
							<i class="user icon"></i>
						</div>
					</div>
					
					<div class="required field">
						<label>Password</label>
						
							<div class="ui icon input">
								<input type="password" name="password">
								<i class="lock icon"></i>
							</div>
					</div>
				</div>
  
				<input class="ui submit button" type="submit" name="submit" value="Login"/>
			
				</form>
			
			</div>
			
		</div>';
	
	}

?>