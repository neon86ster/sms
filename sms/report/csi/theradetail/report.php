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
$showall = $obj->getParameter("showall");
$today = date("Ymd");

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
  				
$branchname=$obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
/*
if($branch==0 || strtolower($branchname)=="all"){
	$reportname = "Therapist Detail CSI Report";
}else{
	$reportname = $branchname."'s Therapist Detail CSI Report";
}*/
$reportname = "Therapist Detail CSI Report";
$rsdate = $obj->getdatecol($column,$begindate,$enddate);
$rs = $obj->getthmsgcsi($begindate,$enddate,$branch,false,$cityid);
$rsbranch = $obj->getbranch($order,$sort);
$branch=array();

$rsth = $obj->gettherapist($order,$sort,$branch);
$th = array();

if($showall==false){
	for($j=0; $j<$rs["rows"]; $j++){
		if(!isset($rs[$j-1]["emp_id"])){$rs[$j-1]["emp_id"]=0;}
		if(!isset($rs[$j-1]["branch_id"])){$rs[$j-1]["branch_id"]=0;}
		if($rs[$j]["emp_id"]!=$rs[$j-1]["emp_id"]){
			$th[$j]=$rs[$j]["emp_id"];
		}
		if($rs[$j]["branch_id"]!=$rs[$j-1]["branch_id"]){
			$branch[$j]=$rs[$j]["branch_id"];
		}
	}
}
$allbranch = implode(",",$branch);
$allth = implode(",",$th);

$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Therapist Detail CSI Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}

if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
if($export!="Excel"&&$export){
	$chkcolumn=4;		// row column per page
	$alltable=ceil($rsdate["rows"]/$chkcolumn);
	if($column=="Total only"){$alltable=1;}
	$rowcnt=0;
	if($collapse=="Collapse"){$chkrow = $obj->getParameter("chkrow",32);}
	else{$chkrow = $obj->getParameter("chkrow",35);}
}
$thtotal = array();

