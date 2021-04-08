<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("marketing.inc.php");
$obj = new marketing();
$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate= $obj->getParameter("end");
$collapse = $obj->getParameter("Collapse","Collapse");
$cmschk = $obj->getParameter("commission",false);
if($cmschk==""){$cmschk=false;}
$branch = $obj->getParameter("branchid");
$city = $obj->getParameter("cityid");
if($branch==""){$branch=$obj->getIdToText("All","bl_branchinfo","branch_id","branch_name");}
$today = date("Ymd");


$giftchk = $obj->getParameter("gift");
$mkchk = $obj->getParameter("mkcode");

if($giftchk=="checked"&&$mkchk=="checked"){		// show all information
	$status = 0;
}else if($giftchk=="checked"&&!$mkchk){			// show only gift certificate information
	$status = 1;
}else if($mkchk=="checked"&&!$giftchk){			// show only marketing code information
	$status = 2;	
}else{											// show all information
	$status = 0;
}
if($status==1){
$rsgifttype = $obj->getgifttype();
$rsissue = $obj->getgiftissue($begindate,$enddate,$branch,$city);
$rsgiftused = $obj->getgiftused($begindate,$enddate,$branch,$city);
}else if($status==2){
$rsmkused = $obj->getmarketingused($begindate,$enddate,$branch,$city,$status);
}else{
$rsgifttype = $obj->getgifttype();
$rsissue = $obj->getgiftissue($begindate,$enddate,$branch,$city);
$rsgiftused = $obj->getgiftused($begindate,$enddate,$branch,$city);
$rsmkused = $obj->getmarketingused($begindate,$enddate,$branch,$city);
}

//$rs = $obj->getcms($branch,$begindate,$enddate,$cmschk,$collapse);
$begin_date = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$end_date = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Marketing Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&chkrow=40&gmt=".$_SESSION["__gmt"]);
}
if($status==1||$status==0){
	for($i=0; $i<$rsgiftused["rows"]; $i++) {
		// warning undifine index on line 66-67
		if(!isset($rsgift["used_qty"][$rsgiftused[$i]["code_id"]])){$rsgift["used_qty"][$rsgiftused[$i]["code_id"]]=0;}
		if(!isset($rsgift["used_amount"][$rsgiftused[$i]["code_id"]])){$rsgift["used_amount"][$rsgiftused[$i]["code_id"]]=0;}
		
		$rsgift["used_qty"][$rsgiftused[$i]["code_id"]]+=$rsgiftused[$i]["used_qty"]; 
		$rsgift["used_amount"][$rsgiftused[$i]["code_id"]]+=$rsgiftused[$i]["used_amount"]; 
	}for($i=0; $i<$rsissue["rows"]; $i++) {
		// warning undifine index on line 73-74
		if(!isset($rsgift["issue_qty"][$rsissue[$i]["code_id"]])){$rsgift["issue_qty"][$rsissue[$i]["code_id"]]=0;}
		if(!isset($rsgift["issue_amount"][$rsissue[$i]["code_id"]])){$rsgift["issue_amount"][$rsissue[$i]["code_id"]]=0;}
		
		$rsgift["issue_qty"][$rsissue[$i]["code_id"]]+=$rsissue[$i]["issue_qty"]; 
		$rsgift["issue_amount"][$rsissue[$i]["code_id"]]+=$rsissue[$i]["issue_amount"]; 
	}
}

