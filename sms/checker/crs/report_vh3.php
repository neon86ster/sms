<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
$obj = new checker();

$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid");
//For undefined variable : ttcs. By Ruck : 20-05-2009
$ttcs=0;

if($branch_id==""){$branch_id=0;}
$export = $obj->getParameter("export",false);

if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print");
}

$rs = $obj->getcrs($branch_id,$begin_date,$end_date);
$rs_ttcs = $obj->getttcs($branch_id,$begin_date,$end_date);
$rs_tthour = $obj->gettthour($branch_id,$begin_date,$end_date);
if(!$rs_tthour){$rs_tthour[0]["total"]=0;}

if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",30);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Closing Report Specific.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}


$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")." Closing Report Details";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
?>

<?
$header = "\t<tr>\n";
$header .= "\t\t<td width=\"100%\" align=\"center\" colspan=\"11\" ><br>\n";
$header .= "\t\t\t<b>Printed: </b>".$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")."\n";
$header .= "\t\t</td>\n";
$header .= "\t</tr>\n";
$header .= "</table></td>\n";
$header .= "\t</tr>\n";
$header .= "</table>\n";
$header .= "<hr style=\"page-break-before:always;border:0;color:#ffffff;\" />\n";
$header .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td valign=\"top\" style=\"padding:10 20 50 20;\" width=\"100%\" align=\"center\">\n";
$header .= "\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td width=\"8%\"></td><td width=\"8%\"></td>\n";
$header .= "\t\t<td width=\"12%\"></td><td width=\"15%\"></td>\n";
$header .= "\t\t<td width=\"7%\"><td width=\"7%\"></td><td width=\"10%\"></td>\n";
$header .= "\t\t<td width=\"10%\"></td><td width=\"10%\"></td>\n";
$header .= "\t\t<td width=\"8%\"></td><td width=\"5%\"></td>\n";
$header .= "\t</tr>\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td class=\"reporth\" width=\"100%\" align=\"center\" colspan=\"11\">\n";
$header .= "\t\t\t<b><p>Spa Management System</p>\n";
$header .= "\t\t\t$reportname</b><br>\n";
$header .= "\t\t\t<p><b style='color:#ff0000'>";
$header .= $dateobj->convertdate($begindate,$sdateformat,$ldateformat);
$header .= ($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat);
$header .= "<b><br><br></p>\n";
$header .= "\t\t</td>\n";
$header .= "\t</tr>\n";
$header .= "\t<tr height=\"32\">\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Receipt No.</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Booking ID</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Customer Name</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Room</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Date</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Time</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Method of Payment</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Total</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Cashier</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Reprinted</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>CMS</b></td>\n";
$header .= "\t</tr>\n";
?>

<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="8%"></td><td width="8%"></td>
		<td width="12%"></td><td width="15%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="10%"></td><td width="10%"></td>
		<td width="10%"></td><td width="8%"></td>
		<td width="5%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="11">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Receipt No.</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Time</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Cashier</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Reprinted</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
	</tr>
