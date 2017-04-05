<?php

/**
* 
*/
class ErrorCode
{
	/*
	User
	 */
	const USERNAME_EXISTS = 1;
	const PASSWORD_CANNOT_EMPTY = 2;
	const USERNAME_CANNOT_EMPTY = 3;
	const REGISTER_FAILED = 4;
	const USERNAME_OR_PASSWORD_INVALID = 5;
	/*
	Article
	 */
	const ARTICLE_TITLE_NOT_EMPTY = 11;
	const ARTICLE_CONTENT_NOT_EMPTY = 12;
	const CREATE_ARTICLE_FAIL = 13;
	const ARTICLE_NOT_EXISTS = 14;
	const ARTICLE_UPDATE_FAIL = 15;
	const ARTICLE_DELETE_FAIL = 16;
	const ARTICLE_AUTHOR_INVILIDE = 17;
	const AUTHORIZED_DENY = 18;
}

?>