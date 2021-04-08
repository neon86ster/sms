<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

$obj->setShowpage(15);
$page = $obj->getParameter("page",1);
$branchid=$obj->getParameter("branchid",2);
$leave = $obj->getParameter("leave");
$thlistid = $obj->getParameter("thlistid");
$now = $obj->getParameter("now");
$sql = "select bl_th_list.*,bl_branchinfo.branch_name,l_employee.emp_nickname as therapist_name,l_employee.emp_code " .
		"from bl_th_list,bl_branchinfo,l_employee " .
		"where bl_branchinfo.branch_id=bl_th_list.branch_id " .
		"and bl_th_list.l_lu_date>=\"" . date("Y-m-d") . "\" " .	
		"and l_employee.emp_id=bl_th_list.th_id " .	
		"and bl_th_list.leave=0 ";
if($branchid>0){$sql .= "and bl_branchinfo.branch_id=$branchid ";}
$sql .= "order by bl_th_list.queue_order,bl_th_list.l_lu_date ";
$rs = $obj->getResult($sql);
$th_queue = array();
$th_queue[0] = "''";
for($i=0; $i<$rs["rows"]; $i++) {
	$th_queue[$i] = $rs[$i]["th_id"];
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%">
			<table cellspacing="0" border="0" cellpadding="0" width="100%" class="generalinfo">
				<tr height="32">
					<?  $style="background-color:#88afbe;" .
									"background-image: url('/images/arrow_down.png');" .
									"border-bottom: 3px solid #eae8e8";?>
					<td style="text-align:center;<?=$style?>">
					<a href="javascript:;" onclick="sortInfo('bl_th_list.branch_id',<?=$page?>)" class="pagelink">
					<b>Queue</b>
					</a>
					</td>
					<td style="text-align:center;background-color:#a8c2cb;">
					<a href="javascript:;" onclick="" class="pagelink">
					<b>Therapist Name</b>
					</a>
					<td style="text-align:center;background-color:#a8c2cb;">
					<a href="javascript:;" onclick="" class="pagelink">
					<b>Order</b>
					</a>
					</td>
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
	?>
					<td class="report" align="center"><?=$i+1?></td>
					<td class="report" align="center">
					<?=$obj->makeTherapistlist("th_queue[$i]",$rs[$i]["th_id"],0,"l_employee.branch_id,l_employee.emp_code,l_employee.emp_nickname","emp_id in (".implode(",",$th_queue).")")?>
					<input type="hidden" id="th_queue[<?=$i?>]" value="<?=$rs[$i]["th_list_id"]?>" />
					</td>
<?
					if($chkPageEdit){
						if($i==0){
							$data = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
									"<img src=\"/images/down.png\" name=\"down\" style=\"cursor: pointer;\" onclick=\"set_thQueue('".$rs[$i]["th_id"]."','$i','".($i+1)."')\">";
						}else if($i==$rs["rows"]-1){
							$data = "<img src=\"/images/up.png\" name=\"up\" style=\"cursor: pointer;\" onclick=\"set_thQueue('".$rs[$i]["th_id"]."','$i','".($i-1)."')\">" .
									"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						}else{
							$data = "<img src=\"/images/up.png\" name=\"up\" style=\"cursor: pointer;\" onclick=\"set_thQueue('".$rs[$i]["th_id"]."','$i','".($i-1)."')\">" .
									"<img src=\"/images/down.png\" name=\"down\" style=\"cursor: pointer;\" onclick=\"set_thQueue('".$rs[$i]["th_id"]."','$i','".($i+1)."')\">";
						}
						echo "<td class=\"report\" align=\"center\">$data</td>";
					}					
?>
 				</tr>
 				<?	} ?>
 			</table><br/>
    		<input type="hidden" id="th_cnt" value="<?=$rs["rows"]?>" >
		</td>
    </tr>
    <tr>
    	<td width="100%" align="center">
    	</td>
	</tr>
</table>