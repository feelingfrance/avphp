

<?php
include 'var.php';
include 'token.php';

session_start(['read_and_close'  => true,]);
//$port = 8088;
//$addr = "http://jemmi.gicp.net";
//$addr = "http://192.168.1.114";
//$index = $addr.":$port/index.php";
$index = $addr.":$port/index.html";

if (!isset($_COOKIE["user"])) {//cookie 生命到期,需要重新登录,返回到登录界面
    header("location: $index");

}
//番号,演员或主题
header("Content-Type: text/html; charset=utf-8");
header("Cache-control: private");
$p_user = "";

if (isset($_GET["p_user"])) {
    $p_user = $_GET["p_user"];
}

if ($p_user == "") {
    header("location: $index");
}
if ($_COOKIE["user"] <> $p_user) {//cookie的值和p_user的值不一样就重新登录
    header("location: $index");
}
setcookie("user", "", time()-$alive);//先删除cookie
setcookie("user","$p_user",time()+$alive);//再定义cookie,保持cookie的有效

if(validToken("token")){
	//echo "token值一致";
} else{
	header("location: $index");
	//echo "token 不一致";
}
$token_value = $_SESSION['token'];//传递token值过去
$webaddr = $addr.":$port/sdf.php?p_user=$p_user&token=$token_value";

//itt 是图片的超级连接番号,用来查询数据库,也可以是其他任何要查询的关键字
$itt = "";
if (isset($_GET["itt"])) {
    $itt = $_GET["itt"];
}

//	echo $itt;
//	exit(1);




