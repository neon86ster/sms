<?
ini_set("memory_limit","-1");
?>
<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

require_once("search.inc.php");
require_once("formdb.inc.php");


$branchid = $obj->getParameter("branchid");
$date = $obj->getParameter("date");	


$obf = new formdb(); 
$obj = new search();
$obj->setDebugStatus(false);

$error="";
$url="";

$csSearch=$obf->getParameter("csSearch");
$chkcsSearch = str_replace("+","%2B","$csSearch");
$chkcsSearch = str_replace("&","%26",$chkcsSearch);
$bcsSearch=$obf->getParameter("bcsSearch");
$batSearch=$obf->getParameter("batSearch");
$memSearch=$obf->getParameter("memSearch");

$camSearch=$obf->getParameter("camSearch");
$chkcamSearch = str_replace("+","%2B","$camSearch");
$chkcamSearch = str_replace("&","%26",$chkcamSearch);
$bmarketing=$obf->getParameter("bmarketing");
$bgiftcer=$obf->getParameter("bgiftcer");
$hotelSearch=$obf->getParameter("hotelSearch");

$data["cs[branch]"] = $branchid;
$data["cs[apptdate]"] = $date;
$data["cs[hidden_apptdate]"] = $dateobj->convertdate($date,$sdateformat,'Ymd');

if($bcsSearch||$batSearch||$memSearch){
	$camSearch="";
}else{
	$csSearch="";	
}

$qstatus = $obj->getParameter("qstatus");
if(!$qstatus){
	$pagename = "quick_search.php?date=".$date."&branchid=".$branchid;
}else{
	$pagename = "quick_search.php?qstatus=1";	
}

$page = $obf->getParameter("page",1);
$records_per_page = 15;
$limit = ($page-1)*$records_per_page;
?>
<?
  $isiPad = strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
  	if ($isiPad == true){
  		$isiPad='ipad';
  	?>
  	<style>
  	<!--
	@media only screen and (device-width: 768px) {
	  /* For general iPad layouts */
	}
	
	@media only screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait) {
	  /* For portrait layouts only */
	}
	
	@media only screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape) {
	  /* For landscape layouts only */
	}
  	-->
  	</style>
  	<?
  	}
  $browser = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    if ($browser == true){
    	$browser = 'iphone';
  	?>
  	<meta name="viewport" content="width=device-width,minimum-scale=1.0, maximum-scale=1.0" />
  	<?
  	}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Quick Search</title>


<input type="hidden" id="branchid" name="branchid" value="<?=$branchid?>"/>
<input type="hidden" id="date" name="date" value="<?=$date?>">

<link href="/css/style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="/css/style.css" />
<script src="scripts/ajax.js" type="text/javascript"></script>
<script src="scripts/component.js" type="text/javascript"></script>

<!-- Begin Code Data Chooser -->
<body><br>
<div class="group5" width="100%" >
<fieldset>
<legend><b>Quick Search : <font class="style1">

<script language="JavaScript">

function disableEnterKey(e)
{
     var key;     
     if(window.event)
          key = window.event.keyCode; //IE
     else
          key = e.which; //firefox     

     return (key != 13);
}

</script> 

</font></b></legend>

<?

  //calculate years of age (input string: YYYY-MM-DD)
 
   function birthday ($birthday)
  {
    list($year,$month,$day) = explode("-",$birthday);
    $year_diff  = date("Y") - $year;
    $month_diff = date("m") - $month;
    $day_diff   = date("d") - $day;
    if ($month_diff < 0) $year_diff--;
    elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
    return $year_diff;
  }
  
?>

<form action="<?=$pagename?>" method="post" name="<?=$pagename?>">
<table width="550" border="0" cellpadding="3" cellspacing="0" >
  <tr>
    <td width="48%" height="40" valign="center" align="left" colspan="2"><span style="margin-left:40px;">Bookings:</span></td>
    <td width="4%" align="center" style="border-left: 1px solid #cccccc;">&nbsp;</td>
    <td width="48%" align="left" colspan="2"><span style="margin-left:25px;">System Campaign Information:</span></td>
  </tr>
  <tr>
    <td width="24%" align="left"><input type='text' name="csSearch" id="csSearch" <?=($csSearch)?"value='".$csSearch."'":""?> size='22' onKeyPress="return disableEnterKey(event);"/></td>
    <td width="24%" align="left"><input title="When clicking this button you will be searching for the Customer name, Phone Number and/or e-mail from the Treatment Information Area." 
    type="submit" name="bcsSearch" id="bcsSearch" value="Customer" class="button" /></td>
    <td width="4%"  align="center" style="border-left: 1px solid #cccccc;">&nbsp;</td>
    <td width="24%" align="left"><input name="camSearch" type="text" id="camSearch" <?=($camSearch)?"value='".$camSearch."'":""?> size="22" maxlength="25" onKeyPress="return disableEnterKey(event);"/></td>
    <td width="24%" align="left"><input title="When clicking this button you will be searching for the Marketing Code using the Description, Company Name or Contract Person." type="submit" name="bmarketing" id="bmarketing" value="Marketing Code" class="button" /></td>
  </tr>
  <tr>
    <td></td>
    <td align="left"><input title="When clicking this button you will be searching for the Agent's name and/or Phone number." type="submit" name="batSearch" id="batSearch" value="Agent" class="button" /></td>
    <td align="center" style="border-left: 1px solid #cccccc;">&nbsp;</td>
    <td></td>
    <td align="left"><input title="When clicking this button you will be searching for the Gift Certificate Information using the Gift ID#, Given to and Given From fields." type="submit" name="bgiftcer" id="bgiftcer" value="Gift Certificate" class="button" /></td>
  </tr>
  <tr>
    <td></td>
    <td align="left"><input title="When clicking this button you will be searching for the Membership Database by Member Name, Phone number, Member ID number and e-mail." 
    type="submit" name="memSearch" id="memSearch" value="Member" class="button" /></td>
    <td align="center" style="border-left: 1px solid #cccccc;">&nbsp;</td>
    <td></td>
    <td align="left"><input title="When clicking this button you will be searching for the Hotel Accomodation by Hotel Name, Contact person, Main Phone, Fax and e-mail." 
    type="submit" name="hotelSearch" id="hotelSearch" value="Hotel Accomodation" class="button" /></td>
  </tr>
