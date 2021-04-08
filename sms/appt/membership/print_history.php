<?php
$root = $_SERVER["DOCUMENT_ROOT"];
include ("$root/include.php");
require_once ("membership.inc.php");
$obj = new membership();

$chkpage = $obj->getParameter("chkpage", 2);
$membercode = $obj->getParameter("memberId");
$export = $obj->getParameter("export");

// find membership information
$memberrs = $obj->getmemberinfo($membercode);
$memberid = $memberrs[0]["member_id"];

// find membership comment
$rscomment = $obj->getmembercomment($memberid);

// add membership comment
$comment = $obj->getParameter("comment", "");
$save = $obj->getParameter("save");
$obj->setErrorMsgColor("#f00");
$successmsg = "";
$errormsg = "";
if ($save) {
	$commentid = $obj->setmembercomment($comment, $memberid, $rscomment);
	if ($commentid) {
		$successmsg = "Update comment complete.";
		$rscomment = $obj->getmembercomment($memberid);
	} else {
		$errormsg = $obj->getErrorMsg();
	}
}

// find membership information
$rssr = $obj->getmembersr($membercode);

// find life to date
$ltd = $obj->getsramount($rssr);

// find year to date
$chkdate = date("Ymd", mktime(0, 0, 0, 1, 1, date("Y"))); // first date of year
$ytd = $obj->getsramount($rssr, $chkdate);

// find customer treatment information
$rstrm = $obj->getmembertrm($membercode);

// for treatment information find maximum massage per each individual info. and massage in each rows
$msg["maxmsgcnt"] = 0;
for ($i = 0; $i < $rstrm["rows"]; $i++) {
	$sql = "select massage_id,trm_name " .
	"from da_mult_msg,db_trm " .
	"where indivi_id=" . $rstrm[$i]["indivi_id"] . " " .
	"and da_mult_msg.massage_id=db_trm.trm_id ";
	$rsmsg = $obj->getResult($sql);
	for ($j = 0; $j < $rsmsg["rows"]; $j++) {
		$msg[$i][$j] = $rsmsg[$j]["trm_name"];
	}
	if ($rsmsg["rows"] > $msg["maxmsgcnt"]) {
		$msg["maxmsgcnt"] = $rsmsg["rows"]; //therapist max massage count 
	}
	// for treatment information find therapist name in each rows
	$sql = "select therapist_id,emp_nickname " .
	"from da_mult_th,l_employee " .
	"where indivi_id=" . $rstrm[$i]["indivi_id"] . " " .
	"and da_mult_th.therapist_id=l_employee.emp_id ";
	$rsth = $obj->getResult($sql);
	$rstrm[$i]["therapist_name"] = "";
	for ($j = 0; $j < $rsth["rows"]; $j++) {
		if ($j) {
			$rstrm[$i]["therapist_name"] .= ",";
		}
		$rstrm[$i]["therapist_name"] .= $rsth[$j]["emp_nickname"];
	}
}

// for oasis's old member history
if ($GLOBALS["global_oasisclient"] == true) {
	require_once ("destiny.inc.php");
	$objomh = new destiny();
	$rs = $objomh->get_memberhistory($membercode);
	$rsth = $objomh->get_membertreatment($membercode);
} else {
	$rs["rows"] = 0;
	$rsth["rows"] = 0;
}

$sum = 0;
for ($i = 0; $i < $rs["rows"]; $i++) {
		if ($rs[$i]["catagory_id"] == 11) {
			$sum += $rs[$i]["total"];
		} else {
			$sum -= $rs[$i]["total"];
		}
}

