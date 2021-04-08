<?php

/*
 * File name : cms.inc.php
 * Description : Class file which is main controller for cms system
 * Author : natt
 * Create date : 22-Oct-2008
 * Modified : natt@chiangmaioasis.com
 */
require_once ("cms.inc.php");
class appt extends cms {
	protected $branchid;
	protected $starttimeid;
	protected $closetimeid;
	protected $starttime;
	protected $closetime;
	protected $modtime;
	protected $apptperiod; //appointment time period
	protected $apptprdist; //appointment time period distance
	protected $limitBranchOnLocation = false;

	function appt() {
		$this->branchid = 0;
		$this->starttimeid = 1;
		$this->closetimeid = 187;
		$this->apptperiod = 30;
		$this->apptprdist = 6;
		$this->modtime = $this->starttimeid % $this->apptprdist;
		$this->starttime = date("H:i:s", mktime(8, 0, 0));
		$this->closetime = date("H:i:s", mktime(23, 30, 0));
	}

	/*
	 * set branch_id and another param for appointment page
	 * @param - branch id
	 */
	function setBranchid($newbid, $debug = false) {
		$this->branchid = $newbid;
		//chk start time and close time form table bl_branchinfo
		$sql = "select * from bl_branchinfo where branch_id=$newbid";
		$branchrs = $this->getResult($sql, $debug);
		$this->starttimeid = $branchrs[0]["start_time_id"];
		$this->closetimeid = $branchrs[0]["close_time_id"];
		// chk time period id form table a_company_info
		$sql = "select tp_id from a_company_info";
		$companyrs = $this->getResult($sql, $debug);
		$tp_id = $companyrs[0]["tp_id"];

		// find time period form table l_timeperiod
		$sql = "select * from l_timeperiod where tp_id=$tp_id";
		$timeperiodrs = $this->getResult($sql, $debug);
		$this->apptperiod = $timeperiodrs[0]["tp_name"];
		$this->apptprdist = $timeperiodrs[0]["tp_distance"];

		//find start time
		$sql = "select * from p_timer where time_id=" . $this->starttimeid;
		$timers = $this->getResult($sql, $debug);
		$this->starttime = $timers[0]["time_start"];
		//find close time
		$sql = "select * from p_timer where time_id=" . $this->closetimeid;
		$timers = $this->getResult($sql, $debug);
		$this->closetime = $timers[0]["time_start"];

		$this->modtime = $this->starttimeid % $this->apptprdist;

	}
	/*
	 * function setLimintBranchOnLocation for allow can select branch in add/edit on manage_booking page
	 * Create by : Ruk 20-02-2009
	 * 
	*/
	function setLimitBranchOnLocation() {
		$this->limitBranchOnLocation = true;
	}

	function getBranchid() {
		return $this->branchid;
	}

	function getStartTimeid() {
		return $this->starttimeid;
	}

	function getCloseTimeid() {
		return $this->closetimeid;
	}

	function getApptPeriod() {
		return $this->apptperiod;
	}

	function getApptDistance() {
		return $this->apptprdist;
	}

	/*
	 * auto calculate individual finish time
	 * @param - appointment time
	 * @param - total individual hour
	 * @modified - add this function in 20-Dec-2008
	 */
	function getIndFinishTime($appttime_id = false, $tthour_id = false) {
		if (!$appttime_id) {
			$this->setErrorMsg("Please insert appointment time for get information!!");
			return false;
		}

		if (!$tthour_id) {
			$this->setErrorMsg("Please insert total hour id for get information!!");
			return false;
		}
		$appttime = $this->getIdToText($appttime_id, "p_timer", "time_start", "time_id");
		$tthour = $this->getIdToText($tthour_id, "l_hour", "hour_name", "hour_id");
		list ($appthr, $apptmi, $apptse) = split(":", $appttime);
		list ($tthourhr, $tthourmi, $tthourse) = split(":", $tthour);
		$fintimehr = $appthr + $tthourhr;
		$fintimemi = $apptmi + $tthourmi;
		$fintimese = $apptse + $tthourse;
		$fintime = date("H:i:s", mktime($fintimehr, $fintimemi, $fintimese));

		return $fintime;
	}

	/*
	 * auto generate select box form database
	 * @param $sname - selectbox name
	 * @param $tbname - table name
	 * @param $fieldname - field name
	 * @param $fieldid - field id
	 * @param $selected - selected id
	 * @param $chkautosubmit - check form auto submit
	 * @param $order - sql query language "order by $order"
	 * @param $wherename,$wherechk,$andparam,$orparam - sql query language "where $wherename=$wherechk and $andparam or $orparam"
	 * @param $disabled - disable selectbox
	 * Modify : 27-03-2009
	 * @param $notEditBook - check page can/can't edit specific for bl_branchinfo - now not use
	 * @param $bookBranchId - set for lock show bl_branchinfo table saperate by branch - now not use
	 * Modify : 25-05-2009
	 * @param $javascript - add javascript in select input 
	 */
	function makeListbox($sname = false, $tbname = false, $fieldname = false, $fieldid = false, $selected = false, $chkautosubmit = false, $order = false, $wherename = false, $wherechk = false, $andparam = false, $orparam = false, $disabled = false, $notEditBook = false, $bookBranchId = false, $javascript = false) {
		$limitBranch = false;
		if (!$tbname) {
			$this->setErrorMsg("Please insert table name to create list box!!");
			return false;
		}
		if ($tbname == "p_timer") {
			//$sql = "select time_id,time_start from p_timer where time_id mod " . $this->apptprdist . " = " . $this->modtime . " and time_id >= " . $this->starttimeid . " and time_id < " . $this->closetimeid . " ";
			$sql = "select time_id,time_start from p_timer where time_id >= " . $this->starttimeid . " and time_id < " . ($this->closetimeid - 3) . " ";
		} else
			if ($tbname == "l_hour") {
				$saperatetime = strtotime($this->closetime) - strtotime($this->starttime);
				$saperatetime = date("H:i:s", mktime(0, 0, $saperatetime));
				$sql = "select hour_id,hour_name from l_hour where hour_name<\"$saperatetime\" ";
				//$sql = "select hour_id,hour_name from l_hour where hour_name<\"$saperatetime\" and ROUND(hour_calculate*60) mod " . $this->apptprdist . " = 0 ";
			} else
				if ($tbname == "cl_product_category,cl_product") {
					$sql = "select $fieldid,$fieldname,cl_product.pd_category_id,cl_product_category.pd_category_name from $tbname";
				} else
					if ($tbname == "l_marketingcode,l_mkcode_category") {
						$sql = "select $fieldid,$fieldname,l_marketingcode.category_id,l_mkcode_category.category_name from $tbname";
					} else
						if ($this->limitBranchOnLocation && $tbname == "bl_branchinfo") {
							$scObj = new secure();
							if ($scObj->isEditBookInLocation()) {
								$sql = "select $fieldid,$fieldname from $tbname";
							} else {
								$limitBranch = true;
								$sql = "select $fieldid,$fieldname from $tbname where branch_active=1 and city_id=" . $scObj->getUserLocationId();
							}
						} else {
							$sql = "select $fieldid,$fieldname from $tbname";
						}

		if ($wherename && !$limitBranch) {
			$sql .= " where $wherename=$wherechk";
		}

		if ($andparam) {
			$sql .= " and $andparam";
		}

		if ($orparam) {
			$sql .= " or $orparam";
		}

		if ($order) {
			$sql .= " order by $order";
		}

		$row = $this->getResult($sql);
		$count = $row["rows"];
		$row[-1] = null;
		
		$tmp = explode("[", $sname);
		//echo $selected;
		if ($sname == "cs[tthouradd]" || $sname == "cs[tthouredit]") {
			$name = "cs[tthour]";
		} else
			if ($sname == "cs[appttimeadd]" || $sname == "cs[appttimeedit]") {
				$name = "cs[appttime]";
			} else {
				$name = $sname;
			}

		echo "<select id=\"$name\" name=\"$name\" ";

		if ($disabled)
			echo " disabled ";

		if ($javascript && !$chkautosubmit) {
			echo " onChange=\"$javascript\" ";
		}

		if ($sname == "cs[tthouradd]") {
			echo " onChange=\"setMaxtthour(this.options[this.selectedIndex].value);this.form.submit();\">";
		}
		if ($sname == "cs[appttimeadd]") {
			echo " onChange=\"setMaxappttime(this.options[this.selectedIndex].value);this.form.submit();\">";
		}

		if (isset ($chkautosubmit) && $chkautosubmit != false) {
			if ($tbname == "cl_product_category,cl_product") {
				echo " onChange=\"addSrd(" . $chkautosubmit[0] . "," . $chkautosubmit[1] . ",1);this.form.submit();\">";
			} else {
				echo " onChange=\"this.form.submit();\">";
			}
		} else {
			echo ">";
		}
		if ($tbname == "dl_sex") {
			echo "<option title=\"\" value=\"0\"></option>";
		}
		if ($tbname == "mb_category") {
			echo "<option title=\"\" value=\"0\"></option>";
		}

		$chkroom = true; //check status in room if room is in the same branch 
		if ($tbname == "bl_room") {
			$chkroom = $this->getIdToText($selected, $tbname, $tbname, $fieldid, "$wherename=$wherechk");
		}

		// update for prevent history of inventory missing inactive information on list box on appointment page
		if ($wherename && !$limitBranch && $chkroom) {
			$chksql = "select * from $tbname where $wherename!=$wherechk and $fieldid=$selected";
			$chkrs = $this->getResult($chksql);
			if ($chkrs["rows"] > 0 && $chkrs[0]["$fieldid"] == $selected) {
				echo "<option title=\"" . $chkrs[0]["$fieldname"] . "\" value=\"" . $chkrs[0]["$fieldid"] . "\" selected=\"selected\">" . $chkrs[0]["$fieldname"] . "</option>";
			}
		}

		for ($i = 0; $i < $count; $i++) {
			if ($tbname == "cl_product_category,cl_product") {
				if ($row[$i]["pd_category_id"] != $row[$i -1]["pd_category_id"]) {
					echo "<optgroup label=\"" . $row[$i]["pd_category_name"] . "\" title=\"" . $row[$i]["pd_category_name"] . "\">";
				}
			}
			if ($tbname == "l_marketingcode,l_mkcode_category") {
				if ($row[$i]["category_id"] != $row[$i -1]["category_id"]) {
					echo "<optgroup label=\"" . $row[$i]["category_name"] . "\" title=\"" . $row[$i]["category_name"] . "\">";
				}
			}
			echo "<option title=\"" . $row[$i]["$fieldname"] . "\" value=" . $row[$i]["$fieldid"];
			if ($row[$i]["$fieldid"] == $selected) {
				echo " selected=\"selected\"";
			}
			if ($tbname == "l_hour") {
				if(substr($row[$i]["$fieldname"], 3, 2)=="00"||substr($row[$i]["$fieldname"], 3, 2)=="30"){
					echo " style=\"background-color:#d7d7d7\"";
				}
			}
			
			echo ">";
			if ($tbname == "l_hour") {
				$data = substr($row[$i]["$fieldname"], 0, 5);
			} else {
				$data = $row[$i]["$fieldname"];
			}
			echo $data . "</option>";
			if ($tbname == "cl_product_category,cl_product") {
				if (isset ($row[$i +1]["pd_category_id"]) && $i && $row[$i]["pd_category_id"] != $row[$i +1]["pd_category_id"]) {
					echo "</optgroup>";
				}
			}
		}

		echo "</select>";
	}

	/*
	 * auto calculate service charge of each product
	 * @param $product - product array all value
	 * @param $j - index of product
	 * @modified - add this function on 10 dec 2008
	 */
	function getsSvc($product = false, $j) {
		if ($product["set_sc"][$j]) {
			if ($product["servicescharge"][$j]) {
				$servicecharge = ($product["total"][$j]) * ($product["servicescharge"][$j] / 100);
			} else {
				$servicecharge = 0;
			}
		} else {
			$servicecharge = 0;
		}
		//echo $product["total"][$j].'+'.$servicecharge.',';
		return $servicecharge;
	}

	/*
	 * auto calculate tax of each product
	 * @param $product - product array all value
	 * @param $j - index of product
	 * @param $svc - service charge of this product
	 * @modified - add this function on 10 dec 2008
	 */
	function getsTax($product = false, $j, $svc = false) {
		if ($product["set_tax"][$j]) {
			$tax = ($product["total"][$j] + $svc) * ($product["taxpercent"][$j] / 100);
		} else {
			$tax = 0;
		}
		//echo $tax."<br/>";
		return $tax;
	}

	/*
	 * auto generate selectbox of Therapist
	 * @param $sname - selectbox name
	 * @param $selected - selected id
	 * @param $chkautosubmit - check form auto submit
	 * @param $order - sql query language "order by $order"
	 * @param $andparam - sql query language "where emp_active=1 and emp_department_id=4 and $andparam "
	 * @modified - add this function on 10 dec 2008
	 */
	function makeTherapistlist($sname = false, $selected = false, $chkautosubmit = false, $order = false, $andparam = false) {
		$sql = "select * from l_employee left join bl_branchinfo on l_employee.branch_id=bl_branchinfo.branch_id where l_employee.emp_active=1 and l_employee.emp_id!=1 and l_employee.emp_department_id=4 ";

		if ($andparam) {
			$sql .= "and " . $andparam . " ";
		}

		if ($selected && $selected != 1) {
			$sql .= " or l_employee.emp_id=$selected ";
		}

		if ($order)
			$sql .= "order by $order ";

		$row = $this->getResult($sql);

		//echo $selected;
		//echo $sql;
		echo "<select id=\"" . $sname . "\" name=\"" . $sname . "\" ";
		if ($chkautosubmit)
			echo " onChange=\"this.form.submit();\">";
		else
			echo ">";

		//echo "<option value=0> --select--</option>";
		echo "<option value='1'> --select--</option>";
		for ($i = 0; $i < $row["rows"]; $i++) {

			$b_code = $this->getIdToText($row[$i]["branch_id"], "bl_branchinfo", "branch_code", "branch_id");

			echo "<option value=" . $row[$i]["emp_id"];

			if ($row[$i]["emp_id"] == $selected) {
				echo " selected=\"selected\"";
			}

			echo ">";

			if ($row[$i]["branch_id"] && $row[$i]["emp_id"] != 1)
				echo $b_code . " " . $row[$i]["emp_code"] . " " . $row[$i]["emp_nickname"] . "</option>";
			else
				if ($row[$i]["emp_id"] == 1)
					echo " " . $row[$i]["emp_nickname"] . "</option>";
				else
					echo $b_code . " " . $row[$i]["emp_nickname"] . "</option>";
		}
		echo "</select>";

	}

	/*
	 * auto generate driver take back time form appointment time id and total hour form booking system
	 * @param $appttime_id - appointment time id
	 * @param $tthour - total hour id from booking system
	 * @modified - add this function on 11 dec 2008
	 */
	function getTbtime($appttime_id = false, $tthour_id = false, $debug = false) {
		if (!$appttime_id) {
			$this->setErrorMsg("Please insert appointment time for get information!!");
			return false;
		}

		if (!$tthour_id) {
			$this->setErrorMsg("Please insert total hour id for get information!!");
			return false;
		}
		$appttime = $this->getIdToText($appttime_id, "p_timer", "time_start", "time_id");
		$tthour = $this->getIdToText($tthour_id, "l_hour", "hour_name", "hour_id");
		list ($appthr, $apptmi, $apptse) = split(":", $appttime);
		list ($tthourhr, $tthourmi, $tthourse) = split(":", $tthour);
		$tbtimehr = $appthr + $tthourhr;
		$tbtimemi = $apptmi + $tthourmi +10;
		$tbtimese = $apptse + $tthourse;
		$tbtime = date("H:i:s", mktime($tbtimehr, $tbtimemi, $tbtimese));
		$sql = "select time_id from p_timer where time_start='$tbtime'";

		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		$rs = $this->getResult($sql);
		return $rs[0]["time_id"];

	}

	/*
	 * add information into table a_bookinginfo
	 * @param $cs - all customer and booking information
	 * @param $cancel - set cancel confirm
	 * @param $cms - set cms confirm
	 * @param $trf - set transportration confirm
	 * @modified - add this function on 11 dec 2008
	 */
	function add($cs = false, $cancel = false, $cms = false, $trf = false, $debug = false) {
		$memcode = 0; // id
		$branch = $cs["branch"]; // id
		$tthour = $cs["tthour"]; // id
		$csname = htmlspecialchars($cs["name"]); // text
		$csphone = htmlspecialchars($cs["phone"]); // text
		$cms = $cs["cms"];
		if (!$csname) {
			$this->setErrorMsg("Please insert customer name!!");
			return false;
		}
		if (!$csphone || substr($csphone, 0, 1) != "+" || substr($csphone, 1, 1) == "0" || strlen($csphone) == 1) {
			if ($csphone != "") {
				$this->setErrorMsg("Please check phone number format must be '+ countrycode citycode number' !!");
				return false;
			}
		}

		$cs["refid"] = ($cs["refid"] != "") ? $cs["refid"] : 0;
		$chksql = $this->getIdToText($cs["refid"], "c_bpds_link", "bpds_id", "bpds_id");
		if (!$chksql && $cs["refid"] != 0) {
			$this->setErrorMsg(" Please check book id: " . $cs["refid"] . " doesn't have in the system!!");
			return false;
		}

		if ($cs["memid"]) {
			$memberSql = "select member_id,expired,expireddate from m_membership where member_code=" . $cs["memid"];
			$memberRs = $this->getResult($memberSql);

			// Check if has this member on table m_membership
			if ($memberRs) {
				$expiredDateMember = str_replace("-", "", $memberRs[0]["expireddate"]);
				$nowDate = date("Ymd");

				// If member code not set to inacitve 
				// and expired date of member more than or equal today or expired date of member is "00000000".
				// Then set this member code save into database.
				if ($memberRs[0]["expired"] && ($expiredDateMember >= $nowDate || $expiredDateMember == "00000000")) {
					$memcode = $cs["memid"];
				} else if (!$memberRs[0]["expired"]) {
						$this->setErrorMsg("This member has been set to inactive. Please check again!!");
						return false;
				} else if ($cs["hidden_apptdate"] > $expiredDateMember) {
						$this->setErrorMsg("Appointment date more than member expired date. Please check again!!");
						return false;
				} else {
						$this->setErrorMsg("This member has been expired. Please check again!!");
						return false;
				}
			} else {
				$this->setErrorMsg("Invalid member code. Please check again!!");
				return false;
			}
		}

		$ttpp = $cs["ttpp"]; // number
		$hotel = $cs["hotel"]; // id
		$roomnum = htmlspecialchars($cs["roomnum"]); // text
		// $trf is b_pickup_confirm
		// For debug undefined index : trf. By Ruck : 18-05-2009 // 
		// change if(isset($trf)) to if($trf=="checked") 
		if ($trf == "checked")
			$trf = 1;
		else
			$trf = 0;

		// $cancel is b_cancel_confirm
		// For debug undefined index : cc. By Ruck : 18-05-2009 // 		
		// change if(isset($cancel)) to if($cancel=="checked")
		if ($cancel == "checked")
			$cancel = 1;
		else
			$cancel = 0;

		// $cms is c_set_cms
		// For debug undefined index : cc. By Ruck : 18-05-2009 // 		
		// change if(isset($cms)) to if($cms=="checked")
		if ($cms == "checked")
			$cms = 1;
		else
			$cms = 0;

		if ($cs["atspa"])
			$atspa = 1;
		else
			$atspa = 0;

		//$apptdate = str_replace('-','',$this->separate_time($cs["apptdate"],5,0)); // year month day => 20060102		
		$apptdate = $cs["hidden_apptdate"];
		$apptime = $cs["appttime"]; // id
		$rs = $cs["rs"]; // id
		$rc = $cs["rc"]; // id

		$bcompany = $cs["bcompany"]; // id
		$bpname = htmlspecialchars($cs["bpname"]); // text
		$bpphone = htmlspecialchars($cs["bpphone"]); // text
		$bcms_id = $cs["cms_percent"]; // id

		if (!$bpphone || substr($bpphone, 0, 1) != "+" || substr($bpphone, 1, 1) == "0" || strlen($bpphone) == 1) {
			if ($bpphone != "") {
				$this->setErrorMsg("Please check phone number format must be '+ countrycode citycode number' !!");
				//return false;
			}
		}

		$tax_id = $this->getIdToText($branch, "bl_branchinfo", "tax_id", "branch_id");
		$servicescharge = $this->getIdToText($branch, "bl_branchinfo", "servicescharge", "branch_id");
		$ip = $_SERVER["REMOTE_ADDR"];
		$userid = $this->getUserIdLogin();
		$inspection_id = $cs["inspection"]; // id

		$sql = "insert into a_bookinginfo(b_branch_id,b_qty_people,b_appt_date,b_appt_time_id,b_book_hour," .
		"c_bp_id,c_bp_person,c_bp_phone,c_set_cms,c_pcms_id," .
		"b_customer_name,b_customer_phone,a_member_code,b_accomodations_id,b_hotel_room," .
		"b_set_cancel,b_set_pickup,b_reservation_id,b_receive_id," .
		"b_set_atspa,c_lu_user,c_lu_date,c_lu_ip," .
		"l_lu_user,l_lu_date,l_lu_ip,tax_id,servicescharge,mkcode_id) ";
		$sql .= "values($branch,$ttpp,$apptdate,$apptime,$tthour," .
		"$bcompany,\"$bpname\",\"$bpphone\",$cms,$bcms_id," .
		"\"" . $csname . "\",\"" . $csphone . "\",\"$memcode\",$hotel,\"$roomnum\"," .
		"$cancel,$trf,$rs,$rc," .
		"$atspa,$userid,now(),\"$ip\"," .
		"$userid,now(),\"$ip\",$tax_id,\"$servicescharge\",$inspection_id)";
		/*$sql = "insert into a_bookinginfo(a_member_code,b_branch_id,b_book_hour,b_customer_name,b_qty_people," .
				"b_accomodations_id,b_hotel_room,b_pickup_confirm,b_cancel_confirm," .
				"b_appt_date,b_appt_time_id,b_reservation_id,b_receive_id," .
				"b_set_atspa,c_set_cms,c_bp_id,c_bp_person,c_bp_phone," .
				"c_pcms_id,c_lu_user,c_lu_date,c_lu_ip," .
				"l_lu_user,l_lu_date,l_lu_ip,tax_id) ";
		$sql .= "values(\"$memcode\",$branch,$tthour,\"".$csname."\",$ttpp," .
				"$hotel,\"$roomnum\",$trf,$cancel," .
				"$apptdate,$apptime,$rs,$rc," .
				"$atspa,$cms,$bcompany,\"$bpname\",\"$bpphone\"," .
				"$bcms_id,$userid,now(),\"$ip\",$userid,now(),\"$ip\",$tax_id)";*/
		//echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		$csid = $this->setResult($sql);

		// @modified - update log_c_bp on 17 june 2009 by natt
		$logid = 0;
		$chksql = "insert into log_c_bp(book_id,b_customer_name," .
		"c_bp_id,c_bp_person,c_bp_phone,c_set_cms,c_pcms_id," .
		"l_lu_user,l_lu_date,l_lu_ip) " .
		"values($csid,\"" . $csname . "\"," .
		"$bcompany,\"$bpname\",\"$bpphone\",$cms,$bcms_id," .
		"$userid,now(),\"$ip\") ";
		if ($csid) {
			$logid = $this->setResult($chksql);
		}

		//update into link table for get real booking id 
		$bpdsid = 0;
		$sql = "insert into c_bpds_link(`tb_id`,`tb_name`) values($csid,\"a_bookinginfo\")";
		if ($csid) {
			$bpdsid = $this->setResult($sql);
		}

		// update refid
		if ($cs["refid"] && $bpdsid) {
			//find all refid of this refid
			$rrefid = $this->getIdToText($cs["refid"], "c_bpds_link", "ref_id", "bpds_id");
			$refid = "$rrefid,$bpdsid," . $cs["refid"];

			//put all refid to array and cutoff "" and repeat id
			$arrreftmp = explode(",", $refid); //saparate to array
			$tarrdiff = array (
				""
			); //cutoff ""
			$arrreftmp = array_diff($arrreftmp, $tarrdiff);
			sort($arrreftmp);
			$arrreftmp[count($arrreftmp)] = "$bpdsid";
			$arrreftmp = $this->detectRepeatarr($arrreftmp);

			//for all refid update with new refid circle
			for ($i = 0; $i < count($arrreftmp); $i++) {
				$arrdiff = array (
					$arrreftmp[$i]
				);
				$arrref = array_diff($arrreftmp, $arrdiff);
				sort($arrref);
				$refid = implode(",", $arrref);
				$sql = "update c_bpds_link set `ref_id`=\"$refid\" where `bpds_id`=" . $arrreftmp[$i];
				$id = $this->setResult($sql);
			}
		}
		if ($bpdsid) {
			return $csid;
		} else {
			$this->setErrorMsg("Can't insert to table c_bpds_link!!");
			return false;
		}

	}

