<?
include("../include.php");
require_once("appt.inc.php");

$obj = new appt();

// system date format	 					
$chksql = "select long_date,short_date from a_company_info";
$chkrs =$obj->getResult($chksql);
$sdateformat = $obj->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
$ldateformat = $obj->getIdToText($chkrs[0]["long_date"],"l_date","date_format","date_id");
		
$branch = $obj->getParameter("branch",false);
$book_id = $obj->getParameter("book_id",false);
$cs_name = $obj->getParameter("cs_name",false);
$appttime = $obj->getParameter("appttime",false);
$name = $obj->getParameter("name",false);
$hour = $obj->getParameter("hour",false);
$thour = $obj->getParameter("thour",false);
$twstart = $obj->getParameter("twstart",false);
$twend = $obj->getParameter("twend",false);
$msg = $obj->getParameter("msg",false);
$obj->setBranchid($branch);
$book_id = $obj->getIdToText($book_id,"c_bpds_link","bpds_id","tb_id","tb_name=\"a_bookinginfo\"");
$maxhour=0;
if(!$hour){$hour=array(); }
if(!$name){$name=array(); }
if(!$msg){$msg=array(); }
for($i=0;$i<count($hour);$i++){
	if($hour[$i]>$maxhour){$maxhour=$hour[$i];}
}

//$finishtime = $obj->getIndFinishTime($appttime,$maxhour);
$finishtime = $obj->getIndFinishTime($appttime,$thour);

$apptdate = $obj->getParameter("apptdate",false);
$room = $obj->getParameter("room",false);

$sex = $obj->getParameter("sex",false);
$national = $obj->getParameter("national",false);


$stream = $obj->getParameter("stream",false);



if(!$stream)
	$stream="No";
else
	$stream="Yes";
	
$package = $obj->getParameter("package",false);
$bath = $obj->getParameter("bath",false);
$facial = $obj->getParameter("facial",false);
$wrap = $obj->getParameter("wrap",false);
$strength = $obj->getParameter("strength",false);
$scrub = $obj->getParameter("scrub",false);
$comment = $obj->getParameter("comment",false);
$branchid = $branch;
$branch = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");

