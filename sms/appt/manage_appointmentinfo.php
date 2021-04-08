<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
$obj->setDebugStatus(false);
$branchid= $obj->getParameter("bid");
$date = $obj->getParameter("date");
$chkPageEdit = $obj->getParameter("chkPageEdit",$chkPageEdit);	// check user can/can't edit all branch
$chkRsView = (isset($chkRsView))?$chkRsView:"";
$chkRsView = $obj->getParameter("chkRsView",$chkRsView);
// ################ branch parameter ###############
$starttime_id = $obj->getIdToText($branchid,"bl_branchinfo","start_time_id","branch_id");
$closetime_id = $obj->getIdToText($branchid,"bl_branchinfo","close_time_id","branch_id");

// ################ room parameter ###############
$chksql = "select * from bl_room where room_active=1 and branch_id=$branchid order by room_name asc";
$roomrs = $obj->getResult($chksql);
$count_room = $roomrs["rows"]; 

// ################ time line of booking #########
$tp_id = $obj->getIdToText("1","a_company_info","tp_id","company_id");
$chksql = "select * from l_timeperiod where tp_id=$tp_id";
$timeperiodrs = $obj->getResult($chksql);
$time_period = $timeperiodrs[0]["tp_name"];
$time_period_distance = $timeperiodrs[0]["tp_distance"];
$chksql = "select * from p_timer where time_id between $starttime_id and $closetime_id";
$timeline = $obj->getResult($chksql); // get time to use in appointment
$count_time = $timeline["rows"]; // count all record to use in first column

// ############### appointment Path #####################
$rs = $obj->getMainappointment($dateobj->convertdate($date,$sdateformat,"Ymd"),$branchid);
	
// system's time period
$sql = "select * from p_timer";
$timeperiodrs = $obj->getResult($sql);
$timeperiod = array();
for($i=0;$i<$timeperiodrs["rows"];$i++){
		$timeperiod[$timeperiodrs[$i]["time_id"]] = $timeperiodrs[$i]["time_start"];
}
	
// system's hour period
$sql = "select * from l_hour";
$hourperiodrs = $obj->getResult($sql);
$hourperiod = array();
for($i=0;$i<$hourperiodrs["rows"];$i++){
		$hourperiod[$hourperiodrs[$i]["hour_id"]] = $hourperiodrs[$i]["hour_name"];
}
	
