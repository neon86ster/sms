<?
include("../../include.php");
require_once("formdb.inc.php");
$obj = new formdb(); 

$obj->setDebugStatus(false);
$filename = 'spamg.xml';
$errormsg ="";

$xml = "<command>".
	  "<table>l_employee</table>
	    <field>*</field>
	    <where name='emp_department_id' operator='='>3</where>" .
	    "<order>emp_nickname asc</order>
	  </command>";
$rs = $obj->getRsXML($xml,$filename);

$xml = "<command>".
	  "<table>gl_gifttype</table>
	    <field>*</field>" .
	    "<where name='gifttype_active' operator='='>1</where>" .
	    "<order>gifttype_name asc</order>
	  </command>";
$gtrs = $obj->getRsXML($xml,$filename);

/***************************************************
 * Security checking
 ***************************************************/
// check user edit permission 
$pageid = "1";	// appointment page
$pagestatus = $object->check_permission($pageid,$permissionrs);
$chkPageEdit=false;$chkPageView=false;
if($pagestatus=="e"){
	$chkPageEdit=true;$chkPageView=true;
}else if($pagestatus=="v"){
	$chkPageEdit=false;$chkPageView=true;
}else if($pagestatus=="n"){
	$chkPageEdit=false;$chkPageView=false;
}

/***************************************************
 * setting gift number start and insert information
 ***************************************************/
