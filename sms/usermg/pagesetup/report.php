<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

// initial parameter 
$pagepermission = $object->getParameter("pagepermission","");
// End initial parameter 

//################# Check page permission ########################
$pagepermissionarray = array();
if($pagepermission==""){
	// Query data from s_grouptemplate for initail group permission interface.
	$sql = "select page_id " .
		"from s_pagename " .
		"where active=1 " .
		"order by page_priority asc";
	
	$rsPagePermission = $object->getResult($sql);
	for($i=0;$i<$rsPagePermission["rows"];$i++){
		$pagepermissionarray[$i] = $rsPagePermission[$i]["page_id"];	
	}
}else{
	// get old data for initail group permission interface.
	$pagepermissionarray = explode(",",$pagepermission);
}
//################### End check page permission ################
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%">
			<table cellspacing="0" border="0" cellpadding="0" width="100%" class="generalinfo">
				<tr height="32">
<?
//start field name generate
			$style = "background-color:#88afbe;";
?>
					<td style="text-align:center;<?=$style?>" width="70%">
					<b>Page Name</b>
					</td>
					<td style="text-align:center;<?=$style?>" width="30%">
					<b>Enable</b>
					</td>
				</tr>
				<?=makemenu(0,0,"odd",$pagepermissionarray);?>
                <input id="allPageId" value="<?=$allPageId?>" type="hidden">
			</table><br/>
		</td>
    </tr>
</table>
<div id="checkDiv">
</div>
<?

function makemenu($menulevel,$id,$class,$pagepermissionarray){
		$object = new cms(); 
		$textout="";
		$sql = "select * from s_pagename where `index`=$menulevel and `page_parent_id`=$id order by page_priority asc";
		$object->setDebugStatus(false);
		$rs = $object->getResult($sql);
	    $left = 30+($menulevel*15);
	    
	   for($i=0;$i<$rs["rows"];$i++){
			$chk=($class=="even")?"0":"1";
    		$class=($menulevel%2==0)?"even":"odd";
			$textout .= "<tr class=\"$class\" height=\"20\">\n";
    		$textout .= "<td width=\"70%\">\n";
    		$textout .= "<div style=\"padding-left:".$left."px;\" >";
			$plus1 = "<img src=\"/images/classic/menu/menu1.png\" style=\"margin-top:2px; border:0\">";
			$plus2 = "<img src=\"/images/menu2.png\" style=\"margin-top:2px; border:0\">";
    		$plus = ($rs[$i]["has_child"]==1&&$rs[$i]["page_id"]>1)?$plus2:$plus1;
				
			//For keep all page id.
			$GLOBALS["allPageId"] .= "|".$rs[$i]["page_id"];
			
    		$textout .= ($rs[$i]["has_child"]==1)?
						"<a href=\"javascript:;\" onClick=\"toggle('".$rs[$i]["page_name"].$rs[$i]["page_id"]."')\">":
						"<a href=\"javascript:;\" >";
			$textout .= "$plus ".$rs[$i]["page_name"]."";
			$textout .= "</a>";
			$textout .= "</div>";
			$textout .= "</td>\n";
			
			//For set initail node value.
			$active = 0;
			if(in_array($rs[$i]["page_id"],$pagepermissionarray)) {
				$active = 1;
			}
			//For check box
			if($rs[$i]["has_child"]==1 && $rs[$i]["page_id"]>1){
				$textout .= "<td align=\"center\" width=\"30%\">&nbsp;<a style=\"cursor: default;\" " .
						"onMouseOver=\"mouseOver('active','".$rs[$i]["page_id"]."');\" " .
						"onMouseOut=\"mouseOut('active','".$rs[$i]["page_id"]."');\" " .
						"onclick=\"partialCheckBox('".$rs[$i]["page_id"]."','active','$id');\">" .
						"<img id=\"activeParent".$rs[$i]["page_id"]."Img\" " .
						"src=\"/images/triState/triState$active.gif\" border='0' width='13' height='13'>" .
						"</a></td>\n";
						
				$textout .= "<input id=\"active[".$rs[$i]["page_id"]."]\" value=\"".$rs[$i]["active"]."\" type=\"hidden\">";
			}else{
				$textout .= "<td align=\"center\" width=\"30%\"><input type=\"checkbox\" id=\"active[".$rs[$i]["page_id"]."]\" ".(($active)?"checked=\"checked\"":"")." onclick=\"partialCheckBox('".$rs[$i]["page_id"]."','active','$id');\"></td>\n";
			}
			$textout .= "<input id=\"".$rs[$i]["page_id"]."_$id\" value=\"0\" type=\"hidden\">";
			$textout .= "</tr>\n";
			
			if($rs[$i]["has_child"]==1){
				$pagereffer = $rs[$i]["index"]+1;
				$textout .= "<tr><td style=\"margin: 0;padding: 0;\" colspan=\"3\">\n";
				// For slab or span menu
				$textout .= "<span id=\"".$rs[$i]["page_name"].$rs[$i]["page_id"]."\" style=\"display: none;margin: 0;padding: 0;\">\n";
				
				$textout .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
				$textout .= "\t";
				
		    	$textout .= "\t".makemenu($menulevel+1,$rs[$i]["page_id"],$class,$pagepermissionarray);
		    	
				$textout .= "</table>\n</span>\n";
				$textout .= "</td>\n</tr>\n";
			}
	 	}
    	
    	return $textout;
}
?>