<?
include("../include.php");
require_once("appt.inc.php");
require_once("date.inc.php");
require_once("secure.inc.php");
$obj = new appt(); 
$scObj = new secure();

$obj->setErrorMsg("");
$pagename = "manage_booking.php";
$obj->setLimitBranchOnLocation();
$errormsg="";
$date = $obj->getParameter("date",date("d-m-Y"));
$newLogin=$obj->getParameter("newLogin");
$successmsg = $obj->getParameter("successmsg");
$obj->setDebugStatus(false);
$obj->setErrorMsgColor("red");
$chkpage = $obj->getParameter("chkpage",1);

//check booking status and set book id
$status = $obj->getParameter("status","add");
if($status!="edit"){$status="add";}
$bookid = $obj->getParameter("bookid","");
if($bookid){$status="edit";}else{$status="add";}

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

// check sale receipt lock/unlock permission 
$chkSREdit=$object->isEditSaleReceipt();

// check before closing booking info windows
$close = $object->getParameter("close",false);
if($close && $status=="edit"){
	$chkbooking = $object->beforeCloseBooking($bookid);
	if($chkbooking){?><script language="javascript">window.close();</script><? }
	else{$errormsg ="Can't Closing this window. Please try again!!";}
}


/***************************************************
 * Booking Infomation
 ***************************************************/
 
// ############# Get some information from request value ##############
$cs = $obj->getParameter("cs",false);
$cc = $obj->getParameter("cc",array());		// change false to array natt/15-05-2009
$trf = $obj->getParameter("trf",false);
$tw = $obj->getParameter("tw",false);
$thcount = $obj->getParameter("thcount");
$msgcount = $obj->getParameter("msgcount");
// ########### End Get some information from request value ############
// --------------------------------------------------------------------
// For debug undefined index : trf,cc. By Ruck : 18-05-2009 // 
if(!isset($cc["cc"])){$cc["cc"]="";}
if(!isset($trf["trf"])){$trf["trf"]="";}

// ############### Initial check status for sale receipt ##############
$srInit = false; //For debug undefined variable: srInit. By Ruck 14-05-2009
if($cs==false){$srInit = true;} 
// ############### Initial check status for sale receipt ##############
// --------------------------------------------------------------------
// #######################  Cancel Path ###############################
if($status=="edit"&&$cs==false){
	$sql="select b_set_cancel from a_bookinginfo where book_id=$bookid";
	$rs_cs=$obj->getResult($sql);
	$cc["cc"] = ($rs_cs[0]["b_set_cancel"]==1)?"checked":"";
	$sql="select * from ac_cancal where book_id=$bookid";
	$rs_cc=$obj->getResult($sql);
	$cc["date"] = $dateobj->convertdate($rs_cc[0]["cancel_datets"],'Y-m-d',$sdateformat);
	$cc["hidden_date"] = $dateobj->convertdate($rs_cc[0]["cancel_datets"],'Y-m-d',"Ymd");
	$cc["comment"] = $rs_cc[0]["cancel_comment"];
}
if($cc["cc"]!="checked"){ 
	$cc["date"] = date($sdateformat);
	$cc["hidden_date"] = date("Ymd");
	$cc["comment"] = "";
}

// debugging all undified variable natt/16-05-2009
//$cc["cc"]=$obj->issetParameter($cc["cc"],"");	if add this line it will cause bug auto cancel booking
$cc["date"]= (isset($cc["date"]))?$cc["date"]:date($sdateformat);
$cc["hidden_date"]= (isset($cc["hidden_date"]))?$cc["hidden_date"]:date("Ymd");
$cc["comment"]= (isset($cc["comment"]))?$cc["comment"]:"";

// #######################  End Cancel Path ###########################
// --------------------------------------------------------------------
// #######################  Transfer Path #############################

if($status=="edit"&&!$cs){
	$sql="select b_set_pickup from a_bookinginfo where book_id=$bookid";
	$rs_cs=$obj->getResult($sql);
	$trf["trf"] = ($rs_cs[0]["b_set_pickup"]==1)?"checked":"";
	$sql="select * from ab_transfer where book_id=$bookid";
	$rs_trf=$obj->getResult($sql);
	if($rs_trf){
		$trf["pu_time"] = $rs_trf[0]["pu_time"];
		$trf["tb_time"] = $rs_trf[0]["tb_time"];
		$trf["dr_pu"] = $rs_trf[0]["driver_pu_id"];
		$trf["dr_tb"] = $rs_trf[0]["driver_tb_id"];
		$trf["p_pu"] = $rs_trf[0]["pu_place"];
		$trf["p_tb"] = $rs_trf[0]["tb_place"];
	}
}

// debugging all undified variable natt/16-05-2009
$trf["dr_pu"] = (isset($trf["dr_pu"]))?$trf["dr_pu"]:"";
$trf["dr_tb"] = (isset($trf["dr_tb"]))?$trf["dr_tb"]:"";
$trf["p_pu"] = (isset($trf["p_pu"]))?$trf["p_pu"]:"";
$trf["p_tb"] = (isset($trf["p_tb"]))?$trf["p_tb"]:"";
// #######################  End Transfer Path #########################
// --------------------------------------------------------------------
// #######################  Therapist Working Sheet Path ##############
$room = array();
if($status=="edit"&&$cs==false){
	//$sql = "select * from d_indivi_info where book_id=$bookid order by indivi_id";
	$sql = "select * from d_indivi_info where book_id=$bookid";
	$rs_tw = $obj->getResult($sql);
	for($i=0;$i<$rs_tw["rows"];$i++){
		$tw[$i]["id"] = $rs_tw[$i]["indivi_id"];
		$tw[$i]["tthour"] = $rs_tw[$i]["hour_id"];
		$tw[$i]["csnameinroom"] = $rs_tw[$i]["cs_name"];
		$tw[$i]["csphoneinroom"] = $rs_tw[$i]["cs_phone"];
		$tw[$i]["csemail"] = $rs_tw[$i]["cs_email"];
		$tw[$i]["csageinroom"] = $rs_tw[$i]["cs_age"];
		$tw[$i]["hidden_csbday"] = $rs_tw[$i]["cs_birthday"]; //yyyy-mm-dd
		if($tw[$i]["hidden_csbday"]=="0000-00-00"){
			$tw[$i]["csbday"] = "";
		}else{
			$tw[$i]["csbday"] = $dateobj->convertdate($tw[$i]["hidden_csbday"],'Y-m-d',$sdateformat);
		}
		$tw[$i]["national"] = $rs_tw[$i]["nationality_id"];
		$tw[$i]["sex"] = $rs_tw[$i]["sex_id"];
		$tw[$i]["resident"] = ($rs_tw[$i]["resident"]==1)?"resident":"noset";
		if($tw[$i]["resident"]!="resident"){
			$tw[$i]["resident"] = ($rs_tw[$i]["visitor"]==1)?"visitor":"noset";
		}
		$tw[$i]["member_use"] = ($rs_tw[$i]["member_use"]==1)?"checked":"";
		$tw[$i]["room"] = $rs_tw[$i]["room_id"];
		$tw[$i]["package"] = $rs_tw[$i]["package_id"];
		$tw[$i]["strength"] = $rs_tw[$i]["strength_id"];
		$tw[$i]["scrub"] = $rs_tw[$i]["scrub_id"];
		$tw[$i]["wrap"] = $rs_tw[$i]["wrap_id"];
		$tw[$i]["bath"] = $rs_tw[$i]["bath_id"];
		$tw[$i]["facial"] = $rs_tw[$i]["facial_id"];
		$tw[$i]["comments"] = $rs_tw[$i]["comments"];
		$tw[$i]["stream"] = ($rs_tw[$i]["stream"])?"checked":"";;
		$tw[$i]["cusstate"] = ($rs_tw[$i]["b_set_inroom"])?"c_set_inroom":"";
		if($tw[$i]["cusstate"]!="c_set_inroom")
			$tw[$i]["cusstate"] = ($rs_tw[$i]["b_set_finish"])?"c_set_finish":"";
		if($tw[$i]["cusstate"]!="c_set_finish"&&$tw[$i]["cusstate"]!="c_set_inroom")
			$tw[$i]["cusstate"] = ($rs_tw[$i]["b_set_atspa"])?"c_set_atspa":"";
		
		$chksql = "select * from da_mult_th where indivi_id=".$tw[$i]["id"];
		$rs_multh = $obj->getResult($chksql);
		$thcount[$i] = $rs_multh["rows"];
		for($j=0;$j<$rs_multh["rows"];$j++){
			$tw[$i][$j]["thid"] = $rs_multh[$j]["multh_id"];
			$tw[$i][$j]["name"] = $rs_multh[$j]["therapist_id"];
			$tw[$i][$j]["hour"] = $rs_multh[$j]["hour_id"];
		}
		
		$chksql2 = "select * from da_mult_msg where indivi_id=".$tw[$i]["id"];
		$rs_multmsg = $obj->getResult($chksql2);
		$msgcount[$i] = $rs_multmsg["rows"];
		for($j=0;$j<$rs_multmsg["rows"];$j++){
			$tw[$i][$j]["msgid"] = $rs_multmsg[$j]["multmsg_id"];
			$tw[$i][$j]["msg"] = $rs_multmsg[$j]["massage_id"];
		}
	}
}
if($status=="edit"){
	$chkPinroom = $obj->checkPeopleInroom($tw);
	if(!$chkPinroom){
		$errormsg = $obj->getErrorMsg();
	}
}

// debugging all undified variable Ruck/16-05-2009
if(!isset($tw["phone"])){$tw["phone"]="";}
// #######################  Therapist Working Sheet Path ##############
// #######################  Customer Infomation Path ###############################
if($status=="add"&&$cs){
	$chktthour = $obj->getParameter("chktthour",false);
	$chkttpp = $obj->getParameter("chkttpp",false);
	$chkappttime = $obj->getParameter("chkappttime",false);
	// if change total people or change total hour or change appointment time
	if(($chkttpp==1||$chktthour==1||$chkappttime==1)&&!isset($_POST["add"])){
		// find busy room
		$chkrs = $obj->checkRoom($cs["hidden_apptdate"],$cs["appttime"],$cs["tthour"],$cs["branch"]);
		if(count($chkrs) > 0){
			$busyroom = implode(",",$chkrs);
		}else{
			$busyroom = "''";
		}
		
		// find free  room
		$chksql = "select room_id, room_qty_people " .
				"from bl_room " .
				"where room_active=1 " .
				"and branch_id=" .$cs["branch"]." ".
				"and room_id not in ($busyroom) " .
				"order by room_name ";
		$chkrs = $obj->getResult($chksql);
		
		// set treatment individual room
		$cnt=0;$chk="";
		$cntallemproom=0;
		for($i=0;$i<$chkrs["rows"];$i++){
			$cntallemproom += $chkrs[$i]["room_qty_people"];
			for($k=0;$k<$chkrs[$i]["room_qty_people"];$k++){
				if($cnt>$cs["ttpp"]-1){
					$chk="break"; 
					break;
				}
				$tw[$cnt]["room"]=$chkrs[$i]["room_id"];
				$tw[$cnt]["tthour"] = $cs["tthour"];	// check room hour
				$tw[$cnt][0]["hour"]=$cs["tthour"];		// check only first therapist hour
				$cnt++;
			}
			if($chk=="break"){
				break;
			}
		}
		if($chktthour==1){
			$chktthour++;
		}else{
			if($chkappttime==1){
				$chktthour=0;
			}else{
				$chkttpp++;
			}
		}
		if($chkappttime==1){
			$chktthour=0;
		}
		// check room can support total people in this time or not
		if($cntallemproom<$cs["ttpp"]){
			$errormsg="Please check Time Appointment period and room!!";
		}
	}
}
if($status=="add"&&$cs==false){
	$cs["branch"] = $obj->getParameter("branch",false);
	$cs["apptdate"] = $obj->getParameter("date",false);
	$cs["hidden_apptdate"] = $dateobj->convertdate($cs["apptdate"],$sdateformat,'Ymd');
	$chktthour = 0;
	$chkttpp = 0;
	$chkappttime = 0;
}

if($status=="edit"&&$cs==false){
	$sql="select * from a_bookinginfo where book_id=$bookid";
	$rs_cs=$obj->getResult($sql);
	if(!$rs_cs["rows"])
		exit("No have information with Book ID : ".$bookid);
	
	//echo "<br>Set CS Name 1";
	$cs["branch"] = $rs_cs[0]["b_branch_id"];
	$cs["ttpp"] = $rs_cs[0]["b_qty_people"];
	$cs["apptdate"] = $dateobj->convertdate($rs_cs[0]["b_appt_date"],'Y-m-d',$sdateformat);
	$cs["hidden_apptdate"] = $dateobj->convertdate($cs["apptdate"],$sdateformat,'Ymd');
	$cs["appttime"] = $rs_cs[0]["b_appt_time_id"];
	$cs["tthour"] = $rs_cs[0]["b_book_hour"];
	$cs["bcompany"] = $rs_cs[0]["c_bp_id"];
	$cs["bpname"] = $rs_cs[0]["c_bp_person"];
	$cs["bpphone"] = $rs_cs[0]["c_bp_phone"];
	$cs["cms"] = ($rs_cs[0]["c_set_cms"]==1)?"checked":"";
	$cs["cms_percent"] = $rs_cs[0]["c_pcms_id"];
	$cs["name"] = $rs_cs[0]["b_customer_name"];
	$cs["memid"] = $rs_cs[0]["a_member_code"];
	$cs["hotel"] = $rs_cs[0]["b_accomodations_id"];
	$cs["roomnum"] = $rs_cs[0]["b_hotel_room"];
	$cs["rs"] = $rs_cs[0]["b_reservation_id"];
	$cs["rc"] = $rs_cs[0]["b_receive_id"];
	$cs["atspa"] = ($rs_cs[0]["b_set_atspa"]==1)?"checked":"";
	$cs["insertdate"] = $rs_cs[0]["l_lu_date"];
	$cs["lastupdateuser"] = $rs_cs[0]["c_lu_user"];
	$cs["lastupdatedate"] = $rs_cs[0]["c_lu_date"];
	$cs["insertuser"] = $rs_cs[0]["l_lu_user"];
	$cs["inspection"] = $rs_cs[0]["mkcode_id"];
	$cs["phone"] = $rs_cs[0]["b_customer_phone"];
	///echo 'appttime: '.$cs["appttime"];
}
//For debug undefined index : . By Ruck : 18-05-2009
if(!isset($cs["cms"])){$cs["cms"]="";}
if(!isset($cs["atspa"])){$cs["atspa"]="";}
if(!isset($cs["memid"])){$cs["memid"]="";}
if(!isset($cs["name"])){$cs["name"]="";}
if(!isset($cs["hotel"])){$cs["hotel"]="";}
if(!isset($cs["roomnum"])){$cs["roomnum"]="";}
if(!isset($cs["phone"])){$cs["phone"]="";}
if(!isset($cs["bpphone"])){$cs["bpphone"]="";}
if(!isset($cs["bpname"])){$cs["bpname"]="";}
//For debug undefined index : . By Ruck : 19-05-2009
if(!isset($cs["rs"])){$cs["rs"]=1;}
if(!isset($cs["rc"])){$cs["rc"]=1;}

// debugging all undified variable natt/16-05-2009
$cs["branch"] = (isset($cs["branch"]))?$cs["branch"]:"11";
$obj->setBranchid($cs["branch"]);	// set branch id for initial all time list
$cs["ttpp"] = (isset($cs["ttpp"]))?$cs["ttpp"]:"1";
$cs["apptdate"] = (isset($cs["apptdate"]))?$cs["apptdate"]:$obj->getParameter("apptselect",date($sdateformat));
$cs["appttime"] = (isset($cs["appttime"]))?$cs["appttime"]:$obj->getStartTimeid();
$cs["tthour"] = (isset($cs["tthour"]))?$cs["tthour"]:"2";
$cs["bcompany"] = (isset($cs["bcompany"]))?$cs["bcompany"]:"1";
$cs["inspection"] = (isset($cs["inspection"]))?$cs["inspection"]:"1";
$cs["cms_percent"] = (isset($cs["cms_percent"]))?$cs["cms_percent"]:"3";
$chkcmspercent = $obj->getIdToText($cs["cms_percent"],"al_percent_cms","pcms_active","pcms_id");
if(!$chkcmspercent&&$status=="add"){$cs["cms_percent"]=0;}
$cs["hidden_apptdate"] = (isset($cs["hidden_apptdate"]))?$cs["hidden_apptdate"]:$dateobj->convertdate($cs["apptdate"],$sdateformat,'Ymd');
$cs["searchchk"] = (isset($cs["searchchk"]))?$cs["searchchk"]:"";
$trf["pu_time"] = $cs["appttime"]-6;
$trf["tb_time"] = $obj->getTbtime($cs["appttime"],$cs["tthour"]);
	
