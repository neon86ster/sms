<?php
/*
 * File name : cms.inc.php
 * Description : Class file which is main controller for cms system
 * Author : natt
 * Create date : 22-Oct-2008
 * Modified : natt@chiangmaioasis.com
 */   
require_once("cms.inc.php");
require_once("date.inc.php");
class report extends cms {
	
	function report(){}
	
	function getBegin($date=false,$sdateformat=false){
		
		list($year,$month,$day) = explode("-",date("Y-m-d"));
	 	list($hours,$minutes,$seconds) = explode(":",date("H:i:s"));
	 		
	 		if(!isset($_GET["gmt"])){$_GET["gmt"]="";}
			if(!isset($_SESSION["__gmt"])){$_SESSION["__gmt"]=$_GET["gmt"];}
	 		
	 		$gmt = isset($_SESSION["__gmt"])?$_SESSION["__gmt"]:$_GET["gmt"];
	 		list($hr,$min) = explode(".",number_format($gmt,2,".",","));
	 		
	 		$hours += $hr-$_SESSION["global_timezone"];
	 		$minutes += $min;
	 		
	 		$unix_time = mktime((int)$hours, (int)$minutes, (int)$seconds, (int)$month, (int)$day, (int)$year);
		$dt = date($sdateformat, $unix_time);
		
		 $d = date("d", $unix_time);
		 $m = date("m", $unix_time);
		 $y = date("Y", $unix_time);
	
		if($date==1){
			return date($sdateformat,mktime(0,0,0,3,25,2009));	//All
			
		} else if ($date==2){
			return $this->getParameter("begin",date($sdateformat,mktime(0,0,0,$m,$d,$y))); //Custom
		} else if ($date==3){	
			if($m<=3&&$m>=1){
				return date($sdateformat,mktime(0,0,0,10,1,$y-1));
			} else if ($m<=6&&$m>=4){
				return date($sdateformat,mktime(0,0,0,1,1,$y));
			} else if ($m<=9&&$m>=7){
				return date($sdateformat,mktime(0,0,0,4,1,$y));
			} else if ($m<=12&&$m>=10){
				return date($sdateformat,mktime(0,0,0,7,1,$y));				//Last Fiscal Quarter
			} 
		} else if  ($date==4){
			if($m<=3&&$m>=1){
				return date($sdateformat,mktime(0,0,0,10,1,$y-1));
			} else if ($m<=6&&$m>=4){
				return date($sdateformat,mktime(0,0,0,1,1,$y));
			} else if ($m<=9&&$m>=7){
				return date($sdateformat,mktime(0,0,0,4,1,$y));
			} else if ($m<=12&&$m>=10){
				return date($sdateformat,mktime(0,0,0,7,1,$y));
			}																		//Last Fiscal Quarter to date
		} else if ($date==5){	
			return date($sdateformat,mktime(0,0,0,1,1,$y-1));				//Last Fiscal Year
		} else if ($date==6){	
			return date($sdateformat,mktime(0,0,0,1,1,$y-1));				//Last Fiscal Year to date
		} else if ($date==7){	
			return date($sdateformat,mktime(0,0,0,date("m")-1,1,date("Y")));		//	//Last Month
		} else if ($date==8){	
			return date($sdateformat,mktime(0,0,0,$m-1,1,$y));		//Last Month to date
		} else if ($date==9){	
			return date($sdateformat,mktime(0,0,0,$m,$d-(7+date("N")),$y));	//Last Week
		} else if ($date==10){ 
			return date($sdateformat,mktime(0,0,0,$m,$d-(7+date("N")),$y));	//Last Week to date
		} else if ($date==11){ 
			if($m<=3&&$m>=1){
				return date($sdateformat,mktime(0,0,0,1,1,$y));
			} else if ($m<=6&&$m>=4){
				return date($sdateformat,mktime(0,0,0,4,1,$y));
			} else if ($m<=9&&$m>=7){
				return date($sdateformat,mktime(0,0,0,7,1,$y));
			} else if ($m<=12&&$m>=10){
				return date($sdateformat,mktime(0,0,0,10,1,$y));
			}																		//This Fiscal Quarter
		} else if ($date==12){ 
			if($m<=3&&$m>=1){
				return date($sdateformat,mktime(0,0,0,1,1,$y));
			} else if ($m<=6&&$m>=4){
				return date($sdateformat,mktime(0,0,0,4,1,$y));
			} else if ($m<=9&&$m>=7){
				return date($sdateformat,mktime(0,0,0,7,1,$y));	
			} else if ($m<=12&&$m>=10){
				return date($sdateformat,mktime(0,0,0,10,1,$y));
			}																		//This Fiscal Quarter to date
		} else if ($date==13){ 
			return date($sdateformat,mktime(0,0,0,1,1,$y));					//This Fiscal Year
		} else if ($date==14){ 
			return date($sdateformat,mktime(0,0,0,1,1,$y));					//This Fiscal Year to date
		} else if ($date==15){ 
			return date($sdateformat,mktime(0,0,0,$m,1,$y));			//This Month
		} else if ($date==16){ 
			return date($sdateformat,mktime(0,0,0,$m,1,$y));			//This Month to date
		} else if ($date==17){ 
			return date($sdateformat,mktime(0,0,0,$m,$d,$y));	//Today
		} else if ($date==18){ 
			return date($sdateformat,mktime(0,0,0,$m,$d-1,$y));//Yesterday
		} else { 
			return $_POST["begin"]; 
		}
		
	}
	
