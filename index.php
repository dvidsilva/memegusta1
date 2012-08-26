<?php
include './dvid.class.php';

require_once('./controller/mobile_device_detect.php');

$mobile = mobile_device_detect(true,true,true,true,true,true,true,false,false);

if($mobile){
	$array[0]['css'] = " <link rel='stylesheet' type='text/css' href='css/mobile.css' />";
}else{
	$array[0]['css'] = " <link rel='stylesheet' type='text/css' href='css/layout.css'  /> ";
}

$site = new dvid;

$md = $site->get_meta();
$md = explode('|',$md);
$array[0]['title'] = $md[0];
$array[0]['img_url'] = $md[1];






include('./menu.php');


$header = $site->parse_template($array,'header.html');


if($site->isloggedin()==true){
	$user[0]['img_url']=$_SESSION['img_url'];
	$user[0]['username']=$_SESSION['username'];
	$user[0]['usr_id']=$_SESSION['id'];
	$user[0]['edit'] ='<a href=\'?f=usr&a=change_pic\' class=\'btn btn-warning\'>Cambiar Foto</a>';
}else{
	$user[0]['img_url']='memegusta.login.png';
	$user[0]['username']='Inicia Sesi&oacute;n';
	$user[0]['usr_id']='0';
	$user[0]['edit'] = '';
}
$small = array('show_post','add','login','signup','stalk','rules');

if(in_array($_GET['a'],$small)){
	$user[0]['ad']='
<div style="width:100%; height:20px;">&nbsp;</div>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-8534578105995627";
/* small right square */
google_ad_slot = "7984648885";
google_ad_width = 300;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<div style="width:100%; height:20px;">&nbsp;</div>
';
}else{
	$user[0]['ad']= '
<div style="width:100%; height:20px;">&nbsp;</div>

<script type="text/javascript"><!--
google_ad_client = "ca-pub-8534578105995627";
/* small right square */
google_ad_slot = "7984648885";
google_ad_width = 300;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<div style="width:100%; height:20px;">&nbsp;</div>

<div style="width:100%; height:20px;">&nbsp;</div>
';
}
/*****second ad
<script type="text/javascript"><!--
google_ad_client = "ca-pub-8534578105995627";
//* second right square 
google_ad_slot = "2003481590";
google_ad_width = 300;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
*/

if($mobile){
	$right = '';
}else{
	$right = $site->parse_template($user,'right_block.html');
}

$action = $run[1];
$page = new $run[0];
$action = $page->$action();
$array[][''] = '';
$footer = $site->parse_template($array,'footer.html');
/*
$seconds_to_cache = 86400;
$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
header("Expires: $ts");
header("Pragma: cache");
header("Cache-Control: max-age=$seconds_to_cache");
*/
echo $header;
echo $right;
echo $action;
echo $footer;
 
?>

