<?
session_start();
$_COOKIE["topicdir"] = '<a href="javascript:;" onclick="gotoURL(\'../../index.php\')" class="top_menu_link">Preferences </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../index.php\')" class="top_menu_link">Update_field </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../general/index.php\')" class="top_menu_link">General </a> > ' .
		'Booking Commission > ';
$_COOKIE["topic"] = 'Booking Commission Information Manager';
$_COOKIE["back"] = '../general/index.php';
include("../../../include.php");
require_once("formdb.inc.php");
require_once("secure.inc.php");
$obj = new formdb(); 
$scObj = new secure();

$sql1 = "select acc_id,acc_name,cmspercent,city_id,acc_active,\"al_accomodations\" as tablename from al_accomodations where acc_id!=1 and acc_active=1 ";
$sql2 = "select bp_id as acc_id,bp_name as acc_name,bp_cmspercent as cmspercent,city_id,bp_active as acc_active,\"al_bookparty\" as tablename from al_bookparty where bp_id!=1 and bp_active=1 ";

if($_GET["city_id"]){
$sql1 .= "and city_id=".$_GET["city_id"]." ";
$sql2 .= "and city_id=".$_GET["city_id"]." ";
}

$where = $obj->getParameter("where",false);
if($_GET["where"]){
$sql1 .= "and lower(acc_name) like \"%".htmlspecialchars(strtolower($where))."%\" ";
$sql2 .= "and lower(bp_name) like \"%".htmlspecialchars(strtolower($where))."%\" ";	
}

$sql = "($sql1) union ($sql2) ";

if($_GET["order"]){	
	$sql .= "order by ".$_GET["order"]." ";
} else {
	$sql .= "order by acc_name ";
}
$chksql = $sql;
$limit = 15;
$page=$obj->getParameter("page",1);
if($page > -1) {$sql .= "limit ".$obj->getPagetolimit($page,$limit).",".$limit." ";}

$obj->setDebugStatus(false);
$filename = '../object.xml';
$url = 'manage_hotelcms.php';

