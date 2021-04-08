<?
include("../../../include.php");
$_COOKIE["topicdir"] = '<a href="javascript:;" onclick="gotoURL(\'../../index.php\')" class="top_menu_link">Preferences </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../index.php\')" class="top_menu_link">Update_field </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../general/index.php\')" class="top_menu_link">General </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'index.php\')" class="top_menu_link">Booking Party </a> > ';
if(isset($_POST["id"])){
	$_COOKIE["topic"] = 'Edit Booking Party Information';
} else if(!isset($_POST["id"])) {
	$_COOKIE["topic"] = 'Add Booking Party Information';
}
$_COOKIE["back"] = 'index.php';

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
	$city_id=$_POST["cityId"];
	$category_id=$_POST["bpCategoryId"];
	$name = $obj->setActive($_POST,$filename,false);
	if($name!=false){
		if($_REQUEST["active"]==1){
			$successmsg="$name is active!!";
		}else{
			$successmsg="$name is inactive!!";
		}
		$order=$_POST["order"];
		$page=$_POST["page"];
		$search=$_POST["where"];
		$successmsg.="&where=$search&order=$order&page=$page";
		$successmsg.="&show_inactive=$showInactive&bpCategoryId=$category_id&cityId=$city_id";
		header("Location: manage_bp.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
}
if($_POST["phpSql"]==""){
	$showInactive=$_POST["show_inactive"];
	$order=$_POST["order"];
	$page=$_POST["page"];
	$search=$_POST["where"];
	$cityId=$_POST["cityId"];
	$bp_category_id=$_POST["bpCategoryId"];
	$_POST["phpSql"]="&bpCategoryId=$bp_category_id&cityId=$cityId&show_inactive=$showInactive&where=$search&order=$order&page=$page";
}
if($_POST["add"] == " save change " && $hasSession){
	$id = $obj->readToUpdate($_POST,$filename);
	if($id){
		$successmsg="Update data complete!!".$_POST["phpSql"];
		header("Location: manage_bp.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
} else if($_POST["add"]==" add " && $hasSession) {
	$id = $obj->readToInsert($_POST,$filename);
	if($id){
		$successmsg="Insert data complete!!";
		header("Location: manage_bp.php?msg=$successmsg");
	} else {
		$errormsg = $obj->getErrorMsg();
	}
} else {
	header("add_bp.php");
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
					<legend><b>Booking Company Information</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style='overflow:auto'>
                    <tbody><tr>
                    	<td valign="bottom">
                        <div style="vertical-align:inherit">
                          <? 
							if(isset($_POST["id"])){
									$xml = "<command>" .
									"<table>al_bookparty</table>" .
									"<where name='bp_id' operator='='>".$_POST["id"]."</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else if(!isset($_POST["id"])) {
								echo $obj->gFormInsert('al_bookparty',$filename);	
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
					<input name="id" id="id" type="hidden" value="<?=$_POST["id"]?>">
					<input type="hidden" name="phpSql" id="phpSql" value="<?=$_POST["phpSql"]?>"/>
					<input name="add" id="add" type="submit" size="" value="<?=(isset($_POST["id"]))?" save change ":" add "?>" onClick='<?=(isset($_POST["id"]))?"set_editData(\"al_bookparty\",".$_POST["id"].")":"set_insertData(\"al_bookparty\")"?>' >&nbsp; 
					<input name="cancel" id="cancel" type="submit" size="" value=" cancel " onClick="getReturnText('manage_bp.php','<?=$_POST["phpSql"]?>','tableDisplay');" >  
                </fieldset>
			</div>
		</td>
    </tr>
</table>
</td>
</tr>
</table>
	<div class="hiddenbar"><img id="spLine" src="../../../images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('../../../images')"/></div>