<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
require_once("csi.inc.php");
$robj = new report();
$obj = new csi();
$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate = $obj->getParameter("end");

$branch_id = $obj->getParameter("branchid",0);
$cityid = $obj->getParameter("cityid",false);
$today = date("Ymd");
$branch_name = strtolower($obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id"));
/*
if($branch_id){
	$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")."'s Customer Comments Report";
}else{
	$reportname = "All Customer Comments Report";
}*/
$reportname = "Customer Comments Report";
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"$reportname.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	//$pdf->convertFromFile("manage_cplinfo3.htm");
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
if($export!="Excel"&&$export){
	if(!isset($rs["rows"])){$rs["rows"]=0;}
	$chkrow = $obj->getParameter("chkrow",38);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
$rs=$obj->getComment($begindate,$enddate,$branch_id,$cityid);
$begin_date = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$end_date = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);

?>
<?
//Get All Branch
        $sql = "select branch_id, branch_name from bl_branchinfo where branch_id<>1 ";
        		if($cityid){$sql .= "and city_id=".$cityid." ";}else
        		if($branch_id){$sql .= "and branch_id=".$branch_id." ";}
        $sql.= "and branch_active=1 order by branch_name asc";
        $rsBranch = $obj->getResult($sql);
        

    			for($j=0; $j<$rsBranch["rows"]; $j++){
    				$nbranchdetail[$j] = $rsBranch[$j]["branch_name"];
    			}
    			if($nbranchdetail){
  	  				$NbranchSrdString = implode(", ", $nbranchdetail); 
  				}
?>
<script type="text/javascript" src="../scripts/component.js"></script>
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="10%"></td><td width="10%"></td><td width="15%"></td><td width="65%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="4">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begin_date,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($end_date,$sdateformat,$ldateformat)?></b></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Appointment Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Comment &amp; Suggestion</b></td>
	</tr>
<?	$rowcnt=0;
for($i=0; $i<$rs["rows"]; $i++) {
if($export!="Excel"&&$export&&$rowcnt%$chkrow==0&&$i){
?>
	<tr>
    	<td width="100%" align="center" colspan="4" ><br>
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
		<td width="10%"></td><td width="10%"></td><td width="15%"></td><td width="65%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="4">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begin_date,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($end_date,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Appointment Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Comment &amp; Suggestion</b></td>
	</tr>
    	
	
<?	
}	

$rowcnt++;
$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
$pagename = "manageBooking".$rs[$i]["book_id"];
$branchname = $rs[$i]["branch_name"];
if($export!=false){
	$id=$rs[$i]["bpds_id"];
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\">".$rs[$i]["bpds_id"]."</a>";
}
$bgcolor="";
if($i%2!=0){$bgcolor="bgcolor=\"#eaeaea\"";}else{$cbgcolor="bgcolor=\"#eaeaea\"";}
if(!$export){
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
}
	?>
				<tr height="20" <?=$bgcolor?>>
					<td align="center" class="report"><b><?=$id?></b></td>
					<td align="center" class="report"><?=$branchname?></td>
					<td align="center" class="report"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?></td>
					<td align="left" class="report">&nbsp;&nbsp;<?=$rs[$i]["csi_comment"]?></td>
 				</tr>
 				<?	} ?>
 				<tr height="20">
 					<td colspan="4" height="20">&nbsp;</td>
 				</tr>
				<tr height="20">
					<td colspan="4" align="center" height="20" valign="top" style="padding-right:7px;">
						<b style="color:#ff0000;">* </b><b>already take out blank and "n/a" field</b>
					</td>
				</tr>
			    <tr height="20">
			    	<td width="100%" align="center" colspan="4" ><br>
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