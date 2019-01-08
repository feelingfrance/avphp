<?php
//token值的所有函数,session
//session 的有效期默认是1440秒,所以token的值为1440秒,
function setToken($token_name,$user,$pwd,$time) {//根据用户名,密码,时间生成md5值
    $_SESSION[$token_name] = md5("$user"."$pwd"."$time");

}

function validToken($token_name) {//根据token名字,返回token值是否和客户端传来的值一致
    //$return = ($_REQUEST[$token_name] === $_SESSION[$token_name] ? true : false);
	$return = ($_GET[$token_name] === $_SESSION[$token_name] ? true : false);
    //setToken();
    return $return;
}



?>