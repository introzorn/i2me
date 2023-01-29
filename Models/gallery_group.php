<?php

namespace App\Models;
use App;
use App\Model as M;
class gallery_group extends App\Model{
    // public $CHARSET='utf8mb4'; 
    // public $COLLATE='unicode_ci';

    public function MIGRATE() //миграции 
    {
    
        $this->TABLE = [
        'id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'name' => 'char(255) NOT NULL',
        'description' => 'text(1080) NOT NULL',
        'PRIMARY KEY'=>'id',
        'CHARSET'=>'utf8'];
    }

    public function GetImagesG($group){
        $sql="SELECT gallery_group.name AS gname, gallery_group.description AS gdisc, gallery_image.id, gallery_image.img, gallery_image.name, gallery_image.description FROM gallery_group LEFT JOIN gallery_image ON gallery_group.id = gallery_image.group_id WHERE gallery_group.name='$group';";
        return $this->queryExec($sql);

    }

}