$allcstotal = 0; $allcsitotal = 0;
for($i=0; $i<$rsbranch["rows"]; $i++) {		// start branch loop		
	if($obj->getIdToText($rsbranch[$i]["branch_id"],"l_employee","emp_id","branch_id","branch_id IN ( $allbranch ) and emp_active=1")>0){
		$totalcsbranch[$i]=0; $totalcsibranch[$i]=0;
		for($k=0; $k<$rsth["rows"]; $k++) {
			if($rsth[$k]["branch_id"]==$rsbranch[$i]["branch_id"]&&
				$obj->getIdToText($rsth[$k]["emp_id"],"l_employee","emp_id","emp_id","emp_id IN ( $allth ) and emp_active=1")>0){
				$totalcs[$k] = 0; $totalcsi[$k] = 0;
				for($d=0;$d<$rsdate["rows"];$d++){ // start trm total loop
					$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$rsth[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
					$csitmp[$k][$d]=$obj->sumeachempfield($rs,"totalcsi",$rsth[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
					$ttcsicnt = ($obj->sumeachempfield($rs,"total",$rsth[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]))?$obj->sumeachempfield($rs,"total",$rsth[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]):1;
					$ptmp[$k][$d]=$csitmp[$k][$d]/$ttcsicnt;
					$totalcs[$k]+=$tmp[$k][$d];
					$totalcsbranch[$i]+=$tmp[$k][$d];
					$allcstotal+=$tmp[$k][$d];
					$totalcsi[$k]+=$obj->sumeachempfield($rs,"totalcsi",$rsth[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
					$totalcsibranch[$i]+=$obj->sumeachempfield($rs,"totalcsi",$rsth[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
					$allcsitotal+=$obj->sumeachempfield($rs,"totalcsi",$rsth[$k]["emp_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
				}
			}
		}
	}
}
//=-----------------------------------------------------------------------------
for($d=0;$d<$rsdate["rows"];$d++){ 
		$csdatetotal[$d] = 0;
		$csidatetotal[$d] = 0;
		for($k=0; $k<$rsth["rows"]; $k++) {
				if($obj->getIdToText($rsth[$k]["emp_id"],"l_employee","emp_id","emp_id","emp_id IN ( $allth ) and emp_active=1")>0){
						$csdatetotal[$d]+=$tmp[$k][$d];
						$csidatetotal[$d]+=$csitmp[$k][$d];
					}
		}
}
//=-----------------------------------------------------------------------------

if($begindate==$enddate){$column="Total only";$rsdate["header"][0]="TOTAL";}
?>

<?if($export!="Excel"){?><script type="text/javascript" src="../scripts/ajax.js"></script><?}?>
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
	$allcolumncnt = 2*($datechk["end"][$a]-$datechk["begin"][$a]+1);
	if($column!="Total only"&&$a==$alltable-1){
		$allcolumncnt+=2;
	}
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
	$header .= "\t\t\t\t\t<td width=\"$columnwidth%\"></td>\n";
}
if($column!="Total only"&&$a==$alltable-1){
	$header .= "\t\t\t\t\t<td width=\"$columnwidth%\"></td>\n";
	$header .= "\t\t\t\t\t<td width=\"$columnwidth%\"></td>\n";
}
if(!isset($percent)){$percent=0;}
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
$header .= "\t\t\t\t\t<td width=\"90\" style=\"text-align:left; border-top:2px #000000 solid;\"><b>&nbsp;</b></td>\n";		
for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
	$header .= "\t\t\t\t\t<td width=\"40\" colspan=\"2\" style=\"text-align:right;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;\"><b style=\"text-decoration: underline;\">".$rsdate["header"][$d]."</b></td>\n";				
}
if($column!="Total only"&&$a==$alltable-1){
	$header .= "\t\t\t\t\t<td width=\"40\" colspan=\"2\" style=\"text-align:center;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;\"><b style=\"text-decoration: underline;\">TOTAL</b></td>\n";
}	
$header .= "\t\t\t\t</tr>\n";
$header .= "\t\t\t\t<tr height=\"35\">\n";	    	
$header .= "\t\t\t\t\t<td width=\"90\" style=\"text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>&nbsp;</b></td>\n";		
for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
	$header .= "\t\t\t\t\t<td width=\"40\" style=\"text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>% CSI</b></td>\n";	
	$header .= "\t\t\t\t\t<td width=\"40\" style=\"text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Cust.</b></td>\n";							
}
if($column!="Total only"&&$a==$alltable-1){
	$header .= "\t\t\t\t\t<td width=\"40\" style=\"text-align:right;padding-left:10px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>% CSI</b></td>\n";
	$header .= "\t\t\t\t\t<td width=\"40\" style=\"text-align:right;padding-left:10px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Cust.</b></td>\n";
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
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
					<? }?>
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
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" colspan="2" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" colspan="2" style="text-align:center;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;"><b style="text-decoration: underline;">TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>% CSI</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Cust.</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>% CSI</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Cust.</b></td>
					<? }?>
				</tr>
<?
if($collapse=="Collapse"){	//check Collapse/Expand loop

for($i=0; $i<$rsbranch["rows"]; $i++) {		// start city loop	
if($totalcsbranch[$i]>0){	
?>
<tr height="32"><?$rowcnt++;?>
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #d0d0d0 double;" bgcolor="#eaeaea"><b>Branch: <?=$rsbranch[$i]["branch_name"]?> </b></td>
	<td colspan="<?=$allcolumncnt?>" bgcolor="#eaeaea" style="border-bottom:3px #d0d0d0 double;">&nbsp;</td><!-- treatment category -->
</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?
for($k=0; $k<$rsth["rows"]; $k++) { 	// start branch name loop
if(!isset($totalcs[$k])){$totalcs[$k]=0;}
		if($rsth[$k]["branch_id"]==$rsbranch[$i]["branch_id"]&&$totalcs[$k]>0){
?>
						<tr height="22"><?$rowcnt++;?>
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsth[$k]["emp_code"]." ".$rsth[$k]["emp_nickname"]?></td>
							<? 	for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>		
								<td align="right"><?=number_format($ptmp[$k][$d],2,".",",")?></td>
								<td align="right"><?=$tmp[$k][$d]?></td>
								<? } ?>	
							<? if($column!="Total only"&&$a==$alltable-1){ ?>
								<td align="right">
								<?=number_format($totalcsi[$k]/$totalcs[$k],2,".",",")?>
								</td>
								<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;
								<?=$totalcs[$k]?>
								</td>
							<? } ?>
						</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?	
		}
} 
?>
		<?  if($a==$alltable-1){ ?>
		<tr height="35"><?$rowcnt++;?>
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=$allcolumncnt-1?>"><b>Total in <?=$rsbranch[$i]["branch_name"]?></b></td>
			<td align="right">
			<b><?=number_format($totalcsibranch[$i]/$totalcsbranch[$i],2,".",",")?></b>
			</td>
			<td align="right">
			<b><?=$totalcsbranch[$i]?></b>
			</td>
		</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
		<?}?>
<?	
	} 
}

}	// End check collapse expand loop
else if($order!="Total Customer"&&$order!="Total CSI")		// for colapse information report
{
		for($k=0; $k<$rsth["rows"]; $k++) {
		if(!isset($totalcs[$k])){$totalcs[$k]=0;}
if($totalcs[$k]>0){
?>
		<tr height="22"><?$rowcnt++;?>
			<td style="padding-left:35px; white-space: nowrap;"><?=$rsth[$k]["emp_code"]." ".$rsth[$k]["emp_nickname"]?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?=number_format($ptmp[$k][$d],2,".",",")?>
			</td>
			<td align="right">
			<?=$tmp[$k][$d]?>
			</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right">
			<?=number_format($totalcsi[$k]/$totalcs[$k],2,".",",");?>
			</td>
			<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;
			<?=$totalcs[$k]?>
			</td>
			<? } ?>
		</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?			}
		}
}
else if($order=="Total Customer")
{
		for($k=0; $k<$rsth["rows"]; $k++) {
			for($d=0;$d<$rsdate["rows"];$d++){
				if($obj->getIdToText($rsth[$k]["emp_id"],"l_employee","emp_id","emp_id","emp_id IN ( $allth ) and emp_active=1")>0){
					$thtotal[$rsth[$k]["emp_id"]]["cs"]=$totalcs[$k];
					$thtotal[$rsth[$k]["emp_id"]]["csi"]=$totalcsi[$k];
				}
			}
			if($sort=="A > Z"){arsort($thtotal);}
			else{asort($thtotal);}
		}
		
		//print_r($thtotal);
		$total = array();
		$cnt=0;	// resorting branch id to new array for show in report
		foreach ($thtotal as $key => $val) {
  			  $tmpthtotal[$cnt] = $key;
  			  $totalcs[$cnt] = $val["cs"];
  			  $totalcsi[$cnt] = $val["csi"];
  			  $cnt++;
		}

		for($k=0; $k<$cnt; $k++) { 	
?>
		<tr height="22"><?$rowcnt++;?>
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpthtotal[$k],"l_employee","emp_code","emp_id")." ".$obj->getIdToText($tmpthtotal[$k],"l_employee","emp_nickname","emp_id")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?	
			$ttcsicnt = ($obj->sumeachempfield($rs,"total",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]))?$obj->sumeachempfield($rs,"total",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]):1;
			$ptmp[$k][$d]=$obj->sumeachempfield($rs,"totalcsi",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d])/$ttcsicnt;
			echo number_format($ptmp[$k][$d],2,".",",");?>
			</td>
			<td align="right">
			<?	$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
			echo $tmp[$k][$d];?>
			</td>
			<? } ?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right">
			<?=number_format($totalcsi[$k]/$totalcs[$k],2,".",",")?>&nbsp;&nbsp;&nbsp;&nbsp;
			</td>
			<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;
			<?=$totalcs[$k]?>
			</td>
			<? } ?>
		</tr><?if($rowcnt%$chkrow==0){echo $header;} ?>
