<?
$root = $_SERVER["DOCUMENT_ROOT"]; 
include("$root/include.php");
require_once("report.inc.php");
$obj = new report();
$obj->setDebugStatus(false);

$date = $obj->getParameter("date",false);
if($date){
	$begin = $obj->getBegin($date,$sdateformat);
	$end = $obj->getEnd($date,$sdateformat);
}else{
	$date = 17;
	$begin = $obj->getParameter("begin",$obj->getBegin($date,$sdateformat)); 
	$end = $obj->getParameter("end",$obj->getEnd($date,$sdateformat));
}
$branch = $obj->getParameter("branchid",false);
$payid = $obj->getParameter("pay_id",false);

if($date==2){
	$hidden_begin = $obj->getParameter("hidden_begin");
	$hidden_end = $obj->getParameter("hidden_end");
}else{
	$hidden_begin = $dateobj->convertdate($begin,$sdateformat,"Ymd");
	$hidden_end = $dateobj->convertdate($end,$sdateformat,"Ymd");
}
if(!isset($_SESSION["__user_id"])){$_SESSION["__user_id"]="";}
$ubranch_id = $obj->getIdToText($_SESSION["__user_id"],"s_user","branch_id","u_id");
$ubranch_name = strtolower($obj->getIdToText($ubranch_id,"bl_branchinfo","branch_name","branch_id"));
//if($ubranch_name!="all"){
if($ubranch_name!="all"){
	$ucity_id = $obj->getIdToText($ubranch_id,"bl_branchinfo","city_id","branch_id");
}
if(!$branch && $ubranch_name!="all"){
	$branch = $ubranch_id;
}

///Daily Receipt///////////////////////////////////////
$closing_id =  $obj->getParameter("closing_id",false);
$cash_row = $obj->getParameter("cash_row",1);
$coin_id  = $obj->getParameter("coin_id");
$cash_qty  = $obj->getParameter("cash_qty");
$cash_value  = $obj->getParameter("cash_value");

for($i=0;$i<$cash_row;$i++){
	if(!isset($coin_id[$i])){$coin_id[$i]="";}
	if(!isset($cash_qty[$i])){$cash_qty[$i]="";}
	if(!isset($cash_value[$i])){$cash_value[$i]="";}
	
	$cash_qty[$i] = str_replace(",","",$cash_qty[$i]);
	$cash_value[$i] = str_replace(",","",$cash_value[$i]);
	
	$data["coin_id[$i]"] = $coin_id[$i];
	$data["cash_qty[$i]"] = $cash_qty[$i];
	$data["cash_value[$i]"] = $cash_value[$i];
}
$cash_data = http_build_query($data, '$data[]');

$total_sumcash = $obj->getParameter("total_sumcash");
$total_sumcash = str_replace(",","",$total_sumcash);

$currency_row = $obj->getParameter("currency_row",1);
$currency = $obj->getParameter("currency");
$rate = $obj->getParameter("rate");
$quantity_cur = $obj->getParameter("quantity_cur");
$cur_value = $obj->getParameter("cur_value");

for($i=0;$i<$currency_row;$i++){
	if(!isset($currency[$i])){$currency[$i]="";}
	if(!isset($rate[$i])){$rate[$i]="";}
	if(!isset($quantity_cur[$i])){$quantity_cur[$i]="";}
	if(!isset($cur_value[$i])){$cur_value[$i]="";}
	
	$rate[$i] = str_replace(",","",$rate[$i]);
	$quantity_cur[$i] = str_replace(",","",$quantity_cur[$i]);
	$cur_value[$i] = str_replace(",","",$cur_value[$i]);
	
	$data["currency[$i]"] = $currency[$i];
	$data["rate[$i]"] = $rate[$i];
	$data["quantity_cur[$i]"] = $quantity_cur[$i];
	$data["cur_value[$i]"] = $cur_value[$i];
}

$cur_data = http_build_query($data, '$data[]');

$total_cur = $obj->getParameter("total_cur");
$total_cur = str_replace(",","",$total_cur);

