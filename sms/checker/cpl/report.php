<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
$obj = new checker();


$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$column= $obj->getParameter("column");
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby","A &gt Z");
$collapse = $obj->getParameter("Collapse");
$branchtotal = array();
$rsdate = $obj->getdatecol($column,$begin_date,$end_date);
$rs = $obj->getcpl(0,$begin_date,$end_date);
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
$export = $obj->getParameter("export",false);
if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Customers Per Location.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print");
}
$allbranchtype = implode(',',$branchtype);
$allbranch = implode(',',$branch);
$alltotal=0;
$alldatetotal=array();
for($i=0; $i<$rscity["rows"]; $i++) {		// start city loop		
	if($obj->getIdToText($rscity[$i]["city_id"],"bl_branchinfo","branch_id","city_id","branch_category_id IN ( $allbranchtype ) and branch_active=1")>0){
		$allltotal[$i]=0;
		for($j=0; $j<$rsbranchtype["rows"]; $j++) {	
			if($obj->getIdToText($rsbranchtype[$j]["branch_category_id"],"bl_branchinfo","branch_id","branch_category_id"," branch_id IN ( $allbranch ) and branch_active=1 and city_id=".$rscity[$i]["city_id"])>0){
				for($k=0; $k<$rsbranch["rows"]; $k++) {
					if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
							$total[$k] = 0;
							for($d=0;$d<$rsdate["rows"];$d++){ // start date total loop
									$tmp[$k][$d]=$obj->sumeachfield($rs,"qty",$rsbranch[$k]["branch_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
									$total[$k]+=$tmp[$k][$d];
							}
					}
				}
			}
			$allbttotal[$i][$j]=0;
			for($d=0;$d<$rsdate["rows"];$d++){// start date total loop
					$bttotal[$i][$j][$d]=0;
					if(!isset($alldatetotal[$d])){$alldatetotal[$d]=0;}
					for($k=0; $k<$rsbranch["rows"]; $k++) { 
						if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
							$bttotal[$i][$j][$d] += $tmp[$k][$d];
							$allbttotal[$i][$j] += $tmp[$k][$d];
							$alldatetotal[$d] += $tmp[$k][$d];
							$alltotal +=  $tmp[$k][$d];
						}
					}
			}
			$allltotal[$i] += $allbttotal[$i][$j];
		}
	}
}
$percent = true;
if($percent){
	$palltotal = $alltotal;
	if($alltotal==0){$palltotal=1;}
}
if($export!="Excel"&&$export){
	$chkcolumn=7;		// row column per page
	$alltable=ceil($rsdate["rows"]/$chkcolumn);
	if($column=="Total only"){$alltable=1;}
	$rowcnt=0;
	if($collapse=="Collapse"){$chkrow = $obj->getParameter("chkrow",27);}
	else{$chkrow = $obj->getParameter("chkrow",40);}
}
if($percent){$pr=1;}else{$pr=0;}
if($begin_date==$end_date){$column="Total only";$rsdate["header"][0]="TOTAL";}	// if begindate = enddate

$reportname = "Customers Per Location";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
?>
<?if($export!="Excel"&&$export!="PDF"){?><script type="text/javascript" src="../scripts/ajax.js"></script><?}?>
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
	