// #######################  End Customer Path #########################
// --------------------------------------------------------------------
// #######################  Commission Path #############################
$cmschk = false;
if($status=="edit"){
	$userId = $obj->getUserIdLogin();
	$sql = "select cms_update_time from s_userpermission where user_id=".$userId;
	$rs_cms = $obj->getResult($sql);
	$cmstime = 0;
	if($rs_cms[0]["cms_update_time"]){$cmstime=$rs_cms[0]["cms_update_time"];}
	
	if($cmstime>0){
		$chkcmstime = $obj->getIdToText($cmstime,"l_timeperiod","tp_name","tp_id");
		list($date,$time) = split(" ",$cs["insertdate"]);
		list($year,$month,$day)= split("-",$date);
		list($h,$m,$s)= split(":",$time);
		$expiretime = date("Y-m-d H:i:s", mktime((int)$h, (int)$m+$chkcmstime, (int)$s, (int)$month, (int)$day, (int)$year));
		$timenow = date("Y-m-d H:i:s");
		if($timenow > $expiretime){
			$cmschk = "disabled";
			$sql="select * from a_bookinginfo where book_id=$bookid";
			$rs_cs=$obj->getResult($sql);
			if(!$rs_cs["rows"])
				exit("No have information with Book ID : ".$bookid);
			
			$cs["bcompany"] = $rs_cs[0]["c_bp_id"];
			$cs["bpname"] = $rs_cs[0]["c_bp_person"];
			$cs["bpphone"] = $rs_cs[0]["c_bp_phone"];
			$cs["cms"] = ($rs_cs[0]["c_set_cms"]==1)?"checked":"";
			$cs["cms_percent"] = $rs_cs[0]["c_pcms_id"];
		}
	}
}
//echo $cmstime." ".$cmschk;


// #######################  End Commission Path #########################
// --------------------------------------------------------------------
// #######################  Comment Path #############################
if($status=="edit"){
	$sql = "select * from ad_comment where book_id=$bookid order by l_lu_date desc";
	//$sql = "select * from ad_comment where book_id=$bookid";
	//echo $sql;
	//die(); 
	$rs_comment = $obj->getResult($sql);
}


// #######################  End Comment Path #########################
// --------------------------------------------------------------------
// #######################  initial salereceipt information ##############################

$srd = $obj->getParameter("srd",false);
$srdcount = $obj->getParameter("srdcount",false);
$mpdcount = $obj->getParameter("mpdcount",false);
$srcount = $obj->getParameter("srcount",1);
// cut off "-- select --" product 
$srd = $obj->getCurrentTab($srd); // get srd and reset it
// count $srdcount for how many product in each salereceipt
$srdcount = $obj->getCurrentRowsInTab($srd,$srcount);
// count $srdcount for how many product in each Method of Payment
$mpdcount = $obj->getCurrentRowsInPay($srd,$srcount);

if($srInit){	// when come in this page 1 st time 	
	// it should be in specific case 
	// 1 st open booking for update or add new booking
	// get salereceipt all information from database
	$srd=$obj->getSaleReceiptData($bookid);
	
	if(count($srd)!=0){ 	// if have salereceipt information
		$srcount=count($srd);	// set $srcount
		//set new $srdcount from $srd
		$srdcount = $obj->getCurrentRowsInTab($srd,$srcount);
		//set new $srdcount from Method of Payment
		$mpdcount = $obj->getCurrentRowsInPay($srd,$srcount);
		
	}	
}

// #######################  End initial salereceipt information #########################
// --------------------------------------------------------------------
// #######################  SalesReceipt Detail #######################
if($status=="edit") {
	$sql = "select tax_id,servicescharge,b_branch_id from a_bookinginfo where book_id=$bookid";
	$rs = $obj->getResult($sql);
	//echo "<br>$sql";
	if($cs["branch"]==$rs[0]["b_branch_id"]){
		$taxpercent = $rs[0]["tax_id"];
		$servicescharge = $rs[0]["servicescharge"];
	}else{
		$sql = "select tax_id,servicescharge from bl_branchinfo where branch_active=1 and branch_id=".$cs["branch"]." order by branch_name limit 0,1";
		$rs = $obj->getResult($sql);
		//echo "<br>$sql";
		$servicescharge = $rs[0]["servicescharge"];
		$taxpercent = $rs[0]["tax_id"];
	}
	
}else{
	$sql = "select tax_id,servicescharge from bl_branchinfo where branch_active=1 and branch_id=".$cs["branch"]." order by branch_name limit 0,1";
	$rs = $obj->getResult($sql);
	//echo "<br>$sql";
	$servicescharge = $rs[0]["servicescharge"];
	$taxpercent=$rs[0]["tax_id"];
}
$subtotal = 0;
if(!$srcount){$srcount=$obj->getParameter("srcount",1);}
for($i=0; $i<$srcount; $i++) {
//======  Start init first value  ==============================================//
// debugging all undified variable natt/16-05-2009
	$amount[$i]["amount"]=0;
	$amount[$i]["svc"]=0;
	$amount[$i]["tax"]=0;
	$amount[$i]["payment"]=0;
//======  End init first value  ================================================//
	for($j=0; $j<$srdcount[$i]; $j++){
			if(!isset($srd[$i][$j]["quantity"])){$srd[$i][$j]["quantity"]=1;}
				
			if(!isset($srd[$i][$j]["quantity"])){$srd[$i][$j]["quantity"]=1;}
			if(!isset($srd[$i][$j]["pd_id"])){$srd[$i][$j]["pd_id"]=1;}
			if(!isset($srd[$i][$j]["plus_sc"])){$srd[$i][$j]["plus_sc"]=1;}
			if(!isset($srd[$i][$j]["plus_tax"])){$srd[$i][$j]["plus_tax"]=1;}
			if(!isset($srd[$i][$j]["pd_id_tmp"])){$srd[$i][$j]["pd_id_tmp"]="";}// debugging all undified index : pd_id_tmp Ruck/16-05-2009
			if(!isset($srd[$i][$j]["unit_price"])){$srd[$i][$j]["unit_price"]=0;}// debugging all undified index : unit_price Ruck/16-05-2009
			
			$chkPrice=false;
			$chkPdChange = false;
			$srd[$i][$j]["set_sc"]=false;
			$srd[$i][$j]["set_tax"]=false;
			
			if( $srd[$i][$j]["pd_id"]!= $srd[$i][$j]["pd_id_tmp"] && !$srInit){
				$srd[$i][$j]["pd_id_tmp"]= $srd[$i][$j]["pd_id"];
				$srd[$i][$j]["unit_price"]=0;
				if($obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_sc","pd_id") || $srd[$i][$j]["pd_id"]==1){
               		$srd[$i][$j]["plus_sc"]=1;
               		$srd[$i][$j]["set_sc"]=true;
	            }else{
	            	$srd[$i][$j]["plus_sc"]=0;
	            }
	            if($obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_tax","pd_id") || $srd[$i][$j]["pd_id"]==1){
	            	$srd[$i][$j]["plus_tax"]=1;
	            	$srd[$i][$j]["set_tax"]=true;
	            }else{
	            	$srd[$i][$j]["plus_tax"]=0;
	            }
				$chkPrice=true;
				$chkPdChange = true;
			}else{
				$srd[$i][$j]["pd_id_tmp"]= $srd[$i][$j]["pd_id"];
				if($obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_sc","pd_id")){
					//echo "<br>Sc value : ".$obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_sc","pd_id")." -- Pd id : ".$srd[$i][$j]["pd_id"];
               		$srd[$i][$j]["set_sc"]=true;
	            }
	            if($obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_tax","pd_id")){
	            	$srd[$i][$j]["set_tax"]=true;
	            }
			}
				
			if(!$srd[$i][$j]["unit_price"]){
				if($srInit){
					$srd[$i][$j]["pd_id_tmp"]= $srd[$i][$j]["pd_id"];
				}
				if($chkPrice){
					//echo "<br>Check New Price <br>";
					$srd[$i][$j]["pd_id_tmp"]= $srd[$i][$j]["pd_id"];
					$srd[$i][$j]["unit_price"]=$obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","standard_price","pd_id");
					
				}
				
			}
			//echo "After check<br>";
			// echo "pid_tmp : ".$srd[$i][$j]["pd_id_tmp"]."<br>Pid : ".$srd[$i][$j]["pd_id"]."<br>";
			$chkautosubmit=array($i,$j);
			
			//-- begin check product/qty/price value
			$product["product_id"][$j] = $srd[$i][$j]["pd_id"];
			$product["category_id"][$j] = $obj->getIdToText($product["product_id"][$j],"cl_product","pd_category_id","pd_id");
			$product["qty"][$j] = $srd[$i][$j]["quantity"];
			$product["unit_price"][$j] = $srd[$i][$j]["unit_price"];
			$product["total"][$j] = $product["qty"][$j]*$product["unit_price"][$j];
			//$product["set_sc"][$j] = ($srd[$i][$j]["srd_id"]!="" && !$chkPdChange)?$obj->getIdToText($srd[$i][$j]["srd_id"],"c_srdetail","set_sc","srdetail_id"):$obj->getIdToText($product["product_id"][$j],"cl_product","set_sc","pd_id");
			//$product["set_tax"][$j] = ($srd[$i][$j]["srd_id"]!="" && !$chkPdChange)?$obj->getIdToText($srd[$i][$j]["srd_id"],"c_srdetail","set_tax","srdetail_id"):$obj->getIdToText($product["product_id"][$j],"cl_product","set_tax","pd_id");
			$product["set_sc"][$j] = $srd[$i][$j]["plus_sc"]; 
			$product["set_tax"][$j] = $srd[$i][$j]["plus_tax"];
			$product["taxpercent"][$j] = $obj->getIdToText($taxpercent,"l_tax","tax_percent","tax_id");
			$product["servicescharge"][$j] = $servicescharge ;
			
			if($obj->getIdToText($product["category_id"][$j],"cl_product_category","set_payment","pd_category_id")==0) {
				if($obj->getIdToText($product["category_id"][$j],"cl_product_category","pos_neg_value","pd_category_id")==0) {
					$amount[$i]["amount"] -= $product["total"][$j];
					$amount[$i]["svc"] -= $obj->getsSvc($product,$j);
					$amount[$i]["tax"] -= $obj->getsTax($product,$j,$obj->getsSvc($product,$j));
				} else {
					$amount[$i]["amount"] += $product["total"][$j];
					$amount[$i]["svc"] += $obj->getsSvc($product,$j);
					$amount[$i]["tax"] += $obj->getsTax($product,$j,$obj->getsSvc($product,$j));
				}
			} else {
				if($obj->getIdToText($product["category_id"][$j],"cl_product_category","pos_neg_value","pd_category_id")==0) {
					$amount[$i]["payment"] += $product["total"][$j];
					$amount[$i]["payment"] += $obj->getsSvc($product,$j);
					$amount[$i]["payment"] += $obj->getsTax($product,$j,$obj->getsSvc($product,$j));
				} else {
					$amount[$i]["payment"] -= $product["total"][$j];
					$amount[$i]["payment"] -= $obj->getsSvc($product,$j);
					$amount[$i]["payment"] -= $obj->getsTax($product,$j,$obj->getsSvc($product,$j));
				}
			}
			
			if($obj->getIdToText($product["category_id"][$j],"cl_product_category","set_gift","pd_category_id")==1){
				$r_gift[$i] = $product["total"][$j];		
			}
		
	}
		$r_amount[$i] = $amount[$i]["amount"];
		$r_svc[$i] = $amount[$i]["svc"];
		$r_tax[$i] = $amount[$i]["tax"];
		$r_payment[$i] = $amount[$i]["payment"];
		$r_total[$i] = $r_amount[$i]+$r_svc[$i]+$r_tax[$i]-$r_payment[$i];
		//not minus payment
		$rnp_total[$i] = $r_amount[$i]+$r_gift[$i]+$r_svc[$i]+$r_tax[$i];
		
		$subtotal += $r_amount[$i];
}
// #######################  End SalesReceipt Detail ###################
// --------------------------------------------------------------------
// #######################  Giftcertificate Detail #######################
$gift = $obj->getParameter("gift",false);
$gift_len = strlen($gift["gift_number"]);
$queueGift = $obj->getParameter("queueGift",false);
$giftchk=$obj->getParameter("giftChk",0);
if(isset($_POST["giftsearch"]) && $gift_len>=3){
	$sql = "select * from g_gift where gift_number like '%$gift[gift_number]%'";
	$rs = $obj->getResult($sql);
	$gift_rows = $rs["rows"];
	
	$errormsg="";
	if($rs["rows"]!=0){
		for($i=0;$i<$gift_rows;$i++){
			$gift[$i]["to"] = $rs[$i]["give_to"];
	   		$gift[$i]["from"] = $rs[$i]["receive_from"];
	    	$gift[$i]["value"] = $rs[$i]["value"];
	    	$gift[$i]["product"] = $rs[$i]["product"];
			$gift[$i]["number"] = $rs[$i]["gift_number"];
			$gift[$i]["id"] = $rs[$i]["gift_id"];
		}
	}else{
		$errormsg="Don't have this gift number.";
		$gift[$i]["to"] = "";
	    $gift[$i]["from"] = "";
	    $gift[$i]["value"] = "";
	    $gift[$i]["product"] = "";
	    $gift[$i]["id"] = "";
	}
}
if(!isset($gift["giftsearch"])||$gift==""){
	$gift["giftsearch"] = "";
	$gift["number"] = "";
	$gift["to"] = "";
	$gift["from"] = "";
	$gift["value"] = "";
	$gift["product"] = "";
	$gift[$i]["id"] = "";
}

//if(isset($_POST["addgift"])){
if($obj->getParameter("addgift")){
$giftnumber=$obj->getParameter("giftnumber","");
	if($giftnumber!=""){
	for($i=0;$i<count($giftnumber);$i++){ 
		$sql = "select book_id,issue,expired,available from g_gift where gift_id=".$giftnumber[$i];
		$rs = $obj->getResult($sql);
		//echo $rs[0]["issue"];
		if($rs[0]["available"]==1){
			if($rs[0]["expired"]>=($dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"Y-m-d",$cs["branch"]))){
				if($rs[0]["book_id"]==0 && $rs){
					if($status=="add"){
						$chkGift=true;				
						for($i=0;$i<count($giftnumber);$i++){ 
								for($k=0;$k<count($queueGift);$k++){
									if($queueGift[$k]==$giftnumber[$i]){
										$chkGift=false;
									}
								} 
							if($chkGift){
								$queueGift[count($queueGift)]=$giftnumber[$i];
								//echo print_r($queueGift);
							}else{
								$errormsg="This gift number has already in this book.";
							}
						}
						
						/*
						for($i=0;$i<count($queueGift);$i++){
							if($queueGift[$i]==$giftnumber[$i]){
								$chkGift=false;
							}
						}
						for($i=0;$i<count($giftnumber);$i++){ 
							if($chkGift){
								$queueGift[count($queueGift)]=$giftnumber[$i];
								//echo print_r($queueGift);
							}else{
								$errormsg="This gift number has already in this book.";
							}
						}*/
					}else if($status=="edit") {
						$sql = "update g_gift set book_id=$bookid,used=".date("Ymd").",receive_by_id=".$cs["rc"]." where gift_id=".$giftnumber[$i];
						//echo "<br>$sql";
						$gid = $obj->setResult($sql);
					}
					
				}else if($rs==false){
					$errormsg="Don't have this gift number on system.";
				}else{
					$errormsg="Gift number ".$obj->getIdToText($giftnumber[$i],"g_gift","gift_number","gift_id")." has been used.";
				}
			}else{
				$errormsg="This gift has been expired.";
			}
		}else{
			$errormsg="This gift is unavailable.";
		}
		} // End for
	}else{
		$errormsg="Please insert gift number.";
		$giftnumber = "";
		$gift["to"] = "";
		$gift["from"] = "";
		$gift["value"] = "";
		$gift["product"] = "";
	}
}
$_POST["deleteGift"] = $obj->getParameter("deleteGift","");
if($_POST["deleteGift"]!=""){
	if($status=="add"){
		for($i=0;$i<count($queueGift);$i++){
			if($queueGift[$i]==$_POST["deleteGift"]){
				$queueGift[$i]="deleted";
			}
		}
	}else if($status=="edit"){
		$sql = "update g_gift set book_id='0',used='0000-00-00' where gift_id=".$_POST["deleteGift"];
		$gid = $obj->setResult($sql);
	}
}
$sqlb = "select * from g_gift where book_id=$bookid";
$rsb = $obj->getResult($sqlb);
if($status=="edit" && isset($_POST["add"])){
	for($i=0;$i<$rsb["rows"];$i++){
		$sql = "update g_gift set receive_by_id=".$cs["rc"]." where gift_number=".$rsb[$i]["gift_number"];
		//echo "<br>$sql";
		$gid = $obj->setResult($sql);
	}
}
// #########################  End Gift Detail #########################
/***************************************************
 * Security checking
 ***************************************************/
// convert date status for permission checking
if($cs["apptdate"]==""){
	$date=$obj->getParameter("date",$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$sdateformat"));
	$date=$dateobj->convertdate($date,$sdateformat,'Y-m-d');
}else{
	$date=$dateobj->convertdate($cs["apptdate"],$sdateformat,'Y-m-d');
}

$now=$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$sdateformat",$cs["branch"]);
// check reservation view limit date permission
if($status == "edit"){
	$preViewDate="";
	$afterViewDate="";
	$chkRsViewDate = $object->isReservationLimit();
	if($chkRsViewDate){
		$preViewDate = $object->getReservationDate("pre_viewdate","appt_viewchk");
		$afterViewDate = $object->getReservationDate("after_viewdate","appt_viewchk");
		$chkRsDate = $object->checkReservationDate($date,'Y-m-d',$preViewDate,$afterViewDate,$now);
		if(!$chkRsDate){
			$chkPageView=false;
		}
	}
}


// check reservtion edit date limit 
$preEditDate="";
$afterEditDate="";
$checkApptPage="false";	// flat for calendar chooser 
// checking if appt_editchk was check
$chkRsEditDate = $object->isReservationLimit("appt_editchk");
if($chkRsEditDate){
	$preEditDate= $object->getReservationDate("pre_editdate","appt_editchk");
	$afterEditDate= $object->getReservationDate("after_editdate","appt_editchk");
	$chkRsDate= $object->checkReservationDate($date,'Y-m-d',$preEditDate,$afterEditDate,$now);
	if(!$chkRsDate){
		$chkPageEdit=false;
		// if reservation edit date was check then reset appointment date
		if($status=="add"){
			$cs["apptdate"] = $dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$sdateformat");
			$cs["hidden_apptdate"] = $dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"Ymd");	
		}
	}
}else{
// seting flat for calendar chooser 
	$preEditDate="notCheck";
	$afterEditDate="notCheck";
}


