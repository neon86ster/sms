<?
include("../../../include.php");
require_once("therapist.inc.php");
require_once("date.inc.php");
$obj = new therapist();
// system date format	 					
$chksql = "select long_date,short_date from a_company_info";
$chkrs =$obj->getResult($chksql);
$sdateformat = $obj->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
$ldateformat = $obj->getIdToText($chkrs[0]["long_date"],"l_date","date_format","date_id");
$dateobj = new convertdate();
$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate= $obj->getParameter("end");
$column= $obj->getParameter("column");
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");
$empid= $obj->getParameter("empid");
$collapse = $obj->getParameter("Collapse");
$branchtotal = array();
$today = date("Ymd");
$rsdate = $obj->getdatecol($column,$begindate,$enddate);
$rs = $obj->getthhourinfo(0,$begindate,$enddate,$empid);
for($j=0; $j<$rs["rows"]; $j++){
	$total[$j]=$rs[$j]["total"];
}
$rscity = $obj->getcity($order,$sort);
$rsbranch = $obj->getbranch($order,$sort);
for($j=0; $j<$rsbranch["rows"]; $j++){
	$branch[$j]=$rsbranch[$j]["branch_id"];
}
$rsthera = $obj->gettherapist($order,$sort,$empid);
for($j=0; $j<$rsthera["rows"]; $j++){
	$therapist[$j]=$rsthera[$j]["emp_id"];
}
//print_r($rsdate);
$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Therapist Hour.xls\"");
	header("Pragma: public");
	header("Expires: 0");
	//header("Content-Type: application/vnd.ms-excel");
	//header('Content-Disposition: attachment; filename="Therapist Hour.xls"');
	//echo "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"xmlns:x=\"urn:schemas-microsoft-com:office:excel\"xmlns=\"http://www.w3.org/TR/REC-html40\">";
}
if($export!=false&&$export!="Excel"){
	$chkcolumn=0;
	$alltable=ceil($rsdate["rows"]/10);
	if($column=="Total only"){$alltable=1;}
	$alltotal=0;
	//echo $alltable;
}
?>
<script type="text/javascript" src="../scripts/component.js"></script>
<link href="../../../css/style.css" rel="stylesheet" type="text/css">
<table border="0" <?($export=="Excel")?"x:str":""?> cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td class="reporth" align="center">
    		<b><p>Spa Management System</p>
    		Therapist Hour Report</b><br>
    		<p class="style1">
    		<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
    		</p>
    	</td>
	</tr>
	<tr>
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0" width="100%">
<?if($export!=false&&$export!="Excel"){ // begin check export function 
	for($a=0;$a<$alltable;$a++){
		if($column!="Total only"){
		if($a==0&&$a!=$alltable-1){$datechk["begin"][0]=0;$datechk["end"][0]=9;$datechk["rows"]=10;}
		else if($a==0&&$a==$alltable-1){
			$datechk["begin"][$a]=0;
			$datechk["end"][$a]=$rsdate["rows"]-1;
			$datechk["rows"]=$datechk["end"][$a]-$datechk["begin"][$a]+2;
		}
		else if($a==$alltable-1){
			$datechk["begin"][$a]=$datechk["begin"][$a-1]+10;
			$datechk["end"][$a]=$rsdate["rows"]-1;
			$datechk["rows"]=$datechk["end"][$a]-$datechk["begin"][$a]+2;
		}else{
			$datechk["begin"][$a]=$datechk["begin"][$a-1]+10;
			$datechk["end"][$a]=$datechk["begin"][$a]+9;
			$datechk["rows"]=10;}
		}else{
			$datechk["begin"][0]=0;$datechk["end"][0]=0;$datechk["rows"]=1;
		}
			//echo $a." : ".$datechk["begin"][$a]."-".$datechk["end"][$a];
		?>
				<tr height="32">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($i=$datechk["begin"][$a];$i<=$datechk["end"][$a];$i++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$i]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
<?
if($collapse=="Collapse"){	//check Collapse/Expand loop
	
for($i=0; $i<$rscity["rows"]; $i++) {		// start city loop		
$alltherapist = implode(",",$therapist);
if($obj->getIdToText($rscity[$i]["city_id"],"l_employee,bl_branchinfo","emp_id","bl_branchinfo`.`city_id",
				" l_employee.emp_id IN ( $alltherapist ) and l_employee.emp_active=1 and l_employee.emp_department_id=4 " .
				"and bl_branchinfo.branch_id=l_employee.branch_id and bl_branchinfo.branch_active=1 and bl_branchinfo.city_id=".$rscity[$i]["city_id"])>0){
?>
<tr height="32">
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #d0d0d0 double;" bgcolor="#eeeee"><b>Location: <?=$rscity[$i]["city_name"]?></b></td>
	<td colspan="<?=($column=="Total only")?$rsdate["rows"]:$datechk["rows"]?>" bgcolor="#eeeee" style="border-bottom:3px #d0d0d0 double;">&nbsp;</td><!-- input city -->
</tr>
<?
		$sum_btype = 0;
		$allltotal[$i] = 0;
		for($j=0; $j<$rsbranch["rows"]; $j++) {		// start branch loop
			$alltherapist = implode(",",$therapist);
			if($obj->getIdToText($rsbranch[$j]["branch_id"],"l_employee,bl_branchinfo","emp_id","l_employee`.`branch_id",
				" l_employee.emp_id IN ( $alltherapist ) and l_employee.emp_active=1 and l_employee.emp_department_id=4 " .
				"and bl_branchinfo.branch_id=l_employee.branch_id and bl_branchinfo.branch_active=1 and bl_branchinfo.city_id=".$rscity[$i]["city_id"])>0){			// check if has employee in this branch and this city
				
			?>
			<tr height="28">
				<td style="padding-left: 20px; white-space: nowrap;"><b>Branch: <?=$rsbranch[$j]["branch_name"]?></b></td>
				<td colspan="<?=($column=="Total only")?$rsdate["rows"]:$datechk["rows"]?>" style="padding-left: 20px;">&nbsp;</td>
			</tr>
					<?
					for($k=0; $k<$rsthera["rows"]; $k++) { 	// start branch name loop
						if($rsthera[$k]["branch_id"]==$rsbranch[$j]["branch_id"]&&$rsbranch[$j]["city_id"]==$rscity[$i]["city_id"]&&$rsthera[$k]["emp_active"]==1){
						?>
						<tr height="22">
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsthera[$k]["emp_code"]." ".$rsthera[$k]["emp_nickname"]?></td>
							<? 	for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start branch total loop
							?>		
								<td align="right">
									<?	$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$rsthera[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]); 
										echo number_format(str_replace(".5",".3",$tmp[$k][$d]),2,".",","); 	?>
								</td>
								<? 
								} ?>	
							<? 
							   if($column!="Total only"&&$a==$alltable-1){ 
							   		$total[$k] = 0;
									for($d=0;$d<=$datechk["end"][$a];$d++){ // start branch total loop
										$total[$k]+=$tmp[$k][$d];
										$allltotal[$i] += $tmp[$k][$d];
									}
							?>
							<td align="right">
							<?= number_format(str_replace(".5",".3",$total[$k]),2,".",",")?>
							</td>
							<? } ?>
						</tr>
						<?	
						}
					} 
					?>
			<tr height="28">
				<td style="padding-left: 10px; white-space: nowrap;border-top:1px #000000 solid;"><b>Total in  <?=$rsbranchtype[$j]["branch_category_name"]?> Category: </b></td>
				<? 
				for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){// start date total loop
					$bttotal[$j]=0;$allbttotal[$j]=0;
					for($k=0; $k<$rsthera["rows"]; $k++) { 
						if($rsthera[$k]["branch_id"]==$rsbranch[$j]["branch_id"]&&$rsbranch[$j]["city_id"]==$rscity[$i]["city_id"]&&$rsthera[$k]["emp_active"]==1){
							$bttotal[$j] += $tmp[$k][$d];
							$allbttotal[$j] += $total[$k];
							//$chkbranch++;
						}
					}
				?>		
				<td align="right" style="border-top:1px #000000 solid;">
				<?=number_format(str_replace(".5",".3",$bttotal[$j]),2,".",",")?>
				</td>
				<? }  ?>	
				<? if($column!="Total only"&&$a==$alltable-1){ ?>
				<td align="right" style="border-top:1px #000000 solid;">
				<?=number_format(str_replace(".5",".3",$allbttotal[$j]),2,".",",")?>
				</td>
				<? } ?>
			</tr>
		<?	 	
			} 
		}
		?>
		<?  if($a==$alltable-1){ ?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=($column=="Total only")?$rsdate["rows"]:$datechk["rows"]?>"><b>Total in <?=$rscity[$i]["city_name"]?></b></td>
			<td align="right">
			<b><?=number_format(str_replace(".5",".3",$allltotal[$i]),2,".",",")?></b>
			</td>
		</tr>
		<?}?>
<?	
	} 
}

}	// End check collapse expand loop
else if($order!="Total")		// for colapse information report
{
		for($k=0; $k<$rsthera["rows"]; $k++) { // start branch total loop
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$rsthera[$k]["emp_code"]." ".$rsthera[$k]["emp_nickname"]?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?	$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$rsthera[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
				echo number_format(str_replace(".5",".3",$tmp[$k][$d]),2,".",","); 	?>
			</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ 
					$total[$k] = 0;
					for($d=0;$d<=$datechk["end"][$a];$d++){ // start branch total loop
						$total[$k]+=$tmp[$k][$d];
						$allltotal[$i] += $tmp[$k][$d];
					}
			?>
			<td align="right">
			<?= number_format(str_replace(".5",".3",$total[$k]),2,".",",")?>
			</td>
			<? } ?>
		</tr>
<?
	}
}
else
{
	if($a==0){
		for($k=0; $k<$rsthera["rows"]; $k++) { // start branch total loop for sort array of total in each branch
			$total[$k] = 0;
			for($d=0;$d<$rsdate["rows"];$d++){
				$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$rsthera[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$total[$k]+=$tmp[$k][$d];
				$emptotal[$rsthera[$k]["emp_id"]]=$total[$k];
			}
			if($sort=="A > Z"){arsort($emptotal);}
			else{asort($emptotal);}
			
		}
		$k=0;	// resorting branch id to new array for show in report
		foreach ($emptotal as $key => $val) {
  			  $tmpemptotal[$k] = $key;
  			  //$total[$k] = $val;
  			  $k++;
		}
	}
		
		for($k=0; $k<$rsthera["rows"]; $k++) {
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpemptotal[$k],"l_employee","emp_code","emp_id")." ".$obj->getIdToText($tmpemptotal[$k],"l_employee","emp_nickname","emp_id")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?	$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$tmpemptotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]); 
				echo number_format(str_replace(".5",".3",$tmp[$k][$d]),2,".",","); 	?>
			</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ 
					$total[$k] = 0;
					for($d=0;$d<=$datechk["end"][$a];$d++){ // start branch total loop
						$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$tmpemptotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
						$total[$k]+=$tmp[$k][$d];
						$allltotal[$i] += $tmp[$k][$d];
					}
			?>
			<td align="right">
			<?= number_format(str_replace(".5",".3",$total[$k]),2,".",",")?>
			</td>
			<? } ?>
		</tr>
