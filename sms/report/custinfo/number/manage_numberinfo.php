<?
session_start();
include("../../../include.php");
require_once("customer.inc.php");
require_once("date.inc.php");
$obj = new customer();
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
$collapse = $obj->getParameter("Collapse");
$branchtotal = array();
$today = date("Ymd");
$rsdate = $obj->getdatecol($column,$begindate,$enddate);
$rs = $obj->getcustnum(0,$begindate,$enddate);
for($j=0; $j<$rs["rows"]; $j++){
	$qty[$j]=$rs[$j]["qty"];
}
$rscity = $obj->getcity($order,$sort);
for($j=0; $j<$rscity["rows"]; $j++){
	$city[$j]=$rscity[$j]["city_id"];
}
$rsbranchtype = $obj->getbranchtype($order,$sort);
for($j=0; $j<$rsbranchtype["rows"]; $j++){
	$branchtype[$j]=$rsbranchtype[$j]["branch_category_id"];
}
$rsbranch = $obj->getbranch($order,$sort);
for($j=0; $j<$rsbranch["rows"]; $j++){
	$branch[$j]=$rsbranch[$j]["branch_id"];
}
$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Number Of Customer.xls\"");
	header("Pragma: public");
	header("Expires: 0");
	//header("Content-Type: application/vnd.ms-excel");
	//header('Content-Disposition: attachment; filename="Number Of Customer.xls"');
	//echo "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"xmlns:x=\"urn:schemas-microsoft-com:office:excel\"xmlns=\"http://www.w3.org/TR/REC-html40\">";
}
if($export!=false&&$export!="Excel"){
	$chkcolumn=0;
	$alltable=ceil($rsdate["rows"]/9);
	if($column=="Total only"){$alltable=1;}
	$alltotal=0;
	//echo round($rsdate["rows"]/9);
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&chkrow=40");
}
?>
<script type="text/javascript" src="../scripts/component.js"></script>
<link href="../../../css/style.css" rel="stylesheet" type="text/css">

