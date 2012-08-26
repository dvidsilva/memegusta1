<?
if($_SERVER['SERVER_NAME']== 'localhost'){
	include '../config/config.local.php';
}else{
	include '../config/config.server.php';
}
$sql = mysql_connect($config['host'],$config['user'],$config['pass']);
mysql_select_db($config['dbname'],$sql);

function q2ar($query){
	$query = mysql_query($query);
	$n=0;
	if(is_resource($query)){
		while($row = mysql_fetch_row($query) ){
			$i=0;
			while ($i < mysql_num_fields($query)){
				$meta = mysql_fetch_field($query, $i);
				$data[$n][$meta->name] = $row[$i];
				$i++;
			}
			$n++;
		}
		return ($data);
	}else{
		return (false);
	}

}
?>
