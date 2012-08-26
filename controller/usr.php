<?php
class usr extends dvid { 
	public function signup(){
		if($_SESSION['active']=='Y'){
			header("location: ?");
		}		
		//Si no se ha enviado informacion, entonces que muestre el formulario
		//cuando llega con datos posst, el ingresa la informacion y 
		//luego da header(location:confirmation) para evitar que 
		//duplique la informacion
		if(($_POST['send']=='' || !isset($_POST['send'])) && $_SESSION['active']!='Y'){
			include('./config/fields.usr.php');			
			$a = $this->parse_fields($a);
			$f = $this->parse_template($a,'usr/add_user.html');
			return($f);
		}elseif(isset($_POST['send'])){
			unset($_POST['send']);
			unset($_POST['rpassword']);
			$q1 = $this->mysql_compare('usr','username',$_POST['username']);
			$q2 = $this->mysql_compare('usr','email',$_POST['email']);
			if($q1 == true && $q2== true){
				$_POST['password'] = md5($_POST['password']);
				$_POST['img_url'] = '';
				$_POST['post_count'] = 0;
				include('controller/class.upload.php');
				$img = new upload($_FILES['img_url']);
				if($img->uploaded){
					$img->file_new_name_body = md5($_POST['username']).md5(time());
  					$img->image_resize = true;
  					$img->image_x = 300;
					$img->image_ratio_y = true;
					$img->image_convert = 'jpg';
					$img->jpeg_size = 43072;
  					$img->Process('./uploads/profile/');
					if ($img->processed) {
						$_POST['img_url'] = $img->file_dst_name;
						$img->clean();
					}else{
					$_SESION['note'] .= $img->error;
					}	
				}else{
					$dpic['F'][]='memegusta.female.1.jpg';
					$dpic['M'][]='memegusta.male.1.jpg';
					$_POST['img_url'] = $dpic[$_POST['gender']][0];
				}				
				$q = $this->form2query('insert','usr');
				$q = $this->mysql($q);
				header("location:?f=usr&a=login&success&username=".$_POST['username']."");
			}else{
				header("location:?f=usr&a=signup&failed");
			}	

		}
	}
	public function login(){
		if($_SESSION['active']=='Y'){
			header('location: ?');
		}
		if( $_POST['send']=='' && $_SESSION['active']!='Y'){
			$a = array();
			$f = $this->parse_template($a,'usr/login.html');
			return($f);
		}
		$q = $this->q2ar(" select * from usr where username = '".$_POST['username']."' and password = md5('".$_POST['password']."') and status in (0 , 1)");
		if(count($q)==1){
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
			header('location:?');
		}else{
			$_SESSION['note']='El password o usuario son incorrectos.';
			header('location:?f=usr&a=login&loginfailed');
		}


	}
	public function logout(){
		session_unset();
		header('location:?');
	}	
	public function stalk(){
		if(($_GET['id']==0 || $_GET['id']=='' ) && $_SESSION['active'] != 'Y'){
			header('location:?f=usr&a=login');		
		}
		if(!isset($_GET['id'])){
			$_GET['id'] == $_SESSION['id'];
		}
		$q = 'select * from usr where id = '.$_GET['id'].' limit 1';
//		echo $q;
		$usr = $this->q2ar($q);
		if($usr[0]['public']=='N'){
			$usr = $this->q2ar('select * from usr where id = '.$_SESSION['id'].' limit 1');							
		}
		
		$f = $this->parse_template($usr,'usr/user.html');
		include('./controller/posts.php');
		$p = new posts;
		$p->stalk = 1;
		$f .= $p->show_posts($_GET['id']);
		return($f);
	}
	public function edit(){
		$usr = $this->q2ar('select * from usr where id = '.$_SESSION['id'].' limit 1');							
		$f = $this->parse_template($usr,'usr/edit.html');


		if(isset($_POST['send'])){
			$q = $this->form2query('update','usr');
			header('location?f=usr&a=stalk');
		}
	}
	public function change_pic(){
		if($_SESSION['active']!='Y'){
			header("location: ?");
		}		
		if( !isset($_POST['send']) && $_SESSION['active']=='Y'){
			include('./config/fields.pic.php');			
			$a = $this->parse_fields($a);
			$f = $this->parse_template($a,'usr/pic.html');
			return($f);
		}elseif(isset($_POST['send']) && $_SESSION['active']=='Y'){
			include('controller/class.upload.php');
			if($_FILES['img_url']['name'] != ''){
				$newimg = $this->upload_pic($_FILES['img_url'],'./uploads/profile/');
				$q = "UPDATE usr SET img_url = '".$newimg."' WHERE id = '".$_SESSION['id']."'";
				$q = $this->mysql($q);	
				$_SESSION['img_url'] = $newimg;
			}
			if($_FILES['cover']['name'] != ''){			
				$newimg = $this->upload_pic($_FILES['cover'],'./uploads/profile/cover/',675);
				$q = "UPDATE usr SET cover  = '".$newimg."' WHERE id = '".$_SESSION['id']."'";
				$q = $this->mysql($q);	
			}
			header("location:?f=usr&a=stalk&id=".$_SESSION['id']."&pic_changed");
		}else{
			header("location:?f=usr&a=stalk&id=".$_SESSION['id']."&failed");
		}	
	}
	private function upload_pic($f,$l,$x = 300,$s = 43072){
		$img = new upload($f);
		if($img->uploaded){
			$img->file_new_name_body = 'memegusta-'.$_SESSION['id'].'-'.time();
			$img->image_resize = true;
			$img->image_x = $x;
			$img->image_ratio_y = true;
			$img->image_convert = 'jpg';
			$img->jpeg_size = $s;
			$img->Process($l);
			if ($img->processed) {
				$img->clean();
				return($img->file_dst_name);
			}else{
				$_SESION['note'] .= $img->error;
			}	
		}else{
			$_SESION['note'] = "Hubo un error al subir la imagen, intenta de nuevo";
			header("location:?f=usr&a=stalk&id=".$_SESSION['id']."&failed");
		}				
	}
}
?>
