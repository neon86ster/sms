<?
include("../include.php");
require_once("appt.inc.php");
$obj = new appt(); 
$obj->setDebugStatus(false);	

$obj->setErrorMsg("");
// set branch select box for limited branch selected list show only branch name in same city 
$obj->setLimitBranchOnLocation();

/********************************************************
 * Initial all information
 ********************************************************/ 	
$chkFirst = $obj->getParameter("chkFirst","");
$newLogin = $obj->getParameter("newLogin","");
$successmsg = $obj->getParameter("successmsg","");
$errormsg = "";
$initstatus = false;


if($chkFirst!="first"){
	$initstatus = true;
} 

if($initstatus==false){
	$cs = $obj->getParameter("cs",false);
	$cc = $obj->getParameter("cc",false);
	$userid = $_SESSION["__user_id"];
}
if(!isset($cc["cc"])){$cc["cc"]="";}
if(!isset($cs["apptdate"])){$cs["apptdate"]="";}
if(!isset($cs["reasons"])){$cs["reasons"]="";}
if(!isset($cs["room"])){$cs["room"]="";}
if(!isset($cs["tthour"])){$cs["tthour"]="";}
if(!isset($cs["appttime"])){$cs["appttime"]="";}
if(!isset($cs["branch"])){$cs["branch"]="";}
if(!isset($cc["comment"])){$cc["comment"]="";}


// check edit status and set book id
$rmid = $obj->getParameter("rmid",false);
if($rmid){$status="edit";}else{$status="add";}


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


// check before closing add/edit maintunance room windows
$close = $object->getParameter("close",false);
if($close && $status=="edit"){
	$chkrm = $object->beforeCloseRM($rmid);
	if($chkrm){?><script language="javascript">window.close();</script><?}
	else{$errormsg ="Can't Closing this window. Please try again!!";}
}


// check if have user use in edit page 
if($status=="edit"&&$initstatus){
	$uid=$object->checkRMUse($rmid);
	if($uid!=false){
		?>
		<script language="javascript">
			alert("This page is used by <?=$obj->getIdToText($uid,"s_user","u","u_id")?>");
			<?=($newLogin)?"opener.parent.location.reload();":""?>
			window.close();
		</script>
		<?
	}else{
		$object->startRM($rmid);
	}
}

// convert date status for permission checking
 if($cs["apptdate"]==""){
	$date=$obj->getParameter("date",date($sdateformat));
	$date=$dateobj->convertdate($date,$sdateformat,'Y-m-d');
}else{
	$date=$dateobj->convertdate($cs["apptdate"],$sdateformat,'Y-m-d');
}

// check reservation view limit date permission
$now=$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$sdateformat",$cs["branch"]);

if($status == "edit"){
	$preViewDate="";
	$afterViewDate="";
	$chkRsViewDate = $object->isReservationLimit();
	if($chkRsViewDate){
		$preViewDate = $object->getReservationDate("pre_viewdate","appt_viewchk");
		$afterViewDate = $object->getReservationDate("after_viewdate","appt_viewchk");
		$chkRsDate = $object->checkReservationDate($date,'Y-m-d',$preViewDate,$afterViewDate,$now);
		if(!$chkRsDate){
			$chkPageView=false;
		}
	}
}

// check reservtion edit date limit 
$preEditDate="";
$afterEditDate="";
$checkApptPage="false";	// flat for calendar chooser 
// checking if appt_editchk was check
$chkRsEditDate = $object->isReservationLimit("appt_editchk");
if($chkRsEditDate){
	$preEditDate= $object->getReservationDate("pre_editdate","appt_editchk");
	$afterEditDate= $object->getReservationDate("after_editdate","appt_editchk");
	$chkRsDate= $object->checkReservationDate($date,'Y-m-d',$preEditDate,$afterEditDate,$now);
	if(!$chkRsDate){
		$chkPageEdit=false;
		// if reservation edit date was check then reset appointment date
		if($status=="add"){
			$cs["saledate"] = date($sdateformat);
			$cs["hidden_saledate"] = date('Ymd');
		}
	}
}else{
// seting flat for calendar chooser 
	$preEditDate="notCheck";
	$afterEditDate="notCheck";
}

// Check this booking is in location the same user location 
$userLocationChk = $object->isEditBookInLocation($cs["branch"]);
if($status=="edit" && $userLocationChk==false){
	$chkPageEdit=false;
}

