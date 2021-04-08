<?
require_once("smsmysql.inc.php");

class smseg
{
	var $ip;	
	var $showpage;
	var $rdcount;
	var $u_id;
	var $errormsg;
	var $msg;
	var $msgcolor;
	
	var $column;
	var $lastid;
	var $affectedrows;
	
	function smseg()
	{		
		$this->msg = "";
		$this->ip = $_SERVER["REMOTE_ADDR"];
		$this->showpage = 20;	
		$this->msgcolor = "red";
		$this->errormsg = "";
		$this->column = 3;
		$this->affectedrows = 0;
		$this->u_id = $_SESSION["__user_id"];
		$this->u = $_SESSION["__user"];
		
	}		
		
	function get_ip()
	{
		return $this->ip;		
	}	
	
	function set_msg($newmsg)
	{
		$this->msg = $newmsg;
	}
	
	function get_msg()
	{
		return $this->msg;
	}
	
	function getParameter($value,$debug = false)
 	{
   		if(isset($_POST[$value]))
    	{
     		return $_POST[$value];
    	}
   		if(isset($_GET[$value]))
    	{
     		return $_GET[$value];
    	}
		if(isset($_FILES[$value]))
    	{
     		return $_FILES[$value];
    	}
    	
  	 	return $debug;
 	}
	
	function get_parameter($value,$debug = false)
 	{
   		if(isset($_POST[$value]))
    	{
     		return $_POST[$value];
    	}
   		if(isset($_GET[$value]))
    	{
     		return $_GET[$value];
    	}
    	if(isset($_FILES[$value]))
    	{
     		return $_FILES[$value];
    	}
  	  return $debug;
 	}
	
	function arr($a)
	{
		echo "<pre>";
			print_r($a);
		echo "</pre>";
	}
	
	function set_showpage($newshowpage = false)
	{
		if(!$newshowpage)
		{
		  $this->showpage = $this->get_showpage();
		}
		$this->showpage = $newshowpage;
	}
	
	function get_showpage()
	{
		return $this->showpage;
	}
	
	function set_rdcount($newrdcount)
	{
		$this->rdcount = $newrdcount;
	}
	
	function get_rdcount()
	{
		return $this->rdcount;
	}
	
	function get_lastid()
	{
		return $this->lastid;
	}
	
	function get_affectedrows()
	{
		return $this->affectedrows;
	}
	
 	function get_data($sql = false,$debug = false)
	{
		$m = new smsmysql($_SESSION["global_database"],$_SESSION["global_user"],$_SESSION["global_pass"]);
		 $rs = $m->getdata($sql,$debug);
		 $this->set_rdcount($m->get_recordcount());
		 if(!$rs)
		  {
			 $this->set_errormsg("<font color=red>".$m->get_msg()."</font>");
		  }
		unset($m);  
		return $rs;
	}

	function set_data($sql = false,$debug = false)
	{
		$m = new smsmysql($_SESSION["global_database"],$_SESSION["global_user"],$_SESSION["global_pass"]);
    // echo $SQL; 
	 	$id = $m->setdata($sql);	 
	
	 	$this->set_errormsg("<br> <b>Error</b>: <font color=red>".$m->get_msg()." </font><br> <b>SQL</b>: $sql");
	 	$this->lastid = $m->get_lastinsertid();
	 	$this->affectedrows = $m->get_affectedrow();
     	unset($m);
	   //  echo "m:$m";$lastid;
	//var $affectedrows;
	   // echo "id:$id";
		return $id;	
	}
	
	function get_totalpage()
	{
		exit("Please create this method in another class!!");
	}
	
