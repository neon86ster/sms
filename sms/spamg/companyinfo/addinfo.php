<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");
require_once("report.inc.php");

$obj = new formdb();
$reportobj = new report();
$errormsg ="";
$errorpic ="";
$timezone = $obj->getParameter("timezone");
if($timezone==false){
	$timezone = $obj->getIdToText(1,"bl_branchinfo","timezone","branch_id");
}
$add = $obj->getParameter("add");
if($add == " save change " && $chkPageView){
	//$logo = $_FILES["company_logo"];
	$symbol = $_FILES["currency_symbol"];
	//echo $logo."<br>".$logo["name"]."<br>".$logo["size"]."<br>".$logo["tmp_name"]."<br>";
	/* if(!empty($logo["size"])){
		if($logo["size"] <= 2000000){
			if($logo["type"]=="image/gif" || $logo["type"]=="image/pjpeg" || $logo["type"]=="image/png" 
			|| $logo["type"]=="image/jpeg" ){
				$imgFile = addslashes(fread(fopen($logo["tmp_name"],"r"),filesize($logo["tmp_name"])));
				$sql="update a_company_info set company_logo ='".$imgFile."',logo_type='".$logo["type"]."'";
				$obj->setResult($sql,false);
				
				//$_POST["company_logo"]=$imgFile;
			}else{
				$errorpic = "<br>But can't upload company logo image.<br> Please check type must be jpeg, gif or png.";
			}
		}else{
			$errorpic = "<br>But can't upload company logo image.<br> Please check size must be less than 2 MB.";
		}
	}*/
	
	if(!empty($symbol["size"])){
		if($symbol["size"] <= 2000000){
			if($symbol["type"]=="image/gif" || $symbol["type"]=="image/pjpeg" 
			|| $symbol["type"]=="image/png" || $symbol["type"]=="image/jpeg"){
				$imgFile = addslashes(fread(fopen($symbol["tmp_name"],"r"),filesize($symbol["tmp_name"])));
				$sql="update a_company_info set currency_symbol ='".$imgFile."',symbol_type='".$logo["type"]."'";
				$obj->setResult($sql,false);
			}else{
				$errorpic = "<br>But can't upload currency symbol image.<br> Please check type must be jpeg, gif or png.";
			}
		}else{
			$errorpic = "<br>But can't upload currency symbol image.<br> Please check size must be less than 2 MB.";
		}
	}
	
	$_POST["company_address"] = str_replace("\n","[br]",$_POST["company_address"]);
	$id = $obj->readToUpdate($_POST,'../object.xml',false);
	if($id){
		$sql="update bl_branchinfo set timezone ='".$timezone."' where branch_id=1";
		$obj->setResult($sql,false);
		$successmsg="Update data complete!!";
		header("Location: index.php?msg=$successmsg&pageid=$pageid".$errorpic);
	} else {
		//echo $obj->setErrorMsg($company_logo);
		$errormsg = $obj->getErrorMsg();
	}
} else {
	header("addinfo.php");
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">

<!--[if IE]>
<style>
span.short_date select.ctrDropDown{
    width:115px;
    font-size:11px;
}
span.short_date select.ctrDropDownClick{
    font-size:11px;

    width:auto;

}
span.short_date select.plainDropDown{
    width:115px;
    font-size:11px;
}

span.long_date select.ctrDropDown{
    width:115px;
    font-size:11px;
}
span.long_date select.ctrDropDownClick{
    font-size:11px;

    width:auto;

}
span.long_date select.plainDropDown{
    width:115px;
    font-size:11px;
}

span.time select.ctrDropDown{
    width:115px;
    font-size:11px;
}
span.time select.ctrDropDownClick{
    font-size:11px;

    width:auto;

}
span.time select.plainDropDown{
    width:115px;
    font-size:11px;
}
</style>
<![endif]-->

</head>
<body>
<form name="upload" action="addinfo.php" enctype="multipart/form-data" method="post">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td width="6px" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
    <td height="49px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" align="center" height="49">
<?
$i = count($pageinfo["parent"]);
$pageinfo["parenturl"][$i] = $pageinfo["pageurl"];
$pageinfo["parent"][$i] = $pageinfo["pagename"];
$pageinfo["pagename"] = "Manage ".$pageinfo["pagename"];
?>
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
    					<td ><b><font style="color:#ff0000;">Error message: </font></b><?=$errormsg ?></td>
    				</tr>
    			</table>
    		</div>
        	<div id="gform">
                          	<? $xml = "<command>" .
									"<table>a_company_info</table>" .
									"<where name='company_id' operator='='>1</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,'../object.xml');	 
 							?>
                    <table boder="0" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px;">
                    	<tbody>

<!--                    	
                    	<tr>
                    		<td>Company_logo</td>
                          	<td align="left">
                        	<input type="file" name="company_logo" id="logo">
                        	</td>
                    	</tr>
-->
                    	<tr>
                    		<td>Time Zone</td>
                          	<td align="left">
                          	<span class="time" style="width: 115px;font-family:Tahoma; font-size: 11px;overflow:hidden;"> 
                        	<?=$reportobj->makeListbox("timezone","l_timezone","description","timezone_id",$timezone,0)?>
                        	</span>
                        	</td>
                    	</tr>
                    	<tr>
                    		<td>Company Name Symbol</td>
                          	<td align="left">
                        	<input type="file" name="currency_symbol" id="logo">
                        	</td>
                    	</tr>
                    	<tr>
                    	<td colspan="2">
                		<fieldset>
                    		<legend> </legend>
                    		<br/>
                    		<input type="hidden" name="pageid" id="pageid" value="<?=$pageid?>">
							<input name="add" id="add" type="submit" size="" value=" save change " style="font-size:11px">&nbsp; 
							<input name="cancel" id="cancel" type="button" value=" cancel " onClick="gotoURL('index.php?pageid=<?=$pageid?>');" style="font-size:11px">
                		</fieldset>
						</td>
                    </tr></tbody>
                    </table>  
			</div>
		</td>
    </tr>
</table>
		</td>
  </tr>
</table> 
</form>
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('/images')"/></div>
</body>
</html>