</table>
</fieldset>
<br />	
<?if(($obf->getParameter("bcsSearch") and $obf->getParameter("csSearch"))){ ?>
<? ################ CUSTOMER ###############  

$obj->setDebugStatus(false);
$rs=$obj->getCustomer($csSearch,$limit,$records_per_page);

if($rs["rows"]<1){$error="Customer search not found!!";}
// manage page

$chkrs=$obj->getCustomer($csSearch);

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
 		$pagearray[] = "<b>".$i."</b>";
	}else{
		$pagearray[] = "<a href=".$pagename."&page=".$i."&csSearch=".$chkcsSearch."&bcsSearch=".$bcsSearch.">".$i."</a>"."\n";
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
    	 $page_back = "<a href=".$pagename."&page=".$i."&csSearch=".$chkcsSearch."&bcsSearch=".$bcsSearch.">&lt;</a>"."\n";
    }
    if($currentpage>=$page_count){
		$page_fwd = " >";
	}else{
		$i = $currentpage+1;
		$page_fwd = "<a href=".$pagename."&page=".$i."&csSearch=".$chkcsSearch."&bcsSearch=".$bcsSearch.">&gt;</a>"."\n";
	}
}
//add back/forward ( '<<' / '>>' ) icon
$use_first_last = true;
if ($use_first_last==true){        
    //make the first page url
    if ($currentpage==$start_page){
       	$page_first = "<< ";
    }else{
    	$page_first = "<a href=".$pagename."&page=1&csSearch=".$chkcsSearch."&bcsSearch=".$bcsSearch.">&lt;&lt;</a>"."\n";
    }
            
    //make the last page url
    if ($currentpage==$end_page){
      	$page_last = " >>";
    }else{
    	$page_last = "<a href=".$pagename."&page=".$page_count."&csSearch=".$chkcsSearch."&bcsSearch=".$bcsSearch.">&gt;&gt;</a>"."\n";
    }
}

$textout = implode(' ',$pagearray);
$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
$textout .= " / Total ".$page_count."<br /><br />";
?>
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
    	  <td colspan="8" ><b style="color:#4987a1;">Customer Search : </b><?=$csSearch;?></td>
    	  </tr>
    	<tr>
          <td class="mainthead">Booking ID</td>
          <td class="mainthead">Member Code</td>
          <td class="mainthead">Customer name</td>
          <td class="mainthead">Phone Number</td>
          <td class="mainthead">Birth Day</td>
          <td class="mainthead">Appointment Date</td>
          <td class="mainthead">Branch</td>
          <td class="mainthead">Package</td>
          <td class="mainthead">E-mail</td>
          <td class="mainthead">Add</td>
        </tr>
  		<?
			for($i=0;$i<$rs["rows"];$i++){
				$trclass = ($i%2==0)?"content_list":"content_list1";
				$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
				$pagename = "manageBooking".$rs[$i]["book_id"];
				$bpdsid=$obf->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
if($rs[$i]["cs_birthday"]=="0000-00-00"){
	$rs[$i]["cs_birthday"]="";
}
		?>
        <tr class='<?=$trclass?>'>
          <td><a href='javascript:;' onClick="newwindow('/appt/<?=$url?>','<?=$pagename?>')" class="menu"><?=$bpdsid?></a></td>
          <?if($obj->hightLightChar($csSearch,$rs[$i]["member_code"])){?>
          <td><a title="Displys same information as when pressing the History button from the Membership area." href='javascript:;;' onClick="window.open('\membership/history_membership.php?memberId=<?=$rs[$i]["member_code"]?>&pageid=1','memberHistory',
		'scrollbars=1top=0, left=0, resizable=yes' +',width=' + (screen.width) +',height=' + (screen.height));" class="menu"><?=$obj->hightLightChar($csSearch,$rs[$i]["member_code"])?></a></td>
          <?}else{?>
          <td>-</td>
          <?}?>
          <td><?=$obf->hightLightChar($csSearch,$rs[$i]["cs_name"])?></td>
          <td><?=$obf->hightLightChar($csSearch,$rs[$i]["cs_phone"])?></td>
          <td><?=$rs[$i]["cs_birthday"]?$dateobj->convertdate($rs[$i]["cs_birthday"],"Y-m-d",$sdateformat):""?></td>
          <td><?=$dateobj->convertdate($rs[$i]["b_appt_date"],"Y-m-d",$sdateformat)?></td>
          <td><?=$rs[$i]["branch_name"]?></td>
          <td><?=$obj->getIdToText($rs[$i]["package_id"],"db_package","package_name","package_id")?></td>
  		  <td><?=$obf->hightLightChar($csSearch,$rs[$i]["cs_email"])?></td>     
          <td>
