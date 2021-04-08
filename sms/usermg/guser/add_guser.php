<?
session_start();
$_COOKIE["topicdir"] = '<a href="javascript:;" onclick="gotoURL(\'../../spamg/index.php\')" class="top_menu_link">Preferences </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../index.php\')" class="top_menu_link"> User_Permission </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'index.php\')" class="top_menu_link"> Group </a> > ';
if(isset($_POST["id"])){
	$_COOKIE["topic"] = 'Edit User Group';
} else if(!isset($_POST["id"])) {
	$_COOKIE["topic"] = 'Add User Group';
}
$_COOKIE["back"] = 'index.php';

include("../../include.php");
require_once("formdb.inc.php");
require_once("secure.inc.php");
$scObj = new secure();
$obj = new formdb();

$errormsg ="";
$hasSession = true;
if(!$scObj->checkLogin()){
	$hasSession = false;
	$errormsg="Can't update data because session timeout. Please login and try again.";
	//header("Location: index.php");
}

$obj->setDebugStatus(false);
$filename = '../user.xml';
if($_POST["method"]=="setactive" && $hasSession){
	$sql = "";
	$showInactive=$_POST["show_inactive"];
	$showDetail= $_POST["show_detail"];
	$name = $obj->setActive($_POST,$filename,false);
	if($name!=false){
		if($_REQUEST["active"]==1){$successmsg="$name is active!!";}else{$successmsg="$name is inactive!!";}
		$order=$_POST["order"];
		$page=$_POST["page"];
		$search=$_POST["where"];
		$successmsg.="&where=$search&order=$order&page=$page";
		$successmsg.="&show_inactive=".$showInactive."&show_detail=".$showDetail;
		header("Location: manage_guser.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
}
if($_POST["phpSql"]==""){
	$order=$_POST["order"];
	$page=$_POST["page"];
	$showInactive=$_POST["show_inactive"];
	$showDetail= $_POST["show_detail"];
	$search=$_POST["where"];
	$_POST["phpSql"]="&show_inactive=$showInactive&cityId=$city_id&show_detail=$showDetail&where=$search&order=$order&page=$page";
}
if($_POST["add"] == " save change " && $hasSession){
	$id = $obj->readToUpdate($_POST,$filename);
	if($id){
		$successmsg="Update data complete!!".$_POST["phpSql"];
		header("Location: manage_guser.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
} else if(isset($_POST["add"]) && $hasSession) {
	$id = $obj->readToInsert($_POST,$filename);
	if($id){
		$successmsg="Insert data complete!!";
		header("Location: manage_guser.php?msg=$successmsg");
	} else {
		$errormsg = $obj->getErrorMsg();
	}
} else {
	header("add_guser.php");
}
$pagexml = "<command>".
			"<table>s_pagename</table>".
 			"<order>page_id</order>".
			"<where name='active' operator='='>1</where>" .
		"</command>";
$prs = $obj->getRsXML($pagexml,$filename);
?>
<table class="main" cellspacing="0" cellpadding="0" height="100%" width="100%">
<tr><td width="6px" height="100%" align="center" rowspan="2" class="hidden_bar">&nbsp;</td>
<td height="64" valign="top" style="">
	<? include "mainhead.php"; ?>
</td></tr>
<tr><td valign="top" style="margin-top:0px;margin-left:0px">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td class="content" width="100%">
    		<div id="showerrormsg" <? if($errormsg==""&&$_POST["add"]==false){?>style="display:none"<? } else {?>style="display:block"<? }?>>
    			<table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    				<tr>
    					<td ><b><font style="color:#ff0000;">Error message: </font></b><?=$errormsg ?></td>
    				</tr>
    			</table>
    		</div>
        	<div>
    			<fieldset>
					<legend><b>User groups</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style='overflow:auto'>
                    <tbody><tr>
                    	<td valign="bottom">
                        <div style="vertical-align:inherit">
                          <? 
							if(isset($_POST["id"])){
									$xml = "<command>" .
									"<table>s_group</table>" .
									"<where name='group_id' operator='='>".$_POST["id"]."</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else if(!isset($_POST["id"])) {
								echo $obj->gFormInsert('s_group',$filename);	
							}
 						?>
                        </div></td>
                    </tr>
                    </tbody></table>
                </fieldset>
                <fieldset>
					<legend> </legend>
					<br/>
					<input name="id" id="id" type="hidden" value="<?=$_POST["id"]?>">
					<input type="hidden" name="phpSql" id="phpSql" value="<?=$_POST["phpSql"]?>"/>
					<input name="add" id="add" type="submit" size="" value="<?=(isset($_POST["id"]))?" save change ":" add "?>" onClick='<?=(isset($_POST["id"]))?"set_editData(\"s_group\",".$_POST["id"].")":"set_insertData(\"s_group\")"?>' >&nbsp; 
					<input name="cancel" id="cancel" type="submit" size="" value=" cancel " onClick="getReturnText('manage_guser.php','<?=$_POST["phpSql"]?>','tableDisplay');" >  
                </fieldset>
			</div>
		</td>
    </tr>
</table>
</td>
</tr>
</table>
	<div class="hiddenbar"><img id="spLine" src="../../images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('../../images')"/></div>