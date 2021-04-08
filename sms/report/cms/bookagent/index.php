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
	$date = 16;
	$begin = $obj->getParameter("begin",$obj->getBegin($date,$sdateformat)); 
	$end = $obj->getParameter("end",$obj->getEnd($date,$sdateformat));
}
$branch = $obj->getParameter("branchid",false);
$city = $obj->getParameter("cityid",false);
if($city){
	$branch=0;	
}

if($date==2){
	$hidden_begin = $obj->getParameter("hidden_begin");
	$hidden_end = $obj->getParameter("hidden_end");
}else{
	$hidden_begin = $dateobj->convertdate($begin,$sdateformat,"Ymd");
	$hidden_end = $dateobj->convertdate($end,$sdateformat,"Ymd");
}
$category = $obj->getParameter("category","0");
$collapse = $obj->getParameter("chkCollapse","Expand");
$cmschk = $obj->getParameter("commission",false);
$search = $obj->getParameter("search",false);
$order = $obj->getParameter("order","Alphabet");
$sortby = $obj->getParameter("chksortby","Z &gt A");

$querystr = "pageid=$pageid&begin=$hidden_begin&end=$hidden_end&branchid=$branch&cityid=$city&Collapse=$collapse&commission=$cmschk&category=$category&order=$order&sortby=$sortby&search='+document.getElementById('search').value.replace('+','%2B')+'";
$print = "report.php?$querystr&export=print";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<script src="/scripts/date-functions.js" type="text/javascript"></script>
<script src="/scripts/datechooser.js" type="text/javascript"></script>
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

</head>
<body onLoad="getReturnText('report.php','<?=$querystr?>','tableDisplay');">
<div id="loading">
<table cellspacing="0" cellpadding="0" class="preloading" >
<tr>
    <td align="center" valign="middle">
		<img src="/images/sms preload.png">
	</td>
