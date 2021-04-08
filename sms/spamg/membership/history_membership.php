<?php
$root = $_SERVER["DOCUMENT_ROOT"];
include ("$root/include.php");
require_once ("membership.inc.php");
$obj = new membership();

$chkpage = $obj->getParameter("chkpage", 2);
$membercode = $obj->getParameter("memberId");

// find membership information
$memberrs = $obj->getmemberinfo($membercode);
$memberid = $memberrs[0]["member_id"];

$mpic=$memberrs[0]["mpic"];

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
$sum = 0;
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

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Membership History</title>
<link href="/appt/css/style.css" rel="stylesheet" type="text/css" />
<link href="/css/styles.css" rel="stylesheet" type="text/css" />
<script src="../scripts/ajax.js" type="text/javascript"></script>
<script src="/scripts/tooltip/boxover.js" type="text/javascript"></script>
</head>

<body>
<form id="memberhistory" name="memberhistory" method="post" action="">
<a name="TOP"></a>
<table class="main" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="right" class="header" style="padding-right:12px;padding-bottom:0px;border-bottom: 2px solid #ffffff;">
			 <table border="0" height="20" cellpadding="0" cellspacing="0" width="100%">
        	    <tr>
        	    	<td width="68%" >
        	    	<span id="errormsg" class="style1" style='color:#ff0000'><? if($errormsg!=""){ ?><img src="/images/errormsg.png" /><? } ?>&nbsp;&nbsp;
					<b class="errormsg"><?=$errormsg?></b></span><span id="successmsg" style='color:#3875d7'>
					<? if($successmsg!=""){ ?><img src="/images/successmsg.png" /><? } ?>&nbsp;&nbsp;<b class="successmsg"><?=$successmsg?></b></span>&nbsp;&nbsp;        	    	</td>
        	    	<td width="40%"  bgcolor="#d6dff7">
                        <span id="tabs">
                                <ul>
                                	<? if($rs["rows"]>0||$rsth["rows"]>0){ ?>
									<li id="tabone" <?=($chkpage==1)?"class=\"current\"":""?>><a href="javascript:;" onClick="showHideMember('omhDiv');"><span>Old History</span></a></li>
									 <? } ?>
	                                <li id="tabtwo" <?=($chkpage==2)?"class=\"current\"":""?>><a href="javascript:;" onClick="showHideMember('saleDiv');"><span>Sales History</span></a></li>
									<li id="tabthree" <?=($chkpage==3)?"class=\"current\"":""?>><a href="javascript:;" onClick="showHideMember('treatDiv');"><span>Treatment History</span></a></li>

                                </ul>
                      </span>     
       	    	  </td>
               </tr>
        	  </table>
        	  	<input type="hidden" name="memberId" id="memberId" value="<?=$membercode?>">
	        	<input type="hidden" id="pageid" name="pageid" value="<?=$pageid?>"/>
	        	<input type="hidden" id="chkpage" name="chkpage" value="<?=$chkpage?>"/>
		</td>
	</tr>
	<tr>
		<td width="100%" style="padding-left: 5px"><br/>
			
			<div align="left" class="group5">
    			<fieldset>
					<legend><b>Membership Information : </b><b style="color:#ff0000"><?=$membercode?></b> 
					<a href="javascript:;" onClick="printMemberHistory('<?=$membercode?>')" style="color:#ff0000;font-weight:normal;">(print)</a></legend>
					<table class="generalinfo" cellpadding="0" cellspacing="0" style=""> 
						<td valign="top" width="60%">
							   <table border="0" class="membership">
								<tr height="70">
		                        	<td colspan="4"><div style="position: absolute;top: 80px;left: 40px"><img src="<?=$customize_part;?>/images/member/<?=$mpic;?>" width="60px" height="60px"></div><div style="position: absolute;top: 80px;left: 40px"><img src="/images/<?=$theme;?>/header/emp_frame.png"/></div></td>
		                        </tr>
		                        
								<tr height="20">
		                        	<td><b>Member Name : </b></td>
		                        	<td width="150px"><?=$memberrs[0]["fname"]." ".$memberrs[0]["mname"]." ".$memberrs[0]["lname"]?></td>
		                        	<td><b>Category : <b></td>
		                        	<td><?=$obj->getIdToText($memberrs[0]["category_id"],"mb_category","category_name","category_id")?></td>
		                        </tr>
		                        <tr height="20">
		                        	<td><b>Start Date : </b></td>
		                        	<td><?=$dateobj->convertdate($memberrs[0]["joindate"],'Y-m-d',$sdateformat)?></td>
		                        	<td><b>Address : <b></td>
		                        	<td style="white-space: normal"><?=str_replace("[br]","<br>",$memberrs[0]["address"])?></td>
		                        </tr>
		                        <tr height="20">
		                            <td><b>Expried Date : </b></td>
		                        	<td><?=($memberrs[0]["expireddate"]=="0000-00-00")?"Unlimited":$dateobj->convertdate($memberrs[0]["expireddate"],'Y-m-d',$sdateformat)?></td>
		                        	<td><b>City : </b></td>
		                        	<td><?=$memberrs[0]["city"]?></td>
		                        </tr>
		                        <tr height="20">
		                            <td><b>Birthday : </b></td>
		                        	<td><?=($memberrs[0]["birthdate"]=="0000-00-00")?"-":$dateobj->convertdate($memberrs[0]["birthdate"],'Y-m-d',$sdateformat)?></td>
		                        	<td><b>State : </b></td>
		                        	<td><?=$memberrs[0]["state"]?></td>
		                        </tr>
		                        <tr height="20">
		                        	<td><b>Nationality : </b></td>
		                        	<td><?=$obj->getIdToText($memberrs[0]["nationality_id"],"dl_nationality","nationality_name","nationality_id")?></td>
		                        	<td><b>Zip Code : </b></td>
		                        	<td><?=$memberrs[0]["zipcode"]?></td>
		                        </tr>
		                        <tr height="20">
		                        	<td><b>Phone : </b></td>
		                        	<td><?=$memberrs[0]["chk_phone"]?"<img src=\"/images/active.png\" border=\"0\" title=\"yes\" />":"<img src=\"/images/inactive.png\" border=\"0\" title=\"no\" />" ?>&nbsp;<?=$memberrs[0]["phone"]?></td>
		                        	<td><b>Mobile  : </b></td>
		                        	<td><?=$memberrs[0]["chk_mobile"]?"<img src=\"/images/active.png\" border=\"0\" title=\"yes\" />":"<img src=\"/images/inactive.png\" border=\"0\" title=\"no\" />"?>&nbsp;<?=$memberrs[0]["mobile"]?></td>
			        			</tr>
		                        <tr height="20">
		                        	<td><b>YTD : </b></td>
		                        	<td><?=number_format($ytd,2,".",",")?></td>
		                        	<td><b>E-mail : </b></td>
		                        	<td><?=$memberrs[0]["chk_email"]?"<img src=\"/images/active.png\" border=\"0\" title=\"yes\" />":"<img src=\"/images/inactive.png\" border=\"0\" title=\"no\" />"?>&nbsp;<?=$memberrs[0]["email"]?></td>
		                        </tr>
			        			
			        			<? if($chkPageEdit){?>
				        		<tr height="20">
				        			<td><b>LTD  : </b></td>
		                        	<td><?=number_format($ltd,2,".",",")?></td>
			        				<td colspan="2"><input type="button" name="b_mhistory" id="b_mhistory" value="Edit Member Profile"
				        					onClick="window.open('add_membershipinfo.php?id=<?=$memberrs[0]["member_id"]?>','NewMembersWindows','height=650,width=350,resizable=0,scrollbars=1');" />
				        			</td>
				        		</tr>
			        			<?}else{?>
				        		<tr height="20">
				        			<td><b>LTD  : </b></td>
		                        	<td><?=number_format($ltd,2,".",",")?></td>
		                        	<td>&nbsp;</td>
		                        	<td>&nbsp;</td>
				        		</tr>
			        			<?}?>
			        			</table>
			        		</td>
			        		<td valign="top" width="40%">
		                   			<table cellspacing="0" cellpadding="0" width="100%" border="0" class="generalinfo">
		                            	<? if($chkPageEdit){?>
		                                  	<tr>
			                                    <td>Comment:</td>
			                                    <td width="200px"><input style="width: 250px;" name="comment" id="comment" type="text"></td>
			                                    <td><input type="submit" name="save" id="submit" value="Save" /></td>
		                                  	</tr>
		                                <?}?>
		                                	<tr>
		                                		<td colspan="3"><br/>
												<div class="commentinner">
		                                		<table border="0" cellspacing="0" cellpadding="0" width="400px">
	                                  				<tr height="20px" style="background:#a8c2cb;"> 
														<td><b>Agent</b></td> 
														<td><b>Comments</b></td> 
				                                 	</tr>
				                                 	<tr>
				                                    <?

