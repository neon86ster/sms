<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("customer.inc.php");
$obj = new customer();
$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate= $obj->getParameter("end");
$column= $obj->getParameter("column");
$branch_id = $obj->getParameter("branchid");
$city_id = $obj->getParameter("cityid",false);
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");
$percent = $obj->getParameter("percent");
$branchtotal = array();
$today = date("Ymd");
$rsdate = $obj->getdatecol($column,$begindate,$enddate);
$rs = $obj->getcusperage($city_id,$begindate,$enddate,0,$branch_id);
for($j=0; $j<$rs["rows"]; $j++){
	$qty[$j]=$rs[$j]["total"];
}
$rsagerange = $obj->makeagerange($sort);

$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Age Of Customer.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export!=false&&$export!="Excel"){
	$chkcolumn=7;
	$alltable=ceil($rsdate["rows"]/$chkcolumn);
	if($column=="Total only"){$alltable=1;}
	$alltotal=0;
	//echo round($rsdate["rows"]/7);
}
$palltotal = 0;
$alltotal=0;
for($i=0; $i<$rsagerange["rows"]; $i++) { // start age range loop for sort array of total in each range
		$total[$i]=0;
		for($d=0;$d<=$rsdate["rows"];$d++){
				if(!isset($rsagerange[$i]["start"])){$rsagerange[$i]["start"]="";}
				if(!isset($rsagerange[$i]["end"])){$rsagerange[$i]["end"]="";}
				if(!isset($rsdate["begin"][$d])){$rsdate["begin"][$d]="";}
				if(!isset($rsdate["end"][$d])){$rsdate["end"][$d]="";}
				
				$tmp[$i][$d]=$obj->sumagefield($rs,"total",$rsagerange[$i]["start"],$rsagerange[$i]["end"],$rsdate["begin"][$d],$rsdate["end"][$d]); 
				$total[$i]+=$tmp[$i][$d];
				$palltotal+=$tmp[$i][$d];
				$alltotal+=$tmp[$i][$d];
		}
}
for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
	$alldatetotal[$d]=0;
	for($i=0; $i<$rsagerange["rows"]; $i++) {
		$alldatetotal[$d] += $tmp[$i][$d];
	}
}
$percent = true;
if($palltotal==0){$palltotal=1;}	//fix problem divice by zero
if($begindate==$enddate&&$column!="Branch"){$column="Total only";$rsdate["header"][0]="TOTAL";}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&chkrow=40&gmt=".$_SESSION["__gmt"]);
}
$reportname="Age Of Customer Reports";
?>
<?
//Get All Branch
        $sql = "select branch_id, branch_name from bl_branchinfo where branch_id<>1 ";
        		if($city_id){$sql .= "and city_id=".$city_id." ";}else
        		if($branch_id){$sql .= "and branch_id=".$branch_id." ";}
        $sql.= "and branch_active=1 order by branch_name asc";
        $rsBranch = $obj->getResult($sql);
        

    			for($j=0; $j<$rsBranch["rows"]; $j++){
    				$nbranchdetail[$j] = $rsBranch[$j]["branch_name"];
    			}
    			if($nbranchdetail){
  	  				$NbranchSrdString = implode(", ", $nbranchdetail); 
  				}
