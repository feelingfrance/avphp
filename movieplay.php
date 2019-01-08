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

// $movie_id = "";//传递过来的电影ID号
if (isset($_GET['movie_id'])) {
	$movie_id = $_GET['movie_id'];
}

$connection = mysqli_connect($host, $user, $pass) or die("Unable to connect!");
//选择一个数据库

mysqli_select_db($connection,$db) or die("Unable to select database!");
$movie_name = "";
$movie_intro = "";
$movie_sql = "select name,mintro from ".$movie_table." where mid = ".$movie_id;

$result = mysqli_query($connection,$movie_sql) or die("Error in query: $movie_sql . ".mysqli_error($connection));
if (mysqli_num_rows($result)>0){
	$tmp = mysqli_fetch_row($result);

	$movie_name = $tmp[0];
	$movie_intro = $tmp[1];

}
$movie_add_sql = "update users set movie='$movie_name' where name='$p_user'";//存储用户最后一次观看的视频
mysqli_query($connection,$movie_add_sql);

mysqli_free_result($result);
mysqli_close($connection);


echo <<< EOT
<html>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">


<!-- 禁用右键:  -->

<script> 
function stop(){ return false; } 
document.oncontextmenu=stop; 
</script> 
<!-- 禁用右键: 为止 -->
 
<body>
<style> 
.abc{ height:300px; border:0px solid #000; margin:0 auto} 
@media screen and (min-width: 1201px) { 
.abc {width: 720px} 
} 
/* 设置了浏览器宽度不小于1201px时 abc 显示720px宽度 */ 

.abc{ height:300px; border:0px solid #000; margin:0 auto} 
@media screen and (min-width: 650px) { 
.abc {width: 640px} 
} 
/* 设置了浏览器宽度不小于650px时 abc 显示640px宽度 */ 

@media screen and (max-width: 400px) { 
.abc {width: 360px;} 
} 
/* 设置了浏览器宽度不大于400px时 abc 显示360px宽度 */ 


</style>
<div class="abc">
<p align="center">


<!--<video src="play.php?movie_name=$movie_name" controls="controls" width="100%" muted="muted" />-->

<video src="$movie_name" controls="controls" width="100%" muted="muted">

your browser does not support the video tag
</video>

</p>
<p align="center">$movie_intro</p>
</div>
</body>
</html>


EOT;

?>