<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb();
$filename = "../object.xml";
$errormsg ="";
$giftid = $obj->getParameter("id");
// for return to the same page 
$gifttypeid = $obj->getParameter("gifttypeid");
$method = $obj->getParameter("method");
$order = $obj->getParameter("order");
$page = $obj->getParameter("page");
$sort=$obj->getParameter("sort");
$search = $obj->getParameter("search");
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
$querystr = "&pageid=$pageid&search=$searchstr&order=$order&page=$page&sort=$sort&gifttypeid=".$gifttypeid;

//set avaliable gift number
if($method=="setactive" && $chkPageEdit){
	$sql = "";
	$active = $obj->getParameter("active");
	$sql = "update g_gift set available=$active where gift_id=$giftid";
	$name = $obj->setResult($sql);
	if($name!=false){
		$name = $obj->getIdToText($giftid,"g_gift","gift_number","gift_id");
		if($active==1){
			$successmsg="Gift No : $name is available!!";
		}else{
			$successmsg="Gift No: $name is unavailable!!";
		}
		$successmsg=$successmsg.$querystr;
		header("Location: index.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
}
$add = $obj->getParameter("add");
if($add == " save change " && $chkPageEdit){
	$id = $obj->readToUpdate($_REQUEST,$filename);
	if($id){
		$bpdsid = $obj->getParameter("bpdsid_sold");
		if(!$bpdsid){
		 		$chksql = "update g_gift set id_sold='',tb_name='' where gift_id=$giftid ";
		 		$chkid = $obj->setResult($chksql);
		}
		if($object->checkAdmin($object->getUserIdLogin())&&$bpdsid){
			$chkid = true;
			if($bpdsid){
		 		$bookid = $obj->getIdToText($bpdsid,"c_bpds_link","tb_id","bpds_id");
		 		$tbname = $obj->getIdToText($bpdsid,"c_bpds_link","tb_name","bpds_id");
		 		if($bookid&&$tbname){
		 			$chksql = "update g_gift set id_sold=$bookid,tb_name='$tbname' where gift_id=$giftid ";
		 			$chkid = $obj->setResult($chksql);
		 		}else{
		 			$obj->setErrorMsg("Please check id sold !!");
		 		}
		 	}
	 		if($bpdsid&&$chkid){
				$successmsg="Update data complete!!";
				$successmsg.=$querystr;
				header("Location: index.php?msg=$successmsg");
			}else{
				$errormsg = $obj->getErrorMsg();
			}
		}else{
			$successmsg="Update data complete!!";
			$successmsg.=$querystr;
			header("Location: index.php?msg=$successmsg");
		}
	} else {
		$errormsg = $obj->getErrorMsg();
	}
} else if($add == " add " && $chkPageEdit) {
	$id = $obj->readToInsert($_REQUEST,$filename);
	if($id){
		$successmsg="Insert data complete!!";
		header("Location: index.php?msg=$successmsg&pageid=$pageid");
	} else {
		$errormsg = $obj->getErrorMsg();
	}
}
if(!$giftid && !isset($_REQUEST["gift_number"])){
	$_GET["gift_number"]=$obj->getNextId("g_gift","gift_number");
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
<form name="giftinfo" id="giftinfo" action="" method="post" style="padding:0;margin:0">
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
    					<td ><b><font style="color:#ff0000;"><img src="/images/errormsg.png" /> Error message: </font></b><?=$errormsg ?></td>
    				</tr>
    			</table>
    		</div>
        	<div>
        		        <? 
							if($giftid){
									$xml = "<command>" .
									"<table>g_gift</table>" .
									"<where name='gift_id' operator='='>$giftid</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else {
								echo $obj->gFormInsert('g_gift',$filename);	
							}
 						?>
 					<?  $readonly = "";
 						if($giftid&&$object->checkAdmin($object->getUserIdLogin())==false){
 							$readonly = "readonly=\"1\"";
 						}
 						if($giftid){
 							if(!isset($bpdsid)){$bpdsid="";}
 							$bookid = $obj->getIdToText($giftid,"g_gift","id_sold","gift_id");
 							$tbname = $obj->getIdToText($giftid,"g_gift","tb_name","gift_id");
 							$bpdsid = ($bpdsid)?$bpdsid:$obj->getIdToText($bookid,"c_bpds_link","bpds_id","tb_id","tb_name='$tbname'");
 							if($bookid>0&&!$bpdsid){$bpdsid=$bookid;}
 						?>
 					<table class="generalinfo">
 						<tbody><tr> 
						<td valign="top" width="70px">ID Sold<font style="color: #ff0000;"> *</font> </td> 
						<td valign="top"><input id="bpdsid_sold" name="bpdsid_sold" maxlength="10" size="" value="<?=$bpdsid?>" type="text" <?=$readonly?>> </td> 
						</tr></tbody>
    				</table>
    				<?}?>
                    <table boder="0" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px;">
                    	<tbody><tr>
                    	<td>
                		<fieldset>
                    		<legend> </legend>
                    		<br/>
                    		<input type="hidden" name="querystr" id="querystr" value="<?=$querystr?>"/>
                    		<input name="id" id="id" type="hidden" value="<?=$giftid?>">
							<input name="add" id="add" type="button" size="" value="<?=($giftid)?" save change ":" add "?>" onClick='<?=($giftid)?"set_editData(\"g_gift\",$giftid)":"set_insertData(\"g_gift\")"?>' > 
							<input name="cancel" id="cancel" type="button" value=" cancel " onClick="gotoURL('index.php?method=cancel<?=($giftid)?$querystr:"&pageid=$pageid"?>');" style="font-size:11px">
<script>

   var mybrowser=navigator.userAgent;

   if(mybrowser.indexOf("MSIE")>0){
      mybs = "IE";
   }
   if(mybrowser.indexOf("Firefox")>0){
         mybs = "Firefox";
   }   
   if(mybrowser.indexOf("Presto")>0){
       mybs = "Opera";
   }         
   if(mybrowser.indexOf("Chrome")>0){
            mybs = "Chrome";
   }   
   if(mybrowser.indexOf("Safari")>0){
            mybs = "Safari";
  			document.getElementById('add').type='submit';
   } 
</script>
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