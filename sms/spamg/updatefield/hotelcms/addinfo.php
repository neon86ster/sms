<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb();
$filename = "../object.xml";
$errormsg ="";
$hotelcmsid = $obj->getParameter("id");
$table=$obj->getParameter("table");
// for return to the same page 
$showinactive = $obj->getParameter("showinactive");
$cityid = $obj->getParameter("cityid");
$method = $obj->getParameter("method");
$order = $obj->getParameter("order");
$page = $obj->getParameter("page",1);
$sort=$obj->getParameter("sort");
$search=$obj->getParameter("search","");
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
$querystr = "&pageid=$pageid&search=$searchstr&order=$order&page=$page&sort=$sort&showinactive=$showinactive&cityid=".$cityid;
// add/update information
$add = $obj->getParameter("add");
if($add == " save change " && $chkPageEdit){
	$accid = $obj->getParameter("acc_id");
	$accname = $obj->getParameter("acc_name");
	$cityid = $obj->getParameter("city_id");
	$cmspercent = $obj->getParameter("cmspercent");
	$tablename = $obj->getParameter("tablename");
	if($tablename=="al_accomodations"){
		$chkid = $obj->getIdToText($accname,"al_accomodations","acc_id","acc_name","acc_id !=$accid");
		$sql="update al_accomodations set acc_name='$accname', cmspercent='$cmspercent', city_id=$cityid where acc_id=$accid ";
	}
	else if($tablename=="al_bookparty"){
		$chkid = $obj->getIdToText($accname,"al_bookparty","bp_id","bp_name","bp_id != $accid");
		$sql="update al_bookparty set bp_name='$accname', bp_cmspercent='$cmspercent', city_id=$cityid where bp_id=$accid ";
	}
	if($chkid>0){
		 	$obj->setErrorMsg("This Accommodation Name is already have in the system. please insert new name!!'");
		 	$errormsg = $obj->getErrorMsg();
	 		$id = false;
	}else{
		$id = $obj->setResult($sql);
	}
	if($id){
		$successmsg="Update data complete!!".$querystr;
		header("Location: index.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
} else {
	header("add_hotelcms.php");
}
if($table=="al_accomodations"){
	$idfield = "acc_id";
}else{
	$idfield = "bp_id";
}
$i = count($pageinfo["parent"]);
$pageinfo["parenturl"][$i] = $pageinfo["pageurl"];
$pageinfo["parent"][$i] = $pageinfo["pagename"];
$pageinfo["pagename"] = "Manage ".$pageinfo["pagename"];
if(!isset($parent)){$parent="";}
$parent = "$parent";

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name="thavi" id="thavi" action="" method="post" style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td width="6px" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
    <td height="49px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" align="center" height="49">
				<?include("$root/menuheader.php");?>
	 	</td>
	  </tr>
</table> 
</div>
  	</td>
  </tr>  
  <tr>
<td valign="top" style="margin-top:0px;margin-left:0px">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%">
    		<div id="showerrormsg" <? if($errormsg==""&&$add==false){?>style="display:none"<? } else {?>style="display:block"<? }?>>
    			<table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    				<tr>
    					<td ><b><font style="color:#ff0000;"><img src="/images/errormsg.png" /> Error message: </font></b><?=$errormsg ?></td>
    				</tr>
    			</table>
    		</div>
        	<div id="gform">
        		        <? 
        		        $disabled="";
						$debug="";
						$hiddeninput="";
						$prename="";
							if($table=="al_accomodations"){
								$xml = "<command>" .
										"<table>$table</table>" .
										"<field>*,'$table' as tablename</field>" .
										"<where name='$idfield' operator='='>".$hotelcmsid."</where>" .
										"</command>";
							} else{
								$xml = "<command>" .
										"<table>$table</table>" .
										"<field>bp_id as acc_id,bp_name as acc_name,bp_cmspercent as cmspercent,city_id,bp_active as acc_active,'$table' as tablename</field>" .
										"<where name='$idfield' operator='='>".$hotelcmsid."</where>" .
										"</command>";
                       		}
							$rs = $obj->getRsXML($xml,$filename);
							$f = simplexml_load_file($filename);
							$element = $f->table->hotelcms;
							$textout = "<table class=\"generalinfo\"> \n";
							foreach($element->field as $ff) {
									$name = $ff["name"];
									
									if($ff["defaultvalue"]=="__get"){
											//$defaultvalue = $_GET["$name"];
											if($table=="al_accomodations"){
											$defaultvalue = $obj->getIdToText($hotelcmsid,"al_accomodations","$name","acc_id");
											}else if($table=="al_bookparty"){
											$defaultvalue = $obj->getIdToText($hotelcmsid,"al_bookparty","$name","bp_id");	
											}
									}else if($ff["defaultvalue"]=="__post"){
											$defaultvalue = $_POST["$name"];
									}else{
											$defaultvalue = $ff["defaultvalue"];
									}
									
									if(!isset($defaultvalue)){$defaultvalue = $rs[0]["$name"];}
									if($ff["showinform"]!="no" && $ff["formtype"]!="hidden" && $ff["showinformEdit"]!="no") {
											$textout .= "<tr> \n";
	 										$textout .= "<td valign='top'>".$ff["formname"];
	 										if($ff["prior"]){$textout .= "<font style='color:#ff0000''> ".$ff["prior"]."</font> ";}
											$textout .= "</td> \n";
							 				$textout .= "<td valign='top'> \n";
							 				if(($ff["formtype"]=="text" || $ff["formtype"]=="file" || $ff["formtype"]=="submit" || $ff["formtype"]=="reset" || $ff["formtype"]=="button")&&$ff["updatein"]=="") {
							 					$textout .= "<input id='$prename".$ff["name"]."' type='".$ff["formtype"]."' name='$prename".$ff["name"]."' maxlength='".$ff["maxlength"]."' size='".$ff["size"]."' value='$defaultvalue' ".$ff["javascript"]."$disabled> $hiddeninput";
							 				}else if($ff["formtype"]=="select") {
								 				//echo $ff["formtype"];
							 					$textout .= $obj->gSelectBox($ff,$filename,$defaultvalue,$debug);
							 				}
							 				$textout .= "</td> \n";
	 										$textout .= "</tr> \n";
									}else if($ff["formtype"]=="hidden"){
							 				$textout .= "<input name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" type=\"".$ff["formtype"]."\" value=\"".$defaultvalue."\"> \n";
							 		}else{
							 				$textout .= "<input name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" type=\"hidden\" value=\"".$defaultvalue."\"> \n";
							 		}	
							}
							$textout .= "<input name='formname' type='hidden' value=\"".$table."\" > \n";
 							$textout .= "</table> \n";
							echo $textout;
							
 						?>
                    <table boder="0" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px;">
                    	<tbody><tr>
                    	<td colspan="2">
                		<fieldset>
                    		<legend> </legend>
                    		<br/>
                    		<input type="hidden" name="querystr" id="querystr" value="<?=$querystr?>"/>
                    		<input name="id" id="id" type="hidden" value="<?=$hotelcmsid?>">
							<input name="add" id="add" type="submit" size="" value=" save change " onClick='<?="setedithotelcms(\"$table\",$hotelcmsid)"?>' > 
							<input name="cancel" id="cancel" type="button" value=" cancel " onClick="gotoURL('index.php?method=cancel<?=$querystr?>');" style="font-size:11px">
                		</fieldset>
						</td>
                    </tr></tbody>
                    </table>  
                </form>
			</div>
		</td>
    </tr>
</table>
		</td>
  </tr>
</table> 
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('/images')"/></div>
</div>
</body>
</html>