 	function runpage($page=0,$link = false,$anotherpara = false,$showpage=false,$orderby=false)
 	{
 		if(!$showpage)
		{
			$showpage = $this->get_showpage();
		}
		//echo $showpage;
		if(!$link)
		{
			$link = '';
		}
		//echo $link;
		$totalpage = $this->get_totalpage($showpage,$anotherpara);
		//echo $totalpage;
		$j=1;	
	
		if($page > 1 && $totalpage > 1) {
			if($orderby)
				echo "<a href='$link?page=".($page-1)."&orderby=$orderby&$anotherpara[0]'>Prev</a> ";
			else	
	 			echo "<a href='$link?page=".($page-1)."&$anotherpara[0]'>Prev</a> ";
		}
		else
		{
			if($page > 1)
			{
	 	 	 echo "   ";
			}
		}

    	if($totalpage==1)
     		echo "<font color='#820901'> 1 </font>";
    	else
      
		for($i=0;$i<$totalpage;$i++)
	 	{
	 		if($i>0) {
	 		 echo "|";
			}
			
	 		if($j == $page) {
	 		 echo "<font color='#820901'> $j </font>";
			}
	 		else { 
				if($orderby)
					echo " <a href='$link?page=$j&orderby=$orderby&$anotherpara[0]'>$j</a> ";
				else
	 		 		echo " <a href='$link?page=$j&$anotherpara[0]'>$j</a> ";
			}
	 	 	$j++;
	 	}  
	
		if($page < $totalpage && $totalpage > 1)
		 {
		 	if($orderby)
		 	 echo " <a href='$link?page=".($page+1)."&orderby=$orderby&$anotherpara[0]'>Next</a> ";
			else
			 echo " <a href='$link?page=".($page+1)."&$anotherpara[0]'>Next</a> ";
		 }
   		 else
  		 {
    		if($page  > 1)
			{
         	   echo "   ";
			} 
    	 }
 	}
	
	function check_admin($u_id=false,$debug=false)
	{
		if(!$u_id) {
			$u_id = $this->get_userid_login();
			if($u_id <= 0)
			{
				$this->set_errormsg("User can't login!");
				return false;
			}
		}
		
		$sql = "select * from p_ugroup where u_id=".$u_id." ";
		$sql .= "and group_id=1 ";
		
		$rs = $this->get_data($sql);
		if($rs["rows"])
			return true;
		else
			return false;
	}
	
	function check_permission($u_id=false,$g_id=false,$can=false,$debug=false)
	{
		if(!$u_id)
		{
			$u_id = $this->get_userid_login();
			if($u_id <= 0)
			{
				$this->set_errormsg("User can't login!");
				return false;
			}
		}
		
		if($this->check_admin($u_id)) {
			return true;
		}
		
		if(!$g_id)
		{
			$this->set_errormsg("Error : main class in function check_permission() line 229");
			return false;			
		}
		
		if(!$can) {
			$this->set_errormsg("Error : main class in function check_permission() line 241");
			return false;
		}
		
		$g = explode(",",$g_id);
		$c = explode(",",$can);
		
		for($i=0; $i<count($g); $i++) {
		
			$sql = "select * from p_ugroup ";
			$sql .= "where u_id=".$u_id." ";
			$sql .= "and group_id=".$g[$i]." ";
			$sql .= "and (";
			
			for($j=0; $j<count($c); $j++) {
				$sql .= "can like '%".$c[$j]."%' or ";
			}

			
			$sql .= " can like '%all%')";
			
			
			if($debug) {
				echo $sql."<br>";
			}
			
			if(!$debug) {
				$rs = $this->get_data($sql);
			}
			
			if($rs["rows"])
				return true;
		
		}
		return false;
		
	}
	
	function get_ucan($u_id,$g_id,$can,$debug=false) {
		if(!$u_id)
			return false;
			
		if(!$g_id)
			return false;
			
			
		$sql = "select * from p_ugroup where u_id=".$u_id." and group_id=".$g_id." limit 1 ";
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		$rs = $this->get_data($sql);
		
		//return $rs;
		if(!$rs["rows"]) {
			return false;
			
		}
		
		$c = explode(",",$rs[0]["can"]);
		$c = array_keys($c,$can);
		
		if(count($c))
			return true;
		else
			return false;
	}
		
	function separate_time($timeinput=false,$to=false)
	{
		if($to==1) {
			list($year, $month, $day) = split('[/.-]',$timeinput);
			$timeoutput = $day."-".$month."-".$year;	
			return $timeoutput;		
		}
		if($to==2) {
			list($day, $month, $year) = split('[/.-]',$timeinput);
			$timeoutput = $year."-".$month."-".$day;		
			return $timeoutput;	
		}
		if($to==3) {
			list($year,$month,$day) = split('[/.-]',$timeinput);
			$timeoutput = date("d-M-y", mktime(0, 0, 0,$month,$day,$year));
			return $timeoutput;
     	}
		if($to==4) {
			list($year, $month, $day) = split('[/.-]',$timeinput);
			$timeoutput = $year."".$month."".$day;	
			return $timeoutput;		
		}
		
		list($day, $month, $year) = split('[/.-]',$timeinput);
		$timeoutput = $year."".$month."".$day;		
		return $timeoutput;	
		
	}

	
	
