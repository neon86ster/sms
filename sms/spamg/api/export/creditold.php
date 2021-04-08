<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
$obj = new checker();


$today = date("Ymd"); //today
//$today = date("Ymd", strtotime("-1 day"));//yesterday
$begindate = $dateobj->convertdate(substr($today,0,4)."-".substr($today,4,2)."-".substr($today,6,2),"Y-m-d",$sdateformat);

$rs_test = $obj->getcrs($branch_id,$today,$today);
				

		
		for($yy=0;$yy<$rs_test["rows"];$yy++)
			{
				
				if($rs_test[$yy]["branch_id"]==2)
				{if($rs_test[$yy]["set_cancel"]!=1 && $rs_test[$yy]["paid_confirm"]==1){	
				$Srddetail[$yy] = $rs_test[$yy]["salesreceipt_id"];}}
				if($rs_test[$yy]["branch_id"]==3)
				{if($rs_test[$yy]["set_cancel"]!=1 && $rs_test[$yy]["paid_confirm"]==1){	
				$Srddetail1[$yy] = $rs_test[$yy]["salesreceipt_id"];}}
				if($rs_test[$yy]["branch_id"]==5)
				{if($rs_test[$yy]["set_cancel"]!=1 && $rs_test[$yy]["paid_confirm"]==1){	
				$Srddetail2[$yy] = $rs_test[$yy]["salesreceipt_id"];}}
				if($rs_test[$yy]["branch_id"]==6)
				{if($rs_test[$yy]["set_cancel"]!=1 && $rs_test[$yy]["paid_confirm"]==1){	
				$Srddetail3[$yy] = $rs_test[$yy]["salesreceipt_id"];}}
				if($rs_test[$yy]["branch_id"]==7)
				{if($rs_test[$yy]["set_cancel"]!=1 && $rs_test[$yy]["paid_confirm"]==1){	
				$Srddetail4[$yy] = $rs_test[$yy]["salesreceipt_id"];}}
				if($rs_test[$yy]["branch_id"]==8)
				{if($rs_test[$yy]["set_cancel"]!=1 && $rs_test[$yy]["paid_confirm"]==1){	
				$Srddetail5[$yy] = $rs_test[$yy]["salesreceipt_id"];}}
				if($rs_test[$yy]["branch_id"]==9)
				{if($rs_test[$yy]["set_cancel"]!=1 && $rs_test[$yy]["paid_confirm"]==1){	
				$Srddetail6[$yy] = $rs_test[$yy]["salesreceipt_id"];}}
				if($rs_test[$yy]["branch_id"]==10)
				{if($rs_test[$yy]["set_cancel"]!=1 && $rs_test[$yy]["paid_confirm"]==1){	
				$Srddetail7[$yy] = $rs_test[$yy]["salesreceipt_id"];}}
				if($rs_test[$yy]["branch_id"]==11)
				{if($rs_test[$yy]["set_cancel"]!=1 && $rs_test[$yy]["paid_confirm"]==1){	
				$Srddetail8[$yy] = $rs_test[$yy]["salesreceipt_id"];}}
				if($rs_test[$yy]["branch_id"]==12)
				{if($rs_test[$yy]["set_cancel"]!=1 && $rs_test[$yy]["paid_confirm"]==1){	
				$Srddetail9[$yy] = $rs_test[$yy]["salesreceipt_id"];}}
	
			}
			//////
			if($Srddetail){$bookSrdString = implode(", ", $Srddetail); }	
			if($Srddetail1){$bookSrdString1 = implode(", ", $Srddetail1); }	
			if($Srddetail2){$bookSrdString2 = implode(", ", $Srddetail2); }	
			if($Srddetail3){$bookSrdString3 = implode(", ", $Srddetail3); }	
			if($Srddetail4){$bookSrdString4 = implode(", ", $Srddetail4); }	
			if($Srddetail5){$bookSrdString5 = implode(", ", $Srddetail5); }	
			if($Srddetail6){$bookSrdString6 = implode(", ", $Srddetail6); }	
			if($Srddetail7){$bookSrdString7 = implode(", ", $Srddetail7); }	
			if($Srddetail8){$bookSrdString8 = implode(", ", $Srddetail8); }	
			if($Srddetail9){$bookSrdString9 = implode(", ", $Srddetail9); }	
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
							//////chiang mai
	$sql_data1="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString1) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data1=$obj->getResult($sql_data1);
									//////sukhumvit 31
	$sql_data2="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString2) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data2=$obj->getResult($sql_data2);
							//echo $sql_data2;
					//////z office
	$sql_data3="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString3) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data3=$obj->getResult($sql_data3);
							
	//////Q-pattaya
	$sql_data4="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString4) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data4=$obj->getResult($sql_data4);
													//////P-Laguna
	$sql_data5="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString5) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data5=$obj->getResult($sql_data5);
												//////P-kata
	$sql_data6="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString6) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data6=$obj->getResult($sql_data6);
												//////P-kamala
	$sql_data7="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString7) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data7=$obj->getResult($sql_data7);
											//////B-Dream
	$sql_data8="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString8) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data8=$obj->getResult($sql_data8);
									//////B-Sukhumvit 51
	$sql_data9="select l_paytype.pay_id,sum(c_srpayment.pay_total)as pay_total,l_paytype.pay_name,pay_category_name,l_paytype_category.pay_category_name,c_srpayment.book_id,c_srpayment.pds_id,l_account.pay_account_name,l_template.pay_template_name " .
									"from c_srpayment,l_paytype,l_account,l_template,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString9) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_account_id=l_account.pay_account_id and " .
									"l_paytype.pay_template_id=l_template.pay_template_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='2' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data9=$obj->getResult($sql_data9);
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
for($ss=0;$ss<$rs_data1["rows"];$ss++)
		{
$content.= 	"<tr>" .
 			"<td></td>" .
 			"<td>".$rs_data1[$ss]["pay_category_name"].":".$rs_data1[$ss]["pay_name"]."</td>".
 			"<td>".$rs_data1[$ss]["pay_category_name"].":".$rs_data1[$ss]["pay_name"]."</td>".
 			"<td>".number_format($rs_data1[$ss]["pay_total"],2,".",",")."</td>".
 			"<td>".$rs_data1[$ss]["pay_account_name"]."</td>".
 			"<td>".$begindate."</td>";
 			if($rs_data1[$ss]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data1[$ss]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data1[$ss]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data1[$ss]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
 $content.=	"<td>".$sub.":".$branch."</td>".
 			"<td></td>" .
 			"<td>1</td>" .
 			"<td>".$rs_data1[$ss]["pay_template_name"]."</td>" .
 			"</tr>" ;
		}	
for($ss=0;$ss<$rs_data2["rows"];$ss++)
		{
$content.= 	"<tr>" .
 			"<td></td>" .
 			"<td>".$rs_data2[$ss]["pay_category_name"].":".$rs_data2[$ss]["pay_name"]."</td>".
 			"<td>".$rs_data2[$ss]["pay_category_name"].":".$rs_data2[$ss]["pay_name"]."</td>".
 			"<td>".number_format($rs_data2[$ss]["pay_total"],2,".",",")."</td>".
 			"<td>".$rs_data2[$ss]["pay_account_name"]."</td>".
 			"<td>".$begindate."</td>";
 			if($rs_data2[$ss]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data2[$ss]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data2[$ss]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data2[$ss]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
 $content.=	"<td>".$sub.":".$branch."</td>".
 			"<td></td>" .
 			"<td>1</td>" .
 			"<td>".$rs_data2[$ss]["pay_template_name"]."</td>" .
 			"</tr>" ;
		}	
for($ss=0;$ss<$rs_data3["rows"];$ss++)
		{
$content.= 	"<tr>" .
 			"<td></td>" .
 			"<td>".$rs_data3[$ss]["pay_category_name"].":".$rs_data3[$ss]["pay_name"]."</td>".
 			"<td>".$rs_data3[$ss]["pay_category_name"].":".$rs_data3[$ss]["pay_name"]."</td>".
 			"<td>".number_format($rs_data3[$ss]["pay_total"],2,".",",")."</td>".
 			"<td>".$rs_data3[$ss]["pay_account_name"]."</td>".
 			"<td>".$begindate."</td>";
 			if($rs_data3[$ss]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data3[$ss]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data3[$ss]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data3[$ss]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
 $content.=	"<td>".$sub.":".$branch."</td>".
 			"<td></td>" .
 			"<td>1</td>" .
 			"<td>".$rs_data3[$ss]["pay_template_name"]."</td>" .
 			"</tr>" ;
		}
		for($ss=0;$ss<$rs_data4["rows"];$ss++)
		{
$content.= 	"<tr>" .
 			"<td></td>" .
 			"<td>".$rs_data4[$ss]["pay_category_name"].":".$rs_data4[$ss]["pay_name"]."</td>".
 			"<td>".$rs_data4[$ss]["pay_category_name"].":".$rs_data4[$ss]["pay_name"]."</td>".
 			"<td>".number_format($rs_data4[$ss]["pay_total"],2,".",",")."</td>".
 			"<td>".$rs_data4[$ss]["pay_account_name"]."</td>".
 			"<td>".$begindate."</td>";
 			if($rs_data4[$ss]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data4[$ss]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data4[$ss]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data4[$ss]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
 $content.=	"<td>".$sub.":".$branch."</td>".
 			"<td></td>" .
 			"<td>1</td>" .
 			"<td>".$rs_data4[$ss]["pay_template_name"]."</td>" .
 			"</tr>" ;
		}	
		for($ss=0;$ss<$rs_data5["rows"];$ss++)
		{
$content.= 	"<tr>" .
 			"<td></td>" .
 			"<td>".$rs_data5[$ss]["pay_category_name"].":".$rs_data5[$ss]["pay_name"]."</td>".
 			"<td>".$rs_data5[$ss]["pay_category_name"].":".$rs_data5[$ss]["pay_name"]."</td>".
 			"<td>".number_format($rs_data5[$ss]["pay_total"],2,".",",")."</td>".
 			"<td>".$rs_data5[$ss]["pay_account_name"]."</td>".
 			"<td>".$begindate."</td>";
 			if($rs_data5[$ss]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data5[$ss]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data5[$ss]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data5[$ss]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
 $content.=	"<td>".$sub.":".$branch."</td>".
 			"<td></td>" .
 			"<td>1</td>" .
 			"<td>".$rs_data5[$ss]["pay_template_name"]."</td>" .
 			"</tr>" ;
		}		
		for($ss=0;$ss<$rs_data6["rows"];$ss++)
		{
$content.= 	"<tr>" .
 			"<td></td>" .
 			"<td>".$rs_data6[$ss]["pay_category_name"].":".$rs_data6[$ss]["pay_name"]."</td>".
 			"<td>".$rs_data6[$ss]["pay_category_name"].":".$rs_data6[$ss]["pay_name"]."</td>".
 			"<td>".number_format($rs_data6[$ss]["pay_total"],2,".",",")."</td>".
 			"<td>".$rs_data6[$ss]["pay_account_name"]."</td>".
 			"<td>".$begindate."</td>";
 			if($rs_data6[$ss]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data6[$ss]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data6[$ss]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data6[$ss]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
 $content.=	"<td>".$sub.":".$branch."</td>".
 			"<td></td>" .
 			"<td>1</td>" .
 			"<td>".$rs_data6[$ss]["pay_template_name"]."</td>" .
 			"</tr>" ;
		}	
		for($ss=0;$ss<$rs_data7["rows"];$ss++)
		{
$content.= 	"<tr>" .
 			"<td></td>" .
 			"<td>".$rs_data7[$ss]["pay_category_name"].":".$rs_data7[$ss]["pay_name"]."</td>".
 			"<td>".$rs_data7[$ss]["pay_category_name"].":".$rs_data7[$ss]["pay_name"]."</td>".
 			"<td>".number_format($rs_data7[$ss]["pay_total"],2,".",",")."</td>".
 			"<td>".$rs_data7[$ss]["pay_account_name"]."</td>".
 			"<td>".$begindate."</td>";
 			if($rs_data7[$ss]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data7[$ss]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data7[$ss]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data7[$ss]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
 $content.=	"<td>".$sub.":".$branch."</td>".
 			"<td></td>" .
 			"<td>1</td>" .
 			"<td>".$rs_data7[$ss]["pay_template_name"]."</td>" .
 			"</tr>" ;
		}	
		for($ss=0;$ss<$rs_data8["rows"];$ss++)
		{
$content.= 	"<tr>" .
 			"<td></td>" .
 			"<td>".$rs_data8[$ss]["pay_category_name"].":".$rs_data8[$ss]["pay_name"]."</td>".
 			"<td>".$rs_data8[$ss]["pay_category_name"].":".$rs_data8[$ss]["pay_name"]."</td>".
 			"<td>".number_format($rs_data8[$ss]["pay_total"],2,".",",")."</td>".
 			"<td>".$rs_data8[$ss]["pay_account_name"]."</td>".
 			"<td>".$begindate."</td>";
 			if($rs_data8[$ss]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data8[$ss]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data8[$ss]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data8[$ss]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
 $content.=	"<td>".$sub.":".$branch."</td>".
 			"<td></td>" .
 			"<td>1</td>" .
 			"<td>".$rs_data8[$ss]["pay_template_name"]."</td>" .
 			"</tr>" ;
		}	
		for($ss=0;$ss<$rs_data9["rows"];$ss++)
		{
$content.= 	"<tr>" .
 			"<td></td>" .
 			"<td>".$rs_data9[$ss]["pay_category_name"].":".$rs_data9[$ss]["pay_name"]."</td>".
 			"<td>".$rs_data9[$ss]["pay_category_name"].":".$rs_data9[$ss]["pay_name"]."</td>".
 			"<td>".number_format($rs_data9[$ss]["pay_total"],2,".",",")."</td>".
 			"<td>".$rs_data9[$ss]["pay_account_name"]."</td>".
 			"<td>".$begindate."</td>";
 			if($rs_data9[$ss]["book_id"]){
							$bpidb=$obj->getIdToText($rs_data9[$ss]["book_id"],"a_bookinginfo","b_branch_id","book_id");
							$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
							$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
							if($rs_data9[$ss]["pds_id"]){
								$bpidb=$obj->getIdToText($rs_data9[$ss]["pds_id"],"c_saleproduct","branch_id","pds_id");
								$branch = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name","branch_id");
								$sub = $obj->getIdToText($bpidb,"bl_branchinfo","branch_name3","branch_id");
							}
 $content.=	"<td>".$sub.":".$branch."</td>".
 			"<td></td>" .
 			"<td>1</td>" .
 			"<td>".$rs_data9[$ss]["pay_template_name"]."</td>" .
 			"</tr>" ;
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
 		
 file_put_contents("excel credit.xls",$content);
?>