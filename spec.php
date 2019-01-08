<?php 
include 'var.php';
include 'token.php';
include 'getrealip.php';
include 'redis.php';
//***redis
if(!isvalid(get_real_ip(),$limit,$ttl)){//同一IP地址,ttl秒内,可以访问limit次
	exit("超出限制次数,请等待".$ttl."秒在登录");
}
//***redis 结束


$index = $addr.":$port/index.html";
if (!isset($_COOKIE["user"])) {//cookie 生命到期,需要重新登录,返回到登录界面
    header("location: $index");
}


$p_user = "";
$tmp = "tmp";//如果有此参数,说明下一个页面(get.php)有此页面来打开,此参数有sdf.php传递给get.php
if (isset($_GET['p_user'])) {
    $p_user = $_GET['p_user'];
}
if ($p_user == "") {
    header("location: $index");
}
if ($_COOKIE["user"] <> $p_user) {//cookie的值和p_user的值不一样就重新登录
    header("location: $index");
	

}

setcookie("user", "", time()-$alive);//先删除cookie
setcookie("user","$p_user",time()+$alive);//再定义cookie,保持cookie的有效
//检查服务器的token值和传递过来的token值是否一直
session_start();
$token_value = $_SESSION['token'];
if(validToken("token")){
	//echo "token值一致";
} else{
	header("location: $index");
	
}

?>


<head>  

<!--定时转到其他页面 -->  
<meta http-equiv="refresh" content="0;url=https://jemmi.online:4433/wordpress">   
</head> 

