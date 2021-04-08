<?
include("../include.php");

$pageinfo = $object->get_pageinfo(1,$permissionrs);

$i = count($pageinfo["parent"]);
$pageinfo["parenturl"][$i] = $pageinfo["pageurl"];
$pageinfo["parent"][$i] = $pageinfo["pagename"];
$pageinfo["pagename"] = "Current Customers";

$pagename = "currentcust.php";
$obj->setDebugStatus(false);

$branchid=$obj->getParameter("branch_id","1");
$search=$obj->getParameter("search","");
$order=$obj->getParameter("order","a_bookinginfo.book_id");
$sort=$obj->getParameter("sort","asc");
$page = $obj->getParameter("page","1");

$records_per_page = 15;
$limit = ($page-1)*$records_per_page;
$rs=$obj->getcurrentcust($branchid,$search,$limit,$records_per_page,"$order $sort");

// manage page
$chkrs=$obj->getcurrentcust($branchid,$search);
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
		$pagearray[] = '<a href=\'javascript:;\' onclick="sortInfo(\'\','.$i.',\''.$pagename.'\')">'.$i.'</a>'."\n";
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
        $page_back = '<a href=\'javascript:;\' onclick="sortInfo(\'\','.$i.',\''.$pagename.'\')">&lt;</a> '."\n";
    }
    if($currentpage>=$page_count){
		$page_fwd = " >";
	}else{
		$i = $currentpage+1;
        $page_fwd = ' <a href=\'javascript:;\' onclick="sortInfo(\'\','.$i.',\''.$pagename.'\')">&gt;</a>'."\n";
	}
}
//add back/forward ( '<<' / '>>' ) icon
$use_first_last = true;
if ($use_first_last==true){        
          
    //make the first page url
    if ($currentpage==$start_page){
       	$page_first = '<< ';
    }else{
        $page_first = '<a href=\'javascript:;\' onclick="sortInfo(\'\',1,\''.$pagename.'\')">&lt;&lt;</a> '."\n";
    }
            
    //make the last page url
    if ($currentpage==$end_page){
      	$page_last = ' >>';
    }else{
        $page_last = ' <a href=\'javascript:;\' onclick="sortInfo(\'\','.$page_count.',\''.$pagename.'\')">&gt;&gt;</a>'."\n";
    }
}

$textout = implode(' ',$pagearray);
$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
$textout .= " / Total ".$page_count."<br /><br />";
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?
 $browser = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    if ($browser == true){
    	$browser = 'iphone';
  	?>
  	<meta name="viewport" content="width=device-width,minimum-scale=1.0, maximum-scale=1.0" />
  	<?
  	}
?>
<title>Current Customer</title>
<link href="/css/styles.css" rel="stylesheet" type="text/css" />
<script src="scripts/ajax.js" type="text/javascript"></script>
<script src="scripts/component.js" type="text/javascript"></script>
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
						<?if(!isset($parent)){$parent="";}?>
			 			<input type="hidden" id="parent" name="parent" value="<?=$parent?>">
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
	 	<?if(!isset($successmsg)){$successmsg="";}?>
	 	&nbsp;<? if($successmsg!=""){ ?><b class="successmsg" style="font-size:10px;">-&nbsp;&nbsp;<?=$successmsg?></b>&nbsp;<img src="/images/successmsg.png" />&nbsp;<? } ?>
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
$arrFields = array("a_bookinginfo.book_id",
				"a_bookinginfo.b_appt_date",
				"bl_branchinfo.branch_name",
				"a_bookinginfo.b_appt_time_id",
				"d_indivi_info.cs_name",
				"d_indivi_info.cs_phone",
				"al_bookparty.bp_name",
				"a_bookinginfo.c_bp_person",
				"a_bookinginfo.c_bp_phone");
				
$arrFieldsname = array("Booking ID","Appointment Date","Branch",
				"Appointment Time","Customer name","Phone Number",
				"Booking Company","Booking Person","B.P. PH #");
				
$chkarrFields = array("book_id","b_appt_date","branch_name",
				"appt_time","cs_name","cs_phone",
				"bp_name","bp_person","bp_phone");
				
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
	$trclass = ($i%2==0)?"odd":"even";
	if($rs[$i]["finish_status"]){
			$csstatus = "finish";
	}else if($rs[$i]["inroom_status"]){
			$csstatus = "inroom";
	}else if($rs[$i]["atspa_status"]){
			$csstatus = "atspa";
	}else{
			$csstatus = "$trclass";
	}
	echo "<tr class=\"$csstatus\" height=\"20\">\n";
	
	for($j=0;$j<$column;$j++){
				$data = "";
				if($chkarrFields[$j]=='book_id'){
						$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
						$pagename = "manageBooking".$rs[$i]["book_id"];
						$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
						
						$data = "<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\" style=\"font-size:13px; font-family:Tahoma; \" >$bpdsid</a>";
				}else if($chkarrFields[$j]=='b_appt_date'){
						$data = $dateobj->convertdate($rs[$i]["$chkarrFields[$j]"],"Y-m-d",$sdateformat);
				}else if($chkarrFields[$j]=='appt_time'){
						$data = substr($rs[$i]["$chkarrFields[$j]"],0,5);
				}else if($chkarrFields[$j]=='cs_name'||$chkarrFields[$j]=='cs_phone'||$chkarrFields[$j]=='bp_name'||$chkarrFields[$j]=='bp_phone'){
						$data = $obj->hightLightChar($search,$rs[$i]["$chkarrFields[$j]"]);
				}else {$data = $rs[$i]["$chkarrFields[$j]"];}
?>
			<td class="report"><?=$data?>&nbsp;</td>
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