?>
<?if($export){?><script type="text/javascript" src="../scripts/component.js"></script><?}?>
<?if($export!="Excel"&&$export!="PDF"){?><link href="../../../css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<?if($export!=false&&$export!="Excel"){ // begin check export function 
	for($a=0;$a<$alltable;$a++){
		if($column!="Total only"){
		if($a==0&&$a!=$alltable-1){$datechk["begin"][0]=0;$datechk["end"][0]=$chkcolumn-1;$datechk["rows"]=$chkcolumn;}
		else if($a==0&&$a==$alltable-1){
			$datechk["begin"][$a]=0;
			$datechk["end"][$a]=$rsdate["rows"]-1;
			$datechk["rows"]=$datechk["end"][$a]-$datechk["begin"][$a]+2;
		}
		else if($a==$alltable-1){
			$datechk["begin"][$a]=$datechk["begin"][$a-1]+$chkcolumn;
			$datechk["end"][$a]=$rsdate["rows"]-1;
			$datechk["rows"]=$datechk["end"][$a]-$datechk["begin"][$a]+2;
		}else{
			$datechk["begin"][$a]=$datechk["begin"][$a-1]+$chkcolumn;
			$datechk["end"][$a]=$datechk["begin"][$a]+$chkcolumn-1;
			$datechk["rows"]=$chkcolumn;}
		}else{
			$datechk["begin"][0]=0;$datechk["end"][0]=0;$datechk["rows"]=1;
		}
			//echo $alltable." : ".$datechk["begin"][$a]."-".$datechk["end"][$a];
		?>
<? if($a){?><hr style="page-break-before:always;border:0;color:#ffffff;" /><?$rowcnt=0;}?>
<?	
	$allcolumncnt = $datechk["end"][$a]-$datechk["begin"][$a]+1;
	if($column!="Total only"&&$a==$alltable-1){
		$allcolumncnt+=1;
	}
	if($percent&&$a==$alltable-1){$allcolumncnt+=1;}
	$columnwidth = 60/$allcolumncnt;
	$firstcolumnwidth = 100-($columnwidth*($allcolumncnt));
?>		
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0">
				<tr>		<!-- set column width for export to pdf 1 -->
					<td width="<?=$firstcolumnwidth?>%"></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%"></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="<?=$columnwidth?>%"></td>
					<? }?>
					<? if($percent&&$a==$alltable-1){?><td width="<?=$columnwidth?>%"></td><?} ?>
				</tr>
				<tr>
			    	<td class="reporth" align="center" style="white-space: nowrap;" colspan="<?=$allcolumncnt+1?>">
			    		<b><p>Spa Management System</p>
			    		<?=$reportname?></b><br>
			    		<p><b style='color:#ff0000'>
			    		<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
			    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
			    		</b></p>
			    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
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
					<? if($percent&&$a==$alltable-1){?><td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">PERCENT</b></td><?}?>
				</tr>
<?
if($order!="Total")	
{
		for($i=0; $i<$rsagerange["rows"]; $i++) { 	// start age range loop
		?>
					<tr height="22">
						<td style="padding-left:35px; white-space: nowrap;"><?=$rsagerange[$i]["start"]." - ".$rsagerange[$i]["end"]?></td>
					<? 	for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start date loop?>
						<td align="right" >
								<?	echo number_format($tmp[$i][$d],0,".",","); 	?>
						</td>
					<? 		
						} 
						if($column!="Total only"&&$a==$alltable-1){ ?>
									<td align="right">
									<?= number_format($total[$i],0,".",","); ?>
									</td>
					<? }?>
					<? if($a==$alltable-1){?>
									<td align="right">
											<?= ($percent)?number_format($total[$i]*100/$palltotal,2,".",","):" " ?>
									</td>
					<? } ?>
					</tr>
		<? }

}
else
{
	if($a==0){
		for($i=0; $i<$rsagerange["rows"]; $i++) { // start branch total loop for sort array of total in each branch
			$bagetotal[$rsagerange[$i]["start"]]=$total[$i];
			$eagetotal[$rsagerange[$i]["end"]]=$total[$i];
			if($sort=="A > Z"){arsort($bagetotal);arsort($eagetotal);}
			else{asort($bagetotal);asort($eagetotal);}
		}
		$i=0;	// resorting branch id to new array for show in report
		foreach ($bagetotal as $key => $val) {
  			  $tmpbagetotal[$i] = $key;
  			  $total[$i] = $val;
  			  $i++;
		}
		$i=0;
		foreach ($eagetotal as $key => $val) {
  			  $tmpeagetotal[$i] = $key;
  			  $i++;
		}
	}
	
	for($i=0; $i<$rsagerange["rows"]; $i++) {
?>
		<tr height="22" >
			<td style="padding-left:35px; white-space: nowrap;"><?=$tmpbagetotal[$i]." - ".$tmpeagetotal[$i]?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?	$tmp[$i][$d]=$obj->sumagefield($rs,"total",$tmpbagetotal[$i],$tmpeagetotal[$i],$rsdate["begin"][$d],$rsdate["end"][$d]); 
			echo number_format($tmp[$i][$d],0,".",",");	?>
			</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right">
			<?= number_format($total[$i],0,".",","); ?>
			</td>
			<? }?>
			<? if($a==$alltable-1){?>
			<td align="right">
					<?= ($percent)?number_format($total[$i]*100/$palltotal,2,".",","):" " ?>
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
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#eeeee\"><b>";
		echo number_format($alldatetotal[$d],0,".",",");
		echo "</b></td>\n";
}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
				
			<td align="right" bgcolor="#eeeee"><b>
			<?= number_format($alltotal,0,".",","); ?>
			</b></td>
			
			<? }?>
			<? if($a==$alltable-1){?>
			
			<td align="right" bgcolor="#eeeee"><b>
			<?=($percent)?number_format($alltotal*100/$palltotal,2,".",","):" "?>
			</b></td>
			
			<? } ?>
			
			</tr>
		    <tr>
		    	<td align="center" colspan="<?=$allcolumncnt+1?>">
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
 </p>
	<?
	}// end check export file
