<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb(); 
// initial parameter 
$order=$obj->getParameter("order");
$sort=$obj->getParameter("sort");
$page = $obj->getParameter("page",1);
$cityid = $obj->getParameter("cityid",1);
$search=$obj->getParameter("search","");
$categoryid=$obj->getParameter("categoryid",0);
$showinactive=$obj->getParameter("showinactive",0);
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
$tbname = "l_marketingcode";
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
$sql = "select * from l_marketingcode ";
$search = strtolower($search);
$searchsql = $obj->convert_char($search);
$sql .= "where mkcode_id=mkcode_id ";
// specific $search / options select
if(!$showinactive){$sql .= "and active=1 ";}
if($categoryid){$sql .= "and category_id=$categoryid ";}
$sql .= "and (lower(sign) like '%$searchsql%' "  .
		"or lower(place) like '%$searchsql%' " .
		"or lower(contactperson) like '%$searchsql%' " .
		"or lower(phone) like '%$searchsql%' " .
		"or lower(REPLACE(comment,\"[br]\",\"\")) like '%$searchsql%') ";
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
					<td style="text-align:center;background-color:#a8c2cb;">&nbsp;
					</td>
<?	} ?>
				</tr>
<?
//end field name generate
//start field element generate
$data = "&nbsp;";
for($i=0;$i<$rs["rows"];$i++){
	if($i%2==1){
		echo "<tr class=\"odd\" height=\"20\">\n";
	}else{
		echo "<tr class=\"even\" height=\"20\">\n";
	}
	for($j=0;$j<$column;$j++){
		if($arrShowinList[$j]!="no") {
				$data = "";
				$chkarrFields = explode(".",$arrFields[$j]); //chk array field of order; some $arrFields is "tablename"."columnname"
				$arrFields[$j] = (count($chkarrFields)>1)?$chkarrFields[1]:$arrFields[$j];
				if($arrFields[$j]=='active') {
					 if($rs[$i]["active"]==1){
							$data = "<img src=\"/images/active.png\" border=\"0\" title=\"active\" />";
					 }else{
							$data = "<img src=\"/images/inactive.png\" border=\"0\" title=\"active\" />";
					 }
				}else if($arrFields[$j]=='category_id') {
						$data = $obj->getIdToText($rs[$i]['category_id'],"l_mkcode_category","category_name","category_id");
				}else if($arrFields[$j]=='comment'){
						$data = str_replace("[br]","<br>",$rs[$i]["$arrFields[$j]"]);
						$data = $obj->hightLightChar($search,$data);
				}else if($arrFormType[$j]=='date') {
						$data = ($rs[$i]["$arrFields[$j]"]=="0000-00-00")?"-":$dateobj->convertdate($rs[$i]["$arrFields[$j]"],'Y-m-d',$sdateformat);
				}else if($arrFields[$j]=='sign'||$arrFields[$j]=='place'||$arrFields[$j]=='contactperson'||$arrFields[$j]=='phone'||$arrFields[$j]=='comment'){
						$data = $obj->hightLightChar($search,$rs[$i]["$arrFields[$j]"]);
				}else {$data = $rs[$i]["$arrFields[$j]"];}
?>
			<td class="report"><?=$data?>&nbsp;</td>
<?		}
	}
	if($chkPageEdit){
		echo "<td class=\"report\"><input type='button' name='codeAdd' id='codeAdd' value='Add' onClick='editmkCode(\"".$rs[$i]["mkcode_id"]."\");'/></td>";
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