$total_cash = $obj->getParameter("total_cash");
$total_cash = str_replace(",","",$total_cash);
$start_money = $obj->getParameter("start_money");
$start_money = str_replace(",","",$start_money);
$tranfer_pc = $obj->getParameter("tranfer_pc");
$tranfer_pc = str_replace(",","",$tranfer_pc);
$total_cash_charges = $obj->getParameter("total_cash_charges");
$total_cash_charges = str_replace(",","",$total_cash_charges);
$out_of_balance = $obj->getParameter("out_of_balance");
$out_of_balance = str_replace(",","",$out_of_balance);
$total_deposit = $obj->getParameter("total_deposit");
$total_deposit = str_replace(",","",$total_deposit);

$giftinfo = $obj->getParameter("giftinfo");
$giftinfo = str_replace("\r\n","<br>",$giftinfo);
$commentinfo = $obj->getParameter("commentinfo");
$commentinfo = str_replace("\r\n","<br>",$commentinfo);
$signature =  $obj->getParameter("signature");
$signature = str_replace("\r\n","<br>",$signature);

$datecheck = $obj->getParameter("datecheck");

$status=$obj->getParameter("status",false);
$save=$obj->getParameter("save",false);
//////////////////////////////////////////////////////

$querystr = "pageid=$pageid&begin=$hidden_begin&end=$hidden_end&branchid=$branch&payid=$payid" .
		"&closing_id=$closing_id&cash_row=$cash_row&$cash_data&total_sumcash=$total_sumcash" .
		"&currency_row=$currency_row&$cur_data&total_cur=$total_cur" .
		"&total_cash=$total_cash&start_money=$start_money&tranfer_pc=$tranfer_pc" .
		"&total_cash_charges=$total_cash_charges&out_of_balance=$out_of_balance" .
		"&total_deposit=$total_deposit" .
		"&giftinfo=$giftinfo&commentinfo=$commentinfo&signature=$signature&status=$status&save=$save";
$print = "report.php?$querystr&export=print";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=(isset($pageinfo["pagename"]))?$pageinfo["pagename"]:""?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<script type="text/javascript" src="/scripts/date-functions.js"></script>
<script type="text/javascript" src="/scripts/datechooser.js"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">

