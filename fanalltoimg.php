<?php
//和AV PROJET 没有关系,仅仅用来下载图片
//根据番号all里文件夹的内容,下载对应番号的cover
//moviee/snisall/snis00343/下载为0snis-343.jpg
include 'stringfanhao.php';
include 'var.php';
$path = "moviee/";


$fans = array("dv");//可以是番号数组,比如("dv";"sdnm")


foreach($fans as $fan) {
    $fanall = $fan."all";
    $fanhaolist = scandir($path.$fanall);



    foreach(array_reverse($fanhaolist) as $fanhao) {
        if (($fanhao == ".") || ($fanhao == ".."))
            continue;
        $tmp = $fanhao;
		if($fan == "dv")
			$fanhao = $fan."-".substr($fanhao,-4);//因为DV的番号有4位数,所以特殊处理
		else
			$fanhao = $fan."-".substr($fanhao,-3);
        $url = $GLOBALS['avhome'].$fanhao;
        // echo $url.$name;
        // echo "<br>";
        //echo $url;
        $imgname = FALSE;
		if(file_exists($path.$fanall."/".$tmp."/"."0".$fanhao.".jpg")){
			continue;
		}
		
        $html = file_get_contents($url);
        
		
		if (strlen($html) > 0) {
            $imgname = getNeedBetween($html,"cover/",".jpg").".jpg";
            $imgaddr = $GLOBALS['avimghome']."cover/".$imgname;//网络上的番号图片地址
        }
        if ($imgname <> FALSE) {
            $fin = saveimg($imgaddr,"0".$fanhao.".jpg",$path.$fanall."/".$tmp);

        }

    }
}





?>