for($i=0; $i<$rscity["rows"]; $i++) {		// start city loop		
$allbranchtype = implode(",",$branchtype);
if($obj->getIdToText($rscity[$i]["city_id"],"bl_branchinfo","branch_id","city_id","branch_category_id IN ( $allbranchtype ) and branch_active=1")>0){
$rowcnt++;
if($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=$allcolumncnt+1?>" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
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
<?	} ?>
<tr height="32">
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #000000 double;" bgcolor="#d3d3d3"><b>Location: <?=$rscity[$i]["city_name"]?></b></td>
	<td colspan="<?=$allcolumncnt?>" style="padding-left:7px; white-space: nowrap; border-bottom:3px #000000 double;" bgcolor="#d3d3d3">&nbsp;</td><!-- input city -->
</tr>
<?
		for($j=0; $j<$rsbranchtype["rows"]; $j++) {		// start branch category loop
			$allbranch = implode(",",$branch);
			if($obj->getIdToText($rsbranchtype[$j]["branch_category_id"],"bl_branchinfo","branch_id","branch_category_id"," branch_id IN ( $allbranch ) and branch_active=1 and city_id=".$rscity[$i]["city_id"])>0){
$rowcnt++;
if($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=$allcolumncnt+1?>" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
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
<?	} ?>
			<tr height="28">
				<td style="padding-left: 20px; white-space: nowrap;"><b>Category: <?=$rsbranchtype[$j]["branch_category_name"]?></b></td>
				<td colspan="<?=$allcolumncnt?>" style="padding-left: 20px;">&nbsp;</td>
			</tr>
					<?
					for($k=0; $k<$rsbranch["rows"]; $k++) { 	// start branch name loop
						if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
$rowcnt++;
if($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=$allcolumncnt+1?>" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
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
<?	} ?>
						<tr height="22">
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
							<? 	//$total[$k] = 0;
								for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start branch total loop
							?>		
								<td align="right">
									<?=number_format($tmp[$k][$d],0,".",",")?>
								</td>
								<? } ?>	
								
							<? if($column!="Total only"&&$a==$alltable-1){ ?>
							<td align="right">
							<?=$total[$k]?>
							</td>
							<? } ?>
							
							<?if($percent&&$a==$alltable-1){?>
										<td align="right">
										<?=number_format(100*$total[$k]/$palltotal,2,".",",")?>
										</td>
							<?}?>
							
						</tr>
						<?	
						}
					} 
$rowcnt++;
if($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=$allcolumncnt+1?>" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
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
<?	} ?>
			<tr height="28">
				<td style="padding-left: 10px; white-space: nowrap;border-top:1px #000000 solid;"><b>Total in  <?=$rsbranchtype[$j]["branch_category_name"]?> Category: </b></td>
				<? 
				for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){// start date total loop
				?>		
				<td align="right" style="border-top:1px #000000 solid;">
				<?=number_format($bttotal[$i][$j][$d],0,".",",")?>
				</td>
				<? }  ?>	
				
				<? if($column!="Total only"&&$a==$alltable-1){ ?>
				<td align="right" style="border-top:1px #000000 solid;">
				<?=number_format($allbttotal[$i][$j],0,".",",")?>
				</td>
				<? } ?>
				
				<?if($percent&&$a==$alltable-1){?>
						<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format(100*$allbttotal[$i][$j]/$palltotal,2,".",",")?>
						</td>
				<?}?>
			</tr>
		<?	 	
			} 
		}
		?>
		<?  if($a==$alltable-1){ ?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=$allcolumncnt-1?>"><b>Total in <?=$rscity[$i]["city_name"]?></b></td>
			<td align="right">
			<b><?=$allltotal[$i]?></b>
			</td>
			<?if($percent){?>
							<td align="right">
							<b><?=number_format(100*$allltotal[$i]/$palltotal,2,".",",")?></b>
							</td>
			<?}?>
		</tr>
		<?}?>
<?	
	} 
}

}	// End check collapse expand loop
else if($order!="Total")		// for colapse information report
{
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop
$rowcnt++;
if($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=$allcolumncnt+1?>" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
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
<?	} ?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?=number_format($tmp[$k][$d],0,".",",")?>
			</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right">
			<?=number_format($total[$k],0,".",",")?>
			</td>
			<? } ?>
			
			<?if($percent&&$a==$alltable-1){?>
						<td align="right">
						<?=number_format(100*$total[$k]/$palltotal,2,".",",")?>
						</td>
			<?}?>
		</tr>
<?
	}
}
else
{
	if($a==0){
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop for sort array of total in each branch
			$branchtotal[$rsbranch[$k]["branch_id"]]=$total[$k];

			if($sort=="A > Z"){arsort($branchtotal);}
			else{asort($branchtotal);}
			
		}
		$k=0;	// resorting branch id to new array for show in report
		foreach ($branchtotal as $key => $val) {
  			  $tmpbranchtotal[$k] = $key;
  			  $total[$k] = $val;
  			  $k++;
		}
	}
		for($k=0; $k<$rsbranch["rows"]; $k++) {
$rowcnt++;
if($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=$allcolumncnt+1?>" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
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
<?	} ?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpbranchtotal[$k],"bl_branchinfo","branch_name","branch_id")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?	$tmp[$k][$d]=$obj->sumeachfield($rs,"qty",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]); 
			echo number_format($tmp[$k][$d],0,".",","); 	?>
			</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right">
			<?=number_format($total[$k],0,".",",")?>
			</td>
			<? } ?>
					
			<?if($percent&&$a==$alltable-1){?>
					<td align="right">
					<?=number_format(100*$total[$k]/$palltotal,2,".",",")?>
					</td>
			<?}?>
		</tr>
