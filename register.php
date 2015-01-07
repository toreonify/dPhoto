<?

    mysql_connect("localhost", "root", "kf3VeMww");
    mysql_select_db("nuPhoto");


    if(isset($_POST['submit']))
    {

        $err = array();

        if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['login']))
        {
            $err[] = "Логин может состоять только из букв английского алфавита и цифр";
        }

        if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30)
        {
            $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
        }    

        $query = mysql_query("SELECT COUNT(id) FROM users WHERE nu_login='".mysql_real_escape_string($_POST['login'])."'");

        if(mysql_result($query, 0) > 0)
        {
            $err[] = "Пользователь с таким логином уже существует в базе данных";
        }


        if(count($err) == 0)
        {
            $login = $_POST['login'];

            $password = md5(md5(trim($_POST['password'])));
  
            mysql_query("INSERT INTO users SET nu_login='".$login."', nu_password='".$password."'");

            header("Location: login.php"); exit();
        } 
        else 
        {
            print "<b>При регистрации произошли следующие ошибки:</b><br>";

            foreach($err AS $error) {
                print $error."<br>";
            }
        }

    }

?>

<form method="POST">

    Логин <input name="login" type="text"><br>

    Пароль <input name="password" type="password"><br>

    <input name="submit" type="submit" value="Зарегистрироваться">

</form>
