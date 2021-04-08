<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb(); 
// initial parameter 
$search=$obj->getParameter("search","");
$showinactive=$obj->getParameter("showinactive",0);
$order=$obj->getParameter("order","trm_name");
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
$tbname = "db_trm";
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
$sql = "select * from db_trm ";
$search = strtolower($search);
// specific $search don't care about category select
$sql .= "where trm_category_id=3 ";
if(!$showinactive){$sql .= "and trm_active=1 ";}
$sql .= "and (lower(trm_name) like '%$search%' "  .
		"or lower(REPLACE(trm_detail,\"[br]\",\"\")) like '%$search%') ";
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
				if($arrFields[$j]=='trm_active') {
					if($chkPageEdit){
						if($rs[$i]["trm_active"]==1){
							$data = "<a href=\"javascript:;\" onClick=\"setEnable('db_trm',".$rs[$i]["trm_id"].",0);\"\>".
								 "<img src=\"/images/active.png\" border=\"0\" title=\"active\" /></a>";
						}else{
							$data = "<a href=\"javascript:;\" onClick=\"setEnable('db_trm',".$rs[$i]["trm_id"].",1);\">".
									"<img src=\"/images/inactive.png\" border=\"0\" title=\"expired\" /></a>";
						}
					}else{
						if($rs[$i]["trm_active"]==1){
							$data = "<img src=\"/images/active.png\" border=\"0\" title=\"active\" />";
						}else{
							$data = "<img src=\"/images/inactive.png\" border=\"0\" title=\"active\" />";
						}
					}
				}else if($arrFields[$j]=='trm_detail'){
						$data = str_replace("[br]","<br>",$rs[$i]["$arrFields[$j]"]);
						$data = $obj->hightLightChar($search,$data);
				}else if($arrFields[$j]=='trm_name'){
						$data = $obj->hightLightChar($search,$rs[$i]["$arrFields[$j]"]);
				}else {$data = $rs[$i]["$arrFields[$j]"];}
?>
			<td class="report"><?=$data?>&nbsp;</td>
<?		}
	}
	if($chkPageEdit){
		echo "<td class=\"report\"><a href=\"javascript:;\" onclick=\"editData('$tbname','".$rs[$i]["trm_id"]."')\">update</a></td>";
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