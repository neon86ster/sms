<?
session_start();
$_COOKIE["topicdir"] = '<a href="javascript:;" onclick="gotoURL(\'../../index.php\')" class="top_menu_link">Preferences </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../index.php\')" class="top_menu_link">Update_field </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../general/index.php\')" class="top_menu_link">General </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'index.php\')" class="top_menu_link">Booking Commission </a> > ';
if(isset($_POST["id"])){
	$_COOKIE["topic"] = 'Edit Booking Commission Information';
} else if(!isset($_POST["id"])) {
	$_COOKIE["topic"] = 'Add Booking Commission Information';
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
$table=$obj->getParameter("table");
if($_POST["phpSql"]==""){
	$showInactive=$_POST["show_inactive"];
	$order=$_POST["order"];
	$page=$_POST["page"];
	$search=$_POST["where"];
	$cityId=$_POST["cityId"];
	$_POST["phpSql"]="&cityId=$cityId&show_inactive=$showInactive&where=$search&order=$order&page=$page";
}
if($_POST["add"] == " save change " && $hasSession){
	$accid=$_POST["acc_id"];
	$accname=$_POST["acc_name"];
	$cityid=$_POST["city_id"];
	$cmspercent=$_POST["cmspercent"];
	if($_POST["tablename"]=="al_accomodations"){$sql="update al_accomodations set acc_name='$accname', cmspercent='$cmspercent', city_id=$cityid where acc_id=$accid ";}
	else if($_POST["tablename"]=="al_bookparty"){$sql="update al_bookparty set bp_name='$accname', bp_cmspercent='$cmspercent', city_id=$cityid where bp_id=$accid ";}
	$id = $obj->setResult($sql);
	if($id){
		$successmsg="Update data complete!!".$_POST["phpSql"];
		header("Location: manage_hotelcms.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
} else {
	header("add_hotelcms.php");
}
if($table=="al_accomodations"){
	$idfield = "acc_id";
}else{
	$idfield = "bp_id";
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
					<legend><b>Booking Commission Information</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style='overflow:auto'>
                    <tbody><tr>
                    	<td valign="bottom">
                        <div style="vertical-align:inherit">
                          <? 		if($table=="al_accomodations"){
										$xml = "<command>" .
										"<table>$table</table>" .
										"<field>*,'$table' as tablename</field>" .
										"<where name='$idfield' operator='='>".$_POST["id"]."</where>" .
										"</command>";
                         			}else{
										$xml = "<command>" .
										"<table>$table</table>" .
										"<field>bp_id as acc_id,bp_name as acc_name,bp_cmspercent as cmspercent,city_id,bp_active as acc_active,'$table' as tablename</field>" .
										"<where name='$idfield' operator='='>".$_POST["id"]."</where>" .
										"</command>";
                       			    }
									$rs = $obj->getRsXML($xml,$filename,$debug);
									$f = simplexml_load_file($filename);
									$element = $f->table->hotelcms;
									$textout = "<table> \n";
									foreach($element->field as $ff) {
										$name = $ff["name"];
										
										if($ff["defaultvalue"]=="__get")
											$defaultvalue = $_GET["$name"];
										else if($ff["defaultvalue"]=="__post")
											$defaultvalue = $_POST["$name"];
										else
											$defaultvalue = $ff["defaultvalue"];
										
										if(!isset($defaultvalue)){$defaultvalue = $rs[0]["$name"];}
										if($ff["showinform"]!="no" && $ff["formtype"]!="hidden" && $ff["showinformEdit"]!="no") {
											$textout .= "<tr> \n";
	 										$textout .= "<td valign='top'>".$ff["formname"];
	 										if($ff["prior"]){$textout .= "<font style='color:#ff0000''> ".$ff["prior"]."</font> ";}
											$textout .= "</td> \n";
							 				$textout .= "<td valign='top'> \n";
							 				if(($ff["formtype"]=="text" || $ff["formtype"]=="file" || $ff["formtype"]=="submit" || $ff["formtype"]=="reset" || $ff["formtype"]=="button")&&$ff["updatein"]=="") {
							 					$textout .= "<input id='$prename".$ff["name"]."' type='".$ff["formtype"]."' name='$prename".$ff["name"]."' maxlength='".$ff["maxlength"]."' size='".$ff["size"]."' value='$defaultvalue' ".$ff["javascript"]."$disabled> $hiddeninput";
							 				}else if($ff["formtype"]=="select") {
								 				//echo $ff["formtype"];
							 					$textout .= $obj->gSelectBox($ff,$filename,$defaultvalue,$debug);
							 				}
							 				$textout .= "</td> \n";
	 										$textout .= "</tr> \n";
										}else if($ff["formtype"]=="hidden"){
							 				$textout .= "<input name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" type=\"".$ff["formtype"]."\" value=\"".$defaultvalue."\"> \n";
							 			}else{
							 				$textout .= "<input name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" type=\"hidden\" value=\"".$defaultvalue."\"> \n";
							 			}	
									}
							 			$textout .= "<input name='formname' type='hidden' value=\"".$tbname."\" > \n";
 										$textout .= "</table> \n";
									echo $textout;
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
					<input name="add" id="add" type="submit" size="" value="<?=(isset($_POST["id"]))?" save change ":" add "?>" onClick='setedithotelcms("<?=$tbname?>","<?=$_POST["id"]?>")' >&nbsp; 
					<input name="cancel" id="cancel" type="submit" size="" value=" cancel " onClick="getReturnText('manage_hotelcms.php','<?=$_POST["phpSql"]?>','tableDisplay');" >  
                </fieldset>
			</div>
		</td>
    </tr>
</table>
</td>
</tr>
</table>
	<div class="hiddenbar"><img id="spLine" src="../../../images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('../../../images')"/></div>