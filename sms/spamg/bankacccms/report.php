<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb(); 
// initial parameter 
$search=$obj->getParameter("search","");
$showinactive=$obj->getParameter("showinactive",0);
//$showdetail=$obj->getParameter("showdetail",0);
$order=$obj->getParameter("order","c_bp_person");
$sort=$obj->getParameter("sort","asc");
$page = $obj->getParameter("page","1");
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
	header("Content-Disposition: attachment; filename=\"Commission Bank Details.xls\"");
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
$tbname = "al_bankacc_cms";
$element = $f->table->$tbname;
$eshowpage = $element->showpage;
$showpage = $eshowpage["value"];
$obj->setShowpage($showpage);
$i = 0;
$arrFields = array();
$cnt_column = 0;
foreach($element->field as $fi){
	//if(!$showdetail){
	//	if($fi["name"]!="l_lu_user"&&$fi["name"]!="l_lu_date"&&$fi["name"]!="l_lu_ip"){
	//			$arrFields[$i] = $fi["name"];
	//			$arrFieldsname[$i] = $fi["formname"];
	//			$arrFormType[$i] = $fi["formtype"];
	//			$arrShowinform[$i] = $fi["showinform"];
	//			$arrShowinList[$i] = $fi["showinList"];
	//			$i++;
	//	}
	//} else {
				$arrFields[$i] = $fi["name"];
				$arrFieldsname[$i] = $fi["formname"];
				$arrFormType[$i] = $fi["formtype"];
				$arrShowinform[$i] = $fi["showinform"];
				$arrShowinList[$i] = $fi["showinList"];
				if($arrShowinList[$i]!="no"){
					$cnt_column++;
				}
				$i++;
	//}	
} 
$column = count($arrFields);
$eid = $element->idfield;
$ename = $element->namefield;
$idfield = $eid["name"];
$namefield = $ename["name"];

//query language
/*$sql = "select * from al_bankacc_cms ";
$search = strtolower($search);
$searchsql = $obj->convert_char($search);
$sql .= "where bankacc_cms_id=bankacc_cms_id ";
if(!$showinactive){$sql .= "and bankacc_active=1 ";}
$sql .= "and (lower(bankacc_number) like '%$searchsql%' " .
			"or lower(c_bp_person) like '%$searchsql%' " .
			"or lower(bankacc_comment) like '%$searchsql%' " .
			"or lower(bankacc_name) like '%$searchsql%' " .
			"or REPLACE(c_bp_phone,\"-\",\"\") like '%$searchsql%') ";
if($order){$sql .= "order by $order $sort ";}*/
$search = strtolower($search);
$searchsql = $obj->convert_char($search);

$sql1 = "select al_bankacc_cms.*,al_accomodations.acc_name as company_name from al_bankacc_cms,al_accomodations ";
$sql1 .= "where al_bankacc_cms.tb_name='al_accomodations' " .
		"and al_bankacc_cms.c_bp_id=al_accomodations.acc_id ";
if(!$showinactive){$sql1 .= "and al_bankacc_cms.bankacc_active=1 ";}
$sql1 .= "and (lower(al_bankacc_cms.bankacc_number) like '%$searchsql%' " .		
			"or lower(al_accomodations.acc_name) like '%$searchsql%' " .
			"or lower(al_bankacc_cms.c_bp_person) like '%$searchsql%' " .
			"or lower(al_bankacc_cms.bankacc_comment) like '%$searchsql%' " .
			"or lower(al_bankacc_cms.bankacc_name) like '%$searchsql%' " .
			"or REPLACE(al_bankacc_cms.c_bp_phone,\"-\",\"\") like '%$searchsql%') ";

$sql2 = "select al_bankacc_cms.*,al_bookparty.bp_name as company_name from al_bankacc_cms,al_bookparty ";
$sql2 .= "where al_bankacc_cms.tb_name='al_bookparty' " .
		"and al_bankacc_cms.c_bp_id=al_bookparty.bp_id ";
if(!$showinactive){$sql2 .= "and al_bankacc_cms.bankacc_active=1 ";}
$sql2 .= "and (lower(al_bankacc_cms.bankacc_number) like '%$searchsql%' " .
			"or lower(al_bookparty.bp_name) like '%$searchsql%' " .
			"or lower(al_bankacc_cms.c_bp_person) like '%$searchsql%' " .
			"or lower(al_bankacc_cms.bankacc_comment) like '%$searchsql%' " .
			"or lower(al_bankacc_cms.bankacc_name) like '%$searchsql%' " .
			"or REPLACE(al_bankacc_cms.c_bp_phone,\"-\",\"\") like '%$searchsql%') ";

