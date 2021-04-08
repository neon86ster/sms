<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
require_once("csi.inc.php");
$robj = new report();
$obj = new csi();
$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate = $obj->getParameter("end");

$branch_id = $obj->getParameter("branchid",1);
$cityid = $obj->getParameter("cityid",false);
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");
$today = date("Ymd");
$category = $obj->getParameter("category");
$category = (!$category)?"All":$obj->getIdToText($category,"fl_csi_index","csii_name","csii_id");
$branch_name = strtolower($obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id"));
/*
if($branch_id){
	$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")."'s Customer ";
	if($category != "All"){$reportname .= "- $category ";}
	$reportname .= "CSI index Report";
}else{
	$reportname = "All Customer ";
	if($category != "All"){$reportname .= "- $category ";}
	$reportname .= "CSI index Report";
}*/

$reportname = "Customer CSI index Report ";
if($category != "All"){$reportname .= "- $category ";}
	
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
	$pdf=new convert2pdf(false,true);
	//$pdf->convertFromFile("manage_cplinfo3.htm");
	$pdf->convertGraphFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&chkrow=55");
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
	//$n = array("Facilities","Temperature","Music","Aroma","Cleanlines","Value for Money","Body Treatments","Massage","Driver","Friendly & Cheerful","Greeting","Attentiveness","Manner / Courtesy");
	
	for($i=0; $i<=$rscsii["rows"]; $i++) {
		if($i) {
			$tmp["rowsname"][$i] = $db[$i-1];		// set colume name from database
			$tmp["formname"][$i] = $n[$i-1];		// set table 1st row header 
			for($j=0; $j<$rscsiv["rows"]; $j++) {
				$tmp["value"][$i][$rscsiv[$j]["csiv_id"]] = 0;	// initial csi value to zero
			}
		}
		else {
			$tmp["formname"][$i] = " ";				// 1st rows(header) in show table
			$tmp["percent"][$i] = "CSI Percent(%)";	
			$tmp["ptotal"][$i] = " ";
		}
	}
	
	$tmp["rows"]=$i;
	return $tmp;
}

// function for initial array for customer comment rows
function allcsitoArray($init,$rs,$rscsiv) {
	$c = $init;
	for($i=1; $i<$init["rows"]; $i++) {				
		for($j=0; $j<$rs["rows"]; $j++) {
			++$c["value"][$i][$rs[$j][$init["rowsname"][$i]]];
			//++$c["value"][$i]["total"];
		}	
		$c["ptotal"][$i] = $c["value"][$i][5]+$c["value"][$i][4]+$c["value"][$i][3]+$c["value"][$i][2];
		//$c["value"][$i]["total"] = $c["value"][$i]["total"]-$c["value"][$i][1];	// $c["value"][$i]["total"] except no-recommended
		
		$c["ppercent"][$i] = 0;
		for($j=0; $j<$rscsiv["rows"]; $j++) {
			if($rscsiv[$j]["csiv_name"]){
				$c["ppercent"][$i] +=$c["value"][$i][$rscsiv[$j]["csiv_id"]]*$rscsiv[$j]["csiv_value"];
			}
		}
		
		//if($c["ptotal"][$i]==0){$c["ptotal"][$i]=1;}
		if($c["ptotal"][$i]==0){$c["ptotal"][$i]=0;}
		$c["percent"][$i] = $c["ppercent"][$i]/$c["ptotal"][$i];
	}
	//unset($c["ptotal"][6]);
	if(array_sum($c["ptotal"])==0){$allcnt=1;}else{$allcnt=array_sum($c["ptotal"]);}
	
	$tmp["ppercent"] = $c["ppercent"];
	//unset($tmp["ppercent"][6]);
	$c["percenttotal"] = array_sum($tmp["ppercent"])/$allcnt;
	return $c;
}

$rs = $obj->getcsinfo($begindate,$enddate,$branch_id,false,$cityid);
$rscsiv = $obj->getcsivalue();
$rscsii = $obj->getcsiindex();
$init = InitA($rscsiv,$rscsii);
//print_r($init);
$csi = allcsitoArray($init,$rs,$rscsiv);

$begin_date = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$end_date = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);

$column= $obj->getParameter("column","Total only");
if($column==""){$column="Total only";}
$rsdate = $obj->getdatecol($column,$begindate,$enddate);



$dataset=array();$yaxis = array();$formcust=array();

