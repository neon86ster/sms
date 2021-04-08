<?
include("../include.php");

$obj->setErrorMsg("");
$pagename = "csi.php";
if(!isset($_REQUEST["book_id"])){$_REQUEST["book_id"]="";}
if(!isset($_REQUEST["indivi_id"])){$_REQUEST["indivi_id"]="";}
$book_id = $_REQUEST["book_id"];
$indivi_id = $_REQUEST["indivi_id"];
$branchid = $obj->getParameter("branch_id");
/***************************************************
 * Security checking
 ***************************************************/
// check user permission 
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

// check reservtion edit date limit 
$date = $obj->getIdToText($book_id,"a_bookinginfo","b_appt_date","book_id");
$preEditDate="";
$afterEditDate="";
// checking if appt_editchk was check
$now=$dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$sdateformat",$branchid);
$chkRsEditDate = $object->isReservationLimit("appt_editchk");
if($chkRsEditDate){
	$preEditDate= $object->getReservationDate("pre_editdate","appt_editchk");
	$afterEditDate= $object->getReservationDate("after_editdate","appt_editchk");
	$chkRsDate= $object->checkReservationDate($date,'Y-m-d',$preEditDate,$afterEditDate,$now);
	if(!$chkRsDate){
		$chkPageEdit=false;
	}
}


/***************************************************
 * Initial all information
 ***************************************************/
$errorMsg="";
$ip = $_SERVER["REMOTE_ADDR"];
$userid = $obj->getUserIdLogin();
$add = $obj->getParameter("add");
if($add==" save change " && $chkPageEdit){
	$condition = $obj->getParameter("condition");
	$condition_other = $obj->getParameter("condition_other");
	$recommend = $obj->getParameter("recommend");
	$recommend_other = $obj->getParameter("recommend_other");
	$s_greeting = $obj->getParameter("s_greeting");
	$s_manner = $obj->getParameter("s_manner");
	$s_atten = $obj->getParameter("s_atten");
	$s_friend = $obj->getParameter("s_friend");
	$s_driver = $obj->getParameter("s_driver");
	$q_mg = $obj->getParameter("q_mg");
	$q_tr = $obj->getParameter("q_tr");
	$q_value = $obj->getParameter("q_value");
	$at_clean = $obj->getParameter("at_clean");
	$at_aroma = $obj->getParameter("at_aroma");
	$at_m = $obj->getParameter("at_m");
	$at_temp = $obj->getParameter("at_temp");
	$at_fac = $obj->getParameter("at_fac");
	$csi_comment = $obj->getParameter("csi_comment");
	$row = $obj->getRowFromId($indivi_id,"f_csi","indivi_id");
	if($row==1){
		$sql = "update f_csi set " .
				"condition_id=$condition, " .
				"condition_other=\"$condition_other\", " .
				"rec_id=$recommend, " .
				"rec_other=\"$recommend_other\", " .
				"s_greeting=$s_greeting, " .
				"s_manner=$s_manner, " .
				"s_attentive=$s_atten, " .
				"s_friendly=$s_friend, " .
				"s_driver=$s_driver, " .
				"q_mg=$q_mg, " .
				"q_tr=$q_tr, " .
				"q_value=$q_value, " .
				"at_clean=$at_clean, " .
				"at_aroma=$at_aroma, " .
				"at_m=$at_m, " .
				"at_temp=$at_temp, " .
				"at_fac=$at_fac, " .
				"csi_comment=\"$csi_comment\"" .
				"where indivi_id=$indivi_id";
	}else{
		$sql = "insert into f_csi(book_id,indivi_id," .
				"condition_id,condition_other,rec_id,rec_other," .
				"s_greeting,s_manner,s_attentive,s_friendly,s_driver," .
				"q_mg,q_tr,q_value," .
				"at_clean,at_aroma,at_m,at_temp,at_fac," .
				"csi_comment," .
				"l_lu_user,l_lu_ip,l_lu_date) values(" .
				"$book_id,$indivi_id," .
				"$condition,\"$condition_other\",$recommend,\"$recommend_other\"," .
				"$s_greeting,$s_manner,$s_atten,$s_friend,$s_driver," .
				"$q_mg,$q_tr,$q_value," .
				"$at_clean,$at_aroma,$at_m,$at_temp,$at_fac," .
				"\"$csi_comment\"," .
				"$userid,\"$ip\",now())";
	}
	//echo $sql;
	$id = $obj->setResult($sql);	
	
}else{
	$errorMsg = "Session time out. <br>Please longin and try again.";
}

	$sql = "select * from d_indivi_info where indivi_id=$indivi_id";
	$rs = $obj->getResult($sql);
	$csname = $rs[0]["cs_name"];
	$csroom = $obj->getIdToText($rs[0]["room_id"],"bl_room","room_name","room_id");
	$sql = "select * from da_mult_th where indivi_id=$indivi_id";
	$rs_th = $obj->getResult($sql);
	$therapist = "";
	for($i=0;$i<$rs_th["rows"];$i++){
		$therapist .= $obj->getIdToText($rs_th[$i]["therapist_id"],"l_employee","emp_nickname","emp_id");
		if($i<$rs_th["rows"]-1){$therapist .= ", ";}
	}
	//echo $therapist;
	$sql = "select * from f_csi where indivi_id=$indivi_id";
	$rs_csi = $obj->getResult($sql);
	$condition = $rs_csi[0]["condition_id"];
	$condition_other = $rs_csi[0]["condition_other"];
	$recommend = $rs_csi[0]["rec_id"];
	$recommend_other = $rs_csi[0]["rec_other"];
	$s_greeting = $rs_csi[0]["s_greeting"];
	$s_manner = $rs_csi[0]["s_manner"];
	$s_atten = $rs_csi[0]["s_attentive"];
	$s_friend = $rs_csi[0]["s_friendly"];
	$s_driver = $rs_csi[0]["s_driver"];
	$q_mg = $rs_csi[0]["q_mg"];
	$q_tr = $rs_csi[0]["q_tr"];
	$q_value = $rs_csi[0]["q_value"];
	$at_clean = $rs_csi[0]["at_clean"];
	$at_aroma = $rs_csi[0]["at_aroma"];
	$at_m = $rs_csi[0]["at_m"];
	$at_temp = $rs_csi[0]["at_temp"];
	$at_fac = $rs_csi[0]["at_fac"];
	$csi_comment = $rs_csi[0]["csi_comment"];
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="scripts/ajax.js"></script>
<script type="text/javascript" src="scripts/component.js"></script>
<title>CSI</title>
</head>

