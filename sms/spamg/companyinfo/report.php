<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb(); 
$tbname = "a_company_info";
$sql = "select * from a_company_info";
$filename = '../object.xml';
$rs = $obj->getResult($sql);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%">
<table cellspacing="0" border="0" cellpadding="0" width="60%" class="generalinfo">
<?
$f = simplexml_load_file($filename);
$element = $f->table->$tbname;
$i=0;
$arrFields = array();
foreach($element->field as $fi){
	$arrFields[$i] = $fi["name"];
	$arrFieldsname[$i] = $fi["formname"];
	$arrFormType[$i] = $fi["formtype"];
	$arrShowinform[$i] = $fi["showinform"];
	$arrShowinList[$i] = $fi["showinList"];
	$i++;
} 
$column = count($arrFields);
?>

<?
for($j=0;$j<$column;$j++){

if($arrShowinList[$j]!="no") {
		
if($j%2==1){
	$bgcolor = "#d3d3d3"; $class = "odd";
}else{
	$bgcolor = "#eaeaea"; $class = "even";
}

if($arrFields[$j]=='tp_id') {
	$data = $obj->getIdToText($rs[0]["$arrFields[$j]"],"l_timeperiod","tp_name","tp_id");
} else if($arrFields[$j]=='short_date'||$arrFields[$j]=='long_date') {
	$data = $obj->getIdToText($rs[0]["$arrFields[$j]"],"l_date","date_type","date_id")." &nbsp;(".$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),$obj->getIdToText($rs[0]["$arrFields[$j]"],"l_date","date_format","date_id")).")";	
} else if($arrFields[$j]=='theme') {
	$theme = $obj->getIdToText($rs[0]["$arrFields[$j]"],"l_theme","theme_name","theme_id");
	$data = "<img src=\"/images/".strtolower($theme).".jpg\" alt=\"".$theme."\">&nbsp;&nbsp;".$theme;
} else if($arrFields[$j]=='company_address') {
	$data = str_replace("[br]","<br>",$rs[0]["$arrFields[$j]"]);
} else if($arrFields[$j]=='th_shift_hour') {
	$data = substr($obj->getIdToText($rs[0]["$arrFields[$j]"],"l_hour","hour_name","hour_id"),0,5);
} else if($arrFields[$j]=='start_time_id' || $arrFields[$j]=='close_time_id') {
	$data = substr($obj->getIdToText($rs[0]["$arrFields[$j]"],"p_timer","time_start","time_id"),0,5);
} else{
	$data = $rs[0]["$arrFields[$j]"];
} 
?>
		<tr class="<?=$class?>" height="24" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='<?php echo $bgcolor; ?>'">
				<td class="report"><b class="pagelink"><?=$arrFieldsname[$j]?> <?=$arrShowinList[$j]?></b></td>
				<td class="report"><?=$data?></td>
		</tr>
<? } 
} ?>
<!--
		<tr class="even" height="20">
				<td class="report"><b  class="pagelink">Company_logo</b></td>
				<td class="report"><img src="viewPicture.php?name=company_logo" height="44px"></td>
		</tr>
-->
		<tr class="odd" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#d3d3d3'">
				<td class="report"><b  class="pagelink">Time Zone</b></td>
				<td class="report"><?=$obj->getIdToText(1,"bl_branchinfo,l_timezone","description","branch_id","bl_branchinfo.timezone=l_timezone.timezone_id");?></td>
		</tr>
		<tr class="even" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#eaeaea'">
				<td class="report"><b  class="pagelink">Company Name Symbol</b></td>
				<td class="report"><img src="viewPicture.php?name=currency_symbol" height="44px"></td>
		</tr>
</table>
    	</td>
	</tr>
</table>