<?
$root = $_SERVER["DOCUMENT_ROOT"];
include ("$root/include.php");
require_once ("therapist.inc.php");
$obj = new therapist();

$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date = $obj->getParameter("end");
$column = $obj->getParameter("column");
$order = $obj->getParameter("order");
$sort = $obj->getParameter("sortby");
$msgid = $obj->getParameter("msgid",0);
$branchid = $obj->getParameter("branchid",0);
$cityid = $obj->getParameter("cityid",0);
$collapse = $obj->getParameter("Collapse");
$empid= $obj->getParameter("empid",0);
$branchtotal = array ();
$today = date("Ymd");
$rsdate = $obj->getdatecol($column, $begin_date, $end_date);
$rs = $obj->getthmsginfo($branchid, $begin_date, $end_date, $msgid, $empid, "massage", $order, $sort, $cityid);
$querystr = "pageid=$pageid&begin=$begin_date&end=$end_date&date=$date&column=$column&order=$order&Collapse=$collapse&sortby=$sort";
$msg = array ();
$i = 0;
$msg["massage_name"] = array ();
$msg["massage_id"] = array ();
for ($j = 0; $j < $rs["rows"]; $j++) {
	$chkmassage = in_array($rs[$j]["massage_id"], $msg["massage_id"]);
	if (!$chkmassage) {
		$msg["massage_name"][$i] = $rs[$j]["massage_name"];
		$msg["massage_id"][$i] = $rs[$j]["massage_id"];
		$i++;
	}
}
$msg["rows"] = $i;
$begindate = $dateobj->convertdate(substr($begin_date, 0, 4) . "-" . substr($begin_date, 4, 2) . "-" . substr($begin_date, 6, 2), "Y-m-d", $sdateformat);
$enddate = $dateobj->convertdate(substr($end_date, 0, 4) . "-" . substr($end_date, 4, 2) . "-" . substr($end_date, 6, 2), "Y-m-d", $sdateformat);
$export = $obj->getParameter("export", false);
if ($export == "Excel" && $chkPageView) {
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Therapist Massage.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if ($export == "PDF" && $chkPageView) {
	require ('convert2pdf.inc.php');
	$pdf = new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . "&export=print&chkrow=40&gmt=".$_SESSION["__gmt"]);
}
if ($export != false && $export != "Excel") {
	$rowcnt = 0;
	$chkcolumn = 7;
	$alltable = ceil($rsdate["rows"] / $chkcolumn);
	if ($column == "Total only") {
		$alltable = 1;
	}
	$alltotal = 0;
	$chkrow = $obj->getParameter("chkrow", 40);
	//echo $alltable;
}
$alltotal = 0;
for ($i = 0; $i < $msg["rows"]; $i++) { // start massage range loop for sort array of total in each range
	$total[$i] = 0;
	for ($d = 0; $d < $rsdate["rows"]; $d++) {
		$tmp[$i][$d] = $obj->sumeachfield($rs, "total", 0, $msg["massage_id"][$i], 0, $rsdate["begin"][$d], $rsdate["end"][$d]);
		$total[$i] += $tmp[$i][$d];
		$alltotal += $tmp[$i][$d];
	}
}
for ($d = 0; $d < $rsdate["rows"]; $d++) { // start branch total loop
	$alldatetotal[$d] = 0;
	for ($i = 0; $i < $msg["rows"]; $i++) {
		$alldatetotal[$d] += $tmp[$i][$d];
	}
}
if ($begindate == $enddate) {
	$column = "Total only";
	$rsdate["header"][0] = "TOTAL";
}
// system's mins period
$minsperiod = array("00"=>"00","08"=>"05","17"=>"10","25"=>"15","33"=>"20","42"=>"25","50"=>"30","58"=>"35","67"=>"40","75"=>"45","83"=>"50","92"=>"55");

