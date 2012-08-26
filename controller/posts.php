<?PHP

class posts extends dvid {
	function __construct(){
		$this->stalk = 0; 
	}
	private function render_content($content,$type){
		if($type==1){ //post_type 1 es img_url
			 $content = "<img src='".$content."' />";
		}
		if($type==2){ //post_type 2 es img uploaded
			$content = "<img src='uploads/posts/".$content."' />";
		}
		if($type==4){ //post_type 3 es text area
			//function that parses text to show it pretty or when stored?
			$content = "<div class='text_content' style=''>".$content."'</div>";
		}
		if($type==3){ //post_type 4 es youtube link
			$content = "<div class=''>
				<iframe width=\"480\" height=\"360\" src=\"http://www.youtube.com/embed/".$content."?rel=0\" frameborder=\"0\" allowfullscreen></iframe>			
				</div>";
		}
		return($content);
	}

	/*Returns an array of the post the usr likes to check against the post displayed and then change the button */
	private function user_likes($posts){
		$l = array(); 
		if($posts==''){
			return($l);
		}
		if($_SESSION['id']!=''){
			$usr = $_SESSION['id'];
		}else{
			return($l);
		}
		$r = $this->q2ar('select post_id,yn from usr2post where usr_id = '.$usr.' and post_id in ('.$posts.') ');
		for($i = 0; $i < count($r); $i++){
			$l[$r[$i]['post_id']] = $r[$i]['yn'];
		}	
		return($l);
	}

	private function get_query($id = 0,$uid = 0){
		$lang = "'EN','ES','POR'";		
		$query = "
			SELECT 
			p.id,
			p.title,
			p.content,
			p.like_count,
			p.date_published,
			p.post_type_id,
			p.nsfw,
			p.report_count,
			p.comment_count,
			p.source,
			p.usr_id,			
			u.username
		FROM post as p, usr as u
		WHERE 
			p.active = 'Y'
			AND p.usr_id = u.id
			AND p.like_count > '-5'
			AND report_count < 10
			AND p.lang in ($lang)
			AND p.content != '' ";
/*		if($_SESSION['age'] <= 18 || $_SESSION['active']!= 'Y'){
			$query .= ' AND nsfw = \'N\'  ';
		}
*/
		if($id!= 0){
			$query .=  " AND p.id = '$id' ";
		}
		if($uid!= 0){
			$query .=  " AND p.usr_id = '$uid' ";
		}
		if(!in_array($_GET['s'],array('best','trendy','all')) && $id == ''){
			$_GET['s'] = 'best';
		}
		if($this->stalk == 0){
			switch ($_GET['s']){
				case 'trendy':
					$query .= " AND p.like_count >= 5 ";
					$query .= " ORDER BY p.id DESC ";
					break;
				case 'best':
					$query .= " AND p.like_count >= 10 ";
					$query .= " ORDER BY p.id DESC ";
					break;
				case 'all':
					$query .= " ORDER BY p.id DESC ";
					break;
				default:
					$query .= " ORDER BY p.id DESC ";
					break;
			}
		}else {
			$query .= " ORDER BY p.id DESC ";
		}
		return($query);
	}
	
	private function fix_post($post){
		if($post['post_type_id']==2){
			$post['img_url'] =  $post['content'];
		}else{
			$post['img_url'] = 'd41d8cd98f00b204e9800998ecf8427e05f56f249f0e3a7489b3ab773da0714c5b4a2146246bc3a3a941f32225bbb792.jpg';
 		}
		if($post['nsfw']=='N'){
			$post['nsfw'] = '';
		}else{
			$post['nsfw'] = '<span class="nsfw" title="Not safe for work" >NSFW</span>';				
		}
		if($post['source']!=''){
			$pos = stripos($post[$i]['source'],'http://');
			if($pos === false){ 
				$post['source'] = 'http://'.$post['source'];
			}
			$post['source'] = "<a href='".$post['source']."' target='_blank'>Source</a> ";
		}	
		$post['content'] = $this->render_content($post['content'],$post['post_type_id']);		
		$post['cuser'] = $_SESSION['id'];		
		return($post);
	}

	public function show_posts($uid=0){

		$query = $this->get_query(0,$uid);

		$links = $this->page_links($query);	
		$query = $this->page($query);
		$post = $this->q2ar($query);
		for($i = 0; $i < count($post); $i ++){
			$post[$i] = $this->fix_post($post[$i]);
			$up[] = $post[$i]['id'];			
		}
		if(is_array($up)){
			$up = implode(',',$up);
		}
		$up = $this->user_likes($up);
		for($i = 0; $i < count($post); $i++){
			if($up[$post[$i]['id']]=='Y'){
				$post[$i]['l'] = ' selected ';
			}
			if($up[$post[$i]['id']]=='N'){
				$post[$i]['d'] = ' selected ';
			}	 	
		}
		if($this->stalk == 0){
			$menu = $this->get_menu();
			$c  =  $this->parse_template($menu,'post/menu.html');
		}
		$c .=  $this->parse_template($post,'post/posts.html');
		$c .= $this->parse_template($links,'post/pag_buttons.html');
		return($c);
	}	

