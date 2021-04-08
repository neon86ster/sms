<?php
/*
 * Created on May 25, 2009
 *
 * update therapist - treatment approve
 */
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
 
 
$thid = $obj->getParameter("thid");
$trmtype = $obj->getParameter("trmtype");
$trmid = $obj->getParameter("trmid");
$method = $obj->getParameter("method");
$thappid = $obj->getParameter("thappid");
$user = $_SESSION["__user_id"];
$ip = $_SERVER["REMOTE_ADDR"];
if($method=="add"&&$trmid!=1){
	if($trmtype=="1" || $trmtype=="4" || $trmtype=="5"){
		$thappid = $obj->getIdToText($trmtype,"bl_th_app","thapp_id","trm_category_id","th_id=$thid");
	}else{
		$thappid = $obj->getIdToText($trmid,"bl_th_app","thapp_id","trm_id","th_id=$thid and trm_category_id=$trmtype");
	}
	if(!$thappid){
		$chksql = "insert into bl_th_app(th_id,trm_id,trm_category_id,l_lu_user,l_lu_date,l_lu_ip)" .
				"values ('$thid','$trmid','$trmtype','$user',now(),'$ip')";
		$chkid = $obj->setResult($chksql);
	}else{
		$chksql = "update bl_th_app set" .
					" active=1," .
					" l_lu_user=$user," .
					" l_lu_date=now()," .
					" l_lu_ip='$ip'" .
					" where thapp_id=$thappid";
		$chkid = $obj->setResult($chksql);
	}
}else if($method=="delete"){
	$chksql = "update bl_th_app set" .
				" active=0," .
				" l_lu_user=$user," .
				" l_lu_date=now()," .
				" l_lu_ip='$ip'" .
				" where thapp_id=$thappid";
	$chkid = $obj->setResult($chksql);
}else if($method=="update"){
	$chksql = "update bl_th_app set" .
				" active=1," .
				" l_lu_user=$user," .
				" l_lu_date=now()," .
				" l_lu_ip='$ip'" .
				" where thapp_id=$thappid";
	$chkid = $obj->setResult($chksql);
}
if($trmtype=="0"){
$chksql = "select db_package.package_name as trm_name,bl_th_app.thapp_id,bl_th_app.active from bl_th_app,db_package " .
			"where bl_th_app.th_id=$thid " .
			"and bl_th_app.trm_id=db_package.package_id " .
			"and bl_th_app.trm_category_id=$trmtype " .
			"and bl_th_app.active=1 " .
			"order by db_package.package_name ";
}else if($trmtype=="1" || $trmtype=="4" || $trmtype=="5"){
$chksql =  "select db_trm_category.trm_category_name as trm_name,bl_th_app.thapp_id,bl_th_app.active from bl_th_app,db_trm_category " .
			"where bl_th_app.th_id=$thid " .
			"and db_trm_category.trm_category_id=bl_th_app.trm_category_id " .
			"and bl_th_app.trm_category_id=$trmtype " .
			"and bl_th_app.active=1 " .
			"order by db_trm_category.trm_category_name ";
}else{
$chksql = "select db_trm.trm_name,bl_th_app.thapp_id,bl_th_app.active from bl_th_app,db_trm " .
			"where bl_th_app.th_id=$thid " .
			"and db_trm.trm_id=bl_th_app.trm_id " .
			"and bl_th_app.trm_category_id=$trmtype " .
			"and bl_th_app.active=1 " .
			"order by db_trm.trm_name ";
}
$chkrs = $obj->getResult($chksql);
$divname = "";
switch ($trmtype) {
case 0:
    $divname = "packagediv";
    break;
case 1:
    $divname = "bathdiv";
    break;
case 2:
    $divname = "facialdiv";
    break;
case 3:
    $divname = "massagediv";
    break;
case 4:
    $divname = "scrubdiv";
    break;
case 5:
    $divname = "wrapdiv";
    break;
}

?>
<table cellspacing="0" border="0" cellpadding="0" width="100%">
<?for($i=0;$i<$chkrs["rows"];$i++){?>
	<tr height="20">
		<td class="report">
		<? if($chkrs[$i]["active"]){ ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="javascript:;" onClick="settreatmentapp('<?=$thid?>','<?=$trmtype?>','','<?=$chkrs[$i]["thapp_id"]?>','delete','<?=$divname?>');" >
			<img src="/images/active.png" border="0" title="approve" />
			</a>&nbsp;
		<? } ?> 
		<?=$chkrs[$i]["trm_name"]?> 
		</td>
	</tr>
<? } ?>
<?
 if(!$chkrs["rows"]&&($trmtype=="1" || $trmtype=="4" || $trmtype=="5")){ ?>
	<tr height="20">
		<td class="report">
 			<a href="javascript:;" onClick="settreatmentapp('<?=$thid?>','<?=$trmtype?>','0','<?=$chkrs[$i]["thapp_id"]?>','add','<?=$divname?>');" >
			<img src="/images/inactive.png" border="0" title="not approve" />
			</a>&nbsp;
 			<?=$obj->getIdToText($trmtype,"db_trm_category","trm_category_name","trm_category_id")?> 
		</td>
	</tr>
<? } ?>
</table>