<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("csi.inc.php");
$obj = new csi();
$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate= $obj->getParameter("end");
$column= $obj->getParameter("column");
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");
$collapse = $obj->getParameter("Collapse");
$branch = $obj->getParameter("branchid",0);
$cityid = $obj->getParameter("cityid",false);
$percent = $obj->getParameter("percent");
$showall = $obj->getParameter("showall");
$today = date("Ymd");
$rsdate = $obj->getdatecol($column,$begindate,$enddate);
$rs = $obj->getrecreport($begindate,$enddate,$branch,false,$cityid);
$rsbranch = $obj->getbranch($order,$sort,$branch,false,$cityid);

$rsrec = $obj->getrec($order,$sort);

$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Recommendation Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
$rectotal = array();
$branchname=$obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
/*
if($branch==0 || strtolower($branchname)=="all"){
	$reportname = "All Branch Recommendation Report";
}else{
	$reportname = $branchname."'s Recommendation Report";
}*/
$reportname = "Recommendation Report";
$palltotal = 0;$alltotal = 0;
for($i=0; $i<$rsbranch["rows"]; $i++) {		// start branch loop		
	$totalbranch[$i]=0;
	for($k=0; $k<$rsrec["rows"]; $k++) {
			$total[$k][$i] = 0;
			for($d=0;$d<$rsdate["rows"];$d++){ // start rec total loop
					$tmp[$d][$k][$i]=$obj->sumeachfield($rs,"total",$rsbranch[$i]["branch_id"],$rsrec[$k]["rec_id"],$rsdate["begin"][$d],$rsdate["end"][$d]); 
					$total[$k][$i]+=$tmp[$d][$k][$i];
					$totalbranch[$i]+=$tmp[$d][$k][$i];
					$alltotal+=$tmp[$d][$k][$i];
					$palltotal+=$tmp[$d][$k][$i];
			}
	}
}
//=-----------------------------------------------------------------------------
for($d=0;$d<$rsdate["rows"];$d++){ 
		$alldatetotal[$d] = 0;
		$palldatetotal[$d] = 0;
		for($k=0; $k<$rsrec["rows"]; $k++) {
			for($i=0; $i<$rsbranch["rows"]; $i++) {		// start branch loop	
				$alldatetotal[$d]+=$tmp[$d][$k][$i];
				$palldatetotal[$d]+=$tmp[$d][$k][$i];
			}
		}
		if($palldatetotal[$d]==0){$palldatetotal[$d] = 1;}
}
if($palltotal==0){$palltotal=1;} //fix problem divice by zero
//=-----------------------------------------------------------------------------
//print_r($tmp);
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
	else{$chkrow = $obj->getParameter("chkrow",40);}
}
if($begindate==$enddate){$column="Total only";$rsdate["header"][0]="TOTAL";}
?>
<?
//Get All Branch
        $sql = "select branch_id, branch_name from bl_branchinfo where branch_id<>1 ";
        		if($cityid){$sql .= "and city_id=".$cityid." ";}else
        		if($branch){$sql .= "and branch_id=".$branch." ";}
        $sql.= "and branch_active=1 order by branch_name asc";
        $rsBranch = $obj->getResult($sql);
        

    			for($j=0; $j<$rsBranch["rows"]; $j++){
    				$nbranchdetail[$j] = $rsBranch[$j]["branch_name"];
    			}
    			if($nbranchdetail){
  	  				$NbranchSrdString = implode(", ", $nbranchdetail); 
  				}
