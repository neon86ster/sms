<?php
include("include.php");
require_once("secure.inc.php");
$object = new secure();

if(!$object->checkLogin()){
	?>
	<script>
		parent.location = "login.php";
	</script>
	<?
}else if($object->login($user, $pass)){
	header("location:home.php");
	}else{
		session_destroy();
	?>
	<script>
	
		parent.location = "login.php";
	</script>
	<?
		
	}
/*-------------old---------	
	}else{
	header("location: home.php");
}*/
?>