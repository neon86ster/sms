<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb();
$object->setDebugStatus(false);
$filename = "../object.xml";
$branchid=$obj->getParameter("branchid");
$order=$obj->getParameter("order","order");
$sort=$obj->getParameter("sort","asc");
$page = $obj->getParameter("page",1);
$formname = $obj->getParameter("formname");

$querystr = "&pageid=$pageid&sort=$sort&order=$order&page=$page&branchid=$branchid";
$add = $obj->getParameter("add");
if($chkPageEdit && $add == " save change "){
	$queue = $obj->getParameter("th_queue");
	$th_queue = explode(",",$queue);
	$sql = "";
	$errormsg = "";
	for($i=0;$i<count($th_queue);$i++){
		if($th_queue[$i] != ''){
			$sql = "update bl_th_list set queue_order=".($i+1)." " .
					"where th_id = ".$th_queue[$i]." " .
						"and branch_id = $branchid " .
						"and l_lu_date >=\"" . date("Y-m-d") . "\"; ";
			$id = $obj->setResult($sql);
			if(!$id){
				$errormsg = $obj->getErrorMsg();
				$errormsg .=$querystr;
				break;
			}
		}		
	}	

	if(!$errormsg){
		$successmsg="Update data complete!!";
		$successmsg.=$querystr;
		header("Location: index.php?msg=$successmsg");
	}else{
		header("Location: index.php?errormsg=$errormsg");
		
	}
}else{
		$errormsg = "Please check information before update therapist queue!!";
		$errormsg .=$querystr;
		header("Location: index.php?errormsg=$errormsg");
}

?>