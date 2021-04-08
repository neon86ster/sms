<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("inventory.inc.php");
$obj = new inventory();
$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate= $obj->getParameter("end");

$sdate = substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2);
$edate = substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2);
	
$column= $obj->getParameter("column");
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");
$collapse = $obj->getParameter("Collapse");
$branch = $obj->getParameter("branchid",0);
$percent = $obj->getParameter("percent");
$showall = $obj->getParameter("showall");
$today = date("Ymd");
$rsdate = $obj->getdatecol($column,$begindate,$enddate,$branch);
$rs = $obj->getinventory($begindate,$enddate,$branch);
$rscategory = $obj->gettrmcategory($order,$sort);
$category=array();
$rstrm = $obj->gettrm($order,$sort);
$trm = array();

if($showall==false){
	for($j=0; $j<$rs["rows"]; $j++){
	if(!isset($rs[$j-1]["trm_id"])){$rs[$j-1]["trm_id"]=0;}
	if(!isset($rs[$j-1]["trm_category_id"])){$rs[$j-1]["trm_category_id"]=0;}
		if($rs[$j]["trm_id"]!=$rs[$j-1]["trm_id"]){
			$trm[$j]=$rs[$j]["trm_id"];
		}
		if($rs[$j]["trm_category_id"]!=$rs[$j-1]["trm_category_id"]){
			$category[$j]=$rs[$j]["trm_category_id"];
		}
	}
}
$allcategory = implode(",",$category);
$alltrm = implode(",",$trm);

$export = $obj->getParameter("export",false);
if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Therapist Inventory Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
$trmtotal = array();
for($i=0; $i<$rscategory["rows"]; $i++) {	
	for($d=0;$d<$rsdate["rows"];$d++){
		$totalcategorypd[$i][$d] = 0;
	}
}
$palltotal = 0;
$alltotal = 0;
for($i=0; $i<$rscategory["rows"]; $i++) {		// start category loop	
	if($obj->getIdToText($rscategory[$i]["trm_category_id"],"db_trm","trm_id","trm_category_id","trm_category_id IN ( $allcategory ) and trm_active=1")>0){
		$totalcategory[$i]=0;
		for($k=0; $k<$rstrm["rows"]; $k++) {
			if($obj->getIdToText($rstrm[$k]["trm_id"],"db_trm","trm_id","trm_id","trm_id IN ( $alltrm ) and trm_active=1")>0){
				if($rstrm[$k]["trm_category_id"]==$rscategory[$i]["trm_category_id"]){
					$total[$k] = 0;
					for($d=0;$d<$rsdate["rows"];$d++){ // start trm total loop
						$tmp[$k][$d]=$obj->sumeachinventoryfield($rs,"total",$rstrm[$k]["trm_id"],$rsdate["begin"][$d],$rsdate["end"][$d]); 
						$total[$k]+=$tmp[$k][$d];
						$totalcategory[$i]+=$tmp[$k][$d];
						$totalcategorypd[$i][$d]+=$tmp[$k][$d];
						$alltotal+=$tmp[$k][$d];
						$palltotal+=$tmp[$k][$d];
						if($column=="Branch"){
							$rsdate["branchid"][$d] = $rsdate["end"][$d];
						}else{$rsdate["branchid"][$d] = $branch;}
					}
				}
			}
		}
	}
//For Steam
	if($rscategory[$i]["trm_category_id"]==6){
		for($d=0;$d<$rsdate["rows"];$d++){
		if(!isset($total[$k])){$total[$k]=0;}
		if(!isset($tmp[$k][$d])){$tmp[$k][$d]=0;}
		if(!isset($totalcategory[$i])){$totalcategory[$i]=0;}
			$sql_stram="select d_indivi_info.*,a_bookinginfo.b_branch_id " .
					"from d_indivi_info,a_bookinginfo where " .
					"d_indivi_info.book_id=a_bookinginfo.book_id " .
					"and d_indivi_info.stream=1 ";
					if($branch){$sql_stram .= "and a_bookinginfo.b_branch_id=".$branch." ";}
					if($rsdate["branchid"][$d] && $column=="Branch"){$sql_stram .="and a_bookinginfo.b_branch_id=".$rsdate["branchid"][$d]." ";
					$sql_stram .= "and a_bookinginfo.b_appt_date>='".$sdate."' and a_bookinginfo.b_appt_date<='".$edate."' ";}
					else{$sql_stram .= "and a_bookinginfo.b_appt_date>='".$rsdate["begin"][$d]."' and a_bookinginfo.b_appt_date<='".$rsdate["end"][$d]."' ";}
			//echo $sql_stram."<br><br>";
			$rs_stram=$obj->getResult($sql_stram);
			if($rs_stram){
				$totalcategorypd[$i][$d]=$rs_stram["rows"];
				$totalcategory[$i]=$totalcategory[$i]+$totalcategorypd[$i][$d];
				$total[$k]+=$tmp[$k][$d];
			}
		}
	}
//End
}
//echo "1. column".$rsdate["rows"].": ".$column;die();
for($d=0;$d<$rsdate["rows"];$d++){
	if($column=="Branch"){
			$rsdate["begin"][$d] = $begindate;
			$rsdate["end"][$d] = $enddate;
	}else{$rsdate["branchid"][$d] = $branch;}
}
if($column=="Branch"){
	$rsdate["end"][$rsdate["rows"]]=0;
}else{$rsdate["end"][$rsdate["rows"]] = $branch;}
//echo "1. column: 2";die();
//=-----------------------------------------------------------------------------
/*for($d=0;$d<$rsdate["rows"];$d++){ 
		$alldatetotal[$d] = 0;
		$palldatetotal[$d] = 0;
		for($k=0; $k<$rstrm["rows"]; $k++) {
				$alldatetotal[$d]+=$tmp[$k][$d];
				$palldatetotal[$d]+=$tmp[$k][$d];
		}
		if($palldatetotal[$d]==0){$palldatetotal[$d] = 1;}
		if($column=="Branch"){
			$rsdate["begin"][$d] = $begindate;
			$rsdate["end"][$d] = $enddate;
		}
}*/
if($palltotal==0){$palltotal=1;} //fix problem divice by zero
//=-----------------------------------------------------------------------------

