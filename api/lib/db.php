<?php
/**
 * 链接数据库并返回数据库句柄
 */
try{
	$pdo = new PDO('mysql:host=localhost;dbname=restFul','root','damon');
	return $pdo;
}catch(Exception $e){
	echo $e->getMessage();
}

?>