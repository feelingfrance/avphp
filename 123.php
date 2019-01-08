<?php

$t = 0;
function aa(){

	 $GLOBALS['t'] = $GLOBALS['t'] + 1;
	 
}
aa();
echo $t;
echo "<br>";
aa();
echo $t;
echo "<br>";
aa();
echo $t;
echo "<br>";
aa();
echo $t;
echo "<br>";





?>