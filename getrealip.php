<?php
//获取客户端真实ip地址  
function get_real_ip(){  
    static $realip;  
    if(isset($_SERVER)){  
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){  
            $realip=$_SERVER['HTTP_X_FORWARDED_FOR'];  
        }else if(isset($_SERVER['HTTP_CLIENT_IP'])){  
            $realip=$_SERVER['HTTP_CLIENT_IP'];  
        }else{  
            $realip=$_SERVER['REMOTE_ADDR'];  
        }  
    }else{  
        if(getenv('HTTP_X_FORWARDED_FOR')){  
            $realip=getenv('HTTP_X_FORWARDED_FOR');  
        }else if(getenv('HTTP_CLIENT_IP')){  
            $realip=getenv('HTTP_CLIENT_IP');  
        }else{  
            $realip=getenv('REMOTE_ADDR');  
        }  
    }  
    return $realip;  
}


// 获取IP地址（摘自discuz）

function getIp() {

    $ip='未知IP';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {

        return is_ip($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:$ip;

    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

        return is_ip($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$ip;

    }else {

        return is_ip($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$ip;

    }

}

function is_ip($str) {

    $ip=explode('.',$str);

    for ($i=0;$i<count($ip);$i++) {

        if ($ip[$i]>255) {

            return false;

        }

    }

    return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$str);


}
  

?>