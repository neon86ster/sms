<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("customer.inc.php");
$obj = new customer();
$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$branchid= $obj->getParameter("branchid");
$cityid = $obj->getParameter("cityid",false);
$column= $obj->getParameter("column");
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");
$collapse = $obj->getParameter("Collapse");
$resident = $obj->getParameter("resident");
$percent = $obj->getParameter("percent",true);
$percent = true;
$showall = $obj->getParameter("showall");
$branchtotal = array();
$today = date("Ymd");
$rsdate = $obj->getdatecol($column,$begin_date,$end_date);
$rs = $obj->getcustnation($begin_date,$end_date,0,0,0,$branchid,$cityid);
$rscontinent = $obj->getcontinent($order,$sort);
$continent = array();

$rsnationality = $obj->getnationality($order,$sort);
$nationality = array();

if($showall==false){
	for($j=0; $j<$rs["rows"]; $j++){
		if(!isset($rs[$j-1]["nationality_id"])){$rs[$j-1]["nationality_id"]=0;}
		if(!isset($rs[$j-1]["continent_id"])){$rs[$j-1]["continent_id"]=0;}
		if($rs[$j]["nationality_id"]!=$rs[$j-1]["nationality_id"]){
			$nationality[$j]=$rs[$j]["nationality_id"];
		}
		if($rs[$j]["continent_id"]!=$rs[$j-1]["continent_id"]){
			$continent[$j]=$rs[$j]["continent_id"];
		}
	}
}
$allcontinent = implode(",",$continent);
$allnationality = implode(",",$nationality);
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Customer Nationality Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export!=false&&$export!="Excel"){
	$chkcolumn=7;
	$alltable=ceil($rsdate["rows"]/$chkcolumn);
	if($column=="Total only"){$alltable=1;}
	$alltotal=0;
}
	
$palltotal = 0;$alltotal = 0;
for($i=0; $i<$rscontinent["rows"]; $i++) {		// start continent loop		
	if($obj->getIdToText($rscontinent[$i]["continent_id"],"dl_nationality","nationality_id","continent_id","continent_id IN ( $allcontinent ) and nationality_active=1")>0){
		$totalcontinent[$i]=0;
		for($k=0; $k<$rsnationality["rows"]; $k++) {
			if($rsnationality[$k]["continent_id"]==$rscontinent[$i]["continent_id"]&&
				$obj->getIdToText($rsnationality[$k]["nationality_id"],"dl_nationality","nationality_id","nationality_id","nationality_id IN ( $allnationality ) and nationality_active=1")>0){
				$total[$k] = 0;
				for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
					$tmp[$k][$d]=$obj->sumqtynation($rs,"qty",$rsnationality[$k]["nationality_id"],$rsdate["begin"][$d],$rsdate["end"][$d]); 
					$total[$k]+=$tmp[$k][$d];
					$totalcontinent[$i]+=$tmp[$k][$d];
					$alltotal+=$tmp[$k][$d];
					$palltotal+=$tmp[$k][$d];
				}
			}
		}
	}
}
for($d=0;$d<$rsdate["rows"];$d++){ 
		$alldatetotal[$d] = 0;
		$palldatetotal[$d] = 0;
		for($k=0; $k<$rsnationality["rows"]; $k++) {
				if($obj->getIdToText($rsnationality[$k]["nationality_id"],"dl_nationality","nationality_id","nationality_id","nationality_id IN ( $allnationality ) and nationality_active=1")>0){
						$alldatetotal[$d]+=$tmp[$k][$d];
						$palldatetotal[$d]+=$tmp[$k][$d];
					}
		}
		if($palldatetotal[$d]==0){$palldatetotal[$d] = 1;}
}
if($palltotal==0){$palltotal=1;} //fix problem divice by zero
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
if($collapse=="Collapse"){	//check Collapse/Expand loop
	$chkrow=30;
}else{
	$chkrow=40;
}
$branchname=$obj->getIdToText($branchid,"bl_branchinfo","branch_name","branch_id");
/*
if($branchid==0 || strtolower($branchname)=="all"){
	$reportname = "All Customer Nationality Report";
}else{
	$reportname = $branchname."'s Customer Nationality Report";
}*/
$reportname = "Customer Nationality Report";
?>
<?
//Get All Branch
        $sql = "select branch_id, branch_name from bl_branchinfo where branch_id<>1 ";
        		if($cityid){$sql .= "and city_id=".$cityid." ";}else
        		if($branchid){$sql .= "and branch_id=".$branchid." ";}
        $sql.= "and branch_active=1 order by branch_name asc";
        $rsBranch = $obj->getResult($sql);
        

    			for($j=0; $j<$rsBranch["rows"]; $j++){
    				$nbranchdetail[$j] = $rsBranch[$j]["branch_name"];
    			}
    			if($nbranchdetail){
  	  				$NbranchSrdString = implode(", ", $nbranchdetail); 
  				}