$rowcnt = 0;
$branchname = $obj->getIdToText($branchid,"bl_branchinfo","branch_name","branch_id");
$cityname = $obj->getIdToText($cityid,"al_city","city_name","city_id");
$empname = $obj->getIdToText($empid,"l_employee","emp_nickname","emp_id");
if(!$empname&&!$branchid&&!$cityid){$empname = "All";}
if($branchname==""){$reportname = $cityname; }
else if($empname==""){$reportname = $branchname; }
else{$reportname = $branchname; }
$reportname .= "$empname's Massage Report";
if(!$cityid&&!$branchid&&$empname == "All"){$reportname = "All Therapist's Massage Report";}
?>

<?if($export){?><script type="text/javascript" src="../scripts/component.js"></script><?}?>
<?if($export!="Excel"&&$export!="PDF"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<?


if ($export != false && $export != "Excel") { // begin check export function 
	for ($a = 0; $a < $alltable; $a++) {
		if ($column != "Total only") {
			if ($a == 0 && $a != $alltable -1) {
				$datechk["begin"][0] = 0;
				$datechk["end"][0] = $chkcolumn -1;
				$datechk["rows"] = $chkcolumn;
			} else
				if ($a == 0 && $a == $alltable -1) {
					$datechk["begin"][$a] = 0;
					$datechk["end"][$a] = $rsdate["rows"] - 1;
					$datechk["rows"] = $datechk["end"][$a] - $datechk["begin"][$a] + 2;
				} else
					if ($a == $alltable -1) {
						$datechk["begin"][$a] = $datechk["begin"][$a -1] + $chkcolumn;
						$datechk["end"][$a] = $rsdate["rows"] - 1;
						$datechk["rows"] = $datechk["end"][$a] - $datechk["begin"][$a] + 2;
					} else {
						$datechk["begin"][$a] = $datechk["begin"][$a -1] + $chkcolumn;
						$datechk["end"][$a] = $datechk["begin"][$a] + $chkcolumn -1;
						$datechk["rows"] = $chkcolumn;
					}
		} else {
			$datechk["begin"][0] = 0;
			$datechk["end"][0] = 0;
			$datechk["rows"] = 1;
		}
?>
<? if($a){?><hr style="page-break-before:always;border:0;color:#ffffff;" /><?$rowcnt=0;}?>	
<?


		$allcolumncnt = $datechk["end"][$a] - $datechk["begin"][$a] + 1;
		if ($column != "Total only" && $a == $alltable -1) {
			$allcolumncnt += 1;
		}
		$columnwidth = 70 / $allcolumncnt;
		$firstcolumnwidth = 100 - ($columnwidth * ($allcolumncnt));
?>	

<?


		$rowcnt++;
		// define header for sparate in export page.
		$header = "\t<tr height=\"20\">\n";
		$header .= "\t\t<td width=\"100%\" align=\"center\" colspan=\"" . ($allcolumncnt +1) . "\" >\n";
		$header .= "\t\t\t<br><b>Printed: </b>" . date($ldateformat . " H:i:s") . "\n";
		$header .= "\t\t</td>\n";
		$header .= "\t</tr>\n";
		$header .= "\t</table></td>\n";
		$header .= "</tr>\n";
		$header .= "</table>\n";
		$header .= "<hr style=\"page-break-before:always;border:0;color:#ffffff;\" />\n";
		$header .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n";
		$header .= "\t<tr>\n";
		$header .= "\t\t<td class=\"content\" width=\"100%\" align=\"center\">\n";
		$header .= "\t\t\t<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$header .= "\t\t\t\t<tr>		<!-- set column width for export to pdf -->\n";
		$header .= "\t\t\t\t\t<td width=\"$firstcolumnwidth%\"></td>\n";
		for ($d = $datechk["begin"][$a]; $d <= $datechk["end"][$a]; $d++) {
			$header .= "\t\t\t\t\t<td width=\"$columnwidth%\"></td>\n";
		}
		if ($column != "Total only" && $a == $alltable -1) {
			$header .= "\t\t\t\t\t<td width=\"$columnwidth%\"></td>\n";
		}
		$header .= "\t\t\t\t</tr>\n";
		$header .= "\t\t\t\t<tr>\n";
		$header .= "\t\t\t\t\t<td class=\"reporth\" align=\"center\" style=\"white-space: nowrap;\" colspan=\"" . ($allcolumncnt +1) . "\">\n";
		$header .= "\t\t\t\t\t<b><p>Spa Management System</p>\n";
		$header .= "\t\t\t\t\t$reportname</b><br>\n";
		$header .= "\t\t\t\t\t<p><b style='color:#ff0000'>\n";
		$header .= "\t\t\t\t\t".$dateobj->convertdate($begindate,$sdateformat,$ldateformat)."\n";
		$header .= "\t\t\t\t\t" . (($enddate == $begindate) ? "" : " - " . $dateobj->convertdate($enddate, $sdateformat, $ldateformat)) . "\n";
		$header .= "\t\t\t\t\t<br><br></b></p>\n";
		$header .= "\t\t\t\t\t</td>\n";
		$header .= "\t\t\t\t</tr>\n";
		$header .= "\t\t\t\t<tr height=\"35\">\n";
		$header .= "\t\t\t\t\t<td width=\"90\" style=\"text-align:left; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>&nbsp;</b></td>\n";
		for ($d = $datechk["begin"][$a]; $d <= $datechk["end"][$a]; $d++) {
			$header .= "\t\t\t\t\t<td width=\"40\" style=\"text-align:right;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b style=\"text-decoration: underline;\">" . $rsdate["header"][$d] . "</b></td>\n";
		}
		if ($column != "Total only" && $a == $alltable -1) {
			$header .= "\t\t\t\t\t<td width=\"40\" style=\"text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b style=\"text-decoration: underline;\">TOTAL</b></td>\n";
		}
		$header .= "\t\t\t\t</tr>\n";
		// end define header
?>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0">
				<tr>		<!-- set column width for export to pdf -->
					<td width="<?=$firstcolumnwidth?>%"></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%"></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="<?=$columnwidth?>%"></td>
					<? }?>
				</tr>
				<tr>
			    	<td class="reporth" align="center" style="white-space: nowrap;" colspan="<?=$allcolumncnt+1?>">
			    		<b><p>Spa Management System</p>
			    		<?=$reportname?></b><br>
			    		<p><b style='color:#ff0000'>
			    		<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
			    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
			    		<br><br></b></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">TOTAL</b></td>
					<? }?>
				</tr>
<?


		if ($order != "Total") {
			for ($i = 0; $i < $msg["rows"]; $i++) { // start massage loop
?>
					<tr height="22"><?$rowcnt++;?>
						<td style="padding-left:35px; white-space: nowrap;"><?=$msg["massage_name"][$i]?></td>
					<?


				for ($d = $datechk["begin"][$a]; $d <= $datechk["end"][$a]; $d++) {
?>


						<td align="right">
						<? list($hr,$min) = explode(".",number_format($tmp[$i][$d],2,".",",")); ?>
						<?=$hr.".".$minsperiod[$min]?>
						</td>
					<?


				}
				if ($column != "Total only" && $a == $alltable -1) {
?>
									<td align="right">
										<? list($hr,$min) = explode(".",number_format($total[$i],2,".",",")); ?>
										<?=$hr.".".$minsperiod[$min]?>
									</td>
					<? } ?>
					</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?


				}
			} else {
			if ($a == 0) {
				for ($i = 0; $i < $msg["rows"]; $i++) { // start branch total loop for sort array of total in each branch
					$total[$i] = 0;
					for ($d = 0; $d < $rsdate["rows"]; $d++) {
						$tmp[$i][$d] = $obj->sumeachfield($rs, "total", 0, $msg["massage_id"][$i], 0, $rsdate["begin"][$d], $rsdate["end"][$d]);
						$total[$i] += $tmp[$i][$d];
						$msgtotal[$msg["massage_id"][$i]] = $total[$i];
					}
					if ($sort == "A > Z") {
						arsort($msgtotal);
					} else {
						asort($msgtotal);
					}
				}
				$k = 0; // resorting branch id to new array for show in report
				foreach ($msgtotal as $key => $val) {
					$tmpmsgtotal[$k] = $key;
					$total[$k] = $val;
					$k++;
				}
			}

			for ($k = 0; $k < $msg["rows"]; $k++) {
?>
		<tr height="22"><?$rowcnt++;?>
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpmsgtotal[$k],"db_trm","trm_name","trm_id")?></td>
<?


				for ($d = $datechk["begin"][$a]; $d <= $datechk["end"][$a]; $d++) {
?>		
			<td align="right">
			<?


					$tmp[$k][$d] = $obj->sumeachfield($rs, "total", 0, $tmpmsgtotal[$k], 0, $rsdate["begin"][$d], $rsdate["end"][$d]);
					list ($hr, $min) = explode(".", number_format($tmp[$k][$d], 2, ".", ","));
?>
					<?=$hr.".".$minsperiod[$min]?>
			</td>
<?


				}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right">
					<? list($hr,$min) = explode(".",number_format($total[$k],2,".",",")); ?>
					<?=$hr.".".$minsperiod[$min]?>
			</td>
			<? } ?>
		</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?


			}
		}