for ($i = 0; $i < $rscomment["rows"]; $i++) {
	$class = "class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";
	if ($i % 2 == 0) {
		$class = "class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";
	}
	list($date,$time) = explode(" ",$rscomment[$i]["l_lu_date"]);
	$commenttime = split(' ', $dateobj->timezonefilter($date,$time,"$sdateformat H:i:s"));
?>
				                                    	<tr height="20px" <?=$class?>> 
				                                 			<td class="report">
				                                 			<?=$commenttime[0]?><br/>
				                                 			<?=$commenttime[1]?><br/>
				                                 			<?=$obj->getIdToText($rscomment[$i]["l_lu_user"],"s_user","u","u_id")?>
				                                 			</td> 
				                                 			<td class="report"><?=str_replace("\n","<br/>",$rscomment[$i]["comments"])?></td>
				                                 		</tr>
				                                 	<? } ?>
				                                 </table>
		                                		</div>
		                                		</td>
		                                	</tr>
		                        	</table>
			        		</td>
			        	</tr>
					</table>
         			</fieldset>
			</div>
		</td>
	</tr>
	<tr>
		<td width="100%" style="padding-left: 5px"><br/><br/>
		
		<!-- Old Member History from destiny -->

        	 <div id="omhDiv" style="display:<?=($chkpage==1)?"block":"none"?>;" class="group5" width="100%" >
        	<? if($rs["rows"]>0||$rsth["rows"]>0){ ?>
    			<fieldset>
    			<a name="MSH"/>
					<legend><b>Old Member History	</b></legend>
					<table class="generalinfo" cellspacing="0" cellpadding="0"> 
<!--
Separate Line between Member information and Member Sales History
Title to show of Member Sales History
-->
			<tr>
				<td align="right"><a href="#MTH" style="font-weight:normal;text-decoration: underline;">Treatment History</a></td>
			</tr>
			<tr>
				<td>
				<fieldset>
				<legend><b>Sales History</b></legend>
				<table width="100%" cellpadding="2" cellspacing="0" border="0">
				<tr height="20px" style="background:#a8c2cb;"> 
					<td><b>Date</b></td> 
					<td><b>Branch</b></td> 
					<td><b>Booking ID</b></td> 
					<td><b>Product</b></td> 
					<td><b>Qty</b></td> 
					<td><b>Amount</b></td> 
					<td><b>Balance</b></td> 
					<td><b>Status</b></td> 
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
				$styleColor = "style=\"color: rgb(255, 0, 0)\"";
				$status = "Just Pay";
			}
		$class = "class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";
		if ($chkColor % 2 == 0) {
			$class = "class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";
		}
