<?
session_start();
$_COOKIE["topicdir"] = '<a href="javascript:;" onclick="gotoURL(\'../../index.php\')" class="top_menu_link">Preferences </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../index.php\')" class="top_menu_link">Update_field </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../products/index.php\')" class="top_menu_link">Products </a> > ' .
		'Product > ';
$_COOKIE["topic"] = 'Product Infomation Manager';
$_COOKIE["back"] = '../products/index.php';
include("../../../include.php");
require_once("formdb.inc.php");
require_once("secure.inc.php");
$obj = new formdb(); 
$scObj = new secure();

$xml = "<command>".
 	"<table>cl_product</table>".
	"<usejoin>no</usejoin>";
if($_GET["order"]){	
	$xml .= "<order>".$_GET["order"]."</order>";
} else {
	$xml .= "<order>pd_name asc</order>";
}
$showInactive="";
$link="";
if(!$_GET["show_inactive"] && !$_GET["where"]){
	$showInactive="<where logic='AND' name='pd_active' operator='='>1</where>";
}else if(!$_GET["where"]){
	$link="&show_inactive=true";
}	
if($_GET["page"]){	
$xml .= "<page>".$_GET["page"]."</page>";
} else {
$xml .= "<page>1</page>";
}
if($_GET["pd_category_id"]||$_GET["pd_category_id"]!=0 || $_GET["pd_categoryId"]) {
	if(!$_GET["pd_category_id"]){
		$_GET["pd_category_id"]=$_GET["pd_categoryId"];
	}
	$xml .= "<where logic='AND' name='pd_category_id' operator='='>".$_GET["pd_category_id"]."</where>";
	$link.="&pd_category_id=".$_GET["pd_category_id"];
} 
if($_GET["where"]){
	$xml .= "<status>search</status>".
			"<where logic='OR' name='pd_name' operator='like'>%".$_GET["where"]."%</where>".
			"<where logic='OR' name='standard_price' operator='like'>%".$_GET["where"]."%</where>";
}else{
	$xml .="<status>search</status>".$showInactive;
			
}
$xml .= "</command>";
$obj->setDebugStatus(false);
$filename = '../object.xml';
$url = 'manage_product.php';
$querystr = ($_GET["pd_category_id"])?'&pd_category_id='.$_GET["pd_category_id"]:'';
if($scObj->isPageEdit($_SERVER["PHP_SELF"])){
	//echo "Can Access Edit";
	$chkPageEdit=true;
}else{
	//echo "Can't Access Edit";
	$chkPageEdit=false;
}
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
    					<td><b><font style="color:#008000;">Success message: </font></b><?=$_GET["msg"]?></td>
    				</tr>
    			</table>
    		</div>
    		<div align="right">
    			<div id="tooldiv" style="display:block;top:0px;left:0px;position:relative;" align="left">
                	<fieldset>
					<legend><b>Product Search</b></legend>
				       <table border="0" cellpadding="2" cellspacing="2">
                       <td height="22px" >
	                        	Category: 
	                        <? 	$ff ='<field name="pd_category_id" 
							  			table="cl_product_category" 
										formname="Category name" first="---select---" 
										formtype="select" defaultvalue="__post"
										javascript="onChange=selectboxSearch(\'manage_product.php\')" />';
								$bid = simplexml_load_string($ff);
	                        	echo $obj->gSelectBox($bid,$filename,$_GET["pd_category_id"]); ?>
	     				</td>
                        <td>
                        <input type="text" name="search" id="search" <?=($_GET["where"])?"value='".$_GET["where"]."'":""?>/>
     					<input type="hidden" name="page" id="page" value="<?=$_GET["page"]?>"/>
     					<input type="hidden" name="order" id="order" value="<?=$_GET["order"]?>"/>
     					&nbsp;&nbsp;
        			    <a href="javascript:;" onClick="gotoSearch('manage_product.php')" class="top_menu_link"><img src="../../../images/btn_search_bg.gif" alt="search" width="16" height="16" border="0"/></a>
        			    &nbsp; <a href="javascript:;" onClick="gotoSearch('manage_product.php')" class="top_menu_link">Search</a> &nbsp;
        			    <a href="javascript:;" onClick="getReturnText('manage_product.php','page=1<?=(($_GET["show_inactive"])?"&show_inactive=true":"")?>','tableDisplay')" class="top_menu_link"><img src="../../../images/btn_show-all_bg.gif" alt="search" width="16" height="16" border="0"/></a>
        			    &nbsp; <a href="#" onClick="getReturnText('manage_product.php','page=1<?=(($_GET["show_inactive"])?"&show_inactive=true":"")?>','tableDisplay')" class="top_menu_link">View All</a>
        			    &nbsp;  &nbsp; <input id='show_inactive' type='checkbox' name='show_inactive' value='1' onClick="showInactive('manage_product.php')" <? echo ($_GET["show_inactive"])?"checked":""?> /> Show Inactive 
        			    </td></table>
     				</fieldset>
				</div> 
        	    <a href="javascript:;" onClick="toggleToolDiv()" class="top_menu_link"><span id="toggletooltxt">Hide Search</span></a>
				<a href="javascript:;" onClick="toggleToolDiv()" class="top_menu_link"><img id="toggletoolimg" src="../../../images/search_hide.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp;
        	</div>
        	<div>
    			<fieldset>
					<legend><b>Product Infomation</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style='overflow:auto'>
                    <tbody>
                    <tr>
                    	<td colspan="2">
                        <div id="Product Infomation">
                        <?
			$f = simplexml_load_file('../object.xml');
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
			//load field name form ../object.xml
			$element = $f->table->$tbname;
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
				$sortupimg = ' <img src="../../../images/arrow_up.gif" border="0" /> ';
				$sortdownimg = ' <img src="../../../images/arrow_down.gif" border="0" /> ';
				
				//start field name generate
				if($dir=='desc'){
					$textout .= " \n".'<tr>';
					for($i=0;$i<$column;$i++){
						if($arrFormType[$i]!="submit"&&$arrFormType[$i]!="button"&&$arrShowinList[$i]!="no"){
							
							$textout .= ("$orderfield"==$arrFields[$i])?" \n".'<td class="sort"><a href=\'javascript:;\' onClick=\'getReturnText("'.$url.'","order='.$arrFields[$i].' asc'.(($_GET["where"])?"& where=".$_GET["where"]:"").(($_GET["page"])?"& page=".$_GET["page"]:"").(($_GET["show_inactive"])?"&show_inactive=true":"").$querystr.'","tableDisplay");\'> '.$arrFieldsname[$i].'</a> '.$sortdownimg.'</td>':" \n".'<td class="mainthead"><a href=\'javascript:;\' onClick=\'getReturnText("'.$url.'","order='.$arrFields[$i].' asc'.(($_GET["where"])?"& where=".$_GET["where"]:"").(($_GET["page"])?"& page=".$_GET["page"]:"").(($_GET["show_inactive"])?"&show_inactive=true":"").$querystr.'","tableDisplay");\'> '.$arrFieldsname[$i].'</a></td>';
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
							$textout .= ("$orderfield"=="$arrFields[$i]")?" \n".'<td class="sort"><a href=\'javascript:;\' onClick=\'getReturnText("'.$url.'","order='.$arrFields[$i].' desc'.(($_GET["where"])?"& where=".$_GET["where"]:"").(($_GET["page"])?"& page=".$_GET["page"]:"").(($_GET["show_inactive"])?"&show_inactive=true":"").$querystr.'","tableDisplay");\'> '.$arrFieldsname[$i].'</a> '.$sortupimg.'</td>':" \n".'<td class="mainthead"><a href=\'javascript:;\' onClick=\'getReturnText("'.$url.'","order='.$arrFields[$i].' desc'.(($_GET["where"])?"& where=".$_GET["where"]:"").(($_GET["page"])?"& page=".$_GET["page"]:"").(($_GET["show_inactive"])?"&show_inactive=true":"").$querystr.'","tableDisplay");\'> '.$arrFieldsname[$i].'</a></td>';
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
				
			if($_GET["pd_category_id"]||$_GET["pd_category_id"]!=0) {
				$chkxml .= "<where logic='AND' name='pd_category_id' operator='='>".$_GET["pd_category_id"]."</where>";
			} 
			if($_GET["where"]){
				$chkxml .= "<status>search</status>".
						"<where logic='OR' name='pd_name' operator='like'>%".$_GET["where"]."%</where>".
						"<where logic='OR' name='standard_price' operator='like'>%".$_GET["where"]."%</where>";
			}
			if(!$_GET["show_inactive"]){
				$chkxml.=$showInactive;
			}
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
								//$align = ("$arrFields[$j]"==$namefield)?"left":"right";
								$align ="left";
								$chkarrFields = explode(".",$arrFields[$j]); //chk array field of order;
								$arrFields[$j] = (count($chkarrFields)>1)?$chkarrFields[1]:$arrFields[$j];
								
								if($arrFields[$j]=='pd_category_id') {
										$data=$obj->getIdToText($rs[$i]['pd_category_id'],"cl_product_category","pd_category_name","pd_category_id");
										$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$data.'</td>';
								} else if($arrFields[$j]=='pd_active') {
									if($chkPageEdit){
										if($rs[$i]["$arrFields[$j]"]==1){
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<a href=\"javascript:;\" onClick=\"javascript:setEnable('cl_product',".$rs[$i]["pd_id"].",0);\" class=\"top_menu_link\">".
														"<img src='../../../images/active.png' border='0' title='active' /></a>".
														"</td>";
										}else{
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<a href=\"javascript:;\" onClick=\"javascript:setEnable('cl_product',".$rs[$i]["pd_id"].",1);\" class=\"top_menu_link\">".
														"<img src='../../../images/inactive.png' border='0' title='inactive' /></a>".
														"</td>";
										}
									}else{
										if($rs[$i]["$arrFields[$j]"]==1){
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<img src='../../../images/active.png' border='0' title='active' />".
														"</td>";
										}else{
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<img src='../../../images/inactive.png' border='0' title='inactive' />".
														"</td>";
										}
									}
										
								} else if($arrFields[$j]=='set_tax') {
									if($chkPageEdit){
										if($rs[$i]["$arrFields[$j]"]==1){
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<a href=\"javascript:;\" onClick=\"javascript:setTax('cl_product',".$rs[$i]["pd_id"].",0);\" class=\"top_menu_link\">".
														"<img src='../../../images/active.png' border='0' title='active' /></a>".
														"</td>";
										}else{
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<a href=\"javascript:;\" onClick=\"javascript:setTax('cl_product',".$rs[$i]["pd_id"].",1);\" class=\"top_menu_link\">".
														"<img src='../../../images/inactive.png' border='0' title='inactive' /></a>".
														"</td>";
										}
									}else{
										if($rs[$i]["$arrFields[$j]"]==1){
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<img src='../../../images/active.png' border='0' title='active' />".
														"</td>";
										}else{
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<img src='../../../images/inactive.png' border='0' title='inactive' />".
														"</td>";
										}
									}
										
								} else if($arrFields[$j]=='set_sc') {
									if($chkPageEdit){
										if($rs[$i]["$arrFields[$j]"]==1){
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<a href=\"javascript:;\" onClick=\"javascript:setSc('cl_product',".$rs[$i]["pd_id"].",0);\" class=\"top_menu_link\">".
														"<img src='../../../images/active.png' border='0' title='active' /></a>".
														"</td>";
										}else{
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<a href=\"javascript:;\" onClick=\"javascript:setSc('cl_product',".$rs[$i]["pd_id"].",1);\" class=\"top_menu_link\">".
														"<img src='../../../images/inactive.png' border='0' title='inactive' /></a>".
														"</td>";
										}	
									}else{
										if($rs[$i]["$arrFields[$j]"]==1){
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<img src='../../../images/active.png' border='0' title='active' />".
														"</td>";
										}else{
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<img src='../../../images/inactive.png' border='0' title='inactive' />".
														"</td>";
										}
									}
								}else if($arrFields[$j]=='pd_name'||$arrFields[$j]=='standard_price'){
									$data=$obj->hightLightChar($_GET["where"],$rs[$i]["$arrFields[$j]"]);
									$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$data.'</td>';
								}else {$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$rs[$i]["$arrFields[$j]"].'</td>';}
							}
						}
						if($chkPageEdit){
							$textout .= " \n".'<td><a href="javascript:;" onclick="editData(\''.$tbname.'\','.$rs[$i]["pd_id"].')">update</a></td></tr>';
						}else{
							$textout .= '</tr>';
						}
						
					}
				}
				//end field element generate
			} else {
				$textout = " \n".'<tr><td colspan="'.($column).'">No record availible...</td></tr>';
			}?>
			<table cellspacing="0" border="0" cellpadding="0" id="infoheader" width="100%">
				<tr><td align="left"> <? if(!$rs1){echo ' 0';}echo " ".$rs1["rows"]." Record Total"; ?></td>
 				<? if($chkPageEdit){?>
 				<td id="addinfo" align="right" width="50%">
 					<a href="javascript:;" onClick="getReturnText('add_product.php','page=1','tableDisplay');" class="top_menu_link">
                	Add New Product</a>&nbsp;&nbsp;<a href="javascript:;" onClick="getReturnText('add_product.php','page=1','tableDisplay');" class="top_menu_link"><img src="../../../images/addIcon.png" alt="Add" width="16" height="16" border="0"/></a>
                </td>
                <? }?>
                </tr>
 			</table>
			<?
			echo "<table class=\"main_table_list\" cellspacing=\"0\" cellpadding=\"0\"> \n".$textout." \n</table> \n";
 			
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
	<div class="hiddenbar"><img id="spLine" src="../../../images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('../../../images')"/></div>