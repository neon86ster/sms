<?php
/*
 * File name : cms.inc.php
 * Description : Class file which is main controller for cms system
 * Author : art
 * Create date : 22-Oct-2008
 * Modified : natt@chiangmaioasis.com
 */
require_once("mysql.inc.php");

class cms {
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

/*
 * Constructor of class cms 
 */	
	function cms(){
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
	}

/********************************************************
 * Get and Set parameter function
 ********************************************************/ 	
/*
 * function to get parameter form POST or GET method
 * @param - Parameter name
 * @param - Return value
 */	
	function getParameter($value, $return=false, $numeric=false) {
		$default = $return;
		if(isset($_POST[$value])) {$return = $_POST[$value];}
		if(isset($_GET[$value])) {$return = $_GET[$value];}
		
		if($numeric){
			if(!ctype_digit($return)){return $default;}
		}else if(!is_array($return)){
			$return =$this->RemoveXSS($return);
		}
		
		return $return;
	}
	
/*
 * function for remove XSS from url parameter
 * @param - Value of the parameter
 */	
	function RemoveXSS($val) {
	   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
	   // this prevents some character re-spacing such as <java\0script>
	   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
	   // note that you have to handle splits with \n and \r and \t later since they *are* allowed in some inputs
	   // remove , 8-Oct-2009 by natt
	   $val = preg_replace('/([\x00-\x08\x0b-\x0c\x0e-\x19])/', '', $val);
	   
	   // straight replacements, the user should never need these since they're normal characters
	   // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A &#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
	   $search = 'abcdefghijklmnopqrstuvwxyz';
	   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	   $search .= '1234567890!@#$%^&*()';
	   $search .= '~`";:?+/={}[]-_|\'\\';
	   for ($i = 0; $i < strlen($search); $i++) {
	      // ;? matches the ;, which is optional
	      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
	   
	      // &#x0040 @ search for the hex values
	      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
	      // &#00064 @ 0{0,7} matches '0' zero to seven times
	      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
	   }
	   
	   // now the only remaining whitespace attacks are \t, \n, and \r
	   $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
	   $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
	   $ra = array_merge($ra1, $ra2);
	   
	   $found = true; // keep replacing as long as the previous round replaced something
	   while ($found == true) {
	      $val_before = $val;
	      for ($i = 0; $i < sizeof($ra); $i++) {
	         $pattern = '/';
	         for ($j = 0; $j < strlen($ra[$i]); $j++) {
	            if ($j > 0) {
	               $pattern .= '(';
	               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
	               $pattern .= '|';
	               $pattern .= '|(&#0{0,8}([9|10|13]);)';
	               $pattern .= ')*';
	            }
	            $pattern .= $ra[$i][$j];
	         }
	         $pattern .= '/i';
	         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
	         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
	         if ($val_before == $val) {
	            // no replacements were made, so exit the loop
	            $found = false;
	         }
	      }
	   }
	   return $val;
	} 

/*
 * function for check parameter if have no data($data=false) then return the return value
 * @param - Value of the parameter
 * @param - Return value
 */	
	function checkParameter($data=false, $return=false) {
		if(isset($data)&&$data){return $data;}	// for checking undified variable natt/16-05-2009
		else {return $return;}
	}

/*
 * function for check isset parameter if have not set data then return the return value
 * @param - Value of the parameter
 * @param - Return value
 * Modify : Ruck 15-05-2009
 */	
	function issetParameter($data, $return=false) {
		if(isset($data)){return $data;}
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
		//For check debug undefine index : __debug. By Ruck : 18-05-2009
		if(isset($_SESSION["__debug"])){
			return $_SESSION["__debug"];	
		}else{
			return false;
		}
		
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
 * function to generate rows limit value in database query that depend on page and limit of total record per page
 * @param - Page
 * @param - Total record per page
 */
	function getPagetolimit($page=0, $limit=false) {
 		if(!$limit) {$limit = $this->getShowpage();}
		if($page>0) {$page = $page - 1;}
		
		return $page*$limit;
 	}
	
/*
 * function to set data (send query string to mysql class)
 * @param - Query string A SQL Query
 */	
	function setResult($sql=false, $debug=false) {
		if(!$sql){
			$this->getErrorMsg("Plese check your query language!!");
			return false;
		}
		$m = new mysql($GLOBALS["global_database"],$GLOBALS["global_user"],$GLOBALS["global_pass"]);
		$id = $m->setdata($sql, $debug);

		$this->setlastinsertid($m->get_lastinsertid());
		$this->setAffectedrows($m->get_affectedrow());
		
		if($this->getDebugStatus()){$this->printDebug("cms.setResult()", false, $sql, $m->get_msg(), $m->__error);}
		if(!$id) {
			$this->setErrorMsg($m->get_msg());
			//$this->printDebug("cms.setResult()","update information not complete..",$sql,$m->get_msg(),$m->__error);
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
		$m = new mysql($GLOBALS["global_database"],$GLOBALS["global_user"],$GLOBALS["global_pass"]);
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
 * function for set data form xml command
 * @param - xml command
 * @patern of $xml command for setRsXML() method
 * $xml ="<command>".
 * "<sql>Query string A SQL Query</sql>".
 * "</command>";
 */	
	function setRsXML($xml, $debug=false) {
		$e = simplexml_load_string($xml);
		$sql = $e->sql;
		
		return $this->setResult($sql, $debug);
	}

/*
 * function for get data form resource xml command
 * @param - xml command
 * @patern1 of $xml command for getRsXML() method
 * $xml ="<command>".
 * "<sql>Query string A SQL Query</sql>".
 * "</command>";
 * @patern2 of $xml command for getRsXML() method
 * $xml ="<command>".
 * "<table>tablename that you need to get the information</table>".
 * "<field>fieldname defalut=*</field>".
 * "<order>column name that your want to sort, you can mix ASC and DESC ex. 'id desc'</order>".
 * "<page>add page to check if page>1</page>".
 * "<where name='fieldname' operator='query string operator'>scope value of field name</where>".
 * "<where logic='query string logical operator ex. AND/OR' name='fieldname' operator='query string operator'>scope value of field name</where>".
 * "</command>";
 */	
	function getRsXML($xml,$filename='object.xml', $debug=false) {
		$e = simplexml_load_string($xml);
		$f = simplexml_load_file($filename);
		
	// initial parameter from $xml => string command
	 	$table = $e->table;
	 	$sql = $e->sql;
	 	$field = $this->checkParameter($e->field, " * ");
	 	
	 	$where = $e->where;
	 	$status = $e->status;
	 	$order = $e->order;
	 	if($e->page){$page = $e->page;}
	 	else{$page = -1;}	
	 	
	// initial parameter from file object.xml
		$limit = $f->table->$table->showpage["value"];
		$aform = $f->table->$table;
		
	 	if($sql) {return $this->getResult($sql);}
	 	else {$sql = "select ".$field." from ".$table." ";}
	 	
	 	if($e->usejoin == "yes") {
	 		foreach ($aform->jointable as $jj) {
				$sql .= $jj["jointype"]." ".$jj["tablename"]." on ".$table.".".$jj["pkfield"]."=".$jj["tablename"].".".$jj["fgkfield"]." ";        
			}
	 	}
	 	
	 	$count = count($where);
	 	
	 	//Change this function for insert bracket in front statement of where. Example : where (...) AND ...
	 	//By Ruck : 26-05-2009. 
	 	$sqlWhere="";
	 	$i=0;
	 	if($where) {
			if($status=="search"){
				foreach($where as $wheres) {
					if($wheres["logic"]=="frontBracket"){
						$sqlWhere = "($sqlWhere)";
						$count--;
					}else{
						if($i<$count && $i!=0) {
							$sqlWhere.=" ".$wheres["logic"]." ";		
						}
						$sqlWhere .= "lower(".$wheres["name"].") ".htmlspecialchars($wheres["operator"]." '".strtolower($wheres)."' ");
						$i++;
					}	
				}
			} else {
				foreach($where as $wheres) {
					if($wheres["logic"]=="frontBracket"){
						$sqlWhere = "($sqlWhere)";
						$count--;
					}else{
						if($i<$count && $i!=0) {
								$sqlWhere.=" ".$wheres["logic"]." ";		
						}
						$sqlWhere .= $wheres["name"]." ".htmlspecialchars($wheres["operator"]." '$wheres' ");
						$i++;	
					}
				}
			}
			$sql.= "where ".$sqlWhere;
		}
		
	 	
	 	if($order) {
	 		$arrchktbname = explode(".",$order);
	 		if(count($arrchktbname)>1) {
	 			$sql .= "order by ".$order." ";
	 		}
	 		else {
	 			$sql .= "order by ".$table.".".$order." ";
	 		}
	 	}
	 	
	 	if($page > -1) {$sql .= "limit ".$this->getPagetolimit($page,$limit).",".$limit." ";}
	 	if($debug){echo $sql."<br/>";}
	 	return $this->getResult($sql, $debug);
	}

	/*
	 * function to sparate time and date to standards format
	 * @param $timeinput - time input that want to saperate
	 * @param $to - format time input 
	 * @param $needtime - if want adding time in time input
	 * @modified - modified this function on 12 dec 2008 
	 * 			 - add $needtime for set tim input
	 * 			 - add $to case 7
 	 */
 	function separate_time($timeinput=false,$to=false,$needtime=false) {
		$arrMonthNumbers = Array("Jan"=>0,"Feb"=>1,"Mar"=>2,"Apr"=>3,
    							"May"=>4,"Jun"=>5,"Jul"=>6,"Aug"=>7,
    							"Sep"=>8,"Oct"=>9,"Nov"=>10,"Dec"=>11);
    	$arrMonthNames = array_keys($arrMonthNumbers);
    	if($needtime){
    		$apTime = split(' ',$timeinput);
    		$dateinput = $apTime[0];
    		$timeinput = $apTime[1];
    	}else{
    		$dateinput = $timeinput;
    	}
    	
    	//print_r($apTime);
		if($to==1) {
			list($year, $month, $day) = split('[/.-]',$dateinput);
			$dateinput = $day."-".$month."-".$year;	
		}
		if($to==2) {
			list($day, $month, $year) = split('[/.-]',$dateinput);
			$dateinput = $year."-".$month."-".$day;	
		}
		if($to==3) {
			list($year,$month,$day) = split('[/.-]',$dateinput);
			$dateinput = date("y-m-d", mktime(0, 0, 0,$month,$day,$year));
     	}
		if($to==4) {
			list($year, $month, $day) = split('[/.-]',$dateinput);
			$dateinput = $year."".$month."".$day;	
		}
		if($to==5) {
			list($day, $month, $year) = split('[/.-]',$dateinput);
			$month = $arrMonthNumbers["$month"]+1;
			$dateinput = ($month>=10)?"$year-$month-$day":"$year-0$month-$day";	
		}
		if($to==6) {
			list($year, $month, $day) = split('[/.-]',$dateinput);
			$month = $arrMonthNames[$month-1];
			$dateinput = $day."-".$month."-".$year;	
		}
		if($to==7) {
			list($day, $month, $year) = split('[/.-]',$dateinput);
			$month = $arrMonthNumbers["$month"]+1;
			$dateinput = $day."-".$month."-".$year;	
		}
		if(!$to){
			list($day, $month, $year) = split('[/.-]',$timeinput);
			$dateinput = $year."".$month."".$day;	
		}	
		if($needtime){
			$timeinput = $dateinput." ".$timeinput;
			return $timeinput;
		}
		return $dateinput;	
		
	}

	/*
	 * function for change date pattern d-m-Y h:m:s to date
	 * @param $timeinput - time input that want to saperate
	 * @param $to - format php time input 
	 * @param $needtime - if want adding time in time input
	 * @modified - add this function on 20 dec 2008 
	 */
	function setTimePatternDY($timeinput=false,$to=false,$needtime=false){
		$arrMonthNumbers = Array("Jan"=>0,"Feb"=>1,"Mar"=>2,"Apr"=>3,
    							"May"=>4,"Jun"=>5,"Jul"=>6,"Aug"=>7,
    							"Sep"=>8,"Oct"=>9,"Nov"=>10,"Dec"=>11);
    	$arrMonthNames = array_keys($arrMonthNumbers);
    	if($needtime){
    		$apTime = split(' ',$timeinput);
    		$dateinput = $apTime[0];
    		$timeinput = $apTime[1];
    	}else{
    		$dateinput = $timeinput;
    	}
    	
    	list($day, $month, $year) = split('[/.-]',$dateinput);
    	if(!is_numeric($month)){$month = $arrMonthNumbers["$month"]+1;}
    	$returndate = date($to,mktime(0,0,0,$month,$day,$year));
    	if($needtime){
    		list($hour,$min,$sec) = split(':',$timeinput);
    		$returndate = date($to,mktime($hour,$min,$sec,$year,$month,$day));
    	}
    	
    	return $returndate;
    }

/*
 * function to get result set of user 
 * @param - Username
 * @param - Ignore user id
 */	
	function checkUser($user, $ignore=false) { // return result set of this user
		if(!$ignore)
	    	$sql = "select * from s_user where u = '$user' ";
	    else
	    	$sql = "select * from s_user where u = '$user' and u_id != $ignore";
	    
	    return $this->getResult($sql);
	}
	
/*
 * function to check id in table
 * @param - id
 * @param - tablename
 * @param - idfield
 * @modified - add this function on 18 dec 2008
 */	
	function checkIdInTable($id=false,$table=false,$index=false,$debug=false){
		if(!$id) {
			$this->setErrorMsg("No have ID $id in $table!!");
			return false;
		}
		
		$sql = "select * from ".$table." where ".$index."=".$id." limit 1";
		if($debug){
 			echo $sql."<br/>";
 			//return false;
 		}
		$row = $this->getResult($sql);
		if($row["rows"]>0){return $id;}else{return false;}
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
	
/*
 * function for generate next or last auto_increment id from table
 * @param - tablename
 * @param - idfield
 * @param - check value for get last or next id
 * @param - check sql value
 * @modified - add this function on 8 dec 2008
 */
	function getNextId($tablename=false,$index=false,$check=false,$debug=false) {
		if(!$tablename) {
			$this->set_errormsg("Please insert table name for get information!!");
			return false;
		}
		
		$sql = "select $index from $tablename order by $index desc limit 1";
		
		if($debug){
 			echo $sql."<br/>";
 			return false;
 		}
 			
		$row = $this->getResult($sql);
		 		
		if($check)
			return $row[0]["$index"];
		else
			return $row[0]["$index"]+1;
	}
	
/*
 * function for generate rows from table
 * @param - id
 * @param - tablename
 * @param - idfield
 * @param - check other condition
 * @modified - add this function on 19 dec 2008
 */
	function getRowFromId($id=false,$table=false,$index=false,$condition=false,$debug=false){
		$sql = "select $index from $table where $index=$id $condition";
		$rs = $this->getResult($sql);
		
		if($debug){echo $sql."<br/>";}
		
		if(!$rs){return 0;}
		else{return $rs["rows"];}
	}
/*
 * function hightLightChar()
 * @param - keyWord
 * @param - content
 * for hight light key word search
 * @modified - 08 April 2009 by Ruck
 */
	function hightLightChar($keyWord=false,$content=false){
		if(!$keyWord){
			return $content;
		}
		$content = str_replace("&amp;","&",$content);
		$content = str_replace("&gt;",">",$content);
		$content = str_replace("&lt;","<",$content);
		$content = str_replace("<br>","[br]",$content);
		
		//highlight character format - \1 mean wording that want to highlight 
		$highlight = '[span class="find-search"]\1[/span]';
		
		//normal character
    	$pattern = '#(%s)#i';
	    $sl_pattern = '#(%s)#i';
		//prevent html character
		/*$pattern = '#(?!<.*?)(%s)(?![^<>]*?>)#';
 		$sl_pattern = '#<a\s(?:.*?)>(%s)</a>#';*/
		
		// Case sensitivity
		$pattern .= 'i';
		$sl_pattern .= 'i';
		
		$keyWord = (array) $keyWord;
		foreach ($keyWord as $word) {
			$word = preg_quote($word);
			
        	// Escape needle with optional whole word check
			//$word = '\b' . $word . '\b';
			
			// Strip links
	        $sl_regex = $this->utf_8_sprintf($sl_pattern, $word);
	        $content = preg_replace($sl_regex, '\1', $content);
	        //$a.=$word;
			$regex = $this->utf_8_sprintf($pattern, $word);
			$content = preg_replace($regex, $highlight, $content);
				
		}
		$content = str_replace(">","&gt;",$content);
		$content = str_replace("<","&lt;",$content);
		$content = str_replace("[br]","<br>",$content);
		$content = str_replace('[span class="find-search"]','<span class="find-search">',$content);
		$content = str_replace('[/span]','</span>',$content);
		return $content;
	}
	
	function utf_8_sprintf ($format) {
 	 	$args = func_get_args();

 		for ($i = 1; $i < count($args); $i++) {
    		$args [$i] = iconv('UTF-8', 'TIS-620', $args [$i]);
 		}
 
  		return iconv('TIS-620', 'UTF-8', call_user_func_array('sprintf', $args));
	}
	
/*
 * function for generate date to date formath "Week, day month year"
 * @param - dateinput pattern "01-01-2008"
 * @param - set if want to add time format
 * @modified - add this function on 12 dec 2008
 */
	function getFootDate($dateinput,$needtime=false){
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
	
/*
 * function for detect repeat value in array return array that no repete value
 * @param - input array value
 * @modified - add this function on 03 apr 2009
 */
	function detectRepeatarr($arr){
		if(!is_array($arr)) {
			$this->getErrorMsg("cms.detectRepeatarr(), please check input parameter must be array value!!");
		}
		
		$tmp = array();
		$j=0;
		for($i=0;$i<count($arr);$i++){
			if(!in_array($arr[$i],$tmp)){
				$tmp[$j]=$arr[$i];
				$j++;
			}
		}
		
		return $tmp;
	}

/*
 * function for generate text page link for data table
 * @modified - move from report.inc.php natt/20-May-2009
 */
	function gen_page($url=false,$currentpage=false,$total_records=false,$records_per_page=false){
		if(!$records_per_page){$records_per_page=$this->getShowpage();}
		$page_count = $total_records / $records_per_page;
		$page_count = (is_int($page_count))?intval($page_count):intval($page_count)+1;
	
		/*echo '$currentpage: '.$currentpage.'<br /> 
		$total_records: '.$total_records.'<br />
		$records_per_page: '.$records_per_page.'<br />
		$page_count: '.$page_count.'<br /><br /><br />';*/
		//show 10 link. like 1-2-3-4-5-6-7-8-9-10
		$align_links_count=10;
		$max_link = ($page_count>$align_links_count)?$align_links_count:$page_count;
		
		$start_page = "$currentpage";
		$end_page = "$currentpage";
		
		//assign endpage and startpage for pagecount
		while($max_link>0){
			$looped = false;
			if(intval($end_page)<$page_count){
				$end_page++;
                $max_link--;
                $looped = true;
			}
			if($start_page>1&&$max_link!='0'){
				$start_page--;
                $max_link--;
                $looped = true;
			}
			if($looped==false){break;}
		}
		/*echo '$currentpage: '.$currentpage.'<br /> 
		$start_page: '.$start_page.'<br />
		$end_page: '.$end_page.'<br />
		$page_count: '.$page_count.'<br />';*/
		//assign array of number page
		$i=$start_page;
        while($i<=$end_page ){
            if("$i"=="$currentpage"){
                $pagearray[] = '<b>'.$i.'</b>';
            }else{
                $pagearray[] = $this->generate_link($i,$i,$url);
			}
            $i++;
        }
		//add back/forward ( '<' / '>' ) icon
		$use_back_forward = true;
		 if ($use_back_forward==true){
            if($currentpage=='1'){
        		$page_back = "< ";
        	}else{
                $page_back = $this->generate_link("<",($currentpage-1).'',$url).' ' ;
        	}
            if($currentpage>=$page_count){
				$page_fwd = " >";
			}else{
                $page_fwd = ' '.$this->generate_link(">",($currentpage+1).'',$url);
			}
        }
		
		//add back/forward ( '<<' / '>>' ) icon
		$use_first_last = true;
		 if ($use_first_last==true){        
            
            //make the first page url
            if ($currentpage==$start_page){
            	$page_first = '<< ';
            }else{
                $page_first = $this->generate_link('<<','1',$url).' ';
            }
            
            //make the last page url
            if ($currentpage==$end_page){
            	$page_last = ' >>';
            }else{
                $page_last = ' '.$this->generate_link('>>',"$page_count",$url) ;
            }
        }
		
		$textout = implode(' ',$pagearray);
		$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
		$textout .= " / Total ".$page_count."<br /><br />";
		echo $textout;
	}
	
	
/*
 * function for generate text page link for data table
 * @modified - move from report.inc.php natt/20-May-2009
 */	
	function generate_link($inner,$page_number,$url){
		$textout = '';
		$textout.= "<a href=\"javascript:;\" onclick=\"sortInfo('',$page_number)\" class=\"pagelink\">$inner</a>\n";
		
		/*$textout.= "<a href=\"javascript:;\" onclick=\"getReturnText('$url','page=$page_number".
		(($_GET["where"])?"&where=".$_GET["where"]:"").
		(($_GET["begin"])?"&begin=".$_GET["begin"]:"").
		(($_GET["end"])?"&end=".$_GET["end"]:"").
		(($_GET["date"])?"&date=".$_GET["date"]:"").
		(($_GET["cityid"])?"&cityid=".$_GET["cityid"]:"").
		(($_GET["cdfunc"])?"&cdfunc=".$_GET["cdfunc"]:"").
		"','tableDisplay')\" class=\"pagelink\">$inner</a>\n";*/
		return $textout;
	}
	
/*
 * function for convert specific character for searching in mysql quary language
 * @modified - add this function by natt/04-08-2009
 */	
	function convert_char($content){
		$content = str_replace("&","&amp;",$content);
		$content = str_replace(">","&gt;",$content);
		$content = str_replace("<","&lt;",$content);
		$content = str_replace('"',"&quot;",$content);
		return $content;
	}
}
?>