<?		  
$data["cs[name]"] = $rs[$i]["cs_name"];
$data["cs[memid]"] = $rs[$i]["member_code"]; 
$data["cs[phone]"] = $rs[$i]["cs_phone"];
$data["cs[bpname]"] = $rs[$i]["cs_name"];
$data["cs[bpphone]"] = $rs[$i]["cs_phone"];

$data["tw[0][csnameinroom]"] = $rs[$i]["cs_name"];
$data["tw[0][csphoneinroom]"] = $rs[$i]["cs_phone"];
$data["tw[0][csemail]"] = $rs[$i]["cs_email"];
$data["tw[0][csbday]"] = $dateobj->convertdate($rs[$i]["cs_birthday"],"Y-m-d",$sdateformat); 
$data["tw[0][hidden_csbday]"]=$rs[$i]["cs_birthday"];
$data["tw[0][national]"] = $rs[$i]["nationality_id"];
$data["tw[0][sex]"] = $rs[$i]["sex_id"];

$data["tw[0][csageinroom]"]="";
if($rs[$i]["cs_birthday"]){
$data["tw[0][csageinroom]"]=birthday($rs[$i]["cs_birthday"]); 
}

$sdata = http_build_query($data, '$data[]');
?>
<input type="hidden" name="cus_name<?=$i?>" id="cus_name<?=$i?>" value="<?=$rs[$i]["cs_name"]?>"/>
<input type="hidden" name="cus_code<?=$i?>" id="cus_code<?=$i?>" value="<?=$rs[$i]["member_code"]?>"/>
<input type="hidden" name="cus_phone<?=$i?>" id="cus_phone<?=$i?>" value="<?=$rs[$i]["cs_phone"]?>"/>
<input type="hidden" name="cus_email<?=$i?>" id="cus_email<?=$i?>" value="<?=$rs[$i]["cs_email"]?>"/>
<input type="hidden" name="cus_hidbday<?=$i?>" id="cus_hidbday<?=$i?>" value="<?=$rs[$i]["cs_birthday"]?>"/>
<input type="hidden" name="cus_bday<?=$i?>" id="cus_bday<?=$i?>" value="<?=$dateobj->convertdate($rs[$i]["cs_birthday"],'Y-m-d',$sdateformat)?>"/>
<input type="hidden" name="cus_sex<?=$i?>" id="cus_sex<?=$i?>" value="<?=$rs[$i]["sex_id"]?>"/>
<input type="hidden" name="cus_nationality<?=$i?>" id="cus_nationality<?=$i?>" value="<?=$rs[$i]["nationality_id"]?>"/>
<input type="hidden" name="cus_age<?=$i?>" id="cus_age<?=$i?>" value="<?=$data["tw[0][csageinroom]"]?>"/>
<?if($qstatus){?>
<input type="button" name="bpAdd" id="bpAdd" value="Add" href="javascript:;" class="button" onClick="Qadd('<?=$i?>','customer');"/>
<?}else{?>
<input title="Transfers information to a new booking for the individual Customer Selected ONLY not from entire previous booking." 
type="button" name="bpAdd" id="bpAdd" value="Add new booking" href="javascript:;" class="button" onClick="miniwindow('qbooking.php?<?=$sdata?>&branchid='+document.getElementById('branchid').value,'qbooking','400','460','0','100','100','0')"/>
<!--<input type="button" name="bpAdd" id="bpAdd" value="Add new booking" href="javascript:;" class="button" onClick="Popup('manage_booking.php?<?=$sdata?>&chkpage=1&date='+document.getElementById('date').value+'&branch='+document.getElementById('branchid').value,'manage_booking');"/>-->
<?}?>
		  </td>
        </tr>
 		<? } ?>
      </table>
    </td>
  </tr>
  <tr>
    <td align="center" ><?=$textout; ?></td>
  </tr>
</table>
</form>
<? ################### END CUSTOMER ##################### ?>
<?}else if(($obf->getParameter("batSearch") and $obf->getParameter("csSearch"))){ ?>
<? ################ AGENT ############### 

$obj->setDebugStatus(false);

$rs=$obj->getAgent($csSearch,$limit,$records_per_page);

if($rs["rows"]<1){$error="Agent search not found!!";}
// manage page

$chkrs=$obj->getAgent($csSearch);

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
 		$pagearray[] = "<b>".$i."</b>";
	}else{
		$pagearray[] = "<a href=".$pagename."&page=".$i."&csSearch=".$chkcsSearch."&batSearch=".$batSearch.">".$i."</a>"."\n";
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
        $page_back = "<a href=".$pagename."&page=".$i."&csSearch=".$chkcsSearch."&batSearch=".$batSearch.">&lt;</a>"."\n";
    }
    if($currentpage>=$page_count){
		$page_fwd = " >";
	}else{
		$i = $currentpage+1;
        $page_fwd = "<a href=".$pagename."&page=".$i."&csSearch=".$chkcsSearch."&batSearch=".$batSearch."\>&gt;</a>"."\n";
	}
}
//add back/forward ( '<<' / '>>' ) icon
$use_first_last = true;
if ($use_first_last==true){        
    //make the first page url
    if ($currentpage==$start_page){
       	$page_first = "<< ";
    }else{
        $page_first = "<a href=".$pagename."&page=1&csSearch=".$chkcsSearch."&batSearch=".$batSearch.">&lt;&lt;</a>"."\n";
    }
            
    //make the last page url
    if ($currentpage==$end_page){
      	$page_last = " >>";
    }else{
        $page_last = "<a href=".$pagename."&page=".$page_count."&csSearch=".$chkcsSearch."&batSearch=".$batSearch.">&gt;&gt;</a>"."\n";
    }
}

