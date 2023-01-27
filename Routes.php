<?php

/**
 * Роуты приложения
 * 
 * Данная конструкция описывает все роуты которые будут отлавливаться и обробатываться движком
 *
 * Пример
 * Router::get("/page/[id]/config/bbb*",function(Query $Request){........}); // отлов диномичного параметра $id
 * Router::get("/",function(Query $Request){........});
 * Router::get("/main",function(Query $Request){........});
 * 
 * @copyright IntroZorn (c) 2022, Хроленко П.А.
 */

namespace App {

    use App\Autoloader;
    use App\Router;
    use App\Query;
    use App\View;
    use App\Controllers as C;
    use App\Models;
    use App;




    /**
     * Главная страница
     */
    Router::get("/", "Main->Index");


    /**
     * Заглушка для страницы 404 со вьюшкой
     */
    Router::get("*/*", function (Query $Request) {
        Router::group("*['\....']", function (Query $Request) {
            Router::Error(404);
        });
       
   
        Router::Error(404, "");
    });


    /**
     * Памятка роутов
     * 
     * Router::get("/[key0]/sdfsdaf/[key1]/['(.*)||name']", "Main->Index");
     * 
     * 
     * Router::get("/[key0]/sdfsdaf/[key1]/['(.*)']", "Main::Index");
     * 
     * 
     * 
     * домены
     * Router::dom_get("*.co/", function (Query $Request) {});
     * 
     * 
     * 
     * 
     * 
     * 
     */
}
