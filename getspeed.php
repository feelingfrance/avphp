<?php


if (isset($_POST['getspeed']))
{
	echo "目前的上传网速为: ".getspeed()."bps";
}

function getspeed() {//通过操作系统命令netstat获取网络速度,比较暴力获取的方式,没有找到PHP原生代码

    exec("netstat -e", $result);

    $tmpstr=explode(" ",$result[4]);

	//print_r($tmpstr);
	$long = count($tmpstr);
    $startbytes=trim($tmpstr[$long - 1]);

    sleep(2);


    exec("netstat -e", $result1);

    $tmpstr=explode(" ",$result1[4]);
	//print_r($tmpstr);
    $stopbytes=trim($tmpstr[$long - 1]);


    $speed = ceil(($stopbytes-$startbytes) / 480);


    return $speed;

}


?>