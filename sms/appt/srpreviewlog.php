<?php
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("logs.inc.php");
$obj = new logs(); 

$pagename = "srpreviewlog.php";
$obj->setDebugStatus(false);
$sr_id=$obj->getParameter("sr_id");
$page = $obj->getParameter("page",1);

$records_per_page = 10;
$limit = ($page-1)*$records_per_page;
$rs=$obj->getSrPrintHis($sr_id,$limit,$records_per_page);
if($rs["rows"]<1){$error="PhoneNumber not found!!";}
// manage page
$and = "";
$chkrs=$obj->getSrPrintHis($sr_id);


//
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
		$pagearray[] = '<a href=\'javascript:;\' onclick="gotoURL(\'srpreviewlog.php?page='.$i.'&sr_id='.$sr_id.'\')">'.$i.'</a>'."\n";
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
        $page_back = '<a href=\'javascript:;\' onclick="gotoURL(\'srpreviewlog.php?page='.$i.'&sr_id='.$sr_id.'\')">&lt;</a> '."\n";
    }
    if($currentpage>=$page_count){
		$page_fwd = " >";
	}else{
		$i = $currentpage+1;
        $page_fwd = ' <a href=\'javascript:;\' onclick="gotoURL(\'srpreviewlog.php?page='.$i.'&sr_id='.$sr_id.'\')">&gt;</a>'."\n";
	}
}
//add back/forward ( '<<' / '>>' ) icon
$use_first_last = true;
if ($use_first_last==true){        
          
    //make the first page url
    if ($currentpage==$start_page){
       	$page_first = '<< ';
    }else{
        $page_first = '<a href=\'javascript:;\' onclick="gotoURL(\'srpreviewlog.php?page=1&sr_id='.$sr_id.'\')">&lt;&lt;</a> '."\n";
    }
            
    //make the last page url
    if ($currentpage==$end_page){
      	$page_last = ' >>';
    }else{
        $page_last = ' <a href=\'javascript:;\' onclick="gotoURL(\'srpreviewlog.php?page='.$page_count.'&sr_id='.$sr_id.'\')">&gt;&gt;</a>'."\n";
    }
}

$textout = implode(' ',$pagearray);
$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
$textout .= " / Total ".$page_count."<br /><br />";
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>History of Sales Receipt</title>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<script src="scripts/component.js" type="text/javascript"></script>
<script src="scripts/ajax.js" type="text/javascript"></script>
<body><br>
<br/>
<div class="group5" width="100%" >
<fieldset>
<legend><b>History of Sales Receipt No. : <font class="style1"><?=$obj->getIdToText($sr_id,"c_salesreceipt","salesreceipt_number","salesreceipt_id")?></font></b></legend>
<form action="<?=$pagename?>" method="post">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cusinfo">
  <tr>
    <td colspan="2">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="comment">
    	<tr height="24">
          <td class="mainthead" style="vertical-align: middle;text-align: center">Printed by</td>
          <td class="mainthead" style="vertical-align: middle;text-align: center">Printed Time</td>
          <td class="mainthead" style="vertical-align: middle;text-align: center">Printed IP</td>
          <td class="mainthead" style="vertical-align: middle;text-align: center">Reprinted</td>
        </tr>
  		<?
			for($i=0;$i<$rs["rows"];$i++){
				$trclass = ($i%2==0)?"content_list":"content_list1";
				list($date,$time) = explode(" ",$rs[$i]["l_lu_date"]);
		?>
        <tr class='<?=$trclass?>'>
          <td style="text-align: center"><?=$rs[$i]["user"]?></td>
          <td style="text-align: center"><?=$dateobj->timezonefilter($date,$time,"$sdateformat h:i A")?></td>
          <td style="text-align: center"><?=$rs[$i]["l_lu_ip"]?></td>
          <td style="text-align: center"><?=($rs[$i]["reprint_times"])?$rs[$i]["reprint_times"]:"-"?></td>
        </tr>
 		<? } ?>
      </table>
    </td>
  </tr>
  <tr>
    <td align="center" style="height:50px;"><?=$textout?></td>
  </tr>
</table>
</form>
<br>
</fieldset>
</body>
