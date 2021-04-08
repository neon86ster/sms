<?php
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

$pagename = "cscheck.php";
$obj->setDebugStatus(false);
$csphone=$obj->getParameter("csphone");
$book_id=$obj->getParameter("book_id");
$page = $obj->getParameter("page",1);
$records_per_page = 10;
$limit = ($page-1)*$records_per_page;
$rs=$obj->getCSHistory($csphone,$limit,$records_per_page);
$error="";
if($rs["rows"]<1){$error="PhoneNumber not found!!";}
// manage page
$and = "";
$chkrs=$obj->getCSHistory($csphone);


//
$chkcsphone = str_replace("+","%2B","$csphone");
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
		$pagearray[] = '<a href=\'javascript:;\' onclick="gotoURL(\'cscheck.php?page='.$i.'&book_id='.$book_id.'&csphone='.$chkcsphone.'\')">'.$i.'</a>'."\n";
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
        $page_back = '<a href=\'javascript:;\' onclick="gotoURL(\'cscheck.php?page='.$i.'&book_id='.$book_id.'&csphone='.$chkcsphone.'\')">&lt;</a> '."\n";
    }
    if($currentpage>=$page_count){
		$page_fwd = " >";
	}else{
		$i = $currentpage+1;
        $page_fwd = ' <a href=\'javascript:;\' onclick="gotoURL(\'cscheck.php?page='.$i.'&book_id='.$book_id.'&csphone='.$chkcsphone.'\')">&gt;</a>'."\n";
	}
}
//add back/forward ( '<<' / '>>' ) icon
$use_first_last = true;
if ($use_first_last==true){        
          
    //make the first page url
    if ($currentpage==$start_page){
       	$page_first = '<< ';
    }else{
        $page_first = '<a href=\'javascript:;\' onclick="gotoURL(\'cscheck.php?page=1&book_id='.$book_id.'&csphone='.$chkcsphone.'\')">&lt;&lt;</a> '."\n";
    }
            
    //make the last page url
    if ($currentpage==$end_page){
      	$page_last = ' >>';
    }else{
        $page_last = ' <a href=\'javascript:;\' onclick="gotoURL(\'cscheck.php?page='.$page_count.'&book_id='.$book_id.'&csphone='.$chkcsphone.'\')">&gt;&gt;</a>'."\n";
    }
}

$textout = implode(' ',$pagearray);
$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
$textout .= " / Total ".$page_count."<br /><br />";
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Check Booking Agent</title>
<link href="../../css/style.css" rel="stylesheet" type="text/css">
<script src="../scripts/component.js" type="text/javascript"></script>
<script src="../scripts/ajax.js" type="text/javascript"></script>
<!-- Begin Code Data Chooser -->
<link rel="stylesheet" type="text/css" href="../scripts/datechooser/datechooser.css">
<body><br>
<br/>
<div class="group5" width="100%" >
<fieldset>
<legend><b>Seach by Customer's Phone Number : <font class="style1">'<?=$csphone?>'</font></b></legend>
<form action="<?=$pagename?>" method="post">
<div id="showerrormsg" <? if($error==""){?>style="display:none"<? } else {?>style="display:block"<? }?>>
    <table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    	<tr>
    		<td ><b><font style="color:#ff0000;">Error message: </font></b><?=$error?></td>
    	</tr>
    </table>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cusinfo">
  <tr>
    <td colspan="2">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="comment">
    	<tr>
          <td class="mainthead">Booking ID</td>
          <td class="mainthead">Member Code</td>
          <td class="mainthead">Customer name</td>
          <td class="mainthead">Phone Number</td>
          <td class="mainthead">Appointment Date</td>
          <td class="mainthead">Branch</td>
          <td class="mainthead">No. of People</td>
          <td class="mainthead">Add</td>
        </tr>
  		<?
			for($i=0;$i<$rs["rows"];$i++){
				$trclass = ($i%2==0)?"content_list":"content_list1";
				$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
				$pagename = "manageBooking".$rs[$i]["book_id"];
				$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
		?>
        <tr class='<?=$trclass?>'>
        	<input type="hidden" name="tbname<?=$i?>" id="tbname<?=$i?>" value="<?=$rs[$i]["tbname"]?>"/>
        	<? 
        		if($rs[$i]["resident"]==1){$chkresident="resident";}
        		else if($rs[$i]["visitor"]==1){$chkresident="visitor";}
        		else{$chkresident="noset";}
        		$set_cms = "false";
        		if($rs[$i]["set_cms"]){
					$today = date("Ymd");
					$apptdate = $rs[$i]["b_appt_date"];
					$sum = abs(strtotime($today)-strtotime($apptdate)) / 86400;
					if($sum <= 14){
						$set_cms = "true";
					}
        		}
        	?>
        		<input type="hidden" name="cs_email<?=$i?>" id="cs_email<?=$i?>" value="<?=$rs[$i]["cs_email"]?>"/>
        		<input type="hidden" name="cs_nationality<?=$i?>" id="cs_nationality<?=$i?>" value="<?=$rs[$i]["nationality_id"]?>"/>
        		<input type="hidden" name="cs_sex<?=$i?>" id="cs_sex<?=$i?>" value="<?=$rs[$i]["sex_id"]?>"/>
        		<input type="hidden" name="cs_bday<?=$i?>" id="cs_bday<?=$i?>" value="<?=($rs[$i]["cs_birthday"]=="0000-00-00")?"":$dateobj->convertdate($rs[$i]["cs_birthday"],"Y-m-d",$sdateformat)?>"/>
        		<input type="hidden" name="cs_hiddenbday<?=$i?>" id="cs_hiddenbday<?=$i?>" value="<?=$rs[$i]["cs_birthday"]?>"/>
        		<input type="hidden" name="resident<?=$i?>" id="resident<?=$i?>" value="<?=$chkresident?>"/>
        		<input type="hidden" name="set_cms<?=$i?>" id="set_cms<?=$i?>" value="<?=$set_cms?>"/>
          <td><a href='javascript:;;' onClick="newwindow('/appt/<?=$url?>','<?=$pagename?>')" class="menu"><?=$bpdsid?></a></td>
          <td><?=($rs[$i]["member_code"])?$rs[$i]["member_code"]:"-"?><input type="hidden" name="member_code<?=$i?>" id="member_code<?=$i?>" value="<?=$rs[$i]["member_code"]?>"/></td>
          <td><?=$rs[$i]["cs_name"]?><input type="hidden" name="cs_name<?=$i?>" id="cs_name<?=$i?>" value="<?=$rs[$i]["cs_name"]?>"/></td>
          <td><?=$obj->hightLightChar($csphone,$rs[$i]["cs_phone"])?><input type="hidden" name="cs_phone<?=$i?>" id="cs_phone<?=$i?>" value="<?=$rs[$i]["cs_phone"]?>"/></td>
          <td><?=$dateobj->convertdate($rs[$i]["b_appt_date"],"Y-m-d",$sdateformat)?></td>
          <td><?=$rs[$i]["branch_name"]?></td>
          <td><?=$rs[$i]["b_qty_people"]?></td>
          <td><input type="button" name="bpAdd" id="bpAdd" value="Add" class="button" href="javascript:;" onClick="editCS('<?=$i?>');"/></td>
        </tr>
 		<? } ?>
      </table>
    </td>
  </tr>
  <tr>
    <td align="center" style="height:50px;"><?=$textout?></td>
  </tr>
</table>
<input type="hidden" name="book_id" value="<?=$book_id?>">
</form>
<br>
</fieldset>
</body>
