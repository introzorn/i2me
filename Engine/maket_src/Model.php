<?php

namespace App\Models;
use App;
use App\Model as M;
class _Helper_Class_Maket_ extends App\Model{
    // public $CHARSET='utf8mb4'; 
    // public $COLLATE='unicode_ci';

    public function MIGRATE() //миграции 
    {
    
        $this->TABLE = [
        'id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'name' => 'char(255) NOT NULL',
        'text' => 'text(1080) NOT NULL',
        'PRIMARY KEY'=>'id',
        'CHARSET'=>'utf8'];
    }


}





