<?
session_start();
$_COOKIE["topicdir"] = '<a href="javascript:;" onclick="gotoURL(\'../../index.php\')" class="top_menu_link">Preferences </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../index.php\')" class="top_menu_link">Update_field </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../massagetype/index.php\')" class="top_menu_link">Massage Type </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'index.php\')" class="top_menu_link">Package </a> > ';
if(isset($_POST["id"])){
	$_COOKIE["topic"] = 'Edit Package Infomation';
} else if(!isset($_POST["id"])) {
	$_COOKIE["topic"] = 'Add Package Infomation';
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
		header("Location: manage_package.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
}
$last_ctrmid = $_POST["lastgroupid"];
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
		$sql = "delete from db_trm_package where package_id=".$_POST["package_id"];
		$obj->setResult($sql);
		for($i=1; $i<=$last_ctrmid; $i++) {
			$group =$_POST["ctrmrs".$i];	
			if($group!=0){
				$sql = "insert into db_trm_package(package_id,trm_id) values(".$_POST["package_id"].",".$group.")";
				//echo $sql."<br/>";
				$obj->setResult($sql);
			}
		}
		for($j=0; $j<$_POST["appenstrmcnt"]; $j++) {
			$group =$_POST["appendtrmrs".$j];	
			if($group!=0){
				$sql = "insert into db_trm_package(package_id,trm_id,trm_package_sign) values(".$_POST["package_id"].",".$group.",1)";
				//echo $sql."<br/>";
				$obj->setResult($sql);
			}
		}
		$successmsg="Update data complete!!".$_POST["phpSql"];
		header("Location: manage_package.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
} else if($_POST["add"]==" add " && $hasSession) {
	$id = $obj->readToInsert($_POST,$filename);
	if($id){
		for($i=1; $i<=$last_ctrmid; $i++) {
			$group =$_POST["ctrmrs".$i];	
			if($group!=0){
				$sql = "insert into db_trm_package(package_id,trm_id) values(".$id.",".$group.")";
				echo $sql."<br/>";
				$obj->setResult($sql);
			}
		}
		$successmsg="Insert data complete!!";
		header("Location: manage_package.php?msg=$successmsg");
	} else {
		$errormsg = $obj->getErrorMsg();
	}
} else {
	header("add_package.php");
}
if($_POST["tadd"]=="tadd"  && $hasSession){
	$errormsg = "";
	if($_POST["newtrm"]!=0){
		$sql = "insert into db_trm_package(package_id,trm_id,trm_package_sign) values(".$_POST["package_id"].",".$_POST["newtrm"].",1)";
		//echo $sql."<br/>";
		$obj->setResult($sql);
	} else {
		$errormsg = "Please check treatment value!!";
	}
}
$ctrmxml = "<command>" .
				"<table>db_trm_category</table>".
				"<order>trm_category_id asc</order>".
			 "</command>";
$ctrmrs = $obj->getRsXML($ctrmxml,$filename,$debug);
$lastctrmid = 0;
$appendrows = 0;
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
					<legend><b>Package Infomation</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style='overflow:auto'>
                    <tbody><tr>
                    	<td valign="bottom">
                        <div style="vertical-align:inherit">
                          <? 
							if(isset($_POST["id"])){
									$xml = "<command>" .
									"<table>db_package</table>" .
									"<where name='package_id' operator='='>".$_POST["id"]."</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else if(!isset($_POST["id"])) {
								echo $obj->gFormInsert('db_package',$filename);	
							}
 						?>
                        </div></td>
                    </tr>
                    </tbody></table>
                </fieldset>
                <fieldset>
					<legend><b>Treatment Infomation</b></legend>
					<? if(isset($_POST["id"])){?>
						Add Treatment: &nbsp;
						<? $atrmxml = "<command>" .
											"<table>db_trm</table>".
											"<order>trm_name asc</order>".
											"</command>";
								$atrmrs = $obj->getRsXML($atrmxml,$filename,$debug); 
								$textout = "<select id=\"newTrm\" name=\"newTrm\" style=\"width:115px;\"> \n";
									$textout .= "<option value=\"0\">-- select --</option> \n";
								for($j=0;$j<$atrmrs["rows"];$j++){
									$textout .= "<option value=\"".$atrmrs[$j]["trm_id"]."\" >".$atrmrs[$j]["trm_name"]."</option> \n";
								}
								$textout .= "</select>";
								echo $textout;
							?>&nbsp;&nbsp;
						<input name="tadd" id="tadd" type="button" size="" value="Add" onclick="addTrm();" /><br/>
					<?}?>
					<table class="main_table_list" cellspacing="0" cellpadding="0">
	                    <tr>
		                    <td class="mainthead" width="20%">Treatment Category</td>
		                    <td class="mainthead">Treatment Name</td>
	                   	</tr>
	                <?  for($i=0; $i<$ctrmrs["rows"]; $i++) {
	                		if($lastctrmid<$ctrmrs[$i]["trm_category_id"]){$lastctrmid = $ctrmrs[$i]["trm_category_id"];}
	                		echo ($i%2==0)?'<tr class="content_list">':'<tr class="content_list1">';
	                ?>		
	                    	<td>&nbsp;&nbsp;<?=$ctrmrs[$i]["trm_category_name"]?></td>
							<td>
							<? $trmxml = "<command>" .
											"<table>db_trm</table>".
											"<where name='trm_category_id' operator='='>".$ctrmrs[$i]["trm_category_id"]."</where>".
											"<order>trm_name asc</order>".
											"</command>";
								$trmrs = $obj->getRsXML($trmxml,$filename,$debug); 
								$textout = "<select id=\"ctrmrs".$ctrmrs[$i]["trm_category_id"]."\" style=\"width:115px;\"> \n";
								$textout .= "<option value=\"0\">-- select --</option> \n";
								for($j=0;$j<$trmrs["rows"];$j++){
									$chktrmxml = "<command>" .
												"<table>db_trm_package</table>".
												"<where name='package_id' operator='='>".$_POST["id"]."</where>".
												"<where logic='AND' name='trm_package_sign' operator='='>0</where>".
												"<where logic='AND' name='trm_id' operator='='>".$trmrs[$j]["trm_id"]."</where>".
												"</command>";
									$chktrmrs = $obj->getRsXML($chktrmxml,$filename,$debug);
									if($chktrmrs["rows"]>0){$selected="selected";} else {$selected="";}
									$textout .= "<option value=\"".$trmrs[$j]["trm_id"]."\" $selected >".$trmrs[$j]["trm_name"]."</option> \n";
								}
								$textout .= "</select>";
								echo $textout;
							?>
							</td>
	                    </tr>
	                 <? } ?>
	                 
	                 <? if(isset($_POST["id"])){?>
	                 <?  $achktrmxml = "<command>" .
										"<table>db_trm_package</table>".
										"<where name='package_id' operator='='>".$_POST["id"]."</where>".
										"<where logic='AND' name='trm_package_sign' operator='='>1</where>".
										"</command>";
						 $achktrmrs = $obj->getRsXML($achktrmxml,$filename,$debug);
						 if($achktrmrs["rows"]){
						 	$appendrows = $achktrmrs["rows"];
						 }
						 
						 if($achktrmrs["rows"]>0){
						 	for($i=0; $i<$achktrmrs["rows"]; $i++) {
								$textout = ($i%2==0)?'<tr class="content_list">':'<tr class="content_list1">';
								$textout .=	'<td>&nbsp;&nbsp;Treatment</td>';
								$atrmxml = "<command>" .
											"<table>db_trm</table>".
											"<order>trm_name asc</order>".
											"</command>";
								$atrmrs = $obj->getRsXML($atrmxml,$filename,$debug); 
								$textout .= "<td><select id=\"appendtrmrs".$i."\" style=\"width:115px;\"> \n";
								$textout .= "<option value=\"0\">-- select --</option> \n";
								for($j=0;$j<$atrmrs["rows"];$j++){
									$achkxml = "<command>" .
												"<table>db_trm_package</table>".
												"<where name='package_id' operator='='>".$_POST["id"]."</where>".
												"<where logic='AND' name='trm_package_sign' operator='='>1</where>".
												"<where logic='AND' name='trm_id' operator='='>".$achktrmrs[$i]["trm_id"]."</where>".
												"</command>";
									$achkrs = $obj->getRsXML($achkxml,$filename,$debug);
									if($achkrs[0]["trm_id"]==$atrmrs[$j]["trm_id"]){$selected="selected";} else {$selected="";}
									$textout .= "<option value=\"".$atrmrs[$j]["trm_id"]."\" $selected >".$atrmrs[$j]["trm_name"]."</option> \n";
								}
								$textout .= "</select>";
								$textout .= '</td>';
								$textout .= '</tr>';
								echo $textout;
						 	}
						 }
	                   }
	                 ?>
                    </table>
                </fieldset>
                <fieldset>
					<legend> </legend>
					<br/>
					<input name="ownUserId" id="ownUserId" type="hidden" value="<?=$_REQUEST["ownUserId"]?>" >&nbsp;
					<input name="id" id="id" type="hidden" value="<?=$_POST["id"]?>">
					<input name="add" id="add" type="button" size="" value="<?=(isset($_POST["id"]))?" save change ":" add "?>" onClick='<?="javascript:set_insertpackageData($lastctrmid,$appendrows);"?>' >&nbsp;
					<input type="hidden" name="phpSql" id="phpSql" value="<?=$_POST["phpSql"]?>"/> 
					<input name="cancel" id="cancel" type="button" size="" value=" cancel " onClick="getReturnText('manage_package.php','<?=$_POST["phpSql"]?>','tableDisplay');" >  
                </fieldset>
			</div>
		</td>
    </tr>
</table>
</td>
</tr>
</table>
	<div class="hiddenbar"><img id="spLine" src="../../../images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('../../../images')"/></div>