<?php  

header("Content-Type: text/html; charset=utf-8");

$serverName = "localhost"; //数据库服务器地址
$uid = "sa";     //数据库用户名
$pwd = "111"; //数据库密码
$connectionInfo = array("UID"=>$uid, "PWD"=>$pwd, "Database"=>"shifenzheng");
$conn = sqlsrv_connect($serverName, $connectionInfo);
if( $conn == false)
{
    echo "SQL SRV数据库连接失败！";
    var_dump(sqlsrv_errors());
    exit;
}else{
   // echo "SQL SRV数据库连接成功<br />";
	
	if (isset($_POST['name'])){
		$name = $_POST['name'];
	}
	if (isset($_POST['addr'])){
		$addr = $_POST['addr'];
	}
	if (isset($_POST['gender'])){
		$gender = $_POST['gender'];
	}
	
	//echo "<br />"."姓名:$name"."<br />";
	
if (isset($_POST['rand'])){
	$sql = "select * from cdsgus where id = cast( floor(rand()*20050144) as int) ";
	
}
else if(!empty($name)){
	$sql = "select top 5 * from dbo.cdsgus where Name = '$name' and Address like '%{$addr}%'";
	//echo $sql;
	}
	else if(!empty($addr) && (!empty($gender))){
		$sql = "select top 10 * from dbo.cdsgus where Gender = '$gender' and Address like '%{$addr}%' and id > cast( floor(rand()*20050144) as int) and Mobile <> ''";
		//echo $sql;
	}
	else{
		$sql = "select top 10 * from dbo.cdsgus where Address like '%{$addr}%'";
		//echo $sql;
	}
	
//echo $sql;
$sql = iconv('UTF-8','GBK',$sql);//转换编码
$stmt = sqlsrv_query($conn, $sql);
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}
echo "<br />";
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
	echo "姓名:";
    echo iconv('GB2312','UTF-8//IGNORE',$row[0]."<br />");
	echo "身份证号码:";
	echo iconv('GB2312','UTF-8//IGNORE',$row[4]."<br />");
	echo "性别:";
	echo iconv('GB2312','UTF-8//IGNORE',$row[5]."<br />");
	echo "地址:";
	echo iconv('GB2312','UTF-8//IGNORE',$row[7]."<br />");
	echo "手机:";
	echo iconv('GB2312','UTF-8//IGNORE',$row[19]."<br />");
	echo "电话:";
	echo iconv('GB2312','UTF-8//IGNORE',$row[20]."<br />");
	echo "EMAIL:";
	echo iconv('GB2312','UTF-8//IGNORE',$row[22]."<br />");
	echo "民族:";
	echo iconv('GB2312','UTF-8//IGNORE',$row[23]."<br />");
	echo "ID:";
	echo iconv('GB2312','UTF-8//IGNORE',$row[32]."<br />");
	  
}


sqlsrv_free_stmt( $stmt);
	
}