$pr=0;
if($percent){
	$palltotal = $alltotal;
	if($alltotal==0){$palltotal=1;}
	$pr=1;
}
if($export!="Excel"&&$export){
	$chkcolumn=7;		// row column per page
	$alltable=ceil($rsdate["rows"]/$chkcolumn);
	if($column=="Total only"){$alltable=1;}
	$rowcnt=0;
	if($collapse=="Collapse"){$chkrow = $obj->getParameter("chkrow",30);}
	else{$chkrow = $obj->getParameter("chkrow",35);}
}
if($begindate==$enddate&&$column!="Branch"){$column="Total only";$rsdate["header"][0]="TOTAL";}
$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);

$branchname=$obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
if($branch==0 || strtolower($branchname)=="all"){
	$reportname = "Therapist Inventory Report";
}else{
	$reportname = $branchname."'s Therapist Inventory Report";
}
?>

<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
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
		?>
<? if($a){?><hr style="page-break-before:always;border:0;color:#ffffff;" /><?}?>	
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
$header .= "\t\t\t\t\t".$dateobj->convertdate($begindate,$sdateformat,$ldateformat)."\n";
$header .= "\t\t\t\t\t".(($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat))."\n";
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

for($i=0; $i<$rscategory["rows"]; $i++) {		// start category loop	
if(!isset($totalcategory[$i])){$totalcategory[$i]=0;}
	if($totalcategory[$i]>0){					// if total category > 0  
?>
<tr height="32"><?$rowcnt++;?>
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #d0d0d0 double;" bgcolor="#eaeaea"><b><?=$rscategory[$i]["trm_category_name"]?> : </b></td>
	<td colspan="<?=$allcolumncnt?>" bgcolor="#eaeaea" style="border-bottom:3px #d0d0d0 double;">&nbsp;</td><!-- treatment category -->
</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?
			for($k=0; $k<$rstrm["rows"]; $k++) { 	// start branch name loop
			if(!isset($rstrm[$k]["trm_category_id"])){$rstrm[$k]["trm_category_id"]="";}
			if(!isset($rscategory[$i]["trm_category_id"])){$rscategory[$i]["trm_category_id"]="";}
			if(!isset($total[$k])){$total[$k]=0;}
			if($rstrm[$k]["trm_category_id"]==$rscategory[$i]["trm_category_id"]&&$total[$k]>0){
?>
<tr height="22"><?$rowcnt++;?>
			<td style="padding-left:35px; white-space: nowrap;"><?=$rstrm[$k]["trm_name"]?></td>
			<? 	
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ 
			?>		
			<td align="right"><?=$tmp[$k][$d]?></td>
			<? } ?>	
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right"><?=$total[$k]?></td>
			<? } ?>
			
			<? if($percent&&$a==$alltable-1){?>
			<td align="right"><?= number_format(100*$total[$k]/$palltotal,2,".",",")?></td>
			<?}?>
</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?				}
			} 
