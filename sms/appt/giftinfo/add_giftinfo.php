<?
include("../../include.php");
require_once("formdb.inc.php");
$obj = new formdb(); 

$obj->setDebugStatus(false);
$filename = 'spamg.xml';
$errormsg ="";

//For debug undefined index : . By Ruck : 20-05-2009
$_POST["from"] = $obj->getParameter("from","");
$_POST["add"] = $obj->getParameter("add","");
$_GET["msg"] = $obj->getParameter("msg","");
$_POST["id"] = $obj->getParameter("id","");
$_POST["receive_by_id"] = $obj->getParameter("receive_by_id","0");
$_POST["product"] = $obj->getParameter("product","");
$_POST["book_id"] = $obj->getParameter("book_id","");
$_POST["id_sold"] = $obj->getParameter("id_sold","");
$_POST["available"] = $obj->getParameter("available",1);
$_POST["receive_from"] = $obj->getParameter("receive_from","");
$_POST["product"] = $obj->getParameter("product","");
$_POST["value"] = $obj->getParameter("value","");
$_POST["issue"] = $obj->getParameter("issue","");
$_POST["tb_name"] = $obj->getParameter("tb_name","a_bookinginfo");
$_POST["give_to"] = $obj->getParameter("give_to","");
$again = $obj->getParameter("again","");


// system date format	 					
$chksql = "select long_date,short_date from a_company_info";
$chkrs =$obj->getResult($chksql);
$sdateformat = $obj->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
$dateobj = new convertdate();
$date = $obj->getParameter("date");


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
 * Initial all information
 ***************************************************/
if(isset($_REQUEST["id_sold"])){
	$_POST["id_sold"]=$_REQUEST["id_sold"];
	$_POST["gifttype_id"]=$_REQUEST["gifttype_id"];
	//$_POST["id_sold"]=$obj->getIdToText($_GET["id_sold"],"c_bpds_link","bpds_id","tb_id","tb_name='a_bookinginfo'");
}
$titleMsg="Add Gift Certificates";
if(!isset($_REQUEST["id"])){$_REQUEST["id"]=0;}
if($_REQUEST["id"]){
	$titleMsg="Edit Gift Certificates";
	$_POST["method"]="edit";
}
if($_POST["id"]&&$_POST["add"] != " save change "){	// '&&$_POST["add"] != " save change "' add by natt/June 08,2009 
	//echo "<br>Set Gift from id";
	$sql = "select * from g_gift where gift_id=".$_POST["id"];
 	$rs = $obj->getResult($sql,false);
	$_POST["gift_number"]=$rs[0]["gift_number"];
	$_POST["give_to"]=$rs[0]["give_to"];
	$_POST["receive_from"]=$rs[0]["receive_from"];
	$_POST["value"]=$rs[0]["value"];
	$_POST["product"]=$rs[0]["product"];
	$_POST["book_id"]=$rs[0]["book_id"];
	$_POST["id_sold"]=$obj->getIdToText($rs[0]["id_sold"],"c_bpds_link","bpds_id","tb_id","tb_name='".$rs[0]["tb_name"]."'");
	if($rs[0]["id_sold"]>0&&!$_POST["id_sold"]){$_POST["id_sold"]=$rs[0]["id_sold"];}
	$_POST["receive_by_id"]=$rs[0]["receive_by_id"];
	$_POST["gifttype_id"]=$rs[0]["gifttype_id"];
	$_POST["issue"]=$dateobj->convertdate($rs[0]["issue"],'Y-m-d',$sdateformat);
	$_POST["expired"]=$dateobj->convertdate($rs[0]["expired"],'Y-m-d',$sdateformat);
	$_POST["used"]=$dateobj->convertdate($rs[0]["used"],'Y-m-d',$sdateformat);
	$_POST["hidden_issue"]=$dateobj->convertdate($rs[0]["issue"],'Y-m-d','Ymd');
	$_POST["hidden_expired"]=$dateobj->convertdate($rs[0]["expired"],'Y-m-d','Ymd');
	$_POST["hidden_used"]=$dateobj->convertdate($rs[0]["used"],'Y-m-d','Ymd');
	$_POST["available"]=$rs[0]["available"];
	$_POST["tb_name"]=$rs[0]["tb_name"];
}else if(!isset($_POST["gift_number"])){
	//echo "<br>Set Next Gift";
	$_POST["gift_number"]=$obj->getNextId("g_gift","gift_number");
}
if(!$_POST["issue"]){
	$expiredDate = $dateobj->plusyear(date('Ymd'),1);
	$_POST["issue"]=date($sdateformat);
	$_POST["expired"]=$dateobj->convertdate($expiredDate,'Ymd',$sdateformat);
	$_POST["used"]=date($sdateformat);
	$_POST["hidden_issue"]=date('Ymd');
	$_POST["hidden_expired"]=$expiredDate;
	$_POST["hidden_used"]="00000000";
}

