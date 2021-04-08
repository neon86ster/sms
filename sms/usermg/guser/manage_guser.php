<?
session_start();
$_COOKIE["topicdir"] = '<a href="javascript:;" onclick="gotoURL(\'../../spamg/index.php\')" class="top_menu_link">Preferences </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../index.php\')" class="top_menu_link">User Permission </a> > ' .
		'Group > ';
$_COOKIE["topic"] = 'User Group Manager';
$_COOKIE["back"] = '../index.php';
include("../../include.php");
require_once("formdb.inc.php");
require_once("secure.inc.php");
$obj = new formdb(); 
$scObj = new secure();
$dateobj = new convertdate(); 
$xml = "<command>".
 	"<table>s_group</table>".
	"<usejoin>no</usejoin>";
$showInactive="";
$link="";

//// For check is admin expert or not. By Ruck : 26-05-2009 ////
$isAdminExpert = $scObj->isAdminExpert();
$showAdminExpert="";
if(!$isAdminExpert){
	$showAdminExpert="<where logic='frontBracket'></where>";
	$showAdminExpert .= "<where logic='AND' name='top_user' operator='='>0</where>";	
}

$_GET["where"]=$obj->getParameter("where",""); //For debug undefined index: where. By Ruck 12-05-2009  

if(!$_GET["show_inactive"] && !$_GET["where"]){
	$showInactive="<where logic='AND' name='active' operator='='>1</where>";
}else if(!$_GET["where"]){
	$link="&show_inactive=true";
}
	
if($_GET["order"]){	
	$xml .= "<order>".$_GET["order"]."</order>";
} else {
	$xml .= "<order>group_name desc</order>";
}	
if($_GET["page"]){	
$xml .= "<page>".$_GET["page"]."</page>";
} else {
$xml .= "<page>1</page>";
}
if($_GET["where"]){
	$xml .= "<status>search</status>".
			"<where name='description' operator='like'>%".$_GET["where"]."%</where>".
			"<where logic='OR' name='group_name' operator='like'>%".$_GET["where"]."%</where>";
}else{
	$xml .="<status>search</status>".$showInactive;
			
}
$xml.=$showAdminExpert;
$xml .= "</command>";
$obj->setDebugStatus(false);
$filename = '../user.xml';
$url = 'manage_guser.php';
$chksql = "select long_date,short_date from a_company_info";
$chkrs =$obj->getResult($chksql);
$sdateformat = $obj->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
$ldateformat = $obj->getIdToText($chkrs[0]["long_date"],"l_date","date_format","date_id");

