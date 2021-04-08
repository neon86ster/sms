<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("user.inc.php");
$userobj = new user();
$object->setDebugStatus(false);

// for return to the same page 
$pagepermission = $object->getParameter("pagepermission","");
$querystr = "&pageid=$pageid";
$add = $object->getParameter("add");
if($add == " save change " && $chkPageEdit){
	$rs = $userobj->update_s_pagename($pagepermission,false);
	if($rs){
		$successmsg="Update data complete!!";
		$successmsg.=$querystr;
		header("Location: index.php?msg=$successmsg");
	} else {
		$errormsg = "Can't update some page ";
		$errormsg .=$querystr;
		$errormsg .= "&pagepermission=$pagepermission";
		header("Location: index.php?errormsg=$errormsg");
	}
	
}
?>