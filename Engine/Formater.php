<?php


namespace App;
class Formater
{

    static function Format($str, $format){
        if($format=="number"){ return self::FormatNumber($str);}
        if($format=="number2"){ return self::FormatNumber2($str);}
    }

    private static function FormatNumber($str){
        $nar = str_split(strrev(trim($str)));
        $retstr="";
        $n=0;
      
        for ($i=0; $i < sizeof($nar) ; $i++) { 
           $n++;
           $retstr.=$nar[$i];
            if($n==2){$retstr.="-";}
            if($n==4){$retstr.="-";}
            if($n==7){$retstr.=")";}
            if($n==10){$retstr.="(";}
           
        }
         return strrev($retstr);
    }
    private static function FormatNumber2($str){
        $nar = str_split(strrev(trim($str)));
        $retstr="";
        $n=0;
       
        for ($i=0; $i < sizeof($nar) ; $i++) { 
           $n++;
           $retstr.=$nar[$i];
            if($n=2){$retstr.=" ";}
            if($n=4){$retstr.=" ";}
            if($n=7){$retstr.=")";}
            if($n=10){$retstr.="(";}
           
        }
         return strrev($retstr);
    }

}