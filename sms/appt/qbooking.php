<?php
include("../include.php");

$obj->setLimitBranchOnLocation();
$obj->setDebugStatus(false);

$errorMsg="";$error="";

$date = $obj->getParameter("apptdate",date($sdateformat));
$hiddendate = $dateobj->convertdate($date,$sdateformat,"Ymd");	// it can have bug years 19xx-20xx

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

//Check branch's date/time 
$branch_id = $obj->getParameter("branchid");

if($obj->getParameter("appttime")){
	$appttime = $obj->getParameter("appttime");
}else{
	$obj->setBranchid($branch_id);
	$appttime=$obj->getStartTimeid();;
}


if($branch_id){
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


if($obj->getParameter("qcs",false)){
	$qcs = $obj->getParameter("qcs",false);
}else{
	$qcs["ttpp"]=1;
	$qcs["tthour"]=2;
}

$ttpp = $qcs["ttpp"];
$tthour = $qcs["tthour"];

$roomError=$obj->getParameter("roomError","");
$cs = $obj->getParameter("cs",false);
$tw = $obj->getParameter("tw",false);

//Debug
if(!isset($cs["name"])){$cs["name"]="";}
if(!isset($cs["memid"])){$cs["memid"]="";}
if(!isset($cs["phone"])){$cs["phone"]="";}
if(!isset($cs["bpname"])){$cs["bpname"]="";}
if(!isset($cs["bpphone"])){$cs["bpphone"]="";}
if(!isset($cs["bcompany"])){$cs["bcompany"]="";}
if(!isset($cs["inspection"])){$cs["inspection"]="";}

$data["cs[branch]"] = $branch_id;
$data["cs[apptdate]"] = $date;
$data["cs[hidden_apptdate]"] = $dateobj->convertdate($date,$sdateformat,'Ymd');
$data["cs[appttime]"] = $appttime;
$data["cs[tthour]"] = $tthour;
$data["cs[ttpp]"] = $ttpp;

$data["cs[name]"] = $cs["name"];
$data["cs[memid]"] = $cs["memid"]; 
$data["cs[phone]"] = $cs["phone"];
$data["cs[bpname]"] = $cs["bpname"];
$data["cs[bpphone]"] = $cs["bpphone"];


$data["cs[bcompany]"] = $cs["bcompany"];
$data["cs[inspection]"] = $cs["inspection"];

if($tw){
//Debug
if(!isset($tw[0]["hidden_csbday"])){$tw[0]["hidden_csbday"]="";}
if(!isset($tw[0]["csnameinroom"])){$tw[0]["csnameinroom"]="";}
if(!isset($tw[0]["csphoneinroom"])){$tw[0]["csphoneinroom"]="";}
if(!isset($tw[0]["csemail"])){$tw[0]["csemail"]="";}
if(!isset($tw[0]["national"])){$tw[0]["national"]="";}
if(!isset($tw[0]["sex"])){$tw[0]["sex"]="";}

$data["tw[0][csnameinroom]"] = $tw[0]["csnameinroom"];
$data["tw[0][csphoneinroom]"] = $tw[0]["csphoneinroom"];
$data["tw[0][csemail]"] = $tw[0]["csemail"];

if($tw[0]["hidden_csbday"]!="0000-00-00"){
	if($tw[0]["hidden_csbday"]!=""){
		$data["tw[0][csbday]"] = $tw[0]["csbday"]; 
		$data["tw[0][hidden_csbday]"] = $tw[0]["hidden_csbday"];
		$data["tw[0][csageinroom]"]= $tw[0]["csageinroom"];	
	}
}
$data["tw[0][national]"] = $tw[0]["national"];
$data["tw[0][sex]"] = $tw[0]["sex"];

	if($cs["memid"]){
		$data["tw[0][member_use]"] = "checked";
	}

for($k=0;$k<$ttpp;$k++){
$data["tw[$k][tthour]"] = $tthour;
$data["tw[$k][0][hour]"] = $tthour;
}


}

if($obj->getParameter("giftnumber","")){
$data["giftChk"] = true;
$data["addgift"]= true;
$giftnumber=$obj->getParameter("giftnumber","");
$data["giftnumber"]=$giftnumber;
}

if($obj->getParameter("qtw")){
	$qtw = $obj->getParameter("qtw");
	if($ttpp>count($qtw)){
		
		$chkrs = $obj->checkRoom($hiddendate,$appttime,$tthour,$branch_id);
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
		$cnt=count($qtw);$chk="";
		$cntallemproom=0;
		
		for($i=0;$i<$nbroomrs["rows"];$i++){
			if($cnt>$ttpp-1){$chk="break"; break;}
			for($j=0;$j<count($qtw);$j++){
				if($nbroomrs[$i]["room_id"]==$qtw[$j]["room_id"]){
					$nbroomrs[$i]["room_qty_people"]--;
				}
			}
	  		for($k=0;$k<$nbroomrs[$i]["room_qty_people"];$k++){
	  			$qtw[$cnt]["room_id"]=$nbroomrs[$i]["room_id"];
	  			$data["tw[$cnt][room]"] = $qtw[$cnt]["room_id"];
	  			$cnt++;
	  		}
	 		if($chk=="break"){break;}
		}	
	}
}else{

	$chkrs = $obj->checkRoom($hiddendate,$appttime,$tthour,$branch_id);
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
					$qtw[$cnt]["room_id"]=$nbroomrs[$i]["room_id"];
					$data["tw[$cnt][room]"] = $qtw[$cnt]["room_id"];
					$cnt++;
			}
			if($chk=="break"){break;}
		}
		
		//fill busy room to qtw when don't have any room available.
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
					$qtw[$cnt]["room_id"] = $broomrs[$i]["room_id"];	
					$data["tw[$cnt][room]"] = $qtw[$cnt]["room_id"];
					$cnt++;	
				}
			}
		}
		if($cntallemproom<$ttpp){$roomError="Please check room!!";}else{$roomError="";}
 }

