<?
session_start();
$_COOKIE["topicdir"] = '<a href="javascript:;" onclick="gotoURL(\'../../spamg/index.php\')" class="top_menu_link">Preferences </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../index.php\')" class="top_menu_link">User Permission </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'index.php\')" class="top_menu_link">User </a> > ';
if(isset($_POST["id"])){
	$_COOKIE["topic"] = 'Edit User';
} else if(!isset($_POST["id"])) {
	$_COOKIE["topic"] = 'Add User';
}
$_COOKIE["back"] = 'index.php';

include("../../include.php");
require_once("formdb.inc.php");
require_once("secure.inc.php");
$obj = new formdb();
$scObj = new secure();

$errormsg ="";
$hasSession = true;
if(!$scObj->checkLogin()){
	$hasSession = false;
	$errormsg="Can't update data because session timeout. Please login and try again.";
	//header("Location: index.php");
}
$scObj->setDebugStatus(false);
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
		header("Location: manage_user.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
}

$xml = "<command>".
			"<table>s_user</table>".
 			"<order>u_id</order>".
			"<where name='u_id' operator='='>".$_POST["id"]."</where>" .
		"</command>";
if(isset($_POST["id"])) {$rs = $obj->getRsXML($xml,$filename);}

//// For check is admin expert or not. By Ruck : 26-05-2009 ////
$isAdminExpert = $scObj->isAdminExpert();
$groupxml = "<command>".
              	"<table>s_group</table>".
    			"<order>group_name</order>".
				"<where name='active' operator='='>1</where>";
if(!$isAdminExpert){
	$groupxml .= "<where logic='AND' name='top_user' operator='='>0</where>";	
}
$groupxml .= "</command>";
$grs = $obj->getRsXML($groupxml,$filename);
$group =$_POST["grs"];	
$obj->setDebugStatus(false);

if($_POST["phpsql"]==""){
	$order=$_POST["order"];
	$page=$_POST["page"];
	$showInactive=$_POST["show_inactive"];
	$showDetail= $_POST["show_detail"];
	$search=$_POST["where"];
	$_POST["phpsql"]="&show_inactive=$showInactive&show_detail=$showDetail&where=$search&order=$order&page=$page";
}

