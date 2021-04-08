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
}else{
	header("location:home.php");
}
?>