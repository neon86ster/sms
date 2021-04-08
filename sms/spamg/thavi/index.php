<?
$root = $_SERVER["DOCUMENT_ROOT"];
include ("$root/include.php");
$obj->setDebugStatus(false);

// for return to the same page 
$sort = $obj->getParameter("sort", "asc");
$order = $obj->getParameter("order", "bl_th_list.l_lu_date");
$page = $obj->getParameter("page", 1);
$querystr = "pageid=$pageid&sort=$sort&order=$order&page=$page";
//undefined index
if(!isset($_SESSION["__user_id"])){$_SESSION["__user_id"]="";}
if(!isset($pageinfo["pagename"])){$pageinfo["pagename"]="";}
//


$ubranch_id = $obj->getIdToText($_SESSION["__user_id"], "s_user", "branch_id", "u_id");
$ubranch_name = strtolower($obj->getIdToText($ubranch_id, "bl_branchinfo", "branch_name", "branch_id"));
if ($ubranch_name != "all") {
	$branch = $ubranch_id;
}
$cityid = $obj->getIdToText($ubranch_id, "bl_branchinfo", "city_id", "branch_id");
$branchid= $obj->getParameter("branchid",$ubranch_id);
$querystr .= "&cityid=$cityid&branchid=$branchid";

$th_shiftone = $obj->getIdToText("$branchid", "bl_th_available", "th_shiftone", "branch_id", "1 order by l_lu_date desc");
$th_shifttwo = $obj->getIdToText("$branchid", "bl_th_available", "th_shifttwo", "branch_id", "1 order by l_lu_date desc");
$updateth = $obj->getParameter("update_th");
$errormsg = '';
$successmsg = '';
//

if ($updateth) {
	//$branchid = $ubranch_id;
	$th_shiftone = $obj->getParameter("th_shiftone", $th_shiftone);
	$th_shifttwo = $obj->getParameter("th_shifttwo", $th_shifttwo);
	$tmp = $obj->addThAvailable($branchid, $th_shiftone, $th_shifttwo);
	if (!$tmp) {
		$errormsg = $obj->getErrorMsg();
	}else{
		$successmsg = "Update Therapists Available Complete !!";
	}
}
$signin = $obj->getParameter("signin");
$thid = $obj->getParameter("thid");
//
$not = $obj->getParameter("not");

//$now = $obj->getParameter("now");
if ($signin == " In " && $thid > 1) {
	$tmp = $obj->addThList($thid,$branchid);
	if($tmp){
		$signin_time = $obj->getIdToText($tmp,"bl_th_list","l_lu_date","th_list_id");
		list($date,$time) = explode(" ",$signin_time);
		$local_signin_time = $dateobj->timezonefilter($date,$time,"Y-m-d H:i:s");
		
		$sql = "update bl_th_list set lc_l_lu_date=\"$local_signin_time\" where th_list_id=$tmp";
		$sql = "update bl_th_list set ot=\"$not\" where th_list_id=$tmp";
		$tmp=$obj->setResult($sql);
	}
	if (!$tmp) {
		$errormsg = $obj->getErrorMsg();
	}else{
		$successmsg = "Update data complete!!";
	}
}

$leave = $obj->getParameter("leave");
$add = $obj->getParameter("add");
$thlistid = $obj->getParameter("thlistid");
$blid = $obj->getParameter("blid");

if($leave=="1"&&$thlistid) {
	$tmp = $obj->removeThList($thlistid);
	$leave_time = $obj->getIdToText($thlistid,"bl_th_list","leave_time","th_list_id");
	list($date,$time) = explode(" ",$leave_time);
	$local_leave_time = $dateobj->timezonefilter($date,$time,"Y-m-d H:i:s");
	$sql = "update bl_th_list set lc_leave_time=\"$local_leave_time\" where th_list_id=$thlistid";
	if($tmp){
		$tmp=$obj->setResult($sql);
	}
	if(!$tmp){
		$errormsg = $obj->getErrorMsg();
	}
}else if($add=="1"&&$thlistid) {
	$sql = "update bl_th_list set branch_id=\"$blid\" where th_list_id=$thlistid";
	$tmp = $obj->setResult($sql);
	if(!$tmp){
		$errormsg = $obj->getErrorMsg();
	}
}

