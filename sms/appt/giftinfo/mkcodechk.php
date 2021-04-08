<?
$_COOKIE["topicdir"] = 'Code Free/Discount > ';
$_COOKIE["topic"] = 'Code Free/Discount Information';
$_COOKIE["back"] = '../general/index.php';
include("../../include.php");
require_once("formdb.inc.php");
require_once("secure.inc.php");
require_once("date.inc.php");
$obj = new formdb(); 
$scObj = new secure();
$dateobj = new convertdate(); 
$chksql = "select long_date,short_date from a_company_info";
$chkrs =$obj->getResult($chksql);
$sdateformat = $obj->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
$ldateformat = $obj->getIdToText($chkrs[0]["long_date"],"l_date","date_format","date_id");

//For debug undefined index : . By Ruck : 19-05-2009
$_GET["allCode"]=$obj->getParameter("allCode","");
$_GET["where"]=$obj->getParameter("where","");
$_GET["search"]=$obj->getParameter("search","");
$_GET["order"]=$obj->getParameter("order","");
$_GET["show_inactive"]=$obj->getParameter("show_inactive","");
$_GET["page"]=$obj->getParameter("page","");
$_GET["category_id"]=$obj->getParameter("category_id","");
$_GET["categoryId"]=$obj->getParameter("categoryId","");
$_GET["msg"]=$obj->getParameter("msg","");
$textout="";
$gifttype="";

//------------------------------------------------//

if($_GET["allCode"]=="all"){
	$_GET["where"]="";
	$_GET["search"]="";
}
if($_GET["search"]){
	$_GET["where"]=$_GET["search"];
	
}
$xml = "<command>".
 	"<table>l_marketingcode</table>".
	"<usejoin>no</usejoin>";
if($_GET["order"]){	
	$xml .= "<order>".$_GET["order"]."</order>";
} else {
	$xml .= "<order>sign asc</order>";
}	
$showInactive="";
$link="";
if(!$_GET["show_inactive"] && !$_GET["where"]){
	$showInactive="<where logic='AND' name='active' operator='='>1</where>";
}else{
	$link="&show_inactive=true";
}
if($_GET["page"]){	
$xml .= "<page>".$_GET["page"]."</page>";
} else {
$xml .= "<page>1</page>";
}
if($_GET["where"]){
	$chkcategoryid = "";
	if($_GET["category_id"]){$chkcategoryid = "logic='OR'";}
	$xml .= "<status>search</status>".
			"<where $chkcategoryid name='sign' operator='like'>%".$_GET["where"]."%</where>".
			"<where logic='OR' name='place' operator='like'>%".$_GET["where"]."%</where>".
			"<where logic='OR' name='contactperson' operator='like'>%".$_GET["where"]."%</where>".
			"<where logic='OR' name='phone' operator='like'>%".$_GET["where"]."%</where>".
			"<where logic='OR' name='comment' operator='like'>%".$_GET["where"]."%</where>".$showInactive;
}else{
	$xml .="<status>search</status>".$showInactive;		
}
if($_GET["category_id"]||$_GET["category_id"]!=0 || $_GET["categoryId"]) {
	if(!$_GET["category_id"]){
		$_GET["category_id"]=$_GET["categoryId"];
	}
	$xml .= "<where logic='AND' name='category_id' operator='='>".$_GET["category_id"]."</where>";
	$link.="&category_id=".$_GET["category_id"];
} 
$xml .= "</command>";
$obj->setDebugStatus(false);
$filename = 'spamg.xml';
$url = 'mkcodechk.php';

