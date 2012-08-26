<?PHP

class meme extends dvid {

	private function get_query($id = 0){
		$lang = "'EN','ES','POR'";		
		$query = "
			SELECT 
			p.id,
			p.title,
			p.description,
			p.comment_count,
			p.img_url,
			p.thumb_url			
			FROM meme_post as p
			WHERE 
			p.lang in ($lang)";
		if($id!= 0){
			$query .=  " AND p.id = '$id' ";
		}
		$query .= " ORDER BY p.id DESC ";
		return($query);
	}
	
	private function fix_post($post){
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
		$c =  $this->parse_template($post,'meme/posts.html');
		$c .= $this->parse_template($links,'meme/pag_buttons.html');
		return($c);
	}	


	public function show_post(){
		$post = intval($_GET['id']);
		if($post == ''){
			return('Invalid Post ID');
		}
		$postid = $post;
		$query = $this->get_query($post);		
		$post = $this->q2ar($query);
		$post[0] = $this->fix_post($post[0]);
		$comments = $this->get_comments($postid);
		$post[0]['tags'] = $this->get_tags($postid,'meme');
		$post[0]['comments'] =  $this->parse_template($comments,'meme/comment.html');
		$c = $this->parse_template($post,'meme/post.html');	
		return($c);
	}
	


	public function add(){
		if(!isset($_POST['send']) && $this->is_authorized()){//change for something more secure
			//show the form, enable to add post
			include('./config/fields.meme.php');			
			$a = $this->parse_fields($a);
			$f = $this->parse_template($a,'meme/add.html');
			return($f);
		}
		if($_POST['send']!='' && $this->is_authorized()){
			$_POST['comment_count'] = 0;
			$_POST['date_added'] = date('Y-m-d G:i:s');
			$_POST['usr_id'] = $_SESSION['id'];

			include('./controller/class.upload.php');
			$img = new upload($_FILES['img_url']);			
			if($img->uploaded){
				$_POST['img_url'] = $this->fix_image($img);
				$_POST['thumb_url'] = $this->thumbnail($img);
			}
			$_POST['description'] = $this->fix_text($_POST['description']);
			$tags = $_POST['tags'];
			unset($_POST['tags']);
			$q = $this->form2query('insert','meme_post');
			$q = $this->mysql($q,'I');
			$tags = $this->set_tags($tags,$q,'meme');
			header("location: ?f=meme&a=show_post&id=$q&success ");
		}		
		//show a form emmm.... parse a form? or make form functions? if i knew xml better :(
		//by now let's just make functions... in a couple nights... but i want it to look good, so what about i parse a template, tada!
		//later, use the information in desc post to make the array
		//no tiene sentido hacerlo de otra manera	
	}
	//get_tags and set_tags is dvid.class.php

	private function get_comments($id){
		$query = "select c.*, u.img_url,u.username from meme_comment as c, usr as u where post_id = $id and u.id = c.usr_id";
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
			header('location: ?');			
			return('');			
		}else{
			$img->file_new_name_body = md5($_POST['username'].time().rand(0,40));
			if($img->image_src_x > 800){
				$img->image_resize = true;
				$img->image_x = 800;
				$img->image_ratio_y = true;
			}
			if($img->image_src_type != 'gif'){
				$img->image_convert = 'jpg';
				$img->jpeg_size = 100000;
				$img->image_border          = '0 0 16 0';
				$img->image_border_color    = '#000000';
				$img->image_text = 'memeGusta.com.co';
				$img->image_text_font       = 2;
				$img->image_text_position   = 'BR';
				$img->image_text_padding_y  = 2;
			}
			$img->Process('./uploads/meme/');
			if ($img->processed) {
			}else{
				$_SESION['note'] .= $img->error;
				return('');
			}
		}	
		return($img->file_dst_name);
	}
	private function thumbnail($img){
		if($img->file_is_image == false){
			$_SESION['note'] .= 'El Archivo subido no era una imagen valida';
			header('location: ?');			
			return('');			
		}else{
			$img->file_new_name_body = md5($_POST['username'].time().rand(0,40)).'thumbnail';
			$img->image_ratio_y = true;
			$img->image_resize = true;
			$img->image_ratio_crop = true;
			$img->image_y = 250;
			$img->image_x = 250;
			$img->image_convert = 'jpg';
			$img->Process('./uploads/meme/');
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
		$content = str_replace("'","\"",$content);		
		$content = str_replace('"','"',$content);
		return($content);
	}
	public function rules(){
		include('./config/rules.php');
		$r = $this->parse_template($a,'post/rules.html');
		return($r);
	}
}

?>
