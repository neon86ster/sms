<?
include("../../include.php");
require_once("formdb.inc.php");
require_once("secure.inc.php");
require_once("destiny.inc.php");
$obj = new formdb(); 
$objOmh = new destiny();
$scObj = new secure();

$chkpage=$obj->getParameter("chkpage",2);
$comment=$obj->getParameter("comment","");
$memberId = $obj->getParameter("memberId");
$msg="";

////////////////////////// Convert date & time/////////////////////////
$chksql = "select long_date,short_date from a_company_info";
$chkrs =$obj->getResult($chksql);
$sdateformat = $obj->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
$dateobj = new convertdate();
$date = $obj->getParameter("date");

////////////////////////// End convert date & time /////////////////////

/////////////////////// Find member data //////////////////
$sql = "select * from m_membership where member_code=$memberId";
$rsMember = $obj->getResult($sql);
///////////////////// End find member data ////////////////

//////////////////// Find comment of each member //////////////////////////
$sql = "select comments,l_lu_user,l_lu_date from ma_comment where member_id=".$rsMember[0]["member_id"]." order by l_lu_date asc";
$rsComment = $obj->getResult($sql);

$chkComment=true;
for($i=0;$i<$rsComment["rows"];$i++){
	if($rsComment[$i]["comments"]==$comment){
		//echo "Add false.<br>";
		$chkComment=false;
	}else{
		//echo "Add true.<br>";
	}
}
$save = $obj->getParameter("save");
if($save){
	if($comment!="" && $chkComment){
		$id=$obj->saveMemberComment($comment,$rsMember[0]["member_id"]);
		if($id){
			$msg="Update comment complete.";
		}else{
			$msg="Can't update comment. Please try again.";
		}
		//echo "Save Comment.<br>$msg<br>".$comment."<br>";	
	}else{
		$msg="Don't have comment for update.";
	}
}
///////////////////////// End find comment ////////////////////////////////

/////////////////////// Find booking data of member for treatment history //////////////////
$sql = "select book_id,b_branch_id,b_appt_date,tax_id,servicescharge from a_bookinginfo where a_member_code=$memberId order by b_appt_date asc";
$rsBook = $obj->getResult($sql);
///////////////////// End find member data ////////////////

/////////////////////// Find data buyer of member //////////////////

$sql1 = "select a_bookinginfo.b_branch_id as branch_id,a_bookinginfo.b_appt_date as date,a_bookinginfo.tax_id as tax_id,a_bookinginfo.servicescharge as servicescharge,c_salesreceipt.salesreceipt_id,c_salesreceipt.book_id,c_salesreceipt.pay_id,c_salesreceipt.pds_id " .
			"from a_bookinginfo,c_salesreceipt " .
			"where c_salesreceipt.book_id=a_bookinginfo.book_id " .
			"and c_salesreceipt.paid_confirm =1 " .
			"and a_bookinginfo.b_set_cancel=0 ".
			"and a_bookinginfo.a_member_code=$memberId ";
$sql2 = "select c_saleproduct.branch_id as branch_id,c_saleproduct.pds_date as date,c_saleproduct.tax_id as tax_id,c_saleproduct.servicescharge as servicescharge,c_salesreceipt.salesreceipt_id,c_salesreceipt.book_id,c_salesreceipt.pay_id,c_salesreceipt.pds_id " .
			"from c_saleproduct,c_salesreceipt " .
			"where c_salesreceipt.pds_id=c_saleproduct.pds_id " .
			"and c_salesreceipt.paid_confirm =1 " .
			"and c_saleproduct.set_cancel=0 ".
			"and c_saleproduct.a_member_code=$memberId";
$sql = "($sql1) union ($sql2) order by date,salesreceipt_id";
//echo "<br>$sql<br>";
$rsSr = $obj->getResult($sql);
//echo "<br>$sql<br>Amount Sr : ".$rsSr["rows"];


///////////////////// End find member data ////////////////

//////////////////// Find comment of each member //////////////////////////
$sql = "select comments,l_lu_user,l_lu_date from ma_comment where member_id=".$rsMember[0]["member_id"]." order by l_lu_date desc";
$rsComment = $obj->getResult($sql);
///////////////////////// End find comment ////////////////////////////////

