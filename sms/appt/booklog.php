<?php
/*
 * Created on Sep 5, 2009
 *
 * Booking and Product for sale Log information specific for some user 
 */
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("logs.inc.php");
$obj = new logs(); 

$bookid = $obj->getParameter("book_id");
$pdsid = $obj->getParameter("pds_id");
$srid = $obj->getParameter("sr_id");
$chkpage = $obj->getParameter("chkpage",1);

if($bookid){
		$bpdsid = $obj->getIdToText($bookid,"c_bpds_link","bpds_id","tb_id","tb_name=\"a_bookinginfo\"");
}else{
		$bpdsid = $obj->getIdToText($pdsid,"c_bpds_link","bpds_id","tb_id","tb_name=\"c_saleproduct\"");
}
if(!$srid){
	if($bookid){
		$srid = $obj->getIdToText($bookid,"log_c_sr","salesreceipt_id","book_id");
	}else{
		$srid = $obj->getIdToText($pdsid,"log_c_sr","salesreceipt_id","pds_id");
	}
}
	if($bookid){ //$bid = branch_id
		$bid = $obj->getIdToText($bookid,"a_bookinginfo","b_branch_id","book_id");
	}else{
		$bid = $obj->getIdToText($pdsid,"c_saleproduct","branch_id","pds_id");
	}
$cmsrs = false;
if($bookid){$cmsrs = $obj->get_log_cms($bookid);}
$thrs = false;
if($bookid){$thrs = $obj->get_log_th($bookid);}