	function getEnd($date=false,$sdateformat=false){
		
		list($year,$month,$day) = explode("-",date("Y-m-d"));
	 	list($hours,$minutes,$seconds) = explode(":",date("H:i:s"));
			
			if(!isset($_GET["gmt"])){$_GET["gmt"]="";}
			if(!isset($_SESSION["__gmt"])){$_SESSION["__gmt"]=$_GET["gmt"];}
	 		
	 		$gmt = isset($_SESSION["__gmt"])?$_SESSION["__gmt"]:$_GET["gmt"];
	 		list($hr,$min) = explode(".",number_format($gmt,2,".",","));
	 		
	 		$hours += $hr-$_SESSION["global_timezone"];
	 		$minutes += $min;
	 		
	 		$unix_time = mktime((int)$hours, (int)$minutes, (int)$seconds, (int)$month, (int)$day, (int)$year);
		$dt = date($sdateformat, $unix_time);
		
		 $d = date("d", $unix_time);
		 $m = date("m", $unix_time);
		 $y = date("Y", $unix_time);
	
		if ($date==1){	
			return date($sdateformat,mktime(0,0,0,$m,$d,$y));	//All
		} else if ($date==2){	
			return $this->getParameter("end",date($sdateformat,mktime(0,0,0,$m,$d,$y)));	//Custom
		} else if ($date==3){	
			if($m<=3&&$m>=1){
				return date($sdateformat,mktime(0,0,0,1,0,$y));
			} else if ($m<=6&&$m>=4){
				return date($sdateformat,mktime(0,0,0,4,0,$y));
			} else if ($m<=9&&$m>=7){
				return date($sdateformat,mktime(0,0,0,7,0,$y));	
			} else if ($m<=12&&$m>=10){
				return date($sdateformat,mktime(0,0,0,10,0,$y));
			}																		//Last Fiscal Quarter
		} else if ($date==4){	
			return date($sdateformat,mktime(0,0,0,$m,$d,$y));	//Last Fiscal Quarter to date
		} else if ($date==5){	
			return date($sdateformat,mktime(0,0,0,1,0,$y));					//Last Fiscal Year
		} else if ($date==6){	
			return date($sdateformat,mktime(0,0,0,$m,$d,$y));	//Last Fiscal Year to date
		} else if ($date==7){	
			return date($sdateformat,mktime(0,0,0,$m,0,$y));			//Last Month
		} else if ($date==8){	
			return date($sdateformat,mktime(0,0,0,$m,$d,$y));	//Last Month to date
		} else if ($date==9){	
			return date($sdateformat,mktime(0,0,0,$m,$d-(date("N")+1),$y));	//Last Week
		} else if ($date==10){ 
			return date($sdateformat,mktime(0,0,0,$m,$d,$y));	//Last Week to date
		} else if ($date==11){ 
			if($m<=3&&$m>=1){
				return date($sdateformat,mktime(0,0,0,4,0,$y));
			} else if ($m<=6&&$m>=4){
				return date($sdateformat,mktime(0,0,0,7,0,$y));
			} else if ($m<=9&&$m>=7){
				return date($sdateformat,mktime(0,0,0,10,0,$y));	
			} else if ($m<=12&&$m>=10){
				return date($sdateformat,mktime(0,0,0,1,0,$y+1));
			}																		//This Fiscal Quarter
		} else if ($date==12){ 
			return date($sdateformat,mktime(0,0,0,$m,$d,$y));	//This Fiscal Quarter to date
		} else if ($date==13){ 
			return date($sdateformat,mktime(0,0,0,12,31,$y));				//This Fiscal Year
		} else if ($date==14){ 
			return date($sdateformat,mktime(0,0,0,$m,$d,$y));	//This Fiscal Year to date
		} else if ($date==15){ 
			return date($sdateformat,mktime(0,0,0,$m+1,0,$y));		//This Month
		} else if ($date==16){ 
			return date($sdateformat,mktime(0,0,0,$m,$d,$y));	//This Month to date
		} else if ($date==17){ 
			return date($sdateformat,mktime(0,0,0,$m,$d,$y));	//Today
		} else if ($date==18){ 
			return date($sdateformat,mktime(0,0,0,$m,$d-1,$y));//Yesterday
		} else { 
			return $_POST["begin"];
		}
		
	}
	