?>
<?if($export!="Excel"&&$export!="PDF"){?><script type="text/javascript" src="../scripts/ajax.js"></script><?}?>
<?if($export!="Excel"&&$export!="PDF"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<?if($export!=false&&$export!="Excel"){ // begin check export function 
$rowcnt=0;
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

<?
$rowcnt++;
// define header for sparate in export page.
$header = "\t<tr height=\"20\">\n";
$header .= "\t\t<td width=\"100%\" align=\"center\" colspan=\"".($allcolumncnt+1)."\" >\n";
$header .= "\t\t\t<br><b>Printed: </b>".$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")."\n";
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
for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
	$header .= "\t\t\t\t\t<td width=\"$columnwidth%\"></td>\n";
}
if($column!="Total only"&&$a==$alltable-1){
	$header .= "\t\t\t\t\t<td width=\"$columnwidth%\"></td>\n";
}
if($percent&&$a==$alltable-1){
	$header .= "\t\t\t\t\t<td width=\"$columnwidth%\"></td>\n";
}
$header .= "\t\t\t\t</tr>\n";
$header .= "\t\t\t\t<tr>\n";
$header .= "\t\t\t\t\t<td class=\"reporth\" align=\"center\" style=\"white-space: nowrap;\" colspan=\"".($allcolumncnt+1)."\">\n";
$header .= "\t\t\t\t\t<b><p>Spa Management System</p>\n";
$header .= "\t\t\t\t\t$reportname</b><br>\n";
$header .= "\t\t\t\t\t<p><b style='color:#ff0000'>\n";
$header .= "\t\t\t\t\t".$dateobj->convertdate($begindate,$sdateformat,$ldateformat);
$header .= (($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat))."\n";
$header .= "\t\t\t\t\t<br><br></b></p>\n";
$header .= "\t\t\t\t\t</td>\n";		
$header .= "\t\t\t\t</tr>\n";
$header .= "\t\t\t\t<tr height=\"35\">\n";	    	
$header .= "\t\t\t\t\t<td width=\"90\" style=\"text-align:left; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>&nbsp;</b></td>\n";		
for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
	$header .= "\t\t\t\t\t<td width=\"40\" style=\"text-align:right;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b style=\"text-decoration: underline;\">".$rsdate["header"][$d]."</b></td>\n";				
}
if($column!="Total only"&&$a==$alltable-1){
	$header .= "\t\t\t\t\t<td width=\"40\" style=\"text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b style=\"text-decoration: underline;\">TOTAL</b></td>\n";
}	
if($percent&&$a==$alltable-1){
	$header .= "\t\t\t\t\t<td width=\"40\" style=\"text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b style=\"text-decoration: underline;\">PERCENT</b></td>\n";
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
					<? if($percent&&$a==$alltable-1){?><td width="<?=$columnwidth?>%"></td><?} ?>
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
					<? if($percent&&$a==$alltable-1){?><td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">PERCENT</b></td><?}?>
				</tr>
<?
if($collapse=="Collapse"){	//check Collapse/Expand loop

	for($i=0; $i<$rscontinent["rows"]; $i++) {		// start city loop	
	if(!isset($totalcontinent[$i])){$totalcontinent[$i]=0;}
	if($totalcontinent[$i]>0){	
?>
<tr height="32"><?$rowcnt++;?>
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #000000 double;" bgcolor="#eaeaea"><b>Location: <?=$rscontinent[$i]["continent_name"]?></b></td>
	<td colspan="<?=$allcolumncnt?>" bgcolor="#eaeaea" style="border-bottom:3px #000000 double;">&nbsp;</td><!-- input city -->
</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?
for($k=0; $k<$rsnationality["rows"]; $k++) { 	// start branch name loop
	if(!isset($total[$k])){$total[$k]=0;}
		if($rsnationality[$k]["continent_id"]==$rscontinent[$i]["continent_id"]&&$total[$k]>0){
?>
						<tr height="22"><?$rowcnt++;?>
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsnationality[$k]["nationality_name"]?></td>
							<? 	for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>		
								<td align="right"><?=number_format($tmp[$k][$d],0,".",",")?></td>
								<? } ?>	
							<? if($column!="Total only"&&$a==$alltable-1){?>
							<td align="right">
							<?=number_format($total[$k],0,".",",")?>
							</td>
							<? } ?>
							<? if($a==$alltable-1){?>
							<td align="right">
							<?= ($percent)?number_format($total[$k]*100/$palltotal,2,".",","):" " ?>
							</td>
							<? } ?>
						</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?						}
					} 
					?>
		<?  if($a==$alltable-1){ ?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=$allcolumncnt-1?>"><b>Total in <?=$rscontinent[$i]["continent_name"]?></b></td>
			<td align="right">
			<b><?=number_format($totalcontinent[$i],0,".",",")?></b>
			</td>
			<? } ?>
			<? if($a==$alltable-1){?>
			<td align="right">
			<b><?=($percent)?number_format($totalcontinent[$i]*100/$palltotal,2,".",","):" "?></b>
			</td>
		</tr>
		<?}?>
<?	
	} 
}

}	// End check collapse expand loop
else if($order!="Total")		// for colapse information report
{
		for($k=0; $k<$rsnationality["rows"]; $k++) {
		if(!isset($total[$k])){$total[$k]=0;}
			if($total[$k]>0){
?>
		<tr height="22"><?$rowcnt++;?>
			<td style="padding-left:35px; white-space: nowrap;"><?=$rsnationality[$k]["nationality_name"]?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?=number_format($tmp[$k][$d],0,".",",")?>
			</td>
			<?}?>			
			<? if($column!="Total only"&&$a==$alltable-1){?>
			<td align="right">
			<?= number_format($total[$k],0,".",",")?>
			</td>
			<? } ?>
			<? if($a==$alltable-1){?>
			<td align="right">
			<?= ($percent)?number_format($total[$k]*100/$palltotal,2,".",","):" "?>
			</td>
			<? } ?>
		</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?			}
		}
}
else
{
	if($a==0){
		for($k=0; $k<$rsnationality["rows"]; $k++) { // start nationality total loop for sort array of total in each rows
			for($d=0;$d<$rsdate["rows"];$d++){
				if($obj->getIdToText($rsnationality[$k]["nationality_id"],"dl_nationality","nationality_id","nationality_id","nationality_id IN ( $allnationality ) and nationality_active=1")>0){
					$nationtotal[$rsnationality[$k]["nationality_id"]]=$total[$k];
				}
			}
		}
		if($sort=="A > Z"){arsort($nationtotal);}
		else{asort($nationtotal);}
		//print_r($nationtotal);
		$total = array();
		$cnt=0;	// resorting branch id to new array for show in report
		foreach ($nationtotal as $key => $val) {
  			  $tmpnationtotal[$cnt] = $key;
  			  $total[$cnt] = $val;
  			  $cnt++;
		}
	}
$rowcnt=0;
		for($k=0; $k<$cnt; $k++) { 	

?>
		<tr height="22"><?$rowcnt++;?>
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpnationtotal[$k],"dl_nationality","nationality_name","nationality_id")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?	$tmp[$k][$d]=$obj->sumqtynation($rs,"qty",$tmpnationtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]); 
			echo number_format($tmp[$k][$d],0,".",",") 	?>
			</td>
			<? } ?>
			<? if($column!="Total only"&&$a==$alltable-1){?>
			<td align="right">
			<?= number_format($total[$k],0,".",",")?>
			</td>
			<? } ?>
			<? if($a==$alltable-1){?>
			<td align="right">
			<?= ($percent)?number_format($total[$k]*100/$palltotal,2,".",","):" " ?>
			</td>
			<? } ?>
		</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?	}
}
?>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#eaeaea"><b>TOTAL</b></td>
			
