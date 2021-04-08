<?
include("../include.php");

require_once("appt.inc.php");


$obj = new appt();


$u_id = $obj->getUserIdLogin();
	
/*if(!$obj->check_login()) {
	header("location: ../index.php");
}
else {
	if(!$obj->check_permission($u_id,"8,14,1","see")) {
		header("location: ../perror.php");
	}
}*/
	



$nodata = "No have";

$book_id = $obj->getParameter("book_id");
$book_id = $obj->getIdToText($book_id,"c_bpds_link","bpds_id","tb_id","tb_name=\"a_bookinginfo\"");
$branch = $obj->getIdToText($obj->getParameter("branch"),"bl_branchinfo","branch_name","branch_id");
$cs_date = $obj->getParameter("cs_date");
$dr_pu = $obj->getIdToText($obj->getParameter("dr_pu"),"l_employee","emp_nickname","emp_id"); 
$dr_tb = $obj->getIdToText($obj->getParameter("dr_tb"),"l_employee","emp_nickname","emp_id"); 
$dr_pu_time = $obj->getIdToText($obj->getParameter("dr_pu_time"),"p_timer","time_start","time_id"); 
$dr_tb_time = $obj->getIdToText($obj->getParameter("dr_tb_time"),"p_timer","time_start","time_id"); 
$cs_name = $obj->getParameter("cs_name");
$cs_hotel = $obj->getIdToText($obj->getParameter("cs_hotel"),"al_accomodations","acc_name","acc_id");
$dr_pu_place = $obj->checkParameter($obj->getParameter("dr_pu_place"),'-');
//if(str_replace(" ","",$dr_pu_place)==""){$dr_pu_place=$cs_hotel;}
$dr_tb_place = $obj->checkParameter($obj->getParameter("dr_tb_place"),'-');
//if(str_replace(" ","",$dr_tb_place)==""){$dr_tb_place=$cs_hotel;}
$cs_roomnum = $obj->getParameter("cs_roomnum");
if(!$cs_roomnum)
	$cs_roomnum = $nodata;

$cs_ttpp = $obj->getParameter("cs_ttpp");
$cs_rs = $obj->getIdToText($obj->getParameter("cs_rs"),"l_employee","emp_nickname","emp_id"); 
if($obj->getParameter("cs_rs")<=1)
	$cs_rs = $nodata;
	
$cs_bcom = $obj->getIdToText($obj->getParameter("cs_bcom",false),"al_bookparty","bp_name","bp_id");

$cs_bp = $obj->getParameter("cs_bp",false);
//$cs_name = $obj->get_parameter("cs_name");


?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href='css/report.css' type='text/css' rel='stylesheet'>
<body>
<table width="225" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td width="10">&nbsp;</td>
	<td>
		<table width="215" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td align="center" colspan="4">*************************</td>
		</tr>
		<tr>
			<td align="center" colspan="4"><b>Transfer Slip</b></td>
		</tr>
		<tr>
			<td align="center" colspan="4" height="30">*************************</td>
		</tr>
		<tr>
			<td align="center" colspan="4"><b>From</b></td>
		</tr>
		<tr>
			<td align="center" colspan="4"><b><?=$branch?></b></td>
		</tr>
		<tr height="40">
			<td align="center" colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td align="left" colspan="4">Book ID : <b class="style1"><?=$book_id?></b></td>
		</tr>
		<tr>
			<td align="left" colspan="4">Date : <b class="style1"><?=$cs_date?></b></td>
		</tr>
		<tr>
			<td align="left" colspan="4">Name : <b class="style1"><?=$cs_name?></b></td>
		</tr>
		<tr>
			<td align="left" colspan="4">Hotel : <b class="style1"><?=$cs_hotel?></b></td>
		</tr>
		<tr>
			<td align="left" colspan="4">Room Number : <b class="style1"><?=$cs_roomnum?></b></td>
		</tr>
		<tr>
			<td align="left" colspan="4">Many People : <b class="style1"><?=$cs_ttpp?></b></td>
		</tr>
		<tr>
			<td align="left" colspan="4">Reservation By : <b class="style1"><?=$cs_rs?></b></td>
		</tr>
<?		
		if($cs_bcom) {
			echo "<tr>";		
			echo "<td align=\"left\" colspan=\"4\">Book Company : <b class=\"style1\">$cs_bcom</b></td>";
			echo "</tr>";
		}
		
		if($cs_bp) {
			echo "<tr>";
			echo "<td align=\"left\" colspan=\"4\">Book Person : <b class=\"style1\">$cs_bp</b></td>";
			echo "</tr>";
		}
?>
		<tr>			
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td align="right" width="80">Pickup :</td>
			<td align="center" width="50">&nbsp;<b><?=($obj->getParameter("dr_pu")<=1)?"No select":$dr_pu?></b></td>
			<td align="center" width="85"><b style="color:#ff0000"><?=$dr_pu_time?></b></td>
			<td align="left" width="85"><b><?=$dr_pu_place?></b></td>
		</tr>
		<tr>
			<td align="right" width="80">Takeback :</td>
			<td align="center" width="50">&nbsp;<b><?=($obj->getParameter("dr_tb")<=1)?"No select":$dr_tb?></b></td>
			<td align="center" width="85"><b style="color:#ff0000"><?=$dr_tb_time?></b></td>
			<td align="left" width="85"><b><?=$dr_tb_place?></b></td>
		</tr>
		<tr>			
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td align="center" colspan="4" height="50" valign="bottom"><b>Thank You</b></td>
		</tr>
		<tr>
			<td align="center" valign="middle" height="60" colspan="4">*************************</td>
		</tr>
		</table>
	</td>
</tr>
</table>
</body>