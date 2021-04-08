<?php
/*
 * Created on Feb 19, 2009
 */
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("therapist.inc.php");
$obj = new therapist();

$begin_date = $obj->getParameter("begin");
$end_date = $obj->getParameter("end");
$branchid = $obj->getParameter("branchid");
$empid = $obj->getParameter("empid");
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");
$city = $obj->getParameter("cityid");
$export = $obj->getParameter("export",false);
if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Therapist Hours Detail.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}

// system's mins period
$minsperiod = array("00"=>"00","08"=>"05","17"=>"10","25"=>"15","33"=>"20","42"=>"25","50"=>"30","58"=>"35","67"=>"40","75"=>"45","83"=>"50","92"=>"55");
	
if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&chkrow=40&gmt=".$_SESSION["__gmt"]);
}

$rs=$obj->getthhourdetail($begin_date,$end_date,$empid,$branchid,$city);
$rsthera = $obj->gettherapist($order,$sort,$empid,0,$city);
$rsbranch = $obj->getbranch($order,$sort,$branchid,$city);
$reportname = "Therapist Hours Detail";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
$thera["msgcnt"][$rs[0]["emp_id"]]=0;
$thera["maxmsgcnt"] = 0;
for($i=0;$i<$rs["rows"];$i++){
	$sql = "select massage_id from da_mult_msg where indivi_id=".$rs[$i]["indivi_id"];
	$rsIndi = $obj->getResult($sql);
	if(!isset($rs[$i-1]["emp_id"])||$rs[$i]["emp_id"]!=$rs[$i-1]["emp_id"]){
		$thera["msgcnt"][$rs[$i]["emp_id"]]=0;
	}
	if($rsIndi["rows"] > $thera["msgcnt"][$rs[$i]["emp_id"]]){
			$thera["msgcnt"][$rs[$i]["emp_id"]]=$rsIndi["rows"];
	}
	if($rsIndi["rows"] > $thera["maxmsgcnt"]){
		$thera["maxmsgcnt"] =$rsIndi["rows"];	//therapist max massage count 
	}
}
if(!$export||$export=="Excel"){
	foreach($thera["msgcnt"] as $key => $value ){
		$thera["msgcnt"][$key] = $thera["maxmsgcnt"];
	}
}
?>
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td class="content" width="100%" align="center">
<?
$allcolumncnt = (6+$thera["msgcnt"][$rs[0]["emp_id"]]);
$allcolumn = 100;
$columnwidth = $allcolumn/$allcolumncnt;
?>
			<table cellspacing="0" border="0" cellpadding="0">
				<tr>
					<? for($h=1;$h<=$allcolumncnt;$h++){ ?>
						<td width="<?=$columnwidth?>%"></td>	
					<? } ?>
				</tr>
				<tr>
			    	<td class="reporth" align="center" colspan="<?=$allcolumncnt?>">
			    		<b><p>Spa Management System</p>
			    		<?=$reportname?></b><br>
			    		<p><b style='color:#ff0000'>
			    		<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
			    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
			    		</p>
			    	</td>
				</tr>
				<tr height="32">
					<td style="text-align:left; padding-left: 20px;padding-right: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Therapist</b></td>
					<td style="text-align:left; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Package</b></td>
			<?		
			for($h=1;$h<=$thera["msgcnt"][$rs[0]["emp_id"]];$h++){
					echo "<td style=\"text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;\"><b>Massage $h</b></td>";
			}
			?>          
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Hours</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
				</tr>
				
