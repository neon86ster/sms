<?php
include("../include.php");
$pagename = "cpbooking.php";
$obj->setDebugStatus(false);

$errorMsg="";$error = "";
$date = $obj->getParameter("apptdate",date($sdateformat));
$hiddendate = $dateobj->convertdate($date,$sdateformat,"Ymd");	// it can have bug years 19xx-20xx

/***************************************************
 * Security checking
 ***************************************************/
 // check user edit permission 
$pageid = "1";	// appointment page
$pagestatus = $object->check_permission($pageid,$permissionrs);
$chkPageEdit=false;$chkPageView=false;
$book_id = $obj->getParameter("book_id","");
if($pagestatus=="e"){
	$chkPageEdit=true;$chkPageView=true;
}else if($pagestatus=="v"){
	$chkPageEdit=false;$chkPageView=true;
}else if($pagestatus=="n"){
	$chkPageEdit=false;$chkPageView=false;
}

//Check branch's date/time 
if($book_id){
	$sql = "select * from a_bookinginfo where book_id=$book_id";	
	$rs = $obj->getResult($sql);
	
	$branch_id = $obj->getParameter("branch_id",$rs[0]["b_branch_id"]);
	$now=$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$sdateformat",$branch_id);
}else{
	$now=$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$sdateformat","1");
}

// check reservtion edit date limit 
$preEditDate="";
$afterEditDate="";
$chkReServation="true";	// flat for calendar chooser 
// checking if appt_editchk was check
$chkRsEditDate = $object->isReservationLimit("appt_editchk");
if($chkRsEditDate){
	$preEditDate= $object->getReservationDate("pre_editdate","appt_editchk");
	$afterEditDate= $object->getReservationDate("after_editdate","appt_editchk");
	$chkRsDate= $object->checkReservationDate($date,$sdateformat,$preEditDate,$afterEditDate,$now);
	if(!$chkRsDate){
		$chkReServation="false";
	}
}else{
// seting flat for calendar chooser 
	$preEditDate="notCheck";
	$afterEditDate="notCheck";
}


if($chkPageEdit==false){
	$errorMsg = "You haven't permission to copy booking !! ";
}

if($chkReServation=="false"||$hiddendate<date('Ymd')){
	$errorMsg = "You can't copy booking to $date !! ";
	$date = date($sdateformat);
	$hiddendate = date('Ymd');
}
//################### End security check ##################################


$tw = $obj->getParameter("tw");
$roomError=$obj->getParameter("roomError","");
$city_change = false; // For check user change city of branch or not (not change : false , change : true)

