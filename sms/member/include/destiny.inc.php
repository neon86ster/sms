<?php
/*
 * File name : cms.inc.php
 * Description : Class file which is main controller for cms system
 * Author : art
 * Create date : 22-Oct-2008
 * Modified : natt@chiangmaioasis.com
 */
require_once("smsmysql.inc.php");

class destiny {
	protected $recordcount;		// number of row after get the result from database connection
	protected $affectedrows;	// number of affected rows in MySQL operation
	protected $lastinsertid;	// the ID that generate form MySQL insert operation
	protected $showpage;		// number of total record per page
	protected $classname;		// 
	protected $msg;				// msg for debug report
	protected $successmsg;		// success msg for debug report
	protected $errormsg;		// error msg for debug report
	protected $errorcolor;		// font color of debuging report (error msg and MySQL error code)
	protected $successcolor;	// font color of debugging report (msg and query string A SQL Query)
	protected $userid;			// user login id
	protected $ip;				// user login ip
	protected $tablename;		//
	protected $dbName;			// set for database name

/*
 * Constructor of class cms 
 */	
	function destiny(){
		$this->recordcount = 0;
		$this->lastinsertid = 0;
		$this->showpage = 20;
		$this->errorcolor = "#FF0000";
		$this->successcolor = "#71A328";
		$this->msg = false;
		$this->successmsg = false;
		$this->errormsg = false;
		$this->userid = @$_SESSION["__user_id"];
		$this->ip = $_SERVER["REMOTE_ADDR"];
		$this->dbName = "tap10_bdestiny";
		$this->_user = "root";
		$this->_pass = "1234";
		//$this->dbName = "TAP10_bdestiny";
		//$this->_user = "smsdestiny";
		//$this->_pass = "T@pO@siS%";
	}

/********************************************************
 * Get and Set parameter function
 ********************************************************/ 	
/*
 * function to get parameter form POST or GET method
 * @param - Parameter name
 * @param - Return value
 */	
	function getParameter($value, $return=false) {
		if(isset($_POST[$value])) {return $_POST[$value];}
		if(isset($_GET[$value])) {return $_GET[$value];}
		return $return;
	}
	
/*
 * function for check parameter if have no data($data=false) then return the return value
 * @param - Value of the parameter
 * @param - Return value
 */	
	function checkParameter($data=false, $return=false) {
		if($data){return $data;}
		else {return $return;}
	}
	
/*
 * function to get and set number of total record per page
 * @param - Number of total record per page
 */	
	function setShowpage($newshowpage=false) {
 		$this->showpage = $newshowpage;
 	}
 	
 	function getShowpage() {
 		return $this->showpage;
 	}
	
/*
 * function to get and set number of rows in a result source
 * @param - Number of rows after get the result from database connection
 */	
	function setRecordcount($newRecordcount) {
		$this->recordcount = $newRecordcount;
	}
	
	function getRecordcount() {
		return $this->recordcount;
	}
	
/*
 * function to set and get affected rows in MySQL operation
 * @param - number of affected rows in MySQL operation
 */		
	function setAffectedrows($newAffectedrows) {
		$this->affectedrows = $newAffectedrows;
	}
	
	function getAffectedrows() {
		return $this->affectedrows;
	}
	
/*
 * function to set and get ID generate form MySQL insert operation
 * @param - the ID that generate form MySQL insert operation
 */	
	function setLastinsertid($newLastinsertid) {
		$this->lastinsertid = $newLastinsertid;
	}
	
	function getLastinsertid() {
		return $this->lastinsertid;
	}
	
/*
 * function to set ang get ip form new ip
 * @param - Ip value
 */		
	function setIp($newip=false) {
		$this->ip = $newip;
		return true;
	}
	
	function getIp() {
		return $this->ip;
	}	
	
/********************************************************
 * Debug status function and debug report function
 ********************************************************/ 	
/*
 * function for set and get debug status in "__debug" session
 * @param - Debug status
 */
	function setDebugStatus($status=false) {
		$_SESSION["__debug"] = $status;
		return $_SESSION["__debug"];
	}
	
	function getDebugStatus() {
		return $_SESSION["__debug"];
	}
	
/*
 * function for set and get error of debug reporting in "__error_reporting" session
 * @param - Error report status
 */
	function setErrorReport($status=false) {
		$_SESSION["__error_reporting"] = $status;
		return $_SESSION["__error_reporting"];
	}

	function getErrorReport(){
		return $_SESSION["__error_reporting"];
	}	

/*
 * function for set and get msg of debug reporting
 * @param - Msg of debug reporting
 */
	function setMsg($newMsg=false) {
		$this->msg = $newMsg;
	}
	
	function getMsg() {
		return "<pre><b>Msg</b>: ".$this->msg."</font></pre>\n";
	}

/*
 * function for set and get success msg of debug reporting
 * @param - Success msg of debug reporting
 */	
	function setSuccessMsg($newMsg=false) {
		$this->successmsg = $newMsg;
	}
	