<?	}
}
else if($order=="Total CSI")
{
		for($k=0; $k<$rsth["rows"]; $k++) {
			for($d=0;$d<$rsdate["rows"];$d++){
				if($obj->getIdToText($rsth[$k]["emp_id"],"l_employee","emp_id","emp_id","emp_id IN ( $allth ) and emp_active=1")>0){
					$thtotal[$rsth[$k]["emp_id"]]["csi"]=$totalcsi[$k]/$totalcs[$k];
					$thtotal[$rsth[$k]["emp_id"]]["cs"]=$totalcs[$k];
				}
			}
			if($sort=="A > Z"){arsort($thtotal);}
			else{asort($thtotal);}
		}
		
		//print_r($thtotal);
		$total = array();
		$cnt=0;	// resorting branch id to new array for show in report
		foreach ($thtotal as $key => $val) {
  			  $tmpthtotal[$cnt] = $key;
  			  $totalcs[$cnt] = $val["cs"];
  			  $totalcsi[$cnt] = $val["csi"];
  			  $cnt++;
		}
		//print_r($total);echo $column;
		for($k=0; $k<$cnt; $k++) { 
 ?>
		<tr height="22"><?$rowcnt++;?>
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpthtotal[$k],"l_employee","emp_code","emp_id")." ".$obj->getIdToText($tmpthtotal[$k],"l_employee","emp_nickname","emp_id")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>		
			<td align="right">
			<?	
			$ttcsicnt = ($obj->sumeachempfield($rs,"total",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]))?$obj->sumeachempfield($rs,"total",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]):1;
			$ptmp[$k][$d]=$obj->sumeachempfield($rs,"totalcsi",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d])/$ttcsicnt;
			echo number_format($ptmp[$k][$d],2,".",",");?>
			</td>
			<td align="right">
			<?	$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
			echo $tmp[$k][$d];?>
			</td>
			<? } ?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<td align="right">
			<?=number_format($totalcsi[$k],2,".",",")?>&nbsp;&nbsp;&nbsp;&nbsp;
			</td>
			<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;
			<?=$totalcs[$k]?>
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
		$divide = ($csdatetotal[$d])?$csdatetotal[$d]:1;
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#eaeaea\"><b>";
		echo number_format($csidatetotal[$d]/$divide,2,".",",");
		echo "</a></b></td>\n";
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#eaeaea\"><b>";
		echo $csdatetotal[$d];
		echo "</a></b></td>\n";
}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
				
			<td align="right" bgcolor="#eaeaea"><b>
			<?=number_format($allcsitotal/$allcstotal,2,".",",")?>
			</b></td>
			<td align="right" bgcolor="#eaeaea"><b>
			<?=$allcstotal?>
			</b></td>
			
			<? } ?>
    </tr>
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
		    	<td class="reporth" align="center" style="white-space: nowrap;" colspan="<?=($column=="Total only"||$enddate==$begindate)?$rsdate["rows"]*2+1:$rsdate["rows"]*2+2 ?>">
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
					<td style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=0;$d<$rsdate["rows"];$d++){ ?>
						<td colspan="2" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"){?>
					<td colspan="2" style="text-align:right;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;"><b style="text-decoration: underline;"> TOTAL </b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=0;$d<$rsdate["rows"];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>% CSI</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Cust.</b></td>
					<? }  ?>
					<? if($column!="Total only"){?>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>% CSI</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Cust.</b></td>
					<? }?>
				</tr>