$appointment[] = array();
$appointment["book_id"] = $appointment["start"] = $appointment["cs_name"] = $appointment["cs_hotel"] = $appointment["ttpp"] =array();
$appointment["bp_person"] = $appointment["bp_name"] = "";
$appointment["room_ids"] = $appointment["room_names"] = $appointment["therapist_names"] = array();		
$appointment["hour_ids"] = $appointment["driver_names"] = $appointment["driver_times"] = $appointment["driver_place"] = $appointment["end"] = array();
$appointment["driver_place"] = $appointment["colormark"] = $appointment["timeperiod"] = array();
$appointment["rskey"] = array();
for($i=0; $i<$rs["rows"]; $i++) {
	$appointment["mem_code"][$i] = $appointment["ttpp"][$i] = 
	$appointment["room_ids"][$i] = $appointment["room_names"][$i] = 
	$appointment["therapist_names"][$i] = $appointment["hour_ids"][$i] = 
	$appointment["driver_names"][$i] = $appointment["driver_times"][$i] = $appointment["driver_place"][$i] =
	$appointment["end"][$i] = $appointment["colormark"][$i] =
	array();
	$appointment["bpds_id"][$i] = $rs[$i]["bpds_id"];	
	$appointment["book_id"][$i] = $rs[$i]["book_id"];	
	$appointment["cs_name"][$i] = $rs[$i]["customer_name"];
	$appointment["mem_code"][$i] = explode("|",$rs[$i]["member_code"]);
	$appointment["mcategory"][$i] = $rs[$i]["mcategory"];
	$appointment["cs_hotel"][$i] = $rs[$i]["accdt_name"];
	$appointment["ttpp"][$i] = explode("|",$rs[$i]["qty_peoples"]);
	$appointment["bp_person"][$i] = $rs[$i]["bp_person"];
	$appointment["bp_name"][$i] = $rs[$i]["bp_name"];
	$appointment["room_ids"][$i] = explode("|",$rs[$i]["room_ids"]);	
	$appointment["room_names"][$i] = explode("|",$rs[$i]["room_names"]);		
	$appointment["therapist_names"][$i] = explode("|",$rs[$i]["th_names"]);		
	$appointment["hour_ids"][$i] = explode("|",$rs[$i]["hour_ids"]);
	$appointment["driver_names"][$i] = explode(",",$rs[$i]["t_names"]);	
	$appointment["driver_times"][$i] = explode(",",$rs[$i]["t_times"]);	
	$appointment["driver_place"][$i] = explode(",",$rs[$i]["t_places"]);	
	$appointment["timeperiod"][$i] = $rs[$i]["tp_distance"];	
	$appointment["cal_start"][$i] = $rs[$i]["appt_time_id"];
	$appointment["cal_end"][$i] = $obj->chkBlockTimeEnd($appointment["hour_ids"][$i],$rs[$i]["appt_time_id"],$hourperiod,$timeline);
	$appointment["start"][$i] = $obj->chkBlockTimeStart($rs[$i]["appt_time_id"],$time_period_distance,$timeline);
	$appointment["end"][$i] = $obj->chkBlockTimeEnd($appointment["hour_ids"][$i],$rs[$i]["appt_time_id"],$hourperiod,$timeline,$rs[$i]["tp_distance"]);
	
	if(isset($appointment["driver_place"][$i][0])&&str_replace(' ','',$appointment["driver_place"][$i][0])==''){
		$appointment["driver_place"][$i][0] = $appointment["cs_hotel"][$i];
	}
	if(isset($appointment["driver_place"][$i][1])&&str_replace(' ','',$appointment["driver_place"][$i][1])==''){
		$appointment["driver_place"][$i][1] = $appointment["cs_hotel"][$i];
	} 

	$appointment["colormark"][$i] = $obj->chkBlockColor($rs[$i]["status"],$rs[$i]["app_type"]);
	$appointment["app_type"][$i]=$rs[$i]["app_type"];
	$appointment["rskey"][$i] = $i;
	
}
$appointment["rows"] = $rs["rows"];
//echo $date;