// Check this booking is in location the same user location 
$userLocationChk = $object->isEditBookInLocation($cs["branch"]);
if($status=="edit" && $userLocationChk==false){
	$chkPageEdit=false;
}

if($userLocationChk==false){
	$chkPageView=false;
}

//Close window if user can't view booking 
if(!$chkPageView){
?>
		<script language="javascript">
			alert("You can't access this booking.");
			<?=($newLogin)?"opener.parent.location.reload();":""?>
			window.close();
		</script>
<?	
}


// check if have user use in edit page 
if($status=="edit"&& $srInit && $chkPageEdit){
	$uid=$object->checkUse($bookid);
	if($uid!=false){
		?>
		<script language="javascript">
			alert("This booking is used by <?=$obj->getIdToText($uid,"s_user","u","u_id")?>");
			<?=($newLogin)?"opener.parent.location.reload();":""?>
			window.close();
		</script>
		<?
	}else{
		$object->startBooking($bookid);
	}
	
}


/***************************************************
 * Insert and Update Information
 ***************************************************/
// system's hour period
$sql = "select * from l_hour";
$hourperiodrs = $obj->getResult($sql);
$hourperiod = array();
for($i=0;$i<$hourperiodrs["rows"];$i++){
		$hourperiod[$hourperiodrs[$i]["hour_id"]] = $hourperiodrs[$i]["hour_name"];
}

// #######################  Insert/Update Database #######################
if(isset($_POST["add"]) && !$newLogin ) {
		if($status=="edit") {
			$errormsg = false;
			$chkPeopleInroom = $obj->checkPeopleInroom($tw);
			if($chkPeopleInroom){
				$checkRm = 0;$err_rm='';
				$checkRoom = 0;$err_room = '';
				$cs["tthour"] = 0;
				//check room is empty or not				
				for($i=0;$i<$cs["ttpp"];$i++){
					$max_hour[$i]=0;
					for($j=0;$j<$thcount[$i];$j++){
						if($max_hour[$i]==0 || $hourperiod[$max_hour[$i]]<$hourperiod[$tw[$i][$j]["hour"]]){$max_hour[$i]=$tw[$i][$j]["hour"];}
					}
					$tw[$i]["hour_id"] = $max_hour[$i];
					if($cs["tthour"]==0 || $hourperiod[$cs["tthour"]] < $hourperiod[$tw[$i]["tthour"]]){$cs["tthour"]=$tw[$i]["tthour"];}
				}
				for($i=0;$i<$cs["ttpp"];$i++){
					$chkroom = $obj->checkEmptyRoom($cs["hidden_apptdate"],$cs["appttime"],$tw[$i]["tthour"],$tw[$i]["room"],$bookid);
					if($chkroom){
						if($checkRm>0){$err_rm.=', ';}
						$err_rm.=$obj->getIdToText($tw[$i]["room"],"bl_room","room_name","room_id");
						$checkRm++;
					}
					//For therapist hours more than room hours
					/*
					if($hourperiod[$tw[$i]["tthour"]]<$hourperiod[$max_hour[$i]]){
						if($checkRoom>0){$err_room.=', ';}
						$err_room.=$obj->getIdToText($tw[$i]["room"],"bl_room","room_name","room_id");
						$checkRoom++;
					}
					*/
					//End
				}
				$err_rmarr=explode(',',$err_rm); $err_rmarr=array_unique($err_rmarr); $err_rm=implode(',',$err_rmarr);
				if($checkRm>0){
					$chkRoom=false;
					$obj->setErrorMsg("Please check Hour & Time Appointment in room: $err_rm");
				}else{$chkRoom=true;}
				$tmp=explode(',',$err_room); $tmp=array_unique($tmp); $err_room=implode(',',$tmp);
				if($checkRoom>0&&$chkRoom==true){
						$chkRoom=false;
						$obj->setErrorMsg("Please check therapist hour in room: $err_room");
				}
				//$chkRoom=false; // chk state
				//check therapist time
				/*if($chkRoom){
					for($i=0;$i<$cs["ttpp"];$i++){
						for($j=0;$j<$thcount[$i];$j++){
							$checkTherapistTime = false;
							$checkTherapistTime = $obj->checkTherapistTime($cs["apptdate"],$tw[$i][$j]["name"],$cs["appttime"],$tw[$i][$j]["hour"],$bookid);
							if($checkTherapistTime){
								if($checkTh>0){$err_th.=', ';}
								$err_th.=$obj->getIdToText($checkTherapistTime,"l_employee","emp_nickname","emp_id");
								$checkTh++;
							}
						}
					}	
					if($checkTh>0){$chkPeople=false;$obj->setErrorMsg("Please check Therapist Time: $err_th");}
					else{$chkPeople=true;}
				}*/
				//echo "Error:".$obj->getErrorMsg();
				//echo $chkPeople;$chkRoom=false;
				//edit therapist
				
				if($chkRoom){$id = $obj->edit($cs,$bookid,$cc["cc"],$cs["cms"],$trf["trf"],false);}
				else{$id=false;}
				if($chkRoom&&$id){
					//edit c_cancle table
					$ccid = $obj->editcc($cc,$bookid);
					//edit transfer(Transportation) table
					$trfid = $obj->edittrf($trf,$bookid);
					//if c_cancle and transfer ok, add comment
					if($ccid&&$trfid){
						if(isset($cs["comment"])&&str_replace(' ','',$cs["comment"])!=''){
							$commentid = $obj->addcomment($cs["comment"],$bookid);
						}
						
						$indivirows = $obj->getRowFromId($bookid,"d_indivi_info","book_id");
						if((str_replace(' ','',$cs["comment"])==''||$commentid)){
							$chkindivi = 1;
							$condition = "and (";
							for($i=0;$i<$cs["ttpp"];$i++){
								if($chkindivi){
									$chktw = $obj->checkIdInTable($tw[$i]["id"],"d_indivi_info","indivi_id");
									if($chktw){
										$twid = $obj->editIndivi($tw[$i],$chktw,$bookid,$status);
										if(!$twid){$chkindivi=0;}
										else{
											//edit da_mult_th
											$multhrows = $obj->getRowFromId($chktw,"da_mult_th","indivi_id");
											$thcondition = "and (";
											for($j=0;$j<$thcount[$i];$j++){
													$chkth = $obj->checkIdInTable($tw[$i][$j]["thid"],"da_mult_th","multh_id");									
													if($chkth){$thid = $obj->editTh($tw[$i][$j],$chkth,$chktw,$bookid,$tw[$i]);}
													else{$thid = $obj->addTh($tw[$i][$j],$chktw,$bookid,$status,$tw[$i]);}
													if(!$thid){$chkindivi=0;}
													else{
														if($chkth){$thcondition .= " multh_id!=$chkth ";}
														else{$thcondition .= " multh_id!=$thid ";}
													}
													if($j<$thcount[$i]-1){$thcondition .= "and";}
											}
											$thcondition .= ")";
											$thcondition=($thcondition=="and ()")?false:$thcondition;
											// del therapist
											if($thcount[$i]<=$multhrows){
												$obj->delTh($chktw,$thcondition);
											}
											
											//edit da_mult_msg
											$multmsgrows = $obj->getRowFromId($chktw,"da_mult_msg","indivi_id");
											$msgcondition = "and (";
											for($j=0;$j<$msgcount[$i];$j++){
													$chkmsg = $obj->checkIdInTable($tw[$i][$j]["msgid"],"da_mult_msg","multmsg_id");
													if($chkmsg){$msgid = $obj->editMsg($tw[$i][$j],$chkmsg,$chktw,$bookid);}
													else{$msgid = $obj->addMsg($tw[$i][$j],$chktw,$bookid,$status);}
													if(!$msgid){$chkindivi=0;}
													else{
														if($chkmsg){$msgcondition .= " multmsg_id!=$chkmsg ";}
														else{$msgcondition .= " multmsg_id!=$msgid ";}
													}
													if($j<$msgcount[$i]-1){$msgcondition .= "and";}
											}
											$msgcondition .= ")";
											$msgcondition=($msgcondition=="and ()")?false:$msgcondition;
											if($msgcount[$i]<=$multmsgrows){
												$obj->delMsg($chktw,$msgcondition);
											}
										}
										$condition .= " indivi_id!=$chktw ";
									}else{
										$twid = $obj->addIndivi($tw[$i],$bookid,$status);
										if(!$twid){$chkindivi=0;}
										else{
											for($j=0;$j<$thcount[$i];$j++){
													$thid = $obj->addTh($tw[$i][$j],$twid,$bookid,$status);
													if(!$thid){$chkindivi=0;}
											}
											for($j=0;$j<$msgcount[$i];$j++){
													$msgid = $obj->addMsg($tw[$i][$j],$twid,$bookid,$status);
													if(!$msgid){$chkindivi=0;}
											}
										}
										$condition .= " indivi_id!=$twid ";
									}
								}
								if($i<$cs["ttpp"]-1){$condition .= "and";}
							}
							$condition .= ")";
							if($cs["ttpp"]<=$indivirows){
								$obj->delIndivi($bookid,$condition);
								$obj->delTh(false,"book_id=$bookid $condition");
								$obj->delMsg(false,"book_id=$bookid $condition");
							}
							
						}else{$obj->setErrorMsg("Can't insert $commentid to ad_comment");}
						
					}else{
						if(!$ccid)
							$obj->setErrorMsg("Can't update $ccid to ac_cancal");
						if(!$trfid)
							$obj->setErrorMsg("Can't update $trfid to ab_transfer");
					}
					
					if($ccid&&$trfid&&(str_replace(' ','',$cs["comment"])==''||$commentid)&&$chkindivi){
						$tmpSrd=$obj->editSaleReceipt($srd,$bookid,0,$object->getUserIdLogin(),$mpdcount);
						if($tmpSrd !="noValue"){
							$srd=$tmpSrd;
						}
						$srcount=count($srd);
						if($srcount==0){
							$srcount=1;
						}
						if($tmpSrd!="noValue"){
						// edit BP = Booking party
							$bpid = $obj->editBP($cs["cms"],$bookid,$subtotal);
							if($bpid){
								$apptId=$obj->editAppoiontment($bookid,$cs,$trf,$tw,$cc["cc"]);
								if($apptId){
									$successmsg = "Update Success!!";
									?>
										<script language="javascript">
											//alert("Update Booking Success!!");
											//window.location.href="manage_booking.php?chkpage=1&bookid=<?=$bookid?>&successmsg=<?=$successmsg?>&chkpage=<?=$chkpage?>&giftChk=<?=$giftchk?>";
											//Remove repeat chkpage when click update. By Ruck : 13-06-2009
											window.location.href="manage_booking.php?bookid=<?=$bookid?>&successmsg=<?=$successmsg?>&chkpage=<?=$chkpage?>&giftChk=<?=$giftchk?>";
											//window.opener.history.go(0);
											//window.close();
										</script>
									<?
								}
							}else{
								$errormsg = $obj->getErrorMsg();
							}
						}else{
							$errormsg = $obj->getErrorMsg();
						}
					}else{
						$errormsg = $obj->getErrorMsg();
					}
					$cs["comment"] = "";
				} else {
					$errormsg = $obj->getErrorMsg();
				}
			} else {
				$errormsg = $obj->getErrorMsg();
			}
		}else {
			
			$errormsg = "";
			$chkPeopleInroom = $obj->checkPeopleInroom($tw);
			$errdate = false;
			if($cs["hidden_apptdate"]<$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"Ymd",$cs["branch"])){
				$errdate = true;
				$obj->setErrorMsg("Please change appointment date to future or today!!");
			}
			if($chkPeopleInroom&&$errdate==false){
				$checkRm = 0;$err_rm='';
				$checkRoom = 0;$err_room='';
				$cs["tthour"] = 0;
				//check room is empty or not
				for($i=0;$i<$cs["ttpp"];$i++){
					$max_hour[$i]=0;
					for($j=0;$j<$thcount[$i];$j++){
						if($max_hour[$i]==0 || $hourperiod[$max_hour[$i]]<$hourperiod[$tw[$i][$j]["hour"]]){$max_hour[$i]=$tw[$i][$j]["hour"];}
					}
					$tw[$i]["hour_id"] = $max_hour[$i];
					if($cs["tthour"]==0  || $hourperiod[$cs["tthour"]] < $hourperiod[$tw[$i]["tthour"]]){$cs["tthour"]=$tw[$i]["tthour"];}
				}
				for($i=0;$i<$cs["ttpp"];$i++){
					$chkroom = $obj->checkEmptyRoom($cs["hidden_apptdate"],$cs["appttime"],$tw[$i]["tthour"],$tw[$i]["room"],$bookid);
					if($chkroom){
						if($checkRm>0){$err_rm.=', ';}
						$err_rm.=$obj->getIdToText($tw[$i]["room"],"bl_room","room_name","room_id");
						$checkRm++;
					}
					if($hourperiod[$tw[$i]["tthour"]]<$hourperiod[$max_hour[$i]]){
						if($checkRoom>0){$err_room.=', ';}
						$err_room.=$obj->getIdToText($tw[$i]["room"],"bl_room","room_name","room_id");
						$checkRoom++;
					}
				}
				$err_rmarr=explode(',',$err_rm); $err_rmarr=array_unique($err_rmarr); $err_rm=implode(',',$err_rmarr);
				if($checkRm>0){
					$chkRoom=false;
					$obj->setErrorMsg("Please check Hour & Time Appointment in room: $err_rm");
				}else{$chkRoom=true;}
				$tmp=explode(',',$err_room); $tmp=array_unique($tmp); $err_room=implode(',',$tmp);
				if($checkRoom>0&&$chkRoom==true){
						$chkRoom=false;
						$obj->setErrorMsg("Please check therapist hour in room: $err_room");
				}
				//$chkRoom=false; // chk state
				//check therapist time
				/*if($chkRoom){
					for($i=0;$i<$cs["ttpp"];$i++){
						for($j=0;$j<$thcount[$i];$j++){
							$checkTherapistTime = false;
							$checkTherapistTime = $obj->checkTherapistTime($cs["apptdate"],$tw[$i][$j]["name"],$cs["appttime"],$tw[$i][$j]["hour"]);
							if($checkTherapistTime){
								if($checkTh>0){$err_th.=', ';}
								$err_th.=$obj->getIdToText($checkTherapistTime,"l_employee","emp_nickname","emp_id");
								$checkTh++;
							}
						}
					}	
					if($checkTh>0){$chkPeople=false;$obj->setErrorMsg("Please check Therapist Time: $err_th");}
					else{$chkPeople=true;}
				}*/
				//echo "Error:".$obj->getErrorMsg();
				//echo $chkPeople;$chkRoom=false;
				//edit therapist
				if($chkRoom){
					$addid = $obj->add($cs,$cc["cc"],$cs["cms"],$trf["trf"],false);
					if($addid){
						$ccid = $obj->editcc($cc,$addid);
						$trfid = $obj->edittrf($trf,$addid);
						if($ccid&&$trfid){
							if((isset($cs["comment"])&&str_replace(' ','',$cs["comment"])!='')){
								$commentid = $obj->addcomment($cs["comment"],$addid);
							}
							
							$chkindivi = 1;
							if((str_replace(' ','',$cs["comment"])==''||$commentid)){
								for($i=0;$i<$cs["ttpp"];$i++){
									if($chkindivi==1){
										$twid = $obj->addIndivi($tw[$i],$addid,$status);
										if(!$twid){$chkindivi=0;}
										else{
											for($j=0;$j<$thcount[$i];$j++){
													$thid = $obj->addTh($tw[$i][$j],$twid,$addid,$status);
													if(!$thid){$chkindivi=0;}
											}
											for($j=0;$j<$msgcount[$i];$j++){
													$msgid = $obj->addMsg($tw[$i][$j],$twid,$addid,$status);
													if(!$msgid){$chkindivi=0;}
											}
										}
									}
								}
							}else{$obj->setErrorMsg("Can't insert $commentid to ad_comment");}
							
							if($chkindivi){
								$chkeditgift = $obj->editgift($queueGift,$addid);
							}
							
						}else{
							if(!$ccid){
								$obj->setErrorMsg("Can't insert $ccid to ac_cancal");
							}
							if(!$trfid){
								$obj->setErrorMsg("Can't insert $trfid to ab_transfer");
							}
						}
						if($ccid&&$trfid&&(str_replace(' ','',$cs["comment"])==''||$commentid)&&$chkindivi&&$chkeditgift){
							//echo "Do edit sale receipt<br>";
							$tmpSrd=$obj->editSaleReceipt($srd,$addid,0,$object->getUserIdLogin(),$mpdcount);
							if($tmpSrd !="noValue"){
							$srd=$tmpSrd;
							}
							//echo $tmpSrd;
							$srcount=count($srd);
							if($srcount==0){
								$srcount=1;
							}
							if($tmpSrd!="noValue"){
								$bpid = $obj->addBP($cs["cms"],$addid,$subtotal);
								if($bpid){
									$apptId=$obj->addAppoiontment($addid,$cs,$trf,$tw,$cc["cc"]);
									if($apptId){
										?>
											<script language="javascript">
												//alert("Add Booking Success!!");
												//window.location.href="manage_booking.php?chkpage=1&bookid=<?=$addid?>";
												//window.opener.history.go(0);
												window.close();
											</script>
										<?
									}
								}else{
									$errormsg = $obj->getErrorMsg();
								}
							}else{
								$errormsg = $obj->getErrorMsg();
							}
						}else{
							$errormsg = $obj->getErrorMsg();
						}
						
						$cs["comment"] = "";
					}else{
						$errormsg = $obj->getErrorMsg();
					}
					if($errormsg!=""){
						$obj->delAll($addid);
					}
				} else {
					$errormsg = $obj->getErrorMsg();
				}
			} else {
				$errormsg = $obj->getErrorMsg();
			}
		}
}
// #######################  End Insert/Update Database #######################
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Booking Manager</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script src="scripts/ajax.js" type="text/javascript"></script>
<script src="scripts/component.js" type="text/javascript"></script>
<script src="scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="scripts/datechooser/datechooser.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="scripts/datechooser/datechooser.css">