//window.print();window.close();
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href='css/report.css' type='text/css' rel='stylesheet'>
<body>
<!--<body>-->
<table width="215" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="10">&nbsp;</td>
    <td>
    <table width="205" cellpadding="3" cellspacing="0" border="0">
      <tr>
        <td align="center" colspan="2"><b>THERAPIST WORKING SHEET</b></td>
      </tr>
      <tr>
      <? 
       $data = $dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$ldateformat, H:i:s A",$branchid);?>
        <td align="center" colspan="2" class="small_2"><?=$data?></td>
      </tr>
      <tr>
        <td align="center" colspan="2"><b><?=$branch?></b><br/><br/></td>
      </tr>
      <tr>
        <td align="left" colspan="2">Book ID : <b><?=$book_id?></b></td>
      </tr>
      <tr>
        <td align="left" colspan="2">Date : <b style="color: #ff0000"><?=$apptdate?></b></td>
      </tr>
      <tr>
        <td align="left" colspan="2">Name : <b style="color: #ff0000"><?=$cs_name?></b></td>
      </tr>
      <tr>
        <td align="left" colspan="2">Nationality : <b>
          <?=($national==120)?"-":$obj->getIdToText($national,"dl_nationality","nationality_name","nationality_id")?>
        </b></td>
      </tr>
      <tr>
        <td align="left" colspan="2">Sex : <b>
          <?=$obj->getIdToText($sex,"dl_sex","sex_type","sex_id")?>
        </b></td>
      </tr>
      <tr>
        <td align="left" colspan="2">Appointment Time :  <b style="color: #ff0000">
          <?=substr($obj->getIdToText($appttime,"p_timer","time_start","time_id"),0,5)?>
        </b></td>
      </tr>
      <tr>
        <td align="left" colspan="2">Finished Time :  <b style="color: #ff0000">
          <?=substr($finishtime,0,5)?>
        </b></td>
      </tr>
      <tr>
        <td align="left" colspan="2">Branch :  <b>
          <?=$branch?>
        </b></td>
      </tr>
      <tr>
        <td align="left" colspan="2">Room Name :  <b>
          <?=$obj->getIdToText($room,"bl_room","room_name","room_id")?>
        </b></td>
      </tr>
      <tr>
        <td align="left" colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"> <b style="color: #ff0000"><? if(count($name)>1){?>This room has <?=count($name)?> therapists.<? }?></b></td>
      </tr>
      <? $cnt=1; 
	  for($i=0;$i<count($name);$i++){ 
	  if(!isset($hour[$i])){$hour[$i]="";}
	  ?>
      <tr>
        <td align="left">Therapist<?=$cnt?> :  <b style="color: #ff0000">
          <?="<br>"?><?=$obj->getIdToText($name[$i],"l_employee","emp_nickname","emp_id")?>
        </b></td>
        <td align="left">Hour : <b style="color: #ff0000">
       	  <?="<br>"?><?=$obj->getIdToText($hour[$i],"l_hour","hour_name","hour_id")?>
        </b></td>
      </tr>
      
       <tr>
      	<td>Start Time : <b style="color: #ff0000">
      	 <?="<br>"?><?=substr($obj->getIdToText($twstart[$i],"p_timer","time_start","time_id"),0,5)?>
      	</b></td>
      	<td>End Time :<b style="color: #ff0000">
      	  <?="<br>"?><?=substr($obj->getIdToText($twend[$i],"p_timer","time_start","time_id"),0,5)?>
      	</b></td>
	  <? $cnt++;}?>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <? if(strlen($obj->getIdToText($package,"db_package","package_name","package_id"))>0){?>
			<tr><td align="left" colspan="3">Package : <b><?=$obj->getIdToText($package,"db_package","package_name","package_id")?></b></td></tr>
	   <? } if($stream=="Yes"){ ?>
            <tr><td align="left" colspan="3">Steam : <b><?=$stream?></b></td></tr>
	   <? } $cnt=1; 
	  	for($i=0;$i<count($msg);$i++){ ?>
			<tr><td align="left" colspan="3">Massage<?=$cnt?> : <b><?=$obj->getIdToText($msg[$i],"db_trm","trm_name","trm_id")?></b></td></tr>
	   <? $cnt++;
	   	  } if(strlen($obj->getIdToText($scrub,"db_trm","trm_name","trm_id"))>0){ ?>
			<tr><td align="left" colspan="3">Scrub : <b><?=$obj->getIdToText($scrub,"db_trm","trm_name","trm_id")?></b></td></tr>
	   <? } if(strlen($obj->getIdToText($wrap,"db_trm","trm_name","trm_id"))>0){ ?>
			<tr><td align="left" colspan="3">Wrap : <b><?=$obj->getIdToText($wrap,"db_trm","trm_name","trm_id")?></b></td></tr>
	   <? } if(strlen($obj->getIdToText($facial,"db_trm","trm_name","trm_id"))>0){ ?>
			<tr><td align="left" colspan="3">Facial : <b><?=$obj->getIdToText($facial,"db_trm","trm_name","trm_id")?></b></td></tr>
	   <? } if(strlen($obj->getIdToText($bath,"db_trm","trm_name","trm_id"))>0){ ?>
			<tr><td align="left" colspan="3">Bath Type : <b><?=$obj->getIdToText($bath,"db_trm","trm_name","trm_id")?></b></td></tr>
	   <? } if(strlen($obj->getIdToText($strength,"l_strength","strength_type","strength_id"))>0){ ?>
			<tr><td align="left" colspan="3">Strength : <b><?=$obj->getIdToText($strength,"l_strength","strength_type","strength_id")?></b></td></tr>
	   <? } if($comment){ ?>
			<tr><td align="left" colspan="3">Comment : <?=$comment?></td></tr>
	   <? } ?>
      <tr>
        <td align="left" colspan="2">&nbsp;</td>
      </tr>
      <? $cnt=1; 
	  for($i=0;$i<count($name);$i++){ ?>
      <tr height="35">
        <td align="left" colspan="2">Therapist <?=$cnt?>:...................................... </td>
      </tr>
	  <? $cnt++;}?>
      <tr>
        <td align="center" colspan="2" valign="bottom">Thank You</td>
      </tr>
      <tr>
        <td align="center" valign="middle" height="30" colspan="2">*************************</td>
      </tr>
    </table></td>
  </tr>
</table>
</body>