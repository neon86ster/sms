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

$branch = $obj->getParameter("branchid",0);
$cityid = $obj->getParameter("cityid",false);

$today = date("Ymd");
$branch_name = strtolower($obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id"));
/*
if($branch){
	$reportname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id")."'s Therapist CSI Rating Graph";
}else{
	$reportname = "All Therapist CSI Rating Graph";
}*/
$reportname = "Therapist CSI Rating Graph";
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
	$pdf->convertGraphFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&chkrow=55&gmt=".$_SESSION["__gmt"]);
}
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",30);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
$begin_date = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$end_date = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);



$rs = $obj->getthmsgcsi($begindate,$enddate,$branch,false,$cityid);
if(!isset($rs['rows'])){$rs['rows']=0;}
$j = 0;
$dataset=array();$yaxis = array();
$dataset[$j] = 0;$csnumset[$j] = 0;
$totalcsi=0; $totalcs=0;
$thcsi = 0; $thcs = 0;
for($i=0;$i<$rs['rows']+1;$i++){
	if(!isset($rs[$i]['totalcsi'])){$rs[$i]['totalcsi']=0;}
	if(!isset($rs[$i]['total'])){$rs[$i]['total']=0;}
	if(!isset($rs[$i-1]['emp_code'])){$rs[$i-1]['emp_code']=0;}
	if(!isset($rs[$i]['emp_code'])){$rs[$i]['emp_code']=0;}
	if(!isset($rs[$i]['therapist_name'])){$rs[$i]['therapist_name']=0;}

	if(!isset($rs[$i-1]['emp_code']) || $rs[$i]['emp_code']!=$rs[$i-1]['emp_code']){
		$yaxis[$j+1] = $rs[$i]['therapist_name']." ".$rs[$i]['emp_code']." ";
		$csnumset[$j] = $thcs;
		if(!$thcs){$thcs=1;}
		$dataset[$j] = $thcsi/$thcs;
		$j ++;
		$thcsi = 0;
		$thcs = 0;
	}
	$thcsi += $rs[$i]['totalcsi'];
	$thcs += $rs[$i]['total'];
	$totalcsi += $rs[$i]['totalcsi'];
	$totalcs += $rs[$i]['total'];
}
//print_r($yaxis);
if(!$totalcs){$totalcs=1;}
$allcsi = $totalcsi/$totalcs;
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
<script type="text/javascript" src="../scripts/component.js"></script>
<?if($export!="Excel"){?><link href="../../../css/style.css" rel="stylesheet" type="text/css"><?}?>
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
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>

	<tr>
    	<td width="90%" align="center" colspan="7" colspan="2">
    		
<div class="graph">
			<?php require 'graph.php' ?> 
</div>
 
    	</td>
	</tr>
	<tr height="30">
    	<td width="100%" align="center" colspan="2"><br>
    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>