//////////// For check permission to access edit page /////////////////////
if($scObj->isPageEdit($_SERVER["PHP_SELF"])){
	//echo "Can Access Edit";
	$chkPageEdit=true;
}else{
	//echo "Can't Access Edit";
	$chkPageEdit=false;
}
//////////// End check permission to access edit page /////////////////////
//////////// Check login /////////////////////
$scObj->checkLogin();
//////////// End check login /////////////////////

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
    					<td><b><font style="color:#008000;">Success message: </font></b><?=$_GET["msg"]?>
						</td>
    				</tr>
    			</table>
    		</div>
    		<div align="right">
    			<div id="tooldiv" style="display:block;top:0px;left:0px;position:relative;" align="left">
                	<fieldset>
					<legend><b>Booking Commission Search</b></legend>
				       <table border="0" cellpadding="2" cellspacing="2">
                        <td height="30px">
	     						&nbsp;&nbsp;City: 
	                        <? 	$ff ='<field name="city_id" 
							  			table="al_city" 
										formname="City name" first="---select---" 
										formtype="select" defaultvalue="__post"
										javascript="onChange=selectboxSearch(\'manage_hotelcms.php\')" />';
								$bid = simplexml_load_string($ff);
	                        	echo $obj->gSelectBox($bid,$filename,$_GET["city_id"]); ?>
     					&nbsp;&nbsp;<input type="text" name="search" id="search" <?=($_GET["where"])?"value='".$_GET["where"]."'":""?>/>
     					<input type="hidden" name="page" id="page" value="<?=$_GET["page"]?>"/>
     					<input type="hidden" name="order" id="order" value="<?=$_GET["order"]?>"/>
     					&nbsp;&nbsp;
        			    <a href="javascript:;" onClick="gotoSearch('manage_hotelcms.php')" class="top_menu_link"><img src="../../../images/btn_search_bg.gif" alt="search" width="16" height="16" border="0"/></a>
        			    &nbsp; <a href="javascript:;" onClick="gotoSearch('manage_hotelcms.php')" class="top_menu_link">Search</a> &nbsp;
        			    <a href="javascript:;" onClick="getReturnText('manage_hotelcms.php','page=1<?=(($_GET["show_inactive"])?"&show_inactive=true":"")?>','tableDisplay')" class="top_menu_link"><img src="../../../images/btn_show-all_bg.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; 
        			    <a href="#" onClick="getReturnText('manage_hotelcms.php','page=1<?=(($_GET["show_inactive"])?"&show_inactive=true":"")?>','tableDisplay')" class="top_menu_link">View All</a> 
        			     <!--&nbsp;  &nbsp; <input id='show_inactive' type='checkbox' name='show_inactive' value='1' onClick="showInactive('manage_hotelcms.php')" <? echo ($_GET["show_inactive"])?"checked":""?> /> Show Inactive-->
        			    </td></table>
     				</fieldset>
     			</div> 
        	    <a href="javascript:;" onClick="toggleToolDiv()" class="top_menu_link"><span id="toggletooltxt">Hide Search</span></a>
				<a href="javascript:;" onClick="toggleToolDiv()" class="top_menu_link"><img id="toggletoolimg" src="../../../images/search_hide.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp;
        	</div>
        	<div>
    			<fieldset>
					<legend><b>Booking Commission Information</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tbody>
                    <tr>
                    	<td colspan="2">
                        <div id="Booking Commission Infomation">
                        <?
			$f = simplexml_load_file('../object.xml');
			
			//load field name form object.xml
			$element = $f->table->hotelcms;
			$showpage = $element->showpage;
			$records_per_page = $showpage["value"];
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
			$idfield = $eid["name"];
			$ename = $element->namefield;
			$namefield = $ename["name"];
			$order = $obj->getParameter("order",'');
			if($order==''){
				$order=$namefield;
				$orderfield = $namefield;
				$dir = 'desc';
			} else {
				//change $order to store order by "field" 
				$strDir=explode(" ",$order);
				$orderfield=$strDir[0];
				$dir = $strDir[1];
			}
			$dir = $obj->checkParameter($dir,'desc');
			$textout = "";
			if(strlen($order)>0){
				$sortupimg = ' <img src="../../../images/arrow_up.gif" border="0" /> ';
				$sortdownimg = ' <img src="../../../images/arrow_down.gif" border="0" /> ';
				
				//start field name generate
				if($dir=='desc'){
					$textout .= " \n".'<tr>';
					for($i=0;$i<$column;$i++){
						if($arrFormType[$i]!="submit"&&$arrFormType[$i]!="button"&&$arrShowinList[$i]!="no"){
							
							$textout .= ("$orderfield"==$arrFields[$i])?" \n".'<td class="sort"><a href=\'javascript:;\' onClick=\'getReturnText("'.$url.'","order='.$arrFields[$i].' asc&city_id='.$_GET["city_id"].(($_GET["where"])?"& where=".$_GET["where"]:"").(($_GET["page"])?"& page=".$_GET["page"]:"").(($_GET["show_inactive"])?"&show_inactive=true":"").'","tableDisplay");\'> '.$arrFieldsname[$i].'</a> '.$sortdownimg.'</td>':" \n".'<td class="mainthead"><a href=\'javascript:;\' onClick=\'getReturnText("'.$url.'","order='.$arrFields[$i].' asc&city_id='.$_GET["city_id"].(($_GET["where"])?"& where=".$_GET["where"]:"").(($_GET["page"])?"& page=".$_GET["page"]:"").(($_GET["show_inactive"])?"&show_inactive=true":"").'","tableDisplay");\'> '.$arrFieldsname[$i].'</a></td>';
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
							$textout .= ("$orderfield"=="$arrFields[$i]")?" \n".'<td class="sort"><a href=\'javascript:;\' onClick=\'getReturnText("'.$url.'","order='.$arrFields[$i].' desc&city_id='.$_GET["city_id"].(($_GET["where"])?"& where=".$_GET["where"]:"").(($_GET["page"])?"& page=".$_GET["page"]:"").(($_GET["show_inactive"])?"&show_inactive=true":"").'","tableDisplay");\'> '.$arrFieldsname[$i].'</a> '.$sortupimg.'</td>':" \n".'<td class="mainthead"><a href=\'javascript:;\' onClick=\'getReturnText("'.$url.'","order='.$arrFields[$i].' desc&city_id='.$_GET["city_id"].(($_GET["where"])?"& where=".$_GET["where"]:"").(($_GET["page"])?"& page=".$_GET["page"]:"").(($_GET["show_inactive"])?"&show_inactive=true":"").'","tableDisplay");\'> '.$arrFieldsname[$i].'</a></td>';
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
				
				//echo $obj->encodeText($xml)."<br />";
				//echo $tbname.", ".$sql.", ".$field.", ".$where.", ".$order.", ".$page.", ".$dir."<br />";
				
				$rs1 = $obj->getResult($chksql,$debug);
				$rs = $obj->getResult($sql,$debug);
				
				if($rs["rows"]>0){
					for($i=0;$i<$rs["rows"];$i++){
						$textout .= ($i%2==0)?" \n".'<tr class="content_list" onMouseOver="high(this)" onMouseOut="low(this)">':'<tr class="content_list1" onMouseOver="high(this)" onMouseOut="low(this)">';
						for($j=0;$j<$column;$j++){
							if($arrShowinList[$j]!="no") {
								//$align = ("$arrFields[$j]"==$namefield)?"left":"right";
								$align ="left";
								$chkarrFields = explode(".",$arrFields[$j]); //chk array field of order;
								$arrFields[$j] = (count($chkarrFields)>1)?$chkarrFields[1]:$arrFields[$j];
								if($arrFields[$j]=='city_id') {
										$chkxml = "<command>".
													"<table>al_city</table>".
													"<namefield name='city_name'></namefield>".
													"<idfield name='city_id'>".$rs[$i]['city_id']."</idfield>".
												"</command>";
										$data=$obj->getNameFormId($chkxml,$filename,false);
										$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$data.'</td>';
								}else if($arrFields[$j]=='acc_name'||$arrFields[$j]=='bp_name'){
									$data=$obj->hightLightChar($_GET["where"],$rs[$i]["$arrFields[$j]"]);
									$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$data.'</td>';
								} else{$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$rs[$i]["$arrFields[$j]"].'</td>';}
							}
						}
						if($chkPageEdit){
							$textout .= " \n".'<td><a href="javascript:;" onclick="edithotelcms(\''.$rs[$i]["tablename"].'\','.$rs[$i]["acc_id"].')">update</a></td></tr>';					
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
				<tr><td align="left"> <? if(!$rs1){echo ' 0';}echo " ".$rs1["rows"]." Total Records"; ?></td>
				<!--
 				<? if($chkPageEdit){?>
 				<td id="addinfo" align="right" width="50%">
 					<a href="javascript:;" onClick="getReturnText('add_hotelcms.php','page=1','tableDisplay');" class="top_menu_link">
                	Add New Booking Commission</a>&nbsp;&nbsp;<a href="javascript:;" onClick="getReturnText('add_hotelcms.php','page=1','tableDisplay');" class="top_menu_link"><img src="../../../images/addIcon.png" alt="Add" width="16" height="16" border="0"/></a>
                </td>
                <?}?>
                -->
                </tr>
 			</table>
			<?
				echo "<table class=\"main_table_list\" cellspacing=\"0\" cellpadding=\"0\"> \n".$textout." \n</table> \n";
 			
 						?><p><div align="center">
 			<?
				//start gen page 
				//echo $dir.$link.$url.$order.$page;
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
	<div class="hiddenbar"><img id="spLine" src="../../../images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('../../../images')"/></div>