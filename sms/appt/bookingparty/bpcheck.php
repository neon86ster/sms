<?php
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

//For debug undefined index : . By Ruck : 20-05-2006
$page = $obj->getParameter("page","1");
$error="";

$pagename = "bpcheck.php";
$obj->setDebugStatus(false);
$bpphone=$obj->getParameter("bpphone");
$chkbpphone = str_replace("+","%2B","$bpphone");
if($bpphone!="" && $bpphone!=0){
	$chksql = "select c_bp_phone from `a_bookinginfo` where c_bp_phone=$bpphone";
	$chkrs = $obj->getResult($chksql);
	if($chkrs["rows"]>0){
		$link= "bpphone=$chkbpphone";
		header("Location: history_bp.php?$link");		
	}
}
$book_id=$obj->getParameter("book_id");
// For check active button add in this page //
$active = $obj->getParameter("active");
//==========================================//
$records_per_page = 10;
$limit = ($page-1)*$records_per_page;
$sql="select * from `al_bankacc_cms` where `bankacc_active`=1 ";
$sql.="and lower(`c_bp_phone`) like '%".strtolower($bpphone)."%'";
$sql.=" order by `c_bp_person` limit $limit,$records_per_page";
$rs=$obj->getResult($sql);
if($rs["rows"]<1){$error="Please check Booking Agent phone number!!";}
// manage page
$chksql="select `bankacc_cms_id` from `al_bankacc_cms` where `bankacc_active`=1 ";
$chksql="select * from `al_bankacc_cms` where `bankacc_active`=1 ";
$chksql.="and lower(`c_bp_phone`) like '%".strtolower($bpphone)."%'";
$chkrs=$obj->getResult($chksql);
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
		$pagearray[] = '<a href=\'javascript:;\' onclick="gotoURL(\'bpcheck.php?page='.$i.'&book_id='.$book_id.'&bpphone='.$chkbpphone.'&active='.$active.'\')">'.$i.'</a>'."\n";
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
        $page_back = '<a href=\'javascript:;\' onclick="gotoURL(\'bpcheck.php?page='.$i.'&book_id='.$book_id.'&bpphone='.$chkbpphone.'&active='.$active.'\')">&lt;</a> '."\n";
    }
    if($currentpage>=$page_count){
		$page_fwd = " >";
	}else{
		$i = $currentpage+1;
        $page_fwd = ' <a href=\'javascript:;\' onclick="gotoURL(\'bpcheck.php?page='.$i.'&book_id='.$book_id.'&bpphone='.$chkbpphone.'&active='.$active.'\')">&gt;</a>'."\n";
	}
}
//add back/forward ( '<<' / '>>' ) icon
$use_first_last = true;
if ($use_first_last==true){        
          
    //make the first page url
    if ($currentpage==$start_page){
       	$page_first = '<< ';
    }else{
        $page_first = '<a href=\'javascript:;\' onclick="gotoURL(\'bpcheck.php?page=1&book_id='.$book_id.'&bpphone='.$chkbpphone.'&active='.$active.'\')">&lt;&lt;</a> '."\n";
    }
            
    //make the last page url
    if ($currentpage==$end_page){
      	$page_last = ' >>';
    }else{
        $page_last = ' <a href=\'javascript:;\' onclick="gotoURL(\'bpcheck.php?page='.$page_count.'&book_id='.$book_id.'&bpphone='.$chkbpphone.'&active='.$active.'\')">&gt;&gt;</a>'."\n";
    }
}
$textout = implode(' ',$pagearray);
$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
$textout .= " / Total ".$page_count."<br /><br />";
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Check Booking Agent</title>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<script src="../scripts/component.js" type="text/javascript"></script>
<script src="../scripts/ajax.js" type="text/javascript"></script>
<!-- Begin Code Data Chooser -->
<link rel="stylesheet" type="text/css" href="../scripts/datechooser/datechooser.css">
<body><br>
<br/>
<div class="group5" width="100%" >
<fieldset>
<legend><b><? if($book_id){?>Book ID : <font class="style1"><?=$obj->getIdToText($book_id,"c_bpds_link","bpds_id","tb_id","tb_name=\"a_bookinginfo\"")?></font><?}else{?>Add Booking<?} ?></b></legend>
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
          <td class="mainthead">PhoneNumber</td>
          <td class="mainthead">Name</td>
          <td class="mainthead">Company Name</td>
          <? if($active==1){?>
          <td class="mainthead">Add</td>
          <?}?>
        </tr>
  		<?
			for($i=0;$i<$rs["rows"];$i++){
				$trclass = ($i%2==0)?"content_list":"content_list1";
		?>
        <tr class='<?=$trclass?>'>
          <td><?=$obj->hightLightChar($bpphone,$rs[$i]["c_bp_phone"])?></td>
          <td><?=$rs[$i]["c_bp_person"]?></td>
          <td><?=($rs[$i]["tb_name"]=="al_bookparty")?$obj->getIdToText($rs[$i]["c_bp_id"],"al_bookparty","bp_name","bp_id"):$obj->getIdToText($rs[$i]["c_bp_id"],"al_accomodations","acc_name","acc_id")?></td>
          <? if($active==1){?>
          <td><input type="button" name="bpAdd" id="bpAdd" value="Add" class="button" href="javascript:;" onClick="editAgent('<?=$rs[$i]["c_bp_phone"]?>','<?=$rs[$i]["c_bp_person"]?>','<?=($rs[$i]["tb_name"]=="al_bookparty")?$rs[$i]["c_bp_id"]:1?>');"/></td>
          <?}?>
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