$textout = implode(' ',$pagearray);
$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
$textout .= " / Total ".$page_count."<br /><br />";
?>
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
    	  <td colspan="4" class=""><b style="color:#4987a1;">Agent Search : </b><?=$csSearch;?></td>
    	  </tr>
    	<tr>
    	<td class="mainthead">Book Id</td>
    	  <td class="mainthead">Customer Name</td>
          <td class="mainthead">Hotel Accommodation</td>
          <td class="mainthead">B.P.Name</td>
          <td class="mainthead">Booking Made By</td>
          <td class="mainthead">B.P. PH #</td>
          <td class="mainthead">Appointment Date</td>
          <td class="mainthead">Branch</td>
          <td class="mainthead">Add</td>
        </tr>
  		<?
			for($i=0;$i<$rs["rows"];$i++){
				$trclass = ($i%2==0)?"content_list":"content_list1";
				$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
				$pagename = "manageBooking".$rs[$i]["book_id"];
				$bpdsid=$obf->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
		?>
        <tr class='<?=$trclass?>'>
          <td><a href='javascript:;' onClick="newwindow('/appt/<?=$url?>','<?=$pagename?>')" class="menu"><?=$bpdsid?></a></td>
          <td><?=$rs[$i]["b_customer_name"]?></td>
          <td><?=$rs[$i]["b_accomodations_id"]==1?"":$obj->hightLightChar($csSearch,$obj->getIdToText($rs[$i]["b_accomodations_id"],"al_accomodations","acc_name","acc_id"))?></td>
          <td><?=$obj->hightLightChar($csSearch,$rs[$i]["c_bp_person"])?></td>
          <td><?=$obj->hightLightChar($csSearch,$obj->getIdToText($rs[$i]["c_bp_id"],"al_bookparty","bp_name","bp_id"))?></td>
          <td><?=$obj->hightLightChar($csSearch,$rs[$i]["c_bp_phone"])?></td>
          <td><?=$dateobj->convertdate($rs[$i]["b_appt_date"],"Y-m-d",$sdateformat)?></td>
          <td><?=$obj->getIdToText($rs[$i]["b_branch_id"],"bl_branchinfo","branch_name","branch_id")?></td>
          <td>
<?		  
$data["cs[bpname]"] = $rs[$i]["c_bp_person"];
$data["cs[bpphone]"] = $rs[$i]["c_bp_phone"];
$data["cs[bcompany]"] = $rs[$i]["c_bp_id"];

//$data["cs[cms]"] = "checked";
	
$chktthour = $obj->getParameter("chktthour",false);
$sdata = http_build_query($data, '$data[]');
?>
<?if($qstatus){?>
<input type="button" name="bpAdd" id="bpAdd" value="Add" href="javascript:;" class="button" onClick="Qadd('<?=$rs[$i]["c_bp_id"]?>','agent','<?=$rs[$i]["c_bp_person"]?>','<?=$rs[$i]["c_bp_phone"]?>');"/>
<?}else{?>
<input title="Transfers information to a new booking for the agent Selected."
type="button" name="bpAdd" id="bpAdd" value="Add new booking" href="javascript:;" class="button" onClick="miniwindow('qbooking.php?<?=$sdata?>&branchid='+document.getElementById('branchid').value,'qbooking','400','460','0','100','100','0')"/>
<?}?>
		  </td>
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
<?php ################### END AGENT ##################### ?>

<?}else if(($obf->getParameter("memSearch") and $obf->getParameter("csSearch"))){ ?>
<? ################ MEMBER ############### 

$obj->setDebugStatus(false);

$rs=$obj->getMember($csSearch,$limit,$records_per_page);

if($rs["rows"]<1){$error="Member search not found!!";}
// manage page

$chkrs=$obj->getMember($csSearch);

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
 		$pagearray[] = "<b>".$i."</b>";
	}else{
		$pagearray[] = "<a href=".$pagename."&page=".$i."&csSearch=".$chkcsSearch."&memSearch=".$memSearch.">".$i."</a>"."\n";
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
        $page_back = "<a href=".$pagename."&page=".$i."&csSearch=".$chkcsSearch."&memSearch=".$memSearch.">&lt;</a>"."\n";
    }
    if($currentpage>=$page_count){
		$page_fwd = " >";
	}else{
		$i = $currentpage+1;
        $page_fwd = "<a href=".$pagename."&page=".$i."&csSearch=".$chkcsSearch."&memSearch=".$memSearch.">&gt;</a>"."\n";
	}
}
//add back/forward ( '<<' / '>>' ) icon
$use_first_last = true;
if ($use_first_last==true){        
    //make the first page url
    if ($currentpage==$start_page){
       	$page_first = "<< ";
    }else{
        $page_first = "<a href=".$pagename."&page=1&csSearch=".$chkcsSearch."&memSearch=".$memSearch.">&lt;&lt;</a>"."\n";
    }
            
    //make the last page url
    if ($currentpage==$end_page){
      	$page_last = " >>";
    }else{
        $page_last = "<a href=".$pagename."&page=".$page_count."&csSearch=".$chkcsSearch."&memSearch=".$memSearch.">&gt;&gt;</a>"."\n";
    }
}

