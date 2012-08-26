<?PHP
class search extends dvid {
	public function search(){
		$a[][] = '';
		$r = $this->parse_template($a,'search.html');
		return($r);
	}
}

?>
