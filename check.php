<?
	// nuPhoto
	// Login check

    include '../config_mysql.php';
    include 'lib-db.php';

	error_reporting(E_ALL);
	ini_set('display_errors', 'on');

    if (isset($_COOKIE['id']) and isset($_COOKIE['hash']))
    {  
        $query = "SELECT * FROM users WHERE id = '".intval($_COOKIE['id'])."' LIMIT 1";
        $userdata = libdb_exec_query_assoc($query);

        if(($userdata['nu_hash'] !== $_COOKIE['hash']) or ($userdata['id'] !== $_COOKIE['id']))
        {
            setcookie("id", "", time() - 3600*24*30*12, "/");
            setcookie("hash", "", time() - 3600*24*30*12, "/");

            header('Loaction: login.php');
        }
        else
        {
			header('Location: index.php');
        }
    }
    else
    {
        print "Включите куки";
    }

?>
