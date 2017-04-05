<?php
/**
* @author Damon <1157581951@qq.com>
*/
class Handler
{
	public function _getBodyParam(){
		$rawdata = file_get_contents('php://input');
		if (empty($rawdata)) {
			throw new Exception("请求失败", 400);
		}
		return json_decode($rawdata,true);
	}
}
?>