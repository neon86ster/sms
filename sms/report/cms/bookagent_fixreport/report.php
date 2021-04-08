<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
require_once("commission.inc.php");
$robj = new report();
$obj = new commission();
$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$begin_date = $begindate;
$enddate= $obj->getParameter("end");
$end_date = $enddate;
$collapse = $obj->getParameter("Collapse","Collapse");
$cmschk = $obj->getParameter("commission",false);
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");
if($cmschk==""){$cmschk=false;}

$branch_id = $obj->getParameter("branchid");
$city_id = $obj->getParameter("cityid",false);

if($branch_id==""){$branch_id=$obj->getIdToText("All","bl_branchinfo","branch_id","branch_name");}
$today = date("Ymd");
$branch_id=($obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")=="All")?false:$branch_id;
$rs = $obj->getcms($branch_id,$begindate,$enddate,$cmschk,$collapse,false,$city_id,$order,$sort);
$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Commission Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&chkrow=40&gmt=".$_SESSION["__gmt"]);
}
$branchname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id");


	 //Get All Branch
        $sql = "select branch_id, branch_name from bl_branchinfo where branch_id<>1 ";
        		if($city_id){$sql .= "and city_id=".$city_id." ";}else
        		if($branch_id){$sql .= "and branch_id=".$branch_id." ";}
        $sql.= "and branch_active=1 order by branch_name asc";
        $rsBranch = $obj->getResult($sql);
        

    			for($j=0; $j<$rsBranch["rows"]; $j++){
    				$nbranchdetail[$j] = $rsBranch[$j]["branch_name"];
    			}
    			if($nbranchdetail){
  	  				$NbranchSrdString = implode(", ", $nbranchdetail); 
  				}

//if($branch_id==0){
$reportname = "Booking Agent Commission Report";
//}else{
//$reportname = "$branchname - Booking Agent Commission Report";
//}
?>
<script type="text/javascript" src="../scripts/component.js"></script>
<?if($export!="Excel"){?>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<?}?>
<span class="pdffirstpage"/>	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
<? if($collapse=="Collapse"){ ?>
	<tr>
		<td width="10%"></td><td width="10%"></td>
		<td width="10%"></td><td width="15%"></td>
		<td width="20%"></td><td width="10%"></td>
		<td width="10%"></td><td width="15%"></td>
	</tr>
<? }else{ ?>
	<tr>
		<td width="25%"></td><td width="25%"></td>
		<td width="25%"></td><td width="25%"></td>
	</tr>
<? } ?>
	<tr>
	    <td class="reporth" width="100%" align="center" colspan="<?=($collapse=="Collapse")?"8":"4"?>" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
