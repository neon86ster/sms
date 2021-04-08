<?
include("../include.php");
require_once("formdb.inc.php");

$obj = new formdb();
$filename = "../object.xml";
$errormsg ="";
$clientid = $obj->getParameter("id");
// for return to the same page 
$showinactive = $obj->getParameter("showinactive");
$method = $obj->getParameter("method");
$order = $obj->getParameter("order");
$page = $obj->getParameter("page");
$sort=$obj->getParameter("sort");
$search = $obj->getParameter("search");
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
$querystr = "&pageid=$pageid&search=$searchstr&order=$order&page=$page&sort=$sort&showinactive=$showinactive";
//set avaliable bank number
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
	$old_client_name = $obj->getIdToText($_REQUEST["client_id"],"p_clientinfo","client_name","client_id");
	if($old_client_name!=$_REQUEST["client_name"]){
		rename($_SERVER["DOCUMENT_ROOT"]."/clients/$old_client_name", 
		$_SERVER["DOCUMENT_ROOT"]."/clients/".$_REQUEST["client_name"]);
	}
	
	$id = $obj->readToUpdate($_REQUEST,$filename);
	if($id){
		$chksql = "update p_clientinfo set l_lu_user=".$_SESSION["__user_id"].",l_lu_date=Now(),l_lu_ip='".$obj->getIp()."' where client_id=$clientid";
		$cid=$obj->setResult($chksql);
		if($cid){	
			$successmsg="Update data complete!!";
			$successmsg.=$querystr;
			header("Location: index.php?msg=$successmsg");			
		} else {
			$accessFirstTime = 0;
			$errormsg = $obj->getErrorMsg();
		}
	} else {
		$errormsg = $obj->getErrorMsg();
	}
} else if($add == " add " && $chkPageEdit) {
	$id = $obj->readToInsert($_REQUEST,$filename);
	if($id){
		$sql = "insert into p_clientconfig( " .
				"`client_id`, `global_oasisclient`,`global_gifttypeid`, " .
				"`global_payid`, `global_admingroupuser`) " .
				"values($id, 0, 2, 11, 1)";
		$client_config_id = $obj->setResult($sql);
		if($client_config_id){
			mkdir($_SERVER["DOCUMENT_ROOT"]."/clients/".$_REQUEST["client_name"], 0755);
			mkdir($_SERVER["DOCUMENT_ROOT"]."/clients/".$_REQUEST["client_name"]."/images", 0755);
			mkdir($_SERVER["DOCUMENT_ROOT"]."/clients/".$_REQUEST["client_name"]."/images/branch", 0777);
			mkdir($_SERVER["DOCUMENT_ROOT"]."/clients/".$_REQUEST["client_name"]."/images/user", 0777);
			copy($_SERVER["DOCUMENT_ROOT"]."/clients/_standard/images/branch/sr_tmp.png", 
			$_SERVER["DOCUMENT_ROOT"]."/clients/".$_REQUEST["client_name"]."/images/branch/sr_tmp.png");
			copy($_SERVER["DOCUMENT_ROOT"]."/clients/_standard/images/branch/tmp.png", 
			$_SERVER["DOCUMENT_ROOT"]."/clients/".$_REQUEST["client_name"]."/images/branch/tmp.png");
			copy($_SERVER["DOCUMENT_ROOT"]."/clients/_standard/images/user/default.gif", 
			$_SERVER["DOCUMENT_ROOT"]."/clients/".$_REQUEST["client_name"]."/images/user/default.gif");
			
			$successmsg="Insert data complete!!";
			header("Location: index.php?msg=$successmsg&pageid=$pageid");
		}else{
			$errormsg = $obj->getErrorMsg();
		}
	} else {
		$errormsg = $obj->getErrorMsg();
	}
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="../scripts/components.js"></script>
  <?include("../jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name="clients" id="clients" action="" method="post" style="padding:0;margin:0">
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
				<?include("../menuheader.php");?>
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
							if($clientid){
									$xml = "<command>" .
									"<table>p_clientinfo</table>" .
									"<where name='client_id' operator='='>$clientid</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else {
								echo $obj->gFormInsert('p_clientinfo',$filename);	
							}
 						?>
                    <table boder="0" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px;">
                    	<tbody><tr>
                    	<td colspan="2">
                		<fieldset>
                    		<legend> </legend>
                    		<br/>
                    		<input type="hidden" name="querystr" id="querystr" value="<?=$querystr?>"/>
                    		<input name="id" id="id" type="hidden" value="<?=$clientid?>">
							<input name="add" id="add" type="submit" size="" value="<?=($clientid)?" save change ":" add "?>" onClick='<?=($clientid)?"set_editData(\"p_clientinfo\",$clientid)":"set_insertData(\"p_clientinfo\")"?>' > 
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
	<div class="hiddenbar"><img id="spLine" src="../images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame()"/></div>
</div>
</body>
</html>