<?
	}
	
}
?>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#eeeee"><b>TOTAL</b></td>
			
<?
for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start branch total loop
		$alldatetotal[$d]=0;
		for($k=0; $k<$rsthera["rows"]; $k++){
				$alldatetotal[$d] += $tmp[$k][$d];
				$alltotal +=  $tmp[$k][$d];
		}
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#eeeee\"><b>";
		echo number_format(str_replace(".5",".3",$alldatetotal[$d]),2,".",",")."</a></b></td>";
}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
				
			<td align="right" bgcolor="#eeeee"><b>
			<?=number_format(str_replace(".5",".3",$alltotal),2,".",",")?>
			</b></td>
			
			<? } 	
			if($a!=$alltable-1){ ?><tr height="40"><td colspan="<? ($column=="Total only")?$rsdate["rows"]:$datechk["rows"] ?>">&nbsp;</td></tr><? }
	}// end check export file
?>	
	
<? }else{ ?>
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
if($collapse=="Collapse"){	//check Collapse/Expand loop
	
for($i=0; $i<$rscity["rows"]; $i++) {		// start city loop		
$alltherapist = implode(",",$therapist);
if($obj->getIdToText($rscity[$i]["city_id"],"l_employee,bl_branchinfo","emp_id","bl_branchinfo`.`city_id",
				" l_employee.emp_id IN ( $alltherapist ) and l_employee.emp_active=1 and l_employee.emp_department_id=4 " .
				"and bl_branchinfo.branch_id=l_employee.branch_id and bl_branchinfo.branch_active=1 and bl_branchinfo.city_id=".$rscity[$i]["city_id"])>0){
?>
<tr height="32">
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #d0d0d0 double;" bgcolor="#eeeee"><b>Location: <?=$rscity[$i]["city_name"]?></b></td>
	<td colspan="<?=($column=="Total only")?$rsdate["rows"]:$rsdate["rows"]+1?>" bgcolor="#eeeee" style="border-bottom:3px #d0d0d0 double;">&nbsp;</td><!-- input city -->
</tr>
<?
		$sum_btype = 0;
		$allltotal[$i]=0;
		for($j=0; $j<$rsbranch["rows"]; $j++) {		// start branch category loop
			$alltherapist = implode(",",$therapist);
			if($obj->getIdToText($rsbranch[$j]["branch_id"],"l_employee,bl_branchinfo","emp_id","l_employee`.`branch_id",
				" l_employee.emp_id IN ( $alltherapist ) and l_employee.emp_active=1 and l_employee.emp_department_id=4 " .
				"and bl_branchinfo.branch_id=l_employee.branch_id and bl_branchinfo.city_id=".$rscity[$i]["city_id"])>0){			// check if has employee in this branch and this city
				
			?>
			<tr height="28">
				<td style="padding-left: 20px; white-space: nowrap;"><b>Branch: <?=$rsbranch[$j]["branch_name"]?></b></td>
				<td colspan="<?=($column=="Total only")?$rsdate["rows"]:$rsdate["rows"]+1?>" style="padding-left: 20px;">&nbsp;</td>
			</tr>
					<?
					for($k=0; $k<$rsthera["rows"]; $k++) { 	// start branch name loop
						if($rsthera[$k]["branch_id"]==$rsbranch[$j]["branch_id"]&&$rsbranch[$j]["city_id"]==$rscity[$i]["city_id"]&&$rsthera[$k]["emp_active"]==1){
						?>
						<tr height="22">
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsthera[$k]["emp_code"]." ".$rsthera[$k]["emp_nickname"]?></td>
							<? 	$total[$k] = 0;
								for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
							?>		
								<td align="right">
									<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsthera[$k]["emp_id"].",".$rscity[$i]["city_id"].",0"?>)"><? } ?>
									<?	$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$rsthera[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]); 
										echo number_format(str_replace(".5",".3",$tmp[$k][$d]),2,".",","); 	?>
									<?if($export==false){?></a><? } ?>
								</td>
								<? $total[$k]+=$tmp[$k][$d];
								} ?>	
							<? 
							   if($column!="Total only"){ ?>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsthera[$k]["emp_id"].",".$rscity[$i]["city_id"].",0"?>)"><?}?>
							<?= number_format(str_replace(".5",".3",$total[$k]),2,".",",")?>
							<?if($export==false){?></a><?}?>
							</td>
							<? } ?>
						</tr>
						<?	
						}
					} 
					?>
			<tr height="28">
				<td style="padding-left: 10px; white-space: nowrap;border-top:1px #000000 solid;"><b>Total in <?=$rsbranch[$j]["branch_name"]?> : </b></td>
				<? 
				$allbttotal[$j]=0;
				for($d=0;$d<$rsdate["rows"];$d++){// start date total loop
					$bttotal[$j]=0;
					for($k=0; $k<$rsthera["rows"]; $k++) { 
						if($rsthera[$k]["branch_id"]==$rsbranch[$j]["branch_id"]&&$rsbranch[$j]["city_id"]==$rscity[$i]["city_id"]&&$rsthera[$k]["emp_active"]==1){
							$bttotal[$j] += $tmp[$k][$d];
							$allbttotal[$j] +=  $tmp[$k][$d];
						}
					}
					
				?>		
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rscity[$i]["city_id"].",".$rsbranch[$j]["branch_id"]?>)"><?}?>
				<?=number_format(str_replace(".5",".3",$bttotal[$j]),2,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<? }  ?>	
				<? if($column!="Total only"){ ?>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",".$rsbranch[$j]["branch_id"]?>)"><?}?>
				<?=number_format(str_replace(".5",".3",$allbttotal[$j]),2,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<? } ?>
			</tr>
		<?	 	$allltotal[$i] += $allbttotal[$j];
			} 
		}
		?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=($column=="Total only")?$rsdate["rows"]:$rsdate["rows"]+1?>"><b>Total in <?=$rscity[$i]["city_name"]?></b></td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",0"?>)"><?}?>
			<b><?=number_format(str_replace(".5",".3",$allltotal[$i]),2,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>
		</tr>
<?	
	} 
}

}	// End check collapse expand loop
else if($order!="Total")		// for colapse information report
{
		for($k=0; $k<$rsthera["rows"]; $k++) { // start branch total loop
			$total[$k]=0;
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$rsthera[$k]["emp_code"]." ".$rsthera[$k]["emp_nickname"]?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsthera[$k]["emp_id"].",0,0"?>)"><?}?>
			<?	$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$rsthera[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
				echo number_format(str_replace(".5",".3",$tmp[$k][$d]),2,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
<?
				$total[$k]+=$tmp[$k][$d];
			}
?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsthera[$k]["emp_id"].",0,0"?>)"><?}?>
			<?= number_format(str_replace(".5",".3",$total[$k]),2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
		</tr>
<?
	}
}
else
{
		for($k=0; $k<$rsthera["rows"]; $k++) { // start branch total loop for sort array of total in each branch
			$total[$k]=0;
			for($d=0;$d<$rsdate["rows"];$d++){
				$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$rsthera[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$total[$k]+=$tmp[$k][$d];
				$emptotal[$rsthera[$k]["emp_id"]]=$total[$k];
			}
			if($sort=="A > Z"){arsort($emptotal);}
			else{asort($emptotal);}
		}
		$k=0;	// resorting branch id to new array for show in report
		foreach ($emptotal as $key => $val) {
  			  $tmpemptotal[$k][0] = $key;
  			  $total[$k] = $val;
  			  $k++;
		}
		
		for($k=0; $k<$rsthera["rows"]; $k++) {
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpemptotal[$k][0],"l_employee","emp_code","emp_id")." ".$obj->getIdToText($tmpemptotal[$k][0],"l_employee","emp_nickname","emp_id")?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpemptotal[$k][0].",0,0"?>)"><?}?>
			<?	$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$tmpemptotal[$k][0],$rsdate["begin"][$d],$rsdate["end"][$d]);
				echo number_format(str_replace(".5",".3",$tmp[$k][$d]),2,".",",");	?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpemptotal[$k][0].",0,0"?>)"><?}?>
			<?= number_format(str_replace(".5",".3",$total[$k]),2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
		</tr>
<?
	}
	
}
?>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#eeeee"><b>TOTAL</b></td>
			
<?
$alltotal=0;
for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
		$alldatetotal[$d]=0;
		for($k=0; $k<$rsthera["rows"]; $k++){
				$alldatetotal[$d] += $tmp[$k][$d];
				$alltotal +=  $tmp[$k][$d];
		}
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#eeeee\"><b>";
		if($export==false){echo "<a href=\"javascript:;\" style=\"text-decoration:none;\" onClick=\"openDetail(".$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0,0)\">".number_format($alldatetotal[$d],2,".",",")."</a></b></td>";}
		else{echo number_format(str_replace(".5",".3",$alldatetotal[$d]),2,".",",")."</a></b></td>";}
}
?>
			<? if($column!="Total only"){ ?>
				
			<td align="right" bgcolor="#eeeee"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0,0"?>)"><?}?>
			<?=number_format(str_replace(".5",".3",$alltotal),2,".",",")?>
			<?if($export==false){?></a><?}?></b></td>
			
			<? } ?>
			
<? } ?>
		</tr>
 			</table>
			<br>
		</td>
    </tr>
    <tr>
    	<td align="center">
    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>