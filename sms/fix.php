<?

/*
$db_user = "oasistest";
$db_pass = "bpSUUDhKKjoaDRp";
$db_dbs = "tap10_oasistest";*/
error_reporting(E_ALL);
$db_user = "smsoasis";
$db_pass = "T@pO@siS%";
$db_dbs = "tap10_oasis";
$conn = mysql_connect("localhost",$db_user,$db_pass);
function setResult($SQL, $debug=false,$db_dbs="tap10_oasis") {
		$conn = $GLOBALS["conn"];
		
		$msg = "";
		
		mysql_select_db("TAP10_oasis", $conn);
		mysql_query("set names utf8");		//set every text to UTF8 support all language
		$rs = mysql_query($SQL, $conn);
		$err   = mysql_error();
		echo "<br>".$err ;echo "----".$SQL.":";
		
		$delete = eregi("^delete", $SQL);
		$update = eregi("^update", $SQL);
		$insert = eregi("^insert", $SQL);
		
		if($delete || $update) {
			$affectedrows = mysql_affected_rows($conn);
			if($affectedrows < 0) {return false;}
			
			if($affectedrows == 0) {return 1;}
			else {return $affectedrows;}
		}
		
		if($insert) {
			$lastinsertid = mysql_insert_id($conn);
			return $lastinsertid;
		}
		
		return $rs;
}
function getResult($SQL, $debug=false, $db_dbs="tap10_oasis") {
		$conn = $GLOBALS["conn"];	// connect to database
		
		$recordcount = 0;
		$msg = "";
		$rs = false;
		mysql_select_db("TAP10_oasis", $conn);
		mysql_query("set names utf8");
		$rs = mysql_query($SQL, $conn);
		$err   = mysql_error();
		echo "<br>".$err ;echo "----".$SQL.":";
		
		if($rs) {
			$recordcount = mysql_num_rows($rs);
			if(intval($recordcount) <= 0) {
				$msg .= "Is Empty..";
				return false;
			}
			for($i=0; $i<intval($recordcount); $i++) {
				$rows[$i] = mysql_fetch_array($rs);
			}
			$rows["rows"] = $recordcount;
			
		} else {return false;}
		return $rows;		
}

$sql = "select c_srdetail.* , cl_product.pd_category_id, cl_product.pd_id , cl_product_category.* " .
		"from c_srdetail ,cl_product , cl_product_category " .
		"where c_srdetail.pd_id = cl_product.pd_id " .
		"and cl_product.pd_category_id = cl_product_category.pd_category_id " .
		"order by c_srdetail.salesreceipt_id limit 30000,10000";
echo $sql;
$rs = getResult($sql);

echo $rs["rows"];
	for($i=0;$i<$rs["rows"];$i++){
		$amount[$i]["unit_price"] = $rs[$i]["unit_price"];
		$amount[$i]["qty"] = $rs[$i]["qty"];
		$amount[$i]["total"]=$amount[$i]["unit_price"]*$amount[$i]["qty"];
		
		if($rs[$i]["set_sc"]==1){
			$amount[$i]["set_sc"]=($amount[$i]["total"]*7)/100;
		}else{
			$amount[$i]["set_sc"]=0;
		}
		
		if($rs[$i]["set_tax"]==1){
			$amount[$i]["set_tax"]=(($amount[$i]["total"]+$amount[$i]["set_sc"])*10)/100;
		}else{
			$amount[$i]["set_tax"]=0;
		}
		
		$r_total[$i]=$amount[$i]["total"]+$amount[$i]["set_sc"]+$amount[$i]["set_tax"];
		$sr_total[$rs[$i]["salesreceipt_id"]] = 0;
	}
	
	for($i=0;$i<$rs["rows"];$i++){
		
		if($rs[$i]["salesreceipt_id"]!=$rs[$i-1]["salesreceipt_id"]){
			echo $rs[$i]["salesreceipt_id"].":".$sr_total[$rs[$i]["salesreceipt_id"]];
		}
		
			if($rs[$i]["pos_neg_value"]==1){		
				$sr_total[$rs[$i]["salesreceipt_id"]] += $r_total[$i];
				echo "+".$r_total[$i];
			}
			if($rs[$i]["pos_neg_value"]==0){
				$sr_total[$rs[$i]["salesreceipt_id"]] -= $r_total[$i];
				echo "-".$r_total[$i];
			}
		
		//echo $rs[$i]["salesreceipt_id"]."--------".$r_total[$i]."<br>";
		if($rs[$i]["salesreceipt_id"]!=$rs[$i+1]["salesreceipt_id"]){
				$sql = "UPDATE c_salesreceipt SET sr_total = '".$sr_total[$rs[$i]["salesreceipt_id"]]."' " .
					"WHERE c_salesreceipt.salesreceipt_id ='".$rs[$i]["salesreceipt_id"]."'";
				//echo $sql."<br>";
				$sqlfix = setResult($sql);
		}
	}
	
	$sql = "SELECT srpayment_id FROM `c_srpayment` " .
			"WHERE `pay_id` IN (3,7,11,13,14)  and `salesreceipt_id` " .
			"in (select `salesreceipt_id` from c_srpayment  " .
			"group by `salesreceipt_id` having count( `salesreceipt_id`) >1)";
	
		
	$rs = getResult($sql);
	$srpayment_id = "";
	for($i=0;$i<$rs["rows"];$i++){
		if($i){
			$srpayment_id .= ",";
		}
		$srpayment_id .= $rs[$i]["srpayment_id"];
	}
	
	$sql1 = "UPDATE c_srpayment SET pay_total = 0 " .
			"WHERE c_srpayment.srpayment_id in ($srpayment_id) and `pay_id` IN (3,7,11,14)";
			//echo $sql1;
	$sqlfix = setResult($sql1);
	
	$sql = "SELECT srpayment_id FROM `c_srpayment` " .
			"WHERE `pay_id` IN (3,7,11,14)  and `salesreceipt_id` " .
			"in (select `salesreceipt_id` from c_srpayment  " .
			"group by `salesreceipt_id` having count( `salesreceipt_id`) =1)";
			
		
	$rs = getResult($sql);
	$srpayment_id = "";
	for($i=0;$i<$rs["rows"];$i++){
		if($i){
			$srpayment_id .= ",";
		}
		$srpayment_id .= $rs[$i]["srpayment_id"];
	}
	
	$sql1 = "UPDATE c_srpayment,c_salesreceipt SET c_srpayment.pay_total = c_salesreceipt.sr_total " .
			"WHERE c_srpayment.srpayment_id in ($srpayment_id) and c_srpayment.`pay_id` IN (3,7,11,14) " .
			"and c_srpayment.salesreceipt_id = c_salesreceipt.salesreceipt_id";
	echo $sql1;
	$sqlfix = setResult($sql1);
	


?>