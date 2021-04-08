<?
	include("../../include.php");
	require_once("formdb.inc.php");

	$obj = new formdb(); 
	$picName=htmlentities($_GET["name"]);
	$column="`company_logo`";
	if($picName=="currency_symbol"){$column="`currency_symbol`";}
	else if($picName=="company_logo"){$column="`company_logo`";}
	//$picId=1;
	$strSQL = "SELECT $column FROM a_company_info ";
	$strSQL .= "WHERE company_id=1";
	//echo $strSQL;
	$rs=$obj->getResult($strSQL,false);
	if($rs[0]["symbol_type"]="image/jpeg" ||$rs[0]["logo_type"]="image/jpeg"){
		header( "Content-type:image/jpeg" ); 
	}
	if($rs[0]["symbol_type"]="image/pjpeg" ||$rs[0]["logo_type"]="image/pjpeg"){
		header( "Content-type:image/jpeg" ); 
	}
	if($rs[0]["symbol_type"]="image/gif" ||$rs[0]["logo_type"]="image/gif"){
		header( "Content-type:image/gif" ); 
	}
	if($rs[0]["symbol_type"]="image/png" ||$rs[0]["logo_type"]="image/png"){
		header( "Content-type:image/png" ); 
	}
	echo $rs[0]["$picName"];
?>
