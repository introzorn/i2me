<?php

namespace App;

use PDO;
use PDOException;

class Model
{

    public $DB_HOST;
    public $DB_PORT;
    public $DB_NAME;
    public $DB_USER;
    public $DB_PASS;
    public $DB_TABLE;
    public $TABLE;
    public static $connection;
    public static $DIE_IF_ERROR = true;
    public  $paginator;
    public  $CHARSET ="utf8";
    public  $COLLATE ="";
    private  $ifCOLLATE="";
    //public  $COLLATE ="unicode_ci";
    //условия where orwhere like limit

    private $where = "";
    private $like = "";
    private $orderby = "";

    private function getClass()
    {
        $className = explode("\\", get_class($this));
        return $className[count($className) - 1];
    }



    public function __construct() //конструктор модели
    {
        if($this->CHARSET==""){$this->CHARSET="utf8";}
        $this->ifCOLLATE=$this->COLLATE?" COLLATE ".$this->CHARSET."_".$this->COLLATE:"";
       

        $DB_TABLE = $this->getClass();
        $this->DB_HOST = isset($this->DB_HOST) ? $this->DB_HOST : DB_HOSTNAME;
        $this->DB_PORT = isset($this->DB_PORT) ? $this->DB_PORT : DB_PORT;
        $this->DB_NAME = isset($this->DB_NAME) ? $this->DB_NAME : DB_DATABASE;
        $this->DB_USER = isset($this->DB_USER) ? $this->DB_USER : DB_USERNAME;
        $this->DB_PASS = isset($this->DB_PASS) ? $this->DB_PASS : DB_PASSWORD;
        $this->DB_TABLE = ($DB_TABLE != '') ? $DB_TABLE : $this->DB_TABLE;

        $this->connect();
    }



    public function connect($errflag = false) //соединение с базой данных
    {
      
       
        try {
            if (self::$connection) {
                $db=self::$connection;
            }else{
                
                $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$this->CHARSET.$this->ifCOLLATE];

             
                 $db = new PDO("mysql:host=" . DB_HOSTNAME . ":" . DB_PORT . ";dbname=" . DB_DATABASE . ";charset=".$this->CHARSET.';', DB_USERNAME, DB_PASSWORD,$options);
                 self::$connection = $db;
            }

           
          
            $db->exec("SET NAMES ".$this->CHARSET.$this->ifCOLLATE.';');
          
           
            $res = $db->query("SHOW TABLES LIKE '" . $this->DB_TABLE . "'");

          

            if ($res->rowCount() <= 0) {
                if ($errflag) {
                    if (self::$DIE_IF_ERROR) {
                        die('table not found');
                    }
                }
                $this->goMIGRATE();
                $this->connect(true);
            }
        } catch (PDOEX  $err) {
            if ($err->code == '1146') {
                if ($errflag) {
                    if (self::$DIE_IF_ERROR) {
                        die('table not found');
                    }
                }
                $this->goMIGRATE();
                $this->connect(true);
            }
            if (self::$DIE_IF_ERROR) {
                die($err->getMessage());
            }
        }

