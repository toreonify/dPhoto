<?
	// nuPhoto
	// Login check

    mysql_connect("localhost", "root", "kf3VeMww");
    mysql_select_db("nuPhoto");

	error_reporting(E_ALL);
	ini_set('display_errors', 'on');

    if (isset($_COOKIE['id']) and isset($_COOKIE['hash']))
    {  
        $query = mysql_query("SELECT * FROM users WHERE id = '".intval($_COOKIE['id'])."' LIMIT 1");
        $userdata = mysql_fetch_assoc($query);

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
