<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb(); 
// initial parameter 
$search=$obj->getParameter("search","");
$categoryid=$obj->getParameter("categoryid",0);
$branchid=$obj->getParameter("branchid",0);
$cityid=$obj->getParameter("cityid",0);
$showinactive=$obj->getParameter("showinactive",0);
$showdetail=$obj->getParameter("showdetail",0);
$order=$obj->getParameter("order","room_name");
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
$tbname = "l_employee";
$element = $f->table->$tbname;
$eshowpage = $element->showpage;
$showpage = $eshowpage["value"];
$obj->setShowpage($showpage);
$i = 0;
$arrFields = array();
foreach($element->field as $fi){
	if(!$showdetail){
		if($fi["name"]!="l_lu_user"&&$fi["name"]!="l_lu_date"&&$fi["name"]!="l_lu_ip"){
				$arrFields[$i] = $fi["name"];
				$arrFieldsname[$i] = $fi["formname"];
				$arrFormType[$i] = $fi["formtype"];
				$arrShowinform[$i] = $fi["showinform"];
				$arrShowinList[$i] = $fi["showinList"];
				$i++;
		}
	} else {
				$arrFields[$i] = $fi["name"];
				$arrFieldsname[$i] = $fi["formname"];
				$arrFormType[$i] = $fi["formtype"];
				$arrShowinform[$i] = $fi["showinform"];
				$arrShowinList[$i] = $fi["showinList"];
				$i++;
	}	
} 
$column = count($arrFields);
$eid = $element->idfield;
$ename = $element->namefield;
$idfield = $eid["name"];
$namefield = $ename["name"];

//query language
$sql = "select l_employee.* from l_employee,bl_branchinfo ";
$search = strtolower($search);
$sql .= "where bl_branchinfo.branch_id=l_employee.branch_id ";
// specific $search / options select
if(!$showinactive){$sql .= "and l_employee.emp_active=1 ";}
if($categoryid){$sql .= "and emp_department_id=$categoryid ";}
if($branchid>1){$sql .= "and l_employee.branch_id=$branchid ";}
if($cityid){$sql .= "and bl_branchinfo.city_id=$cityid ";}
$sql .= "and l_employee.emp_code != 0 "  ;
$sql .= "and (lower(emp_fname) like '%$search%' "  .
		"or lower(emp_lname) like '%$search%' "  .
		"or lower(emp_nickname) like '%$search%' "  .
		"or lower(emp_phonehome) like '%$search%' "  .	
		"or lower(emp_phonemobile) like '%$search%' "  .
		"or lower(emp_code) like '%$search%') ";
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
				if($arrFields[$j]=='branch_id') {
					$data=$obj->getIdToText($rs[$i]['branch_id'],"bl_branchinfo","branch_name","branch_id");
				}else if($arrFields[$j]=='emp_department_id') {
					$data=$obj->getIdToText($rs[$i]['emp_department_id'],"l_employee_department","emp_department_name","emp_department_id");
				}else if($arrFields[$j]=='l_lu_user') {
					$data=$obj->getIdToText($rs[$i]['l_lu_user'],"s_user","u","u_id");
				}else if($arrFields[$j]=='l_lu_date') {	
						list($date,$time) = explode(" ",$rs[$i]["$arrFields[$j]"]);
						$data = ($date=="0000-00-00")?"-":$dateobj->timezone_global($date,$time,"$sdateformat H:i:s");
				}else if($arrFormType[$j]=='date') {
						$data = ($rs[$i]["$arrFields[$j]"]=="0000-00-00")?"-":$dateobj->convertdate($rs[$i]["$arrFields[$j]"],'Y-m-d',$sdateformat);
				}else if($arrFields[$j]=='emp_active') {
					if($chkPageEdit){
						if($rs[$i]["emp_active"]==1){
							$data = "<a href=\"javascript:;\" onClick=\"setEnable('l_employee',".$rs[$i]["emp_id"].",0);\"\>".
								 "<img src=\"/images/active.png\" border=\"0\" title=\"active\" /></a>";
						}else{
							$data = "<a href=\"javascript:;\" onClick=\"setEnable('l_employee',".$rs[$i]["emp_id"].",1);\">".
									"<img src=\"/images/inactive.png\" border=\"0\" title=\"expired\" /></a>";
						}
					}else{
						if($rs[$i]["emp_active"]==1){
							$data = "<img src=\"/images/active.png\" border=\"0\" title=\"active\" />";
						}else{
							$data = "<img src=\"/images/inactive.png\" border=\"0\" title=\"active\" />";
						}
					}
				}else if($arrFields[$j]=='emp_misc_info'){
						$data = str_replace("[br]","<br>",$rs[$i]["$arrFields[$j]"]);
				}else if($arrFields[$j]=='emp_fname'||$arrFields[$j]=='emp_lname'||$arrFields[$j]=='emp_code'||$arrFields[$j]=='emp_nickname'||$arrFields[$j]=='emp_phonehome'||$arrFields[$j]=='emp_phonemobile'){
						$data = $obj->hightLightChar($search,$rs[$i]["$arrFields[$j]"]);
				}else {$data = $rs[$i]["$arrFields[$j]"];}
?>
			<td class="report"><?=$data?>&nbsp;</td>
<?		}
	}
	if($chkPageEdit){
		echo "<td class=\"report\"><a href=\"javascript:;\" onclick=\"editData('$tbname','".$rs[$i]["emp_id"]."')\">update</a></td>";
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