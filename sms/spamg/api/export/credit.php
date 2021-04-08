<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
$obj = new checker();


$today = date("Ymd"); //today
//$today = date("Ymd", strtotime("-1 day"));//yesterday
$begindate = $dateobj->convertdate(substr($today,0,4)."-".substr($today,4,2)."-".substr($today,6,2),"Y-m-d",$sdateformat);


				
$sqlbr= "select branch_id from bl_branchinfo where bl_branchinfo.branch_id<>1 and bl_branchinfo.branch_active=1" ;
		$aa = $obj->getResult($sqlbr);
		


//////////////////////////////		



			
 $content="<table>" .
 		"<tr>" .
 		"<td><b>Description</b></td>" .
 		"<td><b>CustomerRefFullName</b></td>" .
 		"<td><b>ItemRefFullName</b></td>" .
 		"<td><b>Rate</b></td>" .
 		"<td><b>AccountRef</b></td>" .
 		"<td><b>TxnDate</b></td>" .
 		"<td><b>InvoiceLineClassFullName</b></td>" .
 		"<td><b>InvoiceRefNumber</b></td>".
		"<td><b>Quentity</b></td>".
		"<td><b>TemplateRefFullName</b></td>".
 		"</tr>" ;
 for($xx=0;$xx<$aa["rows"];$xx++)
				{	
					
    	  $sql1 = "select c_salesreceipt.salesreceipt_id,a_bookinginfo.b_branch_id as branch_id " .
        		   "from a_bookinginfo, c_salesreceipt, bl_branchinfo " .
        		   "where a_bookinginfo.b_appt_date>='".$today."' ";
          if($city_id){$sql1 .= "and a_bookinginfo.b_branch_id = bl_branchinfo.branch_id " .
          		                "and bl_branchinfo.city_id=".$city_id." ";}
          $sql1 .= "and a_bookinginfo.b_appt_date<='".$today."' " .
        		   "and a_bookinginfo.book_id=c_salesreceipt.book_id " .
        		   "and a_bookinginfo.b_set_cancel=0 " .
        		   "and c_salesreceipt.paid_confirm=1";	   
       	   		   if($aa[$xx]["branch_id"]){$sql1 .= " and a_bookinginfo.b_branch_id=".$aa[$xx]["branch_id"]." ";} 
       	     
       	  $sql2 = "select c_salesreceipt.salesreceipt_id,c_saleproduct.branch_id as branch_id " .
        			"from c_saleproduct, c_salesreceipt, bl_branchinfo " .
        			"where c_saleproduct.pds_date>='".$today."' ";
          if($city_id){$sql2 .= "and c_saleproduct.branch_id = bl_branchinfo.branch_id " .
          		                "and bl_branchinfo.city_id=".$city_id." ";}
          $sql2 .=	"and c_saleproduct.pds_date<='".$today."' " .
        			"and c_saleproduct.pds_id=c_salesreceipt.pds_id " .
        			"and c_saleproduct.set_cancel=0 " .
        		    "and c_salesreceipt.paid_confirm=1";
        		   if($aa[$xx]["branch_id"]){$sql2 .= " and c_saleproduct.branch_id=".$aa[$xx]["branch_id"]." ";}
          
          $sql = "($sql1) union ($sql2) order by salesreceipt_id";
         // echo $sql."---";
      		

			$rs_test=$obj->getResult($sql);
				for($yy=0;$yy<$rs_test["rows"];$yy++)
					{
			
					
				if($rs_test[$yy]["branch_id"]==$aa[$xx]["branch_id"])
				{	
				$Srddetail[$yy] = $rs_test[$yy]["salesreceipt_id"];}
			
					
				}
				if($Srddetail){$bookSrdString = implode(", ", $Srddetail); }
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
$content.= 	"<tr>" .
 			"<td></td>" .
 			"<td>".$rs_data[$ss]["pay_category_name"].":".$rs_data[$ss]["pay_name"]."</td>".
 			"<td>".$rs_data[$ss]["pay_category_name"].":".$rs_data[$ss]["pay_name"]."</td>".
 			"<td>".number_format($rs_data[$ss]["pay_total"],2,".",",")."</td>".
 			"<td>".$rs_data[$ss]["pay_account_name"]."</td>".
 			"<td>".$begindate."</td>";
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
 $content.=	"<td>".$sub.":".$branch."</td>".
 			"<td></td>" .
 			"<td>1</td>" .
 			"<td>".$rs_data[$ss]["pay_template_name"]."</td>" .
 			"</tr>" ;
		}	
}		unset($Srddetail);
	}

/////voucher
$rs = $obj->getcrs($branch_id,$today,$today);
					for($i=0;$i<$rs["rows"];$i++){
				if($rs[$i]["set_cancel"]!=1 && $rs[$i]["paid_confirm"]==1){	
				$Srddetail[$i] = $rs[$i]["salesreceipt_id"];}
					}	
			if($Srddetail){
				    	$bookSrdString = implode(", ", $Srddetail); 
				 	}	
$sql_ptype="SELECT * FROM l_paytype_category where pay_category_id<>'1'";
					$rs_ptype=$obj->getResult($sql_ptype);
	for($i=0;$i<=$rs_ptype["rows"];$i++){
	
	$sql_data="select c_srpayment.*,l_paytype.pay_id,l_paytype.pay_name,pay_category_name,l_account.pay_account_name,l_template.pay_template_name  " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='".$rs_ptype[$i]["pay_category_id"]."'";
						$rs_data=$obj->getResult($sql_data);
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
						
						
				$content.="<tr>";
						if($rs_ptype[$i]["pay_category_id"]!="2"){
				$content.="<td>".$bid."</td>";
							
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
							
				$content.="<td>".$bpname."</td>";
						}else{
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
							
				$content.="<td></td>".
						"<td>".$pay_type[$k]."</td>";
						}
						
			$content.="	<td>"."Credit Card".":".$pay_type[$k]."</td>";
			$content.="	<td>".number_format($pay_value[$k],2,".",",")."</td>";
			$content.="	<td>".$rs_data[$k]["pay_account_name"]."</td>";
			$content.="	<td>".$begindate."</td>";
			$content.="	<td>".$sub.":".$branch."</td>";
			$content.="	<td></td>";
			$content.="	<td>1</td>";
			$content.="	<td>".$rs_data[$k]["pay_template_name"]."</td>";
			$content.="	</tr>";
						
						}
						
						}
	
	
	
	
	}
									
 $content.= "</table>";
 echo $content;
 		
 file_put_contents("excel credit.xls",$content);
?>