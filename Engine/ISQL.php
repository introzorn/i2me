<?php

namespace App;

use mysqli;
//Переводной мост между PDO и MysqlI для совместимости
class ISQL
{
    public $CurConection;
    public $Resource;
    public $sPrepare;
    public const FETCH_ASSOC = 2;

    public function __construct($param = "", $user, $pass)
    {

        if ($param != "") {
            $Par = explode(";", $param);
            $p = [];
            foreach ($Par as $v) {
                $va = explode("=", $v);
                $p[$va[0]] = $va[1];
            }
            $host = explode(":", $p['mysql:host'])[0];
            $port = explode(":", $p['mysql:host'])[1];
            $DBname = $p['dbname'];

            $this->connect($host, $port, $DBname, $user, $pass);


            if ($p['charset']) {
                //var_damp();
                $this->CurConection->set_charset($p['charset']);
            }
            return $this;
        }
    }

    public function connect($host, $port, $DBname, $userName, $password)
    {

        $this->CurConection = new mysqli($host, $userName, $password, $DBname, $port);
        if ($this->CurConection->error) {
            throw new \Exception($this->CurConection->error);
        }
        return $this;
    }

    public function query($SQL)
    {
        $this->Resource = $this->CurConection->query($SQL);

        if (!$this->Resource) {
            throw new \Exception("ERRor Query");
        }
        return $this;
    }

    public function fetchAll()
    {
        $statistic = [];

        while ($data = $this->Resource->fetch_assoc()) {
            $statistic[] = $data;
        }
        if (sizeof($statistic) == 0) {
            throw new \Exception("nullrow");
        }
        return  $statistic;
    }

    public function fetchSimple($param='id')
    {
        $statistic = [];

        while ($data = $this->Resource->fetch_assoc()) {
            $statistic[] = $data[$param];
        }
        // if (sizeof($statistic) == 0) {
        //     throw new \Exception("nullrow");
        // }
        return  $statistic;
    }


    public function fetch()
    {

        $rt = $this->Resource->fetch_assoc();
        if (!$rt) {
            throw new \Exception("nullrow");
        }
        return $rt;
    }


    public function execute(array $array)
    {
        if ($this->sPrepare != "") {
            $sql = $this->sPrepare;

            foreach ($array as $key => $value) {
                if (gettype($value)) {
                    $value = "'" . $value . "'";
                }
                $sql = str_replace(":" . $key, $value, $sql);
            }

            $this->Resource = $this->CurConection->query($sql);
        }
    }

    public function prepare($SQL)
    {
        $this->sPrepare = $SQL;
        return $this;
    }
    public function lastInsertId()
    {
        return mysqli_insert_id($this->CurConection);
    }
}
