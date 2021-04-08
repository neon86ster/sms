<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
$obj = new report(); 
$obj->setDebugStatus(false);

// for return to the same page 
$search=$obj->getParameter("search","");
$categoryid=$obj->getParameter("categoryid",0);
$order=$obj->getParameter("order","member_code");
$sort=$obj->getParameter("sort","asc");
$page = $obj->getParameter("page",1);
$successmsg = $obj->getParameter("msg","");
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
$querystr = "pageid=$pageid&search=$searchstr&sort=$sort&order=$order&page=$page&categoryid=".$categoryid;

/***************************************************
 * Security checking
 ***************************************************/
// check user edit permission 
$pageid = "1";	// appointment page
$pagestatus = $object->check_permission($pageid,$permissionrs);
$chkPageEdit=false;$chkPageView=false;
if($pagestatus=="e"){
	$chkPageEdit=true;$chkPageView=true;
}else if($pagestatus=="v"){
	$chkPageEdit=false;$chkPageView=true;
}else if($pagestatus=="n"){
	$chkPageEdit=false;$chkPageView=false;
}

$pageinfo = $object->get_pageinfo(1,$permissionrs);
$i = count($pageinfo["parent"]);
$pageinfo["parenturl"][$i] = $pageinfo["pageurl"];
$pageinfo["parent"][$i] = $pageinfo["pagename"];
$pageinfo["pagename"] = "Membership";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="/scripts/components.js"></script>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<script type="text/javascript" src="../scripts/component.js"></script>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="getReturnText('report.php','<?=$querystr?>','tableDisplay');">
<div id="loading" style="display:none;">
<table cellspacing="0" cellpadding="0" class="preloading">
<tr>
    <td align="center" valign="middle">
		<img src="/images/sms preload.png">
	</td>
</tr>
</table>
</div> 
<form name="membership" id="membership" action="" method="post" style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" height="100%" width="100%">
  <tr>
    <td height="110" valign="top">
<div id="header">
<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" colspan="2" align="center" height="49">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="leftmenu">
				<tr>
					<td height="47" style="background-image: url('/images/<?=$theme?>/header.png');">
									 <table height="47" border="0" cellpadding="0" cellspacing="0" style='overflow:auto;' class="dir">
								         <tbody>
									         <tr><td>
									         <? for($i=0;$i<count($pageinfo["parent"]);$i++){ ?>
									         <a href="javascript:;" target="mainFrame"><?=$pageinfo["parent"][$i]?> &gt</a>
									         <? } ?>
									          </td></tr>
									         <tr><td><b><?=$pageinfo["pagename"]?></b></td></tr>
								         </tbody>
									 </table>
			 			<input type="hidden" id="pageid" name="pageid" value="1">
					</td>
				</tr>
				<tr>
					<td height="2" background="#eae8e8"><img src="/images/blank.gif" height="2px"></td>
				</tr>
			</table>
		</td>
	  </tr>
 	<tr>
    	<td valign="top" height="30px" colspan="2" class="rheader">
    		<table border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td class="rheader" height="30" style="padding-left: 20px;">
			        Category: <?=$obj->makeListbox("categoryid","all_mb_category","category_name","category_id",$categoryid,0,"category_name","category_active","1",0,0,0,"searchInfo('1');")?>
			        <input type="hidden" name="page" id="page" value="<?=$page?>">
			        <input type="hidden" name="sort" id="sort" value="<?=$sort?>">
			        <input type="hidden" name="order" id="order" value="<?=$order?>">&nbsp;
			        </td><td class="rheader">
						<img src="/images/<?=$theme?>/appt/separate.png">
					</td><td class="rheader">
						&nbsp;&nbsp;Search: &nbsp;
						<input type="text" name="search" id="search" <?=($search)?"value='".$search."'":""?>/>
					</td><td class="rheader">
     					&nbsp;&nbsp;
        			    <a href="javascript:;" onClick="sortInfo('<?=$order?>','1','index.php')"><img src="/images/<?=$theme?>/search.png" alt="search" border="0"/></a>
        			    <a href="javascript:;" onClick="document.getElementById('search').value='';sortInfo('<?=$order?>','1','index.php')"><img src="/images/<?=$theme?>/view.png" alt="view all" border="0"/></a> 
        			    <a href="javascript:;" onClick="sortInfo('<?=$order?>','<?=$page?>','index.php')"><img src="/images/<?=$theme?>/refresh.png" alt="refresh" border="0"/></a>&nbsp; 
        			</td>
			       </tr>
    		</table>
  		</td>
	</tr>
	<tr>
		 <td height="1" colspan="2" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
	</tr>
 	<tr>
	 	<td height="30px" class="rheader" style="padding-left: 20px;">
	 	<?=$pageinfo["pagename"]?> Information 
	 	&nbsp;<? if($successmsg!=""){ ?><b class="successmsg" style="font-size:10px;">-&nbsp;&nbsp;<?=$successmsg?></b>&nbsp;<img src="/images/successmsg.png" />&nbsp;<? } ?>
	 	</td>
    	<td align="right" height="30px" class="rheader">
    		<table border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td class="rheader" height="30" style="margin-buttom: 5px;">
			        <?	
			        	$sql = "select member_id from m_membership ";
			        	$search = strtolower($search);
						$searchsql = $obj->convert_char($search);
						// specific $search don't care about category select
			        	if($categoryid){
			        		$sql .= "where category_id=$categoryid " .
			        				"and (lower(member_code) like '%$searchsql%' " .
			        				"or lower(fname) like '%$searchsql%' " .
			        				"or lower(mname) like '%$searchsql%' " .
			        				"or lower(lname) like '%$searchsql%' " .
			        				"or lower(birthdate) like '%$searchsql%' " .
			        				"or REPLACE(mobile,\"-\",\"\") like '%$searchsql%') ";
			        	}else{
			        		$sql .= "where lower(member_code) like '%$searchsql%' " .
			        				"or lower(fname) like '%$searchsql%' " .
			        				"or lower(mname) like '%$searchsql%' " .
			        				"or lower(lname) like '%$searchsql%' " .
			        				"or lower(birthdate) like '%$searchsql%' " .
			        				"or REPLACE(mobile,\"-\",\"\") like '%$searchsql%' ";
			        	}
			        	$rs = $obj->getResult($sql);
			        	echo $rs["rows"]+0;
			        ?> Total Records &nbsp;
			        </td><td class="rheader">
						<img src="/images/<?=$theme?>/appt/separate.png">&nbsp;
					</td>
  	<? if($chkPageEdit){?>
					<td class="rheader" style="padding: 2 2 2 0;">
						<table border="0" cellspacing="0" cellpadding="0" style="cursor: pointer;" onClick="window.open('add_membershipinfo.php','add_membership',
						'height=650,width=350,resizable=0,scrollbars=1');">
						<tr>
							<td height="30px" class="rheader" style="padding-left: 20px;color: <?=$fontcolor?>;background-color:#a8c2cb;">
								Add New Member
							</td>
							<td style="padding-left: 20px;padding-right: 20px;background-color:#a8c2cb;"><img src="/images/<?=$theme?>/add.png"></td>
						</tr>
						</table> 
					</td>
	<? } ?>
					<td class="rheader">&nbsp; </td>
			       </tr>
    		</table>
  		</td>
	</tr>
	<tr>
		 <td height="1" colspan="2" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
	</tr>
 </table> 
</div>
  	</td>
  </tr>
  <tr>
  		<td valign="top" style="margin-top:0px;margin-left:0px;padding-left:0px;">
			<div id="tableDisplay"></div>
		</td>
   </tr>
</table>
</form> 
</body>
</html>