//Close window if user can't view or edit booking 
if(!$chkPageView){
		?>
		<script language="javascript">
			alert("You can't access this booking.");
			<?=($newLogin)?"opener.parent.location.reload();":""?>
			//window.close();
		</script>
	<?		
}

/********************************************************
 * initial all variable value
 ********************************************************/ 	
$pagename = "manage_mroom.php";

if(!$cs["apptdate"]){
	$cs["apptdate"] = date($sdateformat);
	$cs["hidden_apptdate"] = date("Ymd");
}
if($status=="add"&&$initstatus){
	$cs["branch"] = $obj->getParameter("branch_id",false);
	$cs["apptdate"] = $obj->getParameter("date",false);
	$cs["hidden_apptdate"] = $dateobj->convertdate($cs["apptdate"],$sdateformat,'Ymd');
	$cc["cc"] = "";
	$cc["date"] = $obj->getParameter("date",false);
	$cc["hidden_date"] = $dateobj->convertdate($cc["date"],'Y-m-d',"Ymd");
	$userid = $_SESSION["__user_id"];
}

if($initstatus&&$status=="edit"){
	$sql="select * from r_maintenance where rm_id=$rmid";
	$rs = $obj->getResult($sql);
	$cs["reasons"] = $rs[0]["reasons"];
	$cs["branch"] = $rs[0]["branch_id"];
	$cs["room"] = $rs[0]["room_id"];
	$cs["apptdate"] = $dateobj->convertdate($rs[0]["appt_date"],'Y-m-d',$sdateformat);
	$cs["hidden_apptdate"] = $dateobj->convertdate($rs[0]["appt_date"],'Y-m-d','Ymd');
	$cs["appttime"] = $rs[0]["appt_time"];
	$cs["tthour"] = $rs[0]["hour_id"];
	$cc["cc"] = ($rs[0]["set_cancel"]==1)?"checked":"";
	$cc["date"] = ($rs[0]["set_cancel"]==1)?$dateobj->convertdate($rs[0]["cancel_date"],'Y-m-d',$sdateformat):date($sdateformat);
	$cc["hidden_date"] = ($rs[0]["set_cancel"]==1)?$dateobj->convertdate($rs[0]["cancel_date"],'Y-m-d',"Ymd"):date("Ymd");
	$cs["insertdate"] = $rs[0]["c_lu_date"];
	$cs["insertuser"] = $rs[0]["c_lu_user"];
}

if($cc["cc"]!="checked"){
	$cc["date"] = date($sdateformat);
	$cc["hidden_date"] = date("Ymd");
	$cc["comment"] = "";
}
////////////////// Check user change for update data ////////////////////////
if(empty($_POST["nowUserId"])){
	$_POST["nowUserId"]=$object->getUserIdLogin();
}
$chkUser=true;
if($object->getUserIdLogin()!=$_POST["nowUserId"]){
	$chkUser=false;
}
////////////////// End check user change for update data ////////////////////
///////////////////////// Check User Login //////////////////////////////
//print_r($_SERVER);
//echo "<br>".$_SERVER["REQUEST_URI"];
if($initstatus){
	$currentUrl=$_SERVER["REQUEST_URI"]."&uId=".$_POST["nowUserId"];
}else{
	$currentUrl=$_REQUEST["referer"];
}	
//echo "test";
//echo "<br>$currentUrl";
if(!$object->checkLogin()){
	header("location:../login.php?url=|$currentUrl");
}
///////////////////////// End Check User Login /////////////////////////////

