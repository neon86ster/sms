<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
require_once("csi.inc.php");
$robj = new report();
$obj = new csi();

$begindate = $obj->getParameter("begin");
$enddate = $obj->getParameter("end");

$branch_id = $obj->getParameter("branchid",0);
$cityid = $obj->getParameter("cityid",false);
$today = date("Ymd");
$branch_name = strtolower($obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id"));
/*
if($branch_id){
	$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")."'s Customer CSI Report";
}else{
	$reportname = "All Customer CSI Report";
}*/
$reportname = "Customer CSI Report";
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
	//$pdf->convertFromFile("manage_cplinfo3.htm");
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&chkrow=55&gmt=".$_SESSION["__gmt"]);
}
if($export!="Excel"&&$export){
	if(!isset($rs["rows"])){$rs["rows"]=0;}
	$chkrow = $obj->getParameter("chkrow",30);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
// function for initial array for customer comment rows
function InitA($rscsiv,$rscsii) {
	$tmp = array();
	$db = array();
	$n = array();
	for($i=0; $i<$rscsii["rows"]; $i++){
		$db[$i] = $rscsii[$i]["csii_column_name"];
		$n[$i] = $rscsii[$i]["csii_name"];
	}
	//$db = array("at_fac","at_temp","at_m","at_aroma","at_clean","q_value","q_tr","q_mg","s_driver","s_friendly","s_greeting","s_attentive","s_manner");
	//$n = array("Facilities","Temperature","Music","Aroma","Cleanlines","Value of money","Body Treatments","Massage","Driver","Friendly & Cheerful","Greeting","Attentiveness","Manner / Courtesy");
	
	for($i=0; $i<=count($n); $i++) {
		if($i) {
			$tmp["rowsname"][$i] = $db[$i-1];		// set colume name from database
			$tmp["formname"][$i] = $n[$i-1];		// set table 1st row header 
			for($j=0; $j<$rscsiv["rows"]; $j++) {
				$tmp["value"][$i][$rscsiv[$j]["csiv_value"]] = 0;	// initial csi value to zero
			}
		}
		else {
			$tmp["formname"][$i] = " ";				// 1st rows(header) in show table
			$tmp["percent"][$i] = "CSI Percent(%)";	
		}
	}
	
	$tmp["rows"]=$i;
	//arr($a);
	return $tmp;
}

// function for initial array for customer comment rows
function allcsitoArray($init,$rs) {
	$c = $init;
	
	for($i=1; $i<$init["rows"]; $i++) {				
		for($j=0; $j<$rs["rows"]; $j++) {
			if(!isset($c["value"][$i][$rs[$j][$init["rowsname"][$i]]])){$c["value"][$i][$rs[$j][$init["rowsname"][$i]]]=0;}
			if(!isset($c["value"][$i]["total"])){$c["value"][$i]["total"]=0;}
			++$c["value"][$i][$rs[$j][$init["rowsname"][$i]]];
			++$c["value"][$i]["total"];
		}	
	}		
	//arr($c);		
	return $c;
}

$rs = $obj->getcsinfo($begindate,$enddate,$branch_id,false,$cityid);
$rscsiv = $obj->getcsivalue();
$rscsii = $obj->getcsiindex();
$init = InitA($rscsiv,$rscsii);

//print_r($init);
$csi = allcsitoArray($init,$rs);

//specific on massage total
$msgrs = $obj->getthcsi($begindate,$enddate,$branch_id,false,$cityid);	
$msgindex = 0;
for($i=0; $i<$rscsii["rows"]; $i++){
	if($rscsii[$i]["csii_column_name"]=="q_mg"){$msgindex=$i+1;}
}
for($j=0; $j<5; $j++) {
	$c["value"][$msgindex][$j] = 0;
}
$c["value"][$msgindex]["total"] = 0;
for($j=0; $j<$msgrs["rows"]; $j++) {
	if(!isset($c["value"][$msgindex][$msgrs[$j]["q_mg"]])){$c["value"][$msgindex][$msgrs[$j]["q_mg"]]="";}
		++$c["value"][$msgindex][$msgrs[$j]["q_mg"]];
		++$c["value"][$msgindex]["total"];
}	
for($j=0; $j<count($c["value"][$msgindex]); $j++) {
	$csi["value"][$msgindex]["$j"]=(isset($c["value"][$msgindex][$j]))?$c["value"][$msgindex][$j]:0;
	//$csi["value"][$msgindex]["$j"] = $c["value"][$msgindex][$j];
}
$csi["value"][$msgindex]["total"] = $c["value"][$msgindex]["total"];

//print_r($csi["value"]);
$begin_date = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$end_date = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);

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
<script type="text/javascript" src="../scripts/ajax.js"></script>
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
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begin_date,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($end_date,$sdateformat,$ldateformat)?></b></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Quality</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Excellent</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Good</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Average</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Poor</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No CM</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
	</tr>
		<?
	for($i=($csi['rows']-1); $i>0; $i--) {
	
	$onclick = "";
	$class="";
	
	if($csi["formname"][$i] == "Massage") {
		$bgcolor = "#FF6633";
		$class="class=\"csimassage\"";
		if(!$export){
			$branch_id=$branch_id+0;
			$onclick = "style=\"cursor: pointer;\" onClick=\"javascript:openDetail('TherapistMassage',$begindate,$enddate,$branch_id);\"";
		}
	}
	else {
		$bgcolor = "#FFFFFF";
		if($i%2!=0){$bgcolor="#eaeaea";}
		if(!$export){
			$style="";
			if($i%2==1){$class="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";}else{$class="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";}
		
		}
	}
	if(!isset($csi["value"][$i]["5"])){$csi["value"][$i]["5"]=0;}
	if(!isset($csi["value"][$i]["4"])){$csi["value"][$i]["4"]=0;}
	if(!isset($csi["value"][$i]["3"])){$csi["value"][$i]["3"]=0;}
	if(!isset($csi["value"][$i]["2"])){$csi["value"][$i]["2"]=0;}
	if(!isset($csi["value"][$i]["1"])){$csi["value"][$i]["1"]=0;}
	if(!isset($csi["value"][$i]["total"])){$csi["value"][$i]["total"]=0;}
	?>
	
	
	<tr align="center" height="20" bgcolor="<?=$bgcolor?>" <?=$onclick?> <?=$class?>>
		<td align="left" width="200" class="report"><b><?=$csi["formname"][$i]?><?=($i==8)?" *":""?></b></td>
		<td class="report" align="center"><?=$csi["value"][$i]["5"]+0?></td>
		<td class="report" align="center"><?=$csi["value"][$i]["4"]+0?></td>
		<td class="report" align="center"><?=$csi["value"][$i]["3"]+0?></td>
		<td class="report" align="center"><?=$csi["value"][$i]["2"]+0?></td>
		<td class="report" align="center"><?=$csi["value"][$i]["1"]+0?></td>
		<td class="report" align="center"><?=$csi["value"][$i]["total"]+0?></td>
	</tr>
	
	<?
	}
	?>
 	<tr height="20">
 		<td colspan="7" height="20">&nbsp;</td>
 	</tr>
	<tr height="20">
		<td colspan="7" align="center" height="20" valign="top" style="padding-right:7px;">
			<b style="color:#ff0000;">* </b><b>Massage total allows for more than 1 therapist per customer</b>
		</td>
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
	window.print();
</script>
<?}?>