if($_POST["add"] == " save change " ){
	$id = $obj->readToUpdate($_POST,$filename);
	if($id){
		echo "<script language=\"javascript\">";
		if($_REQUEST["from"]&&$_REQUEST["from"]!="book"){
			?>
				try{
					opener.document.getElementById("appt").submit();
				}catch(e){
					opener.document.getElementById("pdforsale").submit();	
				}
				
			<?
		}else{
			echo "opener.location.reload();" ;	// update success natt/June 08,2009
		}
		echo "window.close();";
		echo "</script>";
	} else {
		$_POST["add"]=false;
			$errormsg = $obj->getErrorMsg();
	}
} else if($_POST["add"]==" add ") {
	$rs = $obj->readToInsert($_POST,$filename);
	$scStat=false;
	//echo $again;
	if($rs){
		echo "<script language=\"javascript\">";
		if($_REQUEST["from"]!="book"){
			?>
			try{
				opener.document.getElementById("appt").submit();
			}catch(e){
				opener.document.getElementById("pdforsale").submit();	
			}
			<?
		}
		if($again=="1"){
			$successmsg="Insert data complete. And add more gift certificate.";
			header("Location: add_giftinfo.php?msg=$successmsg&id_sold=".$_REQUEST["id_sold"]."&from=".$_REQUEST["from"]);
			unset($_POST);
		}else{
			echo "window.close();";
		}
		echo "</script>";
	} else {
		unset($_POST["id"]);
		$errormsg = $obj->getErrorMsg();
	}
} 
if(!isset($_POST["id"])){$_POST["id"]="";}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$titleMsg?></title>
<link href="../../css/style.css" rel="stylesheet" type="text/css" />

<script src="scripts/ajax.js" type="text/javascript"></script>
<script src="scripts/component.js" type="text/javascript"></script>
<script src="scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="scripts/datechooser/datechooser.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="scripts/datechooser/datechooser.css">
</head>
<body>