$th = $obj->getParameter("th");
$ot = $obj->getParameter("ot");
$setactive = $obj->getParameter("setactive");
if($setactive=="setactive")
{
	if($ot==1)
	{
		$sql="update bl_th_list set bl_th_list.ot=0 where th_list_id=$th ";
		$otactive=$obj->setResult($sql);
		
		$successmsg = "OT Inactive!!";
	}else
	{
		$sql="update bl_th_list set bl_th_list.ot=1 where th_list_id=$th ";
		$otactive=$obj->setResult($sql);
		$successmsg = "NO OT active!!";
	}
	
}

if($thid == 1 && !$updateth && $signin){$errormsg = '<font style="color:">Please select Therapist !!</font>';}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">

</head>
<body onLoad="getReturnText('report.php','<?=$querystr?>','tableDisplay');">
<div id="loading">
<table cellspacing="0" cellpadding="0" class="preloading">
<tr>
    <td align="center" valign="middle">
		<img src="/images/sms preload.png">
	</td>
</tr>
</table>
</div> 
<form name="thavi" id="thavi" action="" method="post" style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td width="8" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
 <?


$h1 = 110;
if ($ubranch_name == "all") {
	$chksql = "select th_av_id,f.branch_id," .
			"f.th_shiftone,f.th_shifttwo," .
			"f.`l_lu_date`,bl_branchinfo.branch_name " .
			"from (" .
				"select branch_id, max(th_av_id) as max_id " .
				"from bl_th_available group by branch_id) " .
				"as x inner join bl_th_available as f " .
				"on f.branch_id = x.branch_id " .
			"and f.th_av_id = x.max_id" .
			",bl_branchinfo,al_city " .
			"where bl_branchinfo.branch_id=f.branch_id " .
			"and bl_branchinfo.city_id=al_city.city_id " .
			"order by branch_name";
			
	$chkrs = $obj->getResult($chksql);
	
	$h1 = 110 + (25 * $chkrs["rows"]);
}


?>
    <td height="<?=$h1?>px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" align="left" height="49">
				<?include("$root/menuheader.php");?>	 	</td>
	  </tr>
	  <tr>
	    <td valign="top" align="center" height="10">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
			        <td height="30" align="left" class="rheader" style="padding-left: 20px; padding-bottom:10px;">