//////////// Check user login /////////////////////
$scObj->checkLogin();
//////////// End check user login /////////////////
//////////// For check permission to access edit page /////////////////////
if($scObj->isPageEdit($_SERVER["PHP_SELF"])){
	//echo "Can Access Edit";
	$chkPageEdit=true;
}else{
	//echo "Can't Access Edit";
	$chkPageEdit=false;
}
//////////// End check permission to access edit page /////////////////////
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
    		<div id="showmsg" <? if(!$_GET["msg"]) { ?>style="display:none"<? } ?>>
    			<table style="border: solid 3px #008000;" width="100%" cellspacing="0" cellpadding="10">
    				<tr>
    					<td><b><font style="color:#008000;">Success message: </font></b><?=$_GET["msg"]?></td>
    				</tr>
    			</table>
    		</div>
        	<div align="right">
    			<div id="tooldiv" style="display:block;top:0px;left:0px;position:relative;" align="left">
    				<fieldset>
					<legend><b>User Group Search</b></legend>
				        <table border="0" cellpadding="0" cellspacing="0" width="100%">
				        <tr>
                    	<td valign="bottom">
                        <div style="vertical-align:inherit">
                        	<table><tr><td>
		     					<input type="text" name="search" id="search" <?=($_GET["where"])?"value='".$_GET["where"]."'":""?>/>
		     					<input type="hidden" name="page" id="page" value="<?=$_GET["page"]?>"/>
		     					<input type="hidden" name="order" id="order" value="<?=$_GET["order"]?>"/>
		     					&nbsp;&nbsp;
		        			    <a href="javascript:;" onClick="gotoSearch('manage_guser.php')" class="top_menu_link"><img src="../../images/btn_search_bg.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; <a href="javascript:;" onClick="gotoSearch('manage_guser.php')" class="top_menu_link">Search</a> &nbsp;
		        			    <a href="javascript:;" onClick="getReturnText('manage_guser.php','page=1<?=(($_GET["show_inactive"])?"&show_inactive=true":"")?><?=(($_GET["show_detail"])?"&show_detail=1":"")?>','tableDisplay')" class="top_menu_link"><img src="../../images/btn_show-all_bg.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; 
		        			    <a href="#" onClick="getReturnText('manage_guser.php','page=1<?=(($_GET["show_inactive"])?"&show_inactive=true":"")?><?=(($_GET["show_detail"])?"&show_detail=1":"")?>','tableDisplay')" class="top_menu_link">View All</a> 
		        			    </td>
			                    <td height="30px">
	                        	&nbsp;&nbsp;<input id='show_detail' type='checkbox' name='show_detail' value='1' onClick="selectboxSearch('manage_guser.php','<? echo ($_GET["where"])?"&where=".$_GET["where"]:""?>')" <? echo ($_GET["show_detail"])?"checked":""?> />&nbsp;&nbsp;Detail 
	     						&nbsp;  &nbsp; <input id='show_inactive' type='checkbox' name='show_inactive' value='1' onClick="showInactive('manage_guser.php')" <? echo ($_GET["show_inactive"])?"checked":""?> /> Show Inactive
	     					</td></tr></table>
                        </div></td>
                    </tr>
				  		</table>
               		</fieldset>
			    </div>
        	    <a href="javascript:;" onClick="toggleToolDiv()" class="top_menu_link"><span id="toggletooltxt">Hide Search</span></a>&nbsp; 
        	    <a href="javascript:;" onClick="toggleToolDiv()" class="top_menu_link"><img id="toggletoolimg" src="../../images/search_hide.gif" alt="search" width="16" height="16" border="0"/></a>
			</div>
			<div>
    				<fieldset>
					<legend><b>User groups</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style='overflow:auto'>
                    <tbody>
                    <tr>
                    	<td colspan="2">
                        <div id="UserGroupList">
                        <br/>
                        <?
			$f = simplexml_load_file('../user.xml');
			$e = simplexml_load_string($xml);
			
			//load data form xml command
			$tbname = $e->table;
			$sql = $e->sql;
			$field = $obj->checkParameter($e->field," * ");
			
			$where = $e->where;
			$order = $e->order;
			$page = $obj->checkParameter($e->page,-1);
			$stringDir=explode(" ",$order);
			$dir = $stringDir[1];
			$usejoin = $obj->checkParameter($e->usejoin,"no");
			//load field name form object.xml
			$element = $f->table->$tbname;
			$showpage = $element->showpage;
			$records_per_page = $showpage["value"];
			$i = 0;
			$arrFields = array();
			foreach($element->field as $fi){
				if(!$_GET["show_detail"]){
					if($fi["name"]!="l_lu_user"&&$fi["name"]!="l_lu_date"&&$fi["name"]!="l_lu_ip"){
						$arrFields[$i] = $fi["name"];
						$arrFieldsname[$i] = $fi["formname"];
						$arrFormType[$i] = $fi["formtype"];
						$arrShowinform[$i] = $fi["showinform"];
						$arrShowinList[$i] = $fi["showinList"];
						$i++;
					}
				} else {
					$arrFields[$i] = $fi["name"];
					$arrFieldsname[$i] = $fi["formname"];
					$arrFormType[$i] = $fi["formtype"];
					$arrShowinform[$i] = $fi["showinform"];
					$arrShowinList[$i] = $fi["showinList"];
					$i++;
				}
			} 
			$column = count($arrFields);
			$eid = $element->idfield;
			$idfield = $eid["name"];;
			$ename = $element->namefield;
			$namefield = $ename["name"];
			if($order==''){
				$order=$namefield;
				$dir = 'desc';
			} else {
				//change $order to store order by "field" 
				$strDir=explode(" ",$order);
				if($strDir[1]==$dir){$orderfield=$strDir[0];}
			}
			$dir = $obj->checkParameter($dir,'desc');
			if(strlen($order)>0){
				$sortupimg = ' <img src="../../images/arrow_up.gif" border="0" /> ';
				$sortdownimg = ' <img src="../../images/arrow_down.gif" border="0" /> ';
				
				//start field name generate
				if($dir=='desc'){
					$textout .= " \n".'<tr>';
					for($i=0;$i<$column;$i++){
						if($arrFormType[$i]!="submit"&&$arrFormType[$i]!="button"&&$arrShowinList[$i]!="no"){
							
							$textout .= ("$orderfield"==$arrFields[$i])?" \n".'<td class="sort"><a href=\'javascript:;\' onClick=\'getReturnText("'.$url.'","order='.$arrFields[$i].' asc'.(($_GET["where"])?"& where=".$_GET["where"]:"").(($_GET["page"])?"& page=".$_GET["page"]:"").(($_GET["show_inactive"])?"&show_inactive=true":"").(($_GET["show_detail"])?"&show_detail=1":"").'","tableDisplay");\'> '.$arrFieldsname[$i].'</a> '.$sortdownimg.'</td>':" \n".'<td class="mainthead"><a href=\'javascript:;\' onClick=\'getReturnText("'.$url.'","order='.$arrFields[$i].' asc'.(($_GET["where"])?"& where=".$_GET["where"]:"").(($_GET["page"])?"& page=".$_GET["page"]:"").(($_GET["show_inactive"])?"&show_inactive=true":"").(($_GET["show_detail"])?"&show_detail=1":"").'","tableDisplay");\'> '.$arrFieldsname[$i].'</a></td>';
						}
					}
					if($chkPageEdit){
						$textout .= " \n".'<td class="mainthead">Edit</td></tr>';	
					}else{
						$textout .= '</tr>';
					}
				}else if($dir=='asc'){
					$textout .= " \n".'<tr class="txtheader">';
					for($i=0;$i<$column;$i++){
						if($arrFormType[$i]!="submit"&&$arrFormType[$i]!="button"&&$arrShowinList[$i]!="no"){
							$textout .= ("$orderfield"=="$arrFields[$i]")?" \n".'<td class="sort"><a href=\'javascript:;\' onClick=\'getReturnText("'.$url.'","order='.$arrFields[$i].' desc'.(($_GET["where"])?"& where=".$_GET["where"]:"").(($_GET["page"])?"& page=".$_GET["page"]:"").(($_GET["show_inactive"])?"&show_inactive=true":"").(($_GET["show_detail"])?"&show_detail=1":"").'","tableDisplay");\'> '.$arrFieldsname[$i].'</a> '.$sortupimg.'</td>':" \n".'<td class="mainthead"><a href=\'javascript:;\' onClick=\'getReturnText("'.$url.'","order='.$arrFields[$i].' desc'.(($_GET["where"])?"& where=".$_GET["where"]:"").(($_GET["page"])?"& page=".$_GET["page"]:"").(($_GET["show_inactive"])?"&show_inactive=true":"").(($_GET["show_detail"])?"&show_detail=1":"").'","tableDisplay");\'> '.$arrFieldsname[$i].'</a></td>';
						}		
					}
					if($chkPageEdit){
						$textout .= " \n".'<td class="mainthead">Edit</td></tr>';	
					}else{
						$textout .= '</tr>';
					}
				}
				//end field name generate
				
				//start field element generate
				$chkxml = "<command>
					<table>$tbname</table>
					<usejoin>$usejoin</usejoin>
					<field>$field</field>
					<order>$order</order>";
				if($_GET["where"]){
					$chkxml .=  "<status>search</status>".
								"<where name='description' operator='like'>%".$_GET["where"]."%</where>".
								"<where logic='OR' name='group_name' operator='like'>%".$_GET["where"]."%</where>";
					}
				if(!$_GET["show_inactive"]){
					$chkxml.=$showInactive;
				}
				$chkxml.=$showAdminExpert;
				$chkxml .= "</command>"; 		//chk total record form db
				
				//echo $obj->encodeText($xml)."<br />";
				//echo $tbname.", ".$sql.", ".$field.", ".$where.", ".$order.", ".$page.", ".$dir."<br />";
				$rs1 = $obj->getRsXML($chkxml,$filename,$debug);
				$rs = $obj->getRsXML($xml,$filename,$debug);
				
				if($rs["rows"]>0){
					for($i=0;$i<$rs["rows"];$i++){
						$textout .= ($i%2==0)?" \n".'<tr class="content_list" onMouseOver="high(this)" onMouseOut="low(this)">':'<tr class="content_list1" onMouseOver="high(this)" onMouseOut="low(this)">';
						for($j=0;$j<$column;$j++){
							if($arrShowinList[$j]!="no") {
								$align = ("$arrFields[$j]"==$namefield)?"left":"right";
								$chkarrFields = explode(".",$arrFields[$j]); //chk array field of order;
								$arrFields[$j] = (count($chkarrFields)>1)?$chkarrFields[1]:$arrFields[$j];
								
								if($arrFields[$j]=='branch_id') {
										$data=$obj->getIdToText($rs[$i]['branch_id'],"bl_branchinfo","branch_name","branch_id");
										$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$data.'</td>';
								}else if($arrFields[$j]=='l_lu_user') {
										$data=$obj->getIdToText($rs[$i]['l_lu_user'],"s_user","u","u_id");
										$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$data.'</td>';
								} else if($arrFields[$j]=='l_lu_date') {
										list($date,$time) =  split('[ ]', $rs[$i]["l_lu_date"]);
										$date = $dateobj->convertdate($date,'Y-m-d',$ldateformat);
										$time = $dateobj->converttime($time,'H:i:s','H:i:s');
										$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$date." ".$time.'</td>';
								} else if($arrFields[$j]=='active') {
									if($chkPageEdit){
										if($rs[$i]["$arrFields[$j]"]==1){
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='left'>".
														"<a href=\"javascript:;\" onClick=\"javascript:setEnable('s_group',".$rs[$i]["group_id"].",0);\" class=\"top_menu_link\">".
														"<img src='../../images/active.png' border='0' title='active' /></a>".
														"</td>";
										}else{
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='left'>".
														"<a href=\"javascript:;\" onClick=\"javascript:setEnable('s_group',".$rs[$i]["group_id"].",1);\" class=\"top_menu_link\">".
														"<img src='../../images/inactive.png' border='0' title='inactive' /></a>".
														"</td>";
										}
									}else{
										if($rs[$i]["$arrFields[$j]"]==1){
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='left'>".
														"<img src='../../images/active.png' border='0' title='active' />".
														"</td>";
										}else{
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='left'>".
														"<img src='../../images/inactive.png' border='0' title='inactive' />".
														"</td>";
										}
									}
										
								}else if($arrFields[$j]=='group_name'||$arrFields[$j]=='description'){
									$data=$obj->hightLightChar($_GET["where"],$rs[$i]["$arrFields[$j]"]);
									$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$data.'</td>';
								} else {$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$rs[$i]["$arrFields[$j]"].'</td>';}
							}
						}
						if($chkPageEdit){
							$textout .= " \n".'<td><a href="javascript:;" onclick="editData(\''.$tbname.'\','.$rs[$i]["$arrFields[0]"].')">update</a></td></tr>';					
						}else{
							$textout .="</tr>";
						}
						
					}
				}
				//end field element generate
			} else {
				$textout = " \n".'<tr><td colspan="'.($column+1).'">No record availible...</td></tr>';
			}?>
			<table cellspacing="0" border="0" cellpadding="0" id="infoheader" width="100%">
				<tr><td align="left"> <? if(!$rs1){echo ' 0';}echo " ".$rs1["rows"]." Record Total"; ?></td>
 				<? if($chkPageEdit){?>
 				<td id="addinfo" align="right" width="150px">
 					<a href="javascript:;" onClick="getReturnText('add_guser.php','page=1','tableDisplay');" class="top_menu_link">
                	Add New Group User</a>&nbsp;&nbsp;<a href="javascript:;" onClick="getReturnText('add_guser.php','page=1','tableDisplay');" class="top_menu_link"><img src="../../images/addIcon.png" alt="Add" width="16" height="16" border="0"/></a>
                </td>
 				<td id="addinfo" align="right" width="180px">
 					<a href="javascript:;" onClick="getReturnText('manage_gpage.php','page=1','tableDisplay');" class="top_menu_link">
                	Manage User Group Permission</a>&nbsp;&nbsp;<a href="javascript:;" onClick="getReturnText('manage_gpage.php','page=1','tableDisplay');" class="top_menu_link"><img src="../../images/addusers.png" alt="Add" width="16" height="16" border="0"/></a>
                </td>
                <?}?>
                </tr>
 			</table>
			<?
			echo " <table class=\"main_table_list\" cellspacing=\"0\" cellpadding=\"0\"> \n".$textout." \n</table> \n";
 			
 						?><p><div align="center">
 			<?
				//start gen page 
				if($page>0 && $page==true){
					$obj->genPage($tbname,$order,$dir.$link,$url,$page,$rs1["rows"],$records_per_page);
				}
				//end gen page
 						?></div></p>
                        </div>
                        </td>
                    </tr>
                    </tbody></table>
                </fieldset>
			</div>
		</td>
    </tr>
</table>
</td>
</tr>
</table>
	<div class="hiddenbar"><img id="spLine" src="../../images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('../../images')"/></div>