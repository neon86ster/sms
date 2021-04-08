<?
ini_set("memory_limit","-1");
?>
<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
require_once("customer.inc.php");

$obj  = new checker();
$objc = new customer();

$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid");
$city_id = $obj->getParameter("cityid",0,1);

$ttcs=0;
if(!isset($_SESSION["__user_id"])){$_SESSION["__user_id"]="";}
$sql = "select * from s_user where u_id=".$_SESSION["__user_id"];
$rs_buser = $obj->getResult($sql);

if($branch_id==""){
	if($rs_buser[0]["branch_id"]!=1){
		$branch_id=$rs_buser[0]["branch_id"];
	}else{
		$branch_id=0;
	}
}
$export = $obj->getParameter("export",false);

if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print");
}

$rs = $obj->getcrs($branch_id,$begin_date,$end_date);
$rs_ttcs = $obj->getttcs($branch_id,$begin_date,$end_date);
$rs_tthour = $obj->gettthour($branch_id,$begin_date,$end_date);
$rs_gender = $objc->getcustpersex($begin_date,$end_date,$branch_id,false,$city_id);
$rsres = $objc->getcustlocal("Resident",$begin_date,$end_date,$branch_id,false,$city_id);
$rsvis = $objc->getcustlocal("Visitor",$begin_date,$end_date,$branch_id,false,$city_id);

if(!$rs_tthour){$rs_tthour[0]["total"]=0;}

if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",30);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Summary Report Specific.xls\"");
	header("Pragma: public");
	header("Expires: 0");
	
}

//$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")." Overview Summary";
$reportname = "Overview Summary";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
$totaldate = ( strtotime($end_date) - strtotime($begin_date) ) / ( 60 * 60 * 24 )+1;

?>

