<?php
class form extends dvid {
	function __construct(){
		$this->xml = '';	
		$this->query = '';
		$this->ar = '';
	}
	public function add(){
		$xml = simplexml_load_file($this->xml) or die('Hubo un problema cargando los datos;');
		#$xml->enctype = "ENCTYPE='multipart/form-data'";
		$form  = "<div class='page-header'><h2>".$xml->title['h2']."<small>".$xml->title['small']."</small></h2></div>";
		$form .= "<div class='row-fluid'><div class='span12'>".$xml->description."</div></div>";
		$form .= "<form name=".$xml['name']." id=form action='?".$this->get_currentloc()."' method=post ".$xml->enctype." class='well'>\n";
		$fields = $xml->field;
		foreach($fields as $f){
			$form .= $this->line($f);
			if($f['help']!=''){
				$form .= "<p class='help-block'>".$f['help']."</p>";
			}
		}
		$form .= "</form>\n";
		$form .= "<script type='text/javascript' >\n";
		$form .= "var frmvalidator  = new Validator('form');\n";
		$form .= "frmvalidator.EnableOnPageErrorDisplay();\nfrmvalidator.EnableMsgsTogether();\n";
		//$form .= $this->add_validation($xml);
		$form .= "</script>\n";
		return($form);
	}
	private function line($f){
		$field = "".$f['name'];
		if(is_array($this->ar) && $this->ar[0][$field] != ''){
			$f['default'] = $this->ar[0][$field];
		}
		switch($f['type']):
		case 'usr_id';
		$input .="<input type='hidden' name='".$f['name']."' value='".$_SESSION['usr']['id']."' />\n";
		return($input);
		break;
		case 'hidden';
		$input .= "<input type='hidden' name='".$f['name']."' value='".$f['default']."' />\n";
		return($input);
		break;
		endswitch;

		$input = "<label for='".$f['name']."'>".$f['label'].":</label>\n";
		switch($f['type']):
		case 'password';
		case 'file';
		case 'text';
		$input .= "<input type='".$f['type']."' name='".$f['name']."' value='".$f['default']."' />\n";
		break;
		case 'date';
		$today = ($f['default'] == '' ? date("Y\-m\-d") : $f['default']);
		$input .= "<input type='text' name='".$f['name']."' id='".$f['name']."' value='".$today."' data-date='".$today."' data-date-format='yyyy-mm-dd' />\n<script>$('#".$f['name']."').datepicker();</script>";
		break; 
		case 'textarea';
		$input .= "<textarea class='input-xlarge ' name='".$f['name']."' id='".$f['name']."' >".$f['default']."</textarea>\n";
		break; 
		case 'redactor';
		$input .= "<textarea class='input-xlarge ' name='".$f['name']."' id='".$f['name']."' >".$f['default']."</textarea>\n
			<script type='text/javascript'> $(document).ready(function(){ $('#".$f['name']."').redactor({ focus: true });	}); </script>\n";
		break; 
		case 'select';
		$input .= "<select  name='".$f['name']."' >";
		if(isset($f['table'])){
			if(isset($f['field'])){ $t2 = $f['field']; }else{ $t2 = 'name';}
			if($f['restriction']=='usr_id'){ $restriction = " usr_id = '".$_SESSION['usr']['id']."'" ;}
			if(isset($f["condition"])){$f["condition"] = ' AND '.$f["condition"];}	
			$t1 = "SELECT id as id, $t2 as text FROM ".$f['table']." WHERE 1 = 1  ".$f["condition"]." ".$restriction;
			$t1 = $this->q2ar($t1);
			if(is_array($t1)){
				$input .= $this->option($t1,$f['name']);
			}
		}elseif(isset($f->option)){
			foreach($f->option as $op){
				if(is_array($this->ar)){
					if($this->ar[0][trim($name)] == $option['id'] ){
						$selected = 'selected';	
					}else{ 
						$selected='';
					}
				}else { $selected=''; }
				$input .= '<option value="'.$op['value'].'" '.$selected .' >'.$op['text']."</option>\n";
			}
		}
		$input .= "</select>\n";
		break;
		case 'check';
		$input .= "";
		if(isset($f['table']) && !isset($f['relation'])){
			if(isset($f['field'])){ $t2 = $f['field']; }else{ $t2 = 'name';}
			$t1 = "SELECT id as id, $t2 as text FROM ".$f['table']." ".$f["condition"];
			$t1 = $this->q2ar($t1);
			$input .= $this->check($t1,trim($f['name']));
		}
		case 'radio';
		break;
		case 'button';
		case 'submit';
		return("<label></label><input type='submit' name='".$f['name']."' value='".$f['default']."' class='".$f['class']." '/>\n");
		break;
		break;
		endswitch;
		$input .= "<div class='span12' id='form_".$f['name']."_errorloc' class='errormsg' ></div>\n";
		return($input);
	}
	public function option($options,$name = ''){
		$option_list = '<option value=0 >Selecciona...</option>'."\n";
		foreach($options as $option){
			if(is_array($this->ar)){
				if($this->ar[0][trim($name)] == $option['id'] ){
					$selected = 'selected';	
				}else{ 
					$selected='';
				}
			}else { $selected=''; }
			$option_list .= "<option value='".$option['id']."' ".$selected." >".$option['text']."</option>\n";	
		}
		return($option_list);
	}
	public function check($array,$name){
		$name = substr($name,0,-2);
		foreach($array as $l){
			if( !in_array( "'".$l['id']."'" , $this->ar[0][$name] ) ){
				$selected = '';
			}else{ 
				$selected = ' checked ';	
			}
			$line .= "<tr><td><input type=checkbox name='".$name."[]' value='".$l['id']."' ".$selected." /></td><td>".$l['text']."</td></tr>\n";	
		}
		return($line);
	}

}?>