<?
if($collapse=="Collapse"){	//check Collapse/Expand loop
	
for($i=0; $i<$rsbranch["rows"]; $i++) {		// start city loop		
if($obj->getIdToText($rsbranch[$i]["branch_id"],"l_employee","emp_id","branch_id","branch_id IN ( $allbranch ) and emp_active=1")>0){
?>
<tr height="32">
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #000000 double;" bgcolor="#d3d3d3"><b><?=$rsbranch[$i]["branch_name"]?></b></td>
	<td colspan="<?=($column=="Total only"||$enddate==$begindate)?$rsdate["rows"]*2:$rsdate["rows"]*2+2?>" bgcolor="#d3d3d3" style="border-bottom:3px #000000 double;">&nbsp;</td>
</tr>
<?
for($k=0; $k<$rsth["rows"]; $k++) { 	// start branch name loop
		if($rsth[$k]["branch_id"]==$rsbranch[$i]["branch_id"]&&
		$obj->getIdToText($rsth[$k]["emp_id"],"l_employee","emp_id","emp_id","emp_id IN ( $allth ) and emp_active=1")>0){
?>
						<tr height="22">
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsth[$k]["emp_code"]." ".$rsth[$k]["emp_nickname"]?></td>
							<? for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop ?>		
								<td align="right">
									<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$i]["branch_id"].",".$rsth[$k]["emp_id"]?>)"><? } ?>
									<?=number_format($ptmp[$k][$d],2,".",",")?>
									<?if($export==false){?></a><? } ?>
								</td>
								<td align="right">
									<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$i]["branch_id"].",".$rsth[$k]["emp_id"]?>)"><? } ?>
									<?=$tmp[$k][$d]?>
									<?if($export==false){?></a><? } ?>
								</td>
								<? } ?>	
							<? 
							   if($column!="Total only"){ ?>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rsth[$k]["emp_id"]?>)"><?}?>
							<?=number_format($totalcsi[$k]/$totalcs[$k],2,".",",");?>
							<?if($export==false){?></a><?}?>
							</td>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rsth[$k]["emp_id"]?>)"><?}?>
							<?=$totalcs[$k]?>
							<?if($export==false){?></a><?}?>
							</td>
							<? } ?>
						</tr>
						<?	
						}
					} 
					?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=($column=="Total only"||$begindate==$enddate)?1:$rsdate["rows"]*2+1?>"><b>Total in <?=$rsbranch[$i]["branch_name"]?></b></td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$i]["branch_id"].",0"?>)"><?}?>
			<b><?=number_format($totalcsibranch[$i]/$totalcsbranch[$i],2,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$i]["branch_id"].",0"?>)"><?}?>
			<b><?= $totalcsbranch[$i]?></b>
			<?if($export==false){?></a><?}?>
			</td>
		</tr>
