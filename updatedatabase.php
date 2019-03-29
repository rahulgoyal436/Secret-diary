<?php
	session_start();
	if(array_key_exists("content",$_POST)) {
    	include("connection.php");
      	$query="update `users` set diary='".mysqli_real_escape_string($link,$_POST['content'])."' where id=".mysqli_real_escape_string($link,$_SESSION['id'])." limit 1";
      	mysqli_query($link,$query);
    }
?>