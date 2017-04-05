<?php
/**
* @author Damon <1157581951@qq.com>
*/
require __DIR__.'/../model/User.php';
require_once __DIR__.'/handler.php';
class UserHandler extends Handler
{
	private $_user;
	
	function __construct(User $user)
	{
		$this->_user = $user;
	}

	public function registerHandler($methodType){
		if ($methodType != 'POST') {
			throw new Exception("请求资源不被允许", 405);
		}
		$body = $this->_getBodyParam();
		if (empty($body['username'])) {
			throw new Exception("用户名不能为空", 400);
		}

		if (empty($body['password'])) {
			throw new Exception("密码不能为空", 400);
		}
		return $this->_user->register($body['username'],$body['password']);
	}
}
?>