?>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<table width="100%" border="0" cellspacing="0" cellpadding="20">
		<tr>
		<td width="100%" style="padding-left: 15px"><br/>
			<div align="left" valign="top" class="group5" width="100%">
						<table border="0" width="100%" class="generalinfo">
								<tr>	
									<td width="14%" ></td>
		                        	<td width="13%"></td>
		                        	<td width="10%"></td>
		                        	<td width="28%"></td>
		                        	<td width="8%"></td>
		                        	<td width="12%"></td>
								</tr>
								<tr height="20">
		                        	<td colspan="6" align="center"><b> Information of membership ID : </b><b style="color:#ff0000"><?=$membercode?></b></td>
		                        </tr>
								<tr height="20">
		                        	<td colspan="6" align="center"><hr></td>
		                        </tr>
								<tr height="20">
		                        	<td><b>Member Name : </b></td>
		                        	<td><?=$memberrs[0]["fname"]." ".$memberrs[0]["mname"]." ".$memberrs[0]["lname"]?></td>
		                        	<td><b>Category : <b></td>
		                        	<td><?=$obj->getIdToText($memberrs[0]["category_id"],"mb_category","category_name","category_id")?></td>
		                        	<td><b>Phone : </b></td>
		                        	<td><?=$memberrs[0]["phone"]?></td>
		                        	
		                        </tr>
		                        <tr height="20">
		                        	<td><b>Start Date : </b></td>
		                        	<td><?=$dateobj->convertdate($memberrs[0]["joindate"],'Y-m-d',$sdateformat)?></td>
		                        	<td><b>Address : <b></td>
		                        	<td style="white-space: normal"><?=str_replace("[br]","<br>",$memberrs[0]["address"])?></td>
		                        	<td><b>Mobile  : </b></td>
		                        	<td><?=$memberrs[0]["mobile"]?></td>
		                        </tr>
		                        <tr height="20">
		                            <td><b>Expried Date : </b></td>
		                        	<td><?=($memberrs[0]["expireddate"]=="0000-00-00")?"Unlimited":$dateobj->convertdate($memberrs[0]["expireddate"],'Y-m-d',$sdateformat)?></td>
		                        	<td><b>City : </b></td>
		                        	<td><?=$memberrs[0]["city"]?></td>
		                        	<td><b>E-mail : </b></td>
		                        	<td><?=$memberrs[0]["email"]?>&nbsp;</td>
		                        </tr>
		                        <tr height="20">
		                            <td><b>Birthday : </b></td>
		                        	<td><?=($memberrs[0]["birthdate"]=="0000-00-00")?"-":$dateobj->convertdate($memberrs[0]["birthdate"],'Y-m-d',$sdateformat)?></td>
		                        	<td><b>State : </b></td>
		                        	<td><?=$memberrs[0]["state"]?></td>
		                        	<td>&nbsp;</td>
		                        	<td>&nbsp;</td>
		                        </tr>
		                        <tr height="20">
		                        	<td><b>Nationality : </b></td>
		                        	<td><?=$obj->getIdToText($memberrs[0]["nationality_id"],"dl_nationality","nationality_name","nationality_id")?></td>
		                        	<td><b>Zip Code : </b></td>
		                        	<td><?=$memberrs[0]["zipcode"]?></td>
		                        	<td>&nbsp;</td>
		                        	<td>&nbsp;</td>
		                        </tr>
		                        <tr height="20">
		                        	<td colspan="6" align="center"><hr></td>
		                        </tr>
					</table>
         	</div>
		</td>
	</tr>
	<tr>
		<td width="100%" style="padding-left: 15px"><br/>
		<?