if($book_id){
	$sql = "select * from a_bookinginfo where book_id=$book_id";	
	$rs = $obj->getResult($sql);
	$ttpp = $rs[0]["b_qty_people"];
	$branch_id = $obj->getParameter("branch_id",$rs[0]["b_branch_id"]);
	$obj->setBranchid($branch_id);
	$obj->setLimitBranchOnLocation();
	$appttime = $obj->getParameter("appttime",$rs[0]["b_appt_time_id"]);
	$branchBeforeChange = $obj->getParameter("branchBeforeChange",$branch_id);
	$sql_ind = "select * from d_indivi_info where book_id=$book_id";
	$rs_ind = $obj->getResult($sql_ind);
	
	// For check branch change from old booking.
	$old_city = $obj->getIdToText($rs[0]["b_branch_id"],"bl_branchinfo","city_id","branch_id");
	$new_city = $obj->getIdToText($branch_id,"bl_branchinfo","city_id","branch_id");
	if($new_city != $old_city){
		$city_change = true;
	}
	
	if(!isset($_REQUEST["tw"])){
		if($rs_ind) {	
			for($i=0; $i<$rs_ind["rows"]; $i++) {
				$tw[$i]["room_id"] = $rs_ind[$i]["room_id"];
				$tw[$i]["member_use"] = $rs_ind[$i]["member_use"];
				$tw[$i]["indivi_id"] = $rs_ind[$i]["indivi_id"];
			}
		}
	}else if($branchBeforeChange != $branch_id){
		$branchBeforeChange = $branch_id;
		/*  find new room is available when change branch  */
		// find busy room
		$chkrs = $obj->checkRoom($hiddendate,$appttime,$rs[0]["b_book_hour"],$branch_id);
		if(count($chkrs) > 0){
			$busyroom = implode(",",$chkrs);
		}else{
			$busyroom = "''";
		}
		
		// find not busy room
		$chksql = "select room_id, room_qty_people " .
				"from bl_room " .
				"where room_active=1 " .
				"and branch_id=$branch_id " .
				"and room_id not in ($busyroom) " .
				"order by room_name ";
		$nbroomrs = $obj->getResult($chksql);
		
		// set treatment individual room
		$cnt=0;$chk="";
		$cntallemproom=0;
		for($i=0;$i<$nbroomrs["rows"];$i++){
			$cntallemproom += $nbroomrs[$i]["room_qty_people"];
			for($k=0;$k<$nbroomrs[$i]["room_qty_people"];$k++){
				if($cnt>$ttpp-1){$chk="break"; break;}
					$tw[$cnt]["room_id"]=$nbroomrs[$i]["room_id"];
					$cnt++;
			}
			if($chk=="break"){break;}
		}
		
		//fill busy room to tw when don't have any room available.
		if($ttpp > $cnt){
			// find busy room order by room_name
			$chksql = "select room_id,room_qty_people from bl_room " .
					"where room_active=1 " .
					"and branch_id=$branch_id " .
					"and room_id in ($busyroom) " .
					"order by room_name ";
			$broomrs = $obj->getResult($chksql);
			$peopleRemain = $ttpp-$cnt;	// people wait for fill in room
			for($i=0;$i<$peopleRemain;$i++){
				$room_qty_people = $broomrs[$i]["room_qty_people"];
				$cntallemproom += $room_qty_people;
				for($j=0;$j<$room_qty_people;$j++){
					$tw[$cnt]["room_id"] = $broomrs[$i]["room_id"];	
					$cnt++;	
				}
			}
		}
		if($cntallemproom<$ttpp){$roomError="Please check room!!";}else{$roomError="";}
		/*  end find new room is available when change branch  */
	}
}
if(isset($_REQUEST["Copy"]) && $chkPageEdit && !$roomError){
	$rs_cc = $obj->getResult("select * from ac_cancal where book_id=$book_id");
	$rs_tran = $obj->getResult("select * from ab_transfer where book_id=$book_id");
	$rs_th = $obj->getResult("select * from da_mult_th where book_id=$book_id order by indivi_id");
	$rs_sr = $obj->getResult("select * from c_salesreceipt where book_id=$book_id");
	$rs_srd = $obj->getResult("select * from c_srdetail where book_id=$book_id");
	$rs_comment = $obj->getResult("select * from ad_comment where book_id=$book_id");
	$tw = $_REQUEST["tw"];
	
	if($hiddendate<date('Ymd')){
		$error = "Please change destination date to future or today!!";
	}
	$expiredDateMember = $obj->getIdToText($rs[0]["a_member_code"],"m_membership","expireddate","member_code");
	$expiredDateMemberchk = $dateobj->convertdate($expiredDateMember,"Y-m-d","Ymd");
	if($rs[0]["a_member_code"] && $expiredDateMember!="0000-00-00" && $hiddendate > $expiredDateMemberchk){
		$error = "Appointment date more than member expired date. Please check again!!";
	}
	
	if($error == ""){
		$abi_field = $obj->getResult("show columns from a_bookinginfo");
		$cc_field = $obj->getResult("show columns from ac_cancal");
		$tran_field = $obj->getResult("show columns from ab_transfer");
		$ind_field = $obj->getResult("show columns from d_indivi_info");
		$th_field = $obj->getResult("show columns from da_mult_th");
		$sr_field = $obj->getResult("show columns from c_salesreceipt");
		$srd_field = $obj->getResult("show columns from c_srdetail");
		$comment_field = $obj->getResult("show columns from ad_comment");
		
		$id = $obj->setResult(abi_sql($rs,$abi_field,$hiddendate,$branch_id,$appttime));
		$logid = $obj->setResult(log_sql($rs,$id));
		
		if($id){
			$idSCA=$obj->saveCopyAppoiontment($book_id,$id,$tw,$hiddendate,$branch_id,$appttime,$city_change);
			if($idSCA){	
				if($rs_cc!=false)
					$ccid = $obj->setResult(cc_sql($rs_cc,$cc_field,$id));
				if($rs_tran!=false)
					$tranid = $obj->setResult(tran_sql($rs_tran,$tran_field,$id));
				if($rs_comment!=false)
					$commentid = $obj->setResult(comment_sql($rs_comment,$comment_field,$id));
				if($rs_ind!=false){
					$indid = $obj->setResult(ind_sql($rs_ind,$ind_field,$id,$tw,$branch_id));
				}
				if($indid){
					if($rs_th!=false){}
						$start_id=$obj->getIdToText($id,"a_bookinginfo","b_appt_time_id","book_id");
						$hour_id=$obj->getIdToText($id,"a_bookinginfo","b_book_hour","book_id");
						//Find End Book time

             	  		$book_start_min = 60 * (8 + floor(($start_id-1) / 12)) + 5 * (($start_id -1) % 12);
                  		$hour_book = $obj->getIdToText($hour_id,"l_hour","hour_name","hour_id");
                  		list ($hr, $min, $sec) = explode(":", $hour_book);
                  		$max_book_min = (60 * $hr) + $min;
                  		
                  		$book_end_min = $book_start_min + $max_book_min;
                  	
                  		// convert time finish to id
						$end_id = 12 * (floor( $book_end_min / 60 ) - 8) + 1 + 
						( $book_end_min - 60 * floor( $book_end_min / 60 )) / 5;
             	  
      					/////////////
      					
						$thid = $obj->setResult(th_sql($rs_th,$th_field,$indid,$id,$rs_ind["rows"],$city_change,$start_id,$end_id));
				}
				header("location: cpbooking.php?book_id=$book_id&successmsg=Copy booking to ".$obj->getIdToText($idSCA,"a_appointment","bpds_id","appt_id")." success!!");
			}
		}
	}
}else if(!isset($_REQUEST["Copy"])){
	$roomError = "";
}

