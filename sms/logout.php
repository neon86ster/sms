<?php
session_start();
$root = $_SERVER["DOCUMENT_ROOT"];

require_once("$root/include/config.php");
require_once("$root/include/secure.inc.php");
$object = new secure();
$object->logout();
header("location: index.php");
?>