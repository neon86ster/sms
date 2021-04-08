<?
// Report all errors except E_NOTICE
 error_reporting(E_ALL);
include("include.php");

$i = count($pageinfo["parent"])-1;
$pageinfo["pagename"] = "Online Users";

$pagename = "currentuser.php";
$obj->setDebugStatus(false);

$branchid=$obj->getParameter("branch_id","1");
$search=$obj->getParameter("search","");
$order=$obj->getParameter("order","p_userlist.login_time");
$sort=$obj->getParameter("sort","desc");
$page = $obj->getParameter("page","1");

$successmsg = "";
$errormsg = "";
$userunlockid = $obj->getParameter("userid");
if($userunlockid){
	$id = $object->setUser("logout",$userunlockid);
	if($id){$successmsg="Unlock User Success!!";}
	else{$errormsg="Can not un lock this user,Please check again!!";}
} 

$records_per_page = 15;
$limit = ($page-1)*$records_per_page;
$rs=$object->getUser(0,$branchid,$search,$limit,$records_per_page,"$order $sort");

// manage page
$chkrs=$object->getUser();
$page_count = $chkrs["rows"] / $records_per_page;
$page_count = (is_int($page_count))?intval($page_count):intval($page_count)+1;
$align_links_count=10;
$max_link = ($page_count>$align_links_count)?$align_links_count:$page_count;
$start_page = "$page";
$end_page = "$page";
$currentpage = "$page";
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
$i=$start_page;
while($i<=$end_page ){
    if("$i"=="$currentpage"){
 		$pagearray[] = '<b>'.$i.'</b>';
	}else{
		$pagearray[] = '<a href=\'javascript:;\' onclick="sortInfo(\'\','.$i.',\''.$pagename.'\')" class="pagelink">'.$i.'</a>'."\n";
	}
	$i++;
}
//add back/forward ( '<' / '>' ) icon
$use_back_forward = true;
if ($use_back_forward==true){
 	if($currentpage=='1'){
		$page_back = "< ";
    }else{
    	$i = $currentpage-1;
        $page_back = '<a href=\'javascript:;\' onclick="sortInfo(\'\','.$i.',\''.$pagename.'\')" class="pagelink">&lt;</a> '."\n";
    }
    if($currentpage>=$page_count){
		$page_fwd = " >";
	}else{
		$i = $currentpage+1;
        $page_fwd = ' <a href=\'javascript:;\' onclick="sortInfo(\'\','.$i.',\''.$pagename.'\')" class="pagelink">&gt;</a>'."\n";
	}
}
//add back/forward ( '<<' / '>>' ) icon
$use_first_last = true;
if ($use_first_last==true){        
          
    //make the first page url
    if ($currentpage==$start_page){
       	$page_first = '<< ';
    }else{
        $page_first = '<a href=\'javascript:;\' onclick="sortInfo(\'\',1,\''.$pagename.'\')" class="pagelink">&lt;&lt;</a> '."\n";
    }
            
    //make the last page url
    if ($currentpage==$end_page){
      	$page_last = ' >>';
    }else{
        $page_last = ' <a href=\'javascript:;\' onclick="sortInfo(\'\','.$page_count.',\''.$pagename.'\')" class="pagelink">&gt;&gt;</a>'."\n";
    }
}

$textout = implode(' ',$pagearray);
$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
$textout .= " / Total ".$page_count."<br /><br />";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Online Users</title>
<link href="/css/styles.css" rel="stylesheet" type="text/css" />
<script src="scripts/ajax.js" type="text/javascript"></script>
<script src="scripts/components.js" type="text/javascript"></script>
<script type="text/javascript">

/////////// sort information in Online Users /////////////////////
function sortInfo(order,page,url){
	var search = "";
	if(document.getElementById('search')!=null){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	 }
	 	
	var branch = "";
	if(document.getElementById('branch_id')!=null){
	 	branch = "&branch_id="+document.getElementById("branch_id").value;}
	 	
	var sort = document.getElementById("sort").value;
	
	if(order==""){
		order = "&order="+document.getElementById("order").value;
	}else{
		order = "&order="+order;
		if(sort=="desc"){sort="asc";}else{sort="desc";}
	}
	
	sort = "&sort="+sort;
	var querystr = search+branch+sort+order;
	if(page==""){
		page = 0;
	}
	if(url=="undefined"||url==null){
		url = "index.php";
	}
	querystr = "?"+querystr+"&page="+page;
	location.href=url+querystr;
}
</script>
</head>
<body>
<div id="loading" style="display:none;">
<table cellspacing="0" cellpadding="0" class="preloading">
<tr>
    <td align="center" valign="middle">
		<img src="/images/sms preload.png">
	</td>