        return $this;
    }


    public function MIGRATE() //миграции 
    {
        $this->TABLE = [
            'id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'name' => 'char(255) NOT NULL',
            'addtime' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'PRIMARY KEY' => 'id',
            'CHARSET' => 'utf8'
        ];
    }

    private function goMIGRATE() //запуск миграции
    {
        $this->MIGRATE();
        
        $TABLENAME = $this->DB_TABLE;
        $charset = $this->CHARSET;
        $primary = "";
        $ifCOLLATE=$this->COLLATE?" COLLATE=".$this->CHARSET."_".$this->COLLATE:"";
        $sql_maket = "CREATE TABLE IF NOT EXISTS `%NAME%` (%ROWS% %PRIM%) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=%CHARSET%$ifCOLLATE;";
        $row = [];
        foreach ($this->TABLE as $key => $val) {

            if ($key == 'CHARSET') {
                $charset = $val;
                $val = '';
            }
            if ($key == 'PRIMARY KEY') {
                $primary = ", PRIMARY KEY (`" . $val . "`)";
                $val = '';
            }
            if ($val != '') {
                $row[] = "`" . $key . "` " . $val;
            }
        }

        $row[] = "`add_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP";
    
        $sql_maket = str_replace(["\r\n", "\r", "\n", "\t"], '', $sql_maket);
        $sql_maket = str_replace('%NAME%', $TABLENAME, $sql_maket);
        $sql_maket = str_replace('%PRIM%', $primary, $sql_maket);
        $sql_maket = str_replace('%CHARSET%', $charset, $sql_maket);
        $sql_maket = str_replace('%ROWS%', join(', ', $row), $sql_maket);

    
        $cn= self::$connection->query($sql_maket);
     
    }

    public function add($array) //создание поля, возвращает массив поля с id
    {
        try {
            $keys = array_keys($array);
            $keys_var = join("`,`", $keys);
            $keys_preg = join(", :", $keys);
            self::$connection->exec("SET NAMES ".$this->CHARSET.$this->ifCOLLATE.';');
            $req = self::$connection->prepare("INSERT INTO " . $this->DB_TABLE . " (`" . $keys_var . "`) VALUES (:" . $keys_preg . ")");
            $ret = $req->execute($array);
            if (!$ret) {
                return false;
            }
            return self::$connection->lastInsertId();
        } catch (PDOEX $err) {

            return false;
        }
    }

    public function edit($id, $array) //изменение поля, возвращает массив поля с id или фалсе
    {
        try {
            $keys = array_keys($array);
            foreach ($keys as &$val) {
                $val = '`' . $val . '`=:' . $val;
            }
            $keys_preg = join(", ", $keys);
            $array['id'] = $id;
            self::$connection->exec("SET NAMES ".$this->CHARSET.$this->ifCOLLATE.';');
            $req = self::$connection->prepare("UPDATE " . $this->DB_TABLE . "  SET $keys_preg WHERE id=:id");
            $ret = $req->execute($array);
           
            if (!$ret) {
                return false;
            }
            return true;
        } catch (PDOEX $err) {
            
            return false;
        }
    }

    public function delete($id) //удаляет поле по id
    {
        try {
            $req = self::$connection->prepare("DELETE FROM " . $this->DB_TABLE . " WHERE id=:id");
            $req->execute(['id' => $id]);
            return true;
        } catch (PDOEX $err) {
            return false;
        }
    }

    public function find($id) //поиск элемента
    {
        try {
            $req = self::$connection->prepare("SELECT * FROM " . $this->DB_TABLE . " WHERE id=:id");
            $req->execute(['id' => $id]);
            $ret = $req->fetch(PDO::FETCH_ASSOC);
            if (!$ret) {
                return false;
            }
            return $ret;
        } catch (PDOEX $err) {
            return false;
        }
    }

    public function get($limit = '') //выборка из модели
    {
        try {
            if ($limit != '') {
                $limit = " LIMIT " . $limit;
            }
          
            if ($this->where != "" && strpos($this->where, " WHERE ") <= 0) {
                $this->where = " WHERE " . $this->where;
            }
            $sql = "SELECT * FROM " . $this->DB_TABLE . "" . $this->where . $this->orderby . $limit;
            self::$connection->exec("SET NAMES ".$this->CHARSET.$this->ifCOLLATE.';');
            $req = self::$connection->query($sql);
           //if(!$req){die($sql);} 'если надо проверить ошибку запроса
            $ret = $req->fetchAll(PDO::FETCH_ASSOC);
            if (!$ret) {
                return false;
            }
            $this->where="";
            $this->orderby="";

            return $ret;
        } catch (PDOEX $err) {
            $this->where="";
            $this->orderby="";
            return false;
        }
    }


    public function dell($limit = '') //удаление с условиями
    {
        try {
            if ($limit != '') {
                $limit = " LIMIT " . $limit;
            }
          
            if ($this->where != "" && strpos($this->where, " WHERE ") <= 0) {
                $this->where = " WHERE " . $this->where;
            }
           
            $sql = "DELETE FROM " . $this->DB_TABLE . "" . $this->where . $limit;
            self::$connection->exec("SET NAMES ".$this->CHARSET.$this->ifCOLLATE.';');
            $req = self::$connection->query($sql);
         
           // $ret = $req->fetchAll(PDO::FETCH_ASSOC);
    
            $this->where="";
            $this->orderby="";

            return true;
        } catch (PDOEX $err) {
            $this->where="";
            $this->orderby="";
            return false;
        }
    }




    public function count() //получаем колличество строк запроса
    {
        try {

            if ($this->where != "" && strpos($this->where, " WHERE ") <= 0) {
                $this->where = " WHERE " . $this->where;
            }

            $ifCOLLATE=$this->COLLATE?"  COLLATE ".$this->CHARSET."_".$this->COLLATE:"";
            $this->connection->exec("SET NAMES ".$this->CHARSET.$ifCOLLATE);

            $sql = "SELECT COUNT(*) FROM " . $this->DB_TABLE . "" . $this->where . $this->orderby;
            self::$connection->exec("SET NAMES ".$this->CHARSET.$this->ifCOLLATE.';');
            $req = self::$connection->query($sql);
            $ret = $req->fetchAll(PDO::FETCH_ASSOC);
            if (!$ret) {
                return 0;
            }
            return $ret[0]['COUNT(*)'];;
        } catch (PDOEX $err) {
            return 0;
        }
    }

    public function paginate($lenght = 10) //делаем пагинацию
    {
        $count = $this->count();
        $this->paginator = new Paginator($count, $lenght);
        $ret = $this->get($this->paginator->POS . ', ' . $this->paginator->paginsize);
        if (!$ret) {
            return false;
        }
        return $ret;
    }

    public function getPaginator() //получаем пагинатор в html
    {
        if ($this->paginator) {
            return $this->paginator->PAGINATOR();
        }
        return false;
    }

    public function query($sql,$featch=PDO::FETCH_ASSOC)
    { //быстрый доступ к пдо
        try {
            self::$connection->exec("SET NAMES ".$this->CHARSET.$this->ifCOLLATE.';');
            $req = self::$connection->query($sql,$featch);
            return $req;
        } catch (PDOEX $err) {
            return false;
        }
    }

    public function prepare($sql)
    { //быстрый доступ к пдо
        try {
            self::$connection->exec("SET NAMES ".$this->CHARSET.$this->ifCOLLATE.';');
            $req = self::$connection->prepare($sql);
            return $req;
        } catch (PDOEX $err) {
            return false;
        }
    }

    public function where($key, $us, $val, $nomarks=false)
    {
        if (gettype($val) == 'string' &&  $nomarks==false) {
            $val=self::$connection->quote($val);
        }
        $this->where = $key . " " . $us . " " . $val;
        return $this;
    }

    public function orwhere($key, $us, $val, $nomarks=false)
    {
        if ($this->where == "") {
            return $this->where($key, $us, $val);
        }
        if (gettype($val) == 'string' &&  $nomarks==false) {
            $val=self::$connection->quote($val);
        }
        $this->where = $this->where . " OR " . $key . " " . $us . " " . $val;
        return $this;
    }

    public function andwhere($key, $us, $val, $nomarks=false)
    {
        if ($this->where == "") {
            return $this->where($key, $us, $val);
        }
        if (gettype($val) == 'string' &&  $nomarks==false) {
           // $val = "'" . $val . "'";
           $val=self::$connection->quote($val);
        }
        $this->where = $this->where . " AND " . $key . " " . $us . " " . $val;
        return $this;
    }

    public function orderBy($key, $sort = "")
    {
        if ($sort != "") {
            $sort = " " . $sort;
        }
        $this->orderby = " ORDER BY " . $key . $sort;
        return $this;
    }



    public function NOW(){
        $res=$this->query("SELECT NOW() as `timestamp`");
        if($res === false){return false;}
        $ret=$res->fetch(PDO::FETCH_ASSOC);
        if($ret === false){return false;}
        return $ret['timestamp'];
    }

}


