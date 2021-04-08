<? 
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
$obj->setDebugStatus(false);
$errormsg = "";
$successmsg = "";
$branchid = $obj->getParameter("branch_id");

if(!$branchid){
	$branchid = $obj->getParameter("bid");
}

$date = $obj->getParameter("date");
if($date==""){
	$date=$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$sdateformat",$branchid);
	//$date=date($sdateformat);
}
$dateTmp=$date;		//for reset date if user hasn't accessibility on that date 
$selectDay="";
$hidden_date = $obj->getParameter("hidden_date",$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"Ymd",$branchid));
//$hidden_date = $obj->getParameter("hidden_date",$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"Ymd"));
$year = substr($hidden_date,0,4);
$month = substr($hidden_date,4,2);
$day = substr($hidden_date,6,2);
$nunix_time = mktime(0, 0, 0, (int)$month, (int)$day+1, (int)$year);
$punix_time = mktime(0, 0, 0, (int)$month, (int)$day-1, (int)$year);
$selectDay = $obj->getParameter("selectDay");
if($selectDay==" Next Day "){
	$date = date($sdateformat, $nunix_time);
	$hidden_date = date("Ymd", $nunix_time);
	$selectDay=" Next Day ";
} else if($selectDay==" Previous Day "){
	$date = date($sdateformat, $punix_time);
	$hidden_date = date("Ymd", $punix_time);
	$selectDay=" Previous Day ";
}
$year = substr($hidden_date,0,4);
$month = substr($hidden_date,4,2);
$day = substr($hidden_date,6,2);
$nunix_time = mktime(0, 0, 0, (int)$month, (int)$day+1, (int)$year);
$punix_time = mktime(0, 0, 0, (int)$month, (int)$day-1, (int)$year);
////////////////////// check reservtion date ////////////////////////
$preViewDate="";
$afterViewDate="";
$checkApptPage="true";
$now=$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$sdateformat",$branchid);

$chkRsViewDate = $object->isReservationLimit();
$chkRsView=true;
if($chkRsViewDate){
		$preViewDate= $object->getReservationDate("pre_viewdate","appt_viewchk");
		$afterViewDate= $object->getReservationDate("after_viewdate","appt_viewchk");
		$chkRsDate= $object->checkReservationDate($date,$sdateformat,$preViewDate,$afterViewDate,$now);
	if(!$chkRsDate){
		$chkRsView=false;
		$date=$dateTmp;
		//$hidden_date=$dateobj->timezone_depend_branch($dateTmp,date("H:i:s"),'Ymd',$branchid);
		//echo "Don't have permission";
		$year = substr($hidden_date,0,4);
		$month = substr($hidden_date,4,2);
		$day = substr($hidden_date,6,2);
		$nunix_time = mktime(0, 0, 0, (int)$month, (int)$day, (int)$year);
		$punix_time = mktime(0, 0, 0, (int)$month, (int)$day, (int)$year);
		
		if($selectDay){
			if($selectDay==" Next Day "){
				$hidden_date = date("Ymd", $nunix_time);
				$errormsg ="You don't have permission to access date ".date($sdateformat, $nunix_time)." !!";
			}else if($selectDay==" Previous Day "){
				$hidden_date = date("Ymd", $punix_time);
				$errormsg ="You don't have permission to access date ".date($sdateformat, $punix_time)." !!";
			}	
		}else{
			$date=$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$sdateformat",$branchid);
			$hidden_date=$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"Ymd",$branchid);
	
			//$date=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$sdateformat");
			//$hidden_date=$date=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"Ymd");
			
			$year = substr($hidden_date,0,4);
			$month = substr($hidden_date,4,2);
			$day = substr($hidden_date,6,2);
			$nunix_time = mktime(0, 0, 0, (int)$month, (int)$day+1, (int)$year);
			$punix_time = mktime(0, 0, 0, (int)$month, (int)$day-1, (int)$year);
		}
	}
}else{
		$preViewDate="notCheck";
		$afterViewDate="notCheck";
}
	
