<?php
/**
 * Точка вхождения index
 * 
 * Этот модуль запускается первый при любых запросах
 * Подгружает все необходимые параметры, подгружает Автолоадер и инициализирует сессию
 * 
 * @author Хроленко П.A. <introzorn@yandex.ru>
 * @version 2.0
 * @copyright IntroZorn (c) 2022, Хроленко П.А.
 */
namespace App{


use App\Router;
use App\Autoloader;



if (file_exists('config.php')) {
    require_once('config.php'); //подгружаем конфиги
}else{
    require_once('config_prod.php'); //подгружаем конфиги  продакшина 
}


if(defined('HTTPS_ONLY')===true && constant('HTTPS_ONLY')===true && $_SERVER['HTTPS']==""){
    $url="https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $url");
    exit();
}

session_set_cookie_params(["lifetime"=>SESSION_LIFE]);
session_name('IZ_HASH');  //устанавливаем параметры сессии
session_start();


require_once("Engine/AUTOLOADER.php"); //подгружаем автозагрузчик



require_once('COMPRESSOR.php'); //подгружаем конфиги компрессора
Router::ifREAL();

require_once("Routes.php"); // подгружаем роуты

Router::Error(404);
}