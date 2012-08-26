<?php
//eSV37ff28k
include ('shared.php');
$post = $_GET['post'];
$usr = $_GET['usr'];
$action = $_GET['action'];

if($usr == ''){
	die('R');
}

function like_count($ac,$post,$invert){
	if($ac=='Y' && $invert === 0){
		$query = "update post set like_count = (like_count + 1) where id = '$post'";
	}
	if($ac=='N' && $invert === 0){
		$query = "update post set like_count = (like_count - 1) where id = '$post'";
	}
	if($ac=='N' && $invert === 'I'){
		$query = "update post set like_count = (like_count + 1) where id = '$post'";
	}
	if($ac=='Y' && $invert === 'I'){
		$query = "update post set like_count = (like_count - 1) where id = '$post'";
	}
	if($ac=='N' && $invert === 2){
		$query = "update post set like_count = (like_count - 2) where id = '$post'";
	}
	if($ac=='Y' && $invert === 2){
		$query = "update post set like_count = (like_count + 2) where id = '$post'";
	}
	mysql_query($query) or die(mysql_error());	
}

if(!in_array($usr,array(27,1,26))){
	$verify = q2ar("select * from usr2post where post_id = '$post' and usr_id = '$usr' ");

	if(count($verify)==0){
		$query = "insert into usr2post set post_id = '$post', usr_id = '$usr', yn = '$action'";
		$like = mysql_query($query)or die(mysql_error());
		like_count($action,$post,0);
	}else{
		if($verify[0]['yn'] == $action){
			$query = "delete from usr2post where post_id = '$post' and usr_id = '$usr'";
			$like = mysql_query($query)or die(mysql_error()); 
			like_count($action,$post,'I');
			$same = 1;	
		}else{
			$query = "update usr2post set yn = '$action' where post_id = '$post' and usr_id = '$usr'";
			$like = mysql_query($query)or die(mysql_error());	
			like_count($action,$post,2);
		}
	}
}

if(in_array($usr,array(27,1,26))){
	if($action == 'Y'){
		$q = " update post set like_count = like_count + 1 where id = '$post'";
		
	}
	if($action == 'N'){
		$q = " update post set like_count = like_count - 1 where id = '$post'";
	}
	mysql_query($q) or die(mysql_error());	
}


$r = q2ar('select like_count from post where id = '.$post);
$response['Y'] = 1;
$response['N'] = 0;
echo($response[$action].','.$r[0]['like_count'].','.$same);

?>

