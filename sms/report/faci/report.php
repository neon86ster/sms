<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid");
$export = $obj->getParameter("export",false);

if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print");
}

if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",30);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Summary Report Specific.xls\"");
	header("Pragma: public");
	header("Expires: 0");
	
}

$reportname = "Facilities Utilization";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
$totaldate = ( strtotime($end_date) - strtotime($begin_date) ) / ( 60 * 60 * 24 )+1;

$percentf=0;
//Branch ALL
if(!$branch_id){

	$peid = $obj->getParameter("peid",3);
	$tp_id = $peid;
	$chksql = "select * from l_timeperiod where tp_id=$tp_id";
	$timeperiodrs = $obj->getResult($chksql);
	$time_period = $timeperiodrs[0]["tp_name"];
	$time_period_distance = $timeperiodrs[0]["tp_distance"];
	
	// system's time period
	$sql = "select * from p_timer";
	$timeperiodrs = $obj->getResult($sql);
	$timeperiod = array();
	for($i=0;$i<$timeperiodrs["rows"];$i++){
			$timeperiod[$timeperiodrs[$i]["time_id"]] = $timeperiodrs[$i]["time_start"];
	}
?>
	<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
	<span class="pdffirstpage"/>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
		 <td valign="top" style="padding:10 20 20 20;" width="100%" align="center">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
				<?for($i=0;$i<$colspan;$i++){?>
					<td width="<?=100/$colspan?>%"></td>
				<?}?>
				</tr>
				
				<?
				 //Get All Branch
			        $sql = "select branch_id, branch_name from bl_branchinfo where branch_id<>1 ";
			        		//if($city_id){$sql .= "and city_id=".$city_id." ";}else
			        		if($branch_id){$sql .= "and branch_id=".$branch_id." ";}
			        $sql.= "and branch_active=1 and branch_utilization<>1 order by branch_name asc";
			        $rsBranch = $obj->getResult($sql);
			        $colspan=$rsBranch["rows"]+2;
			        
	// ################ Get time ###############
	//$sql_time="select min(start_time_id) as start_time_id,max(close_time_id) as close_time_id " .
	//		"from bl_branchinfo where branch_id<>1 and branch_active=1 and branch_utilization<>1 ";
	$sql_time="select * from a_company_info";
	$rs_time = $obj->getResult($sql_time);

	$starttime_id = $rs_time[0]["start_time_id"];
	$closetime_id = $rs_time[0]["close_time_id"];
	$chksql = "select * from p_timer where time_id between $starttime_id and $closetime_id";
	$timeline = $obj->getResult($chksql);
	$count_time = $timeline["rows"];
				?>
				<tr>
			    	<td class="reporth" width="100%" align="center" colspan="<?=$colspan?>">
			    		<b><p>Spa Management System</p></b>
			    		<b><?=$reportname?></b><br>
			    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br></p>
			    		<p><b style='color:#ff0000'><?="Branch : "?>
			    		<?
			    			for($j=0; $j<$rsBranch["rows"]; $j++){
			    				$nbranchdetail[$j] = $rsBranch[$j]["branch_name"];
			    			}
			    			if($nbranchdetail){
			  	  				$NbranchSrdString = implode(", ", $nbranchdetail); 
			  				}
			  				echo $NbranchSrdString;
			    		?>
			    		</b><br></p>
			    	</td>
				</tr>
				
				<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Time</b></td>
				<?for($i=0;$i<$rsBranch["rows"];$i++){?>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b><?=$rsBranch[$i]["branch_name"]?></b></td>
				<?}?>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
				</tr>
				
				<?
				$cnt_row=0;
				for($i=0; $i<$timeline["rows"]; $i=$i+$time_period_distance){
					$timestamp = strtotime($timeline[$i]["time_start"]);
								$startptimeid = $timeline[$i]["time_id"];
								$startptime = date("H:i",$timestamp);
								if(isset($timeline[$i+$time_period_distance]["time_start"])){
									$timestamp = strtotime($timeline[$i+$time_period_distance]["time_start"]);
									$lastptime = date("H:i",$timestamp);
								}else{
									$timestamp = strtotime($timeline[$i]["time_start"]);
									$etime = strtotime("+$time_period minutes", $timestamp);
									$lastptime = date('H:i', $etime);
								}
					if($timeline[$i]["time_id"]!=$closetime_id){
									$lastptimeid = $startptimeid + $time_period_distance;
									$cstartptime = "";
									//$style = "border-top:solid 0px;";
									$style = "";
									$check = 0;
					for($j=$i;$j<$i+$time_period_distance;$j++){
										if(isset($timeline[$j]["time_id"])&&$timeline[$j]["time_id"]%12==1){
												$check = 1;
												$cstartptime = strtotime($timeperiod[$timeline[$j]["time_id"]]);
												$cstartptime = date("H:i",$cstartptime);
												$style .= "border-bottom:dashed #000000 1px;color:#000000;";
												break;
										}
										if(isset($timeline[$j]["time_id"])&&$timeline[$j]["time_id"]%12==7){
												$check = 2;
												$style .= "border-bottom:solid 1px;color:#000000;";
												break;
										}
					}
				//$class="onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";
				?>
				<tr height="40" <?=$class?>>
					<?
					echo "<td align=\"center\" style=\"background-color:#d3d3d3;$style\">&nbsp;&nbsp;<span style='color:#000000;'>$startptime-$lastptime</span>&nbsp;&nbsp;</td>\n";
						for($k=0;$k<$rsBranch["rows"];$k++){
							
							$sql_chk="select count(distinct d_indivi_info.room_id) as rcount " .
									",(select count(bl_room.room_id) from bl_room where bl_room.branch_id=".$rsBranch[$k]["branch_id"]." and room_active=1 and room_utilization<>1) as rtotal " .
									"from a_bookinginfo,d_indivi_info where " .
									"a_bookinginfo.b_appt_date>='".$begin_date."' ".
	 								"and a_bookinginfo.b_appt_date<='".$end_date."' " .
	 								
									"and CASE WHEN a_bookinginfo.b_appt_time_id>=$startptimeid and a_bookinginfo.b_appt_time_id<$lastptimeid THEN " .
									"a_bookinginfo.b_appt_time_id>=$startptimeid " .
									"and a_bookinginfo.b_appt_time_id<$lastptimeid " .
									
									"WHEN a_bookinginfo.b_appt_time_id<$startptimeid and a_bookinginfo.b_appt_time_id<$lastptimeid THEN " .
									"(select time_id from p_timer where time_start=SEC_TO_TIME((select TIME_TO_SEC(time_start) from p_timer where time_id=b_appt_time_id)+(select TIME_TO_SEC(hour_name) from l_hour where hour_id =b_book_hour)))>$startptimeid " .
									"END ".
										
									"and a_bookinginfo.b_branch_id=".$rsBranch[$k]["branch_id"]." " .
	 								"and a_bookinginfo.b_set_cancel<>1 " .
	 								"and a_bookinginfo.book_id=d_indivi_info.book_id " .
	 								"group by a_bookinginfo.b_appt_date ";
							
							$getrs=$obj->getResult($sql_chk);
							
							$percentf=0;
							
							if($getrs["rows"]>0){
								for($l=0;$l<$getrs["rows"];$l++){
									$rpercent[$l]=($getrs[$l]["rcount"]/$getrs[$l]["rtotal"])*100;
									$percentf+=$rpercent[$l];
								}
									$percentf=$percentf/$totaldate;

								$sum_percent[$k]+=$percentf;
								$sum_percent_room[$i]+=$percentf;
								$percentf=number_format($percentf,2,".",",");
							}
							$fcolor="#000000";
							if($percentf<25){
								//$fcolor="#ff0000";
								$fcolor="#59da86";
							}else{ 
								if($percentf<75){
									//$fcolor="#ff5a00";
									$fcolor="#fe7e38";
								}else{
									//$fcolor="#005c00";
									$fcolor="#ff4242";
								}
							}
							
							//echo "<td valign=middle align=center style=\"$style\"><span style=\"color:$fcolor;\">".$percentf."%</span></td>\n";
							echo "<td bgcolor='$fcolor' valign=middle align=center style=\"$style\">".$percentf."%</td>\n";
						}
					$total_percent_room=$sum_percent_room[$i]/$rsBranch["rows"];
					$sum_total_percent_room+=$total_percent_room;
					$total_percent_room=number_format($total_percent_room,2,".",",");
					$cnt_row++;
					echo "<td align=\"center\" style=\"$style\"><b>".$total_percent_room."%</b></td>\n";
					?>
				</tr>
				<?
					}
				}?>
				<tr height="40">
					<td bgcolor="#d3d3d3" align="center"><b>Total</b></td>
					<?for($k=0;$k<$rsBranch["rows"];$k++){
						$total_percent=$sum_percent[$k]/$cnt_row;
						$total_percent=number_format($total_percent,2,".",",");
					?>
					<td bgcolor="#d3d3d3" align="center"><b><?=$total_percent?>%</b></td>
					<?}
					$sum_total_percent_room=$sum_total_percent_room/$cnt_row;
					$sum_total_percent_room=number_format($sum_total_percent_room,2,".",",");
					?>
					<td bgcolor="#d3d3d3" align="center"><b><?=$sum_total_percent_room?>%</b></td>
				<tr>
			</table>
				
<?
}else{
	//Not Branch All
	$sql_room="select * from bl_room where branch_id=$branch_id " .
			"and room_active=1 and room_utilization<>1 order by room_name";
	$rs_room=$obj->getResult($sql_room);
	$colspan=$rs_room["rows"]+2;
	
	// ################ Get time ###############
	//$starttime_id = $obj->getIdToText($branch_id,"bl_branchinfo","start_time_id","branch_id");
	//$closetime_id = $obj->getIdToText($branch_id,"bl_branchinfo","close_time_id","branch_id");
	
	$sql_time="select * from a_company_info";
	$rs_time = $obj->getResult($sql_time);

	$starttime_id = $rs_time[0]["start_time_id"];
	$closetime_id = $rs_time[0]["close_time_id"];
	
	$chksql = "select * from p_timer where time_id between $starttime_id and $closetime_id";
	$timeline = $obj->getResult($chksql);
	$count_time = $timeline["rows"];
	
	$peid = $obj->getParameter("peid",3);
	$tp_id = $peid;
	$chksql = "select * from l_timeperiod where tp_id=$tp_id";
	$timeperiodrs = $obj->getResult($chksql);
	$time_period = $timeperiodrs[0]["tp_name"];
	$time_period_distance = $timeperiodrs[0]["tp_distance"];
	
	// system's time period
	$sql = "select * from p_timer";
	$timeperiodrs = $obj->getResult($sql);
	$timeperiod = array();
	for($i=0;$i<$timeperiodrs["rows"];$i++){
			$timeperiod[$timeperiodrs[$i]["time_id"]] = $timeperiodrs[$i]["time_start"];
	}
	?>
	
	<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
	<span class="pdffirstpage"/>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
		 <td valign="top" style="padding:10 20 20 20;" width="100%" align="center">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
				<?for($i=0;$i<$colspan;$i++){?>
					<td width="<?=100/$colspan?>%"></td>
				<?}?>
				</tr>
				
				<?
				 //Get All Branch
			        $sql = "select branch_id, branch_name from bl_branchinfo where branch_id<>1 ";
			        		//if($city_id){$sql .= "and city_id=".$city_id." ";}else
			        		if($branch_id){$sql .= "and branch_id=".$branch_id." ";}
			        $sql.= "and branch_active=1 and branch_utilization<>1 order by branch_name asc";
			        $rsBranch = $obj->getResult($sql);
		        
				?>
				<tr>
			    	<td class="reporth" width="100%" align="center" colspan="<?=$colspan?>">
			    		<b><p>Spa Management System</p></b>
			    		<b><?=$reportname?></b><br>
			    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br></p>
			    		<p><b style='color:#ff0000'><?="Branch : "?>
			    		<?
			    			for($j=0; $j<$rsBranch["rows"]; $j++){
			    				$nbranchdetail[$j] = $rsBranch[$j]["branch_name"];
			    			}
			    			if($nbranchdetail){
			  	  				$NbranchSrdString = implode(", ", $nbranchdetail); 
			  				}
			  				echo $NbranchSrdString;
			    		?>
			    		</b><br></p>
			    	</td>
				</tr>
				
				<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Time</b></td>
				<?for($i=0;$i<$rs_room["rows"];$i++){?>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b><?=$rs_room[$i]["room_name"]?></b></td>
				<?}?>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
				</tr>
				
				<?
				$cnt_row=0;
				for($i=0; $i<$timeline["rows"]; $i=$i+$time_period_distance){
					$timestamp = strtotime($timeline[$i]["time_start"]);
								$startptimeid = $timeline[$i]["time_id"];
								$startptime = date("H:i",$timestamp);
								if(isset($timeline[$i+$time_period_distance]["time_start"])){
									$timestamp = strtotime($timeline[$i+$time_period_distance]["time_start"]);
									$lastptime = date("H:i",$timestamp);
								}else{
									$timestamp = strtotime($timeline[$i]["time_start"]);
									$etime = strtotime("+$time_period minutes", $timestamp);
									$lastptime = date('H:i', $etime);
								}
					if($timeline[$i]["time_id"]!=$closetime_id){
									$lastptimeid = $startptimeid + $time_period_distance;
									$cstartptime = "";
									//$style = "border-top:solid 0px;";
									$style = "";
									$check = 0;
					for($j=$i;$j<$i+$time_period_distance;$j++){
										if(isset($timeline[$j]["time_id"])&&$timeline[$j]["time_id"]%12==1){
												$check = 1;
												$cstartptime = strtotime($timeperiod[$timeline[$j]["time_id"]]);
												$cstartptime = date("H:i",$cstartptime);
												$style .= "border-bottom:dashed #000000 1px;color:#000000;";
												break;
										}
										if(isset($timeline[$j]["time_id"])&&$timeline[$j]["time_id"]%12==7){
												$check = 2;
												$style .= "border-bottom:solid 1px;color:#000000;";
												break;
										}
					}
				//$class="onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";
				?>
				<tr height="40" <?=$class?>>
					<?
					echo "<td align=\"center\" style=\"background-color:#d3d3d3;$style\">&nbsp;&nbsp;<span style='color:#000000;'>$startptime-$lastptime</span>&nbsp;&nbsp;</td>\n";
						for($k=0;$k<$rs_room["rows"];$k++){
							
							$sql_chk="select a_bookinginfo.b_appt_date,count(a_bookinginfo.b_appt_date) as ncount " .
									",(select time_start from p_timer where time_id=b_appt_time_id) as start " .
									",(select time_id from p_timer where time_start=SEC_TO_TIME((select TIME_TO_SEC(time_start) from p_timer where time_id=b_appt_time_id)+(select TIME_TO_SEC(hour_name) from l_hour where hour_id =b_book_hour))) as end_id " .
									"from a_bookinginfo,d_indivi_info where " .
									"a_bookinginfo.b_appt_date>='".$begin_date."' ".
	 								"and a_bookinginfo.b_appt_date<='".$end_date."' " .
	 								
									//"and a_bookinginfo.b_appt_time_id>=$startptimeid " .
									//"and a_bookinginfo.b_appt_time_id<$lastptimeid ".
									
									"and CASE WHEN a_bookinginfo.b_appt_time_id>=$startptimeid and a_bookinginfo.b_appt_time_id<$lastptimeid THEN " .
									"a_bookinginfo.b_appt_time_id>=$startptimeid " .
									"and a_bookinginfo.b_appt_time_id<$lastptimeid " .
									
									"WHEN a_bookinginfo.b_appt_time_id<$startptimeid and a_bookinginfo.b_appt_time_id<$lastptimeid THEN " .
									"(select time_id from p_timer where time_start=SEC_TO_TIME((select TIME_TO_SEC(time_start) from p_timer where time_id=b_appt_time_id)+(select TIME_TO_SEC(hour_name) from l_hour where hour_id =b_book_hour)))>$startptimeid " .
									"END ".
										
									"and a_bookinginfo.b_branch_id=$branch_id " .
	 								"and a_bookinginfo.b_set_cancel<>1 " .
	 								"and a_bookinginfo.book_id=d_indivi_info.book_id " .
	 								"and d_indivi_info.room_id=".$rs_room[$k]["room_id"]." " .
	 								"group by a_bookinginfo.b_appt_date ";
							
							$getrs=$obj->getResult($sql_chk);
	
							$percentf=($getrs["rows"]/$totaldate)*100;
							
							$sum_percent[$k]+=$percentf;
							$sum_percent_room[$i]+=$percentf;
							
							$percentf=number_format($percentf,2,".",",");
							
							$fcolor="#000000";
							if($percentf<25){
								//$fcolor="#ff0000";
								$fcolor="#59da86";
							}else{ 
								if($percentf<75){
									//$fcolor="#ff5a00";
									$fcolor="#fe7e38";
								}else{
									//$fcolor="#005c00";
									$fcolor="#ff4242";
								}
							}
							
							//echo "<td valign=middle align=center style=\"$style\"><span style=\"color:$fcolor;\">".$percentf."%</span></td>\n";
							echo "<td bgcolor='$fcolor' valign=middle align=center style=\"$style\">".$percentf."%</td>\n";
						}
					$total_percent_room=$sum_percent_room[$i]/$rs_room["rows"];
					$sum_total_percent_room+=$total_percent_room;
					$total_percent_room=number_format($total_percent_room,2,".",",");
					$cnt_row++;
					echo "<td align=\"center\" style=\"$style\"><b>".$total_percent_room."%</b></td>\n";
					?>
				</tr>
				<?
					}
				}?>
				<tr height="40">
					<td bgcolor="#d3d3d3" align="center"><b>Total</b></td>
					<?for($k=0;$k<$rs_room["rows"];$k++){
						$total_percent=$sum_percent[$k]/($cnt_row);
						$total_percent=number_format($total_percent,2,".",",");
					?>
					<td bgcolor="#d3d3d3" align="center"><b><?=$total_percent?>%</b></td>
					<?}
					$sum_total_percent_room=$sum_total_percent_room/($cnt_row);
					$sum_total_percent_room=number_format($sum_total_percent_room,2,".",",");
					?>
					<td bgcolor="#d3d3d3" align="center"><b><?=$sum_total_percent_room?>%</b></td>
				<tr>
			</table>
		</td>
		</tr>

<?}?>
	<tr height="100">
			    	<td width="100%" align="left" colspan="11" ><br>
			    			<br><b>Notation : </b><br>
<div></div><br />
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#59da86;"></div> &nbsp;- Green color, Facilities Utilization less than 25%.<br />
<br /> 
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#fe7e38;"></div> &nbsp;- Orange color, Facilities Utilization more than 25% and less than 75%.<br />
<br />
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#ff4242;"></div> &nbsp;- Red color, Facilities Utilization more than 75%.<br />
<br />
			    	</td>
				</tr>
				
</table>
</span>

 
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>