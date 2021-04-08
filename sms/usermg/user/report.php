<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb(); 
// initial parameter 
$search=$obj->getParameter("search","");
$showinactive=$obj->getParameter("showinactive",0);
$showdetail=$obj->getParameter("showdetail",0);
$categoryid = $obj->getParameter("categoryid",0);
$order=$obj->getParameter("order","u");
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
$tbname = "s_user";
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
$sql = "select s_user.*,s_userpermission.group_id from s_user,s_userpermission ";
$search = strtolower($search);
$sql .= "where u not like '0IVKx02vRdlGRSm' " .
		"and s_userpermission.user_id = s_user.u_id ";
// specific $search / options select
if(!$showinactive){$sql .= "and lower(s_user.active)=1 ";}
if($categoryid){$sql .= "and s_userpermission.group_id=$categoryid ";}
$sql .= "and (lower(u) like '%$search%' "  .
		"or lower(fname) like '%$search%' "  .
		"or lower(lname) like '%$search%' "  .
		"or lower(emp_code) like '%$search%' "  .
		"or lower(email) like '%$search%') ";
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
	if($arrFields[$i]=="active"){ 
		$tstyle = "background-color:#a8c2cb;";
		if($order=="s_userpermission.group_id"){ 
			$tstyle = "background-color:#88afbe;" .
					  "background-image: url('/images/arrow_down.png');" .
					  "border-bottom: 3px solid #eae8e8";
		}
?>
					<td style="text-align:center;<?=$tstyle?>"  >
					<a href="javascript:;" onclick="sortInfo('s_userpermission.group_id',<?=$page?>)" class="pagelink">
					<b>Template Permission</b>
					</a>
<?		
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
		$bgcolor = "#d3d3d3";
		echo "<tr class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='$bgcolor'\" >\n";
	}else{
		$bgcolor = "#eaeaea";
		echo "<tr class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='$bgcolor'\" >\n";
	}

	for($j=0;$j<$column;$j++){
		if($arrShowinList[$j]!="no") {
				$data = "";
				$chkarrFields = explode(".",$arrFields[$j]); //chk array field of order; some $arrFields is "tablename"."columnname"
				$arrFields[$j] = (count($chkarrFields)>1)?$chkarrFields[1]:$arrFields[$j];
				if($arrFields[$j]=='branch_id') {
					$data=$obj->getIdToText($rs[$i]['branch_id'],"bl_branchinfo","branch_name","branch_id");
				}else if($arrFields[$j]=='l_lu_user') {
					$data=$obj->getIdToText($rs[$i]['l_lu_user'],"s_user","u","u_id");
				}else if($arrFields[$j]=='l_lu_date') {	
						list($date,$time) = explode(" ",$rs[$i]["$arrFields[$j]"]);
						$data = ($date=="0000-00-00")?"-":$dateobj->timezone_global($date,$time,"$sdateformat H:i:s");
				}else if($arrFormType[$j]=='date') {
						$data = ($rs[$i]["$arrFields[$j]"]=="0000-00-00")?"-":$dateobj->convertdate($rs[$i]["$arrFields[$j]"],'Y-m-d',$sdateformat);
				}else if($arrFields[$j]=='fname'||$arrFields[$j]=='lname'||$arrFields[$j]=='emp_code'||$arrFields[$j]=='email'||$arrFields[$j]=='u'){
						$data = $obj->hightLightChar($search,$rs[$i]["$arrFields[$j]"]);
				}else {$data = $rs[$i]["$arrFields[$j]"];}
				if($arrFields[$j]=="active"){ 
					$data=$obj->getIdToText($rs[$i]['group_id'],"s_group","group_name","group_id");
					echo "<td class=\"report\">$data&nbsp;</td>";
					if($chkPageEdit){
							if($rs[$i]["active"]==1){
								$data = "<a href=\"javascript:;\" onClick=\"setEnable('s_user',".$rs[$i]["u_id"].",0);\"\>".
									 "<img src=\"/images/active.png\" border=\"0\" title=\"active\" /></a>";
							}else{
								$data = "<a href=\"javascript:;\" onClick=\"setEnable('s_user',".$rs[$i]["u_id"].",1);\">".
										"<img src=\"/images/inactive.png\" border=\"0\" title=\"expired\" /></a>";
							}
						}else{
							if($rs[$i]["active"]==1){
								$data = "<img src=\"/images/active.png\" border=\"0\" title=\"active\" />";
							}else{
								$data = "<img src=\"/images/inactive.png\" border=\"0\" title=\"active\" />";
							}
					}
				}
?>
			<td class="report"><?=$data?>&nbsp;</td>
<?		}
	}
	if($chkPageEdit){
		echo "<td class=\"report\"><a href=\"javascript:;\" onclick=\"editData('$tbname','".$rs[$i]["u_id"]."')\">update</a></td>";
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