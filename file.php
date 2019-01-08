<?php
//http.conf 设置禁止外网读取这个文件,只能本地127.0.0.1引用
	include 'var.php';
	include 'stringfanhao.php';
	date_default_timezone_set("prc");
	$count_avtosql = 0;//记录记录多少电影加入到数据库
	$t1 = time();
	
    header("Content-Type: text/html; charset=utf-8");
	if (isset($_POST['dir'])){
		$dir = $_POST['dir'];
	}else{
		echo "目录错误";
		exit;
	}
	//echo $dir."OK";
	//exit;
	
	//$dir = $movie_folder;
	if(!is_dir($dir)){
		echo "$dir 文件夹不存在或者错误";
		return;
	}
	$list = scandir($dir);
	if($list === FALSE){
		echo "scandir($dir)错误";
		return;
	}
	//var_dump($list);
	foreach($list as $file){
		if(($file == ".") || ($file == "..")){//.和..目录跳过
			continue;
		}
		if(stripos($file,"RECYCLE") !== FALSE){
			//echo "RECYCLE";
			continue;
		}
		if(stripos($file,"System Volume Information") !== FALSE){
			//echo "SYSTEM";
			continue;
		}
		avscan("$dir/".$file);
		//*****第二次查询.目录下还有目录的情况
		if(is_file($dir."/".$file)){
			continue;
		}
		$downlist = scandir($dir."/".$file);
		if($downlist === FALSE){
			echo "scandir($dir/$file)错误";
			return;
		}
		foreach($downlist as $ff){
			if(is_file($dir."/".$file."/".$ff)){//第一次查询已经包含这个,在avscan里面包含
				continue;
			}
			if(($ff == ".") || ($ff == "..")){//.和..目录跳过
				continue;
			}
			avscan($dir."/".$file."/".$ff);	
		}
		//*****第二次查询结束
		//echo "$dir/".$file;
		//echo "<br>";
	}
	$t2 = time() - $t1;
	
	echo date("H:i:s",time()).", < $dir > 目录扫描结束,电影数据( $count_avtosql 条)已经存入数据库,用时:"."$t2 秒";
	
	
	
	
	function avscan($dir){//根据路径查找路径的文件或者文件名,然后运行av2sql,把数据添加到数据库,返回正确,否则返回错误
		
		if(is_file($dir)){
			//echo "ISFILE";
			$exten = pathinfo($dir, PATHINFO_EXTENSION);
			if(stripos($GLOBALS['movie_exten'],$exten) === FALSE){//扩展名不是视频文件
					//echo $exten;
					return FALSE;
			}
			$no_exten = basename($dir,".".$exten);//文件名没有后缀
			
			foreach($GLOBALS['wuma_movies'] as $wuma){
				if(stripos($no_exten,$wuma) ===FALSE){//不符合无码规则
					continue;
				}else{
					$no_exten = $no_exten."(无码)";//符合无码规则,加入无码字符,并且直接退出
					break;
				}
				
			}
			av2sql(dirname($dir),basename($dir),$no_exten,"/av.jpg");
			return TRUE;
		}
		if(stripos($dir,"RECYCLE") !== FALSE){
			//echo "RECYCLE";
			return FALSE;
		}
		if(stripos($dir,"System Volume Information") !== FALSE){
			//echo "SYSTEM";
			return FALSE;
		}
		
		$dirname = basename($dir);//获取文件夹名称
		
		foreach($GLOBALS['wuma_movies'] as $wuma){
			if(stripos($dirname,$wuma) === FALSE){//不符合无码规则
				continue;
			}else{
				$dirname = $dirname."(无码)";//符合无码规则,加入无码字符,并且直接退出
				break;
			}
				
		}
		foreach($GLOBALS['guochang_movies'] as $gc){
			if(stripos($dirname,$gc) === FALSE){//不符合国产规则
				continue;
			}else{
				$dirname = $dirname."(国产)";//符合国产规则,加入无码字符,并且直接退出
				break;
			}
				
		}
		
		
		
		try{
			$list = scandir($dir);//获取文件夹下所有文件和文件夹的名称
		}catch (Exception $e){
			return FALSE;
		}
		if($list === FALSE)
			return FALSE;
		//var_dump($list);
		//$listjpg = glob("$dir/*.jpg");//获取文件夹下所有的JPG图片
		$listjpg = globtoimg("$dir");//获取文件夹下所有的JPG图片
		
		
		//var_dump($listjpg);
		$filename = array();
		$picname = "";
		foreach($list as $file){
			$extension = pathinfo($file, PATHINFO_EXTENSION);//扩展名
			
			if(stripos($GLOBALS['movie_exten'],$extension) <> FALSE){//扩展名是视频文件
				$filename[] = $file;
				//break;
			}
		}
		/*
		foreach($listjpg as $file){
			$file = basename($file);
			$prefix = substr($file,0,3);
			//***PHP 居然有相识度的原生程序
			if(empty($filename))
				//continue;
				break;
			//****
			
			//***		
				
			similar_text($file,$filename[0],$percent);
			if(round($percent) > $GLOBALS['sensible']){
				$picname = $file;
				break;
			}
			//*******
			
			
			if(strcasecmp($prefix,substr($filename[0],0,3)) == 0){//前3个字符相同
				$picname = $file;
				break;
			}
			if(stripos($file,substr($filename[0],0,3)) <> FALSE){//视频文件前3个字符包含在图片文件里的话
				$picname = $file;
				break;
			}
		}
		*/
		foreach($filename as $fff){
			$picname = movietojpg($fff,$listjpg);
			if($picname == ""){
				//***如果没有对应的图片,就从网上下载.从数据里里读取番号,从网上下载,保存到本地
				//修改放到av2sql里面去做
			    //***
				// echo "av2sql($dir,$fff,$dirname,/av.jpg)";
				// echo "<br>";
				av2sql($dir,$fff,$dirname,"/av.jpg");
			}
			else
				av2sql($dir,$fff,$dirname,$dir."/".$picname);
			//return TRUE;
		}
		// if($filename <> ""){
			// if($picname == "")
				// av2sql($dir,$filename,$dirname,"/av.jpg");
			// else
				// av2sql($dir,$filename,$dirname,$dir."/".$picname);
			// return TRUE;
		// }
		if(empty($filename))
			return FALSE;
		
		return TRUE;
	}
	function av2sql($dir,$name,$minfo,$mpic){//把文件的信息加入到数据库
		
		//echo "av2sql($dir,$name,$minfo,$mpic)";
		$host = $GLOBALS['host'];
		$user = $GLOBALS['user'];
		$pass = $GLOBALS['pass'];
		$db = $GLOBALS['db'];
		//创建一个mysql连接
        $connection = mysqli_connect($host, $user, $pass) or die("Unable to connect!");
        //选择一个数据库
        mysqli_select_db($connection,$db) or die("Unable to select database!");
		$last_row_sql = "select mid from ".$GLOBALS['movie_table']." order by mid desc limit 0,1";
		$last_row_rs = mysqli_query($connection,$last_row_sql);
		$last_row_mid = mysqli_fetch_row($last_row_rs)[0];
		$last_row_mid = $last_row_mid + 1;
		//******如果没有图片,从网上下载,保存到本地,利用av.jpg这个标志,如果网上能下载就修改av.jpg为网上下载的图片
		$imgname = FALSE;
		if(($mpic == "/av.jpg") && (substr_count($dir."/".$name,"/") > 1)){
			$fanhao = stringtofanhao($minfo);
			if($fanhao === FALSE){//如果找不到番号,试着在没有-的字符串里找番号
				$fanhao = stringtofanhaonohengang($minfo);
			}
			if($fanhao === FALSE){//如果找不到番号,试着在文件名里找番号
				$fanhao = stringtofanhao($name);
				
			}
			if($fanhao === FALSE){//如果找不到番号,试着在没有-的字符串里找番号
				$fanhao = stringtofanhaonohengang($name);
			}
			
			//echo "eee".$fanhao;
			if($fanhao <> FALSE){
				$url = $GLOBALS['avhome'].$fanhao;
				 // echo $url.$name;
				 // echo "<br>";
				$html = file_get_contents($url);
				if(strlen($html) > 0){
					$imgname = getNeedBetween($html,"cover/",".jpg").".jpg";
					$imgaddr = $GLOBALS['avimghome']."cover/".$imgname;//网络上的番号图片地址
				}
				//$imgaddr = $GLOBALS['avimghome']."cover/".$imgname;//网络上的番号图片地址
			}
			if($imgname <> FALSE){
				$fin = saveimg($imgaddr,$fanhao.".jpg",dirname($dir."/".$name));
				if($fin <> FALSE){
					$mpic = $dir."/".$fanhao.".jpg";//修改mpic为网上下载到本地的图片地址
				}
			}
			
		}else if(($mpic == "/av.jpg") && (substr_count($dir."/".$name,"/") < 2)){
			$fanhao = stringtofanhao($minfo);
			if($fanhao === FALSE){//如果找不到番号,试着在没有-的字符串里找番号
				$fanhao = stringtofanhaonohengang($minfo);
			}
			if($fanhao === FALSE){//如果找不到番号,试着在文件名里找番号
				$fanhao = stringtofanhao($name);
			}
			if($fanhao === FALSE){//如果找不到番号,试着在没有-的字符串里找番号
				$fanhao = stringtofanhaonohengang($name);
			}
			if($fanhao <> FALSE){
				$url = $GLOBALS['avhome'].$fanhao;
				$html = file_get_contents($url);
				if(strlen($html) > 0){
					$imgname = getNeedBetween($html,"cover/",".jpg").".jpg";
					
					$imgaddr = $GLOBALS['avimghome']."cover/".$imgname;//网络上的番号图片地址
					$fin = saveimg($imgaddr,$fanhao.".jpg",dirname($dir."/".$name));
					if($fin <> FALSE){
						$mpic = $dir."/".$fanhao.".jpg";//修改mpic为网上下载到本地的图片地址
					}
					//$mpic = $imgaddr;
				}
			}
			
		}
		if((stringtofanhao($minfo) === FALSE) && (stringtofanhao($name) <> FALSE) && ($mpic <> "/av.jpg")){//如果mintro没有番号,可以在name里面找番号,并且代替mintro
			$minfo = stringtofanhao($name);
		}else {
			if((stringtofanhao($minfo) === FALSE) && (stringtofanhaonohengang($name) <> FALSE) && ($mpic <> "/av.jpg")){
				$minfo = stringtofanhaonohengang($name);
			}
		}
		//*********结束网上下载的程序
		$path = $dir."/".$name;
		// if(strpos($mpic," ") <> FALSE)
			// $mpic = "/av.jpg ".$mpic;//因为地址有空格,浏览器不能显示空格地址,所以用一个图片代替
        $sql = "insert into ".$GLOBALS['movie_table']." (name,mid,mintro,mpic) values ('$path','$last_row_mid','$minfo','$mpic')";
		//echo $sql;
        $rs = mysqli_query($connection,$sql);
		mysqli_free_result($last_row_rs);
            //关闭该数据库连接
        mysqli_close($connection);
		$GLOBALS['count_avtosql']++;
		
	}

?>