<?php
require __DIR__.'/model/Article.php';
$pdo = require __DIR__.'/lib/db.php';

$art = new Article($pdo);
//print_r($art->create('Hello','Hello World',8))
//print_r($art->update(3,'天气不错1','我想去玩耍去1',8));
//print_r($user->register('admin','admin'));
print_r($art->delete(3,8));
?>