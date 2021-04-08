<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
$obj = new checker();
$obj->setDebugStatus(false);

$date = $obj->getParameter("date",16);
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$bp_id = $obj->getParameter("bpid");
if($bp_id==""||$bp_id==1){$bp_id=0;}

$today = date("Ymd");
$rs = $obj->getenvl($bp_id,$begin_date,$end_date);

$reportname = "Commission Tracking Record";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);

?>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td class="reporth" width="100%" align="center">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br/>
    		<p class="style1"><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></p>
    	</td>
	</tr>
	<tr>
    	<td class="content" width="100%">
			<table cellspacing="0" border="0" cellpadding="0" width="100%">
				<tr height="32">
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. of People</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS Amount</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Env Number</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Option</b></td>
				</tr>
				<?	$all_sc=0;	
					$all_vat=0;	
					$all_total=0;	
					for($i=0; $i<$rs["rows"]; $i++) {

if($i%2==1){
	echo "<tr bgcolor=\"#efefef\" class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\">\n";
}else{
	echo "<tr bgcolor=\"#ffffff\" class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\">\n";
}

$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
$pagename = "manageBooking".$rs[$i]["book_id"];
$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
	?>
					<input type="hidden" id="book_id[<?=$i?>]" name="book_id[<?=$i?>]" value="<?=$rs[$i]["book_id"]?>" />
					<input type="hidden" id="cmsid[<?=$i?>]" name="cmsid[<?=$i?>]" value="<?=$rs[$i]["cms_id"]?>" />
					<input type="hidden" id="cms[<?=$i?>]" name="cms[<?=$i?>]" value="<?=$rs[$i]["cms"]?>" />
					<td class="report" align="center"><a href='javascript:;;' onClick="newwindow('/appt/<?=$url?>','<?=$pagename?>')" class="menu"><?=$bpdsid?></a></td>
					<td class="report"><?=$dateobj->convertdate($rs[$i]["bookdate"],"Y-m-d",$sdateformat)?></td>
					<td class="report" align="left"><?=$rs[$i]["cms_company"]?>&nbsp;</td>
					<td class="report" align="left"><?=$rs[$i]["cms_name"]?>&nbsp;</td>
					<td class="report" align="left"><?=$rs[$i]["cs_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["cms_phone"]?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["qty_pp"]?>&nbsp;</td>
					<td class="report" align="right"><?=number_format($rs[$i]["cms"]+$rs[$i]["c_cms_value"],2,".",",")?>&nbsp;</td>
					<td class="report" align="center"><input type='text' name='cmsEnvnumber[<?=$i?>]' id='cmsEnvnumber[<?=$i?>]' value=''>&nbsp;</td>
					<td class="report" align="center">
					<?if($chkPageEdit){?>
						<a href="javascript:;" class="top_menu_link" onClick="javascript:addEnv(<?=$i?>,<?=$rs[$i]["cms"]?>);">
						<img src='/images/add.gif' title='add' border='0' />
						</a>
					<?}else{?>
						<img src='/images/add.gif' title='add' border='0' />	
					<?}?>
						</td>
 				</tr>
 				<?	} ?>
 			</table><br/>
		</td>
    </tr>
    <tr>
    	<td width="100%" align="center">
    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?><input type="hidden" id="rows" value="<?=$rs["rows"]?>" />
    	</td>
	</tr>
</table>