///////////////// Find old member history //////////////////
$rs = $objOmh->get_memberhistory($memberId);
$rsth = $objOmh->get_membertreatment($memberId);
///////////////// End find old member history //////////////

//////////////// Initial variable for user balance account /////////////////////////
$balance = 0;
$amount = 0;
$tax = 0;
$sc=0;
$status="Can use";
///////////// End initial variable for user balance account ////////////////////////

////////////// For check user permission to edit this page ////////////////
if($scObj->isPageEdit("/membership/",true) || $scObj->isPageEdit("appt",true)){
	//echo "Can Access Edit";
	$chkPageEdit=true;
}else{
	//echo "Can't Access Edit";
	$chkPageEdit=false;
}
/////////// End for check user permission to edit this page ////////////////
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Member History</title>
<link href="/appt/css/style.css" rel="stylesheet" type="text/css" />
<link href="/css/style.css" rel="stylesheet" type="text/css" />
<script src="../giftinfo/scripts/ajax.js" type="text/javascript"></script>
<script src="../giftinfo/scripts/component.js" type="text/javascript"></script>
<script src="../giftinfo/scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="../giftinfo/scripts/datechooser/datechooser.js" type="text/javascript"></script>
<script type="text/javascript" src="../giftinfo/scripts/tooltip/boxover.js"></script>
<link rel="stylesheet" type="text/css" href="../giftinfo/scripts/datechooser/datechooser.css">
</head>

