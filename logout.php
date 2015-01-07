<?php
	if (isset($_COOKIE['id'])) {
    	unset($_COOKIE['id']);
        unset($_COOKIE['hash']);
        
		setcookie('id', null, -1, '/');
        setcookie('hash', null, -1, '/');
    }

	header('Location: login.php');
?>