	function check_index($table=false)
	{
		if(!$table) {
			$this->set_errormsg("Please insert table name for search information!!");
			return false;
		}
		
		$sql = "select table_name from l_index where table_name like '$table'";
		$rs = $this->get_data($sql);
		return $rs["rows"];
	}	
	
	function keep_lastid($table=false,$last_id=false,$debug=false)
	{
		$next_id = $last_id+1;
		if(!$this->check_index($table)) {
			//echo "insert";
			$sql = "insert into l_index(table_name,last_id,next_id) value(\"$table\",$last_id,$next_id)";			
			return $this->set_data($sql);
		
		}
		
		$sql = "update l_index set last_id=".$last_id.",next_id=".$next_id." where table_name = \"$table\" limit 1 ";
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->set_data($sql);
	}
	
	
	
	function get_footdate($dateinput,$needtime=false)
	{
		if($needtime) {
			$buf = getdate(strtotime("$dateinput"));

			$y = $buf["year"];
			$m = $buf["month"];
			$wd = $buf["weekday"];
			$d = $buf["mday"];
			$h = $buf["hours"];
			
			
			if(strlen($buf["minutes"]) < 2)
				$min = "0".$buf["minutes"];
			else
				$min = $buf["minutes"];
			
			if(strlen($buf["seconds"]) < 2)
				$sec = "0".$buf["seconds"];
			else
				$sec = $buf["seconds"];
						
			return "$wd, $d $m $y, $h:$min:$sec";
		}
		
		list($year, $month, $day) = split('[/.-]',$dateinput);
		$buf = getdate(strtotime("$year-$month-$day"));
		$y = $buf["year"];
		$m = $buf["month"];
		$wd = $buf["weekday"];
		$d = $buf["mday"];
		//echo $buf['year'];
		
			
		
			
		
		
		return "$wd, $d $m $y";
	}
	
	function get_printdate($dateinput)
	{
		list($day, $month, $year) = split('[/.-]',$dateinput);
		$buf = getdate(strtotime("$year-$month-$day"));
		$y = $buf["year"];
		$m = $buf["month"];
		
		$d = $buf["mday"];

		return "$d-$m-$y";
	}
	
	function get_timeInAppt()
	{
		
		$sql = "select * from p_timer where time_id mod 6 = 1 and time_id < 176 and time_id > 18 limit 0,30";		
		return $this->get_data($sql);
			
	}
	
	function check_dataintable($data) //Go make for check data in table if it don't have intable "echo &nbsp;" in table
	{
		if(!$data)
		{
			return "&nbsp;";
		}
		return $data;
	}
	
	function get_IdToText($id=false,$table=false,$feild=false,$index=false)
	{
		if(!$id) {
			$this->set_errormsg("No have ID !!");
			return false;
		}
		
		$sql = "select ".$feild." from ".$table." where ".$index."=".$id." limit 1";
		$row = $this->get_data($sql);
		return $row[0]["".$feild.""];		
	}
	
	
	
	function make_dropdown($return_para=false,$field_text=false,$field_id=false,$rs=false,$receive_para=false,$refresh=false,$size,$id=false,$javascript=false,$event=false)
	{

		$msg = "<select name=".$return_para." style=\"color: #000000; width: $size; font:Tahoma, Verdana; font-size:11px\" ";
					if($id)
						$msg .="id=".$id." ";
					
					
					if($javascript)
						$msg .= "";
						
					if($event)
						$msg .="disabled=\"disabled\" ";
						
					if($refresh){
						$msg .= " onChange=\"addProductcount(1);this.form.submit();\">";
					}else{
						$msg .= ">\n ";
					}	
				for($i=0; $i<$rs["rows"]; $i++)
				{
					if ($rs[$i]["pd_category_id"] != $rs[$i-1]["pd_category_id"]) {
						$msg .= "<optgroup label=\"" . $rs[$i]["pd_category_name"] . "\" title=\"" . $rs[$i]["pd_category_name"] . "\">";
					}
					
					$msg .= "<option title=\"".$rs[$i]["$field_text"]."\" value=".$rs[$i]["$field_id"];
					if ($rs[$i]["$field_id"] == $receive_para) {
						$msg .= " selected=\"selected\"";
					}
					if($event && $rs[$i]["$field_id"]=="19"){
						$msg .= " selected=\"selected\" ";
					}							
					$msg .= ">";
																				
					$msg .= $rs[$i]["$field_text"]."</option>";
				
					if (isset ($rs[$i+1]["pd_category_id"]) && $i && $rs[$i]["pd_category_id"] != $rs[$i+1]["pd_category_id"]) {
						$msg .= "</optgroup>";
					}
				}

				$msg .= "</select>";
			
		return $msg;
	}
	