<?if($export!=false&&$export!="Excel"){ // begin check export function 
	for($a=0;$a<$alltable;$a++){
		if($column!="Total only"){
		if($a==0&&$a!=$alltable-1){$datechk["begin"][0]=0;$datechk["end"][0]=8;$datechk["rows"]=9;}
		else if($a==0&&$a==$alltable-1){
			$datechk["begin"][$a]=0;
			$datechk["end"][$a]=$rsdate["rows"]-1;
			$datechk["rows"]=$datechk["end"][$a]-$datechk["begin"][$a]+2;
		}
		else if($a==$alltable-1){
			$datechk["begin"][$a]=$datechk["begin"][$a-1]+9;
			$datechk["end"][$a]=$rsdate["rows"]-1;
			$datechk["rows"]=$datechk["end"][$a]-$datechk["begin"][$a]+2;
		}else{
			$datechk["begin"][$a]=$datechk["begin"][$a-1]+9;
			$datechk["end"][$a]=$datechk["begin"][$a]+8;
			$datechk["rows"]=10;}
		}else{
			$datechk["begin"][0]=0;$datechk["end"][0]=0;$datechk["rows"]=1;
		}
			//echo $alltable." : ".$datechk["begin"][$a]."-".$datechk["end"][$a];
		?>
<p style="page-break-after:always;">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0">
				<tr>
			    	<td class="reporth" align="center" style="white-space: nowrap;" colspan="<?=($column=="Total only"&&$a==$alltable-1)?$datechk["end"][$a]-$datechk["begin"][$a]+1:$datechk["end"][$a]-$datechk["begin"][$a]+3 ?>">
			    		<b><p>Spa Management System</p>
			    		Number Of Customer</b><br>
			    		<p class="style1">
			    		<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
			    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
			    		</p>
			    	</td>
				</tr>
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
$allbranchtype = implode(",",$branchtype);
if($obj->getIdToText($rscity[$i]["city_id"],"bl_branchinfo","branch_id","city_id","branch_category_id IN ( $allbranchtype ) and branch_active=1")>0){
?>
<tr height="32">
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #d0d0d0 double;" bgcolor="#eeeee"><b>Location: <?=$rscity[$i]["city_name"]?></b></td>
	<td colspan="<?=($column=="Total only")?$rsdate["rows"]:$datechk["rows"]?>" bgcolor="#eeeee" style="border-bottom:3px #d0d0d0 double;">&nbsp;</td><!-- input city -->
</tr>
<?
		$sum_btype = 0;
		for($j=0; $j<$rsbranchtype["rows"]; $j++) {		// start branch category loop
			$allbranch = implode(",",$branch);
			if($obj->getIdToText($rsbranchtype[$j]["branch_category_id"],"bl_branchinfo","branch_id","branch_category_id"," branch_id IN ( $allbranch ) and branch_active=1 and city_id=".$rscity[$i]["city_id"])>0){
			?>
			<tr height="28">
				<td style="padding-left: 20px; white-space: nowrap;"><b>Category: <?=$rsbranchtype[$j]["branch_category_name"]?></b></td>
				<td colspan="<?=($column=="Total only")?$rsdate["rows"]:$datechk["rows"]?>" style="padding-left: 20px;">&nbsp;</td>
			</tr>
					<?
					for($k=0; $k<$rsbranch["rows"]; $k++) { 	// start branch name loop
						if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
						?>
						<tr height="22">
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
							<? 	//$total[$k] = 0;
								for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start branch total loop
							?>		
								<td align="right">
									<?	$tmp[$k][$d]=$obj->sumeachfield($rs,"qty",$rsbranch[$k]["branch_id"],$rsdate["begin"][$d],$rsdate["end"][$d]); 
										echo $tmp[$k][$d]; 	?>
								</td>
								<?  $total[$k]+=$tmp[$k][$d];
									$allltotal[$i] += $tmp[$k][$d];
								} ?>	
							<? 
							   if($column!="Total only"&&$a==$alltable-1){ ?>
							<td align="right">
							<?= $total[$k]?>
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
					for($k=0; $k<$rsbranch["rows"]; $k++) { 
						if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
							$bttotal[$j] += $tmp[$k][$d];
							$allbttotal[$j] += $total[$k];
							//$chkbranch++;
						}
					}
				?>		
				<td align="right" style="border-top:1px #000000 solid;">
				<?=$bttotal[$j]?>
				</td>
				<? }  ?>	
				<? if($column!="Total only"&&$a==$alltable-1){ ?>
				<td align="right" style="border-top:1px #000000 solid;">
				<?=$allbttotal[$j]?>
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
			<b><?=$allltotal[$i]?></b>
			</td>
		</tr>
		<?}?>
<?	
	} 
}

}	// End check collapse expand loop
else if($order!="Total")		// for colapse information report
{
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?	$tmp[$k][$d]=$obj->sumeachfield($rs,"qty",$rsbranch[$k]["branch_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
			echo $tmp[$k][$d]; 	?>
			</td>
<?
				$total[$k]+=$tmp[$k][$d];
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right">
			<?= $total[$k]?>
			</td>
			<? } ?>
		</tr>
<?
	}
}
else
{
	if($a==0){
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop for sort array of total in each branch
			$total[$k] = 0;
			for($d=0;$d<$rsdate["rows"];$d++){
				$tmp[$k][$d]=$obj->sumeachfield($rs,"total",$rsbranch[$k]["branch_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$total[$k]+=$tmp[$k][$d];
				$branchtotal[$rsbranch[$k]["branch_id"]]=$total[$k];
			}
			if($sort=="A > Z"){arsort($branchtotal);}
			else{asort($branchtotal);}
			
		}
		$k=0;	// resorting branch id to new array for show in report
		foreach ($branchtotal as $key => $val) {
  			  $tmpbranchtotal[$k] = $key;
  			  //$total[$k] = $val;
  			  $k++;
		}
	}
		for($k=0; $k<$rsbranch["rows"]; $k++) {
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpbranchtotal[$k],"bl_branchinfo","branch_name","branch_id")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?	$tmp[$k][$d]=$obj->sumeachfield($rs,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]); 
			echo $tmp[$k][$d]; 	?>
			</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ 
					$total[$k] = 0;
					for($d=0;$d<=$datechk["end"][$a];$d++){ // start branch total loop
						$tmp[$k][$d]=$obj->sumeachfield($rs,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]); 
						$total[$k]+=$tmp[$k][$d];
						$allltotal[$i] += $tmp[$k][$d];
					}
			?>
			<td align="right">
			<?= $total[$k]?>
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
		for($k=0; $k<$rsbranch["rows"]; $k++){
				$alldatetotal[$d] += $tmp[$k][$d];
				$alltotal +=  $tmp[$k][$d];
		}
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#eeeee\"><b>";
		echo $alldatetotal[$d]."</a></b></td>";
}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
				
			<td align="right" bgcolor="#eeeee"><b>
			<?=$alltotal?>
			</b></td>
			
			<? } ?>
	<tr height="40">
		<td colspan="<?= ($column=="Total only"&&$a==$alltable-1)?$datechk["end"][$a]-$datechk["begin"][$a]:$datechk["end"][$a]-$datechk["begin"][$a]+3 ?>">&nbsp;</td></tr>
	</tr>
    <tr>
    	<td align="center" colspan="<?=($column=="Total only"&&$a==$alltable-1)?$datechk["end"][$a]-$datechk["begin"][$a]:$datechk["end"][$a]-$datechk["begin"][$a]+3 ?>">
    		<b>Printed: </b><?=date($ldateformat." H:i:s")?>
    	</td>
	</tr>
	<tr height="50">
		<td colspan="<? ($column=="Total only")?$rsdate["rows"]:$datechk["rows"] ?>">&nbsp;</td></tr>
	</tr>
 </table>
		</td>
	</tr>
