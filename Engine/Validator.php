<?php
namespace App;
//класс валидатора
 class Validator {

    public static function VALID($var, $patern){
        $pata=explode("|",$patern);
        for($i=1;$i<sizeof($pata);$i++){
            $par=explode(":",$pata[$i]);
            $ret=true;
            switch ($par[0]) {
                case 'min': $ret=self::V_MinMAX($var,$par[1],strlen($var));break; 
                case 'max' : $ret=self::V_MinMAX($var,0,$par[1]);break;
                case 'lat' : $ret=self::V_LAT($var);break;
                case 'num' : $ret=self::V_NUM($var);break;
                case 'email' : $ret=self::V_EMAIL($var);break;
                case 'login' : $ret=self::V_LOG($var);break;
                case 'pass' : $ret=self::V_PASS($var);break;
                default:
                $ret=true;
                    break;
            }
            if($ret===false){return $par[0];}

        }

        return true;
    }


//валидатор по длинне строки
    public static function V_MinMAX($var,$min,$max):bool{
        if (strlen($var)>=$min && strlen($var)<=$max){ return true;}
        return false;
    }
//валидатор емэила
    public static function V_EMAIL($var):bool{
        return self::PregVALID($var,"/.+@.+\..+/");
    }   
//валидатор латинских букв
    public static function V_LAT($var):bool{
    return self::PregVALID($var,"/[^A-Za-z]/");
    }   

    //валидатор латинский логин
    public static function V_LOG($var):bool{
        return self::PregVALID($var,"/[^A-Za-z0-9]/");
     }   

//валидатор целых
    public static function V_NUM($var):bool{
    return self::PregVALID($var,"/^[0-9]+$/");
    }   
//валидатор паролей
    public static function V_PASS($var):bool{
    return self::PregVALID($var,"/^[A-Za-z0-9]+$/");
    }   
    //валидация по регулярке
    public static function PregVALID($var,$preg):bool{

        if(preg_match($preg,$var)){
            return true;
        }
        return false;

    }

 }