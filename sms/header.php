<?php
	include("include.php");
	require_once("secure.inc.php");
	
	$object = new secure();
	
	$sql = "select * from s_user where u_id=".$_SESSION["__user_id"];
	$rs = $object->getResult($sql);
	$upic=$rs[0]["upic"];
	$uname=$rs[0]["u"];
	$themeid=$object->getIdToText("1","a_company_info","theme","company_id");
	$fontcolor=$object->getIdToText($themeid,"l_theme","theme_color","theme_id");
	$theme=strtolower($object->getIdToText($themeid,"l_theme","theme_name","theme_id"));
?>
<?

//Start : For Sample Site
     function DateDiff($strDate1,$strDate2)
     {
                return (strtotime($strDate2) - strtotime($strDate1))/  ( 60 * 60 * 24 );
     }

if($GLOBALS["global_database"]=="sample" && $_SERVER["SERVER_NAME"]=="sample.localhost"){ 

$defaultapp = "1";
$today_date = date("Y-m-d");

$sql = "select * from a_appointment where appt_id = ".$defaultapp;
$rs_check = $object->getResult($sql);

$default_date = $rs_check[0]["appt_date"];
$diff_date = DateDiff($default_date,$today_date);
$plus_date = "+"."$diff_date"."day";

if($today_date!=$rs_check[0]["appt_date"]){

$sql = "select * from a_appointment";
$rs_app = $object->getResult($sql);

	for($i=0;$i<$rs_app["rows"];$i++){
		$app_data[$i]["appt_id"] = $rs_app[$i]["appt_id"];
		$app_data[$i]["appt_date"] = date("Y-m-d ", strtotime($plus_date,strtotime($rs_app[$i]["appt_date"])));
	
	$update = "UPDATE `sample`.`a_appointment` " .
			"SET `appt_date` = '".$app_data[$i]["appt_date"]."' " .
			"WHERE `a_appointment`.`appt_id` ='".$app_data[$i]["appt_id"]."'";
	$rs = $object->setResult($update);
	}	

$sql = "select * from a_bookinginfo";
$rs_book = $object->getResult($sql);

	for($i=0;$i<$rs_book["rows"];$i++){
		 $book_data[$i]["book_id"] = $rs_book[$i]["book_id"];
		 $book_data[$i]["b_appt_date"] = date("Y-m-d ", strtotime($plus_date,strtotime($rs_book[$i]["b_appt_date"])));	
	
	$update = "UPDATE `sample`.`a_bookinginfo` " .
			"SET `b_appt_date` = '".$book_data[$i]["b_appt_date"]."' " .
			"WHERE `a_bookinginfo`.`book_id` ='".$book_data[$i]["book_id"]."'";
	$rs = $object->setResult($update);
	}

$sql = "select * from r_maintenance";
$rs_main = $object->getResult($sql);

	for($i=0;$i<$rs_main["rows"];$i++){
		$main_data[$i]["rm_id"] = $rs_main[$i]["rm_id"];
		$main_data[$i]["appt_date"] = date("Y-m-d ", strtotime($plus_date,strtotime($rs_main[$i]["appt_date"])));	
	
	$update = "UPDATE `sample`.`r_maintenance` " .
			"SET `appt_date` = '".$main_data[$i]["appt_date"]."' " .
			"WHERE `r_maintenance`.`rm_id` ='".$main_data[$i]["rm_id"]."'";
	$rs = $object->setResult($update);
	}	
}
}
//End : For Sample Site
?>
<script type="text/javascript">
		try{
			window.parent.mainFrame.location;
			window.parent.leftFrame.location;
		}catch(e){
			document.location.href="home.php";
		}
</script>
<link href="css/styles.css" rel="stylesheet" type="text/css">
<script src="scripts/AC_RunActiveContent.js" type="text/javascript"></script>
<!--
<table width="100%" height="58" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="center">
		<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="700" height="65" title="SPA MANAGEMENT SYSTEM">
		 		 <param name="wmode" value="transparent"> 
				  <param name="movie" value="flash/header sms.swf">
				  <param name="quality" value="high">
				  <embed src="flash/header sms.swf" wmode="transparent" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="700" height="65"></embed>
		</object></td>
		 </td>
	  <td class="welcome" width="100" align="center">
      		<font color="<?=$fontcolor?>" class="mainheader"><b>WELCOME<br><?=strtoupper($uname)?></b></font>
      		<br/><b><a href="logout.php" target="_parent" style="color:#666666;">Logout</a></b>
      </td>
      <td width="70" align="right">
      	<div style="position: absolute;top: 3px;right: 9px">
      		<img src="<?=$customize_part?>/images/user/<?=$upic?>" width="60px" height="60px">
       	</div>
      </td>
	</tr>
</table>
<div class="empframe"><img src="images/<?=$theme?>/header/emp_frame.png"/></div>-->