?>	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
<? }else{ ?>
	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	
	<tr>
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0">
			<tr>
		    	<td class="reporth" align="center" colspan="<?=($column=="Total only")?$rsdate["rows"]+2:$rsdate["rows"]+3 ?>">
		    		<b><p>Spa Management System</p>
		    		Age Of Customer Reports</b><br>
		    		<p class="style1">
		    		<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
		    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></p>
		    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
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
					<td style="text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>PERCENT</b></td>
				</tr>
<?
if($order!="Total")	{
		for($i=0; $i<$rsagerange["rows"]; $i++) { 	// start age range loop
		?>
					<tr height="22">
						<td style="padding-left:35px; white-space: nowrap;"><?=$rsagerange[$i]["start"]." - ".$rsagerange[$i]["end"]?></td>
					<? 	for($d=0;$d<$rsdate["rows"];$d++){ // start date loop?>
						<td align="right">
								<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000; font-weight:normal;" onClick="openAgeDetail('<?=$rsdate["begin"][$d]."','".$rsdate["end"][$d]."',".$rsagerange[$i]["start"].",".$rsagerange[$i]["end"]?>)"><? } ?>
								<?= number_format($tmp[$i][$d],0,".",",")	?>
								<?if($export==false){?></a><? } ?>
						</td>
					<? 	} 
						if($column!="Total only"){ ?>
									<td align="right">
									<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openAgeDetail('<?=$rsdate["begin"][0]."','".$rsdate["end"][$d-1]."',".$rsagerange[$i]["start"].",".$rsagerange[$i]["end"]?>)"><?}?>
									<?= number_format($total[$i],0,".",",")?>
									<?if($export==false){?></a><?}?>
									</td>
					<? } ?>
						<td align="right">
								<?= ($percent)?number_format($total[$i]*100/$palltotal,2,".",","):" " ?>
						</td>
					</td>
<?		}
}
else
{
		for($i=0; $i<$rsagerange["rows"]; $i++) { // start branch total loop for sort array of total in each branch
				$bagetotal[$rsagerange[$i]["start"]]=$total[$i];
				$eagetotal[$rsagerange[$i]["end"]]=$total[$i];

			if($sort=="A > Z"){arsort($bagetotal);arsort($eagetotal);}
			else{asort($bagetotal);asort($eagetotal);}
		}
		$i=0;	// resorting branch id to new array for show in report
		foreach ($bagetotal as $key => $val) {
  			  $tmpbagetotal[$i] = $key;
  			  $total[$i] = $val;
  			  $i++;
		}
		$i=0;
		foreach ($eagetotal as $key => $val) {
  			  $tmpeagetotal[$i] = $key;
  			  $i++;
		}
		
		for($i=0; $i<$rsagerange["rows"]; $i++) {
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$tmpbagetotal[$i]." - ".$tmpeagetotal[$i]?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openAgeDetail('<?=$rsdate["begin"][$d]."','".$rsdate["end"][$d]."',".$tmpbagetotal[$i].",".$tmpeagetotal[$i]?>)"><?}?>
			<?	$tmp[$i][$d]=$obj->sumagefield($rs,"total",$tmpbagetotal[$i],$tmpeagetotal[$i],$rsdate["begin"][$d],$rsdate["end"][$d]); 
				echo number_format($tmp[$i][$d],0,".",",") 	?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openAgeDetail('<?=$rsdate["begin"][0]."','".$rsdate["end"][$d]."',".$tmpbagetotal[$i].",".$tmpeagetotal[$i]?>)"><?}?>
			<?=number_format($total[$i],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			<td align="right">
			<?=($percent)?number_format($total[$i]*100/$palltotal,2,".",","):"" ?>
			</td>
		</tr>
<?
	}
	
}
?>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>TOTAL</b></td>
			
<?
for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
		//if($percent){$palldatetotal=number_format($alldatetotal[$d]*100/$palltotal,2,".",",");}
?>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3" style="color:#000000;"><b>
		<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openAgeDetail('<?=$rsdate["begin"][$d]."','".$rsdate["end"][$d]."',0,0"?>)"><?}?>
			<?=number_format($alldatetotal[$d],0,".",",")?>
		<?if($export==false){?></a><?}?>
		</b></td>
<?
}
?>		
			<? if($column!="Total only"){ ?>
				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openAgeDetail('<?=$rsdate["begin"][$d]."','".$rsdate["end"][$d]."',0,0"?>)"><?}?>
			<?=number_format($alltotal,0,".",",")?>
			<?if($export==false){?></a><?}?>
			</b></td>
			
			<? } ?>
			
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=($percent)?number_format($alltotal*100/$palltotal,2,".",","):" " ?>
			</td>
			
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