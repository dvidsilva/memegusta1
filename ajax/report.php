<?php
//eSV37ff28k
include ('shared.php');
$post = $_GET['post'];
$usr = $_GET['usr'];
$action = $_GET['action'];

if($usr == ''){
	die('R');
}

function report_count($post){
	$query = "update post set report_count = (report_count + 1) where id = '$post'";
	mysql_query($query) or die(mysql_error());	
}

$verify = q2ar("select * from report_post where post_id = '$post' and usr_id = '$usr' ");

if(count($verify)==0){
	$query = "insert into report_post set post_id = '$post', usr_id = '$usr', report_type_id = '1'";
	$like = mysql_query($query)or die(mysql_error());
	report_count($post);
	$r = 1;
}else{
	$r = 0;
}

$c = q2ar('select report_count from post where id = '.$post);

echo($r.','.$c[0]['report_count']);

?>

