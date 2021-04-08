<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("customer.inc.php");
$obj = new customer();
$obj->setDebugStatus(0);
$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate= $obj->getParameter("end");
$branchid= $obj->getParameter("branchid");
$cityid = $obj->getParameter("cityid",false);
$column= $obj->getParameter("column");
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");
$collapse = $obj->getParameter("Collapse");
//$resident = $obj->getParameter("resident");
$percent = $obj->getParameter("percent",true);
$branchtotal = array();
$today = date("Ymd");
$rsdate = $obj->getdatecol($column,$begindate,$enddate);
$rsres = $obj->getcustlocal("Resident",$begindate,$enddate,$branchid,false,$cityid);
$rsvis = $obj->getcustlocal("Visitor",$begindate,$enddate,$branchid,false,$cityid);

$rscity = $obj->getcity($order,$sort,$cityid);
for($j=0; $j<$rscity["rows"]; $j++){
	$city[$j]=$rscity[$j]["city_id"];
}
$rsbranchtype = $obj->getbranchtype($order,$sort);
for($j=0; $j<$rsbranchtype["rows"]; $j++){
	$branchtype[$j]=$rsbranchtype[$j]["branch_category_id"];
}
$rsbranch = $obj->getbranch($order,$sort,$branchid,0,$cityid);
for($j=0; $j<$rsbranch["rows"]; $j++){
	$branch[$j]=$rsbranch[$j]["branch_id"];
}
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-Type: application/vnd.ms-excel");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Customer Resident Or Visitor Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
$allbranchtype = implode(',',$branchtype);
$allbranch = implode(',',$branch);
$allrestotal=0;$allvistotal=0;
$allresdatetotal=array();
for($i=0; $i<$rscity["rows"]; $i++) {		// start city loop		
	if($obj->getIdToText($rscity[$i]["city_id"],"bl_branchinfo","branch_id","city_id","branch_category_id IN ( $allbranchtype ) and branch_active=1")>0){
		$alllrestotal[$i] = $alllvistotal[$i] = 0;
		for($j=0; $j<$rsbranchtype["rows"]; $j++) {	
			if($obj->getIdToText($rsbranchtype[$j]["branch_category_id"],"bl_branchinfo","branch_id","branch_category_id"," branch_id IN ( $allbranch ) and branch_active=1 and city_id=".$rscity[$i]["city_id"])>0){
				for($k=0; $k<$rsbranch["rows"]; $k++) {
					if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
							$totalres[$k] = $totalvis[$k] = 0;
							for($d=0;$d<$rsdate["rows"];$d++){ // start date total loop
									$tmpres[$k][$d]=$obj->sumeachfield($rsres,"total",$rsbranch[$k]["branch_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
									$tmpvis[$k][$d]=$obj->sumeachfield($rsvis,"total",$rsbranch[$k]["branch_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
									$totalres[$k]+=$tmpres[$k][$d];
									$totalvis[$k]+=$tmpvis[$k][$d];
									if(!isset($allresdatetotal[$d])){$allresdatetotal[$d]=0;}
									if(!isset($allvisdatetotal[$d])){$allvisdatetotal[$d]=0;}
									if(!isset($allresbttotal[$i][$j])){$allresbttotal[$i][$j]=0;}
									if(!isset($allvisbttotal[$i][$j])){$allvisbttotal[$i][$j]=0;}
									if(!isset($btrestotal[$i][$j][$d])){$btrestotal[$i][$j][$d]=0;}
									if(!isset($btvistotal[$i][$j][$d])){$btvistotal[$i][$j][$d]=0;}
							}
					}
				}
			}
			$allresbttotal[$i][$j] = $allvisbttotal[$i][$j] = 0;
			for($d=0;$d<$rsdate["rows"];$d++){// start date total loop
					$btrestotal[$i][$j][$d] = 0;
					$btvistotal[$i][$j][$d] = 0;
					for($k=0; $k<$rsbranch["rows"]; $k++) { 
						if(!isset($tmpres[$k][$d])){$tmpres[$k][$d]=0;}
						if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
							$btrestotal[$i][$j][$d] += $tmpres[$k][$d];
							$allresbttotal[$i][$j] += $tmpres[$k][$d];
							$allresdatetotal[$d] += $tmpres[$k][$d];
							$allrestotal +=  $tmpres[$k][$d];
							
							$btvistotal[$i][$j][$d] += $tmpvis[$k][$d];
							$allvisbttotal[$i][$j] += $tmpvis[$k][$d];
							$allvisdatetotal[$d] += $tmpvis[$k][$d];
							$allvistotal +=  $tmpvis[$k][$d];
						}
					}
			}
			$alllrestotal[$i] += $allresbttotal[$i][$j];
			$alllvistotal[$i] += $allvisbttotal[$i][$j];
		}
	}
}
if($export!="Excel"&&$export){
	$chkcolumn=2;		// row column per page
	$alltable=ceil($rsdate["rows"]/$chkcolumn);
	if($column=="Total only"){$alltable=1;}
	$rowcnt=0;
	if($collapse=="Collapse"){$chkrow = $obj->getParameter("chkrow",27);}
	else{$chkrow = $obj->getParameter("chkrow",40);}
}
if($percent){$pr=1;}else{$pr=0;}
if($begindate==$enddate){$column="Total only";$rsdate["header"][0]="TOTAL";}	// if begindate = enddate

$reportname = "Customer Resident Or Visitor Report";
$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
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
<?if($export!="Excel"&&$export!="PDF"){?><script type="text/javascript" src="../scripts/ajax.js"></script><script type="text/javascript" src="../../../scripts/components.js"></script><?}?>
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
	$allcolumncnt = 4*($datechk["end"][$a]-$datechk["begin"][$a]+1);
	if($column!="Total only"&&$a==$alltable-1){
		$allcolumncnt+=4;
	}
	$columnwidth = 60/$allcolumncnt;
	$firstcolumnwidth = 100-($columnwidth*($allcolumncnt));
?>		
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0">
				<tr>		<!-- set column width for export to pdf -->
					<td width="<?=$firstcolumnwidth?>%"></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
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
			    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" colspan="4" style="text-align:center;padding-left:10px;border-top:2px #000000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
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
				<tr>		<!-- set column width for export to pdf -->
					<td width="<?=$firstcolumnwidth?>%"></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
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
			    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" colspan="4" style="text-align:center;padding-left:10px;border-top:2px #000000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
				</tr>
<?	} ?>
<tr height="32">
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #000000 double;" bgcolor="#d3d3d3"><b>Location: <?=$rscity[$i]["city_name"]?></b></td>
	<td colspan="<?=($column=="Total only")?4*($datechk["end"][$a]-$datechk["begin"][$a]+2):4*($datechk["end"][$a]-$datechk["begin"][$a]+2)+1?>" style="padding-left:7px; white-space: nowrap; border-bottom:3px #000000 double;" bgcolor="#d3d3d3">&nbsp;</td><!-- input city -->
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
				<tr>		<!-- set column width for export to pdf -->
					<td width="<?=$firstcolumnwidth?>%"></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
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
			    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" colspan="4" style="text-align:center;padding-left:10px;border-top:2px #000000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
				</tr>
<?	} ?>
			<tr height="28">
				<td style="padding-left: 20px; white-space: nowrap;"><b>Category: <?=$rsbranchtype[$j]["branch_category_name"]?></b></td>
				<td colspan="<?=($column=="Total only")?4*($datechk["end"][$a]-$datechk["begin"][$a]+2):4*($datechk["end"][$a]-$datechk["begin"][$a]+2)+1?>" style="padding-left: 20px;">&nbsp;</td>
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
				<tr>		<!-- set column width for export to pdf -->
					<td width="<?=$firstcolumnwidth?>%"></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
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
			    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="<?=$columnwidth?>%" colspan="4" style="text-align:center;padding-left:10px;border-top:2px #000000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
				</tr>