<? if($ubranch_name!="all"){?>
					
				        
						Branch:
					
						<?=$obj->makeListbox("branchid","bl_branchinfo","branch_name","branch_id",$branchid,true,"branch_name","branch_active","1","branch_name!='All' and city_id=$cityid ")?>
												
						Therapists Available&nbsp;&nbsp;
						<input type="text" name="th_available" value="<?=$th_shiftone+$th_shifttwo?>" size="3" disabled="disabled">
						&nbsp;Shift 1&nbsp;
						<input type="text" name="th_shiftone"  value="<?=$th_shiftone?>" size="3" ><!-- <?=($chkPageEdit)?"":"disabled"?>>-->
						&nbsp;Shift 2&nbsp;
						<input type="text" name="th_shifttwo"  value="<?=$th_shifttwo?>" size="3" ><!-- <?=($chkPageEdit)?"":"disabled"?>>-->
					
						<? if($chkPageEdit){?>
						&nbsp;<input type="submit" name="update_th" value=" Save " title="update available therapists">
						<? } ?> 
					<?


} else {
?><br>
					<table width="70%" border="0" align="left" cellpadding="0" cellspacing="0" >
						<tr height="32" style="font-size:13px; font-weight:bold;">
							<td style="text-align:center;background-color:#a8c2cb;">
								<b>Branch</b>
							</td>
							<td style="text-align:center;background-color:#a8c2cb;">
								<b>Therapist Shift 1</b>
							</td>
							<td style="text-align:center;background-color:#a8c2cb;">
								<b>Therapist Shift 2</b>
							</td>
							<td style="text-align:center;background-color:#a8c2cb;">
								<b>Total Therapist Sign-In</b>
							</td>
						
						</tr>
<?
	for ($i = 0; $i < $chkrs["rows"]; $i++) {
		if($i%2==1){
			echo "<tr class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" >\n";
		}else{
			echo "<tr class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" >\n";
		}
?>
							<?
							$sql="SELECT count(bl_th_list.th_id) as totalsign ".
								 "FROM bl_th_list, bl_branchinfo, l_employee ".
								 "WHERE bl_branchinfo.branch_id = bl_th_list.branch_id ".
								 "AND bl_th_list.branch_id = ".$chkrs[$i]["branch_id"]." ".
								 "AND bl_th_list.l_lu_date>=\"" . date("Y-m-d") . "\" " .
								 "AND bl_th_list.leave_time IS NULL ".
								 "AND l_employee.emp_id = bl_th_list.th_id ";
							$totalSign=$obj->getResult($sql);
							?>
							<td class="report" align="center"><?=$chkrs[$i]["branch_name"]?>&nbsp;</td>
							<td class="report" align="center"><?=$chkrs[$i]["th_shiftone"]?>&nbsp;</td>
							<td class="report" align="center"><?=$chkrs[$i]["th_shifttwo"]?>&nbsp;</td>
							<td class="report" align="center"><?=$totalSign[0]["totalsign"]?>&nbsp;</td>
</tr>
<? } ?>
					</table>	
					<br/>
					<? } ?>
			        <input type="hidden" name="cityid" id="cityid" value="<?=$cityid?>">
			        <input type="hidden" name="page" id="page" value="<?=$page?>">
			        <input type="hidden" name="sort" id="sort" value="<?=$sort?>">
			        <input type="hidden" name="order" id="order" value="<?=$order?>">
			        </td>
		  		</tr>
		    	<tr>
		        	<td height="1" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
		    	</tr>
    		</table>  
    	</td>
  	</tr>
  	<? if($chkPageEdit){?>
 	<tr>
    	<td valign="top" height="20px">
    		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td height="30" class="rheader" style="padding-left: 20px;">
			        Therapist Sign-in: <?=$obj->makeTherapistlist("thid",0,0,"l_employee.branch_id,l_employee.emp_code,l_employee.emp_nickname",($cityid)?"bl_branchinfo.city_id=$cityid":"")?> 
			        <!--<input type="hidden" name="now" id="now">-->
			       <?if($ubranch_id!=1){?>
			        <input type="submit" name="signin" value=" In " title="sign in">
			        <?}else{?>
			        <!--<input type="button" name="signin" value=" In " title="sign in" onClick="if(document.getElementById('thid').value!=1){window.open ('addinfo.php?thid='+ (document.getElementById('thid').value)+'&not='+ (document.getElementById('not').value)+'', 'therapist','location=1,top=200,left=500,scrollbars=0, width=250,height=250');}else{this.form.submit();}">-->	
			       	<input type="button" name="signin" value=" In " title="sign in" onClick="sigIn();">
			       <?}?>
			       <input type="checkbox" name="not" id="not" value="1"<?=$not?>>
			    			      
			        No OT 
			         &nbsp;&nbsp;<span id="errormsg" class="style1" ><? if($errormsg!=""){ ?><img src="/images/errormsg.png" /><? } ?>&nbsp;&nbsp;
					<b class="errormsg"><?=$errormsg?></b></span>
					<span style='color:#3875d7'><? if($successmsg!=""){ ?><img src="/images/successmsg.png" /><? } ?>&nbsp;&nbsp;<b class="successmsg"><?=$successmsg?></b></span>
			         </td>
			         	
			       </tr>
			      <tr>
			        <td height="1" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
			      </tr>
    		</table>
  		</td>
	</tr>
	<? } ?>
</table> 
</div>
  	</td>
  </tr>
  <tr>
		<td valign="top" style="margin-top:0px;margin-left:0px">
			<div id="tableDisplay"></div>
		</td>
  </tr>
</table> 
</form> 
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('/images')"/></div>
</body>
</html>