<?
$_COOKIE["topicdir"] = '<a href="javascript:;" onclick="gotoURL(\'../../index.php\')" class="top_menu_link">Report </a> > ' .
		'<a href="javascript:;" onclick="gotoURL(\'../index.php\')" class="top_menu_link">Customer Information </a> > ' .
		'Number Of Customer > ';
$_COOKIE["topic"] = 'Number Of Customer';
$_COOKIE["back"] = '../index.php';
include("../../../include.php");
require_once("report.inc.php");
require_once("date.inc.php");
$obj = new report();
$dateobj = new convertdate();
$obj->setDebugStatus(false);
// system date format	 					
$chksql = "select long_date,short_date from a_company_info";
$chkrs =$obj->getResult($chksql);
$sdateformat = $obj->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
if($obj->getParameter("date")){
	$date = $obj->getParameter("date");
	$begin = $obj->getBegin($date,$sdateformat);
	$end = $obj->getEnd($date,$sdateformat);
}else{
	$date = 16;
	$begin = $obj->getParameter("begin"); $end = $obj->getParameter("end");
}
if(!$obj->getParameter("begin")&&!$obj->getParameter("end")){
	$begin = $obj->getBegin($date,$sdateformat);
	$end = $obj->getEnd($date,$sdateformat);
}
if($obj->getParameter("date")==2){
	$hidden_begin = $obj->getParameter("hidden_begin");
	$hidden_end = $obj->getParameter("hidden_end");
}else{
	$hidden_begin = $dateobj->convertdate($begin,$sdateformat,"Ymd");
	$hidden_end = $dateobj->convertdate($end,$sdateformat,"Ymd");
}
$column = $obj->getParameter("column","Total only");
$order = $obj->getParameter("order","Default");
$collapse = $obj->getParameter("chkCollapse","Expand");
if($order=="Total"){
	$collapse = "Expand";
}
$sortby = $obj->getParameter("chksortby","Z &gt A");
$querystr="'begin=$hidden_begin&end=$hidden_end&date=$date&column=$column&order=$order&Collapse=$collapse&sortby='+document.getElementById('sort').value";
$_COOKIE["print"] = "report.php?begin=$hidden_begin&end=$hidden_end&date=$date&column=$column&order=$order&Collapse=$collapse&sortby=$sortby&export=print";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Number Of Customer Reports</title>
<link href="../../../css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../scripts/checkAccess.js"></script>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<script type="text/javascript" src="../scripts/component.js"></script>
<script src="../scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="../scripts/datechooser/datechooser.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../scripts/datechooser/datechooser.css">
</head>
<body onLoad="getReturnText('report.php',<?=$querystr?>,'tableDisplay');">
<div id="loading" style="position:absolute;left:0px; top:0px; background-color: #FFFFFF;  width:100%; height:100%; text-align:center; filter: alpha(opacity=80);opacity:0.70;">
<table cellspacing="0" cellpadding="0" style="position:absolute;left:40%;top: 40%;">
<tr>
    <td align="center" rowspan="4" style="border:4px solid #4588bf;padding: 5 5 5 5;">
Loading.. <br/><img src="../../../images/pre-loader.gif" border="0">
	</td>
