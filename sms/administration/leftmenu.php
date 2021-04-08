<?
include_once("include.php");
require_once("main.inc.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>leftmenu</title>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="scripts/components.js"></script>
</head>

<?
$pagespan="";// For debug undefine index : pagespan. By Ruck 16-05-2009

/*
 * Function makemenu($uid)
 * make menu list from userid
 */ 
function makemenu($chkrs){
    	$obj = new main(); 
    	
		$textout="";
	  	for($i=0;$i<$chkrs["rows"];$i++){
	   		$left = 30+($chkrs[$i]["menu_level"]*15);	// menu td position
	   		$ileft = 13+($chkrs[$i]["menu_level"]*15);	// image position
			$pagereffer = $chkrs[$i]["menu_level"]+1;	// td background
			
				$textout .= "<tr>\n" .
						"\t<td class=\"images/0$pagereffer.jpg\" height=\"29\" id=\"".$chkrs[$i]["page_name"].$chkrs[$i]["page_id"]."bg\" style=\"padding-left:".$left."px;border-top: 2px #eae8e8 solid;background: url('images/0$pagereffer.jpg');\">\n";
				$textout .= "<img id=\"".$chkrs[$i]["page_name"].$chkrs[$i]["page_id"]."img\" src=\"images/menu1.png\" style=\"position: absolute;left:".$ileft."px;margin-top:2px\">\n";
    			
    			$showDiv = "\"gotoURL('".$chkrs[$i]["url"]."?pageid=".$chkrs[$i]["page_id"]."')\" target=\"mainFrame\"";
    			
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
	  		
				//### For keep all page on left menu ###//
				$GLOBALS["pagespan"].="|".$chkrs[$i]["page_name"].$chkrs[$i]["page_id"];
	  	}
    	return $textout;
		
}
?>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="leftmenu">
<tr>
	<td height="47" align="center" style="background-image: url('images/logobg.png');border-bottom: 3px #eae8e8 strong;">
	<a href="javascript:;" onClick="gotoURL('mainPage.php')">
	<img src="images/smslogo.png" width="118px" height="29px" border="0"></a></td>
</tr>
<tr>
	<td height="2" background="#eae8e8"><img src="images/blank.gif" height="2px"></td>
</tr>
<?=makemenu($permissionrs);?>
<tr>
	<td height="2" style="padding-left:45px;border-top: 2px #eae8e8 solid;background: url('images/04.jpg');"><img src="images/blank.gif" height="2px"></td>
</tr>
</table>

<input type="hidden" id="pagespan" name="pagespan" value="<?=$pagespan?>">
<input type="hidden" id="pageid" name="pageid" value="<?=$pageid?>">
</body>
</html>