<form action="<?=$_SERVER["PHP_SELF"]?>" method="post">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td class="content" width="100%">
    		<div id="showmsg" <? if(!$_GET["msg"]) { ?>style="display:none"<? } ?>>
    			<table style="border: solid 3px #008000;" cellspacing="0" cellpadding="10">
    				<tr>
    					<td><b><font style="color:#008000;">Success message: </font></b><?=$_GET["msg"]?>
						</td>
    				</tr>
    			</table>
    		</div>
    		<div id="showerrormsg" <? if($errormsg==""&&$_POST["add"]==false){?>style="display:none"<? } else {?>style="display:block"<? }?>>
    			<table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    				<tr>
    					<td ><b><font style="color:#ff0000;">Error message: </font></b><?=$errormsg ?></td>
    				</tr>
    			</table>
    		</div>
        	<div>
    			<fieldset>
					<legend><b>Gift Certificates Infomation</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style='overflow:auto'>
                    <tbody><tr>
                    	<td valign="bottom">
                        <div style="vertical-align:inherit">
                         <table><input id='gift_id' name='gift_id' type='hidden' value='<?=$_POST["id"]?>'>
                         <input id='available' name='available' type='hidden' value='<?=$_POST["available"]?>'>
                         <input id='from' name='from' type='hidden' value='<?=$_POST["from"]?>'>
							<tr>
							<td valign='top'>Gift number<font style='color:#ff0000'> *</font> </td>
							<td valign='top'><input id='gift_number' type='text' name='gift_number' maxlength='10' size='' value='<?=$_POST["gift_number"]?>' ></td>
							</tr>
							<tr>
							<td valign='top'>Give to<font style='color:#ff0000'> *</font> </td>
							
							<td valign='top'><input id='give_to' type='text' name='give_to' maxlength='' size='' value='<?=$_POST["give_to"]?>' ></td>
							</tr>
							<tr>
							<td valign='top'>Receive from<font style='color:#ff0000'> *</font> </td>
							<td valign='top'><input id='receive_from' type='text' name='receive_from' maxlength='' size='' value='<?=$_POST["receive_from"]?>' ></td>
							</tr>
							<tr>
							<td valign='top'>Value</td>
							<td valign='top'><input id='value' type='text' name='value' maxlength='10' size='' value='<?=$_POST["value"]?>' ></td>
							</tr>
							<tr>
							<td valign='top'>Type</font></td>
							<td valign='top'>
							<input id='pathFrom' name='pathFrom' type='hidden' value='appt'>
							<input id='gifttype_id' name='gifttype_id' type='hidden' value='<?=$_POST["gifttype_id"]?>'>
							<select name="gifttype_id" id="gifttype_id" disabled="disabled"> 
							<option value='0'>---select---</option> 
							<?
								$sql = "select * from gl_gifttype where gifttype_active=1 order by gifttype_name ASC";
 								$rs = $obj->getResult($sql,false);
 								for($i=0;$i<$rs["rows"];$i++){
 									if($_POST["gifttype_id"]==$rs[$i]["gifttype_id"]){
 										echo "<option value=\"".$rs[$i]["gifttype_id"]."\" selected=\"selected\">".$rs[$i]["gifttype_name"]."</option>";
 									}else{
 										echo "<option value=\"".$rs[$i]["gifttype_id"]."\">".$rs[$i]["gifttype_name"]."</option>";
 									}
 								}
							?>
							</select> 
							 
							</td>
							</tr>
							<tr>
							<td valign='top'>Issue<font style='color:#ff0000'> *</font> </td>
							
							<td valign='top'>&nbsp;&nbsp;<input id='issue' name='issue' value='<?=$_POST["issue"]?>' style="width: 85px;" readonly="1" class="textbox" type="text">
														<input id='hidden_issue' name='hidden_issue' value="<?=$_POST["hidden_issue"];?>" type="hidden"/>
														            &nbsp;&nbsp;<img src="../scripts/datechooser/calendar.gif" onclick="showChooser(this, 'issue', 'issue_showSpan', 1900, 2100, '<?=$sdateformat?>', false);"> 
																	<span id="issue_showSpan" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"/></td>
							</tr>
							<tr>
							<td valign='top'>Expired<font style='color:#ff0000'> *</font> </td>
							<td valign='top'>&nbsp;&nbsp;<input id='expired' name='expired' value='<?=$_POST["expired"]?>' style="width: 85px;" readonly="1" class="textbox" type="text">
														<input id='hidden_expired' name='hidden_expired' value="<?=$_POST["hidden_expired"]?>" type="hidden"/>
											&nbsp;&nbsp;<img src="../scripts/datechooser/calendar.gif" onclick="showChooser(this, 'expired', 'expired_showSpan', 1900, 2100, '<?=$sdateformat?>', false);"> 
														<span id="expired_showSpan" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"/></td>
							</tr>
							<? if($_POST["id"]){ ?>
							<tr>
							<td valign='top'>Receive by</td>
							<td valign='top'><select name="receive_by_id" id="receive_by_id" > 
							<option value='0'>---select---</option> 
							<?
								$sql = "select * from l_employee where emp_department_id='3' and emp_active=1 order by emp_nickname ASC";
 								$rs = $obj->getResult($sql,false);
 								for($i=0;$i<$rs["rows"];$i++){
 									if($_POST["receive_by_id"]==$rs[$i]["emp_id"]){
 										echo "<option value=\"".$rs[$i]["emp_id"]."\" selected=\"selected\">".$rs[$i]["emp_nickname"]."</option>";
 									}else{
 										echo "<option value=\"".$rs[$i]["emp_id"]."\">".$rs[$i]["emp_nickname"]."</option>";
 									}
 								}
							?>
							</select> 
							 
							</td>
							</tr>
							<? } ?>
							<tr>
							<td valign='top'>Product</td>
							<td valign='top'><input id='product' type='text' name='product' maxlength='' size='' value='<?=$_POST["product"]?>' ></td>
							</tr>
							<input id='book_id' name='book_id' type='hidden' value='<?=$_POST["book_id"]?>'>
							<input id='id_sold' name='id_sold' type='hidden' value='<?=$_POST["id_sold"]?>' ></td>
							</tr>
							<input id='l_lu_user' name='l_lu_user' type='hidden' value='thisuser'>
							<input id='l_lu_date' name='l_lu_date' type='hidden' value='thistime'>
							<input id='l_lu_ip' name='l_lu_ip' type='hidden' value='thisip'>
							<input name='formname' id='formname' type='hidden' value='g_gift'>
							</table>

                        </div><br><hr></td>
                    </tr>
                    <tr>
                    	<td>
                    		<table border="0" cellpadding="0" cellspacing="0" width="250px" style='overflow:auto'>
			                    <tbody><tr>
			                    <td style="vertical-align:topt" align="center">
			                    <? if($chkPageEdit){?>
				               		<? if(!isset($_POST["id"])){ ?>
										<br/>
										<input name="again" id="again" value="1" type="checkbox">&nbsp;&nbsp;&nbsp;Add again
										<br/>
								  <? } ?>
									<br/>
									<input name="from" id="from" type="hidden" value="<?=$_POST["from"]?>">
									<input name="id" id="id" type="hidden" value="<?=$_POST["id"]?>">
									<input name="tb_name" id="tb_name" type="hidden" value="<?=$_POST["tb_name"]?>">
									<input name="add" id="add" type="submit" value="<?=($_POST["id"])?" save change ":" add "?>" >
									<input name="cancel" id="cancel" type="submit" value=" cancel " onClick="window.close();" >  
				                	<?}else if($errorMsg){
					                	echo "<b class=\"style1\">$errorMsg</b>";
					                }
					                ?>
			                	</td>
			                </tr>
			                </table>
                    	</td>
                    </tr>
                  </tbody></table>
                  <br/><br/>
                </fieldset>
            </div>
		</td>
    </tr>
   
</table>
</form>
</body>
</html>