?>
<span align="right">
					<div id="booking" align="left" style="padding-bottom:10px;">
                    <input type="hidden" id="EditBid" name="EditBid" value="">
                    <input type="hidden" id="EditRoomid" name="EditRoomid" value="">
                     <input type="hidden" id="ChangeStatus" name="ChangeStatus" value="">
                       <table cellpadding="4" cellspacing="0" class="appt">	
                       <tr>
                       	<td height="30px" class="mainheader">Cust.</td>
                       	<td height="30px" class="mainheader">TH.</td>
                       	<td class="mainheader">Time</td>
                       <?	for($i=0; $i<$count_room; $i++) {  // make head line.
								echo "<td class=\"mainheader\">".$roomrs[$i]["room_name"]."</td>\n";
							}	
					   ?>
                       </tr>
                       <?
                       	for($i=0; $i<$timeline["rows"]; $i=$i+$time_period_distance) {
							$timestamp = strtotime($timeline[$i]["time_start"]);
							$startptimeid = $timeline[$i]["time_id"];
							$startptime = date("H:i",$timestamp);
							if(isset($timeline[$i+$time_period_distance]["time_start"])){
								$timestamp = strtotime($timeline[$i+$time_period_distance]["time_start"]);
								$lastptime = date("H:i",$timestamp);
							}else{
								$timestamp = strtotime($timeline[$i]["time_start"]);
								$etime = strtotime("+$time_period minutes", $timestamp);
								$lastptime = date('H:i', $etime);
							}
                       		if($timeline[$i]["time_id"]!=$closetime_id){
								$lastptimeid = $startptimeid + $time_period_distance;
								$cstartptime = "";
								$style = "border-top:solid 0px;";
								$check = 0;
								for($j=$i;$j<$i+$time_period_distance;$j++){
									if(isset($timeline[$j]["time_id"])&&$timeline[$j]["time_id"]%12==1){
											$check = 1;
											$cstartptime = strtotime($timeperiod[$timeline[$j]["time_id"]]);
											$cstartptime = date("H:i",$cstartptime);
											$style .= "border-top:#6e9fb4 2px groove;";
											break;
									}
									if(isset($timeline[$j]["time_id"])&&$timeline[$j]["time_id"]%12==7){
											$check = 2;
											$style .= "border-top:dashed #6e9fb4 1px;";
											break;
									}
								}
	                       		echo "<tr align=\"center\">\n";
								echo "<td align=\"center\" style=\"$style\">";
								$obj->getTherapistcount($appointment,$startptimeid,$lastptimeid,"cust");
								echo "</td>";
								echo "<td align=\"center\" style=\"$style\">";
								$obj->getTherapistcount($appointment,$startptimeid,$lastptimeid,"th",$dateobj->convertdate($date,$sdateformat,"Y-m-d"),$branchid);
								echo "</td>";
								echo "<td align=\"center\" style=\"background-color:#eae8e8;$style\">&nbsp;&nbsp;$cstartptime&nbsp;&nbsp;</td>\n";
								for($j=0; $j<$count_room; $j++) {
									$block = array();
									$block["color"] = "";$block["popup"]="";$block["data"]="";
									$block = $obj->chkBlockData($appointment,$roomrs[$j]["room_id"],$startptimeid,$lastptimeid,$chkPageEdit,$timeperiod,$chkRsView);
									$color = $obj->checkParameter($block["color"],"");
									if($color!=""){
									$color="background-color:$color;";
									}
									$popup = $obj->checkParameter($block["popup"],"");
									$data = $obj->checkParameter($block["data"],"");
									echo "<td style=\"$color $style; white-space:nowrap;\" $popup>&nbsp;$data&nbsp;</td>";
								}
								echo "</tr>\n";
                       		}
                       	} 
                       ?>
                       </table>
                     </div>
      
				
        		<? if($obj->getProductSale($dateobj->convertdate($date,$sdateformat,"Y-m-d"),$branchid)!=""){ ?>
				<div id="booking" align="left" style="padding-bottom:10px;">
        		<fieldset>
					<legend><b>Product Sale Information</b></legend>			
                       <table cellpadding="4" cellspacing="0" width="100%">	
                       <tr>
                       	<td><? echo $obj->getProductSale($dateobj->convertdate($date,$sdateformat,"Y-m-d"),$branchid,$chkPageEdit); ?></td>
                       </tr>
                       </table>
        		</fieldset>	
				</div>
        		<? } ?>

				
        		<? if($obj->getCancelBooking($dateobj->convertdate($date,$sdateformat,"Ymd"),$branchid)!=""){ ?>
				<div id="booking" align="left" style="padding-bottom:10px;">
        		<fieldset>
					<legend><b>Bookings Canceled</b></legend>			
                       <table cellpadding="4" cellspacing="0" width="100%">	
                       <tr>
                       	<td><?=$obj->getCancelBooking($dateobj->convertdate($date,$sdateformat,"Ymd"),$branchid,$chkPageEdit)?></td>
                       </tr>
                       </table>                     
        		</fieldset>
				</div>
        		<? } ?>

				
				<div id="booking" align="left" style="padding-bottom:10px;">
        		<? if($obj->getCancelRM($dateobj->convertdate($date,$sdateformat,"Ymd"),$branchid)!=""){ ?>
        		<fieldset>
					<legend><b>Room Maintance Canceled</b></legend>
                       <table cellpadding="4" cellspacing="0" width="100%">	
                       <tr>
                       	<td><?=$obj->getCancelRM($dateobj->convertdate($date,$sdateformat,"Ymd"),$branchid,$chkPageEdit)?></td>
                       </tr>
                       </table>
        		</fieldset>
        		<? } ?>
				 </div>
				 
				 