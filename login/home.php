<?php

//Always place this code at the top of the Page
session_start();
if (!isset($_SESSION['id'])) {
    // Redirection to login page twitter or facebook
 	header("location: ../index.php");
}else{
	header("location: ../index.php");
}
?>