if($category=="All"){
$formcust = $csi["formname"];	
	if($order=="Total"){
		array_multisort($csi["percent"],$csi["formname"]);
		array_multisort($csi["ptotal"],$formcust);
			if($sort=="Z > A"){
				for($i=0;$i<count($csi["formname"])-1;$i++){
					$yaxis[$i] = $csi["formname"][1+$i];
					$dataset[$i] = $csi["percent"][1+$i];
					$datap[$i] = $csi["ptotal"][1+$i];
				}
			}else{
				for($i=0;$i<count($csi["formname"])-1;$i++){
					$yaxis[$i] = $csi["formname"][13-$i];
					$dataset[$i] = $csi["percent"][13-$i];
					$datap[$i] = $csi["ptotal"][13-$i];
				}
			}
	}else{
	array_multisort($csi["formname"],$csi["percent"]);	
	array_multisort($formcust,$csi["ptotal"]);
	
	for($i=0;$i<count($csi["formname"])-1;$i++){
		if($sort=="Z > A"){
				for($i=0;$i<count($csi["formname"])-1;$i++){
					$yaxis[$i] = $csi["formname"][1+$i];
					$dataset[$i] = $csi["percent"][1+$i];
					$datap[$i] = $csi["ptotal"][1+$i];
				}
			}else{
				for($i=0;$i<count($csi["formname"])-1;$i++){
					$yaxis[$i] = $csi["formname"][13-$i];
					$dataset[$i] = $csi["percent"][13-$i];
					$datap[$i] = $csi["ptotal"][13-$i];
				}
			}
	}
	}
	$allcsi = $csi["percenttotal"];
}else{
	$tmpkey = array_keys($init["formname"], "$category");
	$columnname = $init["rowsname"][$tmpkey[0]];
	$rs = $obj->getcsinfo($begindate,$enddate,$branch_id,$columnname,$cityid);
	//$rsdate = $obj->getdatecol($column,$begindate,$enddate-1);
	$yaxis = $rsdate["header"];
	for($d=0;$d<$rsdate["rows"];$d++){
		$csid["percent"][$d] = 0;
		$ptotal[$d] = 0;
		for($i=0;$i<$rs["rows"];$i++){
			$appt_date = str_replace("-","",$rs[$i]["b_appt_date"]);
			for($j=0; $j<$rscsiv["rows"]; $j++) {
				if(!isset($csid["value"][$d][$rscsiv[$j]["csiv_id"]])){$csid["value"][$d][$rscsiv[$j]["csiv_id"]]=0;}
				if($appt_date>=$rsdate["begin"][$d]&&$appt_date<=$rsdate["end"][$d]
				&&$rscsiv[$j]["csiv_id"]==$rs[$i]["$columnname"]){
						$csid["value"][$d][$rscsiv[$j]["csiv_id"]] += 1;
				}
			}
		}
		
		for($j=0; $j<$rscsiv["rows"]; $j++) {
			if(!isset($csid["value"][$d][$rscsiv[$j]["csiv_id"]])){$csid["value"][$d][$rscsiv[$j]["csiv_id"]]=0;}
			if($rscsiv[$j]["csiv_name"]){
				$csid["percent"][$d] +=$csid["value"][$d][$rscsiv[$j]["csiv_id"]]*$rscsiv[$j]["csiv_value"];
				$ptotal[$d] += $csid["value"][$d][$rscsiv[$j]["csiv_id"]];
			}
		}
		if($ptotal[$d]==0){$ptotal[$d]=1;}
		$csipd["percent"][$d]=$csid["percent"][$d] / $ptotal[$d] ;
	}
	
	if(array_sum($ptotal)==0){$allcnt=1;}else{$allcnt=array_sum($ptotal);}
	$allcsi = array_sum($csid["percent"])/$allcnt;
	$dataset = $csipd["percent"];
	$datap = $ptotal;
	
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
<script type="text/javascript" src="../scripts/ajax.js"></script>
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="50%"></td><td width="50%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="2">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begin_date,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($end_date,$sdateformat,$ldateformat)?><b></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b></p>
    	</td>
	</tr>

	<tr>
    	<td width="100%" align="center" colspan="2">
    		
<div class="graph">
			<?php require 'graph.php' ?> 
</div>
 
    	</td>
	</tr>
<!--	
	<tr height="32">
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Quality</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Excellent</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Good</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Average</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Poor</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No CM</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
	</tr>
-->	
	<tr height="30">
    	<td width="100%" align="center" colspan="2"><br>
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