$rowcnt = 0;
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",40);
}
$reportname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id")." Marketing Report";
?>
<?if($export!="Excel"){?>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<?}?>
<span class="pdffirstpage"/>	
<?
$header1 = '
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="70%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="30%"></td><td width="15%"></td>
		<td width="15%"></td><td width="10%"></td>
		<td width="15%"></td><td width="15%"></td>
	</tr>
	<tr>
	    <td class="reporth" width="100%" align="center" colspan="6" >
    		<b><p>Spa Management System</p>
    		'.$reportname.'</b><br>
    		<p><b style="color:#ff0000">'.$dateobj->convertdate($begin_date,$sdateformat,$ldateformat).''.(($end_date==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($end_date,$sdateformat,$ldateformat)).'</b><br><br></p>
    	</td>
	</tr>
				<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;">&nbsp;</td>			
					<td align="center" style="border-top:2px #000000 solid;border-bottom:1px #000000 solid;" colspan="5"><b>Gift Certificate/Booking</b></td>
				</tr>
				<tr height="32">	
					<td align="center"><b>Type</b></td>	
					<td align="center" style="border-bottom:1px #000000 solid;" colspan="2"><b>Issue</b></td>
					<td align="center" style="border-bottom:1px #000000 solid;" colspan="3"><b>Used</b></td>
				</tr>
				<tr height="30" >	
					<td align="center" style="border-bottom:2px #ff0000 solid;">&nbsp;</td>	
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Total Customer</b></td>
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
				</tr>
';

$header2 = '
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="70%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="30%"></td><td width="15%"></td>
		<td width="15%"></td><td width="10%"></td>
		<td width="15%"></td><td width="15%"></td>
	</tr>
	<tr>
	    <td class="reporth" width="100%" align="center" colspan="6" >
    		<b><p>Spa Management System</p>
    		'.$reportname.'</b><br>
    		<p><b style="color:#ff0000">'.$dateobj->convertdate($begin_date,$sdateformat,$ldateformat).''.(($end_date==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($end_date,$sdateformat,$ldateformat)).'</b><br><br></p>
    	</td>
	</tr>
				<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;">&nbsp;</td>			
					<td align="center" style="border-top:2px #000000 solid;border-bottom:1px #000000 solid;" colspan="5"><b>Marketing Code/Booking</b></td>
				</tr>
				<tr height="32">	
					<td align="center"><b>Type</b></td>	
					<td align="center" style="border-bottom:1px #000000 solid;" colspan="2"><b>Issue</b></td>
					<td align="center" style="border-bottom:1px #000000 solid;" colspan="3"><b>Used</b></td>
				</tr>
				<tr height="30" >	
					<td align="center" style="border-bottom:2px #ff0000 solid;">&nbsp;</td>	
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Total Customer</b></td>
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
				</tr>
';
$footer = '
 			<tr height="20">
 					<td colspan="6" height="20">&nbsp;</td>
 			</tr>
    <tr height="20">
    	<td width="100%" align="center" colspan="6" >
    		<b>Printed: </b> '.$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s").'
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
';
	echo ($status==1||$status==0)?$header1:$header2;
?>

<? 
	
// for gift certificate issue/used
if($status==1||$status==0){
	
		for($i=0; $i<$rsgifttype["rows"]; $i++) {
			// warning undifine index on line 188-190
			if(!isset($rsgift["issue_amount"][$rsgifttype[$i]["gifttype_id"]])){$rsgift["issue_amount"][$rsgifttype[$i]["gifttype_id"]]=0;}
			if(!isset($rsgift["issue_qty"][$rsgifttype[$i]["gifttype_id"]])){$rsgift["issue_qty"][$rsgifttype[$i]["gifttype_id"]]=0;}
			if(!isset($rsgift["used_amount"][$rsgifttype[$i]["gifttype_id"]])){$rsgift["used_amount"][$rsgifttype[$i]["gifttype_id"]]=0;}
			if(!isset($rsgift["used_qty"][$rsgifttype[$i]["gifttype_id"]])){$rsgift["used_qty"][$rsgifttype[$i]["gifttype_id"]]=0;}
			if($collapse=="Collapse"&&
			($rsgift["issue_amount"][$rsgifttype[$i]["gifttype_id"]]||$rsgift["issue_qty"][$rsgifttype[$i]["gifttype_id"]]
			||$rsgift["used_amount"][$rsgifttype[$i]["gifttype_id"]]||$rsgift["used_qty"][$rsgifttype[$i]["gifttype_id"]])){ 
?>
			<tr height="20" class="even" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#eaeaea'" ><?$rowcnt++;?>
					<td align="left" class="report">&nbsp;&nbsp;<?=$rsgifttype[$i]["gifttype_name"]?></td>
					<td align="center" class="report">
					<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,0,".$rsgifttype[$i]["gifttype_id"].",'g_gift','issue',$status"?>)"><? } ?>
					<?=$rsgift["issue_qty"][$rsgifttype[$i]["gifttype_id"]]?>
					<?if($export==false){?></a><? } ?></td>
					<td align="right" class="report">
					<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,0,".$rsgifttype[$i]["gifttype_id"].",'g_gift','issue',$status"?>)"><? } ?>
					<?=number_format($rsgift["issue_amount"][$rsgifttype[$i]["gifttype_id"]],2,".",",")?>
					<?if($export==false){?></a><? } ?></td>	
					<td align="center" class="report">
					<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,0,".$rsgifttype[$i]["gifttype_id"].",'g_gift','used',$status"?>)"><? } ?>
					<?=$rsgift["used_qty"][$rsgifttype[$i]["gifttype_id"]]?>
					<?if($export==false){?></a><? } ?></td>	
					<td align="center" class="report">-</td>
					<td align="right" class="report">
					<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,0,".$rsgifttype[$i]["gifttype_id"].",'g_gift','used',$status"?>)"><? } ?>
					<?=number_format($rsgift["used_amount"][$rsgifttype[$i]["gifttype_id"]],2,".",",")?>
					<?if($export==false){?></a><? } ?></td>		
			</tr><?=($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0)?"$footer $header1":""?>