?>
<?if($export!="Excel"){//?><script type="text/javascript" src="../scripts/ajax.js"></script><?}?>
<?if($export!="Excel"&&$export!="PDF"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
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
$header .= "\t\t\t\t\t".$dateobj->convertdate($begindate,$sdateformat,$ldateformat);
$header .= (($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat))."\n";
$header .= "\t\t\t\t\t</b></p>\n";
$header .= "\t\t\t\t\t<p><b style='color:#ff0000'>Branch : ".$NbranchSrdString."</b><br><br></p>\n";
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
if($collapse=="Collapse"){	//check Collapse/Expand loop

for($i=0; $i<$rsbranch["rows"]; $i++) {		// start city loop	
	
?>
<tr height="32"><?$rowcnt++;?>
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #000000 double;" bgcolor="#eaeaea"><b><?=$rsbranch[$i]["branch_name"]?> : </b></td>
	<td colspan="<?=$allcolumncnt?>" bgcolor="#eaeaea" style="border-bottom:3px #000000 double;">&nbsp;</td><!-- treatment category -->
</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?
for($k=0; $k<$rsrec["rows"]; $k++) { 	// start branch name loop
?>
						<tr height="22"><?$rowcnt++;?>
							<td style="padding-left:35px; white-space: nowrap;"><?=$obj->checkParameter($rsrec[$k]["rec_name"],"No Recommend")?></td>
							<? 	for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>		
								<td align="right"><?=$tmp[$d][$k][$i]?></td>
								<? } ?>	
							<? 
							   if($column!="Total only"&&$a==$alltable-1){ ?>
							<td align="right">
							<?=$total[$k][$i]?>
							</td>
							<? } ?>
							<? if($percent&&$a==$alltable-1){?>
								<td align="right">
								<?= number_format(100*$total[$k][$i]/$palltotal,2,".",",")?>
								</td>
							<?}?>
						</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?	} ?>
		<?  if($a==$alltable-1){ ?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=($column=="Total only")?$rsdate["rows"]:$datechk["rows"]?>"><b>Total in <?=$rsbranch[$i]["branch_name"]?></b></td>
			<td align="right">
			<b><?=$totalbranch[$i]?></b>
			</td>
			<? if($percent){ ?>
				<td align="right">
				<b><?=number_format(100*$totalbranch[$i]/$palltotal,2,".",",")?></b>
				</td>
			<? } ?>
		</tr>
		<?}?>
<?	
}

}	// End check collapse expand loop
else if($order!="Total")		// for colapse information report
{
		for($k=0; $k<$rsrec["rows"]; $k++) {
?>
		<tr height="22"><?$rowcnt++;?>
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->checkParameter($rsrec[$k]["rec_name"],"No Recommend")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?=array_sum($tmp[$d][$k])?>
			</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right">
			<?=array_sum($total[$k])?>
			</td>
			<? } ?>
			
			<? if($percent&&$a==$alltable-1){?>
			<td align="right">
			<?=number_format(100*array_sum($total[$k])/$palltotal,2,".",",")?>
			</td>
			<?}?>
		</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?			
		}
}
else
{
		if($a==0){
			for($k=0; $k<$rsrec["rows"]; $k++) { // start rec total loop for sort array of total in each rows
				for($d=0;$d<$rsdate["rows"];$d++){
						$rectotal[$rsrec[$k]["rec_id"]]=array_sum($total[$k]);
				}
				if($sort=="A > Z"){arsort($rectotal);}
				else{asort($rectotal);}
			}
			//print_r($rectotal);
			$total = array();
			$cnt=0;	// resorting branch id to new array for show in report
			foreach ($rectotal as $key => $val) {
	  			  $tmprectotal[$cnt] = $key;
	  			  $total[$cnt] = $val;
	  			  $cnt++;
			}
		}

		for($k=0; $k<$cnt; $k++) { 	
?>
		<tr height="22"><?$rowcnt++;?>
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->checkParameter($obj->getIdToText($tmprectotal[$k],"fl_csi_recommend","rec_name","rec_id"),"No Recommend")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?	$tmp[$d][$k]=$obj->sumeachfield($rs,"total",0,$tmprectotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]); 
				echo $tmp[$d][$k];?>
			</td>
			<? } ?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right">
			<?=$total[$k]?>
			</td>
			<? } ?>
			
			<? if($percent&&$a==$alltable-1){?>
			<td align="right">
			<?=number_format(100*$total[$k][$i]/$palltotal,2,".",",")?>
			</td>
			<?}?>
		</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?	}
}
?>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#eaeaea"><b>TOTAL</b></td>
			
<?
for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start branch total loop
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#eaeaea\"><b>";
		echo $alldatetotal[$d];
		echo "</a></b></td>\n";
}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
				
			<td align="right" bgcolor="#eaeaea"><b>
			<?=$alltotal?>
			</b></td>
			
			<? } ?>
			
			<? if($percent&&$a==$alltable-1){ ?>
				
			<td align="right" bgcolor="#eaeaea"><b>
			<?=number_format(100*$alltotal/$palltotal,2,".",",")?>
			</b></td>
			
			<? } ?>
    </tr>
<?/*if($percent){?>
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
<?}*/?>	
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
		    	<td class="reporth" align="center" colspan="<?=($column=="Total only")?$rsdate["rows"]+1+$pr:$rsdate["rows"]+2+$pr ?>">
		    		<b><p>Spa Management System</p>
			    	<?=$reportname?></b><br/>
			    	<p><b style='color:#ff0000'>
			    	<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
			    	<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
			    	</b></p>
		    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
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
	
