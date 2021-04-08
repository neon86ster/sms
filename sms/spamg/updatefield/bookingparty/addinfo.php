<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb();
$filename = "../object.xml";
$errormsg ="";
$bpid = $obj->getParameter("id");
// for return to the same page 
$showinactive = $obj->getParameter("showinactive",0);
$categoryid = $obj->getParameter("categoryid",0);
$cityid=$obj->getParameter("cityid",0);
$cmsid=$obj->getParameter("cmsid",0);
$method = $obj->getParameter("method");
$page = $obj->getParameter("page",1);
$order=$obj->getParameter("order","bp_name");
$sort=$obj->getParameter("sort","asc");
$search = $obj->getParameter("search","");
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
$querystr = "&pageid=$pageid&search=$searchstr&order=$order&page=$page&sort=$sort" .
		"&showinactive=$showinactive" .
		"&cmsid=$cmsid&categoryid=$categoryid&cityid=$cityid";
//set bookparty active
if($method=="setactive" && $chkPageEdit){
	$sql = "";
	$active = $obj->getParameter("active");
	$name = $obj->setActive($_REQUEST,$filename);
	if($name!=false){
		if($active==1){
			$successmsg="$name is active!!";
		}else{
			$successmsg="$name is inactive!!";
		}
		$successmsg=$successmsg.$querystr;
		header("Location: index.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
}
// add/update information
$add = $obj->getParameter("add");
if($add == " save change " && $chkPageEdit){
	$id = $obj->readToUpdate($_REQUEST,$filename);
	if($id){
		$successmsg="Update data complete!!";
		$successmsg.=$querystr;
		header("Location: index.php?msg=$successmsg");
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
							if($bpid){
									$xml = "<command>" .
									"<table>al_bookparty</table>" .
									"<where name='bp_id' operator='='>$bpid</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else {
								echo $obj->gFormInsert('al_bookparty',$filename);	
							}
 						?>
                    <table boder="0" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px;">
                    	<tbody><tr>
                    	<td colspan="2">
                		<fieldset>
                    		<legend> </legend>
                    		<br/>
                    		<input type="hidden" name="querystr" id="querystr" value="<?=$querystr?>"/>
                    		<input name="id" id="id" type="hidden" value="<?=$bpid?>">
							<input name="add" id="add" type="button" size="" value="<?=($bpid)?" save change ":" add "?>" onClick='<?=($bpid)?"set_editData(\"al_bookparty\",$bpid)":"set_insertData(\"al_bookparty\")"?>' > 
							<input name="cancel" id="cancel" type="button" value=" cancel " onClick="gotoURL('index.php?method=cancel<?=$querystr?>');" style="font-size:11px">
                		</fieldset>
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