<!--[if IE]>
<style>
fieldset td.ccspecific select.ctrDropDown{
    width:70px;
    font-size:11px;
}
fieldset td.ccspecific select.ctrDropDownClick{
    font-size:11px;

    width:auto;

}
fieldset td.ccspecific select.plainDropDown{
    width:70px;
    font-size:11px;
}

fieldset td.ccinfo select.ctrDropDown{
    width:100px;
    font-size:11px;
}
fieldset td.ccinfo select.ctrDropDownClick{
    font-size:11px;

    width:auto;

}
fieldset td.ccinfo select.plainDropDown{
    width:100px;
    font-size:11px;
}

td.fix select.ctrDropDown{
    width:115px;
    font-size:11px;
}
td.fix select.ctrDropDownClick{
    font-size:11px;

    width:auto;

}
td.fix select.plainDropDown{
    width:115px;
    font-size:11px;
}
</style>
<![endif]-->

</head>
<body style="background-color: #eae8e8;" onLoad="document.getElementById('loading').style.display='none';">
<div id="loading" style="position:absolute;left:0px; top:0px; background-color: #FFFFFF;  width:100%; height:100%; text-align:center; filter: alpha(opacity=80);opacity:0.70;display:block;z-index:1;">
<table cellspacing="0" cellpadding="0" style="position:absolute;left:40%;top: 40%;">
<tr>
    <td align="center" rowspan="4" style="border:4px solid #4588bf;padding: 5 5 5 5;">
Loading.. <br/><img src="/images/pre-loader.gif" border=0>
	</td>
</tr>
</table>
</div> 
<form name='appt' id='appt' action='<?=$pagename?>' method='post'>
  <table width="100%" border="0px">
    <tr>
      <td class="header" style="padding-bottom:5px">
     <? if($status=="add"){?>
      		  <b>Add New Booking</b>
      <? }else{?>
		      <b>BOOKING ID: </b><b class="style1"><?=$obj->getIdToText($bookid,"c_bpds_link","bpds_id","tb_id","tb_name=\"a_bookinginfo\"",false)?></b>
		      <input type="hidden" id="bookid" name="bookid" value="<?=$_REQUEST["bookid"]?>"/>
		      <? $refid=array();
        		$refid=explode(",",$obj->getIdToText($bookid,"c_bpds_link","ref_id","tb_id","tb_name=\"a_bookinginfo\""));
        		if(count($refid)>=1&&$refid[0]!=""){ ?>
			      	,<br/> Refer to Booking ID: 
	        	<? 	for($i=0;$i<count($refid);$i++){ 
	        			$tbid = $obj->getIdToText($refid[$i],"c_bpds_link","tb_id","bpds_id");
	        			$tbname = $obj->getIdToText($refid[$i],"c_bpds_link","tb_name","bpds_id");
	        			if($i){echo ", ";}
	        			if($tbname=="a_bookinginfo"){ 
	        				$cancel = $obj->getIdToText($tbid,"a_bookinginfo","b_set_cancel","book_id");
	        				$data=$refid[$i];
	        				if($cancel){$data="<del class=\"style1\">".$refid[$i]."</del>";}
	        				?>
		        			<a href='javascript:;;' onClick="newwindow('manage_booking.php?chkpage=1&bookid=<?=$tbid?>','manageBooking<?=$tbid?>')" class="style1">
		        			<?=$data?></a>
	        			<? } else { 
	        				$cancel = $obj->getIdToText($tbid,"c_saleproduct","set_cancel","pds_id");
	        				$data=$refid[$i];
	        				if($cancel){$data="<del class=\"style1\">".$refid[$i]."</del>";}
	        				?>
	        				<a href='javascript:;;' onClick="newwindow('manage_pdforsale.php?pdsid=<?=$tbid?>','managePds<?=$tbid?>')" class="style1">
	        				<?=$data?></a>
	        			<? } ?>
	        	<?	} ?>
	        <? } ?>
      <? }?>
      </td>
      <td class="header" style="padding-left:0px">
      <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
            <td style="padding-left:12px;border-bottom:2px solid #ffffff;">
            <b>Reservation 
            <? if($status=="edit"){?>
            		by:&nbsp;</b><b class="style1"><?=$obj->checkParameter($obj->getIdToText($cs["insertuser"],"s_user","u","u_id"),"- ")?></b>
            		<? // get add date and add time             
              $d = substr($cs["insertdate"],0,10);
              $t = substr($cs["insertdate"],11,8);
                                         
              $data = $dateobj->timezone_depend_branch($d,$t,"$ldateformat, H:i:s",$cs["branch"]);
             ?>
					<b><?=$data?></b>
            		<input type="hidden" id="cs[insertuser]" name="cs[insertuser]" value="<?=$cs["insertuser"]?>"/>
            		<input type="hidden" id="cs[insertdate]" name="cs[insertdate]" value="<?=$cs["insertdate"]?>"/>
            <? 
            	}else{
            		$data = $dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$ldateformat, H:i:s","$cs[branch]");
            		echo "by: <b class=\"style1\">".$obj->getIdToText($obj->getUserIdLogin(),"s_user","u","u_id")."</b> <b>".$data;
            	}
            ?>
            </b>
            </td>
            <td align="right" style="border-bottom:2px solid #ffffff;"><span class="tabmenuheader" style="margin-right:20ox">
             <? if($chkPageEdit){?>
              <input type="submit" id="add" name="add" value=" <?=($status=="edit")?"Update":"Save"?> " class="button" onClick="chkMinus(<?=$srcount?>);" />
              <input type="submit" name="close" value=" Close " class="button" <? if($status=="edit"){ echo $status; ?>onclick="this.form.submit()"<?}else{?>onclick="window.close()"<? }?> />
             <? }
             
				if($status=="edit"){
					$log_viewchk = $obj->getIdToText($userId,"s_userpermission","log_viewchk","user_id");
					if($log_viewchk==1){
			?>
             	<input type="button" id="log_viewchk" name="log_viewchk" value="CHECK LOG" class="button" onClick="window.open('booklog.php?pageid=1&book_id=<?=$bookid?>','bookinglog<?=$bookid?>','location=0,toolbar=0,directoris=0,status=0,menubar=0,scrollbars=1,resizable=0')" />
            		<? }?> 
             <? }?> 
              &nbsp;
              </span> </td>