<?
for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start branch total loop
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#eaeaea\"><b>";
		echo number_format($alldatetotal[$d],0,".",",");
		echo "</a></b></td>\n";
}
?>
			<? if($column!="Total only"&&$a==$alltable-1){?>
				
			<td align="right" bgcolor="#eaeaea"><b style="color:#000000;">
			<?=number_format($alltotal,0,".",",")?>
			</b></td>
			
			<? } ?>
			<? if($a==$alltable-1){?>
				
			<td align="right" bgcolor="#eaeaea"><b style="color:#000000;">
			<?=($percent)?number_format($alltotal*100/$palltotal,2,".",","):" "?>
			</b></td>
			<? } ?>
	</tr>
    <tr>
    	<td align="center" colspan="<?=$allcolumncnt+1?>" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
 </table>
		</td>
	</tr>
</table>
<? }// end check export file ?>	
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script><?}?>
<? }else{ ?>
	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	
	<tr>
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0">
			<tr>
		    	<td class="reporth" align="center" colspan="<?=($column=="Total only")?$rsdate["rows"]+2:$rsdate["rows"]+3 ?>">
		    		<b><p>Spa Management System</p>
		    		<?=$reportname?></b><br>
		    		<p><b style='color:#ff0000'>
		    		<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
		    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
		    		</b></p>
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
if($collapse=="Collapse"){	//check Collapse/Expand loop
	
for($i=0; $i<$rscontinent["rows"]; $i++) {		// start city loop		
if($obj->getIdToText($rscontinent[$i]["continent_id"],"dl_nationality","nationality_id","continent_id","continent_id IN ( $allcontinent ) and nationality_active=1")>0){
?>
<tr height="32">
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #000000 double;" bgcolor="#d3d3d3"><b>Location: <?=$rscontinent[$i]["continent_name"]?></b></td>
	<td colspan="<?=($column=="Total only")?$rsdate["rows"]+1:$rsdate["rows"]+2?>" bgcolor="#d3d3d3" style="border-bottom:3px #000000 double;">&nbsp;</td><!-- input city -->
</tr>
<?
for($k=0; $k<$rsnationality["rows"]; $k++) { 	// start branch name loop
		if($rsnationality[$k]["continent_id"]==$rscontinent[$i]["continent_id"]&&
		$obj->getIdToText($rsnationality[$k]["nationality_id"],"dl_nationality","nationality_id","nationality_id","nationality_id IN ( $allnationality ) and nationality_active=1")>0){
?>
						<tr height="22">
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsnationality[$k]["nationality_name"]?></td>
							<? for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop ?>		
								<td align="right">
									<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openNationDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rscontinent[$i]["continent_id"].",".$rsnationality[$k]["nationality_id"]?>)" class="pagelink"><? } ?>
									<?=number_format($tmp[$k][$d],0,".",",")?>
									<?if($export==false){?></a><? } ?>
								</td>
							<? } ?>	
							<? if($column!="Total only"){ ?>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openNationDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rsnationality[$k]["nationality_id"]?>)" class="pagelink"><?}?>
							<?= number_format($total[$k],0,".",",") ?>
							<?if($export==false){?></a><?}?>
							</td>
							<? } ?>
							<td align="right">
							<?= ($percent)?number_format($total[$k]*100/$palltotal,2,".",","):" "?>
							</td>
						</tr>
						<?	
						}
					} 
					?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=($column=="Total only")?$rsdate["rows"]:$rsdate["rows"]+1?>"><b>Total in <?=$rscontinent[$i]["continent_name"]?></b></td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openNationDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rscontinent[$i]["continent_id"].",0"?>)" class="pagelink"><?}?>
			<b><?=number_format($totalcontinent[$i],0,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<b><?= ($percent)?number_format($totalcontinent[$i]*100/$palltotal,2,".",","):" "?></b>
			</td>
		</tr>
<?	
	} 
}

}	// End check collapse expand loop
else if($order!="Total")		// for colapse information report
{
		for($k=0; $k<$rsnationality["rows"]; $k++) {// start branch total loop
			if($obj->getIdToText($rsnationality[$k]["nationality_id"],"dl_nationality","nationality_id","nationality_id","nationality_id IN ( $allnationality ) and nationality_active=1")>0){
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$rsnationality[$k]["nationality_name"]?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openNationDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rsnationality[$k]["nationality_id"]?>)" class="pagelink"><?}?>
			<?=number_format($tmp[$k][$d],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openNationDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rsnationality[$k]["nationality_id"]?>)" class="pagelink"><?}?>
			<?=number_format($total[$k],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			<td align="right">
			<?= ($percent)?number_format($total[$k]*100/$palltotal,2,".",","):" "?>
			</td>
		</tr>
<?
			}
		}
}
else
{
		for($k=0; $k<$rsnationality["rows"]; $k++) { // start nationality total loop for sort array of total in each rows
			for($d=0;$d<$rsdate["rows"];$d++){
				if($obj->getIdToText($rsnationality[$k]["nationality_id"],"dl_nationality","nationality_id","nationality_id","nationality_id IN ( $allnationality ) and nationality_active=1")>0){
					$nationtotal[$rsnationality[$k]["nationality_id"]]=$total[$k];
				}
			}
		}
		if($sort=="A > Z"){arsort($nationtotal);}
		else{asort($nationtotal);}
		//print_r($nationtotal);
		$total = array();
		$cnt=0;	// resorting branch id to new array for show in report
		foreach ($nationtotal as $key => $val) {
  			  $tmpnationtotal[$cnt] = $key;
  			  $total[$cnt] = $val;
  			  $cnt++;
		}
		//print_r($total);echo $column;
		for($k=0; $k<$cnt; $k++) { 
			
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpnationtotal[$k],"dl_nationality","nationality_name","nationality_id")?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openNationDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$tmpnationtotal[$k]?>)"><?}?>
			<?	$tmp[$k][$d]=$obj->sumqtynation($rs,"qty",$tmpnationtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]); 
			echo number_format($tmp[$k][$d],0,".",",") 	?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openNationDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$tmpnationtotal[$k]?>)"><?}?>
			<?=number_format($total[$k],0,".",",") ?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			<td align="right">
			<?=($percent)?number_format($total[$k]*100/$palltotal,2,".",","):" "?>
			</td>
		</tr>