<!--[if IE]>
<style>
td.rheader span.date select.ctrDropDown{
    width:115px;
    font-size:12px;
}
td.rheader span.date select.ctrDropDownClick{
    font-size:12px;

    width:auto;

}
td.rheader span.date select.plainDropDown{
    width:115px;
    font-size:12px;
}
</style>
<![endif]-->
<script type="text/javascript">
<!--
	function RefreshPage(){
		document.getElementById('status').value=0;	
		document.crs.submit();
	}
	
	function addRow(name){
		TotalValue(name);
		document.getElementById(name).value++;
		document.getElementById('status').value=1;
		document.crs.submit();
	}
	function delRow(name){
		if(document.getElementById(name).value>1){
			document.getElementById(name).value--;
		}
		TotalValue(name);
		document.getElementById('status').value=1;
		document.crs.submit();
	}
	function SumCash(name,i){
		var coin_value=document.getElementById('coin_value['+i+']').value;
		var cash_qty=document.getElementById('cash_qty['+i+']').value;
		coin_value = coin_value.replace(/\,/g,'');
		cash_qty = cash_qty.replace(/\,/g,'');
		document.getElementById('cash_value['+i+']').value=coin_value*cash_qty;
		TotalValue(name);
	}
	function SumCurrency(name,i){	
		var rate=document.getElementById('rate['+i+']').value;
		var quantity_cur=document.getElementById('quantity_cur['+i+']').value;
		rate = rate.replace(/\,/g,'');
		quantity_cur = quantity_cur.replace(/\,/g,'');
		document.getElementById('cur_value['+i+']').value=rate*quantity_cur;
		TotalValue(name);
	}
	function TotalValue(name){
		 var cnt = document.getElementById(name).value;
		 var total = parseFloat(0.00);
		 var value = 0;
		 for(var i=0;i<cnt;i++){
		 	if(name=='cash_row'){
		 		if(document.getElementById('cash_value['+i+']').value==""){
		 			document.getElementById('cash_value['+i+']').value=0;
		 			value=0;
		 		}else{
		 			value=document.getElementById('cash_value['+i+']').value;
		 			value=value.replace(/\,/g,'');
		 		}
		 		total=total+parseFloat(value);
		 		document.getElementById('total_sumcash').value=total;
		 	}else if(name=='currency_row'){
		 		if(document.getElementById('cur_value['+i+']').value==""){
		 			document.getElementById('cur_value['+i+']').value=0;
		 			value=0;
		 		}else{
		 			value=document.getElementById('cur_value['+i+']').value;
		 			value=value.replace(/\,/g,'');
		 		}
		 		total=total+parseFloat(value);
		 		document.getElementById('total_cur').value=total;
		 	}
		 }
	TotalCash();
	}
	function TotalCash(){
		var cash = document.getElementById('total_sumcash').value;
		var cash_cur = document.getElementById('total_cur').value;
		if(cash==""){cash=0;}else{
			cash=cash.replace(/\,/g,'');
		}
		if(cash_cur==""){cash_cur=0;}else{
			cash_cur=cash_cur.replace(/\,/g,'');	
		}
		document.getElementById('total_cash').value=parseFloat(cash)+parseFloat(cash_cur);
	CashDeposit();
	}
	function CashDeposit(){
		var total_cash = document.getElementById('total_cash').value;
		var start_money = document.getElementById('start_money').value;
		var tranfer_pc = document.getElementById('tranfer_pc').value;
		if(total_cash==""){total_cash=0;}else{
			total_cash=total_cash.replace(/\,/g,'');	
		}
		if(start_money==""){start_money=0;}else{
			start_money=start_money.replace(/\,/g,'');	
		}
		if(tranfer_pc==""){tranfer_pc=0;}else{
			tranfer_pc=tranfer_pc.replace(/\,/g,'');	
		}
		document.getElementById('total_deposit').value=parseFloat(total_cash)-parseFloat(start_money)-parseFloat(tranfer_pc);
	TotalCashCharges()
	}

	function TotalCashCharges(){
		var total_deposit = document.getElementById('total_deposit').value;
		var sum_pay = document.getElementById('sum_pay').value;
		var tranfer_pc = document.getElementById('tranfer_pc').value;
		if(total_deposit==""){total_deposit=0;}else{
			total_deposit=total_deposit.replace(/\,/g,'');
		}
		if(sum_pay==""){sum_pay=0;}else{
			sum_pay=sum_pay.replace(/\,/g,'');	
		}
		if(tranfer_pc==""){tranfer_pc=0;}else{
			tranfer_pc=tranfer_pc.replace(/\,/g,'');	
		}
		document.getElementById('total_cash_charges').value=parseFloat(total_deposit)+parseFloat(sum_pay)+parseFloat(tranfer_pc);
	OutofBalance();
	}
	function OutofBalance(){
		var total_cash_charges = document.getElementById('total_cash_charges').value;
		var total_sale = document.getElementById('total_sale').value;
		if(total_cash_charges==""){total_cash_charges=0;}else{
			total_cash_charges=total_cash_charges.replace(/\,/g,'');
		}
		if(total_sale==""){total_sale=0;}else{
			total_sale=total_sale.replace(/\,/g,'');	
		}
		document.getElementById('out_of_balance').value=Math.round((parseFloat(total_cash_charges)-parseFloat(total_sale))*100)/100;
	}
	function blockNonNumbers(obj, e, allowDecimal, allowNegative)
{
	var key;
	var isCtrl = false;
	var keychar;
	var reg;
		
	if(window.event) {
		key = e.keyCode;
		isCtrl = window.event.ctrlKey
	}
	else if(e.which) {
		key = e.which;
		isCtrl = e.ctrlKey;
	}
	
	if (isNaN(key)) return true;
	
	keychar = String.fromCharCode(key);
	
	// check for backspace or delete, or if Ctrl was pressed
	if (key == 8 || isCtrl)
	{
		return true;
	}

	reg = /\d/;
	var isFirstN = allowNegative ? keychar == '-' && obj.value.indexOf('-') == -1 : false;
	var isFirstD = allowDecimal ? keychar == '.' && obj.value.indexOf('.') == -1 : false;
	
	if(isFirstN==false && isFirstD==false && reg.test(keychar)==false){
		alert('Please Input Only Number');
	}
	return isFirstN || isFirstD || reg.test(keychar);
}
	
