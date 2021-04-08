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

$bankacc_cms_id = $obj->getParameter("bankacc_cms_id");

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Commission Bank Details Log</title>
<link href="/css/styles.css" rel="stylesheet" type="text/css" />
<link href="/appt/css/style.css" rel="stylesheet" type="text/css" />
<script src="../scripts/ajax.js" type="text/javascript"></script>
</head>
<body>
<table class="main" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="right" class="header" style="padding-right:12px;padding-bottom:0px;border-bottom: 2px solid #ffffff;">
			<table width="100%" cellpadding="0" cellspacing="0">
        	    <tr>
        	    	<td width="53%" style="padding-bottom:5px">
					<b><?="Commission Bank Details ID: "?></b><b class="style1"><?=$bankacc_cms_id?></b>        	    	</td>
              </tr>
        	  </table>
        		<input type="hidden" id="bankacc_cms_id" name="bankacc_cms_id" value="<?=$bankacc_cms_id?>" />
		</td>
	</tr>
<? 
// check booking for commission log  
if($bankacc_cms_id){ 
	
	//$sql_bankacc="select * from `log_al_bankacc` where `bankacc_cms_id`=$bankacc_cms_id".
	//			 " order by `log_id`";
	$sql_bankacc="select * from `log_al_bankacc` where `bankacc_cms_id`=$bankacc_cms_id";		 
	$rs_bankacc=$obj->getResult($sql_bankacc);
?>
	<tr>
		<td width="100%" style="padding-left: 5px"><br/>
    			<fieldset>
					<legend><b>History	</b></legend><br>
					<table class="generalinfo" cellpadding="0" cellspacing="0"> 
					<tr height="20px" style="background:#a8c2cb;"> 
						<td style="white-space:nowrap;"><b>Log ID</b></td> 
						<td><b>BP Phone</b></td> 
						<td><b>BP Person</b></td> 
						<td><b>Company Name</b></td> 
						<td><b>Bank Name</b></td> 
						<td><b>Branch</b></td> 
						<td><b>Account Name</b></td> 
						<td><b>Account No.</b></td>
						<td><b>Bank Comment</b></td>
						<td><b>Add By</b></td> 
						<td><b>Add Time</b></td> 
						<td><b>Add IP</b></td>
						<td><b>Update By</b></td> 
						<td><b>Update Time</b></td>
						<td><b>Update IP</b></td> 
						<td><b>Active</b></td> 
					</tr>
<? for($i=0;$i<$rs_bankacc["rows"];$i++){ 
		$logid = $rs_bankacc[$i]["log_id"];		
		$bp_phone = ($i)?($obj->checkDiffChar($rs_bankacc[$i-1]["c_bp_phone"],$rs_bankacc[$i]["c_bp_phone"])):$rs_bankacc[$i]["c_bp_phone"];			
		$bp_person = ($i)?($obj->checkDiffChar($rs_bankacc[$i-1]["c_bp_person"],$rs_bankacc[$i]["c_bp_person"])):$rs_bankacc[$i]["c_bp_person"];
		$bp_comment = ($i)?($obj->checkDiffChar($rs_bankacc[$i-1]["bankacc_comment"],$rs_bankacc[$i]["bankacc_comment"])):$rs_bankacc[$i]["bankacc_comment"];
		
	    if($rs_bankacc[$i]["tb_name"]=="al_accomodations"){$bp_company_chk[$i]=$obj->getIdToText($rs_bankacc[$i]["c_bp_id"],"al_accomodations","acc_name","acc_id");}
		else{$bp_company_chk[$i]=$obj->getIdToText($rs_bankacc[$i]["c_bp_id"],"al_bookparty","bp_name","bp_id");}
	    
	    $bp_company = ($i)?($obj->checkDiffChar($bp_company_chk[$i-1],$bp_company_chk[$i])):$bp_company_chk[$i];
	    
	    $bp_bank_chk[$i] = $obj->getIdToText($rs_bankacc[$i]["bank_id"],"l_bankname","bank_Ename","bank_id");
	    $bp_bank= ($i)?($obj->checkDiffChar($bp_bank_chk[$i-1],$bp_bank_chk[$i])):$bp_bank_chk[$i];
	    
	    $bp_branch = ($i)?($obj->checkDiffChar($rs_bankacc[$i-1]["bank_branch"],$rs_bankacc[$i]["bank_branch"])):$rs_bankacc[$i]["bank_branch"];
	    
	    $bp_accname = ($i)?($obj->checkDiffChar($rs_bankacc[$i-1]["bankacc_name"],$rs_bankacc[$i]["bankacc_name"])):$rs_bankacc[$i]["bankacc_name"];
		$bp_accnum = ($i)?($obj->checkDiffChar($rs_bankacc[$i-1]["bankacc_number"],$rs_bankacc[$i]["bankacc_number"])):$rs_bankacc[$i]["bankacc_number"];
		
		$bp_c_lu_date = $rs_bankacc[$i]["c_lu_date"];
		if(!isset($bp_c_lu_user_chk[$i-1])){$bp_c_lu_user_chk[$i-1]="";}
		if(!isset($bp_l_lu_user_chk[$i])){$bp_l_lu_user_chk[$i]="";}
		if($bp_c_lu_date!="0000-00-00 00:00:00"){
		$bp_c_lu_user_chk[$i] = $obj->getIdToText($rs_bankacc[$i]["c_lu_user"],"s_user","u","u_id");
		$bp_c_lu_user = ($i)?($obj->checkDiffChar($bp_c_lu_user_chk[$i-1],$bp_c_lu_user_chk[$i])):$bp_c_lu_user_chk[$i];
		$bp_c_lu_date = ($i)?($obj->checkDiffChar($rs_bankacc[$i-1]["c_lu_date"],$rs_bankacc[$i]["c_lu_date"])):$rs_bankacc[$i]["c_lu_date"];
		$bp_c_lu_ip = ($i)?($obj->checkDiffChar($rs_bankacc[$i-1]["c_lu_ip"],$rs_bankacc[$i]["c_lu_ip"])):$rs_bankacc[$i]["c_lu_ip"];
		}else{
			$bp_c_lu_user="";
			$bp_c_lu_date="";
			$bp_c_lu_ip="";
		}
		
		$bp_l_lu_date = $rs_bankacc[$i]["l_lu_date"];
		if($bp_l_lu_date!="0000-00-00 00:00:00"){
		$bp_l_lu_user_chk[$i] = $obj->getIdToText($rs_bankacc[$i]["l_lu_user"],"s_user","u","u_id");
		$bp_l_lu_user = ($i)?($obj->checkDiffChar($bp_l_lu_user_chk[$i-1],$bp_l_lu_user_chk[$i])):$bp_l_lu_user_chk[$i]; 
		$bp_l_lu_date = ($i)?($obj->checkDiffChar($rs_bankacc[$i-1]["l_lu_date"],$rs_bankacc[$i]["l_lu_date"])):$rs_bankacc[$i]["l_lu_date"];
		$bp_l_lu_ip = ($i)?($obj->checkDiffChar($rs_bankacc[$i-1]["l_lu_ip"],$rs_bankacc[$i]["l_lu_ip"])):$rs_bankacc[$i]["l_lu_ip"];
		}else{
			$bp_l_lu_user="";
			$bp_l_lu_date="";
			$bp_l_lu_ip="";
		}
		
		$bp_active = $rs_bankacc[$i]["bankacc_active"];
		
		$date = "-";$time="";
		//if($updatetime!="-"){list($date,$time) = split(" ",$updatetime);}				
		
		$class="class=\"odd\"";
		if($i%2==0){	
			$class="class=\"even\"";
		}	
?>
						<tr height="20px" <?=$class?>> 
							<td class="report" style="font-size:11px;"><?=$logid?></td> 
							<td class="report" style="font-size:11px;"><?=$bp_phone?></td> 
							<td class="report" style="font-size:11px;"><?=$bp_person?></td>
							<td class="report" style="font-size:11px;"><?=($bp_company)?$bp_company:"&nbsp"?></td>
							<td class="report" style="font-size:11px;"><?=($bp_bank)?$bp_bank:"&nbsp"?></td>
							<td class="report" style="font-size:11px;"><?=($bp_branch)?$bp_branch:"&nbsp"?></td>
							<td class="report" style="font-size:11px;"><?=($bp_accname)?$bp_accname:"&nbsp"?></td>
							<td class="report" style="font-size:11px;"><?=($bp_accnum)?$bp_accnum:"&nbsp"?></td>
							<td class="report" style="font-size:11px;"><?=($bp_comment)?$bp_comment:"&nbsp"?></td> 
							<td class="report" style="font-size:11px;"><?=($bp_c_lu_user)?$bp_c_lu_user:"&nbsp"?></td> 
							<td class="report" style="font-size:11px;"><?=($bp_c_lu_date)?$bp_c_lu_date:"&nbsp"?></td> 
							<td class="report" style="font-size:11px;"><?=($bp_c_lu_ip)?$bp_c_lu_ip:"&nbsp"?></td>  
							<td class="report" style="font-size:11px;"><?=($bp_l_lu_user)?$bp_l_lu_user:"&nbsp"?></td> 
							<td class="report" style="font-size:11px;"><?=($bp_l_lu_date)?$bp_l_lu_date:"&nbsp"?></td> 
							<td class="report" style="font-size:11px;"><?=($bp_l_lu_ip)?$bp_l_lu_ip:"&nbsp"?></td> 
							<td class="report" style="font-size:11px;<?=($bp_active!=1)?"color:red;":""?>"><?=($bp_active==1)?"yes":"no"?></td> 
						</tr>
<? } ?>
					</table>&nbsp;
    			</fieldset>
		</td>
	</tr>
<? } ?>		

		</td>
	</tr>
</table>
</body>
</html>