<? 
$chkpage = $obj->getParameter("chkpage",1);
?>
            <td align="right" style="border-bottom:2px solid #ffffff;"><table border="0" height="18px" cellpadding="0" cellspacing="0" width="100%" >
                <tr>
                	<td align="center" bgcolor="#d6dff7" >
                        <span id="tabs">
                                <ul>
	                                <!-- CSS Tabs -->
									<li id="tabone" <?=($chkpage==1)?"class=\"current\"":""?>><a href="javascript:;" onClick="chkPage(1);"><span>Customer Information</span></a></li>
									<li id="tabtwo" <?=($chkpage==2)?"class=\"current\"":""?>><a href="javascript:;" onClick="chkPage(2);"><span>Treatment Information</span></a></li>
									<li id="tabthree" <?=($chkpage==3)?"class=\"current\"":""?>><a href="javascript:;" onClick="chkPage(3);"><span>Sales Receipt</span></a></li>
                                </ul>
                      </span>     
               	  </td>
                </tr>
              </table></td>
        </table>
        <table width="100%" height="20" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td class="tabmenuheader">
            <? if($status=="edit"){?>
            	<b>Last Update by: </b>
            	<b class="style1"><?=$obj->checkParameter($obj->getIdToText($cs["lastupdateuser"],"s_user","u","u_id"),"- ")?>
            	<input type="hidden" id="cs[lastupdateuser]" name="cs[lastupdateuser]" value="<?=$cs["lastupdateuser"]?>"/>
            	<input type="hidden" id="cs[lastupdatedate]" name="cs[lastupdatedate]" value="<?=$cs["lastupdatedate"]?>"/>
            	</b>
            		<? 
				// get add date and add time             
              $d = substr($cs["lastupdatedate"],0,10);
              $t = substr($cs["lastupdatedate"],11,8);
                                          
              $data = $dateobj->timezone_depend_branch($d,$t,"$ldateformat, H:i:s",$cs["branch"]);
             ?>
					<b><?=$data?></b>
            <? }?>&nbsp;
            </td>
            <td class="tabmenuheader" align="right" style="margin-right:20px">
            <b><span id="errormsg" class="style1"><?=$errormsg?></span></b>
              <input type="hidden" id="status" name="status" value="<?=$status?>"/>
              <input type="hidden" id="chkpage" name="chkpage" value="<?=$chkpage?>"/>
            <span id="successmsg" class="style3" style="display: block;"><b class="style3"><?=($newLogin)?"":$successmsg?></b></span>
            </td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td colspan="2"><div id="custinfo" <? if($chkpage!=1){?>style="display:none;"<? } ?>>
          <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td class="content"><div class="group1">
                  <fieldset>
                  <legend><b>Booking Information</b></legend>
                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <td valign="bottom"><div>
                          <table cellspacing="0" cellpadding="0" class="cusinfo">
                            <tr>
                              <td>Branch:</td>
                              <td class="cc"><?=$obj->makeListbox("cs[branch]","bl_branchinfo","branch_name","branch_id",$cs["branch"],true,"branch_name","branch_active","1","branch_name not like 'All'",false,false,!$chkPageEdit,$cs["branch"])?></td>
                              <td>Total People: <span class="style1">*</span>  </td>
                              <td class="cc"><input type="text" value="<?=$cs["ttpp"]?>" maxlength="2" id="cs[ttpp]" name="cs[ttpp]" style="width:20px" onChange="checkNum(this);this.form.submit();"/>
                                <span class="style1">&nbsp;&nbsp;(Number only)</span>
                                <? if($status=="add"){ ?><input type="hidden" id="chkttpp" name="chkttpp" value="<?=$chkttpp?>"/><? } ?>
                              </td>
                            </tr>
                            <tr>
                              <td>Appointment Date: <span class="style1">*</span> </td>
                              <td class="cc"><input id='cs[apptdate]' name='cs[apptdate]' value="<?=$cs["apptdate"]?>" readonly="1" class="textbox" type="text" style="width:85px"/>
                              <input id='cs[hidden_apptdate]' name='cs[hidden_apptdate]' value="<?=$cs["hidden_apptdate"]?>" type="hidden"/>
                                &nbsp;&nbsp;<img src="scripts/datechooser/calendar.gif" alt="Date Appointment" onClick="showChooser(this, 'cs[apptdate]', 'date_showSpan', 1900, 2100, '<?=$sdateformat?>', false,<?=$checkApptPage?>,'<?=$preEditDate?>','<?=$afterEditDate?>');" />
                                <div id="date_showSpan" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div></td>
                              <td>Total Hours: </td>
                              <td class="cc">
                              	<?=$obj->makeListbox("cs[tthour$status]","l_hour","hour_name","hour_id",$cs["tthour"],0,"hour_name",0,0,"hour_id>1 and hour_name >= \"00:30:00\"")?>
              					<? if($status=="add"){ ?><input type="hidden" id="chktthour" name="chktthour" value="<?=$chktthour?>"/><? } ?>
                              </td>
                            </tr>
                            <tr>
                              <td>Appointment Time: <span class="style1">*</span></td>
                              <td class="cc"><?=$obj->makeListbox("cs[appttime$status]","p_timer","time_start","time_id",$cs["appttime"],1)?></td>
                              <? if($status=="add"){ ?><input type="hidden" id="chkappttime" name="chkappttime" value="<?=$chkappttime?>"/><? } ?>
                            	<td>Refer to Booking ID: </td>
                            	<td class="cc"><input type='text' name='cs[refid]' id='cs[refid]' value="0" size='22' /></td>
                            </tr>
                          </table>
                        </div></td>
                    </tr>
                  </table>
                  </fieldset>
                </div></td>
              <td width="636" rowspan="5" class="comment"><input type="hidden" name="cs[atspa]" value="<?=$cs["atspa"]?>"/>
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="cusinfo">
              	<tr>
              	<?
              	// membercode in the booking
              	$membercode = $obj->getIdToText($bookid,"a_bookinginfo","a_member_code","book_id");
              	$chkmembercode = false;
              	if($membercode==$cs["memid"]){
              		$chkmembercode = true;
              	}
              	if($cs["memid"]&&$chkmembercode){
              		$membercategory=$obj->getIdToText($cs["memid"],"m_membership","category_id","member_code");
              		$membertype=$obj->getIdToText($membercategory,"mb_category","category_name","category_id");
              	 }else{$membertype="";}?>
              		<td align="left"><span id="membercategory" class="membercategory"><?=$membertype?></span>&nbsp;</td>
                 <?// if($status=="edit" && $chkPageEdit){
                 ?>
                  	<td align="right">
                  	<input type="button" name="cs[copybookid]" id="cs[copybookid]" value=" Copy To New Booking " class="button" onClick="miniwindow('cpbooking.php?book_id='+<?=$bookid?>,'cpbooking','400','460','0','100','100','0')" />
                    <input type="button" name="cs[quick_search]" id="cs[quick_search]" value=" Quick Search " class="button" onClick="miniwindow('quick_search.php?qstatus=1','quick_search','470','1020','0','100','100','0')" />
                  	</td>
	              <?// }
	              ?>
              	</tr>
              </table>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><br/>
                      <div class="group3">
                        <fieldset>
                        <legend><b>Reservation comment</b></legend>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                          <tr>
                            <td valign="bottom"><div class="group4">
                                <table cellspacing="0" cellpadding="0" width="100%" border="0" class="cusinfo">
                                  <tr>
                                    <td width="90px" style="vertical-align:top">Booking Comment:</td>
                                    <td><textarea id='cs[comment]' name='cs[comment]' rows='3' class="bcomment"><?=(isset($cs["comment"]))?$cs["comment"]:""?></textarea></td>
                                  </tr>
                                </table>
                                <? if($status=="edit"){?>
                                <div class="comment">
                                  <table border="0" cellspacing="0" cellpadding="0" class="comment" style="width:100%">
                                    <tr>
                                      <td height="20" width="50" class="mainthead">Agent</td>
                                      <td height="20" class="mainthead">Comments</td>
                                    </tr>
                                    <? for($i=0;$i<$rs_comment["rows"];$i++){
                                    		$trclass = ($i%2==0)?"content_list":"content_list1";
											list($date,$time) = explode(" ",$rs_comment[$i]["l_lu_date"]);
											$commenttime = split(' ', $dateobj->timezone_depend_branch($date,$time,"$sdateformat H:i:s",$cs["branch"]));	
                                    ?>
                                    <tr class='<?=$trclass?>'>
                                      <td style="vertical-align:top;">
                                      	<?=$commenttime[0]?><br/>
                                    	<?=$commenttime[1]?><br/>
                                   		<?=$obj->getIdToText($rs_comment[$i]["l_lu_user"],"s_user","u","u_id")?></td>
                                      <td style="vertical-align:top;width:300px;"><?=str_replace("\n","<br/>",$rs_comment[$i]["comments"])?></td>
                                    </tr>
                                    <? }?>
                                  </table>
                                </div>
                                <br/>
                              </div>
							  <? }else{echo "<br/>";}?></td>
                          </tr>
                        </table>
                        </fieldset>
                      </div></td>
                  </tr>
                  <tr>
                    <td><br/><br/>
              <? if($status=="edit" && $cc["cc"]){?>
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="cusinfo">
              	<tr>
              		<td align="center"><span class="cancelbooking">CANCEL BOOKING</span>&nbsp;</td>
              	</tr>
              </table>
              <? }?>
              &nbsp;</td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td class="content"><div class="group2">
                <fieldset>
                <legend><b>Customer Booking Information</b></legend>
                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                    <td valign="bottom"><div>
                        <table cellspacing="0" cellpadding="0" width="97%" class="cusinfo">
                          <tr>
                            <td>Customer Name: <span class="style1">*</span></td>
                            <td class="cc"><input type='text' name='cs[name]' id='cs[name]' value="<?=$cs["name"]?>" size='22' /></td>
                            <td>Member:</td>
                            <td class="cc"><input type="text" name="cs[memid]" id="cs[memid]" value="<?=$cs["memid"]?>" maxlength="5" size="9"  style="width:60px;" onKeyUp="changeMemberButton(<?=$membercode?>)"/>
                                <input type="button" name="b_mhistory" id="b_mhistory" value="<? if(is_numeric($cs["memid"]) && $cs["memid"]>0){echo "History";}else{echo "Search";}?>" class="button" onClick="open_memberdetail(<?=$membercode?>)" title="<? if(is_numeric($cs["memid"]) && $cs["memid"]>0){echo "Member History";}else{echo "Member Search";}?>" /></td>
                          </tr>
                          <tr>
                            <td>Customer Hotel: </td>
                            <? $cityid=$obj->getIdToText($cs["branch"],"bl_branchinfo","city_id","branch_id");?>
                            <td class="ccinfo">         
                            
                            <span style="width: 100px;font-family:Tahoma; font-size: 11px;overflow:hidden;">
                            <?=$obj->makeListbox("cs[hotel]","al_accomodations","acc_name","acc_id",$cs["hotel"],false,"acc_name","acc_active",1,"city_id=$cityid","acc_id=1",false,false,false,"this.className='ctrDropDown'")?>
                            </span>
                            
                             <a href="javascript:;" onMouseOver="showinfo('cs[hotel]','accinfo.php','accinfo');" onMouseOut="document.getElementById('accinfo').style.display='none'">
                               <img src="/images/icon_information.gif" width="16px" height="16px" class="link"/></a>
                               <span id="accinfo" style="position:absolute;display:none;margin-top:-20px;margin-left:130px;z-index:1;"/>
                            </td>
                            <td>Room Number:</td>
                            <td class="cc"><input type='text' id='cs[roomnum]' name='cs[roomnum]' value='<?=$cs["roomnum"]?>' size='22' /></td>
                          </tr>
                          <tr>
                            <td>Phone Number: </td>
                            <td class="cc"><input type='text' name='cs[phone]' id='cs[phone]' value="<?=$cs["phone"]?>" size='22' onChange="checkCSphone(this)" /></td>
                            <td colspan="2"><input type="button" name="csphoneSearch" id="csphoneSearch" title="Search customer by phone number" value="<? if(is_numeric($cs["phone"]) && $cs["phone"]>0){echo "History";}else{echo "Search";}?>" class="button" href="javascript:;" onClick="if(document.getElementById('cs[phone]').value.trim()!=''){miniwindow('csinformation/cscheck.php?book_id='+<?=($bookid=="")?"0":$bookid?>+'&csphone='+document.getElementById('cs[phone]').value.replace('+','%2B'),'cscheck','400','700','0','100','100','0')}"/></td>
                          </tr>
                        </table>
                    </div></td>
                  </tr>
                </table>
                </fieldset>
              </div></td>
            </tr>
            <tr>
              <td class="content"><div class="group2">
                  <fieldset>
                  <legend><b>Booking Made By</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <td valign="bottom"><div>
                          <?
			              	// booking party phone number in the booking
			              	$bpphoneno = $obj->getIdToText($bookid,"a_bookinginfo","c_bp_phone","book_id");
			              ?>
                          <table cellspacing="0" cellpadding="0" border="0" width="100%" class="cusinfo">
                            <tr>
                              <td>B.P. Name:</td>
                              <td class="cc"><input type='text' size="22" value="<?=$cs["bpname"]?>" name="cs[bpname]" id="cs[bpname]" <?=$cmschk?>/></td>
                              <td colspan="2" class="cc">
                              &nbsp;&nbsp;&nbsp;B.P. PH #:&nbsp;&nbsp;
                              <input type='text' id='cs[bpphone]' name='cs[bpphone]' value="<?=$cs["bpphone"]?>" maxlength="15" style="width:75px;" onChange="checkBPphone(this,'<?=$bpphoneno?>')" <?=$cmschk?>/>
	                          <input type="button" name="bpCheck" id="bpCheck" value="<? if(is_numeric($cs["bpphone"]) && $cs["bpphone"]>0){echo "History";}else{echo "Search";}?>" class="button" href="javascript:;" 
	                          onClick="miniwindow('bookingparty/bpcheck.php?book_id='+<?=($bookid=="")?"0":$bookid?>+'&bpphone='+document.getElementById('cs[bpphone]').value.replace('+','%2B')+'&oldbpphone=<?=str_replace('+','%2B',$bpphoneno)?>&active='+<?=($cmschk==false)?"1":"0"?>,'bpcheck','400','700','0','100','100','0')" 
	                          title="<? if(is_numeric($cs["bpphone"]) && $cs["bpphone"]>0){echo "Book Company History";}else{echo "Check old book company";}?>"/>
	                          <? $chkphone=$obj->getIdToText($cs["bpphone"],"al_bankacc_cms","bankacc_cms_id","c_bp_phone","bankacc_active=1"); if($chkphone){ ?>&nbsp;&nbsp;<b class="style1">DDC</b><? } ?>
	                          </td>
                            </tr>
                            <tr>
                              <td>Booking Made By: <span class="style1">*</span></td>
                              <td class="ccinfo">
                              
                               <span style="width: 100px;font-family:Tahoma; font-size: 11px;overflow:hidden;">
                              <?=$obj->makeListbox("cs[bcompany]","al_bookparty","bp_name","bp_id",$cs["bcompany"],0,"bp_name","bp_active","1",0,"bp_id=1",$cmschk,false,false,"this.className='ctrDropDown'")?>
                               </span>
                              
                              <a href="javascript:;" onMouseOver="showinfo('cs[bcompany]','bpinfo.php','bpinfo');" onMouseOut="document.getElementById('bpinfo').style.display='none'">
                               <img src="/images/icon_information.gif" width="16px" height="16px" class="link"/></a>
                               <span id="bpinfo" style="position:absolute;display:none;margin-top:-20px;margin-left:130px;z-index:1;"/>
                              </td>
                              <td>Marketing Code:</td>
                              <td class="ccspecific" style="padding-bottom:10px;padding-top:5px;">
                              
                               <span style="width: 70px;font-family:Tahoma; font-size: 11px;overflow:hidden;">      
                              <?=$obj->makeListbox("cs[inspection]","l_marketingcode,l_mkcode_category","sign","mkcode_id",$cs["inspection"],0,"category_id,sign","active","1","l_marketingcode.category_id=l_mkcode_category.category_id",false,false,false,false,"this.className='ctrDropDown'")?>
                               </span>&nbsp;
                              
                              <input type="button" name="cfdCheck" id="cfdCheck" value="Browse" class="button" title="Browse All Code Free/Discount"
                              onClick="window.open('mkcode/index.php','cfdCheck','location=0,toolbar=0,directoris=0,status=0,menubar=0,scrollbars=1,resizable=0')">
                              </td>
                            </tr>
                            <tr>
                              <td colspan="4" style="padding-left:0px;padding-right:0px;padding-top:10px;border-top:solid 1px #d0d0d0"><table cellspacing="0" cellpadding="0" border="0" width="100%">
                                  <tr>
                                    <td width="40%"><input type="checkbox" value="checked" id="cs[cms]" name="cs[cms]" class="checkbox" <?=$cs["cms"]?> <?=$cmschk?> />
                                      Commission</td>
                                    <td width="25%">Commission Percent: <span class="style1">*</span></td>
                                    <td width="26%"><?=$obj->makeListbox("cs[cms_percent]","al_percent_cms","pcms_percent","pcms_id",$cs["cms_percent"],0,"pcms_percent","pcms_active","1",false,false,$cmschk)?></td>
                                  </tr>
                              </table></td>
                            </tr>
                          </table>
                      </div></td>
                    </tr>
                  </table>
                  </fieldset>
                </div></td>
            </tr>
            <tr>
              <td class="content"><div class="group2">
                  <fieldset>
                  <legend><b>Other Information</b></legend>
                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tbody>
                      <tr>
                        <td valign="bottom"><div>
                            <table cellspacing="0" width="97%" cellpadding="0" class="cusinfo">
                              <tr>
                                <td><input type='checkbox' id='trf[trf]' name='trf[trf]' onClick="showHideCheck('TFC','trf[trf]');" value='checked' <?=$trf["trf"]?> class="checkbox" />
                                  Transportation &nbsp;&nbsp;
                                  <? if($trf["trf"]) {?>
                                  <a href='javascript:;' class="worksheet" onClick="transferwindow(<?=(isset($bookid))?$bookid:$obj->getNextId("a_bookinginfo","book_id")?>,<?=$trf["pu_time"]?>,<?=$trf["tb_time"]?>);">(Print)</a>
                                  <? }?>
                                </td>
                              </tr>
                              <tr>
                                <td class="cc" style="padding-right:0px;">
                                	 <div id="TFC" name="TFC" <?=($trf["trf"])?"style=\"display:block\"":"style=\"display:none\""?>>  
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td>Pick Up Driver:</td>
                                        <td class="cc"><?=$obj->makeListbox("trf[dr_pu]","l_employee","emp_nickname","emp_id",$trf["dr_pu"],0,"emp_nickname","emp_active","1","emp_department_id=1","emp_id=1")?></td>
                                        <td>Place:</td>
                                        <td class="cc"><input type='text' id='trf[p_pu]' name='trf[p_pu]' value='<?=$trf["p_pu"]?>' size='22' /></td>
                                        <td class="style1" style="padding-left:0;padding-right:0;"><?=$obj->getIdToText($trf["pu_time"],"p_timer","time_start","time_id")?></td>
                                      </tr>
                                      <tr>
                                        <td height="25px">Take Back Driver:</td>
                                        <td class="cc"><?=$obj->makeListbox("trf[dr_tb]","l_employee","emp_nickname","emp_id",$trf["dr_tb"],0,"emp_nickname","emp_active","1","emp_department_id=1","emp_id=1")?></td>
                                        <td>Place:</td>
                                        <td class="cc"><input type='text' id='trf[p_tb]' name='trf[p_tb]' value='<?=$trf["p_tb"]?>' size='22' /></td>
                                        <td class="style1" style="padding-left:0;padding-right:0;"><?=$obj->getIdToText($trf["tb_time"],"p_timer","time_start","time_id")?></td>
                                      </tr>
                                    </table>
                                    </div>
                                </td>
                              </tr>
                              <tr>
                                <td><input type='checkbox' id='cc[cc]' name='cc[cc]' value='checked' onClick="showHideCheck('CBC','cc[cc]');" <?=$cc["cc"]?> class="checkbox" />
                                  Cancel Booking </td>
                              </tr>
							  <!-- // check cancel booking to use -->
                              <tr>
                                <td class="cc" style="padding-right:0px;">
                              		 <div id="CBC" name="CBC" <?=($cc["cc"])?"style=\"display:block\"":"style=\"display:none\""?>>  
                                    <table width="98%" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td width="27%">Date of cancellation:</td>
                                        <td width="29%" class="cc"><input id="cc[date]" name="cc[date]" value="<?=$cc["date"]?>" readonly="1" class="textbox" type="text" style="width:85px"/>
                              			  <input id='cc[hidden_date]' name='cc[hidden_date]' value="<?=$cc["hidden_date"]?>" type="hidden"/>
                                          &nbsp;&nbsp;<img src="scripts/datechooser/calendar.gif" alt="" onClick="showChooser(this, 'cc[date]', 'date_showSpan1', 1900, 2100, '<?=$sdateformat?>', false,false,'notCheck','notCheck');" />
                                          <div id="date_showSpan1" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div></td>
                                        <td width="15%" height="25px" class="cc">Reason for Cancellation:</td>
                                        <td width="25%"><input type='text' id='cc[comment]' name='cc[comment]' value="<?=$cc["comment"]?>" size='23' /></td>
                                      </tr>
                                    </table>
                             		</div>
                                  </td>
                              </tr>
                            </table>
                          </div></td>
                      </tr>
                    </tbody>
                  </table>
                  </fieldset>
                </div></td>
            </tr>
            <tr>
              <td class="content"><div class="group2">
                  <fieldset>
                  <legend><b>Reservation Agent</b></legend>
                  <table border="0" cellpadding="0" cellspacing="0" width="95%">
                    <tbody>
                      <tr>
                        <td valign="bottom"><div>
                            <table cellspacing="0" width="100%" cellpadding="0" class="cusinfo">
                              <tr>
                                <td width="23%">Reservation by:</td>
                                <td width="27%"><?=$obj->makeListbox("cs[rs]","l_employee","emp_nickname","emp_id",$cs["rs"],0,"emp_nickname","emp_active","1","emp_department_id=3","emp_id=1")?></td>
                                <td width="25%">Received by:</td>
                                <td width="25%" colspan="2"><?=$obj->makeListbox("cs[rc]","l_employee","emp_nickname","emp_id",$cs["rc"],0,"emp_nickname","emp_active","1","emp_department_id=3","emp_id=1")?></td>
                              </tr>
                            </table>
                          </div></td>
                      </tr>
                    </tbody>
                  </table>
                  </fieldset>
                </div></td>
            </tr>
          </table>
        </div></td>
    </tr>
    <tr>
      <td colspan="2"><div id="therainfo" <? if($chkpage!=2){?>style="display:none;"<? } ?>>
        <div class="group5" width="100%" >
          <fieldset>
          <legend><b>Treatment Information</b>&nbsp;&nbsp;
	         <input type='text' name='tw[phone]' id='tw[phone]' value="<?=(isset($tw["phone"]))?$tw["phone"]:""?>" size='22' onChange="checktwphoneSearch(this)" />&nbsp;&nbsp;
	         <input type="button" name="twphoneSearch"  title="<? if(is_numeric($tw["phone"]) && $tw["phone"]>0){echo "Customer History";}else{echo "Search customer by phone number";}?>" id="twphoneSearch" title="Search customer by phone number" value="<? if(is_numeric($tw["phone"]) && $tw["phone"]>0){echo "History";}else{echo "Search";}?>" class="button" href="javascript:;" onClick="miniwindow('csinformation/twcheck.php?book_id='+<?=($bookid=="")?"0":$bookid?>+'&twphone='+document.getElementById('tw[phone]').value.replace('+','%2B'),'cscheck','400','900','0','100','100','0')"/>
          </legend>
<?
$maxthcount = 0;
$maxmsgcount = 0;
for($i=0; $i<$cs["ttpp"]; $i++) {
	//For debug undefined offset. By Ruck : 19-05-2009
	if(!isset($thcount[$i])){$thcount[$i]=1;} 
	if(!isset($msgcount[$i])){$msgcount[$i]=1;}
	if($thcount[$i]>$maxthcount){$maxthcount=$thcount[$i];}
	if($msgcount[$i]>$maxmsgcount){$maxmsgcount=$msgcount[$i];}
}
?>
          <table border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td>
              <table width="100%" border="0" cellspacing="0" class="cusinfo" cellpadding="0">
                  <tr>
                    <td><b>Status</b></td>
                  </tr>
                  <tr>
                    <td><b>Customer Information</b>
                    <input type="hidden" name="cs[searchchk]" id="cs[searchchk]" value="<?=($cs["searchchk"]=="")?"0":$cs["searchchk"]?>"/></td>
                  </tr>
                  <tr>
                    <td>&nbsp;Name :</td>
                  </tr>
                  <tr>
                    <td>&nbsp;Phone :</td>
                  </tr>
                  <tr>
                    <td>&nbsp;E-mail :</td>
                  </tr>
                  <tr>
                    <td>&nbsp;Age :</td>
                  </tr>
                  <tr>
                    <td>&nbsp;Birthday :</td>
                  </tr>
                  <tr>
                    <td>Sex :</td>
                  </tr>
                  <tr>
                    <td>Nationality :</td>
                  </tr>
                  <tr>
                    <td>Resident :</td>
                  </tr>
                  <? /*if($status=="edit"){*/?>
                  <tr>
                    <td>Member :</td>
                  </tr>
                  <? /*}*/?>
                  <tr>
                    <td><b>Room/Package</b></td>
                  </tr>
                  <tr>
                    <td>Room Name</td>
                  </tr>
                  <tr>
                    <td>Package</td>
                  </tr>
                  <tr>
                    <td>Total Hour</td>
                  </tr>
                  <tr>
                    <td><b>Therapist</b></td>
                  </tr>
                  <tr>
                    <td>&nbsp;Therapist 1 </td>
                  </tr>
                  <tr>
                    <td>&nbsp;Hour 1 </td>
                  </tr>
                   <? for($a=1; $a<$maxthcount; $a++){echo "<tr><td>&nbsp;Therapist ".($a+1)."</td></tr>\n<tr><td>&nbsp;Hour ".($a+1)."</td></tr>";}?>
                  <tr>
                    <td>Additional Therapist</td>
                  </tr>
                  <tr>
                    <td><b>Treatment</b></td>
                  </tr>
                  <tr>
                    <td>&nbsp;Massage 1</td>
                  </tr>
                   <? for($a=1; $a<$maxmsgcount; $a++){echo "<tr><td>&nbsp;Massage ".($a+1)."</td></tr>";}?>
                  <tr>
                    <td>&nbsp;Additional Massage</td>
                  </tr>
                  <tr>
                    <td>&nbsp;Strength</td>
                  </tr>
                  <tr>
                    <td>&nbsp;Scrub</td>
                  </tr>
                  <tr>
                    <td>&nbsp;Wrap</td>
                  </tr>
                  <tr>
                    <td>&nbsp;Bath</td>
                  </tr>
                  <tr>
                    <td>&nbsp;Facial</td>
                  </tr>
                  <tr>
                    <td>Steam</td>
                  </tr>
                  <tr>
                    <td>&nbsp;Comment</td>
                  </tr>
                  <tr>
                    <td><b>Therapist working sheet</b></td>
                  </tr>
                  <? if($status=="edit" or $status=="add"){?>
                  <tr>
                    <td><b>CSI</b></td>
                  </tr>
                  <? }?>
              </table></td>
