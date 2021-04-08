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

//undefind variable
$cityid= (isset($cityid))?$cityid:"";
$payid= (isset($payid))?$payid:"";
$chkpage= (isset($chkpage))?$chkpage:"";
$chkrow= (isset($chkrow))?$chkrow:"";
$rs=(isset($rs))?$rs:"";
$bookSrdString= (isset($bookSrdString))?$bookSrdString:"";
$bookSrdString1= (isset($bookSrdString1))?$bookSrdString1:"";
$bookSrdString2= (isset($bookSrdString2))?$bookSrdString2:"";
$bookSrdString3= (isset($bookSrdString3))?$bookSrdString3:"";
$bookSrdString4= (isset($bookSrdString4))?$bookSrdString4:"";
$bookSrdString5= (isset($bookSrdString5))?$bookSrdString5:"";
$bookSrdString6= (isset($bookSrdString6))?$bookSrdString6:"";
$bookSrdString7= (isset($bookSrdString7))?$bookSrdString7:"";
$bookSrdString8= (isset($bookSrdString8))?$bookSrdString8:"";	
$bookSrdString9= (isset($bookSrdString9))?$bookSrdString9:"";	

		
if($branch_id==""){$branch_id=0;}
	
$branch = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id");
$export = $obj->getParameter("export",false);


/*if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",30);
	$chkpage = ceil($rs["rows"]/$chkrow);
}*/