?>			
						<tr height="20px" <?=$class?>> 
							<td class="mhistory"><?=$dateobj->convertdate($rs[$i]["b_appt_date"],'Y-m-d',$sdateformat)?></td>
							<td><?=$rs[$i]["branch_name"]?></td>
							<td><?=$rs[$i]["book_id"]?></td>
							<td class="mhistory"><?=$rs[$i]["product_name"]?></td>
							<td><?=$rs[$i]["quantity"]?></td>
							<td align="right"><?=number_format($rs[$i]["total"],2,".",",")?></td>
							<td align="right"><?=number_format($sum,2,".",",")?></td>
							<td align="center" <?=$styleColor?>><?=$status?></td>
						</tr>
				<?

		$chkColor++;
	}
?>	
						
				</table>
				</fieldset>
				</td>
			</tr>
			<tr>
				<td align="right"><br><br><a name="MTH"/><a href="#MSH" style="font-weight:normal;text-decoration: underline;">Sale History</a></td>
			</tr>
<!--
Separate Line between Member Sales History and Member Treatment History
Title to show of Member Treatment History
--><div style="clear:both;"></div>
			<tr>
				<td>
				<fieldset>
				<legend><b>Treatment History </b></legend>
				<table width="100%" cellpadding="2" cellspacing="0" border="0">
				<tr height="20px" style="background:#a8c2cb;"> 
					<td><b>Date</b></td> 
					<td><b>Branch</b></td> 
					<td><b>Booking ID</b></td> 
					<td><b>Room</b></td> 
					<td><b>Hour</b></td> 
					<td><b>Package</b></td> 
					<td><b>Massage 1</b></td> 
					<td><b>Massage 2</b></td> 
					<td><b>Therapist</b></td> 
				</tr>
						
