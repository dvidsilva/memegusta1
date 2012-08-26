<?php

class dvid{
	function __construct(){
		if(!isset($_SESSION)){
			session_start();
		}
		//ini_set('memory_limit', '2M');
		if($_SERVER['SERVER_NAME']== 'localhost'){
			include './config/config.local.php';
		}else{
			include './config/config.server.php';
		}
		
		$sql = mysql_connect($config['host'],$config['user'],$config['pass']);
		mysql_select_db($config['dbname'],$sql);
	}


	
	public function mysql($query,$type=''){
		$data = mysql_query($query)or die(mysql_error()); 
		if($type=='I'){
			$q = mysql_insert_id();
			return($q);
		} 
		return($data);
	}	

	/*** 	As parameter receives an array with values like this
		$a['table']['field'] = 'value' and generates a query
		it returns true and the last_inserted_id if worked, false if failed or error
	***/
	public function mysql_insert($array,$table){
		//leer array keys of $a y eso da las tablas, por cada uno de esos iterar sobre $a[$array_key[$i]]
		foreach(array_keys($array) as $c){
			$column[] = $c;
		}
		foreach($array as $v){
			$values[] = $v;
		}
		$sql = 'INSERT INTO '.$table.' set ';
		for($i = 0; $i < count($column); $i ++){
			$sql .= $column[$i]." = '".$values[$i]."'";
		}
		$sql = $this->mysql($sql);
		return mysql_insert_id();
	}
	/***
	 *Basically I use this to see wheter an user exist or not, but might be useful for other stuff
	 */
	public function mysql_compare($table,$field,$value){
		$q = 'select * from '.$table.' where '.$field.' = "'.$value.'" ';
		$compare = $this->mysql($q);
		if(mysql_num_rows($compare)>1){
			$_SESSION['note'] .= 'El valor '.$value.' ya fue usado, intenta con uno diferente.';	
			return(false);
		}else{
			return(true);		
		}
	}

	public function mysql_delete(){
	
	}

