<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("customer.inc.php");
$obj = new customer(); 

$chkpage=$obj->getParameter("chkpage",2);
$csphone = $obj->getParameter("csphone");
$msg="";

// find customer individual information
$csrs = $obj->getapptcustinfo($csphone);

// find customer sales receipt information
$rssr = $obj->getapptcustsr($csphone);

// find customer treatment information
$rstrm = $obj->getapptcusttrm($csphone);

// for treatment information find maximum massage per each individual info. and massage in each rows
$msg["maxmsgcnt"]=0;
for($i=0;$i<$rstrm["rows"];$i++){
	$sql = "select massage_id,trm_name " .
			"from da_mult_msg,db_trm " .
			"where indivi_id=".$rstrm[$i]["indivi_id"]." " .
			"and da_mult_msg.massage_id=db_trm.trm_id ";
	$rsmsg = $obj->getResult($sql);
	for($j=0;$j<$rsmsg["rows"];$j++){
		$msg[$i][$j] = $rsmsg[$j]["trm_name"];
	}
	if($rsmsg["rows"] > $msg["maxmsgcnt"]){
		$msg["maxmsgcnt"] =$rsmsg["rows"];	//therapist max massage count 
	}
	// for treatment information find therapist name in each rows
	$sql = "select therapist_id,emp_nickname " .
			"from da_mult_th,l_employee " .
			"where indivi_id=".$rstrm[$i]["indivi_id"]." " .
			"and da_mult_th.therapist_id=l_employee.emp_id ";
	$rsth = $obj->getResult($sql);
	$rstrm[$i]["therapist_name"] = "";
	for($j=0;$j<$rsth["rows"];$j++){
		if($j){$rstrm[$i]["therapist_name"] .= ",";}
		$rstrm[$i]["therapist_name"] .= $rsth[$j]["emp_nickname"];
	}
}
/***************************************************
 * Security checking
 ***************************************************/