</tr>
</table>
</div> 
<form id="curcust" name="curcust" action="<?=$_SERVER["PHP_SELF"]?>" method="get">
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
			        Branch: <? 	$obj->makeListbox("branch_id","bl_branchinfo","branch_name","branch_id",$branchid,false,"branch_name","branch_active","1",0,0,0,0,0,"sortInfo('',1,'$pagename')"); ?>
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
     					<a href="javascript:;" onClick="document.getElementById('page').value='1';document.curcust.submit();"><img src="/images/<?=$theme?>/search.png" alt="search" border="0"/></a>
        			    <a href="javascript:;" onClick="document.getElementById('page').value='1';document.getElementById('search').value='';document.curcust.submit();"><img src="/images/<?=$theme?>/view.png" alt="view all" border="0"/></a> 
        			    <a href="javascript:;" onClick="document.getElementById('page').value='1';document.curcust.submit();"><img src="/images/<?=$theme?>/refresh.png" alt="refresh" border="0"/></a>&nbsp; 
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
	 	<? if($errormsg!=""){ ?><b class="errormsg" style="font-size:10px;">-&nbsp;&nbsp;<?=$errormsg?></b>&nbsp;<img src="/images/errormsg.png" />&nbsp;<? } ?>
	 	</td>
    	<td align="right" height="30px" class="rheader">
    		<table border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td class="rheader" height="30" style="margin-buttom: 5px;">
			        <? echo $chkrs["rows"];
			        ?> Total Records &nbsp;
			        </td>
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
			<div id="tableDisplay">
 <!-- begin div tableDisplay -->			
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%">
			<table cellspacing="0" border="0" cellpadding="0" width="100%" class="generalinfo">
				<tr height="32">
<?
$arrFields = array("s_user.u",
				"p_userlist.login_time",
				"p_userlist.login_ip",
				"bl_branchinfo.branch_name",
				"");
				
$arrFieldsname = array("User Name","Login Time","Login IP","Branch","Unlock");
				
$chkarrFields = array("u","login_time","login_ip","branch_name",
				"u_id");
				
$column = count($arrFields);

//start field name generate
for($i=0;$i<$column;$i++){
		if($order==$arrFields[$i]){ 
			$style = "background-color:#88afbe;" .
					  "background-image: url('/images/arrow_down.png');" .
					  "border-bottom: 3px solid #eae8e8";
		}else{
		 	$style = "background-color:#a8c2cb;";
		}
?>
					<td style="text-align:center;<?=$style?>">
					<a href="javascript:;" onClick="sortInfo('<?=$arrFields[$i]?>',<?=$page?>,<?="'$pagename'"?>)" class="pagelink">
					<b><?=$arrFieldsname[$i]?></b>
					</a>
					</td>
<? 	
} 
?>	
				</tr>
<?
//end field name generate
//start field element generate
$data = "&nbsp;";
for($i=0;$i<$rs["rows"];$i++){
	$trclass = ($i%2==0)?"height=\"20\" style=\"background-color:#d3d3d3;\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"":"height=\"20\" style=\"background-color:#eaeaea;\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";
	echo "<tr class=\"$trclass\" height=\"20\">\n";
	
	for($j=0;$j<$column;$j++){
				$data = "";$align = "";
					
				if($chkarrFields[$j]=='b_appt_date'){
						$data = $dateobj->convertdate($rs[$i]["$chkarrFields[$j]"],"Y-m-d",$sdateformat);
				}else if($chkarrFields[$j]=='login_time'){
						$login_time = explode(" ",$rs[$i]["login_time"]);
						$data = $dateobj->timezonefilter($login_time[0],$login_time[1],$sdateformat." H:i:s");
				}else if($chkarrFields[$j]=='u'||$chkarrFields[$j]=='login_ip'){
						$data = $obj->hightLightChar($search,$rs[$i]["$chkarrFields[$j]"]);
				}else if($chkarrFields[$j]=='u_id'){
						$align = "align=\"center\""; 
						$data = "<input type=\"button\" name=\"unlock[".$rs[$i]["$chkarrFields[$j]"]."]\" value=\" Unlock \" onClick=\"unlockUser('".$rs[$i]["$chkarrFields[$j]"]."','".$order."',$page,'$pagename');\">";
				}else {$data = $rs[$i]["$chkarrFields[$j]"];}
?>
			<td class="report" <?=$align?>><?=$data?>&nbsp;</td>
<?	}
	echo "</tr>";
}
if(!$rs["rows"]){}
?>
 			</table><br/>
		</td>
    </tr>
    <tr>
    	<td width="100%" align="center">
    		<font class="pagelink"><?=$textout?></font>
    	</td>
	</tr>
</table>
			
			
 <!-- end div tableDisplay -->			
			</div>
		</td>
   </tr>
</table>
</form>