<? 
if($cs["ttpp"]>0) {
		for($i=0; $i<$cs["ttpp"]; $i++) { 
			if(!isset($tw[$i]["member_use"])){$tw[$i]["member_use"]="";}
			if(!isset($tw[$i]["hour"])){$tw[$i]["hour"]="";}
			if(!isset($tw[$i]["tthour"])){$tw[$i]["tthour"]="";}
			if(!isset($tw[$i]["cusstate"])){$tw[$i]["cusstate"]="";}
			if(!isset($tw[$i]["id"])){$tw[$i]["id"]="";}
			if(!isset($tw[$i]["csnameinroom"])){$tw[$i]["csnameinroom"]="";}
			if(!isset($tw[$i]["csphoneinroom"])){$tw[$i]["csphoneinroom"]="";}
			if(!isset($tw[$i]["csemail"])){$tw[$i]["csemail"]="";}
			if(!isset($tw[$i]["csageinroom"])){$tw[$i]["csageinroom"]="";}
			if(!isset($tw[$i]["sex"])){$tw[$i]["sex"]=0;}
			if(!isset($tw[$i]["national"])){$tw[$i]["national"]=0;}
			if(!isset($tw[$i]["resident"])){$tw[$i]["resident"]="noset";}
			if(!isset($tw[$i]["room"])){$tw[$i]["room"]="";}
			if(!isset($tw[$i]["stream"])){$tw[$i]["stream"]="";}
			if(!isset($tw[$i]["strength"])){$tw[$i]["strength"]="";}
			if(!isset($tw[$i]["hidden_csbday"])){$tw[$i]["hidden_csbday"]="0000-00-00";}
			if(!isset($tw[$i]["csbday"])){$tw[$i]["csbday"]="";}
			if(!isset($tw[$i]["scrub"])){$tw[$i]["scrub"]="";}
			if(!isset($tw[$i]["wrap"])){$tw[$i]["wrap"]="";}
			if(!isset($tw[$i]["bath"])){$tw[$i]["bath"]="";}
			if(!isset($tw[$i]["facial"])){$tw[$i]["facial"]="";}
			if(!isset($tw[$i]["comments"])){$tw[$i]["comments"]="";}
			if(!isset($tw[$i]["package"])){$tw[$i]["package"]="";}
			
			if($tw[$i]["hour"]<=1) {
				if($cs["tthour"]<=1)
					$tw[$i]["hour"]=4;
				else
					$tw[$i]["hour"] = $cs["tthour"];
			}
			$thcount[$i] = $obj->checkParameter($thcount[$i],1);
			$msgcount[$i] = $obj->checkParameter($msgcount[$i],1);
?>
              <td><table width="100%" border="0" cellspacing="0" class="cusinfo" cellpadding="0">
                  <tr>
                    <td><select id="tw[<?=$i?>][cusstate]" name="tw[<?=$i?>][cusstate]">
                    	<option title="" value=""></option>
                    	<option title="At spa" value="c_set_atspa" <? if($tw[$i]['cusstate']=="c_set_atspa"){?>selected="selected"<? }?>>At spa</option>
                    	<option title="In room" value="c_set_inroom" <? if($tw[$i]['cusstate']=="c_set_inroom"){?>selected="selected"<? }?>>In room</option>
                    	<option title="Finished" value="c_set_finish" <? if($tw[$i]['cusstate']=="c_set_finish"){?>selected="selected"<? }?>>Finished</option></select></td>
                  </tr>
                  <tr>
                    <td align="center"><b><?=$i+1?>
                      <input type='hidden' name='thcount[<?=$i?>]' id='thcount[<?=$i?>]' value='<?=$thcount[$i]?>' />
                      <input type='hidden' name='msgcount[<?=$i?>]' id='msgcount[<?=$i?>]' value='<?=$msgcount[$i]?>' />
                      <input type='hidden' name='tw[<?=$i?>][id]' id='tw[<?=$i?>][id]' value='<?=$tw[$i]["id"]?>' />
                    </b></td>
                  </tr>
                  <tr>
                    <td><input type='text' id='tw[<?=$i?>][csnameinroom]' name='tw[<?=$i?>][csnameinroom]' value='<?=$tw[$i]["csnameinroom"]?>' size='17' /></td>
                  </tr>
                  <tr>
                    <td><input type='text' id='tw[<?=$i?>][csphoneinroom]' name='tw[<?=$i?>][csphoneinroom]' value='<?=$tw[$i]["csphoneinroom"]?>' size='17' onChange="checkTWphone(this)"  style="width:85px"/>
                    <a href="javascript:;" onClick="miniwindow('csinformation/history_customer.php?csphone='+document.getElementById('tw[<?=$i?>][csphoneinroom]').value.replace('+','%2B'),'cshistory<?=$i?>','600','600')">
             	 	<img src="/images/icon_history.gif" width="16px" height="16px" class="link"/></a>
                    </td>
                  </tr>
                  <tr>
                    <td><input type='text' id='tw[<?=$i?>][csemail]' name='tw[<?=$i?>][csemail]' value='<?=$tw[$i]["csemail"]?>' size='17' onChange="checkEmail(this)" /></td>
                  </tr>
                  <tr>
                    <td><input type='text' id='tw[<?=$i?>][csageinroom]' name='tw[<?=$i?>][csageinroom]' value='<?=$tw[$i]["csageinroom"]?>' size='17' /></td>
                  </tr>
                  <tr>
                    <td>
                    <input id="tw[<?=$i?>][csbday]" name="tw[<?=$i?>][csbday]" value="<?=$tw[$i]["csbday"]?>" readonly="1" class="textbox" type="text" style="width:85px"/>
                    <input id="tw[<?=$i?>][hidden_csbday]" name="tw[<?=$i?>][hidden_csbday]" value="<?=$tw[$i]["hidden_csbday"]?>" type="hidden"/>
                    &nbsp;&nbsp;<img src="scripts/datechooser/calendar.gif" alt="" onClick="showChooser(this, 'tw[<?=$i?>][csbday]', 'tw[<?=$i?>][date_showspan]', 1900, 2100, '<?=$sdateformat?>', false,false,'notCheck','notCheck');" />
                  	<div id="tw[<?=$i?>][date_showspan]" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div></td>               
					</td>
                  </tr>
                  <tr>
                    <td><?=$obj->makeListbox("tw[$i][sex]","dl_sex","sex_type","sex_id",$tw[$i]["sex"],0,"sex_type")?></td>
                  </tr>
                  <tr>
                    <td><?=$obj->makeListbox("tw[$i][national]","dl_nationality","nationality_name","nationality_id",$tw[$i]["national"],0,"nationality_name","nationality_active",1,false,"nationality_id=1")?></td>
                  </tr>
                  <tr>
                    <td><select id="tw[<?=$i?>][resident]" name="tw[<?=$i?>][resident]">
                      <option title="" value="noset" <? if($tw[$i]["resident"]=="noset"){?>selected="selected"<? }?>></option>
                      <option title="Resident" value="resident" <? if($tw[$i]["resident"]=="resident"){?>selected="selected"<? }?>>Resident</option>
                      <option title="Visitor" value="visitor" <? if($tw[$i]["resident"]=="visitor"){?>selected="selected"<? }?>>Visitor</option>
                    </select></td>
                  </tr>
                  <? /*if($status=="edit"){*/?>
                  <tr>
                    <td><span class="b">
                      <input type="checkbox" id='tw[<?=$i?>][member_use]' name='tw[<?=$i?>][member_use]' value="checked" <?=$tw[$i]["member_use"]?> />
                    </span></td>
                  </tr>
                   <? /*}*/?>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><?=$obj->makeListbox("tw[$i][room]","bl_room","room_name","room_id",$tw[$i]["room"],0,"room_name","branch_id",$cs["branch"],"room_active=1 ")?></td>
                  </tr>
                  <tr>
                    <td><?=$obj->makeListbox("tw[$i][package]","db_package","package_name","package_id",$tw[$i]["package"],0,"package_name","package_active",1)?></td>
                  </tr>
                  <tr>
                    <td><?=$obj->makeListbox("tw[$i][tthour]","l_hour","hour_name","hour_id",$tw[$i]["tthour"],0,"hour_name",0,0,"hour_id>1 and hour_name >= \"00:30:00\"")?></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
<? for($j=0;$j<$thcount[$i];$j++) { 
	//For debug undefine index : cusstate. By Ruck : 19-05-2009
	if(!isset($tw[$i][$j]["thid"])){$tw[$i][$j]["thid"]="";}
	if(!isset($tw[$i][$j]["name"])){$tw[$i][$j]["name"]=1;}
	if(!isset($tw[$i][$j]["hour"])){$tw[$i][$j]["hour"]=2;}	
?>
                  <tr>
                    <td class="ccinfo" style="padding-left:5">&nbsp;
                      	<input type='hidden' name='tw[<?=$i?>][<?=$j?>][thid]' id='tw[<?=$i?>][<?=$j?>][thid]' value='<?=$tw[$i][$j]["thid"]?>' />
                        <?=$obj->makeTherapistlist("tw[$i][$j][name]",$tw[$i][$j]["name"],0,"l_employee.branch_id,l_employee.emp_code,l_employee.emp_nickname","bl_branchinfo.city_id=$cityid")?>
                         
                    </td>
                  </tr>
                  <tr>
                    <td><?=$obj->makeListbox("tw[$i][$j][hour]","l_hour","hour_name","hour_id",$tw[$i][$j]["hour"],0,"hour_name",0,0,"hour_id>1 and hour_name >= \"00:15:00\"")?></td>
                  </tr>
<? } ?>
                   <? for($k=0; $k<($maxthcount-$thcount[$i]); $k++){echo "<tr><td>&nbsp;</td></tr>\n<tr><td>&nbsp;</td></tr>";}?>
                  <tr>
                    <td align="center"><input type='button' value=' + ' class='button' onClick="addTh(<?=$i?>);this.form.submit();" /></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
<? for($j=0;$j<$msgcount[$i];$j++) {
	//For debug undefine index : . By Ruck : 18-05-2009	
	if(!isset($tw[$i][$j]["msgid"])){$tw[$i][$j]["msgid"]="";}
	if(!isset($tw[$i][$j]["msg"])){$tw[$i][$j]["msg"]=1;}
	
 ?>
                  <tr>
                    <td>
                      	<input type='hidden' name='tw[<?=$i?>][<?=$j?>][msgid]' id='tw[<?=$i?>][<?=$j?>][msgid]' value='<?=$tw[$i][$j]["msgid"]?>' />
                      	<?=$obj->makeListbox("tw[$i][$j][msg]","db_trm","trm_name","trm_id",$tw[$i][$j]["msg"],0,"trm_name","trm_active",1,"trm_category_id=3","trm_id=1")?>
                    </td>
                  </tr>
<? } ?>
                  <? for($k=0; $k<($maxmsgcount-$msgcount[$i]); $k++){echo "<tr><td>&nbsp;</td></tr>";}?>
                  <tr>
                    <td align="center"><input type='button' value=' + ' class='button' onClick="addMsg(<?=$i?>);this.form.submit();" /></td>
                  </tr>
                  <tr>
                    <td><?=$obj->makeListbox("tw[$i][strength]","l_strength","strength_type","strength_id",$tw[$i]["strength"],0,"strength_type")?></td>
                  </tr>
                  <tr>
                    <td><?=$obj->makeListbox("tw[$i][scrub]","db_trm","trm_name","trm_id",$tw[$i]["scrub"],0,"trm_name","trm_active",1,"trm_category_id=4","trm_id=1")?></td>
                  </tr>
                  <tr>
                    <td><?=$obj->makeListbox("tw[$i][wrap]","db_trm","trm_name","trm_id",$tw[$i]["wrap"],0,"trm_name","trm_active",1,"trm_category_id=5","trm_id=1")?></td>
                  </tr>
                  <tr>
                    <td><?=$obj->makeListbox("tw[$i][bath]","db_trm","trm_name","trm_id",$tw[$i]["bath"],0,"trm_name","trm_active",1,"trm_category_id=1","trm_id=1")?></td>
                  </tr>
                  <tr>
                    <td><?=$obj->makeListbox("tw[$i][facial]","db_trm","trm_name","trm_id",$tw[$i]["facial"],0,"trm_name","trm_active",1,"trm_category_id=2","trm_id=1")?></td>
                  </tr>
                  <tr>
                    <td><input type='checkbox' id="tw[<?=$i?>][stream]" name="tw[<?=$i?>][stream]" value='checked' <?=$tw[$i]["stream"]?> /></td>
                  </tr>
                  <tr>
                    <td><input type='text' id='tw[<?=$i?>][comments]' name='tw[<?=$i?>][comments]' value='<?=$tw[$i]["comments"]?>' size='17' /></td>
                  </tr>
                  <tr>
                    <td align="center"><a href='javascript:;' class="worksheet" onClick="if(check_twvalue(<?=$i?>,<?=$thcount[$i]?>)){therapistwindow(<?=($status=="edit")?$bookid:$obj->getNextId("a_bookinginfo","book_id")?>,<?=$i?>,<?=$thcount[$i]?>,<?=$msgcount[$i]?>,'tw<?=$i?>');}">&nbsp;Print</a></td>
                  </tr>
                  <? if($status=="edit" or $status=="add"){?>
                  <tr>
                    <td align="center"><a href="javascript:;;" onClick="if(check_csivalue(<?=$tw[$i]["id"]?>)){miniwindow('csi.php?book_id=<?=$bookid?>&indivi_id=<?=$tw[$i]["id"]?>','csi','700','600','1','13','20','0')}" class="worksheet">&nbsp;CSI</a></td>
                  </tr>
                  <? }?>
              </table></td>
              <? 		}
   }
?>
            </tr>
          </table>
            </fieldset>
        </div>
      </div></td>
    </tr>
    <tr>
      <td colspan="2"><div id="srinfo" <? if($chkpage!=3){?>style="display:none;"<? } ?>>
          <div class="group5" width="100%" >
            <fieldset>
            <legend><b>Treatment Information Conclusion</b></legend>
            <table cellspacing="0" cellpadding="0" width="100%">
              <tr>
                <td valign="middle" height="23px"> Total customer: <?=$cs["ttpp"]?> persons</td>
              </tr>
              <tr>
                <td align="center" style="padding-left:5px"><table cellspacing="0" cellpadding="0" width="100%" class="comment">
                    <tr>
                      <td class="sort">Customer</td>
                      <td class="mainthead">Room </td>
                      <td class="mainthead">Package </td>
                      <td class="mainthead">Massage 1</td>
                   <? for($a=1; $a<$maxmsgcount; $a++){echo "<td class=\"mainthead\">&nbsp;Massage ".($a+1)."</td>";}?>
                      <td class="mainthead">Bath </td>
                      <td class="mainthead">Facial </td>
                      <td class="mainthead">Scrub </td>
                      <td class="mainthead">Wrap </td>
                      <td class="mainthead">Stream </td>
                    </tr>