<? if($collapse=="Collapse"){ ?>
				<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Hotel Accommodation</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone Number</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Commissionable</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. People</b></td>
				</tr>
<?
$bookcnt=0;$totalbookcnt=0;
$totalqty=0;$eachqty=0;$rowcnt=0;$chkrow=$obj->getParameter("chkrow",40);
for($i=0; $i<$rs["rows"]; $i++) {
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"8":"4"?>" >
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
		<td width="10%"></td><td width="10%"></td>
		<td width="10%"></td><td width="15%"></td>
		<td width="20%"></td><td width="10%"></td>
		<td width="10%"></td><td width="15%"></td>
	</tr>
	<tr>
	    <td width="100%" colspan="<?=($collapse=="Collapse")?"8":"4"?>" align="center" class="reporth" >
    		<b>
    		<p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Hotel Accommodation</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone Number</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Commissionable</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. People</b></td>
	</tr>
    	
	
<?	
}	
	
$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
$pagename = "manageBooking".$rs[$i]["book_id"];
$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");

$bookcnt++;$totalbookcnt++;$rowcnt++;
$eachqty+=$rs[$i]["qty_pp"];$totalqty+=$rs[$i]["qty_pp"];
if($export!=false){
	$id=$bpdsid;
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">$bpdsid</a>";
}
	$class=" class=\"even\" height=\"20\" style=\"background-color:#eaeaea;\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";
?>
			<tr height="20" <?=$class?>>
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?></td>
					<td class="report" align="center"><?=$obj->getIdToText($rs[$i]["b_branch_id"],"bl_branchinfo","branch_name","branch_id")?></td>
					<td class="report" align="center"><?=$rs[$i]["b_accomodations_id"]==1?"&nbsp":$obj->getIdToText($rs[$i]["b_accomodations_id"],"al_accomodations","acc_name","acc_id")?></td>
					<td class="report" align="left" style="padding-left:10px"><?=$rs[$i]["cms_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=($rs[$i]["cms_phone"]=="")?"&nbsp;":$rs[$i]["cms_phone"]?></td>
					<td class="report" align="center"><?=($rs[$i]["c_set_cms"]==1)?"Yes":"No"?></td>
					<td class="report" align="center"><?=($rs[$i]["qty_pp"]=="")?"&nbsp;":$rs[$i]["qty_pp"]?></td>		
			</tr>
<?
	if(!isset($rs[$i+1]["cms_company_name"])||$rs[$i]["cms_company"]!=$rs[$i+1]["cms_company"]){
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"8":"4"?>" >
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
		<td width="10%"></td><td width="10%"></td>
		<td width="10%"></td><td width="15%"></td>
		<td width="20%"></td><td width="10%"></td>
		<td width="10%"></td><td width="15%"></td>
	</tr>
	<tr>
	    <td class="reporth" width="100%" align="center" colspan="<?=($collapse=="Collapse")?"8":"4"?>" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Hotel Accommodation</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone Number</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Commissionable</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. People</b></td>
	</tr>
    	
	
<?	
}	
	$class="class=\"odd\" height=\"20\" style=\"background-color:#d3d3d3;\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";
	?>
			<tr <?=$class?>  height="20">
					<td class="report" align="left" colspan="3"><b><?=$rs[$i]["cms_company_name"]?></b></td>	
					<td class="report">&nbsp;</td>
					<td class="report">&nbsp;</td>
					<td class="report">&nbsp;</td>
					<td class="report" align="center"><b><?=$bookcnt?>&nbsp;bookings</b></td>
					<td class="report" align="center"><b><?=$eachqty?>&nbsp;persons</b></td>	
			</tr>
	<?
		$rowcnt++;
		$bookcnt=0;$eachqty=0;
	}
	
	
}
?>
 			<tr height="20">
 					<td colspan="8" height="20">&nbsp;</td>
 			</tr>
			<tr height="20">
					<td colspan="4" align="center"><b>&nbsp;</b></td>
					<td align="left">&nbsp; </td>
					<td align="right"><b>Total : &nbsp;</b></td>
					<td align="center"><b style='color:#ff0000'><?=$totalbookcnt?>&nbsp;</b><b>bookings</b></td>	
					<td align="center"><b style='color:#ff0000'><?=number_format($totalqty,0,".",",")?> </b><b>persons</b></td>
			</tr>
<? }else{?>
				<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Agent</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Bookings</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Customers</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Sales</b></td>
				</tr>
<?
$bookcnt=0;$totalbookcnt=0;
$totalqty=0;$eachqty=0;$rowcnt=0;$chkrow=$obj->getParameter("chkrow",40);
for($i=0; $i<$rs["rows"]; $i++) {
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"8":"4"?>" >
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
		<td width="25%"></td><td width="25%"></td>
		<td width="25%"></td><td width="25%"></td>
	</tr>
	<tr>
	    <td class="reporth" width="100%" align="center" colspan="<?=($collapse=="Collapse")?"8":"4"?>" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Agent</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Bookings</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Customers</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Sales</b></td>
	</tr>
    	
	
<?	
}		
$bookcnt++;$totalbookcnt++;$rowcnt++;
$eachqty+=$rs[$i]["qty_pp"];$totalqty+=$rs[$i]["qty_pp"];
	if(!isset($rs[$i+1]["cms_company_name"])||$rs[$i]["cms_company"]!=$rs[$i+1]["cms_company"]){
$class = "class=\"odd\" height=\"20\" style=\"background-color:#d3d3d3;\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";
	?>
			<tr <?=$class?> height="20">
					<td class="report" align="left"><?=$rs[$i]["cms_company_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=($rs[$i]["cntbook"]=="")?"&nbsp;":$rs[$i]["cntbook"]?></td>
					<td class="report" align="center"><?=($rs[$i]["qty_pp"]=="")?"&nbsp;":$rs[$i]["qty_pp"]?></td>		
					<td class="report" align="right"><?=$rs[$i]["sr_total"]?number_format($rs[$i]["sr_total"],2,".",","):"0.00"?></td>
			</tr>
	<?
		$bookcnt=0;$eachqty=0;
	}
}?>
 			<tr height="20">
 					<td colspan="4">&nbsp;</td>
 			</tr>
			<tr height="20">
					<td colspan="3" align="right"><b>Total Customers : </b></td>
					<td align="center"><b style='color:#ff0000'><?=number_format($totalqty,0,".",",")?></b></td>
			</tr>
<? 
}
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
?>
    <tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"8":"4"?>" ><br>
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
		    <?=($export)?'':'<p>SMS page generated in '.$total_time.' seconds.</p>'."\n";?>
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