</tr>
</table>
</div> 
<form name="bookagent" id="bookagent" action='' method='post' style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td width="8" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
    <td height="99px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" colspan="3" align="center" height="49">
				<?include("$root/rmenuheader.php");?>
	 	</td>
	  </tr>
 	<tr>
    	<td valign="top" height="30px" colspan="2" class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			<table border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td class="rheader" height="30" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			          Dates:&nbsp;&nbsp;
			        <span class="date" style="width: 115px;font-family:Tahoma; font-size: 12px;overflow:hidden;">
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
       		 		<td class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			        &nbsp;&nbsp;From: <input id='begin' name='begin' value="<?=$begin?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			        <input type="hidden" id="hidden_begin" name="hidden_begin" value="<?=$hidden_begin?>"/>
			        <img align="top" src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'begin', 'date_begin', 1900, 2100, '<?=$sdateformat?>', false,false);" />
			        <div id="date_begin" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"> </div>
			        &nbsp;&nbsp;To: <input id='end' name='end' value="<?=$end?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			        <input type="hidden" id="hidden_end" name="hidden_end" value="<?=$hidden_end?>"/>
			        <img align="top" src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'end', 'date_end', 1900, 2100, '<?=$sdateformat?>', false,false);" />
			        <div id="date_end" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div>
        			</td>
			        <td class="rheader" height="30" align="right" style="padding-left: 20px; background-image: url('/images/<?=$theme?>/appt/menubg.png');">
				        Branch:&nbsp;&nbsp;
						<?=$obj->makeListbox("branchid","all_bl_branchinfo","branch_name","branch_id",$branch,0,"branch_name","branch_active","1","branch_name!='All'")?>
						&nbsp;&nbsp;City:&nbsp;&nbsp;
						<?=$obj->makeListbox("cityid","all_al_city","city_name","city_id",$city,0,"city_name")?>
					</td>
		  		</tr>
    		</table>  
    	</td>
  	</tr>
	<tr bgcolor="#999999">
		  <td height="1" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
	</tr>
	
	<?if($collapse!="Collapse"){?>	
	<tr>
    	<td valign="top" height="30px" colspan="2" class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			<table border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td class="rheader" height="30" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');">
						
						Sort by:&nbsp;&nbsp;
				        <select id="order" name="order">
				          <option title="Alphabet" value="Alphabet" <?=($order=="Alphabet")?"selected":""?>>Alphabet</option>
				          <option title="Total Bookings" value="Total Bookings" <?=($order=="Total Bookings")?"selected":""?>>Total Bookings</option>
				          <option title="Total Customers" value="Total Customers" <?=($order=="Total Customers")?"selected":""?>>Total Customers</option>
				          <option title="Total Sales" value="Total Sales" <?=($order=="Total Sales")?"selected":""?>>Total Sales</option>
				          <option title="Avg Total Sales Per Cust" value="Avg Total Sales Per Cust" <?=($order=="Avg Total Sales Per Cust")?"selected":""?>>Avg Total Sales Per Cust</option>
				         </select>
				         <input type="submit" name="sort" id="sort" value="<?=$sortby?>" onClick="changesbValue(this)"/>
				         <input type="hidden" name="chksortby" id="chksortby" value="<?=$sortby?>"/>
					
						&nbsp;&nbsp;Category:&nbsp;&nbsp;
						<select id="category" name="category">
						 <option title="Default" value="Default" <?=($category=="Default")?"selected":""?>>Default</option>
						  <option title="Category" value="Category" <?=($category=="Category")?"selected":""?>>Category</option>
						</select>
					</td>
		  		</tr>
    		</table>  
    	</td>
  	</tr>
	<tr bgcolor="#999999">
		        	<td height="1" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
	</tr>
<?}?>
	
 	<tr>
    	<td valign="top" height="20px">
    		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td height="30" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');"> Export:&nbsp;&nbsp;
			          <select id="export" name="export">
			            <option title="PDF" value="PDF">PDF</option>
			            <option title="Excel" value="Excel">Excel</option>
			          </select>          
			          &nbsp;&nbsp;<input type="button" name="Export" id="Export" value="Export" onClick="window.open('report.php?begin=<?=$hidden_begin?>&end=<?=$hidden_end?>&branchid=<?=$branch?>&cityid=<?=$city?>&commission=<?=$cmschk?>&Collapse=<?=$collapse?>&category=<?=$category?>&order=<?=$order?>&sortby=<?=$sortby?>&export='+document.getElementById('export').value)"/>
			          &nbsp;&nbsp;<input type="submit" name="Collapse" id="Collapse" value="<?=$collapse?>" onClick="changeValue(this)"/>
			          <input type="hidden" name="chkCollapse" id="chkCollapse" value="<?=$collapse?>"/>
			          &nbsp;&nbsp;<input type="submit" name="Refresh" id="Refresh" value="Refresh" />
			          &nbsp;&nbsp;<input type="checkbox" name="commission" id="commission" value="checked" <?=$cmschk?>/> 
			          &nbsp;&nbsp;Have Commission
			          </td>
			        
			        <td height="30" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');"> 
				       <input type="text" name="search" id="search" <?=($search)?"value='$search'":""?> title="Search Commissions by phone."/>
				        
				        <!--&nbsp;&nbsp;<a href="javascript:;" onClick="getReturnText('report.php','begin=<?=$hidden_begin?>&end=<?=$hidden_end?>&branchid=<?=$branch?>&commission=<?=$cmschk?>&Collapse=<?=$collapse?>&search='+document.getElementById('search').value.replace('+','%2B'),'tableDisplay');" class="top_menu_link" title="Search by booking person,phone and customer name only.">
				        <img src="/images/btn_search_bg.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; 
				        <a href="javascript:;" onClick="getReturnText('report.php','begin=<?=$hidden_begin?>&end=<?=$hidden_end?>&branchid=<?=$branch?>&commission=<?=$cmschk?>&Collapse=<?=$collapse?>&search='+document.getElementById('search').value.replace('+','%2B'),'tableDisplay');" class="top_menu_link" title="Search by booking person,phone and customer name only.">Search</a> &nbsp;-->
				        
				        &nbsp;&nbsp;<a href="javascript:;" onClick="document.bookagent.submit();" class="top_menu_link" title="Search by booking person,phone and customer name only.">
				        <img src="/images/btn_search_bg.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; 
				        <a href="javascript:;" onClick="document.bookagent.submit();">Search</a> &nbsp;
				        
				        <a href="javascript:;" onClick="document.getElementById('search').value='';getReturnText('report.php','begin=<?=$hidden_begin?>&end=<?=$hidden_end?>&branchid=<?=$branch?>&cityid=<?=$city?>&commission=<?=$cmschk?>&Collapse=<?=$collapse?>&category=<?=$category?>&order=<?=$order?>&sortby=<?=$sortby?>&search=','tableDisplay')" class="top_menu_link" title="View all commission dispersed.">
				        <img src="/images/btn_show-all_bg.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; 
				        <a href="javascript:;" onClick="document.getElementById('search').value='';getReturnText('report.php','begin=<?=$hidden_begin?>&end=<?=$hidden_end?>&branchid=<?=$branch?>&cityid=<?=$city?>&commission=<?=$cmschk?>&Collapse=<?=$collapse?>&category=<?=$category?>&order=<?=$order?>&sortby=<?=$sortby?>&search=','tableDisplay')" class="top_menu_link" title="View all commission dispersed.">View All</a>
				        &nbsp;&nbsp; 
					  </td>
					  
			        </tr>
			      <tr bgcolor="#999999">
			        <td height="1" colspan="2" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
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
	<div class="hiddenbar"><img id="spLine" src="../../images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('/images')"/></div>
</body>
</html>