	/*
	 * edit information in table a_bookinginfo
	 * @param $cs - all customer and booking information
	 * @param $cancel - set cancel confirm
	 * @param $cms - set cms confirm
	 * @param $trf - set transportration confirm
	 * @modified - add this function on 11 dec 2008
	 */
	function edit($cs = false, $bookid = false, $cancel = false, $cms = false, $trf = false, $debug = false) {
		$memcode = 0; //For debug undefine variable : memcode. By Ruck : 18-05-2009
		$branch = $cs["branch"]; // id
		$tthour = $cs["tthour"]; // id
		$csname = htmlspecialchars($cs["name"]); // text
		$csphone = htmlspecialchars($cs["phone"]); // text
		$inspection_id = $cs["inspection"]; // id

		if (!$csname) {
			$this->setErrorMsg("Please insert customer name!!");
			return false;
		}

		$cs["refid"] = ($cs["refid"] != "") ? $cs["refid"] : 0;
		$chksql = $this->getIdToText($cs["refid"], "c_bpds_link", "bpds_id", "bpds_id");

		if (!$chksql && $cs["refid"] != 0) {
			$this->setErrorMsg(" Please check book id: " . $cs["refid"] . " doesn't have in the system!!");
			return false;
		}

		if (!$csphone || substr($csphone, 0, 1) != "+" || substr($csphone, 1, 1) == "0" || strlen($csphone) == 1) {
			if ($csphone != "") {
				$this->setErrorMsg("Please check phone number format must be '+ countrycode citycode number' !!");
				return false;
			}
		}
		if ($cs["memid"]) {
			$memberSql = "select member_id,expired,expireddate from m_membership where member_code=" . $cs["memid"];
			$memberRs = $this->getResult($memberSql);

			// Check if has this member on table m_mebership.
			if ($memberRs) {
				//For check booking has this member code or not.
				$chkMemberCodeOnBook = $this->getIdToText($cs["memid"], "a_bookinginfo", "book_id", "a_member_code", "book_id=$bookid");
				$expiredDateMember = str_replace("-", "", $memberRs[0]["expireddate"]);
				$nowDate = date("Ymd");

				// If this booking has this member code. 
				// Or if this member code not set to inacitve and if expired date of member more than or equal today or expired date of member equal "00000000"
				// Then set this member code save into database. 
				if ($chkMemberCodeOnBook) {
					$memcode = $cs["memid"];
				} else if ($memberRs[0]["expired"] && ($expiredDateMember >= $nowDate || $expiredDateMember == "00000000")) {
						$memcode = $cs["memid"];
				} else if (!$memberRs[0]["expired"]) {
						$this->setErrorMsg("This member " . $cs["memid"] . " is disable. Please check again !!");
						return false;
				} else if ($cs["hidden_apptdate"] > $expiredDateMember) {
						$this->setErrorMsg("Appointment date more than member expired date. Please check again!!");
						return false;
				} else {
						$this->setErrorMsg("This member is expired. Please check again!!");
						return false;
				}
			} else {
				$this->setErrorMsg("Invalid member code. Please check again!!");
				return false;
			}

		}

		$ttpp = $cs["ttpp"]; // number
		$hotel = $cs["hotel"]; // id
		$roomnum = htmlspecialchars($cs["roomnum"]); // text
		// $trf is b_pickup_confirm
		// For debug undefined index : trf. By Ruck : 18-05-2009 // 
		// change if(isset($trf)) to if($trf=="checked") 
		if ($trf == "checked")
			$trf = 1;
		else
			$trf = 0;

		// $cancel is b_cancel_confirm
		// For debug undefined index : cc. By Ruck : 18-05-2009 // 		
		// change if(isset($cancel)) to if($cancel=="checked")
		if ($cancel == "checked")
			$cancel = 1;
		else
			$cancel = 0;

		// $cms is c_set_cms
		// For debug undefined index : cc. By Ruck : 18-05-2009 // 		
		// change if(isset($cms)) to if($cms=="checked")
		if ($cms == "checked")
			$cms = 1;
		else
			$cms = 0;

		if ($cs["atspa"])
			$atspa = 1;
		else
			$atspa = 0;

		//$apptdate = str_replace('-','',$this->separate_time($cs["apptdate"],5,0)); // year month day => 20060102
		$apptdate = $cs["hidden_apptdate"];
		//echo $cs["apptdate"].": ".$apptdate;		
		$apptime = $cs["appttime"]; // id
		$rs = $cs["rs"]; // id
		$rc = $cs["rc"]; // id

		$bcompany = $cs["bcompany"]; // id
		$bpname = htmlspecialchars($cs["bpname"]); // text
		$bpphone = htmlspecialchars($cs["bpphone"]); // text
		$bcms_id = $cs["cms_percent"]; // id

		if (!$bpphone || substr($bpphone, 0, 1) != "+" || substr($bpphone, 1, 1) == "0" || strlen($bpphone) == 1) {
			if ($bpphone != "") {
				$this->setErrorMsg("Please check phone number format must be '+ countrycode citycode number' !!");
				//return false;
			}
		}

		$ip = $_SERVER["REMOTE_ADDR"];
		$userid = $this->getUserIdLogin();

		$sql = "update a_bookinginfo set " .
		"b_branch_id=$branch,b_qty_people=$ttpp,b_appt_date=$apptdate,b_appt_time_id=$apptime,b_book_hour=$tthour," .
		"c_bp_id=$bcompany,c_bp_person=\"$bpname\",c_bp_phone=\"$bpphone\",c_set_cms=$cms,c_pcms_id=$bcms_id," .
		"b_customer_name=\"$csname\",b_customer_phone=\"$csphone\",a_member_code=\"$memcode\",b_accomodations_id=$hotel,b_hotel_room=\"$roomnum\"," .
		"b_set_cancel=$cancel,b_set_pickup=$trf,b_reservation_id=$rs,b_receive_id=$rc," .
		"b_set_atspa=$atspa,c_lu_user=$userid,c_lu_date=now(),c_lu_ip=\"$ip\",mkcode_id=$inspection_id " .
		"where book_id=$bookid";

		//echo $sql."<br>";
		$this->setErrorMsg($sql);
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		$csid = $this->setResult($sql);

		// @modified - update log_c_bp on 17 june 2009 by natt
		$logid = 0;
		$chksql = "insert into log_c_bp(book_id,b_customer_name," .
		"c_bp_id,c_bp_person,c_bp_phone,c_set_cms,c_pcms_id," .
		"l_lu_user,l_lu_date,l_lu_ip) " .
		"values($bookid,\"" . $csname . "\"," .
		"$bcompany,\"$bpname\",\"$bpphone\",$cms,$bcms_id," .
		"$userid,now(),\"$ip\") ";
		if ($csid) {
			$logid = $this->setResult($chksql);
		}

		// modified on 3 Apr 2008 for match all ref id in all ref. link
		if ($cs["refid"]) {

			//find all refid
			$bpdsid = $this->getIdToText($bookid, "c_bpds_link", "bpds_id", "tb_id", "`tb_name`=\"a_bookinginfo\"");
			$trefid = $this->getIdToText($bookid, "c_bpds_link", "ref_id", "tb_id", "`tb_name`=\"a_bookinginfo\"");
			$rrefid = $this->getIdToText($cs["refid"], "c_bpds_link", "ref_id", "bpds_id");

			$refid = "$trefid,$rrefid,$bpdsid," . $cs["refid"];

			$arrreftmp = explode(",", $refid); //saparate to array
			$tarrdiff = array (
				""
			); //cutoff ""
			$arrreftmp = array_diff($arrreftmp, $tarrdiff);
			sort($arrreftmp);
			$arrreftmp = $this->detectRepeatarr($arrreftmp);
			//print_r($arrreftmp);
			//echo "<br><br>".count($arrreftmp)."<br>";
			for ($i = 0; $i < count($arrreftmp); $i++) {
				$arrdiff = array (
					$arrreftmp[$i]
				);
				$arrref = array_diff($arrreftmp, $arrdiff);
				sort($arrref);
				$refid = implode(",", $arrref);
				$sql = "update c_bpds_link set `ref_id`=\"$refid\" where `bpds_id`=$arrreftmp[$i]";
				//echo $sql."<br>";
				$id = $this->setResult($sql);
			}

		}

		return $csid;
	}

	/*
	 * edit information in table ac_cancal
	 * @param $cc - all cancel booking information
	 * @param $bookid - booking id
	 * @modified - add this function on 15 dec 2008
	 */
	function editcc($cc = false, $bookid = false, $debug = false) {
		$canceldate = $cc["hidden_date"]; //date
		$cccomment = htmlspecialchars($cc["comment"]); //text
		if (!isset ($cc["cc"])) {
			$cc["cc"] = "";
		} //For debug undefine index : cc. By Ruck : 18-05-2009
		if ($cc["cc"] == "checked") {
			$chksql = "select * from ac_cancal where book_id=$bookid";
			$ccrs = $this->getResult($chksql);
			$ip = $_SERVER["REMOTE_ADDR"];
			$userid = $this->getUserIdLogin();
			if ($ccrs["rows"] > 0) {
				$sql = "update ac_cancal set cancel_datets=$canceldate, cancel_comment=\"$cccomment\", " .
				" c_lu_user=$userid, c_lu_date=now(), c_lu_ip=\"$ip\" where book_id=$bookid";
				$ccid = $this->setResult($sql);
			} else {
				$sql = "insert into ac_cancal(book_id,cancel_datets,cancel_comment,c_lu_user,c_lu_date,c_lu_ip) " .
				"values($bookid,$canceldate,\"$cccomment\",$userid,now(),\"$ip\")";
				$ccid = $this->setResult($sql);
			}
		} else {
			$sql = "delete from ac_cancal where book_id=$bookid";
			$ccid = $this->setResult($sql);
		}

		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		return $ccid;
	}

	function addBP($cms = false, $bookid = false, $subtotal = false, $debug = false) {
		$sql = "update a_bookinginfo set d_accounting=$subtotal where book_id=$bookid";
		$this->setResult($sql);
		if ($cms == "checked") {
			$ip = $_SERVER["REMOTE_ADDR"];
			$userid = $this->getUserIdLogin();
			$sql = "select al_percent_cms.pcms_percent from al_percent_cms,a_bookinginfo " .
			"where al_percent_cms.pcms_id=a_bookinginfo.c_pcms_id " .
			"and a_bookinginfo.book_id=$bookid";
			$rs = $this->getResult($sql);
			$percentcms = $rs[0]["pcms_percent"];
			$cms_amount = $subtotal * $percentcms / 100;
			$sql = "insert into aa_commission(book_id,cms_amount,c_lu_user,c_lu_date,c_lu_ip) values($bookid,$cms_amount,$userid,now(),\"$ip\")";
		} else {
			return true;
		}
		$id = $this->setResult($sql);
		return $id;
	}

	function editBP($cms = false, $bookid = false, $subtotal = false, $debug = false) {
		$sql = "update a_bookinginfo set d_accounting=$subtotal where book_id=$bookid";
		$this->setResult($sql);
		$ip = $_SERVER["REMOTE_ADDR"];
		$userid = $this->getUserIdLogin();
		if ($cms == "checked") {
			$sql = "select al_percent_cms.pcms_percent from al_percent_cms,a_bookinginfo " .
			"where al_percent_cms.pcms_id=a_bookinginfo.c_pcms_id " .
			"and a_bookinginfo.book_id=$bookid";
			$rs = $this->getResult($sql);
			$percentcms = $rs[0]["pcms_percent"];
			$cms_amount = $subtotal * $percentcms / 100;
			$sql = "select book_id from aa_commission where book_id=$bookid";
			$rs = $this->getResult($sql);
			if ($rs["rows"] > 0) {
				$sql = "update aa_commission set cms_amount=$cms_amount,c_lu_user=$userid,c_lu_date=now(),c_lu_ip=\"$ip\" where book_id=$bookid";
			} else {
				$sql = "insert into aa_commission(book_id,cms_amount,c_lu_user,c_lu_date,c_lu_ip) values($bookid,$cms_amount,$userid,now(),\"$ip\")";
			}
		} else {
			$sql = "delete from aa_commission where book_id=$bookid";
		}
		$id = $this->setResult($sql);
		return $id;
	}

	/*
	 * add information in table ad_comment
	 * @param $comment - all comment booking information
	 * @param $bookid - booking id
	 * @modified - add this function on 15 dec 2008
	 */
	function addcomment($comment = false, $bookid = false, $debug = false) {
		$ip = $_SERVER["REMOTE_ADDR"];
		$userid = $this->getUserIdLogin();
		$comments = htmlspecialchars($comment);

		$sql = "insert into ad_comment(book_id,comments,l_lu_user,l_lu_date,l_lu_ip,active) ";
		$sql .= "values($bookid,\"$comments\",$userid,now(),\"$ip\",1)";

		//echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		$csid = $this->setResult($sql);
		//echo "update comment:".$csid."<br/>";
		return $csid;
	}

	/*
	 * edit information in table ab_transfer
	 * @param $trf - booking all driver transfer information
	 * @param $bookid - booking id
	 * @modified - add this function on 15 dec 2008
	 */
	function edittrf($trf = false, $bookid = false, $debug = false) {
		$pickuptime = $trf["pu_time"];
		$takebacktime = $trf["tb_time"];
		$drpickup = $trf["dr_pu"];
		$drtakeback = $trf["dr_tb"];
		$pickupplace = htmlspecialchars($trf["p_pu"]);
		$takebackplace = htmlspecialchars($trf["p_tb"]);

		if ($trf["trf"] == "checked") {
			$chksql = "select * from ab_transfer where book_id=$bookid";
			$trfrs = $this->getResult($chksql);
			if ($trfrs["rows"] > 0) {
				$sql = "update ab_transfer set pu_time=$pickuptime," .
				" tb_time=$takebacktime," .
				" driver_pu_id=$drpickup," .
				" driver_tb_id=$drtakeback," .
				" pu_place=\"$pickupplace\"," .
				" tb_place=\"$takebackplace\"" .
				" where book_id=$bookid";
				$trfid = $this->setResult($sql);
			} else {
				$sql = "insert into ab_transfer(book_id,pu_time,tb_time,driver_pu_id,driver_tb_id,pu_place,tb_place) " .
				"values($bookid,$pickuptime,$takebacktime,$drpickup,$drtakeback,\"$pickupplace\",\"$takebackplace\")";
				$trfid = $this->setResult($sql);
			}
		} else {
			$sql = "delete from ab_transfer where book_id=$bookid";
			$trfid = $this->setResult($sql);
		}

		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		return $trfid;
	}

	/*
	 * add information to table d_indivi_info
	 */
	function addIndivi($tw = false, $bookid = false, $status = false, $debug = false) {
		if (!$status) {
			$this->setErrorMsg("Please check status value before add information in d_indivi_info!!");
			return false;
		}

		if (!$bookid) {
			$this->setErrorMsg("Please check booking id before insert to d_indivi_info!!");
			return false;
		}
		if ($status == "edit" && $tw["room"] == 1) {
			$this->setErrorMsg("Please check room value before add information in d_indivi_info!!");
			return false;
		}

		//For debug undefined index : member_use,stream. By Ruck : 18-05-2009
		if (!isset ($tw["member_use"])) {
			$tw["member_use"] = "";
		}
		if (!isset ($tw["stream"])) {
			$tw["stream"] = "";
		}

		$csname = htmlspecialchars($tw["csnameinroom"]); // text
		$csphone = htmlspecialchars($tw["csphoneinroom"]); // text
		$csemail = htmlspecialchars($tw["csemail"]); // text
		$csage = $tw["csageinroom"]; // int
		$csbirthday = $tw["hidden_csbday"]; // date
		$csnationalid = $tw["national"]; // id
		$cssexid = $tw["sex"]; // id
		$csresident = ($tw["resident"] == "resident") ? 1 : 0; // binary
		$csvisitor = ($tw["resident"] == "visitor") ? 1 : 0; // binary
		$memberuse = ($tw["member_use"] == "checked") ? 1 : 0; // binary
		$room = $tw["room"]; // id
		$hour = $tw["tthour"]; // id
		$package = $tw["package"]; // id
		$strength = $tw["strength"]; // id
		$scrub = $tw["scrub"]; // id
		$wrap = $tw["wrap"]; // id
		$bath = $tw["bath"]; // id
		$facial = $tw["facial"]; // id
		$comments = htmlspecialchars($tw["comments"]); // text
		$stream = ($tw["stream"] == "checked") ? 1 : 0; // binary
		$setinroom = ($tw["cusstate"] == "c_set_inroom") ? 1 : 0; // binary
		$setfin = ($tw["cusstate"] == "c_set_finish") ? 1 : 0; // binary
		$setatspa = ($tw["cusstate"] == "c_set_atspa") ? 1 : 0; // binary

		if ($status == "add") {
			$sql = "insert into d_indivi_info(book_id,cs_name,cs_phone,cs_email,cs_age,cs_birthday," .
			"sex_id,nationality_id,room_id,hour_id,package_id," .
			"strength_id,scrub_id,wrap_id,bath_id,facial_id,stream,comments,b_set_atspa,b_set_inroom,b_set_finish) ";
			$sql .= "values($bookid,\"$csname\",\"$csphone\",\"$csemail\",\"$csage\",\"$csbirthday\"," .
			"$cssexid,$csnationalid,$room,$hour,$package," .
			"$strength,$scrub,$wrap,$bath,$facial,$stream,\"$comments\",$setatspa,$setinroom,$setfin)";
		} else
			if ($status == "edit") {
				$sql = "insert into d_indivi_info(book_id,cs_name,cs_phone,cs_email,cs_age,cs_birthday,resident,visitor,member_use," .
				"sex_id,nationality_id,room_id,hour_id,package_id," .
				"strength_id,scrub_id,wrap_id,bath_id,facial_id,stream,comments,b_set_atspa,b_set_inroom,b_set_finish) ";
				$sql .= "values($bookid,\"$csname\",\"$csphone\",\"$csemail\",\"$csage\",\"$csbirthday\",$csresident,$csvisitor,$memberuse," .
				"$cssexid,$csnationalid,$room,$hour,$package," .
				"$strength,$scrub,$wrap,$bath,$facial,$stream,\"$comments\",$setatspa,$setinroom,$setfin)";
			}
		$indiviid = $this->setResult($sql);

		//echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		return $indiviid;
	}

