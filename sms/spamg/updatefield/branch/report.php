<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb(); 
// initial parameter 
$search=$obj->getParameter("search","");
$showinactive=$obj->getParameter("showinactive",0);
$order=$obj->getParameter("order","branch_name");
$sort=$obj->getParameter("sort","asc");
$page = $obj->getParameter("page",1);
$successmsg = $obj->getParameter("msg","");
$msg=$obj->getParameter("msg","");
$obj->setDebugStatus(false);
$filename = "../object.xml";
$url = "addinfo.php";

$debug=false;
$textout="";
$link="";
// End initial parameter 

// load xml
$f = simplexml_load_file($filename);
$tbname = "bl_branchinfo";
$element = $f->table->$tbname;
$eshowpage = $element->showpage;
$showpage = $eshowpage["value"];
$obj->setShowpage($showpage);
$i = 0;
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
$eid = $element->idfield;
$ename = $element->namefield;
$idfield = $eid["name"];
$namefield = $ename["name"];

//query language
$sql = "select * from bl_branchinfo ";
$search = strtolower($search);
// specific $search don't care about category select
if(!$showinactive){
$sql .= "where branch_active=1 " .
		"and (lower(branch_name) like '%$search%' "  .
		"or lower(REPLACE(branch_address,\"[br]\",\"\")) like '%$search%' " .
		"or lower(REPLACE(branch_msg,\"[br]\",\"\")) like '%$search%' " .
		"or REPLACE(branch_phone,\"-\",\"\") like '%$search%') ";
}else{
$sql .= "where (lower(branch_name) like '%$search%' " .
		"or lower(REPLACE(branch_address,\"[br]\",\"\")) like '%$search%' " .
		"or lower(REPLACE(branch_msg,\"[br]\",\"\")) like '%$search%' " .
		"or REPLACE(branch_phone,\"-\",\"\") like '%$search%') ";
}
// except branch "All" in branch list natt/June 16,2009
$sql .= "and lower(branch_name) not like 'all' ";	
if($order){$sql .= "order by $order $sort ";}
$chkrs = $obj->getResult($sql);
$rows=$obj->getShowpage();
$start = $rows*$page - $rows;
if($page){$sql .= "limit $start,$rows";}	
$rs = $obj->getResult($sql);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%">
			<table cellspacing="0" border="0" cellpadding="0" width="100%" class="generalinfo">
				<tr height="32">
<?
//start field name generate
for($i=0;$i<$column;$i++){
	if($arrFormType[$i]!="submit"&&$arrFormType[$i]!="button"&&$arrShowinList[$i]!="no"){
		if($order==$arrFields[$i]){ 
			$style = "background-color:#88afbe;" .
					  "background-image: url('/images/arrow_down.png');" .
					  "border-bottom: 3px solid #eae8e8";
		}else{
		 	$style = "background-color:#a8c2cb;";
		}
?>
					<td style="text-align:center;<?=$style?>">
					<a href="javascript:;" onclick="sortInfo('<?=$arrFields[$i]?>',<?=$page?>)" class="pagelink">
					<b><?=$arrFieldsname[$i]?></b>
					</a>
					</td>
<? 	}
} ?>
<? if($chkPageEdit){?>
					<td style="text-align:center;background-color:#a8c2cb;">
					<b>Edit</b>
					</td>
<?	} ?>
				</tr>
