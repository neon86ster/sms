<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

$obj->setShowpage(15);
$order=$obj->getParameter("order");
$sort=$obj->getParameter("sort");
$page = $obj->getParameter("page",1);
$cityid = $obj->getParameter("cityid",1);
$branchid = $obj->getParameter("branchid",1);
$blid = $obj->getParameter("blid",1);
$leave = $obj->getParameter("leave");
$add = $obj->getParameter("add");
$thlistid = $obj->getParameter("thlistid");
$now = $obj->getParameter("now");

if($leave=="1"&&$thlistid) {
	$tmp = $obj->removeThList($thlistid);
	$leave_time = $obj->getIdToText($thlistid,"bl_th_list","leave_time","th_list_id");
	list($date,$time) = explode(" ",$leave_time);
	$local_leave_time = $dateobj->timezonefilter($date,$time,"Y-m-d H:i:s");
	$sql = "update bl_th_list set lc_leave_time=\"$local_leave_time\" where th_list_id=$thlistid";
	if($tmp){
		$tmp=$obj->setResult($sql);
	}
	if(!$tmp){
		$errormsg = $obj->getErrorMsg();
	}
}else if($add=="1"&&$thlistid) {
	$sql = "update bl_th_list set branch_id=\"$blid\" where th_list_id=$thlistid";
	$tmp = $obj->setResult($sql);
	if(!$tmp){
		$errormsg = $obj->getErrorMsg();
	}
}
$rs = $obj->getThList($cityid,$branchid,$page,$order,$sort);

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%">
			<table cellspacing="0" border="0" cellpadding="0" width="100%" class="generalinfo">
				<tr height="32">
					<?  if($order=="bl_th_list.branch_id"){ 
							$style="background-color:#88afbe;" .
									"background-image: url('/images/arrow_down.png');" .
									"border-bottom: 3px solid #eae8e8";
	 					}else{
	 						$style="background-color:#a8c2cb;";
	 					} ?>
					<td style="text-align:center;<?=$style?>">
					<a href="javascript:;" onclick="sortInfo('bl_th_list.branch_id',<?=$page?>)" class="pagelink">
					<b>Branch</b>
					</a>
					</td>
					<? if($order=="l_employee.emp_nickname"){ 
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
					<? if($order=="bl_th_list.l_lu_date"){ 
						$style="background-color:#88afbe;" .
									"background-image: url('/images/arrow_down.png');" .
									"border-bottom: 3px solid #eae8e8";
	 					}else{
	 						$style="background-color:#a8c2cb;";
	 					} ?>
					<td style="text-align:center;<?=$style?>">
					<a href="javascript:;" onclick="sortInfo('bl_th_list.l_lu_date',<?=$page?>)" class="pagelink">
					<b>Sign-in Time</b>
					</a>
					<? if($order=="th_shift"){ 
						$style="background-color:#88afbe;" .
									"background-image: url('/images/arrow_down.png');" .
									"border-bottom: 3px solid #eae8e8";
	 					}else{
	 						$style="background-color:#a8c2cb;";
	 					} ?>
					<td style="text-align:center;<?=$style?>">
					<a href="javascript:;" onclick="sortInfo('th_shift',<?=$page?>)" class="pagelink">
					<b>Out Time</b>
					</a>
					</td>
					<? if($order=="bl_th_list.leave_time"){ 
						$style="background-color:#88afbe;" .
									"background-image: url('/images/arrow_down.png');" .
									"border-bottom: 3px solid #eae8e8";
	 					}else{
	 						$style="background-color:#a8c2cb;";
	 					} ?>
	 					
	 				<td style="text-align:center;<?=$style?>">
					<a href="javascript:;" onclick="sortInfo('th_shift',<?=$page?>)" class="pagelink">
					<b>No OT </b>
					</a>
					</td>
					<? if($order=="bl_th_list.ot"){ 
						$style="background-color:#88afbe;" .
									"background-image: url('/images/arrow_down.png');" .
									"border-bottom: 3px solid #eae8e8";
	 					}else{
	 						$style="background-color:#a8c2cb;";
	 					} ?>
	 					
					<td style="text-align:center;<?=$style?>">
					<a href="javascript:;" onclick="sortInfo('bl_th_list.leave_time',<?=$page?>)" class="pagelink">
					<b>Leave</b>
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
$starthour = substr($rs[$i]["l_lu_date"],11,2);
$startmins = substr($rs[$i]["l_lu_date"],14,2);
$shifthour = substr($th_shift,0,2);$shiftmins = substr($th_shift,3,2);
$endtime = mktime((int)$shifthour+$starthour, (int)$startmins+$shiftmins, 0, 0, 0, 0);
$endtime = date("H:i:s", $endtime);
	?>
	
	<?
		//$ubranch_id = $obj->getIdToText($_SESSION["__user_id"], "s_user", "branch_id", "u_id");
		if($chkPageEdit){
	?>
		<td class="report" align="center">
		<?if($cityid){?>
		<?=$obj->makeListbox("branch_name[$i]","bl_branchinfo","branch_name","branch_id",$rs[$i]["branch_id"],0,"branch_name","branch_active","1","branch_name!='All' and city_id=$cityid ")?>&nbsp;
		<?}else{?>
		<?=$obj->makeListbox("branch_name[$i]","bl_branchinfo","branch_name","branch_id",$rs[$i]["branch_id"],0,"branch_name","branch_active","1","branch_name not like 'All'",false,false)?>&nbsp;	
		<?}?>
		<input type="button" name="blchange" value=" Sign In " onclick="chBranch(<?=$rs[$i]["th_list_id"].",$i,$cityid"?>)">&nbsp;</td>
		</td>
	<?
		}else{?>
					<td class="report" align="center"><?=$rs[$i]["branch_name"]?>&nbsp;</td>
	<? }?>
					<td class="report"><?=$rs[$i]["emp_code"]." ".$rs[$i]["therapist_name"]?>&nbsp;</td>
					<?
					list($date,$time) = explode(" ",$rs[$i]["l_lu_date"]);
					$data = $dateobj->timezonefilter($date,$time,"H:i");
					?>
					<td class="report" align="center"><?=$data?>&nbsp;</td>
					<?
					$data = $dateobj->timezonefilter(date("Y-m-d"),$endtime,"H:i");
					?>
					<td class="report" align="center"><?=$data?>&nbsp;</td>
					<td class="report" align="center">
					<?
					if($chkPageEdit){
						if($rs[$i]["ot"]==1){
							
							$data = "<a href=\"javascript:;\" onClick=editot('".$rs[$i]["th_list_id"]."','".$rs[$i]["ot"]."','setactive') >".
								 "<img src=\" /images/active.png\" border=\"0\" title=\"OT\" /></a>";
						}else{
							 
							$data = "<a href=\"javascript:;\" onClick=editot('".$rs[$i]["th_list_id"]."','".$rs[$i]["ot"]."','setactive') >".
									"<img src=\"/images/inactive.png\" border=\"0\" title=\"No OT\" /></a>";
						}
					}else{
						if($rs[$i]["ot"]==1){
							$data = "<img src=\"/images/active.png\" border=\"0\" title=\"OT\" />";
						}else{
							$data = "<img src=\"/images/inactive.png\" border=\"0\" title=\"No OT\" />";
						}
					}
	

					
					?>
					<?=$data?></td>
					<td class="report" align="center">
					
					<? if($rs[$i]["leave"]==1){ ?>
						<?
						list($date,$time) = explode(" ",$rs[$i]["leave_time"]);
						$data = $dateobj->timezonefilter($date,$time,"H:i");
						?>
						<?=" $data"?>&nbsp;
					<? }else{ ?>
						<? if($chkPageEdit){?>
						<input type="button" name="thleave" value=" Sign Out " onclick="removethlist(<?=$rs[$i]["th_list_id"].",$cityid"?>)">&nbsp;</td>
						<? }else{ ?>
						-
						<? } ?>
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
				$rs = $obj->getThList($cityid);
				$obj->gen_page("report.php",$page,$rs["rows"]);
			?>
			</font>
    	</td>
	</tr>
</table>