?>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#eeeee"><b>TOTAL</b></td>
			
<?


		for ($d = $datechk["begin"][$a]; $d <= $datechk["end"][$a]; $d++) { // start branch total loop
			list($hr,$min) = explode(".",number_format($alldatetotal[$d],2,".",","));
			echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#eeeee\"><b>";
			echo $hr.".".$minsperiod[$min];
			echo "</b></td>\n";
		}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
				
			<td align="right" bgcolor="#eeeee"><b>
					<? list($hr,$min) = explode(".",number_format($alltotal,2,".",",")); ?>
					<?=$hr.".".$minsperiod[$min]?>
			</b></td>
			
			<? } ?>
			</tr>
		    <tr>
		    	<td width="100%" align="center" colspan="<?=$allcolumncnt+1?>" >
    			<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
			</tr>
			<tr height="50">
				<td colspan="<?=$allcolumncnt+1?>">&nbsp;</td></tr>
			</tr>
 		</table><br>
		</td>
    </tr>
</table>
<?$rowcnt=0;?>
<?


	}
?>

	
	
	
<? }else{ ?>
	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	
	<tr>
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0">
			<tr>
		    	<td class="reporth" align="center" colspan="<?=($column=="Total only")?$rsdate["rows"]+1:$rsdate["rows"]+2 ?>">
		    		<b><p>Spa Management System</p>
		    		<?=$reportname?></b><br>
		    		<p class="style1">
		    		<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
		    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><br><br>
		    		</p>
		    	</td>
			</tr>
				<tr height="32">
					<td style="text-align:left; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($i=0;$i<$rsdate["rows"];$i++){ ?>
						<td style="text-align:right;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$i]?></b></td>
					<? }  ?>
					<? if($column!="Total only"){?>
					<td style="text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
<?


	if ($order != "Total") {
		for ($i = 0; $i < count($msg["massage_id"]); $i++) { // start age range loop
?>
					<tr height="22">
						<td style="padding-left:35px; white-space: nowrap;"><?=$msg["massage_name"][$i]?></td>
					<?


			for ($d = 0; $d < $rsdate["rows"]; $d++) {
?>


						<td align="right">
								<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="
								openmsgDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",'$empid','$cityid','$branchid','".$msg["massage_id"][$i]."'"?>);"><? } ?>
								<? list($hr,$min) = explode(".",number_format($tmp[$i][$d],2,".",",")); ?>
								<?=$hr.".".$minsperiod[$min]?>
								<?if($export==false){?></a><? } ?>
						</td>
					<?


			}
			if ($column != "Total only") {
?>
						<td align="right">
								<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="
									openmsgDetail(<?=$begin_date.",".$end_date.",'$empid','$cityid','$branchid','".$msg["massage_id"][$i]."'"?>);"><?}?>
								<? list($hr,$min) = explode(".",number_format($total[$i],2,".",",")); ?>
								<?=$hr.".".$minsperiod[$min]?>
								<?if($export==false){?></a><?}?>
						</td>
					<?


			}
		}
	} else {
		for ($i = 0; $i < $msg["rows"]; $i++) { // start branch total loop for sort array of total in each branch
			$total[$i] = 0;

			for ($d = 0; $d < $rsdate["rows"]; $d++) {
				$tmp[$i][$d] = $obj->sumeachfield($rs, "total", 0, $msg["massage_id"][$i], 0, $rsdate["begin"][$d], $rsdate["end"][$d]);
				$total[$i] += $tmp[$i][$d];
				$msgtotal[$msg["massage_id"][$i]] = $total[$i];
			}
			if ($sort == "A > Z") {
				arsort($msgtotal);
			} else {
				asort($msgtotal);
			}
		}
		$k = 0; // resorting branch id to new array for show in report
		foreach ($msgtotal as $key => $val) {
			$tmpmsgtotal[$k] = $key;
			$total[$k] = $val;
			$k++;
		}
		for ($k = 0; $k < $msg["rows"]; $k++) {
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpmsgtotal[$k],"db_trm","trm_name","trm_id")?></td>
<?


			for ($d = 0; $d < $rsdate["rows"]; $d++) {
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="
			openmsgDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",'$empid','$cityid','$branchid','".$tmpmsgtotal[$k]."'"?>);"><? } ?>
			<?


				$tmp[$k][$d] = $obj->sumeachfield($rs, "total", 0, $tmpmsgtotal[$k], 0, $rsdate["begin"][$d], $rsdate["end"][$d]);
				list($hr,$min) = explode(".",number_format($tmp[$k][$d],2,".",",")); 
				echo $hr.".".$minsperiod[$min];
?>
			<?if($export==false){?></a><?}?>
			</td>
<?


			}
