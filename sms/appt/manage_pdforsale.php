<?
include("../include.php");
require_once("appt.inc.php");
$obj = new appt(); 

$obj->setErrorMsg("");
$obj->setLimitBranchOnLocation();
$date = $obj->getParameter("date",date("d-m-Y"));
$pagename = "manage_pdforsale.php";
$lastscan = $obj->getParameter("lastscan",0);
// giftChk for expand/collapse gift Certificate tag div when update/insert booking
$giftchk=$obj->getParameter("giftChk",0);

// For debug undefined index : . By Ruck : 19-05-2009
$chkFirst=$obj->getParameter("chkFirst",false);
$newLogin=$obj->getParameter("newLogin","");
$successmsg=$obj->getParameter("successmsg","");
$pdsid=$obj->getParameter("pdsid",false);
$initStatus = false;
$errormsg = "";
$srInit=false; //20-05-2009

//check edit status and set book id
if($pdsid){$status="edit";}else{$status="add";}

// ################ End Set Date format ##############################
// --------------------------------------------------------------------
$obj->setErrorMsgColor("red");
$usersale = $_SESSION["__user_id"];
// --------------------------------------------------------------------

/***************************************************
 * Security checking
 ***************************************************/
// check user edit permission 
$pageid = "1";	// appointment page
$pagestatus = $object->check_permission($pageid,$permissionrs);
$chkPageEdit=false;$chkPageView=false;
if($pagestatus=="e"){
	$chkPageEdit=true;$chkPageView=true;
}else if($pagestatus=="v"){
	$chkPageEdit=false;$chkPageView=true;
}else if($pagestatus=="n"){
	$chkPageEdit=false;$chkPageView=false;
}

// check sale receipt lock/unlock permission 
$chkSREdit=$object->isEditSaleReceipt();

// check before closing product sale windows
$close = $object->getParameter("close",false);
if($close && $status=="edit"){
	$chkpds = $object->beforeClosePds($pdsid);
	if($chkpds){?><script language="javascript">window.close();</script><?}
	else{$errormsg ="Can't Closing this window. Please try again!!";}
}


/***************************************************
 * product for sales information
 ***************************************************/
// --------------------------------------------------------------------
// ############# Get some information from request value ##############
$cs = $obj->getParameter("cs",false);
$cc = $obj->getParameter("cc",false);

// ########### End Get some information from request value ############
// --------------------------------------------------------------------
// ############# Get some information from request value ##############
$cs = $obj->getParameter("cs",false);
$cc = $obj->getParameter("cc",false);

// ########### End Get some information from request value ############
// --------------------------------------------------------------------
// #######################  SalesReceipt ##############################
if($chkFirst!="first"){
	$srInit = true;
	$initStatus = true;
} 

if(!isset($cs["inspection"])){$cs["inspection"]="";}

$srd = $obj->getParameter("srd",false);
$srdcount = $obj->getParameter("srdcount",false);
$mpdcount = $obj->getParameter("mpdcount",false);
$srcount = $obj->getParameter("srcount",1);
// cut off "-- select --" product 
$srd = $obj->getCurrentTab($srd);			// get srd and reset it
// count $srdcount for how many product in each salereceipt
$srdcount = $obj->getCurrentRowsInTab($srd,$srcount);
// count $srdcount for how many product in each Method of Payment
$mpdcount = $obj->getCurrentRowsInPay($srd,$srcount);
if($srInit){	// when come in this page 1 st time 	
	// it should be in specific case 
	// 1 st open booking for update or add new booking
	// get salereceipt all information from database
	$srd=$obj->getSaleReceiptData(0,$pdsid);
	
	if(count($srd)!=0){ 	// if have salereceipt information
		$srcount=count($srd);	// set $srcount
		//set new $srdcount from $srd
		$srdcount = $obj->getCurrentRowsInTab($srd,$srcount);
		//set new $srdcount from Method of Payment
		$mpdcount = $obj->getCurrentRowsInPay($srd,$srcount);
	}	
}

/*
$srd = $obj->getParameter("srd",false);
$srdcount = $obj->getParameter("srdcount",false);

if(!isset($_POST["add"]) || $status!="edit"){
	$srcount = $obj->getParameter("srcount",1);
	$srdcount = $obj->getParameter("srdcount");
	$srd = $obj->getCurrentTab($srd);
	//echo "<br>Amount slip".count($srd);
	$srdcount = $obj->getCurrentRowsInTab($srd,$srcount);
}
//echo $srInit." <- Sr Init <br>";
if(count($srd)==0 && $srInit){
	//echo "Init<br>";
	$srd=$obj->getSaleReceiptData(0,$pdsid);
	
	//echo count($srd)."<br>";
	if(count($srd)!=0){
		$srcount=count($srd);
		$srdcount = $obj->getCurrentRowsInTab($srd,$srcount);
	}else{
		$srcount = $obj->getParameter("srcount",1);
		$srdcount[0] = 1;
		$srdcount = $obj->getCurrentRowsInTab($srd,$srcount);
		//echo $srcount." <- SrCount<br>".$srdcount[0]."<- SrdCount";
	}
	
}*/
// #######################  End SalesReceipt #########################*/
// --------------------------------------------------------------------
// #################  main produce sale detail Path ###################
if($initStatus&&$status=="add"){
	$cs["branch"] = $obj->getParameter("branch_id",false);
	$cs["saledate"] = $obj->getParameter("date",false);
	$cs["hidden_saledate"] = $dateobj->convertdate($cs["saledate"],$sdateformat,"Ymd");
	$cc["cc"] = "";
	$cc["date"] = $obj->getParameter("date",false);
	$cc["hidden_date"] = $dateobj->convertdate($cc["date"],'Y-m-d',"Ymd");
	$usersale = $_SESSION["__user_id"];
}
if($initStatus&&$status=="edit"){
	$sql="select * from c_saleproduct where pds_id=$pdsid";
	$rs = $obj->getResult($sql);
	$cs["branch"] = $rs[0]["branch_id"];
	//$cs["bookid"] = $rs[0]["book_id"]; No have book_id in table c_saleproduct. Remove by Ruck 20-05-2009
	$saledate = split('[ ]', $rs[0]["pds_date"]);
	$cs["saledate"] = $dateobj->convertdate($saledate[0],'Y-m-d',$sdateformat);
	$cs["hidden_saledate"] = $dateobj->convertdate($saledate[0],'Y-m-d',"Ymd");
	$cc["cc"] = ($rs[0]["set_cancel"]==1)?"checked":"";
	$cc["date"] = $dateobj->convertdate($rs[0]["cancel_date"],'Y-m-d',$sdateformat);
	$cc["hidden_date"] = $dateobj->convertdate($rs[0]["cancel_date"],'Y-m-d',"Ymd");
	$cc["comment"] = $rs[0]["cancel_comment"];
	$usersale = $rs[0]["l_lu_user"];
	$cs["insertdate"] = $rs[0]["l_lu_date"];
	$cs["memid"] = $rs[0]["a_member_code"];
	$cs["inspection"] = $rs[0]["mkcode_id"]; 
}

if($status=="edit"){
	$rsComment = $obj->getResult("select * from ca_comment where pds_id=$pdsid order by l_lu_date desc");}
if($initStatus==false){
	$cs = $obj->getParameter("cs",false);
	$cc = $obj->getParameter("cc",false);
	if(!isset($cs["insertuser"])){$cs["insertuser"]="";}
	$usersale = $cs["insertuser"];
}
// For debug undefined index : . By Ruck : 19-05-2009
if(!isset($cs["comment"])){$cs["comment"]="";}
if(!isset($rsComment)){$rsComment=false;}
if(!isset($cs["memid"])){$cs["memid"]="";}
// For debug undefined index : . By Ruck : 20-05-2009
if(!isset($cc["cc"])){$cc["cc"]="";}

