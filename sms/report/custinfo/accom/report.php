<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("customer.inc.php");
$obj = new customer();
$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate= $obj->getParameter("end");
$collapse = $obj->getParameter("Collapse","Collapse");

$search = $obj->getParameter("search");
$anotherpara = "and al_accomodations.acc_name like '%".$search."%'";
$order = $obj->getParameter("order");
$sort= $obj->getParameter("sortby","A &gt Z");
$column= $obj->getParameter("column","Total only");
$rsdate = $obj->getdatecol($column,$begindate,$enddate);

$total_b=0;
$total_c=0;
$sum_b=array();
$sum_c=array();

if($collapse=="Collapse"){
	$column="Total only";
}

$cmschk = $obj->getParameter("commission",false);
if($cmschk==""){$cmschk=false;}
$branch = $obj->getParameter("branchid",false);
$city_id = $obj->getParameter("cityid",false);
if($city_id==""){$city_id=0;}
$today = date("Ymd");
$rs = $obj->getcusperacc($city_id,$begindate,$enddate,$cmschk,$collapse,0,$branch,$anotherpara,$order,$sort);
$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Customer Accomodations Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
$chkrow = 1;
$rowcnt=0;
if($export!="Excel"&&$export){
	$chkcolumn=2;		// row column per page
	$alltable=ceil($rsdate["rows"]/$chkcolumn);
	
	$chkrow = $obj->getParameter("chkrow",35);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
$cityname = $obj->getIdToText($city_id,"al_city","city_name","city_id");
$reportname = "$cityname Customer Accomodations Report";
?>
<?
//Get All Branch
        $sql = "select branch_id, branch_name from bl_branchinfo where branch_id<>1 ";
        		if($city_id){$sql .= "and city_id=".$city_id." ";}else
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

<script type="text/javascript" src="../scripts/ajax.js"></script>
<?if($export!="Excel"){?>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<?}?>
<span class="pdffirstpage"/>
	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style=<?=($collapse!="Collapse")?"padding:40 20 50 20;":"padding:10 20 50 20;"?> width="100%" align="center">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
<?

if($column!="Total only"){
	
	if($export!=false&&$export!="Excel"){ // begin check export function 
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
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>B</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;">%</td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>C</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;">%</td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>B</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;">%</td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>C</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;">%</td>
					<? }?>
				</tr>
<?

	if($a==0){
		for($i=0; $i<$rs["rows"]; $i++) {
		$total_b=$total_b+$rs[$i]["cntbook"];
		$total_c=$total_c+$rs[$i]["qty_pp"];
		}
	}
for($i=0; $i<$rs["rows"]; $i++) {
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
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>B</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;">%</td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>C</b></td>
						<td width="40" style="text-align:right;padding-right:12px;white-space: nowrap; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;">%</td>
					<? }  ?>
					<? if($column!="Total only"&&$a==$alltable-1){?>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>B</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;">%</td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>C</b></td>
					<td width="40" style="text-align:right;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;">%</td>
					<? }?>
				</tr>
	<?
}

$bgcolor="";
if($i%2!=0){$bgcolor="bgcolor=\"#eaeaea\"";}
if(!$export){
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
}
if($export!=false){
	$accname = $rs[$i]["acc_name"];
}else{
	$accname = $obj->hightLightChar($search,$rs[$i]["acc_name"]);
}

?>	
		<tr <?=$bgcolor?> height="22">
			<td style="padding-left:35px; white-space: nowrap;"><?=$accname?></td>
<?
			for($d=$datechk["begin"][$a];$d<=$datechk["end"][$a];$d++){	?>
<?
$startdate = substr($rsdate["begin"][$d],0,4)."-".substr($rsdate["begin"][$d],4,2)."-".substr($rsdate["begin"][$d],6,2);
$enddate = substr($rsdate["end"][$d],0,4)."-".substr($rsdate["end"][$d],4,2)."-".substr($rsdate["end"][$d],6,2);

$sql_acc = "select count(book_id) as cnt_b,sum(b_qty_people) as cnt_c from a_bookinginfo where a_bookinginfo.b_accomodations_id=".$rs[$i]["acc_id"]." ";
		if($rsdate["end"][$d]==false||$rsdate["begin"][$d]==$rsdate["end"][$d]){$sql_acc .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql_acc .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($city_id){$sql_acc .= "and a_bookinginfo.b_branch_id in (select branch_id from bl_branchinfo where city_id=$city_id) ";}
		if($branch){$sql_acc .= "and a_bookinginfo.b_branch_id=".$branch." ";}
	    $sql_acc .= "and a_bookinginfo.b_set_cancel=0 ";
		$sql_acc .= "group by a_bookinginfo.b_accomodations_id ";
$rs_acc = $obj->getResult($sql_acc);

$rs_sum = $obj->getcusperacc($city_id,$rsdate["begin"][$d],$rsdate["end"][$d],$cmschk,$collapse,0,$branch,$anotherpara,$order,$sort);
if(!isset($sum_b[$d])){$sum_b[$d]=0;}
if(!isset($sum_c[$d])){$sum_c[$d]=0;}
if(!$sum_b[$d] && !$sum_c[$d]){
	for($k=0; $k<$rs_sum["rows"]; $k++){
		$sum_b[$d]=$sum_b[$d]+$rs_sum[$k]["cntbook"];
		$sum_c[$d]=$sum_c[$d]+$rs_sum[$k]["qty_pp"];
	}
}

?>	
			<td class="report" align="right"><b><?=($rs_acc[0]["cnt_b"])?$rs_acc[0]["cnt_b"]:0?></b></td>
			<td class="report" align="right"><?=($rs_acc[0]["cnt_b"])?number_format((($rs_acc[0]["cnt_b"]*100)/$sum_b[$d]),2,".",","):0?></td>
			<td class="report" align="right"><b><?=($rs_acc[0]["cnt_c"])?$rs_acc[0]["cnt_c"]:0?></b></td>
			<td class="report" align="right"><?=($rs_acc[0]["cnt_c"])?number_format((($rs_acc[0]["cnt_c"]*100)/$sum_c[$d]),2,".",","):0?></td>
<?
			}
?>
			<? if($column!="Total only"&&$a==$alltable-1){ ?>
				<td class="report" align="right"><b><?=$rs[$i]["cntbook"]?></b></td>
				<td class="report" align="right"><?=number_format((($rs[$i]["cntbook"]*100)/$total_b),2,".",",")?></td>
				<td class="report" align="right"><b><?=$rs[$i]["qty_pp"]?></b></td>
				<td class="report" align="right"><?=number_format((($rs[$i]["qty_pp"]*100)/$total_c),2,".",",")?></td>
			<? } ?>
					
		</tr>
<?
	}
?>
		
    <tr height="20">
    	<td align="center" colspan="<?=$allcolumncnt+1?>">
    		<br><b>Printed:<?=$allcolumncnt+1?> </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
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
						<td style="text-align:center;padding-right:12px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>B</b></td>
						<td style="text-align:center;padding-right:12px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;">%</td>
						<td style="text-align:center;padding-right:12px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>C</b></td>
						<td style="text-align:center;padding-right:12px; border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;">%</td>
					<? }  ?>
					<? if($column!="Total only"){?>
					<td style="text-align:center;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>B</b></td>
					<td style="text-align:center;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;">%</td>
					<td style="text-align:center;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;"><b>C</b></td>
					<td style="text-align:center;padding-left:10px;border-top:1px #000000 solid;border-bottom:2px #ff0000 solid;">%</td>
					<? }?>
				</tr>