$textout = implode(' ',$pagearray);
$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
$textout .= " / Total ".$page_count."<br /><br />";
?>
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
    	  <td colspan="4" class=""><b style="color:#4987a1;">Member Search : </b><?=$csSearch;?></td>
    	  </tr>
    	<tr>
          <td class="mainthead">Member Code</td>
          <td class="mainthead">First Name</td>
          <td class="mainthead">Last Name</td>
          <td class="mainthead">Category</td>
          <td class="mainthead">Sex</td>
          <td class="mainthead">Nationality</td>
          <td class="mainthead">Phone</td>  
          <td class="mainthead">Mobile</td>
          <td class="mainthead">E-mail</td>
          <td class="mainthead">Add</td>
        </tr>
  		<?
			for($i=0;$i<$rs["rows"];$i++){
				$trclass = ($i%2==0)?"content_list":"content_list1";
		?>
        <tr class='<?=$trclass?>'>
          <td><a title="Displys same information as when pressing the History button from the Membership area." href='javascript:;;' onClick="window.open('\membership/history_membership.php?memberId=<?=$rs[$i]["member_code"]?>&pageid=1','memberHistory',
		'scrollbars=1top=0, left=0, resizable=yes' +',width=' + (screen.width) +',height=' + (screen.height));" class="menu"><?=$obj->hightLightChar($csSearch,$rs[$i]["member_code"])?></a></td>
          <td><?=$obj->hightLightChar($csSearch,$rs[$i]["fname"])?></td>
          <td><?=$obj->hightLightChar($csSearch,$rs[$i]["lname"])?></td>
          <td><?=$obj->getIdToText($rs[$i]["category_id"],"mb_category","category_name","category_id")?></td>
          <td><?=$obj->getIdToText($rs[$i]["sex_id"],"dl_sex","sex_type","sex_id")?></td>
          <td><?=$rs[$i]["state"]?></td>
          <td><?=$obj->hightLightChar($csSearch,$rs[$i]["phone"])?></td>
          <td><?=$obj->hightLightChar($csSearch,$rs[$i]["mobile"])?></td>
          <td><?=$obj->hightLightChar($csSearch,$rs[$i]["email"])?></td>
          <td>
<?		  
$data["cs[name]"] = $rs[$i]["fname"]."  ".$rs[$i]["lname"];
$data["cs[memid]"] = $rs[$i]["member_code"];
$data["cs[phone]"] = $rs[$i]["phone"];
$data["cs[bpname]"] = $rs[$i]["fname"]."  ".$rs[$i]["lname"];
$data["cs[bpphone]"] = $rs[$i]["phone"];

$data["tw[0][csnameinroom]"] = $rs[$i]["fname"]."  ".$rs[$i]["lname"];
$data["tw[0][csphoneinroom]"] = $rs[$i]["phone"];
$data["tw[0][csemail]"] = $rs[$i]["email"];
$data["tw[0][csbday]"] = $dateobj->convertdate($rs[$i]["birthdate"],'Y-m-d',$sdateformat); 
$data["tw[0][hidden_csbday]"] = $rs[$i]["birthdate"];
$data["tw[0][national]"] = $rs[$i]["nationality_id"];
$data["tw[0][sex]"] = $rs[$i]["sex_id"];

$data["tw[0][csageinroom]"]="";
if($rs[$i]["birthdate"]){
$data["tw[0][csageinroom]"]=birthday($rs[$i]["birthdate"]); 
}

$sdata = http_build_query($data, '$data[]');

if($rs[$i]["birthdate"]=="0000-00-00" || $rs[$i]["birthdate"]==""){
		$chk_bdate=false;	
}else{
		$chk_bdate=true;
}
?>
<input type="hidden" name="member_name<?=$i?>" id="member_name<?=$i?>" value="<?=$rs[$i]["fname"]."  ".$rs[$i]["lname"];?>"/>
<input type="hidden" name="member_code<?=$i?>" id="member_code<?=$i?>" value="<?=$rs[$i]["member_code"]?>"/>
<input type="hidden" name="member_phone<?=$i?>" id="member_phone<?=$i?>" value="<?=$rs[$i]["phone"]?>"/>
<input type="hidden" name="member_email<?=$i?>" id="member_email<?=$i?>" value="<?=$rs[$i]["email"]?>"/>
<input type="hidden" name="member_hidbday<?=$i?>" id="member_hidbday<?=$i?>" value="<?=$chk_bdate?$rs[$i]["birthdate"]:""?>"/>
<input type="hidden" name="member_bday<?=$i?>" id="member_bday<?=$i?>" value="<?=$chk_bdate?$dateobj->convertdate($rs[$i]["birthdate"],'Y-m-d',$sdateformat):""?>"/>
<input type="hidden" name="member_sex<?=$i?>" id="member_sex<?=$i?>" value="<?=$rs[$i]["sex_id"]?>"/>
<input type="hidden" name="member_nationality<?=$i?>" id="member_nationality<?=$i?>" value="<?=$rs[$i]["nationality_id"]?>"/>
<input type="hidden" name="member_age<?=$i?>" id="member_age<?=$i?>" value="<?=$chk_bdate?$data["tw[0][csageinroom]"]:""?>"/>
<?if($qstatus){?>
<input type="button" name="bpAdd" id="bpAdd" value="Add" href="javascript:;" class="button" onClick="Qadd('<?=$i?>','member');"/>
<?}else{?>
<input title="Transfers infomation to a new booking for the member Selected."
type="button" name="bpAdd" id="bpAdd" value="Add new booking" href="javascript:;" class="button" onClick="miniwindow('qbooking.php?<?=$sdata?>&branchid='+document.getElementById('branchid').value,'qbooking','400','460','0','100','100','0')"/>
<!--<input type="button" name="bpAdd" id="bpAdd" value="Add new booking" href="javascript:;" class="button" onClick="Popup('manage_booking.php?<?=$sdata?>&chkpage=1&date='+document.getElementById('date').value+'&branch='+document.getElementById('branchid').value,'manage_booking');"/>-->
<?}?>
		  </td>
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
<? ################### END MEMBER ##################### ?>

