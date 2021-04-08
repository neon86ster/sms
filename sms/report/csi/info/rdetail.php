<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("csi.inc.php");
$obj = new csi();
$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate = $obj->getParameter("end");

$branch_id = $obj->getParameter("branchid",1);
$today = date("Ymd");
$branch_name = strtolower($obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id"));
$reportname = "Therapist Massage Customer CSI Report Detail";
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"$reportname.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",40);
	$chkpage = ceil($rs["rows"]/$chkrow);
}$rowcnt=0;
// function for initial array for customer comments in therapist massage
function InitT($name,$rscsiv) {
	$a = $name;
	
	for($i=0; $i<$name["rows"]; $i++) {
		if($i) {
			$a["name"][$i] = $name["name"][$i];

			for($j=0; $j<$rscsiv["rows"]; $j++) {
				$a["value"][$i][$rscsiv[$j]["csiv_value"]] = 0;
			}
		}
		else {
			$a["name"][$i] = "Therapist Name";
			$a["value"][$i] = "Number Customer";
		}
	}
	
	$a["rows"]=$i;
	//arr($a);
	return $a;
}

// function for initial array for customer comment rows
function allcsitoArray($init,$rs) {
	$c = $init;
	
	for($i=1; $i<$init["rows"]; $i++) {				
		for($j=0; $j<$rs["rows"]; $j++) {
			++$c["value"][$i][$rs[$j][$init["rowsname"][$i]]];
			++$c["value"][$i]["total"];
		}	
	}		
	//arr($c);		
	return $c;
}

$rs = $obj->getthcsi($begindate,$enddate,$branch_id);
$rscsiv = $obj->getcsivalue();


$name['name'][0] = " ";
$index=1;
$buf=0;

for($i=0; $i<$rs["rows"]; $i++) {
	if(!isset($csi[$rs[$i]["therapist_name"]][$rs[$i]["q_mg"]])){$csi[$rs[$i]["therapist_name"]][$rs[$i]["q_mg"]]="";}
	if(!isset($csi["tt"])){$csi["tt"]=0;}
	if(!isset($rs[$i-1]["therapist_name"])){$rs[$i-1]["therapist_name"]="";}
	//echo "book id: ".$rs[$i]["book_id"].", therapist_name: ".$rs[$i]["therapist_name"].", csi massage: ".$rs[$i]["q_mg"]."<br>";
	++$csi[$rs[$i]["therapist_name"]][$rs[$i]["q_mg"]];
	++$csi["tt"];
		
	if(($rs[$i]["therapist_name"] != $rs[$i-1]["therapist_name"]) && ($i>0)) {		
		++$index;
		$buf=0;
	}
	
	//if($rs[$i]["q_mg"] != 1)
		$csi[$rs[$i]["therapist_name"]]["total"] = ++$buf;
		
	$name['name'][$index] = $rs[$i]["therapist_name"];

	$name['rows'] = $index+1;
}

$mg = InitT($name,$rscsiv);

for($i=0; $i<$rs["rows"]; $i++) {
	$key = 0;
	$key = array_search($rs[$i]["therapist_name"],$mg["name"]);
	if(!isset($mg["value"][$key][$rs[$i]["q_mg"]])){$mg["value"][$key][$rs[$i]["q_mg"]]="";}
	if(!isset($mg["value"][$key]["total"])){$mg["value"][$key]["total"]=0;}
	if(!isset($mg["allcs"])){$mg["allcs"]=0;}
	//echo "book id: ".$rs[$i]["book_id"].", therapist_name: ".$rs[$i]["therapist_name"].", csi massage: ".$rs[$i]["q_mg"]."<br>";
	++$mg["value"][$key][$rs[$i]["q_mg"]];
	++$mg["value"][$key]["total"];
	++$mg["allcs"];	
	
}

$begin_date = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$end_date = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);