// ################  End main produce sale detail Path ################
// #######################  Sale Products Detail #######################
if($status=="edit") {
	$sql="select tax_id,servicescharge,branch_id from c_saleproduct where pds_id=$pdsid";
	$rs = $obj->getResult($sql);
	if($cs["branch"]==$rs[0]["branch_id"]){
		$taxpercent = $rs[0]["tax_id"];
		$servicescharge = $rs[0]["servicescharge"];
	}else{
		$sql = "select tax_id,servicescharge from bl_branchinfo where branch_active=1 and branch_id=".$cs["branch"]." order by branch_name limit 0,1";
		$rs = $obj->getResult($sql);
		$servicescharge = $rs[0]["servicescharge"];
		$taxpercent = $rs[0]["tax_id"];
	}
	
}else{
	$sql = "select tax_id,servicescharge from bl_branchinfo where branch_active=1 and branch_id=".$cs["branch"]." order by branch_name limit 0,1";
	$rs = $obj->getResult($sql);
	$servicescharge = $rs[0]["servicescharge"];
	$taxpercent = $rs[0]["tax_id"];
}
$subtotal = 0;
if(!$srcount){$srcount=$obj->getParameter("srcount",1);}
for($i=0; $i<$srcount; $i++) {
//======  Start init first value  ==============================================//
// positive.
	$amount[$i]["p_amount"]=0;
	$amount[$i]["p_svc"]=0;
	$amount[$i]["p_tax"]=0;
// negative.
	$amount[$i]["n_amount"]=0;
	$amount[$i]["n_svc"]=0;
	$amount[$i]["n_tax"]=0;
	$amount[$i]["payment"]=0;
	$r_gift[$i]=0;
	
//For debug undefined index : . By Ruck 19-05-2009
	if(!isset($amount[$i]["amount"])){$amount[$i]["amount"]=0;}
	if(!isset($amount[$i]["svc"])){$amount[$i]["svc"]=0;}
	if(!isset($amount[$i]["tax"])){$amount[$i]["tax"]=0;}
	
//======  End init first value  ================================================//
	for($j=0; $j<$srdcount[$i]; $j++){
			//For debug undefined index : . By Ruck 19-05-2009	
            if(!isset($srd[$i][$j]["quantity"])){$srd[$i][$j]["quantity"]=1;}
			if(!isset($srd[$i][$j]["pd_id"])){$srd[$i][$j]["pd_id"]=1;}
			if(!isset($srd[$i][$j]["plus_sc"])){$srd[$i][$j]["plus_sc"]=1;}
			if(!isset($srd[$i][$j]["plus_tax"])){$srd[$i][$j]["plus_tax"]=1;}
			if(!isset($srd[$i][$j]["pd_id_tmp"])){$srd[$i][$j]["pd_id_tmp"]="";}
			if(!isset($srd[$i][$j]["unit_price"])){$srd[$i][$j]["unit_price"]=0;}
			if(!isset($srd[$i][$j]["amount"])){$srd[$i][$j]["amount"]=0;}
			if(!isset($srd[$i][$j]["svc"])){$srd[$i][$j]["svc"]=1;}
			if(!isset($srd[$i][$j]["tax"])){$srd[$i][$j]["tax"]=1;}
					
			$chkPrice=false;
			$chkPdChange = false;
			$srd[$i][$j]["set_sc"]=false;
			$srd[$i][$j]["set_tax"]=false;
			if( $srd[$i][$j]["pd_id"]!= $srd[$i][$j]["pd_id_tmp"] && !$srInit){
				//echo "<br>Set Temp";
				$srd[$i][$j]["pd_id_tmp"]= $srd[$i][$j]["pd_id"];
				$srd[$i][$j]["unit_price"]=0;
				if($obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_sc","pd_id") || $srd[$i][$j]["pd_id"]==1){
               		$srd[$i][$j]["plus_sc"]=1;
               		$srd[$i][$j]["set_sc"]=true;
	            }else{
	            	$srd[$i][$j]["plus_sc"]=0;
	            }
	            if($obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_tax","pd_id") || $srd[$i][$j]["pd_id"]==1){
	            	$srd[$i][$j]["plus_tax"]=1;
	            	$srd[$i][$j]["set_tax"]=true;
	            }else{
	            	$srd[$i][$j]["plus_tax"]=0;
	            }
				$chkPdChange = true;
				$chkPrice=true;
			}else{
				//echo "<br>First Set Temp";
				$srd[$i][$j]["pd_id_tmp"]= $srd[$i][$j]["pd_id"];
				if($obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_sc","pd_id")){
					//echo "<br>Sc value : ".$obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_sc","pd_id")." -- Pd id : ".$srd[$i][$j]["pd_id"];
               		$srd[$i][$j]["set_sc"]=true;
	            }
	            if($obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_tax","pd_id")){
	            	$srd[$i][$j]["set_tax"]=true;
	            }
	           
			}
			
			if(!$srd[$i][$j]["unit_price"]){
				if($srInit){
					$srd[$i][$j]["pd_id_tmp"]= $srd[$i][$j]["pd_id"];
				}
				if($chkPrice){
					//echo "<br>Check New Price <br>";
					$srd[$i][$j]["pd_id_tmp"]= $srd[$i][$j]["pd_id"];
					$srd[$i][$j]["unit_price"]=$obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","standard_price","pd_id");
					
				}
			
			}
			//echo "After check<br>";
			// echo "pid_tmp : ".$srd[$i][$j]["pd_id_tmp"]."<br>Pid : ".$srd[$i][$j]["pd_id"]."<br>";
			$chkautosubmit=array($i,$j);
			
			//-- begin check product/qty/price value
			//echo $srd[$i][$j]["pd_id"]." <-- Pd id <br>";
			$product["product_id"][$j] = $srd[$i][$j]["pd_id"];
			//echo $product["product_id"][$j]." <-- Pd id <br>";
			$product["category_id"][$j] = $obj->getIdToText($product["product_id"][$j],"cl_product","pd_category_id","pd_id");
			$product["qty"][$j] = $srd[$i][$j]["quantity"];
			$product["unit_price"][$j] = $srd[$i][$j]["unit_price"];
			$product["total"][$j] = $product["qty"][$j]*$product["unit_price"][$j];
			//$product["set_sc"][$j] = ($srd[$i][$j]["srd_id"]!="" && !$chkPdChange)?$obj->getIdToText($srd[$i][$j]["srd_id"],"c_srdetail","set_sc","srdetail_id"):$obj->getIdToText($product["product_id"][$j],"cl_product","set_sc","pd_id");
			//$product["set_tax"][$j] = ($srd[$i][$j]["srd_id"]!="" && !$chkPdChange)?$obj->getIdToText($srd[$i][$j]["srd_id"],"c_srdetail","set_tax","srdetail_id"):$obj->getIdToText($product["product_id"][$j],"cl_product","set_tax","pd_id");
			$product["set_sc"][$j] = $srd[$i][$j]["plus_sc"]; 
			$product["set_tax"][$j] = $srd[$i][$j]["plus_tax"];
			$product["taxpercent"][$j] = $obj->getIdToText($taxpercent,"l_tax","tax_percent","tax_id");
			$product["servicescharge"][$j] = $servicescharge ;
			
			if($obj->getIdToText($product["category_id"][$j],"cl_product_category","set_payment","pd_category_id")==0) {
				if($obj->getIdToText($product["category_id"][$j],"cl_product_category","pos_neg_value","pd_category_id")==0) {
					$amount[$i]["amount"] -= $product["total"][$j];
					$amount[$i]["svc"] -= $obj->getsSvc($product,$j);
					$amount[$i]["tax"] -= $obj->getsTax($product,$j,$obj->getsSvc($product,$j));
				} else {
					$amount[$i]["amount"] += $product["total"][$j];
					$amount[$i]["svc"] += $obj->getsSvc($product,$j);
					$amount[$i]["tax"] += $obj->getsTax($product,$j,$obj->getsSvc($product,$j));
				}
			} else {
				if($obj->getIdToText($product["category_id"][$j],"cl_product_category","pos_neg_value","pd_category_id")==0) {
					$amount[$i]["payment"] += $product["total"][$j];
					$amount[$i]["payment"] += $obj->getsSvc($product,$j);
					$amount[$i]["payment"] += $obj->getsTax($product,$j,$obj->getsSvc($product,$j));
				} else {
					$amount[$i]["payment"] -= $product["total"][$j];
					$amount[$i]["payment"] -= $obj->getsSvc($product,$j);
					$amount[$i]["payment"] -= $obj->getsTax($product,$j,$obj->getsSvc($product,$j));
				}
			}
			
			if($obj->getIdToText($product["category_id"][$j],"cl_product_category","set_gift","pd_category_id")==1){
				$r_gift[$i] = $product["total"][$j];		
			}
	}
		$r_amount[$i] = $amount[$i]["amount"];
		$r_svc[$i] = $amount[$i]["svc"];
		$r_tax[$i] = $amount[$i]["tax"];
		$r_payment[$i] = $amount[$i]["payment"];
		$r_total[$i] = $r_amount[$i]+$r_svc[$i]+$r_tax[$i]-$r_payment[$i];
		
		//not minus payment
		$rnp_total[$i] = $r_amount[$i]+$r_gift[$i]+$r_svc[$i]+$r_tax[$i];
		
		$subtotal += $r_amount[$i];
}
//$obj->showSrDetail($srd);
//echo "Count Srd ".count($srd);
// #######################  End SalesReceipt Detail ###################
// --------------------------------------------------------------------

