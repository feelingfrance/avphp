<?php




function isvalid($key,$limit,$alive){//同一IP地址(或关键字),在ALIVE有效期内的访问次数
	//读取redis的key值(IP地址或者其他关键字),通过判断alive(秒)的有效时间以及次数limit
	//如果不符合要求范围否
	//连接本地的 Redis 服务
	$redis = new Redis();
	$redis->connect('127.0.0.1', 6379);
	$check = $redis->exists($key);//查询是否存在key的值
	if($check){
		$redis->incr($key);
		$count = $redis->get($key);
		//echo $count;
		//echo $redis->ttl($key);
		//$redis->expire($key,0);
		if($count > $limit){
			//exit('超出限制次数');
			echo "剩余".$redis->ttl($key)."秒.  ";
			return 0;//无效访问
		}
	}else{
		$redis->incr($key);//key对应的值初始化为0,在加上1
		$redis->expire($key,$alive);
		
	}
	return 1;//有效访问
	
}



?>