</tr>
</table>
</div> 
<form name="crs" action='' method='post' style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" height="100%" width="100%">
  <tr>
    <td width="6px" height="100%" align="center" rowspan="4" class="hidden_bar"><img src="../../../images/blank.gif" width="6px" height="1" /></td>
    <td valign="top" height="40" style=""><? include "mainhead.php";?>
    </td>
  </tr>
  <tr>
    <td height="30px" valign="top" style="">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="custinfo">
      <tr>
        <td bgcolor="#F1F3F5" class="header">
        Dates:&nbsp;&nbsp;
        <select id="date" name="date" onchange="this.form.submit()">
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
        &nbsp;&nbsp;From:&nbsp;&nbsp;<input id='begin' name='begin' value="<?=$begin?>" readonly="1" class="textbox" type="text" style="width:85px"/>
        <input type="hidden" id="hidden_begin" name="hidden_begin" value="<?=$hidden_begin?>"/>
        &nbsp;&nbsp;<img src="../scripts/datechooser/calendar.gif" alt="Date Appointment" onClick="showChooser(this, 'begin', 'date_begin', 1900, 2100, '<?=$sdateformat?>', false,false);" />
        <div id="date_begin" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"> </div>
        &nbsp;&nbsp;To:&nbsp;&nbsp;<input id='end' name='end' value="<?=$end?>" readonly="1" class="textbox" type="text" style="width:85px"/>
        <input type="hidden" id="hidden_end" name="hidden_end" value="<?=$hidden_end?>"/>
        &nbsp;&nbsp;<img src="../scripts/datechooser/calendar.gif" alt="Date Appointment" onClick="showChooser(this, 'end', 'date_end', 1900, 2100, '<?=$sdateformat?>', false,false);" />
        <div id="date_end" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div>
        </td>
        <td bgcolor="#F1F3F5" height="30">
        Columns:&nbsp;&nbsp;
        <select id="column" name="column" onchange="this.form.submit()">
          <option title="Total only" value="Total only" <?=($column=="Total only")?"selected":""?>>Total only</option>
          <option title="Day" value="Day" <?=($column=="Day")?"selected":""?>>Day</option>
          <option title="Week" value="Week" <?=($column=="Week")?"selected":""?>>Week</option>
          <option title="Half month" value="Half month" <?=($column=="Half month")?"selected":""?>>Half month</option>
          <option title="Month" value="Month" <?=($column=="Month")?"selected":""?>>Month</option>
          <option title="Quarter" value="Quarter" <?=($column=="Quarter")?"selected":""?>>Quarter</option>
          <option title="Year" value="Year" <?=($column=="Year")?"selected":""?>>Year</option>
        </select>
		&nbsp;&nbsp;Sort by:&nbsp;&nbsp;
        <select id="order" name="order" onchange="this.form.submit()">
          <option title="Category" value="Category" <?=($order=="Category")?"selected":""?>>Category</option>
          <option title="Default" value="Default" <?=($order=="Default")?"selected":""?>>Default</option>
          <option title="Total" value="Total" <?=($order=="Total")?"selected":""?>>Total</option>
         </select>
          &nbsp;&nbsp;<input type="submit" name="sort" id="sort" value="<?=$sortby?>" onClick="changesbValue(this)"/>
          <input type="hidden" name="chksortby" id="chksortby" value="<?=$sortby?>"/>
        </td>
      </tr>
      <tr bgcolor="#999999">
        <td height="1" colspan="2" bgcolor="#CCCCCC"><img src="../../../images/blank.gif" width="1" height="1" /></td>
      </tr>
    </table>  
    </td>
  </tr>
  <tr>
    <td valign="top" height="20px" style=""><table width="100%" border="0" cellspacing="0" cellpadding="0" class="custinfo">
      <tr>
        <td bgcolor="#F1F3F5" class="header2"> Export:&nbsp;&nbsp;
          <select id="export" name="export">
            <option title="PDF" value="PDF">PDF</option>
            <option title="Excel" value="Excel">Excel</option>
          </select>          
          &nbsp;&nbsp;<input type="button" name="Export" id="Export" value="Export" onClick="window.open('report.php?begin=<?=$hidden_begin?>&end=<?=$hidden_end?>&date=<?=$date?>&column=<?=$column?>&order=<?=$order?>&Collapse=<?=$collapse?>&sortby=<?=$sortby?>&export='+document.getElementById('export').value)"/>
          &nbsp;&nbsp;<input type="submit" name="Collapse" id="Collapse" value="<?=$collapse?>" onClick="changeValue(this)"/>
          <input type="hidden" name="chkCollapse" id="chkCollapse" value="<?=$collapse?>"/>
          &nbsp;&nbsp;<input type="submit" name="Refresh" id="Refresh" value="Refresh" />
          </td>
        </tr>
      <tr bgcolor="#999999">
        <td height="1" bgcolor="#CCCCCC"><img src="../../../images/blank.gif" width="1" height="1" /></td>
      </tr>
    </table>    </td>
  </tr>
  <tr>
  <td align="center" valign="top"><table class="main" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top" align="center">
<div id="tableDisplay"></div>
</td>
</tr>
</table>
  </td>
  </tr>
</table>
	<div class="hiddenbar"><img id="spLine" src="../../../images/bar_close.gif" alt="" width="6px" height="60px" onclick="hiddenLeftFrame('../../../images')"/></div>
</form>  
</body>
</html>