/*
 * Re-coding function abi_sql()
 * Modify : 07-09-2009/Ruck
 */
function abi_sql($rs=false,$rs_field=false,$appt_date=false,$branch_id=false,$appttime=false){
	$sql = "";
	$field = array();
	$values = array();
	
	//For copy all field and value from table a_bookinginfo.	
	for($j=0; $j<$rs_field["rows"]; $j++) {
		if($rs_field[$j]["Field"] != "book_id") {
			$field[$rs_field[$j]["Field"]] = $rs_field[$j]["Field"];	
			$values[$rs_field[$j]["Field"]] = "\"".$rs[0][$rs_field[$j]["Field"]]."\"";
		}
	}
	
	// Set new value that not want copy from table a_bookinginfo.
	$values["b_appt_date"] = "$appt_date";
	$values["b_book_datets"] = "now()";
	$values["b_set_cancel"] ="0";
	$values["b_branch_id"] = "$branch_id";
	$values["b_appt_time_id"] = $appttime;
	$values["c_lu_user"] = "\"".$_SESSION["__user_id"]."\"";
	$values["l_lu_user"] = "\"".$_SESSION["__user_id"]."\"";
	$values["c_lu_date"] = "now()";
	$values["l_lu_date"] = "now()";
	$values["c_lu_ip"] = "\"".$_SERVER["REMOTE_ADDR"]."\"";
	$values["l_lu_ip"] = "\"".$_SERVER["REMOTE_ADDR"]."\"";
	
	// If old book has commission when copy booking during 14 day auto set commission.
	if($values["c_set_cms"] == "1"){
		$oldDate = str_replace("-","",$rs[0]["b_appt_date"]);
		$oldDate +=0;
		$nowDate = date("Ymd");
		$sum = abs(strtotime($nowDate)-strtotime($oldDate)) / 86400;
		if($sum <= 14){
			$values["c_set_cms"] = "1";	
		}else{
			$values["c_set_cms"] = "0";
		}
	}
			
	$sql = "insert into a_bookinginfo (".implode(",",$field).") values(".implode(",",$values)."); ";
	return $sql;
}
function log_sql($rs=false,$bookid=false){
	$csname = $rs[0]["b_customer_name"];
	$bcompany = $rs[0]["c_bp_id"];
	$bpname = $rs[0]["c_bp_person"];
	$bpphone = $rs[0]["c_bp_phone"];
	$cms = $rs[0]["c_set_cms"];
	$bcms_id = $rs[0]["c_pcms_id"];
	$userid = $_SESSION["__user_id"];
	$ip = $_SERVER["REMOTE_ADDR"];
	
	// If old book has commission when copy booking during 14 day auto set commission.
	if($cms == "1"){
		$oldDate = str_replace("-","",$rs[0]["b_appt_date"]);
		$oldDate +=0;
		$nowDate = date("Ymd");
		$sum = abs(strtotime($nowDate)-strtotime($oldDate)) / 86400;
		if($sum <= 14){
			$cms = "1";	
		}else{
			$cms = "0";
		}
	}
			
	$sql = "insert into log_c_bp (book_id,b_customer_name," .
					"c_bp_id,c_bp_person,c_bp_phone,c_set_cms,c_pcms_id," .
					"l_lu_user,l_lu_date,l_lu_ip) " .
					"values($bookid,\"".$csname."\"," .
					"$bcompany,\"$bpname\",\"$bpphone\",$cms,$bcms_id," .
					"$userid,now(),\"$ip\"); ";
	return $sql;
}
function cc_sql($rs=false,$rs_field=false,$book_id=false){
	if(!$book_id) {return false;}
	$sql = false;
	$sql = "insert into ac_cancal(";
	$count = 0;
	
	for($i=0; $i<$rs_field["rows"]; $i++) {
		if($rs_field[$i]["Field"] != "cancel_id") {
				if($count)
					$sql .= ",";				
				$sql .= $rs_field[$i]["Field"];
				$count++;
		}
	}
	$sql .= ") values";
	for($i=0; $i<$rs["rows"]; $i++) {
		if($i)
			$sql .= ",";
		$count = 0;	
		$sql .= "(";				
		for($j=0; $j<$rs_field["rows"]; $j++) {
					
			if($rs_field[$j]["Field"] != "cancel_id") {
				if($count)
					$sql .= ",";
				
				if($rs_field[$j]["Field"] == "book_id")	
					$sql .= "\"".$book_id."\"";
				else
					$sql .= "\"".$rs[$i][$rs_field[$j]["Field"]]."\"";
				
				$count++;
			}
		}		
		$sql .= ")";	
	}
	$sql .= ";";
	return $sql;
}
function comment_sql($rs=false,$rs_field=false,$book_id=false){
	if(!$book_id) {return false;}
	$sql = false;
	$sql = "insert into ad_comment(";
	$count = 0;
	
	for($i=0; $i<$rs_field["rows"]; $i++) {
		if($rs_field[$i]["Field"] != "comment_id") {
				if($count)
					$sql .= ",";				
				$sql .= $rs_field[$i]["Field"];
				$count++;
		}
	}
	$sql .= ") values";
	for($i=0; $i<$rs["rows"]; $i++) {
		if($i)
			$sql .= ",";
		$count = 0;	
		$sql .= "(";				
		for($j=0; $j<$rs_field["rows"]; $j++) {
					
			if($rs_field[$j]["Field"] != "comment_id") {
				if($count)
					$sql .= ",";
				
				if($rs_field[$j]["Field"] == "book_id")	{$sql .= "\"".$book_id."\"";}
				else if($rs_field[$j]["Field"] == "comments"&&strripos($rs[$i][$rs_field[$j]["Field"]],"<div style='color:#55a0ff'>")===false){$sql .= "\"<div style='color:#55a0ff'>".$rs[$i][$rs_field[$j]["Field"]]."</div>\"";}
				else{$sql .= "\"".$rs[$i][$rs_field[$j]["Field"]]."\"";}
					
				
				$count++;
			}
		}		
		$sql .= ")";	
	}
	$sql .= ";";
	return $sql;
}
function tran_sql($rs=false,$rs_field=false,$book_id=false){
	if(!$book_id) {return false;}
	
	$sql = false;
	$sql = "insert into ab_transfer(";
	$count = 0;
	
	for($i=0; $i<$rs_field["rows"]; $i++) {
		if($rs_field[$i]["Field"] != "transfer_id") {
				if($count)
					$sql .= ",";				
				$sql .= $rs_field[$i]["Field"];
				$count++;
		}
	}
	$sql .= ") values";
	for($i=0; $i<$rs["rows"]; $i++) {
		if($i)
			$sql .= ",";
		$count = 0;	
		$sql .= "(";				
		for($j=0; $j<$rs_field["rows"]; $j++) {
					
			if($rs_field[$j]["Field"] != "transfer_id") {
				if($count)
					$sql .= ",";
				
				if($rs_field[$j]["Field"] == "book_id")	
					$sql .= "\"".$book_id."\"";
				else
					$sql .= "\"".$rs[$i][$rs_field[$j]["Field"]]."\"";
				
				$count++;
			}
		}		
		$sql .= ")";	
	}
	$sql .= ";";
	return $sql;
}
function ind_sql($rs=false,$rs_field=false,$book_id=false,$tw=false){
	if(!$book_id) {return false;}
	$sql = false;
	$sql = "insert into d_indivi_info(";
	$count = 0;
	for($i=0; $i<$rs_field["rows"]; $i++) {
		if($rs_field[$i]["Field"] != "indivi_id" && $rs_field[$i]["Field"] != "package_id"
		 && $rs_field[$i]["Field"] != "strength_id"
		 && $rs_field[$i]["Field"] != "scrub_id" && $rs_field[$i]["Field"] != "wrap_id"
		 && $rs_field[$i]["Field"] != "bath_id" && $rs_field[$i]["Field"] != "facial_id"
		 && $rs_field[$i]["Field"] != "stream" && $rs_field[$i]["Field"] != "comments"
		 && $rs_field[$i]["Field"] != "b_set_inroom" && $rs_field[$i]["Field"] != "b_set_finish" 
		 && $rs_field[$i]["Field"] != "b_set_atspa") {
				if($count)
					$sql .= ",";				
				$sql .= $rs_field[$i]["Field"];
				$count++;
		}
	}
	$sql .= ") values";
	for($i=0; $i<$rs["rows"]; $i++) {
		if($i)
			$sql .= ",";
		$count = 0;	
		$sql .= "(";				
		for($j=0; $j<$rs_field["rows"]; $j++) {
					
			if($rs_field[$j]["Field"] != "indivi_id" && $rs_field[$j]["Field"] != "package_id"
			&& $rs_field[$j]["Field"] != "strength_id"
			&& $rs_field[$j]["Field"] != "scrub_id" && $rs_field[$j]["Field"] != "wrap_id"
			&& $rs_field[$j]["Field"] != "bath_id" && $rs_field[$j]["Field"] != "facial_id"
			&& $rs_field[$j]["Field"] != "stream" && $rs_field[$j]["Field"] != "comments"
			&& $rs_field[$j]["Field"] != "b_set_inroom" && $rs_field[$j]["Field"] != "b_set_finish" 
			&& $rs_field[$j]["Field"] != "b_set_atspa") {
				if($count)
					$sql .= ",";
				if($rs_field[$j]["Field"] == "book_id"){$sql .= "\"".$book_id."\"";}
				else if($rs_field[$j]["Field"] == "room_id"){$sql .= "\"".$tw[$i]["room_id"]."\"";}
				else{$sql .= "\"".$rs[$i][$rs_field[$j]["Field"]]."\"";}
				
				$count++;
			}
		}		
		$sql .= ")";	
	}
	$sql .= ";";
	return $sql;
}
function th_sql($rs=false,$rs_field=false,$indivi_id=false,$book_id=false,$indivi_rows=false,$city_change=false,$start_id=false,$end_id=false){
	if(!$book_id) {return false;}
	
	$sql = "insert into da_mult_th(";
	$count = 0;
	$individ = $indivi_id;
	
	for($i=0; $i<$rs_field["rows"]; $i++) {
		if($rs_field[$i]["Field"] != "multh_id") {
				if($count)
					$sql .= ",";				
				$sql .= $rs_field[$i]["Field"];
				$count++;
		}
	}
	$sql .= ") values";
	for($i=0; $i<$rs["rows"]; $i++) {
		if($i){$sql .= ",";}
		else{$tmp=$rs[$i]["indivi_id"];$individ=$indivi_id;}
		if($indivi_rows==1){$individ=$indivi_id;}
		$count = 0;	
		$sql .= "(";				
		for($j=0; $j<$rs_field["rows"]; $j++) {
			if($rs_field[$j]["Field"] != "multh_id") {
				if($count)
					$sql .= ",";
					
				if($rs_field[$j]["Field"] == "book_id")	{
					$sql .= "\"".$book_id."\"";
				}else if($rs_field[$j]["Field"] == "indivi_id"){
					if($tmp != $rs[$i][$rs_field[$j]["Field"]]){
						$individ++;
						$tmp = $rs[$i][$rs_field[$j]["Field"]];
					}
					$sql .= "\"".$individ."\"";
				}else if($rs_field[$j]["Field"]=="therapist_id"){
					//If city change auto save therapist name to "-- select --".
					if($city_change){
						$sql .= "1";
					}else{
						$sql .= "\"".$rs[$i][$rs_field[$j]["Field"]]."\"";
					}
				}else if($rs_field[$j]["Field"]=="start_id"){
					$sql .= "$start_id";
				}else if($rs_field[$j]["Field"]=="end_id"){
					$sql .= "$end_id";
				}else{
					$sql .= "\"".$rs[$i][$rs_field[$j]["Field"]]."\"";
				}
				
				$count++;
			}
		}		
		$sql .= ")";	
	}
	$sql .= ";";
	return $sql;
}
function sr_sql($rs=false,$rs_field=false,$book_id=false){
	if(!$book_id) {return false;}
	
	$sql = false;
	$sql = "insert into c_salesreceipt(";
	$count = 0;
	
	for($i=0; $i<$rs_field["rows"]; $i++) {
		if($rs_field[$i]["Field"] != "salesreceipt_id") {
				if($count)
					$sql .= ",";				
				$sql .= $rs_field[$i]["Field"];
				$count++;
		}
	}
	$sql .= ") values";
	for($i=0; $i<$rs["rows"]; $i++) {
		if($i)
			$sql .= ",";
		$count = 0;	
		$sql .= "(";				
		for($j=0; $j<$rs_field["rows"]; $j++) {
			if($rs_field[$j]["Field"] != "salesreceipt_id") {
				if($count)
					$sql .= ",";
				
				if($rs_field[$j]["Field"] == "book_id")	
					$sql .= "\"".$book_id."\"";
				else
					$sql .= "\"".$rs[$i][$rs_field[$j]["Field"]]."\"";
				
				$count++;
			}
		}		
		$sql .= ")";	
	}
	$sql .= ";";
	return $sql;
}
function srd_sql($rs=false,$rs_field=false,$book_id=false){
	if(!$book_id) {return false;}
	
	$sql = false;
	$sql = "insert into c_srdetail(";
	$count = 0;
	
	for($i=0; $i<$rs_field["rows"]; $i++) {
		if($rs_field[$i]["Field"] != "srdetail_id") {
				if($count)
					$sql .= ",";				
				$sql .= $rs_field[$i]["Field"];
				$count++;
		}
	}
	$sql .= ") values";
	for($i=0; $i<$rs["rows"]; $i++) {
		if($i)
			$sql .= ",";
		$count = 0;	
		$sql .= "(";				
		for($j=0; $j<$rs_field["rows"]; $j++) {
			if($rs_field[$j]["Field"] != "srdetail_id") {
				if($count)
					$sql .= ",";
				
				if($rs_field[$j]["Field"] == "book_id")	
					$sql .= "\"".$book_id."\"";
				else
					$sql .= "\"".$rs[$i][$rs_field[$j]["Field"]]."\"";
				
				$count++;
			}
		}		
		$sql .= ")";	
	}
	$sql .= ";";
	return $sql;
}

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Copy To New Booking</title>
<link href="../css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="scripts/tooltip/boxover.js"></script>
<script src="scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="scripts/datechooser/datechooser.js" type="text/javascript"></script>