<?}else if(($obf->getParameter("bmarketing") and $obf->getParameter("camSearch"))){ ?>
<? ################ MARKETING ############### 

$obj->setDebugStatus(false);

$rs=$obj->getMarketing($camSearch,$limit,$records_per_page);

if($rs["rows"]<1){$error="Marketing Code search not found!!";}
// manage page

$chkrs=$obj->getMarketing($camSearch);

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
 		$pagearray[] = "<b>".$i."</b>";
	}else{
		$pagearray[] = "<a href=".$pagename."&page=".$i."&camSearch=".$chkcamSearch."&bmarketing=".$bmarketing.">".$i."</a>"."\n";
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
        $page_back = "<a href=".$pagename."&page=".$i."&camSearch=".$chkcamSearch."&bmarketing=".$bmarketing.">&lt;</a>"."\n";
    }
    if($currentpage>=$page_count){
		$page_fwd = " >";
	}else{
		$i = $currentpage+1;
        $page_fwd = "<a href=".$pagename."&page=".$i."&camSearch=".$chkcamSearch."&bmarketing=".$bmarketing.">&gt;</a>"."\n";
	}
}
//add back/forward ( '<<' / '>>' ) icon
$use_first_last = true;
if ($use_first_last==true){        
    //make the first page url
    if ($currentpage==$start_page){
       	$page_first = "<< ";
    }else{
        $page_first = "<a href=".$pagename."&page=1&camSearch=".$chkcamSearch."&bmarketing=".$bmarketing.">&lt;&lt;</a>"."\n";
    }
            
    //make the last page url
    if ($currentpage==$end_page){
      	$page_last = " >>";
    }else{
        $page_last = "<a href=".$pagename."&page=".$page_count."&camSearch=".$chkcamSearch."&bmarketing=".$bmarketing.">&gt;&gt;</a>"."\n";
    }
}

$textout = implode(' ',$pagearray);
$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
$textout .= " / Total ".$page_count."<br /><br />";
?>
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
    	  <td colspan="4" class=""><b style="color:#4987a1;">Marketing Code Search : </b><?=$camSearch;?></td>
    	  </tr>
    	<tr>
          <td class="mainthead">Sign</td>
          <td class="mainthead">Category</td>
          <td class="mainthead">Issue</td>
          <td class="mainthead">Expired</td>
          <td class="mainthead">Place</td>
          <td class="mainthead">Contact Person</td>
          <td class="mainthead">Phone</td>  
          <td class="mainthead">Comments</td>
          <?if($qstatus){?>
          <td class="mainthead">Add</td>
          <?}?>
        </tr>
  		<?
			for($i=0;$i<$rs["rows"];$i++){
				$trclass = ($i%2==0)?"content_list":"content_list1";
if($rs[$i]["issue"]=="0000-00-00"){
	$rs[$i]["issue"]="";
}
if($rs[$i]["expired"]=="0000-00-00"){
	$rs[$i]["expired"]="";
}
		?>
        <tr class='<?=$trclass?>'<?if($rs[$i]["expired"]<$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"Y-m-d",$branchid)){?>
          style="background-color: rgb(255, 185, 185);"<?}?>>
          <td><?=$rs[$i]["sign"]?$obj->hightLightChar($camSearch,$rs[$i]["sign"]):"&nbsp"?></td>
          <td><?=$rs[$i]["category_id"]?$obj->getIdToText($rs[$i]["category_id"],"l_mkcode_category","category_name","category_id"):"&nbsp"?></td>
          <td><?=$rs[$i]["issue"]?$dateobj->convertdate($rs[$i]["issue"],"Y-m-d",$sdateformat):"&nbsp"?></td>
          <td><?=$rs[$i]["expired"]?$dateobj->convertdate($rs[$i]["expired"],"Y-m-d",$sdateformat):"&nbsp"?></td>
          <td><?=$rs[$i]["place"]?$rs[$i]["place"]:"&nbsp"?></td>
          <td><?=$rs[$i]["contactperson"]?$obj->hightLightChar($camSearch,$rs[$i]["contactperson"]):"&nbsp"?></td>
          <td><?=$rs[$i]["phone"]?$obj->hightLightChar($camSearch,$rs[$i]["phone"]):"&nbsp"?></td>
          <td><?=$rs[$i]["comment"]?$rs[$i]["comment"]:"&nbsp"?></td>
<?		  
$data["cs[inspection]"] = $rs[$i]["mkcode_id"];
	
