<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb(); 
// initial parameter 
$search=$obj->getParameter("search","");
$showinactive=$obj->getParameter("showinactive",0);
$order=$obj->getParameter("order","l_employee.emp_nickname");
$sort=$obj->getParameter("sort","asc");
$page = $obj->getParameter("page",1);
$successmsg = $obj->getParameter("msg","");
$msg=$obj->getParameter("msg","");
$branchid=$obj->getParameter("branchid",0);
$cityid=$obj->getParameter("cityid",0);
$obj->setDebugStatus(false);
$filename = "../object.xml";
$url = "addinfo.php";

$debug=false;
$textout="";
$link="";
// End initial parameter 

// load xml
$f = simplexml_load_file($filename);
$tbname = "al_bankacc_cms";
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
$sql = "select l_employee.*,bl_branchinfo.branch_name " .
		"from l_employee,bl_branchinfo " .
		"where l_employee.emp_department_id=4 " .
		"and bl_branchinfo.branch_id=l_employee.branch_id ";
if($branchid>1){$sql .= "and bl_branchinfo.branch_id=$branchid ";}
if($cityid>0){$sql .= "and bl_branchinfo.city_id=$cityid ";}
$search = strtolower($search);
$searchsql = $obj->convert_char($search);
if(!$showinactive){
	$sql .= "and l_employee.emp_active=1 " .
				"and (lower(l_employee.emp_nickname) like '%$searchsql%' "  .
				"or l_employee.emp_code like '%$searchsql%') ";
}else{
	$sql .= "and lower(l_employee.emp_nickname) like '%$searchsql%' "  .
				"or l_employee.emp_code like '%$searchsql%' ";
}
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
					<?  if($order=="l_employee.emp_nickname"){ 
							$style="background-color:#88afbe;" .
									"background-image: url('/images/arrow_down.png');" .
									"border-bottom: 3px solid #eae8e8";
	 					}else{
	 						$style="background-color:#a8c2cb;";
	 					} ?>
					<td style="text-align:center;<?=$style?>">
					<a href="javascript:;" onclick="sortInfo('l_employee.emp_nickname',<?=$page?>)" class="pagelink">
					<b>Therapist Name</b>
					</a>
					</td>
					<? if($order=="l_employee.emp_code"){ 
							$style="background-color:#88afbe;" .
									"background-image: url('/images/arrow_down.png');" .
									"border-bottom: 3px solid #eae8e8";
	 					}else{
	 						$style="background-color:#a8c2cb;";
	 					} ?>
					<td style="text-align:center;<?=$style?>">
					<a href="javascript:;" onclick="sortInfo('l_employee.emp_code',<?=$page?>)" class="pagelink">
					<b>Therapist Code</b>
					</a>
					</td>
					<? if($order=="bl_branchinfo.branch_name"){ 
							$style="background-color:#88afbe;" .
									"background-image: url('/images/arrow_down.png');" .
									"border-bottom: 3px solid #eae8e8";
	 					}else{
	 						$style="background-color:#a8c2cb;";
	 					} ?>
					<td style="text-align:center;<?=$style?>">
					<a href="javascript:;" onclick="sortInfo('bl_branchinfo.branch_name',<?=$page?>)" class="pagelink">
					<b>Branch</b>
					</a>
					</td>
  					<? if($chkPageEdit){?>
					<td style="text-align:center;background-color:#a8c2cb;">
					<b>Edit</b>
					</td>
					<? } ?>
				</tr>
<?	for($i=0; $i<$rs["rows"]; $i++) {
						
	if($i%2==1){
		echo "<tr class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" >\n";
	}else{
		echo "<tr class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" >\n";
	}
$th_shift = 0.5*($rs[$i]["th_shift"]-1);
$th_shift= $obj->getIdToText($th_shift,"l_hour","hour_name","hour_calculate");
$starthour = substr($rs[$i]["l_lu_date"],11,2);$startmins = substr($rs[$i]["l_lu_date"],14,2);
$shifthour = substr($th_shift,0,2);$shiftmins = substr($th_shift,3,2);
$endtime = mktime((int)$shifthour+$starthour, (int)$startmins+$shiftmins, 0, 0, 0, 0);
$endtime = date("H:i", $endtime);
$nickname = $obj->hightLightChar($search,$rs[$i]["emp_nickname"]);
$empcode = $obj->hightLightChar($search,$rs[$i]["emp_code"]);
	?>
					<td class="report" align="center"><?=$nickname?>&nbsp;</td>
					<td class="report" align="center"><?=$empcode?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["branch_name"]?>&nbsp;</td>
  					<? if($chkPageEdit){?>
					<td class="report" align="center"><a href="javascript:;" onclick="editData('bl_th_app','<?=$rs[$i]["emp_id"]?>')">update</a></td>
					<? } ?>
 				</tr>
 				<?	} ?>
 			</table><br/>
		</td>
    </tr>
    <tr>
    	<td width="100%" align="center">
    		<font class="pagelink">
    		<? 
				$obj->gen_page("report.php",$page,$chkrs["rows"]);
			?>
			</font>
    	</td>
	</tr>
</table>