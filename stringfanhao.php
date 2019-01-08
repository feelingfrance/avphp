

<?php





function stringtofanhao($str){//根据字符串找出番号.番号最多是5个英文字母+3个阿拉伯数字,最少3个英文字母+3个阿拉伯数字
//比方说,asdfsdf12312jux-352,找出番号为jux-352
//[影子の6park.com] 051014_807-1pon-whole1_hd.mp4

	$num = "";
	$fan = "";
	//$ind = stripos($str,"-");//第一个-出现的位置
	$ind = strripos($str,"-");//最后一个-出现的位置
	if($ind < 3)
		return FALSE;
	if(is_numeric(substr($str,$ind+1,1)) && is_numeric(substr($str,$ind+2,1)) && is_numeric(substr($str,$ind+3,1))){
		$num = substr($str,$ind+1,3);
	}
	
	if(is_letter(substr($str,$ind-1,1)) && is_letter(substr($str,$ind-2,1)) && is_letter(substr($str,$ind-3),1)){
		$fan = substr($str,$ind-3,3);
	}else 
		return FALSE;
	if(is_letter(substr($str,$ind-4,1)) && ($ind > 3)){
		$fan = substr($str,$ind-4,1).$fan;
	}else{
		if($num <> "")		
			return $fan."-".$num;
		else
			return FALSE;
	}
	if(is_letter(substr($str,$ind-5,1)) && ($ind > 4)){
		$fan = substr($str,$ind-5,1).$fan;
	}
	if(($num <> "") && ($fan <> ""))
		return $fan."-".$num;
	else
		return FALSE;
}


function is_letter($str){//查询是否全是英文字母
if (preg_match ("/^[A-Za-z]/", $str))
	return 1;
else
	return 0;
}

	


//saveimg($imgaddr,$fanhao.".jpg","");

function saveimg($imgurl,$imgname,$path){
set_time_limit($GLOBALS['php_exectute_time']);

if(file_exists($path."/$imgname")){
	return TRUE;
}

$fy = fopen($imgurl,'r');
$fl = fopen($path."/$imgname",'w');

 echo "下载的图片文件为:".$path."/$imgname";
 echo "<br>";
 ob_flush();
 flush();
 
if(($fy === FALSE) || ($fl === FALSE)){
	return FALSE;
}
while (!feof($fy)) {//测试文件指针是否到了文件结束的位置
    $tmpvar = fread($fy,1024);
    fwrite($fl,$tmpvar);
}
fclose($fy);
fclose($fl);
return TRUE;

	
}
//取代GLOB,GLOB在win10下,读取带有[]的文件夹,不能读取,只能有scandir来读取. path 不要带/结尾
function globtoimg($path) {
    $listimg = array();
    $list = scandir($path);
    foreach($list as $img) {
        $extension = pathinfo($img, PATHINFO_EXTENSION);//扩展名
        if ($extension == "jpg") {
            $listimg[] = $path."/".$img;
        }
    }

	return $listimg;
}



function getNeedBetween($kw1,$mark1,$mark2){//截取两个字符串之间的字符串,

 $mar1len = strlen($mark1);

 $mar2len = strlen($mark2);

 $st =stripos($kw1,$mark1);

 $ed =stripos($kw1,$mark2);
 
 if(strlen($kw1) < 1)
	 return 0;

 
 if(($st==false||$ed==false)||$st>=$ed)
 return 0;
 $kw=substr($kw1,$st+strlen($mark1),$ed-$st-strlen($mark1));
 return $kw;
}
function stringtofanhaonohengang($str){//str 没有-的情况下,来找番号,mama352,找番号为mama-352,//最后位开始,去除扩展名

	$houzhui = substr(strrchr($str, '.'), 1);
	$str = basename($str,".".$houzhui);//去除扩展名的文件名
	$len = strlen($str);
	$num = "";
	$fan = "";
	if($len < 6){
		return FALSE;
	}
	if(is_numeric(substr($str,$len-1,1)) && is_numeric(substr($str,$len-2,1)) && is_numeric(substr($str,$len-3,1))){
		$num = substr($str,$len-3,3);
	}
	if(is_letter(substr($str,$len-4,1)) && is_letter(substr($str,$len-5,1))&& is_letter(substr($str,$len-6,1))){
		$fan = substr($str,$len-6,3);
	}else{
		return FALSE;
	}
	if(is_letter(substr($str,$len-7,1))){
		$fan = substr($str,$len-7,1).$fan;
	}
	if(($num <> "") && ($fan <> ""))
		return $fan."-".$num;
	else
		return FALSE;
}

function movietojpg($movie,$arrjpg){//给定一个电影文件,和一个图片数组,从数组里找出和电影文件最相识的一张图片
	$pivo = -1;
	$tmp = "";
	
	$houzhui = substr(strrchr($movie, '.'), 1);
	$movie = basename($movie,".".$houzhui);//去除扩展名的文件名
	$movie = strtolower($movie);//大写变小写
	
	foreach($arrjpg as $jpg){
		$jpg = basename($jpg);
		$hz = substr(strrchr($jpg, '.'), 1);
		$jpg = basename($jpg,".".$hz);
		
		similar_text($movie,$jpg,$percent);
		if(round($percent) > $pivo){
			$tmp = $jpg;
			$pivo = round($percent);
		}
	}
	if($pivo > $GLOBALS['sensible'])
		return $tmp.".jpg";
	else 
		return "";


}


?>





