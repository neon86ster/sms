<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");
$obj = new formdb(); 

//query language
$chksql = "select * from s_pagename ";
$search = strtolower($search);
$chksql .= "where active=1 ";
// specific options select
$pageindex=$obj->getParameter("pageindex",0);
if($pageindex){
	$chksql .= "and page_parent_id=$pageindex ";
	$chksql .= "and page_refer=0 ";
	if($pageindex==1||$pageindex==3||$pageindex==6||$pageindex==66)
		$chksql .= "or page_id=$pageindex ";
}else{
	$chksql .= "and page_parent_id=0 ";
}
$chksql .= "order by page_priority ";
$prs = $obj->getResult($chksql);

$add = $obj->getParameter("add");
if($add && $hasSession){
	$msg="";
	$errmsg="";
	$tcms = $obj->getParameter("tcms");
	$srchk = $obj->getParameter("srchk");
	$apptdatechk = $obj->getParameter("apptdatechk");
	$lastgroupid = $obj->getParameter("last_groupid");
	$lastpageid = $obj->getParameter("last_pageid");
	if($pageindex=="cc"){
		$sql="update s_group set cms_update_time=0";
		$id = $obj->setResult($sql);
	}
	for($i=1;$i<=$lastgroupid;$i++) {
		if($pageindex!="cc"){
			for($j=$pageindex;$j<=$lastpageid;$j++) {
				$gpage =$obj->getParameter("prs");	
				if($gpage[$i][$j]==0){
					$group_id=$i;$page_id=$j;$set_view=1;$set_edit=0;
				} 
				if($gpage[$i][$j]==1){
					$group_id=$i;$page_id=$j;$set_view=1;$set_edit=1;
				}
				//echo $group_id." ".$page_id." ".$set_view." ".$set_edit."<br/>";
				
				$ck_intable = $object->checks_gpageIntable($i,$j,false);
				if($ck_intable!=false){
					//echo $group_id." ".$page_id." ".$set_view." ".$set_edit."<br/>";
					//echo "ck_intable group-$i,page-$j: id-".$ck_intable." <br>";
					$ck_pageparent=$object->checks_gpageParent($j,$pageindex,false);
					//echo "<br/>ck_pageparent page-$j,parent-$pageindex: ".(($ck_pageparent)?$ck_pageparent:"false")." <br>";
					if(isset($gpage[$i][$j])){
						$object->edits_gpage($ck_intable,$set_view,$set_edit);
						$msg = "Update Page Permission for Group Complete!!";
					}
					if(!isset($gpage[$i][$j])&&$ck_pageparent!=false) {
						//echo "del $ck_intable<br/>";
						$page = $obj->getIdToText($ck_intable,"s_gpage","page_id","gpage_id");
						//echo "$pageindex, ".$obj->getIdToText($ck_intable,"s_gpage","page_id","gpage_id")."<br/>";
						if($pageindex==4){
							$object->dels_gpage($ck_intable,false);
						}else{
							if($page<21||$page>27){
								$object->dels_gpage($ck_intable,false);
							}
						}
						$msg = "Update Page Permission for Group Complete!!";
					}
					//echo "<br/>";
				}else if($ck_intable==false){
					//echo "ck_intable group-$i,page-$j: isn't in table <br>";
					if(isset($gpage[$i][$j])){
						//echo "insert<br/>";
						$object->adds_gpage($group_id,$page_id,$set_view,$set_edit);
						$msg = "Update Page Permission for Group Complete!!";
					}
				}
			}
		}else{
			if(isset($tcms[$i])){
				$sql="update s_group set cms_update_time=".$tcms[$i]." where group_id=".$i;
				$id = $obj->setResult($sql);
			}
			$sql="update s_group set sr_chk=".$srchk[$i].",apptdate_chk=".$apptdatechk[$i]." where group_id=".$i;
			$id = $obj->setResult($sql);
			if($id){
					$msg = "Update Booking Permission for Group Complete!!";
			}
		}	
	}
}
$groupsql = "select * from s_group " .
				"where active=1 ";
