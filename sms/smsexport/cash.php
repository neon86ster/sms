<?
//$today = date("Ymd"); //today
//$today1 = date("j F Y");today long format 25,may,2012

$today = date("Ymd", strtotime("-1 day"));//yesterday
$today1 = date("j F Y",strtotime("-1 day")); //long date format day,month,year

mysql_connect("localhost", "root", "123456") or die(mysql_error());
echo "Connected to MySQL<br />";

mysql_select_db("sample") or die(mysql_error());
echo "Connected to Database<br />";
//

//



			
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
 
 $sql_cash="SELECT c_closing_receipt.*,c_account_deposit.account_num,c_account_deposit.name,c_account_deposit.account_name,c_account_deposit.template,bl_branchinfo.branch_name,l_account.pay_account_name,l_template.pay_template_name " .
						"FROM c_closing_receipt,c_account_deposit,l_account,l_template,bl_branchinfo " .
						"where closing_date='$today'and c_closing_receipt.closing_branch_id=c_account_deposit.branch_id and " .
						"c_account_deposit.account_num=l_account.pay_account_id and " .
						"c_account_deposit.template=l_template.pay_template_id and " .
						"c_account_deposit.branch_id=bl_branchinfo.branch_id ";
						//echo $sql_cash;
						 $cc = mysql_query($sql_cash);
						
						$rs_cash["rows"]=mysql_num_rows($cc);
								while($i = mysql_fetch_array($cc)){    
		  $rs_cash[] = array( 'total_cash_deposit'=>$i['total_cash_deposit'],
		 					 'account_name'=>$i['account_name'],
		 					  'account_num'=>$i['account_num'],
		 					  'pay_account_name'=>$i['pay_account_name'],
		 					   'pay_template_name'=>$i['pay_template_name'],
		 					   'branch_name'=>$i['branch_name'],
		 					   'closing_branch_id'=>$i['closing_branch_id'],
		 					   'template'=>$i['template'],
		  					  'name'=>$i['name']	);    }
												
						
						
						
	for($x=0;$x<$rs_cash["rows"];$x++){
					if($rs_cash[$x]["total_cash_deposit"]!="0" ){
											
				
				
		$content.= 	"<tr>" .
					"<td>".$rs_cash[$x]["account_name"]."</td>".
				    "<td>".$rs_cash[$x]["name"]."</td>".
					"<td>".number_format($rs_cash[$x]["total_cash_deposit"],2,".",",")."</td>";

		$content.= "<td>".$rs_cash[$x]["pay_account_name"]."</td>".
				   "<td>".$today1."</td>";

		$content.= "<td>Branches:".$rs_cash[$x]["branch_name"]."</td>".
				   "<td></td>";

		$content.= "<td>".$rs_cash[$x]["pay_template_name"]."</td>";
				
					
					}
					
				}						
///
				$sql_pc="SELECT c_closing_receipt.*,c_account.account_num,c_account.name,c_account.customer_name,c_account.template,bl_branchinfo.branch_name,l_account.pay_account_name,l_template.pay_template_name  " .
						"FROM c_closing_receipt,c_account,l_account,l_template,bl_branchinfo " .
						"where closing_date='$today'and c_closing_receipt.closing_branch_id=c_account.branch_id and " .
						"c_account.account_num=l_account.pay_account_id and " .
						"c_account.template=l_template.pay_template_id and " .
						"c_account.branch_id=bl_branchinfo.branch_id ";
						//echo $sql_pc;
						
						
						$ccc = mysql_query($sql_pc);
						
						$rs_pc["rows"]=mysql_num_rows($ccc);
								while($i = mysql_fetch_array($ccc)){    
		  $rs_pc[] = array( 'tranfer_pc'=>$i['tranfer_pc'],
		 					 'customer_name'=>$i['customer_name'],
		 					  'account_num'=>$i['account_num'],
		 					    'pay_account_name'=>$i['pay_account_name'],
		 					   'pay_template_name'=>$i['pay_template_name'],
		 					   'branch_name'=>$i['branch_name'],
		 					   'closing_branch_id'=>$i['closing_branch_id'],
		 					   'template'=>$i['template'],
		  					  'name'=>$i['name']	);    }
												
						
				for($y=0;$y<$rs_pc["rows"];$y++){
				
					if($rs_pc[$y]["tranfer_pc"]!="0" ){
					
				$content.= "<tr>".
						"<td>".$rs_pc[$y]["customer_name"]."</td>".
						"<td>".$rs_pc[$y]["name"]."</td>".
						"<td>".number_format($rs_pc[$y]["tranfer_pc"],2,".",",")."</td>";

				$content.="<td>".$rs_pc[$y]["pay_account_name"]."</td>".
						"<td>".$today1."</td>";

			$content.="<td>Branches:".$rs_pc[$y]["branch_name"]."</td>".
						"<td></td>";

			$content.="<td>".$rs_pc[$y]["pay_template_name"]."</td>";
					
					}
					
						
				}

		
									
 $content.= "</table>";
 echo $content;
 		
 file_put_contents("../excel cash.xls",$content);

?>