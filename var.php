<?php

//http.conf 设置禁止访问这个文件
//https端口为4433
//http端口为8088
//服务器开启了https加密
//服务器进行了地址转换,所有不是4433 HTTPS端口的,都切换到4433端口,进行HTTPS访问
//加密证书和域名有关系,不同的证书对应不同的域名,所有必须对应,否则不能进行4433端口的https访问.
//修改证书和域名的关系,需要修改文件httpd-ssl.conf,修改其中的VirtualHost
//apache error.log 文件里的错误 AH01909: 192.168.1.114:4433:0 server certificate does NOT include an ID which matches the server name
//是由于证书的通用名CN和访问的域名不一致导致,不影响使用.如果要消除这个错误,需要重新制作证书,让CN和域名一致
//php网页后面跟的参数itt 是请求get.php用来搜索的关键字
//php网页后面跟的参数tmp, 如果有参数tmp,说明打开搜索的页面来自sdf.php,用于区别打开搜索的页面是来自get.php,作用
//就是在sdf.php如果不输入关键字搜索,get.php就不读取数据库记录的最后一次搜索记录,并且清空数据库最后一次搜索记录.

$port = 8088; //定义服务器WEB端口
$tls_port = 4433;//定义加密端口
$addr = "http://jemmi.online";
#$addr = "http://192.168.1.114";//定义服务器地址
$host = "localhost";
$user = "root";
$pass = "xiaoping123";
$alive = 1440;//cookie 的存活时间为alive秒,默认1440秒
$ttl = 60; //redis 60秒内
$limit = 100;//redis 在ttl内可以访问limit次(多少时间内的访问次数)
$limit_sql = 300;//sql查询最多记录
$db = "mydb";
//$rs = array("ebod","ipz","bban","cjod","abp","jufd","jux","juy","meyd","mide","mxgs","pppd");
$allfanhaos = array("ebod","ipz","bban","cjod","abp","jufd","jux","juy","meyd","mide","mxgs","pppd","rbd","shkd","star","adn","snis","dvaj","sdnm");//数据库图片番号
$allmoviefanhaos = array_merge($allfanhaos,array("无码","国产","中文字幕"));
$movie_table = "moviewd";//数据库存放电影id和名称的表格名
$spec_table = ";online;users;movie;movie3;movie4;movie1;movie2;moviewd;";//用于区别存储图片的表,这些表存储其他信息,用;做第一个字符
$tmp_fold = iconv("UTF-8", "GBK", "tmp");;//临时文件夹
$spec_users = ";admin;yangguang";//特殊用户组
$spec_addr = "spec.php";//特殊网站
$movie_exten = ";mp4";//电影文件的扩展名,用于指定哪些文件可以加入到电影数据库
$wuma_movies = array("arse","carib","啄木鳥","啄木鸟","heyzo","smd","s_model","x-art","xxx","Dorcel","Private");//无码电影特征
$guochang_movies = array("自拍","偷拍","國產","国产","国内","国语");//国产特征
$avhome = "https://www.javbus.us/";
$avimghome = "https://pics.javcdn.pw/";
$sensible = 10;//相似度标志,similar_text函数,0为相似度最低,100完全相同
$php_exectute_time = 50;//php执行最多时间,单位为秒





function spec_page($s_user,$token) {//根据用户名,显示特殊页面地址

	if(strpos($GLOBALS['spec_users'],$s_user)){
		return $GLOBALS['spec_addr']."?p_user=$s_user&token=$token";
	}
	
}


//*************收费IP查询**************


//$ip = '117.25.13.123';
//$datatype = 'txt';
//$url = 'http://api.ip138.com/query/?ip='.$ip.'&datatype='.$datatype;

//$header = array('token:b4dbe8b43ca8ab9b54c75a309d19d383');
//echo getData($url,$header);   

function getData($ip){//输入IP地址,通过收费服务,显示IP和对应的地址和运营商
	$datatype = 'txt';
	$header = array('token:b4dbe8b43ca8ab9b54c75a309d19d383');
	$url = 'http://api.ip138.com/query/?ip='.$ip.'&datatype='.$datatype; 
    $ch = curl_init();  
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$header); 
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,3);  
    $handles = curl_exec($ch);  
    curl_close($ch);  
    return $handles;  
}

//*


?>