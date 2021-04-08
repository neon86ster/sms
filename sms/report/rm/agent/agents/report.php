<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
require_once("rm.inc.php");

$robj = new report();
$obj = new rm();

$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$search = $obj->getParameter("search",false);
$branch = $obj->getParameter("branchid",0);
$city = $obj->getParameter("cityid",0);
$today = date("Ymd");
$chksearch = $obj->convert_char($search);
$rs = $obj->getcms($begin_date,$end_date,$chksearch,$branch,$city);
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);

if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Agents Relationship Management.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
$reportname = "Agents Relationship Management";
if(!$branch){
	if($city){
		$cityname = $obj->getIdToText($city,"al_city","city_name","city_id");
		$reportname = $cityname."'s ".$reportname;
	}else{
		$reportname = "All branch's ".$reportname;
	}
}else{
	$branchname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
	$reportname = $branchname."'s ".$reportname;
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
		<td width="15%"></td><td width="7%"></td>
		<td width="7%"></td><td width="20%"></td>
		<td width="10%"></td><td width="15%"></td>
		<td width="18%"></td>
	</tr>
	<tr>
	    <td class="reporth" width="100%" align="center" colspan="7" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br><br></p>
    	</td>
	</tr>
				<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Agent</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Bookings</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Customers</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>BP. Address</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>BP. Phone</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>BP. E-Mail</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>BP. Detail</b></td>
				</tr>
<?
$bookcnt=0;$totalbookcnt=0;
$totalqty=0;$eachqty=0;$rowcnt=0;$chkrow=$obj->getParameter("chkrow",40);
for($i=0; $i<$rs["rows"]; $i++) {
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="7" >
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
		<td width="15%"></td><td width="7%"></td>
		<td width="7%"></td><td width="20%"></td>
		<td width="10%"></td><td width="15%"></td>
		<td width="18%"></td>
	</tr>
	<tr>
	    <td class="reporth" width="100%" align="center" colspan="7" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Agent</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Bookings</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Customers</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>BP. Address</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>BP. Phone</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>BP. E-Mail</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>BP. Detail</b></td>
	</tr>
    	
	
<?	
}		
$bookcnt++;$totalbookcnt++;$rowcnt++;
$eachqty+=$rs[$i]["qty_pp"];$totalqty+=$rs[$i]["qty_pp"];
	if(!isset($rs[$i+1]["cms_company_name"])||$rs[$i]["cms_company"]!=$rs[$i+1]["cms_company"]){
$class = "class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";
if($i%2==0){$class = "class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
$bp_address = str_replace("[br]","<br>",$rs[$i]["bp_address"]);
$bp_detail = str_replace("[br]","<br>",$rs[$i]["bp_detail"]);
$onclick = "";
if(!$export){
		$onclick = "style=\"cursor: pointer;\" onClick=\"javascript:openDetail('AgentsRM','$begin_date','$end_date','$branch','$city','".$rs[$i]["bp_id"]."');\"";
}
	?>
			<tr <?=$class?> height="20" <?=$onclick?>>
					<td class="report" align="left">&nbsp;<?=($rs[$i]["bp_id"]==1)?"-------":$obj->hightLightChar($search,$rs[$i]["company_name"])?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["cntbook"]+0?></td>
					<td class="report" align="center"><?=$rs[$i]["qty_pp"]+0?></td>		
					<td class="report" align="left"><?=$obj->hightLightChar($search,$bp_address)?>&nbsp;</td>		
					<td class="report" align="center"><?=($rs[$i]["bp_phone"]=="")?"&nbsp;":$obj->hightLightChar($search,$rs[$i]["bp_phone"])?></td>		
					<td class="report" align="left"><?=($rs[$i]["bp_email"]=="")?"&nbsp;":$obj->hightLightChar($search,$rs[$i]["bp_email"])?></td>		
					<td class="report" align="left"><?=$obj->hightLightChar($search,$bp_detail)?>&nbsp;</td>		
			</tr>
	<?
		$bookcnt=0;$eachqty=0;
	}
}?>
 			<tr height="20">
 					<td colspan="7">&nbsp;</td>
 			</tr>
			<tr height="20">
					<td colspan="2" align="right"><b>Total Customers : </b></td>
					<td align="center"><b style='color:#ff0000'><?=number_format($totalqty,0,".",",")?></b></td>
			</tr>
    <tr height="20">
    	<td width="100%" align="center" colspan="7" ><br>
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