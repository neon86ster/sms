<?
session_start();
$_COOKIE["topicdir"] = '<a href="javascript:;" onclick="gotoURL(\'../../spamg/index.php\')" class="top_menu_link">Preferences </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../index.php\')" class="top_menu_link"> User_Permission </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'index.php\')" class="top_menu_link"> Group </a> > ';
$_COOKIE["topic"] = 'Manage User Group Permission';
$_COOKIE["back"] = 'index.php';

include("../../include.php");
require_once("formdb.inc.php");
require_once("secure.inc.php");
$obj = new formdb();
$scObj = new secure();

$errormsg ="";
$hasSession = true;
if(!$scObj->checkLogin()){
	$hasSession = false;
	$errormsg="Can't update data because session timeout. Please login and try again.";
	//header("Location: index.php");
}

$scObj->setDebugStatus(false);
$xml = "<command>".
			"<table>s_pagename</table>".
 			"<order>page_priority</order>".
			"<where name='active' operator='='>1</where>";
$pageindex=$obj->getParameter("pageindex");
$filename = '../user.xml';
if(isset($pageindex)){
	$xml .= "<where logic='and' name='page_parent_id' operator='='>".$pageindex."</where>";
	$xml .= "<where logic='and' name='page_refer' operator='='>0</where>";
	if($pageindex==1||$pageindex==3||$pageindex==6||$pageindex==66)
		$xml .= "<where logic='or' name='page_id' operator='='>".$pageindex."</where>";
} else {	
	$xml .= "<where logic='and' name='page_parent_id' operator='='>0</where>";
	$pageindex = 0;
}
	$xml .= "</command>";
