<?php
namespace App\Fabrics;
use App\Models\gallery_group;
use App\Models\gallery_image;

class gallery_fabric 
{
    static function go(){
        $grp=New gallery_group;
        $imgs=new gallery_image;

        foreach (glob("View/img/gallery/*",GLOB_ONLYDIR) as $dir) {
            $dirname=basename($dir);
           $grp_id= $grp->add(['name'=>$dirname,'description'=>'описание ']);
            foreach (glob("View/img/gallery/$dirname/*.[jJ][pP][gG]",) as $filename) {

                $imgs->add(['group_id'=>$grp_id,'img'=>basename($filename),'name'=>pathinfo($filename, PATHINFO_FILENAME),'description'=>'описание изображение']);
                echo "$filename размер " . filesize($filename) . "\n";
            }


        }

    }
}
