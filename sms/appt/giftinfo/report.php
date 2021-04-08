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
$gifttypeid=$obj->getParameter("gifttypeid",0);
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
$tbname = "g_gift";
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
$sql = "select * from g_gift ";
$search = strtolower($search);
$searchsql = $obj->convert_char($search);
// specific $search don't care about category select
if($gifttypeid){
$sql .= "where gifttype_id=$gifttypeid " .
		"and (lower(gift_number) like '%$searchsql%' " .
		"or lower(give_to) like '%$searchsql%' " .
		"or lower(receive_from) like '%$searchsql%' " .
		"or lower(product) like '%$searchsql%' " .
		"or lower(value) like '%$searchsql%') ";
}else{
$sql .= "where lower(gift_number) like '%$searchsql%' " .
	"or lower(give_to) like '%$searchsql%' " .
	"or lower(receive_from) like '%$searchsql%' " .
	"or lower(product) like '%$searchsql%' " .
	"or lower(value) like '%$searchsql%' ";
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
		echo "<tr class=\"odd\" height=\"20\">\n";
	}else{
		echo "<tr class=\"even\" height=\"20\">\n";
	}
	for($j=0;$j<$column;$j++){
		if($arrShowinList[$j]!="no") {
				$data = "";
				$chkarrFields = explode(".",$arrFields[$j]); //chk array field of order; some $arrFields is "tablename"."columnname"
				$arrFields[$j] = (count($chkarrFields)>1)?$chkarrFields[1]:$arrFields[$j];
				if($arrFields[$j]=='book_id') {
					$data = $obj->getIdToText($rs[$i]["book_id"],"c_bpds_link","bpds_id","tb_id","tb_name='a_bookinginfo'");
					if($data){
						$data = "<a href=\"javascript:;\" onClick=\"window.open('../../appt/manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."','bookingWindow".$rs[$i]["book_id"]."',
									'resizable=0,scrollbars=1');\" >$data</a>";
					}else{
						$data = $rs[$i]["book_id"];
					}
				}else if($arrFields[$j]=='id_sold') {
					$data = $obj->getIdToText($rs[$i]["id_sold"],"c_bpds_link","bpds_id","tb_id","tb_name='".$rs[$i]["tb_name"]."'");
					if($data){
						if($rs[$i]["tb_name"]=="a_bookinginfo"){
							$data = "<a href=\"javascript:;\" onClick=\"window.open('../../appt/manage_booking.php?chkpage=1&bookid=".$rs[$i]["id_sold"]."','bookingWindow".$rs[$i]["id_sold"]."',
									'resizable=0,scrollbars=1');\" >$data</a>";	
						}else{
							$data = "<a href=\"javascript:;\" onClick=\"window.open('../../appt/manage_pdforsale.php?pdsid=".$rs[$i]["id_sold"]."','managePds".$rs[$i]["id_sold"]."',
									'resizable=0,scrollbars=1');\" >$data</a>";
						}
					}else{
						$data = $rs[$i]["id_sold"];
					}
				}else if($arrFields[$j]=='available') {
					 if($rs[$i]["available"]==1){
							$data = "<img src=\"/images/active.png\" border=\"0\" title=\"active\" />";
					 }else{
							$data = "<img src=\"/images/inactive.png\" border=\"0\" title=\"active\" />";
					 }
				}else if($arrFields[$j]=='gifttype_id') {
						$data = $obj->getIdToText($rs[$i]['gifttype_id'],"gl_gifttype","gifttype_name","gifttype_id");
				}else if($arrFields[$j]=='receive_by_id') {
						$data = $obj->getIdToText($rs[$i]['receive_by_id'],"l_employee","emp_nickname","emp_id");
				}else if($arrFields[$j]=='l_lu_user') {
						$data=$obj->getIdToText($rs[$i]['l_lu_user'],"s_user","u","u_id");
				}else if($arrFields[$j]=='l_lu_date') {	
						list($date,$time) = explode(" ",$rs[$i]["$arrFields[$j]"]);
						$data = ($date=="0000-00-00")?"-":$dateobj->timezonefilter($date,$time,"$sdateformat H:i:s");
				}else if($arrFormType[$j]=='date') {
						$data = ($rs[$i]["$arrFields[$j]"]=="0000-00-00")?"-":$dateobj->convertdate($rs[$i]["$arrFields[$j]"],'Y-m-d',$sdateformat);
				}else if($arrFields[$j]=='gift_number'||$arrFields[$j]=='give_to'||$arrFields[$j]=='receive_from'||$arrFields[$j]=='product'||$arrFields[$j]=='value'){
						$data = $obj->hightLightChar($search,$rs[$i]["$arrFields[$j]"]);
				}else {$data = $rs[$i]["$arrFields[$j]"];}
?>
			<td class="report"><?=$data?>&nbsp;</td>
<?		}
	}
	if($chkPageEdit){
		echo "<td class=\"report\"><a href=\"javascript:;\" onclick=\"window.open('add_giftinfo.php?id=".$rs[$i]["gift_id"]."','update_giftinfo',
								'height=450,width=350,resizable=0,scrollbars=1');\">update</a></td>";
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