/***************************************************
 * Security checking
 ***************************************************/
// convert date status for permission checking
 if($cs["saledate"]==""){
	$date=$obj->getParameter("date",date($sdateformat));
	$date=$dateobj->convertdate($date,$sdateformat,'Y-m-d');
}else{
	$date=$dateobj->convertdate($cs["saledate"],$sdateformat,'Y-m-d');
}

// check reservation view limit date permission
$now=$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$sdateformat",$cs["branch"]);

if($status == "edit"){
	$preViewDate="";
	$afterViewDate="";
	$chkRsViewDate = $object->isReservationLimit();
	if($chkRsViewDate){
		$preViewDate = $object->getReservationDate("pre_viewdate","appt_viewchk");
		$afterViewDate = $object->getReservationDate("after_viewdate","appt_viewchk");
		$chkRsDate = $object->checkReservationDate($date,'Y-m-d',$preViewDate,$afterViewDate,$now);
		if(!$chkRsDate){
			$chkPageView=false;
		}
	}
}

// check reservtion edit date limit 
$preEditDate="";
$afterEditDate="";
$checkApptPage="false";	// flat for calendar chooser 
// checking if appt_editchk was check
$chkRsEditDate = $object->isReservationLimit("appt_editchk");
if($chkRsEditDate){
	$preEditDate= $object->getReservationDate("pre_editdate","appt_editchk");
	$afterEditDate= $object->getReservationDate("after_editdate","appt_editchk");
	$chkRsDate= $object->checkReservationDate($date,'Y-m-d',$preEditDate,$afterEditDate,$now);
	if(!$chkRsDate){
		$chkPageEdit=false;
		// if reservation edit date was check then reset appointment date
		if($status=="add"){
			$cs["saledate"] = date($sdateformat);
			$cs["hidden_saledate"] = date('Ymd');
		}
	}
}else{
// seting flat for calendar chooser 
	$preEditDate="notCheck";
	$afterEditDate="notCheck";
}


// Check this booking is in location the same user location 
$userLocationChk = $object->isEditBookInLocation($cs["branch"]);
if($status=="edit" && $userLocationChk==false){
	$chkPageEdit=false;
}

if($userLocationChk==false){
	$chkPageView=false;
}

//Close window if user can't view or edit booking 
if(!$chkPageView){
		?>
		<script language="javascript">
			alert("You can't access this booking.");
			<?=($newLogin)?"opener.parent.location.reload();":""?>
			window.close();
		</script>
	<?		
}

// check if have user use in edit page 
if($status=="edit"&& $srInit && $chkPageEdit){
	$uid=$object->checkPdsUse($pdsid);
	if($uid!=false){
		?>
		<script language="javascript">
			alert("This Product Sale is used by <?=$obj->getIdToText($uid,"s_user","u","u_id")?>");
			<?=($newLogin)?"opener.parent.location.reload();":""?>
			window.close();
		</script>
		<?
	}else{
		$object->startPds($pdsid);
	}
}


/***************************************************
 * Setting un-initialize value
 ***************************************************/
if($cc["cc"]!="checked"){
	$cc["date"] = date($sdateformat);
	$cc["hidden_date"] = date("Ymd");
	$cc["comment"] = "";
}

if(!$cc["date"]){
	$cc["date"] = date($sdateformat);
	$cc["hidden_date"] = date("Ymd");
}
if(!$cs["saledate"]){
	$cs["saledate"] = date($sdateformat);
	$cs["hidden_saledate"] = date("Ymd");
}
/***************************************************
 * Insert and Update Information
 ***************************************************/
if(isset($_POST["add"]) && !$newLogin ) {
	//if(is_numeric($cs["bookid"])||$cs["bookid"]==""){
	//	$chksql = "select tb_id from c_bpds_link where bpds_id=".$cs["bookid"];
	//	$rs=$obj->getResult($chksql);
	//	if($rs["rows"]>0 || $cs["bookid"]=="" || $cs["bookid"]==0){
	// All check condition are true. Remove by Ruck 20-05-2009 
			if($status=="edit") {
				$errormsg = false;
				$id = $obj->editPds($pdsid,$cs,$cc,$debug=false);
				if($id){
					if(str_replace(' ','',$cs["comment"])!=''){
						$obj->addPsdcomment($cs["comment"],$pdsid,false);
					}
					$tmpSrd=$obj->editSaleReceipt($srd,0,$pdsid,$object->getUserIdLogin(),$mpdcount);	
					if($tmpSrd !="noValue"){
						$srd=$tmpSrd;
					}
					
					$srcount=count($srd);
					if($srcount==0){
						$srcount=1;
					}
					if($tmpSrd !="noValue"){
						$successmsg="Update Success!!";
						header("location: manage_pdforsale.php?pdsid=$pdsid&successmsg=$successmsg&giftChk=$giftchk");
					}
					else{$errormsg = $obj->getErrorMsg();}
				}else{
					$errormsg = $obj->getErrorMsg();
				}
			} else {
				$errormsg = false;
				$errdate = false;
				if($cs["hidden_saledate"]<$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"Ymd",$cs["branch"])){
					$errdate = true;
					$obj->setErrorMsg("Please change appointment date to future or today!!");
				}
				if($errdate==false){
					$id = $obj->addPds($cs,$cc,$servicescharge,$taxpercent,false);		
					if($id){
						if(str_replace(' ','',$cs["comment"])!=''){
							$obj->addPsdcomment($cs["comment"],$id,false);
						}
						$tmpSrd=$obj->editSaleReceipt($srd,0,$id,$object->getUserIdLogin(),$mpdcount);
								
						if($tmpSrd !="noValue"){
							$srd=$tmpSrd;
						}
						//echo $tmpSrd;
						$srcount=count($srd);
						if($srcount==0){
							$srcount=1;
						}
						if($tmpSrd !="noValue"){
							// for insert pds information when insert success doesn't close window but redirect to the pds id
							
							$successmsg="Update Success!!";
							header("location: manage_pdforsale.php?pdsid=$id&successmsg=$successmsg&giftChk=$giftchk");
							?>
								<script language="javascript">
								//	window.close();
								</script>
							<?
							}
						else{$errormsg = $obj->getErrorMsg();}
					}else{
						$errormsg = $obj->getErrorMsg();
					}
				}else{
					$errormsg = $obj->getErrorMsg();
				}
			}
}
$obj->setBranchid($cs["branch"]);
// #######################  End Cancel Path ###########################
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Product Sale</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script src="scripts/json_parse.js" type="text/javascript"></script>
<script src="scripts/ajax.js" type="text/javascript"></script>
<script src="scripts/component.js" type="text/javascript"></script>
<script src="scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="scripts/datechooser/datechooser.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="scripts/datechooser/datechooser.css">

<!--[if IE]>
<style>

td.fix select.ctrDropDown{
    width:115px;
    font-size:11px;
}
td.fix select.ctrDropDownClick{
    font-size:11px;

    width:auto;

}
td.fix select.plainDropDown{
    width:115px;
    font-size:11px;
}
</style>
<![endif]-->