<?	} ?>
						<tr height="22">
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
							<? 	//$total[$k] = 0;
								for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start branch total loop
							?>		
								<?$total = $tmpres[$k][$d]+$tmpvis[$k][$d]; $total = ($total)?$total:1;
								$pr = $tmpres[$k][$d]*100/($total);
								$pv = $tmpvis[$k][$d]*100/($total); ?>
								<td align="right">
									<?=number_format($tmpres[$k][$d],0,".",",")?>
								</td>
								<td align="right">
									<?=number_format($pr,2,".",",")?>
								</td>
								<td align="right">
									<?=number_format($tmpvis[$k][$d],0,".",",")?>
								</td>
								<td align="right">
									<?=number_format($pv,2,".",",")?>
								</td>
								<? } ?>	
								
						<? if($column!="Total only"&&$a==$alltable-1){ ?>
						<?	$total = $totalres[$k]+$totalvis[$k]; $total = ($total)?$total:1;
							$pr = $totalres[$k]*100/($total);
							$pv = $totalvis[$k]*100/($total);?>
							<td align="right"><?=number_format(array_sum($tmpres[$k]),0,".",",")?></td>
							<td align="right"><?=number_format($pr,2,".",",")?></td>
							<td align="right"><?=number_format(array_sum($tmpvis[$k]),0,".",",")?></td>
							<td align="right"><?=number_format($pv,2,".",",")?></td>
						<? } ?>
							
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
				<tr>		<!-- set column width for export to pdf -->
					<td width="<?=$firstcolumnwidth?>%"></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
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
			    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="<?=$columnwidth?>%" colspan="4" style="text-align:center;padding-left:10px;border-top:2px #000000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
				</tr>
