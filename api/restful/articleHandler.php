<?php
/**
* @author Damon <1157581951@qq.com>
*/
require __DIR__.'/../model/Article.php';
require_once __DIR__.'/handler.php';
require_once __DIR__.'/../model/User.php';
class ArticleHandler extends Handler
{
	/**
	 * Article
	 * @var [type]
	 */
	private $_article;
	/**
	 * User
	 * @var [type]
	 */
	private $_user;
	
	function __construct(Article $article,User $user)
	{
		$this->_article = $article;
		$this->_user = $user;
	}
	/**
	 * 综合处理所有的Article请求
	 * @Author     Damon
	 * @CreateTime 2017-04-05T10:28:16+0800
	 * @param      String                   $method 请求方法
	 * @param      String                   $path   请求的资源名
	 * @param      String                   $id     请求ID
	 */
	public function handle($method,$path,$id){
		switch ($method) {
			case 'GET':
				if (empty($id)) {
					return $this->_showArticleList();
				}else{
					return $this->_showArticleDetail();
				}
				break;
			case 'POST':
				return $this->_createArticle();
				break;
			case 'PUT':
				return $this->_updateArticle();
				break;
			case 'DELETE':
				return $this->_deleteArticle();
				break;
			default:
				break;
		}
	}
	/**
	 * 显示文章列表 /articles/ GET
	 * @Author     Damon
	 * @CreateTime 2017-04-05T10:30:08+0800
	 * @return     array                   文章列表
	 */
	private function _showArticleList(){
		//return $this->_article->
	}
	/**
	 * 文章详情 /articles/ID GET
	 * @Author     Damon
	 * @CreateTime 2017-04-05T10:31:05+0800
	 * @return     Article                   文章实体
	 */
	private function _showArticleDetail(){

	}
	/**
	 * 创建文章 /articles/ POST
	 * @Author     Damon
	 * @CreateTime 2017-04-05T10:32:42+0800
	 * @return     [type]                   [description]
	 */
	private function _createArticle(){
		$body = $this->_getBodyParam();
		if (empty($body['title'])) {
			throw new Exception("文章标题不能为空", 400);
		}
		if (empty($body['content'])) {
			throw new Exception('文章内容不能为空',400);
		}
		//开始登陆
		$user = $this->_userLogin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
		//创建文章异常捕获
		try {
			$article = $this->_article->create($body['title'],$body['content'],$user['id']);
		} catch (Exception $e) {
			if (in_array($e->getCode(), [
					ErrorCode::ARTICLE_TITLE_NOT_EMPTY,
					ErrorCode::ARTICLE_CONTENT_NOT_EMPTY,
					ErrorCode::ARTICLE_AUTHOR_INVILIDE
				])) {
				throw new Exception($e->getMessage(), 400);	
			}else{
				throw new Exception($e->getMessage(), 500);
			}
		}
		return $article;
	}
	/**
	 * 更新文章 /articles/ID PUT
	 * @Author     Damon
	 * @CreateTime 2017-04-05T10:33:55+0800
	 * @return     [type]                   [description]
	 */
	private function _updateArticle(){

	}
	/**
	 * 删除文章 /articles/ID DELETE  
	 * @Author     Damon
	 * @CreateTime 2017-04-05T10:35:52+0800
	 * @return     [type]                   [description]
	 */
	private function _deleteArticle(){

	}

	private function _userLogin($PHP_AUTH_USER,$PHP_AUTH_PW){
		try {
			return $this->_user->login($PHP_AUTH_USER,$PHP_AUTH_PW);
		} catch (Exception $e) {
			if (in_array($e->getCode(), 
					[ErrorCode::USERNAME_CANNOT_EMPTY,ErrorCode::PASSWORD_CANNOT_EMPTY,ErrorCode::USERNAME_OR_PASSWORD_INVALID]
				)) {
				throw new Exception($e->getMessage(), 400);
				
			}else{
				throw new Exception($e->getMessage(),500);
			}
		}
	}

}
?>