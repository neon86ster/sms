<?php
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

$pagename = "twcheck.php";
$error = "";
$obj->setDebugStatus(false);
$twphone=$obj->getParameter("twphone");
$chktwphone = str_replace("+","%2B","$twphone");
if($twphone!="" && $twphone!=0){
	$chksql = "select cs_phone from `d_indivi_info` where cs_phone=$twphone";
	$chkrs = $obj->getResult($chksql);
	if($chkrs["rows"]>0){
		$link= "twphone=$chktwphone";
		header("Location: history_tw.php?$link");		
	}
}

$book_id=$obj->getParameter("book_id");
$page = $obj->getParameter("page",1);
$records_per_page = 10;
$limit = ($page-1)*$records_per_page;
$rs=$obj->getTWHistory($twphone,$limit,$records_per_page);
if($rs["rows"]<1){$error="PhoneNumber not found!!";}
// manage page
$and = "";
$chkrs=$obj->getTWHistory($twphone);


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
		$pagearray[] = '<a href=\'javascript:;\' onclick="gotoURL(\'twcheck.php?page='.$i.'&book_id='.$book_id.'&twphone='.$chktwphone.'\')">'.$i.'</a>'."\n";
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
        $page_back = '<a href=\'javascript:;\' onclick="gotoURL(\'twcheck.php?page='.$i.'&book_id='.$book_id.'&twphone='.$chktwphone.'\')">&lt;</a> '."\n";
    }
    if($currentpage>=$page_count){
		$page_fwd = " >";
	}else{
		$i = $currentpage+1;
        $page_fwd = ' <a href=\'javascript:;\' onclick="gotoURL(\'twcheck.php?page='.$i.'&book_id='.$book_id.'&twphone='.$chktwphone.'\')">&gt;</a>'."\n";
	}
}
//add back/forward ( '<<' / '>>' ) icon
$use_first_last = true;
if ($use_first_last==true){        
          
    //make the first page url
    if ($currentpage==$start_page){
       	$page_first = '<< ';
    }else{
        $page_first = '<a href=\'javascript:;\' onclick="gotoURL(\'twcheck.php?page=1&book_id='.$book_id.'&twphone='.$chktwphone.'\')">&lt;&lt;</a> '."\n";
    }
            
    //make the last page url
    if ($currentpage==$end_page){
      	$page_last = ' >>';
    }else{
        $page_last = ' <a href=\'javascript:;\' onclick="gotoURL(\'twcheck.php?page='.$page_count.'&book_id='.$book_id.'&twphone='.$chktwphone.'\')">&gt;&gt;</a>'."\n";
    }
}

$textout = implode(' ',$pagearray);
$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
$textout .= " / Total ".$page_count."<br /><br />";
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Search Customer by Phone Number</title>
<link href="../../css/style.css" rel="stylesheet" type="text/css">
<script src="../scripts/component.js" type="text/javascript"></script>
<script src="../scripts/ajax.js" type="text/javascript"></script>
<body><br>
<br/>
<div class="group5" width="100%" >
<fieldset>
<legend><b><? if($book_id){?>History of customer's <?}else{?>Seaching customer's <?} ?>Phone Number : <font class="style1">'<?=$twphone?>'</font></b></legend>
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
          <td class="mainthead">Book ID</td>
          <td class="mainthead">Customer name</td>
          <td class="mainthead">Phone Number</td>
          <td class="mainthead">Appointment Date</td>
          <td class="mainthead">Branch</td>
          <td class="mainthead">Qty People</td>
          <td class="mainthead">History</td>
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
        	<? if($rs[$i]["tbname"]=="d_indivi_info"){ 
        		if($rs[$i]["resident"]==1){$chkresident="resident";}
        		else if($rs[$i]["visitor"]==1){$chkresident="visitor";}
        		else{$chkresident="noset";}
        	?>
        		<input type="hidden" name="cs_email<?=$i?>" id="cs_email<?=$i?>" value="<?=$rs[$i]["cs_email"]?>"/>
        		<input type="hidden" name="cs_nationality<?=$i?>" id="cs_nationality<?=$i?>" value="<?=$rs[$i]["nationality_id"]?>"/>
        		<input type="hidden" name="cs_sex<?=$i?>" id="cs_sex<?=$i?>" value="<?=$rs[$i]["sex_id"]?>"/>
        		<input type="hidden" name="cs_bday<?=$i?>" id="cs_bday<?=$i?>" value="<?=($rs[$i]["cs_birthday"]=="0000-00-00")?"":$dateobj->convertdate($rs[$i]["cs_birthday"],"Y-m-d",$sdateformat)?>"/>
        		<input type="hidden" name="cs_hiddenbday<?=$i?>" id="cs_hiddenbday<?=$i?>" value="<?=$rs[$i]["cs_birthday"]?>"/>
        		<input type="hidden" name="resident<?=$i?>" id="resident<?=$i?>" value="<?=$chkresident?>"/>
        	<? } ?>
          <td><a href='javascript:;;' onClick="newwindow('/appt/<?=$url?>','<?=$pagename?>')" class="menu"><?=$bpdsid?></a></td>
          <td><?=$rs[$i]["cs_name"]?><input type="hidden" name="cs_name<?=$i?>" id="cs_name<?=$i?>" value="<?=$rs[$i]["cs_name"]?>"/></td>
          <td><?=$obj->hightLightChar($twphone,$rs[$i]["cs_phone"])?><input type="hidden" name="cs_phone<?=$i?>" id="cs_phone<?=$i?>" value="<?=$rs[$i]["cs_phone"]?>"/></td>
          <td><?=$dateobj->convertdate($rs[$i]["b_appt_date"],"Y-m-d",$sdateformat)?></td>
          <td><?=$rs[$i]["branch_name"]?></td>
          <td><?=$rs[$i]["b_qty_people"]?></td>
          <td><a href="javascript:;" onclick="window.open('history_tw.php?twphone=<?=$rs[$i]["cs_phone"]?>','cshistory',
									'location=0,toolbar=0,directoris=0,status=0,menubar=0,scrollbars=1,resizable=0,width=900,height=400');">history</td>
          <td><input type="button" name="bpAdd" id="bpAdd" value="Add" class="button" href="javascript:;" onClick="editTW('<?=$i?>');"/></td>
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
