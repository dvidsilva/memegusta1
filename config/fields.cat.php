<?php 


$a[0]['name'] = 'name'; //text

$a[0]['type'] = 'text'; //text
$a[1]['type'] = 'file'; //img url
$a[5]['type'] = 'hidden'; //hidden default Y
$a[6]['type'] = 'hidden'; //hidden default es
$a[7]['type'] = 'select'; //select, options img_url
 
$a[9]['type'] = 'hidden'; //text
$a[10]['type'] = 'text'; //text

$a[0]['label'] = 'Titulo'; //text
$a[1]['label'] = 'Contenido'; //img url
$a[7]['label'] = 'Tipo de Post'; //select, options img_url //Tipo de Publicaci&oacute;n
$a[5]['label'] = ''; 
$a[6]['label'] = '';

$a[10]['label'] = 'Tags<br/>(Palabras que describan el post, separadas por comas)';

$a[5]['default'] = 'Y'; //hidden default Y
$a[6]['default'] = 'ES'; //hidden default es



$a[7]['options'][0]['value'] = 2;
$a[7]['options'][0]['text'] = 'Subir una Imagen';
$a[7]['options'][2]['value'] = 3;
$a[7]['options'][2]['text'] = 'Url de YouTube';
$a[7]['options'][3]['value'] = 4;
$a[7]['options'][3]['text'] = 'Texto/confesiones';







?>