<body>
<a name="TOP"></a>
<table class="main" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="right" class="header" style="padding-right:12px;padding-bottom:0px;border-bottom: 2px solid #ffffff;">
			 <table border="0" height="20" cellpadding="0" cellspacing="0">
        	    <tr>
        	    	<td>
                        <span id="tabs">
                                <ul>
	                                <!-- CSS Tabs -->
        	    					<? if($rs["rows"]>0||$rsth["rows"]>0){ ?>
									<li id="tabone" <?=($chkpage==1)?"class=\"current\"":""?>><a href="javascript:;" onClick="showHide('omhDiv');"><span>Old History</span></a></li>
									 <? } ?>
									<li id="tabtwo" <?=($chkpage==2)?"class=\"current\"":""?>><a href="javascript:;" onClick="showHide('saleDiv');"><span>Sales History</span></a></li>
									<li id="tabthree" <?=($chkpage==3)?"class=\"current\"":""?>><a href="javascript:;" onClick="showHide('treatDiv');"><span>Treatment History</span></a></li>

                                </ul>
                        </span>     
        	    	</td>
                  </tr>
        	  </table>
	        	<input type="hidden" id="chkpage" name="chkpage" value="<?=$chkpage?>"/>
        		<input type="hidden" id="memberId" name="memberId" value="<?=$memberId?>" />
		</td>
	</tr>
	<tr>
    	<td class="content" width="100%" style="padding-top: 0px">
    		<div align="left">
    			<fieldset>
					<legend><b>Member Information : <?=$memberId?></b></legend>
						<table border="0" cellpadding="2" cellspacing="2">
						<tr>
							<td valign="top" width="60%">
							   <table border="0" cellpadding="2" cellspacing="2">
								<tr>
		                        	<td align="left" width="80px valign="top">Member Name : </td>
		                        	<td width="150px" valign="top" align="left"><? echo $rsMember[0]["fname"]." ".$rsMember[0]["mname"]." ".$rsMember[0]["lname"];?></td>
		                        	<td align="left" width="60px valign="top">E-mail : </td>
		                        	<td align="left" valign="top"><?=$rsMember[0]["email"]?></td>
		                        </tr>
		                        <tr>
		                        	<td align="left" valign="top">Category : </td>
		                        	<td align="left" valign="top"><?=$obj->getIdToText($rsMember[0]["category_id"],"mb_category","category_name","category_id")?></td>
		                        	<td align="left" valign="top">Address : </td>
		                        	<td width="150px" align="left" valign="top"><?=$rsMember[0]["address"]?></td>
		                        </tr>
		                        <tr>
		                            <td align="left" valign="top">Start Date : </td>
		                        	<td align="left" valign="top"><?=$dateobj->convertdate($rsMember[0]["joindate"],'Y-m-d',$sdateformat)?></td>
		                        	<td align="left" valign="top">City : </td>
		                        	<td align="left" valign="top"><?=$rsMember[0]["city"]?></td>
		                        </tr>
		                        <tr>
		                            <td align="left" valign="top">Birthday : </td>
		                        	<td align="left" valign="top"><?=$dateobj->convertdate($rsMember[0]["birthdate"],'Y-m-d',$sdateformat)?></td>
		                        	<td align="left" valign="top">State : </td>
		                        	<td align="left" valign="top"><?=$rsMember[0]["state"]?></td>
		                        </tr>
		                        <tr>
		                        	<td align="left" valign="top">Nationality : </td>
		                        	<td align="left" valign="top"><?=$obj->getIdToText($rsMember[0]["nationality_id"],"dl_nationality","nationality_name","nationality_id")?></td>
		                        	<td align="left" valign="top">Zip Code : </td>
		                        	<td align="left" valign="top"><?=$rsMember[0]["zipcode"]?></td>
		                        </tr>
		                        <tr>
		                        	<td align="left" valign="top">Phone : </td>
		                        	<td align="left" valign="top"><?=$rsMember[0]["phone"]?></td>
		                        	<td align="left" valign="top">Mobile  : </td>
		                        	<td align="left" valign="top"><?=$rsMember[0]["mobile"]?></td>
			        			</tr>
			        			
			        			<? if($chkPageEdit){?>
				        			<tr>
				        			<td>&nbsp;</td>
			                        	<td>&nbsp;</td>
				        				<td colspan="2"><input type="button" name="b_mhistory" id="b_mhistory" value="Edit Member Profile" class="button" 
				        					onClick="window.open('add_membershipinfo.php?id=<?=$rsMember[0]["member_id"]?>','NewMembersWindows','height=650,width=350,resizable=0,scrollbars=1');" />
				        				</td>
				        			</tr>
			        			<?}?>
			        			</table>
			        		</td>
			        		<td valign="top" width="40%">
			        		<div class="group3">
		                        <table border="0" cellpadding="0" cellspacing="0" width="500px">
		                          <tr>
		                            <td valign="bottom" >
		                            	<div class="group4">
		                            	<? if($chkPageEdit){?>
		                            	<form method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
		                            	<input type="hidden" name="memberId" id="memberId" value="<?=$memberId?>">
		                                <table cellspacing="0" cellpadding="0" width="100%" border="0" class="cusinfo">
		                                  <tr>
		                                    <td style="vertical-align:middle">Comment:</td>
		                                    <td width="200px"><input name="comment" class="text" id="comment" type="text"></td>
		                                    <td><input type="submit" name="save" id="submit" value="Save" class="button" /></td>
		                                  </tr>
		                                </table>
		                                </form>
		                                <?}?>
		                                <div class="comment"  style="height:100px;">
		                                  <table border="0" cellspacing="0" cellpadding="0" class="comment" style="width:100%">
		                                    <tr>
		                                      <td height="20" width="50" class="mainthead">Agent</td>
		                                      <td height="20" class="mainthead">Comments</td>
		                                    </tr>
		                                    <? for($i=0;$i<$rsComment["rows"];$i++){
		                                    		$trclass = ($i%2==0)?"content_list":"content_list1";
		                                    		$commenttime=split(' ',$rsComment[$i]["l_lu_date"])
		                                    ?>
		                                    <tr class='<?=$trclass?>'>
		                                      <td style="vertical-align:top;"><?=$dateobj->convertdate($commenttime[0],'Y-m-d',$sdateformat)?>
		                                          <br/>
		                                          <?=$dateobj->converttime($commenttime[1],'H:i:s','H:i:s')?>
		                                          <br/>
		                                          <?=$obj->getIdToText($rsComment[$i]["l_lu_user"],"s_user","u","u_id")?></td>
		                                      <td style="vertical-align:top;width:300px;"><?=str_replace("\n","<br/>",$rsComment[$i]["comments"])?></td>
		                                    </tr>
		                                    <? }?>
		                                  </table>
		                                </div>
		                                <br/>
		                              </div>
								  </tr>
		                        </table>
		                      </div>
			        		</td>
			        	</tr>
			        	</table>
         			</fieldset>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="content" width="100%" style="padding-top: 0px">
