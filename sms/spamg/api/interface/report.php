<?php
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
$obj = new checker();

$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid");
$pd_id = $obj->getParameter("itemid");
$n = $obj->getParameter("n");
$sqlbr= "select branch_id from bl_branchinfo where bl_branchinfo.branch_id<>1 and bl_branchinfo.branch_active=1" ;
				$aa = $obj->getResult($sqlbr);
				
				for($x=0; $x<1; $x++) {
				
			
				$bb=$aa[$x]["branch_id"];
				//echo $bb;
				}

if($branch_id==""){$branch_id=5;}
//$branch = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id");
$export = $obj->getParameter("export",false);

if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Export Excel.xls\"");
	header("Pragma: public");
	header("Expires: 0");
	
}



if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",30);
	$chkpage = ceil($rs["rows"]/$chkrow);
}

$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
?>
<?if($export!="Excel"){?><script type="text/javascript" src="../scripts/ajax.js"></script><?}?>
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>    
		
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>

    	<td valign="top" style="padding:10 20 50 20;" width="100%" >
    		<b><p>Transfer To PC Refrence</p></b>
	<table width="100%" border="1" cellspacing="0"  cellpadding="0">
					
	<tr height="32">
					<td style="text-align:center;background-color:#a8c2cb;"><b>Customer Name</b></td>
					<td style="text-align:center;background-color:#a8c2cb;"><b>Transection</b></td>
					<td style="text-align:center;background-color:#a8c2cb;"><b>Branch</b></td>
					<td style="text-align:center;background-color:#a8c2cb;"><b>Account Number</b></td>
					<td style="text-align:center;background-color:#a8c2cb;"><b>Template</b></td>
					<? if($chkPageEdit){?>
					<td style="text-align:center;background-color:#a8c2cb;"><b>Update</b></td>
					<?}?>	
					
	</tr>
<?
$sql= "select * from c_account" ;
$aa = $obj->getResult($sql);
				for($x=0; $x<$aa["rows"]; $x++) {
	$bgcolor = "#eaeaea"; $class = "even";
		if($x%2==0){
			$bgcolor = "#d3d3d3"; $class = "odd";
		}	
					?>
				<tr bgcolor="<?=$bgcolor?>" class="<?=$class?>"  height="20">
				<td align="left"><?=$aa[$x]["customer_name"]?></td>
					<td align="center"><?=$aa[$x]["name"]?></td>
				<?
								$br=$aa[$x]["branch_id"];
								$branch = $obj->getIdToText($br,"bl_branchinfo","branch_name","branch_id");
				?>
				<td align="center"><?=$branch?></td>
				<?
								$br=$aa[$x]["account_num"];
								$acc = $obj->getIdToText($br,"l_account","pay_account_name","pay_account_id");
				?>
				<td align="center"><?=$acc?></td>
				<?
								$brr=$aa[$x]["template"];
								$tem = $obj->getIdToText($brr,"l_template","pay_template_name","pay_template_id");
				?>
				<td align="center"><?=$tem?></td>
				
				<input type=hidden name=n value="<?=$n=$aa[$x]["account_id"]?>">
				<? if($chkPageEdit){?>
	  			<td align="center" onClick="gotoURL('editinfo.php?n=<?=$n?>');"><a>Update</a></td>
				<?}?>		
				</tr>
				<?}



?>
	
		</table></td>
		
	</tr>
	
	<td valign="top" style="padding:10 20 50 20;" width="100%" >
	
		<table width="100%" border="1" cellspacing="0"  cellpadding="0">
		<b><p>Cash Deposit To Bank</p></b>
			<tr height="32">
					
					<td style="text-align:center;background-color:#a8c2cb;"><b>Customer Name</b></td>
					<td style="text-align:center;background-color:#a8c2cb;"><b>Transection</b></td>
					<td style="text-align:center;background-color:#a8c2cb;"><b>Branch</b></td>
					<td style="text-align:center;background-color:#a8c2cb;"><b>Account Number</b></td>
					<td style="text-align:center;background-color:#a8c2cb;"><b>Template</b></td>
						<? if($chkPageEdit){?>
						<td style="text-align:center;background-color:#a8c2cb;"><b>Update</b></td>
						<?}?>
					
	</tr>
		
<?
$sql= "select * from c_account_deposit" ;
$bb = $obj->getResult($sql);
				for($y=0; $y<$bb["rows"]; $y++) {
$bgcolor = "#eaeaea"; $class = "even";
		if($y%2==0){
			$bgcolor = "#d3d3d3"; $class = "odd";
		}	
					?>
				<tr bgcolor="<?=$bgcolor?>" class="<?=$class?>"  height="20">
				<td align="left"><?=$bb[$y]["account_name"]?></td>
				<td align="center"><?=$bb[$y]["name"]?></td>
				<?
								$br=$bb[$y]["branch_id"];
								$branch = $obj->getIdToText($br,"bl_branchinfo","branch_name","branch_id");
				?>
				<td align="center"><?=$branch?></td>
				<?
								$br=$bb[$y]["account_num"];
								$acc = $obj->getIdToText($br,"l_account","pay_account_name","pay_account_id");
				?>
				<td align="center"><?=$acc?></td>
				<?
								$brr=$bb[$y]["template"];
								$tem = $obj->getIdToText($brr,"l_template","pay_template_name","pay_template_id");
				?>
				<td align="center"><?=$tem?></td>
				<input type=hidden name=n value="<?=$n=$bb[$y]["account_id"]?>">
				<? if($chkPageEdit){?>
	  			<td align="center" onClick="gotoURL('editinfos.php?n=<?=$n?>');"><a>Update</a></td>
				<?}?>
				</tr>
				<?}



?>		
		
		
		
		</table></td>
</table>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>