<?
$today = date("Ymd"); //today
//$today = date("Ymd", strtotime("-1 day"));//yesterday
$today1 = date("j, F, Y"); //long date format day,month,year

mysql_connect("localhost", "root", "123456") or die(mysql_error());
echo "Connected to MySQL<br />";

mysql_select_db("sample") or die(mysql_error());
echo "Connected to Database<br />";


	
$sqlbr= "select branch_id from bl_branchinfo where bl_branchinfo.branch_id<>1 and bl_branchinfo.branch_active=1" ;
			
		 // echo $sql."----";
          $br = mysql_query($sqlbr);
          
		$aa["rows"]=mysql_num_rows($br);
	
		//echo $aa["rows"];
		
		while($i = mysql_fetch_array($br)){    
		  $aa[] = array( 'branch_id'=>$i['branch_id']	);    
		  }
			
/////branch
			
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

          //echo $sql."----<br>";
          $cc = mysql_query($sql);
          
		$rs_test["rows"]=mysql_num_rows($cc);
	
		//echo $rs_test["rows"];
	
		while($i = mysql_fetch_array($cc)){    
		  $rs_test[] = array( 'salesreceipt_id'=>$i['salesreceipt_id'],
		  					  'branch_id'=>$i['branch_id']	);   
		  					   }
	
			//echo $rs_test[$yy]["salesreceipt_id"]."<br>";
				for($yy=0;$yy<$rs_test["rows"];$yy++)
					{
			
					
				if($rs_test[$yy]["branch_id"]==$aa[$xx]["branch_id"])
				{
			
				$Srddetail[$yy] = $rs_test[$yy]["salesreceipt_id"];
				}
	
					
				}			if($Srddetail){$bookSrdString = implode(", ", $Srddetail); }
				//print_r($Srddetail);

if($rs_test[0]["branch_id"])
{	
					$sql_data="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name,a_bookinginfo.b_branch_id,bl_branchinfo.branch_name " .
									"from c_srpayment,l_paytype,a_bookinginfo,bl_branchinfo,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"c_srpayment.book_id=a_bookinginfo.book_id and " .
									"a_bookinginfo.b_branch_id=bl_branchinfo.branch_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
									
					$sql_data2="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name,c_saleproduct.branch_id,bl_branchinfo.branch_name " .
									"from c_srpayment,l_paytype,c_saleproduct,bl_branchinfo,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"c_srpayment.pds_id=c_saleproduct.pds_id and " .
									"c_saleproduct.branch_id=bl_branchinfo.branch_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
					$sql = "($sql_data) union ($sql_data2) ";
					
						$sub = mysql_query($sql);
						//echo $sql;
						$rs_data["rows"]=mysql_num_rows($sub);
						//echo $rs_data["rows"];
						
								while($i = mysql_fetch_array($sub)){    
		  $rs_data[] = array( 'pay_category_name'=>$i['pay_category_name'],
		  					  'pay_name'=>$i['pay_name'],
		  					  'book_id'=>$i['book_id'],
		  					  'pds_id'=>$i['pds_id'],
		  					  'pay_account_name'=>$i['pay_account_name'],
		  					  'pay_template_name'=>$i['pay_template_name'],
		  					  'branch_name'=>$i['branch_name'],
		  					  'pay_total'=>$i['pay_total'],
		  					  	); 
		  					 }
				//
				
		for($ss=0;$ss<$rs_data["rows"];$ss++)
		{
$content.= 	"<tr>" .
 			"<td></td>" .
 			"<td>".$rs_data[$ss]["pay_category_name"].":".$rs_data[$ss]["pay_name"]."</td>".
 			"<td>".$rs_data[$ss]["pay_category_name"].":".$rs_data[$ss]["pay_name"]."</td>".
 			"<td>".number_format($rs_data[$ss]["pay_total"],2,".",",")."</td>".
 			"<td>".$rs_data[$ss]["pay_account_name"]."</td>".
 			"<td>".$today1."</td>";

 $content.=	"<td>Branches:".$rs_data[$ss]["branch_name"]."</td>".
 			"<td></td>" .
 			"<td>1</td>" .
 			"<td>".$rs_data[$ss]["pay_template_name"]."</td>" .
 			"</tr>" ;
		}	
		unset($rs_data);	
}	

