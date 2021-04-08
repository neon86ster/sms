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
$querystr = "pageid=$pageid&begin=$hidden_begin&end=$hidden_end&branchid=$branch&payid=$payid";
$print = "report.php?$querystr&export=print";
 //header( "refresh:3;url=google.php" );
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

</head>
<body onLoad="getReturnText('report1.php','<?=$querystr?>','tableDisplay');">
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
    <td width="8" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
    <td height="99px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" align="right" height="49">
				<?include("$root/rmenuheader.php");?>	 	</td>
	  </tr>
	  <tr>
	    <td valign="top" align="center" height="10">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr height="25">
			        <td class="rheader" style="padding-left: 20px; background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			        Dates:<input type="hidden" id="pageid" name="pageid" value="<?=$pageid?>"/>
			        <span class="date" style="width: 115px;font-family:Tahoma; font-size: 12px;overflow:hidden;">
					<select id="date" name="date" class="ctrDropDown" onBlur="this.className='ctrDropDown';" onMouseDown="this.className='ctrDropDownClick';" onChange="this.className='ctrDropDown';">
						  <option title="All" value="1" <?=($date=="1")?"selected":""?>>All</option>
			              <option title="Custom" value="2" <?=($date=="2")?"selected":""?>>Custom</option>
			              <option title="Today" value="17" <?=($date=="17")?"selected":""?>>Today</option>
			              <option title="Yesterday" value="18" <?=($date=="18")?"selected":""?>>Yesterday</option>
					</select>
				</span>
			        </td>
			         <!--<td class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">-->
		          <!--  &nbsp;&nbsp;<input id="date" name="date" value="<?php
		        //echo $hidden_date;
              	//echo $branchid;
              	echo (isset($begin))?$begin:$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$sdateformat",$branchid);  ?>" style="width: 85px;" readonly="1" class="textbox" type="text" onKeyPress="return disableEnterKey(event);">
		           	<input id='hidden_date' name='hidden_date' value="<?=$hidden_begin?>" type="hidden"/>
		            <a href="javascript:;" style="margin-top:0.3px;position:fixed;" 
				        onclick="showChooser(this, 'date', 'date_showSpan', 1900, 2100, '<?=$sdateformat?>', false,false);"
				        onmouseover="changeimg('calendarimg','/images/calendar.png')" 
				        onmouseout="changeimg('calendarimg','/images/calendar.png')">
				        <img align="top" style="margin-top:3px;" src="/images/calendar.png" id="calendarimg" border="0" title="date">
				        </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				        <div id="date_showSpan" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div>-->
			      
			       <td class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			        &nbsp;From: <input id='begin' name='begin' value="<?=$begin?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			        <input type="hidden" id="hidden_begin" name="hidden_begin" value="<?=$hidden_begin?>"/>
			        <img align="top" src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'begin', 'date_begin', 1900, 2100, '<?=$sdateformat?>', false,false);" />
			       <div id="date_begin" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"> </div>
			       
			        To: <input id='end' name='end' value="<?=$end?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			        <input type="hidden" id="hidden_end" name="hidden_end" value="<?=$hidden_end?>"/>
			        <img align="top" src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'end', 'date_end', 1900, 2100, '<?=$sdateformat?>', false,false);" />
			        <div id="date_end" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div>
			        </td>
			        <?
			     //  echo "start".$begin."--";
			  //     echo $hidden_begin."--";
			   //    echo "end".$end."--";
			   //    echo $hidden_end."--";
			        
			        ?>

				 <!--
			        <td class="rheader" height="30" align="right" style="padding-right: 20px; background-image: url('/images/<?=$theme?>/appt/menubg.png');">
				        Method of Payment:
						<?=$obj->makeListbox("pay_id","all_l_paytype","pay_name","pay_id",$payid,0,"pay_name","pay_active","1","")?>
					</td>
			        <td class="rheader" height="30" align="right" style="padding-right: 20px; background-image: url('/images/<?=$theme?>/appt/menubg.png');">
				        Branch:
						<?=$obj->makeListbox("branchid","all_bl_branchinfo","branch_name","branch_id",$branch,0,"branch_name","branch_active","1","branch_name!='All'")?>
					</td>
					-->
		  		</tr>
		    	<tr bgcolor="#999999">
		        	<td height="1" colspan="4" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
		    	</tr>
    		</table>  
    	</td>
  	</tr>
 	<tr>
    	<td valign="top" height="20px">
    		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			     
			        <td height="30" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');"> 
			            
			            	Export:&nbsp;&nbsp;
			          <select id="export" name="export">
			           	<option title="Excel" value="Excel credit">Excel credit</option>
			           	<option title="Excel" value="Excel cash">Excel cash</option>
			           </select>          
			          &nbsp;&nbsp;<input type="button" name="Export" id="Export" value="Export" onClick="window.open('report.php?<?=$querystr?>&export='+document.getElementById('export').value)"/>
 			       <!--  &nbsp;&nbsp;<input type="button" name="Export" id="Export" value="Export Cash" onClick="window.open('report2.php?<?=$querystr?>&export='+document.getElementById('export').value)"/>-->
 			         <?/*
 			         	$host  = $_SERVER['HTTP_HOST'];
						$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
						$filename = "Export_Credit_" . date('Ymd') . ".xls"; 
						//header("Location: http://$host$uri/$filename");
 			         header( "refresh:5;url=$host$uri/$filename" );
 			           echo 'You\'ll be redirected in about 5 secs. If not, click <a href="www.php">here</a>.';*/
 			         ?>
 			          &nbsp;&nbsp;<input type="submit" name="Refresh" id="Refresh" value="Refresh" />
			           
			          </td>
			       <!--   <td height="30" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			    
			        Export:&nbsp;&nbsp;
			          <select id="export" name="export">
			            <option title="PDF" value="PDF">PDF</option>
			            <option title="Excel" value="Excel">Excel</option>
			          </select>          
			          &nbsp;&nbsp;<input type="button" name="Export" id="Export" value="Export" onClick="window.open('report.php?begin=<?=$hidden_begin?>&end=<?=$hidden_end?>&branchid=<?=$branch?>&export='+document.getElementById('export').value)"/>
			          &nbsp;&nbsp;<input type="submit" name="Refresh" id="Refresh" value="Refresh"/>
			             
			           </td>-->
			        </tr>
			      <tr bgcolor="#999999">
			        <td height="1" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
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