<?	} ?>
			<tr height="28">
				<td style="padding-left: 10px; white-space: nowrap;border-top:1px #000000 solid;"><b>Total in  <?=$rsbranchtype[$j]["branch_category_name"]?> Category: </b></td>
				<? 
				for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){// start date total loop
				?>		
				<?$total = $btrestotal[$i][$j][$d]+$btvistotal[$i][$j][$d]; $total = ($total)?$total:1;
					$pr = $btrestotal[$i][$j][$d]*100/($total);
					$pv = $btvistotal[$i][$j][$d]*100/($total);?>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($btrestotal[$i][$j][$d],0,".",",")?>
					</td>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($pr,2,".",",")?>
					</td>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($btvistotal[$i][$j][$d],0,".",",")?>
					</td>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($pv,2,".",",")?>
					</td>
				<? }  ?>	
				
				<? if($column!="Total only"&&$a==$alltable-1){ ?>
				<? $total = $allresbttotal[$i][$j]+$allvisbttotal[$i][$j]; $total = ($total)?$total:1;
					$pr = $allresbttotal[$i][$j]*100/($total);
					$pv = $allvisbttotal[$i][$j]*100/($total);?>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($allresbttotal[$i][$j],0,".",",")?>
					</td>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($pr,2,".",",")?>
					</td>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($allvisbttotal[$i][$j],0,".",",")?>
					</td>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($pv,2,".",",")?>
					</td>
				<? } ?>
				
			</tr>
		<?	 	
			} 
		}
		?>
		<?  if($a==$alltable-1){ ?>
		<?  $total = $alllrestotal[$i]+$alllvistotal[$i]; $total = ($total)?$total:1;
			$pr = $alllrestotal[$i]*100/($total);
			$pv = $alllvistotal[$i]*100/($total);?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=$allcolumncnt-3?>"><b>Total in <?=$rscity[$i]["city_name"]?></b></td>
			<td align="right">
			<b><?=number_format($alllrestotal[$i],0,".",",")?></b>
			</td><td align="right">
			<b><?=number_format($pr,2,".",",")?></b>
			</td><td align="right">
			<b><?=number_format($alllvistotal[$i],0,".",",")?></b>
			</td><td align="right">
			<b><?=number_format($pv,2,".",",")?></b>
			</td>
		</tr>
		<?}?>
