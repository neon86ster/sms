<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
require_once("commission.inc.php");

$robj = new report();
$obj = new commission();

$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate= $obj->getParameter("end");

$branch = $obj->getParameter("branchid",false);
$city = $obj->getParameter("cityid",false);
$hotelid = $obj->getParameter("hotelid");
$id = ""; $table="";
if(strstr($hotelid,"al_accomodations")){
	$table="al_accomodations";
	$id=str_replace("al_accomodations","",$hotelid);
}
if(strstr($hotelid,"al_bookparty")){
	$table="al_bookparty";
	$id=str_replace("al_bookparty","",$hotelid);
}
if($branch==""){$branch=0;}
if($city==""){$city=0;}
$today = date("Ymd");
$obj->setDebugStatus();
$rs = $obj->gethotelcms($id,$table,$begindate,$enddate,$branch,$city);
$export = $obj->getParameter("export",false);
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",25);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Commission Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if(strstr($hotelid,"al_accomodations")){
	$reportname = $obj->getIdToText($id,"al_accomodations","acc_name","acc_id")." - Exclusive Commission Agreement Report";
}
if(strstr($hotelid,"al_bookparty")){
	$reportname = $obj->getIdToText($id,"al_bookparty","bp_name","bp_id")." - Exclusive Commission Agreement Report";
}
if($hotelid==""){
	$reportname = "Exclusive Commission Agreement Report";
}
$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
?>
<?
//Get All Branch
        $sql = "select branch_id, branch_name from bl_branchinfo where branch_id<>1 ";
        		if($city){$sql .= "and city_id=".$city." ";}else
        		if($branch){$sql .= "and branch_id=".$branch." ";}
        $sql.= "and branch_active=1 order by branch_name asc";
        $rsBranch = $obj->getResult($sql);
        

    			for($j=0; $j<$rsBranch["rows"]; $j++){
    				$nbranchdetail[$j] = $rsBranch[$j]["branch_name"];
    			}
    			if($nbranchdetail){
  	  				$NbranchSrdString = implode(", ", $nbranchdetail); 
  				}
?>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>	
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="14%"></td>
		<td width="6%"></td><td width="6%"></td>
		<td width="6%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="17%"></td><td width="5%"></td>
	</tr>
	<tr>
    	<td width="100%" align="center" colspan="11" class="reporth">
    		<b>
    		<p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p>
    			<b style='color:#ff0000;'>
    			<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
    			</b></p>
    			<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Time</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>NO. of people</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS Percents</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Have CMS</b></td>
	</tr>
<?
$total_cms=0;$totalpp=0;
$rowcnt=0; $total=0;
for($i=0; $i<$rs["rows"]; $i++) {
if($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="11" ><br>
    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
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
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="14%"></td>
		<td width="6%"></td><td width="6%"></td>
		<td width="6%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="17%"></td><td width="5%"></td>
	</tr>
	<tr>
    	<td width="100%" class="reporth" align="center" colspan="11">
    		<b>
    		<p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p>
    			<b style='color:#ff0000;'>
    			<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
    			</b></p>
    			<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Time</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>NO. of people</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS Percents</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Have CMS</b></td>
	</tr>
    	
	
<?	
}	
$total += $rs[$i]["total"];
$total_cms += $rs[$i]["cms"];
$rowcnt++;
$totalpp += $rs[$i]["qty_pp"];
$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
$pagename = "manageBooking".$rs[$i]["book_id"];
$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
$class = "";
$bgcolor="";
if($export!=false){
	$id=$bpdsid;
	if($i%2!=0){$class="bgcolor=\"#EAEAEA\"";}
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','".$pagename."')\" class=\"menu\">".$bpdsid."</a>";
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
}		
?>
				<tr height="20" <?=$class?><?=$bgcolor?>>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=($rs[$i]["branch_name"]==" -- select --")?"-----":$rs[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="left"><?=$rs[$i]["cs_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=substr($rs[$i]["appt_time"], 0, 5)?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["hotel_room"]?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["qty_pp"]?>&nbsp;</td>	
					<td class="report" align="right"><?=number_format($rs[$i]["total"],2,".",",")?>&nbsp;</td>
					<td class="report" align="right"><?=number_format($rs[$i]["cmspercent"],2,".",",")?>&nbsp;%</td>
					<td class="report" align="right"><?=number_format($rs[$i]["cms"],2,".",",")?>&nbsp;</td>	
					<td class="report" align="left">&nbsp;&nbsp;<?=$rs[$i]["cms_name"]?></td>	
<?
				if($rs[$i]["c_set_cms"]) {
					echo "<td class=\"report\" align=\"center\"><b style='color:#ff0000'>Yes</b></td>";
				}
				else {
					echo "<td class=\"report\" align=\"center\"><b>No</b></td>";
				}
	echo "</tr>";
}
?>
 				<tr height="20">
 					<td colspan="11">&nbsp;</td>
 				</tr>
				<tr height="20">
					<td colspan="9" align="right"><b>Total People : </b></td>
					<td align="right"><b style='color:#ff0000'><?=number_format($totalpp,0,".",",")?></b></td>
					<td>&nbsp;</td>
				</tr>
				<tr height="20">
					<td colspan="9" align="right"><b>Total Commission : </b></td>
					<td align="right"><b style='color:#ff0000'><?=number_format($total_cms,2,".",",")?></b></td>
					<td>&nbsp;</td>
				</tr>
			    <tr>
			    	<td width="100%" align="center" colspan="11" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
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