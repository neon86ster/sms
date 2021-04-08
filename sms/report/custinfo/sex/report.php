<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("customer.inc.php");
$obj = new customer();
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
$rs = $obj->getcustpersex($begindate,$enddate);
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
$allbranchtype = implode(",",$branchtype);
$allbranch = implode(",",$branch);

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
$malltotal=0;$falltotal=0;
$falldatetotal=array();$falldatetotal=array();
for($i=0; $i<$rscity["rows"]; $i++) {		// start city loop		
	if($obj->getIdToText($rscity[$i]["city_id"],"bl_branchinfo","branch_id","city_id","branch_category_id IN ( $allbranchtype ) and branch_active=1")>0){
		$mallltotal[$i]=0;$fallltotal[$i]=0;
		for($j=0; $j<$rsbranchtype["rows"]; $j++) {	
			if($obj->getIdToText($rsbranchtype[$j]["branch_category_id"],"bl_branchinfo","branch_id","branch_category_id"," branch_id IN ( $allbranch ) and branch_active=1 and city_id=".$rscity[$i]["city_id"])>0){
				for($k=0; $k<$rsbranch["rows"]; $k++) {
					if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
							$total[$k] = 0;$mtotal[$k] = 0;$ftotal[$k]=0;
							for($d=0;$d<$rsdate["rows"];$d++){ // start date total loop
									$mtmp[$k][$d]=$obj->sumeachfield($rs,"mqty",$rsbranch[$k]["branch_id"],$rsdate["begin"][$d],$rsdate["end"][$d]); 
									$ftmp[$k][$d]=$obj->sumeachfield($rs,"fqty",$rsbranch[$k]["branch_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
									$tmp[$k][$d]=$obj->sumeachfield($rs,"qty",$rsbranch[$k]["branch_id"],$rsdate["begin"][$d],$rsdate["end"][$d]);
									$mtotal[$k]+=$mtmp[$k][$d];
									$ftotal[$k]+=$ftmp[$k][$d]; 
									$total[$k]+=$tmp[$k][$d];
							}
					}
				}
			}
			$mallbttotal[$i][$j]=0;$fallbttotal[$i][$j]=0;
			for($d=0;$d<$rsdate["rows"];$d++){// start date total loop
					$mbttotal[$i][$j][$d]=0;$fbttotal[$i][$j][$d]=0;
					for($k=0; $k<$rsbranch["rows"]; $k++) {
						if(!isset($malldatetotal[$d])){$malldatetotal[$d]=0;}
						if(!isset($falldatetotal[$d])){$falldatetotal[$d]=0;}

						if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
							$mbttotal[$i][$j][$d] += $mtmp[$k][$d];
							$mallbttotal[$i][$j] += $mtmp[$k][$d];
							$malldatetotal[$d] += $mtmp[$k][$d];
							$malltotal +=  $mtmp[$k][$d];
							$fbttotal[$i][$j][$d] += $ftmp[$k][$d];
							$fallbttotal[$i][$j] += $ftmp[$k][$d];
							$falldatetotal[$d] += $ftmp[$k][$d];
							$falltotal +=  $ftmp[$k][$d];
						}
					}
			}
			$mallltotal[$i] += $mallbttotal[$i][$j];
			$fallltotal[$i] += $fallbttotal[$i][$j];
		}
	}
}
if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&chkrow=40&gmt=".$_SESSION["__gmt"]);
}
if($export!="Excel"&&$export){
	$chkcolumn=2;		// row column per page
	$alltable=ceil($rsdate["rows"]/$chkcolumn);
	if($column=="Total only"){$alltable=1;}
	$rowcnt=0;
	if($collapse=="Collapse"){$chkrow = $obj->getParameter("chkrow",27);}
	else{$chkrow = $obj->getParameter("chkrow",40);}
}
if($begindate==$enddate){$column="Total only";$rsdate["header"][0]="TOTAL";}	// if begindate = enddate

