<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
$obj = new checker();


$today = date("Ymd"); 
$begindate = $dateobj->convertdate(substr($today,0,4)."-".substr($today,4,2)."-".substr($today,6,2),"Y-m-d",$sdateformat);
$rs_test = $obj->getcrs($branch_id,$today,$today);

			
 $content="<table>" .
 		"<tr>" .
 		"<td><b>CustomerRefFullName</b></td>" .
 		"<td><b>ItemRefFullName</b></td>" .
 		"<td><b>Amount</b></td>" .
 		"<td><b>Deposit To</b></td>" .
 		"<td><b>TxnDate</b></td>" .
 		"<td><b>InvoiceLineClassFullName</b></td>" .
  		"<td><b>InvoiceRefNumber</b></td>".
		"<td><b>TemplateRefFullName</b></td>".
 		"</tr>" ;
 
 $sql_cash="SELECT c_closing_receipt.*,c_account_deposit.account_num,c_account_deposit.name,c_account_deposit.account_name,c_account_deposit.template " .
						"FROM c_closing_receipt,c_account_deposit " .
						"where closing_date='$today'and c_closing_receipt.closing_branch_id=c_account_deposit.branch_id";
						//echo $sql_cash;
						$rs_cash=$obj->getResult($sql_cash);
						
	for($x=0;$x<$rs_cash["rows"];$x++){
					if($rs_cash[$x]["total_cash_deposit"]!="0" ){
											
				
				
		$content.= 	"<tr>" .
					"<td>".$rs_cash[$x]["account_name"]."</td>".
				    "<td>".$rs_cash[$x]["name"]."</td>".
					"<td>".number_format($rs_cash[$x]["total_cash_deposit"],2,".",",")."</td>";
					
								$br=$rs_cash[$x]["account_num"];
								$acc = $obj->getIdToText($br,"l_account","pay_account_name","pay_account_id");
					
		$content.= "<td>".$acc."</td>".
				   "<td>".$begindate."</td>";
						$bnd=$rs_cash[$x]["closing_branch_id"];
						$bran = $obj->getIdToText($bnd,"bl_branchinfo","branch_name","branch_id");
						$sub = $obj->getIdToText($bnd,"bl_branchinfo","branch_name3","branch_id");
						
		$content.= "<td>".$sub.":".$bran."</td>".
				   "<td></td>";
					
								$brr=$rs_cash[$x]["template"];
								$tem = $obj->getIdToText($brr,"l_template","pay_template_name","pay_template_id");
						
		$content.= "<td>".$tem."</td>";
				
					
					}
					
				}						
///
				$sql_pc="SELECT c_closing_receipt.*,c_account.account_num,c_account.name,c_account.customer_name,c_account.template " .
						"FROM c_closing_receipt,c_account " .
						"where closing_date='$today'and c_closing_receipt.closing_branch_id=c_account.branch_id";
						//echo $sql_pc;
						$rs_pc=$obj->getResult($sql_pc);
				for($y=0;$y<$rs_pc["rows"];$y++){
				
					if($rs_pc[$y]["tranfer_pc"]!="0" ){
					
				$content.= "<tr>".
						"<td>".$rs_pc[$y]["customer_name"]."</td>".
						"<td>".$rs_pc[$y]["name"]."</td>".
						"<td>".number_format($rs_pc[$y]["tranfer_pc"],2,".",",")."</td>";
					
								$br=$rs_pc[$y]["account_num"];
								$acc = $obj->getIdToText($br,"l_account","pay_account_name","pay_account_id");
						
				$content.="<td>".$acc."</td>".
						"<td>".$begindate."</td>";
						$bnd=$rs_pc[$y]["closing_branch_id"];
						$bran = $obj->getIdToText($bnd,"bl_branchinfo","branch_name","branch_id");
						$sub = $obj->getIdToText($bnd,"bl_branchinfo","branch_name3","branch_id");
			$content.="<td>".$sub.":".$bran."</td>".
						"<td></td>";
						
								$brr=$rs_pc[$y]["template"];
								$tem = $obj->getIdToText($brr,"l_template","pay_template_name","pay_template_id");
						
			$content.="<td>".$tem."</td>";
					
					}
					
						
				}

		
									
 $content.= "</table>";
 		
 file_put_contents("excel cash.xls",$content);
?>