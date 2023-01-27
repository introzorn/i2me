<?php
namespace App;
//класс query
 class Query {

	public $ReqParam;
	public $GetParam;
	Public $PostParam;
	public $FullURL;
	Public $domain;
	public $protocol;
	public $cookie;
	public $ReqURL;
	public $method;
	Public $header;
	Public $session;
	public $ContainerData;
	public $BASE_URL;
	public $IsAjax=false;
	public function  __construct($rParam)
	{
		$this->ReqParam = $rParam;
		$this->PostParam=$_POST;
		$this->GetParam=$_GET;
		$this->FullURL =  ($_SERVER['HTTPS'] ?? 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$this->domain =  $_SERVER['HTTP_HOST'];
		$this->protocol=  $_SERVER['HTTPS'] ?? 'http';
		// $this->cookie="";
		$this->ReqURL=$_SERVER['REQUEST_URI'];
		$this->method=$_SERVER['REQUEST_METHOD'] ;
		$this->header=getallheaders();
		$this->BASE_URL=Router::GetBaseUrl();
		$this->BASE_URL2=Router::GetBaseUrl();
		$this->IsAjax=self::ifAjax();
		//$this->session;
		
	}
	private static function ifAjax(){
		$header=getallheaders();
		
		if($header==false){return false;}
		 foreach ($header as $key => $value) {
			if(strtolower($key)=='x-requested-with' && strtolower($value)==strtolower('XMLHttpRequest')){return true;}
		 }
	
		 return false;
	}
	public static function JSON_Response($jsonDATA){
	
		$resp=json_encode($jsonDATA);
		if(!$resp){$resp='{error:"bad json"}';}
		header('Content-type: application/json');
		die($resp);
	}



}
class COOK{


}

class HEAD{


}


?>