<?
$totalthhour = 0;
$totalth = 0;
	for($i=0;$i<$rs["rows"];$i++){
		if(!isset($rs[$i-1]["emp_id"])||$rs[$i]["emp_id"]!=$rs[$i-1]["emp_id"]){
			$total[$rs[$i]["emp_id"]]=0;
		}	
				$hour=explode(':',$rs[$i]["hour_name"]);
				$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
				$pagename = "manageBooking".$rs[$i]["book_id"];
				$bpdsid = $obj->getIdToText($rs[$i]["book_id"],"c_bpds_link","bpds_id","tb_id","tb_name=\"a_bookinginfo\"");
			?>
				<tr height="20" class="even" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#eaeaea'">
					<td class="report" style="text-align:left;padding-left: 10px;white-space: nowrap;"><?=(isset($rs[$i-1]["emp_id"])&&$rs[$i]["emp_code"]==$rs[$i-1]["emp_code"])?"":$rs[$i]["emp_code"]." ".$rs[$i]["emp_nickname"]?>&nbsp;</td>
					<td class="report" style="text-align:left;padding-left: 10px;white-space: nowrap;"><?=$rs[$i]["room_name"]?>&nbsp;</td>
					<td class="report" style="text-align:left;padding-left: 10px;white-space: nowrap;">&nbsp;<?=($rs[$i]["package_id"]==1)?"-":$obj->getIdToText($rs[$i]["package_id"],"db_package","package_name","package_id")?>&nbsp;</td>
<?

$sql = "select massage_id from da_mult_msg where indivi_id=".$rs[$i]["indivi_id"];
$rsMsg = $obj->getResult($sql);
for($j=0;$j<$thera["msgcnt"][$rs[$i]["emp_id"]];$j++){
	if(isset($rsMsg[$j]["massage_id"]) && $rsMsg[$j]["massage_id"]!="" && $rsMsg[$j]["massage_id"]!=1){
						$sql = "select trm_name from db_trm where trm_id=".$rsMsg[$j]["massage_id"];
						$rsTrm = $obj->getResult($sql);
						echo "<td class=\"report\" style=\"text-align:left;padding-left: 10px;white-space: nowrap;\">".$rsTrm[0]["trm_name"]."&nbsp;</td>";
	}else{
						echo "<td class=\"report\" style=\"text-align:left;padding-left: 10px;white-space: nowrap;\">&nbsp;&nbsp; -</td>";
	}
							
}
?>
					<? list($hr,$min) = explode(".",number_format($rs[$i]["hour_calculate"],2,".",",")); ?>
					<td class="report" style="text-align:right;padding-left: 10px;"><?=$hr.".".$minsperiod[$min]?></td>
					<td class="report" style="text-align:center;">
					<?if(!$export){?><a href='javascript:;;' onClick="newwindow('/appt/<?=$url?>','<?=$pagename?>')" class="menu"><?}?>
					<?=$bpdsid?>
					<?if(!$export){?></a><?}?>
					</td>
					<td class="report" style="text-align:left;padding-left: 10px;white-space: nowrap;"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
				</tr>
			<?
			$totalthhour+=$rs[$i]["hour_calculate"];
			$total[$rs[$i]["emp_id"]]+=$rs[$i]["hour_calculate"];
			if(!isset($rs[$i+1]["emp_id"])||$rs[$i]["emp_code"]!=$rs[$i+1]["emp_code"]){
				
				if($export&&$export!="Excel"){
?>
					<? list($hr,$min) = explode(".",number_format($totalthhour,2,".",",")); ?>
				<tr height="35">
					<td colspan="<?=3+$thera["msgcnt"][$rs[$i]["emp_id"]]?>" style="text-align:right;padding-left: 10px;white-space: nowrap;"><b><?=$rs[$i]["emp_code"]." ".$rs[$i]["emp_nickname"]?></b></td>
					<td style="text-align:right;white-space: nowrap;"><b style='color:#ff0000'><?=$hr.".".$minsperiod[$min]?></b></td>
					<td colspan="2">&nbsp;</td>
				</tr>
<?if($i!=$rs["rows"]-1){?>
				</table>
			</div>
    	</td>
    </tr>
	<tr>
    	<td align="center">
    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td class="content" width="100%" align="center">
<?
$allcolumncnt = (6+$thera["msgcnt"][$rs[$i+1]["emp_id"]]);
$allcolumn = 100;
$columnwidth = $allcolumn/$allcolumncnt;
?>
			<table cellspacing="0" border="0" cellpadding="0">
				<tr>
					<? for($h=1;$h<=$allcolumncnt;$h++){ ?>
						<td width="<?=$columnwidth?>%"></td>	
					<? } ?>
				</tr>
				<tr>
			    	<td class="reporth" align="center" colspan="<?=$allcolumncnt?>">
			    		<b><p>Spa Management System</p>
			    		<?=$reportname?></b><br>
			    		<p><b style='color:#ff0000'>
			    		<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
			    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
			    		</p>
			    	</td>
				</tr>
				<tr height="32">
					<td style="text-align:left; padding-left: 20px;padding-right: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Therapist</b></td>
					<td style="text-align:left; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Package</b></td>
			<?		
			for($h=1;$h<=$thera["msgcnt"][$rs[$i+1]["emp_id"]];$h++){
					echo "<td style=\"text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;\"><b>Massage $h</b></td>";
			}
			?>          
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Hours</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Book ID</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
				</tr>
<?}?>
<?					
				}else{
?>
					<? list($hr,$min) = explode(".",number_format($totalthhour,2,".",",")); ?>
				<tr height="22" class="odd" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#d3d3d3'"  bgcolor="#d3d3d3">
					<td class="report" colspan="<?=3+$thera["msgcnt"][$rs[$i]["emp_id"]]?>" style="text-align:right;padding-left: 10px;white-space: nowrap;"><b><?=$rs[$i]["emp_code"]." ".$rs[$i]["emp_nickname"]?></b></td>
					<td class="report" style="text-align:right;white-space: nowrap;"><b style='color:#ff0000'><?=$hr.".".$minsperiod[$min]?></b></td>
					<td class="report" colspan="2">&nbsp;</td>
				</tr>
<?
					
				}
				$totalthhour = 0;
			}
}
?>
<?if(!$export||$export=="Excel"){?>
			<tr height="32">
			<td align="right" colspan="<?=3+$thera["msgcnt"][$rs[$i-1]["emp_id"]]?>" style="padding-left: 10px; white-space: nowrap;"><b>Total of Hours : </b></td>
			<? list($hr,$min) = explode(".",number_format(array_sum($total),2,".",",")); ?>
			<td align="right" style="white-space: nowrap;"><b style='color:#ff0000'><?=$hr.".".$minsperiod[$min]?></b></td>
			<td colspan="2" style="white-space: nowrap;">&nbsp;</td>
			</tr>
<?}?>
			</table>
			</div>
    	</td>
    </tr>
	<tr>
    	<td align="center">
    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
 	<tr height="20">
 			<td>&nbsp;</td>
 	</tr>
</table>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>