<?
	}
	
}
?>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>TOTAL</b></td>
			
<?
for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start branch total loop
		$allindate = $alldatetotal[$d];
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
		echo $allindate."</a></b></td>";
}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=number_format($alltotal,0,".",",")?>
			</b></td>
			<? } ?>
			<?if($percent&&$a==$alltable-1){?>
										<td align="right" bgcolor="#d3d3d3"><b>
										<?=number_format(100*$alltotal/$palltotal,2,".",",")?>
										</b></td>
			<?}?>
			
	</tr>	
<!--
<?/*if($percent){?>
		<tr height="20">
			<td colspan="<?=($column=="Total only")?$rsdate["rows"]+1+$pr:$rsdate["rows"]+2+$pr?>">&nbsp;</td>
		</tr>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>PERCENT</b></td>
			
			<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start branch total loop
					$allindate = number_format(100*$alldatetotal[$d]/$palltotal,2,".",",");
					echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
					echo $allindate."</b></td>\n";
			}
			?>
			<? if($column!="Total only"){ ?>
						<td align="right" bgcolor="#d3d3d3"><b>
						<?=number_format(100*$alltotal/$palltotal,2,".",",")?></b></td>
			<? } ?>
			<td bgcolor="#d3d3d3"></td>
		</tr>
<?}*/?>	
-->
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
	<? }// end check export file ?>	
<? }else{ ?>
	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	
	<tr>
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0">
			<tr>
		    	<td class="reporth" align="center" colspan="<?=($column=="Total only")?$rsdate["rows"]+1+$pr:$rsdate["rows"]+2+$pr ?>">
		    		<b><p>Spa Management System</p>
		    		<?=$reportname?></b><br>
		    		<p><b style='color:#ff0000'>
		    		<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
		    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
		    		<br><br></b></p>
		    	</td>
			</tr>
				<tr height="32">
					<td style="text-align:left; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=0;$d<$rsdate["rows"];$d++){ ?>
						<td style="text-align:right;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"){?>
					<td style="text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">TOTAL</b></td>
					<? }?>
					<? if($percent){?>
					<td style="text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">PERCENT</b></td>
					<? }?>
				</tr>