if(!isset($_POST["gift_number_start"])){
	$_POST["gift_number_start"]=$obj->getNextId("g_gift","gift_number");
}
if($_POST["add"]==" add " && $update && $_POST["hidden_issue"]<=$_POST["hidden_expired"]) {
	$id = $obj->readToInsertMoreGift($_POST,$filename);
	if($id){
		?>
			<script language="javascript">
				opener.location.reload(); 
				window.close();
			</script>
		<?
	} else {
		$errormsg = $obj->getErrorMsg();
	}
} else {
	header("add_giftinfo.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Add Many Gift Certificates</title>
<link href="../../css/style.css" rel="stylesheet" type="text/css" />

<script src="scripts/ajax.js" type="text/javascript"></script>
<script src="scripts/component.js" type="text/javascript"></script>
<script src="scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="scripts/datechooser/datechooser.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="scripts/datechooser/datechooser.css">
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td class="content" width="100%">
    		<div id="showerrormsg" <? if($errormsg==""&&$_POST["add"]==false){?>style="display:none"<? } else {?>style="display:block"<? }?>>
    			<table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    				<tr>
    					<td ><b><font style="color:#ff0000;">Error message: </font></b><?=$errormsg ?></td>
    				</tr>
    			</table>
    		</div>
    		
    		<form id="form1" name="form1" method="post" action="<? echo $_SERVER['PHP_SELF'];?>">
        	<div>
    			<fieldset>
					<legend><b>Gift Certificates Infomation</b></legend>
				
                   <table style="overflow: auto;" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tbody>
                    <tr>
                    	<td valign="bottom">
                        <div style="vertical-align: inherit;">
                          <table>
							<tbody><tr>
							
							<td valign="top">Gift number start<font style="color: rgb(255, 0, 0);"> *</font> </td>
							<td valign="top"><input id="gift_number_start" name="gift_number_start" maxlength="10" size="" value="<?=$_POST["gift_number_start"]?>" type="text"></td>
							</tr>
							<tr>
							<td valign="top">Gift number end<font style="color: rgb(255, 0, 0);"> *</font> </td>
							<td valign="top"><input id="gift_number_end" name="gift_number_end" maxlength="10" size="" value="<?=$_POST["gift_number_end"]?>" type="text"></td>
							</tr>
							<tr>
							<td valign="top">Give to<font style="color: rgb(255, 0, 0);"> *</font> </td>
							<td valign="top"><input id="give_to" name="give_to" maxlength="" size="" value="<?=$_POST["give_to"]?>" type="text"></td>
							</tr>
							<tr>
							<td valign="top">Receive from<font style="color: rgb(255, 0, 0);"> *</font> </td>
							
							<td valign="top"><input id="receive_from" name="receive_from" maxlength="" size="" value="<?=$_POST["receive_from"]?>" type="text"></td>
							</tr>
							<tr>
							<td valign="top">Value</td>
							<td valign="top"><input id="value" name="value" maxlength="10" size="" value="<?=$_POST["value"]?>" type="text"></td>
							</tr>
							<tr>
							<td valign="top">Type<font style="color: rgb(255, 0, 0);"> *</font></td>
							<td valign="top"><select name="gifttype_id" id="gifttype_id"> 
							<option value="0">---select---</option> 
							<?
								for($i=0;$i<$gtrs["rows"];$i++){
									echo "<option value=\"".$gtrs[$i]["gifttype_id"]."\"";
									if($_POST["gifttype_id"]==$gtrs[$i]["gifttype_id"]){echo " selected";}
									echo ">".$gtrs[$i]["gifttype_name"]."</option>";
								}
							?>
							</select> 
							 
							</td>
							</tr>
							<tr>
							<td valign="top">Issue<font style="color: rgb(255, 0, 0);"> *</font> </td>
							<td valign="top"><input type='hidden' id='hidden_issue' name='hidden_issue' value='<?=(isset($_POST["hidden_issue"]))?$dateobj->convertdate($_POST["hidden_issue"],$sdateformat,'Ymd'):date('Ymd')?>'>
											 &nbsp;&nbsp;<input id="issue" name="issue" value="<?=(isset($_POST["issue"]))?$_POST["issue"]:date($sdateformat)?>" style="width: 85px;" readonly="1" class="textbox" type="text">
											 &nbsp;&nbsp;<img src="../scripts/datechooser/calendar.gif" onclick="showChooser(this, 'issue', 'issue_showSpan', 1900, 2100, '<?=$sdateformat?>', false);"> 
											<div id="issue_showSpan" class="dateChooser" style="padding: 5px 0pt 0pt; background: rgb(170, 238, 170) none repeat scroll 0% 0%; display: none; visibility: hidden; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;" align="center"></div></td>
							
							</tr>
							<tr>
							<td valign="top">Expired<font style="color: rgb(255, 0, 0);"> *</font> </td>
							<td valign="top"><input type='hidden' id='hidden_expired' name='hidden_expired' value='<?=(isset($_POST["hidden_expired"]))?$dateobj->convertdate($_POST["hidden_expired"],$sdateformat,'Ymd'):date('Ymd')?>'>
											 &nbsp;&nbsp;<input id="expired" name="expired" value="<?=(isset($_POST["expired"]))?$_POST["expired"]:date($sdateformat)?>" style="width: 85px;" readonly="1" class="textbox" type="text">
											 &nbsp;&nbsp;<img src="../scripts/datechooser/calendar.gif" onclick="showChooser(this, 'expired', 'expired_showSpan', 1900, 2100, '<?=$sdateformat?>', false);"> 
											<div id="expired_showSpan" class="dateChooser" style="padding: 5px 0pt 0pt; background: rgb(170, 238, 170) none repeat scroll 0% 0%; display: none; visibility: hidden; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;" align="center"></div></td>
							</tr>
							<!--
							<tr>
							<td valign="top">Used</td>
							<td valign="top"><input type='hidden' id='hidden_used' name='hidden_used' value='<?=(isset($_POST["hidden_used"]))?$dateobj->convertdate($_POST["hidden_used"],$sdateformat,'Ymd'):date('Ymd')?>'>
											 &nbsp;&nbsp;<input id="used" name="used" value="<?=(isset($_POST["used"]))?$_POST["used"]:date($sdateformat)?>" style="width: 85px;" readonly="1" class="textbox" type="text">
											 &nbsp;&nbsp;<img src="../scripts/datechooser/calendar.gif" onclick="showChooser(this, 'used', 'used_showSpan', 1900, 2100, '<?=$sdateformat?>', false);"> 
											<div id="used_showSpan" class="dateChooser" style="padding: 5px 0pt 0pt; background: rgb(170, 238, 170) none repeat scroll 0% 0%; display: none; visibility: hidden; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;" align="center"></div></td>
							</tr>
							-->
							<tr>
							<td valign="top">Receive by</td>
							<td valign="top"><select name="receive_by_id" id="receive_by_id"> 
							<option value="0">---select---</option> 
							<?
								for($i=0;$i<$rs["rows"];$i++){
									echo "<option value=\"".$rs[$i]["emp_id"]."\">".$rs[$i]["emp_nickname"]."</option>";
								}
							?>
							</select> 
							 
							</td>
							</tr>
							<tr>
							<td valign="top">Product</td>
							<td valign="top"><input id="product" name="product" maxlength="" size="" value="<?=$_POST["product"]?>" type="text"></td>
							</tr>
							<input id="book_id" name="book_id" value="<?=$_POST["book_id"]?>" type="hidden">
							
							<!--
							<tr>
							<td valign="top">ID sold</td>
							<td valign="top"><input id="id_sold" name="id_sold" maxlength="" size="" value="<?=$_POST["id_sold"]?>" type="text"></td>
							</tr>
							-->
							<input name="formname" id="formname" value="g_gift" type="hidden">
							<input id='l_lu_user' name='l_lu_user' type='hidden' value='thisuser'>
							<input id='l_lu_date' name='l_lu_date' type='hidden' value='thistime'>
							<input id='l_lu_ip' name='l_lu_ip' type='hidden' value='thisip'>
							<input name='formname' id='formname' type='hidden' value='g_gift'>
							</table>

                        </div><br><hr><br></td>
                    </tr>
                    <tr>
                    	<td>
                    		<table border="0" cellpadding="0" cellspacing="0" width="250px" style='overflow:auto'>
			                    <tbody><tr>
			                    <td style="vertical-align:topt" align="center">
			                    <? if($chkPageEdit){ ?>
				               		<input name="id" id="id" type="hidden" value="<?=$_POST["id"]?>">
									<input name="add" id="add" type="submit" value="<?=(isset($_POST["id"]))?" save change ":" add "?>" >
									<input name="cancel" id="cancel" type="submit" value=" cancel " onClick="window.close();" >
								<?}?>  
			                	</td>
			                </tr>
			                </table>
                    	</td>
                     </tr>
                  </tbody></table>
                  <br/><br/>
                </fieldset>
			</div>
			</form>
		</td>
    </tr>
</table>
</body>
</html>