if($_POST["add"] == " add "  && $hasSession){
	$last_groupid = $_POST["lastgroupid"];
	//$obj->setDebugStatus(true);
	$u_id = $obj->readToInsert($_POST,$filename);
	if($u_id){
		$rsvnchk_id = true;
		$previewdate = $obj->getParameter('previewdate');
		$viewdateafter = $obj->getParameter('viewdateafter');
		$preeditdate = $obj->getParameter('preeditdate');
		$editdateafter = $obj->getParameter('editdateafter');	
		$rsvnviewchk = $obj->getParameter('rsvnviewchk',0);
		$rsvneditchk = $obj->getParameter('rsvneditchk',0);
		if(is_numeric($previewdate)&&is_numeric($viewdateafter)&&$rsvnviewchk>0){		
				$sql = "insert into s_rsvn_chk(u_id,set_view,pre_viewdate,after_viewdate,set_edit,pre_editdate,after_editdate)  " .
						"values($u_id,1,$previewdate,$viewdateafter,0,0,0)";
				if(is_numeric($preeditdate)&&is_numeric($editdateafter)&&$rsvneditchk>0){
					$sql = "insert into s_rsvn_chk(u_id,set_view,pre_viewdate,after_viewdate,set_edit,pre_editdate,after_editdate)  " .
						"values($u_id,1,$previewdate,$viewdateafter,1,$preeditdate,$editdateafter)";
				}else{
					$errormsg .= "Please check your insert data!!";
					$rsvnchk_id = false;
				}
				$rsvnchk_id = $obj->setResult($sql);
		}else{
				$rsvnchk_id = false;
		}
		if($rsvnchk_id != false){
			for($i=1; $i<=$last_groupid; $i++) {
				if(isset($group[$i][0])){$g_id=$i;$set_view=1;$set_edit=0;}
				if(isset($group[$i][1])){$g_id=$i;$set_view=1;$set_edit=1;}
				if(isset($group[$i][0])||isset($group[$i][1])){
					$sql = "insert into s_ugroup(group_id,u_id,set_view,set_edit,l_lu_user,l_lu_date,l_lu_ip,active)  values($g_id,$u_id,$set_view,$set_edit,".$_SESSION["__user_id"].",Now(),'".$obj->getIp()."',1)";
					//echo $sql."<br />";
					$obj->setResult($sql);
				}
			}
		}else{
			$errormsg .= $obj->getErrorMsg();
		}
		if(str_replace(" ","",$errormsg) || $u_id){
			$successmsg="Insert data complete!!";
			header("Location: manage_user.php?msg=$successmsg");
		}
	} else {
			$errormsg = $obj->getErrorMsg();
	}
} else if($_POST["add"] == " save change "  && $hasSession){
	$last_groupid = $_POST["lastgroupid"];
	$sid = $obj->readToUpdate($_POST,$filename);
	$uid = $_POST["u_id"];
	if($sid){
		$chksql = "update s_user set c_lu_user=".$_SESSION["__user_id"].",c_lu_date=Now(),c_lu_ip='".$obj->getIp()."' where u_id=$uid";
		$cid=$obj->setResult($chksql);
		$rsvnchk_id=$scObj->chkRsvnLimit($uid);
		$previewdate = $obj->getParameter('previewdate');
		$viewdateafter = $obj->getParameter('viewdateafter');
		$preeditdate = $obj->getParameter('preeditdate');
		$editdateafter = $obj->getParameter('editdateafter');	
		$rsvnviewchk = $obj->getParameter('rsvnviewchk');
		$rsvneditchk = $obj->getParameter('rsvneditchk');
		if($rsvnchk_id){
			if($rsvnviewchk<=0){
				$sql = "delete from s_rsvn_chk where u_id=".$uid;
				$rsvnchkrs_id = $obj->setResult($sql);
			}
		}
		$rsvnchkrs_id=true;
		if(is_numeric($previewdate)&&is_numeric($viewdateafter)&&$rsvnviewchk>0){		
				if($rsvnchk_id==false){
					$sql = "insert into s_rsvn_chk(u_id,set_view,pre_viewdate,after_viewdate,set_edit,pre_editdate,after_editdate)  " .
							"values($uid,1,$previewdate,$viewdateafter,0,0,0)";
					if(is_numeric($preeditdate)&&is_numeric($editdateafter)&&$rsvneditchk>0){
						$sql = "insert into s_rsvn_chk(u_id,set_view,pre_viewdate,after_viewdate,set_edit,pre_editdate,after_editdate)  " .
							"values($uid,1,$previewdate,$viewdateafter,1,$preeditdate,$editdateafter)";
							
					}else{
						$errormsg = ("Please check your insert data!!");
						$rsvnchkrs_id = false;
					}
				}else{
					$sql = "update s_rsvn_chk set " .
							"set_view=1, " .
							"pre_viewdate=$previewdate," .
							"after_viewdate=$viewdateafter," .
							"set_edit=0," .
							"pre_editdate=0," .
							"after_editdate=0" .
							" where u_id=$uid";
					if($rsvneditchk>0&&is_numeric($preeditdate)&&is_numeric($editdateafter)){
						$sql = "update s_rsvn_chk set " .
								"set_view=1," .
								"pre_viewdate=$previewdate," .
								"after_viewdate=$viewdateafter," .
								"set_edit=1," .
								"pre_editdate=$preeditdate," .
								"after_editdate=$editdateafter" .
								" where u_id=$uid";
					}else{
						$errormsg = ("Please check your insert data!!");
						$rsvnchkrs_id = false;
					}
				}
				//echo $sql."<br/>";
				$rsvnchkrs_id = $obj->setResult($sql);
		}
		//echo ($rsvnchkrs_id)?"true":"false"."<br/>";
		if($rsvnchkrs_id != false){
			for($i=1; $i<=$last_groupid; $i++) {
				if(isset($group[$i][0])){$g_id=$i;$set_view=1;$set_edit=0;}
				if(isset($group[$i][1])){$g_id=$i;$set_view=1;$set_edit=1;}
				$ck_intable = $scObj->checks_ugroupIntable($uid,$i);
				if($ck_intable) {
					//echo "ck_intable=$ck_intable<br>".$grs[$i]["group_id"]."-".$grs[$i]["group_name"].", gid:".(($g_id==false)?"false":$g_id)."<br/>";
					if(isset($group[$i][0])){
						$scObj->edits_ugroup($uid,$ck_intable,$set_view,$set_edit);
					} else if(!isset($group[$i][0])){
						$scObj->dels_ugroup($uid,$ck_intable);
					}
				} else {
					if(isset($group[$i][0])||isset($group[$i][1])){
						$sql = "insert into s_ugroup(group_id,u_id,set_view,set_edit,l_lu_user,l_lu_date,l_lu_ip,active)  values($g_id,$uid,$set_view,$set_edit,".$_SESSION["__user_id"].",Now(),'".$obj->getIp()."',1)";
						//echo $_SESSION["__user_id"].": ".$sql."<br />";
						$obj->setResult($sql);
					}
				}
			}
		} else {
			$errormsg = $obj->getErrorMsg();
		}
		if($errormsg==""){
		$successmsg="Update data complete!!".$_POST["phpsql"];
		header("Location: manage_user.php?msg=$successmsg");}
	} else {
			$errormsg = $obj->getErrorMsg();
	}
}
$ugroupxml = "<command>" .
				"<table>s_ugroup</table>" .
				"<where name='u_id' operator='='>".$_POST["id"]."</where>" .
			 "</command>";