<body>
<form action="<?=$pagename?>" method="post">
<br/>
<input type="hidden" id="book_id" name="book_id" value="<?=$book_id?>"/>
<input type="hidden" id="indivi_id" name="indivi_id" value="<?=$indivi_id?>"/>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td colspan="2" valign="top" width="40%">
     <div class="group5" width="100%" >
          <fieldset>
          <legend><b>Customer Information</b></legend>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td style="vertical-align:middle;" width="50%"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="cusinfo1">
                <tr>
                  <td style="vertical-align:middle;" width="130"><strong>Customer name: </strong></td>
                  <td><b class="style1"><?=$csname?></b></td>
                  </tr>
                <tr>
                  <td><strong>Room name: </strong></td>
                  <td><b class="style1"><?=$csroom?></b></td>
                  </tr>
                <tr>
                  <td><strong>Therapist name: </strong></td>
                  <td><b class="style1"><?=$therapist?></b></td>
                  </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  </tr>
              </table></td>
              <td style="vertical-align:middle;"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="cusinfo1">
                <tr>
                  <td>Condition</td>
                  <td><span class="cc">
                    <?=$obj->makeListbox("condition","fl_csi_condition","condition_name","condition_id",$condition,0,"condition_name")?>
                  </span></td>
                </tr>
                <tr>
                  <td>Other Condition</td>
                  <td><input type="text" name="condition_other" size="20" value="<?=$condition_other?>" /></td>
                </tr>
                <tr>
                  <td>Recommendation</td>
                  <td><span class="cc">
                    <?=$obj->makeListbox("recommend","fl_csi_recommend","rec_name","rec_id",$recommend,0,"rec_name")?>
                  </span></td>
                </tr>
                <tr>
                  <td>Other Recommendation</td>
                  <td><input type="text" name="recommend_other" size="20" value="<?=$recommend_other?>" /></td>
                </tr>
              </table></td>
            </tr>
          </table>
          <br/>
          </fieldset>
      </div>    </td>
    </tr>
