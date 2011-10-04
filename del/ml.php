<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


if((!isset($argv[1])) OR (!isset($argv[2]))){
	die("
	Base checker, used for searching valid forms\r\n
	Usage:	php mCheck.php threadCount[integer] file[path/to/file] \r\n
	Example: php mCheck.php 70 ./bases/11_08_blogs.txt\r\n

");
}

$thNum = $argv[1];
$file = file($argv[2]);
$num = count($file);
$perTh = $num/$thNum;
$perTh = ceil($perTh);

for($i = 0; $i < $thNum; $i++){
	$start = $perTh*$i + 1;
	echo("(php lastlogin.php '".$start ."|".$perTh."|".$argv[2]."' & ) >> /dev/null 2>&1"); die();
}


?>
