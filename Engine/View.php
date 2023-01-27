<?php

namespace App;

class View
{
    private static $MAKET;
    public static $headStr=[];
    private const MPATH = "View/maket/";
    public static function Show($page, $param = [])
    {

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Pragma: no-cache');
        header('Last-Modified: ' . gmdate("D, d M Y H:i:s \G\M\T"));
        header("Content-Type: text/html;charset=utf-8");

        $fcontent = self::GetView($page, $param);
 
        $HS=join('\r\n',self::$headStr);
        $fcontent=str_replace(["</head>","</HEAD>"],$HS.'</head>',$fcontent);
        echo ($fcontent);
        die();
    }

    public static function SetHeader($headerCode){
        self::$headStr[]=$headerCode;
    }

    public static function GetView($page, $param = [])
    {
        $param['BASE_URL'] = self::GetBaseUrl();
       
        if (!file_exists(self::MPATH . $page . '.html')) {
            return "";
        }
        $f = file_get_contents(self::MPATH . $page . '.html');
        $match = null;
        if (preg_match_all("/%include\['(.*)'\]/", $f, $match)) {
            for ($i = 0; $i < sizeof($match[1]); $i++) {
                $f2 = self::GetView($match[1][$i], $param);
                $f = str_replace($match[0][$i], $f2, $f);
            }
        }
      
       
        foreach ($param as $key => $val) {
            // echo(gettype($val).'; ');
             if(gettype($val)=='array'){continue ;}
            $f = str_replace("%[$key]", $val, $f);
        }

        $f = preg_replace("/%\[(.*)\]/", "", $f);

        return $f;
    }


    public static function GetBaseUrl()
    {
        return Router::GetBaseUrl();
    }
}
