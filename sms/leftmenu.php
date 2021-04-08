<?
include_once("include.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="CACHE-CONTROL" content="public">
<title>leftmenu</title>
<link href="css/styles.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="scripts/components.js"></script>
</head>
<body>

<?
$pagespan="";

/*
 * Function makemenu($uid)
 * make menu list from userid
 */ 
function makemenu($chkrs){
    	$obj = new cms(); 
		$themeid=$GLOBALS["themeid"];
		$fontcolor=$GLOBALS["fontcolor"];
		$theme=$GLOBALS["theme"];
		$textout="";
	  	for($i=0;$i<$chkrs["rows"];$i++){
	   		$left = 30+($chkrs[$i]["menu_level"]*15);	// menu td position
	   		$ileft = 13+($chkrs[$i]["menu_level"]*15);	// image position
			$pagereffer = $chkrs[$i]["menu_level"]+1;	// td background
			
				$textout .= "<tr>\n" .
						"\t<td class=\"images/$theme/menu/0$pagereffer.jpg\" height=\"29\" id=\"".$chkrs[$i]["page_name"].$chkrs[$i]["page_id"]."bg\" style=\"padding-left:".$left."px;border-top: 2px #eae8e8 solid;background: url('images/$theme/menu/0$pagereffer.jpg');\">\n";
				$textout .= "<img id=\"".$chkrs[$i]["page_name"].$chkrs[$i]["page_id"]."img\" src=\"images/$theme/menu/menu1.png\" style=\"position: absolute;left:".$ileft."px;margin-top:2px\">\n";
    			
    			$showDiv = "\"gotoURL('/".$chkrs[$i]["url"]."?pageid=".$chkrs[$i]["page_id"]."')\" target=\"mainFrame\"";
    			
				//print row menu list
				$textout .= "<a href=\"javascript:;\" onClick=$showDiv>\n" .
								"<span id=\"".$chkrs[$i]["page_name"].$chkrs[$i]["page_id"]."span\" >\n"
								.$chkrs[$i]["page_name"]."</span></a>";
				$textout .= "</td>\n</tr>\n";
				
    			//check this row has child or not
		  		if(isset($chkrs[$i+1]["menu_level"])&&$chkrs[$i]["menu_level"]<$chkrs[$i+1]["menu_level"]){
						$textout .= "<tr>\n<td>\n";
						// For slab or span menu
						$textout .= "<span id=\"".$chkrs[$i]["page_name"].$chkrs[$i]["page_id"]."\" style=\"display: none;margin: 0;padding: 0\">\n";
						
						$textout .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
						$textout .= "\t";
		  		}
    			//check end child
		  		if(isset($chkrs[$i+1]["menu_level"])&&$chkrs[$i]["menu_level"]>$chkrs[$i+1]["menu_level"]){
		  			for($k=0;$k<$chkrs[$i]["menu_level"]-$chkrs[$i+1]["menu_level"];$k++){
						$textout .= "</table>\n</span>\n";
						$textout .= "</td>\n</tr>\n";
		  			}
			  	}
			  	
			  	if($chkrs[$i]["page_name"]=="Appointment"){
						$sql = "select * from bl_branchinfo " .
								"where `branch_active`=1 " .
								"and `branch_name` not like 'All' " .
								"order by `branch_name` asc";
						
		 				$brs = $obj->getResult($sql);
		 				$textout .= "<tr>\n<td>\n";
						$textout .= "<span id=\"".$chkrs[$i]["page_name"].$chkrs[$i]["page_id"]."\" style=\"display: none;margin: 0;padding: 0\">\n";
						$textout .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
						$textout .= "\t";
						
		 				for($j=0;$j<$brs["rows"];$j++){
				    		// For change td color
				    		$textout .= "<tr>\n<td class=\"images/$theme/menu/02.jpg\" height=\"29\" id=\"".$brs[$j]["branch_name"].$chkrs[$i]["page_id"]."bg\" style=\"padding-left:45px;border-top: 2px #eae8e8 solid;background: url('images/".$theme."/menu/02.jpg');\">\n";
				    		// For change picture
				    		$textout .= "<img id=\"".$brs[$j]["branch_name"].$chkrs[$i]["page_id"]."img\" src=\"images/".$theme."/menu/menu1.png\" style=\"position: absolute;left:28px;margin-top:2px\">";
				    		
				    		$showDiv ="\"gotoURL('/appt/index.php?pageid=".$chkrs[$i]["page_id"]."&bid=".$brs[$j]["branch_id"]."')\" target=\"mainFrame\"";
				    		$textout .= "<a href=\"javascript:;\" onClick=$showDiv><span id=\"".$brs[$j]["branch_name"].$chkrs[$i]["page_id"]."span\" >".$brs[$j]["branch_name"]."</span></a>";
							$textout .= "</td>\n</tr>\n";
							
							//### For keep all page on left menu ###//
							$GLOBALS["pagespan"].="|".$brs[$j]["branch_name"]."1";
						}
						$textout .= "</table>\n</span>\n";
						$textout .= "</td>\n</tr>\n";
			  	}
				//### For keep all page on left menu ###//
				$GLOBALS["pagespan"].="|".$chkrs[$i]["page_name"].$chkrs[$i]["page_id"];
				
	  	}
	  	$textout .= "<table></table>";
    	return $textout;
		
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="leftmenu">
<tr>
	<td height="47" align="center" style="background-image: url('images/<?=$theme?>/menu/logobg.png');border-bottom: 3px solid #eae8e8;">
	<a href="javascript:;" onClick="gotoURL('mainPage.php?pageid=0')"><img src="spamg/companyinfo/viewPicture.php?name=currency_symbol" width="118px" height="29px" border="0"></a></td>
</tr>
<tr>
	<td height="2" background="#eae8e8"><img src="images/blank.gif" height="2px"></td>
</tr>
<?=makemenu($permissionrs);?>
<tr>
	<td height="2" style="padding-left:45px;border-top: 2px #eae8e8 solid;background: url('images/<?=$theme?>/menu/04.jpg');"><img src="images/blank.gif" height="2px"></td>
</tr>
</table><br/>
<table width="100%">
<tr>
	<td align="center"><br>
		<a href="https://www.tap10.com/support/" target="_blank"><img src="images/staffonline.png" border="0"></a>
    </td>
</tr>
</table>
<input type="hidden" id="pagespan" name="pagespan" value="<?=$pagespan?>">
<input type="hidden" id="pageid" name="pageid" value="<?=$pageid?>">
</body>
</html>