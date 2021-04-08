<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb(); 

// initial parameter 
$order=$obj->getParameter("order");
$sort=$obj->getParameter("sort");
$page = $obj->getParameter("page",1);
if(!$page){$page=1;}
$cityid = $obj->getParameter("cityid",1);
$search=$obj->getParameter("search","");
$categoryid=$obj->getParameter("categoryid",0);
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
$tbname = "m_membership";
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
$sql = "select * from m_membership ";
$search = strtolower($search);
$searchsql = $obj->convert_char($search);
// specific $search don't care about category select
if($categoryid){
$sql .= "where category_id=$categoryid " .
		"and (lower(member_code) like '%$searchsql%' " .
		"or lower(fname) like '%$searchsql%' " .
		"or lower(mname) like '%$searchsql%' " .
		"or lower(lname) like '%$searchsql%' " .
		"or lower(birthdate) like '%$searchsql%' " .
		"or REPLACE(mobile,\"-\",\"\") like '%$searchsql%') ";
}else{
$sql .= "where lower(member_code) like '%$searchsql%' " .
	"or lower(fname) like '%$searchsql%' " .
	"or lower(mname) like '%$searchsql%' " .
	"or lower(lname) like '%$searchsql%' " .
	"or lower(birthdate) like '%$searchsql%' " .
	"or REPLACE(mobile,\"-\",\"\") like '%$searchsql%' ";
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
<? 		if($arrFields[$i]=="expired"){		?>
					<td style="text-align:center;background-color:#a8c2cb;">
					<b>History</b>
					</td>
					<td style="text-align:center;background-color:#a8c2cb;">
					<b>Add</b>
					</td>

<? 			if($chkPageEdit){?>
					<td style="text-align:center;background-color:#a8c2cb;">
					<b>Edit</b>
					</td>
<?			}
		}
	}
} ?>
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
				if($arrFields[$j]=='nationality_id') {
					$nationality = $obj->getIdToText($rs[$i]['nationality_id'],"dl_nationality","nationality_name","nationality_id");
					$data = "$nationality<input type=\"hidden\" name=\"cs_nationality$i\" id=\"cs_nationality$i\" value=\"".$rs[$i]['nationality_id']."\"/>";
				}else if($arrFields[$j]=='sex_id') {
					$sex = $obj->getIdToText($rs[$i]['sex_id'],"dl_sex","sex_type","sex_id");
					$data="$sex<input type=\"hidden\" name=\"cs_sex$i\" id=\"cs_sex$i\" value=\"".$rs[$i]['sex_id']."\"/>";
				}else if($arrFields[$j]=='category_id') {
					$data = $obj->getIdToText($rs[$i]['category_id'],"mb_category","category_name","category_id");
				} else if($arrFields[$j]=='expired') {
					if($chkPageEdit){
						if($rs[$i]["$arrFields[$j]"]==1){
							$data = "<a href=\"javascript:;\" onClick=\"setEnable('m_membership',".$rs[$i]["member_id"].",0);\"\>".
								 "<img src=\"/images/active.png\" border=\"0\" title=\"active\" /></a>";
						}else{
							$data = "<a href=\"javascript:;\" onClick=\"setEnable('m_membership',".$rs[$i]["member_id"].",1);\">".
									"<img src=\"/images/inactive.png\" border=\"0\" title=\"expired\" /></a>";
						}
					}else{
						if($rs[$i]["$arrFields[$j]"]==1){
							$data = "<img src=\"/images/active.png\" border=\"0\" title=\"active\" />";
						}else{
							$data = "<img src=\"/images/inactive.png\" border=\"0\" title=\"active\" />";
						}
					}
				} else if($arrFormType[$j]=='textarea'){
						$data = str_replace("[br]","<br>",$rs[$i]["$arrFields[$j]"]);
				} else if($arrFormType[$j]=='date') {
						if($arrFields[$j]=='birthdate'){
							$data = ($rs[$i]["$arrFields[$j]"]=="0000-00-00")?"-":$obj->hightLightChar($search,$dateobj->convertdate($rs[$i]["$arrFields[$j]"],'Y-m-d',$sdateformat));
							$data .= "<input type=\"hidden\" name=\"cs_bday$i\" id=\"cs_bday$i\" value=\"".(($rs[$i]["birthdate"]=="0000-00-00")?"":$dateobj->convertdate($rs[$i]["birthdate"],"Y-m-d",$sdateformat))."\"/>
        								<input type=\"hidden\" name=\"cs_hiddenbday$i\" id=\"cs_hiddenbday$i\" value=\"".$rs[$i]["birthdate"]."\"/>";
						}else{
							if($rs[$i]["$arrFields[$j]"]=="0000-00-00"&&$arrFields[$j]=='expireddate'){
								$data = "Unlimited";
							}else{
								$data = ($rs[$i]["$arrFields[$j]"]=="0000-00-00")?"-":$dateobj->convertdate($rs[$i]["$arrFields[$j]"],'Y-m-d',$sdateformat);			
							}
						}
				} else if($arrFields[$j]=='member_code' || $arrFields[$j]=='fname'||$arrFields[$j]=='mname'||$arrFields[$j]=='lname'||$arrFields[$j]=='mobile') {
						$data = $obj->hightLightChar($search,$rs[$i]["$arrFields[$j]"]);
						
						$fieldsdata = $rs[$i]["$arrFields[$j]"];
						$data="$data<input type=\"hidden\" name=\"".$arrFields[$j]."$i\" id=\"".$arrFields[$j]."$i\" value=\"$fieldsdata\"/>";
				} else {
						$fieldsdata = $rs[$i]["$arrFields[$j]"];
						$data="$fieldsdata<input type=\"hidden\" name=\"".$arrFields[$j]."$i\" id=\"".$arrFields[$j]."$i\" value=\"$fieldsdata\"/>";
				}
?>
	<td class="report"><?=$data?>&nbsp;</td>
<? 	if($arrFields[$j]=='expired'){ ?>
	<td class="report">
	<a href="javascript:;" onClick="window.open('history_membership.php?memberId=<?=$rs[$i]["member_code"]?>&pageid=<?=$pageid?>','memberHistory',
		'scrollbars=1top=0, left=0, resizable=yes' +',width=' + (screen.width) +',height=' + (screen.height));" >History</a>
	</td>
	
	<?	if($rs[$i]["expired"]==1){ ?>	
	<td class="report">
	<input type="button" name="memberAdd" id="memberAdd" value="Add" href="javascript:;" onClick="editMember('<?=$i?>');"/>
	</td>
	<?	}else{ ?>
	<td class="report" align="center">-</td>
	<? } ?>
	
<?  if($chkPageEdit){ ?>
	<td class="report" align="center"><a href="javascript:;" onClick="window.open('add_membershipinfo.php?id=<?=$rs[$i]["member_id"]?>','update_membership',
										'height=650,width=350,resizable=0,scrollbars=1');">update</a></td>
<?	}
			}
		}
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