// check user edit permission 
$pageid = "1";	// appointment page
$pagestatus = $object->check_permission($pageid,$permissionrs);
$chkPageEdit=false;$chkPageView=false;
if($pagestatus=="e"){
	$chkPageEdit=true;$chkPageView=true;
}else if($pagestatus=="v"){
	$chkPageEdit=false;$chkPageView=true;
}else if($pagestatus=="n"){
	$chkPageEdit=false;$chkPageView=false;
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Customer History</title>
<link href="/appt/css/style.css" rel="stylesheet" type="text/css" />
<link href="/css/styles.css" rel="stylesheet" type="text/css" />
<script src="../giftinfo/scripts/component.js" type="text/javascript"></script>
<script src="/scripts/tooltip/boxover.js" type="text/javascript"></script>
</head>

<body>
<table class="main" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="right" class="header" style="padding-right:12px;padding-bottom:0px;border-bottom: 2px solid #ffffff;">
			 <table border="0" height="20" cellpadding="0" cellspacing="0">
        	    <tr>
        	    	<td>
                        <span id="tabs">
                                <ul>
	                                <li id="tabtwo" <?=($chkpage==2)?"class=\"current\"":""?>><a href="javascript:;" onClick="showHide('saleDiv');"><span>Sales History</span></a></li>
									<li id="tabthree" <?=($chkpage==3)?"class=\"current\"":""?>><a href="javascript:;" onClick="showHide('treatDiv');"><span>Treatment History</span></a></li>

                                </ul>
                        </span>     
        	    	</td>
                  </tr>
        	  </table>
	        	<input type="hidden" id="chkpage" name="chkpage" value="<?=$chkpage?>"/>
		</td>
	</tr>
	<tr>
		<td width="100%" style="padding-left: 5px"><br/>
			<div align="left" class="group5">
    			<fieldset>
    			<?if(!isset($memberId)){$memberId="";}?>
					<legend><b>Customer Information : <?=$memberId?></b></legend>
					<table class="generalinfo" cellpadding="0" cellspacing="0" style="margin:0px;"> 
						<tr height="20px" style="background:#a8c2cb;"> 
							<td><b>Customer Name</b></td> 
							<td><b>Phone</b></td> 
							<td><b>E-mail</b></td> 
							<td><b>Age</b></td> 
							<td><b>Birthday</b></td> 
							<td><b>Sex</b></td> 
							<td><b>Nationality</b></td> 
							<td><b>Resident</b></td> 
						</tr>
<? 
$resident = "-";
if($csrs[0]["resident"]){$resident="Resident";}
else if($csrs[0]["visitor"]){$resident="Visitor";}
?>
						<tr height="20px" class="odd" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#d3d3d3'"> 
							<td class="report"><?=$csrs[0]["cs_name"]?></td> 
							<td class="report"><?=$csrs[0]["cs_phone"]?></td> 
							<td class="report"><?=$csrs[0]["cs_email"]?></td> 
							<td class="report"><?=($csrs[0]["cs_age"]==0)?"-":$csrs[0]["cs_age"]?></td> 
							<td class="report"><?=($csrs[0]["cs_birthday"]=="0000-00-00")?"-":$dateobj->convertdate($csrs[0]["cs_birthday"],'Y-m-d',$sdateformat)?></td> 
							<td class="report"><?=($csrs[0]["sex_type"]==0)?"-":$csrs[0]["sex_type"]?></td> 
							<td class="report"><?=$csrs[0]["nationality_name"]?></td> 
							<td class="report"><?=$resident?></td> 
						</tr>
					</table>
         			</fieldset>
			</div>
		</td>
	</tr>
	<tr>
		<td width="100%" style="padding-left: 5px"><br/><br/>
		
		<!-- Sale History -->

        	 <div id="saleDiv" style="display:block;" class="group5" width="100%" >
    			<fieldset>
					<legend><b>Sale History	</b></legend>
					
					<table class="generalinfo" cellpadding="0" cellspacing="0" style="margin:0px;"> 
						<tr height="20px" style="background:#a8c2cb;"> 
							<td align="center"><b>Date</b></td> 
							<td align="center"><b>Branch</b></td> 
							<td align="center"><b>Booking ID</b></td> 
							<td align="center"><b>Sales Receipt Number</b></td> 
							<td align="center"><b>Amount</b></td> 
							<td align="center"><b>Method of Payment</b></td> 
						</tr>
			<?
for($i=0; $i<$rssr["rows"]; $i++) {

$url = "manage_booking.php?chkpage=1&bookid=".$rssr[$i]["book_id"];
$pagename = "manageBooking".$rssr[$i]["book_id"];
$id="<a href='javascript:;;' onClick=\"window.open('/appt/$url','$pagename','resizable=0,scrollbars=1')\" class=\"menu\">".$rssr[$i]["bpds_id"]."</a>";

$class="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";
if($i%2==0){	
		$class="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";
}	

?>
						<tr height="20px" <?=$class?>> 
								<td class="report" align="center"><?=($rssr[$i]["appt_date"]=="0000-00-00")?"-":$dateobj->convertdate($rssr[$i]["appt_date"],'Y-m-d',$sdateformat)?></td> 
								<td class="report" align="center"><?=$rssr[$i]["branch_name"]?></td>
								<td class="report" align="center"><?=$id?></td>
								<td class="report" align="center"><?=($rssr[$i]["salesreceipt_number"])?$rssr[$i]["salesreceipt_number"]:"-"?></td>
								<td class="report" align="right"><?=number_format($rssr[$i]["sr_total"],2,".",",")?></td>
								<td class="report" align="center"><?=($rssr[$i]["pay_id"]>1)?$rssr[$i]["pay_name"]:"-"?></td>
						</tr>
<?	} ?>
					</table> 
      	 		</fieldset>
		</div>
		
		<!-- Treatment History -->
		
        	 <div id="treatDiv" style="display:none;" class="group5" width="100%" >
		    	<fieldset>
					<legend><b>Treatment History</b></legend>
					
					<table class="generalinfo" cellpadding="0" cellspacing="0" style="margin:0px;"> 
						<tr height="20px" style="background:#a8c2cb;"> 
							<td align="center"><b>Date</b></td> 
							<td align="center"><b>Branch</b></td> 
							<td align="center"><b>Booking ID</b></td> 
							<td align="center"><b>Room</b></td> 
							<td align="center"><b>Hour</b></td> 
							<td align="center"><b>Package</b></td> 
<?		
			for($h=1;$h<=$msg["maxmsgcnt"];$h++){
					echo "<td align=\"center\"><b>Massage $h</b></td>";
			}
?>     							
							<td align="center"><b>Therapist</b></td> 
						</tr>
<?
for($i=0; $i<$rstrm["rows"]; $i++) {
	
$url = "manage_booking.php?chkpage=1&bookid=".$rstrm[$i]["book_id"];
$pagename = "manageBooking".$rstrm[$i]["book_id"];
$bpdsid = $obj->getIdToText($rstrm[$i]["book_id"],"c_bpds_link","bpds_id","tb_id","tb_name=\"a_bookinginfo\"");
$id="<a href='javascript:;;' onClick=\"window.open('/appt/$url','$pagename','resizable=0,scrollbars=1')\" class=\"menu\">$bpdsid</a>";

$class="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";
if($i%2==0){	
		$class="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";
}	
$hour = substr($rstrm[$i]["hour_name"],0,5);
$package = ($rstrm[$i]["package_id"]==1)?"-":$obj->getIdToText($rstrm[$i]["package_id"],"db_package","package_name","package_id");

$trmdetail = "";
if($rstrm[$i]["strength_id"]>1){
	$trmdetail .= "Strength : ".$obj->getIdToText($rstrm[$i]["strength_id"],"l_strength","strength_type","strength_id");
}
if($rstrm[$i]["scrub_id"]>1){
	if($trmdetail != ""){$trmdetail .= "<br>";}
	$trmdetail .= "Scrub : ".$obj->getIdToText($rstrm[$i]["scrub_id"],"db_trm","trm_name","trm_id");
}
if($rstrm[$i]["wrap_id"]>1){
	if($trmdetail != ""){$trmdetail .= "<br>";}
	$trmdetail .= "Wrap : ".$obj->getIdToText($rstrm[$i]["wrap_id"],"db_trm","trm_name","trm_id");
}
if($rstrm[$i]["bath_id"]>1){
	if($trmdetail != ""){$trmdetail .= "<br>";}
	$trmdetail .= "Bath : ".$obj->getIdToText($rstrm[$i]["bath_id"],"db_trm","trm_name","trm_id");
}
if($rstrm[$i]["facial_id"]>1){
	if($trmdetail != ""){$trmdetail .= "<br>";}
	$trmdetail .= "Facial : ".$obj->getIdToText($rstrm[$i]["facial_id"],"db_trm","trm_name","trm_id");
}

$title = " title=\" header=[Treatment Detail] body=[".htmlspecialchars($trmdetail)."]\" style=\"cursor: pointer;\"";
?>
						<tr height="20px" <?="$class $title"?>> 
								<td class="report" align="center"><?=($rstrm[$i]["appt_date"]=="0000-00-00")?"-":$dateobj->convertdate($rstrm[$i]["appt_date"],'Y-m-d',$sdateformat)?></td> 
								<td class="report" align="center"><?=$rstrm[$i]["branch_name"]?></td>
								<td class="report" align="center"><?=$id?></td>
								<td class="report" align="center"><?=$rstrm[$i]["room_name"]?></td>
								<td class="report" align="center"><?=$hour?></td>
								<td class="report" align="center"><?=$package?></td>
<?
for($j=0;$j<$msg["maxmsgcnt"];$j++){
		if(isset($msg[$i][$j])){echo "<td class=\"report\" align=\"center\">".$msg[$i][$j]."</td>";}
		else{echo "<td class=\"report\" align=\"center\">-</td>";}
}
?>
								<td class="report" align="center"><?=$rstrm[$i]["therapist_name"]?></td>
						</tr>
<?	} ?>
					</table> 
      	 		</fieldset>
		</div>
	  </td>
	</tr>
</table>
</body>
</html>