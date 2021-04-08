<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
$obj = new checker();
$obj->setShowpage(20);
$obj->setDebugStatus(false);
$chkPageEdit=true;	


$date = $obj->getParameter("date",17);
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$cdfunc = $obj->getParameter("cdfunc");
if($cdfunc==""){$cdfunc=1;}
$search = $obj->getParameter("where");
$page = $obj->getParameter("page",1,1);
$searchstr = htmlspecialchars($search);

$anotherpara = "";
switch($cdfunc){
	case 1:$anotherpara = ""; $reportname="All Commission Payment"; break;
	case 2:$anotherpara = "and aa_commission.cmsGofst_id!=0 "; $reportname="Commission Disbursed"; break;
	case 3:$anotherpara = "and aa_commission.cmsGofst_id=0 "; $reportname="Commission Not Disbursed"; break;
	default:$anotherpara = "";
}
if($search){
	$anotherpara .= "and (a_bookinginfo.c_bp_phone like '%".$searchstr."%' or aa_commission.cmsEnvnumber like '%".$searchstr."%' or lower(a_bookinginfo.c_bp_person) like '%".strtolower($searchstr)."%'  or lower(a_bookinginfo.b_customer_name) like '%".strtolower($searchstr)."%') ";;
}
$rs = $obj->getcdcms($begin_date,$end_date,$page,"b_appt_date desc,book_id",$anotherpara);

$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);

?>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td class="reporth" width="100%" align="center">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br/>
    		<p class="style1"><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></p>    	</td>
	</tr>
	<tr>
    	<td class="content" width="100%">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tbody>
                    <tr>
                    	<td colspan="2">
                        <div id="companyinfo">
                        
			
			<table cellspacing="0" border="0" cellpadding="0" width="100%">
				<tr height="32">
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Env Number</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Pick Up Date</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Given By</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. of People</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Option</b></td>
				</tr>
				<?	$all_sc=0;	
					$all_vat=0;	
					$all_total=0;	
					for($i=0; $i<$rs["rows"]; $i++) {
						
if(isset($rs[$i]["pay_id"]) && $rs[$i]["pay_id"]>1)
	$paytype = $rs[$i]["pay_name"];
else
	$paytype = "-";

if(isset($rs[$i]["reception_code"]) && $rs[$i]["reception_code"]>1)
	$reception = $rs[$i]["reception_code"]." ".$rs[$i]["reception_name"];
else
	$reception = "-";


if($i%2==1){
	echo "<tr class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\">\n";
}else{
	echo "<tr class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\">\n";
}


$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
$pagename = (isset($rs[$i]["tb_name"]) && $rs[$i]["tb_name"]=="a_bookinginfo")?"manageBooking":"managePds";
$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
	?>
					<input type="hidden" id="book_id[<?=$i?>]" name="book_id[<?=$i?>]" value="<?=$rs[$i]["book_id"]?>" />
					<input type="hidden" id="cmsid[<?=$i?>]" name="cmsid[<?=$i?>]" value="<?=$rs[$i]["cms_id"]?>" />
					<td class="report" align="center"><a href='javascript:;;' onClick="newwindow('/appt/<?=$url?>','<?=$pagename.$bpdsid?>')" class="menu"><?=$bpdsid?></a></td>
					<td class="report"><?=$dateobj->convertdate($rs[$i]["bookdate"],"Y-m-d",$sdateformat)?></td>
					<td class="report" align="center"><?=$obj->getIdToText($rs[$i]["c_bp_id"],"al_bookparty","bp_name","bp_id")?>&nbsp;</td>
					<td class="report" align="center"><?=$obj->hightLightChar($search,$rs[$i]["cms_name"])?>&nbsp;</td>
					<td class="report" align="center"><?=$obj->hightLightChar($search,$rs[$i]["cs_name"])?>&nbsp;</td>
					<td class="report" align="center"><?=$obj->hightLightChar($search,$rs[$i]["cmsEnvnumber"])?>&nbsp;</td>
					<td class="report" align="center" width="130px">
						<? if($rs[$i]["cmsEnvdatepu"]!="0000-00-00"){ ?>
							<?=$dateobj->convertdate($rs[$i]["cmsEnvdatepu"],"Y-m-d",$sdateformat);?>
						<? }else { ?>
						&nbsp;&nbsp;<input id='pickupdate[<?=$i?>]' name='pickupdate[<?=$i?>]' value="<?/*$rs[$i]["cmsEnvdatepu"]*/?>" readonly="1" class="textbox" type="text" style="width:85px"/>
				        <input type="hidden" id="hidden_pickupdate[<?=$i?>]" name="hidden_pickupdate[<?=$i?>]" value=""/>
				        <img src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'pickupdate[<?=$i?>]', 'date_pickupdate[<?=$i?>]', 1900, 2100, '<?=$sdateformat?>', false,false);" />
				        <div id="date_pickupdate[<?=$i?>]" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"> </div>
				        <? } ?>					</td>
					<td class="report" align="center">
						<? if($rs[$i]["cmsGofst_id"]){
								echo $obj->getIdToText($rs[$i]["cmsGofst_id"],"l_employee","emp_nickname","emp_id");
							}else {
								$obj->makeListbox("gaveby[$i]","l_employee","emp_nickname","emp_id",1,"",0,"emp_active",1,"emp_department_id=2","emp_id=1");
							}?>
&nbsp;					</td>
					<td class="report" align="center"><?=$obj->hightLightChar($search,$rs[$i]["cms_phone"])?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["qty_pp"]?>&nbsp;</td>
					<td class="report" align="center">
					<? if($rs[$i]["cmsGofst_id"]){ ?>
							<img src='/images/add2.gif' title='add' border='0' />
					<? }else if($chkPageEdit){ ?>
							<a href="javascript:;" class="top_menu_link" onClick="javascript:addDisp(<?=$i?>,<?="'$page'"?>);"><img src='/images/add.gif' title='add' border='0' /></a>
					<? }else{?>
						<img src='/images/add.gif' title='add' border='0' />
					<?} ?>					</td>
 				</tr>
 				<?	} ?>
 			</table>
			    </div>                        </td>
                    </tr>
                    </tbody></table><br/>		</td>
    </tr>
    <tr>
    	<td width="100%" align="center" ><span class="pagelink">
    		<? 
				$rs = $obj->getcdcms($begin_date,$end_date,false,"book_id",$anotherpara);
				$obj->gen_page("report.php",$page,$rs["rows"],$obj->getShowpage());			// Change function name genPage > gen_page,add parameter $obj->getShowpage()
			?> </span>   	</td>
	</tr>
    <tr>
    	<td width="100%" align="center">
    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?><input type="hidden" id="rows" value="<?=$rs["rows"]?>" />    	</td>
	</tr>
    <tr>
      <td align="center">&nbsp;</td>
    </tr>
</table>
