<?php
if(!session_id()){
	session_start();
}
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

/*
 * Created on Oct 20, 2008
 * initial root path for include file
 */

if (isset ($_SERVER["WINDIR"]) || isset ($_SERVER["windir"]))
	$path = $_SERVER["DOCUMENT_ROOT"] . '/include/';
else
	$path = $_SERVER["DOCUMENT_ROOT"] . '/include/';

set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once ("config.php");
//security setting
require_once ("secure.inc.php");
$object = new secure();
if (!$object->checkLogin()) {
	$querystr = "";
	$bookid = $object->getParameter("bookid");
	if ($bookid) {
		$querystr = "?bookid=$bookid";
	}
	$pdsid = $object->getParameter("pdsid");
	if ($pdsid) {
		$querystr = "?pdsid=$pdsid";
	}
	$rmid = $object->getParameter("rmid");
	if ($rmid) {
		$querystr = "?rmid=$rmid";
	}
?>
		<script>parent.location = "/login.php<?=$querystr?>";</script>
<?

}

$permissionrs = $object->get_upage();
$pageid = $object->getParameter("pageid", "0");
$pagestatus = $object->check_permission($pageid, $permissionrs);
$chkPageEdit = false;
$chkPageView = false;
if ($pagestatus == "e") {
	$chkPageEdit = true;
	$chkPageView = true;
} else
	if ($pagestatus == "v") {
		$chkPageEdit = false;
		$chkPageView = true;
	} else
		if ($pagestatus == "n") {
			$chkPageEdit = false;
			$chkPageView = false;
		}

$pageinfo = $object->get_pageinfo($pageid, $permissionrs);
// check specialist admin who can add branch/room
$isAdminExpert = $object->isAdminExpert();

// Prevent any possible XSS attacks via $_GET.
// modified on June 18,2009 by natt change $_GET to $_REQUEST line 64
foreach ($_REQUEST as $check_url) {
	if (!is_array($check_url)) {
		$check_url = strtolower($check_url);
		if ((eregi("<[^>]*script*\"?[^>]*>", $check_url)) || (eregi("<[^>]*object*\"?[^>]*>", $check_url)) || (eregi("<[^>]*iframe*\"?[^>]*>", $check_url)) || (eregi("<[^>]*applet*\"?[^>]*>", $check_url)) || (eregi("<[^>]*meta*\"?[^>]*>", $check_url)) || (eregi("<[^>]*style*\"?[^>]*>", $check_url)) || (eregi("<[^>]*form*\"?[^>]*>", $check_url)) //|| (eregi("\([^>]*\"?[^)]*\)", $check_url)) ||
		//(eregi("\"", $check_url))
		) {
			die();
		}
	}
}

//for theme/color of web interface
require_once ("appt.inc.php");
$obj = new appt();
$obj->setErrorMsg("");
$pagename = "mainPage.php";
$obj->setDebugStatus(false);

$themeid = $obj->getIdToText("1", "a_company_info", "theme", "company_id");
$fontcolor = $obj->getIdToText($themeid, "l_theme", "theme_color", "theme_id");
$theme = strtolower($obj->getIdToText($themeid, "l_theme", "theme_name", "theme_id"));

// system date format	 	
require_once ("date.inc.php");

$chksql = "select long_date,short_date from a_company_info";
$chkrs = $obj->getResult($chksql);
$sdateformat = $obj->getIdToText($chkrs[0]["short_date"], "l_date", "date_format", "date_id");
$ldateformat = $obj->getIdToText($chkrs[0]["long_date"], "l_date", "date_format", "date_id");
$dateobj = new convertdate();

// system customize images
$webuserarr = explode(".", $_SESSION["SERVER_NAME"]);
$customize_part = "/clients/" . $webuserarr[0];
$customize_img = $_SERVER["DOCUMENT_ROOT"] . "/clients/" . $webuserarr[0];


?>