<?
$root = $_SERVER["DOCUMENT_ROOT"];
include ("$root/include.php");
require_once ("sale.inc.php");
$obj = new sale();
$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date = $obj->getParameter("end");
$acc_func = $obj->getParameter("acc_func", 0);

$branch_id = $obj->getParameter("branchid", 0);
if ($branch_id == "") {
	$branch_id = 0;
}

$cityid = $obj->getParameter("cityid",false);
$order = $obj->getParameter("order");
$sort= $obj->getParameter("sortby","A &gt Z");

$today = date("Ymd");
$rspdcategory = $obj->getpdcategory($acc_func);
$rs = $obj->getitemsale($branch_id, $begin_date, $end_date, false, $cityid, $order, $sort);
$begindate = $dateobj->convertdate(substr($begin_date, 0, 4) . "-" . substr($begin_date, 4, 2) . "-" . substr($begin_date, 6, 2), "Y-m-d", $sdateformat);
$enddate = $dateobj->convertdate(substr($end_date, 0, 4) . "-" . substr($end_date, 4, 2) . "-" . substr($end_date, 6, 2), "Y-m-d", $sdateformat);
$export = $obj->getParameter("export", false);
if ($export == "PDF" && $chkPageView) {
	require ('convert2pdf.inc.php');
	$pdf = new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . "&export=print&chkrow=40&gmt=".$_SESSION["__gmt"]);
}
if ($export == "Excel") {
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Product Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
	//header("Content-Type: application/vnd.ms-excel");
	//header('Content-Disposition: attachment; filename="Product Report.xls"');
	//echo "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"xmlns:x=\"urn:schemas-microsoft-com:office:excel\"xmlns=\"http://www.w3.org/TR/REC-html40\">";
}
?>

<?
//Get All Branch
        $sql = "select branch_id, branch_name from bl_branchinfo where branch_id<>1 ";
        		if($cityid){$sql .= "and city_id=".$cityid." ";}else
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
<span class="pdffirstpage"/>	
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table <?($export=="Excel")?"x:str":""?> width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="30%"></td><td width="20%"></td>
		<td width="25%"></td><td width="25%"></td>
	</tr>
	<tr>
	    <td class="reporth" width="100%" align="center" colspan="5" >
    		<b><p>Spa Management System</p>
    		Product Report </b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
    		<?=($enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr>
	<tr height="32">
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td style="text-align:right;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td style="text-align:right;padding-right:10px;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Percent</b></td>
	</tr>
<?

$all_percent = 0;
$all_amount = 0;
$all_total = 0;
for ($k = 0; $k < $rspdcategory["rows"]; $k++) {
	for ($i = 0; $i < $rs["rows"]; $i++) {
		if ($rspdcategory[$k]["pd_category_id"] == $rs[$i]["pd_category_id"]) {
			$rs[$i]["amount"] = $rs[$i]["total"] + $rs[$i]["totalsc"] + $rs[$i]["totalvat"];
			if ($rs[$i]["pos_neg_value"]) {
				$all_percent += $rs[$i]["amount"];
			} else {
				$all_percent -= $rs[$i]["amount"];
			}
			$all_amount += $rs[$i]["amount"];
		}
	}
}
$all_total = 0;
$subtotal = 0;
$pdcategorytotal = 0;
for ($k = 0; $k < $rspdcategory["rows"]; $k++) {

	for ($i = 0; $i < $rs["rows"]; $i++) {

		if ($rspdcategory[$k]["pd_category_id"] == $rs[$i]["pd_category_id"]) {

			if ( !isset($rs[$i-1]["pd_category_id"]) || $rspdcategory[$k]["pd_category_id"] != $rs[$i-1]["pd_category_id"]) {
?>
	<tr height="28">
			<td style="padding-left:7px; white-space: nowrap;" colspan="3"><b>Category: <?=$rspdcategory[$k]["pd_category_name"]?></b></td>
	</tr>
<?

			}

			$product["total"] = $rs[$i]["amount"];
			$subtotal += ($product["total"]);
			$all_total += ($product["total"]);

			if ($rs[$i]["pos_neg_value"] == 0) {
				$percent = - $rs[$i]["amount"] * 100 / $all_percent;
			} else {
				$percent = $rs[$i]["amount"] * 100 / $all_percent;
			}
			$pdcategorytotal += $percent;
?>
	<tr height="28">
					<td style="text-align:left;padding-left: 20px;white-space: nowrap;"><?=$rs[$i]["pd_name"]?></td>
					<td align="center"><?=number_format($rs[$i]["qty"],2,".",",")?></td>
					<td align="right">
					<?if(!$export){?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".$rs[$i]["pd_id"]?>)"><?}?>
					<?=number_format($rs[$i]["amount"],2,".",",")?>
					<?if(!$export){?></a><?}?></td>
					<td align="right" style="padding-right:10px;"><?=number_format($percent,2,".",",")." %"?></td>
	</tr>

<?if(!isset($rs[$i+1]["pd_category_id"])||$rspdcategory[$k]["pd_category_id"]!=$rs[$i+1]["pd_category_id"]){	?>
	
	<tr height="32">
					<td colspan="2" align="right" height="20" style="padding-left: 10px; white-space: nowrap;border-top:1px #000000 solid;border-bottom:3px #000000 double;"><b>Total in <?=$rspdcategory[$k]["pd_category_name"]?> : </b></td>
					<td align="right"  style="padding-right:10px; white-space: nowrap;border-top:1px #000000 solid;border-bottom:3px #000000 double;"><b class="style1"><?=number_format($subtotal,2,".",",")?></b></td>
					<td align="right"  style="padding-right:10px; white-space: nowrap;border-top:1px #000000 solid;border-bottom:3px #000000 double;"><b class="style1"><?=number_format($pdcategorytotal,2,".",",")." %"?></b></td>
	</tr>
<?
			$subtotal = 0;
			$pdcategorytotal = 0;
		}

	}
}
}
?>
<!--
				<tr>
					<td colspan="2" align="right" height="20"><b>Total Amount : </b></td>
					<td align="right"><b class="style1"><?=number_format($all_total,2,".",",")?></b></td>
					<td >&nbsp;</td>
				</tr>
-->
 			<tr height="20">
 					<td colspan="5" height="20">&nbsp;</td>
 			</tr>
    <tr height="20">
    	<td width="100%" align="center" colspan="5" ><br>
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>