<?php 
$a[0]['name'] = 'title'; //text
$a[11]['name'] = 'img_url';//file
$a[5]['name'] = 'description'; //select, options img_url
$a[7]['name'] = 'active'; //hidden default Y
$a[6]['name'] = 'lang'; //hidden default es
$a[10]['name'] = 'tags';


$a[0]['type'] = 'text'; //text
$a[5]['type'] = 'textarea'; //hidden default Y
$a[6]['type'] = 'hidden'; //hidden default es
$a[7]['type'] = 'hidden'; //select, options img_url
$a[10]['type'] = 'text'; //text
$a[11]['type'] = 'file'; //text

$a[0]['label'] = 'Titulo'; //text
$a[1]['label'] = 'Contenido'; //img url
$a[5]['label'] = ''; 
$a[6]['label'] = '';
$a[10]['label'] = 'Tags<br/>(Palabras que describan el post, separadas por comas)';
$a[11]['label'] = 'Imagen:';

$a[6]['default'] = 'ES'; //hidden default ess
$a[7]['default'] = 1; //hidden default ess
?>