?>
<script type="text/javascript" src="/scripts/ajax.js"></script>
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="28%"></td><td width="12%"></td>
		<td width="12%"></td><td width="12%"></td>
		<td width="12%"></td><td width="12%"></td>
		<td width="12%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="7">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begin_date,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($end_date,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Therapist Name</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Excellent</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Good</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Average</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Poor</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No CM</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
	</tr>
		<?
		
	$total["5"] = 0;$total["4"] = 0;
	$total["3"] = 0;$total["2"] = 0;
	$total["1"] = 0;$total["total"] = 0;
	for($i=1; $i<$mg["rows"]; $i++) {
//if(!$chkrow){$chkrow=1;}
if(!isset($chkrow)){$chkrow=1;}
if($rowcnt%$chkrow==0&&$i>1&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="7" >
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
		<td width="28%"></td><td width="12%"></td>
		<td width="12%"></td><td width="12%"></td>
		<td width="12%"></td><td width="12%"></td>
		<td width="12%"></td>
	</tr>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="7" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">	
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Therapist Name</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Excellent</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Good</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Average</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Poor</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No CM</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
	</tr>
<?	
}		
	$rowcnt++;
	if($i%2==0){
		$style="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";
	}else{
		$style="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";
	}
	?>
	
	<?
	if(!isset($mg["value"][$i]["5"])){$mg["value"][$i]["5"]=0;}
	if(!isset($mg["value"][$i]["4"])){$mg["value"][$i]["4"]=0;}
	if(!isset($mg["value"][$i]["3"])){$mg["value"][$i]["3"]=0;}
	if(!isset($mg["value"][$i]["2"])){$mg["value"][$i]["2"]=0;}
	if(!isset($mg["value"][$i]["1"])){$mg["value"][$i]["1"]=0;}
	if(!isset($mg["value"][$i]["total"])){$mg["value"][$i]["total"]=0;}
	?>
	<tr align="center" height="20" <?=$style?>>
		<td width="150" class="report" align="center"><?=$mg["name"][$i]?></td>
		<td class="report" align="center"><?=$mg["value"][$i]["5"]+0?></td>
		<td class="report" align="center"><?=$mg["value"][$i]["4"]+0?></td>
		<td class="report" align="center"><?=$mg["value"][$i]["3"]+0?></td>
		<td class="report" align="center"><?=$mg["value"][$i]["2"]+0?></td>
		<td class="report" align="center"><?=$mg["value"][$i]["1"]+0?></td>
		<td class="report" align="center"><?=$mg["value"][$i]["total"]?></td>
	</tr>
	
	<?
		$total["5"]+=$mg["value"][$i]["5"];
		$total["4"]+=$mg["value"][$i]["4"];
		$total["3"]+=$mg["value"][$i]["3"];
		$total["2"]+=$mg["value"][$i]["2"];
		$total["1"]+=$mg["value"][$i]["1"];
		$total["total"]+=$mg["value"][$i]["total"];
	}
	?>
	
	<tr align="center" height="32">
			<td width="150" style="border-top:1px #000000 solid;border-bottom:3px #000000 double;"><b>Total</b></td>
			<td style="border-top:1px #000000 solid;border-bottom:3px #000000 double;"><b style='color:#ff0000'><?=$total["5"]+0?></b></td>
			<td style="border-top:1px #000000 solid;border-bottom:3px #000000 double;"><b style='color:#ff0000'><?=$total["4"]+0?></b></td>
			<td style="border-top:1px #000000 solid;border-bottom:3px #000000 double;"><b style='color:#ff0000'><?=$total["3"]+0?></b></td>
			<td style="border-top:1px #000000 solid;border-bottom:3px #000000 double;"><b style='color:#ff0000'><?=$total["2"]+0?></b></td>
			<td style="border-top:1px #000000 solid;border-bottom:3px #000000 double;"><b style='color:#ff0000'><?=$total["1"]+0?></b></td>
			<td style="border-top:1px #000000 solid;border-bottom:3px #000000 double;"><b style='color:#ff0000'><?=$total["total"]?></b></td>
	</tr>
	
	<tr>
    	<td width="100%" align="center" colspan="7" ><br>
    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<?if($export=="print"){?>
<script type="text/javascript">
	//window.print();
</script>
<?}?>