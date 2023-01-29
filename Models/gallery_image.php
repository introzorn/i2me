<?php

namespace App\Models;
use App;
use App\Model as M;
class gallery_image extends App\Model{
    // public $CHARSET='utf8mb4'; 
    // public $COLLATE='unicode_ci';

    public function MIGRATE() //миграции 
    {
    
        $this->TABLE = [
        'id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'group_id' => 'int(11) NOT NULL',
        'img' => 'char(255) NOT NULL',
        'name' => 'char(255) NOT NULL',
        'description' => 'text(1080) NOT NULL',
        'PRIMARY KEY'=>'id',
        'FOREIGN KEY'=>'group_id',
        'REFERENCES'=>'gallery_group(id)',
        'CHARSET'=>'utf8'];
    }


}