<script type="text/javascript">

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}
document.onkeypress = stopRKey;
</script> 
</head>
<body style="background-color: #eae8e8;" onLoad = "focusIt()">

<form name='pdforsale' id='pdforsale' action='<?=$pagename?>' method='post'>
  <table width="100%" border="0px">
    <tr>
      <td class="header" style="padding-bottom:5px">
      <input type="hidden" id="nowUserId" name="nowUserId" value="<?=$object->getUserIdLogin()?>"/>
      <? if($status=="add"){?>
        <b>Add New Sales Item</b>
        <? }else{?>
        <b>Sale Product ID: </b><b class="style1">
        <?=$obj->getIdToText($pdsid,"c_bpds_link","bpds_id","tb_id","tb_name=\"c_saleproduct\"",false)?></b>
        <input type="hidden" id="pdsid" name="pdsid" value="<?=$pdsid?>"/>
        <? }?>
      </td>
      <td class="header" style="padding-left:0px">
      <table width="100%">
          <tr>
            <td style="padding-left:10px"><b>Sale
              <? if($status=="edit"){?>
              by:&nbsp;</b><b class="style1">
               <?=$obj->checkParameter($obj->getIdToText($usersale,"s_user","u","u_id"),"- ")?>
              </b>
               <? // get add date and add time             
              $d = substr($cs["insertdate"],0,10);
              $t = substr($cs["insertdate"],11,8);
                                          
              $data = $dateobj->timezone_depend_branch($d,$t,"$ldateformat, H:i:s",$cs["branch"]);?>
              <b>
			  <b>
              <?=$data?>
              </b>
              <input type="hidden" id="cs[insertuser]" name="cs[insertuser]" value="<?=$usersale?>"/>
              <input type="hidden" id="cs[insertdate]" name="cs[insertdate]" value="<?=$cs["insertdate"]?>"/>
              <? 
            	}else{
            		$data = $dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$ldateformat, H:i:s","$cs[branch]");
            		echo "by: <b class=\"style1\">".$obj->getIdToText($obj->getUserIdLogin(),"s_user","u","u_id")."</b> <b>".$data;
            	}
            ?>
              </b> </td>
            <td align="right"></td>
            <td width="350px" align="right"><span class="tabmenuheader" style="margin-right:20px">
            <? if($chkPageEdit){?>
              <input type="submit" id="add" name="add" value=" <?=($status=="edit")?"Update":"Save"?> " class="button" onClick="chkMinus(<?=$srcount?>);" />
  			  <input type="submit" name="close" value=" Close " class="button" <? if($status=="edit"){ echo $status; ?>onclick="this.form.submit();"<?}else{?>onclick="window.close()"<? }?> />
             <? }
             	$userId = $object->getUserIdLogin();
				if($status=="edit"){
					$log_viewchk = $obj->getIdToText($userId,"s_userpermission","log_viewchk","user_id");
					if($log_viewchk==1){
			?>
             	<input type="button" id="log_viewchk" name="log_viewchk" value="CHECK LOG" class="button" onClick="window.open('booklog.php?pageid=1&chkpage=2&pds_id=<?=$pdsid?>','bookinglog<?=$pdsid?>','location=0,toolbar=0,directoris=0,status=0,menubar=0,scrollbars=1,resizable=0')" />
            		<? }?> 
             <? }?> 
              
              &nbsp;&nbsp; </span> </td>
          </tr>
        </table>
        <table width="100%" height="20" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td class="tabmenuheader">
            <?  $refid=array();
        		$refid=explode(",",$obj->getIdToText($pdsid,"c_bpds_link","ref_id","tb_id","`tb_name`=\"c_saleproduct\""));
        		if(count($refid)>=1&&$refid[0]!=""){ ?>
        	<b>Refer to Booking ID: 
        	<? 	for($i=0;$i<count($refid);$i++){ 
        			$tbid = $obj->getIdToText($refid[$i],"c_bpds_link","tb_id","bpds_id");
        			$tbname = $obj->getIdToText($refid[$i],"c_bpds_link","tb_name","bpds_id");
        			if($i){echo ", ";}
        			if($tbname=="a_bookinginfo"){ 
	        				$cancel = $obj->getIdToText($tbid,"a_bookinginfo","b_set_cancel","book_id");
	        				$data=$refid[$i];
	        				if($cancel){$data="<del class=\"style1\">".$refid[$i]."</del>";}
	        		?>
	        			<a href='javascript:;;' onClick="newwindow('manage_booking.php?chkpage=1&bookid=<?=$tbid?>','manageBooking<?=$tbid?>')" class="style1">
	        			<?=$data?></a>
        			<? } else {  
	        				$cancel = $obj->getIdToText($tbid,"c_saleproduct","set_cancel","pds_id");
	        				$data=$refid[$i];
	        				if($cancel){$data="<del class=\"style1\">".$refid[$i]."</del>";}
	        		?>
        				<a href='javascript:;;' onClick="newwindow('manage_pdforsale.php?pdsid=<?=$tbid?>','managePds<?=$tbid?>')" class="style1">
        				<?=$data?></a>
        			<? } ?>
        	<?	} ?>
        	</b>
        <? } ?></td>
            <td class="tabmenuheader" align="right" style="padding-right:15px;v-align:middle;"><b class="style1">
             <span id="errormsg" class="style1" ><?=$errormsg?></span></b>
             <input type="hidden" id="status" name="status" value="<?=$status?>"/>
            <span id="successmsg" class="style3" style="display: block;"><b class="style3"><?=($newLogin)?"":$successmsg?></b></span>
             </td>
          </tr>
        </table></td>
    </tr>
    <tr>
    <td colspan="2">
	    <br />
	    <div class="group5" width="100%">
	    <fieldset>
   			<legend><b>Other Information</b></legend>
   			<table cellspacing="0" cellpadding="0" border="0" class="cusinfo" width="100%">
	            <tr>
					<td width="15%">Branch:<span class="style1">*</span></td>
                	<td width="20%"><?=$obj->makeListbox("cs[branch]","bl_branchinfo","branch_name","branch_id",$cs["branch"],true,"branch_name","branch_active","1","branch_name not like 'All'")?></td></td>
					<td width="65%" rowspan="6" align="left" style="vertical-align:top; padding-left:0px;">
		            <table cellspacing="0" cellpadding="0" width="90%" border="0">
		            	<tr>
		                	<td style="padding-left:20px" width="60px">Comment:</td>
		                	<td align="left"><textarea  name="cs[comment]" id="cs[comment]" rows="2" class="bcomment"><?=$cs["comment"]?></textarea></td>
		                </tr>
		                <tr>
		                	<td colspan="2">
		                	<div class="comment" style="height:100px;margin-top:5px;">
		                    <table border="0" cellspacing="0" cellpadding="0" class="comment" style="width:100%">
		                    	<tr>
		                         	<td height="20" width="50" class="mainthead">Agent</td>
		                           	<td height="20" class="mainthead">Comments</td>
		                      	</tr>
		                       	<? for($i=0;$i<$rsComment["rows"];$i++){
		                              	$trclass = ($i%2==0)?"content_list":"content_list1";
										list($date,$time) = explode(" ",$rsComment[$i]["l_lu_date"]);
										$commenttime = split(' ', $dateobj->timezone_depend_branch($date,$time,"$sdateformat H:i:s",$cs["branch"]));
		                        ?>
		                        <tr class='<?=$trclass?>'>
		                         	<td style="vertical-align:top;">
		                         		<?=$commenttime[0]?><br/>
		                               	<?=$commenttime[1]?><br/>
		                             	<?=$obj->getIdToText($rsComment[$i]["l_lu_user"],"s_user","u","u_id")?></td>
		                        	<td style="vertical-align:top;width:300px;"><?=str_replace("\n","<br/>",$rsComment[$i]["comments"])?></td>
		                       	</tr>
		                     	<? }?>
		                	</table>
		               		</div>
		               		</td>
		            	</tr>
		            </table>
		            </td>
		        </tr>
		        <tr>
		        	<td width="100px">Sale Date:</td>
		            <td width="120px"><input id='cs[saledate]' name='cs[saledate]' value="<?=$cs["saledate"]?>" readonly="1" class="textbox" type="text" style="width:85px"/>
                              <input id='cs[hidden_saledate]' name='cs[hidden_saledate]' value="<?=$cs["hidden_saledate"]?>" type="hidden"/>
                                &nbsp;&nbsp;<img src="scripts/datechooser/calendar.gif" alt="Date Appointment" onClick="showChooser(this, 'cs[saledate]', 'date_showSpan', 1900, 2100, '<?=$sdateformat?>', false,<?=$checkApptPage?>,'<?=$preEditDate?>','<?=$afterEditDate?>');" />
                                <div id="date_showSpan" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div></td>
		        </tr>
		        <tr>
		        	<td width="100px">Refer to Booking ID:</td>
		            <td width="120px"><input id="cs[bookid]" name="cs[bookid]" size="10" type="text" value="0"/></td>
		        </tr>
		        <tr>
		        	<td width="100px">Member:</td>
                    <td class="cc">&nbsp;&nbsp;&nbsp;<input type="text" name="cs[memid]" id="cs[memid]" value="<?=$cs["memid"]?>" maxlength="5" size="9"  style="width:50px;" onKeyUp="changeMemberButton(document.getElementById('cs[memid]').value)"/>
                    <input type="hidden" name="cs[name]" id="cs[name]" value="" />
                       <input type="button" name="b_mhistory" id="b_mhistory" value="<? if(is_numeric($cs["memid"]) && $cs["memid"]>0){echo "History";}else{echo "Search";}?>" style="width:60px;" class="button" onClick="open_memberdetail(document.getElementById('cs[memid]').value)" 
                       title="<? if(is_numeric($cs["memid"]) && $cs["memid"]>0){echo "Member History";}else{echo "Member Search";}?>" />
                    
                   </td>
		        </tr>
		        
		        <tr>
		         	<td width="100px">Marketing Code:</td>
                              <td class = "fix">
                			  <span style="width: 115px;font-family:Tahoma; font-size: 11px;overflow:hidden;"> 
                              <?=$obj->makeListbox("cs[inspection]","l_marketingcode,l_mkcode_category","sign","mkcode_id",$cs["inspection"],0,"category_id,sign","active","1","l_marketingcode.category_id=l_mkcode_category.category_id",false,false,false,false,"this.className='ctrDropDown'")?>
                               </span>&nbsp;
                              <input type="button" name="cfdCheck" id="cfdCheck" value="Browse" class="button" title="Browse All Code Free/Discount"
                              onClick="window.open('mkcode/index.php','cfdCheck','location=0,toolbar=0,directoris=0,status=0,menubar=0,scrollbars=1,resizable=0')">
                    </td>
		        </tr>
		        
		        <tr>
					<td colspan="2" width="100%"><input type='checkbox' id='cc[cc]' name='cc[cc]' value='checked' onClick="showHideCheck('CBC','cc[cc]');" <?=$cc["cc"]?> class="checkbox" />
                                  Cancel Sale <br>
                    	<div id="CBC" name="CBC" <?=($cc["cc"])?"style=\"display:block\"":"style=\"display:none\""?>>  
                        <table cellpadding="0" cellspacing="0">
                         	<tr>
								<td>Date of cancelation:</td>
								<td><input id="cc[date]" name="cc[date]" value="<?=$cc["date"]?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			                              	<input id='cc[hidden_date]' name='cc[hidden_date]' value="<?=$cc["hidden_date"]?>" type="hidden"/>
			                                &nbsp;&nbsp;<img src="scripts/datechooser/calendar.gif" alt="" onClick="showChooser(this, 'cc[date]', 'date_showSpan1', 1900, 2100, '<?=$sdateformat?>',false,false,'notCheck','notCheck');" />
			                                <div id="date_showSpan1" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div></td>
			                </tr>
					        <tr>
			                    <td>Reason for cancelation:</td>
								<td><input type='text' id='cc[comment]' name='cc[comment]' value="<?=$cc["comment"]?>" size='23' /></td>
			                </tr>
                         </table>
                       </div>              
                    </td>
                </tr>
		    </table>
    	</fieldset>
	    </div>
        <table width="100%" height="20" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td class="tabmenuheader">
             <? 
		     //######### Find gift certificate is sold by this id ################//
			$sql = "select gift_number,receive_from,value,product,gifttype_id,expired from g_gift where id_sold=$pdsid and tb_name='c_saleproduct'order by gift_number asc";
			$rsGift = $obj->getResult($sql);
			//echo "$sql";
			//###### End find gift certificate is sold by this id ###############//
		     
		     if($chkPageEdit){?>
              <tr>
               <td colspan="3" style="padding-left: 0px;">
	    <div class="group5" width="100%">
       <!-- table show Gift Detail -->
           <fieldset>
            <legend><b>Gift Certificate Information</b></legend>
           <table cellpadding="0" cellspacing="0">
          	<tr>
                	<td width="100%" height="23px" align="left" valign="top" style="white-space: nowrap;">
                	<input  class="button" type="button" id="bgift" name="bgift" value="view" onClick="showhidegift('divGift',document.getElementById('giftChk').value);" />
                	Gift Sold :<?=($rsGift["rows"])?$rsGift["rows"]:"0"?>&nbsp;&nbsp;
                	<input id="giftChk" name="giftChk" value="<?=$giftchk?>" type="hidden"></td>
            </tr>
            
		  </table>
		   <div id="divGift" name="divGift" <?=($giftchk)?"style=\"display:block\"":"style=\"display:none\""?>>
		   <table cellpadding="0" cellspacing="0" class="cusinfo" width="50%">
          	<tr valign="top">
			  	<td valign="middle" width="65%" style="padding-left:20px;">
	            <input type="button" name="addNewGift" value="Add Gift Sold" title="add gift for sale"  href="javascript:;" onClick="<?if($status=="edit"){?>window.open('giftinfo/add_giftinfo.php?id_sold=<?=$obj->getIdToText($pdsid,"c_bpds_link","bpds_id","tb_id","tb_name=\"c_saleproduct\"",false)?>&gifttype_id=<?=$GLOBALS["global_gifttypeid"]?>&tb_name=c_saleproduct','NewGiftWindows',
				'height=450,width=350,resizable=0,scrollbars=1');<?}else{?>alert('Please add new sales item before sell gift');<?}?>" class="button"/>&nbsp;&nbsp;<input type="button" name="browseallgift" value="Browse All" title="view all gift certificate information" href="javascript:;" onClick="window.open('giftinfo/manage_giftinfo.php?chkpage=1','NewAllGiftWindows',
				'height=700,width=1020,resizable=0,scrollbars=1');"	class="button"/>
			</td>
		  </tr><tr>
           <td style=" vertical-align:top;" align="right" width="65%">
           	   <div class="comment" style="height:100px">
               	<table cellpadding="0" cellspacing="0" class="comment" width="100%">
                	<tr>
                      <td class="mainthead">Gift Number</td>
                      <td class="mainthead">Value</td>
                      <td class="mainthead">Product</td>
                      <td class="mainthead">Gift From</td>
                      <td class="mainthead">Gift Type</td>
                      <td class="mainthead">Expired</td>
                    </tr>
                    <?
                    
					if($rsGift["rows"]!=0){
						for($i=0;$i<$rsGift["rows"];$i++){
						 $giftType = $obj->getIdToText($rsGift[$i]["gifttype_id"],"gl_gifttype","gifttype_name","gifttype_id");
						 if(($i%2)==0){
		                    echo "<tr class=\"content_list\">";
						 }else{
						 	echo "<tr class=\"content_list1\">";
						 } 
						 echo "<td style=\"color: rgb(85, 160, 255);\" align=\"left\">".$rsGift[$i]["gift_number"]."</td>
		                    <td style=\"color: rgb(85, 160, 255);\" height=\"25px\" align=\"left\">".$rsGift[$i]["value"]."</td>
		                    <td style=\"color: rgb(85, 160, 255);\" align=\"left\">".$rsGift[$i]["product"]."</td>
		                    <td style=\"color: rgb(85, 160, 255);\" align=\"left\">".$rsGift[$i]["receive_from"]."</td>
		                    <td style=\"color: rgb(85, 160, 255);\" align=\"left\">".$giftType."</td>
		                 	<td style=\"color: rgb(85, 160, 255);\" align=\"left\">".$dateobj->convertdate($rsGift[$i]["expired"],'Y-m-d',$sdateformat)."</td>
		                   </tr>";
						}
					}
                    ?>
         		 </table>   
         		 </div> 
            </td>
           </tr>
          </table>     
           </fieldset>
           <!-- End table show Gift Detail -->
           </div>
              </td>
          </tr>
		  
		<?}?>
		</td>
    </tr>
    <tr>
    <td colspan="2">
    <br />
    <div class="group5" width="100%" >
    
    <fieldset>
    <legend><b>Sales Receipt</b></legend>
    <table cellspacing="0" cellpadding="0" >
      <tr>
        <td><table cellspacing="0" cellpadding="0" width="160px" class="cusinfo">
            <tr>
				<td>
				<b>Barcode Scan</b>
				</td>
			</tr>
			<tr>
              <td>
              	<input type='button' name='addsr' value=' Add ' class='button' onClick="addSr(1,'<?=$status?>');this.form.submit();" title="add new sales receipt">
              <!--  <input type="button" name="calsrd" id="calsrd" value=" Calculate " class='button' onClick="miniwindow('calacc.php?branch_id=<?=$cs["branch"]?>','discount','400','430','0','100','100','0')" title="auto calculate each product prices discount sc/vat"/> -->
              </td>
            </tr>