	function get_cmsdate($dateinput) {
	
			$buf = getdate(strtotime("$dateinput"));
			
			$y = $buf["year"];
			$m = $buf["mon"];			
			$d = $buf["mday"];			
						
			if(strlen($buf["minutes"]) < 2)
				$min = "0".$buf["minutes"];
			else
				$min = $buf["minutes"];
			
			if(strlen($buf["hours"]) < 2)
				$h = "0".$buf["hours"];
			else
				$h = $buf["hours"];
			
			
			return $d."/".$m."/".$y." ".$h.":".$min;
	}
	
	
	function getProduct() {
		$sql = "select cl_product.*,cl_product_category.pd_category_name from cl_product,cl_product_category " .
				"where cl_product.pd_active=1 " .
				"and cl_product.pd_category_id=cl_product_category.pd_category_id " .
				"and cl_product.pd_category_id in (1,2,6) " .
				"order by pd_category_name,cl_product.pd_name ";
		return $this->get_data($sql);
	}
 	
 	function getPrice($product_id=false) {
		$sql = "select standard_price from cl_product where pd_id=$product_id";
		$rs = $this->get_data($sql);
		$rs_tmp["standard_price"]=$rs[0]["standard_price"];
	return $rs_tmp["standard_price"];
	}
	
	function getScTax($product_id=false,$tax=false) {
		if($tax){
			$sql = "select set_sc from cl_product where pd_id=$product_id";
		}else{
			$sql = "select set_tax from cl_product where pd_id=$product_id";
		}
		
		$rs = $this->get_data($sql);
		
		if($tax){
			$rs_tmp["set_sc"]=$rs[0]["set_sc"];
			return $rs_tmp["set_sc"];
		}else{
			$rs_tmp["set_tax"]=$rs[0]["set_tax"];
			return $rs_tmp["set_tax"];
		}
	
	}
 		
 	function set_errormsg($newMsg=false) {
		$this->errormsg = $newMsg;
	}
	
	function getMember($member_id=false) {
		$sql = "select * from m_membership where member_id=$member_id";
		$rs = $this->get_data($sql);
		$member_tmp["member_id"]=$rs[0]["member_id"];
		$member_tmp["member_code"]=$rs[0]["member_code"];
		if($rs[0]["mname"]){
			$member_tmp["member_name"]=$rs[0]["fname"]." ".$rs[0]["mname"]." ".$rs[0]["lname"];
		}else{
			$member_tmp["member_name"]=$rs[0]["fname"]." ".$rs[0]["lname"];
		}
		$member_tmp["nationality_id"]=$rs[0]["nationality_id"];
		$member_tmp["phone"]=$rs[0]["phone"];
		$member_tmp["email"]=$rs[0]["email"];
		$member_tmp["sex_id"]=$rs[0]["sex_id"];
		$member_tmp["birthdate"]=$rs[0]["birthdate"];
		$member_tmp["csageinroom"]=$this->birthday($rs[0]["birthdate"]);
		$member_tmp["mpic"]=$rs[0]["mpic"];
	return $member_tmp;
	}
	
	function birthday ($birthday)
  	{
    list($year,$month,$day) = explode("-",$birthday);
    $year_diff  = date("Y") - $year;
    $month_diff = date("m") - $month;
    $day_diff   = date("d") - $day;
    if ($month_diff < 0) $year_diff--;
    elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
    return $year_diff;
  	}
  	