<?			}
		}
		if(array_sum($rsgift["issue_amount"])||array_sum($rsgift["issue_amount"])
			||array_sum($rsgift["used_qty"])||array_sum($rsgift["used_qty"])){
?>		
			<tr  class="odd"  onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#d3d3d3'"  height="20" ><?$rowcnt++;?>
					<td align="left" class="report">&nbsp;&nbsp;<b>All Gift Certificate</b></td>
					<td align="center" class="report">
					<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,0,0,'g_gift','issue',$status"?>)"><? } ?>
					<b><?=array_sum($rsgift["issue_qty"])?></b>
					<?if($export==false){?></a><? } ?></td>
					<td align="right" class="report">
					<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,0,0,'g_gift','issue',$status"?>)"><? } ?>
					<b><?=number_format(array_sum($rsgift["issue_amount"]),2,".",",")?></b>
					<?if($export==false){?></a><? } ?></td>
					<td align="center" class="report">
					<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,0,0,'g_gift','used',$status"?>)"><? } ?>
					<b><?=array_sum($rsgift["used_qty"])?></b>
					<?if($export==false){?></a><? } ?></td>
					<td align="center" class="report">-</td>
					<td align="right" class="report">
					<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,0,0,'g_gift','used',$status"?>)"><? } ?>
					<b><?=number_format(array_sum($rsgift["used_amount"]),2,".",",")?></b>
					<?if($export==false){?></a><? } ?></td>		
			</tr><?=($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0)?"$footer $header1":""?>
<?		} ?>
<tr height="20" class="even" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#eaeaea'" >
	<td align="center" colspan="6">&nbsp;* For Gift Certificate issued, it is always all inclusive.</td>
</tr>
<?
}
?>		
				