class PDOEX extends PDOException
{
    //класс для пдо исключений
    public function __construct(PDOException $e)
    {
        if (strstr($e->getMessage(), 'SQLSTATE[')) {
            preg_match('/SQLSTATE\[(\w+)\] \[(\w+)\] (.*)/', $e->getMessage(), $matches);
            $this->code = ($matches[1] == 'HT000' ? $matches[2] : $matches[1]);
            $this->message = $matches[3];
        }
    }
}

class Paginator // класс пагинатора для удобного извлечения записей из бд
{
    public $paginsize;
    public $paginall;
    public $page;
    public $minpage;
    public $maxpage;
    public $POS;
    public $PAGIN_ARRAY = [];
    public static $MYBASE_URL = '/';

    public function __construct($count, int $size = 10)
    {
        $this->paginsize = $size;
        $this->page = $_GET['page'] ?? 1;
        $this->paginall = $count;
        $this->minpage = 1;
        $this->maxpage = ceil($this->paginall / $this->paginsize);
        if ($this->page > $this->maxpage) {
            $this->page = $this->maxpage;
        }
        if ($this->page < 1) {
            $this->page = 1;
        }
        $this->POS = ($this->page - 1) * $this->paginsize;
        // $this->PAGIN_ARRAY = array_slice($array, $this->POS, $this->paginsize);

        return $this->PAGIN_ARRAY;
    }

    public function PAGINATOR()
    {
        $pageArray[] = '<span class="pagin pagin_cur">' . $this->page . '</span>';
        $m = 4;
        $n = $this->page;
        $l = $n;
        $r = $n;



        for ($i = 1; count($pageArray) < 5; $i++) {
            $l = $n - $i;
            $r = $n + $i;
            if ($l > 0) {
                array_unshift($pageArray, '<a class="pagin pagin_link" title="Страница ' . $l . '" href="p' . $l . '" onclick="pagin(event,' . $l . ')">' . $l . '</a>');
            }
            if ($r <= $this->maxpage) {
                array_push($pageArray, '<a class="pagin pagin_link" title="Страница ' . $r . '" href="p' . $r . '" onclick="pagin(event,' . $r . ')">' . $r . '</a>');
            }
            if ($l <= 0 && $r > $this->maxpage) {
                break;
            }
        }

        if ($l > 1) {
            array_unshift($pageArray, '<a class="pagin pagin_link" title="В начало" href="p1" onclick="pagin(event,1)">❮</a>');
        } else {
            array_unshift($pageArray, '<span class="pagin pagin_hide">&nbsp;</span>');
        }
        if ($r < $this->maxpage) {
            array_push($pageArray, '<a class="pagin pagin_link" title="В конец" href="p' . $this->maxpage . '"  onclick="pagin(event,' . $this->maxpage . ')">❯</a>');
        } else {
            array_push($pageArray, '<span class="pagin pagin_hide">&nbsp;</span>');
        }

        return join("", $pageArray);
    }
}
