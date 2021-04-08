<?php
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

$twphone=$obj->getParameter("twphone");
$sql="select a_bookinginfo.book_id,a_bookinginfo.b_customer_name, a_bookinginfo.b_appt_date, bl_branchinfo.branch_name, a_bookinginfo.b_qty_people " .
		"from a_bookinginfo,bl_branchinfo,d_indivi_info " .
		"where a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
		"and d_indivi_info.book_id=a_bookinginfo.book_id " .
		"and d_indivi_info.cs_phone=$twphone " .
		"order by a_bookinginfo.book_id,a_bookinginfo.b_appt_date";
$rs=$obj->getResult($sql);

?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Customer History</title>
<link href="../../css/style.css" rel="stylesheet" type="text/css">
<script src="../scripts/component.js" type="text/javascript"></script>
<script src="../scripts/ajax.js" type="text/javascript"></script>
<body><br>
<br/>
<div class="group5" width="100%" >
<fieldset>
<legend><b>History of customer's Phone Number : <span class="style1"> <?=$twphone?></span></b></legend>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cusinfo">
  <tr>
    <td colspan="2">
    	<table width="100%" border="1" cellspacing="0" cellpadding="0" class="comment">
    	<tr>
          <td class="mainthead">Appointment Date</td>
          <td class="mainthead">Branch</td>
          <td class="mainthead">Book ID</td>
          <td class="mainthead">Booking name</td>
          <td class="mainthead">Room</td>
          <td class="mainthead">Hour</td>
          <td class="mainthead">Package</td>
          <?
          	$maxMsg=0;
			$chkId=false;
			for($i=0;$i<$rs["rows"];$i++){
				$sql = "select * from d_indivi_info where book_id=".$rs[$i]["book_id"]." and cs_phone=$twphone order by book_id asc";
				$rsIndi = $obj->getResult($sql);
				for($j=0;$j<$rsIndi["rows"];$j++){
					$sql = "select massage_id from da_mult_msg where indivi_id=".$rsIndi[$j]["indivi_id"];
					$rsMsg = $obj->getResult($sql);
					if($maxMsg<$rsMsg["rows"]){
						$maxMsg=$rsMsg["rows"];
					}
				}
			}
			
			for($i=1;$i<=$maxMsg;$i++){
					echo "<td class=\"mainthead\">Massage $i</td>";
			}
          ?>
          <td class="mainthead">Therapist</td>
        </tr>
  		<?	$chkColor=0;
			for($i=0;$i<$rs["rows"];$i++){
				$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
				$pagename = "manageBooking".$rs[$i]["book_id"];
				$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
				$sql = "select * from d_indivi_info where book_id=".$rs[$i]["book_id"]." and cs_phone=$twphone order by book_id asc";
				$rsIndi = $obj->getResult($sql);
				for($j=0;$j<$rsIndi["rows"];$j++){
					$packageDetail ="";
					
					if($rsIndi[$j]["strength_id"]!=1){
						if($packageDetail==""){
							$packageDetail.="Strength : ".$obj->getIdToText($rsIndi[$j]["strength_id"],"l_strength","strength_type","strength_id");
						}else{
							$packageDetail.="<br>Strength : ".$obj->getIdToText($rsIndi[$j]["strength_id"],"l_strength","strength_type","strength_id");
						}
						
					}
					if($rsIndi[$j]["scrub_id"]!=1){
						if($packageDetail==""){
							$packageDetail.="Scrub : ".$obj->getIdToText($rsIndi[$j]["scrub_id"],"db_trm","trm_name","trm_id");
						}else{
							$packageDetail.="<br>Scrub : ".$obj->getIdToText($rsIndi[$j]["scrub_id"],"db_trm","trm_name","trm_id");
						}
					}
					
					if($rsIndi[$j]["wrap_id"]!=1){
						if($packageDetail==""){
							$packageDetail.="Wrap : ".$obj->getIdToText($rsIndi[$j]["wrap_id"],"db_trm","trm_name","trm_id");
						}else{
							$packageDetail.="<br>Wrap : ".$obj->getIdToText($rsIndi[$j]["wrap_id"],"db_trm","trm_name","trm_id");
						}
					}
					if($rsIndi[$j]["bath_id"]!=1){
						if($packageDetail==""){
							$packageDetail.="Bath : ".$obj->getIdToText($rsIndi[$j]["bath_id"],"db_trm","trm_name","trm_id");
						}else{
							$packageDetail.="<br>Bath : ".$obj->getIdToText($rsIndi[$j]["bath_id"],"db_trm","trm_name","trm_id");
						}
					}
					if($rsIndi[$j]["facial_id"]!=1){
						if($packageDetail==""){
							$packageDetail.="Facial : ".$obj->getIdToText($rsIndi[$j]["facial_id"],"db_trm","trm_name","trm_id");
						}else{
							$packageDetail.="<br>Facial : ".$obj->getIdToText($rsIndi[$j]["facial_id"],"db_trm","trm_name","trm_id");
						}
					}
						
					if($packageDetail!=""){$title="title=\" header=[Package Detail] body=[".htmlspecialchars($packageDetail)."]\"  style=\"color: #ff0000; cursor: pointer;\"";}else{$title="";}
						$sql = "select hour_id from da_mult_th where indivi_id=".$rsIndi[$i]["indivi_id"]." order by hour_id desc";
						$rsHour = $obj->getResult($sql);
						$sql = "select da_mult_th.therapist_id,l_employee.emp_nickname from da_mult_th,l_employee " .
								"where indivi_id=".$rsIndi[$i]["indivi_id"]." " .
								"and l_employee.emp_id=da_mult_th.therapist_id";
						$rsMult_th = $obj->getResult($sql);
						if(($chkColor%2)==0){
			            	echo "<tr class=\"content_list\" onmouseover=\"high(this)\" onmouseout=\"low(this)\">";
						}else{
							echo "<tr class=\"content_list1\" onmouseover=\"high(this)\" onmouseout=\"low(this)\">";
						} 
		?>
          <td><?=$dateobj->convertdate($rs[$i]["b_appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
          <td><?=$rs[$i]["branch_name"]?>&nbsp;</td>
          <td><a href='javascript:;;' onClick="newwindow('../../appt/<?=$url?>','<?=$pagename?>')" class="menu"><?=$bpdsid?></a>&nbsp;</td>
          <td><?=$rs[$i]["b_customer_name"]?>&nbsp;</td>
          <td><?=$obj->getIdToText($rsIndi[$i]["room_id"],"bl_room","room_name","room_id")?>&nbsp;</td>
          <td><?=$obj->getIdToText($rsHour[0]["hour_id"],"l_hour","hour_name","hour_id")?>&nbsp;</td>
          <td <?=$title?>><?=($rsIndi[$i]["package_id"]==1)?"-":$obj->getIdToText($rsIndi[$i]["package_id"],"db_package","package_name","package_id")?>&nbsp;</td>
       		<? for($td=0;$td<$maxMsg;$td++){
					if($rsMsg[$td]["massage_id"]!="" && $rsMsg[$td]["massage_id"]!=1){
						$sql = "select trm_name from db_trm where trm_id=".$rsMsg[$td]["massage_id"];
						$rsTrm = $obj->getResult($sql);
						echo "<td>".$rsTrm[0]["trm_name"]."&nbsp;</td>";
					}else{
						echo "<td>&nbsp;&nbsp; -</td>";
					}
							
				}
       			$thName="&nbsp;";	
				for($th=0;$th<$rsMult_th["rows"];$th++){
						if($th==0){
								$thName = $rsMult_th[0]["emp_nickname"];
						}else{
								$thName .= " , ".$rsMult_th[$th]["emp_nickname"];
						}
				}
				echo "<td> $thName</td>";
			?>
		</tr>
        		<?		$chkColor++;
					} 
 		 } ?>
      </table>
    </td>
  </tr>
</table>
<br>
</fieldset>
</body>