$reportname = "Gender of Customer";
$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
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
	$allcolumncnt = 4*($datechk["end"][$a]-$datechk["begin"][$a]+1);
	if($column!="Total only"&&$a==$alltable-1){
		$allcolumncnt+=4;
	}
	$columnwidth = 60/$allcolumncnt;
	$firstcolumnwidth = 100-($columnwidth*($allcolumncnt));
?>	<table border="0" cellspacing="0" cellpadding="0" width="100%">
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
			    		<br><br></b></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" colspan="4" style="text-align:center;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;"><b style="text-decoration: underline;">TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
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
			    		<br><br></b></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" colspan="4" style="text-align:center;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;"><b style="text-decoration: underline;">TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
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
			    		<br><br></b></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" colspan="4" style="text-align:center;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;"><b style="text-decoration: underline;">TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
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
			    		<br><br></b></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" colspan="4" style="text-align:center;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;"><b style="text-decoration: underline;">TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
				</tr>
<?	} ?>
						<tr height="22" style="color:#000000;" >
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
							<? 	//$total[$k] = 0;
								for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ // start branch total loop
							?>		
								<?$total = $mtmp[$k][$d]+$ftmp[$k][$d]; $total = ($total)?$total:1;
								$pm = $mtmp[$k][$d]*100/($total);
								$pf = $ftmp[$k][$d]*100/($total); ?>
								<td align="right">
									<?=number_format($mtmp[$k][$d],0,".",",")?>
								</td>
								<td align="right">
									<?=number_format($pm,2,".",",")?>
								</td>
								<td align="right">
									<?=number_format($ftmp[$k][$d],0,".",",")?>
								</td>
								<td align="right">
									<?=number_format($pf,2,".",",")?>
								</td>
								<? } ?>	
								
						<? if($column!="Total only"&&$a==$alltable-1){ ?>
						<?	$total = $mtotal[$k]+$ftotal[$k]; $total = ($total)?$total:1;
							$pm = $mtotal[$k]*100/($total);
							$pf = $ftotal[$k]*100/($total);?>
							<td align="right"><?=number_format(array_sum($mtmp[$k]),0,".",",")?></td>
							<td align="right"><?=number_format($pm,2,".",",")?></td>
							<td align="right"><?=number_format(array_sum($ftmp[$k]),0,".",",")?></td>
							<td align="right"><?=number_format($pf,2,".",",")?></td>
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
			    		<br><br></b></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" colspan="4" style="text-align:center;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;"><b style="text-decoration: underline;">TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
				</tr>
<?	} ?>
			<tr height="28" style="color:#000000;">
				<td style="padding-left: 10px; white-space: nowrap;border-top:1px #000000 solid;"><b>Total in  <?=$rsbranchtype[$j]["branch_category_name"]?> Category: </b></td>
				<? 
				for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){// start date total loop
				?>		
				<?$total = $mbttotal[$i][$j][$d]+$fbttotal[$i][$j][$d]; $total = ($total)?$total:1;
					$pm = $mbttotal[$i][$j][$d]*100/($total);
					$pf = $fbttotal[$i][$j][$d]*100/($total);?>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($mbttotal[$i][$j][$d],0,".",",")?>
					</td>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($pm,2,".",",")?>
					</td>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($fbttotal[$i][$j][$d],0,".",",")?>
					</td>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($pf,2,".",",")?>
					</td>
				<? }  ?>	
				
				<? if($column!="Total only"&&$a==$alltable-1){ ?>
				<? $total = $mallbttotal[$i][$j]+$fallbttotal[$i][$j]; $total = ($total)?$total:1;
					$pm = $mallbttotal[$i][$j]*100/($total);
					$pf = $fallbttotal[$i][$j]*100/($total);?>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($mallbttotal[$i][$j],0,".",",")?>
					</td>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($pm,2,".",",")?>
					</td>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($fallbttotal[$i][$j],0,".",",")?>
					</td>
					<td align="right" style="border-top:1px #000000 solid;">
						<?=number_format($pf,2,".",",")?>
					</td>
				<? } ?>
				
			</tr>
		<?	 	
			} 
		}
		?>
		<?  if($a==$alltable-1){ ?>
		<?  $total = $mallltotal[$i]+$fallltotal[$i]; $total = ($total)?$total:1;
			$pm = $mallltotal[$i]*100/($total);
			$pf = $fallltotal[$i]*100/($total);?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=$allcolumncnt-3?>"><b>Total in <?=$rscity[$i]["city_name"]?></b></td>
			<td align="right">
			<b><?=number_format($mallltotal[$i],0,".",",")?></b>
			</td><td align="right">
			<b><?=number_format($pm,2,".",",")?></b>
			</td><td align="right">
			<b><?=number_format($fallltotal[$i],0,".",",")?></b>
			</td><td align="right">
			<b><?=number_format($pf,2,".",",")?></b>
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
			    		<br><br></b></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" colspan="4" style="text-align:center;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;"><b style="text-decoration: underline;">TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
				</tr>
<?	} ?>
		<tr height="22" style="color:#000000;">
			<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){
?>			
				<?	$total = $mtmp[$k][$d]+$ftmp[$k][$d]; $total = ($total)?$total:1;
					$pm = $mtmp[$k][$d]*100/($total);
					$pf = $ftmp[$k][$d]*100/($total); ?>
				<td align="right">
					<?=number_format($mtmp[$k][$d],0,".",",")?>
				</td>
				<td align="right">
					<?=number_format($pm,2,".",",")?>
				</td>
				<td align="right">
					<?=number_format($ftmp[$k][$d],0,".",",")?>
				</td>
				<td align="right">
					<?=number_format($pf,2,".",",")?>
				</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
				<?	$total = $mtotal[$k]+$ftotal[$k]; $total = ($total)?$total:1;
					$pm = $mtotal[$k]*100/($total);
					$pf = $ftotal[$k]*100/($total);?>
					<td align="right"><?=number_format($mtotal[$k],0,".",",")?></td>
					<td align="right"><?=number_format($pm,2,".",",")?></td>
					<td align="right"><?=number_format($ftotal[$k],0,".",",")?></td>
					<td align="right"><?=number_format($pf,2,".",",")?></td>
			<? } ?>
			
		</tr>