<?	
	} 
}

}	// End check collapse expand loop
else if($order=="Total Resident")		// Total Resident report
{
	if($a==0){
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop for sort array of total in each branch
			$branchtotal[$rsbranch[$k]["branch_id"]]=$totalres[$k];

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
				<tr>		<!-- set column width for export to pdf -->
					<td width="<?=$firstcolumnwidth?>%"></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
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
			    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="<?=$columnwidth?>%" colspan="4" style="text-align:center;padding-left:10px;border-top:2px #000000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
				</tr>
<?	} 
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpbranchtotal[$k],"bl_branchinfo","branch_name","branch_id")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){	
				$tmpres[$k][$d]=$obj->sumeachfield($rsres,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$tmpvis[$k][$d]=$obj->sumeachfield($rsvis,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$total = $tmpvis[$k][$d]+$tmpres[$k][$d]; $total = ($total)?$total:1;
				$pr = $tmpres[$k][$d]*100/($total);
				$pv = $tmpvis[$k][$d]*100/($total);
?>		
			<td align="right">
				<?=number_format($tmpres[$k][$d],0,".",",")?>
			</td>
			<td align="right">
				<?=number_format($pr,2,".",",")?>
			</td>
			<td align="right">
				<?=number_format($tmpvis[$k][$d],0,".",",")?>
			</td>
			<td align="right">
				<?=number_format($pv,2,".",",")?>
			</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<?	$total = array_sum($tmpres[$k])+array_sum($tmpvis[$k]); $total = ($total)?$total:1;
				$pr = array_sum($tmpres[$k])*100/($total);
				$pv = array_sum($tmpvis[$k])*100/($total);?>
				<td align="right"><?=number_format($totalres[$k],0,".",",")?></td>
				<td align="right"><?=number_format($pr,2,".",",")?></td>
				<td align="right"><?=number_format($totalvis[$k],0,".",",")?></td>
				<td align="right"><?=number_format($pv,2,".",",")?></td>
			<? } ?>
					
		</tr>
<?
	}
	
}		//end Total Resident report
else if($order=="Total Visitor")		// Total Visitor report
{
	if($a==0){
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop for sort array of total in each branch
			$branchtotal[$rsbranch[$k]["branch_id"]]=$totalvis[$k];

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
				<tr>		<!-- set column width for export to pdf -->
					<td width="<?=$firstcolumnwidth?>%"></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
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
			    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="<?=$columnwidth?>%" colspan="4" style="text-align:center;padding-left:10px;border-top:2px #000000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
				</tr>
<?	} 
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpbranchtotal[$k],"bl_branchinfo","branch_name","branch_id")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){	
				$tmpres[$k][$d]=$obj->sumeachfield($rsres,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$tmpvis[$k][$d]=$obj->sumeachfield($rsvis,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$total = $tmpvis[$k][$d]+$tmpres[$k][$d]; $total = ($total)?$total:1;
				$pr = $tmpres[$k][$d]*100/($total);
				$pv = $tmpvis[$k][$d]*100/($total);
?>		
			<td align="right">
				<?=number_format($tmpres[$k][$d],0,".",",")?>
			</td>
			<td align="right">
				<?=number_format($pr,2,".",",")?>
			</td>
			<td align="right">
				<?=number_format($tmpvis[$k][$d],0,".",",")?>
			</td>
			<td align="right">
				<?=number_format($pv,2,".",",")?>
			</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<?	$total = array_sum($tmpres[$k])+array_sum($tmpvis[$k]); $total = ($total)?$total:1;
				$pr = array_sum($tmpres[$k])*100/($total);
				$pv = array_sum($tmpvis[$k])*100/($total);?>
				<td align="right"><?=number_format($totalres[$k],0,".",",")?></td>
				<td align="right"><?=number_format($pr,2,".",",")?></td>
				<td align="right"><?=number_format($totalvis[$k],0,".",",")?></td>
				<td align="right"><?=number_format($pv,2,".",",")?></td>
			<? } ?>
					
		</tr>
<?
	}
	
}		//end Total Visitor report
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
				<tr>		<!-- set column width for export to pdf -->
					<td width="<?=$firstcolumnwidth?>%"></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
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
			    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="<?=$columnwidth?>%" colspan="4" style="text-align:center;padding-left:10px;border-top:2px #000000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
						<td width="40" style="text-align:right;padding-right:12px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
						<td width="40" style="text-align:right;padding-right:12px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
					<td width="40" style="text-align:right;padding-left:10px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
					<td width="40" style="text-align:right;padding-left:10px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
				</tr>
<?	} ?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>			
				<?	$total = $tmpres[$k][$d]+$tmpvis[$k][$d]; $total = ($total)?$total:1;
					$pr = $tmpres[$k][$d]*100/($total);
					$pv = $tmpvis[$k][$d]*100/($total); ?>
				<td align="right">
					<?=number_format($tmpres[$k][$d],0,".",",")?>
				</td>
				<td align="right">
					<?=number_format($pr,2,".",",")?>
				</td>
				<td align="right">
					<?=number_format($tmpvis[$k][$d],0,".",",")?>
				</td>
				<td align="right">
					<?=number_format($pv,2,".",",")?>
				</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
				<?	$total = $totalres[$k]+$totalvis[$k]; $total = ($total)?$total:1;
					$pr = $totalres[$k]*100/($total);
					$pv = $totalvis[$k]*100/($total);?>
					<td align="right"><?=number_format($totalres[$k],0,".",",")?></td>
					<td align="right"><?=number_format($pr,2,".",",")?></td>
					<td align="right"><?=number_format($totalvis[$k],0,".",",")?></td>
					<td align="right"><?=number_format($pv,2,".",",")?></td>
			<? } ?>
			
		</tr>
