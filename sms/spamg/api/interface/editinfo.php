<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");


$obj = new formdb();
$filename = "../object.xml";
$errormsg ="";

// for return to the same page 
$showinactive = $obj->getParameter("showinactive");
//$showdetail=$obj->getParameter("showdetail",0);
$method = $obj->getParameter("method");
$order = $obj->getParameter("order");
$page = $obj->getParameter("page");
$sort=$obj->getParameter("sort");
$search = $obj->getParameter("search");
$categoryid= $obj->getParameter("categoryid");

$n = $obj->getParameter("n");
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
//$querystr = "&pageid=$pageid&search=$searchstr&order=$order&page=$page&sort=$sort&showdetail=$showdetail&showinactive=".$showinactive;
$querystr = "&n=$n&pageid=115&search=$searchstr&order=$order&page=$page&sort=$sort&showinactive=".$showinactive;

$uid = $_SESSION["__user_id"];
$sql_uid="SELECT userpermission_id 	 FROM `s_userpermission` WHERE `user_id` =".$uid." AND `group_id` =1";
$rs_uid=$obj->getResult($sql_uid);
if($rs_uid || $_SESSION["adminExpert"]==1){
	$date_s_add=true;
}else{
	$date_s_add=false;
}

/////////////////////
$n = $obj->getParameter("n");
$n1 = $obj->getParameter("n1");
$n2 = $obj->getParameter("n2");
$n3 = $obj->getParameter("n3");
$n4 = $obj->getParameter("n4");
$n5 = $obj->getParameter("n5");

$save=$obj->getParameter("save",false);
if($save){
	


if($n1=="" || $n2=="" || $n4=="" || $n5=="")
{
	$successmsg="Update data complete!!";
			$successmsg.=$querystr;
			header("Location: addinfo.php?msg=$successmsg");
			
}
else{	


$sql_save="UPDATE c_account 
SET customer_name='$n1',name='$n2',branch_id='$n3', account_num='$n4',template='$n5' WHERE account_id='$n' " ;

//echo "<br><br><br><br><br>".$sql_save;
$rs_save=$obj->setResult($sql_save);
$successmsg="Update data complete!!";
			$successmsg.=$querystr;
			header("Location: index.php?msg=$successmsg");

}
}


