<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb();
$filename = "../object.xml";
$errormsg ="";

$add = $obj->getParameter("add");
$page_id = $obj->getParameter("pageid"); 
$gift_number_start = $obj->getParameter("gift_number_start");
$gift_number_end = $obj->getParameter("gift_number_end","");
$give_to = $obj->getParameter("give_to","");
$receive_from = $obj->getParameter("receive_from","");
$value = $obj->getParameter("value","");
$gifttype_id = $obj->getParameter("gifttype_id","");
$receive_by_id = $obj->getParameter("receive_by_id",0);
$product = $obj->getParameter("product","");
$hidden_issue = $obj->getParameter("hidden_issue",date("Ymd"));
$hidden_expired = $obj->getParameter("hidden_expired",date("Ymd"));
$debug = false;

if($add == " add " && $chkPageView) {
	$id = $obj->readToInsertMoreGift($_REQUEST,$filename);
	if($id){
		$successmsg="Insert data complete!!";
		header("Location: index.php?msg=$successmsg&pageid=$page_id");
	} else {
		$errormsg = $obj->getErrorMsg();
	}
}
$i = count($pageinfo["parent"]);
if(!isset($pageinfo["pageurl"])){$pageinfo["pageurl"]="";}
if(!isset($pageinfo["pagename"])){$pageinfo["pagename"]="";}
$pageinfo["parenturl"][$i] = $pageinfo["pageurl"];
$pageinfo["parent"][$i] = $pageinfo["pagename"];
$pageinfo["pagename"] = "Manage ".$pageinfo["pagename"];

if(!isset($_REQUEST["gift_number_start"])){
	$gift_number_start=$obj->getNextId("g_gift","gift_number");
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<script src="/scripts/date-functions.js" type="text/javascript"></script>
<script src="/scripts/datechooser.js" type="text/javascript"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name="addinfos" id="addinfos" action="" method="get" style="padding:0;margin:0">
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
        	<div>  
        	<table class="generalinfo"><input id="gift_id" name="gift_id" value="" type="hidden">
			 <tbody>
			 <tr>
				<td valign="top">Gift Number Start<font style="color: #ff0000;"> *</font> </td>
				<td valign="top"><input id="gift_number_start" name="gift_number_start" maxlength="10" size="" value="<?=$gift_number_start?>" type="text"></td>
			 </tr>
			 <tr>
				<td valign="top">Gift Number End<font style="color: #ff0000;"> *</font> </td>
				<td valign="top"><input id="gift_number_end" name="gift_number_end" maxlength="10" size="" value="<?=$gift_number_end?>" type="text"></td>
			 </tr>
			 <tr>
				<td valign="top">Give To<font style="color: #ff0000;"> *</font> </td>
				
				<td valign="top"><input id="give_to" name="give_to" maxlength="" size="" value="<?=$give_to?>" type="text"></td>
			 </tr>
			 <tr>
				<td valign="top">Receive From<font style="color: #ff0000;"> *</font> </td>
				<td valign="top"><input id="receive_from" name="receive_from" maxlength="" size="" value="<?=$receive_from?>" type="text"></td>
			 </tr>
			 <tr>
				<td valign="top">Value</td>
				<td valign="top"><input id="value" name="value" maxlength="10" size="" value="<?=$value?>" type="text"></td>
			 </tr>
			 <tr>
				<td valign="top">Type</td>
				
				<td valign="top">
				<? $ff = array();
					$ff["table"] = "gl_gifttype";
					$ff["name"] = "gifttype_id";
					$ff["javascript"] = "";
					$ff["first"] = "---select---";
				echo $obj->gSelectBox($ff,$filename,$gifttype_id,$debug);
				?>				 
				</td>
			 </tr>
			 <?
				$defaultissue = $obj->getParameter("hidden_issue",date("Ymd"));
				$defaultexpire = $obj->getParameter("hidden_expired",false);
				if(!$defaultexpire){
					$defaultexpire = $dateobj->plusmonth(date("Ymd"),6);
				}
			 ?>
			 <tr>
				<td valign="top">Issue<font style="color: #ff0000;"> *</font> </td>
				<td valign="top">&nbsp;&nbsp;
				<input id="hidden_issue" name="hidden_issue" value="<?=$defaultissue?>" type="hidden">
				<input id="issue" value="<?=$dateobj->convertdate($defaultissue,"Ymd",$sdateformat)?>" style="width: 85px;" readonly="1" class="textbox" type="text">
				&nbsp;&nbsp;<img src="/images/calendar.png" onclick="showChooser(this, 'issue', 'issue_showSpan', 1900, 2100, '<?=$sdateformat?>', false);"> 
				<span id="issue_showSpan" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px;" align="center"></span></td>
			 </tr>
			 <tr>
				<td valign="top">Expired<font style="color: #ff0000;"> *</font> </td>
				<td valign="top"><form name="expired">&nbsp;&nbsp;
				<input id="hidden_expired" name="hidden_expired" value="<?=$defaultexpire?>" type="hidden">
				<input id="expired" value="<?=$dateobj->convertdate($defaultexpire,"Ymd",$sdateformat)?>" style="width: 85px;" readonly="1" class="textbox" type="text">
				 &nbsp;&nbsp;<img src="/images/calendar.png" onclick="showChooser(this, 'expired', 'expired_showSpan', 1900, 2100, '<?=$sdateformat?>', false);"> 
				<span id="expired_showSpan" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px;" align="center"></span></form></td>
			</tr>
				<input id="used" name="used" value="" type="hidden">
			 <!--
			 <tr>
				<td valign="top">Receive By</td>
				<td valign="top">
				<? $ff = array();
					$ff["table"] = "l_employee";
					$ff["name"] = "receive_by_id";
					$ff["javascript"] = "";
					$ff["first"] = "---select---";
				echo $obj->gSelectBox($ff,$filename,$gifttype_id,$debug);
				?>
				</select> 
				 
				</td>
			 </tr>
			-->
			 <tr>
				<td valign="top">Product</td>
				<td valign="top"><input id="product" name="product" maxlength="" size="" value="" type="text"></td>
			 </tr>
				<input id="l_lu_user" name="l_lu_user" value="thisuser" type="hidden">
				<input id="l_lu_date" name="l_lu_date" value="thistime" type="hidden">
				<input id="l_lu_ip" name="l_lu_ip" value="thisip" type="hidden">
				<input id="book_id" name="book_id" value="" type="hidden">
				<input id="id_sold" name="id_sold" value="" type="hidden">
				<input id="available" name="available" value="" type="hidden">
				<input name="formname" id="formname" value="g_gift" type="hidden">
			 </tbody>
			 </table>
                    <table boder="0" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px;">
                    	<tbody><tr>
                    	<td colspan="2">
                		<fieldset>
                    		<legend> </legend>
                    		<br/>
                    		<input type="hidden" name="pageid" id="pageid" value="<?=$pageid?$pageid:$page_id?>"/>
                    		<input name="add" id="add" type="submit" size="" value=" add " >
							<input name="cancel" id="cancel" type="button" value=" cancel " onClick="gotoURL('index.php?pageid=<?=$pageid?>');" style="font-size:11px">
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
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('/images')"/></div>
</div>
</body>
</html>