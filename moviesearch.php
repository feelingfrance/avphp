<?php


include 'var.php';
header("Content-Type: text/html; charset=utf-8");
if (isset($_POST['movieitem'])) {
    $movieitem = $_POST['movieitem'];
} else {
    echo "错误";
    exit;

}
if($movieitem == ""){
	echo "查不到影片: $movieitem";
	exit;
}
$connection = mysqli_connect($host, $user, $pass) or die("Unable to connect!");

//选择一个数据库

mysqli_select_db($connection,$db) or die("Unable to select database!");

$movie_sql = "select * from ".$movie_table." where mintro like '%{$movieitem}%' or name like '%{$movieitem}%' order by mid desc";

$result = mysqli_query($connection,$movie_sql) or die("Error in query: $movie_sql . ".mysqli_error($connection));
$movie_name = array();
//echo $movie_sql;

while ($row=mysqli_fetch_row($result)) {

    $movie_name[] = $row;
}

if(empty($movie_name)){//不能加:,JS的特点,对象和数组的关系,我这边不考虑了,就不加:,eval 的特点
	echo "查不到影片  $movieitem";
	return;
}
//var_dump($movie_name);
echo json_encode($movie_name);

mysqli_free_result($result);
mysqli_close($connection);



?>