<?
$array[0]['menu'] = "
<li class='dropdown' ><a href='?' class='dropdown-toggle' data-toggle='dropdown' ><i class=' icon-chevron-left icon-white' ></i> Inicio<b class='caret'></b></a>
	<ul class='dropdown-menu'>
		<li><a href='?s=best'>Lo Mejor</a></li>
		<li><a href='?s=trendy'>Chevere</a></li>
		<li><a href='?s=all'>Todos</a></li>
	</ul>
</li>

<li><a href='?f=posts&a=rules' class='mitem'><i class=' icon-list-alt icon-white'></i> Reglas</a></li>
<li><a href='?f=meme&a=show_posts' class='mitem'><i class='icon-question-sign icon-white'></i>Meme</a></li>
<li><a href='?f=posts&a=add' class='mitem'><i class='icon-upload icon-white' ></i> Publicar</a></li>

";


if($site->isloggedin()==true){	
	$array[0]['menu'] .= "<li><a href='?f=usr&a=logout' ><i class=' icon-off icon-white'></i> Cerrar Sesi&oacute;n</a></li>";
	$array[0]['login'] = '';
}else{
	$array[0]['menu'] .= "<li><a href='?f=usr&a=login' class='mitem'>Iniciar Sesi&oacute;n
		</a></li><li><a href='?f=usr&a=signup' class='mitem'>Registrarse</a></li>";
	$array[0]['login'] = "<li><a href='./login/?login&oauth_provider=facebook'><img src='./media/memegusta.facebook.png'/></a></li>";
}
if($site->is_authorized()){
	$array[0]['menu'] .= "<li><a href='?f=meme&a=add' >Add Meme</a></li>";
}

$run = $site->action();
//$_SESSION['note']='prueba';
if(isset($_SESSION['note'])){
	$array[0]['note'] = '<div class="alert alert-warning">'.$_SESSION['note'].'</div>';
	unset($_SESSION['note']);
}else{
	$array[0]['note']= '';
}
?>