for($i=0;$i<$ttpp;$i++){
	
	//if($i!=0){
			//		if(!$qtw[$i]["room_id"]){
			//			$qtw[$i]["room_id"]=$qtw[$i-1]["room_id"];
			//			$data["tw[$i][room]"] = $qtw[$i]["room_id"];
			//		}
		 // }
		  
		$data["tw[$i][room]"] = $qtw[$i]["room_id"];
}

	
$sdata = http_build_query($data, '$data[]');
$pagename = "qbooking.php?$sdata";
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Booking</title>
<link href="../css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="scripts/tooltip/boxover.js"></script>
<script src="scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="scripts/datechooser/datechooser.js" type="text/javascript"></script>
<script src="scripts/ajax.js" type="text/javascript"></script>
<script type="text/javascript">
function checkNum(obj){
	if(isNaN(obj.value)) {
			obj.value = 1;
			alert("Please check Total people of booking use number only!!");
	}else if(obj.value==0) {
			obj.value = 1;
			alert("Please check Total people of booking will more than 0!!");
	}
}
</script>
<!-- Begin Code Data Chooser -->
<link rel="stylesheet" type="text/css" href="scripts/datechooser/datechooser.css">
<body><br>
<div class="group5" width="100%" >
<fieldset>
<legend><b>Add this booking to<font class="style1">
<?if(!isset($book_id)){$book_id="";}?>
<?=$obj->getIdToText($book_id,"c_bpds_link","bpds_id","tb_id","tb_name = 'a_bookinginfo'")?>
</font></b></legend>

<div id="error" style="display:none;">	
    <table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    	<tr>
    		<td>
    		<b><img src="/images/errormsg.png"><font style="color:#ff0000"> Error message: </font></b>
    		Please change "not available" room to another !!</td>
    	</tr>
    </table>
</div>