?>
<tr height="35"><?$rowcnt++;?>
			<td style="padding-left: 20px; white-space: nowrap;" align="right"><b>Total in <?=$rscategory[$i]["trm_category_name"]?></b></td>
			<?	
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ 
			?>
			<td align="right">
			<b><?= $totalcategorypd[$i][$d]?></b>
			</td>
			<? } ?>	
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right">
			<b><?=$totalcategory[$i]?></b>
			</td>
			<?}?>
</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?	
	} 
}

}	// End check collapse expand loop
else if($order!="Total")		// for colapse information report
{
		for($k=0; $k<$rstrm["rows"]; $k++) {	// start trm loop
		if(!isset($total[$k])){$total[$k]=0;}
		if($total[$k]>0){
?>
		<tr height="22"><?$rowcnt++;?>
			<td style="padding-left:35px; white-space: nowrap;"><?=$rstrm[$k]["trm_name"]?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right"><?=$tmp[$k][$d]?></td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right"><?=$total[$k]?></td>
	 		<? } ?>
			
			<? if($percent&&$a==$alltable-1){?>
			<td align="right">
			<?=number_format(100*$total[$k]/$palltotal,2,".",",")?>
			</td>
			<?}?>
		</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?			}
		}
}
else
{
		if($a==0){
			for($k=0; $k<$rstrm["rows"]; $k++) { // start trm total loop for sort array of total in each rows
				for($d=0;$d<$rsdate["rows"];$d++){
					if($obj->getIdToText($rstrm[$k]["trm_id"],"db_trm","trm_id","trm_id","trm_id IN ( $alltrm )")>0){
						$trmtotal[$rstrm[$k]["trm_id"]]=$total[$k];
					}
				}
				if($sort=="A > Z"){arsort($trmtotal);}
				else{asort($trmtotal);}
			}
			$total = array();
			$cnt=0;	// resorting branch id to new array for show in report
			foreach ($trmtotal as $key => $val) {
	  			  $tmptrmtotal[$cnt] = $key;
	  			  $total[$cnt] = $val;
	  			  $cnt++;
			}
		}

		for($k=0; $k<$cnt; $k++) { 	
?>
		<tr height="22"><?$rowcnt++;?>
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmptrmtotal[$k],"db_trm","trm_name","trm_id")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?	$tmp[$k][$d]=$obj->sumeachinventoryfield($rs,"total",$tmptrmtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d],$rsdate["branchid"][$d]); 
			echo $tmp[$k][$d];?>
			</td>
			<? } ?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right">
			<?=$total[$k]?>
			</td>
			<? } ?>
			
			<? if($percent&&$a==$alltable-1){?>
			<td align="right">
			<?=number_format(100*$total[$k]/$palltotal,2,".",",")?>
			</td>
			<?}?>
		</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?	}
}
?>
<? /* ?>
<!--
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>TOTAL</b></td>
			
<?
for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start branch total loop
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
		echo $alldatetotal[$d];
		echo "</a></b></td>\n";
}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=$alltotal?>
			</b></td>
			
			<? } ?>
			
			<? if($percent&&$a==$alltable-1){ ?>
				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=number_format(100*$alltotal/$palltotal,2,".",",")?>
			</b></td>
			
			<? } ?>
    </tr>
<?if($percent){?>
		<tr height="20">
			<td colspan="<?=($column=="Total only")?$rsdate["rows"]+1+$pr:$rsdate["rows"]+2+$pr?>">&nbsp;</td>
		</tr>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>TOTAL PERCENT</b></td>
					
<?
for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start each date percent loop
		$allindate=number_format(100*$alldatetotal[$d]/$palltotal,2,".",",");
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
		echo "$allindate</b></td>";
}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=number_format(100*$alltotal/$palltotal,2,".",",")?>
			</b></td>
			
			<? } ?>
			<td bgcolor="#d3d3d3"></td>
		</tr>
<?}?>	
-->
<? */
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=$allcolumncnt+1?>" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
		</td>
	</tr>
	<tr height="50">
		<td colspan="<?=$allcolumncnt+1?>">&nbsp;</td>
	</tr>
 </table>
		</td>
	</tr>
