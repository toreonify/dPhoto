<?php
	// nuPhoto
	// Register page
	
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
    $err = array();
	
	// Connect Database library
	include_once("lib-db.php");
	// Connect Render library
	include_once("lib-render.php");

	$page = 'register';

    if(isset($_POST['submit']))
    {

        if (!preg_match("/^[a-zA-Z0-9]+$/", $_POST['login'])) {
            $err[] = "Логин может состоять только из букв английского алфавита и цифр";
        }

        if ( (strlen($_POST['login']) < 3) || (strlen($_POST['login']) > 30) ) {
            $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
        }    

        if ( (strlen($_POST['password']) < 3) || (strlen($_POST['password']) > 30) ) {
            $err[] = "Пароль должен быть не меньше 3-х символов и не больше 30";
        }   

		$mysql = NULL;
		connect($mysql);

        $query = libdb_exec_query_assoc("SELECT COUNT(id) FROM users WHERE nu_login='".$mysql->real_escape_string($_POST['login'])."'");

		$mysql->close();

        if ($query['COUNT(id)'] > 0) {
            $err[] = "Пользователь с таким логином уже существует в базе данных";
        }

        if(count($err) == 0) {
	        
            $login = $_POST['login'];

            $password = md5(md5(trim($_POST['password'])));
  
            libdb_exec_query("INSERT INTO users SET nu_login='".$login."', nu_password='".$password."'");

            header("Location: login.php"); exit();
            
        } 

    }

	lib_render_template("begin", $page);	
	lib_render_template("header");	
	lib_render_template("register");
	lib_render_template("end");	

?>