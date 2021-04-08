<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("rm.inc.php");
require_once ("membership.inc.php");

$obm = new membership();
$obj = new rm();

$search = $obj->getParameter("search",false);
$categoryid = $obj->getParameter("categoryid",0);
$order = $obj->getParameter("order","member_code");
$sortby = $obj->getParameter("sortby","Z &gt; A");
$showinactive=$obj->getParameter("showinactive",0);
$chksearch = $obj->convert_char($search);
$rs = $obj->getmembership($chksearch,$categoryid,$showinactive,$order,$sortby);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Membership Relationship Management.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf(false,false,true);
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=pdf&gmt=".$_SESSION["__gmt"]);
}
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",25);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
$reportname = "Membership Relationship Management";
if(!$categoryid){
	$reportname = "Membership Relationship Management";
}else{
	$categoryname = $obj->getIdToText($categoryid,"mb_category","category_name","category_id");
	$reportname = $categoryname." Relationship Management";
}
?>
<?if($export!="Excel"){?>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<?}?>
<span class="pdffirstpage"/>	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="4%"></td><td width="10%"></td>
		<td width="9%"></td><td width="4%"></td>
		<td width="5%"></td><td width="5%"></td>
		<td width="5%"></td>
		<td width="5%"></td><td width="9%"></td>
		<td width="4%"></td><td width="4%"></td>
		<td width="5%"></td><td width="4%"></td>
		<td width="4%"></td><td width="9%"></td>
		<td width="7%"></td><td width="7%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="17">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br><br>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Member Code</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Member name</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Category</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gender</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Nationality</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Sign Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Expired Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Birth date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Address</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>City</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>State</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Zip Code</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Mobile</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Email</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>YTD</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>LTD</b></td>
	</tr>
<?
$bookcnt=0;$rowcnt=0;$chkrow=25;
if($export=="print"){
	$chkrow=10;
}
for($i=0; $i<$rs["rows"]; $i++) {
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20"><td colspan="17" height="20">&nbsp;</td></tr>
	<tr>
    	<td width="100%" align="center" colspan="17" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="4%"></td><td width="10%"></td>
		<td width="9%"></td><td width="4%"></td>
		<td width="5%"></td><td width="5%"></td>
		<td width="5%"></td>
		<td width="5%"></td><td width="9%"></td>
		<td width="4%"></td><td width="4%"></td>
		<td width="5%"></td><td width="4%"></td>
		<td width="4%"></td><td width="9%"></td>
		<td width="7%"></td><td width="7%"></td>
	</tr>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="17" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br><br>
    	</td>
	</tr>
	<tr height="32">	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Member Code</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Member name</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Category</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gender</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Nationality</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Sign Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Expired date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Birth date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Address</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>City</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>State</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Zip Code</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Mobile</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Email</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>YTD</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>LTD</b></td>			
	</tr>
<?	
}	
$rowcnt++;