<?

	$chkColor = 0;
	for ($i = 0; $i < $rsth["rows"]; $i++) {

		$trmdetail = "";
		if ($rsth[$i]["strength_type"]) {
			$trmdetail .= "Strength : " . $rsth[$i]["strength_type"];
		}
		if ($rsth[$i]["bath_type"]) {
			if ($trmdetail != "") {
				$trmdetail .= "<br>";
			}
			$trmdetail .= "Bath : " . $rsth[$i]["bath_type"];
		}
		if ($rsth[$i]["facial_type"]) {
			if ($trmdetail != "") {
				$trmdetail .= "<br>";
			}
			$trmdetail .= "Facial : " . $rsth[$i]["facial_type"];
		}
		if ($rsth[$i]["scrub_type"]) {
			if ($trmdetail != "") {
				$trmdetail .= "<br>";
			}
			$trmdetail .= "Scrub : " . $rsth[$i]["scrub_type"];
		}
		if ($rsth[$i]["wrap_type"]) {
			if ($trmdetail != "") {
				$trmdetail .= "<br>";
			}
			$trmdetail .= "Wrap : " . $rsth[$i]["wrap_type"];
		}

		$title = ($trmdetail == "") ? "" : " title=\" header=[Treatment Detail] body=[" . htmlspecialchars($trmdetail) . "]\" style=\"cursor: pointer;\"";

		$class = "class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";
		if (($chkColor % 2) == 0) {
			$class = "class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";
		}
?>					
						<tr height="20px" <?="$class $title"?>> 
							<td class="mhistory"><?=$dateobj->convertdate($rsth[$i]["b_appt_date"],'Y-m-d',$sdateformat)?></td>
							<td><?=$rsth[$i]["branch_name"]?></td>
							<td><?=$rsth[$i]["book_id"]?></td>
							<td><?=$rsth[$i]["room_name"]?></td>
							<td><?=$rsth[$i]["hour_use"]?></td>
							<?

		if ($rsth[$i]["package_name"]) {
			echo "<td align=\"left\" style=\"color: #ff0000;\">" . $rsth[$i]["package_name"] . "</td>";
		} else {
			echo "<td>-</td>";
		}
?>
							
							<td class="mhistory"><? if($rsth[$i]["m1"]){ echo $rsth[$i]["m1"]; }else { echo "-"; } ?></td>
							<td class="mhistory"><? if($rsth[$i]["m2"]){ echo $rsth[$i]["m2"]; }else { echo "-"; } ?></td>
							<td><?=$rsth[$i]["therapist_name"]?></td>
						</tr>
					
					
					<?

		$chkColor++;
	}
