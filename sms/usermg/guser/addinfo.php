<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");
require_once("user.inc.php");

$obj = new formdb();
$userobj = new user();
$filename = "../object.xml";
$errormsg ="";
$allPageId = "";
$ugroupid = $obj->getParameter("id",false);
// for return to the same page 
$showinactive=$obj->getParameter("showinactive",0);
$showdetail=$obj->getParameter("showdetail",0);
$method = $obj->getParameter("method");
$order = $obj->getParameter("order");
$page = $obj->getParameter("page");
$sort=$obj->getParameter("sort");
$search = $obj->getParameter("search");
$pagepermission = $obj->getParameter("pagepermission","");
$accessFirstTime = $obj->getParameter("first",0);
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
$querystr = "&pageid=$pageid&showinactive=$showinactive&showdetail=$showdetail&search=$searchstr&order=$order&page=$page&sort=$sort";

//set group active
if($method=="setactive" ){
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
$add = $obj->getParameter("add");
if($add == " save change " ){
	$id = $obj->readToUpdate($_REQUEST,$filename);
	if($id){
		$permission = $userobj->update_permission($_REQUEST,$ugroupid,"s_grouptemplate");
		if($permission){
			$successmsg="Update data complete!!";
			$successmsg.=$querystr;
			header("Location: index.php?msg=$successmsg");
		} else {
			$accessFirstTime = 0;
			$errormsg = "The error occur when try to update permission for this group template. Please update information again prevent losing permission.";
		}
	} else {
		$accessFirstTime = 0;
		$errormsg = $obj->getErrorMsg();
	}
} else if($add == " add " && $chkPageView ) {
	$id = $obj->readToInsert($_REQUEST,$filename);
	if($id){
		$ugroupid = $id;
		$permission = $userobj->update_permission($_REQUEST,$id,"s_grouptemplate");
		if($permission){
			$successmsg="Update data complete!!";
			header("Location: index.php?msg=$successmsg&pageid=$pageid");
		} else {
			$accessFirstTime = 0;
			$errormsg = "The error occur when try to update permission for this group template. Please update information again prevent losing permission.";
		}
	} else {
		$accessFirstTime = 0;
		$errormsg = $obj->getErrorMsg();
	}
}

$pagepermissionarray = array();
if($ugroupid && $accessFirstTime){
	// Query data from s_grouptemplate for initail group permission interface.
	$sql = "select page_id,edit_permission,view_permission " .
		"from s_grouptemplate " .
		"where group_id=$ugroupid " .
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
}else{
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
<form name="usergroup" id="usergroup" action="" method="post" style="padding:0;margin:0">
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

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%" align="left">
    		<div id="showerrormsg" <? if($errormsg==""&&$add==false){?>style="display:none"<? } else {?>style="display:block"<? }?>>
    			<table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    				<tr>
    					<td ><b><font style="color:#ff0000;"><img src="/images/errormsg.png" /> Error message: </font></b><?=$errormsg ?></td>
    				</tr>
    			</table>
    		</div>
        	<div>
        		        <? 
							if($ugroupid){
									$xml = "<command>" .
									"<table>s_group</table>" .
									"<where name='group_id' operator='='>$ugroupid</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else {
								echo $obj->gFormInsert('s_group',$filename);	
							}
							
							$cms_update_time = $obj->getParameter("cms_update_time","0");
						    $uschk = $obj->getParameter("uschk","");
						    $srchk = $obj->getParameter("srchk","");
						    $logviewchk = $obj->getParameter("logviewchk","");
						    $apptviewchk = $obj->getParameter("appt_viewchk","");
						    $appteditchk = $obj->getParameter("appt_editchk","");
						    $pre_viewdate = $obj->getParameter("previewdate",0);
						    $after_viewdate = $obj->getParameter("viewdateafter",0);
						    $pre_editdate = $obj->getParameter("preeditdate",0);
						    $after_editdate = $obj->getParameter("editdateafter",0);
						    $uschk = ($uschk=="1")?"checked":"";
						    $srchk = ($srchk=="1")?"checked":"";
						    $apptviewchk = ($apptviewchk=="1")?"checked":"";
						    $appteditchk = ($appteditchk=="1")?"checked":"";
						    $logviewchk = ($logviewchk=="1")?"checked":"";
						    
						    if($ugroupid && $accessFirstTime){
						       	$sql = "select * from s_group where group_id=$ugroupid ";
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
						    }
						  ?>
						  
                    <table cellpadding="0" cellspacing="0" class="generalinfo" style="margin-top:0px; left:0px;">
		                    <tr height="32">
		                    	<td colspan="3" style="padding-left: 0;border-bottom: 3px double #d3d3d3;">
		                    	 <b>&nbsp;&nbsp;&nbsp;&nbsp;Template Permission</b>		                    	</td>
					        </tr>
					        <tr height="22">
						        <td width="200" align="left" style="white-space:nowrap;padding-left: 30;width: 200px">Commission Locked Timer :</td>
						        <? 
						        $xml = "<field name=\"cms_update_time\" formname=\"cms_update_time\" defaultvalue=\"__post\" formtype=\"select\" table=\"l_timeperiod\" first=\"---select---\" />";
 								$field = simplexml_load_string($xml); 
						        ?>
						        <td width="200" align="left"><?=$obj->gSelectBox($field,$filename,$cms_update_time)?></td>
						        <td width="300" align="left"></td>
					        </tr>
					        <tr height="22">
						        <td align="left" style="padding-left: 30;">User Online unlock :</td>
						        <td width="200" align="left">
					          <input size="26" type="checkbox" id="uschk" name="1" value="1" <?=$uschk?>/>						        </td>
						        <td width="300" align="left"></td>
					        </tr>
					        <tr height="22">
						        <td align="left" style="padding-left: 30;">Sale Receipt unlocked :</td>
						        <td width="200" align="left">
					          <input size="26" type="checkbox" id="srchk" name="1" value="1" <?=$srchk?>/>						        </td>
						        <td width="300" align="left"></td>
					        </tr>
					        <tr height="22">
						        <td align="left" style="padding-left: 30;">View log on Booking :</td>
						        <td width="200" align="left">
					          <input size="26" type="checkbox" id="logviewchk" name="1" value="1" <?=$logviewchk?>/>						        </td>
						        <td width="300" align="left"></td>
					        </tr>
					        <tr height="22">
						        <td align="left" style="padding-left: 30;">Appointment Date - View Limit :</td>
						        <td width="200" align="left">
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
						        onClick="toggle_rsvnchk('appt_viewchk');"/> -->								</td>
						        <td width="300" align="left">
						        <span id="rsvnviewlimit" name="rsvnviewlimit" <?if($apptviewchk!="checked"){?>style="display:none"<? } ?> >
						        &nbsp;&nbsp;past&nbsp;&nbsp;
						        <input type="text" name="previewdate" id="previewdate" onChange="checkDateBox('previewdate','pre','view');" value="<?=$pre_viewdate?>" size="7"/>&nbsp;&nbsp;days&nbsp;&nbsp;
					          &nbsp;&nbsp;future&nbsp;&nbsp;<input type="text" name="afterviewdate" id="afterviewdate" onChange="checkDateBox('afterviewdate','after','view');" value="<?=$after_viewdate?>" size="7"/>&nbsp;&nbsp;days&nbsp;&nbsp;								</span></td>
					        </tr>
					       
					        <tr height="22">
						        <td align="left" style="padding-left: 30;">Appointment Date - Edit Limit :</td>
						        <td width="200" align="left">
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
						      onClick="toggle_rsvnchk('appt_editchk');"/> -->								</td>
						        <td width="300" align="left">
						        <span id="rsvneditlimit" name="rsvneditlimit" <?if($appteditchk!="checked"){?>style="display:none"<? } ?> >
						        &nbsp;&nbsp;past&nbsp;&nbsp;
						        <input type="text" name="preeditdate" id="preeditdate" onChange="checkDateBox('preeditdate','pre','edit');" value="<?=$pre_editdate?>" size="7"/>&nbsp;&nbsp;days&nbsp;&nbsp;
					          &nbsp;&nbsp;future&nbsp;&nbsp;<input type="text" name="aftereditdate" id="aftereditdate" onChange="checkDateBox('aftereditdate','after','edit');" value="<?=$after_editdate?>" size="7"/>&nbsp;&nbsp;days&nbsp;&nbsp;						        </span>								</td>
					        </tr>
							 <tr height="22">
					          <td colspan="3" style="padding-left: 30;">  
							  
					<table boder="1" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px">
                    	<tbody>
                    	<tr height="29">
                    	<td colspan="3" style="padding-left: 0;border-bottom: 3px double #d3d3d3;">
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
                    </table>
					
					</td>
			          </tr>
	      </table>
                  
                    <table boder="0" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px;">
                    	<tbody><tr>
                    	<td colspan="2">
                		<fieldset>
                    		<legend> </legend>
                    		<br/><!--'<?=($ugroupid)?"set_editData(\"s_group\")":"set_insertData(\"s_group\")"?>'-->
                    		<input id="allPageId" value="<?=$allPageId?>" type="hidden">
                    		<input type="hidden" name="querystr" id="querystr" value="<?=$querystr?>"/>
                    		<input name="id" id="id" type="hidden" value="<?=$ugroupid?>">
							<input name="add" id="add" type="button" size="" value="<?=($ugroupid)?" save change ":" add "?>" 
							onClick="set_editData('s_group')" > 
							<input name="cancel" id="cancel" type="button" value=" cancel " 
							onClick="gotoURL('index.php?method=cancel<?=$querystr?>');" style="font-size:11px">
                		</fieldset>
						
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
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" width="6px" height="60px" onClick="hiddenLeftFrame('')"/></div>
</div>
</body>
</html>
<script type="text/javascript">
initialPartialCheckBox(0,"view",1);
initialPartialCheckBox(0,"edit",1);
</script>
<?

function makemenu($menulevel,$id,$class,$pagepermissionarray){
		$obj = new cms(); 
		$textout="";
		$sql = "select * from s_pagename where `index`=$menulevel and `page_parent_id`=$id order by page_priority asc";
		$obj->setDebugStatus(false);
		$rs = $obj->getResult($sql);
	    $left = 30+($menulevel*15);
	    $ileft = 13+($menulevel*15);
	    
	   for($i=0;$i<$rs["rows"];$i++){
			if($rs[$i]["active"]==1){
    			$chk=($class=="even")?"0":"1";
    			$class=($menulevel%2==0)?"even height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ":"odd height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";
				//$textout .= "<tr class=\"$class\" height=\"20\">\n";
				$textout .= "<tr class=\"$class\" height=\"20\">\n";
    			$textout .= "<td style=\"padding-left:".$left."px;\" width=\"50%\" >\n";
    			
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