<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("user.inc.php");
require_once("formdb.inc.php");
require_once("report.inc.php");

$obj = new formdb();
$userobj = new user();
$reportobj = new report();
$filename = "../object.xml";
$errormsg ="";
$uid = $obj->getParameter("id");
// for return to the same page 
$showinactive = $obj->getParameter("showinactive");
$showdetail=$obj->getParameter("showdetail");
$categoryid = $obj->getParameter("categoryid",0);
$method = $obj->getParameter("method");
$order = $obj->getParameter("order","u");
$page = $obj->getParameter("page");
$sort=$obj->getParameter("sort");
$search = $obj->getParameter("search");
$group_id = $obj->getParameter("group_id",false);
$accessFirstTime = $obj->getParameter("first",0);
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
$querystr = "&pageid=$pageid&categoryid=$categoryid&search=$searchstr&order=$order&page=$page&sort=$sort" .
		"&showinactive=$showinactive&showdetail=$showdetail";
//set emp active
if($method=="setactive" && $chkPageEdit){
	$sql = "";
	$active = $obj->getParameter("active");
	$name = $obj->setActive($_REQUEST,$filename);
	if($name!=false){
		if($active==1){
			$successmsg="$name is active!!";
		}else{
			$successmsg="$name is inactive!!";
		}
		$successmsg=$successmsg.$querystr;
		header("Location: index.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
}
// add/update information
$group =$obj->getParameter("grs");
$add = $obj->getParameter("add",false);
$last_groupid = $obj->getParameter("lastgroupid");
if($add == " save change " && $chkPageEdit){
	$id = $obj->readToUpdate($_REQUEST,$filename);
	if($id){
		$chksql = "update s_user set c_lu_user=".$_SESSION["__user_id"].",c_lu_date=Now(),c_lu_ip='".$obj->getIp()."' where u_id=$uid";
		$cid=$obj->setResult($chksql);
		$permission = $userobj->update_permission($_REQUEST,$uid,"s_upage");
		if($permission&&$cid){
			$successmsg="Update data complete!!";
			$successmsg.=$querystr;
			header("Location: index.php?msg=$successmsg");
		} else {
			$accessFirstTime = 0;
			$errormsg = "The error occur when try to update permission for this user. Please update information again prevent losing permission.";
		}
	} else {
		$accessFirstTime = 0;
		$errormsg = $obj->getErrorMsg();
	}
} else if($add == " add " && $chkPageEdit) {
	$uid = $obj->readToInsert($_REQUEST,$filename);
	//echo $id;
	if($uid){
		$permission = $userobj->update_permission($_REQUEST,$uid,"s_upage");
		if($permission){
			$successmsg="Update data complete!!";
			header("Location: index.php?msg=$successmsg&pageid=$pageid");
		} else {
			$accessFirstTime = 0;
			$errormsg = "The error occur when try to update permission for this user. Please update information again prevent losing permission.";
		}
	} else {
		$accessFirstTime = 0;
		$errormsg = $obj->getErrorMsg();
	}
}
//user group permission
$groupsql = "select * from s_group " .
			"where active=1 ";
$groupsql .= "order by group_name ";
$grs = $obj->getResult($groupsql);


// Query data from s_grouptemplate for initail group permission interface.
$pagepermission = $obj->getParameter("pagepermission","");
$pagepermissionarray = array();
if($uid && $accessFirstTime){
	$sql = "select page_id,edit_permission,view_permission " .
		"from s_upage " .
		"where user_id=$uid " .
		"order by page_priority asc";
		$rsPagePermission = $obj->getResult($sql);
		$j=0;
	for($i=0;$i<$rsPagePermission["rows"];$i++){
		if($rsPagePermission[$i]["edit_permission"] == 1){
			$pagepermissionarray[$j] = $rsPagePermission[$i]["page_id"]."_e";
			$j++;	
		}else if($rsPagePermission[$i]["view_permission"] == 1){
			$pagepermissionarray[$j] = $rsPagePermission[$i]["page_id"]."_v";
			$j++;
		}
	}
}else if(!$accessFirstTime && !$method && $group_id){
	$sql = "select page_id,edit_permission,view_permission " .
		"from s_grouptemplate " .
		"where group_id=$group_id " .
		"order by page_priority asc";
	$rsPagePermission = $obj->getResult($sql);
	$j=0;
	for($i=0;$i<$rsPagePermission["rows"];$i++){
		if($rsPagePermission[$i]["edit_permission"] == 1){
			$pagepermissionarray[$j] = $rsPagePermission[$i]["page_id"]."_e";
			$j++;	
		}else if($rsPagePermission[$i]["view_permission"] == 1){
			$pagepermissionarray[$j] = $rsPagePermission[$i]["page_id"]."_v";
			$j++;
		}
	}	
}else if(!$accessFirstTime && $method){
	// get old data for initail group permission interface.
	$pagepermissionarray = explode(",",$pagepermission);
}


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<script src="/scripts/date-functions.js" type="text/javascript"></script>
<script src="/scripts/datechooser.js" type="text/javascript"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name="user" id="user" action="" method="get" style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td width="6px" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
    <td height="49px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" align="center" height="49">
<?
$i = count($pageinfo["parent"]);
$pageinfo["parenturl"][$i] = $pageinfo["pageurl"];
$pageinfo["parent"][$i] = $pageinfo["pagename"];
$pageinfo["pagename"] = "Manage ".$pageinfo["pagename"];
?>
				<?include("$root/menuheader.php");?>
	 	</td>
	  </tr>
</table> 
</div>
  	</td>
  </tr>  
  <tr>
<td valign="top" style="margin-top:0px;margin-left:0px">
<input id="pageid" name="pageid" value="<?=$pageid?>" type="hidden">	
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%">
    		<div id="showerrormsg" <? if($errormsg==""&&$add==false){?>style="display:none"<? } else {?>style="display:block"<? }?>>
    			<table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    				<tr>
    					<td ><b><font style="color:#ff0000;"><img src="/images/errormsg.png" /> Error message: </font></b><?=$errormsg ?></td>
    				</tr>
    			</table>
    		</div>
        	<div>
        		<table border="0" cellpadding="0" cellspacing="0" style='overflow:auto' class="generalinfo">
                <tbody> <tr>
			        <td class="rheader" colspan="2" style="padding-left: 20px;padding-right: 10px;">
			        User Information:
			        </td>
			        </tr>
			        <tr>
                    	<td valign="top">
        		        <? 
							if($uid){
									$xml = "<command>" .
									"<table>s_user</table>" .
									"<where name='u_id' operator='='>$uid</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else {
								echo $obj->gFormInsert('s_user',$filename);	
							}
							$cms_update_time = 0;
							$uschk = "";
							$srchk = "";
							$logviewchk = "";
							$apptviewchk = "";
							$appteditchk = "";
							$pre_viewdate = 0;
							$after_viewdate = 0;
							$pre_editdate = 0;
							$after_editdate = 0;
							if($uid && $accessFirstTime){
								$sql = "select * from s_userpermission where user_id=$uid ";
								$rs = $obj->getResult($sql);
								$group_id = $rs[0]["group_id"];
								$cms_update_time = $rs[0]["cms_update_time"];
								$uschk = ($rs[0]["us_chk"]=="1")?"checked":"";
								$srchk = ($rs[0]["sr_chk"]=="1")?"checked":"";
								$logviewchk = ($rs[0]["log_viewchk"]=="1")?"checked":"";
								$apptviewchk = ($rs[0]["appt_viewchk"]=="1")?"checked":"";
								$appteditchk = ($rs[0]["appt_editchk"]=="1")?"checked":"";
								$pre_viewdate = $rs[0]["pre_viewdate"];
								$after_viewdate = $rs[0]["after_viewdate"];
								$pre_editdate = $rs[0]["pre_editdate"];
								$after_editdate = $rs[0]["after_editdate"];
							}else if(!$accessFirstTime && !$method && $group_id){
								$sql = "select * from s_group where group_id=$group_id ";
								$rs = $obj->getResult($sql);
								$cms_update_time = $rs[0]["cms_update_time"];
								$uschk = ($rs[0]["us_chk"]=="1")?"checked":"";
								$srchk = ($rs[0]["sr_chk"]=="1")?"checked":"";
								$logviewchk = ($rs[0]["log_viewchk"]=="1")?"checked":"";
								$apptviewchk = ($rs[0]["appt_viewchk"]=="1")?"checked":"";
								$appteditchk = ($rs[0]["appt_editchk"]=="1")?"checked":"";
								$pre_viewdate = $rs[0]["pre_viewdate"];
								$after_viewdate = $rs[0]["after_viewdate"];
								$pre_editdate = $rs[0]["pre_editdate"];
								$after_editdate = $rs[0]["after_editdate"];
							}else if(!$accessFirstTime && $method){
								
								$cms_update_time = $obj->getParameter("cms_update_time","0");
							    $uschk = $obj->getParameter("uschk",""); 
							    $srchk = $obj->getParameter("srchk","");
							    $apptviewchk = $obj->getParameter("appt_viewchk","");
							    $appteditchk = $obj->getParameter("appt_editchk","");
							    $logviewchk = $obj->getParameter("logviewchk","");
							    $pre_viewdate = $obj->getParameter("previewdate",0);
							    $after_viewdate = $obj->getParameter("viewdateafter",0);
							    $pre_editdate = $obj->getParameter("preeditdate",0);
							    $after_editdate = $obj->getParameter("editdateafter",0);
							    $uschk = ($uschk=="1")?"checked":""; 
							    $srchk = ($srchk=="1")?"checked":"";
							    $logviewchk = ($logviewchk=="1")?"checked":"";
							    $apptviewchk = ($apptviewchk=="1")?"checked":"";
							    $appteditchk = ($appteditchk=="1")?"checked":"";
							}
						?>
 						</td>
                        <? if($uid) {?>
                        <td valign="top" align="left">
                        <div>
                        <img id="images_employee" name="images_employee" src="<?=$customize_part?>/images/user/<?=($uid)?$obj->getIdToText($uid,"s_user","upic","u_id"):"default.gif"?>"><br>
						<a href="javascript:;;" onClick="window.open('uploadupic.php?uid=<?=$uid?>','uploadpicture','height=200,width=500,resizable=0,scrollbars=1');">change picture</a>
                        </div>
                        </td>
                        <? } ?>
                    </tr>
                    <tr>
			        <td class="rheader" colspan="2" height="32" style="padding-left: 20px;padding-right: 10px;"><br>
			        User Permission:
			        </td>
			        </tr>
			        <tr>
                    	<td valign="top" colspan="2">
                    	<? $ff = "<field name=\"group_id\" table=\"s_group\" first=\"\" >"; ?>
                    	<table cellspacing="0" border="0" cellpadding="0" width="100%" class="generalinfo" style="padding-top:0px;">
					        <tr height="22">
						        <td style="white-space:nowrap;padding-left: 30;width: 200px">Template Permission :</td>
						        <? 
						        $groupIndexBeforeChange =0;
						        $textout = "<select name=\"group_id\" id=\"group_id\"  onChange=\"confirmDialog(this);\"> \n";
								$textout .= "<option value='0'>--select--</option> \n";
								for($i=0; $i<$grs["rows"];$i++) {
									$selected = '';
									if($group_id==$grs[$i]["group_id"]){
										$selected = 'selected'; 
										$groupIndexBeforeChange = $i+1;	
									}
									$textout .= "<option value=\"".$grs[$i]["group_id"]."\" $selected >".$grs[$i]["group_name"]."</option> \n";
										
								}
								if(($accessFirstTime && $group_id==0) || $group_id=="customized"){
									$textout .= "<option value='customized' selected=\"selected\">Customized</option> \n";
									$groupIndexBeforeChange = $i+1;	
								}
								$textout .= "</select> \n";
								?>
								<td>
									<?=$textout?>
									<input id="groupIndexBeforeChange" value="<?=$groupIndexBeforeChange?>" type="hidden">									
								</td>
						        
					        </tr>
					        <tr height="22">
						        <td style="white-space:nowrap;padding-left: 30;width: 200px">Commission Locked Timer :</td>
						        <td width="100px"><?
									$sql = "select 	tp_id,tp_name from l_timeperiod order by tp_id asc";
									$timeRs = $obj->getResult($sql);				        
						        	$textout = "<select name=\"cms_update_time\" id=\"cms_update_time\"  onChange=\"checkCustomizedPermission();\"> \n";
									$textout .= "<option value='0'>--select--</option> \n";
									for($i=0; $i<$timeRs["rows"];$i++) {
										$selected = '';
										if($cms_update_time==$timeRs[$i]["tp_id"]){
											$selected = 'selected'; 
										}
										$textout .= "<option value=\"".$timeRs[$i]["tp_id"]."\" $selected >".$timeRs[$i]["tp_name"]."</option> \n";
											
									}
									$textout .= "</select> \n";
									echo $textout;
						        ?></td>
						        <td></td>
					        </tr>
					          <tr height="22">
						        <td style="padding-left: 30;">User Online unlock :</td>
						        <td>
						        <input size="26" type="checkbox" id="uschk" name="1" value="1" onClick="checkCustomizedPermission();" <?=$uschk?>/>
						        </td>
						        <td></td>
					        </tr>
					        <tr height="22">
						        <td style="padding-left: 30;">Sale Receipt unlocked :</td>
						        <td>
						        <input size="26" type="checkbox" id="srchk" name="1" value="1" onClick="checkCustomizedPermission();" <?=$srchk?>/>
						        </td>
						        <td></td>
					        </tr>
					        <tr height="22">
						        <td style="padding-left: 30;">View log on Booking :</td>
						        <td>
						        <input size="26" type="checkbox" id="logviewchk" name="1" value="1" onClick="checkCustomizedPermission();" <?=$logviewchk?>/>
						        </td>
						        <td></td>
					        </tr>
					        <tr height="22">
						        <td style="padding-left: 30;">Reservation Date - View Limit :</td>
						        <td>
						        <? if($apptviewchk!="checked"){ ?>
						       &nbsp;<a style="cursor: default;" 
						        onclick="toggle_rsvnchk('appt_viewchk');">
						        <img id="rsvnviewImg" src="/images/triState/triState0.gif" border='0' width='13' height='13'>
						        <input id="appt_viewchk" value="0" type="hidden">
						        </a>
						        <? } else { ?>
						       &nbsp;<a style="cursor: default;" 
						        onclick="toggle_rsvnchk('appt_viewchk');">
						        <img id="rsvnviewImg" src="/images/triState/triState4.gif" border='0' width='13' height='13'>
						        <input id="appt_viewchk" value="1" type="hidden">
						        </a>
						        <? } ?>
						        <!-- <input size="26" type="checkbox" id="appt_viewchk" name="1" value="1" <?=$apptviewchk?> 
						        onClick="toggle_rsvnchk('appt_viewchk');"/> -->
								</td>
						        <td>
						        <span id="rsvnviewlimit" name="rsvnviewlimit" <?if($apptviewchk!="checked"){?>style="display:none"<? } ?> >
						        &nbsp;&nbsp;past&nbsp;&nbsp;<input type="text" name="previewdate" id="previewdate" onChange="checkDateBox('previewdate','pre','view');" value="<?=$pre_viewdate?>" size="7"/>&nbsp;&nbsp;days&nbsp;&nbsp;
						        &nbsp;&nbsp;future&nbsp;&nbsp;<input type="text" name="afterviewdate" id="afterviewdate" onChange="checkDateBox('afterviewdate','after','view');" value="<?=$after_viewdate?>" size="7"/>&nbsp;&nbsp;days&nbsp;&nbsp;
								</span></td>
					        </tr>
					        <tr height="22">
						        <td style="padding-left: 30;">Reservation Date - Edit Limit :</td>
						        <td>
						        <? if($appteditchk!="checked"){ ?>
						       &nbsp;<a style="cursor: default;" 
						        onclick="toggle_rsvnchk('appt_editchk');">
						        <img id="rsvneditImg" src="/images/triState/triState0.gif" border='0' width='13' height='13'>
						        <input id="appt_editchk" value="0" type="hidden">
						        </a>
						        <? } else { ?>
						       &nbsp;<a style="cursor: default;" 
						        onclick="toggle_rsvnchk('appt_editchk');">
						        <img id="rsvneditImg" src="/images/triState/triState4.gif" border='0' width='13' height='13'>
						        <input id="appt_editchk" value="1" type="hidden">
						        </a>
						        <? } ?>
						      <!--  <input size="26" type="checkbox" id="appt_editchk" name="1" value="1" <?=$appteditchk?> 
						      onClick="toggle_rsvnchk('appt_editchk');"/> -->
								</td>
						        <td>
						        <span id="rsvneditlimit" name="rsvneditlimit" <?if($appteditchk!="checked"){?>style="display:none"<? } ?> >
						        &nbsp;&nbsp;past&nbsp;&nbsp;<input type="text" name="preeditdate" id="preeditdate" onChange="checkDateBox('preeditdate','pre','edit');" value="<?=$pre_editdate?>" size="7"/>&nbsp;&nbsp;days&nbsp;&nbsp;
						        &nbsp;&nbsp;future&nbsp;&nbsp;<input type="text" name="aftereditdate" id="aftereditdate" onChange="checkDateBox('aftereditdate','after','edit');" value="<?=$after_editdate?>" size="7"/>&nbsp;&nbsp;days&nbsp;&nbsp;
						        </span>
								</td>
					        </tr>
					    </table>
					    </td>
                    </tr>
			        <tr>
                    	<td valign="top" colspan="2" style="padding-left:0px;"> 	
                    <table boder="1" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px">
                    	<tbody>
                    	<tr height="29">
                    	<td colspan="3" style="padding-left: 0;border-bottom: 3px double #d3d3d3;" class="rheader">
                    	 <b>Page Permission
                    	 (<a href="javascript:;;" onClick="collapse_all()">collapse all</a> , 
                    	 <a href="javascript:;;" onClick="expand_all()">expand all</a>)
                    	 </b>
                    	</td>
                    	</tr>
                    	<tr height="29">
                    	<td style="padding-left: 5;border-bottom: 3px double #d3d3d3;" class="rheader">
                    	 <b>Page</b>
                    	</td>
                    	<td style="padding-left: 5;border-bottom: 3px double #d3d3d3;" class="rheader">
                    	 <b>View</b>
                    	</td>
                    	<td style="padding-left: 10;border-bottom: 3px double #d3d3d3;" class="rheader">
                    	 <b>Edit</b>
                    	</td>
                    	</tr>
                    	
                    	<?=makemenu(0,0,"odd",$pagepermissionarray);?>
                    	</tbody>
                    </table><br/>&nbsp;&nbsp;
                    		<input id="allPageId" value="<?=$allPageId?>" type="hidden">
                    		<input type="hidden" name="querystr" id="querystr" value="<?=$querystr?>"/>
                    		<input type="hidden" name="first" id="first" value="0"/>
                    		<input name="id" id="id" type="hidden" value="<?=$uid?>">
							<input name="add" id="add" type="button" size="" value="<?=($uid)?" save change ":" add "?>" onClick="set_editData('s_user')" > 
							<input name="cancel" id="cancel" type="button" value=" cancel " onClick="gotoURL('index.php?<?=$querystr?>');" style="font-size:11px">
                		</td>
                    </tr></tbody>
                    </table>  
                </form>
			</div>
		</td>
    </tr>
</table>
		</td>
  </tr>
</table> 
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('/images')"/></div>
</div>
</body>
</html>
<script type="text/javascript">
initialPartialCheckBox(0,"view",1);
initialPartialCheckBox(0,"edit",1);
</script>
<?
//For keep all page id.
$_POST["allPageId"] = "";

function makemenu($menulevel,$id,$class,$pagepermissionarray){
		$obj = new cms(); 
		$textout="";
		$sql = "select * from s_pagename where `index`=$menulevel and `page_parent_id`=$id order by page_priority asc";
		$obj->setDebugStatus(false);
		$rs = $obj->getResult($sql);
	    $left = 30+($menulevel*15);
	    $ileft = 13+($menulevel*15);
	   if(!isset($GLOBALS["allPageId"])){$GLOBALS["allPageId"]="";}
	   for($i=0;$i<$rs["rows"];$i++){
			if($rs[$i]["active"]==1){
    			$chk=($class=="even")?"0":"1";
    			$class=($menulevel%2==0)?"even":"odd";
				//$textout .= "<tr class=\"$class\" height=\"20\">\n";
				$textout .= "<tr class=\"$class\" height=\"20\">\n";
    			$textout .= "<td style=\"padding-left:".$left."px;\">\n";
    			
				$plus1 = "<img src=\"/images/classic/menu/menu1.png\" style=\"margin-top:2px; border:0\">";
				$plus2 = "<img src=\"/images/menu2.png\" style=\"margin-top:2px; border:0\">";
    			$plus = ($rs[$i]["has_child"]==1&&$rs[$i]["page_id"]>1)?$plus2:$plus1;
				
				//For keep all page id.
				$GLOBALS["allPageId"] .= "|".$rs[$i]["page_id"];
				
    			$textout .= ($rs[$i]["has_child"]==1)?
							"<a href=\"javascript:;\" onClick=\"toggle('".$rs[$i]["page_name"].$rs[$i]["page_id"]."')\">":
							"<a href=\"javascript:;\" >";
				$textout .= "$plus ".$rs[$i]["page_name"]."";
				$textout .= "</a>";
				$textout .= "</td>\n";
				
				//For set initail node value.
				$editValue = 0;
				$viewValue = 0;
				if(in_array($rs[$i]["page_id"]."_e",$pagepermissionarray)) {
					$editValue = 1;
					$viewValue = 1;
				}else if(in_array($rs[$i]["page_id"]."_v",$pagepermissionarray)) {
					$viewValue = 1;
				}
				
				//For check box
				if($rs[$i]["has_child"]==1 && $rs[$i]["page_id"]>1){
					$textout .= "<td width=\"200px\">&nbsp;<a style=\"cursor: default;\" onMouseOver=\"mouseOver('view','".$rs[$i]["page_id"]."');\" onMouseOut=\"mouseOut('view','".$rs[$i]["page_id"]."');\" onclick=\"partialCheckBox('".$rs[$i]["page_id"]."','view','$id');\"><img id=\"viewParent".$rs[$i]["page_id"]."Img\" src=\"/images/triState/triState$viewValue.gif\" border='0' width='13' height='13'></a></td>\n";
					$textout .= "<td width=\"200px\">&nbsp;<a style=\"cursor: default;\" onMouseOver=\"mouseOver('edit','".$rs[$i]["page_id"]."');\" onMouseOut=\"mouseOut('edit','".$rs[$i]["page_id"]."');\" onclick=\"partialCheckBox('".$rs[$i]["page_id"]."','edit','$id');\"><img id=\"editParent".$rs[$i]["page_id"]."Img\" src=\"/images/triState/triState$editValue.gif\" border='0' width='13' height='13'></a></td>\n";
					$textout .= "<input id=\"view[".$rs[$i]["page_id"]."]\" value=\"$viewValue\" type=\"hidden\">";
					$textout .= "<input id=\"edit[".$rs[$i]["page_id"]."]\" value=\"$editValue\" type=\"hidden\">";
				}else{
					$textout .= "<td width=\"200px\"><input type=\"checkbox\" id=\"view[".$rs[$i]["page_id"]."]\" ".(($viewValue)?"checked=\"checked\"":"")." onclick=\"partialCheckBox('".$rs[$i]["page_id"]."','view','$id');\"></td>\n";
					$textout .= "<td width=\"200px\"><input type=\"checkbox\" id=\"edit[".$rs[$i]["page_id"]."]\" ".(($editValue)?"checked=\"checked\"":"")." onclick=\"partialCheckBox('".$rs[$i]["page_id"]."','edit','$id');\"></td>\n";
				}
				$textout .= "<input id=\"".$rs[$i]["page_id"]."_$id\" value=\"0\" type=\"hidden\">";
				
				$textout .= "</tr>\n";
				
				if($rs[$i]["has_child"]==1){
					$pagereffer = $rs[$i]["index"]+1;
					$textout .= "<tr><td style=\"margin: 0;padding: 0;\" colspan=\"3\">\n";
					// For slab or span menu
					$textout .= "<span id=\"".$rs[$i]["page_name"].$rs[$i]["page_id"]."\" style=\"display: none;margin: 0;padding: 0;\">\n";
					
					$textout .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
					$textout .= "\t";
					
		    		$textout .= "\t".makemenu($menulevel+1,$rs[$i]["page_id"],$class,$pagepermissionarray);
		    		
					$textout .= "</table>\n</span>\n";
					$textout .= "</td>\n</tr>\n";
				}
	    	}
			
    	}
    	
    	return $textout;
}
?>