$lastgroupid=0;
if(isset($_POST["id"])) {$ugrs = $obj->getRsXML($ugroupxml,$filename);}
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
    		<div id="showerrormsg" <? if($errormsg=="" &&!isset($_POST["add"])){?>style="display:none"<? } else {?>style="display:block"<? }?>>
    			<table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    				<tr>
    					<td ><b><font style="color:#cc0000;">Error message: </font></b><?php echo "$errormsg"; ?></td>
    				</tr>
    			</table>
    		</div>
        	<div >
    			<fieldset>
					<legend><b>User Information</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style='overflow:auto'>
                    <tbody><tr>
                    	<td valign="bottom" width="350">
                        <div style="vertical-align:inherit;">
                          <? 
							if(isset($_POST["id"])){
									$xml = "<command>" .
									"<table>s_user</table>" .
									"<where name='u_id' operator='='>".$_POST["id"]."</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);
							} else if(!isset($_POST["id"])) {
								echo $obj->gFormInsert('s_user',$filename);
							}
 						?>
                        </div></td>
                        <? if($_POST["id"]) {?>
                        <td valign="top" align="left">
                        <div>
                        <img id="images_employee" name="images_employee" src="../../images/user/<?=(isset($_POST["id"]))?$obj->getIdToText($_POST["id"],"s_user","upic","u_id"):"default.gif"?>"><br>
						<?if($hasSession){?>
						<a href="javascript:;;" onClick="window.open('uploadupic.php?uid=<?=$_POST["id"]?>','uploadpicture','height=200,width=500,resizable=0,scrollbars=1');">change picture</a>
                        <?}?>
                        </div>
                        </td>
                        <? } ?>
                    </tr>
                    </tbody>
                    </table>
                </fieldset>
    			<fieldset>
					<legend><b>User Permission</b></legend>
                    
					<table class="main_table_list" cellspacing="0" cellpadding="0">
	                    <tr>
		                    <td class="mainthead">Group For User</td>
		                    <td class="mainthead">Permission for User and Group</td>
	                   	</tr>
	                <?   	$rsvnviewcnt = 0; $rsvneditcnt = 0;
	                		for($i=0; $i<$grs["rows"]; $i++) {
	                 			if($lastgroupid<$grs[$i]["group_id"]){$lastgroupid = $grs[$i]["group_id"];}
	                			echo ($i%2==0)?'<tr class="content_list">':'<tr class="content_list1">';
	         					//if($grs[$i]["group_id"]==3){
								//	$chkrsvn="showResvBookingLimit(this);";
								//}else{
								//	$chkrsvn="";
								//}
								
	                ?>
	                    	<td>&nbsp;&nbsp;<?=$grs[$i]["group_name"]?>
							</td>
							<td>
							<? 
	                			//	if(isset($_POST["id"])) {
										$viewchk = $obj->getCanCheckbox($_POST["id"],$grs[$i]["group_id"],"set_view");
	                					$editchk = $obj->getCanCheckbox($_POST["id"],$grs[$i]["group_id"],"set_edit");
										$rsvnviewchk = $obj->getrsvnchk(1,$grs[$i]["group_id"],"set_view");
	                					$rsvneditchk = $obj->getrsvnchk(1,$grs[$i]["group_id"],"set_edit");
										//echo ($rsvnviewchk)?"true":"false";
										if($rsvnviewchk=="checked"){$chkrsvn1="showResvBookingLimit(this);";}else{$chkrsvn1="";}
										if($rsvneditchk=="checked"){$chkrsvn2="showResvBookingEditLimit(this);";}else{$chkrsvn2="";}
										if($rsvnviewchk=="checked"&&$viewchk=="checked"){$rsvnviewcnt++;}
										if($rsvneditchk=="checked"&&$editchk=="checked"){$rsvneditcnt++;}
								//	}
	                		?>
									<input size="26" type="checkbox" name="grs[<?=$grs[$i]["group_id"]?>]" value="1" onClick="<?=$chkrsvn1?>chkUserSelectView(this);" <?=$viewchk?>>&nbsp;view&nbsp;&nbsp;
									<input size="26" type="checkbox" name="grs[<?=$grs[$i]["group_id"]?>]" value="1" onClick="<?=$chkrsvn2?>chkUserSelectEdit(this);" <?=$editchk?>>&nbsp;edit
							</td>
	                    </tr>
	                 <? } ?>
                    </table>
                </fieldset><br />
                <input type="hidden" id="rsvnviewchk" name="rsvnviewchk" value="<?=$rsvnviewcnt?>"/>&nbsp;
                <input type="hidden" id="rsvneditchk" name="rsvneditchk" value="<?=$rsvneditcnt?>"/>
                <div id="resvlimit" <?if($rsvnviewcnt<=0){?>style="display:none"<? } ?>>
                <?
                	if(isset($_POST["id"])){
						$rsvnchkxml = "<command>" .
										"<table>s_rsvn_chk</table>" .
										"<where name='u_id' operator='='>".$_POST["id"]."</where>" .
									"</command>";
						$rsvnchkrs = $obj->getRsXML($rsvnchkxml,$filename);
                	}
							
                ?>
                <fieldset>
					<legend><b>Reservation Agent Limited in Booking System</b></legend>
					<table border="0" cellpadding="0" cellspacing="0" width="100%" style='overflow:auto'>
                    <tbody><tr>
                    	<td valign="bottom">
                        <div style="vertical-align:inherit">
                        <table class="main_table_list" cellspacing="1" cellpadding="2">
                        	<tr class="content_list1" style="display:inline">
                        		<td width="60"><b>&nbsp;&nbsp;View</b></td>
                        		<td>&nbsp;&nbsp;past&nbsp;&nbsp;<input type="text" name="previewdate" id="previewdate" value="<?=$obj->checkParameter($rsvnchkrs[0]["pre_viewdate"],"0")?>" size="7"/>&nbsp;&nbsp;days&nbsp;&nbsp;</td>
                        		<td>&nbsp;&nbsp;future&nbsp;&nbsp;<input type="text" name="viewdateafter" id="viewdateafter" value="<?=$obj->checkParameter($rsvnchkrs[0]["after_viewdate"],"0")?>" size="7"/>&nbsp;&nbsp;days&nbsp;&nbsp;</td>
                        	</tr>
                        	<tr/>
                        	<tr class="content_list1" id="resveditlimit" <?if($rsvneditcnt<=0){?>style="display:none"<? } else if($rsvneditcnt>0) { ?>style="display:inline"<? } ?>>
                        		<td width="60"><b>&nbsp;&nbsp;Edit</b></td>
                        		<td>&nbsp;&nbsp;past&nbsp;&nbsp;<input type="text" name="preeditdate" id="preeditdate" value="<?=$obj->checkParameter($rsvnchkrs[0]["pre_editdate"],"0")?>" size="7"/>&nbsp;&nbsp;days&nbsp;&nbsp;</td>
                        		<td>&nbsp;&nbsp;future&nbsp;&nbsp;<input type="text" name="editdateafter" id="editdateafter" value="<?=$obj->checkParameter($rsvnchkrs[0]["after_editdate"],"0")?>" size="7"/>&nbsp;&nbsp;days&nbsp;&nbsp;</td>
                        	</tr>
                        </table>
                        </div>
                        </td>
                    </tr></tbody>
                    </table>
				</fieldset>
                </div>
    			<fieldset>
					<legend> </legend>
					<br/>
					<input type="hidden" id="id" name="id" value="<?=$_POST["id"]?>">
					<input type="hidden" name="phpsql" id="phpsql" value="<?=$_POST["phpsql"]?>"/>
					<input name="add" id="add" type="submit" size="" value="<?=(isset($_POST["id"]))?" save change ":" add "?>" onClick='<?=(isset($_POST["id"]))?"set_insertUserData(\"s_user\",".$lastgroupid.")":"set_insertUserData(\"s_user\",".$lastgroupid.")"?>' >&nbsp; 
					<input name="cancel" id="cancel" type="submit" size="" value=" cancel " onClick="getReturnText('manage_user.php','<?=$_POST["phpsql"]?>','tableDisplay');" >
                </fieldset>
			</div>
		</td>
    </tr>
</table>
</td>
</tr>
</table>
	<div class="hiddenbar"><img id="spLine" src="../../images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('../../images')"/></div>