if(isset($_POST["add"]) && $chkUser) {
	$chkroom = $obj->checkEmptyRoom($cs["hidden_apptdate"],$cs["appttime"],$cs["tthour"],$cs["room"],0,$rmid);
	if($chkroom){
		$err_rm=$obj->getIdToText($chkroom,"bl_room","room_name","room_id");
		$errormsg = "Please check Hour & Time Appointment in room: $err_rm";
	}else{
			if($status=="edit") {
				$errormsg = false;
				$id = $obj->editRM($rmid,$cs,$cc);
				if($id){
						$successmsg="Update Success!!";
						header("location: manage_mroom.php?rmid=$rmid&successmsg=$successmsg");
				}else{
					$errormsg = $obj->getErrorMsg();
				}
			} else {
				$errormsg = false;
				$errdate = false;
				if($cs["hidden_apptdate"]<$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"Ymd",$cs["branch"])){
					$errdate = true;
					$obj->setErrorMsg("Please change appointment date to future or today!!");
				}
				if($errdate==false){
					$id = $obj->addRM($cs,$cc);
					if($id){
							//$successmsg="Update Success!!";
							//header("location: manage_mroom.php?rmid=$id&successmsg=$successmsg");
							?>
								<script language="javascript">
									window.close();
								</script>
							<?
					}else{
						$errormsg = $obj->getErrorMsg();
					}
				}else{
					$errormsg = $obj->getErrorMsg();
				}
			}
	}
}
$initstatus = false;
$obj->setBranchid($cs["branch"]);
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Maintenance Room</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script src="scripts/ajax.js" type="text/javascript"></script>
<script src="scripts/component.js" type="text/javascript"></script>
<script src="scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="scripts/datechooser/datechooser.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="scripts/datechooser/datechooser.css">
</head>
<body style="background-color: #eae8e8;">
<form name='mroom' id='mroom' action='<?=$pagename?>' method='post'>
  <table width="100%" border="0px">
    <tr>
      <td class="header" style="padding-bottom:5px">
      <input type="hidden" id="referer" name="referer" value="<?=$currentUrl?>"/>
      <input type="hidden" id="nowUserId" name="nowUserId" value="<?=$object->getUserIdLogin()?>"/>
      <? if($status=="add"){?>
        <b>Add Maintenance Room</b>
        <? }else{?>
        <b>Maintenance Room ID: </b><b class="style1">
        <?=$rmid?></b>
        <input type="hidden" id="rmid" name="rmid" value="<?=$rmid?>"/>
        <? }?>
      </td>
      <td class="header" style="padding-left:0px">
      <table width="100%">
          <tr>
            <td style="padding-left:10px"><b>Add
              <? if($status=="edit"){?>
              by:</b><b class="style1">
              <?=$obj->checkParameter($obj->getIdToText($cs["insertuser"],"s_user","u","u_id"),"- ")?>
              </b>
              <? 
              // get add date and add time             
              $d = substr($cs["insertdate"],0,10);
              $t = substr($cs["insertdate"],11,8);
                                          
              $data = $dateobj->timezone_depend_branch($d,$t,"$ldateformat, H:i:s",$cs["branch"]);
              
              ?>
              <b>
              <?=$data?>
              </b>
              <input type="hidden" id="cs[insertuser]" name="cs[insertuser]" value="<?=$cs["insertuser"]?>"/>
              <input type="hidden" id="cs[insertdate]" name="cs[insertdate]" value="<?=$cs["insertdate"]?>"/>
              <? 
            	}else{  		  	
            	echo "by: <b class=\"style1\">".$obj->getIdToText($obj->getUserIdLogin(),"s_user","u","u_id")."</b> <b>".
				$data = $dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$ldateformat, H:i:s","$cs[branch]");
            	}
            ?>
              </b> </td>
            <td align="right"></td>
            <td width="350px" align="right"><span class="tabmenuheader" style="margin-right:20px">
              <input type="submit" id="add" name="add" value=" <?=($status=="edit")?"Update":"Save"?> " class="button" onClick="this.form.submit()" />
  			  <input type="submit" name="close" value=" Close " class="button" <? if($status=="edit"){?>onclick="this.form.submit();"<?}else{?>onclick="window.close()"<? }?> />
             
              &nbsp;&nbsp; </span> </td>
          </tr>
        </table>
        <table width="100%" height="20" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td class="tabmenuheader">
            <td class="tabmenuheader" align="right" style="padding-right:15px;v-align:middle;"><b class="style1">
             <span id="errormsg" class="style1" ><?=$errormsg?></span></b>
             <input type="hidden" id="status" name="status" value="<?=$status?>"/>
            <span id="successmsg" class="style3" style="display: block;"><b class="style3"><?=($newLogin)?"":$successmsg?></b></span>
             </td>
          </tr>
        </table></td>
    </tr>
    <tr>
    <td colspan="2">
	    <br />
	    <div class="group5" width="100%" >
	    <fieldset>
   			<legend><b>Maintenance Room Information</b></legend>
   			<table cellspacing="0" cellpadding="0" border="0" class="cusinfo" width="100%">
	            <tr>
					<td width="120px">Branch : <span class="style1">*</span></td>
                	<td width="140px"><?=$obj->makeListbox("cs[branch]","bl_branchinfo","branch_name","branch_id",$cs["branch"],true,"branch_name","branch_active","1","branch_name not like 'All'")?></td></td>
					<td rowspan="6" align="left" style="vertical-align:top; padding-left:0px;">
					
					
					<table cellspacing="0" cellpadding="0" width="90%" border="0">
		            	<tr>
		                	<td style="padding-left:20px;vertical-align:top;" width="146">Reasons : </td>
	                	  <td width="1100" align="left"><textarea name="cs[reasons]" id="cs[reasons]" rows="2" class="bcomment"><?=$cs["reasons"]?></textarea></td>
		                </tr>
		                <tr>
		                	<td colspan="2" style="padding-left:20px;vertical-align:top;">
							<input type='checkbox' id='cc[cc]' name='cc[cc]' value='checked' onClick="showHideCheck('CBC','cc[cc]');" <?=$cc["cc"]?> class="checkbox" />
		                	Cancel <br/><br/>
		                	<span id="CBC" name="CBC" <?=($cc["cc"])?"style=\"display:block\"":"style=\"display:none; \""?>>  
		                        <table cellpadding="0" cellspacing="0" width="100%">
		                         	<tr>
										<td width="12%">Date of cancelation:</td>
										<td width="88%"><input id="cc[date]" name="cc[date]" value="<?=$cc["date"]?>" readonly="1" class="textbox" type="text" style="width:85px"/>
					                              	<input id='cc[hidden_date]' name='cc[hidden_date]' value="<?=$cc["hidden_date"]?>" type="hidden"/>
					                                &nbsp;&nbsp;<img src="scripts/datechooser/calendar.gif" alt="" onClick="showChooser(this, 'cc[date]', 'date_showSpan1', 1900, 2100, '<?=$sdateformat?>',false,false,'notCheck','notCheck');" />
	                                  <div id="date_showSpan1" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div></td>
					                </tr>
							        <tr>
					                    <td>Reason for cancelation:</td>
										<td><input type='text' id='cc[comment]' name='cc[comment]' value="<?=$cc["comment"]?>" size='23' /></td>
					                </tr>
                            </table>
                       		</span>   
                   		  </td>
		                </tr>
		            </table>
					
					
					</td>
		        </tr>
	            <tr>
					<td width="120px">Maintenance Room : <span class="style1">*</span></td>
                	<td width="140px"><?=$obj->makeListbox("cs[room]","bl_room","room_name","room_id",$cs["room"],0,"room_name","branch_id",$cs["branch"],"room_active=1");
                	?></td>
					<td rowspan="6" align="left" style="vertical-align:top; padding-left:0px;">&nbsp;</td>
		        </tr>
		        <tr>
		        	<td width="120px">Maintenance Date : <span class="style1">*</span></td>
		            <td width="140px"><input id='cs[apptdate]' name='cs[apptdate]' value="<?=$cs["apptdate"]?>" readonly="1" class="textbox" type="text" style="width:85px"/>
                              <input id='cs[hidden_apptdate]' name='cs[hidden_apptdate]' value="<?=$cs["hidden_apptdate"]?>" type="hidden"/>
                               &nbsp;&nbsp;<img src="scripts/datechooser/calendar.gif" alt="Date Appointment" onClick="showChooser(this, 'cs[apptdate]', 'date_showSpan', 1900, 2100, '<?=$sdateformat?>', false,<?=$checkApptPage?>,'<?=$preEditDate?>','<?=$afterEditDate?>');" />
                                <div id="date_showSpan" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div></td>
		        </tr>
		        <tr>
                    <td>Time Appointment : <span class="style1">*</span></td>
                    <td><?=$obj->makeListbox("cs[appttime]","p_timer","time_start","time_id",$cs["appttime"],1)?></td>
                </tr>
		        <tr>
		        	<td width="100px">Total Hours : <span class="style1">*</span></td>
                    <td  width="120px">
                    <?=$obj->makeListbox("cs[tthour]","l_hour","hour_name","hour_id",$cs["tthour"],1,"hour_name",0,0,"hour_id>1 and hour_name >= \"00:30:00\"")?>
                    </td>
		        </tr>
		    </table>
    	</fieldset>
	    </div>
	    </td>
    </tr>
    <tr>
    <td colspan="2"></td>
  	</tr>
 </table>
  <input type='hidden' id="chkFirst" name="chkFirst" value='first'>
</form>
</body>
</html>