<?
	}
}
else
{
	if($a==0){
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop for sort array of total in each branch
			$branchtotal[$rsbranch[$k]["branch_id"]]=$totalres[$k]+$totalvis[$k];

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
				<tr>		<!-- set column width for export to pdf -->
					<td width="<?=$firstcolumnwidth?>%"></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
						<td width="<?=$columnwidth?>%"></td><td width="<?=$columnwidth?>%"></td>
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
			    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="<?=$columnwidth?>%" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="<?=$columnwidth?>%" colspan="4" style="text-align:center;padding-left:10px;border-top:2px #000000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
				</tr>
<?	} 
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpbranchtotal[$k],"bl_branchinfo","branch_name","branch_id")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){	
				$tmpres[$k][$d]=$obj->sumeachfield($rsres,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$tmpvis[$k][$d]=$obj->sumeachfield($rsvis,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$total = $tmpvis[$k][$d]+$tmpres[$k][$d]; $total = ($total)?$total:1;
				$pr = $tmpres[$k][$d]*100/($total);
				$pv = $tmpvis[$k][$d]*100/($total);
?>		
			<td align="right">
				<?=number_format($tmpres[$k][$d],0,".",",")?>
			</td>
			<td align="right">
				<?=number_format($pr,2,".",",")?>
			</td>
			<td align="right">
				<?=number_format($tmpvis[$k][$d],0,".",",")?>
			</td>
			<td align="right">
				<?=number_format($pv,2,".",",")?>
			</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<?	$total = array_sum($tmpres[$k])+array_sum($tmpvis[$k]); $total = ($total)?$total:1;
				$pr = array_sum($tmpres[$k])*100/($total);
				$pv = array_sum($tmpvis[$k])*100/($total);?>
				<td align="right"><?=number_format($totalres[$k],0,".",",")?></td>
				<td align="right"><?=number_format($pr,2,".",",")?></td>
				<td align="right"><?=number_format($totalvis[$k],0,".",",")?></td>
				<td align="right"><?=number_format($pv,2,".",",")?></td>
			<? } ?>
					
		</tr>
<?
	}
	
}
?>
		<tr height="22">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>TOTAL</b></td>
			
<?
for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start branch total loop
		$allresindate = $allresdatetotal[$d];
		$allvisindate = $allvisdatetotal[$d];
		$total = $allresindate+$allvisindate; $total = ($total)?$total:1;
		$pr = $allresindate*100/($total);
		$pv = $allvisindate*100/($total);
		
?>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3">
			<b><?=number_format($allresindate,0,".",",")?></b>
		</td>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3">
			<b><?=number_format($pr,2,".",",")?></b>
		</td>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3">
			<b><?=number_format($allvisindate,0,".",",")?></b>
		</td>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3">
			<b><?=number_format($pv,2,".",",")?></b>
		</td>