//////////// For check permission to access edit page /////////////////////
if($scObj->isPageEdit($_SERVER["PHP_SELF"])){
	//echo "Can Access Edit";
	$chkPageEdit=true;
}else{
	//echo "Can't Access Edit";
	$chkPageEdit=false;
}
$chkPageEdit=false;
//////////// End check permission to access edit page /////////////////////
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Code Free/Discount</title>
<link href="../../css/style.css" rel="stylesheet" type="text/css" />
<script src="scripts/ajax.js" type="text/javascript"></script>
<script src="scripts/component.js" type="text/javascript"></script>
<script src="scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="scripts/datechooser/datechooser.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="scripts/datechooser/datechooser.css">
</head>
<body>
<form id="mkcode" name="mkcode" action="<?=$_SERVER["PHP_SELF"]?>" method="get">
<table class="main" cellspacing="0" cellpadding="0" height="100%" width="100%">
<tr>
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
    			<fieldset>
					<legend><b>Code Free/Discount Search</b></legend>
					<div id="tooldiv" style="display:block;top:0px;left:0px;position:relative;" align="left">
                       <table border="0" cellpadding="2" cellspacing="2">
                       <td height="22px" >
	                        	Category: 
	                        <? 	$ff ='<field name="category_id" 
							  			table="l_mkcode_category" 
										formname="Category name" first="---select---" 
										formtype="select" defaultvalue="__post"
										javascript="onChange=selectboxSearch(\'mkcodechk.php\')" />';
								$bid = simplexml_load_string($ff);
	                        	echo $obj->gSelectBox($bid,$filename,$_REQUEST["category_id"]); ?>
	     				</td>
                        <td height="30px">
     					<input type="text" name="search" id="search" <?=($_GET["where"])?"value='".$_GET["where"]."'":""?>/>
     					<input type="hidden" id="allCode" name="allCode" value="0" />
     					<input type="hidden" name="page" id="page" value="<?=$_GET["page"]?>"/>
     					<input type="hidden" name="order" id="order" value="<?=$_GET["order"]?>"/>
     					&nbsp;&nbsp;
        			    <a href="javascript:;" onClick="document.mkcode.submit();" class="top_menu_link"><img src="../../images/btn_search_bg.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; 
        			    <a href="javascript:;" onClick="document.getElementById('category_id').options[0].selected=true;document.getElementById('page').value=1;document.mkcode.submit();" class="top_menu_link">Search</a> &nbsp;
        			    <a href="javascript:;" onClick="showAll('all')" class="top_menu_link"><img src="../../images/btn_show-all_bg.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; 
        			    <a href="javascript:;" onClick="showAll('all')" class="top_menu_link">View All</a>
        			    &nbsp;  &nbsp; <input id='show_inactive' type='checkbox' name='show_inactive' value='1' onClick="showInactive('mkcodechk.php')" <? echo ($_GET["show_inactive"])?"checked":""?> /> Show Inactive
                        </td></table>
     				</div> 
        			    <a href="javascript:;" onClick="toggleToolDiv()" class="top_menu_link"><span id="toggletooltxt">Hide Search</span></a>
				 		<a href="javascript:;" onClick="toggleToolDiv()" class="top_menu_link"><img id="toggletoolimg" src="../../images/search_hide.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp;
        		</fieldset>
			</div>
        	<div>
    			<fieldset>
					<legend><b>Code Free/Discount Infomation</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style='overflow:auto'>
                    <tbody>
                    <tr>
                    	<td colspan="2">
                        <div id="Code Free/Discount Infomation">
                        <?
			$f = simplexml_load_file('spamg.xml');
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
			//load field name form spamg.xml
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
				$sortupimg = ' <img src="../../images/arrow_up.gif" border="0" /> ';
				$sortdownimg = ' <img src="../../images/arrow_down.gif" border="0" /> ';
				
				//start field name generate
				if($dir=='desc'){
					$textout .= " \n".'<tr>';
					for($i=0;$i<$column;$i++){
						if($arrFormType[$i]!="submit"&&$arrFormType[$i]!="button"&&$arrShowinList[$i]!="no"){
							$textout .= ("$orderfield"==$arrFields[$i])?" \n".'<td class="sort"><a href=\'javascript:;\' onClick=\'showSortmkcode("'.$arrFields[$i]." asc".'","'.(($_GET["page"])?$_GET["page"]:"").'");\'> '.$arrFieldsname[$i].'</a> '.$sortdownimg.'</td>':" \n".'<td class="mainthead"><a href=\'javascript:;\' onClick=\'showSortmkcode("'.$arrFields[$i]." asc".'","'.(($_GET["page"])?$_GET["page"]:"").'");\'> '.$arrFieldsname[$i].'</a></td>';
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
							$textout .= ("$orderfield"=="$arrFields[$i]")?" \n".'<td class="sort"><a href=\'javascript:;\' onClick=\'showSortmkcode("'.$arrFields[$i]." desc".'","'.(($_GET["page"])?$_GET["page"]:"").'");\'> '.$arrFieldsname[$i].'</a> '.$sortupimg.'</td>':" \n".'<td class="mainthead"><a href=\'javascript:;\' onClick=\'showSortmkcode("'.$arrFields[$i]." desc".'","'.(($_GET["page"])?$_GET["page"]:"").'");\'> '.$arrFieldsname[$i].'</a></td>';
						}		
					}
					if($chkPageEdit){
						$textout .= " \n".'<td class="mainthead">Edit</td></tr>';	
					}else{
						$textout .= '<td class="mainthead">Add</td></tr>';
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
					$chkxml .= "<status>search</status>".
								"<where name='sign' operator='like'>%".$_GET["where"]."%</where>".
								"<where logic='OR' name='place' operator='like'>%".$_GET["where"]."%</where>".
								"<where logic='OR' name='contactperson' operator='like'>%".$_GET["where"]."%</where>".
								"<where logic='OR' name='phone' operator='like'>%".$_GET["where"]."%</where>".
								"<where logic='OR' name='comment' operator='like'>%".$_GET["where"]."%</where>";
					}
				if($_GET["category_id"]||$_GET["category_id"]!=0) {
					$chkxml .= "<where logic='AND' name='category_id' operator='='>".$_GET["category_id"]."</where>";
				} 
				$chkxml .= "</command>"; 		//chk total record form db
				
				//echo $obj->encodeText($xml)."<br />";
				//echo $tbname.", ".$sql.", ".$field.", ".$where.", ".$order.", ".$page.", ".$dir."<br />";
				$rs1 = $obj->getRsXML($chkxml,$filename);
				$rs = $obj->getRsXML($xml,$filename);
				
				if($rs["rows"]>0){
					for($i=0;$i<$rs["rows"];$i++){
						$textout .= ($i%2==0)?" \n".'<tr class="content_list" onMouseOver="high(this)" onMouseOut="low(this)">':'<tr class="content_list1" onMouseOver="high(this)" onMouseOut="low(this)">';
						for($j=0;$j<$column;$j++){
							if($arrShowinList[$j]!="no") {
								//$align = ("$arrFields[$j]"==$namefield)?"left":"right";
								$align ="left";
								$chkarrFields = explode(".",$arrFields[$j]); //chk array field of order;
								$arrFields[$j] = (count($chkarrFields)>1)?$chkarrFields[1]:$arrFields[$j];
								
								if($arrFields[$j]=='active') {
									if($chkPageEdit){
										if($rs[$i]["$arrFields[$j]"]==1){
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<a href=\"javascript:;\" onClick=\"javascript:setEnable('l_marketingcode',".$rs[$i]["mkcode_id"].",0);\" class=\"top_menu_link\">".
														"<img src='../../images/active.png' border='0' title='active' /></a>".
														"</td>";
										}else{
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<a href=\"javascript:;\" onClick=\"javascript:setEnable('l_marketingcode',".$rs[$i]["mkcode_id"].",1);\" class=\"top_menu_link\">".
														"<img src='../../images/inactive.png' border='0' title='inactive' /></a>".
														"</td>";
										}
									}else{
										if($rs[$i]["$arrFields[$j]"]==1){
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<img src='../../images/active.png' border='0' title='active' />".
														"</td>";
										}else{
											$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".
														"<img src='../../images/inactive.png' border='0' title='inactive' />".
														"</td>";
										}
									}
								}else if($arrFormType[$j]=='date') {
										$data = (($rs[$i]["$arrFields[$j]"]=="0000-00-00")?"-":$dateobj->convertdate($rs[$i]["$arrFields[$j]"],'Y-m-d',$sdateformat));
										$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$data.'</td>';
								}else if($arrFields[$j]=='category_id') {
										$data = ($rs[$i]["$arrFields[$j]"]>0)?$obj->getIdToText($rs[$i]["$arrFields[$j]"],"l_mkcode_category","category_name","category_id"):"-";
										$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$data.'</td>';
								}else {$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='$align'>".$rs[$i]["$arrFields[$j]"].'</td>';}
							}
						}
						$textout .=" \n<td style='padding-left:7px;padding-right:7px;' align='left'><input type='button' name='codeAdd' id='codeAdd' value='Add' class='button' onClick='editmkCode(\"".$rs[$i]["mkcode_id"]."\");'/></td>";
						$textout .="</tr>";
						
					}
				}
				//end field element generate
			} else {
				$textout = " \n".'<tr><td colspan="'.($column+1).'">No record availible...</td></tr>';
			}?>
			<table cellspacing="0" border="0" cellpadding="0" id="infoheader" width="100%">
				<tr><td align="left"> <? if(!$rs1){echo ' 0';}echo " ".$rs1["rows"]." Total Records"; ?></td>
                </tr>
 			</table>
			<?
			echo "<table class=\"main_table_list\" cellspacing=\"0\" cellpadding=\"0\"> \n".$textout." \n</table> \n";
 			
 						?><p><div align="center">
 			<?
				//start gen page 
				if($page>0 && $page==true){
					$obj->genGiftPage($tbname,$order,$dir,$page,$rs1["rows"],$records_per_page,$gifttype);
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
</form>