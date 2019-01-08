<?php
include 'var.php';
$ips = array();
if (isset($_POST['ips']))
{
	$ips = $_POST['ips'];
	foreach ($ips as $ip){
		echo getData($ip)."<br>";
	}
	
	
} else {
	echo "没有IP";
}



?>