<?
if($collapse=="Collapse"){	//check Collapse/Expand loop

for($i=0; $i<$rscity["rows"]; $i++) {		// start city loop		
$allbranchtype = implode(",",$branchtype);
if($obj->getIdToText($rscity[$i]["city_id"],"bl_branchinfo","branch_id","city_id","branch_category_id IN ( $allbranchtype ) and branch_active=1")>0){
?>
<tr height="32">
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #000000 double;" bgcolor="#d3d3d3"><b>Location: <?=$rscity[$i]["city_name"]?></b></td>
	<td colspan="<?=($column=="Total only")?$rsdate["rows"]+$pr:$rsdate["rows"]+1+$pr?>" bgcolor="#d3d3d3" style="border-bottom:3px #000000 double;">&nbsp;</td><!-- input city -->
</tr>
<?	
		for($j=0; $j<$rsbranchtype["rows"]; $j++) {		// start branch category loop
			$allbranch = implode(",",$branch);
			if($obj->getIdToText($rsbranchtype[$j]["branch_category_id"],"bl_branchinfo","branch_id","branch_category_id"," branch_id IN ( $allbranch ) and branch_active=1 and city_id=".$rscity[$i]["city_id"])>0){
			?>
			<tr height="28">
				<td style="padding-left: 20px; white-space: nowrap;"><b>Category: <?=$rsbranchtype[$j]["branch_category_name"]?></b></td>
				<td colspan="<?=($column=="Total only")?$rsdate["rows"]+$pr:$rsdate["rows"]+1+$pr?>" style="padding-left: 20px;">&nbsp;</td>
			</tr>
					<?
					for($k=0; $k<$rsbranch["rows"]; $k++) { 	// start branch name loop
						if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
						?>
						<tr height="22">
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
							<? for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop ?>		
								<td align="right">
									<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><? } ?>
									<?=number_format($tmp[$k][$d],0,".",",")?>
									<?if($export==false){?></a><? } ?>
								</td>
								<? } ?>	
							<? if($column!="Total only"){ ?>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
							<?=number_format($total[$k],0,".",",")?>
							<?if($export==false){?></a><?}?>
							</td>
							<? } ?>
							
							<?if($percent){?>
											<td align="right">
											<?=number_format(100*$total[$k]/$palltotal,2,".",",")?>
											</td>
							<?}?>
							
						</tr>
						<?	
						}
					} 
					?>
			<tr height="28">
				<td style="padding-left: 10px; white-space: nowrap;border-top:1px #000000 solid;"><b>Total in  <?=$rsbranchtype[$j]["branch_category_name"]?> Category: </b></td>
				<? for($d=0;$d<$rsdate["rows"];$d++){// start date total loop ?>		
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
				<?=number_format($bttotal[$i][$j][$d],0,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<? }  ?>	
				<? if($column!="Total only"){ ?>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
				<?=number_format($allbttotal[$i][$j],0,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<? } ?>
				
				
				<?if($percent){?>
						<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format(100*$allbttotal[$i][$j]/$palltotal,2,".",",")?>
						</td>
				<?}?>
									
			</tr>
		<?	} 
		}
		?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=($column=="Total only")?$rsdate["rows"]:$rsdate["rows"]+1?>"><b>Total in <?=$rscity[$i]["city_name"]?></b></td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",0"?>)" class="pagelink"><?}?>
			<b><?=number_format($allltotal[$i],0,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>
			
			<?if($percent){?>
					<td align="right"><b>
					<?=number_format(100*$allltotal[$i]/$palltotal,2,".",",")?>
					</b></td>
			<?}?>
		</tr>
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
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($tmp[$k][$d],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>

			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($total[$k],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			

			<?if($percent){?>
					<td align="right">
					<?=number_format(100*$total[$k]/$palltotal,2,".",",")?>
					</td>
			<?}?>
		</tr>
<?
	}
}
else
{
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop for sort array of total in each branch
			$branchtotal[$rsbranch[$k]["branch_id"]]=$total[$k];
			if($sort=="A > Z"){arsort($branchtotal);}
			else{asort($branchtotal);}
		}
		$k=0;	// resorting branch id to new array for show in report
		foreach ($branchtotal as $key => $val) {
  			  $tmpbranchtotal[$k] = $key;
  			  $total[$k] = $val;
  			  $k++;
		}
		
		for($k=0; $k<$rsbranch["rows"]; $k++) {
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpbranchtotal[$k],"bl_branchinfo","branch_name","branch_id")?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	$tmp[$k][$d]=$obj->sumeachfield($rs,"qty",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]); 
			echo number_format($tmp[$k][$d],0,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($total[$k],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			
			<?if($percent){?>
					<td align="right">
					<?=number_format(100*$total[$k]/$palltotal,2,".",",")?>
					</td>
			<?}?>
			
		</tr>
<?
	}
	
}
?>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>TOTAL</b></td>
			
<?
for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
		$allindate = $alldatetotal[$d];
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
		if($export==false){echo "<a href=\"javascript:;\" style=\"text-decoration:none; color:#000000;\" onClick=\"openDetail(".$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0,0)\" class=\"pagelink\"><b>".number_format($allindate,0,".",",")."</b></a></b></td>\n";}
		else{echo number_format($allindate,0,".",",")."</a></b></td>\n";}
}
?>
			<? if($column!="Total only"){ ?>				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0,0"?>)" class="pagelink"><?}?>
			<?=number_format($alltotal,0,".",",")?><?if($export==false){?></a><?}?>
			</b></td>
			<? } ?>
			
			<?if($percent){?>
					<td align="right" bgcolor="#d3d3d3"><b>
					<?=number_format(100*$alltotal/$palltotal,2,".",",")?>
					</b></td>
			<?}?>
			
			</tr>
		
<?/*if($percent){?>
		<tr height="20">
			<td colspan="<?=($column=="Total only")?$rsdate["rows"]+1+$pr:$rsdate["rows"]+2+$pr?>">&nbsp;</td>
		</tr>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>PERCENT</b></td>
			
			<?
			for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
					$allindate = number_format(100*$alldatetotal[$d]/$palltotal,2,".",",");
					echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
					echo $allindate."</b></td>\n";
			}
			?>
			<? if($column!="Total only"){ ?>
						<td align="right" bgcolor="#d3d3d3"><b>
						<?=number_format(100*$alltotal/$palltotal,2,".",",")?></b></td>
			<? } ?>
			<td bgcolor="#d3d3d3"></td>
		</tr>
<?}*/?>	
		    <tr>
		    	<td align="center" colspan="<?=($column=="Total only")?$rsdate["rows"]+1+$pr:$rsdate["rows"]+2+$pr?>">
		    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
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