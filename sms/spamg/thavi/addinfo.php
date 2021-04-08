<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("appt.inc.php");

$obj = new appt();

$pageid = "78";
$pagestatus = $object->check_permission($pageid,$permissionrs);
$chkPageEdit=false;$chkPageView=false;
if($pagestatus=="e"){
	$chkPageEdit=true;$chkPageView=true;
}else if($pagestatus=="v"){
	$chkPageEdit=false;$chkPageView=true;
}else if($pagestatus=="n"){
	$chkPageEdit=false;$chkPageView=false;
}

if($chkPageEdit){
$thid=$obj->getParameter("thid");
$signin = $obj->getParameter("signin");
$bid = $obj->getParameter("branch_name");
$status = $obj->getParameter("not");


if($signin=="Sign-in" && $bid){
	$chkthid = $obj->getIdToText($thid, "bl_th_list", "th_list_id", "th_id", "`leave` = \"0\" and `l_lu_date`>=\"" . date("Y-m-d") . "\"");
	
		if ($chkthid > 0) {
			$obj->setErrorMsg("This therapist is already sign in!!");
			?>
				<script type="text/javascript">
				opener.document.getElementById('thavi').submit();
				window.close();
				</script>
			<?
		return false;}
		$ip = $_SERVER["REMOTE_ADDR"]; // text
		$userid = $obj->getUserIdLogin(); // id
		$thshift = $obj->getIdToText("1", "a_company_info", "th_shift_hour", "company_id"); // id
		
		$now = "now()";
		
		$sql = "select * from bl_th_list " .
				"where l_lu_date>=\"" . date("Y-m-d") . "\"" .
				"and branch_id=$bid " .
				"order by queue_order desc ";
		$rs = $obj->getResult($sql);
		$next_queue = $rs[0]["queue_order"]+1;
		$sql = "insert into bl_th_list (th_id,queue_order,branch_id,th_shift,l_lu_user,l_lu_date,l_lu_ip,ot)" .
		"values($thid, $next_queue, $bid, $thshift, $userid, $now,\"$ip\",$status)";
		$insert=$obj->setResult($sql);
		
		if($insert){$successmsg = "Update data complete!!";
		?>
				<script type="text/javascript">
				opener.document.getElementById('thavi').submit();
				window.close();
				</script>
		<?
		 
		}
}

$sql="select * from l_employee where emp_id=$thid and emp_department_id=4 ";
$rs = $obj->getResult($sql);
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<body>
<!--<body>-->
<form action="" method="post">
<table width="215" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="10">&nbsp;</td>
    <td>
    <table width="205" cellpadding="3" cellspacing="0" border="0">
      <tr>
        <td align="center" colspan="2"><b>Therapist Working Branch</b></td>
      </tr>
	  
	  <tr>
        <td align="center" colspan="2" style="font-size:11px;">Your Permission is Branch's All Please Select Therapist Working Branch</td>
      </tr>
	  
	  <tr>
        <td align="left" width="60%"></b></td>
		 <td align="left" width="45%"></td>
      </tr>
	  
	  <tr><input type="hidden" name="thid" value="<?=$thid?>">
        <td align="left" style="font-size:12px;"><b>Code : </b></td>
		 <td align="left"style="font-size:12px;color:red;"><b><?=$rs[0]["emp_code"]?></b></td>
      </tr>
	  
	  <tr>
        <td align="left" style="font-size:12px;"><b>Name : </b></td>
		 <td align="left"style="font-size:12px;color:red;"><b><?=$rs[0]["emp_nickname"]?></b></td>
      </tr>
	  
	  <tr>
        <td align="left" style="font-size:12px;"><b>Branch : </b></td>
		 <td align="left"style="font-size:12px;color:red;"><b><?=$obj->getIdToText($rs[0]["branch_id"], "bl_branchinfo", "branch_name", "branch_id")?></b></td>
      </tr>
	  
	  <tr>
        <td align="left" style="font-size:12px;"><b>Branch to Sign-in: </b></td>
		 <td align="left"style="font-size:12px;color:red;"><?=$obj->makeListbox("branch_name","bl_branchinfo","branch_name","branch_id",$rs[0]["branch_id"],0,"branch_name","branch_active","1","branch_name not like 'All'",false,false)?></td>
      </tr>

		<tr>
		<td>


		</td> 
		</tr>
	  <tr>
        <td align="center" colspan="2"><br><input type="submit" name="signin" value="Sign-in">
		<input type="button" value="Cancel" onClick="window.close();">
		</td>
      </tr>
	  
    </table></td>
  </tr>
</table>
</form>
</body>
<?}?>