<?php
/**
* @author Damon <1157581951@qq.com>
*/
require_once __DIR__.'/../lib/errorCode.php';
class User
{
	/**
	 * 数据库链接句柄
	 * @var [type]
	 */
	private $_db;
	function __construct($db)
	{
		$this->_db = $db;
	}
	/**
	 * 用户登录逻辑实现
	 * @Author Damon
	 * @DateTime  2017-04-04T18:29:18+0800
	 * @param     String                   $username 用户名
	 * @param     String                   $password 密码
	 */
	public function login($username,$password){
		if (empty($password)) {
			throw new Exception("用户名不能为空", ErrorCode::USERNAME_CANNOT_EMPTY);
		}

		if (empty($password)) {
			throw new Exception("密码不能为空", ErrorCode::PASSWORD_CANNOT_EMPTY);
		}

		$sql = 'SELECT * FROM `users` WHERE `name` = :username AND `password` = :password';
		$stmt = $this->_db->prepare($sql);
		$password = $this->_md5($password);
		$stmt->bindParam(':username',$username);
		$stmt->bindParam(':password',$password);
		$stmt->execute();

		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if (empty($result)) {
			throw new Exception("用户名或密码错误", ErrorCode::USERNAME_OR_PASSWORD_INVALID);
		}
		unset($result['password']);
		return $result;
	}
	/**
	 * 用户注册逻辑实现
	 * @Author Damon
	 * @DateTime  2017-04-04T18:29:18+0800
	 * @param     String                   $username 用户名
	 * @param     String                   $password 密码
	 */
	public function register($username,$password){
		if (empty($password)) {
			throw new Exception("用户名不能为空", ErrorCode::USERNAME_CANNOT_EMPTY);
		}
		if ($this->_isuserNameExist($username)) {
			throw new Exception("用户名已经存在", ErrorCode::USERNAME_EXISTS);
		}
		if (empty($password)) {
			throw new Exception("密码不能为空", ErrorCode::PASSWORD_CANNOT_EMPTY);
		}
		//写入数据库
		$sql = 'INSERT INTO `users`(`name`,`password`,`create_at`) VALUES (:username,:password,:createAt)';
		$stmt = $this->_db->prepare($sql);
		$createAt = time();
		$md5_password = $this->_md5($password);
		$stmt->bindParam(':username',$username);
		$stmt->bindParam(':password',$md5_password);
		$stmt->bindParam(':createAt',$createAt);

		if (!$stmt->execute()) {
			throw new Exception("注册失败", ErrorCode::REGISTER_FAILED);
		}

		return [
			'id' => $this->_db->lastInsertId(),
			'name' => $username,
			'create_at' => $createAt
		];
	}
	/**
	 * 检测用户用是否存在
	 * @Author Damon
	 * @DateTime  2017-04-04T19:02:18+0800
	 * @param     String                   $username 用户名
	 * @return    BOOL                           
	 */
	private function _isuserNameExist($username){
		//$res = false;
		$sql = 'SELECT * FROM `users` WHERE name =:username';
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':username',$username);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return !empty($result);
	}

	private function _md5($str,$key = 'damon'){
		return md5($str.$key);
	}
}
?>