<form id="cpbooking" action="<?=$pagename?>" method="post">
<? if(!isset($_GET["successmsg"])) { ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cusinfo">
  <tr>
    <td width="50px"></td>
    <td align="right">Date : </td>
    <td><input id="apptdate" name="apptdate" value="<?=$date?>" style="width: 85px;" readonly="1" type="text"><!--<?=$afterEditDate?><?=$preEditDate?>-->
      &nbsp;&nbsp;<img src="scripts/datechooser/calendar.gif" onClick="showChooser(this, 'apptdate', 'date_showSpan', 1900, 2100, '<?=$sdateformat?>', false,<?=$chkReServation?>,'notCheck','notCheck');">
      <div id="date_showSpan" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div>
      </td>
  </tr>
  <tr>
    <td width="50px">&nbsp;</td>
    <td align="right">Time : </td>
    <td>
    <?
    	echo $obj->makeListbox("appttime","p_timer","time_start","time_id",$appttime,true);
    ?></td>
  </tr>
  
  <tr>
    <td align="right"  colspan="2">Total Hours : </td>
    <td>
    <?=$obj->makeListbox("qcs[tthour]","l_hour","hour_name","hour_id",$qcs["tthour"],true,"hour_name",0,0,"hour_id>1 and hour_name >= \"00:30:00\"")?></td>
  </tr>
  
  <tr>
    <td width="50px">&nbsp;</td>
    <td align="right">Branch : </td>
    <td>
    <? 
    echo $obj->makeListbox("branchid","bl_branchinfo","branch_name","branch_id",$branch_id,true,"branch_name","branch_active","1","branch_name not like 'All'",false,false,!$chkPageEdit,$branch_id);
    ?></td>
  </tr>
  <tr>

  <tr>
    <td align="right" colspan="2">Total People : </td>
    <td><input type="text" value="<?=$qcs["ttpp"]?>" maxlength="2" id="qcs[ttpp]" name="qcs[ttpp]" onChange="checkNum(this);this.form.submit();"/>
  </tr>
  <tr>
  
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">
    	<?if($chkPageEdit){?><input type="button" name="Add" id="Add" value="Add" class="button"
    	href="javascript:;" onClick="if(document.getElementById('roomError').value){
		document.getElementById('error').style.display = 'block';
	}else{
		Popup('manage_booking.php?<?=$sdata?>&chkpage=1&date='+document.getElementById('apptdate').value+'&branch='+document.getElementById('branchid').value,'manage_booking');
	}"><?}?>
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
			for($i=0;$i<$qcs["ttpp"];$i++){
					 
		?>
        <tr>
          <td>Working Sheet <?=$i+1?></td>
          <?
          //if($i!=0){
		//			if(!$qtw[$i]["room_id"]){
		//				$qtw[$i]["room_id"]=$qtw[$i-1]["room_id"];
		//				$data["tw[$i][room]"] = $qtw[$i]["room_id"];
		//			}
		//  }
          ?>
          <td><?=$obj->makeListbox("qtw[$i][room_id]","bl_room","room_name","room_id",$qtw[$i]["room_id"],1,"room_name","branch_id",$branch_id,"room_active=1 ")?></td>
          <td>
          <?
				// each individual room's maximum hour
				$hour[$i] = $tthour;
				
          		// fucntion checkEmptyRoom return false that mean the room is available.
          		// this function compare room from interface with database.
          		$chkroom[$qtw[$i]["room_id"]] = $obj->checkEmptyRoom($hiddendate,$appttime,$hour[$i],$qtw[$i]["room_id"]);
          		
          		// If room is available (check interface with databalse).
          		if($chkroom[$qtw[$i]["room_id"]] == false){
          			// Check room on interface.
          			if(in_array($qtw[$i]["room_id"],$chk_room_id)){
          				// If room is repeat room. Increase qty of people on this room.
						$chk_room_qty[$qtw[$i]["room_id"]]++;
					}else{
						// If is new room initial qty of people to "1".
						$chk_room_id[$qtw[$i]["room_id"]] = $qtw[$i]["room_id"];
						$chk_room_qty[$qtw[$i]["room_id"]] = 1;
					}
					
					// Check qty people in room must less then or equal qty people selected.
					$room_qty = $obj->getIdToText($qtw[$i]["room_id"],"bl_room","room_qty_people","room_id");
					if($room_qty < $chk_room_qty[$qtw[$i]["room_id"]]){
						$chkroom[$qtw[$i]["room_id"]] = 1;
					}	
          		}
          		
          		if($chkroom[$qtw[$i]["room_id"]] == false){
					echo"<b style='color:#008000;'>Available!!</b>";
          		}else{
          			$chkroom[$qtw[$i]["room_id"]]=1;
					echo "<b class='style1'>Not Available!!</b>";
				}
				
          ?>
          </td>
        </tr>
 		<? 
			}		
			
			if(in_array("1",$chkroom)){
				$roomError = "Please change not available room to another !!";	
			}else{
				$roomError= "";
			}
		?>
      </table>
    </td>
  </tr>
</table>
<input type="hidden" name="roomError" id="roomError" value="<?=$roomError?>">
<script type="text/javascript">
function checkErr(){
	if(document.getElementById('roomError').value){
		document.getElementById('error').style.display = 'block';
	}
}
	checkErr();
</script>
<? } ?>
</form>

<br>
</fieldset>
</body>
</html>