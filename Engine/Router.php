<?php

/**
 * Класс Роутинга
 * 
 * В данном классе описывается парсинг URL адреса для дальнейшего перераспределения функционала приложения
 * 
 * @copyright IntroZorn (c) 2022, Хроленко П.А.
 */

namespace App;

use App\Controllers as C;
use App\Models;
use App\Query;

class Router
{
	private static $COMPRSTR;

  	/**
     * Парсер роутера
     * 
     * Этот приватный метод используется для парсинга URL адреса и возвращения параметров запроса в пользовательскую функцию
     *
     * @param string $method HTTP метод который отлавливаем [GET,POST,HEAD] ALL - игнорирует отлов
     * @param string $pattern патерн по которому сверяется URL
	 * @param mixed $callable пользовательская функция колбэк в случае если урл соответствует патерну
	 * @param boolean $domain указываем будет ли учитываться доменное имя
     * @return void метод не возвращает значений
     */
	private static function RealizeURL(String $method, String $pattern,  $callable, $domain = false)
	{

		if ($_SERVER['REQUEST_METHOD'] != strtoupper($method) && $_SERVER['REQUEST_METHOD'] != 'HEAD' && strtoupper($method) != "ALL") {
			return;
		}

		$pattern = trim($pattern);

		



		if ($pattern == "" ) {
			return;
		}

		$requestParts  = explode('?', $_SERVER['REQUEST_URI']);

		if($pattern=="/" && $pattern!=$requestParts[0]){return;}

		$keys["_request_string"] = "";




		if ($domain == true) {
			$requestParts[0] = $_SERVER['HTTP_HOST'] . $requestParts[0];
		}


		if (strpos($pattern, "[") || strpos($pattern, "']")) {
			$patternArray = explode("/", $pattern);

			$iterat = 1;

			$patternArray = array_map(function ($item) use (&$keys, &$iterat, &$nextItem) {

				if (preg_match("/(\[\'(.*?)'\])/", $item, $match)) {
					$part = explode("||", $match[2]);
					sizeof($part) == 0 ? $part[] = "" : false;
					trim($part[1]) == "" ? $part[1] = "_param" . $iterat++ : false;
					$keys[] = $part[1];
					$str = preg_quote(str_replace($match[0], "%param%", $item));
					return str_replace("%param%", $part[0], $str);
				} else if (preg_match("/(\[([^'][^\W]\w*?[^'])\])/", $item, $match)) {

					$keys[] = $match[2];
					$str = preg_quote(str_replace($match[0], "%param%", $item));
					return str_replace("%param%", "(.*)", $str);
				}


				return $item;
			}, $patternArray);


			$pattern = join('\\/', $patternArray);
		} else {

		$pattern = preg_quote($pattern,"/");

		
	
		}
	
		$pattern=trim($pattern);
		if ($pattern[0] . $pattern[1] == "\\*") {
			$pattern[0] = " ";
			$pattern[1] = " ";
		} else {
			$pattern = "^" . $pattern;
		}
		

		if ($pattern[strlen($pattern) - 1] . $pattern[strlen($pattern) - 2] == "\\*") {
			$pattern[strlen($pattern) - 1] = " ";
			$pattern[strlen($pattern) - 2] = " ";
		} else {
			$pattern .= "?";
		}
	
		$pattern = "/" . trim($pattern) . "/";
		
		if (preg_match($pattern, $requestParts[0], $matches)) {
			
		

			if(sizeof($keys)<sizeof($matches)){array_unshift($keys,"");}
			if(sizeof($keys)>sizeof($matches)){array_unshift($matches,"");}

			$reqPram = array_combine($keys, $matches);
		
			$_GET = array_merge($reqPram, $_GET);
			$Request = new Query($reqPram);
			if (gettype($callable) == "string") {
				if (strpos($callable, "::") > 0) {
					$fn = "App\\Controllers\\" . $callable;
					$fn($Request);
					die;
				}

				$clb = explode("->", $callable);
				$cls = "App\\Controllers\\" . $clb[0];
				$controller = new $cls;
				$fn = $clb[1];


				$controller->$fn($Request);
				die;
			}
			$callable($Request);
			die;
		}



		return;
	}


  	/**
     * Отловить url GET запроса
     * 
     * Этот метод позваляет отловить URL адрес GET Запроса. 
     *
     * @param string $pattern Патерн который может содержать в себе [name] - динамичесую переменную или ['(.*)||name']регулярное выражение а также знак * в начале и конце для указания нестрогости сравнивания
	 * @param mixed $callable пользовательская функция колбэк в случае если url соответствует патерну
     * @return void метод не возвращает значений
     */

	public static function get(string $pattern, $function)
	{
		self::RealizeURL("GET", $pattern, $function);
	}


	/**
     * Отловить url POST запроса
     * 
     * Этот метод позваляет отловить URL адрес POST Запроса. 
     *
     * @param string $pattern Патерн который может содержать в себе [name] - динамичесую переменную или ['(.*)||name']регулярное выражение а также знак * в начале и конце для указания нестрогости сравнивания
	 * @param mixed $callable пользовательская функция колбэк в случае если url соответствует патерну
     * @return void метод не возвращает значений
     */