  	function checkRoom($apptdate = false, $starttime = false, $hour = false, $branchid = false) {
		//Add 15 mins After
		$hour_cal = $this->get_IdToText($hour, "l_hour", "hour_calculate", "hour_id");
		$hour_cal+=0.25;
		$hour = $this->get_IdToText($hour_cal, "l_hour", "hour_id", "hour_calculate");
		//
		
		// convert time start to minute
		$starttime_min = 60 * (8 + floor(($starttime -1) / 12)) + 5 * (($starttime -1) % 12);
		
		// find time finish in each room
		$hour_name = $this->get_IdToText($hour, "l_hour", "hour_name", "hour_id");
		list ($hr, $min, $sec) = explode(":", $hour_name);
		$hour_min = (60 * $hr) + $min;
		$endtime_min = $starttime_min + $hour_min;	// time finish in minute
				
		// convert time finish to id
		$endtime = 12 * (floor( $endtime_min / 60 ) - 8) + 1 + 
		( $endtime_min - 60 * floor( $endtime_min / 60 )) / 5; 
		
		//Add 15 mins before
			$starttime-=3;
		//
				
		$sql1 = "select a_bookinginfo.book_id, d_indivi_info.room_id, a_bookinginfo.b_appt_time_id as start_time," .
		"a_bookinginfo.b_book_hour,l_hour.hour_name,l_hour.hour_calculate, " . 
		"max(d_indivi_info.hour_id) as max_hour from a_bookinginfo,da_mult_th,d_indivi_info,l_hour where " .
		"a_bookinginfo.book_id=d_indivi_info.book_id " .
		"and d_indivi_info.book_id = da_mult_th.book_id " .
		"and da_mult_th.indivi_id=d_indivi_info.indivi_id " .
		"and l_hour.hour_id = d_indivi_info.hour_id " .
		"and a_bookinginfo.b_appt_date=$apptdate " .
		"and a_bookinginfo.b_branch_id=$branchid " .
		"and a_bookinginfo.b_set_cancel=0 ";

		$sql1 .= "group by da_mult_th.indivi_id";

		$sql2 = "select r_maintenance.rm_id as book_id, r_maintenance.room_id, r_maintenance.appt_time as start_time," .
		"r_maintenance.hour_id as b_book_hour,l_hour.hour_name,l_hour.hour_calculate, " .
		"r_maintenance.hour_id as max_hour from r_maintenance,l_hour where " .
		" r_maintenance.appt_date=$apptdate " .
		"and l_hour.hour_id = r_maintenance.hour_id " .
		"and r_maintenance.branch_id=$branchid " .
		"and r_maintenance.set_cancel=0 ";

		$sql = "($sql1) union ($sql2)";

		$rs = $this->get_data($sql);
		//echo "this booking start-end :$starttime $endtime<br>";
		$room = array(); $cnt = 0;
		
		if ($rs["rows"] > 0) {
			for ($i = 0; $i < $rs["rows"]; $i++) {
				
				// convert time start to minute
				$time_start_min[$i] = 60 * (8 + floor(($rs[$i]["start_time"] -1) / 12)) + 5 * (($rs[$i]["start_time"] -1) % 12);
				// find time finish in each room
				$hour_name = $rs[$i]["hour_name"];
				list ($hr, $min, $sec) = explode(":", $hour_name);
				$max_hour_min[$i] = (60 * $hr) + $min;
				$time_end_min[$i] = $time_start_min[$i] + $max_hour_min[$i];	// time finish in minute
				
				// convert time finish to id
				$rs[$i]["end_time"] = 12 * (floor( $time_end_min[$i] / 60 ) - 8) + 1 + 
				( $time_end_min[$i] - 60 * floor( $time_end_min[$i] / 60 )) / 5; 
				
				if ($starttime >= $rs[$i]["start_time"] && $starttime < $rs[$i]["end_time"]) {
					$room[$cnt] = $rs[$i]["room_id"];
					$cnt++;
				}else 
				if ($endtime > $rs[$i]["start_time"] && $endtime <= $rs[$i]["end_time"]) {
					$room[$cnt] = $rs[$i]["room_id"];
					$cnt++; 
				}else 
				if ($starttime <= $rs[$i]["start_time"] && $endtime >= $rs[$i]["end_time"]) {
					$room[$cnt] = $rs[$i]["room_id"];
					$cnt++; 
				}else 
				if ($starttime >= $rs[$i]["start_time"] && $endtime <= $rs[$i]["end_time"]) {
					$room[$cnt] = $rs[$i]["room_id"];
					$cnt++;
				}
			}
		}
		return array_values(array_unique($room));

	}
	