$prs = $obj->getRsXML($xml,'../../object.xml');
if(isset($_POST["add"]) && $hasSession){
	$msg="";
	$errmsg="";
	$tcms = $_POST["tcms"];
	$srchk = $_POST["srchk"];
	$apptdatechk = $_POST["apptdatechk"];
	if($pageindex=="cc"){
		$sql="update s_group set cms_update_time=0";
		$id = $obj->setResult($sql);
	}
	for($i=1;$i<=$_POST["last_groupid"];$i++) {
		if($pageindex!="cc"){
			for($j=$pageindex;$j<=$_POST["last_pageid"];$j++) {
				$gpage =$_POST["prs"];	
				if($gpage[$i][$j]==0){
					$group_id=$i;$page_id=$j;$set_view=1;$set_edit=0;
				} 
				if($gpage[$i][$j]==1){
					$group_id=$i;$page_id=$j;$set_view=1;$set_edit=1;
				}
				//echo $group_id." ".$page_id." ".$set_view." ".$set_edit."<br/>";
				
				$ck_intable = $scObj->checks_gpageIntable($i,$j,false);
				if($ck_intable!=false){
					//echo $group_id." ".$page_id." ".$set_view." ".$set_edit."<br/>";
					//echo "ck_intable group-$i,page-$j: id-".$ck_intable." <br>";
					$ck_pageparent=$scObj->checks_gpageParent($j,$pageindex,false);
					//echo "<br/>ck_pageparent page-$j,parent-$pageindex: ".(($ck_pageparent)?$ck_pageparent:"false")." <br>";
					if(isset($gpage[$i][$j])){
						$scObj->edits_gpage($ck_intable,$set_view,$set_edit);
						$msg = "Update Page Permission for Group Complete!!";
					}
					if(!isset($gpage[$i][$j])&&$ck_pageparent!=false) {
						//echo "del $ck_intable<br/>";
						$page = $obj->getIdToText($ck_intable,"s_gpage","page_id","gpage_id");
						//echo "$pageindex, ".$obj->getIdToText($ck_intable,"s_gpage","page_id","gpage_id")."<br/>";
						if($pageindex==4){
							$scObj->dels_gpage($ck_intable,false);
						}else{
							if($page<21||$page>27){
								$scObj->dels_gpage($ck_intable,false);
							}
						}
						$msg = "Update Page Permission for Group Complete!!";
					}
					//echo "<br/>";
				}else if($ck_intable==false){
					//echo "ck_intable group-$i,page-$j: isn't in table <br>";
					if(isset($gpage[$i][$j])){
						//echo "insert<br/>";
						$scObj->adds_gpage($group_id,$page_id,$set_view,$set_edit);
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
//// For check is admin expert or not. By Ruck : 26-05-2009 ////
$isAdminExpert = $scObj->isAdminExpert();
$groupxml = "<command>".
              	"<table>s_group</table>".
    			"<order>group_id</order>".
				"<where name='active' operator='='>1</where>";
if(!$isAdminExpert){
	$groupxml .= "<where logic='AND' name='top_user' operator='='>0</where>";	
}
$groupxml .= "</command>";
$grs = $obj->getRsXML($groupxml,'../user.xml');
$last_groupid=0;
$last_pageid=0;
?>
<table class="main" cellspacing="0" cellpadding="0" height="100%" width="100%">
<tr><td width="6px" height="100%" align="center" rowspan="2" class="hidden_bar">&nbsp;</td>
<td height="64" valign="top" style="">
	<? include "mainhead.php"; ?>
</td></tr>
<tr><td valign="top" style="margin-top:0px;margin-left:0px">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td class="content" width="100%">
    		<div id="showmsg" <?  if($msg==""){?>style="display:none"<? } else {?>style="display:block"<? } ?>>
    			<table style="border: solid 3px #008000;" width="100%" cellspacing="0" cellpadding="10">
    				<tr>
    					<td><b><font style="color:#008000;">Success message: </font></b><?=$msg?></td>
    				</tr>
    			</table>
    		</div>
    		<div id="showerrormsg" <? if($errmsg=="" && $errormsg==""){?>style="display:none"<? } else {?>style="display:block"<? }?>>
    			<table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    				<tr>
    					<td><b><font style="color:#cc0000;">Error message: </font></b><?=$errormsg.$errmsg ?></td>
    				</tr>
    			</table>
    		</div>
    		<div align="right">
    			<div id="tooldiv" style="display:block;top:0px;left:0px;position:relative;" align="left">
	    			<fieldset>
						<legend><b>Specific group detail permission per page</b></legend>
	                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr><td>
                        	Page index :
                        <? 	$xml = "<command>".
					 				"<table>s_pagename</table>".
					 				"<field>page_id,page_name</field>".
					 				"<order>page_priority</order>".
					 				"<where name='page_parent_id' operator='='>0</where>".
					 				"<where logic='and' name='active' operator='='>1</where>" .
					 				"<where logic='and' name='page_id' operator='!='>78</where>" .
					 				"</command>";
					 		$rs = $obj->getRsXML($xml,'../../object.xml');
					 		
					 		$textout = "<select name=\"page_index\" id=\"page_index\"  onChange=\"getReturnText('manage_gpage.php','pageindex='+this.options[this.selectedIndex].value,'tableDisplay')\"> \n";
					 		
					 		$textout .= "<option value='0'>---select---</option> \n";
					 		
					 		for($i=0; $i<$rs["rows"];$i++) {
					 			$selected = ($pageindex==$rs[$i]["page_id"])?'selected':'';
					 			$textout .= "<option value=\"".$rs[$i]["page_id"]."\" $selected >".$rs[$i]["page_name"]."</option> \n";
					 		}
					 		$selected = "";
					 		if($pageindex=='cc')
					 			$selected = 'selected';
					 		$textout .= "<option value='cc' $selected>Booking financial accessibility</option> \n";
					 		$textout .= "</select> \n";
					 		echo $textout; ?>
     					</td></table>
     				</fieldset>
				</div>
     		    <a href="javascript:;" onClick="toggleToolDiv()" class="top_menu_link"><img id="toggletoolimg" src="../../images/search_hide.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; 
        	    <a href="javascript:;" onClick="toggleToolDiv()" class="top_menu_link"><span id="toggletooltxt">Hide Search</span></a>
        	</div>
        	<div>
    			<fieldset>
					<legend><b>Page Permission for Group</b></legend>
                    
					<table class="main_table_list" cellspacing="0" cellpadding="0">
	                    <tr>
		                    <td class="mainthead" width="150px" rowspan="2">Group For User</td>
		                    <? if($pageindex!="cc"){
		                    		for($j=0;$j<$prs["rows"];$j++){
		                    			//For generate main header of page name level 1
				                    	echo '<td class="mainthead" colspan="2">'.$prs[$j]["page_name"].'</td>';
				                    	if($prs[$j]["page_id"]>$last_pageid){$last_pageid=$prs[$j]["page_id"];}
				                    	if($prs[$j]["page_id"]==13||$prs[$j]["page_id"]==12){
								 			$chkxml = "<command>".
								 				"<table>s_pagename</table>".
								 				"<field>page_id,page_name</field>".
								 				"<order>page_priority</order>".
								 				"<where name='page_parent_id' operator='='>".$prs[$j]["page_id"]."</where>".
								 				"<where logic='and' name='active' operator='='>1</where>" .
								 				"</command>";
								 			$chkrs = $obj->getRsXML($chkxml,'../../object.xml');
									 		for($k=0;$k<$chkrs["rows"];$k++){
									 			//For generate main header of page name level 2
									 			echo '<td class="mainthead1" colspan="2">'.$chkrs[$k]["page_name"].'</td>';
						                    	if($chkrs[$k]["page_id"]>$last_pageid){$last_pageid=$chkrs[$k]["page_id"];}
									 		}
						 				}
						 			}		                    	
		                       }else{
		                       		echo '<td class="mainthead" rowspan="2" title="Sepecific date range for view/edit booking,By default haven\'t view/edit limited.">Specific view/edit date range</td>';
		                       		echo '<td class="mainthead" rowspan="2">Sale Receipt Permission</td>';
		                       		echo '<td class="mainthead" rowspan="2">Commission Update Time</td>';
		                       }
		                    ?>
	                   	</tr> 
	                   	<tr>
		                    <? 
		                    if($pageindex!="cc"){
			                    for($j=0;$j<$prs["rows"];$j++){
			                    	//For generate main header of view,edit level 1
			                    	echo '<td class="mainthead" width="80px">view</td><td class="mainthead" width="80px">edit</td>'."\n";
			                    	if($prs[$j]["page_id"]==13||$prs[$j]["page_id"]==12){
						 			$chkxml = "<command>".
						 				"<table>s_pagename</table>".
						 				"<field>page_id,page_name</field>".
						 				"<order>page_priority</order>".
						 				"<where name='page_parent_id' operator='='>".$prs[$j]["page_id"]."</where>".
						 				"<where logic='and' name='active' operator='='>1</where>" .
						 				"</command>";
						 				$chkrs = $obj->getRsXML($chkxml,'../../object.xml');
						 				for($k=0;$k<$chkrs["rows"];$k++){
						 					//For generate main header of view,edit level 2
			                    			echo '<td class="mainthead1" width="80px">view</td><td class="mainthead1" width="80px">edit</td>'."\n";
						 				}
						 			}		
			                    }
		                    } ?>
	                   	</tr>
	                <?   	for($i=0; $i<$grs["rows"]; $i++) {
	                			echo ($i%2==0)?'<tr class="content_list">':'<tr class="content_list1">';
	                			if($grs[$i]["group_id"]>$last_groupid){$last_groupid=$grs[$i]["group_id"];}
	                ?>
	                    	<td>
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
						 				$chkxml = "<command>".
									 				"<table>s_pagename</table>".
									 				"<field>page_id,page_name</field>".
									 				"<order>page_priority</order>".
									 				"<where name='page_parent_id' operator='='>".$prs[$j]["page_id"]."</where>".
									 				"<where logic='and' name='active' operator='='>1</where>" .
									 				"</command>";
							 			$chkrs = $obj->getRsXML($chkxml,'../../object.xml');
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
                    </table>
                </fieldset>
    			<fieldset>
					<legend> </legend>
					<br/>
					<input type="hidden" id="id" name="id" value="<?=$_POST["id"]?>">
					<input name="add" id="add" type="submit" size="" value=" save change " onClick='updatePagePermission(<?=$last_groupid?>,<?=$last_pageid?>)' >&nbsp; 
					<input name="cancel" id="cancel" type="submit" size="" value=" cancel " onClick="getReturnText('manage_guser.php','page=1','tableDisplay');" >
                	<div id="show"/>
                </fieldset>
			</div>
		</td>
    </tr>
</table>
</td>
</tr>
</table>
	<div class="hiddenbar"><img id="spLine" src="../../images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('../../images')"/></div>