<?
$maxsrdcount = 0;
$maxmpdcount = 0;
for($i=0; $i<$srcount; $i++) {
	//$srdcount = $obj->getParameter("srdcount");
	if($srdcount[$i]>$maxsrdcount){$maxsrdcount=$srdcount[$i];}
	if($mpdcount[$i]>$maxmpdcount){$maxmpdcount=$mpdcount[$i];}
}
?>
            <tr>
              <td>Product/Qty/Price <span class="style1">*</span></td>
            </tr>
            <? for($a=1; $a<$maxsrdcount; $a++){echo "<tr id='clone_mrow'><td>&nbsp;</td></tr>";}?>
            <!--
            <tr>
              <td align="right">Sub-Total</td>
            </tr>
            <tr>
              <td align="right">Service Charges (
                <?=$servicescharge?>
                %)</td>
            </tr>
            <tr>
              <td align="right">Tax (
                <?=$obj->getIdToText($taxpercent,"l_tax","tax_percent","tax_id")?>
                %)</td>
            </tr>
            <tr>
              <td align="right">Payment</td>
            </tr>
            -->
            <tr id="mrow">
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td align="right">Total</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>Method of Payment <span class="style1">*</span> </td>
            </tr>
             <? for($a=1; $a<$maxmpdcount; $a++){echo "<tr><td>&nbsp;</td></tr>";}?>
            <tr>
              <td>Comment </td>
            </tr>
            <tr>
              <td>Paid</td>
            </tr>
            <? if($status=="edit"){?>
            <tr>
            	<td>Paid tick by</td>
            </tr>
            <tr>
                <td>Date : Time</td>
            </tr>
            <? } ?>
            <tr>
              <td>Print Preview <span class="style1">*</span> </td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
          </table></td>