	function InsertBookingData($member,$location,$tthour,$csnum,$appointment_date,$appoint_time,$app_roomid,$app_roomname,$app_qty_peoples,$cnt_hour,$cnt_status){
		//print_r($member);
		$appdate=date('Y-m-d', strtotime($appointment_date));
		$ip = $_SERVER["REMOTE_ADDR"];
		$tax_id = $this->get_IdToText($location, "bl_branchinfo", "tax_id", "branch_id");
		$servicescharge = $this->get_IdToText($location, "bl_branchinfo", "servicescharge", "branch_id");
		
		$sql = "insert into a_bookinginfo(a_member_code,b_branch_id,b_book_hour," .
				"b_customer_name,b_customer_phone,b_qty_people,b_appt_date,b_appt_time_id," .
				"l_lu_user,l_lu_date,l_lu_ip,tax_id,servicescharge,book_type) ";
		
		$sql .= "values(".$member["member_code"].",$location,$tthour,'".$member["member_name"]."','".$member["phone"]."'," .
				"$csnum,'$appdate',$appoint_time,'".$member["member_id"]."',now(),'$ip',$tax_id,$servicescharge,2)";
		$bookid=$this->set_data($sql);
		
		$sql_bpds="insert into c_bpds_link(tb_id,tb_name) values($bookid,'a_bookinginfo')";
		$bpdsid=$this->set_data($sql_bpds);
		
		$mcategory_id=$this->get_IdToText($member["member_id"], "m_membership", "category_id", "member_id");
		$mcategory=$this->get_IdToText($mcategory_id, "mb_category", "category_name", "category_id");
		$sql_app="insert into a_appointment(bpds_id,book_id,branch_id,appt_date,appt_time_id,customer_name," .
				"member_code,mcategory,room_ids,room_names,qty_peoples,hour_ids,status,app_type) " .
				"values($bpdsid,$bookid,$location,'$appdate',$appoint_time,'".$member["member_name"]."'," .
				"'".$member["member_code"]."|','$mcategory','$app_roomid','$app_roomname','$app_qty_peoples','$cnt_hour','$cnt_status',2)";
	
		$appid=$this->set_data($sql_app);

		$loop_qty=explode("|", $app_qty_peoples);
		$loop_room=explode("|", $app_roomid);
		for($k=0;$k<count($loop_qty);$k++){
	
		$sql_indi_member="insert into d_indivi_info(book_id,cs_name,cs_phone,cs_email," .
				"cs_age,cs_birthday,room_id,nationality_id,sex_id,member_use," .
				"hour_id) values($bookid,'".$member["member_name"]."','".$member["phone"]."'," .
				"'".$member["email"]."','".$member["csageinroom"]."','".$member["birthdate"]."'," .
				"'".$loop_room[$k]."','".$member["nationality_id"]."'," .
				"'".$member["sex_id"]."',1,$tthour)";
				
		$sql_indi="insert into d_indivi_info(book_id,cs_name,cs_phone,cs_email," .
				"cs_age,cs_birthday,room_id,nationality_id,sex_id,member_use," .
				"hour_id) values($bookid,'',''," .
				"'','','','".$loop_room[$k]."',''," .
				"'',0,$tthour)";
		
			for($l=0;$l<$loop_qty[$k];$l++){
				if($l==0){
					if($k==0){
						$in_id=$this->set_data($sql_indi_member);
						$sql_damulth="insert into da_mult_th(book_id,indivi_id,therapist_id,hour_id) " .
									"values($bookid,$in_id,1,$tthour)";
						$this->set_data($sql_damulth);
					}else{
						$in_id=$this->set_data($sql_indi);
						$sql_damulth="insert into da_mult_th(book_id,indivi_id,therapist_id,hour_id) " .
									"values($bookid,$in_id,1,$tthour)";
						$this->set_data($sql_damulth);
					}
				}else{
						$in_id=$this->set_data($sql_indi);
						$sql_damulth="insert into da_mult_th(book_id,indivi_id,therapist_id,hour_id) " .
									"values($bookid,$in_id,1,$tthour)";
						$this->set_data($sql_damulth);
				}
			}
		}
		
		for($l=0;$l<$csnum;$l++){
			$sql_damulth="insert into da_mult_th(book_id,indivi_id,therapist_id,hour_id) " .
					"values()";
		}
		
		/*
		$sql_log = "insert into log_c_bp(book_id,b_customer_name,c_set_cms,c_bp_id,c_pcms_id," .
				"l_lu_user,l_lu_date,l_lu_ip) " .
				"values($bookid,'".$member["member_name"]."',0,1,1,'".$member["member_id"]."',now(),'$ip')";
		$logid=$this->set_data($sql_log);
		*/
		if($bookid&&$bpdsid&&$appid){
			return true;
		}else{
			return false;
		}
	}
	