</table>
<?$rowcnt=0;?>
<? }// end check export file ?>
<? if($export=="print"){ ?>
<script type="text/javascript">
	window.print();
</script>
<? } ?>

<? }else{?>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0">
			<tr>
		    	<td class="reporth" align="center" colspan="<?=($column=="Total only")?$rsdate["rows"]+1+$pr:$rsdate["rows"]+2+$pr ?>">
		    		<b><p>Spa Management System</p>
			    	<?=$reportname?></b><br/>
			    	<p><b style='color:#ff0000'>
			    	<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
			    	<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
			    	<br><br></p>
		    	</td>
			</tr>
				<tr height="32">
					<td style="text-align:left; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=0;$d<$rsdate["rows"];$d++){ ?>
						<td style="text-align:right;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"){?>
					<td style="text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;"> TOTAL </b></td>
					<? }?>
					<? if($percent){?>
						<td style="text-align:center;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">TOTAL<br>PERCENT</b></td>
					<? }?>
				</tr>
<?
if($collapse=="Collapse"){	//check Collapse/Expand loop
for($i=0; $i<$rscategory["rows"]; $i++) {		// start category loop		
if(!isset($totalcategory[$i])){$totalcategory[$i]=0;}
if($totalcategory[$i]>0){						// check if category total > 0
?>
<tr height="32">
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #000000 double;" bgcolor="#d3d3d3"><b>Category: <?=$rscategory[$i]["trm_category_name"]?></b></td>
	<td colspan="<?=($column=="Total only")?$rsdate["rows"]+$pr:$rsdate["rows"]+1+$pr?>" bgcolor="#d3d3d3" style="border-bottom:3px #000000 double;">&nbsp;</td><!-- input city -->
</tr>
<?
		for($k=0; $k<$rstrm["rows"]; $k++) { 	// start trm loop
if(!isset($rsdate["begin"][0])){$rsdate["begin"][0]="";}
if(!isset($rsdate["end"][$d-1])){$rsdate["end"][$d-1]="";}
if(!isset($rstrm[$k]["trm_id"])){$rstrm[$k]["trm_id"]=0;}
if(!isset($rsdate["branchid"][$d])){$rsdate["branchid"][$d]=0;}
if(!isset($rscategory[$i]["trm_category_id"])){$rscategory[$i]["trm_category_id"]=0;}
if(!isset($total[$k])){$total[$k]=0;}
			if($rstrm[$k]["trm_category_id"]==$rscategory[$i]["trm_category_id"]&&$total[$k]>0){
?>
						<tr height="22">
							<td style="padding-left:35px; white-space: nowrap;"><?=$rstrm[$k]["trm_name"]?></td>
							<? for($d=0;$d<$rsdate["rows"];$d++){ ?>		
								<td align="right">
									<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openinvDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rscategory[$i]["trm_category_id"].",".$rstrm[$k]["trm_id"].",'".$rsdate["branchid"][$d]."'"?>)"><? } ?>
									<?=$tmp[$k][$d]?>
									<?if($export==false){?></a><? } ?>
								</td>
								<? } ?>	
							<? if($column!="Total only"){ ?>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openinvDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rstrm[$k]["trm_id"].",'".$rsdate["branchid"][$d]."'"?>)"><?}?>
							<?=$total[$k]?>
							<?if($export==false){?></a><?}?>
							</td>
							<? } ?>
							<? if($percent){?>
							<td align="right">
							<?=number_format(100*$total[$k]/$palltotal,2,".",",")?>
							</td>
							<?} ?>
						</tr>
						<?	
						}
					} 
					?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right"><b>Total in <?=$rscategory[$i]["trm_category_name"]?></b></td>
			<? for($d=0;$d<$rsdate["rows"];$d++){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openinvDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rscategory[$i]["trm_category_id"].",0,'".$rsdate["branchid"][$d]."'"?>)"><?}?>
			<b><?= $totalcategorypd[$i][$d]?></b>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>	
			<? if($enddate!=$begindate&&$column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openinvDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rscategory[$i]["trm_category_id"].",0,'".$rsdate["branchid"][$d]."'"?>)"><?}?>
			<b><?=$totalcategory[$i]?></b>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			<? if($percent){?>
							<td align="right"><b>
							<?=number_format(100*$totalcategory[$i]/$palltotal,2,".",",")?>
							</b></td>
			<?} ?>
			
		</tr>
	<?	
			} 
		}
		
}	// End check collapse expand loop
else if($order!="Total")		// for colapse information report
{
		for($k=0; $k<$rstrm["rows"]; $k++) {// start branch total loop
if(!isset($rsdate["begin"][0])){$rsdate["begin"][0]="";}
if(!isset($rsdate["end"][$d-1])){$rsdate["end"][$d-1]="";}
if(!isset($rscategory[$i]["trm_category_id"])){$rscategory[$i]["trm_category_id"]=0;}
if(!isset($tmptrmtotal[$k])){$tmptrmtotal[$k]=0;}
if(!isset($rsdate["branchid"][$d])){$rsdate["branchid"][$d]=0;}
if(!isset($total[$k])){$total[$k]=0;}	
			if($total[$k]>0){
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$rstrm[$k]["trm_name"]?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openinvDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rstrm[$k]["trm_id"].",'".$rsdate["branchid"][$d]."'"?>)"><?}?>
			<?=$tmp[$k][$d]?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openinvDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rstrm[$k]["trm_id"].",'".$rsdate["branchid"][$d]."'"?>)"><?}?>
			<?=$total[$k]?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			
			<? if($percent){?>
							<td align="right">
							<?=number_format(100*$total[$k]/$palltotal,2,".",",")?>
							</td>
			<?} ?>
			
		</tr>
<?
			}
		}
}
else
{
		for($k=0; $k<$rstrm["rows"]; $k++) { // start nationality total loop for sort array of total in each rows
			for($d=0;$d<$rsdate["rows"];$d++){
				if($obj->getIdToText($rstrm[$k]["trm_id"],"db_trm","trm_id","trm_id","trm_id IN ( $alltrm ) and trm_active=1")>0){
					$trmtotal[$rstrm[$k]["trm_id"]]=$total[$k];
				}
			}
			if($sort=="A > Z"){arsort($trmtotal);}
			else{asort($trmtotal);}
		}
		//print_r($trmtotal);
		$total = array();
		$cnt=0;	// resorting branch id to new array for show in report
		foreach ($trmtotal as $key => $val) {
  			  $tmptrmtotal[$cnt] = $key;
  			  $total[$cnt] = $val;
  			  $cnt++;
		}
		//print_r($total);echo $column;
		for($k=0; $k<$cnt; $k++) {
if(!isset($rsdate["begin"][0])){$rsdate["begin"][0]="";}
if(!isset($rsdate["end"][$d-1])){$rsdate["end"][$d-1]="";}
if(!isset($rscategory[$i]["trm_category_id"])){$rscategory[$i]["trm_category_id"]=0;}
if(!isset($tmptrmtotal[$k])){$tmptrmtotal[$k]=0;}
if(!isset($rsdate["branchid"][$d])){$rsdate["branchid"][$d]=0;}			
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmptrmtotal[$k],"db_trm","trm_name","trm_id")?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openinvDetail('<?=$rsdate["begin"][$d]."','".$rsdate["end"][$d]."','".$rscategory[$i]["trm_category_id"]."','".$tmptrmtotal[$k]."','".$rsdate["branchid"][$d]."'"?>)"><?}?>
			<?	$tmp[$k][$d]=$obj->sumeachinventoryfield($rs,"total",$tmptrmtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d],$rsdate["branchid"][$d]); 
				echo $tmp[$k][$d];	?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openinvDetail('<?=$rsdate["begin"][0]."','".$rsdate["end"][$d-1]."','".$rscategory[$i]["trm_category_id"]."','".$tmptrmtotal[$k]."','".$rsdate["branchid"][$d]."'"?>)"><?}?>
			<?=$total[$k]?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			
			<? if($percent){?>
							<td align="right">
							<?=number_format(100*$total[$k]/$palltotal,2,".",",")?>
							</td>
			<?} ?>
			
		</tr>
