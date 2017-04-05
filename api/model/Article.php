<?php
/**
* @author Damon <1157581951@qq.com>
*/
require_once __DIR__.'/../lib/errorCode.php';
class Article
{
	/**
	 * 数据库链接句柄
	 * @var [type]
	 */
	private $_db;
	/**
	 * 构造函数，赋值数据库句柄
	 * @Author Damon
	 * @DateTime  2017-04-04T21:26:13+0800
	 * @param     [type]                   $db [description]
	 */
	function __construct($db)
	{
		$this->_db = $db;
	}
	/**
	 * 创建新文章
	 * @Author     Damon
	 * @CreateTime 2017-04-04T21:41:00+0800
	 * @param      String                   $title   文章标题
	 * @param      String                   $content 文章内容
	 * @return     Article                            文章实体
	 */
	public function create($title,$content,$uid){
		if (empty($title)) {
			throw new Exception("文章标题不能为空", ErrorCode::ARTICLE_TITLE_NOT_EMPTY);
		}
		if (empty($content)) {
			throw new Exception("文章内容不能为空", ErrorCode::ARTICLE_CONTENT_NOT_EMPTY);
		}
		if (empty($uid) || !$this->_isUserExist($uid)) {
			throw new Exception("文章作者无效", ErrorCode::ARTICLE_AUTHOR_INVILIDE);
		}
		$sql = 'INSERT `articles`(`title`,`content`,`userid`) VALUES (:title,:content,:userid)';
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':title',$title);
		$stmt->bindParam(':content',$content);
		$stmt->bindParam(':userid',$uid);
		if(!$stmt->execute()){
			throw new Exception("创建文章失败", ErrorCode::CREATE_ARTICLE_FAIL);
		}
		return [
			'id' => $this->_db->lastInsertId(),
			'title' => $title,
			'content' => $content
		];
	}
	/**
	 * 编辑文章
	 * @Author     Damon
	 * @CreateTime 2017-04-04T21:51:49+0800
	 * @param      String                   $id      文章ID
	 * @param      String                   $title   文章标题
	 * @param      String                   $content 文章内容
	 * @return     Article                            文章实体
	 */
	public function update($id,$title,$content,$uid){
		if (!$this->_isArticleExists($id)) {
			throw new Exception("编辑的文章不存在", ErrorCode::ARTICLE_NOT_EXISTS);
		}
		if (empty($title)) {
			throw new Exception("文章标题不能为空", ErrorCode::ARTICLE_TITLE_NOT_EMPTY);
		}
		if (empty($content)) {
			throw new Exception("文章内容不能为空", ErrorCode::ARTICLE_CONTENT_NOT_EMPTY);
		}
		if ($this->_getUserByAId($id) != $uid) {
			throw new Exception("您没有权限修改", ErrorCode::AUTHORIZED_DENY);
		}
		$sql = 'UPDATE `articles` SET `title` = :title, `content` = :content WHERE `id` = :id';
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':id',$id);
		$stmt->bindParam(':title',$title);
		$stmt->bindParam(':content',$content);
		if(!$stmt->execute()){
			throw new Exception("文章更新失败", ErrorCode::ARTICLE_UPDATE_FAIL);
		}
		return [
			'id' => $id,
			'title' => $title,
			'content' => $content,
			'userid' => $uid,
		];
	}
	/**
	 * 删除文章
	 * @Author     Damon
	 * @CreateTime 2017-04-04T22:28:56+0800
	 * @param      String                   $id  文章ID
	 * @param      String                   $uid 用户ID
	 */
	public function delete($id,$uid){
		if (!$this->_isArticleExists($id)) {
			throw new Exception("要删除的文章不存在", ErrorCode::ARTICLE_NOT_EXISTS);
		}
		if ($this->_getUserByAId($id) != $uid) {
			throw new Exception("您没有权限删除", ErrorCode::AUTHORIZED_DENY);
		}

		$sql = 'DELETE FROM `articles` WHERE `id` = :id';
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':id',$id);
		if (!$stmt->execute()) {
			throw new Exception("删除文章失败", ErrorCode::ARTICLE_DELETE_FAIL);
		}
	}
	/**
	 * 判断文章是否已经存在
	 * @Author     Damon
	 * @CreateTime 2017-04-04T21:43:47+0800
	 * @param      String                   $id 文章ID
	 * @return     boolean                      是否存在此文章
	 */
	private function _isArticleExists($id){
		$sql = 'SELECT * FROM `articles` WHERE id =:id';
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':id',$id);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return !empty($result);
	}
	/**
	 * 检测用户用是否存在
	 * @Author Damon
	 * @DateTime  2017-04-04T19:02:18+0800
	 * @param     String                   $username 用户名
	 * @return    BOOL                           
	 */
	private function _isUserExist($uid){
		//$res = false;
		$sql = 'SELECT * FROM `users` WHERE id =:uid';
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':uid',$uid);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return !empty($result);
	}
	/**
	 * 根据文章ID获取文章作者ID
	 * @Author     Damon
	 * @CreateTime 2017-04-04T22:15:25+0800
	 * @param      String                   $aid 文章ID
	 * @return     String                        作者ID
	 */
	private function _getUserByAId($aid){
		$sql = 'SELECT `userid` FROM `articles` WHERE id =:aid';
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':aid',$aid);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!empty($result)) {
			return $result['userid'];
		}else{
			return '';
		}
		//return !empty($result);
	}
}
?>