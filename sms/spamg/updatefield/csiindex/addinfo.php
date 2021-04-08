<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb();
$filename = "../object.xml";
$errormsg ="";
$csiiid = $obj->getParameter("id");
// for return to the same page 
$showdetail=$obj->getParameter("showdetail");
$method = $obj->getParameter("method");
$order = $obj->getParameter("order");
$page = $obj->getParameter("page");
$sort=$obj->getParameter("sort");
$search=$obj->getParameter("search","");
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
$querystr = "&pageid=$pageid&search=$searchstr&order=$order&page=$page&sort=$sort&showdetail=$showdetail";

$add = $obj->getParameter("add");
if($add){
		$chkid = $obj->getIdToText($_REQUEST["csii_name"],"fl_csi_index","csii_id","csii_name","csii_active=1 and csii_id != $csiiid");
		if($chkid>0){
		 	$obj->setErrorMsg("This customer service name index is already have in the system. please insert new name!!'");
		 	$errormsg = $obj->getErrorMsg();
	 		$add = false;
		 }
}

if($add == " save change " && $chkPageEdit){
	$last_csi_id = $_REQUEST["csii_id"];
	$last_csi_index = $obj->getIdToText($_REQUEST["csii_id"],"fl_csi_index","csii_name","csii_id"); 
	if($last_csi_index!=$_REQUEST["csii_name"]){
		$_REQUEST["csii_id"]=false;
		$id = $obj->readToInsert($_REQUEST,$filename);
		if($id){
			$chksql = "update fl_csi_index set csii_active=0 where csii_id = $last_csi_id";
			if($obj->setResult($chksql)){
				$successmsg="Update data complete!!";
				$successmsg.=$querystr;
				header("Location: index.php?msg=$successmsg");
			}else{
				$errormsg = $obj->getErrorMsg();
			}
		} else {
			$errormsg = $obj->getErrorMsg();
		}
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
<form name="csiindex" id="csiindex" action="" method="post" style="padding:0;margin:0">
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
        	<div id="gform">
        		        <? 
							if($csiiid){
									$xml = "<command>" .
									"<table>fl_csi_index</table>" .
									"<where name='csii_id' operator='='>$csiiid</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else {
								echo $obj->gFormInsert('fl_csi_index',$filename);	
							}
 						?>
                    <table boder="0" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px;">
                    	<tbody><tr>
                    	<td colspan="2">
                		<fieldset>
                    		<legend> </legend>
                    		<br/>
                    		<input type="hidden" name="querystr" id="querystr" value="<?=$querystr?>"/>
                    		<input name="id" id="id" type="hidden" value="<?=$csiiid?>">
							<input name="add" id="add" type="button" size="" value="<?=($csiiid)?" save change ":" add "?>" onClick='<?=($csiiid)?"set_editData(\"fl_csi_index\",$csiiid)":"set_insertData(\"fl_csi_index\")"?>' > 
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
                <table cellpadding="0" cellspacing="0" class="generalinfo" style="margin-top:0px">
		                    <tr height="32">
		                    	<td colspan="4" style="padding-left: 0;border-bottom: 3px double #d3d3d3;">
		                    	 <b>CSI Index History</b>
		                    	</td>
		                    </tr>
		                    <tr height="10">
		                    	<td colspan="4"></td>
		                    </tr>
		                    <tr height="32">
		                    	<td style="text-align:center;background-color:#a8c2cb;">
		                    	<b>CSI Index</b>
		                    	</td>
		                    	<td style="text-align:center;background-color:#a8c2cb;">
		                    	<b>Add by</b>
		                    	</td>
		                    	<td style="text-align:center;background-color:#a8c2cb;">
		                    	<b>Add time</b>
		                    	</td>
		                    	<td style="text-align:center;background-color:#a8c2cb;">
		                    	<b>Add ip</b>
		                    	</td>
		                    </tr>
		                    
<?
$csii_column_name = $obj->getIdToText($csiiid,"fl_csi_index","csii_column_name","csii_id");
$sql = "select * from fl_csi_index where csii_column_name = \"$csii_column_name\" order by l_lu_date";
$rs = $obj->getResult($sql);
for($i=0;$i<$rs["rows"];$i++){
?>	
							<tr class="<?=($i%2==1)?"odd":"even"?>" height="20">
								<td class="report"><?=$rs[$i]["csii_name"]?>&nbsp;</td>
								<td class="report"><?=$obj->getIdToText($rs[$i]['l_lu_user'],"s_user","u","u_id")?>&nbsp;</td>
								<td class="report"><?=($rs[$i]["l_lu_date"]=="0000-00-00 00:00:00")?"-":$dateobj->convertdate($rs[$i]["l_lu_date"],'Y-m-d',$sdateformat);?>&nbsp;</td>
								<td class="report"><?=$rs[$i]["l_lu_ip"]?>&nbsp;</td>
							</tr>
<?	
}
?>
				</table>					        
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