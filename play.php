<?php
include 'var.php';
//通过隐藏视频地址来进行播放
$movie_name = "";
if (isset($_GET['movie_name'])) {
    $movie_name = $_GET['movie_name'];
}


$file = "$movie_name";//大文件超过php.ini中的内存配置


//$fp = fopen($file,'rb');


// while (!feof($fp)) {//测试文件指针是否到了文件结束的位置
    // $tmpvar = fread($fp,1024);
    // echo $tmpvar;
// }
// fclose($fp);
// exit;


//PutMovie($file);//断点续传,偶尔有问题,不懂
//smartReadFile($file,$movie_name);
GetMp4File($file);//这个好点
//getmp4($file);//这个国外网站拷过来

function getmp4($path){
	
	

//$path = 'file.mp4';

$size=filesize($path);

$fp=fopen($path,'rb');
// if(!$fm) {
  //You can also redirect here
  // header ("HTTP/1.0 404 Not Found");
  // die();
// }

$begin=0;
$end=$size;

if(isset($_SERVER['HTTP_RANGE'])) {
  if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
    $begin=intval($matches[0]);
    if(!empty($matches[1])) {
      $end=intval($matches[1]);
    }
  }
}

if($begin>0||$end<$size)
  header('HTTP/1.0 206 Partial Content');
else
  header('HTTP/1.0 200 OK');

header("Content-Type: video/mp4");
header('Accept-Ranges: bytes');
header('Content-Length:'.($end-$begin));
header("Content-Disposition: filename=".basename($path)); 
header("Content-Range: bytes $begin-$end/$size");
header("Content-Transfer-Encoding: binary\n");
//header('Connection: close');

$cur=$begin;
fseek($fp,$begin);
    while(!feof($fp)) { 
        $p = min(1024, $end - $begin + 1); 
        $begin += $p; 
        echo fread($fp,$p); 
    } 
    fclose($fp); 
// while(!feof($fm)&&$cur<$end&&(connection_status()==0))
// { echo fread($fm,min(1024,$end-$cur));
  // $cur+=1024;
  // usleep(1000);
// }
//die();
}

function PutMovie($file) {

    header("Content-type: video/mp4");

    header("Accept-Ranges: bytes");



    $size = (filesize($file));

    if (isset($_SERVER['HTTP_RANGE'])) {



        header("HTTP/1.1 206 Partial Content");

        list($name, $range) = explode("=", $_SERVER['HTTP_RANGE']);

        list($begin, $end) =explode("-", $range);

        if ($end == 0) $end = $size - 100;

    }

    else {

        $begin = 0;
        $end = $size - 100;

    }

    header("Content-Length: " . ($end - $begin + 1));

    header("Content-Disposition: filename=".basename($file));

    header("Content-Range: bytes ".$begin."-".$end."/".$size);



    $fp = fopen($file, 'rb');

    fseek($fp, $begin);

    while (!feof($fp)) {

        $p = min(1024, $end - $begin + 1);

        $begin += $p;

        echo fread($fp, $p);

    }

    fclose($fp);
    exit;

}

//方法主要是将视频拆分成N个碎片，每份1KB，此方法缺陷是用户在观看视频是会不断向服务器读取视频，因此流量大了。。。你懂得...

function GetMp4File($file) { 
    $size = filesize($file); 
    header("Content-type: video/mp4"); 
    header("Accept-Ranges: bytes"); 
    if(isset($_SERVER['HTTP_RANGE'])){ 
        header("HTTP/1.1 206 Partial Content"); 
        list($name, $range) = explode("=", $_SERVER['HTTP_RANGE']); 
        list($begin, $end) =explode("-", $range); 
        if($end == 0){ 
            $end = $size - 1; 
        } 
    }else { 
        $begin = 0; $end = $size - 1; 
    } 
    header("Content-Length: " . ($end - $begin + 1)); 
    header("Content-Disposition: filename=".basename($file)); 
    header("Content-Range: bytes ".$begin."-".$end."/".$size); 
	header("Content-Transfer-Encoding: binary");
    $fp = fopen($file, 'rb'); 
    fseek($fp, $begin); 
    while(!feof($fp)) { 
        $p = min(1024, $end - $begin + 1); 
        $begin += $p; 
        echo fread($fp, $p); 
    } 
    fclose($fp);
	exit;
} 
?>

