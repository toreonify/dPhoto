<?
	// nuPhoto
	// Login page
	
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	
	// Connect Database library
	include_once("lib-db.php");
	// Connect Render library
	include_once("lib-render.php");

	$page = 'login';
	
    function generateCode($length=6) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";

        $clen = strlen($chars) - 1;  
        
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];  
        }

        return $code;
    }

    if(isset($_POST['submit']))
    {
        $query = libdb_exec_query_assoc("SELECT id, nu_password FROM `users` WHERE `nu_login`='".addslashes($_POST['login'])."' LIMIT 1");
		
        if($query['nu_password'] === md5(md5($_POST['password'])))
        {
            $hash = md5(generateCode(10));        
			
            libdb_exec_query("UPDATE `users` SET `nu_hash`='".addslashes($hash)."', `nu_lastlogin`=now() WHERE `id`='".addslashes($query['id'])."'");

            setcookie("id", $query['id'], time()+60*60*24*30);
            setcookie("hash", $hash, time()+60*60*24*30);

            header("Location: check.php"); 
            exit();
        }
        else
        {
            print "Вы ввели неправильный логин/пароль";
        }

    }

	lib_render_template("begin");	
	lib_render_template("header");	
	lib_render_template("login");
	lib_render_template("end");	
	
?>