<?	
	} 
}

}	// End check collapse expand loop
else if($order!="Total Customer"&&$order!="Total CSI")		// for colapse information report
{
		for($k=0; $k<$rsth["rows"]; $k++) {// start branch total loop
			if($obj->getIdToText($rsth[$k]["emp_id"],"l_employee","emp_id","emp_id","emp_id IN ( $allth ) and emp_active=1")>0){
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$rsth[$k]["emp_code"]." ".$rsth[$k]["emp_nickname"]?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rsth[$k]["emp_id"]?>)"><?}?>
			<?=number_format($ptmp[$k][$d],2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rsth[$k]["emp_id"]?>)"><?}?>
			<?=$tmp[$k][$d]?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rsth[$k]["emp_id"]?>)"><?}?>
			<?=number_format($totalcsi[$k]/$totalcs[$k],2,".",",");?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rsth[$k]["emp_id"]?>)"><?}?>
			<?=$totalcs[$k]?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			
		</tr>
<?
			}
		}
}
else if($order=="Total Customer")
{

		for($k=0; $k<$rsth["rows"]; $k++) {
			for($d=0;$d<$rsdate["rows"];$d++){
				if($obj->getIdToText($rsth[$k]["emp_id"],"l_employee","emp_id","emp_id","emp_id IN ( $allth ) and emp_active=1")>0){
					$thtotal[$rsth[$k]["emp_id"]]["cs"]=$totalcs[$k];
					$thtotal[$rsth[$k]["emp_id"]]["csi"]=$totalcsi[$k];
				}
			}
			if($sort=="A > Z"){arsort($thtotal);}
			else{asort($thtotal);}
		}
		
		//print_r($thtotal);
		$total = array();
		$cnt=0;	// resorting branch id to new array for show in report
		foreach ($thtotal as $key => $val) {
  			  $tmpthtotal[$cnt] = $key;
  			  $totalcs[$cnt] = $val["cs"];
  			  $totalcsi[$cnt] = $val["csi"];
  			  $cnt++;
		}
		//print_r($total);echo $column;
		for($k=0; $k<$cnt; $k++) { 
			
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpthtotal[$k],"l_employee","emp_code","emp_id")." ".$obj->getIdToText($tmpthtotal[$k],"l_employee","emp_nickname","emp_id")?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$tmpthtotal[$k]?>)"><?}?>
			<?	
			$csitmp[$k][$d]=$obj->sumeachempfield($rs,"totalcsi",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
			$ttcsicnt = ($obj->sumeachempfield($rs,"total",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]))?$obj->sumeachempfield($rs,"total",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]):1;
			$ptmp[$k][$d]=$csitmp[$k][$d]/$ttcsicnt; 
				echo number_format($ptmp[$k][$d],2,".",",");	?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$tmpthtotal[$k]?>)"><?}?>
			<?	$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]); 
				echo $tmp[$k][$d];	?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$tmpthtotal[$k]?>)"><?}?>
			<?=number_format($totalcsi[$k]/$totalcs[$k],2,".",",");?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$tmpthtotal[$k]?>)"><?}?>
			<?=$totalcs[$k]?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			
		</tr>