$sql3 = "select *,'' as company_name from al_bankacc_cms ";
$sql3 .= "where c_bp_id=0 ";
if(!$showinactive){$sql3 .= "and bankacc_active=1 ";}
$sql3 .= "and (lower(bankacc_number) like '%$searchsql%' " .
			"or lower(c_bp_person) like '%$searchsql%' " .
			"or lower(bankacc_comment) like '%$searchsql%' " .
			"or lower(bankacc_name) like '%$searchsql%' " .
			"or REPLACE(c_bp_phone,\"-\",\"\") like '%$searchsql%') ";

$sql = "($sql1) union ($sql2)  union ($sql3) ";
if($order){$sql .= "order by $order $sort ";}

//echo $sql;

$chkrs = $obj->getResult($sql);
$rows=$obj->getShowpage();
if(!$page){$page="1";}
$start = $rows*$page - $rows;
if($page){$sql .= "limit $start,$rows";}	
$rs = $obj->getResult($sql);
?>

<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%">
			<table cellspacing="0" border="0" cellpadding="0" width="100%" class="generalinfo">
			
			<tr>
			 <?for($i=0;$i<($cnt_column);$i++){?>
			 <td width="<?=100/($cnt_column)?>%"></td>
			 <?	}?>
			</tr>
	
			<tr bgcolor="#88afbe" height="32">
<?
//start field name generate
for($i=0;$i<$column;$i++){
	if($arrFormType[$i]!="submit"&&$arrFormType[$i]!="button"&&$arrShowinList[$i]!="no"){
		if($order==$arrFields[$i] && !$export){ 
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
<? if(!$export){?>
					<td style="text-align:center;background-color:#a8c2cb;">
					<b>View Log</b>
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
				if($arrFields[$j]=='bank_id') {
					$data=$obj->getIdToText($rs[$i]['bank_id'],"l_bankname","bank_Ename","bank_id");
				}else if($arrFields[$j]=='c_bp_id') {
					if($rs[$i]['tb_name']=="al_accomodations"){$data=$obj->getIdToText($rs[$i]['c_bp_id'],"al_accomodations","acc_name","acc_id");}
					else{$data=$obj->getIdToText($rs[$i]['c_bp_id'],"al_bookparty","bp_name","bp_id");}
				}else if($arrFields[$j]=='c_lu_user') {
						$data=$obj->getIdToText($rs[$i]['c_lu_user'],"s_user","u","u_id");
				}else if($arrFields[$j]=='c_lu_date') {	
						list($date,$time) = explode(" ",$rs[$i]["$arrFields[$j]"]);
						$data = ($date=="0000-00-00")?"-":$dateobj->timezone_global($date,$time,"$sdateformat H:i:s");
				}else if($arrFields[$j]=='bankacc_active') {
					if($chkPageEdit){
						if($rs[$i]["bankacc_active"]==1){
							$data = "<a href=\"javascript:;\" onClick=\"setEnable('al_bankacc_cms',".$rs[$i]["bankacc_cms_id"].",0);\"\>".
								 "<img src=\"/images/active.png\" border=\"0\" title=\"active\" /></a>";
						}else{
							$data = "<a href=\"javascript:;\" onClick=\"setEnable('al_bankacc_cms',".$rs[$i]["bankacc_cms_id"].",1);\">".
									"<img src=\"/images/inactive.png\" border=\"0\" title=\"expired\" /></a>";
						}
					}else{
						if($rs[$i]["bankacc_active"]==1){
							$data = "<img src=\"/images/active.png\" border=\"0\" title=\"active\" />";
						}else{
							$data = "<img src=\"/images/inactive.png\" border=\"0\" title=\"active\" />";
						}
					}
				}else if($arrFields[$j]=='bankacc_number'||$arrFields[$j]=='c_bp_person'||$arrFields[$j]=='c_bp_phone'||$arrFields[$j]=='bankacc_name'||$arrFields[$j]=='bankacc_comment'){
						$data = $obj->hightLightChar($search,$rs[$i]["$arrFields[$j]"]);
				}else if($arrFields[$j]=='company_name'){
						$data = $obj->hightLightChar($search,$rs[$i]["$arrFields[$j]"]);
				}else {$data = $rs[$i]["$arrFields[$j]"];}
?>
			<td class="report"><?=$data?>&nbsp;</td>
<?		}
	}
	if($chkPageEdit && !$export){
		echo "<td class=\"report\"><a href=\"javascript:;\" onclick=\"editData('$tbname','".$rs[$i]["bankacc_cms_id"]."')\">update</a></td>";
	}
?>
	<td class="report"><input name="view_log" id="view_log" type="button" value="view" onClick="window.open('banklog.php?bankacc_cms_id=<?=$rs[$i]['bankacc_cms_id']?>','Banklog<?=$rs[$i]['bankacc_cms_id']?>','location=0,toolbar=0,directoris=0,status=0,menubar=0,scrollbars=1,resizable=0')" style="font-size:11px"></td>
<?
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