$cx="";
if ((isset($_POST['submit'])) || ($itt <> ""))
{

    if (isset($_POST['submit'])) {
        $what=$_POST['yourcontent'];

    }

    if ($itt <> "") {
        $what = $itt;

    }


    if (substr_count($what," ") == strlen($what)) {
        $what = "";
    }


    if ($what == "") {
        if (isset($_GET["tmp"])) {//有前一个页面打开的
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
            $delete_search_sql = "update users set search='' where name='$p_user'";
            mysqli_query($connection,$delete_search_sql);

            $what = "番号,演员或主题";

            //关闭该数据库连接
            mysqli_close($connection);
        } else {
            //$what = "番号,演员或主题";
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
            $fanhao_sql = "SELECT search FROM users where name='$p_user'";
            $rs = mysqli_query($connection,$fanhao_sql);
            $row = mysqli_fetch_row($rs);
            $what = $row[0];
            //echo strlen($what);
            if ( strlen($what) < 1) {//search 内容为空,说明还是第一次查询
                //echo "sdfasdf";
                $what = "番号,演员或主题";
            }

            mysqli_free_result($rs);
            //关闭该数据库连接
            mysqli_close($connection);
        }


    }
echo <<< EOT
<html><body>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
 </head>
<form method='post' action='get.php?p_user=$p_user&token=$token_value'>
<p style=text-align:center><input type='text' placeholder="$what" name='yourcontent'>
<input type='submit' name='submit' value='AV GO'></form>

</p>


<!--<a href="$webaddr" style="position:absolute;right:200px;top:10px">返回首页</a>-->
<div align="right">
	<a href="$webaddr">返回首页</a>
</div>
</body></html>

EOT;



//echo "rrr".$what."rrr";
if (($what == "番号,演员或主题") || ($what == " ") || ($what == ".")) {
    echo "<script> alert('请输入查询内容')
    </script>";
} else {

//echo $what;
	$addhen = $what;
	$addhentmp = str_split($what);//字符串变换比如 jux291=jux-291,让jux291也能当做jux-291
	$cnt = count($addhentmp);
	if($cnt > 2){
		if(is_numeric($addhentmp[$cnt-1]) and is_numeric($addhentmp[$cnt-2]) and is_numeric($addhentmp[$cnt-3])){
			$ttt = array_slice($addhentmp,0,-3);
			$addhen = implode($ttt);
			$addhen = $addhen."-".$addhentmp[$cnt-3].$addhentmp[$cnt-2].$addhentmp[$cnt-1];
		}		
	}
    $cx=" where 番号 like '%{$what}%' or 番号 like '%{$addhen}%' or 内容 like '%{$what}%' order by rand() limit $limit_sql";//限制每个查询结果的条目,避免服务器拥堵
	//echo $cx;
    $pic_addr="";
//连接数据库的参数


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


    //mysqli_query($connection,"DELETE FROM pivo");
    //mysqli_query($connection,"INSERT INTO pivo (prerecord) VALUES ('$what')");

    $add_search_sql = "update users set search='$what' where name='$p_user'";
    mysqli_query($connection,$add_search_sql);

    $rs = mysqli_query($connection,"SHOW TABLES FROM $db");
    $avtable = array();
    //获取所有表格的名称
    while ($row = mysqli_fetch_row($rs)) {
        // if ($row[0] == "online")//忽略下面表
            // continue;
        // if ($row[0] == "users")
            // continue;
		// if ($row[0] == "movie")
            // continue;
		if (stripos($spec_table,$row[0]))
		{
			continue;
		}
        $avtable[] = $row[0];

        // if($row[0] <> "online") //忽略pivo这个表
        // $avtable[] = $row[0];
    }
    echo "<table cellpadding=10 border=1 align=center>";
    echo "<tr>";
    echo "<th>标题</th><th>番号</th><th>发行日期</th><th>演员</th><th>字幕</th><th>影片</th>";
    echo "</tr>";


    $pic_addr_array = array();//远程图片地址的数组,目前没有使用,网上其他网站的图片链接地址
    $pic_addr_local_array = array();
    $pic_addr_all_local_array = array();//缩略图的所有大图片的本地访问地址的数组
	$all_num_rows = 0;//最多显示的条目,初始化为0 ,最多显示条目<=$limit_sql
    foreach($avtable as $tb) {//遍历所有表


        //开始查询
        //$query = "SELECT * FROM $tb WHERE 字段6 LIKE '%abp-108%'";
        $query = "SELECT * FROM $tb".$cx;
		//echo $query;
        //执行SQL语句
        $result = mysqli_query($connection,$query) or die("Error in query: $query. ".mysqli_error($connection));
		//$all_num_rows = $all_num_rows + mysqli_num_rows($result);
		if($all_num_rows > $limit_sql){//如果所有表的查询结果条目已经超过limit_sql,就不继续
			break;
		}
		$all_num_rows = $all_num_rows + mysqli_num_rows($result);
        //显示返回的记录集行数

        if (mysqli_num_rows($result)>0) {
            //如果返回的数据集行数大于0，则开始以表格的形式显示
            // echo "<table cellpadding=10 border=1 align=center>";
            // echo "<tr>";
            // echo "<th>标题</th><th>番号</th><th>发行日期</th><th>演员</th><th>字幕</th>";
            // echo "</tr>";

            //$pic_addr_array = array();
            //$pic_addr_local_array = array();
            // $pic_fanhao_array = array();
            while ($row=mysqli_fetch_row($result)) {

                // echo "<td>".$row[0]."</td>";
                //echo "<td>".$row[1]."</td>";
                //echo "<td>".$row[2]."</td>";
                $pic_multi_addr_local = $tb."all/"."$tb";


                $pic_addr_local="$tb"."/"."$row[2]".".jpg";//本地图片地址名称
                if (strpos("$row[1]","字幕") !==false) {
                    $pic_addr_local="$tb"."/"."$row[2]"."_cavi.jpg";
                }

                $pic_addr_local_array[] = $pic_addr_local;
                //$pic_fanhao_array[] = $row[2];
                $pic_addr=$row[0];//远程图片地址
                $pic_addr_array[] = $pic_addr;



                //******获取abpall的地址************************
                $fanhao = $row[2];
                $fanhao_num = str_ireplace("$tb-","",$fanhao);
				
				
				
                //$pic_multi_addr_local = $pic_multi_addr_local."00".$fanhao_num;
				if($fanhao_num > 999){
					$pic_multi_addr_local = $pic_multi_addr_local."0".$fanhao_num;//av这个番号,因为大于999,所以特殊处理,少些一个0
				}else{
					$pic_multi_addr_local = $pic_multi_addr_local."00".$fanhao_num;
				}
                //echo $pic_multi_addr_local;


                //**********************************************
                //echo "<td>".$row[5]."</td>";
                //echo  "<a href='www.baidu.com'>超链接测试按钮</a>";
                echo "<tr>";
                //echo "<td>"."<a href='$pic_addr_local'>$row[5]</a>"."</td>";
                echo "<td>"."<a href='allpic.php?item=$pic_multi_addr_local&p_user=$p_user&token=$token_value'>$row[5]</a>"."</td>";

                $pic_addr_all_local_array[] = "allpic.php?item=$pic_multi_addr_local&p_user=$p_user&token=$token_value";




                echo "<td>".$row[2]."</td>";
                echo "<td>".$row[3]."</td>";
                echo "<td>"."<a href='get.php?itt=$row[4]&p_user=$p_user&token=$token_value'>$row[4]</a>"."</td>";//请求get.php搜索关键字itt,这里itt为row[4]的内容


                if (stripos("$row[1]","字幕") !== false) {
                    echo "<td>"."中文字幕"."</td>";
                }
                else {
                    //echo "<td>"."无"."</td>";
                    echo "<td><p style=text-align:center>无</td>";
                }
                //echo "</tr>";
				$delhenfanhao = str_ireplace("-","",$row[2]);
				$movie_sql = "select mid from $movie_table where mintro like '%{$row[2]}%' or mintro like '%{$delhenfanhao}%'";
				$rsmovie = mysqli_query($connection,$movie_sql) or die("Error in query: $movie_sql. ".mysqli_error($connection));
				
				if (mysqli_num_rows($rsmovie)>0) {
					$mid = mysqli_fetch_row($rsmovie)[0];
					echo "<td>"."<a href='movieplay.php?p_user=$p_user&token=$token_value&movie_id=$mid'>播放</a>"."</td>";
				}
				echo "</tr>";
				mysqli_free_result($rsmovie);
				
                //图片地址远程0
                //内容1
                //标题5
                //番号2
                //时间3
                //演员4
            }

        }


    }//endforeach
    echo "</table>";
    //$substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1));
    $i = 0;
    foreach ($pic_addr_local_array as $url) {
        $substr_fanhao = substr($url, strlen("/")+strpos($url, "/"),(strlen($url) - strpos($url, "."))*(-1));
        echo "<p style=text-align:center><a href=$pic_addr_all_local_array[$i]><img src='$url' /></a></p>";
        echo "<p style=text-align:center><a href=$pic_addr_all_local_array[$i]>$substr_fanhao</a></p>";

        $i = $i+1;
    }

    //释放记录集所占用的内存
    mysqli_free_result($result);
    mysqli_free_result($rs);

    //关闭该数据库连接
    mysqli_close($connection);



}

}else {

    //echo $webaddr;
    header("location: $webaddr");
}

?>