	function checkLogin(){	
		if(isset($_SESSION["__member_id"])&& isset($_SESSION["__member_code"])){
			return $_SESSION["__member_id"];		
		}else{
			return false;
		}
	}
	
	function login($user, $pass){	
		$sql_u = "select * from m_membership where member_code = '$user' ";
		$rs_id=$this->get_data($sql_u);
			if($rs_id["rows"] < 1) {
				$this->set_msg("Member not found...");
				return false;
			}
		if($rs_id){
			$sql_pass = "select * from m_membership where member_code='$user' and member_pass = '$pass' ";
			$rs_pass=$this->get_data($sql_pass);
				if($rs_pass["rows"] < 1) {
					$this->set_msg("Password is incorrect...");
				return false;
				}else{
					$_SESSION["__member_id"]=$rs_id[0]["member_id"];
					$_SESSION["__member_code"]=$rs_id[0]["member_code"];
					return true;
				}
		}
	
	}
	function online(){
		$sql_u = "select * from m_membership where member_id = '".$_SESSION["__member_id"]."' " .
				"and member_code = '".$_SESSION["__member_code"]."' ";
		$rs_id=$this->get_data($sql_u);
			if($rs_id["rows"] < 1) {
				return false;
			}else{
				return true;
			}
	}
	
	function getMemberBalance($membercode){
	
	$sum = 0;
	// for oasis's old member history
		require_once ("destiny.inc.php");
		$objomh = new destiny();
		$rs = $objomh->get_memberhistory($membercode);
		
		for ($i = 0; $i < $rs["rows"]; $i++) {
			if ($rs[$i]["catagory_id"] == 11) {
				$sum += $rs[$i]["total"];
			} else {
				$sum -= $rs[$i]["total"];
			}
		}

	//table a_bookinginfo
		$sql1 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"\"a_bookinginfo\" as tb_name,";
		$sql1 .= "a_bookinginfo.book_id as book_id,";
		$sql1 .= "a_bookinginfo.servicescharge as servicescharge,";
		$sql1 .= "l_tax.tax_percent as taxpercent,";
		$sql1 .= "cl_product.pd_name as pd_name,";
		$sql1 .= "c_srdetail.unit_price as unit_price,";
		$sql1 .= "c_srdetail.qty as quantity,";
		$sql1 .= "cl_product_category.plus_minus_value as plus_minus_value,";
		$sql1 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql1 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql1 .= "c_srdetail.set_tax as plus_vat,";
		$sql1 .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql1 .= "a_bookinginfo.b_branch_id as branch_id,";
		$sql1 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql1 .= "bl_branchinfo.branch_name,";
		$sql1 .= "c_srdetail.pd_id as pd_id,";
		$sql1 .= "c_srdetail.salesreceipt_id as salesreceipt_id,";
		$sql1 .= "c_salesreceipt.sr_total, ";	
		$sql1 .= "a_bookinginfo.b_appt_date as appt_date ";
		$sql1 .= "from c_bpds_link,a_bookinginfo,c_salesreceipt,c_srdetail,cl_product,cl_product_category,l_tax,bl_branchinfo ";
		$sql1 .= "where a_bookinginfo.book_id = c_bpds_link.tb_id ";
		$sql1 .= "and c_bpds_link.tb_name = \"a_bookinginfo\" ";
		$sql1 .= "and a_bookinginfo.tax_id = l_tax.tax_id ";
		$sql1 .= "and a_bookinginfo.book_id=c_srdetail.book_id ";
		$sql1 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql1 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql1 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql1 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($membercode){$sql1 .= "and a_bookinginfo.a_member_code = \"$membercode\" ";}
		$sql1 .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql1 .= "and a_bookinginfo.b_set_cancel<>1 ";
		$sql1 .= "and c_srdetail.pd_id<>1 ";	


	//table c_saleproduct
		$sql2 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"c_bpds_link.tb_name as tb_name,";
		$sql2 .= "c_saleproduct.pds_id as book_id,";
		$sql2 .= "c_saleproduct.servicescharge as servicescharge,";
		$sql2 .= "l_tax.tax_percent as taxpercent,";
		$sql2 .= "cl_product.pd_name as pd_name,";
		$sql2 .= "c_srdetail.unit_price as unit_price,";
		$sql2 .= "c_srdetail.qty as quantity,";
		$sql2 .= "cl_product_category.plus_minus_value as plus_minus_value,";
		$sql2 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql2 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql2 .= "c_srdetail.set_tax as plus_vat,";
		$sql2 .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql2 .= "c_saleproduct.branch_id as branch_id,";
		$sql2 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql2 .= "bl_branchinfo.branch_name,";
		$sql2 .= "c_srdetail.pd_id as pd_id,";
		$sql2 .= "c_srdetail.salesreceipt_id as salesreceipt_id,";
		$sql2 .= "c_salesreceipt.sr_total, ";
		$sql2 .= "c_saleproduct.pds_date as appt_date ";
		$sql2 .= "from c_saleproduct,c_salesreceipt,c_srdetail,cl_product,cl_product_category,c_bpds_link,l_tax,bl_branchinfo ";
		$sql2 .= "where c_saleproduct.tax_id = l_tax.tax_id ";
		$sql2 .= "and c_saleproduct.pds_id = c_bpds_link.tb_id ";
		$sql2 .= "and c_saleproduct.pds_id=c_srdetail.pds_id ";
		$sql2 .= "and c_bpds_link.tb_name = \"c_saleproduct\" ";
		$sql2 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql2 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql2 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql2 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($membercode){$sql2 .= "and c_saleproduct.a_member_code = \"$membercode\" ";}
		$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
		$sql2 .= "and c_saleproduct.set_cancel<>1 ";
		$sql2 .= "and c_srdetail.pd_id<>1";

		$sqlm = "($sql1) union ($sql2) order by appt_date,pos_neg_value desc,bpds_id,branch_id,srdetail_id";
		
		$rsm=$this->get_data($sqlm);
		
	$product = array ();
	$chkColor = 1;
	$product["balance"] = $sum;
	$balanceindex = 0;
		for ($i = 0; $i < $rsm["rows"]; $i++) {
			if ($rsm[$i]["plus_minus_value"] == 1) {
				$product["total"] = $rsm[$i]["amount"];
				$product["balance"] += $product["total"];
				$img_action = "plus.gif";
				$minus = "";
				
			}else{
				if ($rsm[$i]["pd_id"]==80){
     				    $product["set_sc"] = $rsm[$i]["plus_servicecharge"];
						$product["set_tax"] = $rsm[$i]["plus_vat"];
     				    $product["total"] = $rsm[$i]["amount"];
     				    if($rsm[$i]["plus_servicecharge"]==1){
     				    	$product["set_sc"] = ($rsm[$i]["amount"]*7)/100;	
     				    }else{
     				    	$product["set_sc"]=0;
     				    }
     				    if($rsm[$i]["plus_vat"]==1){
     				    	$product["set_tax"] = (($rsm[$i]["amount"]+$product["set_sc"])*10)/100; 
     				    }else
     				    {
     				    	$product["set_tax"]=0;
     				    }
     				    $product["total"] = $rsm[$i]["amount"] + $product["set_sc"] + $product["set_tax"];
						$product["balance"] -= $product["total"];
						$img_action = "minus.gif";
						$minus = "<span style='color:red'>";
						
						if(number_format($product["balance"],2,".",",")==(0.00)){
 							$product["balance"]=abs(number_format($product["balance"],2,".",","));
 						}
     			}
			}
		}
	return $product["balance"];
	}
	
	function getCompanny(){
		$strSQL = "SELECT company_name FROM a_company_info ";
		$strSQL .= "WHERE company_id=1";
	
		$rs=$this->get_data($strSQL,false);
		
		return $rs[0]["company_name"];
		
	}
	
}
?>