	public static function post(string $pattern, $function)
	{
		self::RealizeURL("POST", $pattern, $function);
	}


	/**
     * Отловить url запроса
     * 
     * Этот метод позваляет отловить URL адрес GET/POST Запроса. Можно использовать для группировки роутов 
     *
     * @param string $pattern Патерн который может содержать в себе [name] - динамичесую переменную или ['(.*)||name']регулярное выражение а также знак * в начале и конце для указания нестрогости сравнивания
	 * @param mixed $callable пользовательская функция колбэк в случае если url соответствует патерну
     * @return void метод не возвращает значений
     */

	public static function group(string $pattern, $function)
	{
		self::RealizeURL("ALL", $pattern, $function);
	}


  	/**
     * Отловить url GET запроса
     * 
     * Этот метод позваляет отловить URL адрес GET Запроса. 
	 * Этот метод учитывает в работе также доменное имя
     *
     * @param string $pattern Патерн который может содержать в себе [name] - динамичесую переменную или ['(.*)||name']регулярное выражение а также знак * в начале и конце для указания нестрогости сравнивания
	 * @param mixed $callable пользовательская функция колбэк в случае если url соответствует патерну
     * @return void метод не возвращает значений
     */

	public static function dom_get(string $pattern, $function)
	{
		self::RealizeURL("GET", $pattern, $function, true);
	}



	/**
	 * 
	 * Отловить url POST запроса
	 * 
	 * Этот метод позваляет отловить URL адрес POST Запроса. 
	 * Этот метод учитывает в работе также доменное имя
	 *
	 * @param string $pattern Патерн который может содержать в себе [name] - динамичесую переменную или ['(.*)||name']регулярное выражение а также знак * в начале и конце для указания нестрогости сравнивания
	 * @param mixed $callable пользовательская функция колбэк в случае если url соответствует патерну
	 * @return void метод не возвращает значений
	 */

	public static function dom_post(string $pattern, $function)
	{
		self::RealizeURL("POST", $pattern, $function, true);
	}


	/**
     * Отловить url запроса
     * 
     * Этот метод позваляет отловить URL адрес GET/POST Запроса. Можно использовать для группировки роутов.
	 * Этот метод учитывает в работе также доменное имя.
     *
     * @param string $pattern Патерн который может содержать в себе [name] - динамичесую переменную или ['(.*)||name']регулярное выражение а также знак * в начале и конце для указания нестрогости сравнивания
	 * @param mixed $callable пользовательская функция колбэк в случае если url соответствует патерну
     * @return void метод не возвращает значений
     */

	public static function dom_group(string $pattern, $function)
	{
		self::RealizeURL("ALL", $pattern, $function);
	}






	/**
     * Чекер реальных обьектов
     * 
     * Этот метод выдает файл клииенту, если он существует физически
	 * 
     *
     */

	public static function ifREAL()
	{

		$base = explode('/', $_SERVER['SCRIPT_NAME']);
		$base[count($base) - 1] = "";
		$basepath = join("/", $base);


		$ReqA  = explode('?', $_SERVER['REQUEST_URI']);
		$ReqA[0] = str_replace($basepath, '/', $ReqA[0]);
		$Furl = 'View' . $ReqA[0];
		$fta = explode('.', $Furl);

		$Ftype = strtolower($fta[sizeof($fta) - 1]);

		if (file_exists($Furl) && is_file($Furl)) {
			if ($Ftype == 'php') {
				require_once($Furl);
				die();
			} else {

				header('accept-ranges: bytes');
				header('Content-Description: File Transfer');
				//header('Content-Type: ' . mime_content_type($Furl));
				header('Content-Type: ' . MIME::GetMIME('.' . $Ftype));
				//header('Content-Disposition: attachment; filename=' . basename($Furl));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($Furl));
				if (self::if_COMP($Furl)) {

					$minfile = self::DO_COMPRESS($Furl);
					header('Content-Length: ' . strlen($minfile));
					echo ($minfile);
					die;
				}
				readfile($Furl);
				die();
			}
		}
	}







	public static function COMPRESSOR($array)
	{
		self::$COMPRSTR = $array;
	}


	private static function if_COMP($file)
	{
		return in_array($file, self::$COMPRSTR);
	}


	public static function DO_COMPRESS($file)
	{
		$buf = file_get_contents($file);
		$fta = explode('.', $file);
		$Ftype = strtolower($fta[sizeof($fta) - 1]);

		if ($Ftype == 'css') {
			return self::compress_css($buf);
		}
		if ($Ftype == 'js') {
			return self::compress_js($buf);
		}
		return $buf;
	}


