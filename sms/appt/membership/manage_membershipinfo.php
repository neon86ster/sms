<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

$member_code=$obj->getParameter("memberId");
$old_member_code=$obj->getParameter("oldmemberId");
if($member_code!="" && $member_code!=0){
	$member_id = $obj->getIdToText($member_code,"m_membership","member_id","member_code","");
	if($member_id>0&&$member_code==$old_member_code){
		header("Location: history_membership.php?pageid=1&memberId=$member_code");		
	}else{
		// for return to the same page 
		$search=$obj->getParameter("search",$member_code);
		$categoryid=$obj->getParameter("categoryid",0);
		$order=$obj->getParameter("order","member_code");
		$sort=$obj->getParameter("sort","asc");
		$page = $obj->getParameter("page",1);
		$successmsg = $obj->getParameter("msg","");
		$searchstr=str_replace("+","%2B",$search);
		$searchstr=str_replace("&","%26",$searchstr);
		$querystr = "pageid=1&search=$searchstr&sort=$sort&order=$order&page=$page&categoryid=$categoryid";
		
		header("Location: index.php?$querystr");	
	}
}else{
		// for return to the same page 
		$search=$obj->getParameter("search",$member_code);
		$categoryid=$obj->getParameter("categoryid",0);
		$order=$obj->getParameter("order","member_code");
		$sort=$obj->getParameter("sort","asc");
		$page = $obj->getParameter("page",1);
		$successmsg = $obj->getParameter("msg","");
		$searchstr=str_replace("+","%2B",$search);
		$searchstr=str_replace("&","%26",$searchstr);
		$querystr = "pageid=1&search=$searchstr&sort=$sort&order=$order&page=$page&categoryid=$categoryid";
		
		header("Location: index.php?$querystr");	
}	
?>