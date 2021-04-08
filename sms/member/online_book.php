<?
/*
 * file for let customers reservation oasis booking pass website
 * @modified - 6-Oct-2009 by natt@tap10.net
 *           - change chiangmainoasis.com to oasisspa.net
 */
session_start();

//$_SESSION["__member_id"]=1;

include("include/smseg.inc.php");
$root = $_SERVER["DOCUMENT_ROOT"];
$smseg = new smseg();

if(!$smseg->checkLogin()){
	?>
	<script>
		parent.location = "login.php";
	</script>
	<?
}else{

$pagename = $_SERVER["PHP_SELF"];
$webuserarr = explode(".", $_SESSION["SERVER_NAME"]);
$customize_part = "/clients/" . $webuserarr[0];

$member = $smseg->getMember($_SESSION["__member_id"]);
$mpic=$member["mpic"];
$bmember = $smseg->getMemberBalance($member["member_code"]);
$location = $smseg->getParameter("location");
$appointment_date = $smseg->getParameter("appointment_date",date("d-m-Y"));
$appoint_time = $smseg->getParameter("cs_apptime",25);
$csnum = $smseg->getParameter("cs_number");
$tthour = $smseg->getParameter("hourid");
$check = $smseg->getParameter("bookingcheck");
$dproduct = array();
$product = $smseg->getParameter("product");
$productcount = $smseg->getParameter("productcount",1);
$rs = $smseg->getProduct();

$chk_room=true;
$success=false;

if($check){
	$hinden_appdate=date('Ymd', strtotime($appointment_date));
	$chkrs = $smseg->checkRoom($hinden_appdate,$appoint_time,$tthour,$location);

	if(count($chkrs) > 0){
			$busyroom = implode(",",$chkrs);
		}else{
			$busyroom = "''";
		}
	
		// find not busy room
		$chksql = "select room_id, room_qty_people " .
				"from bl_room " .
				"where room_active=1 " .
				"and branch_id=$location " .
				"and room_id not in ($busyroom) " .
				"order by room_name ";
		$nbroomrs = $smseg->get_data($chksql);
		
		if($nbroomrs){
			$totalqty_people=0;
			for($i = 0; $i < $nbroomrs["rows"]; $i++) {
				$totalqty_people+=$nbroomrs[$i]["room_qty_people"];
			}
				if($totalqty_people<$csnum){
					$chk_room=false;
				}else{
		// set treatment individual room
		$cnt=0;$chk="";
		$cntallemproom=0;
		for($i=0;$i<$nbroomrs["rows"];$i++){
			$cntallemproom += $nbroomrs[$i]["room_qty_people"];
			for($k=0;$k<$nbroomrs[$i]["room_qty_people"];$k++){
				if($cnt>$csnum-1){$chk="break"; break;}
					$tw[$cnt]["room_id"]=$nbroomrs[$i]["room_id"];
					$cnt++;
			}
			if($chk=="break"){break;}
		}
		//print_r($tw);
		$app_roomid="";
		$app_roomname="";
		$cnt_qty=$csnum;
		$cnt_hour="";
		$cnt_status="";
		
		for($j=0;$j<count($tw);$j++){
			if($j==0){
				$app_roomid .= $tw[$j]["room_id"];
				$app_roomname .= $smseg->get_IdToText($tw[$j]["room_id"], "bl_room", "room_name", "room_id");
				
				$room_qty_people=$smseg->get_IdToText($tw[$j]["room_id"], "bl_room", "room_qty_people", "room_id");
				if($cnt_qty>=$room_qty_people){
					$cnt_qty=$cnt_qty-$room_qty_people;
					$app_qty_peoples .= "$room_qty_people";
				}else if($cnt_qty){
					$app_qty_peoples .= "$cnt_qty";
					$cnt_qty=0;
				}
			$cnt_status.=0;
			}else{
				if($tw[$j]["room_id"]!=$tw[$j-1]["room_id"]){
					$app_roomid .= "|".$tw[$j]["room_id"];
					$app_roomname .= "|".$smseg->get_IdToText($tw[$j]["room_id"], "bl_room", "room_name", "room_id");
				
					$room_qty_people=$smseg->get_IdToText($tw[$j]["room_id"], "bl_room", "room_qty_people", "room_id");
					if($cnt_qty>=$room_qty_people){
						$cnt_qty=$cnt_qty-$room_qty_people;
						$app_qty_peoples .= "|"."$room_qty_people";
					}else if($cnt_qty){
						$app_qty_peoples .= "|"."$cnt_qty";
						$cnt_qty=0;
					}
				$cnt_status.="|"."0";
				}
			}
		}

		$loop_qty=explode("|", $app_qty_peoples);
		for($k=0;$k<count($loop_qty);$k++){
			for($l=0;$l<$loop_qty[$k];$l++){
				if($l==0){
					if($k==0){
						$cnt_hour.=$tthour;
					}else{
						$cnt_hour.="|".$tthour;
					}
				}else{
					$cnt_hour.=",".$tthour;
				}
			}
		}
		$success=$smseg->InsertBookingData($member,$location,$tthour,$csnum,$appointment_date,$appoint_time,$app_roomid,$app_roomname,$app_qty_peoples,$cnt_hour,$cnt_status);
	}
		}else{
			$chk_room=false;
		}
}
?>
<html>
<head>
<title><?=$_SESSION["companyname"]?> Online Booking</title>
<meta name="keywords" content="" />
<META NAME="Description" CONTENT="">
<link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
<link rel="icon" href="/images/favicon.ico" type="image/x-icon" />
</head>

<link href="style.css" type="text/css" rel="stylesheet">
<script src="calendar1.js" type="text/javascript" language="javascript"></script>

<script type="text/javascript">

	function addProductcount(ck) {
	
		var productcount = document.getElementById("productcount");
		var product = document.getElementById("product[product]["+(productcount.value-1)+"]");
		
				
			if(ck!=1) {
				if(productcount.value > 1)
					productcount.value--;
					
			}
			else {
				if(product.value != 1) {
					productcount.value++;
				}
			}
		
	}
	
	function setTotal() {
		var qty,amount = Array();
		qty = document.getElementById("qty");
		amount = document.getElementById("amount");

		var total = document.getElementById("total");
		var pcount = document.getElementById("productcount");
		var i,a;
		
		for(i=0; i<pcount; i++) {
			a += qty[i]*amount[i];
		}
		
		total.value = a;
	}
	
	function refresh_order(ele,productcount) {
	
	for(i=0;i<productcount;i++){
		document.getElementById('product[product]['+i+']').value=1;
	}
	ele.submit();
	}
	
	function checkSubmit(ele) {
		var location = document.getElementById('location');
		var appdate = document.getElementById('appointment_date');
		var productcount = document.getElementById("productcount");
		var product = document.getElementById("product[product][0]");
		var bookingcheck = document.getElementById("bookingcheck");
		
		var date = new Date();
		var d  = date.getDate();
		var day = (d < 10) ? '0' + d : d;
		var m = date.getMonth() + 1;
		var month = (m < 10) ? '0' + m : m;
		var yy = date.getYear();
		var year = (yy < 1000) ? yy + 1900 : yy;
		
		var today = day+'-'+month+'-'+year;
		var sday = today.split("-");
		var eday = appdate.value.split("-");
		sDate = new Date(sday[2],sday[1]-1,sday[0]); 
		eDate = new Date(eday[2],eday[1]-1,eday[0]); 
		var daysDiff = Math.round((eDate-sDate)/86400000);
		
		var total = document.getElementById("totalamount");
		var bmem = document.getElementById("memamount");
		
		if(daysDiff<0){
			alert ("Please Select Appointment Date greater than today's date");
		}else if(location.value==0){
			alert ("Please Select Location");
		}else if(productcount.value==1 && product.value==1){
			alert ("Please Select Package");
		}else{
			if(parseFloat(total.value)>parseFloat(bmem.value)){
				alert ("Please Refill Your Member Balance in by \n\t1. Contact Reservation menu\n\t2. Gift Certificate menu");
			}else{
				bookingcheck.value='1';
				ele.submit();
			}
		}
	}
	
	function show_apptime(ele){
			ele.submit();
	}

</script>
<?
//Get App Time For Branch
			$sql_gtime="SELECT `start_time_id`,`close_time_id` FROM `bl_branchinfo` WHERE `branch_id` =$location";
			$rs_gtime=$smseg->get_data($sql_gtime);
			
			$sql_apptime="SELECT time_id,TIME_FORMAT(time_start,'%H:%i') as time_start FROM p_timer WHERE mod(time_id,6)=1 and time_id>".$rs_gtime[0]["start_time_id"]." " .
					"and time_id<".$rs_gtime[0]["close_time_id"]." ";
			$rs_apptime=$smseg->get_data($sql_apptime);
//
?>
<body class="onlinebooking">
<form action="<?=$pagename?>" method="post" name="onlinebooking" id="onlinebooking" enctype="multipart/form-data">
 <table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0" align="center" background="../images/bg.jpg">
<tr>
	<td valign="top">
	<table width="750" cellpadding="0" cellspacing="0" border="0" align="center" background="../images/index_bg.gif">
	
	<tr>
		<td width="10">&nbsp;</td>
		<td width="730" height="30">&nbsp;</td>
		<td width="10" valign="top">&nbsp;</td>
	</tr>
	
	<tr>
		<td width="10">&nbsp;</td>
		<td width="730" height="30">
		<table width="730" cellpadding="0" cellspacing="0" border="0">
			<tr align="right"height="70">
			<td>
			<img border="2" src="<?=$customize_part;?>/images/member/<?=$mpic;?>" width="60px" height="60px">
			</td>
			</tr>
			<tr align="right">
				<td>Member:&nbsp;<?=$member["member_code"]?>&nbsp;(<a style="text-decoration: none" href="logout.php " target="_parent" title="Click here to log out">log out</a>)</td>
			</tr>
		</table>
		</td>
		<td width="10" valign="top">&nbsp;</td>
	</tr>
	
	<?if($chk_room==false){?>
	<tr>
		<td width="10">&nbsp;</td>
		<td width="730" height="30">
		<table width="730" cellpadding="2" cellspacing="0" border="1" align="center">
			<tr bgcolor="#970f0f">
				<td><b><font color="#ffffff">This Time Appointment period and room not available. Please select new Time Appointment.</font><b></td>
			</tr>
		</table>
		</td>
		<td width="10" valign="top">&nbsp;</td>
	</tr>
	
	<tr>
		<td width="10">&nbsp;</td>
		<td width="730" height="30">
			<font color="#ff0000">&nbsp;Recommend Appointment Time<br></font>
			<?
			$count_rec=0;
			for($i=0;$i<$rs_apptime["rows"];$i++){
				if($count_rec<2){
					$rchkrs = $smseg->checkRoom($hinden_appdate,$rs_apptime[$i]["time_id"],$tthour,$location);
					if(count($chkrs) > 0){
						$rbusyroom = implode(",",$rchkrs);
					}else{
						$rbusyroom = "''";
					}
					
					// find not busy room
					$rchksql = "select room_id, room_qty_people " .
							"from bl_room " .
							"where room_active=1 " .
							"and branch_id=$location " .
							"and room_id not in ($rbusyroom) " .
							"order by room_name ";
					$rnbroomrs = $smseg->get_data($rchksql);

					if($rnbroomrs){
						$totalqty_people=0;
						for($j = 0; $j < $rnbroomrs["rows"]; $j++) {
							$totalqty_people+=$rnbroomrs[$j]["room_qty_people"];
						}
							if($totalqty_people>$csnum){
								echo "&nbsp;&nbsp;&nbsp;<font color='#ff0000'>- ".$rs_apptime[$i]["time_start"]."</font><br>";
								$count_rec++;
							}
					}
				}else{
					break;
				}
			}
			?>
		</td>
		<td width="10" valign="top">&nbsp;</td>
	</tr>
	<?}?>
	
	<?if($success==true){?>
	<tr>
		<td width="10">&nbsp;</td>
		<td width="730" height="30">
		<table width="730" cellpadding="2" cellspacing="0" border="1" align="center">
			<tr bgcolor="#08611f">
				<td><b><font color="#ffffff">This Online Booking has success. Thank you for booking.</font><b></td>
			</tr>
		</table>
		</td>
		<td width="10" valign="top">&nbsp;</td>
	</tr>
	<?}?>
	<tr>
		<td width="10">&nbsp;</td>
		<td width="730" valign="top">
		
		<table width="730" cellpadding="0" cellspacing="0" border="0">
		<tr>			
			
			
							
			<td width="365" valign="top" >
			<table width="365" cellpadding="2" cellspacing="0" border="0">
			<tr class="just">
				<td colspan="3">
				<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="20">&nbsp;</td>
					<td class="head_gold_b" height="20px"><font color="#498ab7">Booking Information</font></td>
				</tr>
				</table>
				</td>
			</tr>
								
			<tr>
				<td width="150" align="right">Member's Name</td>
				<td width="10">&nbsp;</td>
				<td width="195" align="left"><input type="text" size="25" value="<?=$member["member_name"]?>" id="cs_name" name="cs_name" readonly="read-only"></td>
			</tr>
			
			
			<tr>
				<td width="150" align="right">Country</td>
				<td width="10">&nbsp;</td>
				<td width="195" align="left">
				<input type="text" size="25" value="<?=$smseg->get_IdToText($member["nationality_id"],"dl_nationality","nationality_name","nationality_id");?>" id="country" name="country" readonly="read-only">
				</td>
			</tr>
			
			
			<tr>
				<td align="right">E-mail Address</td>
				<td>&nbsp;</td>
				<td align="left"><input type="text" size="25" value="<?=$member["email"]?>" id="email" name="email" readonly="read-only"></td>
			</tr>
			
			<tr>
				<td align="right">Phone Number</td>
				<td>&nbsp;</td>
				<td align="left"><input type="text" size="25" value="<?=$member["phone"]?>" id="phone" name="phone" readonly="read-only"></td>
			</tr>
			
			<tr>
				<td align="right"><!--<font color="red">*--></font>Appointment Date</td>
				<td>&nbsp;</td>
				<td align="left"><input type="text" id="appointment_date" name="appointment_date" value="<?=$appointment_date?>" size="21" readonly="read-only">&nbsp;<a href="javascript:cal1.popup();"><img src='images/cal.gif' width="16" height="16" border="0" alt="Click Here to Pick up the date"></a></td>
			</tr>
			
			<tr>
				<td align="right"><!--<font color="red">*--></font>Number of Customers</td>
				<td>&nbsp;</td>
				<td align="left">
				<select id="cs_number" name="cs_number" style="width:60px; font:Tahoma, Verdana; font-size:11px">
					<option <?if($csnum==1){?>selected<?}?> value="1">1</option>
					<option <?if($csnum==2){?>selected<?}?> value="2">2</option>
					<option <?if($csnum==3){?>selected<?}?> value="3">3</option>
					<option <?if($csnum==4){?>selected<?}?> value="4">4</option>
				</select>
				</td>
			</tr>
			
			<tr>
				<td align="right"><font color="red">*</font>Location</td>
				<td>&nbsp;</td>
				<td align="left">
				<?
				$sql_branch="select * from bl_branchinfo where branch_id<>1 and branch_active =1";
				$rs_branch=$smseg->get_data($sql_branch);
				?>
				<select id="location" name="location" style="width:80px; font:Tahoma, Verdana; font-size:11px" onchange="show_apptime(this.form)">
					<option value="0">---Select---</option>
					<?for($i=0;$i<$rs_branch["rows"];$i++){?>
						<option <?if($location==$rs_branch[$i]["branch_id"]){?>selected<?}?> value="<?=$rs_branch[$i]["branch_id"]?>"><?=$rs_branch[$i]["branch_name"]?></option>
					<?}?>
				</select>
				</td>
			</tr>
			<?
			if(!$location){$s_apptime="display:none";}	
			?>
			<tr style="<?=$s_apptime?>">
				<td align="right"><!--<font color="red">*--></font>Appointment Time</td>
				<td>&nbsp;</td>
				<td align="left">
				<select id="cs_apptime" name="cs_apptime" style="width:60px; font:Tahoma, Verdana; font-size:11px;">
					<?for($i=0;$i<$rs_apptime["rows"];$i++){?>
						<option <?if($appoint_time==$rs_apptime[$i]["time_id"]){?>selected<?}?> value="<?=$rs_apptime[$i]["time_id"]?>"><?=$rs_apptime[$i]["time_start"]?></option>
					<?}?>					
				</select>
				</td>
			</tr>
			
			</table>
			</td>
			
			
			
			<td width="365" valign="top">
			<table width="365" cellpadding="2" cellspacing="0" border="0">
			<tr class="just">
				<td colspan="3">
				<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="head_gold_b" height="20px"><font color="#498ab7">Product Information</font></td>
				</tr>
				</table>
				</td>
			</tr>

			<!--Get SMS-->
				<tr>
					<td width="350">
					<table width="350" cellpadding="2" cellspacing="0" border="1" bordercolor="#006666" align="center">
					<tr bgcolor="#006666">
						<td colspan="5" align="center" class="b" height="25"><font color="#ffffff">Select Package</font></td>
					</tr>
					<tr align="center">
						<td width="40" align="center">No.</td>
						<td >Package</td>
						<td width="50" valign="top">Qty</td>
						<td valign="bottom">Price</td>
						<td>Total
						</td>
					</tr>
					
					<?
					$total=0;
					$ntotal=0;
					$count=0;
					$taxservice=0;
					$totalhout=0;
					
					for($i=0; $i<$productcount; $i++) {
					$sc=0;
					$tax=0;
					if($product["product"][$i]>1||$i==$productcount-1){	
						$dproduct[$i] = $smseg->make_dropdown("product[product][$count]","pd_name","pd_id",$rs,$product["product"][$i],"1",150,"product[product][$count]",0,$event);	
					
					
						if(!$product["qty"][$i]){
							$qtyv = 1;
						}else{
							$qtyv = $product["qty"][$i];
						}
						if($smseg->getPrice($product["product"][$i])){
								$a = $smseg->getPrice($product["product"][$i]);
						}else{
								$a = "0";
						}
						$total += $qtyv*$a;	
						
					
						if($smseg->getScTax($product["product"][$i])){
							$sc = ($qtyv*$a*(10/100));
						}else{$sc = 0;}
						
						if($smseg->getScTax($product["product"][$i],1)){
							$tax = $sc+((($qtyv*$a)+$sc)*(7/100));
						}
						
						$taxservice += $tax;
			
					?>
			
					<tr align="center">
						<td align="center"><?=$count+1?></td>
						<td><?=$dproduct[$i]?></td>
						<td valign="bottom"><input type="text" size="3" maxlength="2" value="<?=$qtyv?>" id="product[qty][<?=$count?>]" name="product[qty][<?=$count?>]" onChange="this.form.submit()"></td>
						<td valign="bottom"><input type="text" size="8" maxlength="7" value="<?=number_format($a,2,".",",")?>" id="product[amount][<?=$count?>]" name="product[amount][<?=$count?>]" disabled="disabled" style="text-align:right"></td>
						<td valign="bottom"><input type="text" size="8" maxlength="7" value="<?=number_format($qtyv*$a,2,".",",")?>" id="product[t][<?=$count?>]" name="product[t][<?=$count?>]" disabled="disabled" style="text-align:right"></td>
					</tr>
					<?		$count++;
						}
						//$tamount = $total+$ntotal;
					
					$hour_id=$smseg->get_IdToText($product["product"][$i],"cl_product","hour_id","pd_id");
					$totalhout+=$smseg->get_IdToText($hour_id,"l_hour","hour_calculate","hour_id")*$qtyv;
					}
					$productcount = $count;
					$t_hour_id=$smseg->get_IdToText($totalhout,"l_hour","hour_id","hour_calculate");
					?>
					<input type="hidden" name="hourid" id="hourid" value="<?=$t_hour_id?>">
					
			        <tr>
						<td colspan="4" align="center">Total</td>
						<td align="center"><input type="text" size="8" id="ptotal" name="ptotal" value="<?=number_format($total,2,".",",")?>" disabled="disabled" style="text-align:right"></td>
					</tr>
					<tr>
						<td colspan="4" align="center">Tax &amp; Services Charge</td>
						<td align="center"><input type="text" size="8" id="taxsc" name="taxsc" value="<?=number_format($taxservice,2,".",",")?>" disabled="disabled" style="text-align:right"></td>
					</tr>
					<tr>
						<td colspan="4" align="center">Total Amount</td>
						<td align="center"><input type="text" size="8" id="total" name="total" value="<?=number_format($total+$taxservice,2,".",",")?>" disabled="disabled" style="text-align:right"></td>
					</tr>
					<input type="hidden" size="8" id="totalamount" name="totalamount" value="<?=number_format($total+$taxservice,2,".","")?>">
					<!--Member Balance-->
					<tr>
						<td colspan="4" align="center">Your Member Balance</td><?if($bmember<($total+$taxservice)){$color="red";}else{$color="green";}?>
						<td align="center"><input type="text" size="8" id="membalance" name="membalance" style="color:<?=$color?>;text-align:right;"value="<?=number_format($bmember,2,".",",")?>" disabled="disabled" style="text-align:right"></td>
					</tr>
					<input type="hidden" size="8" id="memamount" name="memamount" value="<?=number_format($bmember,2,".","")?>">
					<tr>
						<td colspan="5" align="right">
						<input type="button" name="refresh" class="button" value="refresh order" onClick="refresh_order(this.form,<?=$productcount?>)">
						<!--&nbsp;<input type="button" onClick="addProductcount(false);this.form.submit();" value=" delete bottom product " class="button" id="del">-->
						</td>
					</tr>	
					</table>
					<input type="hidden" name="productcount" id="productcount" value="<?=$productcount?>">
					</td>
				</tr>
			<!--End Get SMS-->
		
			<tr>
				<td height="25" align="center"><input type="button" id="booking" name="booking" value="  Booking Now  " class="button" onClick="checkSubmit(this.form);"></td>
				<td align="left"></td>
			</tr>
			<input type="hidden" name="bookingcheck" id="bookingcheck" value="">
			</table>
			</td>
		</tr>
		
		</table>
		</td>
		<td width="10" valign="top">&nbsp;</td>
	</tr>
	
	</table>
	</td>
</tr>


</table>
<script language="JavaScript">
				var cal1 = new calendar1(document.forms['onlinebooking'].elements['appointment_date']);
				cal1.year_scroll = true;
				cal1.time_comp = false;	 								
</script>
</form>
</body>
</html>

<?}?>