if(!$isAdminExpert){
	$groupsql .= "and top_user=0 ";
}
$grs = $obj->getResult($groupsql);
$last_groupid=0;
$last_pageid=0;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%">
			<table cellspacing="0" border="0" cellpadding="0" width="100%" class="generalinfo">
				<tr height="35">
					<td style="text-align:center;background-color:#a8c2cb;white-space:nowrap;" rowspan="2">
						<b>Group For User</b>
					</td>
<?
//start field name generate
  	if($pageindex!="cc"){
  		
  		for($j=0;$j<$prs["rows"];$j++){

		//generate main header of page name level 1
?>
					<td style="text-align:center;background-color:#a8c2cb;" colspan="2">
					<b><?=$prs[$j]["page_name"]?></b>
					</td>
		<? 		
		if($prs[$j]["page_id"]>$last_pageid){$last_pageid=$prs[$j]["page_id"];}
		if($prs[$j]["page_id"]==13||$prs[$j]["page_id"]==12){
			$chksql = "select page_id,page_name from s_pagename " .
						"where page_parent_id=".$prs[$j]["page_id"]." " .
						"and active=1 ";
			$chkrs = $obj->getResult($chksql);		
			for($k=0;$k<$chkrs["rows"];$k++){
				?>
				<td style="text-align:center;background-color:#a8c2cb;" colspan="2">
					<b><?=$chkrs[$k]["page_name"]?></b>
				</td>
				<?
				if($chkrs[$k]["page_id"]>$last_pageid){$last_pageid=$chkrs[$k]["page_id"];}
			}
		}
		
		}
  	}else { 
		?>
				<td style="text-align:center;background-color:#a8c2cb;" rowspan="2" title="Sepecific date range for view/edit booking,By default haven\'t view/edit limited.">
					<b>Specific view/edit date range</b>
				</td>
				<td style="text-align:center;background-color:#a8c2cb;" rowspan="2">
					<b>Sale Receipt Permission</b>
				</td>
				<td style="text-align:center;background-color:#a8c2cb;" rowspan="2">
					<b>Commission Update Time</b>
				</td>
<? } ?>
			</tr><tr>
		                    <? 
		                    if($pageindex!="cc"){
			                    for($j=0;$j<$prs["rows"];$j++){
			                    	//For generate main header of view,edit level 1
			                    	echo "<td height=\"20\" style=\"text-align:center;background-color:#a8c2cb;border-top:#eae8e8 1px solid;\" width=\"80px\"><b>view</b></td>" .
			                    		 "<td style=\"text-align:center;background-color:#a8c2cb;border-top:#eae8e8 1px solid;\" width=\"80px\"><b>edit</b></td>\n";
			                    	if($prs[$j]["page_id"]==13||$prs[$j]["page_id"]==12){
			                    		$chksql = "select page_id,page_name from s_pagename " .
				                    				"where page_parent_id=".$prs[$j]["page_id"]." " .
				                    				"and active=1 " .
				                    				"order by page_priority ";
						 				$chkrs = $obj->getResult($chksql);
						 				for($k=0;$k<$chkrs["rows"];$k++){
						 					//For generate main header of view,edit level 2
					                    	echo "<td style=\"text-align:center;background-color:#a8c2cb;border-top:#eae8e8 1px solid;\" width=\"80px\"><b>view</b></td>" .
					                    		 "<td style=\"text-align:center;background-color:#a8c2cb;border-top:#eae8e8 1px solid;\" width=\"80px\"><b>edit</b></td>\n";
						 				}
						 			}		
			                    }
		                    } ?>
	                   	</tr>
	                <?   	for($i=0; $i<$grs["rows"]; $i++) {
					
	                			echo ($i%2==0)?"<tr class=\"even\">":"<tr class=\"odd\">";
								
	                			if($grs[$i]["group_id"]>$last_groupid){$last_groupid=$grs[$i]["group_id"];}
	                ?>
	                    	<td style="white-space:nowrap;">
								&nbsp;&nbsp;<?=$grs[$i]["group_name"]?>
							</td>
							<? if($pageindex!="cc"){	
								for($j=0;$j<$prs["rows"];$j++){
										$viewchk = $obj->getPageCheckbox($prs[$j]["page_id"],$grs[$i]["group_id"],"set_view");
	                					$editchk = $obj->getPageCheckbox($prs[$j]["page_id"],$grs[$i]["group_id"],"set_edit");
	                		?>
	                		<td>
	                			<input size="26" type="checkbox" name="prs[<?=$grs[$i]["group_id"]?>][<?=$prs[$j]["page_id"]?>]" value="0" <?=$viewchk?> onClick="chkDeselectEdit(this);">
							</td><td>
								<input size="26" type="checkbox" name="prs[<?=$grs[$i]["group_id"]?>][<?=$prs[$j]["page_id"]?>]" value="1" <?=$editchk?> onClick="chkSelectView(this);">
							</td>
								<?	if($prs[$j]["page_id"]==13||$prs[$j]["page_id"]==12){
			                    		$chksql = "select page_id,page_name from s_pagename " .
				                    				"where page_parent_id=".$prs[$j]["page_id"]." " .
				                    				"and active=1 " .
				                    				"order by page_priority ";
						 				$chkrs = $obj->getResult($chksql);
							 			for($k=0;$k<$chkrs["rows"];$k++){
											$viewchk = $obj->getPageCheckbox($chkrs[$k]["page_id"],$grs[$i]["group_id"],"set_view");
			                				$editchk = $obj->getPageCheckbox($chkrs[$k]["page_id"],$grs[$i]["group_id"],"set_edit");
	                		?>
					 		<td>
		                    		<input size="26" type="checkbox" name="prs[<?=$grs[$i]["group_id"]?>][<?=$chkrs[$k]["page_id"]?>]" value="0" <?=$viewchk?> onClick="chkDeselectEdit(this);">
							</td><td>
									<input size="26" type="checkbox" name="prs[<?=$grs[$i]["group_id"]?>][<?=$chkrs[$k]["page_id"]?>]" value="1" <?=$editchk?> onClick="chkSelectView(this);">
							</td>
						 			<? 	}
						 			} 		
								} 
							} else { 
								$srchk = ($grs[$i]["sr_chk"]==1)?"checked":"";
								$apptdatechk = ($grs[$i]["apptdate_chk"]==1)?"checked":"";
							?>
							<td>
									<input size="26" type="checkbox" id="apptdatechk[<?=$grs[$i]["group_id"]?>]" name="1" value="<?=$grs[$i]["group_id"]?>" <?=$apptdatechk?>/>
							</td>
							<td>
									<input size="26" type="checkbox" id="srchk[<?=$grs[$i]["group_id"]?>]" name="1" value="<?=$grs[$i]["group_id"]?>" <?=$srchk?>/>
							</td>
							<td>
									<select name="utcms[<?=$grs[$i]["group_id"]?>]" id="utcms[<?=$grs[$i]["group_id"]?>]" style="width: 150px;"> 
											<option value="0" title="Not lock on booking commission path" <?=($grs[$i]["cms_update_time"]==0)?"selected=\"selected\"":""?>>Unlimited</option> 
											<option value="1" title="lock on booking commission path after create booking 15 minutes" <?=($grs[$i]["cms_update_time"]==1)?"selected=\"selected\"":""?>>15</option> 
											<option value="2" title="lock on booking commission path after create booking 30 minutes" <?=($grs[$i]["cms_update_time"]==2)?"selected=\"selected\"":""?>>30</option> 
											<option value="3" title="lock on booking commission path after create booking 1 hours" <?=($grs[$i]["cms_update_time"]==3)?"selected=\"selected\"":""?>>60</option> 
									</select> 
									
							</td>
							<? } ?>
	                    </tr>
	                 <? } ?>
 			</table><br/>
		</td>
    </tr>
    <tr>
    	<td width="100%" align="center">
					<input type="hidden" id="id" name="id" value="<?=$_POST["id"]?>">
					<input name="add" id="add" type="button" size="" value=" save change " onClick='updatePagePermission(<?=$last_groupid?>,<?=$last_pageid?>)'>&nbsp; 
					<input name="cancel" id="cancel" type="submit" size="" value=" cancel " onClick="gotoURL('index.php','pageindex=0','tableDisplay');">
    	</td>
	</tr>
</table>