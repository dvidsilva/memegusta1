<?php
//eSV37ff28k
include ('shared.php');



function comment_count($post){
	$query = "update post set comment_count = (comment_count + 1) where id = '$post'";
	mysql_query($query) or die(mysql_error());	
}
function add_comment($post,$usr,$content,$comment_id=0){
	$query = "insert into comment set comment_id = '$comment_id', usr_id = '$usr', content = '$content', post_id = '$post'";
	$like = mysql_query($query)or die(mysql_error());
	$i = mysql_insert_id();
	$query = "update usr set comment_count = (comment_count + 1) where id = '$usr'";
	mysql_query($query) or die(mysql_error());	
	comment_count($post);
	return('Y,'.$i);
}

$post = $_POST['post'];
$usr = $_POST['usr'];
$content = $_POST['content'];
$comment_id = $_POST['comment_id'];
if($usr == ''){
	die('R');
}
if($comment_id==''){
	$comment_id=0;
}
$c = add_comment($post,$usr,$content,$comment_id);
echo($c);

?>