<? 
// for marketing code issue/used
if($status==2||$status==0){
	if($status==0){
?>

				<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;">&nbsp;</td>			
					<td align="center" style="border-top:2px #000000 solid;border-bottom:1px #000000 solid;" colspan="5"><b>Marketing Code/Booking</b></td>
				</tr>
				<tr height="32">	
					<td align="center"><b>Type</b></td>	
					<td align="center" style="border-bottom:1px #000000 solid;" colspan="2"><b>Issue</b></td>
					<td align="center" style="border-bottom:1px #000000 solid;" colspan="3"><b>Used</b></td>
				</tr>
				<tr height="30" >	
					<td align="center" style="border-bottom:2px #ff0000 solid;">&nbsp;</td>	
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Total Customer</b></td>
					<td align="center" style="border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
				</tr>
<?
	}
	
		$usedqty=0;$usedamount=0;$usedperson=0;
		$usedqtypertype = 0; $usedpertype = 0;$usedpersonpertype = 0;
		for($i=0; $i<$rsmkused["rows"]; $i++) {
			$usedqty+=$rsmkused[$i]["used_qty"];$usedamount+=$rsmkused[$i]["used_amount"];
			$usedperson+=$rsmkused[$i]["used_person"];
			if(!isset($rsmkused[$i+1]["code_id"])){$rsmkused[$i+1]["code_id"]=0;}
			if($rsmkused[$i]["code_id"]!=$rsmkused[$i+1]["code_id"]){
					$usedqtypertype+=$usedqty; $usedpertype+=$usedamount;$usedpersonpertype += $usedperson;
					if($collapse=="Collapse"){ 
		?>
					<tr height="20" class="even" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#eaeaea'" ><?$rowcnt++;?>
							<td class="report" align="left">&nbsp;&nbsp;<?=$rsmkused[$i]["code_name"]?></td>
							<td class="report" align="center"><?="-"?></td>
							<td class="report" align="right"><?="-"?></td>
							<td class="report" align="center">
							<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,".$rsmkused[$i]["code_id"].",".$rsmkused[$i]["type_id"].",'".$rsmkused[$i]["tb_name"]."','qty',$status"?>)"><? } ?>
							<?=$usedqty?>
							<?if($export==false){?></a><? } ?></td>
							<td class="report" align="center">
							<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,".$rsmkused[$i]["code_id"].",".$rsmkused[$i]["type_id"].",'".$rsmkused[$i]["tb_name"]."','qty',$status"?>)"><? } ?>
							<?=$usedperson?>
							<?if($export==false){?></a><? } ?></td>
							<td class="report" align="right">
							<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,".$rsmkused[$i]["code_id"].",".$rsmkused[$i]["type_id"].",'".$rsmkused[$i]["tb_name"]."','amount',$status"?>)"><? } ?>
							<?=number_format($usedamount,2,".",",")?>
							<?if($export==false){?></a><? } ?></td>		
					</tr><?=($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0)?"$footer $header2":""?>
				<? 	}
				if(!isset($rsmkused[$i+1]["type_id"])){$rsmkused[$i+1]["type_id"]=0;}
				if($rsmkused[$i]["type_id"]!=$rsmkused[$i+1]["type_id"]){ 
				?>
					<tr class="odd"  onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#d3d3d3'"  height="20" ><?$rowcnt++;?>
							<td align="left" class="report">&nbsp;&nbsp;<b><?=$rsmkused[$i]["type_name"]?></b></td>
							<td align="center" class="report"><b><?="-"?></b></td>
							<td align="right" class="report"><b><?="-"?></b></td>
							<td align="center" class="report">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,0,".$rsmkused[$i]["type_id"].",'".$rsmkused[$i]["tb_name"]."','qty',$status"?>)"><? } ?>
							<b><?=$usedqtypertype?></b>
							<?if($export==false){?></a><? } ?></td>
							<td align="center" class="report">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,0,".$rsmkused[$i]["type_id"].",'".$rsmkused[$i]["tb_name"]."','qty',$status"?>)"><? } ?>
							<b><?=$usedpersonpertype?></b>
							<?if($export==false){?></a><? } ?></td>
							<td align="right" class="report">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openmkDetail(<?="$begindate,$enddate,$branch,$city,0,".$rsmkused[$i]["type_id"].",'".$rsmkused[$i]["tb_name"]."','amount',$status"?>)"><? } ?>
							<b><?=number_format($usedpertype,2,".",",")?></b>
							<?if($export==false){?></a><? } ?></td>		
					</tr><?=($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0)?"$footer $header2":""?>
		<?			$usedqtypertype = 0; $usedpertype = 0;$usedpersonpertype = 0;
				}
				$usedqty = 0; $usedamount = 0;$usedperson = 0;
			}
		}
		?>
<?
}
?>	
 			<tr height="20">
 					<td colspan="6" height="20">&nbsp;</td>
 			</tr>
    <tr height="20">
    	<td width="100%" align="center" colspan="6" >
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