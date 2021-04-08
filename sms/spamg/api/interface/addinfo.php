<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");

$obj = new formdb();
$filename = "../object.xml";
$errormsg ="";
$bankacccmsid = $obj->getParameter("id");
// for return to the same page 
$showinactive = $obj->getParameter("showinactive");
//$showdetail=$obj->getParameter("showdetail",0);
$method = $obj->getParameter("method");
$order = $obj->getParameter("order");
$page = $obj->getParameter("page");
$sort=$obj->getParameter("sort");
$search = $obj->getParameter("search");
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
//$querystr = "&pageid=$pageid&search=$searchstr&order=$order&page=$page&sort=$sort&showdetail=$showdetail&showinactive=".$showinactive;
$querystr = "&pageid=$pageid&search=$searchstr&order=$order&page=$page&sort=$sort&showinactive=".$showinactive;

$uid = $_SESSION["__user_id"];
$sql_uid="SELECT userpermission_id 	 FROM `s_userpermission` WHERE `user_id` =".$uid." AND `group_id` =1";
$rs_uid=$obj->getResult($sql_uid);
if($rs_uid || $_SESSION["adminExpert"]==1){
	$date_s_add=true;
}else{
	$date_s_add=false;
}
/////////////////////
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
$sql_save="INSERT INTO c_account 
( account_id, name, customer_name, branch_id, account_num,template ) VALUES( \"\" ,\"$n2\" , \"$n1\" , \"$n3\" , \"$n4\" , \"$n5\" ) " ; 
echo "<br><br><br><br><br>".$sql_save;
$rs_save=$obj->setResult($sql_save);
$successmsg="Update data complete!!";
			$successmsg.=$querystr;
			header("Location: index.php?msg=$successmsg");

}
}


///////////////	
/////////////////////
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
							if($bankacccmsid){
									$xml = "<command>" .
									"<table>al_bankacc_cms</table>" .
									"<where name='bankacc_cms_id' operator='='>$bankacccmsid</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);
						?>
<?
$hidden_c_add=$obj->getIdToText($bankacccmsid,"al_bankacc_cms","c_lu_user","bankacc_cms_id");

$hidden_c_date=$obj->getIdToText($bankacccmsid,"al_bankacc_cms","c_lu_date","bankacc_cms_id");

list($date_add, $time_add) = explode(' ', $hidden_c_date);
list($year_add, $month_add, $day_add) = explode('-', $date_add);
list($hour_add, $minute_add, $second_add) = explode(':', $time_add);

$timestamp = mktime($hour_add, $minute_add, $second_add, $month_add, $day_add, $year_add);

$e_date_add = date("d-M-y",$timestamp);
?>
<?if($date_s_add){?>
<table class="generalinfo"> 
<tbody>

<tr>
	  <td valign='top' class="add">Add by</td>
	  <td valign='top'><select name="add_by_id" id="add_by_id"> 
		<option value='0'>---select---</option> 
		<?
		$sql = "select s_user.u_id,s_user.u,s_userpermission.group_id from s_user,s_userpermission where s_user.u_id=s_userpermission.user_id " .
				"and s_userpermission.group_id in (1,10,11) order by u";
 		$rs = $obj->getResult($sql,false);
 				for($i=0;$i<$rs["rows"];$i++){
 					if($hidden_c_add==$rs[$i]["u_id"]){
 						echo "<option value=\"".$rs[$i]["u_id"]."\" selected=\"selected\">".$rs[$i]["u"]."</option>";
 					}else{
 						echo "<option value=\"".$rs[$i]["u_id"]."\">".$rs[$i]["u"]."</option>";
 					}
 				}
		?>
		</select> 		 
	</td>
</tr>

<tr> 
<td>Add time</td> 
<td valign="top"> 
&nbsp;&nbsp;
<input id="hidden_c_date" name="hidden_c_date" value="<?=$hidden_c_date?>" type="hidden">
<input id="c_date" name="c_date" value="<?=$e_date_add?>" style="width: 85px;" readonly="1" class="textbox" type="text">
&nbsp;&nbsp;<img src="/images/calendar.png" onclick="showChooser(this, 'c_date', 'c_date_showSpan', 1900, 2100, 'd-M-y', false);"><span id="c_date_showSpan" class="dateChooser" style="padding: 5px 0pt 0pt; background: rgb(170, 238, 170) none repeat scroll 0% 0%; display: none; visibility: hidden; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;" align="center"></span></td> 
</tr>
</tbody>
<?}else{?>
	<input id="hidden_c_date" name="hidden_c_date" value="<?=$hidden_c_date?>" type="hidden">
<?}?>			
						<?
							
							} else {
								echo $obj->gFormInsert('al_bankacc_cms',$filename);	
						?>

<table >
<tr>
<td>Customer Name</td>
<td><input type=text name=n1 value=<?=$n1?>></td>
</tr>
<tr>
<td>Transection</td>
<td><input type=text name=n2 value=<?=$n2?>></td>
</tr>
<tr>
<td>Branch</td>
<td><select name=n3> 
		<option value='0'>---select---</option> 
		<?
		$sql = "select * from bl_branchinfo where branch_active = 1  ";
		
 		$rss = $obj->getResult($sql,false);
 				for($i=1;$i<$rss["rows"];$i++){
 					if($rss[$i]["branch_id"]=="0"){
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
<td><input type=text name=n3 value=<?=$n3?>></td>-->
</tr>
<tr>
<td>Account Number</td>
<td><select name=n4> 
		<option value='0'>---select---</option> 
		<?
		$sql = "select * from l_account where account_active = 1  ";
		
 		$rss = $obj->getResult($sql,false);
 				for($i=0;$i<$rss["rows"];$i++){
 					if($rss[$i]["pay_account_id"]=="0"){
 						echo "<option value=\"".$rss[$i]["pay_account_id"]."\" selected=\"selected\">".$rss[$i]["pay_account_name"]."</option>";
 					}
 					else
 					{
 						echo "<option value=\"".$rss[$i]["pay_account_id"]."\">".$rss[$i]["pay_account_name"]."</option>";
 					}
 				}
		?>
		
		</select> 	</td>
<!--<td><input type=text name=n4 value=<?=$n4?>></td>-->
</tr>
<tr>
<td>Template</td>
<td><select name=n5> 
		<option value='0'>---select---</option> 
		<?
		$sql = "select * from l_template where template_active = 1  ";
		
 		$rss = $obj->getResult($sql,false);
 				for($i=0;$i<$rss["rows"];$i++){
 					if($rss[$i]["pay_template_id"]=="0"){
 						echo "<option value=\"".$rss[$i]["pay_template_id"]."\" selected=\"selected\">".$rss[$i]["pay_template_name"]."</option>";
 					}
 					else
 					{
 						echo "<option value=\"".$rss[$i]["pay_template_id"]."\">".$rss[$i]["pay_template_name"]."</option>";
 					}
 				}
		?>
		
		</select> 	</td>
<!--<td><input type=text name=n5 value=<?=$n5?>></td>-->
</tr>
</table> 

<table class="generalinfo"> 
<tbody><tr><br>
<td>Add time : <?=date("d-M-y H:i:s")?></td> 
</tr></tbody>
						
						<?	
							}
 						?>
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