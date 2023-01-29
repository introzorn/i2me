<?php

namespace App\Controllers;

use App\Lang;
//use App\Models\DB;
use App\View;
use App\Query;
use App\Router;
use App\Formater;
use App\Opengraph;
use App\Controllers\Auth;
use App\Models\gallery_group;

class Main
{

    function Index(Query $Request){
        $data=Lang::LOAD("index");
        $galls=(new gallery_group)->get();

        foreach ($galls as $key => $gall_group) {
            $data['gallaries'] .='<button id="gallary_group_'.$gall_group['id'].'" data-group-i="'.$gall_group['id'].'" title="'.$gall_group['description'].'">'.$gall_group['name'].'</button>'."\r\n";
        }
      
        View::Show("main",$data);
    }

}