?>
			
				</table>
				</fieldset>
				</td>
			</tr>
			<tr>
				<td align="right"><br><br>
				<a href="#MSH" style="font-weight:normal;text-decoration: underline;">Sale History</a> , 
				<a href="#MTH" style="font-weight:normal;text-decoration: underline;">Treatment History</a> , 
				<a href="#TOP" style="font-weight:normal;text-decoration: underline;">Top Page</a></td>
			</tr>	
			</table><br>
      	 		</fieldset>
      	 	<? } ?>
			</div>
		
		<!-- Sale History -->

        	 <div id="saleDiv" style="display:<?=($chkpage==2)?"block":"none"?>;" class="group5" width="100%" >
    			<fieldset>
					<legend><b>Sale History	</b></legend>
					
					<table width="100%" cellpadding="2" cellspacing="0" border="0" class="generalinfo">
					<tr height="20px" style="background:#a8c2cb;"> 
						<td><b>Date</b></td> 
						<td><b>Branch</b></td> 
						<td><b>Booking ID</b></td> 
						<td><b>Sales-Receipt ID</b></td> 
						<td><b>Amount</b></td> 
						<td><b>Balance</b></td> 
						<td><b>Status</b></td> 
					</tr> 

		        	<? if($sum>0){ ?>
					<tr height="20px" class="even">
						<td class="report"><?=date($sdateformat,mktime(0, 0, 0, 3, 25, 2009))?></td> 
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
	$balanceindex = 0;
		
	for ($i = 0; $i < $rssr["rows"]; $i++) {			
			
			// check plus_minus_value before check membership payment type
			// because when customer buy membership they can paid by another payment
			if ($rssr[$i]["plus_minus_value"] == 1) {
				$product["total"] = $rssr[$i]["amount"];
				$product["balance"] += $product["total"];
				$img_action = "plus.gif";
				$minus = "";
				
			} else
				 if ($rssr[$i]["member_takeout"] == 1){
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
			/*
				if ($rssr[$i]["pay_id"] == $GLOBALS["global_payid"]) { // membership payment type
					if(!$rssr[$i]["pay_total"]){
					$product["set_sc"] = $rssr[$i]["plus_servicecharge"];
					$product["set_tax"] = $rssr[$i]["plus_vat"];
					$product["total"] = $rssr[$i]["amount"];
					$sc = $obj->getsSvc($product);
					$vat = $obj->getsTax($product, $sc);
					$product["total"] = $rssr[$i]["amount"] + $sc + $vat;
					$product["balance"] -= $product["total"];
					}else{
						$product["total"] = $rssr[$i]["pay_total"];
						$product["balance"] -= $product["total"];
					}
				}
			*/
			
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

			//if ($rssr[$i]["plus_minus_value"] == 1 || $rssr[$i]["pay_id"] == $GLOBALS["global_payid"]) {
			  if ($rssr[$i]["plus_minus_value"] == 1 || $rssr[$i]["member_takeout"] == 1) {
				$class = "class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";
				if ($chkColor % 2 == 0) {
					$class = "class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";
				}
				if($product["balance"] <= 5000){
					$class = "class=\"warn\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#ffcccc'\"";
				}
?>					
				<?

				//for member pay many package
				//	if($i==0){
				//		$chk = $rssr[0]["salesreceipt_id"];
				//	}else if($chk!=$rssr[$i]["salesreceipt_id"]){
				//		$chk = $rssr[$i]["salesreceipt_id"];
				//	}else if($rssr[$i]["plus_minus_value"] ==0 && $rssr[$i]["plus_minus_value"] == 0){
				//		$product["balance"]=$product["balance"]+$product["total"];
				//		$product["total"]=0;
				//	}
				
		if ($rssr[$i]["tb_name"] == "c_saleproduct") {
			$urlb = "manage_pdforsale.php?pdsid=" . $rssr[$i]["book_id"];
			$pagenameb = "managePds" . $rssr[$i]["book_id"];
		} else {
			$urlb = "manage_booking.php?chkpage=1&bookid=" . $rssr[$i]["book_id"];
			$pagenameb = "manageBooking" . $rssr[$i]["book_id"];
		}
				
				?>
						<tr height="20px" <?=$class?> <?php if($product["balance"]<=5000){ echo "class='warn' onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#ffcccc'\"";} ?>> 
							<td class="eport"><?=($rssr[$i]["appt_date"]=="0000-00-00")?"-":$dateobj->convertdate($rssr[$i]["appt_date"],'Y-m-d',$sdateformat)?></td> 
							<td class="report"><?=$rssr[$i]["branch_name"]?></td>
							<td class="report"><?
							echo "<a href='javascript:;;' onClick=\"window.open('/appt/$urlb','$pagenameb','resizable=0,scrollbars=1')\" class=\"menu\">".$rssr[$i]["bpds_id"]."</a>";
							?></td>
							<td class="report"><?=$rssr[$i]["salesreceipt_id"]?></td>
							<!--<td class="report"><?=$rssr[$i]["quantity"]?></td>-->
							<td class="report" align="right">
							<img src="../../images/<?php echo $img_action; ?>"  align="left">
							
							
							<?php echo $minus.number_format($product["total"],2,".",","); ?>
							
							
							</td>
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
?>
					</table> 
      	 		</fieldset>
		</div>
		
		<!-- Treatment History -->
		
        	 <div id="treatDiv" style="display:<?=($chkpage==3)?"block":"none"?>;" class="group5" width="100%" >
		    	<fieldset>
					<legend><b>Treatment History</b></legend>
		
					<table class="generalinfo" cellpadding="0" cellspacing="0" > 
						<tr height="20px" style="background:#a8c2cb;"> 
							<td align="center"><b>Date</b></td> 
							<td align="center"><b>Branch</b></td> 
							<td align="center"><b>Booking ID</b></td> 
							<td align="center"><b>Room</b></td> 
							<td align="center"><b>Hour</b></td> 
							<td align="center"><b>Package</b></td> 
<?

	for ($h = 1; $h <= $msg["maxmsgcnt"]; $h++) {
		echo "<td align=\"center\"><b>Massage $h</b></td>";
	}
?>     							
							<td align="center"><b>Therapist</b></td> 
						</tr>
<?

	for ($i = 0; $i < $rstrm["rows"]; $i++) {

		$url = "manage_booking.php?chkpage=1&bookid=" . $rstrm[$i]["book_id"];
		$pagename = "manageBooking" . $rstrm[$i]["book_id"];
		$bpdsid = $obj->getIdToText($rstrm[$i]["book_id"], "c_bpds_link", "bpds_id", "tb_id", "tb_name=\"a_bookinginfo\"");
		$id = "<a href='javascript:;;' onClick=\"window.open('/appt/$url','$pagename','resizable=0,scrollbars=1')\" class=\"menu\">$bpdsid</a>";

		$class = "class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";
		if ($i % 2 == 0) {
			$class = "class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";
		}
		$hour = substr($rstrm[$i]["hour_name"], 0, 5);
		$package = ($rstrm[$i]["package_id"] == 1) ? "-" : $obj->getIdToText($rstrm[$i]["package_id"], "db_package", "package_name", "package_id");

		$trmdetail = "";
		if ($rstrm[$i]["strength_id"] > 1) {
			$trmdetail .= "Strength : " . $obj->getIdToText($rstrm[$i]["strength_id"], "l_strength", "strength_type", "strength_id");
		}
		if ($rstrm[$i]["scrub_id"] > 1) {
			if ($trmdetail != "") {
				$trmdetail .= "<br>";
			}
			$trmdetail .= "Scrub : " . $obj->getIdToText($rstrm[$i]["scrub_id"], "db_trm", "trm_name", "trm_id");
		}
		if ($rstrm[$i]["wrap_id"] > 1) {
			if ($trmdetail != "") {
				$trmdetail .= "<br>";
			}
			$trmdetail .= "Wrap : " . $obj->getIdToText($rstrm[$i]["wrap_id"], "db_trm", "trm_name", "trm_id");
		}
		if ($rstrm[$i]["bath_id"] > 1) {
			if ($trmdetail != "") {
				$trmdetail .= "<br>";
			}
			$trmdetail .= "Bath : " . $obj->getIdToText($rstrm[$i]["bath_id"], "db_trm", "trm_name", "trm_id");
		}
		if ($rstrm[$i]["facial_id"] > 1) {
			if ($trmdetail != "") {
				$trmdetail .= "<br>";
			}
			$trmdetail .= "Facial : " . $obj->getIdToText($rstrm[$i]["facial_id"], "db_trm", "trm_name", "trm_id");
		}

		$title = "";
		if ($trmdetail != "") {
			$title = " title=\" header=[Treatment Detail] body=[" . htmlspecialchars($trmdetail) . "]\" style=\"cursor: pointer;\"";
		}
?>
						<tr height="20px" <?="$class $title"?>> 
								<td class="report"><?=($rstrm[$i]["appt_date"]=="0000-00-00")?"-":$dateobj->convertdate($rstrm[$i]["appt_date"],'Y-m-d',$sdateformat)?></td> 
								<td class="report"><?=$rstrm[$i]["branch_name"]?></td>
								<td class="report"><?=$id?></td>
								<td class="report"><?=$rstrm[$i]["room_name"]?></td>
								<td class="report"><?=$hour?></td>
								<td class="report"><?=$package?></td>
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
	  </td>
	</tr>
</table>
</form>
</body>
</html>
