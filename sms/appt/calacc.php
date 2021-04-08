<?php
session_start();
include("../include.php");
require_once("appt.inc.php");
//print_r($_REQUEST);
$obj = new appt();
$pagename = "calacc.php";
$sql = "select tax_percent from l_tax where branch_id=".$_REQUEST["branch_id"]." order by tax_id desc limit 0,1";
$rs = $obj->getResult($sql);
$tax = $rs[0]["tax_percent"];
$sc = $obj->getIdToText($_REQUEST["branch_id"],"bl_branchinfo","servicescharge","branch_id");
$amountdisvat = 0;
$amountdissc = 0;
$amountdisscvat = 0;
$error="";
if(isset($_REQUEST["amount"])){
	$amount = $_REQUEST["amount"];
}
if(is_numeric($_REQUEST["amount"])){
	$amountdissc=(100*$_REQUEST["amount"])/(100+$sc);
	$amountdisvat=(100*$_REQUEST["amount"])/(100+$tax);
	$amountdisscvat=(100*$_REQUEST["amount"])/(100+$tax+$sc+($tax*$sc)/100);
}
else if(isset($_REQUEST["amount"])){
	$error="Please enter number in amount field!!";
}
else{
	$amount = 0;
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Copy To New Booking</title>
<link href='css/style.css' type='text/css' rel='stylesheet'>
<script src="scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="scripts/datechooser/datechooser.js" type="text/javascript"></script>
<!-- Begin Code Data Chooser -->
<link rel="stylesheet" type="text/css" href="scripts/datechooser/datechooser.css">
<body><br>
<div class="group5" width="100%" >
<fieldset>
<legend><b>Amount calculate: </b></legend>
<form action="<?=$pagename?>" method="post">
<div id="showerrormsg" <? if($error==""){?>style="display:none"<? } else {?>style="display:block"<? }?>>
    <table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    	<tr>
    		<td ><b><font style="color:#ff0000;">Error message: </font></b><?=$error?></td>
    	</tr>
    </table>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cusinfo">
  <tr>
    <td>&nbsp;</td>
    <td align="right"><input type="submit" name="Calculate" id="Calculate" value="Calculate" class="button">
    </td>
  </tr>
  <tr>
    <td colspan="2">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="comment">
    	<tr>
          <td class="mainthead">Amount</td>
          <td><input type="text" name="amount" id="amount" value="<?=$amount?>" class="button"></td>
        </tr>
        <tr>
          <td class="mainthead">SC <?=$sc?> %</td>
          <td><input type="text" name="vat" id="vat" readonly="1" class="button" value="<?=$amountdissc?>"></td>
        </tr>
        <tr>
          <td class="mainthead">VAT <?=$tax?> %</td>
          <td><input type="text" name="vat" id="vat" readonly="1" class="button" value="<?=$amountdisvat?>"></td>
        </tr>
    	<tr>
          <td class="mainthead">SC/VAT <?=$tax+$sc+($tax*$sc)/100?> %</td>
          <td><input type="text" name="scvat" id="scvat" readonly="1" class="button" value="<?=$amountdisscvat?>"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<input type="hidden" name="branch_id" value="<?=$_REQUEST["branch_id"]?>">
</form>
<br>
</fieldset>
</body>