	public function get_menu(){
		$a[0]['tg'] =  'best';
		$a[1]['tg'] =  'trendy';
		$a[2]['tg'] =  'all';
		for($i = 0; $i <= 2; $i++){
			if($a[$i]['tg'] == $_GET['s']){
				$a[$i]['class'] = 'current';
			}else{
				$a[$i]['class'] = '';
			}
			if($_GET['s']==''){
				$a[0]['class'] = 'current';
			}
		}
		$a[0]['name'] =  'Lo Maximo';
		$a[1]['name'] =  'Chevere';
		$a[2]['name'] =  'La Cueva';		
		return($a);
	}
	public function show_post(){
		$post = intval($_GET['id']);
		if($post == ''){
			return('Invalid Post ID');
		}
		$postid = $post;
		$query = $this->get_query($post);		
		$post = $this->q2ar($query);
		$up = $this->user_likes($postid);	
		if($up[$post[0]['id']]=='Y'){
			$post[0]['l']=' selected ';
		}
		if($up[$post[0]['id']]=='N'){
			$post[0]['d']=' selected ';
		}		
		$post[0] = $this->fix_post($post[0]);
		//$comments = $this->get_comments($postid);
		$post[0]['tags'] = $this->get_tags($postid);
		//$post[0]['comments'] =  $this->parse_template($comments,'post/comment.html');
		$c = $this->parse_template($post,'post/post.html');	

		return($c);
	}
	


	public function add(){
		if(!isset($_POST['send']) && $this->isloggedin()){//change for something more secure
			//show the form, enable to add post
			include('./config/fields.post.php');			
			$a = $this->parse_fields($a);
			$f = $this->parse_template($a,'post/add_post.html');
			return($f);
		}elseif($_POST['send']!='' && $this->isloggedin() == true){
			//insert the info in the database, and then header(location:show post) or somehing so he doesnt hit refresh and re insert.
			//add usr_id
			if(!isset($_POST['nsfw'])){
				$_POST['nsfw']= 'N';
			}else{
				$_POST['nsfw']= 'Y';
			}
			$_POST['report_count'] = 0;
			$_POST['like_count'] = 0;
			$_POST['comment_count'] = 0;
			$_POST['date_published'] = date('Y-m-d G:i:s');
			$_POST['usr_id'] = $_SESSION['id'];
			if($_POST['post_type_id']==3){
				preg_match('/[\\?\\&]v=([^\\?\\&]+)/',$_POST['content'],$matches);
				$_POST['content'] = $matches[1];				
			}
			if($_POST['post_type_id']==2){
				include('controller/class.upload.php');
				$img = new upload($_FILES['content']);			
				if($img->uploaded){
				$_POST['content'] = $this->fix_image($img);
			}
			}
			if($_POST['post_type_id']==4){
				$_POST['content'] = $this->fix_text($_POST['content']);
			}
			$tags = $_POST['tags'];
			unset($_POST['tags']);
			$q = $this->form2query('insert','post');
			$q = $this->mysql($q,'I');
			$q2 = $this->mysql('update usr set post_count = (post_count + 1)  where id = '.$_SESSION['id'].'');
			$tags = $this->set_tags($tags,$q);
			header("location: ?f=posts&a=show_post&id=$q&success ");
		}else{
			$a[0][] = 1;
			$f = $this->parse_template($a,'must_login.html');
			return($f);
		}		
	}

	private function get_comments($id){
		$query = "select c.*, u.img_url,u.username from comment as c, usr as u where post_id = $id and u.id = c.usr_id";
		$c = $this->q2ar($query); 
		$e = 0;
		$comment = array();
		for($i = 0; $i < count($c); $i++){
			if($c[$i]['comment_id']==0){
				$comment[$e] = $c[$i];
				$comment[$e]['child'] = $this->child_comments($comment[$e]['id'],$c);
				$e++;
			}

		}
		return($comment);	
	}	      
	private function child_comments($id,$comments){
		$c = array();
		for($i = 0; $i < count($comments); $i++){
			if($comments[$i]['comment_id']==$id){
				$c[] = $comments[$i];
			}		
		}
		$c = $this->parse_template($c,'post/ccomment.html'); 
		return($c);
	}
	private function fix_image($img){
		if($img->file_is_image == false){
			$_SESION['note'] .= 'El Archivo subido no era una imagen valida';
			return('');
		}else{
			$img->file_new_name_body = md5($_POST['username']).md5(time()).md5(rand(0,4000));
			if($img->image_src_x > 1024){
				$img->image_resize = true;
				$img->image_x = 1024;
				$img->image_ratio_y = true;
			}
			if($img->image_src_type != 'gif'){
				$img->image_convert = 'jpg';
				$img->jpeg_quality = 100;
				$img->image_border          = '0 0 30 0';
				$img->image_border_color    = '#000000';
				$img->image_watermark       = './media/watermark.png';
				$img->image_watermark_position = 'BR';
			}
			$img->Process('./uploads/posts/');
			if ($img->processed) {
				$img->clean();
			}else{
				$_SESION['note'] .= $img->error;
				return('');
			}
		}	
		return($img->file_dst_name);
	}
	private function fix_text($content){	
		$content = str_replace("\t","   ",$content);
		$content = str_replace("\n"," <br/> ",$content);
		$content = str_replace("'","",$content);		
		$content = str_replace("\"","\"",$content);
		return($content);
	}
	public function rules(){
		include('./config/rules.php');
		$r = $this->parse_template($a,'post/rules.html');
		return($r);
	}
}

?>