	function editIndivi($tw = false, $indiviid = false, $bookid = false, $status = false, $debug = false) {
		if (!$status || $status != "edit") {
			$this->setErrorMsg("Please check status value before update information in d_indivi_info!!");
			return false;
		}

		if (!$indiviid) {
			$this->setErrorMsg("Please check individual id before update d_indivi_info!!");
			return false;
		}

		if (!$bookid) {
			$this->setErrorMsg("Please check booking id before update d_indivi_info!!");
			return false;
		}

		if ($tw["room"] == 1) {
			$this->setErrorMsg("Please check room value before add information in d_indivi_info!!");
			return false;
		}

		//For debug undefine index : member_use,stream. By Ruck : 18-05-2009
		if (!isset ($tw["member_use"])) {
			$tw["member_use"] = "";
		}
		if (!isset ($tw["stream"])) {
			$tw["stream"] = "";
		}

		$csname = htmlspecialchars($tw["csnameinroom"]); // text
		$csphone = htmlspecialchars($tw["csphoneinroom"]); // text
		$csemail = htmlspecialchars($tw["csemail"]); // text
		$csage = $tw["csageinroom"]; // int
		$csbirthday = $tw["hidden_csbday"]; // date
		$csnationalid = $tw["national"]; // id
		$cssexid = $tw["sex"]; // id
		$csresident = ($tw["resident"] == "resident") ? 1 : 0; // binary
		$csvisitor = ($tw["resident"] == "visitor") ? 1 : 0; // binary
		$memberuse = ($tw["member_use"] == "checked") ? 1 : 0; // binary
		$room = $tw["room"]; // id
		$hour = $tw["tthour"]; // id
		$package = $tw["package"]; // id
		$strength = $tw["strength"]; // id
		$scrub = $tw["scrub"]; // id
		$wrap = $tw["wrap"]; // id
		$bath = $tw["bath"]; // id
		$facial = $tw["facial"]; // id
		$comments = htmlspecialchars($tw["comments"]); // text
		$stream = ($tw["stream"] == "checked") ? 1 : 0; // binary
		$setinroom = ($tw["cusstate"] == "c_set_inroom") ? 1 : 0; // binary
		$setfin = ($tw["cusstate"] == "c_set_finish") ? 1 : 0; // binary
		$setatspa = ($tw["cusstate"] == "c_set_atspa") ? 1 : 0; // binary

		$sql = "update d_indivi_info set " .
		"book_id=$bookid," .
		"cs_name=\"$csname\"," .
		"cs_phone=\"$csphone\"," .
		"cs_email=\"$csemail\"," .
		"cs_birthday=\"$csbirthday\"," .
		"cs_age=\"$csage\"," .
		"resident=$csresident,visitor=$csvisitor," .
		"member_use=$memberuse," .
		"sex_id=$cssexid," .
		"nationality_id=$csnationalid," .
		"room_id=$room," .
		"hour_id=$hour," .
		"package_id=$package," .
		"strength_id=$strength,scrub_id=$scrub,wrap_id=$wrap,bath_id=$bath,facial_id=$facial,stream=$stream," .
		"comments=\"$comments\",b_set_atspa=$setatspa,b_set_inroom=$setinroom,b_set_finish=$setfin " .
		"where indivi_id=$indiviid";
		$indivi_id = $this->setResult($sql);

		//echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		return $indivi_id;
	}

	function delIndivi($bookid = false, $condition = false, $debug = false) {
		if (!$bookid) {
			$this->setErrorMsg("Please check booking id before delete to da_mult_th!!");
			return false;
		}
		$sql = "delete from d_indivi_info where book_id=$bookid $condition";
		//	echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		$id = $this->setResult($sql);

		return true;
	}

	/*
	 * @Modified - Change on 19 Feb 2009 (49 -> 1)
	 * 	$chksql = "delete from da_mult_th where indivi_id=$indiviid and therapist_id=49";	
	 */
	function addTh($tw = false, $indiviid = false, $bookid = false, $status = false, $debug = false) {
		if (!$status) {
			$this->setErrorMsg("Please check status value before insert to da_mult_th!!!");
			return false;
		}
		if (!$bookid) {
			$this->setErrorMsg("Please check booking id before insert to da_mult_th!!");
			return false;
		}
		if (!$indiviid) {
			$this->setErrorMsg("Please check individual id before insert to da_mult_th!!");
			return false;
		}

		/*$chkth = $this->getRowFromId($indiviid,"da_mult_th","indivi_id");
		if($status=="edit"&&$chkth==1&&$tw["name"]==49){
			$this->setErrorMsg("Please check therapist name before insert to da_mult_th!!!");
			return false;
		}*/

		$thid = $tw["name"]; // int
		$hour = $tw["hour"]; // int

		$sql = "insert into da_mult_th(book_id,indivi_id,therapist_id,hour_id)" .
		" values($bookid,$indiviid,$thid,$hour)";
		$multhid = $this->setResult($sql);

		$chksql = "select multh_id from da_mult_th where indivi_id=$indiviid order by therapist_id desc,hour_id desc";
		$chkrs = $this->getResult($chksql);
		if ($chkrs["rows"] > 1) {
			$chksql = "delete from da_mult_th where indivi_id=$indiviid and therapist_id=1 and multh_id!=" . $chkrs[0]["multh_id"];
			$this->setResult($chksql);
		}
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		return $multhid;

	}

	/*
	 * @Modified - Change on 19 Feb 2009 (57 -> 1)
	 * 	$chksql = "delete from da_mult_msg where indivi_id=$indiviid and massage_id=56";
	 */
	function addMsg($tw = false, $indiviid = false, $bookid = false, $status = false, $debug = false) {
		if (!$status) {
			$this->setErrorMsg("Please check status value before insert to da_mult_msg!!!");
			return false;
		}
		if (!$bookid) {
			$this->setErrorMsg("Please check booking id before insert to da_mult_msg!!");
			return false;
		}
		if (!$indiviid) {
			$this->setErrorMsg("Please check individual id before insert to da_mult_msg!!");
			return false;
		}

		$msgid = $tw["msg"]; // int

		$sql = "insert into da_mult_msg(book_id,indivi_id,massage_id)" .
		" values($bookid,$indiviid,$msgid)";
		$multmsgid = $this->setResult($sql);

		//	echo $sql."<br>";
		$chksql = "delete from da_mult_msg where indivi_id=$indiviid and massage_id=1";
		$this->setResult($chksql);
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		return $multmsgid;
	}

	/*
	 * @Modified - Change on 19 Feb 2009 (49 -> 1)
	 * 	$chksql = "delete from da_mult_th where indivi_id=$indiviid and therapist_id=49";	
	 */
	function editTh($tw = false, $multh_id = false, $indiviid = false, $bookid = false, $debug = false) {
		if (!$bookid) {
			$this->setErrorMsg("Please check booking id before insert to d_indivi_info!!");
			return false;
		}
		if (!$indiviid) {
			$this->setErrorMsg("Please check individual id before insert to d_indivi_info!!");
			return false;
		}

		$thid = $tw["name"];
		$hour = $tw["hour"];

		$sql = "update da_mult_th set book_id=$bookid,indivi_id=$indiviid,therapist_id=$thid,hour_id=$hour" .
		" where multh_id=$multh_id";
		$multhid = $this->setResult($sql);

		$chksql = "select multh_id from da_mult_th where indivi_id=$indiviid order by therapist_id desc,hour_id desc";
		$chkrs = $this->getResult($chksql);
		if ($chkrs["rows"] > 1) {
			$chksql = "delete from da_mult_th where indivi_id=$indiviid and therapist_id=1 and multh_id!=" . $chkrs[0]["multh_id"];
			$this->setResult($chksql);
		}
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		return $multhid;
	}

	/*
	 * @Modified - Change on 19 Feb 2009 (57 -> 1)
	 * 	$chksql = "delete from da_mult_msg where indivi_id=$indiviid and massage_id=56";
	 */
	function editMsg($tw = false, $multmsg_id = false, $indiviid = false, $bookid = false, $debug = false) {
		if (!$bookid) {
			$this->setErrorMsg("Please check booking id before insert to da_mult_msg!!");
			return false;
		}
		if (!$indiviid) {
			$this->setErrorMsg("Please check individual id before insert to da_mult_msg!!");
			return false;
		}

		$msgid = $tw["msg"];

		$sql = "update da_mult_msg set book_id=$bookid,indivi_id=$indiviid,massage_id=$msgid" .
		" where multmsg_id=$multmsg_id";
		$multmsgid = $this->setResult($sql);

		//	echo $sql."<br>";
		$chksql = "delete from da_mult_msg where indivi_id=$indiviid and massage_id=1";
		$this->setResult($chksql);

		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		return $multmsgid;
	}

	function delTh($indiviid = false, $condition = false, $debug = false) {
		if ($indiviid == false) {
			$sql = "delete from da_mult_th where $condition";
		} else {
			$sql = "delete from da_mult_th where indivi_id=$indiviid $condition";
		}
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		$id = $this->setResult($sql);

		return true;
	}

	function delMsg($indiviid = false, $condition = false, $debug = false) {
		if ($indiviid == false) {
			$sql = "delete from da_mult_msg where $condition";
		} else {
			$sql = "delete from da_mult_msg where indivi_id=$indiviid $condition";
		}
		//echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		$id = $this->setResult($sql);

		return true;
	}

	/*
	 * edit information in table g_gift
	 * @param $gift - booking all gift information
	 * @param $bookid - booking id
	 * @modified - add this function on 16 dec 2008
	 */
	function editgift($gift = false, $bookid = false, $debug = false) {
		if (count($gift) != 1) {
			for ($i = 1; $i < count($gift); $i++) {
				$sql = "update g_gift set book_id=$bookid,used=" . date("Ymd") . " where gift_number=" . $gift[$i];
				$gid = $this->setResult($sql);
				//echo $sql."<br>";
				if ($debug) {
					echo $sql . "<br>";
				}
				if (!$gid) {
					$this->setErrorMsg("Cann't update gift number $gift[$i]!!");
					return false;
				}
			}
		}
		return true;
	}

	/*
	 * Delete all table that have relation with a_bookinginfo 
	 * @param $bookid - booking id
	 * @modified - add this function on 18 dec 2008
	 * @modified - add condition check $bookid before delete rows on 18 dec 2008
	 */
	function delAll($bookid = false, $debug = false) {
		if ($bookid) {
			$sql = "delete from a_bookinginfo where book_id=$bookid";
			$this->setResult($sql, $debug);

			$sql = "delete from ac_cancel where book_id=$bookid";
			$this->setResult($sql, $debug);

			$sql = "delete from ab_transfer where book_id=$bookid";
			$this->setResult($sql, $debug);

			$sql = "delete from ad_comment where book_id=$bookid";
			$this->setResult($sql, $debug);

			$sql = "delete from aa_commission where book_id=$bookid";
			$this->setResult($sql, $debug);

			$sql = "delete from f_csi where book_id=$bookid";
			$this->setResult($sql, $debug);

			$sql = "delete from d_indivi_info where book_id=$bookid";
			$this->setResult($sql, $debug);

			$sql = "delete from da_mult_th where book_id=$bookid";
			$this->setResult($sql, $debug);

			$sql = "delete from da_mult_msg where book_id=$bookid";
			$this->setResult($sql, $debug);

			$sql = "update g_gift set book_id=0 where book_id=$bookid";
			$this->setResult($sql, $debug);

			//$sql="delete from c_salesreciept where book_id=$bookid";
			$sql = "delete from c_salesreceipt where book_id=$bookid";
			$this->setResult($sql, $debug);

			$sql = "delete from c_srdetail where book_id=$bookid";
			$this->setResult($sql, $debug);

			$sql = "delete from a_appointment where book_id=$bookid";
			$this->setResult($sql, $debug);
		}
	}

	/*
	 * @modified - solve warning message can't case string to array add function array_filter on 19 Feb 2009
	 */
	function checkPeopleInroom($tw = false) {
		$j = 0;
		for ($i = 0; $i < count($tw); $i++) {
			// debugging all undified offset Ruck/16-05-2009
			if (isset ($tw[$i]["room"])) {
				$room["room"][$j] = $tw[$i]["room"];
				$j++;
			}
		}
		$room = array_count_values(array_filter($room["room"]));
		//print_r($room);
		while ($tmp = current($room)) {
			$num = $this->getIdToText(key($room), "bl_room", "room_qty_people", "room_id");
			$roomname = $this->getIdToText(key($room), "bl_room", "room_name", "room_id");
			if ($tmp > $num) {
				$this->setErrorMsg("Number of People in room \"$roomname\" is to many!!");
				return false;
			}
			next($room);
		}
		return true;
	}

	/*
	 * function for check therapist time before add/edit booking information
	 * now it doesn't use
	 * @modified - add this function 22-Dec-2008
	 * @modified - edit add condition for check therapist time this function 23-Dec-2008 
	 */
	function checkTherapistTime($apptdate = false, $therapistid = false, $starttime = false, $hour = false, $bookid = false, $debug = false) {
		if (!$starttime) {
			$this->setErrorMsg("Please insert appointment time for check therapist time!!");
			return false;
		}
		if (!$apptdate) {
			$this->setErrorMsg("Please insert appointment date for check therapist time!!");
			return false;
		}
		if (!$therapistid) {
			$this->setErrorMsg("Please insert therapist id for check therapist time!!");
			return false;
		}

		$apptdate = str_replace('-', '', $this->separate_time($apptdate, 5, 0)); // 2-Jan-2006 => 20060102	
		$endtime = $starttime + ($hour -1) * 6;
		$sql = "select da_mult_th.hour_id as hour_id,a_bookinginfo.b_appt_time_id from a_bookinginfo,da_mult_th " .
		"where a_bookinginfo.book_id = da_mult_th.book_id " .
		"and a_bookinginfo.b_appt_date=" . $apptdate . " " .
		"and da_mult_th.therapist_id=$therapistid ";
		if ($bookid) {
			$sql .= "and a_bookinginfo.book_id!=$bookid ";
		}

		if ($debug) {
			echo $sql . "<br>";
			//return false;
		}

		$rs = $this->getResult($sql);
		//echo $hour." ".$starttime."-".$endtime."<br>";
		//echo $this->getIdToText($hour,"l_hour","hour_name","hour_id").": ".$this->getIdToText($starttime,"p_timer","time_start","time_id")."-".$this->getIdToText($endtime,"p_timer","time_start","time_id")."<br>";
		//echo $sql."<br>".$rs["rows"];
		if ($rs["rows"] > 0) {
			for ($i = 0; $i < $rs["rows"]; $i++) {
				$plustime = ($rs[$i]["hour_id"] - 1) * 6;
				//echo $rs[$i]["hour_id"]." ".$rs[$i]["b_appt_time_id"]."-".($rs[$i]["b_appt_time_id"]+$plustime)."<br>";
				//echo $this->getIdToText($rs[$i]["hour_id"],"l_hour","hour_name","hour_id").": ".$this->getIdToText($rs[$i]["b_appt_time_id"],"p_timer","time_start","time_id")."-".$this->getIdToText($rs[$i]["b_appt_time_id"]+$plustime,"p_timer","time_start","time_id");
				if ($starttime < $rs[$i]["b_appt_time_id"]) {
					if ($endtime <= $rs[$i]["b_appt_time_id"]) {
						return false;
					}
					return $therapistid;
				} else
					if ($starttime >= $rs[$i]["b_appt_time_id"]) {
						if ($starttime >= $rs[$i]["b_appt_time_id"] + $plustime) {
							return false;
						}
						return $therapistid;
					}
			}
		} else {
			return false;
		}

	}

