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
$category = $obj->getParameter("category");

$search = $obj->getParameter("search");
$order = $obj->getParameter("order");
$sort= $obj->getParameter("sortby","A &gt Z");

$anotherpara = "and (l_employee.emp_code like '%".$search."%' or lower(l_employee.emp_nickname) like lower('%".$search."%')) ";

$cmschk = $obj->getParameter("commission",false);
if($cmschk==""){$cmschk=false;}

$branch_id = $obj->getParameter("branchid");
$city_id = $obj->getParameter("cityid",false);

if($branch_id==""){$branch_id=$obj->getIdToText("All","bl_branchinfo","branch_id","branch_name");}
$today = date("Ymd");
$branch_id=($obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")=="All")?false:$branch_id;

//$rs = $obj->getrescms($branch_id,$begindate,$enddate,$cmschk,$collapse,false,$city_id,$anotherpara,$order,$sort,$category);
$rs = $obj->getResAgent($branch_id,$begindate,$enddate,$cmschk,$city_id,$collapse,$anotherpara,$order,$sort,$category);

//Get Total Sales
if($collapse=="Expand"){
	$total_book=0;
for($i=0;$i<$rs["rows"];$i++){
	$total_book+=$rs[$i]["total_book"];
}
}
//
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

$reportname = "Booking Agent Reservation Center Report";

$sum_cntbook=array();
$sum_qty_pp=array();
$sum_sr_total=array();
?>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<?if($export!="Excel"){?>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<?}?>
<span class="pdffirstpage"/>	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style=<?=($collapse!="Collapse")?"padding:40 20 50 20;":"padding:10 20 50 20;"?> width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
<? if($collapse=="Collapse"){ ?>
	<tr>
		<td width="10%"></td><td width="15%"></td>
		<td width="10%"></td><td width="15%"></td>
		<td width="20%"></td><td width="15%"></td>
		</td><td width="15%"></td>
	</tr>
<? }else{ ?>
	<tr>
		<td width="30%"></td><td width="35%"></td><td width="35%"></td>
	</tr>
<? } ?>
	<tr>
	    <td class="reporth" width="100%" align="center" colspan="<?=($collapse=="Collapse")?"7":"3"?>" >
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Made By</b></td>
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
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"7":"3"?>" >
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
	    <td width="100%" colspan="<?=($collapse=="Collapse")?"7":"3"?>" align="center" class="reporth" >
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Made By</b></td>
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
					<td class="report" align="left" style="padding-left:10px"><?=$rs[$i]["cms_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$obj->getIdToText($rs[$i]["c_bp_id"],"al_bookparty","bp_name","bp_id")?></td>
					<td class="report" align="center"><?=($rs[$i]["c_set_cms"]==1)?"Yes":"No"?></td>
					<td class="report" align="center"><?=($rs[$i]["qty_pp"]=="")?"&nbsp;":$rs[$i]["qty_pp"]?></td>		
			</tr>
<?
	if(!isset($rs[$i+1]["emp_nickname"])||$rs[$i]["emp_nickname"]!=$rs[$i+1]["emp_nickname"]){
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"7":"3"?>" >
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
	    <td class="reporth" width="100%" align="center" colspan="<?=($collapse=="Collapse")?"7":"3"?>" >
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Made By</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Commissionable</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. People</b></td>
	</tr>
    	
	
<?	
}	
	//$class="class=\"odd\" height=\"20\" style=\"background-color:#d3d3d3;\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";
	$class="bgcolor=\"#d3d3d3\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";
	?>
			<tr <?=$class?> height="20">
					<td class="report" align="left" colspan="3"><b><?=$rs[$i]["emp_nickname"]?></b></td>	
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
 					<td colspan="7" height="20">&nbsp;</td>
 			</tr>
			<tr height="20">
					<td colspan="3" align="center"><b>&nbsp;</b></td>
					<td align="left">&nbsp; </td>
					<td align="right"><b>Total : &nbsp;</b></td>
					<td align="center"><b style='color:#ff0000'><?=$totalbookcnt?>&nbsp;</b><b>bookings</b></td>	
					<td align="center"><b style='color:#ff0000'><?=number_format($totalqty,0,".",",")?> </b><b>persons</b></td>
			</tr>
<? }else{?>
				<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Emp. Code</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Emp. Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Bookings</b></td>
				</tr>
<?
$bookcnt=0;$totalbookcnt=0;$cnt_sum=0;
$totalqty=0;$eachqty=0;$rowcnt=0;$chkrow=$obj->getParameter("chkrow",40);
for($i=0; $i<$rs["rows"]; $i++) {
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"7":"3"?>" >
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
	    <td class="reporth" width="100%" align="center" colspan="<?=($collapse=="Collapse")?"7":"3"?>" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Emp. Code</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Emp. Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Bookings</b></td>
	</tr>
    	
	
<?	
}		
if(!isset($rs[$i]["qty_pp"])){$rs[$i]["qty_pp"]=0;}

$bookcnt++;$totalbookcnt++;$rowcnt++;
$eachqty+=$rs[$i]["qty_pp"];$totalqty+=$rs[$i]["qty_pp"];

$bgcolor="";
if($category!="Department" && $i%2!=0){$bgcolor="bgcolor=\"#eaeaea\"";}else{$cbgcolor="bgcolor=\"#d3d3d3\"";}
if($category!="Department" && !$export){
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
}
if($export!=false){
	$empcode = $rs[$i]["emp_code"];
	$empname = $rs[$i]["emp_nickname"];
}else{
	$empcode = $obj->hightLightChar($search,$rs[$i]["emp_code"]);
	$empname = $obj->hightLightChar($search,$rs[$i]["emp_nickname"]);
}

$dp_category_id[$i]=$rs[$i]["dep_id"];

if(!isset($dp_category_id[$i-1])){$dp_category_id[$i-1]=0;}
if(!isset($dp_category_id[$i])){$dp_category_id[$i]=0;}
if($category=="Department" && $dp_category_id[$i]!=$dp_category_id[$i-1] && $i!=0){
if(!isset($sum_cntbook[$cnt_sum])){$sum_cntbook[$cnt_sum]=0;}
$cnt_sum++;
	$sum_cntbook[$cnt_sum]=$sum_cntbook[$cnt_sum]+$rs[$i]["total_book"];

?>
<tr <?=$cbgcolor?> height="20">
		<td class="report" align="center"><b><?=$obj->getIdToText($dp_category_id[$i-1],"l_employee_department","emp_department_name","emp_department_id")?></b></td>
		<td class="report" align="center"><b>&nbsp;</b></td>
		<td class="report" align="center"><b><?=number_format($sum_cntbook[$cnt_sum-1],0,".",",")?></b></td>		
</tr>
<?
}else{
if(!isset($sum_cntbook[$cnt_sum])){$sum_cntbook[$cnt_sum]=0;}

	$sum_cntbook[$cnt_sum]=$sum_cntbook[$cnt_sum]+$rs[$i]["total_book"];

}
	?>
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?=($empcode=="")?"&nbsp;":$empcode?>&nbsp;</td>
					<td class="report" align="center"><?=($empname=="")?"&nbsp;":$empname?></td>
					<td class="report" align="center"><?=($rs[$i]["total_book"]=="")?"&nbsp;":number_format($rs[$i]["total_book"],0,".",",")?></td>		
			</tr>
	<?
		$bookcnt=0;$eachqty=0;
	//}
if($category=="Department" && ($i+1)==$rs["rows"]){
?>
<tr <?=$cbgcolor?> height="20">
		<td class="report" align="center"><b><?=$obj->getIdToText($dp_category_id[$i-1],"l_employee_department","emp_department_name","emp_department_id")?></b></td>
		<td class="report" align="center"><b>&nbsp;</b></td>
		<td class="report" align="center"><b><?=number_format($sum_cntbook[$cnt_sum],0,".",",")?></b></td>		
</tr>
<?	
}
}?>
 			<tr height="20">
 					<td colspan="3">&nbsp;</td>
 			</tr>
 			<tr height="20">
					<td colspan="2" align="right"><b>Total Bookings : </b><b style='color:#ff0000'><?=number_format($total_book,0,".",",")?></b></td>
					<td>&nbsp;</td>
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
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"7":"3"?>" ><br>
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