	function getSuccessMsg() {
		return "<font style=\"color:".$this->successcolor."\">".$this->successmsg."</font>";
	}
	
/*
 * function for set and get error msg of debug reporting
 * @param - Error msg of debug reporting
 */	
	function setErrorMsg($newMsg=false) {
		$this->errormsg = $newMsg;
	}
	
	function getErrorMsg() {
		return "<font style=\"color:".$this->errorcolor."\">".$this->errormsg."</font>";
	}
	
	function setErrorMsgColor($newcolor){
		$this->errorcolor = $newcolor;
	}
	
/*
 * function to echo debug report
 * @param - Method name ex. cms.printDebug()
 * @param - Debug msg
 * @param - Query string A SQL Query
 * @param - Error msg ex. error msg form MySQL connection
 * @param - Error code number ex. number of error msg form mysql connection
 */
	function printDebug($method, $msg=false, $SQL=false, $error=false, $errorcode=false) {
		$textout = "<pre> \n";
		$textout .= "<b>Object: ".get_class($this)."</b> \n";
		$textout .= "<b>From: <font color='#2b71d6'>$method</font></b> \n";
		
		if($msg) {$textout .= "<b>Msg</b>: <font color=".$this->successcolor.">$msg</font> \n";}
		if($SQL) {$textout .= "<b>SQL</b>: <font color=".$this->successcolor.">$SQL</font> \n";}
		
		if($error) {$textout .= "<b>Error</b>: <font color=".$this->errorcolor.">$error</font> \n";}
		if($errorcode) {$textout .= "<b>Error code</b>: <font color=".$this->errorcolor.">$errorcode</font> \n";}
		$textout .= "</pre>";
		echo $textout;
	}
	
/********************************************************
 * GET and SET information function() normal and XML 
 * use to update information with database
 ********************************************************/ 		
	
/*
 * function to set data (send query string to mysql class)
 * @param - Query string A SQL Query
 */	
	function setResult($SQL=false, $debug=false) {
		if(!$SQL){
			$this->getErrorMsg("Plese check your query language!!");
			return false;
		}
		$m = new mysql($this->dbName,$this->_user,$this->_pass);
		$id = $m->setdata($SQL, $debug);
		
		$this->setlastinsertid($m->get_lastinsertid());
		$this->setAffectedrows($m->get_affectedrow());
		
		if($this->getDebugStatus()){$this->printDebug("cms.setResult()", false, $SQL, $m->get_msg, $m->__error);}
		if(!$id) {
			$this->setErrorMsg($m->get_msg());
			$this->printDebug("cms.setResult()","update information not complete..",$SQL,$m->get_msg(),$m->__error);
		}
		
		unset($m);
		return $id;
	}
	
/*
 * function to get data resource form Query string (send query string to mysql class)
 * @param - Query string A SQL Query
 */	
	function getResult($SQL=false, $debug=false) {
		if(!$SQL){
			$this->getErrorMsg("Plese check your query language!!");
			return false;
		}
		$m = new smsmysql($this->dbName,$this->_user,$this->_pass);
		$rs = $m->getdata($SQL, $debug);
		$this->setRecordcount($m->get_recordcount(false));
		
		if(!$rs && $this->getDebugStatus()) {
			$this->setErrorMsg($m->get_msg());
			$this->printDebug("cms.getResult()",false,$SQL,$m->get_msg(),$m->__error);
		}
		if($this->getDebugStatus()||$debug==true) {$this->printDebug("cms.getResult()","founds <b>".$rs["rows"]."</b> row(s)",$SQL,$m->get_msg(),$m->__error);}
		
		unset($m);
		return $rs;
	}

/*
 * function to get information result form id 
 * @param - id
 * @param - tablename
 * @param - fieldname
 * @param - idfield
 * @modified - add this function on 1 dec 2008
 */	
	function getIdToText($id=false,$table=false,$field=false,$index=false,$condition=false,$debug=false) {
		if(!$id) {
			$this->setErrorMsg("No have ID $id in $table!!");
			return false;
		}
		
		$sql = "select ".$field." from ".$table." where `$index`='$id'";
		if($condition){
			$sql .= " and $condition";
		}
		$sql .= " limit 0,1";
		if($debug){
 			echo $sql."<br/>";
 			return false;
 		}
		$row = $this->getResult($sql);
		return $row[0]["".$field.""];		
	}
	