for($i=0; $i<$rsbranch["rows"]; $i++) {		// start city loop		
?>
<tr height="32">
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #000000 double;" bgcolor="#d3d3d3"><b><?=$rsbranch[$i]["branch_name"]?></b></td>
	<td colspan="<?=($column=="Total only")?$rsdate["rows"]+$pr:$rsdate["rows"]+1+$pr?>" bgcolor="#d3d3d3" style="border-bottom:3px #000000 double;">&nbsp;</td><!-- input city -->
</tr>
<?
for($k=0; $k<$rsrec["rows"]; $k++) { 	// start branch name loop
?>
						<tr height="22">
							<td style="padding-left:35px; white-space: nowrap;"><?=$obj->checkParameter($rsrec[$k]["rec_name"],"No Recommend")?></td>
							<? for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop ?>		
								<td align="right">
									<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openrecDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$i]["branch_id"].",".$rsrec[$k]["rec_id"]?>)"><? } ?>
									<?=$tmp[$d][$k][$i]?>
									<?if($export==false){?></a><? } ?>
								</td>
								<? } ?>	
							<? 
							   if($column!="Total only"){ ?>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openrecDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$i]["branch_id"].",".$rsrec[$k]["rec_id"]?>)"><?}?>
							<?=$total[$k][$i]?>
							<?if($export==false){?></a><?}?>
							</td>
							<? } ?>
							<? if($percent){?>
							<td align="right">
							<?=number_format(100*$total[$k][$i]/$palltotal,2,".",",")?>
							</td>
							<?} ?>
						</tr>
						<?	
					} 
					?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=($column=="Total only")?$rsdate["rows"]:$rsdate["rows"]+1?>"><b>Total in <?=$rsbranch[$i]["branch_name"]?></b></td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrecDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$i]["branch_id"].",0"?>)"><?}?>
			<?= $totalbranch[$i]?>
			<?if($export==false){?></a><?}?>
			</td>
			
			<? if($percent){?>
							<td align="right"><b>
							<?=number_format(100*$totalbranch[$i]/$palltotal,2,".",",")?>
							</b></td>
			<?} ?>
			
		</tr>
<?	
}

}	// End check collapse expand loop
else if($order!="Total")		// for colapse information report
{
		for($k=0; $k<$rsrec["rows"]; $k++) {// start branch total loop
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->checkParameter($rsrec[$k]["rec_name"],"No Recommend")?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openrecDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",$branch,".$rsrec[$k]["rec_id"]?>)"><?}?>
			<?=array_sum($tmp[$d][$k])?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openrecDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",$branch,".$rsrec[$k]["rec_id"]?>)"><?}?>
			<?=array_sum($total[$k])?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			
			<? if($percent){?>
							<td align="right">
							<?=number_format(100*array_sum($total[$k])/$palltotal,2,".",",")?>
							</td>
			<?} ?>
			
		</tr>
<?
		}
}
else
{
		for($k=0; $k<$rsrec["rows"]; $k++) { // start recommend total loop for sort array of total in each rows
			for($d=0;$d<$rsdate["rows"];$d++){
					$rectotal[$rsrec[$k]["rec_id"]]=array_sum($total[$k]);
			}
			if($sort=="A > Z"){arsort($rectotal);}
			else{asort($rectotal);}
		}
		//print_r($rectotal);
		$total = array();
		$cnt=0;	// resorting branch id to new array for show in report
		foreach ($rectotal as $key => $val) {
  			  $tmprectotal[$cnt] = $key;
  			  $total[$cnt] = $val;
  			  $cnt++;
		}
		//print_r($tmprectotal);echo "<br>";
		//print_r($total);echo $column;
		for($k=0; $k<$cnt; $k++) { 
			
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->checkParameter($obj->getIdToText($tmprectotal[$k],"fl_csi_recommend","rec_name","rec_id"),"No Recommend")?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openrecDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",$branch,".$tmprectotal[$k]?>)"><?}?>
			<?	$tmp[$d][$k]=$obj->sumeachfield($rs,"total",0,$tmprectotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]); 
				echo $tmp[$d][$k];	?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openrecDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",$branch,".$tmprectotal[$k]?>)"><?}?>
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
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>TOTAL</b></td>
			
<?
for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
		if($export==false){
			echo "<a href=\"javascript:;\" style=\"text-decoration:none;  color:#000000;\" onClick=\"openrecDetail(".$rsdate["begin"][$d].",".$rsdate["end"][$d].",$branch,0)\">";
			echo $alldatetotal[$d];
			echo "</a></b></td>\n";
		}else{
			echo $alldatetotal[$d];
			echo "</b></td>\n";
		}
}
?>
			<? if($column!="Total only"){ ?>
				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrecDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",$branch,0"?>)"><?}?>
			<?=$alltotal?>
			<?if($export==false){?></a><?}?></b></td>
			
			<? } ?>
			
			<? if($percent){?>
							<td align="right" bgcolor="#d3d3d3"><b>
							<?=number_format(100*$alltotal/$palltotal,2,".",",")?>
							</b></td>
			<?} ?>
			
			</tr>
<?/*if($percent){?>

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
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrecDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",$branch,0"?>)"><?}?>
			<?=number_format(100*$alltotal/$palltotal,2,".",",")?>
			<?if($export==false){?></a><?}?></b></td>
			
			<? } ?>
			
			<td align="right" bgcolor="#d3d3d3"></td>
			
    </tr>
<?}*/?>
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