<? } ?>
<? } else if($order=="Total CSI")
{

		for($k=0; $k<$rsth["rows"]; $k++) {
			for($d=0;$d<$rsdate["rows"];$d++){
				if($obj->getIdToText($rsth[$k]["emp_id"],"l_employee","emp_id","emp_id","emp_id IN ( $allth ) and emp_active=1")>0){
					$thtotal[$rsth[$k]["emp_id"]]["csi"]=$totalcsi[$k]/$totalcs[$k];
					$thtotal[$rsth[$k]["emp_id"]]["cs"]=$totalcs[$k];
				}
			}
			if($sort=="A > Z"){arsort($thtotal);}
			else{asort($thtotal);}
		}
		
		//print_r($thtotal);
		$total = array();
		$cnt=0;	// resorting branch id to new array for show in report
		foreach ($thtotal as $key => $val) {
  			  $tmpthtotal[$cnt] = $key;
  			  $totalcs[$cnt] = $val["cs"];
  			  $totalcsi[$cnt] = $val["csi"];
  			  $cnt++;
		}
		//print_r($total);echo $column;
		for($k=0; $k<$cnt; $k++) { 
			
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpthtotal[$k],"l_employee","emp_code","emp_id")." ".$obj->getIdToText($tmpthtotal[$k],"l_employee","emp_nickname","emp_id")?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$tmpthtotal[$k]?>)"><?}?>
			<?	
			$csitmp[$k][$d]=$obj->sumeachempfield($rs,"totalcsi",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
			$ttcsicnt = ($obj->sumeachempfield($rs,"total",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]))?$obj->sumeachempfield($rs,"total",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]):1;
			$ptmp[$k][$d]=$csitmp[$k][$d]/$ttcsicnt; 
				echo number_format($ptmp[$k][$d],2,".",",");	?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$tmpthtotal[$k]?>)"><?}?>
			<?	$tmp[$k][$d]=$obj->sumeachempfield($rs,"total",$tmpthtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]); 
				echo $tmp[$k][$d];	?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$tmpthtotal[$k]?>)"><?}?>
			<?=number_format($totalcsi[$k],2,".",",");?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" class="pagelink" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$tmpthtotal[$k]?>)"><?}?>
			<?=$totalcs[$k]?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			
		</tr>
<? } ?>
<? } ?>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>TOTAL</b></td>
			
<?
for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
		$divide = ($csdatetotal[$d])?$csdatetotal[$d]:1;
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
		if($export==false){
			echo "<a href=\"javascript:;\" style=\"text-decoration:none; color:#000000;\" onClick=\"openthDetail(".$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0)\">";
			echo number_format($csidatetotal[$d]/$divide,2,".",",");
			echo "</a></b></td>\n";
		}else{
			echo number_format($csidatetotal[$d]/$divide,2,".",",");
			echo "</b></td>\n";
		}
		echo "<td style=\"padding-left: 20px; white-space: nowrap;\" align=\"right\" bgcolor=\"#d3d3d3\"><b>";
		if($export==false){
			echo "<a href=\"javascript:;\" style=\"text-decoration:none; color:#000000;\" onClick=\"openthDetail(".$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0)\">";
			echo $csdatetotal[$d];
			echo "</a></b></td>\n";
		}else{
			echo $csdatetotal[$d];
			echo "</b></td>\n";
		}
}
?>
			<? if($column!="Total only"){ ?>
				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0"?>)"><?}?>
			<?=number_format($allcsitotal/$allcstotal,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</b></td>
				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openthDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0"?>)"><?}?>
			<?=$allcstotal?>
			<?if($export==false){?></a><?}?></b></td>
			
			<? } ?>
			
			</tr>
		    <tr>
		    	<td align="center" colspan="<?=($column=="Total only"||$enddate==$begindate)?$rsdate["rows"]*2+1:$rsdate["rows"]*2+2?>">
		    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
		    	</td>
			</tr>
 		</table><br>
		</td>
    </tr>
</table>
<? } ?>