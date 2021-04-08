<?
ini_set("memory_limit","-1");
?>
<?php
session_start();
include("../../include.php");
require_once("account.inc.php");
require_once("date.inc.php");
require_once("secure.inc.php");

$obj = new account();
$scObj = new secure();
$chkPageView=false;
if($scObj->checkLogin()){
	$chkPageView=true;
}
$obj->setErrorMsgColor("#ff0000");
if($_REQUEST["pagenum"] && $chkPageView){
	$pagenum = $obj->getParameter("pagenum");
	$oldsrs = explode(",",$_REQUEST["oldsrs"]);
	$srs = explode(",",$_REQUEST["srs"]);
	$branch = $_REQUEST["branchid"];
	$srs = $obj->reorderSr($srs);
	$chkresetpagenum = $obj->resetPagenum($oldsrs,$srs,$pagenum,$branch);
	if($chkresetpagenum){
		$chkrs = $obj->updatePagenum($srs,$pagenum);
		if($chkrs){
			echo "<span style='color:#3875d7'>Update Success!!</span>";
		}else{
			echo $obj->getErrorMsg();
		}
	}
	else{
		echo $obj->getErrorMsg();
	}
}else{
	echo "<span style='color:#ff0000'>Can't update data because session timeout. Please login and try again.!!</span>";
}

?>