<?
//end field name generate
//start field element generate
$data = "&nbsp;";
for($i=0;$i<$rs["rows"];$i++){
	if($i%2==1){
		echo "<tr class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" >\n";
	}else{
		echo "<tr class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" >\n";
	}
	for($j=0;$j<$column;$j++){
		if($arrShowinList[$j]!="no") {
				$data = "";
				$chkarrFields = explode(".",$arrFields[$j]); //chk array field of order; some $arrFields is "tablename"."columnname"
				$arrFields[$j] = (count($chkarrFields)>1)?$chkarrFields[1]:$arrFields[$j];
				if($arrFields[$j]=='city_id') {
					$data=$obj->getIdToText($rs[$i]['city_id']," al_city","city_name","city_id");
				}else if($arrFields[$j]=='branch_category_id') {
					$data=$obj->getIdToText($rs[$i]['branch_category_id'],"bl_branch_category","branch_category_name","branch_category_id");
				}else if($arrFields[$j]=='timezone') {
					$data=$obj->getIdToText($rs[$i]['timezone'],"l_timezone","description","timezone_id");
					/*list($hr,$min) = explode(".",number_format($data,2,".",","));
					$data = ($data>0)?"GMT+":"GMT-";
					$data .= $hr.":".$min;*/
				}else if($arrFields[$j]=='spa_dayoff') {
					$data=$obj->getIdToText($rs[$i]['spa_dayoff'],"l_day","day_name","day_id");
				}else if($arrFields[$j]=='start_time_id') {
					$data=$obj->getIdToText($rs[$i]['start_time_id'],"p_timer","time_start","time_id");
				}else if($arrFields[$j]=='close_time_id') {
					$data=$obj->getIdToText($rs[$i]['close_time_id'],"p_timer","time_start","time_id");
				}else if($arrFields[$j]=='tax_id') {
					$data=$obj->getIdToText($rs[$i]['tax_id'],"l_tax","tax_percent","tax_id");
				}else if($arrFields[$j]=='branch_cms') {
					$data=$obj->getIdToText($rs[$i]['branch_cms'],"al_percent_cms","pcms_percent","pcms_id");
				}else if($arrFields[$j]=='pcms_id') {
					$data=$obj->getIdToText($rs[$i]['pcms_id'],"al_percent_cms","pcms_percent","pcms_id");
				}else if($arrFields[$j]=='branch_name3') {
					$data = $obj->hightLightChar($search,$rs[$i]["$arrFields[$j]"]);
				}
				else if($arrFields[$j]=='branch_active') {
					if($chkPageEdit){
						if($rs[$i]["branch_active"]==1){
							$data = "<a href=\"javascript:;\" onClick=\"setEnable('$tbname',".$rs[$i]["branch_id"].",0);\"\>".
								 "<img src=\"/images/active.png\" border=\"0\" title=\"active\" /></a>";
						}else{
							$data = "<a href=\"javascript:;\" onClick=\"setEnable('$tbname',".$rs[$i]["branch_id"].",1);\">".
									"<img src=\"/images/inactive.png\" border=\"0\" title=\"expired\" /></a>";
						}
					}else{
						if($rs[$i]["branch_active"]==1){
							$data = "<img src=\"/images/active.png\" border=\"0\" title=\"active\" />";
						}else{
							$data = "<img src=\"/images/inactive.png\" border=\"0\" title=\"active\" />";
						}
					}
				}else if($arrFields[$j]=='branch_msg'||$arrFields[$j]=='branch_address'){
						$data = str_replace("[br]","<br>",$rs[$i]["$arrFields[$j]"]);
						$data = $obj->hightLightChar($search,$data);
				}else if($arrFormType[$j]=='textarea'){
						$data = str_replace("[br]","<br>",$rs[$i]["$arrFields[$j]"]);
				}else if($arrFields[$j]=='branch_name'||$arrFields[$j]=='branch_phone'){
						$data = $obj->hightLightChar($search,$rs[$i]["$arrFields[$j]"]);
				}else {$data = $rs[$i]["$arrFields[$j]"];}
?>
			<td class="report"><?=$data?>&nbsp;</td>
<?		}
	}
	if($chkPageEdit){
		echo "<td class=\"report\"><a href=\"javascript:;\" onclick=\"editData('$tbname','".$rs[$i]["branch_id"]."')\">update</a></td>";
	}
	echo "</tr>";
}
if(!$rs["rows"]){}
?>
 			</table><br/>
		</td>
    </tr>
    <tr>
    	<td width="100%" align="center">
    		<font class="pagelink">
 			<?
				$obj->gen_page("report.php",$page,$chkrs["rows"]);
			?></font>
    	</td>
	</tr>
</table>