<?php
namespace App;
use App\Router;
/**
 * Конфигурирование компрессова
 * 
 * Данная конструкция описывает все файлы которые будут сжаты компрессором
 * --------------------------
 * Пример
 * Router::COMPRESSOR([
 *      "view/css/style.css",
 *      "view/css/mystyle.css",
 *  ]);
 * 
 * @copyright IntroZorn (c) 2022, Хроленко П.А.
 */


 
Router::COMPRESSOR([

    "view/js/animhead.js",
    "view/js/script.js",
    "view/css/style.css",
    "view/css/mystyle.css",


 ]);