<?
for($i=0; $i<$srcount; $i++) {
	if(!$srdcount[$i])
		$srdcount[$i] = 1;

//For debug undefined index : . By Ruck : 19-05-2009
if(!isset($srd[$i][0]["sr_id"])){$srd[$i][0]["sr_id"]="";}
if(!isset($srd[$i][0]["comment"])){$srd[$i][0]["comment"]="";}
if(!isset($srd[$i][0]["paid"])){$srd[$i][0]["paid"]="";}
if(!isset($sumprice[$i])){$sumprice[$i]=0;}
if(!isset($srd[$i][0]["paytype"])){$srd[$i][0]["paytype"]=1;}
if(!isset($srd[$i][0]["pay_price"])){$srd[$i][0]["pay_price"]="";}

	$chkSR=true;
		$rsChk=$obj->getIdToText($srd[$i][0]["sr_id"],"c_salesreceipt","salesreceipt_id","salesreceipt_id","paid_confirm=1");
		if($rsChk){
				$chkSR=false;
							
		}else{
			$chkSREdit=true;
		}
		$srnumber = $obj->getIdToText($srd[$i][0]["sr_id"],"c_salesreceipt","salesreceipt_number","salesreceipt_id");
		
?>
        <td><table cellspacing="0" cellpadding="0" width="275px" class="cusinfo">
            <tr>
				<td>
				<input type="text" class="barcode" id="barcode[<?=$i?>]" name="barcode[<?=$i?>]" value="" onKeypress="checkKey(this,'pdforsale','<?=$i?>',event,'<?=$cs["branch"]?>')">
				<input type="button" name="setFocus" value="Scan" onClick="focusIt('<?=$i?>');">
				</td>
			</tr>
			<tr>
              <td align="center" title="Sales Receipt number will generate when sales receipt is printed in the same date with appointment date.">
              <input type='hidden' name='srdcount[<?=$i?>]' id='srdcount[<?=$i?>]' value='<?=$srdcount[$i]?>' />
                <b>
                <?=($i+1)?> <?=($srnumber)?"- $srnumber":""?>
                </b> </td>
            </tr>
            <? for($j=0; $j<$srdcount[$i]; $j++){ 
            	// debugging all undified index natt - June 11,2009
					
				if(!isset($srd[$i][$j]["quantity"])){$srd[$i][$j]["quantity"]=1;}
				if(!isset($srd[$i][$j]["pd_id"])){$srd[$i][$j]["pd_id"]=1;}
				if(!isset($srd[$i][$j]["plus_sc"])){$srd[$i][$j]["plus_sc"]=1;}
				if(!isset($srd[$i][$j]["plus_tax"])){$srd[$i][$j]["plus_tax"]=1;}
				if(!isset($srd[$i][$j]["pd_id_tmp"])){$srd[$i][$j]["pd_id_tmp"]="";}
				if(!isset($srd[$i][$j]["unit_price"])){$srd[$i][$j]["unit_price"]=0;}
				
				if(!isset($srd[$i][$j]["mpd_id"])){$srd[$i][$j]["mpd_id"]="";}
            	if(!isset($srd[$i][$j]["paytype"])){$srd[$i][$j]["paytype"]=1;}
				if(!isset($srd[$i][$j]["pay_price"])){$srd[$i][$j]["pay_price"]="";}
				
            	//For debug undefined index : . By Ruck : 19-05-2009
				if(!isset($srd[$i][$j]["srd_id"])){$srd[$i][$j]["srd_id"]="";}
            	
            	?>
            <tr>
              <td class = "fix">
              <span style="width: 115px;font-family:Tahoma; font-size: 11px;overflow:hidden;">     
              <?=$obj->makeListbox("srd[$i][$j][pd_id]","cl_product_category,cl_product","pd_name","pd_id",$srd[$i][$j]["pd_id"],$chkautosubmit,"cl_product_category.pd_category_priority, cl_product.pd_name","cl_product.pd_active",1,"cl_product.pd_category_id=cl_product_category.pd_category_id",false,!$chkSR)?>
              </span>
                /
                <input size="1" id= "srd[<?=$i?>][<?=$j?>][quantity]" name="srd[<?=$i?>][<?=$j?>][quantity]" value="<?=($srd[$i][$j]["quantity"])?$srd[$i][$j]["quantity"]:"1"?>" maxlength="3" type="text" style="width:20px" <?=($chkSR)?"":"disabled"?> onChange="checkqtyNum(this);this.form.submit();"/>
                /
                <input size="7" id="srd[<?=$i?>][<?=$j?>][unit_price]" name="srd[<?=$i?>][<?=$j?>][unit_price]" value="<?=($srd[$i][$j]["unit_price"])?$srd[$i][$j]["unit_price"]:"0"?>" type="text" style="width:50px" <?=($chkSR)?"":"disabled"?> onChange="checkpriceNum(this);this.form.submit();"/>
               
               <!-- Link for set plus sc and tax -->
               <?
               /////////// For set calculate with tax and servicecharge or not /////////////
               //echo "<br>Pd id : ".$srd[$i][$j]["pd_id"];
               if(!isset($srd[$i][$j]["set_sc"])){$srd[$i][$j]["set_sc"]=0;}
                if(!isset($srd[$i][$j]["set_tax"])){$srd[$i][$j]["set_tax"]=0;}
               /*if($srd[$i][$j]["pd_id"]!=1 && $srd[$i][$j]["pd_id"]!=""){
               		if($chkSR){
	               		if($srd[$i][$j]["set_sc"]){
	               			?><a href="javascript:;" onClick="javascript:setScProduct('srd[<?=$i?>][<?=$j?>][plus_sc]','pdforsale');" class="top_menu_link"><img src="../images/<?=($srd[$i][$j]["plus_sc"])?"active":"inactive"?>.gif" title="Service Charge" border="0"></a> <? 			
	               		}else{
	               			echo "<img src=\"../images/non.gif\" title=\"Service Charge\" border=\"0\">&nbsp;";	
	               		}
	               }else{
	               		if($obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_sc","pd_id")){
							?><img src="../images/<?=($obj->getIdToText($srd[$i][$j]["srd_id"],"c_srdetail","set_sc","srdetail_id"))?"active":"inactive"?>.gif" title="Service Charge" border="0"> <?		
	               		}else{
	               			if($srd[$i][$j]["plus_sc"]){
	               				echo "<img src=\"../images/active.gif\" title=\"Service Charge\" border=\"0\">&nbsp;";
	               			}else{
	               				echo "<img src=\"../images/non.gif\" title=\"Service Charge\" border=\"0\">&nbsp;";	
	               			}
							
	               		}
	               			
	               }
               }
               if($srd[$i][$j]["pd_id"]!=1 && $srd[$i][$j]["pd_id"]!=""){
               		if($chkSR){
	               		if($srd[$i][$j]["set_tax"]){
	               			?><a href="javascript:;" onClick="javascript:setTaxProduct('srd[<?=$i?>][<?=$j?>][plus_tax]','pdforsale');" class="top_menu_link"><img src="../images/<?=($srd[$i][$j]["plus_tax"])?"active":"inactive"?>.gif" title="Tax" border="0"></a><?               			
	               		}else{
	               			echo "<img src=\"../images/non.gif\" title=\"Tax\" border=\"0\">";
	               		}
	               }else{
	               		if($obj->getIdToText($srd[$i][$j]["pd_id"],"cl_product","set_tax","pd_id")){
		            		?><img src="../images/<?=($obj->getIdToText($srd[$i][$j]["srd_id"],"c_srdetail","set_tax","srdetail_id"))?"active":"inactive"?>.gif" title="Tax" border="0"><?
		            	}else{
		            		if($srd[$i][$j]["plus_tax"]){
		            			echo "<img src=\"../images/active.gif\" title=\"Tax\" border=\"0\">";
		            		}else{
		            			echo "<img src=\"../images/non.gif\" title=\"Tax\" border=\"0\">";	
		            		}
							
		            	}
	               }
               }*/
               
               ?>
                <input type='hidden' id="srd[<?=$i?>][<?=$j?>][plus_sc]" name="srd[<?=$i?>][<?=$j?>][plus_sc]" value='<?=$srd[$i][$j]["plus_sc"]?>'>
                <input type='hidden' id="srd[<?=$i?>][<?=$j?>][plus_tax]" name="srd[<?=$i?>][<?=$j?>][plus_tax]" value='<?=$srd[$i][$j]["plus_tax"]?>'>
               	<input type='hidden' id="srd[<?=$i?>][<?=$j?>][srd_id]" name="srd[<?=$i?>][<?=$j?>][srd_id]" value='<?=$srd[$i][$j]["srd_id"]?>'>
                <input type='hidden' id="srd[<?=$i?>][<?=$j?>][pd_id_tmp]" name="srd[<?=$i?>][<?=$j?>][pd_id_tmp]" value='<?=$srd[$i][$j]["pd_id_tmp"]?>'>
              </td>
              
              <?if(!$chkSR && ($j<($srdcount[$i]-1))){?>
              	<input type='hidden' name='srd[<?=$i?>][<?=$j?>][pd_id]' id='srd[<?=$i?>][<?=$j?>][pd_id]' value='<?=$srd[$i][$j]["pd_id"]?>' />
                <input name="srd[<?=$i?>][<?=$j?>][quantity]" value="<?=($srd[$i][$j]["quantity"])?$srd[$i][$j]["quantity"]:"1"?>" type="hidden"/>
               	<input id="srd[<?=$i?>][<?=$j?>][unit_price]" name="srd[<?=$i?>][<?=$j?>][unit_price]" value="<?=($srd[$i][$j]["unit_price"])?$srd[$i][$j]["unit_price"]:"0"?>" type="hidden" />
              <?}?>
            </tr>
            <? } ?>
            <? for($k=0; $k<($maxsrdcount-$srdcount[$i]); $k++){echo "<tr id='clone_brow[".$i."]'><td>&nbsp;</td></tr>";}?>
            <!--
            <tr>
              <td align="right" style="padding-right:65px"><?=number_format($r_amount[$i],2,".",",")?>
                ฿</td>
            </tr>
            <tr>
              <td align="right" style="padding-right:65px"><?=number_format($r_svc[$i],2,".",",")?>
                ฿</td>
            </tr>
            <tr>
              <td align="right" style="padding-right:65px"><?=number_format($r_tax[$i],2,".",",")?>
                ฿</td>
            </tr>
            <tr>
              <td align="right" style="padding-right:65px"><?=number_format($r_payment[$i],2,".",",")?>
                ฿</td>
            </tr>
            -->
            <tr id="brow[<?=$i?>]">
              <td>&nbsp;</td>
            </tr>
            <tr>
 				<?
 				if(number_format($r_total[$i],2,".",",")==(0.00)){
 					$r_total[$i]=abs(number_format($r_total[$i],2,".",","));
 				}
 				?>
              <td align="right" style="padding-right:65px"><span id="total[<?=$i?>]"><?=number_format($r_total[$i],2,".",",")?></span>
              <input type='hidden' id="srd[<?=$i?>][0][sr_total]" name="srd[<?=$i?>][0][sr_total]" value='<?=$r_total[$i]?>'>
              <input type='hidden' id="srd[<?=$i?>][0][sr_total1]" name="srd[<?=$i?>][0][sr_total1]" value='<?=$rnp_total[$i]?>'>
      ฿</td>
            </tr>
            <tr>
              <td align="left"><input type='hidden' id="srd[<?=$i?>][0][subtotal]" name="srd[<?=$i?>][0][subtotal]" value='<?=$r_amount[$i]?>'></td>
            </tr>
  <? 
          
  //For Muti Payment Method: By David : 26-02-2010
  //Debug For First = Select
            if($srd[$i][0]["paytype"]==1){$mpdcount[$i]=1;}
  // 
  for($j=0; $j<$mpdcount[$i]; $j++){
       
       if(!isset($srd[$i][$j]["mpd_id"])){$srd[$i][$j]["mpd_id"]="";}
       if(!isset($srd[$i][$j]["paytype"])){$srd[$i][$j]["paytype"]=1;}
       if(!isset($srd[$i][$j]["pay_price"])){$srd[$i][$j]["pay_price"]="";}
          	
       $checksubmit = "";
       if($j==$mpdcount[$i]-1){$checksubmit = "this.form.submit();";}
  ?>
            <tr>  
             <td class = "fix" align="center">
              <span style="width: 115px;font-family:Tahoma; font-size: 11px;overflow:hidden;"> 
             <?=$obj->makeListbox("srd[$i][$j][paytype]","l_paytype","pay_name","pay_id",$srd[$i][$j]["paytype"],0,"pay_name","pay_active",1,false,false,!$chkSR,false,false,"CheckDuplicate($i,$j);findmax($i);$checksubmit")?>
             </span>&nbsp;
             <?
           			//Debug for change lower price : By David : 17-02-2010
           			if($srd[$i][0]["pay_price"]>number_format($r_total[$i],2,".","")){
    					$srd[$i][0]["pay_price"]=number_format($r_total[$i],2,".","");
    				}
    				//Set first price : By David : 17-02-2010
    				if(!$srd[$i][0]["pay_price"] && $mpdcount[$i]==1){
    					$srd[$i][0]["pay_price"]=number_format($r_total[$i],2,".","");
    				}else{
    					$sumprice[$i] = $sumprice[$i]+$srd[$i][$j]["pay_price"];					
		          		if($j==$mpdcount[$i]-1){		          			
		          			$srd[$i][$j]["pay_price"]=number_format($r_total[$i],2,".","")-number_format($sumprice[$i],2,".","");
		          		}
          			}          		
    		?>
             <input type="text" size="7" id="srd[<?=$i?>][<?=$j?>][pay_price]" name="srd[<?=$i?>][<?=$j?>][pay_price]" 
             value="<?
             if(!$srd[$i][$j]["pay_price"]){
             	if($mpdcount[$i]==1){
             		echo number_format($sumprice[$i],2,".","");
             	}else{
             		echo "0";
             	}	
             }else{
             	echo number_format($srd[$i][$j]["pay_price"],2,".","");
             	}
             ?>" type="text" style="width:50px" <?if($srd[$i][$j]["paytype"]<=1){echo "readonly";} ?> 
             onchange="checkpayNum(this);addPayPrice(<?=$i?>,<?=$j?>);findmax(<?=$i?>);chkPlus(<?=$srcount?>);" <?=($chkSR)?"":"disabled"?>/>
             
             </td>
             <input type='hidden' id="srd[<?=$i?>][<?=$j?>][mpd_id]" name="srd[<?=$i?>][<?=$j?>][mpd_id]" value='<?=$srd[$i][$j]["mpd_id"]?>'>
             <input type='hidden' name='pdcount[<?=$j?>]' id='pdcount[<?=$j?>]' value='<?=$j;?>' />
            </tr>
            
             <?if(!$chkSR && ($j<($mpdcount[$i]-1))){?>
              	<input type='hidden' name='srd[<?=$i?>][<?=$j?>][paytype]' id='srd[<?=$i?>][<?=$j?>][paytype]' value='<?=$srd[$i][$j]["paytype"]?>' />
                <input type='hidden' name='srd[<?=$i?>][<?=$j?>][pay_price]' id='srd[<?=$i?>][<?=$j?>][pay_price]' value='<?=$srd[$i][$j]["pay_price"]?>' />
             <?}?> 
 			<?}?>
 <?
 //Debug find max price
 for($j=0; $j<$mpdcount[$i]-1; $j++){
 	
 	if(!isset($srd[$i][$j]["mpd_id"])){$srd[$i][$j]["mpd_id"]="";}
    if(!isset($srd[$i][$j]["paytype"])){$srd[$i][$j]["paytype"]=1;}
    if(!isset($srd[$i][$j]["pay_price"])){$srd[$i][$j]["pay_price"]="";}
 	
 	if($j==0){
 		$max = $srd[$i][0]["pay_price"];	
 		$maxid[$i] = $srd[$i][0]["paytype"];
 	}
 		if($max<$srd[$i][$j]["pay_price"]){
 			$max = $srd[$i][$j]["pay_price"];
 			$maxid[$i] = $srd[$i][$j]["paytype"];
 		}
 }
 
 if(!isset($maxid[$i])){$maxid[$i]=1;}
 
 ?>
 <? for($k=0; $k<($maxmpdcount-$mpdcount[$i]); $k++){echo "<tr><td>&nbsp;</td></tr>";}?>
 <input type='hidden' name='srd[<?=$i?>][0][maxpaid]' id='srd[<?=$i?>][0][maxpaid]' value='<?=$maxid[$i]?>' /> 
 <input type='hidden' name='mpdcount[<?=$i?>]' id='mpdcount[<?=$i?>]' value='<?=$mpdcount[$i]?>' />
            </tr>
            <tr>
              <td align="center"><input name="srd[<?=$i?>][0][comment]" id="srd[<?=$i?>][0][comment]" value="<?=$srd[$i][0]["comment"]?>" <?=($chkSR)?"":"disabled"?> type="text">
              </td>
            </tr>
            <tr>
  			  <td align="center"><input name="srd[<?=$i?>][0][paid]" id="srd[<?=$i?>][0][paid]" value="checked" <?=($chkSREdit)?"":"disabled"?> type="checkbox" <? if($srd[$i][0]["paid"]){echo "checked";}?> onChange="Chk_paid(<?=$i?>);"/>
  			  </td>
            </tr>
            <? if($status=="edit"){?>
            <tr>
              <td align="center">
              <?
              	if($srd[$i][0]["sr_id"]){
              		$sr_lu_user_id = $obj->getIdToText($srd[$i][0]["sr_id"],"c_salesreceipt","sr_lu_user","salesreceipt_id");
	              	$sr_lu_user = $obj->getIdToText($sr_lu_user_id,"s_user","u","u_id");
	              	if($sr_lu_user){
	              		echo $sr_lu_user;	
	              	}else{
	              		echo "-";
	              	}	
              	}else{
              		echo "-";
              	}
              	
              ?>
  			  </td>
            </tr>
            <tr>
              <td align="center">
				<?
				if($srd[$i][0]["sr_id"]){
					$paidDate =$obj->getIdToText($srd[$i][0]["sr_id"],"c_salesreceipt","sr_datets","salesreceipt_id");
					if($paidDate!="0000-00-00 00:00:00"){
						list($date,$time) =  split('[ ]', $paidDate);
						$commenttime = $dateobj->timezone_depend_branch($date,$time,"$ldateformat H:i:s",$cs["branch"]);	
						//$date = $dateobj->convertdate($date,'Y-m-d',$ldateformat);
						//$time = $dateobj->converttime($time,'H:i:s','H:i:s');
            			echo  $commenttime; //$date.", ".$time;	
					}else{
						echo "-";
					}
				}else{
					echo "-";
				}	
					
				?>
  			  </td>
            </tr>
            <? } ?>
            <tr>
              <? if($srd[$i][0]["sr_id"]){ ?>
              <td align="center"><a href='javascript:;' class="worksheet" onClick="pdsaleReceiptWindow(<? echo  "'$pdsid','".$srd[$i][0]["sr_id"]."','$status'";?>)">&nbsp;(Print)</a>
              <?}else{?>
               <td align="center">&nbsp;</td>
              <?}?>
              <? if($srd[$i][0]["sr_id"]){ ?>
              <a href="javascript:;" onClick="miniwindow('srpreviewlog.php?sr_id='+<?=$srd[$i][0]["sr_id"]?>,'srpreviewlog','400','460','0','100','100','0')">
              <img src="/images/icon_history.gif" width="16px" height="16px" class="link"/></a>
              <? } ?>
              </td>
            </tr>
            <tr>
              <td><input type='hidden' id="srd[<?=$i?>][0][sr_id]" name="srd[<?=$i?>][0][sr_id]" value='<?=$srd[$i][0]["sr_id"]?>'>
              <?if(!$chkSR){?>
              	<input id="srd[<?=$i?>][<?=$j?>][paytype]" name="srd[<?=$i?>][<?=$j?>][paytype]" value="<?=$srd[$i][$j]["paytype"]?>" type="hidden" />
              	<input name="srd[<?=$i?>][0][comment]" id="srd[<?=$i?>][0][comment]" value='<?=$srd[$i][0]["comment"]?>' type="hidden">
              	
              <?}
              	if(!$chkSREdit){?>
              		<input name="srd[<?=$i?>][0][paid]" value="checked" type="hidden" <? if($srd[$i][0]["paid"]){echo "checked";}?> />
              <?}?>
              	<input name="srd[<?=$i?>][0][now_check_paid]" id="srd[<?=$i?>][0][now_check_paid]" value='<?=($srd[$i][0]["paid"])?"1":"0"?>' type="hidden">
              </td>
            </tr>
            </table>
           </td>
          <? } ?>
    </table>
    </td>
    </tr>
    
  </table>
  <input type='hidden' id="chkFirst" name="chkFirst" value='first'>
  <input type='hidden' id="srcount" name="srcount" value='<?=$srcount?>'>
  <input type="hidden" id="lastscan" name="lastscan" value="0">
  </td>
  </tr>
  </table>
</form>
</body>
</html>