?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="
			openmsgDetail(<?=$begin_date.",".$end_date.",'$empid','$cityid','$branchid','".$tmpmsgtotal[$k]."'"?>);"><? } ?>
			<? list($hr,$min) = explode(".",number_format($total[$k],2,".",",")); ?>
			<?=$hr.".".$minsperiod[$min]?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
		</tr>
<?


		}

	}
?>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>TOTAL</b></td>
			
<?


	for ($d = 0; $d < $rsdate["rows"]; $d++) { // start branch total loop
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
		list($hr,$min) = explode(".",number_format($alldatetotal[$d],2,".",","));
		if ($export == false) {
			if(!isset($msg["massage_id"][$i])){$msg["massage_id"][$i] = "";}
			echo "<a href=\"javascript:;\" style=\"text-decoration:none;\" onClick=\"" .
					"openmsgDetail(".$rsdate["begin"][$d].",".$rsdate["end"][$d].",'$empid','$cityid','$branchid','".$msg["massage_id"][$i]."');\">";
			echo $hr.".".$minsperiod[$min];
			echo "</a></b></td>\n";
		} else {
			echo $hr.".".$minsperiod[$min] . "</b></td>\n";
		}
	}
?>
			<? if($column!="Total only"){ ?>
				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="
			openmsgDetail(<?=$begin_date.",".$end_date.",'$empid','$cityid','$branchid','0'"?>);"><? } ?>
			<? list($hr,$min) = explode(".",number_format($alltotal,2,".",",")); ?>
			<?=$hr.".".$minsperiod[$min]?>
			<?if($export==false){?></a><?}?>
			</b></td>
			
			<? } ?>
			</tr>
		    <tr>
		    	<td align="center" colspan="<?=($column=="Total only")?$rsdate["rows"]+1:$rsdate["rows"]+2?>"><br>
		    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
		    	</td>
			</tr>
 		</table><br>
		</td>
    </tr>
</table>
<? } ?>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>