<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr>
  <td valign="top" style="padding:10 20 20 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="5%"></td>
		<td width="12%"></td>
		<td width="5%"></td>
		<td width="7%"></td>
		<td width="7%"></td>
		<td width="7%"></td>
		<td width="7%"></td>
		<td width="10%"></td>
		<td width="10%"></td>
		<td width="10%"></td>
		<td width="10%"></td>
		<td width="10%"></td>
	</tr>
	
	<?
	 //Get All Branch
        $sql = "select branch_id, branch_name from bl_branchinfo where branch_id<>1 ";
        		if($city_id){$sql .= "and city_id=".$city_id." ";}else
        		if($branch_id){$sql .= "and branch_id=".$branch_id." ";}
        $sql.= "and branch_active=1 order by branch_name asc";
        $rsBranch = $obj->getResult($sql);
    
        
	?>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="12">
    		<b><p>Spa Management System</p></b>
    		<b><?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br></p>
    		<p><b style='color:#ff0000'><?="Branch : "?>
    		<?
    			for($j=0; $j<$rsBranch["rows"]; $j++){
    				$nbranchdetail[$j] = $rsBranch[$j]["branch_name"];
    			}
    			if($nbranchdetail){
  	  				$NbranchSrdString = implode(", ", $nbranchdetail); 
  				}
  				echo $NbranchSrdString;
    		?>
    		</b><br></p>
    	</td>
	</tr>
    
    <tr height="20" bgcolor="#D3D3D3">
        <td colspan="12" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Overview</b></td>
    </tr>
    <tr height="20">
        <td align="left" style="border-bottom:1px #000000 solid;"><b>Branch</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Amount</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>(%)</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Hours</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Free Hours</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Customers</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Free Cust.</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Avg. Cust. Per Day</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Avg. Income Per Day</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Avg. Hours Per Cust.</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Avg. Income Per Hour.</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Avg. Income Per Cust.</b></td>
    </tr>
	
   <?
   	$total=0;
   	$totalfcust=0;
    $totalfhours=0;
   	for($j=0; $j<$rsBranch["rows"]; $j++){
   		$sql1 = "select a_bookinginfo.b_branch_id as branch_id, sum(c_salesreceipt.sr_total) as total " .
        		   "from a_bookinginfo, c_salesreceipt " .
        		   "where a_bookinginfo.b_appt_date>='".$begin_date."' " .
        		   "and a_bookinginfo.b_appt_date<='".$end_date."' " .
        		   "and a_bookinginfo.b_branch_id=".$rsBranch[$j]["branch_id"]." " .
        		   "and a_bookinginfo.book_id=c_salesreceipt.book_id " .
        		   "and a_bookinginfo.b_set_cancel=0 " .
        		   "and c_salesreceipt.paid_confirm=1 " .
        		   "group by a_bookinginfo.b_branch_id";
        	
        	$sql2 = "select c_saleproduct.branch_id, sum(c_salesreceipt.sr_total) as total " .
        			"from c_saleproduct, c_salesreceipt " .
        			"where c_saleproduct.pds_date>='".$begin_date."' " .
        			"and c_saleproduct.pds_date<='".$end_date."' " .
        			"and c_saleproduct.branch_id=".$rsBranch[$j]["branch_id"]." " .
        			"and c_saleproduct.pds_id=c_salesreceipt.pds_id " .
        			"and c_saleproduct.set_cancel=0 " .
        		    "and c_salesreceipt.paid_confirm=1 " .
        		    "group by c_saleproduct.branch_id";
        		    
        		          	
        	$rsTotal1 = $obj->getResult($sql1);
        	$amount1 = $rsTotal1[0]["total"];
        	$rsTotal2 = $obj->getResult($sql2);
        	$amount2 = $rsTotal2[0]["total"];
        	$amount[$j] = $amount1+$amount2;
        	$total = $total+$amount[$j];
        	
        	$branchdetail[$j] = $rsBranch[$j]["branch_id"];	
   
 	$freeb = "select c_salesreceipt.book_id,sum(c_salesreceipt.sr_total) as total  " .
 			"from c_salesreceipt,a_bookinginfo " .
 			"where c_salesreceipt.book_id=a_bookinginfo.book_id " .
 			"and a_bookinginfo.a_member_code=0 " .
 			"and a_bookinginfo.b_set_cancel=0 " .
 			"and a_bookinginfo.b_branch_id=".$rsBranch[$j]["branch_id"]." " .
 			"and a_bookinginfo.b_appt_date>='".$begin_date."' ".
 			"and a_bookinginfo.b_appt_date<='".$end_date."' ".
 			"group by c_salesreceipt.book_id";

	$rsfreeb = $obj->getResult($freeb);

	$book_free=array();
		for($f=0; $f<$rsfreeb["rows"]; $f++){
			if($rsfreeb[$f]["total"]==0){
				$book_free[$f]=$rsfreeb[$f]["book_id"];
				//$cnt_fb[$j]++;
			}
		}
   $total_fbook=implode(", ", $book_free); 
   
   $free_cust = "select sum(a_bookinginfo.b_qty_people) as total_fc, sum(l_hour.hour_calculate) as total_fh from a_bookinginfo,l_hour where a_bookinginfo.book_id in (".$total_fbook.") " .
   		"and a_bookinginfo.b_book_hour=l_hour.hour_id ";
   $rsfree_cust = $obj->getResult($free_cust);
   $fcust[$j]=$rsfree_cust[0]["total_fc"];
   $fhours[$j]=$rsfree_cust[0]["total_fh"];
   $totalfcust = $totalfcust+$fcust[$j];
   $totalfhours = $totalfhours+$fhours[$j];

   $book_free=null;
   $total_fbook=null;
   }
   
   if($branchdetail){
    		$branchSrdString = implode(",", $branchdetail); 
   }
 	        	    	     
   $pAmount=0;
   $tpAmount=0;
   $totalc=0;
   $totalh=0;
   $tIperH=0;
   $tHoerC=0;
   for($j=0; $j<$rsBranch["rows"]; $j++){?>
   <tr height="20">
        <td align="left"><?=$rsBranch[$j]["branch_name"]?></td>
        <td align="right"><a href="javascript:;" style="text-decoration:none; color:#000000;"   onClick="openrDetail(<?="$begin_date,$end_date,".$rsBranch[$j]["branch_id"].",$branch_id".",'false'".",'Overview',".$city_id?>)"><?=number_format($amount[$j],2,".",",")?></a></td>
        <?if($total!=0){
        		$pAmount = ($amount[$j]*100)/$total;
        		$tpAmount = $tpAmount+$pAmount;
        	}else{$pAmount=0.00;}?>
        <td align="right"><?=number_format($pAmount,2,".",",")?></td>
        <?$rs_tthour = $obj->gettthour($rsBranch[$j]["branch_id"],$begin_date,$end_date);
        		if($rs_tthour){
        			$totalh = $totalh+number_format($rs_tthour[0]["total"],2,".",""); 
        		}else{$rs_tthour[0]["total"]="0.00";}?>
        <td align="right"><?=number_format($rs_tthour[0]["total"],2,".",",")?></td>
        
        <td align="right"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,".$rsBranch[$j]["branch_id"].",false,".'0'.",'freecust',".$city_id?>)"><?=number_format($fhours[$j],2,".",",")?></a></td>
        
        <?$rs_ttcs = $obj->getttcs($rsBranch[$j]["branch_id"],$begin_date,$end_date);
        		if($rs_ttcs){
        			$totalc = $totalc+$rs_ttcs[0]["qty"];
        		}else{$rs_ttcs[0]["qty"]="0";}?>
        <td align="right"><?=number_format($rs_ttcs[0]["qty"],2,".",",")?></td>
        
        <td align="right"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,".$rsBranch[$j]["branch_id"].",false,".'0'.",'freecust',".$city_id?>)"><?=number_format($fcust[$j],2,".",",")?></a></td>
        
        <td align="right"><?=number_format(($rs_ttcs[0]["qty"]/$totaldate),2,".",",")?></td>
        <td align="right"><?=number_format(($amount[$j]/$totaldate),2,".",",")?></td>
        <?if($rs_ttcs[0]["qty"]!=0){
        	$HperC = number_format(($rs_tthour[0]["total"]/$rs_ttcs[0]["qty"]),2,".",""); 
        }else{$HperC="0";}?>
        <td align="right"><?=number_format($HperC,2,".","")?></td>
        <?if(($rs_tthour[0]["total"]-$fhours[$j])!=0){
        	$IperH = number_format(($amount[$j]/($rs_tthour[0]["total"]-$fhours[$j])),2,".",""); 
        }else{$IperH="0";}?>
        <td align="right"><?=number_format($IperH,2,".",",")?></td>
        <?if(($rs_ttcs[0]["qty"]-$fcust[$j])!=0){
        			$tAIC = number_format(($amount[$j])/($rs_ttcs[0]["qty"]-$fcust[$j]),2,".","");
        }else{$tAIC="0.00";}?>
        <td align="right"><?=number_format($tAIC,2,".",",")?></td>
   </tr>

   <?}
   if($tpAmount==0){$tpAmount="0.00%";}else{$tpAmount="100%";}
   if(!$totalc){
   		$TotalAIC="0.00";
   }else{$TotalAIC=number_format($total/($totalc-$totalfcust),2,".",",");}
   ?>  
   <tr height="20">
       <td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total</b></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><a href="javascript:;" style="text-decoration:none; color:#000000;"  onClick="openrDetail(<?="$begin_date,$end_date,".$branch_id.",$branch_id".",'false'".",'Overview',".$city_id?>)"><?=number_format($total,2,".",",")?></a></b></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$tpAmount?></b></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=number_format($totalh,2,".",",")?></b></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,"."false,".'0'.",'freecust',".$city_id?>)"><?=number_format($totalfhours,2,".",",")?></a><b></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=($totalc)?number_format($totalc,2,".",","):"0.00";?></b></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,"."false,".'0'.",'freecust',".$city_id?>)"><?=($totalfcust)?number_format($totalfcust,2,".",","):"0.00";?></a></b></td> 
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=number_format(($totalc/$totaldate),2,".",",")?></b></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=number_format(($total/$totaldate),2,".",",")?></b></td>
   		<?if($totalc!=0){
   			$tHoerC=number_format($totalh/$totalc,2,".","");
   			$tIperH=number_format($total/($totalh-$totalfhours),2,".","");
   		}else{$tHoerC="0";}?>
   		<td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=number_format($tHoerC,2,".",",")?></b></td>
   		<td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=number_format($tIperH,2,".",",")?></b></td>
   		<td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$TotalAIC?></b></td>
   </tr>
   </table>
  </td>
 </tr>
 
 
  <tr>
 	<td valign="top" style="padding:10 20 20 20;" width="100%" align="left">
    <table width="100%" border="0" cellpadding="2" cellspacing="0">
 		<tr>
		  <td width="28%"></td>
		  <td width="10%"></td>
		  <td width="10%"></td>
		  <td width="4%"></td>
		  <td width="28%"></td>
		  <td width="10%"></td>
		  <td width="10%"></td>
		</tr>
		
		<tr height="20">
          <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid; value=2"><b>Payment Types</b></td>
          <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;">&nbsp;</td>
          <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;">&nbsp;</td>
          <td>&nbsp;</td>
          <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid; value=3"><b>Sales</b></td>
		  <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;">&nbsp;</td>
		  <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;">&nbsp;</td>	     
        </tr>
        
        <tr height="20">
          <td align="left" style="border-bottom:1px #000000 solid;"><b>Payment</b></td>
          <td align="center" style="border-bottom:1px #000000 solid;"><b>Amount</b></td>
          <td align="center" style="border-bottom:1px #000000 solid;"><b>(%)</b></td>
          <td></td> 
          <td align="left" style="border-bottom:1px #000000 solid;"><b>Categories</b></td>
          <td align="center" style="border-bottom:1px #000000 solid;"><b>Amount</b></td>
          <td align="center" style="border-bottom:1px #000000 solid;"><b>(%)</b></td>  
        </tr>
    	
    	<?
    	$sql = "select pay_id, pay_name from l_paytype order by pay_name asc";
        $rsPay = $obj->getResult($sql);
        
        $sql = "select pd_category_id, pd_category_name,pos_neg_value from cl_product_category " .
        		"order by pos_neg_value DESC , pd_category_name ASC";
        $rsProduct = $obj->getResult($sql);
        
    	?>
    	
    	<?
    	
    	  $sql1 = "select c_salesreceipt.salesreceipt_id " .
        		   "from a_bookinginfo, c_salesreceipt, bl_branchinfo " .
        		   "where a_bookinginfo.b_appt_date>='".$begin_date."' ";
          if($city_id){$sql1 .= "and a_bookinginfo.b_branch_id = bl_branchinfo.branch_id " .
          		                "and bl_branchinfo.city_id=".$city_id." ";}
          $sql1 .= "and a_bookinginfo.b_appt_date<='".$end_date."' " .
        		   "and a_bookinginfo.book_id=c_salesreceipt.book_id " .
        		   "and a_bookinginfo.b_set_cancel=0 " .
        		   "and c_salesreceipt.paid_confirm=1";	   
       	   		   if($branch_id){$sql1 .= " and a_bookinginfo.b_branch_id=".$branch_id." ";} 
       	     
       	  $sql2 = "select c_salesreceipt.salesreceipt_id " .
        			"from c_saleproduct, c_salesreceipt, bl_branchinfo " .
        			"where c_saleproduct.pds_date>='".$begin_date."' ";
          if($city_id){$sql2 .= "and c_saleproduct.branch_id = bl_branchinfo.branch_id " .
          		                "and bl_branchinfo.city_id=".$city_id." ";}
          $sql2 .=	"and c_saleproduct.pds_date<='".$end_date."' " .
        			"and c_saleproduct.pds_id=c_salesreceipt.pds_id " .
        			"and c_saleproduct.set_cancel=0 " .
        		    "and c_salesreceipt.paid_confirm=1";
        		   if($branch_id){$sql2 .= " and c_saleproduct.branch_id=".$branch_id." ";}
          
          $sql = "($sql1) union ($sql2) order by salesreceipt_id";
         // echo $sql;
          $rsSr = $obj->getResult($sql);
       
        $Srddetail=array();
          	for($j=0; $j<$rsSr["rows"]; $j++){
				$Srddetail[$j] = $rsSr[$j]["salesreceipt_id"];	
		    }
		$bookSrdString="";
		if($Srddetail){
    		$bookSrdString = implode(",", $Srddetail); 
 	    }
 	    
 	    //Get All Payment
 	    $pay_row=0;
 	    $Sumse=0;
 	    for($j=0; $j<$rsPay["rows"]; $j++){
 	    $sql = "select c_srpayment.pay_id,  sum(c_srpayment.pay_total) as total from c_srpayment " .
 	    		"where c_srpayment.salesreceipt_id in (".$bookSrdString.") " .
 	    		"and c_srpayment.pay_id=".$rsPay[$j]["pay_id"]."";
 	    //echo $sql."<br><br>"; 
 	    
 	    $rsMp = $obj->getResult($sql);
 	    	if($rsMp[0]["pay_id"]){
 	    		$totalse[$j]=$rsMp[0]["total"];
 	    	}else{
 	    		$sql = "select c_salesreceipt.pay_id, sum(c_salesreceipt.sr_total) as total from c_salesreceipt " .
 	    		"where c_salesreceipt.salesreceipt_id in (".$bookSrdString.") " .
 	    		"and c_salesreceipt.pay_id=".$rsPay[$j]["pay_id"]."";
 	    		$rsSe = $obj->getResult($sql);
 	    			if($rsSe[0]["pay_id"]){
 	    				$totalse[$j]=$rsSe[0]["total"];
 	    			}else{
 	    				$totalse[$j]=0;
 	    			}
 	    	}
 	    if($totalse[$j]>0){
 	    	$pay_id[$pay_row]=$rsPay[$j]["pay_id"];
 	    	$pay_name[$pay_row]=$rsPay[$j]["pay_name"];
 	    	$pay_price[$pay_row]=$totalse[$j];
 	    	$pay_row++;
 	    }
 	    	$Sumse = $Sumse+$totalse[$j];
 	    }
 	  
 	    //for($j=0; $j<$pay_row; $j++){
 	    	//$Sumse=$Sumse+$pay_price[$j];
 	    //}
 	    
 	    //Get All Product
 	    $pro_row=0;
 	    for($j=0; $j<$rsProduct["rows"]; $j++){
 	    	
 	    	$sql = "select c_srdetail.unit_price, c_srdetail.qty, c_srdetail.set_tax, " .
 	    			"c_srdetail.set_sc, cl_product_category.pd_category_id, " .
 	    			"cl_product_category.pos_neg_value " .
 	    			"from c_srdetail, cl_product, cl_product_category " .
 	    		    "where c_srdetail.salesreceipt_id in (".$bookSrdString.") " .
 	    		    "and c_srdetail.pd_id=cl_product.pd_id " .
 	    		    "and cl_product.pd_category_id=cl_product_category.pd_category_id " .
 	    		    "and cl_product_category.pd_category_id=".$rsProduct[$j]["pd_category_id"]."";
	    
          $rsAm = $obj->getResult($sql);
         // if($rsAm){
          	//echo $sql."<br><br>";
          //}
          if(!isset($Atotal[$j])){$Atotal[$j]=0;}
          			for($k=0; $k<$rsAm["rows"]; $k++){
          				
          				$rsAm[$k]["unit_price"]=$rsAm[$k]["unit_price"]*$rsAm[$k]["qty"];
          				if($rsAm[$k]["set_tax"]==1){
          					$set_tax = ($rsAm[$k]["unit_price"]*7)/100;	
          				}else{$set_tax=0;}
          				$t_total = $rsAm[$k]["unit_price"]+$set_tax;
          				if($rsAm[$k]["set_sc"]==1){
          					$set_sc = ($t_total*10)/100; 
          				}else{$set_sc=0;}
          				$total_amount[$k] = $t_total+$set_sc;
          				
          				//if($rsAm[$k]["pos_neg_value"]==0){
          					//$total_amount[$k]=-$total_amount[$k];
          				//}
          				
          				$Atotal[$j] = $Atotal[$j]+$total_amount[$k]; 	         				
          			}
       if($Atotal[$j]>0){
 	    	$pro_name[$pro_row]=$rsProduct[$j]["pd_category_name"];
 	    	$pro_id[$pro_row]=$rsProduct[$j]["pd_category_id"];/////category id /////////////
 	    	$pro_posneg[$pro_row]=$rsProduct[$j]["pos_neg_value"];
 	    	if($pro_posneg[$pro_row]==0){
 	    		$posneg[$pro_row]="-";
 	    	}
 	    	$pro_price[$pro_row]=$Atotal[$j];
 	    	$pro_row++;
 	   }
          			//$Stotal = $Stotal+$Atotal[$j];
       }
       
       $Stotal=0;
       $RStotal=0;
       $pos_total=0;
       $neg_total=0;
       for($j=0; $j<$pro_row; $j++){
 	    	$Stotal=$Stotal+$pro_price[$j];
 	    	if($pro_posneg[$j]==1){
 	    		$RStotal=$RStotal+$pro_price[$j];
 	    		$pos_total=$pos_total+$pro_price[$j];
 	    	}else{
 	    		$RStotal=$RStotal-$pro_price[$j];
 	    		$neg_total=$neg_total+$pro_price[$j];
 	    	}
 	    }
 	    
 	    //if($rsPay["rows"]>$rsProduct["rows"]){
        	//$trow = $rsPay["rows"];
        	//}else{$trow=$rsProduct["rows"];}

     if($pay_row>$pro_row){
        	$trow = $pay_row;
        	$pro_status=true;
       }else{
       		$trow=$pro_row;
       		$pay_status=true;
       } 