<!-- Begin Code Data Chooser -->
<link rel="stylesheet" type="text/css" href="scripts/datechooser/datechooser.css">
<body><br>
<div class="group5" width="100%" >
<fieldset>
<legend><b>Master Book ID : <font class="style1">
<?=$obj->getIdToText($book_id,"c_bpds_link","bpds_id","tb_id","tb_name = 'a_bookinginfo'")?>
</font></b></legend>
<form id="cpbooking" action="<?=$pagename?>" method="post">
<div id="showmsg" <? if(!isset($_GET["successmsg"])){?>style="display:none"<? } ?>>
	<table style="border: solid 3px #008000;" width="100%" cellspacing="0" cellpadding="10">
    	<tr>
    		<td><b><font style="color:#008000;">Success message: </font></b><?=$obj->getParameter("successmsg");?>
    		<br>This window will close in : 
    		<span id="countDown" class="style1"></span>
    		</td>
    	</tr>
	</table>
</div>
<div id="error" <? if($error==""&& $errorMsg==""&&$roomError==""){?>style="display:none"<? } else { ?>style="display:block"<? }?>>
    <table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    	<tr>
    		<td>
    		<b><img src="/images/errormsg.png"><font style="color:#ff0000"> Error message: </font></b>
    		<?=$errorMsg.$error.$roomError?></td>
    	</tr>
    </table>
