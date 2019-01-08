

<?php
include 'var.php';
include 'token.php';
include 'getrealip.php';
include 'redis.php';
//$port = 8088;
//$addr = "http://jemmi.gicp.net";
//$addr = "http://192.168.1.114";
//$index = $addr.":$port/index.php";

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
//
$spec_addr = spec_page($p_user,$token_value);//特殊网页的地址加用户名和token值


echo <<< EOT
<html><body>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
 </head>
EOT;
if($spec_addr <> "") echo "<p style=text-align:center><a href=$spec_addr>福利链接</a></p>";
echo <<< EOT
<form method='post' action='get.php?p_user=$p_user&tmp=$tmp&token=$token_value'>
<p style=text-align:center><input type='text' placeholder="番号,演员或主题" name='yourcontent'>
<input type='submit' name='submit' value='AV GO'></form> 
</p>

<p style=text-align:right><a href=movie.php?p_user=$p_user&token=$token_value>在线电影</a></p>

</body></html>

EOT;

//记录连接此页面的用户IP和最后访问时间,并且记录到数据库表online中
// $host = "localhost";
// $user = "root";
// $pass = "123456";
// $db = "mydb";
// $tb = "bban";
//$pic_multi_addr_local = $tb."all/"."$tb";



// $tb = "mytab";
//创建一个mysql连接
$connection = mysqli_connect($host, $user, $pass) or die("Unable to connect!");
//选择一个数据库
mysqli_select_db($connection,$db) or die("Unable to select database!");
//$name_ip = $_SERVER['REMOTE_ADDR'];
//$name_ip = get_real_ip();
$name_ip = getIp();//第二种获取IP的方法
$time = time();
//echo $name_ip;
$sql = "replace into online(name_ip,last_time,user) values ('$name_ip','$time','$p_user')";
mysqli_query($connection,$sql);

mysqli_close($connection);


//手动输入数据库所有的表名称


// $rs = array("ebod","ipz","bban","cjod","abp","jufd","jux","juy","meyd","mide","mxgs","pppd");

displaypic_rand($allfanhaos,3,$p_user);


function displaypic_rand($allfanhaos,$num,$p_user){//输入数据库表的数组和显示图片的数量
	echo <<< EOT

<style>

li {float:left;list-style:none;margin:3px;display:block;width:147px;}

div {width:600px;height:100px;margin:0 auto;}

li p{text-align:center;}

</style>

<div>


<ul>
EOT;
while ($num > 0) {
    $avtable_rand = $allfanhaos[array_rand($allfanhaos)];
    //This will get an array of all the gif, jpg and png images in a folder
    $img_array = array();
    $img_array = glob($avtable_rand."/*.{jpg}",GLOB_BRACE);
    //Pick a random image from the array
    if ($img_array == null) {
        //exit(1);
        //$num = $num - 1;
        continue;
    }
    $img = array_rand($img_array);
    //Display the image on the page
    $url = $img_array[$img];
    $substr_fanhao = substr($url, strlen("/")+strpos($url, "/"),(strlen($url) - strpos($url, "."))*(-1));
    $substr_fanhao = str_ireplace("_cavi","",$substr_fanhao);


echo <<< EOT

	<li>
		
		<a href="get.php?itt=$substr_fanhao&p_user=$p_user&token=$GLOBALS[token_value]"><img src="$url" style="width:147px;height:200px;" /></a>
		<p>$substr_fanhao</p>
	</li>
EOT;

		

		
		// echo "<img style='vertical-align:middle' alt='.$img_array[$img].' src='$img_array[$img]' />"; 
		// $url = $img_array[$img];
		// $substr_fanhao = substr($url, strlen("/")+strpos($url, "/"),(strlen($url) - strpos($url, "."))*(-1));
		// echo "<p></p>";
		// echo "$substr_fanhao";
		// echo "<p></p>";
		$num = $num - 1;
	}
	echo <<< EOT
</ul>
  
</div>

EOT;
	
}




?>