unset($rs_test);
unset($Srddetail);




}
///////voucher

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

          //echo $sql."----<br>";
          $cc = mysql_query($sql);
          
		$rs_test["rows"]=mysql_num_rows($cc);
	
		//echo $rs_test["rows"];
	
		while($i = mysql_fetch_array($cc)){    
		  $rs_test[] = array( 'salesreceipt_id'=>$i['salesreceipt_id'],
		  					  'branch_id'=>$i['branch_id']	);   
		  					   }
	
		
				for($yy=0;$yy<$rs_test["rows"];$yy++)
					{
				$Srddetail[$yy] = $rs_test[$yy]["salesreceipt_id"];
					}			

				if($Srddetail){$bookSrdString = implode(", ", $Srddetail); }
				//print_r($Srddetail); 	
				
				
			$sql_ptype="SELECT * FROM l_paytype_category where pay_category_id<>'1'";
				  $type = mysql_query($sql_ptype);
				  	$rs_ptype["rows"]=mysql_num_rows($type);
				
				//echo $rs_ptype["rows"]."<br>";
							while($i = mysql_fetch_array($type)){    
		  			$rs_ptype[] = array( 'pay_category_id'=>$i['pay_category_id']	);   
		  					   }	
		  
		  
		 for($i=0;$i<$rs_ptype["rows"];$i++){
		 	
		  			$sql_data="select c_srpayment.*,l_paytype.pay_id,l_paytype.pay_name,pay_category_name,l_account.pay_account_name,l_template.pay_template_name,bl_branchinfo.branch_name,c_bpds_link.tb_id as books_id,al_bookparty.bp_name  " .
									"from c_srpayment,l_paytype,a_bookinginfo,al_bookparty,c_bpds_link,bl_branchinfo,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"c_srpayment.book_id=a_bookinginfo.book_id and " .
									"a_bookinginfo.c_bp_id=al_bookparty.bp_id and " .
									"a_bookinginfo.book_id=c_bpds_link.tb_id and " .
									"a_bookinginfo.b_branch_id=bl_branchinfo.branch_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='".$rs_ptype[$i]["pay_category_id"]."'";
									  $data = mysql_query($sql_data);
									 //echo $sql_data."<br>";
							$rs_data["rows"]=mysql_num_rows($data);
									  
									  while($v = mysql_fetch_array($data)){    
		  								$rs_data[] = array( 'pay_category_id'=>$v['pay_category_id'],
		  													'book_id'=>$v['book_id'],
		  													'pds_id'=>$v['pds_id'],
		  													'pay_name'=>$v['pay_name'],	
		  													'pay_total'=>$v['pay_total'],
		  													'books_id'=>$v['books_id'],
		  													'bp_name'=>$v['bp_name'],
		  													'branch_name'=>$v['branch_name'],
		  													'pay_account_name'=>$v['pay_account_name'],
		  													'pay_template_name'=>$v['pay_template_name']	);   
		  					  			 }
									  
									  
									 //echo $rs_data[0]["pay_account_name"]."----";
		for($k=0;$k<$rs_data["rows"];$k++){
				
							if($rs_ptype[$i]["pay_category_id"]!="2")
							{
								

							
							//echo $rs_data[$k]["pay_name"];
							$pay_value[$k]=$rs_data[$k]["pay_total"];
						
						
				$content.="<tr>";
						if($rs_ptype[$i]["pay_category_id"]!="2"){
				$content.="<td>".$rs_data[$k]["books_id"]."</td>";
							

							
				$content.="<td>".$rs_data[$k]["bp_name"]."</td>";
						}
						
			$content.="	<td>"."Credit Card".":".$rs_data[$k]["pay_name"]."</td>";
			$content.="	<td>".$rs_data[$k]["pay_total"]."</td>";
			$content.="	<td>".$rs_data[$k]["pay_account_name"]."</td>";
			$content.="	<td>".$today1."</td>";
			$content.="	<td>Branches:".$rs_data[$k]["branch_name"]."</td>";
			$content.="	<td></td>";
			$content.="	<td>1</td>";
			$content.="	<td>".$rs_data[$k]["pay_template_name"]."</td>";
			$content.="	</tr>";
						
						}
						unset($pay_type);
						unset($pay_value);
					
						}					  
		  					   	
		  					   	
		  					   	
		  					   	

unset($rs_data);//erase array rs_data
}	


		echo $content;		
				
 $content.= "</table>";
 //file_put_contents("../excel credit.xls",$content);				

?>