<? 
$chksql = "select * from fl_csi_index where csii_active=1 order by csii_column_name"; 
$chkrs = $obj->getResult($chksql);
$csiindex = array();
for($i=0;$i<$chkrs["rows"];$i++){
	$csiindex[$chkrs[$i]["csii_column_name"]] = $chkrs[$i]["csii_name"];
}
?>
<tr>
  <td valign="top"><div class="group5" >
    <fieldset>
    <legend><b>Service</b></legend>
      <table border="0" cellspacing="0" cellpadding="0" class="cusinfo1">
      <tr>
        <td width="130" ><?=$csiindex["s_greeting"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("s_greeting","fl_csi_value","csiv_name","csiv_id",$s_greeting,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
      <tr>
        <td><?=$csiindex["s_manner"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("s_manner","fl_csi_value","csiv_name","csiv_id",$s_manner,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
      <tr>
        <td><?=$csiindex["s_attentive"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("s_atten","fl_csi_value","csiv_name","csiv_id",$s_atten,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
      <tr>
        <td><?=$csiindex["s_friendly"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("s_friend","fl_csi_value","csiv_name","csiv_id",$s_friend,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
      <tr>
        <td><?=$csiindex["s_driver"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("s_driver","fl_csi_value","csiv_name","csiv_id",$s_driver,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
    </table>
      </fieldset><br/>
      <fieldset>
    <legend><b>Quantity</b></legend>
      <table border="0" cellspacing="0" cellpadding="0" class="cusinfo1">
      <tr>
        <td><?=$csiindex["q_mg"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("q_mg","fl_csi_value","csiv_name","csiv_id",$q_mg,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
      <tr>
        <td><?=$csiindex["q_tr"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("q_tr","fl_csi_value","csiv_name","csiv_id",$q_tr,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
      <tr>
        <td><?=$csiindex["q_value"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("q_value","fl_csi_value","csiv_name","csiv_id",$q_value,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
    </table>
      </fieldset>
  </div></td>
  <td valign="top" width="50%"><div class="group5" width="100%" >
    <fieldset>
    <legend><b>Atmosphere</b></legend>
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="cusinfo1">
      <tr>
        <td><?=$csiindex["at_clean"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("at_clean","fl_csi_value","csiv_name","csiv_id",$at_clean,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
      <tr>
        <td><?=$csiindex["at_aroma"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("at_aroma","fl_csi_value","csiv_name","csiv_id",$at_aroma,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
      <tr>
        <td><?=$csiindex["at_m"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("at_m","fl_csi_value","csiv_name","csiv_id",$at_m,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
      <tr>
        <td><?=$csiindex["at_temp"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("at_temp","fl_csi_value","csiv_name","csiv_id",$at_temp,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
      <tr>
        <td><?=$csiindex["at_fac"]?></td>
        <td><span class="cc">
          <?=$obj->makeListbox("at_fac","fl_csi_value","csiv_name","csiv_id",$at_fac,0,"order_state,csiv_value desc")?>
        </span></td>
      </tr>
    </table>
      </fieldset><br/>
      <fieldset>
    <legend><b>Comments</b></legend>
    <table border="0" cellspacing="0" cellpadding="0" class="cusinfo1">
    <tr>
      <td width="150">Comments or Suggestions</td>
      <td><textarea rows="5" name="csi_comment"><?=$csi_comment?></textarea></td>
    </tr>
  </table></fieldset>
  </div></td>
</tr>
<tr>
  <td colspan="2" align="center">
 	 <div class="group5" align="center" >
                <fieldset>
					<legend> </legend>
					<? if($chkPageEdit){?>
						<br/>
						<input name="add" id="add" type="submit" size="" value=" save change " onClick="this.form.submit()" >
						<input name="cancel" id="cancel" type="submit" size="" value=" cancel " onClick="window.close()" >  
	                <?}?>
                </fieldset>
			</div>
  </td>
  </tr>
</table>
</form>
</body>