$srrs = $obj->get_log_sr($bookid,$pdsid);
if($srrs["rows"]){
	$srdetailrs = $obj->get_log_srdetail($bookid,$pdsid,$srid);
	$mpdetailrs = $obj->get_log_mpdetail($bookid,$pdsid,$srid);
	$srprintrs = $obj->getSrPrintHis($srid);
}else{
	$srdetailrs["rows"] = 0;
	$srprintrs["rows"] = 0;
	$mpdetailrs["rows"] = 0;
}
// all sales receipt number
$srindex = 0;
$sr["salesreceipt_number"] = array();
$sr["salesreceipt_id"] = array();
$sr["salesreceipt_active"] = array();
for($i=0;$i<$srrs["rows"];$i++){ 
	$keyword = $srrs[$i]["salesreceipt_id"];
	$key = array_search($keyword, $sr["salesreceipt_id"]);
	if(!$key){
			$srindex++;
			$key = $srindex;
	}
	$sr["salesreceipt_id"][$key] = $srrs[$i]["salesreceipt_id"];
	
	$sr["salesreceipt_number"][$key] = $srindex;
	$sr["salesreceipt_number"][$key] .= ($srrs[$i]["salesreceipt_number"])?" - ".$srrs[$i]["salesreceipt_number"]:"";
	$sr["salesreceipt_active"][$key] = $srrs[$i]["active"];
	$fontcolor = "";
	if($sr["salesreceipt_active"][$key]==0){$fontcolor = "style=\"color:#ff0000;\"";}
	$sr["salesreceipt_number"][$key] = "<a href=\"javascript:;;\" onClick=\"this.href='booklog.php?book_id=$bookid&pds_id=$pdsid&sr_id=".$srrs[$i]["salesreceipt_id"]."&chkpage=2';\" $fontcolor>".$sr["salesreceipt_number"][$key]."</a>";
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Booking Log</title>
<link href="/css/styles.css" rel="stylesheet" type="text/css" />
<link href="/appt/css/style.css" rel="stylesheet" type="text/css" />
<script src="scripts/component.js" type="text/javascript"></script>
</head>
<body>
<table class="main" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="right" class="header" style="padding-right:12px;padding-bottom:0px;border-bottom: 2px solid #ffffff;">
			<table width="100%" cellpadding="0" cellspacing="0">
        	    <tr>
        	    	<td width="53%" style="padding-bottom:5px">
					<b><?=($bookid)?"BOOKING ID: ":"Sale Product ID: "?></b><b class="style1"><?=$bpdsid?></b>        	    	</td>
        	    	<td width="47%" bgcolor="#d6dff7" style="white-space:nowrap;">
                        <span id="tabs">
                                <ul>
	                                <!-- CSS Tabs -->
        	    					<? if($bookid){ ?>
									<li id="tabone" <?=($chkpage==1)?"class=\"current\"":""?>><a href="javascript:;" onClick="showHideLog('cmsdiv');"><span>Commission Log</span></a></li>
									<? } ?>
									<li id="tabtwo" <?=($chkpage==2)?"class=\"current\"":""?>><a href="javascript:;" onClick="showHideLog('srdiv');"><span>Sales Receipt Log</span></a></li>
									<? if($bookid){ ?>
									<li id="tabthree" <?=($chkpage==3)?"class=\"current\"":""?>><a href="javascript:;" onClick="showHideLog('thediv');"><span>Therapist Log</span></a></li>
                                	<? } ?>
                                </ul>
                      </span>     
       	    	  </td>
              </tr>
        	  </table>
	        	<input type="hidden" id="chkpage" name="chkpage" value="<?=$chkpage?>"/>
        		<input type="hidden" id="book_id" name="book_id" value="<?=$bookid?>" />
		</td>
	</tr>
<? 
// check booking for commission log  
if($bookid){ ?>
	<tr>
		<td width="100%" style="padding-left: 5px"><br/>
<span id="cmsdiv" style="display: <?=($chkpage==1)?"block":"none"?>;">
        	 <div class="group5" width="100%">
    			<fieldset>
					<legend><b>Booking Commission History	</b></legend><br>
					<table class="generalinfo" cellpadding="0" cellspacing="0" style="margin: -10px;"> 
					<tr height="20px" style="background:#a8c2cb;"> 
						<td style="white-space:nowrap;"><b>Log ID</b></td> 
						<td><b>Booking ID</b></td> 
						<td><b>Customer Name</b></td> 
						<td><b>Booking Party</b></td> 
						<td><b>Booking Person</b></td> 
						<td><b>Phone Number</b></td> 
						<td><b>Percent Commission</b></td>
						<td><b>Commission Value</b></td>  
						<td><b>Set CMS</b></td> 
						<td style="white-space:nowrap;"><b>Update By</b></td> 
						<td><b>Update Time</b></td> 
						<td><b>Update IP</b></td> 
					</tr>
<? for($i=0;$i<$cmsrs["rows"];$i++){ 
		$logid = $cmsrs[$i]["log_id"];		
		$bookid = $cmsrs[$i]["book_id"];		
		$csname = ($i)?($obj->checkDiffChar($cmsrs[$i-1]["b_customer_name"],$cmsrs[$i]["b_customer_name"])):$cmsrs[$i]["b_customer_name"];		
		$bpname = ($i)?($obj->checkDiffChar($cmsrs[$i-1]["company_name"],$cmsrs[$i]["company_name"])):$cmsrs[$i]["company_name"];		
		$bpperson = ($i)?($obj->checkDiffChar($cmsrs[$i-1]["c_bp_person"],$cmsrs[$i]["c_bp_person"])):$cmsrs[$i]["c_bp_person"];
		$bpphone = ($i)?($obj->checkDiffChar($cmsrs[$i-1]["c_bp_phone"],$cmsrs[$i]["c_bp_phone"])):$cmsrs[$i]["c_bp_phone"];
		$percentcms = ($i)?($obj->checkDiffChar($cmsrs[$i-1]["pcms_percent"],$cmsrs[$i]["pcms_percent"])):$cmsrs[$i]["pcms_percent"];
		$cmsrs[$i]["cms_value"]=number_format($cmsrs[$i]["cms_value"],2,".","");
		$cmsvalue = ($i)?($obj->checkDiffChar($cmsrs[$i-1]["cms_value"],$cmsrs[$i]["cms_value"])):$cmsrs[$i]["cms_value"];			
		$setcommission = ($cmsrs[$i]["c_set_cms"])?"<font style=\"color:#ff0000\">yes</font>":"no";		
		$updateperson = ($i)?($obj->checkDiffChar($cmsrs[$i-1]["user"],$cmsrs[$i]["user"])):$cmsrs[$i]["user"];	
		$updatetime = ($cmsrs[$i]["l_lu_date"]=="0000-00-00 00:00")?"-":$cmsrs[$i]["l_lu_date"];	
		$date = "-";$time="";
		if($updatetime!="-"){list($date,$time) = split(" ",$updatetime);}			
		$updateip = $cmsrs[$i]["l_lu_ip"];		
		$class="class=\"odd\"";
		if($i%2==0){	
			$class="class=\"even\"";
		}	
?>
						<tr height="20px" <?=$class?>> 
							<td class="report"><?=$logid?></td> 
							<td class="report"><?=$bpdsid?></td> 
							<td class="report"><?=$csname?></td> 
							<td class="report"><?=$bpname?></td> 
							<td class="report"><?=$bpperson?></td> 
							<td class="report" style="white-space:nowrap;"><?=$bpphone?></td> 
							<td class="report"><?=$percentcms?></td> 
							<td class="report"><?=$cmsvalue?></td> 
							<td class="report"><?=$setcommission?></td> 
							<td class="report"><?=$updateperson?></td> 
							<td class="report"><?=$dateobj->timezone_depend_branch($date,$time,"$sdateformat H:i:s",$bid)?></td>  
							<td class="report"><?=$updateip?></td> 
						</tr>
<? } ?>
					</table>&nbsp;
    			</fieldset>
			</div>
</span>
		</td>
	</tr>
<? } ?>
<? 
// check booking for therapist log  
if($bookid){ 
	
	?>
	<tr>
		<td width="100%" style="padding-left: 5px"><br/>
<span id="thediv" style="display: <?=($chkpage==3)?"block":"none"?>;">
        	 <div class="group5" width="100%">
    			<fieldset>
					<legend><b>Therapist Log</b></legend><br>
			<table class="generalinfo" cellpadding="0" cellspacing="0" style="margin: -10px;"> 
					<tr height="20px" style="background:#a8c2cb;"> 
						<td style="white-space:nowrap;"><b>Log ID</b></td> 
						<td><b>Booking ID</b></td> 
						<td><b>Therapist</b></td> 
						<td><b>Hour</b></td> 
						<td><b>Room</b></td> 
						<td><b>Package</b></td> 
						<td><b>Customer</b></td> 
						<td style="white-space:nowrap;"><b>Update By</b></td> 
						<td><b>Update Time</b></td> 
						<td><b>Update IP</b></td> 
						<td><b>Status</b></td> 
					</tr>
<? 
for($i=0;$i<$thrs["rows"];$i++){ 
		$logid = $thrs[$i]["log_id"];		
		$bookid = $thrs[$i]["book_id"];		
		$therapis = $thrs[$i]["therapist"];	
		$code = $thrs[$i]["emp_code"];
		$hour = $thrs[$i]["hour"];
		$t_room = $thrs[$i]["t_room"];
		$t_package = $thrs[$i]["t_package"];	
		$customer = $thrs[$i]["customer"];
		$updateperson = ($i)?($obj->checkDiffChar($thrs[$i-1]["user"],$thrs[$i]["user"])):$thrs[$i]["user"];	
		$updatetime = ($thrs[$i]["l_lu_date"]=="0000-00-00 00:00")?"-":$thrs[$i]["l_lu_date"];	
		$date = "-";$time="";
		if($thrs[$i]["log_th_status"]==""){$log_th_status="";
			}elseif($thrs[$i]["log_th_status"]==0){$log_th_status="Insert";
			}elseif($thrs[$i]["log_th_status"]==1){$log_th_status="Update";
			}elseif($thrs[$i]["log_th_status"]==2){$log_th_status="Delete";
			}elseif($thrs[$i]["log_th_status"]==3){$log_th_status="Decrease Customer";
			}else{$log_th_status="Increase Customer";}
			
		if($updatetime!="-"){list($date,$time) = split(" ",$updatetime);}			
		$updateip = $thrs[$i]["l_lu_ip"];		
		$class="class=\"odd\"";
		if($i%2==0){	
			$class="class=\"even\"";
		}	
?>
						<tr height="20px" <?=$class?>> 
							<td class="report"><?=$logid?></td> 
							<td class="report"><?=$bpdsid?></td> 
							<td class="report"><?php
							if($code==0){
								echo $therapis;
							}else{
								echo $code." ".$therapis;
							}
							  ?></td> 
							<td class="report"><?=$hour?></td> 
							<td class="report"><?=$t_room?></td> 
							<td class="report"><?=$t_package?></td> 
							<td class="report"><?=$customer?></td> 
							<td class="report"><?=$updateperson?></td> 
							<td class="report"><?=$dateobj->timezone_depend_branch($date,$time,"$sdateformat H:i:s",$bid)?></td>  
							<td class="report"><?=$updateip?></td> 
							<td class="report"><?php 
							if($thrs[$i]["log_th_status"]==2 || $thrs[$i]["log_th_status"]==3){ 
								echo "<span style='color:#FF0000;'>"."$log_th_status"."</span>";
							}else{
								echo $log_th_status;
							}
							
							 ?></td> 
						</tr>
<? } ?>
					</table>&nbsp;
			
    			</fieldset>
			</div>
</span>
		</td>
	</tr>
<? } ?>

	<tr>
		<td width="100%" style="padding-left: 5px">
<span id="srdiv" style="display: <?=($chkpage==2)?"block":"none"?>;">
        	 <div class="group5" width="100%">
    			<fieldset>
					<legend><b>Sales Receipt No: <?=implode(", \n",array_filter($sr["salesreceipt_number"]));?>	</b></legend><br>
					<table class="generalinfo" cellpadding="0" cellspacing="0" style="margin-top: -20px;"> 
					<tr height="100px;"> 
							<td width="70%" style="position:relation;"><br/>
								<div class="log">
		                        <fieldset>
		                        <legend><b>Sales Receipt History Log</b></legend>
		                        	<div class="loginner">
	                                  <table border="0" cellspacing="0" cellpadding="0">
	                                  <tr height="20px" style="background:#a8c2cb;"> 
										<td style="white-space:nowrap;"><b>Log ID</b></td> 
	                                  	<td><b>Booking ID</b></td> 
										<td><b>Receipt No.</b></td> 
										<td><b>Paid confirm</b></td> 
										<td><b>Method of Payment</b></td> 
										<td><b>Comment</b></td> 
										<td><b>Total</b></td> 
										<td><b>Update By</b></td> 
										<td><b>Update Time</b></td> 
										<td><b>Update IP</b></td> 
										<td><b>Remove</b></td> 
	                                  </tr>
	                                  
<? for($i=0,$chkcnt=0;$i<$srrs["rows"];$i++){ 
	if($srrs[$i]["salesreceipt_id"]==$srid){
		$logid = $srrs[$i]["log_id"];		
		$srnumber = ($chkcnt)?($obj->checkDiffChar($srrs[$i-1]["salesreceipt_number"],$srrs[$i]["salesreceipt_number"])):$srrs[$i]["salesreceipt_number"];		
		$setpaid = ($srrs[$i]["paid_confirm"])?"<font style=\"color:#ff0000\">yes</font>":"no";
		$payment = ($chkcnt)?($obj->checkDiffChar($srrs[$i-1]["payment"],$srrs[$i]["payment"])):$srrs[$i]["payment"];
		$comment = "&nbsp;".$srrs[$i]["sr_comment"];
		$total = $srrs[$i]["sr_total"];	
		$updateperson = ($chkcnt)?($obj->checkDiffChar($srrs[$i-1]["user"],$srrs[$i]["user"])):$srrs[$i]["user"];			
		$updatetime = ($srrs[$i]["sr_datets"]=="0000-00-00 00:00")?"-":$srrs[$i]["sr_datets"];		
		$date = "-";$time="";
		if($updatetime!="-"){list($date,$time) = split(" ",$updatetime);}		
		$updateip = $srrs[$i]["l_lu_ip"];	
		$remove = ($srrs[$i]["active"]==0)?"<font style=\"color:#ff0000\">yes</font>":"no";	
		$class="class=\"odd\"";
		$chkcnt++;
		if($i%2==0){	
			$class="class=\"even\"";
		}	
?>
						<tr height="20px" <?=$class?>> 
							<td class="report"><?=$logid?></td> 
							<td class="report"><?=$bpdsid?></td> 
							<td class="report"><?=($srnumber)?$srnumber:"-"?></td> 
							<td class="report"><?=$setpaid?></td> 
							<td class="report"><?=($srrs[$i]["pay_id"]>1)?$payment:"-"?></td> 
							<td class="report"><?=$comment?></td> 
							<td class="report"><?=$total?></td> 
							<td class="report"><?=$updateperson?></td> 
							<td class="report"><?=$dateobj->timezone_depend_branch($date,$time,"$sdateformat H:i:s",$bid)?></td> 
							<td class="report"><?=$updateip?></td> 
							<td class="report"><?=$remove?></td> 
						</tr>
<? }
}?>

	                                  </table>&nbsp;
	                                 </div>
	                            </fieldset>
	                            </div>
							</td> 
							<td width="30%" valign="top"><br/>
								<div class="log">
		                        <fieldset>
		                        <legend><b>Sales Receipt Printed Log</b></legend>
		                        	<div class="loginner">
	                                  <table border="0" cellspacing="0" cellpadding="0">
	                                  <tr height="20px" style="background:#a8c2cb;"> 
										<td style="white-space:nowrap;"><b>Log ID</b></td> 
										<td><b>Printed By</b></td> 
										<td><b>Printed Time</b></td> 
										<td><b>Printed IP</b></td> 
	                                  	<td><b>Reprinted</b></td> 
	                                  </tr>
	                                  
<? for($i=0;$i<$srprintrs["rows"];$i++){ 
		$logid = $srprintrs[$i]["log_id"];		
		$updateperson = ($i)?($obj->checkDiffChar($srprintrs[$i-1]["user"],$srprintrs[$i]["user"])):$srprintrs[$i]["user"];			
		$updatetime = ($srprintrs[$i]["l_lu_date"]=="0000-00-00 00:00")?"-":$srprintrs[$i]["l_lu_date"];			
		$updateip = $srprintrs[$i]["l_lu_ip"];		
		$date = "-";$time="";
		if($updatetime!="-"){list($date,$time) = split(" ",$updatetime);}		
		$reprinttime = ($srprintrs[$i]["reprint_times"]=="0")?"-":$srprintrs[$i]["reprint_times"];	
		$class="class=\"odd\"";
		if($i%2==0){	
			$class="class=\"even\"";
		}	
?>
						<tr height="20px" <?=$class?>> 
							<td class="report"><?=$logid?></td> 
							<td class="report"><?=$updateperson?></td> 
							<td class="report"><?=$dateobj->timezone_depend_branch($date,$time,"$sdateformat H:i:s",$bid)?></td> 
							<td class="report"><?=$updateip?></td> 
							<td class="report"><?=$reprinttime?></td> 
						</tr>
<? } ?>

	                                  </table>&nbsp;
	                                 </div>
	                            </fieldset>
	                            </div>
							</td> 
					</tr>
					<tr height="20px">
							<td colspan="2"><br/>
								<div class="log">
		                        <fieldset>
		                        <legend><b>Sales Receipt Detail Log</b></legend>
		                        	<div class="loginner" style="height: 400px;">
	                                  <table border="0" cellspacing="0" cellpadding="0">
	                                  <tr height="20px" style="background:#a8c2cb;"> 
										<td style="white-space:nowrap;"><b>Log ID</b></td> 
										<td><b>Sales Receipt detail ID</b></td> 
										<td><b>Product</b></td> 
										<td><b>Amount</b></td> 
										<td><b>Qty.</b></td> 
										<td><b>Set tax</b></td> 
										<td><b>Set sc</b></td> 
										<td><b>Update By</b></td> 
										<td><b>Update Time</b></td> 
										<td><b>Update IP</b></td> 
	                                  	<td><b>Remove</b></td> 
	                                  </tr>
	                                  
<? for($i=0;$i<$srdetailrs["rows"];$i++){ 
		$logid = $srdetailrs[$i]["log_id"];		
		$chkstatus = ($i&&$srdetailrs[$i]["srdetail_id"]==$srdetailrs[$i-1]["srdetail_id"])?true:false;
		$product = ($chkstatus)?($obj->checkDiffChar($srdetailrs[$i-1]["pd_name"],$srdetailrs[$i]["pd_name"])):$srdetailrs[$i]["pd_name"];
		$amount = ($chkstatus)?($obj->checkDiffChar($srdetailrs[$i-1]["unit_price"],$srdetailrs[$i]["unit_price"])):$srdetailrs[$i]["unit_price"];
		$qty = ($chkstatus)?($obj->checkDiffChar($srdetailrs[$i-1]["qty"],$srdetailrs[$i]["qty"])):$srdetailrs[$i]["qty"];
		$settax = ($srdetailrs[$i]["set_tax"])?"<img src=\"/images/active.png\" border=\"0\" title=\"active\" />":"<img src=\"/images/inactive.png\" border=\"0\" title=\"inactive\" />";
		$setsc = ($srdetailrs[$i]["set_sc"])?"<img src=\"/images/active.png\" border=\"0\" title=\"active\" />":"<img src=\"/images/inactive.png\" border=\"0\" title=\"inactive\" />";
	
		$updateperson = ($i)?($obj->checkDiffChar($srdetailrs[$i-1]["user"],$srdetailrs[$i]["user"])):$srdetailrs[$i]["user"];			
		$updatetime = ($srdetailrs[$i]["l_lu_date"]=="0000-00-00 00:00")?"-":$srdetailrs[$i]["l_lu_date"];		
		$date = "-";$time="";
		if($updatetime!="-"){list($date,$time) = split(" ",$updatetime);}			
		$updateip = $srdetailrs[$i]["l_lu_ip"];	
		$remove = ($srdetailrs[$i]["active"]==0)?"<font style=\"color:#ff0000\">yes</font>":"no";		
		$class="class=\"odd\"";
		if($i%2==0){	
			$class="class=\"even\"";
		}	
?>
						<tr height="20px" <?=$class?>> 
							<td class="report"><?=$logid?></td> 
							<td class="report"><?=$srdetailrs[$i]["srdetail_id"]?></td> 
							<td class="report"><?=$product?></td> 
							<td class="report"><?=$amount?></td> 
							<td class="report"><?=$qty?></td> 
							<td class="report"><?=$settax?></td> 
							<td class="report"><?=$setsc?></td> 
							<td class="report"><?=$updateperson?></td> 
							<td class="report"><?=$dateobj->timezone_depend_branch($date,$time,"$sdateformat H:i:s",$bid)?></td> 
							<td class="report"><?=$updateip?></td> 
							<td class="report"><?=$remove?></td> 
						</tr>
<? } ?>

	                                  </table>
	                                 </div>
	                            </fieldset>
	                            </div></td> 
					</tr>
					
	
					
					<tr height="20px">
							<td colspan="2"><br/>
								<div class="log">
		                        <fieldset>
		                        <legend><b>Mutipayment Detail Log</b></legend>
		                        	<div class="loginner" style="height: 300px;">
	                                  <table border="0" cellspacing="0" cellpadding="0">
	                                  <tr height="20px" style="background:#a8c2cb;"> 
										<td style="white-space:nowrap;"><b>Log ID</b></td> 
										<td><b>Pay Type</b></td> 
										<td><b>Pay Price</b></td> 
										<td><b>Update By</b></td> 
										<td><b>Update Time</b></td> 
										<td><b>Update IP</b></td> 
	                                  	<td><b>Remove</b></td> 
	                                  </tr>
	                                  
<? for($i=0;$i<$mpdetailrs["rows"];$i++){ 
		$logid = $mpdetailrs[$i]["log_id"];		
		$updateperson = ($i)?($obj->checkDiffChar($mpdetailrs[$i-1]["user"],$mpdetailrs[$i]["user"])):$srdetailrs[$i]["user"];			
		$updatetime = ($mpdetailrs[$i]["l_lu_date"]=="0000-00-00 00:00")?"-":$mpdetailrs[$i]["l_lu_date"];		
		$date = "-";$time="";
		if($updatetime!="-"){list($date,$time) = split(" ",$updatetime);}			
		$updateip = $mpdetailrs[$i]["l_lu_ip"];	
		$remove = ($mpdetailrs[$i]["active"]==0)?"<font style=\"color:#ff0000\">yes</font>":"no";		
		$class="class=\"odd\"";
		if($i%2==0){	
			$class="class=\"even\"";
		}	
?>
						<tr height="20px" <?=$class?>> 
							<td class="report"><?=$logid?></td> 
							<td class="report"><?=$mpdetailrs[$i]["pay_name"]?></td> 
							<td class="report"><?=$mpdetailrs[$i]["pay_total"]?></td> 
							<td class="report"><?=$updateperson?></td> 
							<td class="report"><?=$dateobj->timezone_depend_branch($date,$time,"$sdateformat H:i:s",$bid)?></td> 
							<td class="report"><?=$updateip?></td> 
							<td class="report"><?=$remove?></td> 
						</tr>
<? } ?>

	                                  </table>&nbsp;
	                                 </div>
	                            </fieldset>
	                            </div></td> 
					</tr>

			
			
					</table>
			
    			</fieldset>
			</div>
</span>
		</td>
	</tr>
</table>
</body>
</html>