</table>
 </p>
	<?
	}// end check export file
?>	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
<? }else{ ?>
	
<table border="0" <?($export=="Excel")?"x:str":""?> cellspacing="0" cellpadding="0" width="100%">
	
	<tr>
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0">
			<tr>
		    	<td class="reporth" align="center" colspan="<?=($column=="Total only")?$rsdate["rows"]+1:$rsdate["rows"]+2 ?>">
		    		<b><p>Spa Management System</p>
		    		Number Of Customer</b><br>
		    		<p class="style1">
		    		<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
		    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
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
if($collapse=="Collapse"){	//check Collapse/Expand loop
	
for($i=0; $i<$rscity["rows"]; $i++) {		// start city loop		
$allbranchtype = implode(",",$branchtype);
if($obj->getIdToText($rscity[$i]["city_id"],"bl_branchinfo","branch_id","city_id","branch_category_id IN ( $allbranchtype ) and branch_active=1")>0){
?>
<tr height="32">
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #d0d0d0 double;" bgcolor="#eeeee"><b>Location: <?=$rscity[$i]["city_name"]?></b></td>
	<td colspan="<?=($column=="Total only")?$rsdate["rows"]:$rsdate["rows"]+1?>" bgcolor="#eeeee" style="border-bottom:3px #d0d0d0 double;">&nbsp;</td><!-- input city -->
</tr>
<?
		$sum_btype = 0;
		$allltotal[$i]=0;
		for($j=0; $j<$rsbranchtype["rows"]; $j++) {		// start branch category loop
			$allbranch = implode(",",$branch);
			if($obj->getIdToText($rsbranchtype[$j]["branch_category_id"],"bl_branchinfo","branch_id","branch_category_id"," branch_id IN ( $allbranch ) and branch_active=1 and city_id=".$rscity[$i]["city_id"])>0){
			?>
			<tr height="28">
				<td style="padding-left: 20px; white-space: nowrap;"><b>Category: <?=$rsbranchtype[$j]["branch_category_name"]?></b></td>
				<td colspan="<?=($column=="Total only")?$rsdate["rows"]:$rsdate["rows"]+1?>" style="padding-left: 20px;">&nbsp;</td>
			</tr>
					<?
					for($k=0; $k<$rsbranch["rows"]; $k++) { 	// start branch name loop
						if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
						?>
						<tr height="22">
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
							<? 	$total[$k] = 0;
								for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
							?>		
								<td align="right">
									<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><? } ?>
									<?	$tmp[$k][$d]=$obj->sumeachfield($rs,"qty",$rsbranch[$k]["branch_id"],$rsdate["begin"][$d],$rsdate["end"][$d]); 
										echo $tmp[$k][$d]; 	?>
									<?if($export==false){?></a><? } ?>
								</td>
								<? $total[$k]+=$tmp[$k][$d];
								} ?>	
							<? 
							   if($column!="Total only"){ ?>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
							<?= $total[$k]?>
							<?if($export==false){?></a><?}?>
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
				$allbttotal[$j]=0;
				for($d=0;$d<$rsdate["rows"];$d++){// start date total loop
					$bttotal[$j]=0;
					for($k=0; $k<$rsbranch["rows"]; $k++) { 
						if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
							$bttotal[$j] += $tmp[$k][$d];
							$allbttotal[$j] +=  $tmp[$k][$d];
						}
					}
					
				?>		
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
				<?=$bttotal[$j]?>
				<?if($export==false){?></a><?}?>
				</td>
				<? }  ?>	
				<? if($column!="Total only"){ ?>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
				<?=$allbttotal[$j]?>
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
			<b><?=$allltotal[$i]?></b>
			<?if($export==false){?></a><?}?>
			</td>
		</tr>
<?	
	} 
}

}	// End check collapse expand loop
else if($order!="Total")		// for colapse information report
{
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop
			$total[$k]=0;
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",0,0"?>)"><?}?>
			<?	$tmp[$k][$d]=$obj->sumeachfield($rs,"qty",$rsbranch[$k]["branch_id"],$rsdate["begin"][$d],$rsdate["end"][$d]); 
			echo $tmp[$k][$d]; 	?>
			<?if($export==false){?></a><?}?>
			</td>
<?
				$total[$k]+=$tmp[$k][$d];
			}
?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",0,0"?>)"><?}?>
			<?= $total[$k]?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
		</tr>
<?
	}
}
else
{
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop for sort array of total in each branch
			$total[$k]=0;
			for($d=0;$d<$rsdate["rows"];$d++){
				$tmp[$k][$d]=$obj->sumeachfield($rs,"qty",$rsbranch[$k]["branch_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$total[$k]+=$tmp[$k][$d];
				$branchtotal[$rsbranch[$k]["branch_id"]]=$total[$k];
			}
			if($sort=="A > Z"){arsort($branchtotal);}
			else{asort($branchtotal);}
		}
		$k=0;	// resorting branch id to new array for show in report
		foreach ($branchtotal as $key => $val) {
  			  $tmpbranchtotal[$k][0] = $key;
  			  $total[$k] = $val;
  			  $k++;
		}
		
		for($k=0; $k<$rsbranch["rows"]; $k++) {
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpbranchtotal[$k][0],"bl_branchinfo","branch_name","branch_id")?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k][0].",0,0"?>)"><?}?>
			<?	$tmp[$k][$d]=$obj->sumeachfield($rs,"qty",$tmpbranchtotal[$k][0],$rsdate["begin"][$d],$rsdate["end"][$d]); 
			echo $tmp[$k][$d]; 	?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k][0].",0,0"?>)"><?}?>
			<?= $total[$k]?>
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
		for($k=0; $k<$rsbranch["rows"]; $k++){
				$alldatetotal[$d] += $tmp[$k][$d];
				$alltotal +=  $tmp[$k][$d];
		}
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#eeeee\"><b>";
		if($export==false){echo "<a href=\"javascript:;\" style=\"text-decoration:none;\" onClick=\"openDetail(".$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0,0)\">".$alldatetotal[$d]."</a></b></td>\n";}
		else{echo $alldatetotal[$d]."</a></b></td>\n";}
}
?>
			<? if($column!="Total only"){ ?>
				
			<td align="right" bgcolor="#eeeee"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0,0"?>)"><?}?>
			<?=$alltotal?><?if($export==false){?></a><?}?></b></td>
			
			<? } ?>
			</tr>
			<tr height="40">
				<td align="center" colspan="<?= ($column=="Total only")?$rsdate["rows"]+1:$rsdate["rows"]+2 ?>">&nbsp;</td></tr>
			</tr>
		    <tr>
		    	<td align="center" colspan="<?=($column=="Total only")?$rsdate["rows"]+1:$rsdate["rows"]+2?>">
		    		<b>Printed: </b><?=date($ldateformat." H:i:s")?>
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