<!-- ######################## Old Member History from destiny ####################################### -->
	  <div id="omhDiv" style="display:none;">
	   <div class="group5" width="100%" >
    	<fieldset>
    	<a name="MSH"></a>
			<legend><b>Old Member History</b></legend>
			<table class="main_table_list" cellspacing="0" cellpadding="0"> 
<!--
Separate Line between Member information and Member Sales History
Title to show of Member Sales History
-->
			<tr>
				<br><br>
				<td align="right"><a href="#MTH">Treatment History</a></td>
			</tr>
			<tr>
				<td>
				<fieldset>
				<legend><b>Sales History</b></legend>
				<table width="100%" cellpadding="2" cellspacing="0" border="0" bordercolor="#FFFFFF">
				
				<tr class="txtheader"> 
					<td class="mainthead">Date</td> 
					<td class="mainthead">Branch</td>
					<td class="mainthead">Booking ID</td>
					<td class="mainthead">Product</td>
					<td class="mainthead">Qty</td>
					<td class="mainthead">Amount</td>
					<td class="mainthead">Balance</td>
					<td class="mainthead">Status</td>
				</tr> 
				<?
				$sum=0;
				$chkColor=0;	
				for($i=0; $i<$rs["rows"]; $i++) {
					
					if($rs[$i]["catagory_id"]==11) {
						$sum += $rs[$i]["total"];
					}
					else {
						$sum -= $rs[$i]["total"];
					}
					
					
					if($sum > 0) {
						$styleColor="";
						$status = "<font color='green'>Can Use</font>";
						$detail_color = "#CFDFC4";
					}
					else if($sum == 0) {
						$styleColor="";
						$status = "Balance!!";
						$detail_color = "#CFDFC4";
					}
					else {
						$styleColor="style=\"color: rgb(255, 0, 0)\"";
						$status="Just Pay";
					}
					if(($chkColor%2)==0){
					   	echo "<tr class=\"content_list\" onmouseover=\"high(this)\" onmouseout=\"low(this)\">";
					}else{
						echo "<tr class=\"content_list1\" onmouseover=\"high(this)\" onmouseout=\"low(this)\">";
					}	
					?>			
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
					<td align="right">
					<a name="MTH"></a>
					<br><br><br>
						<a href="#MSH">Sale History</a>	
					</td>
				</tr>
					<!--
					Separate Line between Member Sales History and Member Treatment History
					Title to show of Member Treatment History
					-->
				<tr>
						<td>
						<fieldset>
						<legend><b>Treatment History </b></legend>
						<table width="100%" cellpadding="2" cellspacing="0" border="0" bordercolor="#FFFFFF">
						<tr class="txtheader">
							<td class="mainthead">Date</td> 
							<td class="mainthead">Branch</td>
							<td class="mainthead">Booking ID</td>
							<td class="mainthead">Room</td>
							<td class="mainthead">Hour</td>
							<td class="mainthead">Package</td>
							<td class="mainthead">Massage 1</td>
							<td class="mainthead">Massage 2</td>		
							<td class="mainthead">Therapist</td>
						</tr>
						
					<?
					$chkColor=0;
					for($i=0; $i<$rsth["rows"]; $i++) {
						if(($chkColor%2)==0){
					       	echo "<tr class=\"content_list\" onmouseover=\"high(this)\" onmouseout=\"low(this)\">";
						}else{
							echo "<tr class=\"content_list1\" onmouseover=\"high(this)\" onmouseout=\"low(this)\">";
						}	
					?>
					
							<td class="mhistory"><?=$dateobj->convertdate($rsth[$i]["b_appt_date"],'Y-m-d',$sdateformat)?></td>
							<td><?=$rsth[$i]["branch_name"]?></td>
							<td><?=$rsth[$i]["book_id"]?></td>
							<td><?=$rsth[$i]["room_name"]?></td>
							<td><?=$rsth[$i]["hour_use"]?></td>
							<? 
							if($rsth[$i]["package_name"]){
								$showPackageDetail="";
								if($rsth[$i]["strength_type"]){
									if($showPackageDetail==""){
										$showPackageDetail.="Strength : ".$rsth[$i]["strength_type"];
									}else{
										$showPackageDetail.="<br>Strength : ".$rsth[$i]["strength_type"];
									}	
								}
								if($rsth[$i]["facial_type"]){
									if($showPackageDetail==""){
										$showPackageDetail.="Facial : ".$rsth[$i]["facial_type"];
									}else{
										$showPackageDetail.="<br>Facial : ".$rsth[$i]["facial_type"];
									}	
								}
								if($rsth[$i]["wrap_type"]){
									if($showPackageDetail==""){
										$showPackageDetail.="Warp : ".$rsth[$i]["wrap_type"];
									}else{
										$showPackageDetail.="<br>Warp : ".$rsth[$i]["wrap_type"];
									}	
								}
								if($rsth[$i]["scrub_type"]){
									if($showPackageDetail==""){
										$showPackageDetail.="Scrub : ".$rsth[$i]["scrub_type"];
									}else{
										$showPackageDetail.="<br>Scrub : ".$rsth[$i]["scrub_type"];
									}	
								}
								if($rsth[$i]["bath_type"]){
									if($showPackageDetail==""){
										$showPackageDetail.="Bath : ".$rsth[$i]["bath_type"];
									}else{
										$showPackageDetail.="<br>Bath : ".$rsth[$i]["bath_type"];
									}	
								}
								 
								echo "<td align=\"left\" title=\" header=[Package Detail] body=[".$obj->encodeText($showPackageDetail)."]\" style=\"color: #ff0000; cursor: pointer;\">".$rsth[$i]["package_name"]."</td>";
							}else{
								echo "<td>-</td>";
							}?>
							
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
				<a href="#MSH">Sale History</a> 
				<a href="#MTH">Treatment History</a> 
				<a href="#TOP">Top Page</a></td>
			</tr>	
			</table> 
			</fieldset>
		</div>
		</div>
<!-- ######################## Sale History ####################################### -->
        	<div id="saleDiv" style="display:block;">
        	 <div class="group5" width="100%" >
    			<fieldset>
					<legend><b>Sale History	</b></legend>
			<table class="main_table_list" cellspacing="0" cellpadding="0"> 
 
			<tr class="txtheader"> 
				<td class="mainthead">Date</td> 
				<td class="mainthead">Branch</td>
				<td class="mainthead">Booking ID</td>
				<td class="mainthead">Product</td>
				<td class="mainthead">Qty</td>
				<td class="mainthead">Amount</td>
				<td class="mainthead">Balance</td>
				<td class="mainthead">Status</td>
			</tr> 
			
        	<? if($sum>0){ ?>
			<tr class="content_list" onmouseover="high(this)" onmouseout="low(this)">
				<td><?=date($sdateformat,mktime(0, 0, 0, 3, 25, 2009))?></td> 
				<td>Office</td>
				<td>-</td>
				<td>Grand total from Destiny</td>
				<td>1</td>
				<td align="right"><?=number_format($sum,2,".",",")?></td>
				<td align="right"><?=number_format($sum,2,".",",")?></td>
				<td align="center">Can use.</td>
			</tr> 
			<? }else{$sum=0;} ?>
			<?$balance = $sum;
			$chkColor=0;
			//$obj->setdebugStatus(true);
			for($i=0;$i<$rsSr["rows"];$i++){
				$branchName = $obj->getIdToText($rsSr[$i]["branch_id"],"bl_branchinfo","branch_name","branch_id");
				if($rsSr[$i]["book_id"]!=0){
					//echo "<br>Set Book";
					$bpdsId = $obj->getIdToText($rsSr[$i]["book_id"],"c_bpds_link","bpds_id","tb_id","`tb_name`='a_bookinginfo'");
					$link = "manage_booking.php?chkpage=1&bookid=".$rsSr[$i]["book_id"];
				}else{
					//echo "<br>Set product";
					$bpdsId = $obj->getIdToText($rsSr[$i]["pds_id"],"c_bpds_link","bpds_id","tb_id","`tb_name`='c_saleproduct'");
					$link = "manage_pdforsale.php?pdsid=".$rsSr[$i]["pds_id"];
				}
				
				$sql = "select * from c_srdetail where salesreceipt_id=".$rsSr[$i]["salesreceipt_id"]." order by srdetail_id asc";
				$rsSrd = $obj->getResult($sql);
				//echo "<br>$sql";
				for($k=0;$k<$rsSrd["rows"];$k++){
					$sql = "select * from cl_product where pd_id=".$rsSrd[$k]["pd_id"];
					$rsPd = $obj->getResult($sql);
					//$pd_category_id=$obj->getIdToText($rsSrd[$k]["pd_id"],"cl_product","pd_category_id","pd_id");
					if($obj->getIdToText($rsPd[0]["pd_category_id"],"cl_product_category","pos_neg_value","pd_category_id")){
						$servicescharge=0;
						$taxpercent=0;
						if($rsSrd[0]["set_sc"]){
							$servicescharge=$rsSr[$i]["servicescharge"];
						}
						if($rsSrd[0]["set_tax"]){
							$taxpercent = $obj->getIdToText($rsSr[$i]["tax_id"],"l_tax","tax_percent","tax_id");
						}
						
						
						if($obj->getIdToText($rsPd[0]["pd_category_id"],"cl_product_category","plus_minus_value","pd_category_id")){
							//echo "<br>11 ".$rsPd[0]["pd_name"];
							$balance+=$rsSrd[$k]["qty"]*$rsSrd[$k]["unit_price"];
							$amount=$rsSrd[$k]["qty"]*$rsSrd[$k]["unit_price"];
						}else if($rsSr[$i]["pay_id"]==11){
							$amount=$rsSrd[$k]["qty"]*$rsSrd[$k]["unit_price"];
							$sc = $amount*($servicescharge/100);
							$tax = ($amount+$sc)*((float)$taxpercent/100);
							//echo "<br>Tax percent : ".$tax."<br>ServicesCharge : ".$sc;
							$amount = $amount+$tax+$sc;
							$balance-=$amount;
						}
						
						if($balance>0){
							$styleColor="";
							$status="Can use.";
						}else{
							$styleColor="style=\"color: rgb(255, 0, 0)\"";
							$status="Just Pay";
						}
						//echo $sql."<br>";
						if($rsSr[$i]["pay_id"]==11 || $obj->getIdToText($rsPd[0]["pd_category_id"],"cl_product_category","plus_minus_value","pd_category_id")){
							if(($chkColor%2)==1){
					        	echo "<tr class=\"content_list\" onmouseover=\"high(this)\" onmouseout=\"low(this)\">";
							}else{
								echo "<tr class=\"content_list1\" onmouseover=\"high(this)\" onmouseout=\"low(this)\">";
							}	 
							echo "<td>".$dateobj->convertdate($rsSr[$i]["date"],'Y-m-d',$sdateformat)."</td>
								<td>$branchName</td>
								<td><a href=\"javascript:;\" onClick=\"window.open('../$link','bookingWindow".$rsBook[$i]["book_id"]."',
								'height=700,width=1020,resizable=0,scrollbars=1');\" >
	                    			 ".$bpdsId."</a></td>
								<td>".$rsPd[0]["pd_name"]."</td>
								<td>".$rsSrd[$k]["qty"]."</td>
								<td align=\"right\">".number_format($amount,2,".",",")."</td>
								<td align=\"right\">".number_format($balance,2,".",",")."</td>
								<td  align=\"center\" $styleColor>$status</td>
								</tr> ";
								$chkColor++;
						}
					}
					if($balance<0){
						$balance=0;
					}
			}
		}
		?>
			</table> 
      	 </fieldset>
      	 </div>
		</div>
	<!-- ######################## Treatment History ####################################### -->
	  <div id="treatDiv" style="display:none;">
	   <div class="group5" width="100%" >
    	<fieldset>
			<legend><b>Treatment History</b></legend>
			<table class="main_table_list" cellspacing="0" cellpadding="0"> 

			<?
			$txtMainheader="<tr class=\"txtheader\"> 
				<td class=\"mainthead\">Date</td> 
				<td class=\"mainthead\">Branch</td>
				<td class=\"mainthead\">Booking ID</td>
				<td class=\"mainthead\">Room</td>
				<td class=\"mainthead\">Hour</td>
				<td class=\"mainthead\">Package</td>";
				
			$maxMsg=0;
			$chkId=false;
			for($i=0;$i<$rsBook["rows"];$i++){
				$sql = "select * from d_indivi_info where book_id=".$rsBook[$i]["book_id"]." AND member_use=1 order by book_id asc";
				$rsIndi = $obj->getResult($sql);
				for($j=0;$j<$rsIndi["rows"];$j++){
					$sql = "select massage_id from da_mult_msg where indivi_id=".$rsIndi[$j]["indivi_id"];
					$rsMsg = $obj->getResult($sql);
					if($maxMsg<$rsMsg["rows"]){
						$maxMsg=$rsMsg["rows"];
						if($rsMsg[0]["massage_id"]>1){
							$chkId=true;
						}
						//echo $rsMsg["rows"]."<br>".$rsMsg[0]["massage_id"]."<br>";	
					}
				}
			}
			for($i=1;$i<=$maxMsg;$i++){
				if($chkId){
					$txtMainheader.="<td class=\"mainthead\">Massage $i</td>";
				}
			}
			$txtMainheader.="<td class=\"mainthead\">Therapist</td>
							</tr> ";
			echo $txtMainheader;
			$chkColor=0;
			for($i=0;$i<$rsBook["rows"];$i++){
				$sql = "select branch_name from bl_branchinfo where branch_id=".$rsBook[$i]["b_branch_id"];
				$rsBranch = $obj->getResult($sql);
				$sql = "select * from d_indivi_info where book_id=".$rsBook[$i]["book_id"]." AND member_use=1 order by book_id asc";
				//echo $sql."<br>";
				$rsIndi = $obj->getResult($sql);
				
				for($j=0;$j<$rsIndi["rows"];$j++){
					$sql = "select room_name from bl_room where room_id=".$rsIndi[$j]["room_id"];
					$rsRoom = $obj->getResult($sql);
					
					$sql = "select * from da_mult_th where book_id=".$rsBook[$i]["book_id"]." AND indivi_id=".$rsIndi[$j]["indivi_id"];
					$rsMult_th = $obj->getResult($sql);
					$sql = "select massage_id from da_mult_msg where indivi_id=".$rsIndi[$j]["indivi_id"];
					$rsMsg = $obj->getResult($sql);
					
					$sql = "select hour_id from da_mult_th where indivi_id=".$rsIndi[$j]["indivi_id"]." order by hour_id desc";
					$rsHour = $obj->getResult($sql);
					
					$sql = "select package_name from db_package where package_id=".$rsIndi[$j]["package_id"];
					$rsPk = $obj->getResult($sql);
					//echo $sql."<br>";
					
					$showPackageDetail ="";
					if($rsIndi[$j]["strength_id"]!=1){
						if($showPackageDetail==""){
							$showPackageDetail.="Strength : ".$obj->getIdToText($rsIndi[$j]["strength_id"],"l_strength","strength_type","strength_id");
						}else{
							$showPackageDetail.="<br>Strength : ".$obj->getIdToText($rsIndi[$j]["strength_id"],"l_strength","strength_type","strength_id");
						}
						
					}
					if($rsIndi[$j]["scrub_id"]!=1){
						if($showPackageDetail==""){
							$showPackageDetail.="Scrub : ".$obj->getIdToText($rsIndi[$j]["scrub_id"],"db_trm","trm_name","trm_id");
						}else{
							$showPackageDetail.="<br>Scrub : ".$obj->getIdToText($rsIndi[$j]["scrub_id"],"db_trm","trm_name","trm_id");
						}
					}
					
					if($rsIndi[$j]["wrap_id"]!=1){
						if($showPackageDetail==""){
							$showPackageDetail.="Wrap : ".$obj->getIdToText($rsIndi[$j]["wrap_id"],"db_trm","trm_name","trm_id");
						}else{
							$showPackageDetail.="<br>Wrap : ".$obj->getIdToText($rsIndi[$j]["wrap_id"],"db_trm","trm_name","trm_id");
						}
					}
					if($rsIndi[$j]["bath_id"]!=1){
						if($showPackageDetail==""){
							$showPackageDetail.="Bath : ".$obj->getIdToText($rsIndi[$j]["bath_id"],"db_trm","trm_name","trm_id");
						}else{
							$showPackageDetail.="<br>Bath : ".$obj->getIdToText($rsIndi[$j]["bath_id"],"db_trm","trm_name","trm_id");
						}
					}
					if($rsIndi[$j]["facial_id"]!=1){
						if($showPackageDetail==""){
							$showPackageDetail.="Facial : ".$obj->getIdToText($rsIndi[$j]["facial_id"],"db_trm","trm_name","trm_id");
						}else{
							$showPackageDetail.="<br>Facial : ".$obj->getIdToText($rsIndi[$j]["facial_id"],"db_trm","trm_name","trm_id");
						}
					}
					
						if(($chkColor%2)==0){
			            	echo "<tr class=\"content_list\" onmouseover=\"high(this)\" onmouseout=\"low(this)\">";
						}else{
							echo "<tr class=\"content_list1\" onmouseover=\"high(this)\" onmouseout=\"low(this)\">";
						} 
						
						echo "<td>".$dateobj->convertdate($rsBook[$i]["b_appt_date"],'Y-m-d',$sdateformat)."</td>
							<td>".$rsBranch[0]["branch_name"]."</td>
							<td><a href=\"javascript:;\" onClick=\"window.open('../manage_booking.php?chkpage=1&bookid=".$rsBook[$i]["book_id"]."','bookingWindow".$rsBook[$i]["book_id"]."',
									'height=700,width=1020,resizable=0,scrollbars=1');\" >".$obj->getIdToText($rsBook[$i]["book_id"],"c_bpds_link","bpds_id","tb_id","`tb_name`='a_bookinginfo'")."</a></td>
							<td>".$rsRoom[0]["room_name"]."</td>
							<td>".$obj->getIdToText($rsHour[0]["hour_id"],"l_hour","hour_name","hour_id")."&nbsp;</td>";
						if($rsPk[0]["package_name"]!=""){
							if($showPackageDetail==""){
								echo "<td align=\"left\">".$rsPk[0]["package_name"]."</td>";
							}else{
								echo "<td align=\"left\" title=\" header=[Package Detail] body=[".$obj->encodeText($showPackageDetail)."]\" style=\"color: #ff0000; cursor: pointer;\">".$rsPk[0]["package_name"]."</td>";								
							}
							
							
						}else{
							echo "<td>&nbsp;&nbsp; -</td>";
						}	
						for($td=0;$td<$maxMsg;$td++){
							//echo $rsMsg[$td]["massage_id"]."<br>";
							if(isset($rsMsg[$td]["massage_id"])){//For debug undefined offset : 1. By Ruck : 19-05-2009
								if($rsMsg[$td]["massage_id"]!="" && $rsMsg[$td]["massage_id"]!=1){
									$sql = "select trm_name from db_trm where trm_id=".$rsMsg[$td]["massage_id"];
									//echo $sql."<br>";
									$rsTrm = $obj->getResult($sql);
									echo "<td>".$rsTrm[0]["trm_name"]."</td>";
								}else{
									echo "<td>&nbsp;&nbsp; -</td>";
								}			
							}
						}
						$thName="";	
						for($th=0;$th<$rsMult_th["rows"];$th++){
							$sql = "select emp_nickname from l_employee where emp_id=".$rsMult_th[$th]["therapist_id"];
							$rsTh = $obj->getResult($sql);
							if($th==0){
								$thName = $rsTh[0]["emp_nickname"];
							}else{
								$thName .= " , ".$rsTh[0]["emp_nickname"];
							}
						}
						echo "<td> $thName</td>
							</tr> ";	
						$chkColor++;
				}
			}
			
			?>
			</table> 
			</fieldset>
		</div>
		</div>
	  </td>
	</tr>
</table>
</body>
</html>