$bgcolor="";
if($i%2!=0){$bgcolor="bgcolor=\"#d3d3d3\"";}else{$bgcolor="bgcolor=\"#eaeaea\"";}
$expireddate = str_replace("-","",$rs[$i]["expireddate"]);
if($rs[$i]["expired"]==0||($rs[$i]["expireddate"]!="0000-00-00"&&$expireddate<date("Ymd"))){$bgcolor="bgcolor=\"#ffb9b9\"";}
if(!$export){
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";}
	else{$bgcolor=" class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
	if($rs[$i]["expired"]==0||($rs[$i]["expireddate"]!="0000-00-00"&&$expireddate<date("Ymd"))){$bgcolor="class=\"paidconfirm\"";}
}
if($export!=false){
	$code = $rs[$i]["member_code"];
	$name = $rs[$i]["fname"]." ".$rs[$i]["mname"]." ".$rs[$i]["lname"];
	$phone = $rs[$i]["phone"];
	$mobile = $rs[$i]["mobile"];
	$email = $rs[$i]["email"];
}else{
	$code = $obj->hightLightChar($search,$rs[$i]["member_code"]);
	$name = $obj->hightLightChar($search,$rs[$i]["fname"]." ".$rs[$i]["mname"]." ".$rs[$i]["lname"]);
	$phone = $obj->hightLightChar($search,$rs[$i]["phone"]);
	$mobile = $obj->hightLightChar($search,$rs[$i]["mobile"]);
	$email = $obj->hightLightChar($search,$rs[$i]["email"]);
}
?>
<?
			$rssr = $obm->getmembersr($code);
			$ltd = $obm->getsramount($rssr);
			$chkdate = date("Ymd", mktime(0, 0, 0, 1, 1, date("Y"))); // first date of year
			$ytd = $obm->getsramount($rssr, $chkdate);
		
		//for sort YTD & LTD update data in DB
			if($ytd!=$rs[$i]["ytd"]){
			$u_ytd  = "UPDATE m_membership SET `ytd` = '" . $ytd . "' WHERE member_id =".$rs[$i]["member_id"];
			$uyld = $obm->setResult($u_ytd);
			}
			
			if($ltd!=$rs[$i]["ltd"]){
			$u_ltd = "UPDATE m_membership SET `ltd` = '" . $ltd . "' WHERE member_id =".$rs[$i]["member_id"];
			$ultd = $obm->setResult($u_ltd);
			}
		//end
?>	
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?if(!$export){?><a class="menu" href="javascript:;" onClick="window.open('/spamg/membership/history_membership.php?memberId=<?=$rs[$i]["member_code"]?>','memberHistory',
		'scrollbars=1, top=0, left=0, resizable=yes' +',width=' + (screen.width) +',height=' + (screen.height));" ><?=$code?></a><?}else{?><b><?=$code?></b><?}?></td>
					<td class="report" align="left"><?=$name?></td>
					<td class="report" align="center"><?=$rs[$i]["category_name"]?></td>
					<td class="report" align="center"><?=$rs[$i]["sex_type"]?></td>
					<td class="report" align="center"><?=$rs[$i]["nationality_name"]?></td>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["joindate"],"Y-m-d",$sdateformat)?></td>
					<td class="report" align="center"><?=$rs[$i]["expireddate"]=="0000-00-00"?"Unlimited":$dateobj->convertdate($rs[$i]["expireddate"],"Y-m-d",$sdateformat)?></td>
					<td class="report" align="center"><?=($rs[$i]["birthdate"]=="0000-00-00")?"-":$dateobj->convertdate($rs[$i]["birthdate"],"Y-m-d",$sdateformat)?></td>
					<td class="report" align="left"><?=($export && $export=="Excel")?str_replace("[br]"," ",$rs[$i]["address"]):str_replace("[br]","<br>",$rs[$i]["address"])?></td>
					<td class="report" align="center"><?=$rs[$i]["city"]?></td>
					<td class="report" align="center"><?=$rs[$i]["state"]?></td>
					<td class="report" align="center"><?=$rs[$i]["zipcode"]?></td>
					<td class="report" align="left"><?if(!$export or $export=="print"){?><?=$rs[$i]["chk_phone"]?"<img src=\"/images/active.png\" border=\"0\" title=\"yes\" />":"<img src=\"/images/inactive.png\" border=\"0\" title=\"no\" />"?><?}?><?if(!$export){?><?=($phone=="")?"&nbsp;":$phone?><?}else{if($rs[$i]["chk_phone"]!=0){?><?=$phone?><?}}?></td>
					<td class="report" align="left"><?if(!$export or $export=="print"){?><?=$rs[$i]["chk_mobile"]?"<img src=\"/images/active.png\" border=\"0\" title=\"yes\" />":"<img src=\"/images/inactive.png\" border=\"0\" title=\"no\" />"?><?}?><?if(!$export){?><?=($mobile=="")?"&nbsp;":$mobile?><?}else{if($rs[$i]["chk_mobile"]!=0){?><?=$mobile?><?}}?></td>
					<td class="report" align="left"><?if(!$export or $export=="print"){?><?=$rs[$i]["chk_email"]?"<img src=\"/images/active.png\" border=\"0\" title=\"yes\" />":"<img src=\"/images/inactive.png\" border=\"0\" title=\"no\" />"?><?}?><?if(!$export){?><?=($email=="")?"&nbsp;":$email?><?}else{if($rs[$i]["chk_email"]!=0){?><?=$email?><?}}?></td>	
					<td class="report" align="right"><?=number_format($ytd,2,".",",")?></td>
					<td class="report" align="right"><?=number_format($ltd,2,".",",")?></td>
			</tr>
<?
}
?>
	<tr height="20"><td colspan="17" height="20">&nbsp;</td></tr>
    <tr>
    	<td width="100%" align="center" colspan="17" ><br>
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>

<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>