<?

for($i=0; $i<$rs["rows"]; $i++) {
$total_b=$total_b+$rs[$i]["cntbook"];
$total_c=$total_c+$rs[$i]["qty_pp"];
}

for($i=0; $i<$rs["rows"]; $i++) {
	
$bgcolor="";
if($i%2!=0){$bgcolor="bgcolor=\"#eaeaea\"";}
if(!$export){
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
}
if($export!=false){
	$accname = $rs[$i]["acc_name"];
}else{
	$accname = $obj->hightLightChar($search,$rs[$i]["acc_name"]);
}
?>				
		<tr <?=$bgcolor?> height="32">
			<td class="report" align="left"><?=$accname?></td>
					<? for($d=0;$d<$rsdate["rows"];$d++){ ?>
<?
$startdate = substr($rsdate["begin"][$d],0,4)."-".substr($rsdate["begin"][$d],4,2)."-".substr($rsdate["begin"][$d],6,2);
$enddate = substr($rsdate["end"][$d],0,4)."-".substr($rsdate["end"][$d],4,2)."-".substr($rsdate["end"][$d],6,2);

$sql_acc = "select count(book_id) as cnt_b,sum(b_qty_people) as cnt_c from a_bookinginfo where a_bookinginfo.b_accomodations_id=".$rs[$i]["acc_id"]." ";
		if($rsdate["end"][$d]==false||$rsdate["begin"][$d]==$rsdate["end"][$d]){$sql_acc .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql_acc .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($city_id){$sql_acc .= "and a_bookinginfo.b_branch_id in (select branch_id from bl_branchinfo where city_id=$city_id) ";}
		if($branch){$sql_acc .= "and a_bookinginfo.b_branch_id=".$branch." ";}
	    $sql_acc .= "and a_bookinginfo.b_set_cancel=0 ";
		$sql_acc .= "group by a_bookinginfo.b_accomodations_id ";
$rs_acc = $obj->getResult($sql_acc);

$rs_sum = $obj->getcusperacc($city_id,$rsdate["begin"][$d],$rsdate["end"][$d],$cmschk,$collapse,0,$branch,$anotherpara,$order,$sort);
if(!isset($sum_b[$d])){$sum_b[$d]=0;}
if(!isset($sum_c[$d])){$sum_c[$d]=0;}
if(!$sum_b[$d] && !$sum_c[$d]){
	for($k=0; $k<$rs_sum["rows"]; $k++){
		$sum_b[$d]=$sum_b[$d]+$rs_sum[$k]["cntbook"];
		$sum_c[$d]=$sum_c[$d]+$rs_sum[$k]["qty_pp"];
	}
}

?>
						<td class="report" align="right"><b><?=($rs_acc[0]["cnt_b"])?$rs_acc[0]["cnt_b"]:0?></b></td>
						<td class="report" align="right"><?=($rs_acc[0]["cnt_b"])?number_format((($rs_acc[0]["cnt_b"]*100)/$sum_b[$d]),2,".",","):0?></td>
						<td class="report" align="right"><b><?=($rs_acc[0]["cnt_c"])?$rs_acc[0]["cnt_c"]:0?></b></td>
						<td class="report" align="right"><?=($rs_acc[0]["cnt_c"])?number_format((($rs_acc[0]["cnt_c"]*100)/$sum_c[$d]),2,".",","):0?></td>
					<? }  ?>
					<td class="report" align="right"><b><?=$rs[$i]["cntbook"]?></b></td>
					<td class="report" align="right"><?=number_format((($rs[$i]["cntbook"]*100)/$total_b),2,".",",")?></td>
					<td class="report" align="right"><b><?=$rs[$i]["qty_pp"]?></b></td>
					<td class="report" align="right"><?=number_format((($rs[$i]["qty_pp"]*100)/$total_c),2,".",",")?></td>	
		</tr>
<?
	}
?>
		
 		</table><br>
		</td>
    </tr>

	<tr>
		    	<td align="center">
		    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
		    	</td>
	</tr>
</table>
    </tr>
</table>
<? }?>
<?
}else{
////////////
?>	
<? if($collapse=="Collapse"){ ?>
	<tr>
		<td width="10%"></td><td width="10%"></td>
		<td width="10%"></td><td width="25%"></td>
		<td width="25%"></td><td width="10%"></td>
		<td width="10%"></td>
	</tr>
<? }else{ ?>
	<tr>
		<td width="35%"></td><td width="30%"></td>
		<td width="35%"></td>
	</tr>
<? } ?>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="<?=($collapse=="Collapse")?"7":"3"?>" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate=="")?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
<? if($collapse=="Collapse"){ ?>
	
				<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. People</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
				</tr>
<?
$bookcnt=0;$totalbookcnt=0;
$totalqty=0;$eachqty=0;
$rowcnt=0;
for($i=0; $i<$rs["rows"]; $i++) {
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"7":"3"?>" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="10%"></td><td width="10%"></td>
		<td width="10%"></td><td width="25%"></td>
		<td width="25%"></td><td width="10%"></td>
		<td width="10%"></td>
	</tr>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="7" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate=="")?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. People</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
	</tr>
<?	
}	
	
