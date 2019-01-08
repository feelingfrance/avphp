<?php
include 'var.php';
include 'token.php';
//$port = 8088;
//$addr = "http://jemmi.gicp.net";
//$addr = "http://192.168.1.114";
//$index = $addr.":$port/index.php";
$index = $addr.":$port/index.html";
if (!isset($_COOKIE["user"])) {//cookie 生命到期,需要重新登录,返回到登录界面
    header("location: $index");

}
session_start();
if(validToken("token")){
	//echo "token值一致";
} else{
	header("location: $index");
	
}

$p_user = "";
if (isset($_GET['p_user'])) {
    $p_user = $_GET['p_user'];
}
if ($_COOKIE["user"] <> $p_user) {//cookie的值和p_user的值不一样就重新登录
    header("location: $index");
}
setcookie("user", "", time()-$alive);//先删除cookie
setcookie("user","$p_user",time()+$alive);//再定义cookie,保持cookie的有效



$item = "";
$item = $_GET['item']."/*";
//echo $item;
foreach(glob("$item") as $f) {


    echo "<p style=text-align:center><img src='$f' /></p>";


}



?>