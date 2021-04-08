<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb();
$filename = "../object.xml";
$errormsg ="";
$memberid = $obj->getParameter("id");

//for contract
$contract = $obj->getParameter("table"); 

// for return to the same page 
$categoryid = $obj->getParameter("categoryid");
$method = $obj->getParameter("method");
$order = $obj->getParameter("order");
$page = $obj->getParameter("page",1);
$sort=$obj->getParameter("sort");
$search = $obj->getParameter("search");
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
$querystr = "&pageid=$pageid&search=$searchstr&order=$order&page=$page&sort=$sort&categoryid=".$categoryid;

if($method=="setactive" && $chkPageEdit){
	
	$sql = "";
  if($contract!="phone" && $contract!="mobile" && $contract!="email"){
	$active = $obj->getParameter("active");
	$sql = "update m_membership set expired=$active where member_id=$memberid";
	$name = $obj->setResult($sql);
	if($name!=false){
		$name = $obj->getIdToText($memberid,"m_membership","member_code","member_id");
		if($active==1){
			$successmsg="Member code : $name is active!!";
		}else{
			$successmsg="Member code : $name is inactive!!";
		}
		$successmsg=$successmsg.$querystr;
		header("Location: index.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
  }else{
  		$active = $obj->getParameter("active");
  		$sql = "update m_membership set chk_".$contract."=$active where member_id=$memberid";
	    $name_contract = $obj->setResult($sql);
	    if($name_contract!=false){
		$name = $obj->getIdToText($memberid,"m_membership","member_code","member_id");
		if($active==1){
			$successmsg="Member code : $name Contract from $contract is active!!";
		}else{
			$successmsg="Member code : $name Contract from $contract is inactive!!";
		}
		$successmsg=$successmsg.$querystr;
		header("Location: index.php?msg=$successmsg");
		} else {
			$errormsg = $obj->getErrorMsg();
		}
  }
}
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

if(!isset($_REQUEST["member_code"])&&!$memberid){
	$_GET["member_code"]=$obj->getNextId("m_membership","member_code");
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
select.ctrDropDown{
    width:121px;
    font-size:11px;
}
select.plainDropDown{
    width:121px;
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
        	<div id="gform">
        		        <? 
							if($memberid){
									$xml = "<command>" .
									"<table>m_membership</table>" .
									"<where name='member_id' operator='='>$memberid</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else {
								echo $obj->gFormInsert('m_membership',$filename);	
								if($errormsg){
									?>
									<script type="text/javascript">
										document.getElementById('member_code').value='<?=$obj->getParameter("member_code")?>';
										document.getElementById('fname').value='<?=$obj->getParameter("fname")?>';
										document.getElementById('mname').value='<?=$obj->getParameter("mname")?>';
										document.getElementById('lname').value='<?=$obj->getParameter("lname")?>';
										document.getElementById('category_id').value='<?=$obj->getParameter("category_id")?>';
										document.getElementById('sex_id').value='<?=$obj->getParameter("sex_id")?>';
										document.getElementById('nationality_id').value='<?=$obj->getParameter("nationality_id")?>';
										document.getElementById('joindate').value='<?=$obj->getParameter("joindate")?>';
										document.getElementById('expireddate').value='<?=$obj->getParameter("expireddate")?>';
										document.getElementById('birthdate').value='<?=$obj->getParameter("birthdate")?>';
										document.getElementById('address').value='<?=$obj->getParameter("address")?>';
										document.getElementById('city').value='<?=$obj->getParameter("city")?>';
										document.getElementById('state').value='<?=$obj->getParameter("state")?>';
										document.getElementById('zipcode').value='<?=$obj->getParameter("zipcode")?>';
										document.getElementById('phone').value='<?=$obj->getParameter("phone")?>';
										document.getElementById('mobile').value='<?=$obj->getParameter("mobile")?>';
										document.getElementById('email').value='<?=$obj->getParameter("email")?>';
										document.getElementById('comments').value='<?=$obj->getParameter("comments")?>';
										<?if($obj->getParameter("chk_phone")){?>
											document.getElementById('chk_phone').checked = true;
										<?}?>
										<?if($obj->getParameter("chk_mobile")){?>
											document.getElementById('chk_mobile').checked = true;
										<?}?>
										<?if($obj->getParameter("chk_email")){?>
											document.getElementById('chk_email').checked = true;
										<?}?>
									</script>
									<?
								}
							}
 						?>
                    <table boder="0" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px;">
                    	<tbody><tr>
                    	<td colspan="2">
                		<fieldset>
                    		<legend> </legend>
                    		<br/>
                    		<input type="hidden" name="querystr" id="querystr" value="<?=$querystr?>"/>
                    		<input name="id" id="id" type="hidden" value="<?=$memberid?>">
							<input name="add" id="add" type="button" size="" value="<?=($memberid)?" save change ":" add "?>" onClick='<?=($memberid)?"set_editData(\"m_membership\",$memberid)":"set_insertData(\"m_membership\")"?>' > 
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