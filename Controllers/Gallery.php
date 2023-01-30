<?php

namespace App\Controllers;

use App\Controllers\Auth;
//use App\Models\DB;
use App\Query;
use App\View;
use App\Lang;
use App\Formater;
use App\Models\gallery_group;
use App\Router;
use App\Opengraph;


class Gallery
{

    function Index(Query $Request){
      //  if($Request->IsAjax===false){return;}
        $group=$Request->ReqParam['group'];
        $gallaryGroup=New gallery_group;
        $images=$gallaryGroup->GetImagesG($group);

        $resp["success"]=true;
        $resp["images"]=$images;

        Router::JSON_Response($resp);
    }

}