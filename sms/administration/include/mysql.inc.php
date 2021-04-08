<?php
/*
 * File name : mysql.inc.php
 * Description : Class file which use to make a database connection
 * Author : art
 * Create date : 21-Oct-2008
 * Modified : natt@chiangmaioasis.com
 */    
class mysql {
	protected $_user; 			// username use to connect the database
	protected $_pass; 		// password use to connect the database
	protected $_host;			// hostname use for connect the database
	protected $_dbs;			// database name;
	protected $_connection;		// keep connection after connect
	protected $_msg;			// msg to tell developer
	protected $_recordcount;	// number of rows after get the result from database connection
	protected $_lastinsertid;	// the ID that generate form MySQL insert operation
	protected $_affectedrows;	// number of affected rows in MySQL operation
	var $__error;				// number of error msg form mysql connection
	
/*
 * Constructor of class mysql 
 */
	function mysql($dbname="tap_smscore",$user="root",$pass="123456"){
		$this->__error = false;
		if(isset($_SESSION["_mysql_host"])) {$this->_host = $_SESSION["_mysql_host"];} 
		else {$this->_host = "localhost";}
		
		$this->_host = "localhost";
		$this->_user = "$user";
		$this->_pass = "$pass";
		$this->_dbs = "$dbname";
		$this->_msg = "";
		$this->_recordcount = 0;
		$this->_lastinsertid = 0;
	}
	
/*
 * function to set and get database name
 * @param - Database name
 */	
	function setDbs($newdbs) {
		$this->_dbs = $newdbs;
	}
	
	function getDbs() {
		return $this->_dbs;
	}
	
/*
 * function to connect MySQL database
 * @param - Debugging database connection
 */
	function connect($debug=false) {		// need to disconnect function getResult() on another method
		$conn = @mysql_connect($this->_host,$this->_user,$this->_pass);
		$this->_connection = $conn;
		if(!$conn){
			$this->msg .= @mysql_error();
			if($debug){$this->msg .= @mysql_error($conn);}
			
			$conn = @mysql_connect("localhost","root","pakin3223");
			
			if(!$conn) {
				$this->msg .= @mysql_error();
				$conn = @mysql_connection("127.0.0.1","root","pakin32");
			} else {$_SESSION["_mysql_host"] = "localhost";}
			
			if(!$conn){$this->msg .= @mysql_error();} 
			else {$_SESSION["_mysql_host"] = "127.0.0.1";}
			
		} else {$_SESSION["_mysql_host"] = $this->_host;}
		
		if(!$conn){echo $this->getMsg();}
		
		return $conn;
	}
	
/*
 * function to close MySQL Connection
 * @param - MySQL connection
 */	
	function disconnect($conn) {
		mysql_close($conn);
	}

/*
 * function to set data (insert/update/delete data form the database)
 * @param - Query string A SQL Query
 * @param - Debugging database connection
 */
	function setdata($SQL, $debug=false) {
		$this->__error = false;
		$conn = $this->connect($debug);
		$this->_connection = $conn;
		$this->_msg = "";
		
		mysql_select_db($this->_dbs, $conn);
		mysql_query("set names utf8");		//set every text to UTF8 support all language
		$rs = mysql_query($SQL, $conn);
		
		$delete = eregi("^delete", $SQL);
		$update = eregi("^update", $SQL);
		$insert = eregi("^insert", $SQL);
		
		if($delete || $update) {
			$this->_affectedrows = mysql_affected_rows($conn);
			if($this->_affectedrows < 0) {return false;}
			
			if($this->_affectedrows == 0) {return 1;}
			else {return $this->_affectedrows;}
		}
		
		if($insert) {
			$this->_lastinsertid = mysql_insert_id($conn);
			return $this->_lastinsertid;
		}
		
		if(!$rs) {
			$this->_msg = mysql_error($conn);
			$this->__error = mysql_errno($conn);
		}
		$this->disconnect($conn);
		return $rs;
	}
	
/*
 * function to get resource form database
 * @param - Query string A SQL Query
 * @param - Debugging database connection
 * @param - Set true if don't want query result form this method (will return $rows=false)
 * @param - 
 */
	function getdata($SQL, $debug=false, $notrows=false, $result=true) {
		$this->__error = false;
		$conn = $this->connect($debug);		// connect to database
		
		$this->_connection = $conn;
		$this->_recordcount = 0;
		$this->_msg = "";
		$rs = false;
		
		mysql_select_db($this->_dbs, $conn);
		$this->_msg .= mysql_error();
		mysql_query("set names utf8");
		$rs = mysql_query($SQL, $conn);
		
		if(!$rs) {
			$this->__error = @mysql_errno();
			$this->_msg .= mysql_error($conn);
			$this->disconnect($conn);
		}
		
		if($rs) {
			$this->_recordcount = mysql_num_rows($rs);
			if(intval($this->_recordcount) <= 0) {
				$this->_msg .= "Is Empty..";
				return false;
			}
			
			for($i=0; $i<intval($this->_recordcount); $i++) {
				if(!$notrows) {$rows[$i] = mysql_fetch_array($rs);}
			}
			if(!$notrows) {$rows["rows"] = $this->_recordcount;}
			
		} else {return false;}
			
		
		$this->disconnect($conn);
		return $rows;		
	}
	
/*
 * function to get number of rows in a result
 * @param - Query string A SQL Query 
 */
	function get_recordcount($SQL=false){
		if(!$SQL) {return $this->_recordcount;}
		
		$this->getdata($SQL);
		return $this->_recordcount;
	}
	
/*
 * function to get an error msg form database connection
 */
	function get_msg() {
		return $this->_msg;
	}

/*
 * function to set and get affected rows in MySQL operation
 */	
	function get_affectedrow() {
		return $this->_affectedrows;
	}
	
/*
 * function to get ID generate form MySQL insert operation
 */
	function get_lastinsertid() {
		return $this->_lastinsertid;
	}
	
}

?>