<? } ?>
<? } ?>
		<tr height="22" style="color:#000000;">
			<td style="padding-left: 20px; white-space: nowrap; color:#000000;" align="right" bgcolor="#d3d3d3"><b>TOTAL</b></td>
			
<?
for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
		if($export==false){
			echo "<a href=\"javascript:;\" style=\"text-decoration:none; color:#000000;\" onClick=\"openNationDetail(".$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0)\">";
			echo number_format($alldatetotal[$d],0,".",",");
			echo "</a>";
		}else{
			echo number_format($alldatetotal[$d],0,".",",");
		}
		echo "</b></td>\n";
}
?>
			<? if($column!="Total only"){ ?>
				
			<td align="right" bgcolor="#d3d3d3"><b style="color:#000000;">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openNationDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0"?>)"><?}?>
			<?=number_format($alltotal,2,".",",")?>
			<?if($export==false){?></a><?}?></b></td>
			
			<? } ?>
			<td align="right" bgcolor="#d3d3d3"><b style="color:#000000;">
			<?=($percent)?number_format($alltotal*100/$palltotal,2,".",","):" "?>
			</b></td>
			
			</tr>
		    <tr>
		    	<td align="center" colspan="<?=($column=="Total only")?$rsdate["rows"]+1:$rsdate["rows"]+2?>">
		    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
		    	</td>
			</tr>
 		</table><br>
		</td>
    </tr>
</table>
<? } ?>