<?
$all_total=0;		
$rowcnt=0;
$paytype["type"] = array();
$paytype["value"] = array();
$payvalue = array();
$pay_index = 1;
$Srdetail = array ();
$oldSrd = array ();
$Srdold = array ();
$PayId = array ();
$srcound = 0;
for($i=0; $i<$rs["rows"]; $i++) {
	
// separate page when export
if($export!="Excel"&&$export&&$rowcnt%$chkrow==0&&$i){
	echo $header;	$rowcnt=0;
}	

// summary each payment's total
$keyword = ($rs[$i]["pay_id"]!=1)?$rs[$i]["pay_name"]:"Unknown";
$key = array_search($keyword, $paytype["type"]);
if(!$key) {	
		$key = $pay_index;
		$pay_index++;
}
if(!isset($paytype["value"][$key])){$paytype["value"][$key]=0;}
$paytype["type"][$key] = $keyword;
$paytype["value"][$key] += $rs[$i]["sr_total"];

// each rows' color
$bgcolor = "#eaeaea"; $class = "even";
if($i%2==0){
	$bgcolor = "#d3d3d3"; $class = "odd";
}
if($rs[$i]["paid_confirm"]==0){
	$bgcolor = "#ffb9b9"; $class = "paidconfirm";
}

// define booking id links
$url = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rs[$i]["book_id"]."";
$pagename = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rs[$i]["book_id"]:"managePds".$rs[$i]["book_id"];
if($export!=false){
	$id="<b>".$rs[$i]["bpds_id"]."</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rs[$i]["bpds_id"]."</a>";
}	

// define room in each room 
if($rs[$i]["tb_name"]=="a_bookinginfo"){
	$roomname=array();
	$sqlRoom = "select distinct room_name from d_indivi_info,bl_room " .
				"where d_indivi_info.room_id=bl_room.room_id " .
				"and book_id =".$rs[$i]["book_id"] ;
	$rsRoom = $obj->getResult($sqlRoom);
	for($j=0; $j<$rsRoom["rows"]; $j++){
		$roomname[$j]=$rsRoom[$j]["room_name"];
	}
	sort($roomname);
	$rname = implode(", ",array_filter($roomname));
}else{
	$rname = "-";
}

// define another value
$payname = ($rs[$i]["pay_id"]>1)?$rs[$i]["pay_name"]:"-";
$all_total+=$rs[$i]["sr_total"];
$sr_id = $rs[$i]["salesreceipt_id"];
$cashier =($rs[$i]["u"]==null)?"-": $rs[$i]["u"];
$reprint = ($rs[$i]["reprint_times"]==0)?"-":$rs[$i]["reprint_times"];
$cms = ($rs[$i]["cms"])?"<span style='color:#ff0000'>yes</span>":"<span style='color:#ff0000'>no</span>";
$rowcnt++;	

$sqlcMp = "select salesreceipt_id from c_srpayment where salesreceipt_id=".$rs[$i]["salesreceipt_id"]."";
		$cmpId = $obj->getResult($sqlcMp);
		
	if($cmpId["rows"]>1){
		$bgcolor = "#eaf7cc"; $class = "multipay";
	}	
		
?>			
			<tr bgcolor="<?=$bgcolor?>" class="<?=$class?>" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='<?php echo $bgcolor; ?>'">   			
					<td class="report" align="center"><?=($rs[$i]["salesreceipt_number"])?$rs[$i]["salesreceipt_number"]:"-"?></td>
					<td class="report" align="center"><?=$id?></td>
					<td class="report"><?=$rs[$i]["customer_name"]?></td>
					<td class="report"><?=$rname?></td>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=substr($rs[$i]["hour_name"],0,5)?></td>
					<td class="report" align="center"><?=$payname?></td>
					<td class="report" align="right"><?=number_format($rs[$i]["sr_total"],2,".",",")?></td>
					<td class="report" align="center"><?=$cashier?></td>
					<td class="report" align="center"><?=$reprint?></td>
					<td class="report" align="center"><?=$cms?></td>
 				</tr>
 				<?					
 	//Get all salesreceipt_id in report	
 	$Srddetail[$i] = $rs[$i]["salesreceipt_id"];			
 	
 	//$sqlMp = "select * from c_srpayment where salesreceipt_id=".$rs[$i]["salesreceipt_id"]."";
		//$mpId = $obj->getResult($sqlMp);
		//echo $sqlMp."<br>".$mpId["rows"]."<br>";
	if(!$cmpId){			
 	$sqlSr = "select `c_salesreceipt`.`pay_id` ,`c_salesreceipt`.`sr_total` ,`c_salesreceipt`.`salesreceipt_id` , `l_paytype`.`pay_name` from c_salesreceipt, l_paytype where salesreceipt_id=".$rs[$i]["salesreceipt_id"]." AND `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`";
		//echo $sqlSr; 
		$srId = $obj->getResult($sqlSr);	
		for ($k = 0; $k < $srId["rows"]; $k++) {
				$oldSrd[$srcound]["pay_id"] = $srId[$k]["pay_id"];
				$oldSrd[$srcound]["paytype"] = $srId[$k]["pay_name"];
				$oldSrd[$srcound]["pay_price"] = $srId[$k]["sr_total"];
				$Srdold[$srcound] = $srId[$k]["salesreceipt_id"];
			}	
	$srcound++;
	}
 		
 				
 				} 
 	if($Srddetail){
    	$bookSrdString = implode(",", $Srddetail); 
 	}
 	if($Srdold){
    	$bookSrdOld = implode(",", $Srdold); 
 	} 
 	
 	$sqlPd = "SELECT `c_srpayment`.`pay_id` FROM c_srpayment WHERE `c_srpayment`.`salesreceipt_id` IN ( ".$bookSrdString." ) group by `c_srpayment`.`pay_id`";
		$srPd = $obj->getResult($sqlPd);
		for ($k = 0; $k < $srPd["rows"]; $k++) {
				$PayId[$k] = $srPd[$k]["pay_id"];

			}	
 	
 	if($PayId){
    	$bookPayId = implode(",", $PayId);
 		} 
 		?>
 				<tr height="20">
 					<td colspan="11" height="20">&nbsp;</td>
 				</tr>
<?
 if($export && (count($paytype["type"])+$rowcnt) > $chkrow){
?>
	<tr>
		<td width="100%" align="center" colspan="11" ><br>
			<b>Printed: <?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?></b>
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
		<td width="8%"></td><td width="8%"></td>
		<td width="12%"></td><td width="15%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="10%"></td><td width="10%"></td>
		<td width="10%"></td><td width="8%"></td>
		<td width="5%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="11">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
<?
 }
 ?>
				<tr height="20">
					<td colspan="3" align="left" height="20" style="padding-right:7px;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td width="70%"></td><td width="30%"></td>
					</tr>
					
