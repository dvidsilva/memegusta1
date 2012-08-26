<?
/*Manage common sql operations**/
require_once('./form.php');
class foro extends dvid{
	public function __construct(){
		$this->table  = "forum_post";
		$this->action = "insert";
		$this->return = "I";
		$this->file   = "add.xml";
		$this->template = 'blank.html';
		$this->success = 'La Categoria fue creado exitosamente con id ';
	}
	public function add(){
		if(!isset($_POST['send'])){
			$form = new form;
			$form->xml = './model/foro/add.xml';
			$a[0]['content'] = $form->add();
			$c = $this->parse_template($a,$this->template);
			return($c);
		}else{
			$q = $this->form2query($this->action,$this->table);
			$q = $this->mysql($q,'I');
			$_SESSION['note'] = 'El post fue creado con éxito.';	
			return($q);
		}
	}
}
?>

