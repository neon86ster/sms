<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb(); 
// initial parameter 
$search=$obj->getParameter("search","");
$cityid=$obj->getParameter("cityid",0);
$order=$obj->getParameter("order","acc_name");
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
$tbname = "hotelcms";
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
$sql1 = "select acc_id,acc_name,cmspercent,city_id,acc_active,\"al_accomodations\" as tablename from al_accomodations where acc_id!=1 and acc_active=1 ";
$sql2 = "select bp_id as acc_id,bp_name as acc_name,bp_cmspercent as cmspercent,city_id,bp_active as acc_active,\"al_bookparty\" as tablename from al_bookparty where bp_id!=1 and bp_active=1 ";
$search = strtolower($search);
if($cityid){
	$sql1 .= "and city_id=$cityid ";
	$sql2 .= "and city_id=$cityid ";
}
$sql1 .= "and lower(acc_name) like '%$search%' ";
$sql2 .= "and lower(bp_name) like '%$search%' ";
$sql = "($sql1) union ($sql2) ";
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
					$data=$obj->getIdToText($rs[$i]['city_id'],"al_city","city_name","city_id");
				}else if($arrFields[$j]=='acc_name'){
						$data = $obj->hightLightChar($search,$rs[$i]["$arrFields[$j]"]);
				}else if($arrFields[$j]=='tablename'){
						$data = ($rs[$i]['tablename']=="al_accomodations")?"Accomodation":"Booking Company";
				}else if($arrFields[$j]=='cmspercent'){
						$data = number_format($rs[$i]['cmspercent'],2,".","");
				}else {$data = $rs[$i]["$arrFields[$j]"];}
?>
			<td class="report"><?=$data?>&nbsp;</td>
<?		}
	}
	if($chkPageEdit){
		echo "<td class=\"report\"><a href=\"javascript:;\" onclick=\"editData('".$rs[$i]['tablename']."','".$rs[$i]["acc_id"]."')\">update</a></td>";
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