<? 
if($cs["ttpp"]>0) {
		for($i=0; $i<$cs["ttpp"]; $i++) { 
			if($tw[$i]["hour"]<=1) {
				if($cs["tthour"]<=1)
					$tw[$i]["hour"]=4;
				else
					$tw[$i]["hour"] = $cs["tthour"];
			}
			$tw[$i]["member_use"] = (isset($tw[$i]["member_use"]))?$tw[$i]["member_use"]:"";
			$thcount[$i] = $obj->checkParameter($thcount[$i],1);
			$msgcount[$i] = $obj->checkParameter($msgcount[$i],1);
			$class = ($i%2==0)?"content_list":"content_list1";
?>
                    <tr class='<?=$class?>'>
                    <?
                    if($chkmembercode&&$tw[$i]["member_use"] == "checked"){
                    	echo "<td>".($i+1).".<a href=\"javascript:;\" class=\"worksheet\" onClick=\"window.open('membership/manage_membershipinfo.php?memberId=".$cs["memid"]."&oldmemberId=".$cs["memid"]."','memberHistoryWindow',
						'location=0,toolbar=0,directoris=0,status=0,menubar=0,scrollbars=1,resizable=0,width=1020,height=700,left=500,top=100');\" >
                      ".$tw[$i]["csnameinroom"]."</a></td>";
                    }else{
                    	echo "<td>".($i+1).". ".$tw[$i]["csnameinroom"]."</td>";
                    }
                    ?>
                      <td><?=$obj->getIdToText($tw[$i]["room"],"bl_room","room_name","room_id")?></td>
                      <td><?=$obj->getIdToText($tw[$i]["package"],"db_package","package_name","package_id")?></td>
           <? for($j=0;$j<$msgcount[$i];$j++) {
           	// debugging all undified variable natt/16-05-2009 ?>
                      <td><?=isset($tw[$i][$j]["msg"])?$obj->getIdToText($tw[$i][$j]["msg"],"db_trm","trm_name","trm_id"):""?></td>
			<? } ?>
                  <? for($k=0; $k<($maxmsgcount-$msgcount[$i]); $k++){echo "<td>&nbsp;</td>\n";}?>
                      <td><?=$obj->getIdToText($tw[$i]["bath"],"db_trm","trm_name","trm_id")?></td>
                      <td><?=$obj->getIdToText($tw[$i]["facial"],"db_trm","trm_name","trm_id")?></td>
                      <td><?=$obj->getIdToText($tw[$i]["scrub"],"db_trm","trm_name","trm_id")?></td>
                      <td><?=$obj->getIdToText($tw[$i]["wrap"],"db_trm","trm_name","trm_id")?></td>
                      <td><?=($tw[$i]["stream"]=="checked")?"/":""?></td>
                    </tr>
<? 		}
} 
?>
                </table></td>
              </tr>
            </table>
            </fieldset>
          </div>
          <br/>
            <? 
             //######### Find gift certificate is sold by this id ################//
			$sql = "select gift_number,receive_from,value,product,gifttype_id,expired from g_gift where id_sold=$bookid and tb_name='a_bookinginfo'order by gift_number asc";
			$rsGift = $obj->getResult($sql);
			//echo "$sql";
			//###### End find gift certificate is sold by this id ###############//
          
          $giftNum=0;
          if($status=="edit"){
	          if($rsb!=0)
			  	$giftNum=$rsb["rows"];
          }else if($status=="add"){
          	for($i=0;$i<count($queueGift);$i++){
          		if(!isset($queueGift[$i])){$queueGift[$i]="";}
          		if($queueGift[$i]!="deleted"){
          			$giftNum++;
          		}
          	}
          	$giftNum-=1;
          }
          ?>
          <div class="group5" width="100%" >
            <fieldset>
            <legend><b>Gift Certificate Information</b></legend>
            <table cellspacing="0" cellpadding="0" width="100%">
              <tr>
                <td colspan="2" width="120" height="23px" align="left" valign="top" style="white-space: nowrap;">
                <input type="button" id="bgift" name="bgift" value="view" onClick="showhidegift('divGift',document.getElementById('giftChk').value);" />
                
                <input id="giftChk" name="giftChk" value="<?=$giftchk?>" type="hidden">
                &nbsp;&nbsp;Use gift certificate : <?=$giftNum?> &nbsp;&nbsp;&nbsp;&nbsp;Sold gift certificate : <?=($rsGift["rows"])?$rsGift["rows"]:"0"?> </td>
              </tr>
 		      <tr>
                <td colspan="2">
        <div id="divGift" name="divGift" <?=($giftchk)?"style=\"display:block\"":"style=\"display:none\""?>>
       <!-- table show Gift Detail -->
          <table cellpadding="0" cellspacing="0" class="cusinfo" width="100%">
            <tr >
             <td style=" vertical-align:top" width="35%">
                  <table cellpadding="0" cellspacing="0" class="cusinfo" width="100%">
                	<tr>
                      <td height="25px">Gift Number&nbsp;&nbsp;
                        <!--<input type="text" name="gift[gift_number]" id="gift[gift_number]" value="<?=$gift[$i]["gift_number"]?>" maxlength="10" size="10" style="width:40px" />&nbsp;&nbsp;-->
                      	<input type="text" name="gift[gift_number]" id="gift[gift_number]" value="" maxlength="10" size="10" style="width:40px" />&nbsp;&nbsp;
                      	<input type="submit" name="giftsearch" id="giftsearch" value=" Search Gift " class="button" />&nbsp;&nbsp;
                      
                    </td>
                    </tr>
                   <?
                  
                  $sub=0;
                    for($i=0;$i<count($queueGift);$i++){
          				if(!isset($queueGift[$i])){$queueGift[$i]="";}
                    	if($queueGift[$i]!="deleted"){
	                    	echo "<input type=\"hidden\" id=\"queueGift[".($i-$sub)."]\" name=\"queueGift[".($i-$sub)."]\"
	                 			 value=\"".$queueGift[$i]."\"/>\n";
							
                    	}else{
                    		$sub++;
                    	}
                    }
                    
                    ?>
                    <tr>
                    	<td>
						<div style="overflow:auto;<?php if($gift[$i]["number"]){ echo " border:1px solid #eaeaea;height:130px;"; } ?>">
						<?php for($i=0;$i<$gift_rows;$i++){ 
						if($i%2==1){
$class =  "class=\"odd\" onmouseover=\"this.style.backgroundColor='#b0dfde'\" style=\"margin:0px; background-color:#d3d3d3\" onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";
	}else{
$class =  "class=\"even\"  onmouseover=\"this.style.backgroundColor='#b0dfde'\" style=\"margin:0px; background-color:#eaeaea\" onmouseout=\"this.style.backgroundColor='#eaeaea'\"";
	} ?>

					<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#d3d3d3"class="<?php echo $class; ?>" style="border:1px solid #d3d3d3;">
	                        <tr style="">
		                      <td width="2%" > <input type="checkbox" name="giftnumber[]" id="giftnumber[]" value="<?php echo $gift[$i]["id"]; ?>" style="width:20px;" /></td>
		                    <input type="hidden" id="gift[<?=$i?>][to]" name="gift[<?=$i?>][to]" value="<?=$gift[$i]["to"]?>"/>
		                    <input type="hidden" id="gift[<?=$i?>][from]" name="gift[<?=$i?>][from]" value="<?=$gift[$i]["from"]?>"/>
		                    <input type="hidden" id="gift[<?=$i?>][value]" name="gift[<?=$i?>][value]" value="<?=$gift[$i]["value"]?>"/>
		                    <input type="hidden" id="gift[<?=$i?>][product]" name="gift[<?=$i?>][product]" value="<?=$gift[$i]["product"]?>"/>
								                      
							  <td width="51%"  align="left" valign="bottom" ><b style="font-size:10px;">Gift Number : <?php echo $gift[$i]["number"]; ?></b><br />
							    Gift To : <?=$gift[$i]["to"]?>
						        <br />
						        Value : 
					          <?=$gift[$i]["value"]?></td>
	                          <td width="47%" align="left" valign="bottom"  >Gift From :
                              <?=$gift[$i]["from"]?>
                              <br />
                              Product :
                              <?=$gift[$i]["product"]?></td>
		                    </tr>
		                  </table>


						 <?php } ?> 
						 </div>
						 
						 <? if($chkPageEdit){?><br>
                      	<input type="submit" name="addgift" id="addgift" value=" Add Gift " class="button" title="add gift into this book" />
                    	<? } ?>
						  </td>
		              </tr>
		             <? if($chkPageEdit){?>
		              <tr valign="top">
                		 <td align="left" valign="middle" ><br>
                		  <input type="button" name="addNewGift" value=" Add Gift Sold" title="add gift for sale"  href="javascript:;" onClick="<? if($status=="edit"){ ?>window.open('giftinfo/add_giftinfo.php?id_sold=<?=$obj->getIdToText($bookid,"c_bpds_link","bpds_id","tb_id","tb_name=\"a_bookinginfo\"",false)?>&gifttype_id=<?=$GLOBALS["global_gifttypeid"]?>','NewGiftWindows',
						'height=450,width=350,resizable=0,scrollbars=1');<?}else{?>alert('Please create booking before sell gift');<?}?>"
		 				class="button">&nbsp;
						 <input type="button" name="browseallgift" value=" Browse All Gift " title="view all gift certificate information" href="javascript:;" onClick="window.open('giftinfo/manage_giftinfo.php?chkpage=1','NewAllGiftWindows',
						'height=700,width=1020,resizable=0,scrollbars=1');"
		 				class="button"></td>
					</tr>
					<?}?>
                  </table>
            </td>
            <td width="10px">&nbsp;</td>
           	<td style=" vertical-align:top;" align="right" width="65%">
           	   <div class="comment" style="height:100px">
               	<table cellpadding="0" cellspacing="0" class="comment" width="100%">
                	<tr>
                      <td class="mainthead">Gift Number</td>
                      <td class="mainthead">Value</td>
                      <td class="mainthead">Product</td>
                      <td class="mainthead">Gift From</td>
                      <td class="mainthead">Gift Type</td>
                      <td class="mainthead">Expired</td>
                      <td class="mainthead">Info.</td>
                      <td class="mainthead">Remove
                      <input type="hidden" id="deleteGift" name="deleteGift" value="" /></td>
                      
                    </tr>
                    <?
                    $chkColor=0;
					if($rsb["rows"]!=0){
						for($i=0;$i<$rsb["rows"];$i++){
						 $giftType = $obj->getIdToText($rsb[$i]["gifttype_id"],"gl_gifttype","gifttype_name","gifttype_id");	
						 if(($chkColor%2)==0){
		                    echo "<tr class=\"content_list\">";
						 }else{
						 	echo "<tr class=\"content_list1\">";
						 } 
						 echo "<td align=\"left\">".$rsb[$i]["gift_number"]."</td>
		                    <td height=\"25px\" align=\"left\">".$rsb[$i]["value"]."</td>
		                    <td align=\"left\">".$rsb[$i]["product"]."</td>
		                    <td align=\"left\">".$rsb[$i]["receive_from"]."</td>
		                    <td align=\"left\">".$giftType."</td>
		                    <td align=\"left\">".$dateobj->convertdate($rsb[$i]["expired"],'Y-m-d',$sdateformat)."</td>
		                    <td align=\"center\">Use</a>
		                    <td align=\"center\">";
		                if($chkPageEdit){
		                	echo "<a href=\"javascript:;\" onclick=\"setDeleteGift('".$rsb[$i]["gift_id"]."');\" class=\"top_menu_link\">" .
			                    	"<img src=\"../images/inactive.gif\" title=\"Delete\" border=\"0\"></a>";
			            }else{
			            	echo "<img src=\"../images/inactive.gif\" title=\"Delete\" border=\"0\">";
			            }
						
			            echo "</td></tr>";
			               $chkColor++;
						}
					}else if($status=="add"){
						$sub=0;
			          	for($i=1;$i<count($queueGift);$i++){
			          	 if($queueGift[$i]!="deleted"){ 
						 
			          		$sqlb = "select * from g_gift where gift_id=".$queueGift[$i];
							$rsb = $obj->getResult($sqlb);
							
							$giftType = $obj->getIdToText($rsb[0]["gifttype_id"],"gl_gifttype","gifttype_name","gifttype_id");
							
	if((($i-$sub)%2)!=0){
		$class = "class=\"odd\" onmouseover=\"this.style.backgroundColor='#b0dfde'\" style=\"margin:0px; background-color:#ffffff\" onmouseout=\"this.style.backgroundColor='#ffffff'\"";
	}else{
		$class = "class=\"even\" onmouseover=\"this.style.backgroundColor='#b0dfde'\" style=\"margin:0px; background-color:#eaeaea\" onmouseout=\"this.style.backgroundColor='#eaeaea'\"";
	}
							echo "<tr class=\"$class\" >";
							echo "<td align=\"left\">".$rsb[0]["gift_number"]."</td>
			                    <td align=\"left\">".$rsb[0]["value"]."</td>
			                    <td align=\"left\">".$rsb[0]["product"]."</td>
			                    <td align=\"left\">".$rsb[0]["receive_from"]."</td>
			                    <td align=\"left\" style=\"border:1px solid #ffffff;\">".$giftType."</td>
		                    	<td align=\"left\">".$rsb[0]["expired"]."</td>
			                    <td align=\"center\">Use</td>
			                    <td align=\"center\">
			                    	<a href=\"javascript:;\" onclick=\"setDeleteGift('".$rsb[0]["gift_id"]."');\" class=\"top_menu_link\">" .
			                    	"<img src=\"../images/inactive.gif\" title=\"Delete\" border=\"0\"></a></td>
			                   
			                    </tr>";  	
			          	  }else{
			          	  	$sub++;
			          	  }
			          	}
			          }
			          if($rsGift["rows"]!=0){
						for($i=0;$i<$rsGift["rows"];$i++){
						 $giftType = $obj->getIdToText($rsGift[$i]["gifttype_id"],"gl_gifttype","gifttype_name","gifttype_id");
						 if(($chkColor%2)==0){
		                    echo "<tr class=\"content_list\">";
						 }else{
						 	echo "<tr class=\"content_list1\">";
						 } 
						 echo "<td style=\"color: rgb(85, 160, 255);\" align=\"left\">".$rsGift[$i]["gift_number"]."</td>
		                    <td style=\"color: rgb(85, 160, 255);\" height=\"25px\" align=\"left\">".$rsGift[$i]["value"]."</td>
		                    <td style=\"color: rgb(85, 160, 255);\" align=\"left\">".$rsGift[$i]["product"]."</td>
		                    <td style=\"color: rgb(85, 160, 255);\" align=\"left\">".$rsGift[$i]["receive_from"]."</td>
		                    <td style=\"color: rgb(85, 160, 255);\" align=\"left\">".$giftType."</td>
		                 	<td style=\"color: rgb(85, 160, 255);\" align=\"left\">".$dateobj->convertdate($rsGift[$i]["expired"],'Y-m-d',$sdateformat)."</td>
		                    <td style=\"color: rgb(85, 160, 255);\" align=\"center\">Sold</a>
		                    <td style=\"color: rgb(85, 160, 255);\" align=\"center\"> - </a>
		                   </tr>";
		                   $chkColor++;
						}
					  }
                    ?>
         		 </table>   
         		 </div> 
            </td>
           </tr>
          </table>      
           <!-- End table show Gift Detail -->
           </div>
                </td>
              </tr>
         
             </table>
            </fieldset>
          </div>
          <br />
          <div class="group5" width="100%" >
<fieldset>
            <legend><b>Sales Receipt</b></legend>
            <table cellspacing="0" cellpadding="0" >
              <tr>
                <td><table cellspacing="0" cellpadding="0" width="160px" class="cusinfo">
                    <tr>
                      <td><input type='button' name='addsr' value=' Add ' class='button' onClick="addSr(1,'<?=$status?>');this.form.submit();" title="add new sales receipt">
                      <!--	<input type="button" name="calsrd" id="calsrd" value=" Calculator " title="auto calculate each product prices discount sc/vat" class='button' onClick="miniwindow('calacc.php?branch_id=<?=$cs["branch"]?>','discount','400','430','0','100','100','0')" /> -->
                      </td>
                    </tr>
<?
$maxsrdcount = 0;
$maxmpdcount = 0;
for($i=0; $i<$srcount; $i++) {
	//$srdcount = $obj->getParameter("srdcount");
	if($srdcount[$i]>$maxsrdcount){$maxsrdcount=$srdcount[$i];}
	if($mpdcount[$i]>$maxmpdcount){$maxmpdcount=$mpdcount[$i];}
}
?>
                    <tr>
                      <td>Product/Qty/Price <span class="style1">*</span></td>
                    </tr>
                    <? for($a=1; $a<$maxsrdcount; $a++){echo "<tr><td>&nbsp;</td></tr>";}?>
                    <!--
                    <tr>
                      <td align="right">Sub-Total</td>
                    </tr>
                    <tr>
                      <td align="right">Service Charges ( 
                        <?=$servicescharge?>
                        %)</td>
                    </tr>
                    <tr>
                      <td align="right">Tax ( 
                        <?=$obj->getIdToText($taxpercent,"l_tax","tax_percent","tax_id")?>
                        %)</td>
                    </tr>
                    <tr>
                      <td align="right">Payment</td>
                    </tr>
                    -->
                    <tr>
		              <td>&nbsp;</td>
		            </tr>
                    <tr>
                      <td align="right">Total</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>Method of Payment <span class="style1">*</span> </td>
                    </tr>
                    <? for($a=1; $a<$maxmpdcount; $a++){echo "<tr><td>&nbsp;</td></tr>";}?>
                    <tr>
                      <td>Comment </td>
                    </tr>
                    <tr>
                      <td>Paid</td>
                    </tr>
                    <? if($status=="edit"){ ?>
                    <tr>
                      <td>Paid tick by</td>
                    </tr>
                    <tr>
                      <td>Date : Time</td>
                    </tr>
                    <? } ?>
                    <tr>
                      <td>Print Preview <span class="style1">*</span> </td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
 <?
