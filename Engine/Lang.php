<?php

namespace App;

Class Lang
{
    static function LOAD($name, $lang="ru"){
        include('Lang/'.$lang.'/'.$name.'.php');
        return($lng_data);
    }


}