<?
}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ 
					$total = $allrestotal+$allvistotal; $total = ($total)?$total:1;
					$pr = $allrestotal*100/($total);
					$pv = $allvistotal*100/($total); ?>				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=number_format($allrestotal,0,".",",")?>
			</b></td>			
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=number_format($pr,2,".",",")?>
			</b></td>			
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=number_format($allvistotal,0,".",",")?>
			</b></td>			
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=number_format($pv,2,".",",")?>
			</b></td>
			<? } ?>
			
	</tr>	
    <tr height="20">
    	<td align="center" colspan="<?=$allcolumncnt+1?>">
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
    	<td class="reporth" align="center">
    		<b><p>Spa Management System</p>
		    <?=$reportname?></b><br>
		    <p class="style1">
		    <?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
		    <?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
		    </p>
		    <p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br></p>
    	</td>
    </tr>
	<tr>
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0">
				<tr height="32">
					<td style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=0;$d<$rsdate["rows"];$d++){ ?>
						<td colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"){?>
					<td colspan="4" style="text-align:center;padding-left:10px;border-top:2px #000000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="32">
					<td style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=0;$d<$rsdate["rows"];$d++){ ?>
						<td style="text-align:center;padding-right:12px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
						<td style="text-align:center;padding-right:12px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td style="text-align:center;padding-right:12px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>V</b></td>
						<td style="text-align:center;padding-right:12px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"){?>
					<td style="text-align:center;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>R</b></td>
					<td style="text-align:center;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td style="text-align:center;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>V</b></td>
					<td style="text-align:center;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
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
	<? $allcolumn = ($column=="Total only")?$rsdate["rows"]*4:$rsdate["rows"]*4+4;
	 for($d=0;$d<$allcolumn;$d++){ ?>
			<td  bgcolor="#d3d3d3" style="border-bottom:3px #000000 double;">&nbsp;</td>
	<? }  ?>
</tr>
<?	
		for($j=0; $j<$rsbranchtype["rows"]; $j++) {		// start branch category loop
			$allbranch = implode(",",$branch);
			if($obj->getIdToText($rsbranchtype[$j]["branch_category_id"],"bl_branchinfo","branch_id","branch_category_id"," branch_id IN ( $allbranch ) and branch_active=1 and city_id=".$rscity[$i]["city_id"])>0){
			?>
			<tr height="28">
				<td style="padding-left: 20px; white-space: nowrap;"><b>Category: <?=$rsbranchtype[$j]["branch_category_name"]?></b></td>
				<td colspan="<?=($column=="Total only")?$rsdate["rows"]*4:$rsdate["rows"]*4+4?>" style="padding-left: 20px;">&nbsp;</td>
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
									<?=number_format($tmpres[$k][$d],0,".",",")?>
									<?if($export==false){?></a><? } ?>&nbsp;
								</td>
								<td align="right">
								<?$total = $tmpres[$k][$d]+$tmpvis[$k][$d]; $total = ($total)?$total:1;
								$pr = $tmpres[$k][$d]*100/($total);?>
								<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><? } ?>
									<?=number_format($pr,2,".",",")?>
								<?if($export==false){?></a><? } ?>&nbsp;
								</td>
								<td align="right">
									<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><? } ?>
									<?=number_format($tmpvis[$k][$d],0,".",",")?>
									<?if($export==false){?></a><? } ?>&nbsp;
								</td>
								<td align="right">
								<?$pv = $tmpvis[$k][$d]*100/($total);?>
								<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><? } ?>
									<?=number_format($pv,2,".",",")?>
								<?if($export==false){?></a><? } ?>&nbsp;
								</td>
								<? } ?>	
							<? if($column!="Total only"){ ?>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
							<?=number_format($totalres[$k],0,".",",")?>
							<?if($export==false){?></a><?}?>
							</td>
							<td align="right">
							<? $total = $totalres[$k]+$totalvis[$k]; $total = ($total)?$total:1;
								$pr = $totalres[$k]*100/($total);?>
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
							<?=number_format($pr,2,".",",")?>
							<?if($export==false){?></a><?}?>
							</td>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
							<?=number_format($totalvis[$k],0,".",",")?>
							<?if($export==false){?></a><?}?>
							</td>
							<td align="right">
							<?$pv = $totalvis[$k]*100/($total);?>
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
							<?=number_format($pv,2,".",",")?>
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
				<? for($d=0;$d<$rsdate["rows"];$d++){// start date total loop ?>		
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
				<?=number_format($btrestotal[$i][$j][$d],0,".",",")?>
				<?if($export==false){?></a><?}?>&nbsp;
				</td>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
					<?$total = $btrestotal[$i][$j][$d]+$btvistotal[$i][$j][$d]; $total = ($total)?$total:1;
					$pr = $btrestotal[$i][$j][$d]*100/($total);?>
				<?=number_format($pr,2,".",",")?>&nbsp;
				<?if($export==false){?></a><?}?>
				</td>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
				<?=number_format($btvistotal[$i][$j][$d],0,".",",")?>
				<?if($export==false){?></a><?}?>&nbsp;
				</td>
				<td align="right" style="border-top:1px #000000 solid;">
					<?$pv = $btvistotal[$i][$j][$d]*100/($total);?>
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
				<?=number_format($pv,2,".",",")?>&nbsp;
				<?if($export==false){?></a><?}?>
				</td>
				<? }  ?>	
		<? if($column!="Total only"){ ?>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
				<?=number_format($allresbttotal[$i][$j],0,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
					<? $total = $allresbttotal[$i][$j]+$allvisbttotal[$i][$j]; $total = ($total)?$total:1;
					$pr = $allresbttotal[$i][$j]*100/($total);?>
				<?=number_format($pr,2,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
				<?=number_format($allvisbttotal[$i][$j],0,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<td align="right" style="border-top:1px #000000 solid;">
					<?$pv = $allvisbttotal[$i][$j]*100/($total);?>
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)" class="pagelink"><?}?>
				<?=number_format($pv,2,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
		<? } ?>
									
			</tr>
		<?	} 
		}
		?>
		<tr height="35">
			<? 
			if($allcolumn>1000){
				$allcolumn = ($column=="Total only")?($rsdate["rows"]-1)*4-2:($rsdate["rows"])*4-2;
				 for($d=0;$d<$allcolumn;$d++){ ?>
				<td style="padding-left: 20px; white-space: nowrap;">&nbsp;</td>
				<? }  ?>
				<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="3"><b>Total in <?=$rscity[$i]["city_name"]?></b></td>
			<? } else { ?>
				<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=($column=="Total only")?($rsdate["rows"]-1)*4+1:($rsdate["rows"])*4+1?>"><b>Total in <?=$rscity[$i]["city_name"]?></b></td>
			<? } ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",0"?>)" class="pagelink"><?}?>
			<b><?=number_format($alllrestotal[$i],0,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
				<? $total = $alllrestotal[$i]+$alllvistotal[$i]; $total = ($total)?$total:1;
					$pr = $alllrestotal[$i]*100/($total);?>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",0"?>)" class="pagelink"><?}?>
			<b><?=number_format($pr,2,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",0"?>)" class="pagelink"><?}?>
			<b><?=number_format($alllvistotal[$i],0,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
					<?$pv = $alllvistotal[$i]*100/($total);?>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",0"?>)" class="pagelink"><?}?>
			<b><?=number_format($pv,2,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>
			
		</tr>
<?	
	} 
}

}	// End check collapse expand loop
else if($order=="Total Resident")		// for Total Resident report
{
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop for sort array of total in each branch
			$branchtotal[$rsbranch[$k]["branch_id"]]=$totalres[$k];
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
				$tmpres[$k][$d]=$obj->sumeachfield($rsres,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$tmpvis[$k][$d]=$obj->sumeachfield($rsvis,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$total = $tmpvis[$k][$d]+$tmpres[$k][$d]; $total = ($total)?$total:1;
				$pr = $tmpres[$k][$d]*100/($total);
				$pv = $tmpvis[$k][$d]*100/($total);
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	 echo number_format($tmpres[$k][$d],0,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	echo number_format($pr,2,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	 echo number_format($tmpvis[$k][$d],0,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	echo number_format($pv,2,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){ ?>
			<?	$total = array_sum($tmpres[$k])+array_sum($tmpvis[$k]); $total = ($total)?$total:1;
				$pr = array_sum($tmpres[$k])*100/($total);
				$pv = array_sum($tmpvis[$k])*100/($total);
			?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format(array_sum($tmpres[$k]),0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($pr,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format(array_sum($tmpvis[$k]),0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($pv,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			
		</tr>
<?
	}
	
}		// End Total Resident report
else if($order=="Total Visitor")		// for Total Visitor report
{
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop for sort array of total in each branch
			$branchtotal[$rsbranch[$k]["branch_id"]]=$totalvis[$k];
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
				$tmpres[$k][$d]=$obj->sumeachfield($rsres,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$tmpvis[$k][$d]=$obj->sumeachfield($rsvis,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$total = $tmpvis[$k][$d]+$tmpres[$k][$d]; $total = ($total)?$total:1;
				$pr = $tmpres[$k][$d]*100/($total);
				$pv = $tmpvis[$k][$d]*100/($total);
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	 echo number_format($tmpres[$k][$d],0,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	echo number_format($pr,2,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	 echo number_format($tmpvis[$k][$d],0,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	echo number_format($pv,2,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){ ?>
			<?	$total = array_sum($tmpres[$k])+array_sum($tmpvis[$k]); $total = ($total)?$total:1;
				$pr = array_sum($tmpres[$k])*100/($total);
				$pv = array_sum($tmpvis[$k])*100/($total);
			?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format(array_sum($tmpres[$k]),0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($pr,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format(array_sum($tmpvis[$k]),0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($pv,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			
		</tr>
<?
	}
	
}		// End Total Visitor report
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
			<?=number_format($tmpres[$k][$d],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
				<?$total = $tmpres[$k][$d]+$tmpvis[$k][$d]; $total = ($total)?$total:1;
				$pr = $tmpres[$k][$d]*100/($total);?>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($pr,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($tmpvis[$k][$d],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?$pv = $tmpvis[$k][$d]*100/($total);?>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($pv,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>

			<? if($column!="Total only"){ ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($totalres[$k],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
				<?$total = $totalres[$k]+$totalvis[$k]; $total = ($total)?$total:1;
				$pr = $totalres[$k]*100/($total);?>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($pr,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($totalvis[$k],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?$pv = $totalvis[$k]*100/($total);?>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($pv,2,".",",")?>
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
			$branchtotal[$rsbranch[$k]["branch_id"]]=$totalvis[$k]+$totalres[$k];
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
				$tmpres[$k][$d]=$obj->sumeachfield($rsres,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$tmpvis[$k][$d]=$obj->sumeachfield($rsvis,"total",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$total = $tmpvis[$k][$d]+$tmpres[$k][$d]; $total = ($total)?$total:1;
				$pr = $tmpres[$k][$d]*100/($total);
				$pv = $tmpvis[$k][$d]*100/($total);
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	 echo number_format($tmpres[$k][$d],0,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	echo number_format($pr,2,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	 echo number_format($tmpvis[$k][$d],0,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?	echo number_format($pv,2,".",","); 	?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){ ?>
			<?	$total = array_sum($tmpres[$k])+array_sum($tmpvis[$k]); $total = ($total)?$total:1;
				$pr = array_sum($tmpres[$k])*100/($total);
				$pv = array_sum($tmpvis[$k])*100/($total);
			?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format(array_sum($tmpres[$k]),0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($pr,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format(array_sum($tmpvis[$k]),0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k].",0,0"?>)" class="pagelink"><?}?>
			<?=number_format($pv,2,".",",")?>
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
for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
		$allresindate = $allresdatetotal[$d];
		$allvisindate = $allvisdatetotal[$d];
		$total = $allresindate+$allvisindate; $total = ($total)?$total:1;
		$pr = $allresindate*100/($total);
		$pv = $allvisindate*100/($total);
?>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3">
		<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0,0"?>)" class="pagelink"><?}?>
		<b><?=number_format($allresindate,0,".",",")?></b>
		<?if($export==false){?></a><?}?>
		</td>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3">
		<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0,0"?>)" class="pagelink"><?}?>
		<b><?=number_format($pr,2,".",",")?></b>
		<?if($export==false){?></a><?}?>
		</td>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3">
		<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0,0"?>)" class="pagelink"><?}?>
		<b><?=number_format($allvisindate,0,".",",")?></b>
		<?if($export==false){?></a><?}?>
		</td>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3">
		<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0,0"?>)" class="pagelink"><?}?>
		<b><?=number_format($pv,2,".",",")?></b>
		<?if($export==false){?></a><?}?>
		</td>
<?
}
?>
			<? if($column!="Total only"){ 
				$total = $allrestotal+$allvistotal; $total = ($total)?$total:1;
				$pr = $allrestotal*100/($total);
				$pv = $allvistotal*100/($total); ?>				
			<td align="right" bgcolor="#d3d3d3">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0,0"?>)" class="pagelink"><?}?>
			<b><?=number_format($allrestotal,0,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>			
			<td align="right" bgcolor="#d3d3d3">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0,0"?>)" class="pagelink"><?}?>
			<b><?=number_format($pr,2,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>			
			<td align="right" bgcolor="#d3d3d3">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0,0"?>)" class="pagelink"><?}?>
			<b><?=number_format($allvistotal,0,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>			
			<td align="right" bgcolor="#d3d3d3">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0,0"?>)" class="pagelink"><?}?>
			<b><?=number_format($pv,2,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>
			<? } ?>
			
			</tr>
 		</table><br>
		</td>
    </tr>
<?
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4); 
?>
	<tr>
		    	<td align="center">
		    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
		    		<?='<p>SMS page generated in '.$total_time.' seconds.</p>'."\n";?>
		    	</td>
	</tr>
</table>
    </tr>
</table>
<? } ?>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>