	/*
	 * auto generate select box form database for all report page
	 * @param $sname - selectbox name
	 * @param $tbname - table name
	 * @param $fieldname - field name
	 * @param $fieldid - field id
	 * @param $selected - selected id
	 * @param $chkautosubmit - check form auto submit
	 * @param $order - sql query language "order by $order"
	 * @param $wherename,$wherechk,$andparam,$orparam - sql query language "where $wherename=$wherechk and $andparam or $orparam"
	 * @param $disabled - disable selectbox
	 * Modify : 25-05-2009
	 * @param $javascript - add javascript in select input 
	 * @modified - add this function on 30 Jan 2009
	 */
	function makeListbox($sname=false,$tbname=false,$fieldname=false,$fieldid=false,
		$selected=false,$chkautosubmit=false,$order=false,$wherename=false,$wherechk=false,
		$andparam=false,$orparam=false,$disabled=false,$javascript=false) {
		
		if(!$tbname) {
			$this->setErrorMsg("Please insert table name to create list box!!");
			return false;
		}
		$chktbname = explode('_', $tbname, 2);
		$check = '';
		if($chktbname[0]=="all"){$tbname = $chktbname[1];$check = "all";}
		
		if($tbname=="p_timer") {
			$sql = "select time_id,time_start from p_timer where time_id mod ".$this->apptprdist." = ".$this->modtime." and time_id >= ".$this->starttimeid." and time_id < ".$this->closetimeid." limit 0,30";
			//echo $sql."<br>";
		} else if($tbname=="l_hour"){
			$saperatetime = strtotime($this->closetime)-strtotime($this->starttime);
			$saperatetime = date("H:i:s",mktime(0,0,$saperatetime));
			
			$sql = "select hour_id,hour_name from l_hour where hour_name<\"$saperatetime\"";
		} else if($tbname=="cl_product_category,cl_product"){
			$sql = "select $fieldid,$fieldname,cl_product.pd_category_id,cl_product_category.pd_category_name from $tbname";
		} else {
			$sql = "select $fieldid,$fieldname from $tbname";
		}
		
		if($wherename) {
			$sql .= " where $wherename=$wherechk";
		}
			
		if($andparam) {
			$sql .= " and $andparam";
		}
			
		if($orparam) {
			$sql .= " or $orparam";
		}
			
		if($order) {
			$sql .= " order by $order";
		}
		//echo $sql."<br>";
		$row = $this->getResult($sql);
		$count = $row["rows"];
		
		$tmp = explode("[",$sname);
		//echo $selected;
		echo "<select id=\"$sname\" name=\"$sname\" class=\"ctrDropDown\" onBlur=this.className='ctrDropDown'; onMouseDown=this.className='ctrDropDownClick'; onChange=this.className='ctrDropDown';";
		if($tbname=="al_bookparty")
		
		if($javascript&&!$chkautosubmit){
			echo " onChange=\"$javascript\" ";
		}
		
		if($disabled)
			echo " disabled ";
		if(isset($chkautosubmit)&&$chkautosubmit!=false){
			if($tbname=="cl_product_category,cl_product"){
				echo " onChange=\"addSrd(".$chkautosubmit[0].",".$chkautosubmit[1].",1);this.form.submit();\">";
			} else {
				echo " onChange=\"this.form.submit();\">";
			}
		} else {
			echo ">";
		}		
		if($check == "all"){echo "<option title=\"All\" value=\"0\">All</option>";}
		for($i=0; $i < $count; $i++) {
			if($tbname=="cl_product_category,cl_product"){
				if($row[$i]["pd_category_id"]!=$row[$i-1]["pd_category_id"]){echo "<optgroup label=\"".$row[$i]["pd_category_name"]."\" title=\"".$row[$i]["pd_category_name"]."\">";}
			}
			if($tbname=="l_hour"){$data=substr($row[$i]["$fieldname"],0,5);}else{$data=$row[$i]["$fieldname"];}
			if(str_replace(" ","",$data)=="" && $tbname=="db_package"){
				$data=" No Package";
			}else if(str_replace(" ","",$data)=="" && $tbname=="db_trm"){
				$data=" No Massage";
			}else if($data==" -- select --" && $tbname=="al_bookparty"){
				$data=" All Booking Company";
			}else{
				$data=$data;
			}
			echo "<option title=\"".$data."\" value=\"".$row[$i]["$fieldid"]."\"";
			if ($row[$i]["$fieldid"] == $selected) {
				echo " selected=\"selected\"";
			}
			echo ">";
			echo $data."</option>";
			if($tbname=="cl_product_category,cl_product"){
				if($i&&$row[$i]["pd_category_id"]!=$row[$i+1]["pd_category_id"]){echo "</optgroup>";}
			}
		}
		
		echo "</select>";	
	}
	
	
	/*
	 * auto calculate service charge of each product in Accounting Sale receipt
	 * @param $product - product array all value
	 * @param $j - index of product
	 * @modified - add this function on 31 Jan 2009
	 */
	function getsSvc($product=false) {
		if($product["set_sc"]){
			if($product["servicescharge"]) {
				$servicecharge = ($product["total"])*($product["servicescharge"]/100);
			}
			else {
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
	function getsTax($product=false,$svc=false){
		if($product["set_tax"]) {
					$tax = ($product["total"]+$svc)*($product["taxpercent"]/100);
		}
		else {
			$tax = 0;
		}
		//echo $tax."<br>";
		return $tax;
	}

/*
 * function for seperate startdate-enddate using Columns to resultset 
 * for generate Columns in the report
 */
 function getdatecol($column=false,$begindate=false,$enddate=false,$branchid=false){
 	$rs = array();
 	$rs["rows"] = 0;
 	$rs["begin"] = array();
 	$rs["end"] = array();
	$dateobj = new convertdate();
	$chkrs =$this->getResult("select long_date,short_date from a_company_info");
	$sdateformat = $this->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
	$ldateformat = $this->getIdToText($chkrs[0]["long_date"],"l_date","date_format","date_id");
 	$perioddate = $dateobj->countdays($enddate,$begindate)+1;
 	$periodweek = ceil(($perioddate)/7);
 	$periodmonth = $dateobj->countmonths($enddate,$begindate);
	$bday=substr($begindate,6,2);$bmonth=substr($begindate,4,2);$byear=substr($begindate,0,4);
	$eday=substr($enddate,6,2);$emonth=substr($enddate,4,2);$eyear=substr($enddate,0,4);
	$periodyear = $eyear-$byear+1;
 	if($column=="Total only"){
 		$rs["rows"] = 1;
 		$rs["begin"][0] = $begindate;
 		$rs["end"][0] = $enddate;
 		$rs["header"][0] = "TOTAL";
 	}
 	if($column=="Day"){//echo $perioddate;
 		$rs["rows"] = $perioddate;
 		for($i=0;$i<$perioddate;$i++){
 			$rs["begin"][$i] = $dateobj->plusdate($begindate,$i);
 			$rs["end"][$i] = $dateobj->plusdate($begindate,$i);
 			$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y");
 		}
 		
 	}
 	if($column=="Week"){
 		$rs["rows"] = $periodweek;
 		$weeknum = $dateobj->convertdate($begindate,"Ymd","N");
 		for($i=0;$i<$periodweek;$i++){
 			if($i==0&&$begindate!=$enddate){
 				$rs["begin"][0] = $begindate;
 				if($weeknum==7){$rs["end"][0] = $dateobj->plusdate($begindate,6);}
 				else{$rs["end"][0] = $dateobj->plusdate($begindate,6-$weeknum);}
 				//echo $rs["begin"][0]." - ".$rs["end"][0];
 				//echo "<br><br><br><b>$i</b>: ".$rs["begin"][0]." - ".$rs["end"][0];
 				if($rs["begin"][$i]<$enddate && $rs["end"][$i]>$enddate){
	 				$rs["end"][$i] = $enddate;
		 			if(substr($rs["end"][0],0,4)!=substr($rs["begin"][0],0,4)){
		 				$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][0],"Ymd","M j, y");
		 			}else{
			 			if(substr($rs["begin"][$i],6,2)==substr($rs["end"][$i],6,2)){$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y");}
			 			else{$rs["header"][$i] = str_replace(",","-".intval(substr($rs["end"][$i],6,2)).",",$dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y"));}
		 			}
 				}else if(substr($rs["end"][0],0,4)!=substr($rs["begin"][0],0,4)){
 					if($weeknum==7){
 						$rs["header"][0] = "Week of <br>";
 						$rs["header"][0] .= $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");
 					}else {
 						$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][0],"Ymd","M j, y");
 					}
 				}else{
	 				if($weeknum==7){
		 				$rs["header"][0] = "Week of <br>";
		 				if(substr($rs["begin"][0],4,2)==substr($rs["end"][0],4,2)){
		 						if(substr($rs["begin"][0],6,2)==substr($rs["end"][0],6,2)){$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");}
		 						else{$rs["header"][0] .= $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");}
		 				}else{
		 						if(substr($rs["begin"][0],6,2)==substr($rs["end"][0],6,2)){$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");}
		 						else{$rs["header"][0] .= $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");}
		 				}
	 				}else{
		 				if(substr($rs["begin"][0],4,2)==substr($rs["end"][0],4,2)){
		 						if(substr($rs["begin"][0],6,2)==substr($rs["end"][0],6,2)){$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");}
		 						else{$rs["header"][0] = str_replace(",","-".intval(substr($rs["end"][0],6,2)).",",$dateobj->convertdate($rs["begin"][0],"Ymd","M j, y"));}
		 				}else{
		 						if(substr($rs["begin"][0],6,2)==substr($rs["end"][0],6,2)){$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");}
		 						else{$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][0],"Ymd","M j, y");}
		 				}
	 				}
 				}
 				//echo "<br><b>$i</b>: ".$rs["begin"][0]." - ".$rs["end"][0];
 			}else if($begindate==$enddate){
 				$rs["begin"][0] = $begindate;
 				$rs["end"][0] = $begindate;
 				$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");
 			}else{
 				//if(substr($rs["begin"][$i],0,4)==){}
 				$rs["begin"][$i] = $dateobj->plusdate($rs["end"][$i-1],1);
 				$rs["end"][$i] = $dateobj->plusdate($rs["begin"][$i],6);
	 			if($rs["begin"][$i]<$enddate && $rs["end"][$i]<=$enddate){
		 				$rs["header"][$i] = "Week of <br>".$dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y");
	 			}else{
	 				$rs["end"][$i] = $enddate;
		 			if(substr($rs["begin"][$i],6,2)==substr($rs["end"][$i],6,2)){$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y");}
		 			else{$rs["header"][$i] = str_replace(",","-".intval(substr($rs["end"][$i],6,2)).",",$dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y"));}
		 		}
 			}
 		}
 		if($rs["end"][$periodweek-1]<$enddate){
 			$rs["rows"] = $periodweek+1;
 			$rs["begin"][$periodweek] = $dateobj->plusdate($rs["end"][$periodweek-1],1);
 			$rs["end"][$periodweek] = $dateobj->plusdate($rs["begin"][$periodweek],6);
 			if($rs["begin"][$periodweek]<$enddate && $rs["end"][$periodweek]<=$enddate){
		 				$rs["header"][$periodweek] = "Week of <br>".$dateobj->convertdate($rs["begin"][$periodweek],"Ymd","M j, y");
	 			}else{
	 				$rs["end"][$periodweek] = $enddate;
		 			if(substr($rs["begin"][$periodweek],6,2)==substr($rs["end"][$periodweek],6,2)){$rs["header"][$periodweek] = $dateobj->convertdate($rs["begin"][$periodweek],"Ymd","M j, y");}
		 			else{$rs["header"][$periodweek] = str_replace(",","-".intval(substr($rs["end"][$periodweek],6,2)).",",$dateobj->convertdate($rs["begin"][$periodweek],"Ymd","M j, y"));}
		 		}
		}
 	}
 	if($column=="Half month"){
 		if($bday>15){$rs["rows"]--;} // first half month
 		if($eday<15){$rs["rows"]--;} // last half month
 		$j=0;
 		for($i=0;$i<$periodmonth;$i++){
 			if($i==0){
 				$rs["begin"][$j] = $begindate;
 				$rs["end"][$j] = ($bday>15)?$dateobj->plusdate($begindate,0,1,0,0):$dateobj->plusdate($begindate,0,0,0,15);
 				if($rs["begin"][$j]<$enddate && $rs["end"][$j]>$enddate){
	 				$rs["end"][$j] = $enddate;
		 			if(substr($rs["end"][0],0,4)!=substr($rs["begin"][0],0,4)){
		 				$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$j],"Ymd","M j, y");
		 			}else{
			 			if(substr($rs["begin"][$j],6,2)==substr($rs["end"][$j],6,2)){$rs["header"][$j] = $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y");}
			 			else{$rs["header"][$j] = str_replace(",","-".intval(substr($rs["end"][$j],6,2)).",",$dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y"));}
		 			}
 				}else if(substr($rs["end"][0],0,4)!=substr($rs["begin"][0],0,4)){
 						$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][0],"Ymd","M j, y");
 				}else{
	 				if(substr($rs["begin"][$j],6,2)==1||substr($rs["begin"][$j],6,2)==16){
		 				$rs["header"][0] = "Half month of <br>";
			 				if(substr($rs["begin"][0],6,2)==substr($rs["end"][0],6,2)){$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");}
			 				else{$rs["header"][0] .= $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");}
	 				}else{
			 				if(substr($rs["begin"][0],6,2)==substr($rs["end"][0],6,2)){$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");}
			 				else{$rs["header"][0] = str_replace(",","-".intval(substr($rs["end"][0],6,2)).",",$dateobj->convertdate($rs["begin"][0],"Ymd","M j, y"));}
		 			}
 				}
				$j++;
				if($bday<15){
					$rs["begin"][$j] = $dateobj->plusdate($begindate,0,0,0,16);
 					$rs["end"][$j] = $dateobj->plusdate($begindate,0,1,0,0);
 					
	 				if($rs["begin"][$j]<$enddate && $rs["end"][$j]>$enddate){
		 				$rs["end"][$j] = $enddate;
			 			if(substr($rs["end"][0],0,4)!=substr($rs["begin"][0],0,4)){
			 				$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$j],"Ymd","M j, y");
			 			}else{
				 			if(substr($rs["begin"][$j],6,2)==substr($rs["end"][$j],6,2)){$rs["header"][$j] = $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y");}
				 			else{$rs["header"][$j] = str_replace(",","-".intval(substr($rs["end"][$j],6,2)).",",$dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y"));}
			 			}
	 				}else if(substr($rs["end"][$j],0,4)!=substr($rs["begin"][$j],0,4)){
	 						$rs["header"][$j] = $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$j],"Ymd","M j, y");
	 				}else{
			 			$rs["header"][$j] = "Half month of <br>";
			 			if(substr($rs["begin"][$j],6,2)==substr($rs["end"][$j],6,2)){$rs["header"][$j] = $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y");}
			 			else{$rs["header"][$j] .= $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y");}
	 				}
					$j++;
				}
 			}else{
					$rs["begin"][$j] = $dateobj->plusdate($rs["end"][$j-1],0,1,0,1);
 					$rs["end"][$j] = $dateobj->plusdate($rs["end"][$j-1],0,1,0,15);
 					
	 				if($rs["begin"][$j]<=$enddate && $rs["end"][$j]>$enddate){
		 				$rs["end"][$j] = $enddate;
			 			if(substr($rs["end"][$j],0,4)!=substr($rs["begin"][0],0,4)){
			 				$rs["header"][$j] = $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$j],"Ymd","M j, y");
			 			}else{
				 			if(substr($rs["begin"][$j],6,2)==substr($rs["end"][$j],6,2)){$rs["header"][$j] = $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y");}
				 			else{$rs["header"][$j] = str_replace(",","-".intval(substr($rs["end"][$j],6,2)).",",$dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y"));}
			 			}
	 				}else if(substr($rs["end"][$j],0,4)!=substr($rs["begin"][$j],0,4)){
	 						$rs["header"][$j] = $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$j],"Ymd","M j, y");
	 				}else{
			 			$rs["header"][$j] = "Half month of <br>";
			 			if(substr($rs["begin"][$j],6,2)==substr($rs["end"][$j],6,2)){$rs["header"][$j] = $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y");}
			 			else{$rs["header"][$j] .= $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y");}
	 				}
					$j++;
					if(($i<$periodmonth-1)||($eday>15&&$i==$periodmonth-1)){
						$rs["begin"][$j] = $dateobj->plusdate($rs["end"][$j-1],0,0,0,16);
	 					$rs["end"][$j] = $dateobj->plusdate($rs["end"][$j-1],0,1,0,0);
	 					
		 				if($rs["begin"][$j]<=$enddate && $rs["end"][$j]>$enddate){
			 				$rs["end"][$j] = $enddate;
				 			if(substr($rs["end"][$j],0,4)!=substr($rs["begin"][$j],0,4)){
				 				$rs["header"][$j] = $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$j],"Ymd","M j, y");
				 			}else{
					 			if(substr($rs["begin"][$j],6,2)==substr($rs["end"][$j],6,2)){$rs["header"][$j] = $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y");}
					 			else{$rs["header"][$j] = str_replace(",","-".intval(substr($rs["end"][$j],6,2)).",",$dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y"));}
				 			}
		 				}else if(substr($rs["end"][$j],0,4)!=substr($rs["begin"][$j],0,4)){
		 						$rs["header"][$j] = $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$j],"Ymd","M j, y");
		 				}else{
				 			$rs["header"][$j] = "Half month of <br>";
				 			if(substr($rs["begin"][$j],6,2)==substr($rs["end"][$j],6,2)){$rs["header"][$j] = $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y");}
				 			else{$rs["header"][$j] .= $dateobj->convertdate($rs["begin"][$j],"Ymd","M j, y");}
		 				}
						$j++;
					}
 			}
 		} 
 		$rs["rows"] = count($rs["header"]); 		//fix bug halfmonth report column @modified on 25-Feb-2009
 		//for($i=0;$i<$j;$i++){	echo "<br><b>$i</b>: ".$rs["begin"][$i]." - ".$rs["end"][$i];	}
 	}
 	if($column=="Month"){
 		$rs["rows"] = $periodmonth;
 		for($i=0;$i<$periodmonth;$i++){
 			if($i==0){
 				$rs["begin"][0] = $begindate;
 				$rs["end"][0] = $dateobj->plusdate($begindate,0,1,0,0);
 				if($rs["begin"][0]<$enddate && $rs["end"][0]>$enddate){
	 				$rs["end"][0] = $enddate;
		 			if(substr($rs["end"][0],0,4)!=substr($rs["begin"][0],0,4)){
		 				$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][0],"Ymd","M j, y");
		 			}else{
			 			if(substr($rs["begin"][0],6,2)==substr($rs["end"][0],6,2)){$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");}
			 			else{$rs["header"][0] = str_replace(",","-".intval(substr($rs["end"][0],6,2)).",",$dateobj->convertdate($rs["begin"][0],"Ymd","M j, y"));}
		 			}
 				}else if(substr($rs["end"][0],0,4)!=substr($rs["begin"][0],0,4)){
 						$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][0],"Ymd","M j, y");
 				}else{
	 				if(substr($rs["begin"][0],6,2)==1||substr($rs["begin"][0],6,2)==16){
			 				if(substr($rs["begin"][0],6,2)==substr($rs["end"][0],6,2)){$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");}
			 				else{$rs["header"][0] .= $dateobj->convertdate($rs["begin"][0],"Ymd","M y");}
	 				}else{
			 				if(substr($rs["begin"][0],6,2)==substr($rs["end"][0],6,2)){$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");}
			 				else{$rs["header"][0] = str_replace(",","-".intval(substr($rs["end"][0],6,2)).",",$dateobj->convertdate($rs["begin"][0],"Ymd","M j, y"));}
		 			}
 				}
 			}else{
 				$rs["begin"][$i] = $dateobj->plusdate($rs["end"][$i-1],0,1,0,1);
 				$rs["end"][$i] = $dateobj->plusdate($rs["end"][$i-1],0,2,0,0);
		 		if($rs["begin"][$i]<=$enddate && $rs["end"][$i]>$enddate){
			 			$rs["end"][$i] = $enddate;
				 		if(substr($rs["end"][$i],0,4)!=substr($rs["begin"][$i],0,4)){
				 				$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$i],"Ymd","M j, y");
				 		}else{
					 			if(substr($rs["begin"][$i],6,2)==substr($rs["end"][$i],6,2)){$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y");}
					 			else{$rs["header"][$i] = str_replace(",","-".intval(substr($rs["end"][$i],6,2)).",",$dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y"));}
				 		}
		 		}else{
				 		if(substr($rs["begin"][$i],6,2)==substr($rs["end"][$i],6,2)){$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y");}
				 		else{$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M y");}
		 		}
 			}
 		}
 	}
 	if($column=="Quarter"){
 		$rs["rows"] = 0;
 		for($i=0;$i<$periodmonth;$i++){
 			if($i==0){
 				$rs["rows"]++;
 				$rs["begin"][0] = $begindate;
		 		if($bmonth<=3&&$bmonth>=1){
						$begintmp = date("Ymd",mktime(0,0,1,1,1,$byear));
						$rs["end"][0] = date("Ymd",mktime(0,0,0,4,0,$byear));
				} else if ($bmonth<=6&&$bmonth>=4){
						$begintmp = date("Ymd",mktime(0,0,1,4,1,$byear));
						$rs["end"][0] = date("Ymd",mktime(0,0,0,7,0,$byear));
				} else if ($bmonth<=9&&$bmonth>=7){
						$begintmp = date("Ymd",mktime(0,0,1,7,1,$byear));
						$rs["end"][0] = date("Ymd",mktime(0,0,0,10,0,$byear));
				} else if ($bmonth<=12&&$bmonth>=10){
						$begintmp = date("Ymd",mktime(0,0,1,10,1,$byear));	
						$rs["end"][0] = date("Ymd",mktime(0,0,0,1,0,$byear+1));	
				} 
 			}else if($rs["end"][$i-1]<$enddate){
 				$rs["rows"]++;
 				if($i-1==0){$begintmp=$begintmp;}else{$begintmp=$rs["begin"][$i-1];}
 				if(substr($rs["end"][$i-1],4,1)==12&&$begintmp==$rs["begin"][$i-1]){$plusyear=1;}else{$plusyear=0;}
				$rs["begin"][$i] = $dateobj->plusdate($begintmp,0,3,$plusyear,1);
				$rs["end"][$i] = $dateobj->plusdate($rs["end"][$i-1],0,4,$plusyear,0);
				if($rs["begin"][$i]<$enddate && $rs["end"][$i]>$enddate){$rs["end"][$i] = $enddate;}
 			}else{
 				break;
 			}
 			//echo "<br><b>$i</b>: ".$rs["begin"][$i]." - ".$rs["end"][$i];
 			if($rs["begin"][$i]==$enddate){
	 			$rs["end"][$i] = $enddate;
	 			$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y");
	 		}else if($rs["begin"][$i]<$enddate && $rs["end"][$i]>$enddate){
	 			$rs["end"][$i] = $enddate;
	 			if(substr($rs["end"][$i],0,4)==substr($rs["begin"][$i],0,4)){
		 				if(substr($rs["begin"][$i],4,2)==substr($rs["end"][$i],4,2)){
		 						if(substr($rs["begin"][$i],6,2)==substr($rs["end"][$i],6,2)){$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y");}
			 					else{$rs["header"][$i] = str_replace(",","-".intval(substr($rs["end"][$i],6,2)).",",$dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y"));}
		 				}else{
		 					$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$i],"Ymd","M j, y");
		 				}
	 			}
 			}else{
	 			if(substr($rs["end"][$i],0,4)==substr($rs["begin"][$i],0,4)){
		 				if(substr($rs["begin"][$i],4,2)==01&&substr($rs["end"][$i],4,2)==12){
		 						if(substr($rs["begin"][$i],6,2)==01&&substr($rs["end"][$i],6,2)==31){$rs["header"][$i] = substr($rs["begin"][$i],0,4);}
		 						else{$rs["header"][$i] = str_replace(",","-".intval(substr($rs["end"][$i],6,2)).",",$dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y"));}
		 				}else if(substr($rs["begin"][$i],4,2)==substr($rs["end"][$i],4,2)){
		 						if(substr($rs["begin"][$i],6,2)==substr($rs["end"][$i],6,2)){$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y");}
			 					else{$rs["header"][$i] = str_replace(",","-".intval(substr($rs["end"][$i],6,2)).",",$dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y"));}
		 				}else{
			 				if(substr($rs["begin"][$i],4,6)=="0101"&&substr($rs["end"][$i],4,6)=="0331"){
			 						$rs["header"][$i] = "1st Quarter <br>".substr($rs["begin"][$i],0,4);
			 				}else if(substr($rs["begin"][$i],4,6)=="0401"&&substr($rs["end"][$i],4,6)=="0630"){
			 						$rs["header"][$i] = "2nd Quarter <br>".substr($rs["begin"][$i],0,4);
			 				}else if(substr($rs["begin"][$i],4,6)=="0701"&&substr($rs["end"][$i],4,6)=="0930"){
			 						$rs["header"][$i] = "3rd Quarter <br>".substr($rs["begin"][$i],0,4);
			 				}else if(substr($rs["begin"][$i],4,6)=="1001"&&substr($rs["end"][$i],4,6)=="1231"){
			 						$rs["header"][$i] = "4th Quarter <br>".substr($rs["begin"][$i],0,4);
			 				}else{$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$i],"Ymd","M j, y");}
		 					//$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y")." to ".$dateobj->convertdate($rs["end"][$i],"Ymd","M j, y");
		 				}
	 			}
 			}
 			
 		}
 	}
 	if($column=="Year"){
 		$rs["rows"] = $periodyear;
 		for($i=0;$i<$periodyear;$i++){
 			if($i==0){
 				$rs["begin"][0] = $begindate;
 				$rs["end"][0] = $dateobj->plusdate($begindate,0,0,1,0,1);
 			}else{
 				$rs["begin"][$i] = $dateobj->plusdate($rs["end"][$i-1],0,0,1,1,1);
 				$rs["end"][$i] = $dateobj->plusdate($rs["begin"][$i],0,0,1,0,1);
 			}
 			if($rs["begin"][$i]==$enddate){
	 			$rs["end"][$i] = $enddate;
	 			$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y");
	 		}else if($rs["begin"][$i]<$enddate && $rs["end"][$i]>$enddate){
	 			$rs["end"][$i] = $enddate;
	 			if(substr($rs["end"][$i],0,4)==substr($rs["begin"][$i],0,4)){
		 				if(substr($rs["begin"][$i],4,2)==01&&substr($rs["end"][$i],4,2)==12){
		 						if(substr($rs["begin"][$i],6,2)==01&&substr($rs["end"][$i],6,2)==31){$rs["header"][$i] = substr($rs["begin"][$i],0,4);}
		 						else{$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$i],"Ymd","M j, y");}
		 				}else if(substr($rs["begin"][$i],4,2)==substr($rs["end"][$i],4,2)){
		 						if(substr($rs["begin"][$i],6,2)==substr($rs["end"][$i],6,2)){$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y");}
			 					else{$rs["header"][$i] = str_replace(",","-".intval(substr($rs["end"][$i],6,2)).",",$dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y"));}
		 				}else{
		 					$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$i],"Ymd","M j, y");
		 				}
	 			}
 			}else{
	 			if(substr($rs["end"][$i],0,4)==substr($rs["begin"][$i],0,4)){
		 				if(substr($rs["begin"][$i],4,2)==01&&substr($rs["end"][$i],4,2)==12){
		 						if(substr($rs["begin"][$i],6,2)==01&&substr($rs["end"][$i],6,2)==31){$rs["header"][$i] = substr($rs["begin"][$i],0,4);}
		 						else{$rs["header"][$i] = str_replace(",","-".intval(substr($rs["end"][$i],6,2)).",",$dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y"));}
		 				}else{
		 					$rs["header"][$i] = $dateobj->convertdate($rs["begin"][$i],"Ymd","M j, y")." to <br>".$dateobj->convertdate($rs["end"][$i],"Ymd","M j, y");
		 				}
	 			}
 			}
 			//echo "<br><b>$i</b>: ".$rs["begin"][$i]." - ".$rs["end"][$i];
 		}
 	}
 	if($begindate == $enddate){			// add this condition in 23-Apr-2009 for detect case $begindate=$enddate
 			$rs["rows"] = 1;
 			$perioddate = 0;
 			$rs["begin"] = array();$rs["end"] = array();$rs["header"] = array();
 			$rs["begin"][0] = $dateobj->plusdate($begindate,0);
 			$rs["end"][0] = $dateobj->plusdate($begindate,0);
 			$rs["header"][0] = $dateobj->convertdate($rs["begin"][0],"Ymd","M j, y");
 	}
 	if($column=="Branch"){
 		$sql = "select * from bl_branchinfo where branch_name not like \"All\" ";
 		if($branchid){$sql .= "and branch_id=$branchid ";}		
 		//$sql .= "order by branch_name ";
 		$chkrs = $this->getResult($sql); 
 		$rs["rows"] = $chkrs["rows"];
 		for($i=0;$i<$chkrs["rows"];$i++){
 			$rs["header"][$i] = $chkrs[$i]["branch_name"];
 			$rs["begin"][$i] = $chkrs[$i]["branch_name"];
 			$rs["end"][$i] = $chkrs[$i]["branch_id"];
 		}
 	}
 	
 	//print_r($rs);echo "<br>";
 	return $rs;
 }
 

/*
 * summary total result set by branch,category an location
 * $fieldname - fieldname of summary value for example: customer per location report sunary "qty"
 */	
	function sumeachfield($rs,$fieldname,$branch_id=false,$begin=false,$end=false){
		//print_r($rs);
		$sum = 0;
		for($i=0;$i<$rs["rows"];$i++){
			$appt_date = str_replace("-","",$rs[$i]["b_appt_date"]);
			if($appt_date>=$begin&&$appt_date<=$end){
				if($rs[$i]["branch_id"]==$branch_id){
					$sum += $rs[$i]["$fieldname"];
				}
			}
		}
		return $sum;
	}
}
?>