	public function mysql_update(){
	
	}
	//receives a string or array and fixes text
	public function normalize_text($a){
		$char['normalizeChars'] = array(
	    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'&Aacute;', 'Á'=>'&Aacute;', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 
	    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'&Eacute;', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'&Iacute;', 'Î'=>'I', 
	    'Ï'=>'I', 'Ñ'=>'&Ntilde;', 'Ò'=>'O', 'Ó'=>'&Oacute;', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'&Uacute;', 
	    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'&aacute;', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 
	    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'&eacute;', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'&iacute;', 'î'=>'i', 
	    'ï'=>'i', 'ð'=>'o', 'ñ'=>'&ntilde;', 'ò'=>'o', 'ó'=>'&oacute;', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 
	    'ú'=>'&uacute;', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
		);
		if(is_array($a)){
			$t = array ();
			foreach($a as $s){
				$s1 = strtolower($s);
				$t[] = strtr($s1, $char['normalizeChars']);
			}
			return($t);
		}else{
			$a = strtolower($a);
			return(strtr($a, $char['normalizeChars']));	
		}
	}
	public function form2query($action,$table){
		unset($_POST['send']);
		$fields = array_keys($_POST);
		if($action == 'insert'){
			$q = 'insert into '.$table.' set ';
		}
		if($action == 'update'){
			$q = 'update '.$table.' set ';
		}
		foreach($fields as $f){
			$q .= " $f = '".$_POST[$f]."', ";
		}
		$q = rtrim($q,', ');
		return($q);
	}
	public function q2ar($query){
		$query = $this->mysql($query);
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
	/**
	 * Returns the result of a query as an array with key equal to k and value v
	 * */
	public function q2csl($q,$k,$v){
		$q = $this->mysql($q);
		if(is_resource($q)){
			while($row = mysql_fetch_assoc($q) ){
				$data[$row[$k]] = $row[$v];
			}
			return ($data);	
		}else{
			return (false);
		}		
	}
	/**
	 * Receives a list like 1,2,3 and returns '1','2','3' in an array
	 * */
	public function l2q($l){
		$l = explode(',',$l);
		if(count($l) < 1){
			return('');
		}
		foreach($l as $s){
			$s = trim($s);
			$n[] = "'".$s."'";
		}
		return($n);
	}

	public function table($array,$headers,$title){
		$table = '';
		$table .= "<table id=table >\n";
		$table .= "<caption >$title</caption>\n";	
		$table .="	<thead><tr>\n";
		$r = 1;
		if(!is_array($headers)){
			$headers = array_keys($array[0]);
		}
		foreach ($headers as $head){
			$table.="	<th>".$head."</th>\n";
		}
		$table .= "	</tr></thead>\n";
		foreach ($array as $l){
			$table.="	<tr>\n";
			foreach($l as $c){
				$table .= "<td>$c</td>";
			}
			$table .= "	</tr>\n";
		}
		return($table);
	}
	
	//receives an array with keys value and text and makes a combo box from it
	//assumes i converted all _post to variables
	public function ar2select($options,$name = ''){
		$option_list = '<option value=0 >Selecciona...</option>'."\n";
		foreach($options as $option){
			if($_POST[$name] == $option[$value] || $_GET[$name] == $option[$value]){
				$selected = 'selected';	
			}else{ 
				$selected='';
			}
			$option_list .= "<option value='".$option[$value]."' ".$selected." >".$option[$text]."</option>\n";	
		}
		return($option_list);
	}
	public function get_default_file(){
		return('posts');	
	}
	public function get_default_action(){
		return('show_posts');
	}
	public function action(){
		include './config/programs.php'; 
		$current_file = $_GET['f'];
		$current_action = $_GET['a'];
		if($current_file == '' ||  !in_array($current_file,$files)){
			$current_file = $this->get_default_file();
		}		
		if($current_action == '' || !isset($current_action) || !in_array($current_action,$actions[$current_file])){
			$current_action = $this->get_default_action();
		}
		$program_file = "./controller/$current_file.php";		
		include ($program_file);
		$r = array ($current_file,$current_action);
		return($r);
	}
	public function get_meta(){
		if($_GET['a']=='show_post'){
			$id = $_GET['id'];
			$q = 'select title,content,post_type_id from post where id = '.$id.'  limit 1';
			$q = $this->q2ar($q);
			if($q[0]['post_type_id']!=2){
				$q[0]['content'] = 'd41d8cd98f00b204e9800998ecf8427e52a6d3b379e82c364a7b89e6456f7934cf67355a3333e6e143439161adc2d82e.jpg';
			}
			return($q[0]['title'].'|'.$q[0]['content']);
		}
		$id = $_GET['id'];
		$q = 'select content from post where post_type_id = 2 ORDER BY RAND()  limit 1';
		$q = $this->q2ar($q);
		$img = ($q[0]['content']);
		$a[] = '';
		$a['meme'] = 'Conoce a los memes';
		$a['post'] = 'Califica y Comenta';
		$a['rules'] = 'Reglas';
		if($_GET['f']!=''){
			$f = $_GET['f'];
		}else{
			$f = 0;
		}
		if($_GET['a']=='rules'){
			$f = $_GET['a'];
		}

		return($a[$f].'|'.$img);
	}	
	public function parse_template($array,$file){
		$file = './template/'.$file;
		if(file_exists($file)){
			$template = file_get_contents($file);
		}else{
			return $file.' not found';
		}

		$template = explode("<!--LOOP_START-->",$template);
		$h = $template[0];
		$template = explode("<!--LOOP_END-->", $template[1]);
		$f = $template[1];
		$t = $template[0];
		if(is_array($array)){
			foreach($array as $l){
				foreach(array_keys($l) as $v){
					$w[] = "/%$v#/";
				}
				$c .= preg_replace($w,$l,$t);
			}
		}
		return($h.$c.$f); 
	}
	public function parse_fields($array){
		$in = array_keys($array);
		foreach($in as $i){
			if( in_array($array[$i]['name'],array_keys($_GET)) && $array[$i]['type']=='text'){
				$array[$i]['default'] = $_GET[$array[$i]['name']]; 
			}		
			if($array[$i]['label']!=''){
				$array[$i]['label'] = "<label for='".$array[$i]['name']."'>".$array[$i]['label']."</label>";			
			}
			if($array[$i]['type']=='hidden' || $array[$i]['type']=='password' || $array[$i]['type']=='text' || $array[$i]['type']=='file' || $array[$i]['type']=='checkbox'){
				$array[$i]['input'] = "<div id='".$array[$i]['name']."' ><input type='".$array[$i]['type']."' name='".$array[$i]['name']."' value='".$array[$i]['default']."'/></div>";
			}
			if($array[$i]['type']=='textarea'){
				$array[$i]['input'] = "<textarea name='".$array[$i]['name']."' >".$array[$i]['default']."</textarea>";
			}			
			if($array[$i]['type'] == 'date'){
				$array[$i]['input']= "<script>DateInput('".$array[$i]['name']."', true, 'YYYY-MM-DD')</script>";
			}
			if($array[$i]['type'] == 'select'){
				$array[$i]['input']="<select name='".$array[$i]['name']."' >";
				if(!is_array($array[$i]['options'])){
					$options = explode('|',$array[$i]['options']);					
					foreach($options as $o){
						$array[$i]['input'] .= "<option value='".$o."'>".$o."</option>";
					}
				}else{
					foreach($array[$i]['options'] as $o){
						$array[$i]['input'] .= "<option value='".$o['value']."'>".$o['text']."</option>";
					}
				}
				$array[$i]['input'] .= "</select>";
			}
			unset($array[$i]['options']);
			unset($array[$i]['default']);
		}		
		return($array);
	}
	public function page($query){
		$page = $this->get_page();
		$pagesize = $this->get_pagesize();
		$offset = ($page - 1) * $pagesize;		
		$query = $query.' limit '.$offset.', '.$pagesize.' ';
		return($query);
	}
	//Helps me get the previous and next button for post or pages that only have this two 
	public function page_links($query){
		$page = $this->get_page();
		$pagesize = $this->get_pagesize();
		$q = explode('FROM',$query);
		$q = 'SELECT count(*) as ceil FROM '.$q[1];
		$query = $this->q2ar($q);
		$ceil = $query[0]['ceil'];
		$pages = ceil($ceil/$pagesize);

		$current = $this->get_currentloc();
		$current = preg_replace("/&page=([0-9]*)/",'',$current);
		if($page > 1){
			$links[0]['prev_page'] = '?'.$current.'&page='.($page - 1);
			$links[0]['prev_class'] = '';			
		}else{
			$links[0]['prev_page'] = '';
			$links[0]['prev_class'] = ' inactive ';
		}
		if($page < $pages){
			$links[0]['next_page'] = '?'.$current.'&page='.($page + 1);
			$links[0]['next_class'] = '';
		}else{
			$links[0]['next_page'] = '';
			$links[0]['next_class'] = ' inactive ';
		}		

		return $links;
	}
	public function get_page(){
		$page = intval($_GET['page']);
		if($page == '' || $page < 0 ){
			$page = 1;
		}
		return($page);
	}
	public function get_pagesize(){
		$pagesize = intval($_GET['ps']);
		if($pagesize == '' || $pagesize < 2 ){
			$pagesize = 10;
		}
		return($pagesize);
	}
	public function get_currentloc(){
		$current = $_SERVER['REQUEST_URI'];
		$current = explode('?',$current);
		$current = $current[1];
		return($current);
	}
	public function isloggedin(){
		if($_SESSION['active'] == 'Y'){
			return(true);
		}else{
			return(false);
		}
	}
	public function is_authorized(){
		if(in_array($_SESSION['id'],array(1,9,32,26,27))){
			return(true);
		}else{
			return(false);
		}
	}	
	public function restrict($type=1){
		if($type===0){
			if($this->isloggedin()===true){
				header('location: ?');
				return('');
			}else{
				return('');
			}
		}
		if($type===1){
			if($this->isloggedin()===false){
				header('location: ?');
				return('');
			}else{
				return('');
			}
		}
	}
	public function set_session($q){
		$_SESSION['id'] = $q[0]['id'];
		$_SESSION['username'] = $q[0]['username'];
		$_SESSION['active'] = 'Y';			
		$_SESSION['fname'] = $q[0]['fname'];			
		$_SESSION['img_url'] = $q[0]['img_url'];
		$_SESSION['usr_type'] = $q[0]['img_url'];
		$_SESSION['status'] = $q[0]['status'];
		$uy = date('Y');
		$cy = explode('-',$q[0]['birthdate']); 
		$cy = $cy[0];
		$_SESSION['age'] = $uy - $cy;
		return(true);
	}
	/***
	 * Coje los tags ingresados por el usuario al post, revisa cuales estan en la base de datos y cuales no, inserta los que no
	 * esten y para el resto. inserta en tag2post 
	***/
	public function set_tags($t,$p,$table = 'post'){
		if(trim($t) == ''){
			return('');
		}
		$t = $this->normalize_text($t);
		$tl = explode(',',$t);
		for($i = 0; $i<count($tl);$i++){
			$tl[$i] = trim($tl[$i]);
		}
		$t = $this->l2q($t);
		$n = implode(',',$t);
		$tags = $this->q2csl('select id,name from tag where name in ('.$n.')','id','name');
		$s = array();
		if(!is_array($tags)){
			$tags = array ();
		}
		foreach($tl as $t1){
			if(!in_array($t1,$s)){
				if(in_array($t1,$tags)){
					$tid = array_search($t1,$tags);
					$q = $this->mysql("insert into tag2$table set post_id = '".$p."', tag_id = '".$tid."'");
					$q = $this->mysql("update tag set tagged_count = (tagged_count + 1) where id = '".$tid."'");
				} else {
					$q = "insert into tag set name = '".$t1."', tagged_count = 1";  
					$q = $this->mysql($q,'I');
					$q = $this->mysql("insert into tag2$table set post_id = '".$p."', tag_id = '".$q."'");
				}
			}
			$s[] = $t1;
		}
		return('');
	}
	
	public function get_tags($id,$table = 'post'){
		$q = "select tag.id,tag.name from tag2$table ,tag  where tag2$table.tag_id =tag.id and tag2$table.post_id = $id";
		$l = $this->q2csl($q,'id','name');
		if(is_array($l)){
			$tag = '';
			foreach($l as $li){
				$tag .= '<span class="label label-info">'.$li.'</span> ';
			}
			$l = 'Tags : '.$tag;
			return($l);
		}
		return(' ');
		
	}
}

?>
