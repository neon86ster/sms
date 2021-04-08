<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb(); 
// initial parameter 
$search=$obj->getParameter("search","");
$categoryid=$obj->getParameter("categoryid",0);
$cityid=$obj->getParameter("cityid",0);
$cmsid=$obj->getParameter("cmsid",0);
$showinactive=$obj->getParameter("showinactive",0);
$order=$obj->getParameter("order","bp_name");
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

$export = $obj->getParameter("export",false);
if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Booking Party Information.xls\"");
	header("Pragma: public");
	header("Expires: 0");
	
}

if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf(0,0,1);
	//$pdf->convertFromFile("manage_cplinfo3.htm");
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print");
}
$chkrow=0;
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",30);
	//$chkpage = ceil($rs["rows"]/$chkrow);
}

// load xml
$f = simplexml_load_file($filename);
$tbname = "al_bookparty";
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
$sql = "select al_bookparty.* from al_bookparty ";
$search = strtolower($search);
$sql .= "where bp_id=bp_id ";
// specific $search / options select
if(!$showinactive){$sql .= "and bp_active=1 ";}
if($categoryid){$sql .= "and bp_category_id=$categoryid ";}
if($cityid){$sql .= "and city_id=$cityid ";}
if($cmsid){$sql .= "and bp_pcms=$cmsid ";}
$sql .= "and (lower(bp_name) like '%$search%' "  .
		"or lower(REPLACE(bp_detail,\"[br]\",\"\")) like '%$search%') ";
if($order){$sql .= "order by $order $sort ";}
$chkrs = $obj->getResult($sql);
$rows=$obj->getShowpage();
$start = $rows*$page - $rows;
if(!$export){
if($page){$sql .= "limit $start,$rows";}
}
$rs = $obj->getResult($sql);
?>

<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%">
			<table cellspacing="0" border="0" cellpadding="0" width="100%" class="generalinfo">
	
	<tr>
	 <?for($i=0;$i<($column-2);$i++){?>
	 <td width="<?=100/($column-2)?>%"></td>
	 <?	}?>
	</tr>
				
				<tr  bgcolor="#88afbe" height="32">
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
<? if($chkPageEdit && !$export){?>
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
		//echo "<tr  class=\"odd\" height=\"32\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" >\n";
	?>
	<tr bgcolor="#d3d3d3" class="odd" height="32" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#d3d3d3'">
	<?
	}else{
		//echo "<tr class=\"even\" height=\"32\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" >\n";
	?>
	<tr class="even" height="32" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='#eaeaea'">
	<?
	}
	for($j=0;$j<$column;$j++){
		if($arrShowinList[$j]!="no") {
				$data = "";
				$chkarrFields = explode(".",$arrFields[$j]); //chk array field of order; some $arrFields is "tablename"."columnname"
				$arrFields[$j] = (count($chkarrFields)>1)?$chkarrFields[1]:$arrFields[$j];
				if($arrFields[$j]=='bp_category_id') {
					$data=$obj->getIdToText($rs[$i]['bp_category_id'],"al_bookparty_category","bp_category_name","bp_category_id");
				}else if ($arrFields[$j]=='bp_country'){
					$data=$obj->getIdToText($rs[$i]['bp_country'],"dl_nationality","nationality_name","nationality_id");
				}else if($arrFields[$j]=='city_id') {
					$data=$obj->getIdToText($rs[$i]['city_id'],"al_city","city_name","city_id");
				}else if ($arrFields[$j]=='bp_pcms'){
					$data=$obj->getIdToText($rs[$i]['bp_pcms'],"al_percent_cms","pcms_percent","pcms_id");
				}else if($arrFormType[$j]=='date') {
						$data = ($rs[$i]["$arrFields[$j]"]=="0000-00-00")?"-":$dateobj->convertdate($rs[$i]["$arrFields[$j]"],'Y-m-d',$sdateformat);
				}else if($arrFields[$j]=='bp_active') {
					if($chkPageEdit){
						if($rs[$i]["bp_active"]==1){
							$data = "<a href=\"javascript:;\" onClick=\"setEnable('al_bookparty',".$rs[$i]["bp_id"].",0);\"\>".
								 "<img src=\"/images/active.png\" border=\"0\" title=\"active\" /></a>";
						}else{
							$data = "<a href=\"javascript:;\" onClick=\"setEnable('al_bookparty',".$rs[$i]["bp_id"].",1);\">".
									"<img src=\"/images/inactive.png\" border=\"0\" title=\"expired\" /></a>";
						}
					}else{
						if($rs[$i]["bp_active"]==1){
							$data = "<img src=\"/images/active.png\" border=\"0\" title=\"active\" />";
						}else{
							$data = "<img src=\"/images/inactive.png\" border=\"0\" title=\"active\" />";
						}
					}
				}else if($arrFields[$j]=='bp_detail'){
						if($export && $export=="Excel"){
						$data = str_replace("[br]"," ",$rs[$i]["$arrFields[$j]"]);	
						}else{
						$data = str_replace("[br]","<br>",$rs[$i]["$arrFields[$j]"]);
						}
						$data = $obj->hightLightChar($search,$data);
				}else if($arrFormType[$j]=='textarea'){
						if($export && $export=="Excel"){
						$data = str_replace("[br]"," ",$rs[$i]["$arrFields[$j]"]);	
						}else{
						$data = str_replace("[br]","<br>",$rs[$i]["$arrFields[$j]"]);
						}
				}else if($arrFields[$j]=='bp_name'){
						$data = $obj->hightLightChar($search,$rs[$i]["$arrFields[$j]"]);
				}else {$data = $rs[$i]["$arrFields[$j]"];}
?>
			<td class="report"><?=$data?>&nbsp;</td>
<?		}
	}
	if($chkPageEdit && !$export){
		echo "<td class=\"report\"><a href=\"javascript:;\" onclick=\"editData('$tbname','".$rs[$i]["bp_id"]."')\">update</a></td>";
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
 				if(!$export){
				$obj->gen_page("report.php",$page,$chkrs["rows"]);
 				}
			?></font>
    	</td>
	</tr>
</table>