<?
	//Get c_srpayment to show result
		
		$newPd = array ();
		$sqlPd = "SELECT `l_paytype`.`pay_name` , SUM( `c_srpayment`.`pay_total` ) AS total_price
FROM c_srpayment, l_paytype
WHERE `c_srpayment`.`pay_id` = `l_paytype`.`pay_id`
AND `c_srpayment`.`salesreceipt_id` IN ( ".$bookSrdString." ) AND `c_srpayment`.`pay_id` != 1 GROUP BY `l_paytype`.`pay_name`";
		//echo $sqlPd; 
		$srPd = $obj->getResult($sqlPd);

		if(!$srPd){
			for($i=1; $i<=count($paytype["type"]); $i++) {?>
				<tr height="20">
						<td align="right"><b><?=$paytype["type"][$i] ?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($paytype["value"][$i],2,".",",")?></b></td>
					</tr>
			<? } ?>
		<?}else{
		for ($i = 0; $i < $srPd["rows"]; $i++) {
			$newPd[$i]["mp_type"] = $srPd[$i]["pay_name"];
			$newPd[$i]["mp_price"] = $srPd[$i]["total_price"];
		
		for ($k = 0; $k < $srcound; $k++) {
				if($newPd[$i]["mp_type"]==$oldSrd[$k]["paytype"]){
					$newPd[$i]["mp_price"]=$newPd[$i]["mp_price"]+$oldSrd[$k]["pay_price"];	
				}
		}
?>
								
					
					<tr height="20">
						<td align="right"><b><?=$newPd[$i]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newPd[$i]["mp_price"],2,".",",")?></b></td>
					</tr>
<? 
		}
	} 
	$newSr = array ();
	$sqlSr = "select `c_salesreceipt`.pay_id , `l_paytype`.`pay_name`, sum(`c_salesreceipt`.`sr_total`) as sr_total from " .
 			"`c_salesreceipt`, `l_paytype` where `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`" .
 			"and `c_salesreceipt`.`salesreceipt_id` in (".$bookSrdOld.") " .
 			"and `c_salesreceipt`.`pay_id` not in (".$bookPayId.") " .
 			//"and `c_srpayment`.`pay_id` != 1 " .
 			"and `c_salesreceipt`.`pay_id` != 1 " .
 			"group by `c_salesreceipt`.`pay_id`";
 	//echo $sqlSr;
		$srSd = $obj->getResult($sqlSr);
		for ($k = 0; $k < $srSd["rows"]; $k++) {	
					$newSr[$k]["mp_type"]=$srSd[$k]["pay_name"];	
					$newSr[$k]["mp_price"]=$srSd[$k]["sr_total"];
					
?>
					<tr height="20">
						<td align="right"><b><?=$newSr[$k]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newSr[$k]["mp_price"],2,".",",")?></b></td>
					</tr>
<? 
		}
?>							
					</table>
					</td>
					<td colspan="8" align="right" height="20" valign="top" style="padding-right:7px;">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
							<td width="75%"></td><td width="25%"></td>
							</tr>
							<tr height="20">
								<td align="right"><b>Total Revenue : &nbsp;&nbsp;</b></td>
								<td align="right"><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($all_total,2,".",",")?></b></td>
							</tr>
							<tr height="20">
							<?
								if($rs_ttcs["rows"]>1){
									for($k=0; $k<$rs_ttcs["rows"]; $k++) {
										$totalcus = $totalcus+$rs_ttcs[$k]["qty"];
										$rs_ttcs[0]["qty"] = $totalcus; 
									}	
								}
								
								if($rs_tthour["rows"]>1){
									for($k=0; $k<$rs_tthour["rows"]; $k++) {
										$totalth = $totalth+$rs_tthour[$k]["total"];
										$rs_tthour[0]["total"] = $totalth; 
									}	
								}
							?>
								<td align="right"><b>Total Customers : &nbsp;&nbsp;</b></td>
								<td align="right"><b>&nbsp;&nbsp;<?=number_format($rs_ttcs[0]["qty"],2,".",",")?></b></td>
							</tr>
							<tr height="20">
								<td align="right"><b>Total Therapist Hour : &nbsp;&nbsp;</b></td>
								<td align="right"><b>&nbsp;&nbsp;<?=number_format(str_replace(".5",".3",$rs_tthour[0]["total"]),2,".",",")?></b></td>
							</tr>
						</table>
					</td>
				</tr>
			    <tr height="20">
			    	<td width="100%" align="center" colspan="11" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
				</tr>
				<tr height="100">
			    	<td width="100%" align="left" colspan="11" ><br>
			    		<br><b>Notation : </b><br><br>
			    			
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#EAF7CC;"></div> &nbsp;- Green line, Multi method of payment in sale receipt.<br />
<br /> 
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#FFB9B9;"></div> &nbsp;- Red line, This sale receipt is not paid yet.<br />
<br />

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