$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
$pagename = "manageBooking".$rs[$i]["book_id"];
$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");

$bookcnt++;$totalbookcnt++;$rowcnt++;
$eachqty+=$rs[$i]["qty_pp"];$totalqty+=$rs[$i]["qty_pp"];
if($export!=false){
	$id=$bpdsid;
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$bpdsid."</a>";
}
if($rs[$i]["has_cms"]==1){
	$hascms = "<span style=\"color:#ff0000\">Yes</span>";
}else{
	$hascms = "<span>No</span>";}
?>
			<tr height="20"  class="even" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#eaeaea'">
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=$rs[$i]["branch_name"]?></td>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:10px"><?=$rs[$i]["cms_company"]?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:10px"><?=$rs[$i]["cms_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["qty_pp"]?>&nbsp;</td>		
					<td class="report" align="center"><?=$hascms?>&nbsp;</td>	
			</tr>
<?
	if(!isset($rs[$i+1]["acc_name"])){$rs[$i+1]["acc_name"]="";}
	if($rs[$i]["acc_name"]!=$rs[$i+1]["acc_name"]){
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"7":"3"?>" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="10%"></td><td width="10%"></td>
		<td width="10%"></td><td width="25%"></td>
		<td width="25%"></td><td width="10%"></td>
		<td width="10%"></td>
	</tr>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="7" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate=="")?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. People</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
	</tr>
<?	
}	
		$rowcnt++;
	?>
			<tr bgcolor="#eaeaea" class="odd" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#eaeaea'">
					<td class="report" align="left" colspan="4"><b><?=$rs[$i]["acc_name"]?></b></td>
					<td class="report" align="center"><b><?=$bookcnt?>&nbsp;bookings</b></td>
					<td class="report" align="center"><b><?=$eachqty?>&nbsp;persons</b></td>	
					<td class="report" align="center"><b>&nbsp;</b></td>		
			</tr>
	<?
		$bookcnt=0;$eachqty=0;
	}
}
?>
 			<tr height="20">
 					<td colspan="7" >&nbsp;</td>
 			</tr>
			<tr height="20">
					<td align="left" colspan="3">&nbsp; </td>
					<td align="right"><b>Total : &nbsp;</b></td>
					<td align="center"><b style='color:#ff0000'><?=$totalbookcnt?>&nbsp;</b><b>bookings</b></td>	
					<td align="center"><b style='color:#ff0000'><?=number_format($totalqty,0,".",",")?> </b><b>persons</b></td>
					<td align="center"><b>&nbsp;</b></td>	
			</tr>
