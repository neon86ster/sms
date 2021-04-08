<?
session_start();
$_COOKIE["topicdir"] = '<a href="javascript:;" onclick="gotoURL(\'../../index.php\')" class="top_menu_link">Preferences </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../index.php\')" class="top_menu_link">Update_field </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../massagetype/index.php\')" class="top_menu_link">Massage Type </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'index.php\')" class="top_menu_link">Bath </a> > ';
if(isset($_POST["id"])){
	$_COOKIE["topic"] = 'Edit Bath Infomation';
} else if(!isset($_POST["id"])) {
	$_COOKIE["topic"] = 'Add Bath Infomation';
}
$_COOKIE["back"] = 'index.php';

include("../../../include.php");
require_once("formdb.inc.php");
require_once("secure.inc.php");
$scObj = new secure();
$obj = new formdb();

$errormsg ="";
$hasSession = true;
$ownUserId=$_REQUEST["ownUserId"];
if(!$scObj->checkLogin()){
	$hasSession = false;
	$errormsg="Can't update data because session timeout. Please login and try again.";
	//header("Location: index.php");
}

$obj->setDebugStatus(false);
$filename = '../object.xml';

if($_POST["method"]=="setactive" && $hasSession){
	$sql = "";
	$showInactive=$_POST["show_inactive"];
	$name = $obj->setActive($_POST,$filename,false);
	if($name!=false){
		if($_REQUEST["active"]==1){$successmsg="$name is active!!";}else{$successmsg="$name is inactive!!";}
		$order=$_POST["order"];
		$page=$_POST["page"];
		$search=$_POST["where"];
		$successmsg.="&where=$search&order=$order&page=$page";
		$successmsg.="&show_inactive=".$showInactive;
		//echo $successmsg.$_POST["where"];
		header("Location: manage_bath.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
}
if($_POST["phpSql"]==""){
	$order=$_POST["order"];
	$page=$_POST["page"];
	$showInactive=$_POST["show_inactive"];
	$search=$_POST["where"];
	$_POST["phpSql"]="&show_inactive=$showInactive&where=$search&order=$order&page=$page";
}
if($_POST["add"] == " save change " && $hasSession){
	$id = $obj->readToUpdate($_POST,$filename);
	if($id){
		$successmsg="Update data complete!!".$_POST["phpSql"];
		header("Location: manage_bath.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
} else if($_POST["add"]==" add "  && $hasSession) {
	$id = $obj->readToInsert($_POST,$filename);
	if($id){
		$obj->setResult("update db_trm set trm_category_id=1 where trm_id=".$id);
		$successmsg="Insert data complete!!";
		header("Location: manage_bath.php?msg=$successmsg");
	} else {
		$errormsg = $obj->getErrorMsg();
	}
} else {
	header("add_bath.php");
}
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
					<legend><b>Bath Infomation</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style='overflow:auto'>
                    <tbody><tr>
                    	<td valign="bottom">
                        <div style="vertical-align:inherit">
                          <? 
							if(isset($_POST["id"])){
									$xml = "<command>" .
									"<table>db_trm</table>" .
									"<where name='trm_id' operator='='>".$_POST["id"]."</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else if(!isset($_POST["id"])) {
								echo $obj->gFormInsert('db_trm',$filename);	
							}
 						?>
                        </div></td>
                    </tr>
                    </tbody></table>
                </fieldset>
                <fieldset>
					<legend> </legend>
					<br/>
					<input name="ownUserId" id="ownUserId" type="hidden" value="<?=$_REQUEST["ownUserId"]?>" >&nbsp;
					<input name="trmCategoryId" id="trmCategoryId" type="hidden" value="1">
					<input name="id" id="id" type="hidden" value="<?=$_POST["id"]?>">
					<input type="hidden" name="phpSql" id="phpSql" value="<?=$_POST["phpSql"]?>"/>
					<input name="add" id="add" type="submit" size="" value="<?=(isset($_POST["id"]))?" save change ":" add "?>" onClick='<?=(isset($_POST["id"]))?"set_editData(\"db_trm1\",".$_POST["id"].")":"set_insertData(\"db_trm1\")"?>' >&nbsp; 
					<input name="cancel" id="cancel" type="submit" size="" value=" cancel " onClick="getReturnText('manage_bath.php','<?=$_POST["phpSql"]?>','tableDisplay');" >  
                </fieldset>
			</div>
		</td>
    </tr>
</table>
</td>
</tr>
</table>
	<div class="hiddenbar"><img id="spLine" src="../../../images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('../../../images')"/></div>