	/*
	* function for check room is empty or not before add/edit booking information
	* @modified - add this function 22-Dec-2008
	* @modified - edit add condition for check room time this function 23-Dec-2008 
	*/
	function checkEmptyRoom($apptdate = false, $starttime = false, $hour = false, $roomid = false, $bookid = false, $rmid = false, $debug = false) {
		if (!$apptdate) {
			$this->setErrormsg("Please insert appointment date for check it!!");
			return false;
		}
		if (!$roomid) {
			$this->setErrormsg("Please insert room id for check it!!");
			return false;
		}

				
		// convert time start to minute
		$starttime_min = 60 * (8 + floor(($starttime -1) / 12)) + 5 * (($starttime -1) % 12);
		// find time finish in each room
		$hour_name = $this->getIdToText($hour, "l_hour", "hour_name", "hour_id");
		list ($hr, $min, $sec) = explode(":", $hour_name);
		$hour_min = (60 * $hr) + $min;
		$endtime_min = $starttime_min + $hour_min;	// time finish in minute
				
		// convert time finish to id
		$endtime = 12 * (floor( $endtime_min / 60 ) - 8) + 1 + 
		( $endtime_min - 60 * floor( $endtime_min / 60 )) / 5; 
				
				
		//$plustime = ($hour -1) * 6;
		//$endtime = $starttime + $plustime;

		$sql1 = "select a_bookinginfo.book_id, d_indivi_info.room_id, a_bookinginfo.b_appt_time_id as start_time," .
		"a_bookinginfo.b_book_hour,l_hour.hour_name,l_hour.hour_calculate, " . 
		"d_indivi_info.hour_id from a_bookinginfo,d_indivi_info,l_hour where " .
		"a_bookinginfo.book_id=d_indivi_info.book_id " .
		"and l_hour.hour_id = d_indivi_info.hour_id " .
		"and a_bookinginfo.b_appt_date=$apptdate " .
		"and d_indivi_info.room_id=$roomid " .
		"and a_bookinginfo.b_set_cancel=0 ";

		if ($bookid) {
			$sql1 .= "and a_bookinginfo.book_id!=$bookid ";
		}

		$sql1 .= "group by d_indivi_info.indivi_id";

		$sql2 = "select r_maintenance.rm_id as book_id, r_maintenance.room_id, r_maintenance.appt_time as start_time," .
		"r_maintenance.hour_id as b_book_hour,l_hour.hour_name,l_hour.hour_calculate, " .
		"r_maintenance.hour_id as max_hour from r_maintenance,l_hour where " .
		" r_maintenance.appt_date=$apptdate " .
		"and l_hour.hour_id = r_maintenance.hour_id " .
		"and r_maintenance.room_id=$roomid " .
		"and r_maintenance.set_cancel=0 ";

		if ($rmid) {
			$sql2 .= "and r_maintenance.rm_id!=$rmid ";
		}

		$sql = "($sql1) union ($sql2)";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		$rs = $this->getResult($sql);
		$room = false;
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
					$room = $roomid;
				}else
				if ($endtime > $rs[$i]["start_time"] && $endtime <= $rs[$i]["end_time"]) {
					$room = $roomid;
				}else
				if ($starttime <= $rs[$i]["start_time"] && $endtime >= $rs[$i]["end_time"]) {
					$room = $roomid;
				}else
				if ($starttime >= $rs[$i]["start_time"] && $endtime <= $rs[$i]["end_time"]) {
					$room = $roomid;
				}
			}
		}	
		return $room;

	}

	/*
	* function for check busy room or not before add/edit booking information
	* @modified - add this function 27-Oct-2009
	*/
	function checkRoom($apptdate = false, $starttime = false, $hour = false, $branchid = false, $bookid = false, $rmid = false, $debug = false) {
		if (!$apptdate) {
			$this->setErrormsg("Please insert appointment date for check it!!");
			return false;
		}
		if (!$branchid) {
			$this->setErrormsg("Please insert branch id for check it!!");
			return false;
		}

				
		// convert time start to minute
		$starttime_min = 60 * (8 + floor(($starttime -1) / 12)) + 5 * (($starttime -1) % 12);
		// find time finish in each room
		$hour_name = $this->getIdToText($hour, "l_hour", "hour_name", "hour_id");
		list ($hr, $min, $sec) = explode(":", $hour_name);
		$hour_min = (60 * $hr) + $min;
		$endtime_min = $starttime_min + $hour_min;	// time finish in minute
				
		// convert time finish to id
		$endtime = 12 * (floor( $endtime_min / 60 ) - 8) + 1 + 
		( $endtime_min - 60 * floor( $endtime_min / 60 )) / 5; 
				
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

		if ($bookid) {
			$sql1 .= "and a_bookinginfo.book_id!=$bookid ";
		}

		$sql1 .= "group by da_mult_th.indivi_id";

		$sql2 = "select r_maintenance.rm_id as book_id, r_maintenance.room_id, r_maintenance.appt_time as start_time," .
		"r_maintenance.hour_id as b_book_hour,l_hour.hour_name,l_hour.hour_calculate, " .
		"r_maintenance.hour_id as max_hour from r_maintenance,l_hour where " .
		" r_maintenance.appt_date=$apptdate " .
		"and l_hour.hour_id = r_maintenance.hour_id " .
		"and r_maintenance.branch_id=$branchid " .
		"and r_maintenance.set_cancel=0 ";

		if ($rmid) {
			$sql2 .= "and r_maintenance.rm_id!=$rmid ";
		}

		$sql = "($sql1) union ($sql2)";
		
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		$rs = $this->getResult($sql);
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

	function checkTh($indiviid) {
		$sql = "select * from da_mult_th where indivi_id=$indiviid";
		$rs = $this->getResult($sql);

		if ($rs) {
			return $rs;
		} else {
			return false;
		}
	}

	function checkMsg($indiviid) {
		$sql = "select * from da_mult_msg where indivi_id=$indiviid";
		$rs = $this->getResult($sql);

		if ($rs) {
			return $rs["rows"];
		} else {
			return 0;
		}
	}

	/*
	 * function to get user login id from "__user_id" session
	 * @modified - add this function on 11 dec 2008
	 */
	function getUserIdLogin() {
		return $_SESSION["__user_id"];
	}

	//######################################## Function for Sale Product Page ####################################
	/*
	 * get product sale id in manage appointment page
	 * @param $date - all detail information
	 * @param $branch_id - set cancel confirm
	 * @param $debug - servicescharge on createtime
	 * @modified - add this function on 26 Jan 2009 by natt
	 * @moidified - check user can click link or not 21-Feb-2009
	 */
	function getProductSale($date = false, $branch_id = false, $isEdit = false, $debug = false) {
		$sql = "select bpds_id,tb_id,set_cancel from c_bpds_link,c_saleproduct where c_saleproduct.pds_id=c_bpds_link.tb_id" .
		" and c_saleproduct.pds_date=\"" . $date . "\" and branch_id=$branch_id and c_bpds_link.tb_name=\"c_saleproduct\" order by bpds_id ";
		$rs = $this->getResult($sql);
		$ccbook = "";
		for ($i = 0; $i < $rs["rows"]; $i++) {
			if ($ccbook != '') {
				$ccbook .= ", ";
			}
			if ($isEdit) {
				if ($rs[$i]["set_cancel"] == 0) {
					$ccbook .= "<a href='javascript:;;' onClick=\"newwindow('manage_pdforsale.php?pdsid=" . $rs[$i]["tb_id"] . "','managePds" . $rs[$i]["tb_id"] . "')\" class=\"menu\">ID : " . $rs[$i]['bpds_id'] . "</a>";
				} else {
					$ccbook .= "<a href='javascript:;;' onClick=\"newwindow('manage_pdforsale.php?pdsid=" . $rs[$i]["tb_id"] . "','managePds" . $rs[$i]["tb_id"] . "')\" class=\"menu\"><del>ID : " . $rs[$i]['bpds_id'] . "</del></a>";
				}
			} else {
				if ($rs[$i]["set_cancel"] == 0) {
					$ccbook .= "<b class=\"menu\">ID : " . $rs[$i]['bpds_id'] . "</b>";
				} else {
					$ccbook .= "<b class=\"menu\"><del>ID : " . $rs[$i]['bpds_id'] . "</del></b>";
				}
			}

		}
		//echo $sql."<br/>";
		//echo $ccbook;
		return $ccbook;
	}

	/*
	 * add information into table c_saleproduct
	 * @param $cs - all detail information
	 * @param $cc - set cancel confirm
	 * @param $servicescharge - servicescharge on createtime
	 * @param $taxpercent - taxpercent on createtime
	 * @modified - add this function on 26 Jan 2009 by natt
	 */
	function addPds($cs = false, $cc = false, $servicescharge = false, $taxpercent = false, $debug = false) {
		$memcode = 0;
		if (!$cs["branch"]) {
			$this->setErrorMsg("Please check branch id before insert to c_bpds_link!!");
			return false;
		}
		/*if($cs["hidden_saledate"]<date("Ymd")){
			$obj->setErrorMsg("Please change product sale date to future or today!!");
			return false;
		}*/
		$branchid = $cs["branch"]; // id
		$bookid = ($cs["bookid"] != "") ? $cs["bookid"] : 0; // id

		$chksql = $this->getIdToText($bookid, "c_bpds_link", "bpds_id", "bpds_id");
		if (!$chksql && $bookid != 0) {
			$this->setErrorMsg("Please check reference id value!!");
			return false;
		}

		if ($cs["memid"]) {
			$memberSql = "select member_id,expired,expireddate from m_membership where member_code=" . $cs["memid"];
			$memberRs = $this->getResult($memberSql);

			// Check if this member has on table m_membership
			if ($memberRs) {
				//For check booking has this member code or not.
				$expiredDateMember = str_replace("-", "", $memberRs[0]["expireddate"]);
				$nowDate = date("Ymd");

				// If this member code not set to inacitve
				// and expired date of member more than or equal today or expired date of member equal "00000000"
				// Then set this member code save into database. 
				if ($memberRs[0]["expired"] && ($expiredDateMember >= $nowDate || $expiredDateMember == "00000000")) {

					$memcode = $cs["memid"];
				} else if (!$memberRs[0]["expired"]) {
						$this->setErrorMsg("This member has been set to disable. Please check again $chkMemberCodeOnBook!!");
						return false;
				} else if ($cs["hidden_saledate"] > $expiredDateMember) {
						$this->setErrorMsg("Appointment date more than member expired date. Please check again!!");
						return false;
				} else {
						$this->setErrorMsg("This member has been expired. Please check again!!");
						return false;
					}
			} else {
				$this->setErrorMsg("Invalid member code. Please check again!!");
				return false;
			}

		}

		$date = $cs["hidden_saledate"]; // date
		$cancel = ($cc["cc"] != false) ? 1 : 0; // binary
		$cancelcomment = ($cc["cc"] != false) ? htmlspecialchars($cc["comment"]) : ""; //text
		$canceldate = ($cc["cc"] != false) ? $cc["hidden_date"] : 0; // date
		$taxid = $taxpercent; // id
		$ip = $_SERVER["REMOTE_ADDR"]; // text
		$userid = $this->getUserIdLogin(); // id

		$sql = "insert into c_saleproduct(pds_date,branch_id,set_cancel,cancel_date,cancel_comment," .
		"l_lu_user,l_lu_date,l_lu_ip,tax_id,servicescharge,a_member_code) ";
		$sql .= "values(\"$date\",$branchid,$cancel,$canceldate,\"$cancelcomment\"," .
		"$userid,now(),\"$ip\",$taxid,$servicescharge,$memcode)";
		$pdsid = $this->setResult($sql);

		//echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		$bpdsid = 0;
		$sql = "insert into c_bpds_link(`tb_id`,`tb_name`) values($pdsid,\"c_saleproduct\")";
		if ($pdsid) {
			$bpdsid = $this->setResult($sql);
		}
		//echo $bpdsid;
		// id
		/* //last update for ring ref id in 02-04-2009	
		if($bookid){
			//for this's sale product id
			$sql = "update c_bpds_link set `ref_id`=\"$bookid\" where `bpds_id`=$bpdsid";
			$id = $this->setResult($sql);
			//for reference book id
			$refid=$this->getIdToText($bookid,"c_bpds_link","ref_id","bpds_id");
			$arrreftmp=explode(",",$refid);
			$arrdiff=array('');
			$arrref=array_diff($arrreftmp,$arrdiff);
			if(in_array($bpdsid,$arrref)||$bookid==$bpdsid){
				sort($arrref);
				$refid=implode(",",$arrref);
			}else{
				$arrref[count($arrref)]=$bpdsid;
				sort($arrref);
				$refid=implode(",",$arrref);
			}
			$sql = "update c_bpds_link set `ref_id`=\"$refid\" where `bpds_id`=$bookid";
			$id = $this->setResult($sql);
		}*/
		if ($bookid && $bpdsid) {
			$rrefid = $this->getIdToText($bookid, "c_bpds_link", "ref_id", "bpds_id");

			$refid = "$rrefid,$bpdsid,$bookid";

			$arrreftmp = explode(",", $refid); //saparate to array
			$tarrdiff = array (
				""
			); //cutoff ""
			$arrreftmp = array_diff($arrreftmp, $tarrdiff);
			sort($arrreftmp);
			$arrreftmp[count($arrreftmp)] = "$bpdsid";
			$arrreftmp = $this->detectRepeatarr($arrreftmp);

			//echo count($arrreftmp)."<br>";
			for ($i = 0; $i < count($arrreftmp); $i++) {
				$arrdiff = array (
					$arrreftmp[$i]
				);
				$arrref = array_diff($arrreftmp, $arrdiff);
				sort($arrref);
				$refid = implode(",", $arrref);
				$sql = "update c_bpds_link set `ref_id`=\"$refid\" where `bpds_id`=" . $arrreftmp[$i];
				//echo $sql."<br>";
				$id = $this->setResult($sql);
			}

		}
		if ($bpdsid) {
			return $pdsid;
		} else {
			return false;
		}
	}

	/*
	 * update information into table c_saleproduct
	 * @param $pds_id - pds_id that'll be update
	 * @param $cs - all detail information
	 * @param $cc - set cancel confirm
	 * @modified - add this function on 26 Jan 2009 by natt
	 */
	function editPds($pds_id = false, $cs = false, $cc = false, $debug = false) {
		$memcode = 0;
		if (!$cs["branch"]) {
			$this->setErrorMsg("Please check branch id before insert to c_bpds_link!!");
			return false;
		}
		$branchid = $cs["branch"]; // id

		$bookid = ($cs["bookid"] != "") ? $cs["bookid"] : 0; // id
		$chksql = $this->getIdToText($bookid, "c_bpds_link", "bpds_id", "bpds_id");
		if (!$chksql && $bookid != 0) {
			$this->setErrorMsg("Please check reference id value!!");
			return false;
		}

		if ($cs["memid"]) {
			$memberSql = "select member_id,expired,expireddate from m_membership where member_code=" . $cs["memid"];
			$memberRs = $this->getResult($memberSql);

			// Check if this member has on table m_membership.
			if ($memberRs) {
				//For check booking has this member code or not.
				$chkMemberCodeOnBook = $this->getIdToText($cs["memid"], "c_saleproduct", "pds_id", "a_member_code", "pds_id=$pds_id");
				$expiredDateMember = str_replace("-", "", $memberRs[0]["expireddate"]);
				$nowDate = date("Ymd");

				// If this booking has this member code.
				// Or if this member code not set to inacitve and if expired date of member more than or equal today or expired date of member equal "00000000"
				// Then set this member code save into database. 
				if ($chkMemberCodeOnBook) {
					$memcode = $cs["memid"];
				} else if ($memberRs[0]["expired"] && ($expiredDateMember >= $nowDate || $expiredDateMember == "00000000")) {
						$memcode = $cs["memid"];
				} else if (!$memberRs[0]["expired"]) {
							$this->setErrorMsg("This member " . $cs["memid"] . " is disable. Please check again !!");
							return false;
				
				} else if ($cs["hidden_saledate"] > $expiredDateMember) {
						$this->setErrorMsg("Appointment date more than member expired date. Please check again!!");
						return false;
				} else {
						$this->setErrorMsg("This member is expired. Please check again!!");
						return false;
				}
			} else {
				$this->setErrorMsg("Invalid member code. Please check again!!");
				return false;

			}

		}

		$date = $cs["hidden_saledate"]; // date
		$cancel = ($cc["cc"] == "checked") ? 1 : 0; // binary
		$cancelcomment = ($cc["cc"] == "checked") ? htmlspecialchars($cc["comment"]) : ""; //text
		$canceldate = ($cc["cc"] == "checked") ? $cc["hidden_date"] : 0; // date
		$ip = $_SERVER["REMOTE_ADDR"]; // text
		$userid = $this->getUserIdLogin(); // id
		$sql = "update c_saleproduct set branch_id=$branchid," .
		"set_cancel=$cancel," .
		"a_member_code=$memcode," .
		"pds_date=$date," .
		"cancel_date=$canceldate," .
		"cancel_comment=\"$cancelcomment\"," .
		"l_lu_user=$userid," .
		"l_lu_date=now()," .
		"l_lu_ip=\"$ip\" ";
		$sql .= "where pds_id=$pds_id";
		$pdsid = $this->setResult($sql);

		//echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		// modified on 3 Apr 2008 for match all ref id in all ref. link
		if ($cs["bookid"]) {

			//find all refid
			$bpdsid = $this->getIdToText($pds_id, "c_bpds_link", "bpds_id", "tb_id", "`tb_name`=\"c_saleproduct\"");
			$trefid = $this->getIdToText($pds_id, "c_bpds_link", "ref_id", "tb_id", "`tb_name`=\"c_saleproduct\"");
			$rrefid = $this->getIdToText($cs["bookid"], "c_bpds_link", "ref_id", "bpds_id");

			$refid = "$trefid,$rrefid,$bpdsid," . $cs["bookid"];

			$arrreftmp = explode(",", $refid); //saparate to array
			$tarrdiff = array (
				""
			); //cutoff ""
			$arrreftmp = array_diff($arrreftmp, $tarrdiff);
			sort($arrreftmp);
			$arrreftmp = $this->detectRepeatarr($arrreftmp);
			//print_r($arrreftmp);
			//echo "<br><br>".count($arrreftmp)."<br>";
			for ($i = 0; $i < count($arrreftmp); $i++) {
				$arrdiff = array (
					$arrreftmp[$i]
				);
				$arrref = array_diff($arrreftmp, $arrdiff);
				sort($arrref);
				$refid = implode(",", $arrref);
				$sql = "update c_bpds_link set `ref_id`=\"$refid\" where `bpds_id`=$arrreftmp[$i]";
				//echo $sql."<br>";
				$id = $this->setResult($sql);
			}

		}

		if ($pdsid) {
			return $pdsid;
		} else {
			return false;
		}
	}

	/*
	 * add information in table ca_comment
	 * @param $comment - all comment booking information
	 * @param $bookid - booking id
	 * @modified - add this function on 26 Jan 2009 by natt
	 */
	function addPsdcomment($comment = false, $pdsid = false, $debug = false) {
		$ip = $_SERVER["REMOTE_ADDR"];
		$userid = $this->getUserIdLogin();
		$comments = htmlspecialchars($comment);

		$sql = "insert into ca_comment(pds_id,comments,l_lu_user,l_lu_date,l_lu_ip,active) ";
		$sql .= "values($pdsid,\"$comments\",$userid,now(),\"$ip\",1)";

		//echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		$csid = $this->setResult($sql);
		//echo "update comment:".$csid."<br/>";
		return $csid;
	}
	//##################################### End Function for Sale Product Page ###################################
	//########################################## Function for Sale Receipt #######################################
	/*
	 * function to edit data into table c_salereceipt
	 * @modified - add this function on 19 dec 2008
	 * @modified - recoding on this function 14 july 2009 by Ruck
	 */
function editSaleReceipt($srd, $bookid = 0, $pdsid = 0, $userId = false,$mpdcount) {

		$newSrd = $this->getCurrentTab($srd);
		$chkStat = true;

		// This loop focus on insert sale receipt id (sr_id) for each sale receipt
		for ($i = 0; $i < count($newSrd); $i++) {
			if(!isset($tmp["srd_id"])){$tmp["srd_id"]="";}
			if(!isset($tmp["mpd_id"])){$tmp["mpd_id"]="";}
			if ($newSrd[$i][0]["sr_id"] == "") {
				//value for insert into table c_salesreceipt
				$tmp["comment"] = $newSrd[$i][0]["comment"];
				$tmp["sr_total"] = $newSrd[$i][0]["sr_total"];
				$tmp["paytype"] = $newSrd[$i][0]["paytype"];
				$tmp["pay_price"] = $newSrd[$i][0]["pay_price"];
				
				//value for insert into table c_srdetail
				$tmp["pd_id"] = $newSrd[$i][0]["pd_id"];
				$tmp["quantity"] = $newSrd[$i][0]["quantity"];
				$tmp["unit_price"] = $newSrd[$i][0]["unit_price"];
				$tmp["plus_sc"] = $newSrd[$i][0]["plus_sc"];
				$tmp["plus_tax"] = $newSrd[$i][0]["plus_tax"];

				$tmp = $this->insertSaleReceipt($tmp, $bookid, "", $pdsid, "");
			
				if (!$tmp) {
					$this->setErrorMsg("Can't insert data into sale receipt slip.");
					return "noValue";
				} else {
					//when insert sale recipt complete.
					//get sale receipt id (sr_id) and sale receipt detail id (srd_id) back.
					$newSrd[$i][0]["sr_id"] = $tmp["sr_id"];
					$newSrd[$i][0]["srd_id"] = $tmp["srd_id"];
					$newSrd[$i][0]["mpd_id"] = $tmp["mpd_id"];
				}
			}
		}

		//This loop focus on update data in table c_calesreceipt 
		//And insert/update product data into table c_srdetail
		for ($i = 0; $i < count($newSrd); $i++) {
		
			//change check variable from $newSrd[$i][0]["now_check_paid"] to $paid_confirm(value from database table c_salesreceipt)
			$paid_confirm = $this->getIdToText($newSrd[$i][0]["sr_id"], "c_salesreceipt", "paid_confirm", "salesreceipt_id");
			if ($paid_confirm == 0 || $newSrd[$i][0]["paid"] != 1) {

				//for update data in table c_salesreceipt.
				$sqlSr = "update c_salesreceipt set " .
				"paid_confirm='" . $newSrd[$i][0]["paid"] . "', " .
				"pay_id='" . $newSrd[$i][0]["maxpaid"] . "', ";

				if ($newSrd[$i][0]["paid"] == 1 && $paid_confirm == 0) {
					$sqlSr .= "sr_lu_user='$userId',sr_datets=now(),";
				}

				$sqlSr .= "sr_total='" . $newSrd[$i][0]["sr_total"] . "', "; // modified-30-Apr-2009/natt for update sr_total 
				$sqlSr .= "sr_comment='" . htmlspecialchars($newSrd[$i][0]["comment"]) . "' " .
				"where salesreceipt_id='" . $newSrd[$i][0]["sr_id"] . "' ";
				$srId = $this->setResult($sqlSr);

				// update log for salereceipt
				// update log only in update process because when insert some salereceipt 
				// we must have condition for check paid confirm for update salereceipt detail  
				// and update paid confirm again after check condition
				$lsr_id = $newSrd[$i][0]["sr_id"];
				$lsr_number = $this->getIdToText($lsr_id, "c_salesreceipt", "salesreceipt_number", "salesreceipt_id");
				$lbook_id = $bookid;
				$lpaid_confirm = $newSrd[$i][0]["paid"];
				//$lpay_id = $newSrd[$i][0]["paytype"];
				$lpay_id = $newSrd[$i][0]["maxpaid"];
				$lsr_lu_user = $userId;
				$lsr_datets = "now()";
				$lsr_total = $newSrd[$i][0]["sr_total"];
				$lsr_comment = htmlspecialchars($newSrd[$i][0]["comment"]);
				$lpds_id = $pdsid;
				$ll_lu_ip = $_SERVER["REMOTE_ADDR"];
				$chksql = "insert into log_c_sr(" .
				"salesreceipt_id,salesreceipt_number,book_id," .
				"paid_confirm,pay_id,sr_lu_user,sr_datets," .
				"sr_comment,pds_id,sr_total,l_lu_ip) " .
				"value ('$lsr_id','$lsr_number','$lbook_id'," .
				"'$lpaid_confirm','$lpay_id','$lsr_lu_user',$lsr_datets," .
				"'$lsr_comment','$lpds_id','$lsr_total','$ll_lu_ip')";
				$logid = $this->setResult($chksql);

				// if update sale receipt success
				if ($srId) {
					// for each salereceipt detail
					for ($j = 0; $j < count($newSrd[$i]); $j++) {
						
							if(!isset($newSrd[$i][$j]["srd_id"])){$newSrd[$i][$j]["srd_id"]="";}
							if(!isset($tmp["srd_id"])){$tmp["srd_id"]="";}
							if(!isset($newSrd[$i][$j]["pd_id"])){$newSrd[$i][$j]["pd_id"]="";}
							if(!isset($newSrd[$i][$j]["quantity"])){$newSrd[$i][$j]["quantity"]=0;}
							if(!isset($newSrd[$i][$j]["unit_price"])){$newSrd[$i][$j]["unit_price"]="";}
							if(!isset($newSrd[$i][$j]["plus_sc"])){$newSrd[$i][$j]["plus_sc"]="";}
							if(!isset($newSrd[$i][$j]["plus_tax"])){$newSrd[$i][$j]["plus_tax"]="";}
						//echo count($newSrd[$i])."<br>";
						// if have srd_id
						if ($newSrd[$i][$j]["srd_id"] != "") {
							
							//if field set_tax of product in table cl_product not set (equal zero)
							//auto update field set_tax that product in table c_salesreceipt to not set (zero). 
							$settax = $this->getIdToText($newSrd[$i][$j]["pd_id"], "cl_product", "set_tax", "pd_id");
							if (!$settax) {
								$newSrd[$i][$j]["plus_tax"] = $settax;
							}

							//if field set_sc of product in table cl_product not set (equal zero)
							//auto update field set_sc that product in table c_salesreceipt to not set (zero). 
							$setsc = $this->getIdToText($newSrd[$i][$j]["pd_id"], "cl_product", "set_sc", "pd_id");
							if (!$setsc) {
								$newSrd[$i][$j]["plus_sc"] = $setsc;
							}

							// update data in table c_srdetail.
							$sqlSrD = "UPDATE c_srdetail SET " .
							"pd_id='" . $newSrd[$i][$j]["pd_id"] . "'," .
							"unit_price='" . $newSrd[$i][$j]["unit_price"] . "'," .
							"qty='" . $newSrd[$i][$j]["quantity"] . "'," .
							"set_tax='" . $newSrd[$i][$j]["plus_tax"] . "'," .
							"set_sc='" . $newSrd[$i][$j]["plus_sc"] . "'," .
							"book_id='$bookid', pds_id='$pdsid' " .
							"WHERE srdetail_id='" . $newSrd[$i][$j]["srd_id"] . "'";
							$srDId = $this->setResult($sqlSrD);
							
							// update data in table c_srpayment.
							//$sqlMpD = "UPDATE c_srpayment SET " .
							//"pay_id='" . $newSrd[$i][$j]["paytype"] . "',pay_total='" . $newSrd[$i][$j]["pay_price"] . "' ".
							//"WHERE srpayment_id='" . $newSrd[$i][$j]["mpd_id"] . "'";
							//$mpDId = $this->setResult($sqlMpD);
							
							if (!$srDId) {
								$this->setErrorMsg("Can't update detail in sale receipt slip.");
								return "noValue";
							}
							// else if not have srd_id
						} else {

							//value for insert into table c_srdetail.
							$tmp["pd_id"] = $newSrd[$i][$j]["pd_id"];
							$tmp["quantity"] = $newSrd[$i][$j]["quantity"];
							$tmp["unit_price"] = $newSrd[$i][$j]["unit_price"];
							$tmp["plus_sc"] = $newSrd[$i][$j]["plus_sc"];
							$tmp["plus_tax"] = $newSrd[$i][$j]["plus_tax"];
				
							//$tmp["paytype"] = $newSrd[$i][$j]["paytype"];
							//$tmp["pay_price"] = $newSrd[$i][$j]["pay_price"];
							
							$tmp = $this->insertSaleReceipt($tmp, $bookid, $newSrd[$i][0]["sr_id"], $pdsid, "");
				
							if (!$tmp) {
								$this->setErrorMsg("Can't insert detail into sale receipt slip.");
								return "noValue";
							}
							//when insert sale receipt detail complete.
							//get sale receipt detail id (srd_id) back.
							$newSrd[$i][$j]["srd_id"] = $tmp["srd_id"];
						}
						
						

						// update log for salereceipt detail
						// update log only in update process because 
						// we update all salereceipt detail again after add/update salereceipt finish 
						$lsrd_id = $newSrd[$i][$j]["srd_id"];
						$lsr_id = $newSrd[$i][0]["sr_id"];
						$lpd_id = $newSrd[$i][$j]["pd_id"];
						$lunit_price = $newSrd[$i][$j]["unit_price"];
						$lquantity = $newSrd[$i][$j]["quantity"];
						$lbook_id = $bookid;
						$lpds_id = $pdsid;
						$lplus_tax = $newSrd[$i][$j]["plus_tax"];
						$lplus_sc = $newSrd[$i][$j]["plus_sc"];
						$ll_lu_user = $userId;
						$ll_lu_ip = $_SERVER["REMOTE_ADDR"];
						$ll_datets = "now()";
						$chksql = "insert into log_c_srdetail(" .
						"srdetail_id,salesreceipt_id,pd_id,unit_price," .
						"qty,book_id,pds_id,set_tax,set_sc," .
						"l_lu_user,l_lu_ip,l_lu_date) " .
						"value ('$lsrd_id','$lsr_id','$lpd_id','$lunit_price'," .
						"'$lquantity','$lbook_id','$lpds_id','$lplus_tax'," .
						"'$lplus_sc','$ll_lu_user','$ll_lu_ip',$ll_datets)";
						$logid = $this->setResult($chksql);
					}
					
					// update data in table c_srpayment.
					//for ($k = 0; $k < $mpdcount[$i]; $k++) {
					for ($k = 0; $k < $mpdcount[$i]; $k++) {
						if(!isset($newSrd[$i][$k]["mpd_id"])){$newSrd[$i][$k]["mpd_id"]="";}
						if(!isset($newSrd[$i][$k]["paytype"])){$newSrd[$i][$k]["paytype"]="";}
						if(!isset($newSrd[$i][$k]["pay_price"])){$newSrd[$i][$k]["pay_price"]="";}
						
						if ($newSrd[$i][$k]["mpd_id"] != "") {
							
							$sqlMpD = "UPDATE c_srpayment SET " .
							"pay_id='" . $newSrd[$i][$k]["paytype"] . "',pay_total='" . $newSrd[$i][$k]["pay_price"] . "' ".
							"WHERE srpayment_id='" . $newSrd[$i][$k]["mpd_id"] ."'";
							//echo $sqlMpD."<br>"; 
							$mpDId = $this->setResult($sqlMpD);
							if (!$mpDId) {
								$this->setErrorMsg("Can't update detail in sale receipt slip.");
								return "noValue";
							}	
						}else {

							//value for insert into table c_srpayment.
							$smp["paytype"] = $newSrd[$i][$k]["paytype"];
							$smp["pay_price"] = $newSrd[$i][$k]["pay_price"];
							
							$smp = $this->insertSaleReceipt($srd, $bookid, $newSrd[$i][0]["sr_id"], $pdsid, $smp);
				
							if (!$smp) {
								$this->setErrorMsg("Can't insert detail into sale receipt slip.");
								return "noValue";
							}
							if(!isset($smp["mpd_id"])){$smp["mpd_id"]="";}
							$newSrd[$i][$k]["mpd_id"] = $smp["mpd_id"];
						 }
						// update log for c_srpayment
						// update log only in update process because 
						// we update all muti payment detail again after add/update salereceipt finish 
								$lmpd_id = $newSrd[$i][$k]["mpd_id"];
								$lbook_id = $bookid;
								$lsr_id = $newSrd[$i][0]["sr_id"];
								$lpds_id = $pdsid;
								$lp_id = $newSrd[$i][$k]["paytype"];
								$lp_price = $newSrd[$i][$k]["pay_price"];
								$ll_lu_user = $userId;
								$ll_lu_ip = $_SERVER["REMOTE_ADDR"];
								$ll_datets = "now()";
								if($lp_id!=0){
								$chksql = "insert into log_c_srpayment(" .
								"srpayment_id,book_id,salesreceipt_id,pds_id," .
								"pay_id,pay_total," .
								"l_lu_user,l_lu_ip,l_lu_date) " .
								"value ('$lmpd_id','$lbook_id','$lsr_id','$lpds_id'," .
								"'$lp_id','$lp_price'," .
								"'$ll_lu_user','$ll_lu_ip',$ll_datets)";
								$logid = $this->setResult($chksql);
								}
						}
				} else {
					$this->setErrorMsg("Can't update data in sale receipt slip.");
					return "noValue";
				}
			}
		}
		$this->deleteSaleReceiptData($newSrd, $bookid, $pdsid,$mpdcount);

		return $newSrd;
	}

	/*
	 * function to insert data into table c_salereceipt
	 * @modified - add this function on 19 dec 2008
	 * @modified - recoding on this function 14 july 2009 by Ruck
	 */
	function insertSaleReceipt($srd, $bookid = 0, $sr_id, $pdsid = 0) {
		// case insert c_salesreceipt
		if ($sr_id == "") {
			$sqlSr = "insert into c_salesreceipt(salesreceipt_id,book_id," .
			"pay_id,sr_comment,sr_total,pds_id)" .
			" values('','$bookid','" . $srd["paytype"] . "'," .
			"'" . htmlspecialchars($srd["comment"]) . "'," .
			"'" . $srd["sr_total"] . "',$pdsid)";
			$srId = $this->setResult($sqlSr);
			$srd["sr_id"] = $srId;
		} else {
			$srId = $sr_id;
		}

		// after insert c_salesreceipt then insert 1st c_srdetail of this c_salesreceipt
		// or when we want to insert c_srdetail but already have c_salesreceipt can use this condition 
		// just set $sr_id pass trought the function argument
		if(!isset($srd["pd_id"])){$srd["pd_id"]=0;}
		if ($srId && $srd["pd_id"]!=0) {

			$sqlSrD = "insert into c_srdetail(srdetail_id,salesreceipt_id,pd_id," .
			"unit_price,qty,book_id,pds_id,set_sc,set_tax) " .
			"values('','$srId','" . $srd["pd_id"] . "" .
			"','" . $srd["unit_price"] . "" .
			"','" . $srd["quantity"] . "','$bookid',$pdsid," .
			"'" . $srd["plus_sc"] . "','" . $srd["plus_tax"] . "')";

			$srDId = $this->setResult($sqlSrD);
			$srd["srd_id"] = $srDId;
			
			if (!$srDId) {
				return false;
			}
		}
			if(!isset($smp["paytype"])){$smp["paytype"]=0;}
			if ($smp["paytype"])  {
				
				//$sql = "select * from c_srpayment where salesreceipt_id=" . $srId ." and pay_id =".$smp["paytype"];
				//$Mp = $this->getResult($sql);
				//echo $sql."<br>";	
				//if(!$Mp){
					$sqlMpD = "insert into c_srpayment(srpayment_id,book_id,salesreceipt_id," .
					"pds_id,pay_id,pay_total) " .
					"values('','$bookid','$srId','$pdsid','".$smp["paytype"]."','".$smp["pay_price"]."')";
					//echo $sql."<br>";
					$mpDId = $this->setResult($sqlMpD);
					$srd["mpd_id"] = $mpDId;				
				//}
				
				if (!$mpDId) {
					return false;
				}
			} 	
		return $srd;
	}
	/*
	* function to show data in array sale receipt 3 dimension
	* @modified - add this function on 19 dec 2008
	*/
	function showSrDetail($srd) {
		echo "<table border=\"1\">";
		echo "<tr>" .
		"<td><b> srd_id </b></td>" .
		"<td><b> pd_id </b></td>" .
		"<td><b> qty </b></td>" .
		"<td><b> unit price </b></td>" .
		"<td><b> set SC </b></td>" .
		"<td><b> set Tax </b></td>" .
		"</tr>";

		$amountSr = 0;
		for ($i = 0; $i < count($srd); $i++) {
			$emptyData = true;
			$countSql = 0;

			echo "<tr>" .
			"<td colspan=\"6\"><b>srd_id</b>" . $srd[$i][0]["sr_id"] . " " . (($srd[$i][0]["paid"]) ? "yes" : "no") . "<br/>" .
			"<b>comment:</b>" . $srd[$i][0]["comment"] . "<br/>" .
			"<b>pay type:</b>" . $srd[$i][0]["paid"] . "<br/>" .
			"</td>" .
			"</tr>";

			for ($j = 0; $j < count($srd[$i]); $j++) {
				if ($srd[$i][$j]["pd_id"] != 1) {
					$emptyData = false;
					echo "<tr>" .
					"<td>" . $srd[$i][$j]["srd_id"] . "&nbsp;</td>" .
					"<td>" . $srd[$i][$j]["pd_id"] . "</td>" .
					"<td>" . $srd[$i][$j]["quantity"] . "</td>" .
					"<td>" . $srd[$i][$j]["unit_price"] . "</td>" .
					"<td>" . $srd[$i][$j]["plus_sc"] . "</td>" .
					"<td>" . $srd[$i][$j]["plus_tax"] . "</td>" .
					"</tr>";
					/*
					echo "sr_id -> ".$srd[$i][0]["sr_id"]."<br>";
					echo "paid -> ".$srd[$i][0]["paid"]."<br>";
					echo "pay type -> ".$srd[$i][0]["paytype"]."<br>";
					echo "Sr_comment -> ".$srd[$i][0]["comment"]."<br>";
					echo "srd_id -> ".$srd[$i][$j]["srd_id"]."<br>";
					echo "pd_id -> ".$srd[$i][$j]["pd_id"]."<br>";
					echo "qty -> ".$srd[$i][$j]["quantity"]."<br>";
					echo "unit price -> ".$srd[$i][$j]["unit_price"]."<br>";
					echo "Plus SC -> ".$srd[$i][$j]["plus_sc"]."<br>";	
					echo "Plus Tax -> ".$srd[$i][$j]["plus_tax"]."<br>";
					*/
				}
			}
			if (!$emptyData) {
				$amountSr++;
			}
			echo "<tr>" .
			"<td colspan=\"6\">&nbsp;------------------------------------------------------</td>" .
			"</tr>";
		}
		echo "</table>";
		//echo "Amount Sale Receipt -> ".$amountSr."<br>";
	}
	/*
	* function to get current sale receipt tab 
	* @modified - add this function on 19 dec 2008
	* @modified - recoding on this function 14 july 2009 by Ruck
	* Remove now_check_paid variable.Don't use value on interface by ruck 14-07-2009
	*/
	function getCurrentTab($srd) {
		$newSrd = array ();
		$amountSr = 0;
		if ($srd == false) {
			return array ();
		}
		for ($i = 0; $i < count($srd); $i++) {
			$emptyData = true;
			$count = 0;
			for ($j = 0; $j < count($srd[$i]); $j++) {

				//For debug undefined index:  pd_id. By Ruck : 18-05-2009
				if (!isset ($srd[$i][$j]["pd_id"])) {
					$srd[$i][$j]["pd_id"] = 1;
				}

				// --Select-- have pd_id value = 1
				// cutoff pd_id = 1 
				if ($srd[$i][$j]["pd_id"] != 1) {
					$emptyData = false;

					//For debug undefined index: By Ruck : 18-05-2009
					if (isset ($srd[$i][0]["sr_id"])) {
						$newSrd[$amountSr][0]["sr_id"] = $srd[$i][0]["sr_id"];
					} else {
						$newSrd[$amountSr][0]["sr_id"] = "";
					}
					if (isset ($srd[$i][$j]["pd_id_tmp"])) {
						$newSrd[$amountSr][$count]["pd_id_tmp"] = $srd[$i][$j]["pd_id_tmp"];
					} else {
						$newSrd[$amountSr][$count]["pd_id_tmp"] = "";
					}
					if (isset ($srd[$i][$j]["quantity"])) {
						$newSrd[$amountSr][$count]["quantity"] = $srd[$i][$j]["quantity"];
					} else {
						$newSrd[$amountSr][$count]["quantity"] = 1;
					}
					if (isset ($srd[$i][$j]["unit_price"])) {
						$newSrd[$amountSr][$count]["unit_price"] = $srd[$i][$j]["unit_price"];
					} else {
						$newSrd[$amountSr][$count]["unit_price"] = 0;
					}
					if (isset ($srd[$i][0]["paid"])) {
						$chkPaid = $srd[$i][0]["paid"];
					} else {
						$chkPaid = 0;
					}

					if ($chkPaid === "checked" || $chkPaid == 1) {
						$newSrd[$amountSr][0]["paid"] = 1;
					} else {
						$newSrd[$amountSr][0]["paid"] = 0;
					}

					$newSrd[$amountSr][0]["paytype"] = $srd[$i][0]["paytype"];
					$newSrd[$amountSr][0]["comment"] = $srd[$i][0]["comment"];
					$newSrd[$amountSr][0]["sr_total"] = $srd[$i][0]["sr_total"];
					$newSrd[$amountSr][$count]["srd_id"] = $srd[$i][$j]["srd_id"];
					$newSrd[$amountSr][$count]["pd_id"] = $srd[$i][$j]["pd_id"];
					$newSrd[$amountSr][$count]["plus_sc"] = $srd[$i][$j]["plus_sc"];
					$newSrd[$amountSr][$count]["plus_tax"] = $srd[$i][$j]["plus_tax"];
					$count++;
				}
			}
			if (!$emptyData) {
				$amountSr++;
			}
		}
		return $newSrd;
	}

	/* get current rows in sale receipt when initial sales receipt
	 * @modified - add this function on 20 dec 2008
	 * This function will return amount of rows in each sales receipt
	 */
	function getCurrentRowsInTab($srd, $srcount) {
		$newSrdCount = array ();
		for ($i = 0; $i < $srcount; $i++) {
			$rows = 1; // set $rows = 1 for condition if open 1 st time it must have 1 srd, if select some product it must show next srd for select
			if (isset ($srd[$i])) { // For debug Undefined offset:  0,1. By Ruck : 15-05-2009
				for ($j = 0; $j < count($srd[$i]); $j++) {
					if ($srd[$i][$j]["pd_id"] != 1 && $srd[$i][$j]["pd_id"] != 0) {
						$rows++;
					}
				}
			}
			$newSrdCount[$i] = $rows;

		}
		return $newSrdCount;
	}

	/* function to get data of sale receipt in this book 
	* @modified - add this function on 20 dec 2008
	* This function will return array of sale receipt data
	*/
	function getSaleReceiptData($bookid = 0, $pdsid = 0) {
		$newSrd = array ();
		//$sqlSr="select * from c_salesreceipt where book_id=$bookid and pds_id=$pdsid order by salesreceipt_id asc";
		$sqlSr = "select * from c_salesreceipt where book_id=$bookid and pds_id=$pdsid";
		$srId = $this->getResult($sqlSr);
		//echo $sqlSr."<br>".$srId["rows"]."<br>";
		for ($i = 0; $i < $srId["rows"]; $i++) {
			$newSrd[$i][0]["paid"] = $srId[$i]["paid_confirm"];
			$newSrd[$i][0]["paytype"] = $srId[$i]["pay_id"];
			$newSrd[$i][0]["comment"] = $srId[$i]["sr_comment"];

			$sqlSrD = "select * from c_srdetail where salesreceipt_id=" . $srId[$i]["salesreceipt_id"] . " order by srdetail_id asc ";
			$srDId = $this->getResult($sqlSrD);

			//echo $sqlSrD."<br>";
			for ($j = 0; $j < $srDId["rows"]; $j++) {
				//echo "sr_id -> ".$srDId[$j]["srdetail_id"]."<br>";
				$newSrd[$i][0]["sr_id"] = $srDId[$j]["salesreceipt_id"];
				$newSrd[$i][$j]["srd_id"] = $srDId[$j]["srdetail_id"];
				$newSrd[$i][$j]["pd_id"] = $srDId[$j]["pd_id"];
				$newSrd[$i][$j]["quantity"] = $srDId[$j]["qty"];
				$newSrd[$i][$j]["unit_price"] = $srDId[$j]["unit_price"];
				$newSrd[$i][$j]["plus_tax"] = $srDId[$j]["set_tax"];
				$newSrd[$i][$j]["plus_sc"] = $srDId[$j]["set_sc"];

			}
		}

		return $newSrd;
	}
	/* function for delete data from sale receipt detail in this book 
	* @modified - add this function on 20 dec 2008
	*/
	function deleteSaleReceiptData($srd, $bookid = 0, $pdsid = 0) {
		$sqlSr = "select salesreceipt_id from c_salesreceipt where book_id=$bookid and pds_id=$pdsid";
		$srId = $this->getResult($sqlSr);

		//For debug variable : deleteSrd,deleteId. By Ruck : 18-05-2009
		$deleteSr = array ();
		$deleteSrdetail = array ();

		if ($srId) {
			$countSr = 0;
			$countSrdetail = 0;

			for ($i = 0; $i < $srId["rows"]; $i++) {
				$hasSr = false;

				for ($j = 0; $j < count($srd); $j++) {
					if ($srId[$i]["salesreceipt_id"] == $srd[$j][0]["sr_id"]) {
						$hasSr = true;
					}
				}

				//$hasSr = true => salesreceipt_id in database match with interface
				//$hasSr = false => salesreceipt_id in database not match with interface
				if (!$hasSr) {
					//$hasSr = false :: add salesreceipt_id into array and waitting for delete.
					$deleteSr[$countSr] = $srId[$i]["salesreceipt_id"];
					$countSr++;
				} else {
					$sqlSrD = "select srdetail_id from c_srdetail where salesreceipt_id=" . $srId[$i]["salesreceipt_id"];
					$srDId = $this->getResult($sqlSrD);

					if ($srDId) {

						for ($q = 0; $q < $srDId["rows"]; $q++) {
							$hasSrdetail = false;

							for ($si = 0; $si < count($srd); $si++) {
								for ($sj = 0; $sj < count($srd[$si]); $sj++) {
									if ($srDId[$q]["srdetail_id"] == $srd[$si][$sj]["srd_id"]) {
										$hasSrdetail = true;
									}
								}
							}
							//$hasSrdetail = true => srdetail_id in database match with interface
							//$hasSrdetail = false => srdetail_id in database not match with interface
							if (!$hasSrdetail) {
								//$hasSr = false :: add srdetail_id into array and waitting for delete.
								$deleteSrdetail[$countSrdetail] = $srDId[$q]["srdetail_id"];
								$countSrdetail++;
							}
						}
					}
				}
			}

			// update log_c_srdetail
			for ($i = 0; $i < count($deleteSrdetail); $i++) {
				$this->updatelog_sr_detail($deleteSrdetail[$i], 0);
			}

			if (count($deleteSrdetail) > 0) {
				//Delete  record in table srdetail which have srdetail_id match in $deleteSrdetail array.  
				$deleteSrdString = implode(",", $deleteSrdetail);
				$sql = "DELETE FROM c_srdetail WHERE srdetail_id in($deleteSrdString)";
				$this->setResult($sql);
			}

			// update log_c_srdetail
			for ($i = 0; $i < count($deleteSr); $i++) {
				$this->updatelog_sr($deleteSr[$i], 0);

				$sql = "select srdetail_id from c_srdetail where salesreceipt_id = " . $deleteSr[$i];
				$rs = $this->getResult($sql);
				for ($j = 0; $j < $rs["rows"]; $j++) {
					$this->updatelog_sr_detail($rs[$i]["srdetail_id"], 0);
				}
			}

			if (count($deleteSr) > 0) {
				//Delete record in table c_srdetail and c_salesreceipt which 
				//have salesreceipt_id match in $deleteSr array.
				$deleteSrString = implode(",", $deleteSr);
				$sql = "DELETE FROM c_srdetail WHERE salesreceipt_id in($deleteSrString)";
				$this->setResult($sql);
				$sql = "DELETE FROM c_salesreceipt WHERE salesreceipt_id in($deleteSrString)";
				$delete = $this->setResult($sql);
			}

		}
	}

	/* 
	 * function for update sale receipt log
	 * @modified - add this function on 04-Sep-2009
	 */
	function updatelog_sr($sr_id = false, $active = 1, $debug = false) {
		$chksql = "select * from c_salesreceipt " .
		"where salesreceipt_id = " . $sr_id;
		$logrs = $this->getResult($chksql);

		// update log for salereceipt when delete some salereceipt
		$lsr_id = $logrs[0]["salesreceipt_id"];
		$lsr_number = $logrs[0]["salesreceipt_number"];
		$lbook_id = $logrs[0]["book_id"];
		$lpaid_confirm = $logrs[0]["paid_confirm"];
		$lpay_id = $logrs[0]["pay_id"];
		$lsr_lu_user = $_SESSION["__user_id"];
		$lsr_datets = "now()";
		$lsr_total = $logrs[0]["sr_total"];
		$lsr_comment = htmlspecialchars($logrs[0]["sr_comment"]);
		$lpds_id = $logrs[0]["pds_id"];
		$ll_lu_ip = $_SERVER["REMOTE_ADDR"];
		$chksql = "insert into log_c_sr(" .
		"salesreceipt_id,salesreceipt_number,book_id," .
		"paid_confirm,pay_id,sr_lu_user,sr_datets,active," .
		"sr_comment,pds_id,sr_total,l_lu_ip) " .
		"value ('$lsr_id','$lsr_number','$lbook_id'," .
		"'$lpaid_confirm','$lpay_id','$lsr_lu_user',$lsr_datets,$active," .
		"'$lsr_comment','$lpds_id','$lsr_total','$ll_lu_ip')";
		$logid = $this->setResult($chksql);
		return $logid;
	}

	/* 
	 * function for update sale receipt detail log
	 * @modified - add this function on 04-Sep-2009
	 */
	function updatelog_sr_detail($srd_id = false, $active = 1, $debug = false) {
		$chksql = "select * from c_srdetail " .
		"where srdetail_id = " . $srd_id;
		$logrs = $this->getResult($chksql);

		// update log for salereceipt detail
		// update log only in update process because 
		// we update all salereceipt detail again after add/update salereceipt finish 
		$lsrd_id = $logrs[0]["srdetail_id"];
		$lsr_id = $logrs[0]["salesreceipt_id"];
		$lpd_id = $logrs[0]["pd_id"];
		$lunit_price = $logrs[0]["unit_price"];
		$lquantity = $logrs[0]["qty"];
		$lbook_id = $logrs[0]["book_id"];
		$lpds_id = $logrs[0]["pds_id"];
		$lplus_tax = $logrs[0]["set_tax"];
		$lplus_sc = $logrs[0]["set_sc"];
		$ll_lu_user = $_SESSION["__user_id"];
		$ll_lu_ip = $_SERVER["REMOTE_ADDR"];
		$ll_datets = "now()";
		$chksql = "insert into log_c_srdetail(" .
		"srdetail_id,salesreceipt_id,pd_id,unit_price,active," .
		"qty,book_id,pds_id,set_tax,set_sc," .
		"l_lu_user,l_lu_ip,l_lu_date) " .
		"value ('$lsrd_id','$lsr_id','$lpd_id','$lunit_price',$active," .
		"'$lquantity','$lbook_id','$lpds_id','$lplus_tax'," .
		"'$lplus_sc','$ll_lu_user','$ll_lu_ip',$ll_datets)";
		$logid = $this->setResult($chksql);
		return $logid;
	}

	//########################################## End Function for Sale Receipt #######################################
	//########################################## Function for Manage Appointment View Page #######################################
	/* function for add therapist available in table bl_th_available
	 * @modified - add this function on 22 dec 2008
	 */
	function addThAvailable($branch_id = false, $th_shiftone, $th_shifttwo, $debug = false) {
		if (!$branch_id) {
			$this->setErrorMsg("Please select branch before insert Therapists Available value !!");
			return false;
		}
		if (!is_numeric($th_shiftone) || !is_numeric($th_shifttwo)) {
			$this->setErrorMsg("Please check on Therapists Available value !!");
			return false;
		}

		$sql = "insert into bl_th_available " .
		"values('',$branch_id,$th_shiftone,$th_shifttwo,now())";

		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		return $this->setResult($sql);
	}

	/* function for add therapist sign-in list in table bl_th_list
	 * @modified - add this function on 20 may 2009
	 */
	function addThList($thid, $now = false, $debug = false) {
		$chkthid = $this->getIdToText($thid, "bl_th_list", "th_list_id", "th_id", "`leave` = \"0\" and `l_lu_date`>=\"" . date("Y-m-d") . "\"");
		if ($chkthid > 0) {
			$this->setErrorMsg("This therapist is already sign in!!");
			return false;
		}
		$ip = $_SERVER["REMOTE_ADDR"]; // text
		$userid = $this->getUserIdLogin(); // id
		$thshift = $this->getIdToText("1", "a_company_info", "th_shift_hour", "company_id"); // id
		$ubranchid = $this->getIdToText("$userid", "s_user", "branch_id", "u_id"); // this user id
		if ($ubranchid > 1) {
			$branchid = $ubranchid; // id
		} else {
			$branchid = $this->getIdToText("$thid", "l_employee", "branch_id", "emp_id"); // id
		}
		if (!$now) {
			$now = "now()";
		} else {
			$now = "\"$now\"";
		}
		$sql = "select * from bl_th_list " .
				"where l_lu_date>=\"" . date("Y-m-d") . "\"" .
				"and branch_id=$branchid " .
				"order by queue_order desc ";
		$rs = $this->getResult($sql);
		$next_queue = $rs[0]["queue_order"]+1;
		$sql = "insert into bl_th_list (th_id,queue_order,branch_id,th_shift,l_lu_user,l_lu_date,l_lu_ip)" .
		"values($thid, $next_queue, $branchid, $thshift, $userid, $now,\"$ip\")";
		
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		return $this->setResult($sql);
	}

	/* function for set therapist's leave in table bl_th_list
	 * @modified - add this function on 20 may 2009
	 */
	function removeThList($thlistid = false, $now = false, $debug = false) {
		if (!$now) {
			$now = "now()";
		} else {
			$now = "\"$now\"";
		}
		$ip = $_SERVER["REMOTE_ADDR"]; // text
		$userid = $this->getUserIdLogin(); // id
		$thshift = $this->getIdToText("1", "a_company_info", "th_shift_hour", "company_id"); // id
		$sql = "update `bl_th_list` set " .
		"`leave`=1, " .
		"`leave_time`=$now " .
		"where `th_list_id`=$thlistid ";

		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		return $this->setResult($sql);
	}

	/* function for get therapist sign-in list in table bl_th_list
	 * @modified - add this function on 20 may 2009
	 */
	function getThList($cityid = false, $page = false, $order = false, $sort = false, $debug = false) {
		$rows = $this->getShowpage(); // number of rows per page
		$start = $rows * $page - $rows; // starting from record $start
		$sql = "select bl_th_list.*,bl_branchinfo.branch_name,l_employee.emp_nickname as therapist_name,l_employee.emp_code " .
		"from bl_th_list,bl_branchinfo,l_employee " .
		"where bl_branchinfo.branch_id=bl_th_list.branch_id " .
		"and bl_th_list.l_lu_date>=\"" . date("Y-m-d") . "\" " .
		"and l_employee.emp_id=bl_th_list.th_id ";
		if ($cityid) {
			$sql .= "and bl_branchinfo.city_id=$cityid ";
		}
		if ($order == "th_shift") {
			$order = "bl_th_list.l_lu_date";
		}
		if ($order) {
			$sql .= "order by $order $sort,bl_th_list.l_lu_date ";
		} else {
			$sql .= "order by bl_th_list.l_lu_date,l_employee.emp_nickname ";
		}
		if ($page) {
			$sql .= "limit $start,$rows";
		}
		//echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		return $this->getResult($sql);
	}

	/* function for get Result set of Appointment from table a_bookinginfo, d_indivi_info
	 * @modified - add this function on 22 dec 2008
	 */
	function getMainappointment($date = false, $branch_id = false, $debug = false) {
		$sql = "select a_appointment.*,l_timeperiod.tp_distance" .
		" from a_appointment,l_timeperiod,a_company_info" .
		" where appt_date=" . $date . " and branch_id=$branch_id and b_set_cancel=0 and l_timeperiod.tp_id=a_company_info.tp_id";

		if (false) {
			echo $sql . "<br>";
			return false;
		}
		//echo $sql."<br>";
		return $this->getResult($sql);
	}
	/*
	 * @moidified - check user can click link or not 21-Feb-2009 *Not add this function
	*/
	function getCancelBooking($date = false, $branch_id = false, $isEdit = false, $debug = false) {
		$sql = "select book_id,bpds_id from a_appointment where appt_date=" . $date . " and branch_id=$branch_id and b_set_cancel=1 and bpds_id!=0 order by book_id";
		$rs = $this->getResult($sql);
		$ccbook = "";
		for ($i = 0; $i < $rs["rows"]; $i++) {
			if ($ccbook != '') {
				$ccbook .= ", ";
			}
			if ($isEdit) {
				$ccbook .= "<a href='javascript:;;' onClick=\"newwindow('manage_booking.php?chkpage=1&bookid=" . $rs[$i]["book_id"] . "','manageBooking" . $rs[$i]["book_id"] . "')\" class=\"menu\">ID : " . $rs[$i]['bpds_id'] . "</a>";
			} else {
				$ccbook .= "<b class=\"menu\">ID : " . $rs[$i]['bpds_id'] . "</b>";
			}

		}
		return $ccbook;
	}

	/*
	 * function for generate booking block color for book status
	 * @modified - add on 23-Dec-2008
	 * @modified - edit function add condetion for check blockcolor if customer > 2 in same room 
	 */
	function chkBlockColor($status = false) {
		$status = explode("|", $status);
		//print_r($status);
		for ($i = 0; $i < count($status); $i++) {
			if ($status[$i] == 4) {
				$colormask[$i] = "#da9e1e"; //maintenance room
			}
			elseif ($status[$i] == 3) {
				$colormask[$i] = "#a4b9d9"; //finish all old - #b6c7e0
			} else
				if ($status[$i] == 2) {
					$colormask[$i] = "#e9a9c9"; //at room
				} else
					if ($status[$i] == 1) {
						$colormask[$i] = "#b7a5d3"; //at spa old - #c2bbcf
					} else {
						$colormask[$i] = "#dfe37d"; //no have booking
					}
		}
		return $colormask;
	}

	/*
	 * function reset appointment time for each client
	 * @modified - add this function on 24-Dec-2008
	 */
	function chkBlockTimeStart($time_start = false, $time_period_distance = false, $timeline = false){
		
        for($i=0; $i<$timeline["rows"]; $i=$i+$time_period_distance) {
			$startptimeid = $timeline[$i]["time_id"];
			$lastptimeid = $startptimeid + $time_period_distance;
			
			if($time_start>=$startptimeid&&$time_start<$lastptimeid){		
				if($time_period_distance==3){
					$check_time_start = abs($time_start -$startptimeid);
					if($check_time_start<=1){$time_start=$startptimeid;}
					else{$time_start=$lastptimeid;}
				}else if($time_period_distance==6){
					$check_time_start = abs($time_start -$startptimeid);
					if($check_time_start<3){$time_start=$startptimeid;}
					else if($check_time_start>=3){$time_start=$lastptimeid;}
				}
				break;
			}
        }
        /*	
		// find time start remove mins
		if($time_period_distance==3){
			$check_time_start = ($time_start -1) % 3;
			if($check_time_start==1){
					$time_start=$time_start-1;
			}else if($check_time_start==2){
					$time_start=$time_start+1;
			}
		}else if($time_period_distance==6){
			$check_time_start = ($time_start -1) % 6;
			if($check_time_start<3){
					$time_start=$time_start-$check_time_start;
			}else if($check_time_start>=3){
					$time_start=$time_start+6-$check_time_start;
			}
		}
		*/
		return $time_start;
	}
	
	/*
	 * function check block height that come from time end
	 * @modified - add this function on 24-Dec-2008
	 */
	function chkBlockTimeEnd($hours = false, $time_start = false, $hourperiod = false, $timeline = false, $time_period_distance = false) {
		$time_end = array ();
		for ($i = 0; $i < count($hours); $i++) {
			$erhour = explode(",", $hours[$i]); //cast therapist hour in each room to array and sort
			sort($erhour);
			$maxhour[$i] = $erhour[count($erhour) - 1];

			// convert time start to minute
			$time_start_min[$i] = 60 * (8 + floor(($time_start -1) / 12)) + 5 * (($time_start -1) % 12);
			// find time finish in each room
			$hour_name = (isset($hourperiod[$maxhour[$i]]))?$hourperiod[$maxhour[$i]]:"00:00:00";
			
			list ($hr, $min, $sec) = explode(":", $hour_name);
			$max_hour_min[$i] = (60 * $hr) + $min;
			$time_end_min[$i] = $time_start_min[$i] + $max_hour_min[$i];	// time finish in minute
			
			// convert time finish to id
			$time_end[$i] = 12 * (floor( $time_end_min[$i] / 60 ) - 8) + 1 + 
			( $time_end_min[$i] - 60 * floor( $time_end_min[$i] / 60 )) / 5; 
			
			// find time end remove mins
			if($time_period_distance){
		        for($j=0; $j<$timeline["rows"]; $j=$j+$time_period_distance) {
					$startptimeid = $timeline[$j]["time_id"];
					$lastptimeid = $startptimeid + $time_period_distance;
					
					if($time_end[$i]>=$startptimeid&&$time_end[$i]<=$lastptimeid){		
						if($time_period_distance==3){
							$check_time_start = abs($time_end[$i] -$startptimeid);
							if($check_time_start<=1){$time_end[$i]=$startptimeid;}
							else{$time_end[$i]=$lastptimeid;}
						}else if($time_period_distance==6){
							$check_time_start = abs($time_end[$i] -$startptimeid);
							if($check_time_start<3){$time_end[$i]=$startptimeid;}
							else if($check_time_start >=3){$time_end[$i]=$lastptimeid;}
						}
						break;
					}
		        }
	        }
			/*if($time_period_distance){
				if($time_period_distance==3){
						$check_time_end = ($time_end[$i] -1) % 3;
						if($check_time_end==1){
								$time_end[$i]=$time_end[$i]-1;
						}else if($check_time_end==2){
								$time_end[$i]=$time_end[$i]+1;
						}
				}else if($time_period_distance==6){
						$check_time_end = ($time_end[$i] -1) % 6;
						if($check_time_end<3){
								$time_end[$i]=$time_end[$i]-$check_time_end;
						}else if($check_time_end>=3){
								$time_end[$i]=$time_end[$i]+6-$check_time_end;
						}
				}
			}*/
		}
		//echo "<br>time_period_distance: $time_period_distance";
		//echo "<br>time start: $time_start ";print_r($time_start_min);
		//echo "<br>time end: ";print_r($time_end);
		//echo "<br>time hour: ";print_r($hours);
		if (!$time_end) {
			return 0;
		} else {
			return $time_end;
		}
	}

	/*
	 * function for generate data in table timeline-room in booking view page
	 * @modifeid - add this function on 23-Dec-2008 
	 * @modified - edit function add condition for check popup message and check each room timeline
	 */
	function chkBlockData($appointment = false, $room_id = false, $time_start = false, $time_end = false, $isEdit = false, $timeperiod = false) {
		$ans[] = array ();
		$ans["data"] = "";
		$ans["popup"] = "";
		$ans["color"] = "";
		$checkdata = false; //check block data validate this block has data
		for ($i = 0; $i < count($appointment["room_ids"]); $i++) {
			for ($j = 0; $j < count($appointment["room_ids"][$i]); $j++) {
				if ($appointment["room_ids"][$i][$j] == $room_id)
					$checkdata = true;
			}
			if ($checkdata) {
				$key = array_search($room_id, $appointment["room_ids"][$i]);
				/*if($room_id==$appointment["room_ids"][$i][$key])
					echo $appointment["end"][$i][$key];*/
				if (($time_start >= $appointment["start"][$i]) && ($time_start < $appointment["end"][$i][$key]) && $room_id == $appointment["room_ids"][$i][$key]) {
					//----------- generate bookind id text link -------------
					if ($appointment["start"][$i] >= $time_start&&$appointment["start"][$i] <= $time_end) {
						$color = "#387BC7";
						$class = "menu";
						if ((isset ($appointment["mem_code"][$i][0]) && count($appointment["mem_code"][$i]) == 1 && $appointment["mem_code"][$i][0] > 0) || (isset ($appointment["mem_code"][$i][$key]) && $appointment["mem_code"][$i][$key] != 0)) {
							$color = "#DE2418";
							$class = "mmenu";
						}
						$ans["data"] = "<font style=\"font-size:10px\">".substr($timeperiod[$appointment["cal_start"][$i]],0,5)."-".substr($timeperiod[$appointment["cal_end"][$i][$key]],0,5)."<font color=\"".$appointment["colormark"][$i][$key]."\">_</font></font>";
						//fix for header Maintunance room $appointment["bpds_id"][$i]=0 @modified 17-Apr-2009
						if ($isEdit) {
							if ($appointment["bpds_id"][$i] > 0) {
								$ans["data"] .= "<a href='javascript:;;' onClick=\"newwindow('manage_booking.php?chkpage=1&bookid=" . $appointment["book_id"][$i] . "','manageBooking" . $appointment["book_id"][$i] . "')\" style=\"font-size:10px\" class=\"$class\">ID:" . $appointment["bpds_id"][$i] . "</a>";
							} else {
								$mhead = ($appointment["therapist_names"][$i][$key] == "Maintenance Room") ? "Room Maintenance" : $appointment["therapist_names"][$i][$key];
								$ans["data"] .= "<b><a href='javascript:;;' onClick=\"newwindow('manage_mroom.php?chkpage=1&rmid=" . $appointment["book_id"][$i] . "','manageRM" . $appointment["book_id"][$i] . "')\" style=\"font-size:10px\" class=\"menu\">" . $mhead . "</a></b>";
							}
						} else {
							if ($appointment["bpds_id"][$i] > 0) {
								$ans["data"] .= "<b style=\"color:$color;font-size:10px\">ID:" . $appointment["bpds_id"][$i] . "</b>";
							} else {
								$ans["data"] .= "<b style=\"color:$color;font-size:10px\">" . $appointment["therapist_names"][$i][$key] . "</b>";
							}
						}
					}

					//----------- generate block color -------------
					if (isset ($appointment["driver_names"][$i][0]) && isset ($appointment["driver_names"][$i][1])) {
						if ($appointment["start"][$i] != $time_start) {
							$ans["color"] = ($appointment["colormark"][$i][$key] == "#dfe37d") ? "#dfe37d" : $appointment["colormark"][$i][$key];
						} else {
							if ($appointment["colormark"][$i][$key] == "#a4b9d9") {
								$ans["color"] = "#a4b9d9";
							} else
								if ($appointment["colormark"][$i][$key] == "#e9a9c9") {
									$ans["color"] = "#e9a9c9";
								} else
									if ($appointment["colormark"][$i][$key] == "#b7a5d3") {
										$ans["color"] = "#b7a5d3";
									} else {
										$ans["color"] = "#99cc00";
									}

							if ($appointment["driver_names"][$i][0] != "No Pickup" && $appointment["driver_names"][$i][1] != "No Takeback") {
								$ans["data"] .= "<b class=\"putb\" style=\"font-size:10px\">PU/TB</b>";
							} else
								if ($appointment["driver_names"][$i][1] != "No Takeback") {
									$ans["data"] .= "<b class=\"putb\" style=\"font-size:10px\">TB</b>";
								} else
									if ($appointment["driver_names"][$i][0] != "No Pickup") {
										$ans["data"] .= "<b class=\"putb\" style=\"font-size:10px\">PU</b>";
									}
						}
					} else {
						$ans["color"] = $appointment["colormark"][$i][$key];
					}

					//----------- generate therapist name -------------
					if ($appointment["start"][$i] + $appointment["timeperiod"][$i] == $time_start) {
						$ans["data"] = "";
						if ($appointment["therapist_names"][$i][$key] == "Maintenance Room") {
							$ans["data"] = " ";
						} else {
							$ans["data"] = "<b>" . str_replace(" -- select --", " ", $appointment["therapist_names"][$i][$key]) . "</b>";
						}

					}

					//----------- generate popup msg -------------
					$msg = "Name : " . $appointment["cs_name"][$i] . "<br/>";
					if ($appointment["cs_hotel"][$i] != " -- select --") {
						$msg .= "Hotel : " . $appointment["cs_hotel"][$i] . "<br/>";
					}
					if (isset ($appointment["driver_names"][$i][0]) && isset ($appointment["driver_names"][$i][1])) {
						if ($appointment["driver_names"][$i][0] != "No Pickup") {
							$msg .= "Driver P/U : " . str_replace(" -- select --", "No select", $appointment["driver_names"][$i][0]) . " " .
							$appointment["driver_times"][$i][0] . " " .
							$appointment["driver_place"][$i][0] . "<br/>";
						}
						if ($appointment["driver_names"][$i][1] != "No Takeback") {
							$msg .= "Driver T/B : " . str_replace(" -- select --", "No select", $appointment["driver_names"][$i][1]) . " " .
							$appointment["driver_times"][$i][1] . " " .
							$appointment["driver_place"][$i][1] . "<br/>";
						}
					}
					if ($appointment["bp_person"][$i] != "") {
						$msg .= "Booking Person : " . $appointment["bp_person"][$i] . "<br/>";
					}
					if ($appointment["bp_name"][$i] != " -- select --") {
						$msg .= "Booking Company : " . $appointment["bp_name"][$i] . "<br/>";
					}
					if ($appointment["mcategory"][$i] != "") {
						$msg .= "Membership Category : " . $appointment["mcategory"][$i] . "<br/>";
					}
					$msg .= "Total Customers : " . $appointment["ttpp"][$i][$key] . "<br/>";
					$msg = str_replace("[",'(',$msg);
					$msg = str_replace("]",')',$msg);
					$ans["popup"] = "title=\" header=[Booking ID : " . $appointment["bpds_id"][$i] . "] body=[" . $msg . "]\"";
					if ($appointment["bpds_id"][$i] == 0) {
						$msg = "&lt;b&gt;Reason&lt;/b&gt; : " . $appointment["cs_name"][$i] . " ";
						$ans["popup"] = "title=\" header=[] body=[" . $msg . "]\"";
					}
				}
			}
		}

		return $ans;
	}

	/*
	 * function for count therapist number that work in the timeline
	 * @modified - add this function on 24-Dec-2008 
	 */
	function getTherapistcount($appointment = false, $time_start = false, $time_end = false) {
		$cnt = 0;
		// the old one - count therapist number
		//echo count($appointment["room_ids"])."hour id: ";print_r($appointment["hour_ids"]);echo " <br/>";
		/*for($i=0;$i<$appointment["rows"];$i++){
			//if($time_start==$appointment["start"][$i])
			//	print_r($appointment["start"][$i]);
			if(isset($appointment["hour_ids"][$i])){
				$hour = explode(",",implode(",", $appointment["hour_ids"][$i]));
				//print_r($hour);echo "<br/>";
				//echo $this->getIdToText($hour[$i],"l_hour","hour_name","hour_id");
				for($j=0;$j<count($hour);$j++){
					if($appointment["start"][$i]<=$time_start&&$appointment["start"][$i]+6*($hour[$j]-1)>$time_start&&$appointment["bpds_id"][$i]!=0){
						//echo '$hour['.$j.']-'.$this->getIdToText($hour[$j],"l_hour","hour_name","hour_id").':'.$hour[$j]." ";
						$cnt++;
					}
				}
			}
		}
		*/
		// count customer number natt - Oct 26,2009
		for ($i = 0; $i < $appointment["rows"]; $i++) {
			if (isset ($appointment["hour_ids"][$i]) && $appointment["bpds_id"][$i] > 0) {
				$hour = explode(",", implode(",", $appointment["hour_ids"][$i]));
				for ($j = 0; $j < count($appointment["ttpp"][$i]); $j++) {
					if ($appointment["cal_start"][$i] < $time_end && $appointment["cal_end"][$i][$j] > $time_start ) {
						if (!isset ($appointment["ttpp"][$i][$j])) {
							$appointment["ttpp"][$i][$j] = 0;
						}
						$cnt += $appointment["ttpp"][$i][$j];
					}
				}
			}
		}
		if ($cnt == 0) {
			$cnt = "&nbsp;";
		}
		echo $cnt;
	}

	/*
	 * function for save copy appointment and insert table a_appointment  
	 * @modified - add this function on 23-01-2009 
	 */
	function saveCopyAppoiontment($book_id, $id, $tw, $date, $branch_id, $apptime, $city_change, $debug = false) { // $book_id - old booking id, $id - new copy booking id

		$roomNames = ""; //string
		$roomIds = ""; //string
		$status = ""; //string
		$qtyPeoples = ""; //string
		$hourIds = ""; //string
		$th_names = ""; //string
		$member_ids = ""; //string
		$appointment = array (); //array

		$sql = "select * from a_appointment where book_id=$book_id";
		$rs = $this->getResult($sql);

		$appointment["customer_name"] = $rs[0]["customer_name"]; // text
		$appointment["member_codes_old"] = preg_split("/\|+/", $rs[0]["member_code"]); // array ("|" is speacial character when use "\|" change to normal character.)
		$appointment["mcategory"] = $rs[0]["mcategory"]; // text
		$appointment["therapist_names_old"] = preg_split("/[|,]+/", $rs[0]["th_names"]); // array  (speacial character is in [] like "[|+]" will change to narmal character.)
		$appointment["hour_ids_old"] = preg_split("/[|,]+/", $rs[0]["hour_ids"]); // array
		$appointment["accdt_name"] = $rs[0]["accdt_name"]; // text
		$appointment["t_times"] = $rs[0]["t_times"]; // text
		$appointment["t_names"] = $rs[0]["t_names"]; // text
		$appointment["t_places"] = $rs[0]["t_places"]; // text
		$appointment["bp_name"] = $rs[0]["bp_name"]; // text
		$appointment["bp_person"] = $rs[0]["bp_person"]; // text	
		$appointment["b_set_cancel"] = 0; // integer :: reset b_set_cancel for copy booking
		$appointment["status"] = array (); //													// array
		$appointment["room_ids"] = array (); // array
		$appointment["room_names"] = array (); // array
		$appointment["qty_peoples"] = array (); // array
		$appointment["therapist_names"] = array (); // array
		$appointment["hour_ids"] = array (); // array
		$appointment["member_ids"] = array (); // array

		rsort($appointment["member_codes_old"]);

		for ($i = 0; $i < count($tw); $i++) {
			$chkroom = false; // check all room in this 
			for ($j = 0; $j < count($appointment["room_ids"]); $j++) {
				if ($tw[$i]["room_id"] == $appointment["room_ids"][$j]) {
					$chkroom = true;
					break;
				}
			}

			if ($chkroom) {
				$appointment["qty_peoples"][$j]++;
				$appointment["hour_ids"][$j] .= "," . $appointment["hour_ids_old"][$i];

				if ($appointment["member_ids"][$j] == "") {
					if ($tw[$i]["member_use"]) {
						$appointment["member_ids"][$j] = $appointment["member_codes_old"][0];
					}
				}
				//if city change auto save therapist name to "-- select --" 
				if ($city_change) {
					$appointment["therapist_names"][$j] .= ",-- select --";
				} else {
					$appointment["therapist_names"][$j] .= "," . $appointment["therapist_names_old"][$i];
				}
			} else {
				$appointment["room_ids"][$j] = $tw[$i]["room_id"];
				$appointment["room_names"][$j] = $this->getIdToText($tw[$i]["room_id"], "bl_room", "room_name", "room_id");
				$appointment["member_ids"][$j] = ($tw[$i]["member_use"]) ? $appointment["member_codes_old"][0] : "";
				$appointment["status"][$j] = 0;
				$appointment["qty_peoples"][$j] = 1;
				$appointment["hour_ids"][$j] = $appointment["hour_ids_old"][$i];

				//if city change auto save therapist name to "-- select --" 
				if ($city_change) {
					$appointment["therapist_names"][$j] = "-- select --";
				} else {
					$appointment["therapist_names"][$j] = $appointment["therapist_names_old"][$i];
				}
			}
		}

		// Convert array to string.
		$roomNames = implode("|", $appointment["room_names"]);
		$roomIds = implode("|", $appointment["room_ids"]);
		$status = implode("|", $appointment["status"]);
		$qtyPeoples = implode("|", $appointment["qty_peoples"]);
		$hourIds = implode("|", $appointment["hour_ids"]);
		$th_names = implode("|", $appointment["therapist_names"]);

		// sum meber_ids more than zero mean some work sheet set use member.
		if (array_sum($appointment["member_ids"]) > 0) {
			$member_ids = implode("|", $appointment["member_ids"]);
		} else {
			$member_ids = $rs[0]["member_code"];
		}

		if ($debug) {
			echo "<br>Debug SQL : nsert into c_bpds_link(tb_id,tb_name) values($id,\"a_bookinginfo\"))";
		} else {
			$bpdsid = $this->setResult("insert into c_bpds_link(tb_id,tb_name) values($id,\"a_bookinginfo\")");
		}

		$sql = "insert into a_appointment(bpds_id,book_id,branch_id,appt_date" .
		",appt_time_id,customer_name,room_ids,room_names" .
		",qty_peoples,th_names,hour_ids,accdt_name,t_times," .
		"t_names,t_places,bp_name" .
		",bp_person,b_set_cancel,status,member_code,mcategory)";

		$sql .= " values(\"$bpdsid\",\"$id\",\"$branch_id\",\"$date\"" .
		",\"$apptime\",\"" . $appointment["customer_name"] . "\",\"$roomIds\",\"$roomNames\"" .
		",\"$qtyPeoples\",\"$th_names\",\"$hourIds\",\"" . $appointment["accdt_name"] . "\",\"" . $appointment["t_times"] . "\"," .
		"\"" . $appointment["t_names"] . "\",\"" . $appointment["t_places"] . "\",\"" . $appointment["bp_name"] . "\"" .
		",\"" . $appointment["bp_person"] . "\",\"" . $appointment["b_set_cancel"] . "\",\"$status\",\"$member_ids\",\"" . $appointment["mcategory"] . "\")";

		if ($debug) {
			echo "<br>Debug SQL : $sql";
		} else {
			if ($bpdsid) {
				$id = $this->setResult($sql);
				return $id;
			} else {
				return false;
			}
		}
	}
	/*
	* function for update and insert table a_appointment  
	*  @modified - add this function on 14-01-2009 
	*/
	function addAppoiontment($bookId, $cs, $trf, $tw, $cancel) {
		//print_r($tw);
		$roomIdTmp = array ();
		$roomIds = "";
		$roomNames = "";
		$th = array ();
		$thNames = "";
		$hId = array ();
		$hourIds = "";
		$qtyP = array ();
		$qtyPeople = "";
		$cStat = array ();
		$cusstates = "";
		$accName = $this->getIdToText($cs["hotel"], "al_accomodations", "acc_name", "acc_id");
		$tTimes = $this->getIdToText($trf["pu_time"], "p_timer", "time_start", "time_id") . "," . $this->getIdToText($trf["tb_time"], "p_timer", "time_start", "time_id");
		$tNames = $this->getIdToText($trf["dr_pu"], "l_employee", "emp_nickname", "emp_id") . "," . $this->getIdToText($trf["dr_tb"], "l_employee", "emp_nickname", "emp_id");
		$tPlaces = $trf["p_pu"] . "," . $trf["p_tb"];
		$bCompany = $this->getIdToText($cs["bcompany"], "al_bookparty", "bp_name", "bp_id");
		$setcc = 0;
		$memIds = ""; //set defualt of member id is empty string.
		$memId = array ();
		$mCateName = ""; //set defualt of member category name is empty string.

		if ($cs["memid"]) {
			$memCateId = $this->getIdToText($cs["memid"], "m_membership", "category_id", "member_code");
			$mCateName = $this->getIdToText($memCateId, "mb_category", "category_name", "category_id");
		}
		if ($cancel == "checked") {
			$setcc = 1;
		}
		$ni = 0;
		for ($i = 0; $i < $cs["ttpp"]; $i++) {
			//For debug undefine index :member_use. By Ruck : 18-05-2009
			if (!isset ($tw[$i]["member_use"])) {
				$tw[$i]["member_use"] = "";
			}
			//For debug undefine index :member_use. By Ruck : 19-05-2009
			if (!isset ($th[$ni])) {
				$th[$ni] = "";
			}
			if (!isset ($hId[$ni])) {
				$hId[$ni] = "";
			}
			if (!isset ($cStat[$ni])) {
				$cStat[$ni] = "";
			}
			if (!isset ($qtyP[$ni])) {
				$qtyP[$ni] = 0;
			}

			if ($i == 0) {
				$roomIdTmp[$ni] = $tw[$i]["room"];
				$roomIds = $tw[$i]["room"];
				$roomNames = $this->getIdToText($tw[$i]["room"], "bl_room", "room_name", "room_id");
				$th[$ni] = $this->getIdToText($tw[$i][0]["name"], "l_employee", "emp_nickname", "emp_id");
				$hId[$ni] = $tw[$i]["tthour"];

				for ($k = 1; $k < count($tw[$i]); $k++) {
					// debugging all undified offset Ruck/16-05-2009
					if (isset ($tw[$i][$k]["name"])) {
						if ($tw[$i][$k]["name"] != "") {
							$th[$ni] .= "," . $this->getIdToText($tw[$i][$k]["name"], "l_employee", "emp_nickname", "emp_id");
							$hId[$ni] .= "," . $tw[$i]["tthour"];
						}
					}

				}
				$cStat[$ni] = $tw[$i]["cusstate"];
				$qtyP[$ni]++;
				$cusstates = $tw[$i]["cusstate"];
				$memIds = ($tw[$i]["member_use"] == "checked") ? $cs["memid"] : "";
				$ni++;
			} else {
				$chkStat = false;
				for ($j = 0; $j < count($roomIdTmp); $j++) {
					if ($tw[$i]["room"] == $roomIdTmp[$j]) {
						$chkStat = true;
						break;
					}
				}

				if ($chkStat) {

					for ($k = 0; $k < count($tw[$i]); $k++) {
						// debugging all undified offset Ruck/16-05-2009
						if (isset ($tw[$i][$k]["name"])) {
							if ($tw[$i][$k]["name"] != "") {
								$th[$j] .= "," . $this->getIdToText($tw[$i][$k]["name"], "l_employee", "emp_nickname", "emp_id");
								$hId[$ni] .= "," . $tw[$i]["tthour"];
							}
						}
					}
					$memIds .= "," . (($tw[$i]["member_use"] == "checked") ? $cs["memid"] : "");
					$cusstates .= "," . $tw[$i]["cusstate"];
					$cStat[$j] = "," . $tw[$i]["cusstate"];
					$qtyP[$j]++;
				} else {
					//echo "<br>".$tw[$i]["room"];
					$roomIdTmp[$ni] = $tw[$i]["room"];
					$roomIds .= "|" . $tw[$i]["room"];
					$roomNames .= "|" . $this->getIdToText($tw[$i]["room"], "bl_room", "room_name", "room_id");
					$th[$ni] .= "|" . $this->getIdToText($tw[$i][0]["name"], "l_employee", "emp_nickname", "emp_id");
					$hId[$ni] .= "|" . $tw[$i]["tthour"];
					//echo "<br>0 -- ".$tw[$i][0]["name"];
					for ($k = 1; $k < count($tw[$i]); $k++) {
						// debugging all undified offset Ruck/16-05-2009
						if (isset ($tw[$i][$k]["name"])) {
							if ($tw[$i][$k]["name"] != "") {
								$th[$ni] .= "," . $this->getIdToText($tw[$i][$k]["name"], "l_employee", "emp_nickname", "emp_id");
								$hId[$ni] .= "," . $tw[$i]["tthour"];
							}
						}
					}
					$cStat[$ni] = "|" . $tw[$i]["cusstate"];
					$qtyP[$ni]++;
					$cusstates .= "|" . $tw[$i]["cusstate"];
					$memIds .= "|" . (($tw[$i]["member_use"] == "checked") ? $cs["memid"] : "");
					$ni++;
				}
			}
		}
		$notSetMU = true;
		$memIdArray = array ();
		$memId = explode("|", $memIds);
		$cStatArray = array ();
		$cStat = explode("|", $cusstates);
		for ($i = 0; $i < count($cStat); $i++) {
			$cStatTmp = explode(",", $cStat[$i]);
			$memIdTmp = explode(",", $memId[$i]);
			$cStatArray[$i] = 0;
			$memIdArray[$i] = "";
			$chkFinish = 0;

			for ($j = 0; $j < count($memIdTmp); $j++) {
				if ($memIdTmp[$j] != "") {
					$notSetMU = false;
					$memIdArray[$i] = $memIdTmp[$j];
				}
			}
			for ($j = 0; $j < count($cStatTmp); $j++) {

				if ($cStatTmp[$j] == "c_set_inroom") {
					$cStatArray[$i] = 2;
					break;
				} else
					if ($cStatTmp[$j] == "c_set_atspa" && $cStatArray[$i] != 2) {
						$cStatArray[$i] = 1;
					} else {
						//echo "<br>Not Set".$cStatArray[$i];
						$cStatArray[$i] = 0;
					}
				if ($cStatTmp[$j] == "c_set_finish") {
					$chkFinish++;
				}
				if ($chkFinish == count($cStatTmp)) {
					$cStatArray[$i] = 3;
				}
			}
		}
		for ($i = 0; $i < count($roomIdTmp); $i++) {
			$thNames .= $th[$i];
			$hourIds .= $hId[$i];
			if ($i == 0) {
				$qtyPeople = $qtyP[$i];
				$cusstates = $cStatArray[$i];
				$memIds = $memIdArray[$i];
			} else {
				$qtyPeople .= "|" . $qtyP[$i];
				$cusstates .= "|" . $cStatArray[$i];
				$memIds .= "|" . $memIdArray[$i];
			}
		}
		if ($notSetMU) {
			if ($cs["memid"]) {
				$memIds = $cs["memid"];
			} else {
				$memIds = "";
			}

		}
		if (!$trf["trf"] == "checked") {
			$tTimes = " ";
			$tNames = " ";
			$tPlaces = " ";
		}

		$bpdsid = $this->getIdToText($bookId, "c_bpds_link", "bpds_id", "tb_id", "tb_name=\"a_bookinginfo\"", false);
		$sql = "insert into a_appointment(appt_id,bpds_id,book_id,branch_id,appt_date," .
		"appt_time_id,customer_name,room_ids,room_names,qty_peoples,th_names,hour_ids,accdt_name,t_times," .
		"t_names,t_places,bp_name,bp_person,b_set_cancel,status,member_code,mcategory)";
		$sql .= " values(\"\",$bpdsid,\"$bookId\",\"" . $cs["branch"] . "\",\"" . $cs["hidden_apptdate"] . "\"" .
		",\"" . $cs["appttime"] . "\",\"" . htmlspecialchars($cs["name"]) . "\",\"$roomIds\",\"$roomNames\",\"$qtyPeople\",\"$thNames\",\"$hourIds\",\"$accName\",\"$tTimes\"," .
		"\"" . htmlspecialchars($tNames) . "\",\"" . htmlspecialchars($tPlaces) . "\",\"" . $bCompany . "\",\"" . htmlspecialchars($cs["bpname"]) . "\",\"$setcc\",\"$cusstates\",\"$memIds\",\"" . $mCateName . "\")";
		//echo "<br>".$sql;				
		$id = $this->setResult($sql);
		return $id;
	}

	function editAppoiontment($bookId, $cs, $trf, $tw, $cancel) {

		$roomIdTmp = array ();
		$roomIds = "";
		$roomNames = "";
		$th = array ();
		$thNames = "";
		$hId = array ();
		$hourIds = "";
		$qtyP = array ();
		$qtyP[0] = 0; //For debug undefine offset : 0. By Ruck : 18-05-2009
		$qtyPeople = "";
		$cStat = array ();
		$cusstates = "";
		$accName = $this->getIdToText($cs["hotel"], "al_accomodations", "acc_name", "acc_id");
		$tTimes = $this->getIdToText($trf["pu_time"], "p_timer", "time_start", "time_id") . "," . $this->getIdToText($trf["tb_time"], "p_timer", "time_start", "time_id");
		$tNames = $this->getIdToText($trf["dr_pu"], "l_employee", "emp_nickname", "emp_id") . "," . $this->getIdToText($trf["dr_tb"], "l_employee", "emp_nickname", "emp_id");
		$tPlaces = $trf["p_pu"] . "," . $trf["p_tb"];
		$bCompany = $this->getIdToText($cs["bcompany"], "al_bookparty", "bp_name", "bp_id");
		$setcc = 0;
		$memIds = ""; //set defualt of member id is empty string.
		$memId = array ();
		$mCateName = ""; //set defualt of member category name is empty string.

		if ($cs["memid"]) {
			$memCateId = $this->getIdToText($cs["memid"], "m_membership", "category_id", "member_code");
			$mCateName = $this->getIdToText($memCateId, "mb_category", "category_name", "category_id");
		}
		if ($cancel == "checked") {
			$setcc = 1;
		}
		$ni = 0;
		for ($i = 0; $i < $cs["ttpp"]; $i++) {
			//For debug undefine index :member_use. By Ruck : 18-05-2009
			if (!isset ($tw[$i]["member_use"])) {
				$tw[$i]["member_use"] = "";
			}
			//For debug undefine index :member_use. By Ruck : 19-05-2009
			if (!isset ($th[$ni])) {
				$th[$ni] = "";
			}
			if (!isset ($hId[$ni])) {
				$hId[$ni] = "";
			}
			if (!isset ($cStat[$ni])) {
				$cStat[$ni] = "";
			}
			if (!isset ($qtyP[$ni])) {
				$qtyP[$ni] = 0;
			}

			if ($i == 0) {
				$roomIdTmp[$ni] = $tw[$i]["room"];
				$roomIds = $tw[$i]["room"];
				$roomNames = $this->getIdToText($tw[$i]["room"], "bl_room", "room_name", "room_id");
				$th[$ni] = $this->getIdToText($tw[$i][0]["name"], "l_employee", "emp_nickname", "emp_id");
				$hId[$ni] = $tw[$i]["tthour"];

				for ($k = 1; $k < count($tw[$i]); $k++) {
					// debugging all undified offset Ruck/16-05-2009
					if (isset ($tw[$i][$k]["name"])) {
						if ($tw[$i][$k]["name"] != "") {
							$th[$ni] .= "," . $this->getIdToText($tw[$i][$k]["name"], "l_employee", "emp_nickname", "emp_id");
							$hId[$ni] .= "," . $tw[$i]["tthour"];
						}
					}
				}

				$cStat[$ni] = $tw[$i]["cusstate"];
				$qtyP[$ni]++;
				$cusstates = $tw[$i]["cusstate"];
				$memIds = ($tw[$i]["member_use"] == "checked") ? $cs["memid"] : "";
				$ni++;
			} else {
				$chkStat = false;
				for ($j = 0; $j < count($roomIdTmp); $j++) {
					if ($tw[$i]["room"] == $roomIdTmp[$j]) {
						$chkStat = true;
						break;
					}
				}

				if ($chkStat) {
					for ($k = 0; $k < count($tw[$i]); $k++) {
						// debugging all undified offset Ruck/16-05-2009
						if (isset ($tw[$i][$k]["name"])) {
							if ($tw[$i][$k]["name"] != "") {
								$th[$j] .= "," . $this->getIdToText($tw[$i][$k]["name"], "l_employee", "emp_nickname", "emp_id");
								$hId[$j] .= "," . $tw[$i]["tthour"];
							}
						}
					}
					$memIds .= "," . (($tw[$i]["member_use"] == "checked") ? $cs["memid"] : "");
					$cusstates .= "," . $tw[$i]["cusstate"];
					$cStat[$j] = "," . $tw[$i]["cusstate"];
					$qtyP[$j]++;
				} else {
					$roomIdTmp[$ni] = $tw[$i]["room"];
					$roomIds .= "|" . $tw[$i]["room"];
					$roomNames .= "|" . $this->getIdToText($tw[$i]["room"], "bl_room", "room_name", "room_id");
					$th[$ni] .= "|" . $this->getIdToText($tw[$i][0]["name"], "l_employee", "emp_nickname", "emp_id");
					$hId[$ni] .= "|" . $tw[$i]["tthour"];

					for ($k = 1; $k < count($tw[$i]); $k++) {
						// debugging all undified offset Ruck/16-05-2009
						if (isset ($tw[$i][$k]["name"])) {
							if ($tw[$i][$k]["name"] != "") {
								$th[$ni] .= "," . $this->getIdToText($tw[$i][$k]["name"], "l_employee", "emp_nickname", "emp_id");
								$hId[$ni] .= "," . $tw[$i]["tthour"];
							}
						}
					}

					$cStat[$ni] = "|" . $tw[$i]["cusstate"];
					$qtyP[$ni]++;
					$cusstates .= "|" . $tw[$i]["cusstate"];
					$memIds .= "|" . (($tw[$i]["member_use"] == "checked") ? $cs["memid"] : "");
					$ni++;
				}
			}
		}

		$notSetMU = true;
		$memIdArray = array ();
		$memId = explode("|", $memIds);
		$cStatArray = array ();
		$cStat = explode("|", $cusstates);
		for ($i = 0; $i < count($cStat); $i++) {
			$cStatTmp = explode(",", $cStat[$i]);
			$memIdTmp = explode(",", $memId[$i]);
			$cStatArray[$i] = 0;
			$memIdArray[$i] = "";
			$chkFinish = 0;

			for ($j = 0; $j < count($memIdTmp); $j++) {
				if ($memIdTmp[$j] != "") {
					$notSetMU = false;
					$memIdArray[$i] = $memIdTmp[$j];
				}
			}
			for ($j = 0; $j < count($cStatTmp); $j++) {
				if ($cStatTmp[$j] == "c_set_inroom") {
					$cStatArray[$i] = 2;
					break;
				} else
					if ($cStatTmp[$j] == "c_set_atspa" && $cStatArray[$i] != 2) {
						$cStatArray[$i] = 1;
					} else {
						$cStatArray[$i] = 0;
					}
				if ($cStatTmp[$j] == "c_set_finish") {
					$chkFinish++;
				}
				if ($chkFinish == count($cStatTmp)) {
					$cStatArray[$i] = 3;
				}
			}
		}
		for ($i = 0; $i < count($roomIdTmp); $i++) {
			$thNames .= $th[$i];
			$hourIds .= $hId[$i];
			if ($i == 0) {
				$qtyPeople = $qtyP[$i];
				$cusstates = $cStatArray[$i];
				$memIds = $memIdArray[$i];
			} else {
				$qtyPeople .= "|" . $qtyP[$i];
				$cusstates .= "|" . $cStatArray[$i];
				$memIds .= "|" . $memIdArray[$i];
			}
		}
		if ($notSetMU) {
			if ($cs["memid"]) {
				$memIds = $cs["memid"];
			} else {
				$memIds = "";
			}

		}
		if (!$trf["trf"] == "checked") {
			$tTimes = " ";
			$tNames = " ";
			$tPlaces = " ";
		}

		$sql = "UPDATE a_appointment SET  branch_id=\"" . $cs["branch"] . "\",appt_date=\"" . $cs["hidden_apptdate"] . "\"," .
		"appt_time_id=\"" . $cs["appttime"] . "\",customer_name=\"" . htmlspecialchars($cs["name"]) . "\"," .
		"member_code=\"$memIds\",mcategory=\"$mCateName\",room_ids=\"$roomIds\"," .
		"room_names=\"$roomNames\",qty_peoples=\"" . $qtyPeople . "\",th_names=\"$thNames\",hour_ids=\"$hourIds\"," .
		"accdt_name=\"$accName\",t_times=\"$tTimes\",t_names=\"" . htmlspecialchars($tNames) . "\",t_places=\"" . htmlspecialchars($tPlaces) . "\",bp_name=\"$bCompany\"," .
		"bp_person=\"" . $cs["bpname"] . "\",b_set_cancel=\"$setcc\",status=\"$cusstates\" WHERE book_id=\"$bookId\"";

		//echo "<br>".$sql;				
		$id = $this->setResult($sql);
		return $id;
	}
	/*
	 * End function for update and insert table a_appointment
	 */

	/*
	 * function for get customer history 
	 * used in search cs history by phone
	 * @modified - add this function on 18-02-2009
	 * @modified - 1-Oct-2009 add column cs_email,cs_birthday,sex_id,nationality_id,resident and visitor in query
	 */
	function getCSHistory($csphone = false, $limit = 0, $records_per_page = false, $debug = false) {
		$sql = "select a_bookinginfo.book_id,a_bookinginfo.b_qty_people, a_bookinginfo.b_appt_date, " .
		"bl_branchinfo.branch_name,a_bookinginfo.c_set_cms as set_cms,a_bookinginfo.a_member_code as member_code, " .
		"\"d_indivi_info\" as tbname,d_indivi_info.indivi_id as indivi_id, " .
		"d_indivi_info.cs_name,d_indivi_info.cs_phone,d_indivi_info.cs_email, " .
		"d_indivi_info.cs_age,d_indivi_info.cs_birthday,d_indivi_info.sex_id, " .
		"d_indivi_info.nationality_id,d_indivi_info.resident, d_indivi_info.visitor " .
		"from a_bookinginfo,bl_branchinfo,d_indivi_info ";

		$sql .= "where a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
		"and a_bookinginfo.book_id=d_indivi_info.book_id  " .
		"and d_indivi_info.cs_name!=\"\" ";
		if ($csphone) {
			$sql .= "and d_indivi_info.cs_phone like \"%$csphone%\" ";
		}

		$sql .= "order by book_id,cs_name ";
		if ($records_per_page) {
			$sql .= "limit $limit,$records_per_page";
		}

		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		return $this->getResult($sql);
	}

	/*
	 * function for get customer history for treatment information
	 * used in search cs history by phone in treatment information tab
	 * @modified - add this function on 18-02-2009
	 * @modified - 2-Oct-2009 add column cs_email,cs_birthday,sex_id,nationality_id,resident and visitor in query
	 */
	function getTWHistory($csphone = false, $limit = 0, $records_per_page = false, $debug = false) {
		$sql = "select a_bookinginfo.book_id,a_bookinginfo.b_qty_people, a_bookinginfo.b_appt_date, " .
		"bl_branchinfo.branch_name,a_bookinginfo.a_member_code as member_code, " .
		"\"d_indivi_info\" as tbname,d_indivi_info.indivi_id as indivi_id, " .
		"d_indivi_info.cs_name,d_indivi_info.cs_phone,d_indivi_info.cs_email, " .
		"d_indivi_info.cs_age,d_indivi_info.cs_birthday,d_indivi_info.sex_id, " .
		"d_indivi_info.nationality_id,d_indivi_info.resident, d_indivi_info.visitor " .
		"from a_bookinginfo,bl_branchinfo,d_indivi_info ";
		$sql .= "where a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
		"and a_bookinginfo.book_id=d_indivi_info.book_id " .
		"and d_indivi_info.cs_name!=\"\" ";
		if ($csphone) {
			$sql .= "and d_indivi_info.cs_phone like \"%$csphone%\" ";
		}

		$sql .= "order by book_id,cs_name ";
		if ($records_per_page) {
			$sql .= "limit $limit,$records_per_page";
		}

		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		//echo $sql."<br>";
		return $this->getResult($sql);
	}

	//######################################## Function for Room Maintunance Page ####################################
	/*
	 * update information into table r_maintenance
	 * @param $rmid - rm_id that'll be update
	 * @param $cs - all detail information
	 * @param $cc - set cancel confirm
	 * @modified - add this function on 07 Apr 2009 by natt
	 */
	function editRM($rmid = false, $cs = false, $cc = false, $debug = false) {
		if (!$cs["room"]) {
			$this->setErrorMsg("Please select room before insert to r_maintenance!!");
			return false;
		}
		if (!$cs["branch"]) {
			$this->setErrorMsg("Please check branch id before insert to r_maintenance!!");
			return false;
		}

		$roomid = $cs["room"]; // id
		$roomname = $this->getIdToText($roomid, "bl_room", "room_name", "room_id"); //text
		$branchid = $cs["branch"]; // id
		$date = $cs["hidden_apptdate"]; // date
		$appttime = $cs["appttime"]; // id
		$tthour = $cs["tthour"]; // id
		$reasons = htmlspecialchars($cs["reasons"]); //text

		$cancel = ($cc["cc"] == "checked") ? 1 : 0; // binary
		$cancelcomment = ($cc["cc"] == "checked") ? htmlspecialchars($cc["comment"]) : ""; //text
		$canceldate = ($cc["cc"] == "checked") ? $cc["hidden_date"] : 0; // date

		$ip = $_SERVER["REMOTE_ADDR"]; // text
		$userid = $this->getUserIdLogin(); // id
		$sql = "update r_maintenance set " .
		"room_id=$roomid," .
		"branch_id=$branchid," .
		"appt_date=$date," .
		"appt_time=$appttime," .
		"hour_id=$tthour," .
		"reasons=\"$reasons\"," .
		"set_cancel=$cancel," .
		"cancel_date=$canceldate," .
		"cancel_comment=\"$cancelcomment\"," .
		"l_lu_user=$userid," .
		"l_lu_date=now()," .
		"l_lu_ip=\"$ip\" ";
		$sql .= "where rm_id=$rmid";
		$rows = $this->setResult($sql);

		//echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
		}

		$apptid = $this->getIdToText($rmid, "r_maintenance", "appt_id", "rm_id");

		$sql = "update a_appointment set " .
		"room_ids=$roomid," .
		"room_names=\"$roomname\"," .
		"branch_id=$branchid," .
		"appt_date=$date," .
		"appt_time_id=$appttime," .
		"hour_ids=$tthour," .
		"customer_name=\"$reasons\"," .
		"b_set_cancel=$cancel," .
		"th_names=\"Maintenance Room\"," .
		"status=4 ";
		$sql .= "where appt_id=$apptid";
		$apptid = $this->setResult($sql);

		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		if ($rows && $apptid) {
			return $rmid;
		} else {
			if (!$apptid) {
				$this->setErrorMsg("Please check on a_appointment information!!");
			}
			return false;
		}
	}

	/*
	 * add information into table r_maintenance
	 * @param $cs - all detail information
	 * @param $cc - set cancel confirm
	 * @modified - add this function on 07 Apr 2009 by natt
	 */
	function addRM($cs = false, $cc = false, $debug = false) {
		if (!$cs["room"]) {
			$this->setErrorMsg("Please select room before insert to r_maintenance!!");
			return false;
		}
		if (!$cs["branch"]) {
			$this->setErrorMsg("Please check branch id before insert to r_maintenance!!");
			return false;
		}

		$roomid = $cs["room"]; // id
		$roomname = $this->getIdToText($roomid, "bl_room", "room_name", "room_id"); //text
		$branchid = $cs["branch"]; // id
		$date = $cs["hidden_apptdate"]; // date
		$appttime = $cs["appttime"]; // id
		$tthour = $cs["tthour"]; // id
		$reasons = htmlspecialchars($cs["reasons"]); //text

		$cancel = ($cc["cc"] == "checked") ? 1 : 0; // binary
		$cancelcomment = ($cc["cc"] == "checked") ? htmlspecialchars($cc["comment"]) : ""; //text
		$canceldate = ($cc["cc"] == "checked") ? $cc["hidden_date"] : 0; // date

		$ip = $_SERVER["REMOTE_ADDR"]; // text
		$userid = $this->getUserIdLogin(); // id

		$sql = "insert into r_maintenance(room_id,branch_id,appt_date,appt_time,hour_id,reasons," .
		"set_cancel,cancel_date,cancel_comment,c_lu_user,c_lu_date,c_lu_ip) ";
		$sql .= "values($roomid,$branchid,$date,$appttime,$tthour,\"$reasons\"," .
		"$cancel,$canceldate,\"$cancelcomment\",$userid,now(),\"$ip\")";
		$rmid = $this->setResult($sql);

		//echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}

		$sql = "insert into a_appointment(book_id,room_ids,room_names,branch_id,appt_date,appt_time_id,hour_ids,customer_name," .
		"th_names,b_set_cancel,status) ";
		$sql .= "values($rmid,$roomid,\"$roomname\",$branchid,$date,$appttime,$tthour,\"$reasons\"," .
		"\"Maintenance Room\",$cancel,4)";
		$apptid = $this->setResult($sql);

		$chksql = "update r_maintenance set appt_id=$apptid where rm_id=$rmid";
		$chkid = $this->setResult($chksql);

		if ($rmid && $apptid) {
			return $rmid;
		} else {
			if (!$apptid) {
				$this->setErrorMsg("Please check on a_appointment information!!");
			}
			return false;
		}
	}

	/*
	 * function for generate Maintenance Room that was cancel in appointment page
	 * @modified - add this function on 08 Apr 2009 by natt
	 */
	function getCancelRM($date = false, $branch_id = false, $isEdit = false, $debug = false) {
		$sql = "select book_id,bpds_id from a_appointment where appt_date=" . $date . " and branch_id=$branch_id and b_set_cancel=1 and bpds_id=0 order by book_id";
		$rs = $this->getResult($sql);
		$ccbook = "";
		for ($i = 0; $i < $rs["rows"]; $i++) {
			if ($ccbook != '') {
				$ccbook .= ", ";
			}
			if ($isEdit) {
				$ccbook .= "<a href='javascript:;;' onClick=\"newwindow('manage_mroom.php?chkpage=1&rmid=" . $rs[$i]["book_id"] . "','manageRM" . $rs[$i]["book_id"] . "')\" class=\"menu\">ID : " . $rs[$i]['book_id'] . "</a>";
			} else {
				$ccbook .= "<b class=\"menu\">ID : " . $rs[$i]['book_id'] . "</b>";
			}

		}
		return $ccbook;
	}

	/*
	 * function for get current customer information 
	 *  @modified - add this function on 12-05-2009
	 */
	function getcurrentcust($branchid = false, $where = false, $limit = 0, $records_per_page = false, $order = false, $debug = false) {
		/*
		$sql1 = "select a_bookinginfo.book_id,a_bookinginfo.b_customer_name as cs_name, a_bookinginfo.b_qty_people, a_bookinginfo.b_appt_date, " .
				"bl_branchinfo.branch_name,\"a_bookinginfo\" as tbname, \"0\" as indivi_id,a_bookinginfo.c_bp_phone as cs_phone  " .
				"from a_bookinginfo,bl_branchinfo ";
		$sql1 .= "where a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		if($csphone){$sql1 .= "and a_bookinginfo.c_bp_phone like \"%$csphone%\" " ;}
		*/
		$sql = "select a_bookinginfo.book_id,d_indivi_info.cs_name,d_indivi_info.cs_age, a_bookinginfo.b_appt_date, " .
		"bl_branchinfo.branch_name,\"d_indivi_info\" as tbname, d_indivi_info.indivi_id as indivi_id, d_indivi_info.cs_phone," .
		"d_indivi_info.b_set_finish as finish_status," .
		"d_indivi_info.b_set_inroom as inroom_status," .
		"d_indivi_info.b_set_atspa as atspa_status," .
		"al_bookparty.bp_name,a_bookinginfo.c_bp_person as bp_person,a_bookinginfo.c_bp_phone as bp_phone,p_timer.time_start as appt_time  " .
		"from a_bookinginfo,bl_branchinfo,d_indivi_info,al_bookparty,p_timer ";
		$sql .= "where a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
		"and a_bookinginfo.c_bp_id=al_bookparty.bp_id  " .
		"and a_bookinginfo.book_id=d_indivi_info.book_id  " .
		"and a_bookinginfo.b_appt_time_id=p_timer.time_id  ";
		$sql .= "and a_bookinginfo.b_appt_date>=CURDATE()  ";
		if ($where) {
			$sql .= "and ( d_indivi_info.cs_phone like \"%" . htmlspecialchars(strtolower($where)) . "%\" ";
			$sql .= "or lower(d_indivi_info.cs_name) like \"%" . htmlspecialchars(strtolower($where)) . "%\" ";
			$sql .= "or lower(al_bookparty.bp_name) like \"%" . htmlspecialchars(strtolower($where)) . "%\" ";
			$sql .= "or a_bookinginfo.c_bp_phone like \"%" . htmlspecialchars(strtolower($where)) . "%\") ";
		}
		if ($branchid > 1) {
			$sql .= "and a_bookinginfo.b_branch_id=$branchid ";
		}
		if ($order) {
			$order = "$order,";
		}
		//$sql = "($sql1) UNION ($sql2) order by book_id,cs_name ";
		$sql .= " order by $order bl_branchinfo.branch_id, a_bookinginfo.b_appt_date,book_id,cs_name ";
		if ($records_per_page) {
			$sql .= "limit $limit,$records_per_page";
		}

		//echo $sql."<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		return $this->getResult($sql);
	}

}
?>