<?}else{?>
				<tr height="32">
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Accommodation</b></td>		
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Bookings</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Customers</b></td>
				</tr>
<?
$bookcnt=0;$totalbookcnt=0;
$totalqty=0;$eachqty=0;
$rowcnt=0;
for($i=0; $i<$rs["rows"]; $i++) {
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"7":"3"?>" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />		
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="35%"></td><td width="30%"></td>
		<td width="35%"></td>
	</tr>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="3" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate=="")?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Accommodation</b></td>		
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Bookings</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Customers</b></td>
	</tr>
<?	
}		
	
$bookcnt++;$totalbookcnt+=$rs[$i]["cntbook"];
$eachqty+=$rs[$i]["qty_pp"];$totalqty+=$rs[$i]["qty_pp"];
$bgcolor="";
//if($category!="Category" && $i%2!=0){$bgcolor="bgcolor=\"#eaeaea\"";}else{$cbgcolor="bgcolor=\"#eaeaea\"";}
//if($category!="Category" && !$export){
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
//}
if($export!=false){
	$accname = $rs[$i]["acc_name"];
}else{
	$accname = $obj->hightLightChar($search,$rs[$i]["acc_name"]);
}
	?>
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="left"><?=$rs[$i]["acc_name"]?></td>
					<td class="report" align="center"><?=$rs[$i]["cntbook"]?></td>
					<td class="report" align="center"><?=$rs[$i]["qty_pp"]?></td>		
			</tr>
	<?
		$bookcnt=0;$eachqty=0;$rowcnt++;

}?>
 			<tr height="20">
 					<td colspan="3">&nbsp;</td>
 			</tr>
			<tr height="20">
					<td align="center"><b style='color:#ff0000'>Total : </b></td>
					<td align="center"><b style='color:#ff0000'><?=$totalbookcnt?>&nbsp;</b><b>bookings</b></td>	
					<td align="center"><b style='color:#ff0000'><?=number_format($totalqty,0,".",",")?> </b><b>persons</b></td>
			</tr>
<? 
}?>
<?}
////////////
?>
<?if($column=="Total only"){?>
    <tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"7":"3"?>" ><br>
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<?}?>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>