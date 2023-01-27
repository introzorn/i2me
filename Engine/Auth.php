<?php


namespace App;

use App\Models;
use App\Models\Model;
use App\Validator as V;
use PDO;
use App\PDOEX;
//мдель пользователя по дефолту
class users extends \App\Model
{

    public function MIGRATE()
    {
        $this->TABLE = [
            'id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'user' => 'char(255) NOT NULL',
            'email' => 'char(255) NOT NULL',
            'password' => 'char(255) NOT NULL',
            'permission' => 'int(11) NOT NULL DEFAULT 0',
            'PRIMARY KEY' => 'id',
            'CHARSET' => 'utf8'
        ];
    }


    //получить данные о пользователе
    public function GetUser($username)
    {
        $username = strtolower($username);
        try {
            // $req = $this->connection->prepare("SELECT * FROM " . $this->DB_TABLE . " WHERE user=:user");
            $user = $this->where("user", "=", $username)->get();
            // $req->execute(['user' => $username]);
            //   $user = $req->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                return $user[0];
            } else {
                return false;
            }
        } catch (PDOEX $err) {
            return false;
        }
        return false;
    }



    //создание пользователя
    public function SetUser($param)
    {
        $param['user'] = strtolower($param['user']);
        $param['password'] = md5($param['password']);
        try {
            $LID = $this->add($param);
            if (!$LID) {
                return false;
            }
            $user = $this->find($LID);
            if ($user) {
                return $user;
            } else {
                return false;
            }
        } catch (PDOEX $err) {
            return false;
        }

        return false;
    }
}

class Auth
{

    public static $RegError;  // ошибки регистрации
    public static $LogError; // ошибки авторизации
    public static $LOGINED; // булевое значения статуса автаризации 
    public static $USER;    // данные пользователя из ДБ
    public static $ASUSER = false;
    public static $AuthByEmail = false;

    public static $login_e_text = [
        "login" => "Неверный формат имя пользователя",
        "min" => "Слишком короткое имя пользователя",
        "max" => "Слишком длинное имя пользователя",
        "not" => "Неверное имя пользователя или пароль",
        "null" => "Введены не все поля",
        "uniq" => "такой пользователь уже существует",
    ];

    public static $pass_e_text = [
        "pass" => "Неверный формат пароля",
        "min" => "Слишком короткий пароль",
        "max" => "Слишком длинный пароль",
        "not" => "Неверное имя пользователя или пароль",
        "==" => "Пароли не совподают",
        "null" => "Введены не все поля",
    ];

    public static $email_e_text = [
        "email" => "Неверный формат email",
        "min" => "Слишком короткий email",
        "max" => "Слишком длинный email",
        "not" => "Неверный email или пароль",
        "null" => "Введены не все поля",
        "uniq" => "такой email уже существует",
    ];

    public static function GETUR()
    {
        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = explode('?', $url);
        $url = $url[0];
        $url = str_replace("/admin/", '/', $url);
        $url = str_replace("/user/", '/', $url);
        return $url;
    }

    //обработка регистрации пользователя
    public static function  Reg()
    {
        if (self::$LOGINED) {
            return true;
        }

        $login = $_POST['login'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];
        $email = $_POST['email'];
        $_SESSION['aw_login'] = $login;



        if ($login == "" || $password == "" || $password2 == "") {
            self::$RegError = self::$login_e_text['null'];
            return false;
        }

        $v = V::VALID($login, "min:4|max:32|login");
        if ($v != true) {
            self::$RegError = self::$login_e_text[$v];
            return false;
        }


        if ($password != $password2) {
            self::$RegError = self::$pass_e_text['=='];
            return false;
        }

        $m = new users();
        if ($m->GetUser($login)) {
            self::$RegError = self::$pass_e_text['uniq'];
            return false;
        }
        $addarray = ['user' =>  $login, 'password' => $password,];
        if (V::valid($email, "min:5|max:32|email") == true) {
            $addarray['email'] = $email;
        }
        $u = $m->SetUser(['user' =>  $login, 'password' => $password,]);
        if ($u == false) {
            self::$RegError = 'ошибка создания пользователя';
            return false;
        }

        self::$USER = $u;
        self::$LOGINED = true;
        $_SESSION['login'] = $login;
        $_SESSION['password'] = md5($password);
        return true;
    }

    //авторизация
    public static function Login()
    {


        $login = $_POST['login'] ?? $_SESSION['login'];

        $password = $_POST['password'];
        if ($_POST['password']) {
            $password = md5($password);
        } else {
            $password = $_SESSION['password'];
        }

        $_SESSION['aw_login'] = $login;


        if ($login == "" || $password == "") {
            self::$RegError = self::$login_e_text['null'];
            return false;
        }

        $v = V::VALID($login, "min:4|max:32|login");

        if ($v != true) {
            self::$RegError = self::$login_e_text[$v];
            return false;
        }

        $m = new users();
        $user = $m->GetUser($login);


        if (!$user) {
            self::$LogError = self::$login_e_text['not'];
            return false;
        }

        if ($password != $user['password']) {
            self::$LogError = self::$login_e_text['not'];
            return false;
        }

        self::$USER = $user;
        self::$LOGINED = true;
        $_SESSION['login'] = $login;
        $_SESSION['password'] = $password;

        return true;
    }

    // процедура разлога
    public static function Logout()
    {
        session_destroy();
        self::$USER = "";
        self::$LOGINED = false;
    }
}
