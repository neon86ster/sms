<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb();
$filename = "../object.xml";
$errormsg ="";
$branchid = $obj->getParameter("id");
// for return to the same page 
$showinactive = $obj->getParameter("showinactive");
$method = $obj->getParameter("method");
$order = $obj->getParameter("order");
$page = $obj->getParameter("page");
$sort=$obj->getParameter("sort");
$search = $obj->getParameter("search");
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
$querystr = "&pageid=$pageid&search=$searchstr&order=$order&page=$page&sort=$sort&showinactive=".$showinactive;
//set branch active
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
$add = $obj->getParameter("add");
if($add == " save change " && $chkPageEdit){
	$id = $obj->readToUpdate($_REQUEST,$filename);
	if($id){
		// line 51-83 update bl_sc servicecharge history
		$servicescharge = $obj->getParameter("servicescharge");		// number
		$userid = $_SESSION["__user_id"];							// id
		$ip = $_SERVER["REMOTE_ADDR"];								// text
		$sql="select branch_id from bl_sc where branch_id=$id order by sc_id desc";
		$rs = $obj->getResult($sql);
		if(!isset($rs["sc_percent"])){$rs["sc_percent"]=0;}
		if($rs["sc_percent"]!=$servicescharge){
			$chksql = "insert into bl_sc(`sc_percent`,`branch_id`,`l_lu_user`,`l_lu_date`,`l_lu_ip`,`active`) " .
			 "values('$servicescharge',$branchid,'$userid',Now(),'$ip',1)";
			 $chkid = $obj->setResult($chksql);
		}
		
		$successmsg="Update data complete!!";
		$successmsg.=$querystr;
		header("Location: index.php?msg=$successmsg");
	} else {
		$errormsg = $obj->getErrorMsg();
	}
} else if($add == " add " && $chkPageEdit) {
	$id = $obj->readToInsert($_REQUEST,$filename);
	if($id){
		// line 95-113 update bl_sc servicecharge history
		$servicescharge = $obj->getParameter("servicescharge");		// number
		$userid = $_SESSION["__user_id"];							// id
		$ip = $_SERVER["REMOTE_ADDR"];								// text
		$chksql="insert into bl_sc(`sc_percent`,`branch_id`,`l_lu_user`,`l_lu_date`,`l_lu_ip`,`active`) " .
			 "values('$servicescharge',$id,'$userid',Now(),'$ip',1)";
		$chkid=$obj->setResult($chksql);
				
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

<!--[if IE]>
<style>
span.timezone select.ctrDropDown{
    width:115px;
    font-size:11px;
}
span.timezone select.ctrDropDownClick{
    font-size:11px;

    width:auto;

}
span.timezone select.plainDropDown{
    width:115px;
    font-size:11px;
}
</style>
<![endif]-->

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
        		<table border="0" cellpadding="0" cellspacing="0" style='overflow:auto' class="generalinfo">
                <tbody> <tr>
			        <td class="rheader" colspan="2" style="padding-left: 25px; padding-right: 10px; padding-top:20px;">
			        Branch Information:
			        </td>
			        </tr>
			        <tr>
                    	<td valign="top">
        		        <? 
							if($branchid){
									$xml = "<command>" .
									"<table>bl_branchinfo</table>" .
									"<where name='branch_id' operator='='>$branchid</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else {
								echo $obj->gFormInsert('bl_branchinfo',$filename);	
							}
 						?>
 						</td>
                       <? if($branchid && $branchid > 1) {?>
                        <td valign="top" align="left">
                        <div>
                        <img id="images_branch" name="images_branch" src="<?=$customize_part?>/images/branch/<?=($branchid && $obj->getIdToText($branchid,"bl_branchinfo","bpic","branch_id"))?$obj->getIdToText($branchid,"bl_branchinfo","bpic","branch_id"):"tmp.png"?>"><br>
						<?if($chkPageEdit){?>
						<a href="javascript:;;" onClick="window.open('uploadupic.php?bid=<?=$branchid?>','uploadpicture','height=200,width=500,resizable=0,scrollbars=1');">change branch image</a>
                        <?}?>
                        </div><br/><br/>
                        <div>
                        <img id="images_sr" name="images_sr" src="<?=$customize_part?>/images/branch/<?=($branchid && $obj->getIdToText($branchid,"bl_branchinfo","sr_logo","branch_id"))?$obj->getIdToText($branchid,"bl_branchinfo","sr_logo","branch_id"):"sr_tmp.png"?>"><br>
						<?if($chkPageEdit){?>
						<a href="javascript:;;" onClick="window.open('uploadsrpic.php?bid=<?=$branchid?>','uploadsrpicture','height=200,width=500,resizable=0,scrollbars=1');">change sale receipt logo</a>
                        <?}?>
                        </div>
                        </td>
                        <? } ?>
                    </tr></table><br>
                    <table boder="0" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px;">
                    	<tbody><tr>
                    	<td colspan="2">
                		<fieldset>
                    		<legend> </legend>
                    		<br/>
                    		<input type="hidden" name="querystr" id="querystr" value="<?=$querystr?>"/>
                    		<input name="id" id="id" type="hidden" value="<?=$branchid?>">
							<input name="add" id="add" type="button" size="" value="<?=($branchid)?" save change ":" add "?>" onClick='<?=($branchid)?"set_editData(\"bl_branchinfo\",$branchid)":"set_insertData(\"bl_branchinfo\")"?>' > 
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
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('/images')"/></div>
</div>
</body>
</html>