///////////////	
if($method=="setactive" && $chkPageEdit){
	$sql = "";
	$active = $obj->getParameter("active");
	$name = $obj->setActive($_REQUEST,$filename);
	if($name!=false){
		$bankacccmsid = $obj->getParameter("id");
		$userid = $_SESSION["__user_id"];
		$userip = $_SERVER["REMOTE_ADDR"];
		$chksql = "INSERT `log_al_bankacc` " .
					"(`bankacc_cms_id`, `c_bp_phone`, `c_bp_person`," .
					"`c_bp_id`,`tb_name`,`bank_id`,`bank_branch`," .
					"`bankacc_name`,`bankacc_number`,`bankacc_active`," .
					"`bankacc_comment`,l_lu_user,l_lu_date,l_lu_ip) " .
					"SELECT `bankacc_cms_id`,`c_bp_phone`,`c_bp_person`," .
					"`c_bp_id`,`tb_name`,`bank_id`," .
					"`bank_branch`,`bankacc_name`,`bankacc_number`," .
					"`bankacc_active`,`bankacc_comment`,'$userid',now(),'$userip' " .
					"FROM al_bankacc_cms where al_bankacc_cms.bankacc_cms_id=$bankacccmsid;";
		$logid = $obj->setResult($chksql);
		
		if($logid){
			if($active==1){
				$successmsg="$name is active!!";
			}else{
				$successmsg="$name is inactive!!";
			}
			$successmsg=$successmsg.$querystr;
			header("Location: index.php?msg=$successmsg");
		}else{			
			$errormsg = $obj->getErrorMsg();
		}
	} else {
			$errormsg = $obj->getErrorMsg();
	}
}
$add = $obj->getParameter("add");
if($add == " save change " && $chkPageEdit){
	
	$userid = $_SESSION["__user_id"];
    $userip = $_SERVER["REMOTE_ADDR"];
    $add_by_id = $obj->getParameter("add_by_id");
    if($add_by_id){
    	$_REQUEST["c_lu_user"]=$add_by_id;
    }
    
    $hidden_c_date = $obj->getParameter("hidden_c_date");
    if($hidden_c_date){
	 	//$_REQUEST["c_lu_user"]=$userid;
		$_REQUEST["c_lu_ip"]=$userip;
	 	$_REQUEST["c_lu_date"]=$hidden_c_date;
	}
	
    $_REQUEST["l_lu_user"]=$userid;
	$_REQUEST["l_lu_ip"]=$userip;
	
	$id = $obj->readToUpdate($_REQUEST,$filename);
	if($id){
		$_REQUEST["formname"] = "log_al_bankacc";
		$_REQUEST["bankacc_cms_id"] = $bankacccmsid;
		
		$logid = $obj->readToInsert($_REQUEST,$filename);
		if($logid){
			$successmsg="Update data complete!!";
			$successmsg.=$querystr;
			header("Location: index.php?msg=$successmsg");
		}else{			
			$errormsg = $obj->getErrorMsg();
		}
	} else {
		$errormsg = $obj->getErrorMsg();
	}
} else if($add == " add " && $chkPageEdit) {
	$userid = $_SESSION["__user_id"];
    $userip = $_SERVER["REMOTE_ADDR"];
	$_REQUEST["c_lu_user"]=$userid;
	$_REQUEST["c_lu_date"]=$_REQUEST["l_lu_date"];
	$_REQUEST["c_lu_ip"]=$userip;
	$_REQUEST["l_lu_user"]="";
	$_REQUEST["l_lu_date"]="";
	$_REQUEST["l_lu_ip"]="";
	$id = $obj->readToInsert($_REQUEST,$filename);
	if($id){
		$_REQUEST["formname"] = "log_al_bankacc";
		$_REQUEST["bankacc_cms_id"] = $id;
		$logid = $obj->readToInsert($_REQUEST,$filename);
		if($logid){
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
<script type="text/javascript" src="../scripts/ajax.js"></script>
<script src="/scripts/date-functions.js" type="text/javascript"></script>
<script src="/scripts/datechooser.js" type="text/javascript"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
<style>
table.generalinfo td.add{
	width: 33%;
}
</style>
</head>
<body>
<form name="thavi" method="post" id="thavi" action="" method="post" style="padding:0;margin:0">

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
				<?//include("$root/menuheader.php");?>
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="mainheader">
	<tr>
   					<td width="50%"></td>
   					<td width="38%"></td>
   					<td width="6%"></td>
   					<td width="6%"></td>
  	</tr>
	<tr>
		<td height="47" style="background-image: url('/images/<?=$theme?>/header.png');">
						 <table height="47" border="0" cellpadding="0" cellspacing="0" style='overflow:auto;' class="dir">
					         <tbody>
						         <tr><td>
						        
						         	
						        <a href="javascript:;" onclick="gotoURL('<?=$pageinfo["parenturl"][0]?>')" target="mainFrame">Home &gt</a>
						         <a href="javascript:;" onclick="gotoURL('index.php?pageid=113')" target="mainFrame">Prefrences &gt</a>
						         <a href="javascript:;" onclick="gotoURL('index.php?pageid=113')" target="mainFrame">API &gt</a>
						         <a href="javascript:;" onclick="gotoURL('index.php?pageid=115')" target="mainFrame">Interface &gt</a>
						          </td></tr>
						         <tr><td><b>Manage Interface</b></td></tr>
					         </tbody>
						 </table>
 			<input type="hidden" id="parent" name="parent" value="<?=$parent?>">
		</td>
		<td height="47" align="right" style="background-image: url('/images/<?=$theme?>/header.png');">
						<img src="/images/separate.png" />&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="javascript:;" onclick="gotoURL('<?=$pageinfo["parenturl"][0]?>')" target="_parent"><img src="/images/<?=$theme?>/home.png" border="0" title="Home" /></a>&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="/images/separate.png" />&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="javascript:;" onclick="gotoURL('index.php?pageid=113')" target="_parent"><img src="/images/<?=$theme?>/up.png" border="0" title="Up" /></a>&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="/images/separate.png" />&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="/logout.php" target="_parent"><img src="/images/<?=$theme?>/logout.png" border="0" title="Logout" /></a>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="/images/separate.png" />&nbsp;&nbsp;&nbsp;&nbsp;
		</td>
		<td height="47" align="right" style="background-image: url('/images/<?=$theme?>/header.png');">
						<font style="font-size:11px;color:#444;">WELCOME 
						<br><?=strtoupper($_SESSION["__user"])?>
						<br>
						<a href="/logout.php" target="_parent" style="color:#666666;font-weight: bold;">
						logout
						</a>
						</font>
					</td>
		<td height="47" align="center" style="background-image: url('/images/<?=$theme?>/header.png');">
						<span>
						<img style="border:1px solid #5792a9;" src="<?=$customize_part?>/images/user/<?=$obj->getIdToText($_SESSION["__user_id"], "s_user", "upic", "u_id")?>" width="40px" height="40px">
						</span>
		</td>
	</tr>
	<tr>
		<td colspan="4" height="2" background="#eae8e8"><img src="/images/blank.gif" height="2px"></td>
	</tr>
</table>


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
extract($_POST);



$sql= "select * from c_account where account_id='$n'" ;
$aa = $obj->getResult($sql);

	for($x=0; $x<$aa["rows"]; $x++) {
//echo $aa[$x]["name"];
?>
<table >
<tr>
<td></td>
<td><input type=hidden name=n value="<?=$n=$aa[$x]["account_id"]?>"></td>
</tr>
<tr>
<td>Customer Name</td>
<td><input type=text name=n1 value="<?=$n1=$aa[$x]["customer_name"]?>"></td>
</tr>
<tr>
<td>Transection</td>
<td><input type=text name=n2 value="<?=$n2=$aa[$x]["name"]?>"></td>
</tr>
<tr>
<td>Branch</td>
<td><select name=n3> 
		<option value='0'>---select---</option> 
		<?
		$sql = "select * from bl_branchinfo where branch_active = 1  ";
		
 		$rss = $obj->getResult($sql,false);
 				for($i=1;$i<$rss["rows"];$i++){
 					if($rss[$i]["branch_id"]==$aa[$x]["branch_id"]){
 						echo "<option value=\"".$rss[$i]["branch_id"]."\" selected=\"selected\">".$rss[$i]["branch_name"]."</option>";
 					}
 					else
 					{
 						echo "<option value=\"".$rss[$i]["branch_id"]."\">".$rss[$i]["branch_name"]."</option>";
 					}
 				}
		?>
		
		</select> 	</td>
<!--<td>Branch</td>
<td><input type=text name=n3 value="<?=$n3=$aa[$x]["branch_id"]?>"></td>-->
</tr>
<tr>
<td>Account Number</td>
<td><select name=n4> 
		<option value='0'>---select---</option> 
		<?
		$sql = "select * from l_account where account_active = 1  ";
		
 		$rss = $obj->getResult($sql,false);
 				for($i=0;$i<$rss["rows"];$i++){
 					if($rss[$i]["pay_account_id"]==$aa[$x]["account_num"]){
 						echo "<option value=\"".$rss[$i]["pay_account_id"]."\" selected=\"selected\">".$rss[$i]["pay_account_name"]."</option>";
 					}
 					else
 					{
 						echo "<option value=\"".$rss[$i]["pay_account_id"]."\">".$rss[$i]["pay_account_name"]."</option>";
 					}
 				}
		?>
		
		</select> 	</td>
<!--<td><input type=text name=n4 value="<?=$n4=$aa[$x]["account_num"]?>"></td>-->
</tr>
<tr>
<td>Template</td>
<td><select name=n5> 
		<option value='0'>---select---</option> 
		<?
		$sql = "select * from l_template where template_active = 1  ";
		
 		$rss = $obj->getResult($sql,false);
 				for($i=0;$i<$rss["rows"];$i++){
 					if($rss[$i]["pay_template_id"]==$aa[$x]["template"]){
 						echo "<option value=\"".$rss[$i]["pay_template_id"]."\" selected=\"selected\">".$rss[$i]["pay_template_name"]."</option>";
 					}
 					else
 					{
 						echo "<option value=\"".$rss[$i]["pay_template_id"]."\">".$rss[$i]["pay_template_name"]."</option>";
 					}
 				}
		?>
		
		</select> 	</td>
<!--<td><input type=text name=n5 value="<?=$n5=$aa[$x]["template"]?>"></td>-->
</tr>
</table> 
<?}?>
<table class="generalinfo"> 
<tbody><tr><br>
<td>Add time : <?=date("d-M-y H:i:s")?></td> 
</tr></tbody>
						
						
                    <table boder="0" cellpadding="0" cellspacing="0" width="100%" class="generalinfo" style="margin-top:0px;">
                    	<tbody><tr>
                    	<td colspan="2">
                		<fieldset>
                    		<legend> </legend>
                    		<br/>
                    		<input type="hidden" name="querystr" id="querystr" value="<?=$querystr?>"/>
                    		<input name="id" id="id" type="hidden" value="<?=$bankacccmsid?>">
                    		<input type="submit" name="save" value="save">
							<input name="cancel" id="cancel" type="button" value=" cancel " onClick="gotoURL('index.php?method=cancel<?=$querystr?>');" style="font-size:11px">
							
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
</form>
</body>
</html>