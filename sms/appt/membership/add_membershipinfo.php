<?
include("../../include.php");
require_once("formdb.inc.php");
$obj = new formdb(); 
$obj->setDebugStatus(false);
$filename = '../object.xml';
$errormsg ="";
// system date format	 			
$dateobj = new convertdate(); 
$chksql = "select long_date,short_date from a_company_info";
$chkrs =$obj->getResult($chksql);
$sdateformat = $obj->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
$ldateformat = $obj->getIdToText($chkrs[0]["long_date"],"l_date","date_format","date_id");

$id=$obj->getParameter("id",0);

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


/***************************************************
 * Update information into database
 ***************************************************/
if(!isset($_POST["member_code"])&&!$id){
	$_POST["member_code"]=$obj->getNextId("m_membership","member_code");
}
if(!isset($_REQUEST["method"])){$_REQUEST["method"]="";}
if(!isset($_POST["add"])){$_POST["add"]="";}

if($_REQUEST["method"]=="setactive"){
	$sql = "";
	$category_id=$_REQUEST["categoryid"];
	$sql = "update m_membership set expired=".$_REQUEST["expired"]." where member_id=".$_REQUEST["id"];
	$name = $obj->setResult($sql);
	if($name!=false){
		$name = $obj->getIdToText($_REQUEST["id"],"m_membership","member_code","member_id");
		if($_REQUEST["expired"]==1){
			$successmsg="Member code : $name is active!!";
		}else{
			$successmsg="Member code : $name is inactive!!";
		}
		$order=$obj->getParameter("order","member_code");
		$page=$obj->getParameter("page",1);
		$search=$obj->getParameter("search","");
		$successmsg.="&search=$search&order=$order&page=$page";
		$successmsg.="&show_inactive=".$showInactive."&categoryid=".$category_id;
		//echo "$successmsg<br>";
		header("Location: manage_membershipinfo.php?msg=$successmsg");
	} else {
			$errormsg = $obj->getErrorMsg();
	}
}
if($_POST["add"] == " save change "){
	$_POST["method"]="edit";
	$_POST["address"]=str_replace("\n","[br]",$_POST["address"]);
	$saveId = $obj->readToUpdate($_POST,$filename);
	if($saveId){
		?>
			<script language="javascript">
				opener.location.reload(); 
				window.close();
			</script>
		<?
	} else {
			
			$errormsg = $obj->getErrorMsg();
	}
} else if($_POST["add"]==" add ") {
	$_POST["address"]=str_replace("\n","[br]",$_POST["address"]);
	$id = $obj->readToInsert($_POST,$filename);
	if($id){
		?>
			<script language="javascript">
				opener.location.reload(); 
				window.close();
			</script>
		<?
	} else {
		$errormsg = $obj->getErrorMsg();
	}
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Booking Manager</title>
<link href="/css/style.css" rel="stylesheet" type="text/css" />
<script src="../giftinfo/scripts/ajax.js" type="text/javascript"></script>
<script src="../giftinfo/scripts/component.js" type="text/javascript"></script>
<script src="../giftinfo/scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="../giftinfo/scripts/datechooser/datechooser.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../giftinfo/scripts/datechooser/datechooser.css">
<script src="/scripts/components.js" type="text/javascript"></script>

<!--[if IE]>
<style>
span.category_id select.ctrDropDown{
    width:115px;
    font-size:12px;
}
span.category_id select.ctrDropDownClick{
    font-size:12px;

    width:auto;

}
span.category_id select.plainDropDown{
    width:115px;
    font-size:12px;
}

span.nationality_id select.ctrDropDown{
    width:115px;
    font-size:12px;
}
span.nationality_id select.ctrDropDownClick{
    font-size:12px;

    width:auto;

}
span.nationality_id select.plainDropDown{
    width:115px;
    font-size:12px;
}

</style>
<![endif]-->

</head>
<body>
<form action="<?=$_SERVER["PHP_SELF"]?>" method="post">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td class="content" width="100%">
    		<div id="showerrormsg" <? if($errormsg==""&&$_POST["add"]==false){?>style="display:none"<? } else {?>style="display:block"<? }?>>
    			<table style="border: solid 3px #ff0000;" width="100%" cellspacing="0" cellpadding="10">
    				<tr>
    					<td ><b><font style="color:#ff0000;">Error message: </font></b><?=$errormsg ?></td>
    				</tr>
    			</table>
    		</div>
        	<div>
    			<fieldset>
					<legend><b>Membership Infomation</b></legend>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style='overflow:auto'>
                    <tr align="center" height="125">
                     <td>
                        <img id="images_member" name="images_member" src="<?=$customize_part?>/images/member/<?=($id)?$obj->getIdToText($id,"m_membership","mpic","member_id"):"default.gif"?>"><br>
						<?if($id>0){?>
						<a style="text-decoration:none" href="javascript:;;" onClick="window.open('uploadmpic.php?mid=<?=$id?>','uploadpicture','height=200,width=500,resizable=0,scrollbars=1');"><b>change picture</b></a>
                        <?}?>
                    </td>
                    </tr>
                    <tbody><tr>
                    	<td valign="bottom">
							<? 
							if($id>0){
									$xml = "<command>" .
									"<table>m_membership</table>" .
									"<where name='member_id' operator='='>$id</where>" .
									"</command>";
								echo $obj->gFormEdit($xml,$filename);	 
							} else {
								echo $obj->gFormInsert('m_membership',$filename);	
							}
 							?>
                          </td>
                    </tr>
                    </tbody></table>
                </fieldset>
                <fieldset>
					<legend> </legend>
					<br>
					<div align="center">
					 <? if($chkPageEdit){?>
						<input name="id" id="id" type="hidden" value="<?=$id?>">
						<input name="add" id="add" type="submit" value="<?=($id>0)?" save change ":" add "?>">
						<input name="cancel" id="cancel" type="submit" value=" cancel " onClick="window.close();" >  
	            	<?}else if($errorMsg){
					   	echo "<b class=\"style1\">$errorMsg</b>";
					  }
					?>
	            	</div>
	            </fieldset>
			</div>
		</td>
    </tr>

</table>
</form>
</body>
</html>