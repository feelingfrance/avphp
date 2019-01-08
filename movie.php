
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
// if (isset($_GET['movie_id'])) {
    // $movie_id = $_GET['movie_id'];
// }
//连接数据库,根据movie_id,查询这个ID对应的电影名

echo <<< EOT
<html>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body{
        position: relative;
    }
    .my_css{
        position: fixed;
        right: 10px;
        top: 0;
		text-align:center;
    }
	.my_css1{
    position: fixed;
    right: 30px;
    top: 20px;
	text-align:center;
    }
	.my_css2{
    position: fixed;
    right: 0px;
    top: 80px;
	text-align:center;
    }
	.my_css3{
    position: absolute;
    right: 420px;
    top: 0px;
	text-align:center;
    }
</style>
<body>
<body>
<!--
    <div class="my_css">点击图像播放</div>
	<div class="my_css2">图在片在,图亡片亡</div>
	<div class="my_css1"><img src= attention.jpg height='60' width='60' /></div>
	-->
</body>

EOT;
$connection = mysqli_connect($host, $user, $pass) or die("Unable to connect!");
//选择一个数据库,读取电影的各类信息,用于显示

mysqli_select_db($connection,$db) or die("Unable to select database!");
$movie_name = array();
$movie_sql = "select * from ".$movie_table." order by rand()";//获取movie表里的内容,包括电影ID,图片名称,电影介绍
$result = mysqli_query($connection,$movie_sql) or die("Error in query: $movie_sql . ".mysqli_error($connection));
$i = 0;
while ($row=mysqli_fetch_row($result)) {
	$movie_name[$i][0] = $row[0];//电影名称
	$movie_name[$i][1] = $row[1];//电影ID
	$movie_name[$i][2] = $row[2];//电影介绍
	$movie_name[$i][3] = $row[3];//电影图片
	$i = $i + 1;
}


mysqli_free_result($result);
mysqli_close($connection);

echo <<< EOT

<p style=text-align:center><input type="text" id="field" onkeydown="if(event.keyCode==13) {test()}" /> 影片模糊搜索(番号) <button type="button" class="btn" id="btn" value="" onclick="fun(this)">提交</button></p>
<script>  
function test()  
{  
    $("#btn").click();  
}  
</script>  


<script type="text/javascript" language="javascript" src="jquery.min.js"></script>
<script type="text/javascript" language="javascript">

		
	
		var arr=new Array();
		var html = '';
		var p_user = "$p_user";
		var token_value = "$token_value";
		function fun(n) {
			
				if(n.value==""){
					var tt=$("#field").val();
				}else {
					var tt=n.value;
				}

			$.ajax({
				url:"moviesearch.php", 			
				type: "POST",	
				data:{movieitem:tt},
				success:function(result){
				try{
					arr=eval(result);
				}catch(ex){
					$("#view").html(result);
					return;
				}				
						
					for(var i=0;i<arr.length;i++){
						html += "<p align='center'><a href='movieplay.php?p_user=" + p_user + "&token=" + token_value + "&movie_id=" + arr[i][1] + "'><img src='" + arr[i][3] + "' height='357' width='533' /></a> <br/>";
						html += "<a href='movieplay.php?p_user=" + p_user + "&token=" + token_value + "&movie_id=" + arr[i][1] + "'>" + arr[i][2] + "</a> </p>";						

					}
					
					$("#view").html(html);
					html = '';
				}
			});
			
		}

	</script>

	
	<style type="text/css">
    .foot{width: 100%; height: 200px; text-align: center;}
    .foot ul{display: inline; margin-left: -10px;}
    .foot ul li{display: inline-block; margin-left: 10px; line-height: 30px;}

    </style>
	<div class="foot">
    <ul>
EOT;
	foreach($allmoviefanhaos as $fanhao){
		echo "<li><p style=text-align:center><button type='button' class='btn' id='btn1' value='$fanhao' onclick='fun(this)'>$fanhao</button></p></li>";
	}
echo <<< EOT

    </ul>
    </div>
	</div>

	<center><p id="view"></p></center>
EOT;

for($row=0;$row<count($movie_name);$row++){
	echo "<p align='center'>
	<a href='movieplay.php?p_user=$p_user&token=$token_value&movie_id={$movie_name[$row][1]}'><img src='{$movie_name[$row][3]} 'height='357' width='536' /></a>
	<br/>
	<a href='movieplay.php?p_user=$p_user&token=$token_value&movie_id={$movie_name[$row][1]}'>{$movie_name[$row][2]}</a> 
</p>";
	
}


echo <<< EOT

</body>
</html>
EOT;








?>



