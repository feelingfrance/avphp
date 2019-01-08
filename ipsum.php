<html>
<head>
<meta charset="utf-8" />
<title>页面统计</title>
</head>
<body>
<p style=text-align:center>输入密码才能访问这个页面</p>
</body>
</html>


<?php
include 'var.php';
//phpinfo();
$min = 100;//查询min分钟内的访问IP
if (isset($_POST['time']) && is_numeric($_POST['time'])){
	$min = $_POST['time'];
}



session_start([
                  'read_and_close'  => true,

              ]);


if (isset($_POST['password']) && $_POST['password'] == 'superball') {
	
    $_SESSION['ok'] = 1;
    // header("location: $webaddr");

}
if (!isset($_SESSION['ok'])) {
    exit('
         <form method="post">
         <p style=text-align:center>密码：<input type="password" name="password" />
		 <input type="submit" value="登陆" />
		 <p style=text-align:left>查询时间(分钟)：默认 100分钟<input type="text" name="time" />
         
         </p>
         </form>
         ');
}


//读取数据库表online的数据,获取一段时间内的访问人数

// $host = "localhost";
// $user = "root";
// $pass = "123456";
// $db = "mydb";
$limit = time()-$min*60; //min分钟内的在线数,*60表示单位为分钟





// $tb = "mytab";
//创建一个mysql连接
$connection = mysqli_connect($host, $user, $pass) or die("Unable to connect!");
//选择一个数据库
mysqli_select_db($connection,$db) or die("Unable to select database!");
$sqlcount = "select count(*) from online where last_time>=$limit and name_ip <> '127.0.0.1'";
$sqlall = "select * from online where last_time>=$limit order by last_time desc";
$rescount = mysqli_query($connection,$sqlcount);
$resall = mysqli_query($connection,$sqlall);
list($online_count) = mysqli_fetch_row($rescount);

echo "访问数据库在线人数($min 分钟内)： $online_count 人";
echo "<br />";
$oldtime = 0;
$nowtime = time();
$ips = array();//用于收费查询,查询的IP地址放入数组
if (mysqli_num_rows($resall)>0) {
    while ($row=mysqli_fetch_row($resall)) {
		if(strcmp($row[0],"127.0.0.1") == 0)
			continue;
		$oldtime = $row[1];
		$t = ceil(($nowtime - $oldtime) / 60);
		echo $t."分钟前访问. ";
		//echo getData($row[0]);//收费查询
        echo "$row[0]";
		$ips[] = $row[0];
		$html = iconv("gb2312","UTF-8",file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip=$row[0]")); 
		$html1 = str_ireplace("1","",$html);
		echo " ".$html1;
		echo " -----用户名: ".$row[2];
		$sqlmovie = "select movie from users where name='$row[2]'";
		$rsmovie = mysqli_query($connection,$sqlmovie);
		$lastmovie = mysqli_fetch_row($rsmovie);
		echo " 最近一次观看电影: $lastmovie[0]";
        echo "<br />";
		mysqli_free_result($rsmovie);

    }
}

//print_r($_SESSION);
mysqli_free_result($rescount);
mysqli_free_result($resall);
mysqli_close($connection);

//查看连接状态,为了换行,保存到临时文件,在加入换行符号
if (!file_exists($tmp_fold)) {

    mkdir ($tmp_fold,0777,true);
}
$tmpfile = tempnam($tmp_fold,"TMP0");
system("netstat -an|findstr $tls_port|findstr ESTABLISHED|findstr /v \"\<127\" >$tmpfile");
$myfile = fopen("$tmpfile","r");
while(!feof($myfile)) {
	$itemStr = fgets($myfile);
	echo "<br/>".$itemStr;
}
fclose($myfile);
unlink($tmpfile);
//echo "目前的上传网速为: ".getspeed()."bps";//获取上传网速




?>



    <script type="text/javascript" language="javascript" src="jquery.min.js"></script>
	<script type="text/javascript" language="javascript">
		function fun(n) {
		
			var tt=eval(<?php echo json_encode($ips);?>);//php 数组转换为JS数组
		
			$.ajax({
				url:"ippay.php", 			//the page containing php script
				type: "POST", 				//request type
				data:{ips:tt},//数组TT 传递给php页面
				success:function(result){
					$("#view").html(result);
					
				}
			});
		}
		
		function fun2(n) {
			var url = "getspeed.php";
			var data = {
				getspeed : n.value
			};
			jQuery.post(url, data, callback);
		}
		function callback(data) {

			$("#viewspeed").html(data);
		}
	</script>
	<div>
		<button type="button" class="btn2" id="btn2" onclick="fun2(this)">显示上传速度</button>
		<button type="button" class="btn" id="btn1" onclick="fun(this)">查询运营商(收费)</button>
	</div>
	
	
	<p id="viewspeed"></p>
	<p id="view"></p>