if ($chkpage == 1) { //For print old member history
?> 
		<!-- Old Member History from destiny -->

        	 <div id="omhDiv" style="display:<?=($chkpage==1)?"block":"none"?>;" class="group5" width="100%" align="center" >
        	<? if($rs["rows"]>0||$rsth["rows"]>0){ ?>
    				<b style="color: #ff0000">Old Member History	</b><br>&nbsp;
    				
    				
			<table width="100%" class="generalinfo" cellspacing="0" cellpadding="0"> 
<!--
Separate Line between Member information and Member Sales History
Title to show of Member Sales History
-->
			<tr>
				<td align="center">
				<b>Sales History</b><br />&nbsp;
				<table width="100%" cellpadding="2" cellspacing="0" border="0">
				<tr height="24px"> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Date</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Qty</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Amount</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Balance</b></td> 
				</tr> 
				<?

	$sum = 0;
	$chkColor = 0;
	for ($i = 0; $i < $rs["rows"]; $i++) {

		if ($rs[$i]["catagory_id"] == 11) {
			$sum += $rs[$i]["total"];
		} else {
			$sum -= $rs[$i]["total"];
		}

		if ($sum > 0) {
			$styleColor = "";
			$status = "Can Use";
		} else
			if ($sum == 0) {
				$styleColor = "";
				$status = "Balance!!";
			} else {
				$styleColor = "style=\"color: #ff0000\"";
				$status = "Just Pay";
			}
		$class="bgcolor=\"#d3d3d3\"";
		if ($chkColor % 2 == 0) {
			$class="bgcolor=\"#eaeaea\"";
		}
?>			
						<tr height="20px" <?=$class?>> 
							<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["b_appt_date"],'Y-m-d',$sdateformat)?></td>
							<td class="report"><?=$rs[$i]["branch_name"]?></td>
							<td class="report"><?=$rs[$i]["book_id"]?></td>
							<td class="report"><?=$rs[$i]["product_name"]?></td>
							<td class="report"><?=$rs[$i]["quantity"]?></td>
							<td class="report" align="right"><?=number_format($rs[$i]["total"],2,".",",")?></td>
							<td class="report" align="right"><?=number_format($sum,2,".",",")?></td>
						</tr>
				<?

		$chkColor++;
	}
?>	
						
				</table>
				</td>
			</tr>
			
<!--
Separate Line between Member Sales History and Member Treatment History
Title to show of Member Treatment History
-->
			<tr>
				<td style="padding-top:20px" align="center">
				<hr>
				<br><br><br>
				<b>Treatment History </b><br />&nbsp;
				<table width="100%" cellpadding="2" cellspacing="0" border="0">
				<tr height="24px"> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Date</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Hour</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Package</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Massage 1</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Massage 2</b></td> 
					<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Therapist</b></td> 
				</tr>
						
<?

	$chkColor = 0;
	for ($i = 0; $i < $rsth["rows"]; $i++) {
		$class="bgcolor=\"#d3d3d3\"";
		if ($i % 2 == 0) {
			$class="bgcolor=\"#eaeaea\"";
		}
?>					
						<tr height="20px" <?=$class?>> 
							<td class="report" align="center"><?=$dateobj->convertdate($rsth[$i]["b_appt_date"],'Y-m-d',$sdateformat)?></td>
							<td class="report" align="center"><?=$rsth[$i]["branch_name"]?></td>
							<td class="report" align="center"><?=$rsth[$i]["book_id"]?></td>
							<td class="report" align="center"><?=$rsth[$i]["room_name"]?></td>
							<td class="report" align="center"><?=$rsth[$i]["hour_use"]?></td>
							<?

		if ($rsth[$i]["package_name"]) {
			echo "<td class=\"report\" align=\"left\" style=\"color: #ff0000;\">" . $rsth[$i]["package_name"] . "</td>";
		} else {
			echo "<td class=\"report\">-</td>";
		}
?>
							
							<td class="report"><? if($rsth[$i]["m1"]){ echo $rsth[$i]["m1"]; }else { echo "-"; } ?></td>
							<td class="report"><? if($rsth[$i]["m2"]){ echo $rsth[$i]["m2"]; }else { echo "-"; } ?></td>
							<td class="report"><?=$rsth[$i]["therapist_name"]?></td>
						</tr>
					
					
					<?

		$chkColor++;
	}
?>
			
				</table>
				</td>
			</tr>
			</table><br>
      	 	<? } ?>
			</div>
		<?

} // End old membership history
if ($chkpage == 2) { //For print Sale history
?>
		<!-- Sale History -->

        	 <div id="saleDiv" class="group5" width="100%" align="center">
    				<b>Sale History	</b>
					
					<table width="100%" cellpadding="2" cellspacing="0" border="0" class="generalinfo">
					<tr height="8">
			            <td colspan="8" align="center">&nbsp;</td>
			        </tr>
					<tr height="24px"> 
						<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Date</b></td> 
						<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td> 
						<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td> 
						<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Sales-Receipt ID</b></td> 
						<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Amount</b></td> 
						<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Balance</b></td>
						<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Status</b></td>  
					</tr> 

		        	<? if($sum>0){ ?>
					<tr height="20px" bgcolor="#eaeaea">
						<td class="report" align="center"><?=date($sdateformat,mktime(0, 0, 0, 3, 25, 2009))?></td> 
						<td class="report">Office</td>
						<td class="report">-</td>
						<td class="report">Grand total from Destiny</td>
						<!--<td class="report">1</td>-->
						<td class="report" align="right"><?=number_format($sum,2,".",",")?></td>
						<td class="report" align="right"><?=number_format($sum,2,".",",")?></td>
						<td class="report" align="center">Can use.</td>
					</tr> 
					<? }else{$sum=0;} ?>
					
<?
	$product = array ();
	$chkColor = 1;
	$product["balance"] = $sum;
	for ($i = 0; $i < $rssr["rows"]; $i++) {

		if ($rssr[$i]["tb_name"] == "c_saleproduct") {
			$url = "manage_pdforsale.php?pdsid=" . $rssr[$i]["book_id"];
			$pagename = "managePds" . $rssr[$i]["book_id"];
		} else {
			$url = "manage_booking.php?chkpage=1&bookid=" . $rssr[$i]["book_id"];
			$pagename = "manageBooking" . $rssr[$i]["book_id"];
		}
		$id = $rssr[$i]["bpds_id"];

		$class="bgcolor=\"#d3d3d3\"";
		// amount calculater
	if ($rssr[$i]["pd_id"]==80){
				 	    /*$product["set_sc"] = $rssr[$i]["plus_servicecharge"];
						$product["set_tax"] = $rssr[$i]["plus_vat"];
     				    $product["total"] = $rssr[$i]["amount"];
     				    $sc = $obj->getsSvc($product);
						$vat = $obj->getsTax($product, $sc);
     				    $product["total"] = $rssr[$i]["amount"] + $sc + $vat;
						$product["balance"] -= $product["total"];*/	 
     				    $product["set_sc"] = $rssr[$i]["plus_servicecharge"];
						$product["set_tax"] = $rssr[$i]["plus_vat"];
     				    $product["total"] = $rssr[$i]["amount"];
     				    if($rssr[$i]["plus_servicecharge"]==1){
     				    	$product["set_sc"] = ($rssr[$i]["amount"]*7)/100;	
     				    }else{
     				    	$product["set_sc"]=0;
     				    }
     				    if($rssr[$i]["plus_vat"]==1){
     				    	$product["set_tax"] = (($rssr[$i]["amount"]+$product["set_sc"])*10)/100; 
     				    }else
     				    {
     				    	$product["set_tax"]=0;
     				    }
     				    $product["total"] = $rssr[$i]["amount"] + $product["set_sc"] + $product["set_tax"];
						$product["balance"] -= $product["total"];
						$img_action = "minus.gif";
						$minus = "<span style='color:red'>";
						
						if(number_format($product["balance"],2,".",",")==(0.00)){
 							$product["balance"]=abs(number_format($product["balance"],2,".",","));
 						}
     				//number_format($r_total[$i],2,".",",")
     				}


			// check plus_minus_value before check membership payment type
			// because when customer buy membership they can paid by another payment
			if(!isset($rssr[$i]["pay_id"])){$rssr[$i]["pay_id"]=0;}
			if ($rssr[$i]["plus_minus_value"] == 1) {
				$product["total"] = $rssr[$i]["amount"];
				$product["balance"] += $product["total"];
			} else
				if ($rssr[$i]["pay_id"] == $GLOBALS["global_payid"]) { // membership payment type
					$product["set_sc"] = $rssr[$i]["plus_servicecharge"];
					$product["set_tax"] = $rssr[$i]["plus_vat"];
					$product["total"] = $rssr[$i]["amount"];
					$sc = $obj->getsSvc($product);
					$vat = $obj->getsTax($product, $sc);
					$product["total"] = $rssr[$i]["amount"] + $sc + $vat;
					$product["balance"] -= $product["total"];
				}

			if ($product["balance"] > 0) {
				$status = "Can Use";
				$styleColor = "";
			} else
				if ($product["balance"] == 0) {
					$status = "Balance!!";
					$styleColor = "";
				} else {
					$status = "Just Pay";
					$styleColor = "style=\"color: rgb(255, 0, 0)\"";
				}

			if ($rssr[$i]["plus_minus_value"] == 1 || $rssr[$i]["pd_id"] == 80) {
				$class="bgcolor=\"#d3d3d3\"";
				if($chkColor%2==0){	
						$class="bgcolor=\"#eaeaea\"";
				}
?>	
						<tr height="20px" <?=$class?>> 
								<td class="report" align="center"><?=($rssr[$i]["appt_date"]=="0000-00-00")?"-":$dateobj->convertdate($rssr[$i]["appt_date"],'Y-m-d',$sdateformat)?></td> 
								<td class="report"><?=$rssr[$i]["branch_name"]?></td>
								<td class="report"><?=$id?></td>
								<td class="report"><?=$rssr[$i]["salesreceipt_id"]?></td>
								<td class="report" align="right"><?=number_format($product["total"],2,".",",")?></td>
								<td class="report" align="right"><?=number_format($product["balance"],2,".",",")?></td>
								<td class="report" align="center" <?=$styleColor?>>
							
							<?=$status?>
							<?php if($product["balance"]<=5000){
								echo "<span style='color:red'>"." (Refill)"."</span>";
							}?>
							</td>
						</tr>
		
<?


				$chkColor++;
				// reset product balance
				if ($product["balance"] < 0) {
					//$product["balance"] = 0;
				}

			}
		} // end amount calculater
	}