<? } ?>
<? } ?>
<? /* ?>
<!--
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>TOTAL</b></td>
			
<?
for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
		if($export==false){
			echo "<a href=\"javascript:;\" style=\"text-decoration:none;\" onClick=\"openinvDetail(".$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0,'".$rsdate["branchid"][$d]."')\">";
			echo $alldatetotal[$d];
			echo "</a></b></td>\n";
		}else{
			echo $alldatetotal[$d];
			echo "</a></b></td>\n";
		}
}
?>
			<? if($column!="Total only"){ ?>
				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openinvDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0".",'".$rsdate["branchid"][$d]."'"?>)"><?}?>
			<?=$alltotal?>
			<?if($export==false){?></a><?}?></b></td>
			
			<? } ?>
			
			<? if($percent){?>
							<td align="right" bgcolor="#d3d3d3"><b>
							<?=number_format(100*$alltotal/$palltotal,2,".",",")?>
							</b></td>
			<?} ?>
			
			</tr>
<?if($percent){?>

		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>TOTAL PERCENT</b></td>
			
<?
for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
		$allindate=number_format(100*$alldatetotal[$d]/$palltotal,2,".",",");
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
		echo "$allindate</b></td>";
}
?>
			<? if($column!="Total only"){ ?>
			<td align="right" bgcolor="#d3d3d3"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none;" onClick="openinvDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0,".$rsdate["branchid"][$d-1].""?>)"><?}?>
			<?=number_format(100*$alltotal/$palltotal,2,".",",")?>
			<?if($export==false){?></a><?}?></b></td>
			
			<? } ?>
			
			<td align="right" bgcolor="#d3d3d3"></td>
			
    </tr>
<?}?>
-->
<? */
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
 ?>
		    <tr>
		    	<td align="center" colspan="<?=($column=="Total only")?$rsdate["rows"]+1:$rsdate["rows"]+2?>">
		    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
		    		<?='<p>SMS page generated in '.$total_time.' seconds.</p>'."\n";?>
		    	</td>
			</tr>
 		</table><br>
		</td>
    </tr>
</table>
<? } ?>