$pay_status=true;
$pro_status=true;
$neg_status=true;
$pay_count = 0;
$sta_pay=true;
$sta_pro=true;
$allstatus=true;

if($Sumse==0){$tSumse="0.00%";}else{$tSumse="100%";}
if($Stotal==0){$tStotal="0.00%";}else{$tStotal="100%";}
if($pos_total==0){$p_pos_total="0.00%";}else{$p_pos_total="100%";}
if($neg_total==0){$p_neg_total="0.00%";$neg_status=false;}else{$p_neg_total="100%";}

    	for($j=0; $j<$trow; $j++){
    		
    	?>
    	
    	
    	<tr height="20">
          <?if($pay_count!=$pay_row){?>
          <td align="left"><?if($pay_id[$pay_count]!=1){//$rsPay[$j]["pay_id"]!=1){
				echo $pay_name[$pay_count];//$rsPay[$j]["pay_name"];
        	}else{echo "Unknown";}?></td>
          <td align="right"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,'flase',".$pay_id[$pay_count].",'payment',".$city_id?>)"><?if($pay_name[$pay_count]){//$rsPay[$j]["pay_name"]){payment
          		echo number_format($pay_price[$pay_count],2,".",",");//echo number_format($totalse[$j],2,".",",");here for the onclick payment
    	    }//else{echo "";}?></a></td>
          <td align="right"><?if($pay_name[$pay_count]/*$rsPay[$j]["pay_name"]*/){if($Sumse!=0){
        		echo number_format(($pay_price[$pay_count]*100)/$Sumse,2,".",",");//echo number_format(($totalse[$j]*100)/$Sumse,2,".",",");
        	}/*else{echo "0.00";}*/}//else{echo "";}?></a></td>
          <?}elseif($sta_pay){?>
          	<td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total1</b></td>
          	<td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,"."false,".'0'.",'payment',".$city_id?>)"><b><?=number_format($Sumse,2,".",",")?></b></a></td> <!--payment-->
          	<td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$tSumse?></b></td>		
          <?
             $sta_pay=false;
             $pay_status=false;
          }else{?>
 			<td></td>
 			<td></td>
 			<td></td>
 		  <?}$pay_status=false;?>         
          <?if(!isset($pro_posneg[$j])){$pro_posneg[$j]=0;}?>
          <?if(!$pro_posneg[$j] && $pro_posneg[$j-1]){
		   	if($pay_count<$pay_row){
			$pay_count++;	
			}	
          ?>
            <?if($neg_status){?>
            <td align="left"></td>
            <td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total</b></td>
          	<td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; name=n5 value=5 color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'0'.",'false'".",'pos',".$city_id?>)"><b><?=number_format($pos_total,2,".",",")?></b></a></td>
          	<td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$p_pos_total?></b></td>    
          	<?}else{?>
            <td align="left"></td>
            <td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total<??></b></td>
            <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; name=n5 value=5 color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'0'.",'false'".",'Sale',".$city_id?>)"><b><?=number_format($RStotal,2,".",",")?></b></a></td> <!--sale-->
            <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$tStotal?></b></td> 
   		    <?$pro_status=false;}?>	
          	</tr>
          	
          	<tr height="20">
          	<?
          	//if($pay_id[$pay_count]){
          	if(isset($pay_id[$pay_count])){
          	?>
          	<td align="left"><?if($pay_id[$pay_count]!=1){
				echo $pay_name[$pay_count];
        	}else{echo "Unknown";}?></td>
          	<td align="right"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,'flase',".$pay_id[$pay_count].",'payment',".$city_id?>)"><?if($pay_name[$pay_count]){//payment
          		echo number_format($pay_price[$pay_count],2,".",",");
    	    }?></a></td>
          	<td align="right"><?if($pay_name[$pay_count]){if($Sumse!=0){
        		echo number_format(($pay_price[$pay_count]*100)/$Sumse,2,".",",");
        	}}?></td>
        	
        	<?}elseif($sta_pay){?>
        		<td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total</b></td>
          		<td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".$branch_id.",".'0'.",'payment',".$city_id?>)"><b><?=number_format($Sumse,2,".",",")?></b></a></td>    <!--payment-->
          		<td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$tSumse?></b></td>
        	<?$sta_pay=false;}else{?>
        		<td></td>
 				<td></td>
 				<td></td>	
        	<?}?>
        	
          <?}?>
          
          <?
          if(!isset($pro_name[$j])){$pro_name[$j]="";}
          if(!isset($posneg[$j])){$posneg[$j]="";}
          ?>
          
          <?if($j<$pro_row){?>
          <td align="left"></td>
          <td align="left"><?if($pro_name[$j]!=""){
				echo $pro_name[$j];
        	}?></td>
          <td align="right">
          <a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".$pro_id[$j].",'false'".",'Sale',".$city_id?>)"> <!-- $pro_id[$j]  sale $rss[$j]["pd_id"] -->
          <?=$posneg[$j]?>
          <?if($pro_name[$j]){echo number_format($pro_price[$j],2,".",",");}?>
          </td></a>
          <td align="right"><?if($pro_name[$j]){if($Stotal!=0){
          	if($pro_posneg[$j]){
        		echo number_format(($pro_price[$j]*100)/$pos_total,2,".",",");
          	}else{
          		echo number_format(($pro_price[$j]*100)/$neg_total,2,".",",");
          	}
        	}}?></td>  
		<?}elseif($j==$pro_row && $neg_status){?>
		  <td align="left"></td>
		  <td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total</b></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; name=n5 value=5 color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'0'.",'false'".",'neg',".$city_id?>)"><b><?if($neg_total){?>-<?}?><?=number_format($neg_total,2,".",",")?></b></a></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$p_neg_total?></b></td>  			
		<?$neg_status=false;
		}elseif($pro_status){?>
		  <td align="left"></td>
   		  <td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total</b></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; name=n5 value=5 color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'0'.",'false'".",'Sale',".$city_id?>)"><b><?=number_format($RStotal,2,".",",")?></b></a></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$tStotal?></b></td>
		<?$pro_status=false;}?>        
        </tr>
    <?
    	if($pay_count<$pay_row){
		$pay_count++;	
		}
    }
    ?>
   <?
   if($pay_row==$pro_row){
   $pay_status=true;
   }
   ?>
   
        <tr height="20">
   <?if($pay_status && $sta_pay){?>
          <td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total</b></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".$branch_id.",".'0'.",'payment',".$city_id?>)"><b><?=number_format($Sumse,2,".",",")?></b></a></td>        <!--payment-->
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$tSumse?></b></td>
          <td></td>
   <?}else{?>
   		  <td></td>
   		  <td></td>
   		  <td></td>
   		  <td></td>
   <?}?>
   <?if($pro_status){?>
   		  <?if($neg_status){?>
          <td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total<??></b></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; name=n5 value=5 color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'0'.",'false'".",'neg',".$city_id?>)"><b><?if($neg_total){?>-<?}?><?=number_format($neg_total,2,".",",")?></b></a></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$p_neg_total?></b></td>  
   		  <?}else{?>
   		  <td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total<??></b></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; name=n5 value=5 color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'0'.",'false'".",'Sale',".$city_id?>)"><b><?=number_format($RStotal,2,".",",")?></b></a></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$tStotal?></b></td> 
   		  <?$allstatus=false;}?>
   		  </tr>
   		  <?if(1){?>
   		  <tr height="20">
   		  <td></td>
   		  <td></td>
   		  <td></td>	
   		  <td></td>
   		  <?if($allstatus){?>
   		  <td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total<??></b></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; name=n5 value=5 color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'0'.",'false'".",'Sale',".$city_id?>)"><b><?=number_format($RStotal,2,".",",")?></b></a></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$tStotal?></b></td>  
   		  <?}else{?>
   		  <td></td>
   		  <td></td>
   		  <td></td>	
   		  <?}?>
   		  <?}?>
   <?}else{?>
   		  <td></td>
   		  <td></td>
   		  <td></td>	  
   <?}?>
        </tr>
        
	</table>
    </td>
  </tr>

 <tr>
 	<td valign="top" style="padding:10 20 20 20;" width="100%" align="left">	
	<table width="70%" border="0" cellpadding="2" cellspacing="0">
	<tr>
		<td width="26%"></td>
		<td width="11%"></td>
		<td width="11%"></td>
		<td width="11%"></td>
		<td width="11%"></td>

	</tr>
	<tr height="20" bgcolor="#D3D3D3">
        <td colspan="5" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Marketing Report</b></td>
    </tr>

    <tr height="20">
        <td style="border-bottom:1px #000000 solid;"><b>Type</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Qty</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Total Customer</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>Amount</b></td>
        <td align="center" style="border-bottom:1px #000000 solid;"><b>(%)</b></td>
    </tr>
    
    <?    
        //Marketing used
        
		//table l_marketingcode
		$sql1 = "select l_marketingcode.category_id as type_id,l_mkcode_category.category_name as type_name," .
				"l_marketingcode.mkcode_id as code_id,l_marketingcode.sign as code_name," .
				"\"l_marketingcode\" as tb_name," .
				"a_bookinginfo.book_id,c_bpds_link.bpds_id,count(a_bookinginfo.book_id) as used_qty,sum(a_bookinginfo.b_qty_people) as used_person," .
				"(select sum(c_salesreceipt.sr_total) from c_salesreceipt where c_salesreceipt.book_id = a_bookinginfo.book_id and c_salesreceipt.paid_confirm=1) as used_amount ";
				//"(select sum(c_salesreceipt.sr_total) from c_salesreceipt where c_salesreceipt.book_id = a_bookinginfo.book_id) as used_amount ,(select count(g_gift.book_id) from g_gift where g_gift.book_id = a_bookinginfo.book_id) as count_gift ";
		
		
		$sql1 .= "from a_bookinginfo,l_marketingcode,l_mkcode_category,c_bpds_link ";
		if($city_id){$sql1 .= ",bl_branchinfo ";}
		$sql1 .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$begin_date==$end_date){$sql1 .= "and a_bookinginfo.b_appt_date='".$begin_date."' ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$begin_date."' and a_bookinginfo.b_appt_date<='".$end_date."' ";}
		if($branch_id){$sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($city_id){
			$sql1 .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
			$sql1 .= "and bl_branchinfo.city_id=".$city_id." ";
		}
		$sql1 .= "and a_bookinginfo.mkcode_id=l_marketingcode.mkcode_id ";
		$sql1 .= "and a_bookinginfo.mkcode_id!=1 ";
		$sql1 .= "and l_mkcode_category.category_id=l_marketingcode.category_id ";
		$sql1 .= "and c_bpds_link.tb_id=a_bookinginfo.book_id ";
		$sql1 .= "and c_bpds_link.tb_name like \"a_bookinginfo\" ";
		//$sql1 .= ($status!=2)?"and (select count(g_gift.book_id) from g_gift where g_gift.book_id = a_bookinginfo.book_id) = 0 ":"";
		
		$sql1 .= "group by a_bookinginfo.book_id ";
		$sql1 .= "order by l_mkcode_category.category_name,l_marketingcode.sign ";
		//echo $sql1;
		
		//table l_marketingcode-product
		$sql2 = "select l_marketingcode.category_id as type_id,l_mkcode_category.category_name as type_name," .
				"l_marketingcode.mkcode_id as code_id,l_marketingcode.sign as code_name," .
				"\"l_marketingcode\" as tb_name," .
				"c_saleproduct.pds_id,c_bpds_link.bpds_id,count(c_saleproduct.pds_id) as used_qty,count(c_saleproduct.pds_id) as used_person," .
				"(select sum(c_salesreceipt.sr_total) from c_salesreceipt where c_salesreceipt.pds_id = c_saleproduct.pds_id and c_salesreceipt.paid_confirm=1) as used_amount ";
				
		
		
		$sql2 .= "from c_saleproduct,l_marketingcode,l_mkcode_category,c_bpds_link ";
		if($city_id){$sql2 .= ",bl_branchinfo ";}
		$sql2 .= "where c_saleproduct.set_cancel=0 ";
		if($end_date==false||$begin_date==$end_date){$sql2 .= "and c_saleproduct.pds_date='".$begin_date."' ";}
		else{$sql2 .= "and c_saleproduct.pds_date>='".$begin_date."' and c_saleproduct.pds_date<='".$end_date."' ";}
		if($branch_id){$sql2 .= "and c_saleproduct.branch_id=".$branch_id." ";}
		if($city_id){
			$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
			$sql2 .= "and bl_branchinfo.city_id=".$city_id." ";
		}
		$sql2 .= "and c_saleproduct.mkcode_id=l_marketingcode.mkcode_id ";
		$sql2 .= "and c_saleproduct.mkcode_id!=1 ";
		$sql2 .= "and l_mkcode_category.category_id=l_marketingcode.category_id ";
		$sql2 .= "and c_bpds_link.tb_id=c_saleproduct.pds_id ";
		$sql2 .= "and c_bpds_link.tb_name like \"c_saleproduct\" ";
		//$sql2 .= ($status!=2)?"and (select count(g_gift.id_sold) from g_gift where g_gift.id_sold = c_saleproduct.pds_id) = 0 ":"";
		
		$sql2 .= "group by c_saleproduct.pds_id ";
		
		$sql = "($sql1) union ($sql2) order by type_name,code_name";
		//echo $sql;
		$rsuMarket = $obj->getResult($sql);
		
		$cntt=0;
		for($j=0; $j<$rsuMarket["rows"]; $j++){
			if(!isset($t_type[$cntt])){$t_type[$cntt]=0;}
			if(!isset($rsuMarket[$j]["type_id"])){$rsuMarket[$j]["type_id"]=0;}
			if(!isset($rsuMarket[$j+1]["type_id"])){$rsuMarket[$j+1]["type_id"]=0;}
			$t_type[$cntt]+=$rsuMarket[$j]["used_amount"];
			if($rsuMarket[$j]["type_id"]!=$rsuMarket[$j+1]["type_id"]){
				$cntt++;
			}
		}
		$cntt=0;
		$umQty=0;
		$umPer=0;
		$umAmount=0;
		$t_usedqtypertype=0;
		$usedqtypertype=0;
		$usedpersonpertype=0;
		$usedpertype=0;
		$t_usedpertype=0;
		$t_usedpersonpertype=0;
		for($j=0; $j<$rsuMarket["rows"]; $j++){
			$umQty = $umQty+$rsuMarket[$j]["used_qty"];
			$umPer = $umPer+$rsuMarket[$j]["used_person"];
        	$umAmount = $umAmount+$rsuMarket[$j]["used_amount"];
      if(!isset($rsuMarket[$j+1]["code_id"])){$rsuMarket[$j+1]["code_id"]=0;}
      if($rsuMarket[$j]["code_id"]!=$rsuMarket[$j+1]["code_id"]){
					$usedqtypertype+=$umQty;
					$usedpersonpertype+=$umPer; 
					$usedpertype+=$umAmount;
      ?>
    
    <tr height="20">
    	<td align="left"><?=$rsuMarket[$j]["code_name"]?></td>
        <td align="right"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'market',".$city_id.",'false',".$rsuMarket[$j]["code_id"].",".$rsuMarket[$j]["type_id"].",'l_marketingcode'".",'amount','0'"?>)"><?=number_format($umQty,0,".",",")?></a></td>
        <td align="right"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'market',".$city_id.",'false',".$rsuMarket[$j]["code_id"].",".$rsuMarket[$j]["type_id"].",'l_marketingcode'".",'amount','0'"?>)"><?=number_format($umPer,0,".",",")?></a></td>
        <td align="right"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'market',".$city_id.",'false',".$rsuMarket[$j]["code_id"].",".$rsuMarket[$j]["type_id"].",'l_marketingcode'".",'amount','0'"?>)"><?=number_format($umAmount,2,".",",")?></a></td>
        <td align="right"><?=($t_type[$cntt]==0)?"0.00":number_format(($umAmount*100)/$t_type[$cntt],2,".",",")?></td>
    </tr>
<?if($rsuMarket[$j]["type_id"]!=$rsuMarket[$j+1]["type_id"]){?>
	<tr height="20">	
		<td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$rsuMarket[$j]["type_name"]?></b></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'market',".$city_id.",'false',".'0'.",".$rsuMarket[$j]["type_id"].",'l_marketingcode'".",'amount','0'"?>)"><b><?=number_format($usedqtypertype,0,".",",")?></b></a></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'market',".$city_id.",'false',".'0'.",".$rsuMarket[$j]["type_id"].",'l_marketingcode'".",'amount','0'"?>)"><b><?=number_format($usedpersonpertype,0,".",",")?></b></a></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'market',".$city_id.",'false',".'0'.",".$rsuMarket[$j]["type_id"].",'l_marketingcode'".",'amount','0'"?>)"><b><?=number_format($usedpertype,2,".",",")?></b></a></td>	
		<td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$usedpertype?"100%":"0.00%"?></b></td>
	</tr>
<?
	$t_usedqtypertype+=$usedqtypertype;$t_usedpertype+=$usedpertype;$t_usedpersonpertype+=$usedpersonpertype;
	$usedqtypertype = 0; $usedpertype = 0;$usedpersonpertype = 0;
  	$cntt++;
  }

$umQty=0;
$umPer=0;
$umAmount=0;
      }   
}?>
	<tr height="20">	
		<td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total</b></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,"."'false','false'".",'total',".$city_id.",'false',".'0'.",".$rsuMarket[$j]["type_id"].",'l_marketingcode'".",'amount','0'"?>)"><b><?=number_format($t_usedqtypertype,2,".",",")?></b></a></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,"."'false','false'".",'total',".$city_id.",'false',".'0'.",".$rsuMarket[$j]["type_id"].",'l_marketingcode'".",'amount','0'"?>)"><b><?=number_format($t_usedpersonpertype,2,".",",")?></b></a></td>
        <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,"."'false','false'".",'total',".$city_id.",'false',".'0'.",".$rsuMarket[$j]["type_id"].",'l_marketingcode'".",'amount','0'"?>)"><b><?=number_format($t_usedpertype,2,".",",")?></b></a></td>	
		<td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?=$t_usedpertype?"100%":"0.00%"?></b></td>
	</tr>
     
   </table> 
  </td>
 </tr>

 <tr>
 	<td valign="top" style="padding:10 20 20 20;" width="100%" align="left" >
    <table width="70%" border="0" cellpadding="2" cellspacing="0" style="page-break-before: always">
 		<tr>
		  <td width="11%"></td>
		  <td width="11%"></td>
		  <td width="11%"></td>
		  <td width="4%"></td>
		  <td width="11%"></td>
		  <td width="11%"></td>
		  <td width="11%"></td>
		</tr>
		
		<tr height="20">
          <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Client</b></td>
          <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;">&nbsp;</td>
          <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;">&nbsp;</td>
          <td></td>
          <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Resident/Visitor</b></td>
          <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;">&nbsp;</td>
          <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;">&nbsp;</td>     
        </tr>
        
        <tr height="20">
          <td align="left" style="border-bottom:1px #000000 solid;"><b>Gender</b></td>
          <td align="center" style="border-bottom:1px #000000 solid;"><b>Amount</b></td>
          <td align="center" style="border-bottom:1px #000000 solid;"><b>(%)</b></td>
          <td></td> 
          <td align="left" style="border-bottom:1px #000000 solid;"><b>Category</b></td>
          <td align="center" style="border-bottom:1px #000000 solid;"><b>Amount</b></td>
          <td align="center" style="border-bottom:1px #000000 solid;"><b>(%)</b></td> 
        </tr>
	<?
	$total_male=0;
	$total_female=0;

	for($j=0; $j<$rs_gender["rows"];$j++){
		$total_male=$total_male+$rs_gender[$j]["mqty"];
		$total_female=$total_female+$rs_gender[$j]["fqty"];
	}
	$total_gender=$total_male+$total_female;
		
		for($j=0; $j<2; $j++){

	?>

     <?
        $total_res=$rsres["rows"];
        $total_vis=$rsvis["rows"];
        $total_cate=$total_res+$total_vis;
        if($total_cate){
        $Ptotal_res=number_format(($total_res*100)/$total_cate,2,".",",");
        $Ptotal_vis=number_format(($total_vis*100)/$total_cate,2,".",",");
        }else{$Ptotal_res="0.00";$Ptotal_vis="0.00";}
     ?>
     
     <?if($j==0){?>
        <tr height="20">
          <td align="left">Male</td>
          <td align="right"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false',"."'gender',".$city_id.",'1'"?>)"><?=$total_male?number_format($total_male,0,".",","):0?></a></td>
          <?if($total_gender!=0){?>
          <td align="right"><?=number_format(($total_male*100)/$total_gender,2,".",",")?></td>
          <?}else{?>
          <td align="right">0.00</td>
          <?}?>
          <td></td>
          <td align="left">Resident</td>	
          <td align="right"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'resident',".$city_id.",'false'"?>)"><?=$total_res?number_format($total_res,0,".",","):0?></td>
          <td align="right"><?=number_format($Ptotal_res,2,".",",")?></td>
        </tr>
     <?}?>
     <?if($j==1){?>   
        <tr height="20">
          <td align="left">Female</td>	
          <td align="right"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'gender',".$city_id.",'2'"?>)"><?=$total_female?number_format($total_female,0,".",","):0?></a></td>
          <?if($total_gender!=0){?>
          <td align="right"><?=number_format(($total_female*100)/$total_gender,2,".",",")?></td>
          <?}else{?>
          <td align="right">0.00</td>
          <?}?>
          <td></td>
          <td align="left">Visitor</td>	
          <td align="right"><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'visitor',".$city_id.",'false'"?>)"><?=$total_vis?number_format($total_vis,0,".",","):0?></td>
          <td align="right"><?=number_format($Ptotal_vis,2,".",",")?></td>
        </tr>
    <?}?>
    <?
		} 
    ?>    
        <tr height="20">
          <td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total</b></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; name=n5 value=5 color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'gender',".$city_id.",".'0'?>)"><b><?=number_format($total_gender,0,".",",")?></b></a></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?if($total_gender!=0){echo "100%";}else{echo "0.00%";}?></b></td>
          <td></td> 
          <td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total</b></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; name=n5 value=5 color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'place',".$city_id?>)"><b><?=number_format($total_cate,0,".",",")?></b></a></td>
          <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b><?if($total_cate!=0){echo "100%";}else{echo "0.00%";}?></b></td>
        </tr>
        
	</table>
    </td>
  </tr>
        
        <?
        $total_res=$rsres["rows"];
        $total_vis=$rsvis["rows"];
        $total_cate=$total_res+$total_vis;
        if($total_cate){
        $Ptotal_res=number_format(($total_res*100)/$total_cate,2,".",",");
        $Ptotal_vis=number_format(($total_vis*100)/$total_cate,2,".",",");
        }else{$Ptotal_res="0.00";$Ptotal_vis="0.00";}
        ?>
  
  <?
$useragent = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)) {
    $browser_version=$matched[1];
    $browser = 'IE';
} elseif (preg_match( '|Opera ([0-9].[0-9]{1,2})|',$useragent,$matched)) {
    $browser_version=$matched[1];
    $browser = 'Opera';
} elseif(preg_match('|Firefox/([0-9\.]+)|',$useragent,$matched)) {
        $browser_version=$matched[1];
        $browser = 'Firefox';
} elseif(preg_match('|Safari/([0-9\.]+)|',$useragent,$matched)) {
        $browser_version=$matched[1];
        $browser = 'Safari';
} else {
        // browser not recognized!
    $browser_version = 0;
    $browser= 'other';
}
  ?>
  <tr>
 	<td valign="top" style="padding:10 20 20 20;" width="100%" align="left">
    <table width="33%" border="0" cellpadding="2" cellspacing="0"  <?if($browser=="Firefox"){?>style="page-break-before: always"<?}?>>
 		<tr>
		  <td width="11%"></td>
		  <td width="11%"></td>
		   <td width="11%"></td>
		</tr>
		
		<tr height="20">
          <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CSI</b></td>
  		  <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;">&nbsp;</td>
		   <td bgcolor="#D3D3D3" align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;">&nbsp;</td>
  		</tr>
  		
  		<tr height="20">
          <td align="left" style="border-bottom:1px #000000 solid;"><b>Branch</b></td>
		  <td align="center" style="border-bottom:1px #000000 solid;"><b>Cust.</b></td>  
          <td align="center" style="border-bottom:1px #000000 solid;"><b>(%)</b></td>  
        </tr>
	<?$countnullcsi=0;$tpcsi=0;$total_cust=0;
		for($j=0; $j<$rsBranch["rows"]; $j++){
		$tcount[$j]=0;
	?>
        <tr height="20">
          <td align="left"><?=$rsBranch[$j]["branch_name"]?></td>
        <?
		        $sql = "select a_bookinginfo.b_branch_id,a_bookinginfo.b_appt_date," .
				"da_mult_th.therapist_id,l_employee.emp_code,l_employee.emp_nickname as therapist_name," .
				"max(da_mult_th.hour_id) as max_hour,f_csi.* ";
		$sql .= "from f_csi,a_bookinginfo,l_employee,da_mult_th,d_indivi_info,bl_branchinfo ";
		
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		$sql .= "and a_bookinginfo.book_id = f_csi.book_id ";
		$sql .= "and a_bookinginfo.book_id = d_indivi_info.book_id ";
		$sql .= "and a_bookinginfo.book_id = da_mult_th.book_id ";
		$sql .= "and d_indivi_info.book_id = da_mult_th.book_id ";
		$sql .= "and f_csi.book_id = da_mult_th.book_id ";
		$sql .= "and f_csi.book_id = d_indivi_info.book_id ";
		$sql .= "and d_indivi_info.indivi_id = da_mult_th.indivi_id ";
		$sql .= "and d_indivi_info.indivi_id = f_csi.indivi_id ";
		$sql .= "and l_employee.emp_id = da_mult_th.therapist_id ";
		$sql .= "and a_bookinginfo.b_appt_date >=\"$begin_date\" " .
				"and a_bookinginfo.b_appt_date <=\"$end_date\" ";
		$sql .= "and l_employee.emp_id <> 1 ";
		
		if($branchSrdString) {
		$sql .= "and a_bookinginfo.b_branch_id = ".$rsBranch[$j]["branch_id"]." " .
					"and bl_branchinfo.branch_id=a_bookinginfo.b_branch_id ";
		}
				
		$sql .= "group by f_csi.indivi_id ";	// only for 1 st therapist'll used to count in csi msg. value
		$sql .= "order by bl_branchinfo.branch_name";
		
		$rscsi = $obj->getResult($sql);
		
		$sql="select * from fl_csi_value";
		$rscsiv = $obj->getResult($sql);
		
		$sql = "select * from fl_csi_index where csii_active=1";
		$rscsii = $obj->getResult($sql);
		
		$total_cust+=$rscsi["rows"];
		?>  
		  <td align="right"><?=($rscsi["rows"])?$rscsi["rows"]:0?></td>
		  <td align="right">
		<?
		for($k=0;$k<$rscsi["rows"];$k++){
		$csi[$k]=0;
		$count[$k]=0;
		for($i=0;$i<$rscsiv["rows"];$i++){
		
	if($rscsiv[$i]["csiv_id"]!=1){
		if($rscsi[$k]["s_greeting"]==$rscsiv[$i]["csiv_id"]){$s_greetingp[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$s_greetingp[$k];}
		if($rscsi[$k]["s_manner"]==$rscsiv[$i]["csiv_id"]){$s_manner[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$s_manner[$k];}
		if($rscsi[$k]["s_attentive"]==$rscsiv[$i]["csiv_id"]){$s_attentive[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$s_attentive[$k];}
		if($rscsi[$k]["s_friendly"]==$rscsiv[$i]["csiv_id"]){$s_friendly[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$s_friendly[$k];}
		if($rscsi[$k]["s_driver"]==$rscsiv[$i]["csiv_id"]){$s_driver[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$s_driver[$k];}
		if($rscsi[$k]["q_mg"]==$rscsiv[$i]["csiv_id"]){$q_mg[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$q_mg[$k];}
		if($rscsi[$k]["q_tr"]==$rscsiv[$i]["csiv_id"]){$q_tr[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$q_tr[$k];}
		if($rscsi[$k]["q_value"]==$rscsiv[$i]["csiv_id"]){$q_value[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$q_value[$k];}
		if($rscsi[$k]["at_clean"]==$rscsiv[$i]["csiv_id"]){$at_clean[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$at_clean[$k];}
		if($rscsi[$k]["at_aroma"]==$rscsiv[$i]["csiv_id"]){$at_aroma[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$at_aroma[$k];}
		if($rscsi[$k]["at_m"]==$rscsiv[$i]["csiv_id"]){$at_m[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$at_m[$k];}
		if($rscsi[$k]["at_temp"]==$rscsiv[$i]["csiv_id"]){$at_temp[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$at_temp[$k];}
		if($rscsi[$k]["at_fac"]==$rscsiv[$i]["csiv_id"]){$at_fac[$k]=$rscsiv[$i]["csiv_value"];$count[$k]++;$csi[$k]=$csi[$k]+$at_fac[$k];}
	}
	    }
		//$csi[$k]=$csi[$k]/$count[$k];
		//$csi[$k] = ($s_greetingp[$k]+$s_manner[$k]+$s_attentive[$k]+$s_friendly[$k]
		//		   +$s_driver[$k]+$q_mg[$k]+$q_tr[$k]+$q_value[$k]+$at_clean[$k]
		//		   +$at_aroma[$k]+$at_m[$k]+$at_temp[$k]+$at_fac[$k])/$rscsii["rows"];
		
		//echo $csi[$k]."-".$count[$k]."<br>";
	if(!isset($allcsi[$j])){$allcsi[$j]=0;}	
		$allcsi[$j] = $allcsi[$j]+$csi[$k]; 
		$tcount[$j]=$tcount[$j]+$count[$k];
	}
	//if($allcsi[$j]!=0){
		//$tcis=$allcsi[$j]/$rscsi["rows"];
	if(isset($allcsi[$j])){
		$tcis=($tcount[$j]==0)?"0.00":$allcsi[$j]/$tcount[$j];
	}else{$tcis=0;}
    
    if($rsBranch[$j]["branch_id"]){
    	//echo number_format($tcis,2,".",",");
		?><a href="javascript:;" style="text-decoration:none; color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,".$rsBranch[$j]["branch_id"].",'false'".",'false'".",'csi',".$city_id?>)"><?=number_format($tcis,2,".",",");?></a><?
    }else{echo "";}
          ?></td>  
        </tr>
    
    <?
    		if($tcis==0){
    			$countnullcsi++;
    		}else{
    			if(!isset($tpcsi)){$tpcsi=0;}
    			$tpcsi = $tpcsi+$tcis;
    		}
		} 
		 
    if($rsBranch["rows"]-$countnullcsi==0){
    	$totalcisper = "0";
    }else{
    	$totalcisper = $tpcsi/($rsBranch["rows"]-$countnullcsi);
    }
    ?>  
  	
  		<tr height="20">
          <td align="left" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><b>Total</b></td>
           <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; name=n5 value=5 color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'csi',".$city_id?>)"><b><?=($total_cust)?$total_cust:0?></b></a></td>  
		  <td align="right" style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><a href="javascript:;" style="text-decoration:none; name=n5 value=5 color:#000000;" onClick="openrDetail(<?="$begin_date,$end_date,$branch_id,".'false'.",'false'".",'csi',".$city_id?>)"><b><?=number_format($totalcisper,2,".",",");?>%</b></a></td>  
        </tr>
      
   </table>
   </td>
 </tr> 
 
 <tr>
 	<td valign="top" style="padding:10 20 50 20;" width="100%" align="left">
	<table width="100%" border="0" cellpadding="2" cellspacing="0">
		<tr>
		   <td width="100%"></td>
	    </tr>
		<tr height="20">
    	   <td align="left" colspan="12"><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?></td>
  		</tr>
	</table>
	</td>
 </tr>

</p>
 
</table>
</span>


<?if($export=="print"){?>

<script type="text/javascript">
	window.print();
</script>
<?}?>