<?
	}
}
else
{
	if($a==0){
		for($k=0; $k<$rsbranch["rows"]; $k++) { // start branch total loop for sort array of total in each branch
			$branchtotal[$rsbranch[$k]["branch_id"]]=$mtotal[$k]+$ftotal[$k];

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
			    		<br><br></b></p>
			    	</td>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$d]?></b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" colspan="4" style="text-align:center;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;"><b style="text-decoration: underline;">TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="35">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Male</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Female</b></td>
					<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>%</b></td>
					<? }?>
				</tr>
<?	} 
?>
		<tr height="22" style="color:#000000;">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpbranchtotal[$k],"bl_branchinfo","branch_name","branch_id")?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){	
				$mtmp[$k][$d]=$obj->sumeachfield($rs,"mqty",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$ftmp[$k][$d]=$obj->sumeachfield($rs,"fqty",$tmpbranchtotal[$k],$rsdate["begin"][$d],$rsdate["end"][$d]);
				$total = $ftmp[$k][$d]+$mtmp[$k][$d]; $total = ($total)?$total:1;
				$pm = $mtmp[$k][$d]*100/($total);
				$pf = $ftmp[$k][$d]*100/($total);
?>		
			<td align="right">
				<?=number_format($mtmp[$k][$d],0,".",",")?>
			</td>
			<td align="right">
				<?=number_format($pm,2,".",",")?>
			</td>
			<td align="right">
				<?=number_format($ftmp[$k][$d],0,".",",")?>
			</td>
			<td align="right">
				<?=number_format($pf,2,".",",")?>
			</td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
			<?	$total = array_sum($mtmp[$k])+array_sum($ftmp[$k]); $total = ($total)?$total:1;
				$pm = array_sum($mtmp[$k])*100/($total);
				$pf = array_sum($ftmp[$k])*100/($total);?>
				<td align="right"><?=number_format(array_sum($mtmp[$k]),0,".",",")?></td>
				<td align="right"><?=number_format($pm,2,".",",")?></td>
				<td align="right"><?=number_format(array_sum($ftmp[$k]),0,".",",")?></td>
				<td align="right"><?=number_format($pf,2,".",",")?></td>
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
		$mallindate = $malldatetotal[$d];
		$fallindate = $falldatetotal[$d];
		$total = $mallindate+$fallindate; $total = ($total)?$total:1;
		$pm = $mallindate*100/($total);
		$pf = $fallindate*100/($total);
		
?>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3">
			<b><?=number_format($mallindate,0,".",",")?></b>
		</td>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3">
			<b><?=number_format($pm,2,".",",")?></b>
		</td>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3">
			<b><?=number_format($fallindate,0,".",",")?></b>
		</td>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3">
			<b><?=number_format($pf,2,".",",")?></b>
		</td>
<?
}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ 
					$total = $malltotal+$falltotal; $total = ($total)?$total:1;
					$pm = $malltotal*100/($total);
					$pf = $falltotal*100/($total); ?>				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=number_format($malltotal,0,".",",")?>
			</b></td>			
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=number_format($pm,2,".",",")?>
			</b></td>			
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=number_format($falltotal,0,".",",")?>
			</b></td>			
			<td align="right" bgcolor="#d3d3d3"><b>
			<?=number_format($pf,2,".",",")?>
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
    	<td class="content" width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0">
			<tr>
		    	<td class="reporth" align="center" colspan="<?=($column=="Total only")?4*($rsdate["rows"]+1):4*($rsdate["rows"]+1)+1 ?>">
		    		<b><p>Spa Management System</p>
		    		<?=$reportname?></b><br>
		    		<p class="style1">
		    		<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
		    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
		    		</p>
		    	</td>
			</tr>
				<tr height="32">
					<td style="text-align:left; border-top:2px #000000 solid;"><b>&nbsp;</b></td>
					<? for($i=0;$i<$rsdate["rows"];$i++){ ?>
						<td colspan="4" style="text-align:center;padding-right:12px;white-space: nowrap; border-top:2px #000000 solid;"><b style="text-decoration: underline;"><?=$rsdate["header"][$i]?></b></td>
					<? }  ?>
					<? if($column!="Total only"){?>
					<td colspan="4" style="text-align:center;padding-left:10px;overflow:hidden; border-top:2px #000000 solid;"><b>TOTAL</b></td>
					<? }?>
				</tr>
				<tr height="30">
					<td width="90" style="text-align:left; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>&nbsp;</b></td>
					<? for($i=0;$i<$rsdate["rows"];$i++){ ?>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">Male</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">%</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">Female</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">%</b></td>
					<? }  ?>
					<? if($column!="Total only"){?>
						<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">Male</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">%</b></td>
						<td width="40" style="text-align:right;padding-left:10px;overflow:hidden; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">Female</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b style="text-decoration: underline;">%</b></td>
					<? }?>
				</tr>
<?
if($collapse=="Collapse"){	//check Collapse/Expand loop
	
for($i=0; $i<$rscity["rows"]; $i++) {		// start city loop		
$allbranchtype = implode(",",$branchtype);
if($obj->getIdToText($rscity[$i]["city_id"],"bl_branchinfo","branch_id","city_id","branch_category_id IN ( $allbranchtype ) and branch_active=1")>0){
?>
<tr height="32">
	<td style="padding-left:7px; white-space: nowrap; border-bottom:3px #d0d0d0 double;" bgcolor="#d0d0d0"><b>Location: <?=$rscity[$i]["city_name"]?></b></td>
	<td colspan="<?=($column=="Total only")?4*$rsdate["rows"]:4*($rsdate["rows"]+1)?>" bgcolor="#d0d0d0" style="border-bottom:3px #d0d0d0 double;">&nbsp;</td><!-- input city -->
</tr>
<?
		for($j=0; $j<$rsbranchtype["rows"]; $j++) {		// start branch category loop
			$allbranch = implode(",",$branch);
			if($obj->getIdToText($rsbranchtype[$j]["branch_category_id"],"bl_branchinfo","branch_id","branch_category_id"," branch_id IN ( $allbranch ) and branch_active=1 and city_id=".$rscity[$i]["city_id"])>0){
			?>
			<tr height="28">
				<td style="padding-left: 20px; white-space: nowrap;"><b>Category: <?=$rsbranchtype[$j]["branch_category_name"]?></b></td>
				<td colspan="<?=($column=="Total only")?4*$rsdate["rows"]:4*($rsdate["rows"]+1)?>" style="padding-left: 20px;">&nbsp;</td>
			</tr>
					<?
					for($k=0; $k<$rsbranch["rows"]; $k++) { 	// start branch name loop
						if($rsbranch[$k]["branch_category_id"]==$rsbranchtype[$j]["branch_category_id"]&&$rsbranch[$k]["city_id"]==$rscity[$i]["city_id"]&&$rsbranch[$k]["branch_active"]==1){
						?>
						<tr height="22" >
							<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
							<? 	
								for($d=0;$d<$rsdate["rows"];$d++){ // start branch total loop
								$total = $mtmp[$k][$d]+$ftmp[$k][$d]; $total = ($total)?$total:1;
								$pm = $mtmp[$k][$d]*100/$total;
								$pf = $ftmp[$k][$d]*100/$total;
							?>		
								<td align="right">
									<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><? } ?>
									<?=number_format($mtmp[$k][$d],0,".",",")?><?if($export==false){?></a><? } ?>
								</td>
								<td align="right">
									<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><? } ?>
									<?=number_format($pm,2,".",",")?><?if($export==false){?></a><? } ?>
								</td>
								<td align="right">
									<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><? } ?>
									<?=number_format($ftmp[$k][$d],0,".",",")?><?if($export==false){?></a><? } ?>
								</td>
								<td align="right">
									<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><? } ?>
									<?=number_format($pf,2,".",",")?><?if($export==false){?></a><? } ?>
								</td>
								<? 
								} ?>	
							<? 
							   if($column!="Total only"){ 
								$total = $mtotal[$k]+$ftotal[$k]; $total = ($total)?$total:1;
								$pm = $mtotal[$k]*100/$total;
								$pf = $ftotal[$k]*100/$total;
							?>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
							<?= number_format($mtotal[$k],0,".",",")?>
							<?if($export==false){?></a><?}?>
							</td>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
							<?= number_format($pm,2,".",",")?>
							<?if($export==false){?></a><?}?>
							</td>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
							<?= number_format($ftotal[$k],0,".",",")?>
							<?if($export==false){?></a><?}?>
							</td>
							<td align="right">
							<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
							<?= number_format($pf,2,".",",")?>
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
				for($d=0;$d<$rsdate["rows"];$d++){// start date total loop
					$total = $mbttotal[$i][$j][$d]+$fbttotal[$i][$j][$d]; $total = ($total)?$total:1;
					$pm = $mbttotal[$i][$j][$d]*100/$total;
					$pf = $fbttotal[$i][$j][$d]*100/$total;
				?>		
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
				<?=number_format($mbttotal[$i][$j][$d],0,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
				<?= number_format($pm,2,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
				<?=number_format($fbttotal[$i][$j][$d],0,".",",")?>
				<?if($export==false){?></a><?}?>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
				<?= number_format($pf,2,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<? } ?>	
				<? if($column!="Total only"){ 
					$total = $mallbttotal[$i][$j]+$fallbttotal[$i][$j]; $total = ($total)?$total:1;
					$pm = $mallbttotal[$i][$j]*100/$total;
					$pf = $fallbttotal[$i][$j]*100/$total;
				?>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
				<?=number_format($mallbttotal[$i][$j],0,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
				<?= number_format($pm,2,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
				<?=number_format($fallbttotal[$i][$j],0,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<td align="right" style="border-top:1px #000000 solid;">
				<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",".$rsbranchtype[$j]["branch_category_id"]?>)"><?}?>
				<?= number_format($pf,2,".",",")?>
				<?if($export==false){?></a><?}?>
				</td>
				<? } ?>
			</tr>
		<?	 	
			} 
		}
		$total = $mallltotal[$i]+$fallltotal[$i]; $total = ($total)?$total:1;
		$pm = $mallltotal[$i]*100/$total;
		$pf = $fallltotal[$i]*100/$total;
		?>
		<tr height="35">
			<td style="padding-left: 20px; white-space: nowrap;" align="right" colspan="<?=($column=="Total only")?$rsdate["rows"]:4*$rsdate["rows"]+1?>"><b>Total in <?=$rscity[$i]["city_name"]?></b></td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",0"?>)"><?}?>
			<b><?=number_format($mallltotal[$i],0,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",0"?>)"><?}?>
			<b><?= number_format($pm,2,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",0"?>)"><?}?>
			<b><?=number_format($fallltotal[$i],0,".",",")?></b>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,".$rscity[$i]["city_id"].",0"?>)"><?}?>
			<b><?= number_format($pf,2,".",",")?></b>
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
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$rsbranch[$k]["branch_name"]?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
				$total = $mtmp[$k][$d]+$ftmp[$k][$d]; $total = ($total)?$total:1;
				$pm = $mtmp[$k][$d]*100/$total;
				$pf = $ftmp[$k][$d]*100/$total;
							
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",0,0"?>)"><?}?>
			<?=number_format($mtmp[$k][$d],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",0,0"?>)"><?}?>
			<?=number_format($pm,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",0,0"?>)"><?}?>
			<?=number_format($ftmp[$k][$d],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$rsbranch[$k]["branch_id"].",0,0"?>)"><?}?>
			<?=number_format($pf,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){
					$total = $mtotal[$k]+$ftotal[$k]; $total = ($total)?$total:1;
					$pm = $mtotal[$k]*100/$total;
					$pf = $ftotal[$k]*100/$total;
			 ?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",0,0"?>)"><?}?>
			<?= number_format($mtotal[$k],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",0,0"?>)"><?}?>
			<?= number_format($pm,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",0,0"?>)"><?}?>
			<?= number_format($ftotal[$k],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$rsbranch[$k]["branch_id"].",0,0"?>)"><?}?>
			<?= number_format($pf,2,".",",")?>
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
			for($d=0;$d<$rsdate["rows"];$d++){
				$branchtotal[$rsbranch[$k]["branch_id"]]=$total[$k];
			}
			if($sort=="A > Z"){arsort($branchtotal);}
			else{asort($branchtotal);}
		}
		$k=0;	// resorting branch id to new array for show in report
		foreach ($branchtotal as $key => $val) {
  			  $tmpbranchtotal[$k][0] = $key;
  			  $total[$k] = $val;
  			  $mtotal[$k] =0;$ftotal[$k]=0;
  			  for($d=0;$d<$rsdate["rows"];$d++){
  			  		$mtmp[$k][$d]=$obj->sumeachfield($rs,"mqty",$tmpbranchtotal[$k][0],$rsdate["begin"][$d],$rsdate["end"][$d]); 
  			  		$ftmp[$k][$d]=$obj->sumeachfield($rs,"fqty",$tmpbranchtotal[$k][0],$rsdate["begin"][$d],$rsdate["end"][$d]);
  			  		$mtotal[$k] += $mtmp[$k][$d];
  			  		$ftotal[$k] += $ftmp[$k][$d];
  			  } 
  			  $k++;
		}
		
		for($k=0; $k<$rsbranch["rows"]; $k++) {
?>
		<tr height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$obj->getIdToText($tmpbranchtotal[$k][0],"bl_branchinfo","branch_name","branch_id")?></td>
<?
			for($d=0;$d<$rsdate["rows"];$d++){
				$total = $mtmp[$k][$d]+$ftmp[$k][$d]; $total = ($total)?$total:1;
				$pm = $mtmp[$k][$d]*100/$total;
				$pf = $ftmp[$k][$d]*100/$total;
?>		
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k][0].",0,0"?>)"><?}?>
			<?= number_format($mtmp[$k][$d],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k][0].",0,0"?>)"><?}?>
			<?= number_format($pm,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k][0].",0,0"?>)"><?}?>
			<?= number_format($ftmp[$k][$d],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",".$tmpbranchtotal[$k][0].",0,0"?>)"><?}?>
			<?= number_format($pf,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
<?
			}
?>
			<? if($column!="Total only"){ 
					$total = $mtotal[$k]+$ftotal[$k]; $total = ($total)?$total:1;
					$pm = $mtotal[$k]*100/$total;
					$pf = $ftotal[$k]*100/$total;
			?>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k][0].",0,0"?>)"><?}?>
			<?= number_format($mtotal[$k],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k][0].",0,0"?>)"><?}?>
			<?= number_format($pm,2,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k][0].",0,0"?>)"><?}?>
			<?= number_format($ftotal[$k],0,".",",")?>
			<?if($export==false){?></a><?}?>
			</td>
			<td align="right">
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",".$tmpbranchtotal[$k][0].",0,0"?>)"><?}?>
			<?= number_format($pf,2,".",",")?>
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
		$total = $malldatetotal[$d]+$falldatetotal[$d]; $total = ($total)?$total:1;
		$pm = $malldatetotal[$d]*100/$total;
		$pf = $falldatetotal[$d]*100/$total;
