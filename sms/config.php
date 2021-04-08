<?php
/*
 * Created on Sep 21, 2009
 *
 * Config all client's differenct variable   
 */
$root = $_SERVER["DOCUMENT_ROOT"];
require_once("$root/include/mysql.inc.php");
 
// when 1 st open program 
if(!isset($_SESSION["SERVER_NAME"])){
	$_SESSION["SERVER_NAME"] = $_SERVER["SERVER_NAME"];
	
	//$m = new mysql("tap_smscore","root","qwerty");
	
	/* Config for server
	 * $m = new mysql("tap_smscore","tapsmScoR3","=5q\+XXij7ELnL!");*/
	$m = new mysql("tap_smscore","tap_smscore","=5q\+XXij7ELnL!"); 
	$sql = "select * from p_clientconfig, p_clientinfo, l_timezone " .
			"where p_clientinfo.client_id = p_clientconfig.client_id " .
			"and p_clientinfo.client_url = '".$_SESSION["SERVER_NAME"]."' " .
			"and p_clientinfo.timezone = l_timezone.timezone_id " .
			"and p_clientinfo.active = 1 ";
	//echo $sql;
	$rs = $m->getdata($sql);
	unset($m);
		
	if($rs){
		$_SESSION["global_database"] = $rs[0]["database_name"];
		$_SESSION["global_user"] = $rs[0]["database_user"];
		$_SESSION["global_pass"] = $rs[0]["database_pass"];
		$_SESSION["global_gifttypeid"] = $rs[0]["global_gifttypeid"];
		$_SESSION["global_payid"] = $rs[0]["global_payid"];
		$_SESSION["global_admingroupuser"] = $rs[0]["global_admingroupuser"];
		$_SESSION["global_oasisclient"] = $rs[0]["global_oasisclient"];
		$_SESSION["global_timezone"] = $rs[0]["gmt"];
	}
	
} 
/*else{
		$_SESSION["global_database"] = "tap10_oasis";
		$_SESSION["global_user"] = "root";
		$_SESSION["global_pass"] = "qwerty";
		$_SESSION["global_gifttypeid"] = 11;
		$_SESSION["global_payid"] = 11;
		$_SESSION["global_admingroupuser"] = 1;
		$_SESSION["global_oasisclient"] = 0;
}	*/	

if($_SESSION["SERVER_NAME"]!=$_SERVER["SERVER_NAME"]){
	session_destroy();
	$_SESSION["SERVER_NAME"] = $_SERVER["SERVER_NAME"];
	$root = "https://".$_SERVER["SERVER_NAME"]."/login.php";	
	?>
	<script>parent.location = "<?=$root?>";</script>
	<?	
}

// initial some value for global information id
if(isset($_SESSION["global_database"])){
	$global_oasisclient = $_SESSION["global_oasisclient"];
	$global_database = $_SESSION["global_database"];
	$global_user = $_SESSION["global_user"];
	$global_pass = $_SESSION["global_pass"];
	$global_gifttypeid = $_SESSION["global_gifttypeid"];
	$global_payid = $_SESSION["global_payid"];
	$global_admingroupuser = $_SESSION["global_admingroupuser"];

}else{
	die();
}
?>