-->
</script>
</head>
<body onLoad="getReturnText('report.php','<?=$querystr?>','tableDisplay');">
<div id="loading">
<table cellspacing="0" cellpadding="0" class="preloading">
<tr>
    <td align="center" valign="middle">
		<img src="/images/sms preload.png">
	</td>
</tr>
</table>
</div> 
<form name="crs" id="crs" action="" method="post" style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td width="8" height="100%" align="center" rowspan="4" class="hidden_bar" >&nbsp;</td>
  <tr>
    <!--<td height="<? if($ubranch_id<=1){ ?>99px<?}else{?>49px<?}?>" valign="top">-->
	<td height="99px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" align="center" height="49">
				<?include("$root/rmenuheader.php");?>
	 	</td>
	  </tr>

	  <tr>
	    <td valign="top" align="center" height="10">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
			        <td class="rheader" style="padding-left: 20px; background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			        <?if($ubranch_name=="all"){?>
					Dates:<input type="hidden" id="pageid" name="pageid" value="<?=$pageid?>"/>
			        <span class ="date" style="width: 115px;font-family:Tahoma; font-size: 12px;overflow:hidden;">
					<select id="date" name="date" class="ctrDropDown" onBlur="this.className='ctrDropDown';" onMouseDown="this.className='ctrDropDownClick';" onChange="this.className='ctrDropDown';">
						  <option title="All" value="1" <?=($date=="1")?"selected":""?>>All</option>
			              <option title="Custom" value="2" <?=($date=="2")?"selected":""?>>Custom</option>
			              <option title="Last Fiscal Quarter" value="3" <?=($date=="3")?"selected":""?>>Last Fiscal Quarter</option>
			              <option title="Last Fiscal Quarter to date" value="4" <?=($date=="4")?"selected":""?>>Last Fiscal Quarter to date</option>
			              <option title="Last Fiscal Year" value="5" <?=($date=="5")?"selected":""?>>Last Fiscal Year</option>
			              <option title="Last Fiscal Year to date" value="6" <?=($date=="6")?"selected":""?>>Last Fiscal Year to date</option>
			              <option title="Last Month" value="7" <?=($date=="7")?"selected":""?>>Last Month</option>
			              <option title="Last Month to date" value="8" <?=($date=="8")?"selected":""?>>Last Month to date</option>
			              <option title="Last Week" value="9" <?=($date=="9")?"selected":""?>>Last Week</option>
			              <option title="Last Week to date" value="10" <?=($date=="10")?"selected":""?>>Last Week to date</option>
			              <option title="This Fiscal Quarter" value="11" <?=($date=="11")?"selected":""?>>This Fiscal Quarter</option>
			              <option title="This Fiscal Quarter to date" value="12" <?=($date=="12")?"selected":""?>>This Fiscal Quarter to date</option>
			              <option title="This Fiscal Year" value="13" <?=($date=="13")?"selected":""?>>This Fiscal Year</option>
			              <option title="This Fiscal Year to date" value="14" <?=($date=="14")?"selected":""?>>This Fiscal Year to date</option>
			              <option title="This Month" value="15" <?=($date=="15")?"selected":""?>>This Month</option>
			              <option title="This Month to date" value="16" <?=($date=="16")?"selected":""?>>This Month to date</option>
			              <option title="Today" value="17" <?=($date=="17")?"selected":""?>>Today</option>
			              <option title="Yesterday" value="18" <?=($date=="18")?"selected":""?>>Yesterday</option>
					</select>
				</span>
			        </td>
			        <td class="rheader" style="padding-top: 3px; background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			        &nbsp;From: <input id='begin' name='begin' value="<?=$begin?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			        <input type="hidden" id="hidden_begin" name="hidden_begin" value="<?=$hidden_begin?>"/>
			        <img align="top" src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'begin', 'date_begin', 1900, 2100, '<?=$sdateformat?>', false,false);" />
			        <div id="date_begin" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"> </div>
			        &nbsp;To: <input id='end' name='end' value="<?=$end?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			        <input type="hidden" id="hidden_end" name="hidden_end" value="<?=$hidden_end?>"/>
			        <img align="top" src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'end', 'date_end', 1900, 2100, '<?=$sdateformat?>', false,false);" />
			        <div id="date_end" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div>
			        <?}else{?>
						<input type="button" name="Refresh" id="Refresh" value="Refresh" onClick="RefreshPage();"/>
					<?}?>
					</td>
			        <?if($hidden_begin!=$hidden_end){?>
			        <td class="rheader" height="30" align="right" style="padding-right: 20px; background-image: url('/images/<?=$theme?>/appt/menubg.png');">
				        Method of Payment:
						<?=$obj->makeListbox("pay_id","all_l_paytype","pay_name","pay_id",$payid,0,"pay_name","pay_active","1","")?>
					</td>
					<?}?>
			        <td class="rheader" height="30" align="right" style="padding-right: 20px; background-image: url('/images/<?=$theme?>/appt/menubg.png');">
				        Branch:
						<?if($ubranch_name!="all"){?>
						<?=$obj->makeListbox("branchid","bl_branchinfo","branch_name","branch_id",$branch,0,"branch_name","branch_active","1","branch_name!='All' and city_id=$ucity_id ")?>
						<?}else{?>
						<?=$obj->makeListbox("branchid","bl_branchinfo","branch_name","branch_id",$branch,0,"branch_name","branch_active","1","branch_name!='All'")?>
						<?}?>
					</td>
		  		</tr>
		    	<tr bgcolor="#999999">
		        	<td height="1" colspan="4" bgcolor="<?=$fontcolor?>"><img src="../../images/blank.gif" width="1" height="1" /></td>
		    	</tr>
    		</table>  
    	</td>
  	</tr>
 	<tr style="<?=($ubranch_name=="all")?"":"display:none;"?>">
    	<td valign="top" height="20px">
    		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td height="30" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');"> 
			         <?if($hidden_begin!=$hidden_end){?>
			        Export:&nbsp;&nbsp;
			          <select id="export" name="export">
			            <option title="PDF" value="PDF">PDF</option>
			            <option title="Excel" value="Excel">Excel</option>
			          </select>          
			          &nbsp;&nbsp;<input type="button" name="Export" id="Export" value="Export" onClick="window.open('report.php?begin=<?=$hidden_begin?>&end=<?=$hidden_end?>&branchid=<?=$branch?>&export='+document.getElementById('export').value)"/>
			          &nbsp;&nbsp;<input type="submit" name="Refresh" id="Refresh" value="Refresh"/>
			          <?}?>
			          <?if($hidden_begin==$hidden_end){?>
			          &nbsp;&nbsp;<input type="button" name="Refresh" id="Refresh" value="Refresh" onClick="RefreshPage();"/>
			          <!--
			          &nbsp;&nbsp;<input type="button" name="Daily Print" id="Daily Print" value="Daily Print"  onClick="window.open('rdetail.php?date=<?=$hidden_begin?>&branch='+(document.getElementById('branchid').value)+'', 'DailyRecipt','location=1,top=0,left=100,scrollbars=1,menubar=1, width=800,height=800')"/>
			          -->
			          <?}?>
			          </td>
			        </tr>
			      <tr bgcolor="#999999">
			        <td height="1" bgcolor="<?=$fontcolor?>"><img src="../../images/blank.gif" width="1" height="1" /></td>
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
			<div id="tableDisplay"></div>
		</td>
  </tr>
</table> 
</form>
 


	<div class="hiddenbar"><img id="spLine" src="../../images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('../../images')"/></div>
</body>
</html>