for($i=0; $i<$srcount; $i++) {
	
	if(!$srdcount[$i])
	    $srdcount[$i] = 1;
	
//For debug undefined index
if(!isset($srd[$i][0]["sr_id"])){$srd[$i][0]["sr_id"]="";}
if(!isset($srd[$i][0]["paytype"])){$srd[$i][0]["paytype"]="";}
if(!isset($srd[$i][0]["comment"])){$srd[$i][0]["comment"]="";}
if(!isset($srd[$i][0]["paid"])){$srd[$i][0]["paid"]="";}
	
	
	$chkSR=true;
		$rsChk=$obj->getIdToText($srd[$i][0]["sr_id"],"c_salesreceipt","salesreceipt_id","salesreceipt_id","paid_confirm=1");
		if($rsChk){
				$chkSR=false;
							
		}else{
			$chkSREdit=true;
		}
		$srnumber = $obj->getIdToText($srd[$i][0]["sr_id"],"c_salesreceipt","salesreceipt_number","salesreceipt_id");
?>
        <td><table cellspacing="0" cellpadding="0" width="275px" class="cusinfo">
            <tr>
              <td align="center" title="Sales Receipt number will generate when sales receipt is printed in the same date with appointment date.">
              <input type='hidden' name='srdcount[<?=$i?>]' id='srdcount[<?=$i?>]' value='<?=$srdcount[$i]?>' />
                <b>
                <?=($i+1)?> <?=($srnumber)?"- $srnumber":""?>
                </b> </td>
            </tr>
            <? for($j=0; $j<$srdcount[$i]; $j++){ 
            	// debugging all undified index natt - June 11,2009
           		if(!isset($srd[$i][$j]["srd_id"])){$srd[$i][$j]["srd_id"]="";}
           		if(!isset($srd[$i][$j]["pd_id"])){$srd[$i][$j]["pd_id"]=1;}
           		if(!isset($srd[$i][$j]["quantity"])){$srd[$i][$j]["quantity"]=1;}
           		if(!isset($srd[$i][$j]["unit_price"])){$srd[$i][$j]["unit_price"]=0;}
           		if(!isset($srd[$i][$j]["set_sc"])){$srd[$i][$j]["set_sc"]=0;}
           		if(!isset($srd[$i][$j]["set_tax"])){$srd[$i][$j]["set_tax"]=0;}
           		if(!isset($srd[$i][$j]["plus_tax"])){$srd[$i][$j]["plus_tax"]=1;}
           		if(!isset($srd[$i][$j]["plus_sc"])){$srd[$i][$j]["plus_sc"]=1;}
           		if(!isset($srd[$i][$j]["pd_id_tmp"])){$srd[$i][$j]["pd_id_tmp"]="";}
            ?>
            <tr>
              <td class="fix">
               <span style="width: 115px;font-family:Tahoma; font-size: 11px;overflow:hidden;">
              <?=$obj->makeListbox("srd[$i][$j][pd_id]","cl_product_category,cl_product","pd_name","pd_id",$srd[$i][$j]["pd_id"],$chkautosubmit,"cl_product_category.pd_category_priority, cl_product.pd_name","cl_product.pd_active",1,"cl_product.pd_category_id=cl_product_category.pd_category_id",false,!$chkSR)?>
               </span> 
                /
                <input size="1" name="srd[<?=$i?>][<?=$j?>][quantity]" value="<?=($srd[$i][$j]["quantity"])?$srd[$i][$j]["quantity"]:"1"?>" maxlength="10" type="text" style="width:20px" <?=($chkSR)?"":"disabled"?> onChange="checkqtyNum(this);this.form.submit();"/>
                /
                <input size="7" id="srd[<?=$i?>][<?=$j?>][unit_price]" name="srd[<?=$i?>][<?=$j?>][unit_price]" value="<?=($srd[$i][$j]["unit_price"])?$srd[$i][$j]["unit_price"]:"0"?>" type="text" style="width:50px" <?=($chkSR)?"":"disabled"?> onChange="checkpriceNum(this);this.form.submit();"/>
                
               <!-- Link for set plus sc and tax -->
               <?
               /////////// For set calculate with tax and servicecharge or not /////////////
               //echo "<br>Pd id : ".$srd[$i][$j]["pd_id"];
               if($srd[$i][$j]["pd_id"]!=1 && $srd[$i][$j]["pd_id"]!=""){
               		if($chkSR){
	               		if($srd[$i][$j]["set_sc"]){
	               			?><a href="javascript:;" onClick="javascript:setScProduct('srd[<?=$i?>][<?=$j?>][plus_sc]','appt');" class="top_menu_link"><img src="../images/<?=($srd[$i][$j]["plus_sc"])?"active":"inactive"?>.gif" title="Service Charge" border="0"></a> <? 			
	               		}else{
	               			echo "<img src=\"../images/non.gif\" title=\"Service Charge\" border=\"0\">&nbsp;";	
	               		}
	               }else{
	               		if($obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_sc","pd_id")){
							?><img src="../images/<?=($obj->getIdToText($srd[$i][$j]["srd_id"],"c_srdetail","set_sc","srdetail_id"))?"active":"inactive"?>.gif" title="Service Charge" border="0"> <?		
	               		}else{
	               			if($srd[$i][$j]["plus_sc"]){
	               				echo "<img src=\"../images/active.gif\" title=\"Service Charge\" border=\"0\">&nbsp;";
	               			}else{
	               				echo "<img src=\"../images/non.gif\" title=\"Service Charge\" border=\"0\">&nbsp;";	
	               			}
							
	               		}
	               			
	               }
               }
               if($srd[$i][$j]["pd_id"]!=1 && $srd[$i][$j]["pd_id"]!=""){
               		if($chkSR){
	               		if($srd[$i][$j]["set_tax"]){
	               			?><a href="javascript:;" onClick="javascript:setTaxProduct('srd[<?=$i?>][<?=$j?>][plus_tax]','appt');" class="top_menu_link"><img src="../images/<?=($srd[$i][$j]["plus_tax"])?"active":"inactive"?>.gif" title="Tax" border="0"></a><?               			
	               		}else{
	               			echo "<img src=\"../images/non.gif\" title=\"Tax\" border=\"0\">";
	               		}
	               }else{
	               		if($obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_tax","pd_id")){
		            		?><img src="../images/<?=($obj->getIdToText($srd[$i][$j]["srd_id"],"c_srdetail","set_tax","srdetail_id"))?"active":"inactive"?>.gif" title="Tax" border="0"><?
		            	}else{
		            		if($srd[$i][$j]["plus_tax"]){
		            			echo "<img src=\"../images/active.gif\" title=\"Tax\" border=\"0\">";
		            		}else{
		            			echo "<img src=\"../images/non.gif\" title=\"Tax\" border=\"0\">";	
		            		}
							
		            	}
	               }
               }
               
               ?>
               	<input type='hidden' id="srd[<?=$i?>][<?=$j?>][plus_sc]" name="srd[<?=$i?>][<?=$j?>][plus_sc]" value='<?=$srd[$i][$j]["plus_sc"]?>'>
                <input type='hidden' id="srd[<?=$i?>][<?=$j?>][plus_tax]" name="srd[<?=$i?>][<?=$j?>][plus_tax]" value='<?=$srd[$i][$j]["plus_tax"]?>'>
               	<input type='hidden' id="srd[<?=$i?>][<?=$j?>][srd_id]" name="srd[<?=$i?>][<?=$j?>][srd_id]" value='<?=$srd[$i][$j]["srd_id"]?>'>
                <input type='hidden' id="srd[<?=$i?>][<?=$j?>][pd_id_tmp]" name="srd[<?=$i?>][<?=$j?>][pd_id_tmp]" value='<?=$srd[$i][$j]["pd_id_tmp"]?>'>
              </td>
              
              <?if(!$chkSR && ($j<($srdcount[$i]-1))){?>
              	<input type='hidden' name='srd[<?=$i?>][<?=$j?>][pd_id]' id='srd[<?=$i?>][<?=$j?>][pd_id]' value='<?=$srd[$i][$j]["pd_id"]?>' />
                <input name="srd[<?=$i?>][<?=$j?>][quantity]" value="<?=($srd[$i][$j]["quantity"])?$srd[$i][$j]["quantity"]:"1"?>" type="hidden"/>
               	<input id="srd[<?=$i?>][<?=$j?>][unit_price]" name="srd[<?=$i?>][<?=$j?>][unit_price]" value="<?=($srd[$i][$j]["unit_price"])?$srd[$i][$j]["unit_price"]:"0"?>" type="hidden" />
              <?}?>
            </tr>
            <? } ?>
            <? for($k=0; $k<($maxsrdcount-$srdcount[$i]); $k++){echo "<tr><td>&nbsp;</td></tr>";}?>
            <!--
            <tr>
              <td align="right" style="padding-right:65px"><?=number_format($r_amount[$i],2,".",",")?>
                </td>
            </tr>
            <tr>
              <td align="right" style="padding-right:65px"><?=number_format($r_svc[$i],2,".",",")?>
                </td>
            </tr>
            <tr>
              <td align="right" style="padding-right:65px"><?=number_format($r_tax[$i],2,".",",")?>
                </td>
            </tr>
            <tr>
              <td align="right" style="padding-right:65px"><?=number_format($r_payment[$i],2,".",",")?>
                </td>
            </tr>
            -->
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
            	<?
 				if(number_format($r_total[$i],2,".",",")==(0.00)){
 					$r_total[$i]=abs(number_format($r_total[$i],2,".",","));
 				}
 				?>
              <td align="right" style="padding-right:65px"><?=number_format($r_total[$i],2,".",",")?>
               <input type='hidden' id="srd[<?=$i?>][0][sr_total]" name="srd[<?=$i?>][0][sr_total]" value='<?=$r_total[$i]?>'>
              <input type='hidden' id="srd[<?=$i?>][0][sr_total1]" name="srd[<?=$i?>][0][sr_total1]" value='<?=$rnp_total[$i]?>'>฿
                </td>
            </tr>
            <tr>
              <td align="left"><input type='hidden' id="srd[<?=$i?>][0][subtotal]" name="srd[<?=$i?>][0][subtotal]" value='<?=$r_amount[$i]?>'></td>
            </tr>
          <? 
          
          //For Muti Payment Method: By David
         
          for($j=0; $j<$mpdcount[$i]; $j++){
          	if(!isset($srd[$i][$j]["mpd_id"])){$srd[$i][$j]["mpd_id"]="";}
          	$checksubmit = "";
          	if($j==$mpdcount[$i]-1){$checksubmit = "this.form.submit();";}
          	?>
            <tr>
             <td align="center" class="fix">
             <span style="width: 115px;font-family:Tahoma; font-size: 11px;overflow:hidden;">
             <?=$obj->makeListbox("srd[$i][$j][paytype]","l_paytype","pay_name","pay_id",$srd[$i][$j]["paytype"],0,"pay_name","pay_active",1,false,false,!$chkSR,false,false,"CheckDuplicate($i,$j);findmax($i);$checksubmit")?>
             </span>&nbsp;          
           	<?
           			//Debug for change lower price : By David : 17-02-2010
           			if($srd[$i][0]["pay_price"]>number_format($r_total[$i],2,".","")){
    					$srd[$i][0]["pay_price"]=number_format($r_total[$i],2,".","");
    				}
    				//Set first price : By David : 17-02-2010
    				if(!$srd[$i][0]["pay_price"] && $mpdcount[$i]==1){
    					$srd[$i][0]["pay_price"]=number_format($r_total[$i],2,".","");
    				}else{
    					$sumprice[$i] = $sumprice[$i]+$srd[$i][$j]["pay_price"];					
		          		if($j==$mpdcount[$i]-1){		          			
		          			$srd[$i][$j]["pay_price"]=number_format($r_total[$i],2,".","")-number_format($sumprice[$i],2,".","");
		          		}
          			}          		
    		?>
             <input type="text" size="7" id="srd[<?=$i?>][<?=$j?>][pay_price]" name="srd[<?=$i?>][<?=$j?>][pay_price]" 
             value="<?
             if(!$srd[$i][$j]["pay_price"]){
             	if($mpdcount[$i]==1){
             		echo number_format($sumprice[$i],2,".","");
             	}else{
             		echo "0";
             	}	
             }else{
             	echo number_format($srd[$i][$j]["pay_price"],2,".","");
             	}
             ?>" type="text" style="width:50px" <?if($srd[$i][$j]["paytype"]<=1){echo "readonly";} ?> 
             onchange="checkpayNum(this);addPayPrice(<?=$i?>,<?=$j?>);findmax(<?=$i?>);chkPlus(<?=$srcount?>);" <?=($chkSR)?"":"disabled"?>/>
             
             </td>
             <input type='hidden' id="srd[<?=$i?>][<?=$j?>][mpd_id]" name="srd[<?=$i?>][<?=$j?>][mpd_id]" value='<?=$srd[$i][$j]["mpd_id"]?>'>
             <input type='hidden' name='pdcount[<?=$j?>]' id='pdcount[<?=$j?>]' value='<?=$j;?>' />
            </tr>
            <?if(!$chkSR && ($j<($mpdcount[$i]-1))){?>
              	<input type='hidden' name='srd[<?=$i?>][<?=$j?>][paytype]' id='srd[<?=$i?>][<?=$j?>][paytype]' value='<?=$srd[$i][$j]["paytype"]?>' />
                <input type='hidden' name='srd[<?=$i?>][<?=$j?>][pay_price]' id='srd[<?=$i?>][<?=$j?>][pay_price]' value='<?=$srd[$i][$j]["pay_price"]?>' />
             <?}?>
          <?}?>
 <?
 //Debug find max price
 for($j=0; $j<$mpdcount[$i]-1; $j++){
 	if($j==0){
 		$max = $srd[$i][0]["pay_price"];	
 		$maxid = $srd[$i][0]["paytype"];
 	}
 		if($max<$srd[$i][$j]["pay_price"]){
 			$max = $srd[$i][$j]["pay_price"];
 			$maxid = $srd[$i][$j]["paytype"];
 		}
 }
 if(!$maxid){
 	$maxid=1;
 }
 ?>
           <? for($k=0; $k<($maxmpdcount-$mpdcount[$i]); $k++){echo "<tr><td>&nbsp;</td></tr>";}?>
          <input type='hidden' name='srd[<?=$i?>][0][maxpaid]' id='srd[<?=$i?>][0][maxpaid]' value='<?=$maxid?>' /> 
          <input type='hidden' name='mpdcount[<?=$i?>]' id='mpdcount[<?=$i?>]' value='<?=$mpdcount[$i]?>' />
            <tr>
              <td align="center"><input name="srd[<?=$i?>][0][comment]" id="srd[<?=$i?>][0][comment]" value="<?=$srd[$i][0]["comment"]?>" <?=($chkSR)?"":"disabled"?> type="text">
              </td>
            </tr>
            <tr>
              <td align="center"><input name="srd[<?=$i?>][0][paid]" id="srd[<?=$i?>][0][paid]" value="checked" <?=($chkSREdit)?"":"disabled"?> type="checkbox" <? if($srd[$i][0]["paid"]){echo "checked";}?> onChange="Chk_paid(<?=$i?>);"/>
  			  </td>
            </tr>
             <? if($status=="edit"){ ?>
            <tr>
              <td align="center">
              <?
              	if($srd[$i][0]["sr_id"]){
              		$sr_lu_user_id = $obj->getIdToText($srd[$i][0]["sr_id"],"c_salesreceipt","sr_lu_user","salesreceipt_id");
	              	$sr_lu_user = $obj->getIdToText($sr_lu_user_id,"s_user","u","u_id");
	              	if($sr_lu_user){
	              		echo $sr_lu_user;	
	              	}else{
	              		echo "-";
	              	}	
              	}else{
              		echo "-";
              	}
              	
              ?>
  			  </td>
            </tr>
            <tr>
              <td align="center">
				<?
				if($srd[$i][0]["sr_id"]){
					$paidDate =$obj->getIdToText($srd[$i][0]["sr_id"],"c_salesreceipt","sr_datets","salesreceipt_id");
					if($paidDate!="0000-00-00 00:00:00"){
						list($date,$time) =  split('[ ]', $paidDate);
						$data = $dateobj->timezone_depend_branch($date,$time,"$ldateformat, H:i:s",$cs["branch"]);
            			echo $data;	
					}else{
						echo "-";
					}
				}else{
					echo "-";
				}	
					
				?>
  			  </td>
            </tr>
            <? } ?>
            <tr>
              <td align="center"><a href='javascript:;' class="worksheet" onClick="saleReceiptWindow(<?$sr_id=(isset($srd[$i][0]["sr_id"]))?$srd[$i][0]["sr_id"]:"";$bookid=($status=="add")?"":"$bookid"; echo  "'$bookid','".$sr_id."','$status'";?>)">&nbsp;(Print)</a> 
              <? if($srd[$i][0]["sr_id"]){ ?>
              <a href="javascript:;" onClick="miniwindow('srpreviewlog.php?sr_id='+<?=$srd[$i][0]["sr_id"]?>,'srpreviewlog','400','460','0','100','100','0')">
              <img src="/images/icon_history.gif" width="16px" height="16px" class="link"/></a>
              <? } ?>
              </td>
            </tr>
            <tr>
              <td><input type='hidden' id="srd[<?=$i?>][0][sr_id]" name="srd[<?=$i?>][0][sr_id]" value='<?=$srd[$i][0]["sr_id"]?>'>
              <?if(!$chkSR){?>
              	<input id="srd[<?=$i?>][<?=$j?>][paytype]" name="srd[<?=$i?>][<?=$j?>][paytype]" value="<?=$srd[$i][$j]["paytype"]?>" type="hidden" />
              	<input name="srd[<?=$i?>][0][comment]" id="srd[<?=$i?>][0][comment]" value='<?=$srd[$i][0]["comment"]?>' type="hidden">
              <?}
              	if(!$chkSREdit){?>
              		<input name="srd[<?=$i?>][0][paid]" value="checked" type="hidden" <? if($srd[$i][0]["paid"]){echo "checked";}?> />
              <?}?>
              <input name="srd[<?=$i?>][0][now_check_paid]" id="srd[<?=$i?>][0][now_check_paid]" value='<?=($srd[$i][0]["paid"])?"1":"0"?>' type="hidden">
              </td>
            </tr>
            </table>
           </td>
          <? } ?>
    </tr>
    
  </table>
            <input type='hidden' id="chkFirst" name="chkFirst" value='first'>
            <input type='hidden' id="srcount" name="srcount" value='<?=$srcount?>'>
            
          
          </div>
        </div></td>
    </tr>
  </table>
</form>
<?
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo '<p>SMS page generated in '.$total_time.' seconds.</p>'."\n";
?>
</body>
</html>