?>
					</table> 
      	</div>
		<?


//} //End print sale history
if ($chkpage == 3) {
?>
		<!-- Treatment History -->
		
        	 <div id="treatDiv" style="display:<?=($chkpage==3)?"block":"none"?>;" class="group5" width="100%" align="center">
		    		<b>Treatment History</b>
					
					<table width="100%" cellpadding="2" cellspacing="0" border="0" class="generalinfo">
						<tr height="8">
				            <td colspan="8" align="center">&nbsp;</td>
				        </tr> 
						<tr height="24px"> 
							<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Date</b></td> 
							<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Branch</b></td> 
							<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Booking ID</b></td> 
							<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Room</b></td> 
							<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Hour</b></td> 
							<td style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;" align="center"><b>Package</b></td> 
<?


	for ($h = 1; $h <= $msg["maxmsgcnt"]; $h++) {
		echo "<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Massage $h</b></td>";
	}
?>     							
							<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Therapist</b></td> 
						</tr>
<?


	for ($i = 0; $i < $rstrm["rows"]; $i++) {

		$bpdsid = $obj->getIdToText($rstrm[$i]["book_id"], "c_bpds_link", "bpds_id", "tb_id", "tb_name=\"a_bookinginfo\"");
		$id = $bpdsid;
		$hour = substr($rstrm[$i]["hour_name"], 0, 5);
		$package = ($rstrm[$i]["package_id"] == 1) ? "-" : $obj->getIdToText($rstrm[$i]["package_id"], "db_package", "package_name", "package_id");
		
		$class="bgcolor=\"#d3d3d3\"";
		if($i%2==0){	
				$class="bgcolor=\"#eaeaea\"";
		}
?>
						<tr height="20px" <?="$class"?>> 
								<td class="report" align="center"><?=($rstrm[$i]["appt_date"]=="0000-00-00")?"-":$dateobj->convertdate($rstrm[$i]["appt_date"],'Y-m-d',$sdateformat)?></td> 
								<td class="report" align="center"><?=$rstrm[$i]["branch_name"]?></td>
								<td class="report" align="center"><?=$id?></td>
								<td class="report" align="center"><?=$rstrm[$i]["room_name"]?></td>
								<td class="report" align="center"><?=$hour?></td>
								<td class="report" ><?=$package?></td>
<?


		for ($j = 0; $j < $msg["maxmsgcnt"]; $j++) {
			if (isset ($msg[$i][$j])) {
				echo "<td class=\"report\">" . $msg[$i][$j] . "</td>";
			} else {
				echo "<td class=\"report\" align=\"center\">-</td>";
			}
		}
?>
								<td class="report"><?=$rstrm[$i]["therapist_name"]?></td>
						</tr>
<?	} ?>
					</table> 
      	 		</fieldset>
		</div>
		<?


	} // End print treatment story
?>
	  </td>
	</tr>
</table>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>