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
	$path = $_SERVER["DOCUMENT_ROOT"] . '/administration/include/';
else
	$path = $_SERVER["DOCUMENT_ROOT"] . '/administration/include/';

set_include_path(get_include_path() . PATH_SEPARATOR . $path);

//check server name access
/*if($_SERVER["SERVER_NAME"]!="www.spaprogram.net"){
	die();
}*/

//security setting
require_once ("secure.inc.php");
$object = new secure();
if (!$object->checkLogin()) {
	
?>
		<script>
			parent.location = "login.php";
		</script>
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

require_once ("main.inc.php");
$obj = new main();
$fontcolor = "#5197bb";
$theme = "classic";
?>