	//############################# Function from destiny sytem for generate old member history ##################///
	function get_memberhistory($member_code=false,$debug=false)
	{
		if(!$member_code) {
			$this->setErrorMsg("No have member code for get information!!");
			return false;
		}
		
		$sql = "select a_master.b_appt_date,l_branchname.branch_name,a_membership.*,c_srdetail.*,l_product.product_name,l_product.catagory_id, ";
		$sql .= "sum(case l_product.catagory_id when 5 then -(c_srdetail.unit_price*c_srdetail.quantity) when 9 then -(c_srdetail.unit_price*c_srdetail.quantity) else (c_srdetail.unit_price*c_srdetail.quantity) end)+sum(case l_product.catagory_id when 5 then -((c_srdetail.unit_price*c_srdetail.quantity)*(l_percent_cms.cms_percent/100)) when 3 then 0 when 9 then 0 else ((c_srdetail.unit_price*c_srdetail.quantity)*(l_percent_cms.cms_percent/100)) end)+sum(case l_product.catagory_id when 5 then -(((c_srdetail.unit_price*c_srdetail.quantity)+(c_srdetail.unit_price*c_srdetail.quantity)*(l_percent_cms.cms_percent/100))*((7/100)*(l_product.plus_vat))) when 3 then ((c_srdetail.unit_price*c_srdetail.quantity)*((7/100)*(l_product.plus_vat))) when 9 then -((c_srdetail.unit_price*c_srdetail.quantity)*((7/100)*(l_product.plus_vat))) else (((c_srdetail.unit_price*c_srdetail.quantity)+(c_srdetail.unit_price*c_srdetail.quantity)*(l_percent_cms.cms_percent/100))*((7/100)*(l_product.plus_vat))) end) as total ";
				
		$sql .= "from a_master left join a_membership on a_master.a_member_id=a_membership.member_code ";
		$sql .= "left join c_salesreceipt on a_master.book_id=c_salesreceipt.book_id ";
		$sql .= "left join c_srdetail on c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql .= "left join l_product on c_srdetail.product_id=l_product.product_id,l_branchname,l_percent_cms ";
		
		$sql .= "where a_membership.member_code=\"".$member_code."\" ";
		$sql .= "and a_master.b_branch_id=l_branchname.branch_id ";
		$sql .= "and l_product.catagory_id<>9 ";
		$sql .= "and l_product.catagory_id<>5 ";
		$sql .= "and l_product.catagory_id<>3 ";
		$sql .= "and c_salesreceipt.servicecharge_id=l_percent_cms.cms_id ";
		
		$sql .= "group by c_srdetail.srdetail_id ";
		
		$sql .= "order by a_master.b_appt_date,a_master.book_id,c_srdetail.srdetail_id ";
		
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
	function get_membertreatment($member_code=false,$debug=false)
	{
		if(!$member_code) {
			$this->setErrorMsg("No have member code for get information!!");
			return false;
		}
		
		
		$sql = "SELECT a_master.book_id, a_master.b_appt_date, l_branchname.branch_name, l_room.room_name, l_hour.hour_use, l_package.package_name, t_massagetype.massage_type AS m1, t_massagetype2.massage_type AS m2, t_facialtype.facial_type , t_wraptype.wrap_type, t_scrubtype.scrub_type, t_bathtype.bath_type, t_strength.strength_type, l_therapist.therapist_name ";
		$sql .= "FROM a_master ";
		$sql .= "LEFT JOIN d_therapist ON a_master.book_id = d_therapist.book_id ";
		$sql .= "LEFT JOIN l_package ON d_therapist.package_id = l_package.package_id ";
		$sql .= "LEFT JOIN t_massagetype ON d_therapist.massage1_id = t_massagetype.massage_id ";
		$sql .= "LEFT JOIN t_massagetype2 ON d_therapist.massage2_id = t_massagetype2.massage_id ";
		$sql .= "LEFT JOIN t_facialtype ON d_therapist.facial_id = t_facialtype.facial_id ";
		$sql .= "LEFT JOIN t_wraptype ON d_therapist.wrap_id = t_wraptype.wrap_id ";
		$sql .= "LEFT JOIN t_scrubtype ON d_therapist.scrub_id = t_scrubtype.scrub_id ";
		$sql .= "LEFT JOIN t_bathtype ON d_therapist.bath_id = t_bathtype.bath_id ";
		$sql .= "LEFT JOIN t_strength ON d_therapist.strength_id = t_strength.strength_id ";
		$sql .= "LEFT JOIN l_therapist ON d_therapist.therapist_id = l_therapist.therapist_id ";
		$sql .= "LEFT JOIN l_branchname ON a_master.b_branch_id = l_branchname.branch_id ";
		$sql .= "LEFT JOIN l_room ON d_therapist.room_id = l_room.room_id ";
		$sql .= "LEFT JOIN l_hour ON d_therapist.hours_id = l_hour.hour_id ";
		$sql .= "WHERE a_master.a_member_id =".$member_code." ";
		$sql .= "AND d_therapist.member_use =1 ";
		$sql .= "ORDER BY a_master.b_appt_date, a_master.b_branch_id";
		
		
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	//############################# End function from destiny sytem for generate old member history ##################///
}
?>