</div>
<? if(!isset($_GET["successmsg"])) { ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cusinfo">
  <tr>
    <td width="100px">Copy this booking to</td>
    <td width="50px" align="right">Date : </td>
    <td><input id="apptdate" name="apptdate" value="<?=$date?>" style="width: 85px;" readonly="1" type="text"><!--<?=$afterEditDate?><?=$preEditDate?>-->
      &nbsp;&nbsp;<img src="scripts/datechooser/calendar.gif" onClick="showChooser(this, 'apptdate', 'date_showSpan', 1900, 2100, '<?=$sdateformat?>', false,<?=$chkReServation?>,'notCheck','notCheck');">
      <div id="date_showSpan" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div>
      </td>
  </tr>
  <tr>
    <td width="100px">&nbsp;</td>
    <td width="50px" align="right">Time : </td>
    <td>
    <?
    	echo $obj->makeListbox("appttime","p_timer","time_start","time_id",$appttime,1);
    ?></td>
  </tr>
  <tr>
    <td width="100px">&nbsp;</td>
    <td width="50px" align="right">Branch : </td>
    <td>
    <? 
    echo $obj->makeListbox("branch_id","bl_branchinfo","branch_name","branch_id",$branch_id,true,"branch_name","branch_active","1","branch_name not like 'All'",false,false,!$chkPageEdit,$branch_id);
    ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">
    	<?if($chkPageEdit){?><input type="submit" name="Copy" id="Copy" value="Copy" class="button"><?}?>
    </td>
  </tr>
  <tr>
    <td colspan="3">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="comment">
    	<tr>
          <td class="mainthead">Working Sheet</td>
          <td class="mainthead">Room No.</td>
          <td class="mainthead">Available</td>
        </tr>
  		<?
  			$chk_room_id = array();		//Array for check room id.
  			$chk_room_qty = array();	//Array for check qty people in room.
  			$chkroom = array();			//Array for check available room.
			for($i=0;$i<$ttpp;$i++){
					 
		?>
        <tr>
          <td>Working Sheet <?=$i+1?></td>
          <td><?=$obj->makeListbox("tw[$i][room_id]","bl_room","room_name","room_id",$tw[$i]["room_id"],1,"room_name","branch_id",$branch_id,"room_active=1 ")?>
          		<!-- add hidden field of member_use for send to function saveCopyAppointment for check member use of each working sheet -->
          		<input type="hidden" name="tw[<?=$i?>][member_use]" value="<?=$tw[$i]["member_use"]?>">
          		<input type="hidden" name="tw[<?=$i?>][indivi_id]" value="<?=$tw[$i]["indivi_id"]?>">
          </td>
          <td>
          <?
				// each individual room's maximum hour
				$hour[$i] = $rs_ind[$i]["hour_id"];
			
          		// fucntion checkEmptyRoom return false that mean the room is available.
          		// this function compare room from interface with database.
          		$chkroom[$tw[$i]["room_id"]] = $obj->checkEmptyRoom($hiddendate,$appttime,$hour[$i],$tw[$i]["room_id"]);
          		
          		// If room is available (check interface with databalse).
          		if($chkroom[$tw[$i]["room_id"]] == false){
          			// Check room on interface.
          			if(in_array($tw[$i]["room_id"],$chk_room_id)){
          				// If room is repeat room. Increase qty of people on this room.
						$chk_room_qty[$tw[$i]["room_id"]]++;
					}else{
						// If is new room initial qty of people to "1".
						$chk_room_id[$tw[$i]["room_id"]] = $tw[$i]["room_id"];
						$chk_room_qty[$tw[$i]["room_id"]] = 1;
					}
					
					// Check qty people in room must less then or equal qty people selected.
					$room_qty = $obj->getIdToText($tw[$i]["room_id"],"bl_room","room_qty_people","room_id");
					if($room_qty < $chk_room_qty[$tw[$i]["room_id"]]){
						$chkroom[$tw[$i]["room_id"]] = 1;
					}	
          		}
          		
          		if($chkroom[$tw[$i]["room_id"]] == false){
					echo"<b style='color:#008000;'>Available!!</b>";
          		}else{
          			$chkroom[$tw[$i]["room_id"]]=1;
					echo "<b class='style1'>Not Available!!</b>";
				}
					
          ?>
          </td>
        </tr>
 		<? 
			}
			// If found "1" in array chkroom set $roomError
			// "1" in array chkroom mean is some room has qty of people more than limit qty of that room.
			if(in_array("1",$chkroom)){
				$roomError = "Please change not available room to another !!";	
			}
			
		?>
      </table>
    </td>
  </tr>
</table>
<input type="hidden" name="book_id" value="<?=$book_id?>">
<input type="hidden" name="branchBeforeChange" value="<?=$branchBeforeChange?>">
<input type="hidden" name="ttpp" value="<?=$ttpp?>">
<input type="hidden" name="roomError" value="<?=$roomError?>">

<? } ?>
</form>

<br>
</fieldset>
</body>
</html>
<?
//////// Count down for close this window in 5 sec //// 
if(isset($_GET["successmsg"])){
?>
<script type="text/javascript">

document.getElementById("countDown").innerHTML=10
var i=5;
function countDown() 
{
 if(i >= 0)
 {
 	document.getElementById("countDown").innerHTML=i;
  	i = i-1;
  	var c = window.setTimeout("countDown()", 1000);
 }
 else 
 {
 	window.close();
 }
}
countDown();
</script>
<?}?>