////////////// For check user permission to edit this page ////////////////
$preEditDate="";
$afterEditDate="";
// checking if appt_editchk was check
$chkRsEditDate = $object->isReservationLimit("appt_editchk");
if($chkRsEditDate){
	$preEditDate= $object->getReservationDate("pre_editdate","appt_editchk");
	$afterEditDate= $object->getReservationDate("after_editdate","appt_editchk");
	$chkRsDate= $object->checkReservationDate($date,$sdateformat,$preEditDate,$afterEditDate,$now);
	if(!$chkRsDate){
		$chkPageEdit=false;
	}
}
// For check user can edit in this branch or not
if($chkPageEdit && $object->isEditBookInLocation($branchid)){
		$chkPageEdit=true;	
}else{
		$chkPageEdit=false;
}

//echo "<br><br><br><br><br><br>";
//echo "<br>ChkDate $chkRsDate";
//echo "<br>ChkRequestDate ".$date;
//echo "<br>ChkPreViewDate ".$preEditDate;
//echo "<br>ChkIsRsLimit ".$isRsLimit;
//print_r($isRsLimit);

/////////// End for check user permission to edit this page ////////////////

$userLocationId = $object->getUserLocationId();
///////////// End check user is admin or stay in branch ////////////////////
////////////////////// check search by book id //////////////////////
$search = $obj->getParameter("search");
$bookid = $obj->getParameter("bookid");
if($search==" Search "){
	$chksql = "select * from c_bpds_link where bpds_id=".$bookid;
	$chkrs =$obj->getResult($chksql);
	if($chkrs[0]["tb_name"]=="c_saleproduct"){
		$bookBranchId=$obj->getIdToText($chkrs[0]["tb_id"],"c_saleproduct","branch_id","pds_id");	
	}else{
		$bookBranchId=$obj->getIdToText($chkrs[0]["tb_id"],"a_bookinginfo","b_branch_id","book_id");
	}
	$bookLocationId = $obj->getIdToText($bookBranchId,"bl_branchinfo","city_id","branch_id");
	//echo "<br>$bookLocationId : $userLocationId";
	//echo "<br>is edit : ".$object->isEditBookInLocation($branchid);
	//echo "<br>Tb Name : ".$chkrs[0]["tb_name"];
	if($chkrs["rows"]==0){
		$errormsg="No match for booking/product sale ID: ".$bookid."!!";
	}else if($object->isEditBookInLocation($branchid)!="All" && $userLocationId!=$bookLocationId){//} && $chkrs[0]["tb_name"]=="a_bookinginfo"){
		$errormsg="Access denied for another location !!";
	}else{
		if($chkrs[0]["tb_name"]=="a_bookinginfo"){
		?>
		<script language="javascript">
			window.open('manage_booking.php?chkpage=1&bookid=<?=$chkrs[0]["tb_id"]?>','','scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
		</script>
		<?
		} else {
		?>
		<script language="javascript">
			window.open('manage_pdforsale.php?pdsid=<?=$chkrs[0]["tb_id"]?>','managePds<?=$chkrs[0]["tb_id"]?>','resizable=0,scrollbars=1');
		</script>
		<?
		}
	}
}
$sql = "select bl_th_list.th_list_id " .
				"from bl_th_list " .
				"where bl_th_list.branch_id=$branchid " .
				"and bl_th_list.l_lu_date>=\"".date("Y-m-d")."\"" .
				"and bl_th_list.leave=0 ";			// add bl_th_list.leave=0 for show only therapist who isn't leave, natt
$thrs = $obj->getResult($sql);
$th_signin = $thrs["rows"]+0;

$sql_ot = "select bl_th_list.th_list_id " .
				"from bl_th_list " .
				"where bl_th_list.branch_id=$branchid " .
				"and bl_th_list.ot=0 " .
				"and bl_th_list.l_lu_date>=\"".date("Y-m-d")."\"" .
				"and bl_th_list.leave=0 ";			// add bl_th_list.leave=0 for show only therapist who isn't leave, natt
$thrs_ot = $obj->getResult($sql_ot);
$th_ot = $thrs_ot["rows"]+0;

$th_shiftone = $obj->getIdToText("$branchid","bl_th_available","th_shiftone","branch_id","1 order by l_lu_date desc")+0;
$th_shifttwo = $obj->getIdToText("$branchid","bl_th_available","th_shifttwo","branch_id","1 order by l_lu_date desc")+0;
$popupwin = $obj->getParameter("popupwin");
$pageinfo["parent"]=array("Home","Appointment");
$pageinfo["parenturl"]=array("/mainPage.php?pageid=0","/appt/home.php?pageid=1");
$pageinfo["parentid"]=array("0","1");
$pageinfo["pagename"]=$obj->getIdToText($branchid,"bl_branchinfo","branch_name","branch_id");
$pageinfo["pageid"]="1";

$editID=$obj->getParameter("EditBid");
$editRoom=$obj->getParameter("EditRoomid");
$editStatus=$obj->getParameter("ChangeStatus");
if($editID&&$editRoom&&$editStatus){
$at_spa=0;
$in_room=0;
$finish=0;
$roomId=array();
$roomStatus=array();
	if($editStatus=="A"){
			$at_spa=1;
			$in_room=0;
			$finish=0;
	}else if($editStatus=="I"){
			$at_spa=0;
			$in_room=1;
			$finish=0;
	}else if($editStatus=="F"){
			$at_spa=0;
			$in_room=0;
			$finish=1;
	}
	$sql_estatus =  "update d_indivi_info set b_set_atspa = '$at_spa'" .
			",b_set_inroom = '$in_room' " .
			",b_set_finish = '$finish' " .
			"where book_id= '$editID' " .
			"and room_id= '$editRoom' ";
	$rs = $obj->setResult($sql_estatus);
	
	$sql_app = "select * from a_appointment where book_id=".$editID;
	$rs_app = $obj->getResult($sql_app);
	
	$roomId = explode("|", $rs_app[0]["room_ids"]);
	$ni="";
	$roomArray="";
	for($i=0;$i<count($roomId);$i++){
		if($roomId[$i]==$editRoom){
			$ni=$i;
		}
	}
	$roomStatus = explode("|", $rs_app[0]["status"]);
		$sta_room=0;
		if($at_spa==1){
			$sta_room=1;
		}else if($in_room==1){
			$sta_room=2;
		}else if($finish==1){
			$sta_room=3;
		}
	if($sta_room==1){
		for($i=0;$i<count($roomStatus);$i++){
			$roomStatus[$i]=$sta_room;
		}	
	}else{
		$roomStatus[$ni]=$sta_room;
	}
	$roomArray = implode("|",$roomStatus);
	$sql_update="update a_appointment set status='$roomArray' " .
			"where book_id= '$editID' ";
	$rs_update = $obj->setResult($sql_update);
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="scripts/ajax.js"></script>
<script type="text/javascript" src="scripts/tooltip/boxover.js"></script>
<script src="scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="scripts/datechooser/datechooser.js" type="text/javascript"></script>
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
  <?include("$root/jsdetect.php");?>
<link rel="stylesheet" type="text/css" href="scripts/datechooser/datechooser.css">
<link href="/css/styles.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="showMessage();" OnKeyPress="return disableKeyPress(event)"> 
<form name="appointment" id="appointment" action="" method="get">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
<?if(!$popupwin){?>
 <tr>
    <td width="8" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
<?}?>
    <td height="<?=(!$popupwin)?"115":"65"?>px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">

<?if(!$popupwin){?>
      <tr>
	    <td valign="top" align="center" height="49">
				<?include("$root/menuheader.php");?>
	 	</td>
	  </tr>
<?}?>
  	  <tr>
	    <td valign="top" align="center" height="30px">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
			     <td class="rheader" height="30px" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');">
					<input type="hidden" name="pageid" id="pageid" value="<?=$pageid?>"/>
			        <?if($popupwin){?>
						<input type="hidden" name="popupwin" id="popupwin" value="<?=$popupwin?>"/>
					<?}?>
		        	Branch:&nbsp;&nbsp;
		    		<? 	$obj->makeListbox("branch_id","bl_branchinfo","branch_name","branch_id",$branchid,true,"branch_name","branch_active","1","branch_name not like 'All'"); ?>
		            &nbsp;&nbsp;<input id="date" name="date" value="<?php
		        //echo $hidden_date;
              	//echo $branchid;
              	echo (isset($date))?$date:$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$sdateformat",$branchid);  ?>" style="width: 85px;" readonly="1" class="textbox" type="text" onKeyPress="return disableEnterKey(event);">
		           	<input id='hidden_date' name='hidden_date' value="<?=$hidden_date?>" type="hidden"/>
		            <a href="javascript:;" style="margin-top:0.3px;position:fixed;" 
				        onclick="showChooser(this, 'date', 'date_showSpan', 1900, 2100, '<?=$sdateformat?>', false,<?=$checkApptPage?>,'<?=$preViewDate?>','<?=$afterViewDate?>');"
				        onmouseover="changeimg('calendarimg','/images/calendar.png')" 
				        onmouseout="changeimg('calendarimg','/images/calendar.png')">
				        <img align="top" style="margin-top:3px;" src="/images/calendar.png" id="calendarimg" border="0" title="date">
				        </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<div id="date_showSpan" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="button" type="submit" name="selectDay" value=" Previous Day " title="<?=date($sdateformat, $punix_time)?>" style='width: 83px;background:no-repeat url("/images/<?=$theme?>/appt/previous day.png");'>
					&nbsp;&nbsp;<input class="button" type="submit" name="selectDay" value=" Next Day " title="<?=date($sdateformat, $nunix_time)?>" style='width: 83px;background:no-repeat url("/images/<?=$theme?>/appt/next day.png");'>
					<input type="hidden" id="chkPageEdit" name="chkPageEdit" value="<?=$chkPageEdit?>" />
					<input type="hidden" id="chkRsView" name="chkRsView" value="<?=$chkRsView?>" />
					<?if(!$popupwin){?>
			        	&nbsp;<input class="button" type="button" name="bpopupwin" id="bpopupwin" value="View Full Screen" style="width: 20px;height:16px;background:no-repeat url('/images/view full screen.png');"
			        	onClick="newwindow('index.php?popupwin=popupwin&pageid=1&bid='+document.getElementById('branch_id').value,'Appointments','resizable=yes,menubar=no,scrollbars=yes');" title="view full screen"/>
			        <?}?>
			        &nbsp;<input id="crrcust" name="crrcust" size="10" type="text" value="<?=$obj->getParameter("crrcust")?>" class="textbox" onKeyPress="return disableEnterKey(event);"> 
			        &nbsp;<input type="button" name="popUp4" value="Current Customers" title="get current customers"
					href="javascript:;" onClick="searchcust();" 
			        style="width: 83px;background:no-repeat url('/images/<?=$theme?>/appt/current cust.png');" class="button">
			        &nbsp;
			        <? if($chkPageEdit){?>
			        <input type="button" name="popUp" value=" Quick Search " title="quick search information" href="javascript:;" onClick="quicksearch('quick_search.php?date='+document.getElementById('date').value+'&branchid='+document.getElementById('branch_id').value+'');" style="width: 100px;background:no-repeat url('/images/<?=$theme?>/appt/quick_search.png');" class="button">		
					<? }?>
					&nbsp;&nbsp;<span id="errormsg" class="style1" style='color:#ff0000'><? if($errormsg!=""){ ?><img src="/images/errormsg.png" /><? } ?>&nbsp;&nbsp;
					<b class="errormsg"><?=$errormsg?></b></span>
					<span style='color:#3875d7'><? if($successmsg!=""){ ?><img src="/images/successmsg.png" /><? } ?>&nbsp;&nbsp;<b class="successmsg"><?=$successmsg?></b></span>
				   </td> 
			      </tr>
			      <tr>
			        <td height="1" style="background:<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
			      </tr>
    			</table>  
    		</td>
  		</tr>
		<tr>
    	 <td valign="center" height="20px" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');">
    		<table border="0" cellspacing="0" cellpadding="0">
			      <tr>
        			<td height="30" class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
				       ID:&nbsp;&nbsp;<input id="bookid" name="bookid" size="6" type="text" value="<?=$bookid?>" class="textbox" onKeyPress="return disableEnterKey(event);"/>
				        &nbsp;&nbsp;<input class="button" type="submit" id="search" name="search" value=" Search " id="searchimg" height="20px" 
				        style="width: 63px;background:no-repeat url('/images/<?=$theme?>/appt/search.png');" title="Search by booking or product sale ID">
				        &nbsp;
				    </td><td class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
						<img src="/images/<?=$theme?>/appt/separate.png">
					</td><td class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
						<? if($chkPageEdit){?>
						&nbsp;&nbsp;<input class="button" type="button" name="popUp" value=" Add Booking " style="width: 110px;background:no-repeat url('/images/<?=$theme?>/appt/add booking.png');" title="add new booking"
						href="javascript:;" onClick="newwindow('manage_booking.php?chkpage=1&date='+document.getElementById('date').value+'&branch='+document.getElementById('branch_id').value,'manage_booking');"/>
						&nbsp;&nbsp;<input class="button" type="button" name="popUp2" value=" Add Product Sale " style="width: 110px;background:no-repeat url('/images/<?=$theme?>/appt/add product sale.png');" title="add new product sale"
						href="javascript:;" onClick="newwindow('manage_pdforsale.php?chkpage=1&date='+document.getElementById('date').value+'&branch_id='+document.getElementById('branch_id').value,'manage_pdforsale');"/>
						&nbsp;&nbsp;<input class="button" type="button" name="popUp3" value=" Room Maintance " style="width: 110px;background:no-repeat url('/images/<?=$theme?>/appt/room maintance.png');" title="add room maintenance"
						href="javascript:;" onClick="newwindow('manage_mroom.php?chkpage=1&date='+document.getElementById('date').value+'&branch_id='+document.getElementById('branch_id').value,'manage_mroom');"/>
						<? }?>&nbsp;
				     </td><td class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
						<img src="/images/<?=$theme?>/appt/separate.png">
					</td><td class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
						&nbsp;&nbsp;TH sign-in:&nbsp;&nbsp;&nbsp;&nbsp;<?=$th_signin?>
						:OT(<?=$th_ot?>)
						&nbsp;&nbsp;SCH:&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="hidden" id="bid" name="bid" value="<?=$branchid?>">
				        <?=$th_shiftone?> / <?=$th_shifttwo?>
						<div id="therapistavi" class="bg" style="display:none;"></div>
						<a href="javascript:;" onClick="openthQueue('&branchid='+document.getElementById('branch_id').value+'&date='+document.getElementById('hidden_date').value,'thinfo.php','thinfo');" > 
                        <img src="/images/icon_queue.png" class="link" height="16px" width="16px" border="0" title="Therapist Queue"></a>
				    </td>
			        </tr>
    			</table>    
    	  </td>
  	   </tr>
				    <tr>
				        <td height="1" style="background:<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
				    </tr>
</table> 
</div>
  	</td>
  </tr>
  <tr>
  		<td valign="top" style="margin-top:0px;margin-left:0px;padding-left:15px;">
			<div id="tableDisplay"></div>
		</td>
   </tr>
</table>
</form> 
<?if(!$popupwin){?>
	<div class="hiddenbar"><img id="spLine" src="../../images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('../../images')"/></div>
<?}?>
</body>
</html>