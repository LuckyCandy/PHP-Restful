<?php
/**
* @author Damon <1157581951@qq.com>
*/
require __DIR__.'/userHandler.php';
require __DIR__.'/articleHandler.php';
$pdo = require __DIR__.'/../lib/db.php';
class Restful
{
	/**
	 * User Handler
	 * @var UserHandler
	 */
	private $_userHandler;
	/**
	 * Article Handler
	 * @var ArticleHandler
	 */
	private $_articleHandler;
	/**
	 * 请求方法
	 * @var [String]
	 */
	private $_requestMethod;
	/**
	 * 请求的资源名称
	 * @var [String]
	 */
	private $_resourceName;
	/**
	 * 请求的资源ID，可以没有
	 * @var [String]
	 */
	private $_id;
	/**
	 * 可以请求的资源
	 * @var array
	 */
	private $_allowResource = ['users','articles'];
	/**
	 * 允许请求使用的方法
	 * @var array
	 */
	private $_allowMethods = ['GET','POST','DELETE','PUT','OPTIONS'];
	/**
	 * 常用响应状态码定义
	 * @var array
	 */
	private $_statusCode = [
		200 => 'ok',
		204 => 'No Content',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		500 => 'Srever Internal Error',
	];
	
	function __construct(UserHandler $userHandler,ArticleHandler $articleHandler)
	{
		$this->_userHandler = $userHandler;
		$this->_articleHandler = $articleHandler;
		$this->start();
	}
	/**
	 * 初始化配置方法
	 * @Author Damon
	 * @DateTime  2017-04-04T17:27:54+0800
	 * @return void        
	 */
	function start(){
		try {
			$this->_setup();
			switch ($this->_resourceName) {
				case 'users':
					return $this->_json($this->_userHandler->registerHandler($this->_requestMethod));
					break;
				case 'articles':
					return $this->_json($this->_articleHandler->handle($this->_requestMethod,$this->_resourceName,$this->_id));
					break;
				default:
					break;
			}
		} catch (Exception $e) {
			$this->_json(['errorCode'=>$e->getCode(),'errorMsg'=>$e->getMessage()],$e->getCode());
		}
		
	}
	/**
	 * 初始化方法
	 * @Author Damon
	 * @DateTime  2017-04-04T17:29:37+0800
	 * @return    Void
	 */
	private function _setup(){
		/**
		 * Http方法赋值以及判断
		 */
		$this->_requestMethod = $_SERVER['REQUEST_METHOD'];
		if (!in_array($this->_requestMethod, $this->_allowMethods)) {
			throw new Exception("请求方法不被允许", 405);
		}
		/**
		 * 设置_resourceName以及资源是否允许访问
		 */
		$pathArr = explode('/', $_SERVER['PATH_INFO']);
		$this->_resourceName = $pathArr[1];
		if (!empty($pathArr[2])) {
			$this->_id = $pathArr[2];
		}
		if (!in_array($this->_resourceName, $this->_allowResource)) {
			throw new Exception("请求资源不被允许", 400);
		}
	}
	/**
	 * 将数组内容以json的格式输出
	 * @Author Damon
	 * @DateTime  2017-04-04T17:45:00+0800
	 * @param     array                  
	 */
	private function _json($array,$code = 0){
		if ($code > 0 && $code != 200 && $code != 204) {
			header("HTTP/1.1 ".$code." ".$_statusCode[$code]);
		}
		header('Content-Type:application/json;charset=utf-8');
		echo json_encode($array,JSON_UNESCAPED_UNICODE);
		exit();
	}
}
$user = new User($pdo);
$article = new Article($pdo);
$articleHandler = new ArticleHandler($article,$user);
$userHandler = new UserHandler($user);
$restful = new Restful($userHandler,$articleHandler);
?>