$chktthour = $obj->getParameter("chktthour",false);
$sdata = http_build_query($data, '$data[]');
?>
<?if($qstatus){?>
<td>
<input type="button" name="bpAdd" id="bpAdd" value="Add" href="javascript:;" class="button" onClick="Qadd('<?=$rs[$i]["mkcode_id"]?>','mkcode');"/>
</td>
<?}else{?>
<!--<input type="button" name="bpAdd" id="bpAdd" value="Add new booking" href="javascript:;" class="button" onClick="miniwindow('qbooking.php?<?=$sdata?>&branchid='+document.getElementById('branchid').value,'qbooking','400','460','0','100','100','0')"/>-->
<!--<input type="button" name="bpAdd" id="bpAdd" value="Add new booking" href="javascript:;" class="button" onClick="Popup('manage_booking.php?<?=$sdata?>&chkpage=1&date='+document.getElementById('date').value+'&branch='+document.getElementById('branchid').value,'manage_booking');"/>-->
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
</form>
<? ################### END MARKETING ##################### ?>

<?}else if(($obf->getParameter("bgiftcer") and $obf->getParameter("camSearch"))){ ?>
<? ################ GIFT ############### 

$obj->setDebugStatus(false);

$rs=$obj->getGift($camSearch,$limit,$records_per_page);

if($rs["rows"]<1){$error="Gift Certificate search not found!!";}
// manage page

$chkrs=$obj->getGift($camSearch);

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
 		$pagearray[] = "<b>".$i."</b>";
	}else{
		$pagearray[] = "<a href=".$pagename."&page=".$i."&camSearch=".$chkcamSearch."&bgiftcer=".$bgiftcer.">".$i."</a>"."\n";
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
        $page_back = "<a href=".$pagename."&page=".$i."&camSearch=".$chkcamSearch."&bgiftcer=".$bgiftcer.">&lt;</a>"."\n";
    }
    if($currentpage>=$page_count){
		$page_fwd = " >";
	}else{
		$i = $currentpage+1;
        $page_fwd = "<a href=".$pagename."&page=".$i."&camSearch=".$chkcamSearch."&bgiftcer=".$bgiftcer.">&gt;</a>"."\n";
	}
}
//add back/forward ( '<<' / '>>' ) icon
$use_first_last = true;
if ($use_first_last==true){        
    //make the first page url
    if ($currentpage==$start_page){
       	$page_first = "<< ";
    }else{
        $page_first = "<a href=".$pagename."&page=1&camSearch=".$chkcamSearch."&bgiftcer=".$bgiftcer.">&lt;&lt;</a>"."\n";
    }
            
    //make the last page url
    if ($currentpage==$end_page){
      	$page_last = " >>";
    }else{
        $page_last = "<a href=".$pagename."&page=".$page_count."&camSearch=".$chkcamSearch."&bgiftcer=".$bgiftcer.">&gt;&gt;</a>"."\n";
    }
}

$textout = implode(' ',$pagearray);
$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
$textout .= " / Total ".$page_count."<br /><br />";
?>
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
    	  <td colspan="4" class=""><b style="color:#4987a1;">Gift Certificate Search : </b><?=$camSearch;?></td>
    	  </tr>
    	<tr>
          <td class="mainthead">Gift Number</td>
          <td class="mainthead">Give To</td>
          <td class="mainthead">Receive From</td>
          <td class="mainthead">Value</td>
          <td class="mainthead">Type</td>
          <td class="mainthead">Issue</td>
          <td class="mainthead">Expired</td>  
          <td class="mainthead">Used</td>
          <td class="mainthead">Receive By</td>
          <td class="mainthead">Product</td>
          <td class="mainthead">Book Id</td>
          <td class="mainthead">Id Sold</td>
          <?if($qstatus){?>
          <td class="mainthead">Add</td>
          <?}?>
        </tr>
  		<?
			for($i=0;$i<$rs["rows"];$i++){
				$trclass = ($i%2==0)?"content_list":"content_list1";

if($rs[$i]["book_id"]){
$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
				
				$pagename = "manageBooking".$rs[$i]["book_id"];
				
$bpdsid=$obf->getIdToText($rs[$i]["book_id"],"c_bpds_link","bpds_id","tb_id","tb_name='a_bookinginfo'");

}

if($rs[$i]["id_sold"]){
$url = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rs[$i]["id_sold"]."":"manage_pdforsale.php?pdsid=".$rs[$i]["id_sold"]."";
$pagename = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rs[$i]["id_sold"]:"managePds".$rs[$i]["id_sold"];

$pdsid=$obf->getIdToText($rs[$i]["id_sold"],"c_bpds_link","bpds_id","tb_id","tb_name='".$rs[$i]["tb_name"]."'");
}

if($rs[$i]["issue"]=="0000-00-00"){
	$rs[$i]["issue"]="";
}
if($rs[$i]["expired"]=="0000-00-00"){
	$rs[$i]["expired"]="";
}
		?>
        <tr class='<?=$trclass?>'<?if($rs[$i]["expired"]<$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"Y-m-d",$branchid)){?>
          style="background-color: rgb(255, 185, 185);"<?}?>>
          <td><?=$rs[$i]["gift_number"]?$obj->hightLightChar($camSearch,$rs[$i]["gift_number"]):"&nbsp"?></td>
          <td><?=$rs[$i]["give_to"]?$obj->hightLightChar($camSearch,$rs[$i]["give_to"]):"&nbsp"?></td>
          <td><?=$rs[$i]["receive_from"]?$obj->hightLightChar($camSearch,$rs[$i]["receive_from"]):"&nbsp"?></td>
          <td><?=$rs[$i]["value"]?$rs[$i]["value"]:"&nbsp"?></td>
          <td><?=$rs[$i]["gifttype_id"]?$obj->getIdToText($rs[$i]["gifttype_id"],"gl_gifttype","gifttype_name","gifttype_id"):"&nbsp"?></td>
          <td><?=$rs[$i]["issue"]?$dateobj->convertdate($rs[$i]["issue"],"Y-m-d",$sdateformat):"&nbsp"?></td>
          <td><?=$rs[$i]["issue"]?$dateobj->convertdate($rs[$i]["expired"],"Y-m-d",$sdateformat):"&nbsp"?></td>
          <td><?=$rs[$i]["used"]?$rs[$i]["used"]:"&nbsp"?></td>
          <td><?=$rs[$i]["receive_by_id"]?$obj->getIdToText($rs[$i]["receive_by_id"],"l_employee","emp_nickname","emp_id"):"&nbsp"?></td>
          <td><?=$rs[$i]["product"]?$rs[$i]["product"]:"&nbsp"?></td>
          <td><a href='javascript:;' onClick="newwindow('/appt/<?=$url?>','<?=$pagename?>')" class="menu"><?=$rs[$i]["book_id"]?$bpdsid:"&nbsp"?></a></td>
          <td><a href='javascript:;' onClick="newwindow('/appt/<?=$url?>','<?=$pagename?>')" class="menu"><?=$rs[$i]["id_sold"]?$pdsid:"&nbsp"?></a></td>

