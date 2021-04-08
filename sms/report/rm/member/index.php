<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
$obj = new report();
$obj->setDebugStatus(false);

$search = $obj->getParameter("search",false);
$searchstr = str_replace("&","%26",$search);
$searchstr = str_replace("+","%2B",$searchstr);
$showinactive=$obj->getParameter("showinactive",0);
$categoryid = $obj->getParameter("categoryid",0,1);
$order = $obj->getParameter("order","member_code");
$chkarr = array("birthdate","sex_id","nationality_id","joindate","fill_date","expireddate","ytd","ltd");
if(!in_array($order,$chkarr)){$order="member_code";}
$sortby = $obj->getParameter("chksortby","Z &gt A");

//$chkarr = array("Z &gt A","A &gt Z");echo $sortby;
//if(!in_array($sortby,$chkarr)){$sortby="Z &gt A";}
$querystr = "pageid=$pageid&categoryid=$categoryid&search=$searchstr&showinactive=$showinactive&order=$order&sortby=$sortby";
$print = "report.php?$querystr&export=print";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=(isset($pageinfo["pagename"]))?$pageinfo["pagename"]:""?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">

<!--[if IE]>
<style>
td.rheader span.st select.ctrDropDown{
    width:115px;
    font-size:12px;
}
td.rheader span.st select.ctrDropDownClick{
    font-size:12px;

    width:auto;

}
td.rheader span.st select.plainDropDown{
    width:115px;
    font-size:12px;
}
</style>
<![endif]-->


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
<form name="membership" id="membership" action='index.php' method='post' style="padding:0;margin:0">
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
			            Category:&nbsp;&nbsp;
						<?=$obj->makeListbox("categoryid","all_mb_category","category_name","category_id",$categoryid,0,"category_name","category_active","1",0,0,0,"")//"searchCust('','',this.options[this.selectedIndex].value);")
						?>
						&nbsp;&nbsp;<input type="text" name="search" id="search" <?=($search)?"value='".$search."'":""?> title="Search all customer information."/> 
					  	&nbsp;&nbsp;<a href="javascript:;" onClick="searchCust('','','<?=$categoryid?>');" class="top_menu_link" title="Search all customer information.">
				        <img src="/images/btn_search_bg.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; 
				        <a href="javascript:;" onClick="searchCust('','','<?=$categoryid?>');" class="top_menu_link" title="Search all customer information.">Search</a> &nbsp;
				        <a href="javascript:;" onClick="document.getElementById('search').value='';searchCust('','','0');" class="top_menu_link" title="View all customer information.">
				        <img src="/images/btn_show-all_bg.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; 
				        <a href="javascript:;" onClick="document.getElementById('search').value='';searchCust('','','0');" class="top_menu_link" title="View all customer information.">View All</a>
				        &nbsp;&nbsp;
					</td><td class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');">
        				&nbsp;&nbsp; 
        				<input id='showinactive' type='checkbox' name='showinactive' value='1' <? echo ($showinactive)?"checked":""?> /> Show Inactive
						&nbsp;&nbsp;Sort by:&nbsp;&nbsp;
				        <span class="st" style="width: 115px;font-family:Tahoma; font-size: 12px;overflow:hidden;">
					    <select id="order" name="order" class="ctrDropDown" onBlur="this.className='ctrDropDown';" onMouseDown="this.className='ctrDropDownClick';" onChange="this.className='ctrDropDown';">	    
				          <option title="Member Code" value="member_code" <?=($order=="member_code")?"selected":""?>>Member Code</option>
				          <option title="Birth date" value="birthdate" <?=($order=="birthdate")?"selected":""?>>Birthday (date-month)</option>
				          <option title="Gender" value="sex_id" <?=($order=="sex_id")?"selected":""?>>Gender</option>
				          <option title="Nationality" value="nationality_id" <?=($order=="nationality_id")?"selected":""?>>Nationality</option>
				          <option title="New Member" value="joindate" <?=($order=="joindate")?"selected":""?>>New Member</option>
				          <option title="Renewals" value="fill_date" <?=($order=="fill_date")?"selected":""?>>Renewals</option>
				          <option title="Expiry" value="expireddate" <?=($order=="expireddate")?"selected":""?>>Expiry</option>
				          <option title="YTD" value="ytd" <?=($order=="ytd")?"selected":""?>>YTD</option>
				          <option title="LTD" value="ltd" <?=($order=="ltd")?"selected":""?>>LTD</option>
				         </select>
				         </span>	
				          &nbsp;&nbsp;<input type="submit" name="sort" id="sort" value="<?=$sortby?>" onClick="changesbValue(this,'<?=$categoryid?>')"/>
				          <input type="hidden" name="chksortby" id="chksortby" value="<?=$sortby?>"/>
				          <input type="hidden" name="pageid" id="pageid" value="<?=$pageid?>"/>
        			</td>
		  		</tr>
    		</table>  
    	</td>
  	</tr>
	<tr bgcolor="#999999">
		        	<td height="1" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
	</tr>
 	<tr>
    	<td valign="top" height="20px">
    		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td height="30" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');"> Export:&nbsp;&nbsp;
			      	  <select id="export" name="export">
			            <option title="PDF" value="PDF">PDF</option>
			            <option title="Excel" value="Excel">Excel</option>
			          </select>          
						&nbsp;&nbsp;<input type="button" name="Export" id="Export" value="Export" onClick="window.open('report.php?<?=$querystr?>&export='+document.getElementById('export').value)"/>
						&nbsp;&nbsp;<input type="submit" name="Refresh" id="Refresh" value="Refresh" />
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
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('/images')"/></div>
</body>
</html>