	private static  function compress_css($buffer)
	{

		$buffer = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $buffer);
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", "  ", "    ", "    "), "", $buffer);
		return $buffer;
	}


	private  static   function compress_js($buffer)
	{
		$buffer = preg_replace("/\/\/[^\n]*/", "", $buffer);
		$buffer = preg_replace("/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/", "", $buffer);
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", "  ", "    ", "    "), "", $buffer);

		return $buffer;
	}






	/**
     * Производит редирект на указанный url
     *
     * @param string $url - Адрес перенаправления, может быть как и точным так и относительным
     * @return void 
     */

	public static function Redirect($url)
	{

		if (!strpos('http://', $url) || !strpos('https://', $url)) {
			$url = self::GetBaseUrl() . $url;
		}

		header('location: ' . $url);
		die();
	}




	/**
     * Метод возвращает базовый URL веб приложения
     *
     * @return string базовый URL веб приложения
     */

	public static function GetBaseUrl()
	{

		$base = explode('/', $_SERVER['SCRIPT_NAME']);
		$base[count($base) - 1] = "";

		$baseURL = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . '/'; 
	
		return $baseURL;
	}



	/**
	 * Ответ в JSON
	 * 
     * Метод превращает обьект в JSON и выдает его пользователю
     *
	 * @param object $jsonDATA - JSON обьект
	 * @param int $flag -флаги jsonencode
     * @return void 
     */

	public static function JSON_Response($jsonDATA, $flag = 0)
	{

		$resp = json_encode($jsonDATA, $flag);
		if (!$resp) {
			$resp = '{error:"bad json"}';
		}
		header('Content-type: application/json');
		die($resp);
	}

	/**
	 * Ответ в text/plain
	 * 
     * Метод просто возвращает текст пользователю
     *
	 * @param string $TextDATA - Текст который вернется пользователю
     * @return void 
     */

	public static function TEXT_Response($TextDATA)
	{
		header('Content-type: text/plain');
		die($TextDATA);
	}





	/**
	 * Ответ сервера
	 * 
     * Метод генерирует ответ сервера
     *
	 * @param int $status - номер ответ
	 * @param string $data - Этот текст будет показан в браузере. чтобы не выводить в брацзер $data выставлаем как null
     * @return array в случае если $headonly = true. устанавливается только заголовок и метод возвращает массив из кода и текста статуса 
     */

	public static function HTTP_Status(int $status, $data="",$headOnly=false)
	{

		
		$stat[100] = 'Continue';
		$stat[101] = 'Switching Protocols';
		$stat[200] = 'OK';
		$stat[201] = 'Created';
		$stat[202] = 'Accepted';
		$stat[203] = 'Non-Authoritative Information';
		$stat[204] = 'No Content';
		$stat[205] = 'Reset Content';
		$stat[206] = 'Partial Content';
		$stat[300] = 'Multiple Choices';
		$stat[301] = 'Moved Permanently';
		$stat[302] = 'Moved Temporarily';
		$stat[303] = 'See Other';
		$stat[304] = 'Not Modified';
		$stat[305] = 'Use Proxy';
		$stat[400] = 'Bad Request';
		$stat[401] = 'Unauthorized';
		$stat[402] = 'Payment Required';
		$stat[403] = 'Forbidden';
		$stat[404] = 'Not Found';
		$stat[405] = 'Method Not Allowed';
		$stat[406] = 'Not Acceptable';
		$stat[407] = 'Proxy Authentication Required';
		$stat[408] = 'Request Time-out';
		$stat[409] = 'Conflict';
		$stat[410] = 'Gone';
		$stat[411] = 'Length Required';
		$stat[412] = 'Precondition Failed';
		$stat[413] = 'Request Entity Too Large';
		$stat[414] = 'Request-URI Too Large';
		$stat[415] = 'Unsupported Media Type';
		$stat[500] = 'Internal Server Error';
		$stat[501] = 'Not Implemented';
		$stat[502] = 'Bad Gateway';
		$stat[503] = 'Service Unavailable';
		$stat[504] = 'Gateway Time-out';
		$stat[505] = 'HTTP Version not supported';

		if(!isset($stat[$status])){$status=200;}

		header("HTTP/1.1 $status $stat[$status]");

		if($headOnly){return [$status , $stat[$status]];}

		if(is_null($data)){die;}
		$data==""?$data=$status.'-'.$stat[$status]:false;
		die("<h1>$data</h1>");
	}

	/**
	 * Генератор ошибок
	 * 
     * Выдает клиенту ошибку в виде текста или страницу
     *
	 * @param int $num - номер ошибки
	 * @param string $view - страница вью. если параметр не задан то генерируется только header; Если $view="" тогда вызывается стандартная страница ошибок; если $view="text" выдается текст ошибки
     * @return void
     */

	public static function Error(int $num,$view=null){
		self::HTTP_Status($num,"",true);
		if(is_null($view)){die;}
		$status=Lang::LOAD("http_status");
		$text=$status[$num]??$status[0];
		if($view==""){$view="ErrorPage/HTTP_ERROR";}
		if($view=="text"){die("<h1>$num - $text</h1>");}
		View::Show($view,['error'=>$num,'error_text'=>$text]);
	}



}