$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
if($begin_date==$end_date){
	if($export=="Excel credit")
{
if($export=="Excel credit" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Export Excel Credit.xls\"");
	header("Pragma: public");
	header("Expires: 0");
	
}
?>

<?if($export!="Excel credit"){?><script type="text/javascript" src="../scripts/ajax.js"></script><?}?>
<?if($export!="Excel credit"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>    
			          
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0"  cellpadding="0">
	<!--<tr>
		<td width="7%"></td><td width="26%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="9%"></td>
		<td width="7%"></td>
	</tr>-->

	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Description</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CustomerRefFullName</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>ItemRefFullName</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Rate</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>AccountRef</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>TxnDate</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>InvoiceLineClassFullName</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>InvoiceRefNumber</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Quentity</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>TemplateRefFullName</b></td>
	</tr>
	<?
$date=$begin_date;


$begindate = $dateobj->convertdate(substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2),"Y-m-d","d-M-Y");
////////////////////////////
$Srddetail=array();
$Srddetail1=array();
$Srddetail2=array();
$Srddetail3=array();
$Srddetail4=array();
$Srddetail5=array();
$Srddetail6=array();
$Srddetail7=array();
$Srddetail8=array();
$Srddetail9=array();

			
$sqlbr= "select branch_id from bl_branchinfo where bl_branchinfo.branch_id<>1 and bl_branchinfo.branch_active=1" ;
		$aa = $obj->getResult($sqlbr);
		
		for($xx=0;$xx<$aa["rows"];$xx++)
				{	
					
    	  $sql1 = "select c_salesreceipt.salesreceipt_id,a_bookinginfo.b_branch_id as branch_id " .
        		   "from a_bookinginfo, c_salesreceipt, bl_branchinfo " .
        		   "where a_bookinginfo.b_appt_date>='".$begin_date."' ";
          if($city_id){$sql1 .= "and a_bookinginfo.b_branch_id = bl_branchinfo.branch_id " .
          		                "and bl_branchinfo.city_id=".$city_id." ";}
          $sql1 .= "and a_bookinginfo.b_appt_date<='".$end_date."' " .
        		   "and a_bookinginfo.book_id=c_salesreceipt.book_id " .
        		   "and a_bookinginfo.b_set_cancel=0 " .
        		   "and c_salesreceipt.paid_confirm=1";	   
       	 if($aa[$xx]["branch_id"]){$sql1 .= " and a_bookinginfo.b_branch_id=".$aa[$xx]["branch_id"]." ";} 
       	     
       	  $sql2 = "select c_salesreceipt.salesreceipt_id,c_saleproduct.branch_id as branch_id " .
        			"from c_saleproduct, c_salesreceipt, bl_branchinfo " .
        			"where c_saleproduct.pds_date>='".$begin_date."' ";
          if($city_id){$sql2 .= "and c_saleproduct.branch_id = bl_branchinfo.branch_id " .
          		                "and bl_branchinfo.city_id=".$city_id." ";}
          $sql2 .=	"and c_saleproduct.pds_date<='".$end_date."' " .
        			"and c_saleproduct.pds_id=c_salesreceipt.pds_id " .
        			"and c_saleproduct.set_cancel=0 " .
        		    "and c_salesreceipt.paid_confirm=1";
        if($aa[$xx]["branch_id"]){$sql2 .= " and c_saleproduct.branch_id=".$aa[$xx]["branch_id"]." ";}
          
          $sql = "($sql1) union ($sql2) order by salesreceipt_id";
        //  echo $sql."---";
      		

			
			$rs_test=$obj->getResult($sql);
				for($yy=0;$yy<$rs_test["rows"];$yy++)
					{
			
					
				if($rs_test[$yy]["branch_id"]==$aa[$xx]["branch_id"])
				{	
				$Srddetail[$yy] = $rs_test[$yy]["salesreceipt_id"];

				}
					}				if($Srddetail){$bookSrdString = implode(", ", $Srddetail); }
				//print_r($Srddetail);

if($rs_test[0]["branch_id"])
{


					$sql_data="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data=$obj->getResult($sql_data);
							//echo $sql_data;
								for($ss=0;$ss<$rs_data["rows"];$ss++)
								{
						
							?>
						<tr height="20">
						<td class="report" align="center">&nbsp;</td>
						<td class="report" align="center"><?=$rs_data[$ss]["pay_category_name"]?>:<?=$rs_data[$ss]["pay_name"]?></td>
						<td class="report" align="center"><?=$rs_data[$ss]["pay_category_name"]?>:<?=$rs_data[$ss]["pay_name"]?></td>
						<td class="report" align="center"><?=number_format($rs_data[$ss]["pay_total"],2,".",",")?></td>
						<td class="report" align="center"><?=$rs_data[$ss]["pay_account_name"]?></td>
						<td class="report" align="center"><?=$begindate?></td>
						<?
									if($rs_data[$ss]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data[$ss]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data[$ss]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data[$ss]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
						
						?>
						<td class="report" align="center"><?=$sub?>:<?=$branch?></td>
						<td class="report" align="center">&nbsp;</td>
						<td class="report" align="center">1</td>
						<td class="report" align="center"><?=$rs_data[$ss]["pay_template_name"]?></td>
						</tr>
						<?
					}
					
			}

		unset($Srddetail);
					
	}
				
//////////////////////////////
		
$rs = $obj->getcrs($branch_id,$date,$date);
						
					$total=0;
					$cnt=0;
					$Srddetail=array();
					$bookList=array();
					$pdsList=array();
					$bookListString="";
					$pdsListString="";
					$bookSrdString="";
					for($i=0;$i<$rs["rows"];$i++){
						
						if($rs[$i]["tb_name"]=="a_bookinginfo"){
							$bookList[$i]=$rs[$i]["book_id"];
						}else{
							$pdsList[$i]=$rs[$i]["book_id"];	
						}
					if($rs[$i]["set_cancel"]!=1 && $rs[$i]["paid_confirm"]==1){		
						$url = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rs[$i]["book_id"]."";
						$pagename = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rs[$i]["book_id"]:"managePds".$rs[$i]["book_id"];
					$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rs[$i]["bpds_id"]."</a>";	
					$total+=$rs[$i]["sr_total"];
			 
			 	
					 $Srddetail[$i] = $rs[$i]["salesreceipt_id"];
					 
				
					 }
					 	 
					}

					if($Srddetail){
				    	$bookSrdString = implode(", ", $Srddetail); 
				 	}
					
	//echo $bookSrdString;

	$sql_ptype="SELECT * FROM l_paytype_category where pay_category_id<>'1'";
					$rs_ptype=$obj->getResult($sql_ptype);
					
					
														for($i=0;$i<=$rs_ptype["rows"];$i++){
					//undefine offset
					if(!isset($rs_ptype[$i]["pay_category_id"])){$rs_ptype[$i]["pay_category_id"]="";}
															
								if($rs_ptype[$i]["pay_category_id"]=="2"){
		$sql_data="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_templatel_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='".$rs_ptype[$i]["pay_category_id"]."' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data=$obj->getResult($sql_data);
							//echo $sql_data."---------";
									
					}else{
						$sql_data="select c_srpayment.*,l_paytype.pay_id,l_paytype.pay_name,pay_category_name,l_account.pay_account_name,l_template.pay_template_name  " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='".$rs_ptype[$i]["pay_category_id"]."'";
						$rs_data=$obj->getResult($sql_data);
						
					}
															
					
						
			
			//echo $rs_data["rows"];
					if(!isset($total_pay[$i])){$total_pay[$i]=0;}
						for($k=0;$k<$rs_data["rows"];$k++){
							if($rs_ptype[$i]["pay_category_id"]!="2")
							{
							if($rs_ptype[$i]["pay_category_id"]!="2"){
								if($rs_data[$k]["book_id"]){
									$bid=$obj->getIdToText($rs_data[$k]["book_id"],"c_bpds_link","bpds_id","tb_id","tb_name='a_bookinginfo'");
									$url="manage_booking.php?chkpage=1&bookid=".$rs_data[$k]["book_id"]."";
									$pagename="manageBooking".$rs_data[$k]["book_id"];
									
									$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
									$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
															
								}else{
								$bid=$obj->getIdToText($rs_data[$k]["pds_id"],"c_bpds_link","bpds_id","tb_id","tb_name='c_saleproduct'");
								$url="manage_pdforsale.php?pdsid=".$rs_data[$k]["pds_id"]."";
								$pagename="managePds".$rs_data[$k]["pds_id"];
								
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
								}
								$bid_link[$k]="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$bid."</a>";	
							}
							
							$pay_type[$k]=$rs_data[$k]["pay_name"];
							$pay_value[$k]=$rs_data[$k]["pay_total"];
						
						?>
						<tr height="20">
						<?if($rs_ptype[$i]["pay_category_id"]!="2"){?>
							<td class="report" align="center"><?=$bid?></td>
							<?
							$bpid=$obj->getIdToText($rs_data[$k]["book_id"],"a_bookinginfo","c_bp_id","book_id");
							$bpname=$obj->getIdToText($bpid,"al_bookparty","bp_name","bp_id");
								if($rs_data[$k]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data[$k]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data[$k]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data[$k]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							?>
							<td class="report" align="center"><?=$bpname?></td>
						<?}else{
							if($rs_data[$k]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data[$k]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data[$k]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data[$k]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							?>
						<td class="report" align="center">&nbsp;</td>
						<td class="report" align="center"><?=$pay_type[$k]?></td>
						<?}?>
						<!--<td class="report" align="center"><?=$pay_type[$k]?></td>-->
						<td class="report" align="center"><?=($rs_ptype[$i]["pay_category_id"]==4)?"Credit Card".":".$pay_type[$k]:$pay_type[$k]?></td>
						<td class="report" align="center"><?=number_format($pay_value[$k],2,".",",")?></td>
						<td class="report" align="center"><?=$rs_data[$k]["pay_account_name"]?></td>
						<td class="report" align="center"><?=$begindate?></td>
						<td class="report" align="center"><?=$sub?>:<?=$branch?></td>
						<td class="report" align="center">&nbsp;</td>
						<td class="report" align="center">1</td>
						<td class="report" align="center"><?=$rs_data[$k]["pay_template_name"]?></td>
						</tr>
						<?
						}?>
						<?
						}
					}
	
	?>
	
		</table></td>
	</tr>
</table>


<?}
else{
	if($export!="Excel credit" && $export!="Excel cash")
	{
		
	 //file_put_contents("Export Excel Credit.xls", file_get_contents("script1.php"));
	?>
<?if($export!="Excel"){?><script type="text/javascript" src="../scripts/ajax.js"></script><?}?>
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>    
			          
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0"  cellpadding="0">
	<!--<tr>
		<td width="7%"></td><td width="26%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="9%"></td>
		<td width="7%"></td>
	</tr>-->
	<b><p>All Credit Card</p></b>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Description</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CustomerRefFullName</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>ItemRefFullName</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Rate</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>AccountRef</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>TxnDate</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>InvoiceLineClassFullName</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>InvoiceRefNumber</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Quentity</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>TemplateRefFullName</b></td>
	</tr>
	<?
$date=$begin_date;


$begindate = $dateobj->convertdate(substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2),"Y-m-d","d-M-Y");
////////////////////////////
$Srddetail=array();
$Srddetail1=array();
$Srddetail2=array();
$Srddetail3=array();
$Srddetail4=array();
$Srddetail5=array();
$Srddetail6=array();
$Srddetail7=array();
$Srddetail8=array();
$Srddetail9=array();

			
$sqlbr= "select branch_id from bl_branchinfo where bl_branchinfo.branch_id<>1 and bl_branchinfo.branch_active=1" ;
		$aa = $obj->getResult($sqlbr);
		
		for($xx=0;$xx<$aa["rows"];$xx++)
				{	
					
    	  $sql1 = "select c_salesreceipt.salesreceipt_id,a_bookinginfo.b_branch_id as branch_id " .
        		   "from a_bookinginfo, c_salesreceipt, bl_branchinfo " .
        		   "where a_bookinginfo.b_appt_date>='".$begin_date."' ";
          if($city_id){$sql1 .= "and a_bookinginfo.b_branch_id = bl_branchinfo.branch_id " .
          		                "and bl_branchinfo.city_id=".$city_id." ";}
          $sql1 .= "and a_bookinginfo.b_appt_date<='".$end_date."' " .
        		   "and a_bookinginfo.book_id=c_salesreceipt.book_id " .
        		   "and a_bookinginfo.b_set_cancel=0 " .
        		   "and c_salesreceipt.paid_confirm=1";	   
       	 if($aa[$xx]["branch_id"]){$sql1 .= " and a_bookinginfo.b_branch_id=".$aa[$xx]["branch_id"]." ";} 
       	     
       	  $sql2 = "select c_salesreceipt.salesreceipt_id,c_saleproduct.branch_id as branch_id " .
        			"from c_saleproduct, c_salesreceipt, bl_branchinfo " .
        			"where c_saleproduct.pds_date>='".$begin_date."' ";
          if($city_id){$sql2 .= "and c_saleproduct.branch_id = bl_branchinfo.branch_id " .
          		                "and bl_branchinfo.city_id=".$city_id." ";}
          $sql2 .=	"and c_saleproduct.pds_date<='".$end_date."' " .
        			"and c_saleproduct.pds_id=c_salesreceipt.pds_id " .
        			"and c_saleproduct.set_cancel=0 " .
        		    "and c_salesreceipt.paid_confirm=1";
        if($aa[$xx]["branch_id"]){$sql2 .= " and c_saleproduct.branch_id=".$aa[$xx]["branch_id"]." ";}
          
          $sql = "($sql1) union ($sql2) order by salesreceipt_id";
        //  echo $sql."---";
      		

			
			$rs_test=$obj->getResult($sql);
				for($yy=0;$yy<$rs_test["rows"];$yy++)
					{
			
					
				if($rs_test[$yy]["branch_id"]==$aa[$xx]["branch_id"])
				{	
				$Srddetail[$yy] = $rs_test[$yy]["salesreceipt_id"];

				}
					}				if($Srddetail){$bookSrdString = implode(", ", $Srddetail); }
				//print_r($Srddetail);

if($rs_test[0]["branch_id"])
{


					$sql_data="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data=$obj->getResult($sql_data);
							//echo $sql_data;
								for($ss=0;$ss<$rs_data["rows"];$ss++)
								{
						
							?>
						<tr height="20">
						<td class="report" align="center">&nbsp;</td>
						<td class="report" align="center"><?=$rs_data[$ss]["pay_category_name"]?>:<?=$rs_data[$ss]["pay_name"]?></td>
						<td class="report" align="center"><?=$rs_data[$ss]["pay_category_name"]?>:<?=$rs_data[$ss]["pay_name"]?></td>
						<td class="report" align="center"><?=number_format($rs_data[$ss]["pay_total"],2,".",",")?></td>
						<td class="report" align="center"><?=$rs_data[$ss]["pay_account_name"]?></td>
						<td class="report" align="center"><?=$begindate?></td>
						<?
									if($rs_data[$ss]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data[$ss]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data[$ss]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data[$ss]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
						
						?>
						<td class="report" align="center"><?=$sub?>:<?=$branch?></td>
						<td class="report" align="center">&nbsp;</td>
						<td class="report" align="center">1</td>
						<td class="report" align="center"><?=$rs_data[$ss]["pay_template_name"]?></td>
						</tr>
						<?
					}
					
			}

		unset($Srddetail);
					
	}
				
//////////////////////////////
		
$rs = $obj->getcrs($branch_id,$date,$date);
						
					$total=0;
					$cnt=0;
					$Srddetail=array();
					$bookList=array();
					$pdsList=array();
					$bookListString="";
					$pdsListString="";
					$bookSrdString="";
					for($i=0;$i<$rs["rows"];$i++){
						
						if($rs[$i]["tb_name"]=="a_bookinginfo"){
							$bookList[$i]=$rs[$i]["book_id"];
						}else{
							$pdsList[$i]=$rs[$i]["book_id"];	
						}
					if($rs[$i]["set_cancel"]!=1 && $rs[$i]["paid_confirm"]==1){		
						$url = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rs[$i]["book_id"]."";
						$pagename = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rs[$i]["book_id"]:"managePds".$rs[$i]["book_id"];
					$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rs[$i]["bpds_id"]."</a>";	
					$total+=$rs[$i]["sr_total"];
			 
			 	
					 $Srddetail[$i] = $rs[$i]["salesreceipt_id"];
					 
				
					 }
					 	 
					}

					if($Srddetail){
				    	$bookSrdString = implode(", ", $Srddetail); 
				 	}
					
	//echo $bookSrdString;

	$sql_ptype="SELECT * FROM l_paytype_category where pay_category_id<>'1'";
					$rs_ptype=$obj->getResult($sql_ptype);
					
					
														for($i=0;$i<=$rs_ptype["rows"];$i++){
					//undefine offset
					if(!isset($rs_ptype[$i]["pay_category_id"])){$rs_ptype[$i]["pay_category_id"]="";}	
					
								if($rs_ptype[$i]["pay_category_id"]=="2"){
		$sql_data="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_templatel_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='".$rs_ptype[$i]["pay_category_id"]."' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data=$obj->getResult($sql_data);
							//echo $sql_data."---------";
									
					}else{
						$sql_data="select c_srpayment.*,l_paytype.pay_id,l_paytype.pay_name,pay_category_name,l_account.pay_account_name,l_template.pay_template_name  " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='".$rs_ptype[$i]["pay_category_id"]."'";
						$rs_data=$obj->getResult($sql_data);
						
					}
															
					
						
			
			//echo $rs_data["rows"];
					if(!isset($total_pay[$i])){$total_pay[$i]=0;}
					//undefined variable
					$bpidb= (isset($bpidb))?$bpidb:"";
						for($k=0;$k<$rs_data["rows"];$k++){
							if($rs_ptype[$i]["pay_category_id"]!="2")
							{
							if($rs_ptype[$i]["pay_category_id"]!="2"){
								if($rs_data[$k]["book_id"]){
									$bid=$obj->getIdToText($rs_data[$k]["book_id"],"c_bpds_link","bpds_id","tb_id","tb_name='a_bookinginfo'");
									$url="manage_booking.php?chkpage=1&bookid=".$rs_data[$k]["book_id"]."";
									$pagename="manageBooking".$rs_data[$k]["book_id"];
									
									$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
									$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
															
								}else{
								$bid=$obj->getIdToText($rs_data[$k]["pds_id"],"c_bpds_link","bpds_id","tb_id","tb_name='c_saleproduct'");
								$url="manage_pdforsale.php?pdsid=".$rs_data[$k]["pds_id"]."";
								$pagename="managePds".$rs_data[$k]["pds_id"];
								
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
								}
								$bid_link[$k]="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$bid."</a>";	
							}
							
							$pay_type[$k]=$rs_data[$k]["pay_name"];
							$pay_value[$k]=$rs_data[$k]["pay_total"];
						
						?>
						<tr height="20">
						<?if($rs_ptype[$i]["pay_category_id"]!="2"){?>
							<td class="report" align="center"><?=$bid_link[$k]?></td>
							<?
							$bpid=$obj->getIdToText($rs_data[$k]["book_id"],"a_bookinginfo","c_bp_id","book_id");
							$bpname=$obj->getIdToText($bpid,"al_bookparty","bp_name","bp_id");
								if($rs_data[$k]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data[$k]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data[$k]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data[$k]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							?>
							<td class="report" align="center"><?=$bpname?></td>
						<?}else{
							if($rs_data[$k]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data[$k]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data[$k]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data[$k]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							?>
						<td class="report" align="center">&nbsp;</td>
						<td class="report" align="center"><?=$pay_type[$k]?></td>
						<?}?>
						<!--<td class="report" align="center"><?=$pay_type[$k]?></td>-->
						<td class="report" align="center"><?=($rs_ptype[$i]["pay_category_id"]==4)?"Credit Card".":".$pay_type[$k]:$pay_type[$k]?></td>
						<td class="report" align="center"><?=number_format($pay_value[$k],2,".",",")?></td>
						<td class="report" align="center"><?=$rs_data[$k]["pay_account_name"]?></td>
						<td class="report" align="center"><?=$begindate?></td>
						<td class="report" align="center"><?=$sub?>:<?=$branch?></td>
						<td class="report" align="center">&nbsp;</td>
						<td class="report" align="center">1</td>
						<td class="report" align="center"><?=$rs_data[$k]["pay_template_name"]?></td>
						</tr>
						<?
						}?>
						<?
						}
					}
	
	?>
	
		</table></td>
	</tr>
</table>

<?}}
if($export=="Excel cash")
{
if($export=="Excel cash" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Export Excel Cash.xls\"");
	header("Pragma: public");
	header("Expires: 0");
	
}
?>
<?if($export!="Excel cash"){?><script type="text/javascript" src="../scripts/ajax.js"></script><?}?>
<?if($export!="Excel cash"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>    
<!--table cash-->

<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0"  cellpadding="0">
	<!--<tr>
		<td width="7%"></td><td width="26%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="9%"></td>
		<td width="7%"></td>
	</tr>-->

	
	<tr height="32">
					
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CustomerRefFullName</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>ItemRefFullName</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Deposit To</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>TxnDate</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>InvoiceLineClassFullName</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>InvoiceRefNumber</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>TemplateRefFullName</b></td>
	</tr>
<?
				$sql_cash="SELECT c_closing_receipt.*,c_account_deposit.account_num,c_account_deposit.name,c_account_deposit.account_name,c_account_deposit.template " .
						"FROM c_closing_receipt,c_account_deposit " .
						"where closing_date='$begin_date'and c_closing_receipt.closing_branch_id=c_account_deposit.branch_id";
						//echo $sql_cash;
						$rs_cash=$obj->getResult($sql_cash);
						
				for($x=0;$x<$rs_cash["rows"];$x++){
					if($rs_cash[$x]["total_cash_deposit"]!="0" ){
											
				
					?>
					<tr height="20">
						<td class="report" align="center"><?=$rs_cash[$x]["account_name"]?></td>
						<td class="report" align="center"><?=$rs_cash[$x]["name"]?></td>
						<td class="report" align="center"><?=number_format($rs_cash[$x]["total_cash_deposit"],2,".",",")?></td>
						<?
								$br=$rs_cash[$x]["account_num"];
								$acc = $obj->getIdToText($br,"l_account","pay_account_name","pay_account_id");
						?>
						<td class="report" align="center"><?=$acc?></td>
						<td class="report" align="center"><?=$begindate?></td>
						<?$bnd=$rs_cash[$x]["closing_branch_id"];
						$bran = $obj->getIdToText($bnd,"bl_branchinfo","branch_name","branch_id");
						$sub = $obj->getIdToText($bnd,"bl_branchinfo","branch_name3","branch_id");?>
						
						<td class="report" align="center"><?=$sub?>:<?=$bran?></td>
						<td class="report" align="center">&nbsp;</td>
						<?
								$brr=$rs_cash[$x]["template"];
								$tem = $obj->getIdToText($brr,"l_template","pay_template_name","pay_template_id");
						?>
						<td class="report" align="center"><?=$tem?></td>
				<?
					
					}
					
				}
				$sql_pc="SELECT c_closing_receipt.*,c_account.account_num,c_account.name,c_account.customer_name,c_account.template " .
						"FROM c_closing_receipt,c_account " .
						"where closing_date='$begin_date'and c_closing_receipt.closing_branch_id=c_account.branch_id";
						//echo $sql_pc;
						$rs_pc=$obj->getResult($sql_pc);
				for($y=0;$y<$rs_pc["rows"];$y++){
				
					if($rs_pc[$y]["tranfer_pc"]!="0" ){
					?>
					<tr height="20">
						<td class="report" align="center"><?=$rs_pc[$y]["customer_name"]?></td>
						<td class="report" align="center"><?=$rs_pc[$y]["name"]?></td>
						<td class="report" align="center"><?=number_format($rs_pc[$y]["tranfer_pc"],2,".",",")?></td>
						<?
								$br=$rs_pc[$y]["account_num"];
								$acc = $obj->getIdToText($br,"l_account","pay_account_name","pay_account_id");
						?>
						<td class="report" align="center"><?=$acc?></td>
						<td class="report" align="center"><?=$begindate?></td>
						<?$bnd=$rs_pc[$y]["closing_branch_id"];
						$bran = $obj->getIdToText($bnd,"bl_branchinfo","branch_name","branch_id");
						$sub = $obj->getIdToText($bnd,"bl_branchinfo","branch_name3","branch_id");?>
						<td class="report" align="center"><?=$sub?>:<?=$bran?></td>
						<td class="report" align="center">&nbsp;</td>
						<?
								$brr=$rs_pc[$y]["template"];
								$tem = $obj->getIdToText($brr,"l_template","pay_template_name","pay_template_id");
						?>
						<td class="report" align="center"><?=$tem?></td>
					<?
					}
					
						
				}
?>
		</table></td>
	</tr>
</table>
<?
}else{
	if($export!="Excel credit" && $export!="Excel cash")
	{
	?>

<!--table cash-->

<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0"  cellpadding="0">
	<!--<tr>
		<td width="7%"></td><td width="26%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="9%"></td>
		<td width="7%"></td>
	</tr>-->
	<b><p>All Cash to PC & Cash to Bank</p></b>
	
	<tr height="32">
					
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CustomerRefFullName</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>ItemRefFullName</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Deposit To</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>TxnDate</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>InvoiceLineClassFullName</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>InvoiceRefNumber</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>TemplateRefFullName</b></td>
	</tr>
<?
				$sql_cash="SELECT c_closing_receipt.*,c_account_deposit.account_num,c_account_deposit.name,c_account_deposit.account_name,c_account_deposit.template " .
						"FROM c_closing_receipt,c_account_deposit " .
						"where closing_date='$begin_date'and c_closing_receipt.closing_branch_id=c_account_deposit.branch_id";
						//echo $sql_cash;
						$rs_cash=$obj->getResult($sql_cash);
						
				for($x=0;$x<$rs_cash["rows"];$x++){
					if($rs_cash[$x]["total_cash_deposit"]!="0" ){
											
				
					?>
					<tr height="20">
						<td class="report" align="center"><?=$rs_cash[$x]["account_name"]?></td>
						<td class="report" align="center"><?=$rs_cash[$x]["name"]?></td>
						<td class="report" align="center"><?=number_format($rs_cash[$x]["total_cash_deposit"],2,".",",")?></td>
						<?
								$br=$rs_cash[$x]["account_num"];
								$acc = $obj->getIdToText($br,"l_account","pay_account_name","pay_account_id");
						?>
						<td class="report" align="center"><?=$acc?></td>
						<td class="report" align="center"><?=$begindate?></td>
						<?$bnd=$rs_cash[$x]["closing_branch_id"];
						$bran = $obj->getIdToText($bnd,"bl_branchinfo","branch_name","branch_id");
						$sub = $obj->getIdToText($bnd,"bl_branchinfo","branch_name3","branch_id");?>
						
						<td class="report" align="center"><?=$sub?>:<?=$bran?></td>
						<td class="report" align="center">&nbsp;</td>
						<?
								$brr=$rs_cash[$x]["template"];
								$tem = $obj->getIdToText($brr,"l_template","pay_template_name","pay_template_id");
						?>
						<td class="report" align="center"><?=$tem?></td>
				<?
					
					}
					
				}
				$sql_pc="SELECT c_closing_receipt.*,c_account.account_num,c_account.name,c_account.customer_name,c_account.template " .
						"FROM c_closing_receipt,c_account " .
						"where closing_date='$begin_date'and c_closing_receipt.closing_branch_id=c_account.branch_id";
						//echo $sql_pc;
						$rs_pc=$obj->getResult($sql_pc);
				for($y=0;$y<$rs_pc["rows"];$y++){
				
					if($rs_pc[$y]["tranfer_pc"]!="0" ){
					?>
					<tr height="20">
						<td class="report" align="center"><?=$rs_pc[$y]["customer_name"]?></td>
						<td class="report" align="center"><?=$rs_pc[$y]["name"]?></td>
						<td class="report" align="center"><?=number_format($rs_pc[$y]["tranfer_pc"],2,".",",")?></td>
						<?
								$br=$rs_pc[$y]["account_num"];
								$acc = $obj->getIdToText($br,"l_account","pay_account_name","pay_account_id");
						?>
						<td class="report" align="center"><?=$acc?></td>
						<td class="report" align="center"><?=$begindate?></td>
						<?$bnd=$rs_pc[$y]["closing_branch_id"];
						$bran = $obj->getIdToText($bnd,"bl_branchinfo","branch_name","branch_id");
						$sub = $obj->getIdToText($bnd,"bl_branchinfo","branch_name3","branch_id");?>
						<td class="report" align="center"><?=$sub?>:<?=$bran?></td>
						<td class="report" align="center">&nbsp;</td>
						<?
								$brr=$rs_pc[$y]["template"];
								$tem = $obj->getIdToText($brr,"l_template","pay_template_name","pay_template_id");
						?>
						<td class="report" align="center"><?=$tem?></td>
					<?
					}
					
						
				}
?>
		</table></td>
	</tr>
</table>
<?}}
}else{?>
<b><p>Please Select one day only!!!</p></b>
		<b><p>Start Date & End Date Must Be Same!!!</p></b>
<?}?>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>