?>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>
		<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0,0"?>)"><?}?>
			<?=number_format($malldatetotal[$d],0,".",",")?>
		<?if($export==false){?></a><?}?>
		</b></td>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>
		<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0,0"?>)"><?}?>
			<?= number_format($pm,2,".",",")?>
		<?if($export==false){?></a><?}?>
		</b></td>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>
		<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0,0"?>)"><?}?>
			<?=number_format($falldatetotal[$d],0,".",",")?>
		<?if($export==false){?></a><?}?>
		</b></td>
		<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>
		<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][$d].",".$rsdate["end"][$d].",0,0,0"?>)"><?}?>
			<?= number_format($pf,2,".",",")?>
		<?if($export==false){?></a><?}?>
		</b></td>
<?
}
?>
			<? if($column!="Total only"){ 
				$total = $malltotal+$falltotal; $total = ($total)?$total:1;
				$pm = $malltotal*100/$total;
				$pf = $falltotal*100/$total;
			?>
				
			<td align="right" bgcolor="#d3d3d3"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0,0"?>)"><?}?>
			<?=number_format($malltotal,0,".",",")?><?if($export==false){?></a><?}?></b></td>
			<td align="right" bgcolor="#d3d3d3"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0,0"?>)"><?}?>
			<?=number_format($pm,2,".",",")?><?if($export==false){?></a><?}?></b></td>
			<td align="right" bgcolor="#d3d3d3"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0,0"?>)"><?}?>
			<?=number_format($falltotal,0,".",",")?><?if($export==false){?></a><?}?></b></td>
			<td align="right" bgcolor="#d3d3d3"><b>
			<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openSexDetail(<?=$rsdate["begin"][0].",".$rsdate["end"][$d-1].",0,0,0"?>)"><?}?>
			<?=number_format($pf,2,".",",")?><?if($export==false){?></a><?}?></b></td>
			
			<? } ?>
			</tr>
			<tr height="40">
				<td align="center" colspan="<?= ($column=="Total only")?4*$rsdate["rows"]+1:4*($rsdate["rows"]+1)+1 ?>">&nbsp;</td></tr>
			</tr>
		    <tr>
		    	<td align="center" colspan="<?=($column=="Total only")?4*$rsdate["rows"]+1:4*($rsdate["rows"]+1)+1?>">
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