
<?php

include 'var.php';
include 'token.php';
include 'redis.php';
include 'getrealip.php';

//$port = 8088;
//$addr = "http://jemmi.gicp.net";
//$addr = "http://192.168.1.114";
if(!isvalid(get_real_ip()."login",$limit,$ttl)){//同一IP地址+,60秒内,可以访问5次,加入login字符串,用于标记此确认是登录页面的
	exit('超出限制次数,请等待60秒再登录登录');
}
setcookie("user", "", time()-$alive);//先删除cookie
$index = $addr.":$port/index.html";


$webaddr = $addr.":$port/sdf.php";

$user1="";
$pwd1="";
if (isset($_POST['user'])) {
    $user1=$_POST['user'];
}
if (isset($_POST['pwd'])) {
    $pwd1=$_POST['pwd'];
}


// $host = "localhost";
// $user = "root";
// $pass = "123456";
// $db = "mydb";
//创建一个mysql连接
$connection = mysqli_connect($host, $user, $pass) or die("Unable to connect!");
//选择一个数据库
mysqli_select_db($connection,$db) or die("Unable to select database!");
$sql = "select * from users where name = '$user1' and pwd = '$pwd1'";

$result = mysqli_query($connection,$sql) or die(mysqli_error($connection));
$time = time();
$time_sql = "update users set code='$time' where name='$user1'";




if (mysqli_num_rows($result) <> 0) {
    setcookie("user","$user1",time()+$alive);//登录成功后,设置cookie,生命为n秒,其他页面要查询cookie是否成活
	//密码正确,设置token值
	session_start();
	if(!isset($_SESSION['token']) || $_SESSION['token'] == '') {
		setToken("token","$user1","$pwd1",microtime());
	}
	
	$token_value = $_SESSION['token'];
	//token值通过参数传递给服务器
	
    mysqli_query($connection,$time_sql) or die(mysqli_error($connection));
    header("location: $webaddr"."?p_user=$user1&token=$token_value");//token值传递到服务器
} else {
    //echo "密码错误";
    echo "<script> alert('密码错误'); </script>";
    echo "<meta http-equiv='Refresh' content='0;URL=$index'>";
    //echo "<script>alert('密码错误');</script>";
    //header("refresh:{1};url={$index}");
    //header("location: $index");
}
mysqli_free_result($result);	
mysqli_close($connection);
?>