<?		  
$data["giftChk"] = true;
$data["addgift"]= true;
$data["giftnumber[]"]=$rs[$i]["gift_number"];

$sdata = http_build_query($data, '$data[]');
?>
<?if($qstatus){?>
<td>
<input type="button" name="bpAdd" id="bpAdd" value="Add" href="javascript:;" class="button" onClick="Qadd('<?=$rs[$i]["gift_number"]?>','gift');"/>
</td>
<?}else{?>
<!--<input type="button" name="bpAdd" id="bpAdd" value="Add new booking" href="javascript:;" class="button" onClick="miniwindow('qbooking.php?<?=$sdata?>&branchid='+document.getElementById('branchid').value,'qbooking','400','460','0','100','100','0')"/>-->
<!--<input type="button" name="bpAdd" id="bpAdd" value="Add new booking" href="javascript:;" class="button" onClick="Popup('manage_booking.php?<?=$sdata?>&chkpage=1&date='+document.getElementById('date').value+'&branch='+document.getElementById('branchid').value,'manage_booking');"/>-->
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
</form>
<? ################### END GIFT ##################### ?>

<?}else if(($obf->getParameter("hotelSearch") and $obf->getParameter("camSearch"))){ ?>
<? ################ Hotel ############### 

$obj->setDebugStatus(false);

$rs=$obj->getHotel($camSearch,$limit,$records_per_page);

if($rs["rows"]<1){$error="Hotel Accomodation search not found!!";}
// manage page

$chkrs=$obj->getHotel($camSearch);

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
 		$pagearray[] = "<b>".$i."</b>";
	}else{
		$pagearray[] = "<a href=".$pagename."&page=".$i."&camSearch=".$chkcamSearch."&hotelSearch=".$hotelSearch.">".$i."</a>"."\n";
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
        $page_back = "<a href=".$pagename."&page=".$i."&camSearch=".$chkcamSearch."&hotelSearch=".$hotelSearch.">&lt;</a>"."\n";
    }
    if($currentpage>=$page_count){
		$page_fwd = " >";
	}else{
		$i = $currentpage+1;
        $page_fwd = "<a href=".$pagename."&page=".$i."&camSearch=".$chkcamSearch."&hotelSearch=".$hotelSearch.">&gt;</a>"."\n";
	}
}
//add back/forward ( '<<' / '>>' ) icon
$use_first_last = true;
if ($use_first_last==true){        
    //make the first page url
    if ($currentpage==$start_page){
       	$page_first = "<< ";
    }else{
        $page_first = "<a href=".$pagename."&page=1&camSearch=".$chkcamSearch."&hotelSearch=".$hotelSearch.">&lt;&lt;</a>"."\n";
    }
            
    //make the last page url
    if ($currentpage==$end_page){
      	$page_last = " >>";
    }else{
        $page_last = "<a href=".$pagename."&page=".$page_count."&camSearch=".$chkcamSearch."&hotelSearch=".$hotelSearch.">&gt;&gt;</a>"."\n";
    }
}

$textout = implode(' ',$pagearray);
$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
$textout .= " / Total ".$page_count."<br /><br />";
?>
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
    	  <td colspan="4" class=""><b style="color:#4987a1;">Hotel Accomodation Search : </b><?=$camSearch;?></td>
    	  </tr>
    	<tr>
          <td class="mainthead">Accommodation Name</td>
          <td class="mainthead">Contact person</td>
          <td class="mainthead">Main Phone</td>
          <td class="mainthead">Fax</td>
          <td class="mainthead">E-mail</td>
          <td class="mainthead">City</td>
          <td class="mainthead">Address</td>
          <td class="mainthead">Specific Details</td>
        </tr>
  		<?
			for($i=0;$i<$rs["rows"];$i++){
				$trclass = ($i%2==0)?"content_list":"content_list1";

		?>
		<tr class='<?=$trclass?>'>
          <td><?=$obj->hightLightChar($camSearch,$rs[$i]["acc_name"])?></td>
          <td><?=$obj->hightLightChar($camSearch,$rs[$i]["acc_person"])?></td>
          <td><?=$obj->hightLightChar($camSearch,$rs[$i]["acc_phone"])?></td>
          <td><?=$obj->hightLightChar($camSearch,$rs[$i]["acc_fax"])?></td>
          <td><?=$obj->hightLightChar($camSearch,$rs[$i]["acc_email"])?></td>
          <td><?=$obf->getIdToText($rs[$i]["city_id"],"al_city","city_name","city_id")?></td>
          <td><?=$rs[$i]["acc_address"]?></td>
          <td><?=$rs[$i]["acc_detail"]?></td>
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
<? ################### END Hotel ##################### ?>

<?}else if($obf->getParameter("batSearch") or $obf->getParameter("bcsSearch") or $obf->getParameter("memSearch") or $obf->getParameter("bmarketing") or $obf->getParameter("bgiftcer") or $obf->getParameter("hotelSearch")){?>
<?$error="Please input at least 1 keyword to search"?>
 	<table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    	<tr>
    		<td ><b><font style="color:#ff0000;">Error message: </font></b><?=$error?></td>
    	</tr>
    </table>
<?}?>
</body>
</html>
