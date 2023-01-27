<?php

spl_autoload_register(function ($class_name) {
	if(strpos($class_name,"App\\")===0){

		$path="Engine\\";
		if(strpos($class_name,"App\\Controllers")===0){$path="";}
		if(strpos($class_name,"App\\Models")===0){$path="";}
		
		$class_name= str_replace("\\","/",str_replace("App\\",$path,$class_name));


	}	
    require_once $class_name . '.php';
});

