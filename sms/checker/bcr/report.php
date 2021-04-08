<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
$obj = new checker();

$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid");
$payid = $obj->getParameter("payid");
if($branch_id==""){$branch_id=5;}
$export = $obj->getParameter("export",false);

if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Branch Closing Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	//$pdf->convertFromFile("manage_cplinfo3.htm");
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print");
}
$chkrow=0;
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",30);
	//$chkpage = ceil($rs["rows"]/$chkrow);
}

$rs = $obj->getcrs($branch_id,$begin_date,$end_date,$payid);
$rs_ttcs = $obj->getttcs($branch_id,$begin_date,$end_date,$payid);
$rs_tthour = $obj->gettthour($branch_id,$begin_date,$end_date,$payid);
if(!$rs_tthour){$rs_tthour[0]["total"]=0;}

if(!isset($_SESSION["__user_id"])){$_SESSION["__user_id"]="";}
$ubranch_id = $obj->getIdToText($_SESSION["__user_id"],"s_user","branch_id","u_id");
$ubranch_name = strtolower($obj->getIdToText($ubranch_id,"bl_branchinfo","branch_name","branch_id"));
if($ubranch_name!="all"){
	$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")." Daily Sales Report";
}else{
	if(!$branch_id){$branch_id=1;}	// change branch name to All if branch id is 0 
	$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")." Closing Report";
}
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
?>

<?if($begin_date==$end_date){
$date=$begin_date;
$branch = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id");

$begindate = $dateobj->convertdate(substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2),"Y-m-d","d-M-Y");

$rs = $obj->getcrs($branch_id,$date,$date);
$rs_ttcs = $obj->getttcs($branch_id,$date,$date);
$rs_tthour = $obj->gettthour($branch_id,$date,$date);

$total_sale= $obj->getParameter("total_sale");

$closing_id = $obj->getParameter("closing_id");

$cash_row = $obj->getParameter("cash_row",1);
$coin_id  = $obj->getParameter("coin_id");
$cash_qty  = $obj->getParameter("cash_qty");
$cash_value  = $obj->getParameter("cash_value");
$total_sumcash = $obj->getParameter("total_sumcash");

$currency_row = $obj->getParameter("currency_row",1);
$currency = $obj->getParameter("currency");
$rate = $obj->getParameter("rate");
$quantity_cur = $obj->getParameter("quantity_cur");
$cur_value = $obj->getParameter("cur_value");
$total_cur = $obj->getParameter("total_cur");

$total_cash = $obj->getParameter("total_cash");
$start_money = $obj->getParameter("start_money");
$tranfer_pc = $obj->getParameter("tranfer_pc");
$total_deposit = $obj->getParameter("total_deposit");

$giftinfo = $obj->getParameter("giftinfo");
$giftinfo = str_replace("<br>","\r\n",$giftinfo);
$commentinfo = $obj->getParameter("commentinfo");
$commentinfo = str_replace("<br>","\r\n",$commentinfo);
$signature =  $obj->getParameter("signature");
$signature = str_replace("<br>","\r\n",$signature);

///Save Data///
$save=$obj->getParameter("save",false);
if($save){
	$sql_check="select * from `c_closing_receipt` where " .
				"`closing_date`= '$date' and " .
				"`closing_branch_id`= '$branch_id' ";
	$rs_check=$obj->getResult($sql_check);
	if($rs_check){
	$closing_id = $rs_check[0]["closing_id"];
	}
	$db_total_cash_charges = $obj->getParameter("total_cash_charges");
	$db_out_of_balance =  $obj->getParameter("out_of_balance");
	if(!$closing_id){
	$sql_save="insert into `c_closing_receipt` (
						`closing_date` ,
						`closing_branch_id` ,
						`total_cash` ,
						`start_money` ,
						`tranfer_pc` ,
						`total_cash_deposit` ,
						`total_cash_charge` ,
						`out_of_balance` ,
						`gift_detail` ,
						`comment_detail` ,
						`signature` ," .
						"l_lu_user, " .
						"l_lu_date) values (
						'$date', '$branch_id', '$total_cash', '$start_money', '$tranfer_pc', '$total_deposit', " .
						"'$db_total_cash_charges', '$db_out_of_balance', '$giftinfo', '$commentinfo'," .
						" '$signature','".$_SESSION["__user_id"]."', Now())";

	$rs_save=$obj->setResult($sql_save);
	//Get closing_id after insert//
	$sql_check="select `closing_id` from `c_closing_receipt` where " .
				"`closing_date`= '$date' and " .
				"`closing_branch_id`= '$branch_id' ";
	$rs_chk=$obj->getResult($sql_check);
	$closing_id=$rs_chk[0]["closing_id"];
	}else{
	$sql_update="update `c_closing_receipt` set " .
			"`total_cash` = '$total_cash', " .
			"`start_money` = '$start_money', " .
			"`tranfer_pc` = '$tranfer_pc', " .
			"`total_cash_deposit` = '$total_deposit', " .
			"`total_cash_charge` = '$db_total_cash_charges', " .
			"`out_of_balance` = '$db_out_of_balance', " .
			"`gift_detail` = '$giftinfo', " .
			"`comment_detail` ='$commentinfo', " .
			"`signature` = '$signature' ," .
			"`l_lu_user` = '".$_SESSION["__user_id"]."' ," .
			"`l_lu_date` = Now() " .
			"where `closing_id`='$closing_id'";
	$rs_update=$obj->setResult($sql_update);
	}
	//Cash Table Insert/Update
	$sql_check="select `cash_id` from `c_closing_cash` where `closing_id`='".$closing_id."'";
	$chk_row=$obj->getResult($sql_check);
	if($chk_row["rows"]>$cash_row){
		$sql_del="delete from `c_closing_cash` where `cash_id` > '$cash_row' and `closing_id`='".$closing_id."'";
		$rs_del=$obj->setResult($sql_del);
	}
	for($i=0;$i<$cash_row;$i++){
		$sql_check="select `cash_id` from `c_closing_cash` where `cash_id`='".($i+1)."' and " .
				"`closing_id`='".$closing_id."'";
		$rs_chk=$obj->getResult($sql_check);

		if(!$rs_chk[0]["cash_id"]){
			$sql_insert="insert into `c_closing_cash` (`cash_id`,`closing_id`,`coin_id`,`cash_qty`,`cash_value`) values " .
					"('".($i+1)."','$closing_id','".$coin_id[$i]."','".$cash_qty[$i]."','".$cash_value[$i]."')";
			$rs_insert=$obj->setResult($sql_insert);
		}else{
			$sql_update="update `c_closing_cash` set " .
					"`coin_id` = '".$coin_id[$i]."', " .
					"`cash_qty` = '".$cash_qty[$i]."', " .
					"`cash_value` = '".$cash_value[$i]."' " .
					"where `cash_id`='".($i+1)."' and `closing_id`='".$closing_id."'";
			$rs_update=$obj->setResult($sql_update);
		}
	}
	//End Cash Table Insert/Update
	
	//Foreign Currency  Table Insert/Update
	$sql_check="select `foreign_id` from `c_closing_foreign` where `closing_id`='".$closing_id."'";
	$chk_row=$obj->getResult($sql_check);
	if($chk_row["rows"]>$currency_row){
		$sql_del="delete from `c_closing_foreign` where `foreign_id` > '$currency_row' and `closing_id`='".$closing_id."'";
		$rs_del=$obj->setResult($sql_del);
	}
	for($i=0;$i<$currency_row;$i++){
		$sql_check="select `foreign_id` from `c_closing_foreign` where `foreign_id`='".($i+1)."' and " .
				"`closing_id`='".$closing_id."'";
		$rs_chk=$obj->getResult($sql_check);

		if(!$rs_chk[0]["foreign_id"]){
			$sql_insert="insert into `c_closing_foreign` (`foreign_id`,`closing_id`,`foreign_currency`,`foreign_rate`,`foreign_qty`,`foreign_value`) values " .
					"('".($i+1)."','$closing_id','".$currency[$i]."','".$rate[$i]."','".$quantity_cur[$i]."','".$cur_value[$i]."')";
			$rs_insert=$obj->setResult($sql_insert);
		}else{
			$sql_update="update `c_closing_foreign` set " .
					"`foreign_currency` = '".$currency[$i]."', " .
					"`foreign_rate` = '".$rate[$i]."', " .
					"`foreign_qty` = '".$quantity_cur[$i]."', " .
					"`foreign_value` = '".$cur_value[$i]."' " .
					"where `foreign_id`='".($i+1)."' and `closing_id`='".$closing_id."'";
			$rs_update=$obj->setResult($sql_update);
		}
	}
	//End Foreign Currency Table Insert/Update
}
///////////////	

//Get Infomation
$status = $obj->getParameter("status");

if(!$status){
	$sql_daily="select * from `c_closing_receipt` where " .
				"`closing_date`= '$date' and " .
				"`closing_branch_id`= '$branch_id' ";
	$rs_daily=$obj->getResult($sql_daily);
	if($rs_daily){
		$closing_id=$rs_daily[0]["closing_id"];
		$total_cash=$rs_daily[0]["total_cash"];
		$start_money=$rs_daily[0]["start_money"];
		$tranfer_pc=$rs_daily[0]["tranfer_pc"];
		$total_deposit=$rs_daily[0]["total_cash_deposit"];
		$giftinfo=$rs_daily[0]["gift_detail"];
		$commentinfo=$rs_daily[0]["comment_detail"];
		$signature=$rs_daily[0]["signature"];
		$save_by=$obj->getIdToText($rs_daily[0]["l_lu_user"],"s_user","u","u_id");
		$l_lu_date=$rs_daily[0]["l_lu_date"];
		list($save_date,$save_time) =  split('[ ]', $l_lu_date);
		$save_date=$dateobj->convertdate($save_date,"Y-m-d",$ldateformat);
		$save_time = $dateobj->converttime($save_time,"H:i:s","H:i:s");
		
		$sql_cash="select * from `c_closing_cash` where closing_id ='$closing_id'";
		$rs_cash=$obj->getResult($sql_cash);
		$cash_row=$rs_cash["rows"];
		$total_sumcash=0;
		for($i=0;$i<$cash_row;$i++){
			$coin_id[$i]=$rs_cash[$i]["coin_id"];
			$cash_qty[$i]=$rs_cash[$i]["cash_qty"];
			$cash_value[$i]=$rs_cash[$i]["cash_value"];
			$total_sumcash+=$cash_value[$i];
		}
		
		$sql_for="select * from `c_closing_foreign` where closing_id ='$closing_id'";
		$rs_for=$obj->getResult($sql_for);
		$currency_row=$rs_for["rows"];
		$total_cur=0;
		for($i=0;$i<$currency_row;$i++){
			$currency[$i]=$rs_for[$i]["foreign_currency"];
			$rate[$i]=$rs_for[$i]["foreign_rate"];
			$quantity_cur[$i]=$rs_for[$i]["foreign_qty"];
			$cur_value[$i]=$rs_for[$i]["foreign_value"];
			$total_cur+=$cur_value[$i];
		}
	}else{
		$closing_id="";
		$total_cash="";
		$start_money="";
		$tranfer_pc="";
		$total_deposit="";
		$giftinfo="";
		$commentinfo="";
		$signature="";
		$cash_qty=array();
		$cash_value=array();
		$total_sumcash="";
		$currency_row=1;
		$currency=array();
		$rate=array();
		$quantity_cur=array();
		$cur_value=array();
		$total_cur="";
	}
}
//
?>
	<style>
<!--
* {
	font-family: "Microsoft Sans Serif";
	font-size: 11px;
	color: #000000;
}

td.detail {
	border:1px solid;
}

td.detail a:hover {
	text-decoration:none;
	color : #DE2418;
}

input[type=text] {
	width: 100px;
	text-align:right;
} 

textarea{
	width: 100%;
	line-height: 1.5;
}
-->
</style>
<script type="text/javascript">
(function(a,b){function cv(a){return f.isWindow(a)?a:a.nodeType===9?a.defaultView||a.parentWindow:!1}function cs(a){if(!cg[a]){var b=c.body,d=f("<"+a+">").appendTo(b),e=d.css("display");d.remove();if(e==="none"||e===""){ch||(ch=c.createElement("iframe"),ch.frameBorder=ch.width=ch.height=0),b.appendChild(ch);if(!ci||!ch.createElement)ci=(ch.contentWindow||ch.contentDocument).document,ci.write((c.compatMode==="CSS1Compat"?"<!doctype html>":"")+"<html><body>"),ci.close();d=ci.createElement(a),ci.body.appendChild(d),e=f.css(d,"display"),b.removeChild(ch)}cg[a]=e}return cg[a]}function cr(a,b){var c={};f.each(cm.concat.apply([],cm.slice(0,b)),function(){c[this]=a});return c}function cq(){cn=b}function cp(){setTimeout(cq,0);return cn=f.now()}function cf(){try{return new a.ActiveXObject("Microsoft.XMLHTTP")}catch(b){}}function ce(){try{return new a.XMLHttpRequest}catch(b){}}function b$(a,c){a.dataFilter&&(c=a.dataFilter(c,a.dataType));var d=a.dataTypes,e={},g,h,i=d.length,j,k=d[0],l,m,n,o,p;for(g=1;g<i;g++){if(g===1)for(h in a.converters)typeof h=="string"&&(e[h.toLowerCase()]=a.converters[h]);l=k,k=d[g];if(k==="*")k=l;else if(l!=="*"&&l!==k){m=l+" "+k,n=e[m]||e["* "+k];if(!n){p=b;for(o in e){j=o.split(" ");if(j[0]===l||j[0]==="*"){p=e[j[1]+" "+k];if(p){o=e[o],o===!0?n=p:p===!0&&(n=o);break}}}}!n&&!p&&f.error("No conversion from "+m.replace(" "," to ")),n!==!0&&(c=n?n(c):p(o(c)))}}return c}function bZ(a,c,d){var e=a.contents,f=a.dataTypes,g=a.responseFields,h,i,j,k;for(i in g)i in d&&(c[g[i]]=d[i]);while(f[0]==="*")f.shift(),h===b&&(h=a.mimeType||c.getResponseHeader("content-type"));if(h)for(i in e)if(e[i]&&e[i].test(h)){f.unshift(i);break}if(f[0]in d)j=f[0];else{for(i in d){if(!f[0]||a.converters[i+" "+f[0]]){j=i;break}k||(k=i)}j=j||k}if(j){j!==f[0]&&f.unshift(j);return d[j]}}function bY(a,b,c,d){if(f.isArray(b))f.each(b,function(b,e){c||bC.test(a)?d(a,e):bY(a+"["+(typeof e=="object"||f.isArray(e)?b:"")+"]",e,c,d)});else if(!c&&b!=null&&typeof b=="object")for(var e in b)bY(a+"["+e+"]",b[e],c,d);else d(a,b)}function bX(a,c,d,e,f,g){f=f||c.dataTypes[0],g=g||{},g[f]=!0;var h=a[f],i=0,j=h?h.length:0,k=a===bR,l;for(;i<j&&(k||!l);i++)l=h[i](c,d,e),typeof l=="string"&&(!k||g[l]?l=b:(c.dataTypes.unshift(l),l=bX(a,c,d,e,l,g)));(k||!l)&&!g["*"]&&(l=bX(a,c,d,e,"*",g));return l}function bW(a){return function(b,c){typeof b!="string"&&(c=b,b="*");if(f.isFunction(c)){var d=b.toLowerCase().split(bN),e=0,g=d.length,h,i,j;for(;e<g;e++)h=d[e],j=/^\+/.test(h),j&&(h=h.substr(1)||"*"),i=a[h]=a[h]||[],i[j?"unshift":"push"](c)}}}function bA(a,b,c){var d=b==="width"?a.offsetWidth:a.offsetHeight,e=b==="width"?bv:bw;if(d>0){c!=="border"&&f.each(e,function(){c||(d-=parseFloat(f.css(a,"padding"+this))||0),c==="margin"?d+=parseFloat(f.css(a,c+this))||0:d-=parseFloat(f.css(a,"border"+this+"Width"))||0});return d+"px"}d=bx(a,b,b);if(d<0||d==null)d=a.style[b]||0;d=parseFloat(d)||0,c&&f.each(e,function(){d+=parseFloat(f.css(a,"padding"+this))||0,c!=="padding"&&(d+=parseFloat(f.css(a,"border"+this+"Width"))||0),c==="margin"&&(d+=parseFloat(f.css(a,c+this))||0)});return d+"px"}function bm(a,b){b.src?f.ajax({url:b.src,async:!1,dataType:"script"}):f.globalEval((b.text||b.textContent||b.innerHTML||"").replace(be,"/*$0*/")),b.parentNode&&b.parentNode.removeChild(b)}function bl(a){f.nodeName(a,"input")?bk(a):"getElementsByTagName"in a&&f.grep(a.getElementsByTagName("input"),bk)}function bk(a){if(a.type==="checkbox"||a.type==="radio")a.defaultChecked=a.checked}function bj(a){return"getElementsByTagName"in a?a.getElementsByTagName("*"):"querySelectorAll"in a?a.querySelectorAll("*"):[]}function bi(a,b){var c;if(b.nodeType===1){b.clearAttributes&&b.clearAttributes(),b.mergeAttributes&&b.mergeAttributes(a),c=b.nodeName.toLowerCase();if(c==="object")b.outerHTML=a.outerHTML;else if(c!=="input"||a.type!=="checkbox"&&a.type!=="radio"){if(c==="option")b.selected=a.defaultSelected;else if(c==="input"||c==="textarea")b.defaultValue=a.defaultValue}else a.checked&&(b.defaultChecked=b.checked=a.checked),b.value!==a.value&&(b.value=a.value);b.removeAttribute(f.expando)}}function bh(a,b){if(b.nodeType===1&&!!f.hasData(a)){var c=f.expando,d=f.data(a),e=f.data(b,d);if(d=d[c]){var g=d.events;e=e[c]=f.extend({},d);if(g){delete e.handle,e.events={};for(var h in g)for(var i=0,j=g[h].length;i<j;i++)f.event.add(b,h+(g[h][i].namespace?".":"")+g[h][i].namespace,g[h][i],g[h][i].data)}}}}function bg(a,b){return f.nodeName(a,"table")?a.getElementsByTagName("tbody")[0]||a.appendChild(a.ownerDocument.createElement("tbody")):a}function W(a,b,c){b=b||0;if(f.isFunction(b))return f.grep(a,function(a,d){var e=!!b.call(a,d,a);return e===c});if(b.nodeType)return f.grep(a,function(a,d){return a===b===c});if(typeof b=="string"){var d=f.grep(a,function(a){return a.nodeType===1});if(R.test(b))return f.filter(b,d,!c);b=f.filter(b,d)}return f.grep(a,function(a,d){return f.inArray(a,b)>=0===c})}function V(a){return!a||!a.parentNode||a.parentNode.nodeType===11}function N(a,b){return(a&&a!=="*"?a+".":"")+b.replace(z,"`").replace(A,"&")}function M(a){var b,c,d,e,g,h,i,j,k,l,m,n,o,p=[],q=[],r=f._data(this,"events");if(!(a.liveFired===this||!r||!r.live||a.target.disabled||a.button&&a.type==="click")){a.namespace&&(n=new RegExp("(^|\\.)"+a.namespace.split(".").join("\\.(?:.*\\.)?")+"(\\.|$)")),a.liveFired=this;var s=r.live.slice(0);for(i=0;i<s.length;i++)g=s[i],g.origType.replace(x,"")===a.type?q.push(g.selector):s.splice(i--,1);e=f(a.target).closest(q,a.currentTarget);for(j=0,k=e.length;j<k;j++){m=e[j];for(i=0;i<s.length;i++){g=s[i];if(m.selector===g.selector&&(!n||n.test(g.namespace))&&!m.elem.disabled){h=m.elem,d=null;if(g.preType==="mouseenter"||g.preType==="mouseleave")a.type=g.preType,d=f(a.relatedTarget).closest(g.selector)[0],d&&f.contains(h,d)&&(d=h);(!d||d!==h)&&p.push({elem:h,handleObj:g,level:m.level})}}}for(j=0,k=p.length;j<k;j++){e=p[j];if(c&&e.level>c)break;a.currentTarget=e.elem,a.data=e.handleObj.data,a.handleObj=e.handleObj,o=e.handleObj.origHandler.apply(e.elem,arguments);if(o===!1||a.isPropagationStopped()){c=e.level,o===!1&&(b=!1);if(a.isImmediatePropagationStopped())break}}return b}}function K(a,c,d){var e=f.extend({},d[0]);e.type=a,e.originalEvent={},e.liveFired=b,f.event.handle.call(c,e),e.isDefaultPrevented()&&d[0].preventDefault()}function E(){return!0}function D(){return!1}function m(a,c,d){var e=c+"defer",g=c+"queue",h=c+"mark",i=f.data(a,e,b,!0);i&&(d==="queue"||!f.data(a,g,b,!0))&&(d==="mark"||!f.data(a,h,b,!0))&&setTimeout(function(){!f.data(a,g,b,!0)&&!f.data(a,h,b,!0)&&(f.removeData(a,e,!0),i.resolve())},0)}function l(a){for(var b in a)if(b!=="toJSON")return!1;return!0}function k(a,c,d){if(d===b&&a.nodeType===1){var e="data-"+c.replace(j,"$1-$2").toLowerCase();d=a.getAttribute(e);if(typeof d=="string"){try{d=d==="true"?!0:d==="false"?!1:d==="null"?null:f.isNaN(d)?i.test(d)?f.parseJSON(d):d:parseFloat(d)}catch(g){}f.data(a,c,d)}else d=b}return d}var c=a.document,d=a.navigator,e=a.location,f=function(){function J(){if(!e.isReady){try{c.documentElement.doScroll("left")}catch(a){setTimeout(J,1);return}e.ready()}}var e=function(a,b){return new e.fn.init(a,b,h)},f=a.jQuery,g=a.$,h,i=/^(?:[^<]*(<[\w\W]+>)[^>]*$|#([\w\-]*)$)/,j=/\S/,k=/^\s+/,l=/\s+$/,m=/\d/,n=/^<(\w+)\s*\/?>(?:<\/\1>)?$/,o=/^[\],:{}\s]*$/,p=/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,q=/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,r=/(?:^|:|,)(?:\s*\[)+/g,s=/(webkit)[ \/]([\w.]+)/,t=/(opera)(?:.*version)?[ \/]([\w.]+)/,u=/(msie) ([\w.]+)/,v=/(mozilla)(?:.*? rv:([\w.]+))?/,w=/-([a-z])/ig,x=function(a,b){return b.toUpperCase()},y=d.userAgent,z,A,B,C=Object.prototype.toString,D=Object.prototype.hasOwnProperty,E=Array.prototype.push,F=Array.prototype.slice,G=String.prototype.trim,H=Array.prototype.indexOf,I={};e.fn=e.prototype={constructor:e,init:function(a,d,f){var g,h,j,k;if(!a)return this;if(a.nodeType){this.context=this[0]=a,this.length=1;return this}if(a==="body"&&!d&&c.body){this.context=c,this[0]=c.body,this.selector=a,this.length=1;return this}if(typeof a=="string"){a.charAt(0)!=="<"||a.charAt(a.length-1)!==">"||a.length<3?g=i.exec(a):g=[null,a,null];if(g&&(g[1]||!d)){if(g[1]){d=d instanceof e?d[0]:d,k=d?d.ownerDocument||d:c,j=n.exec(a),j?e.isPlainObject(d)?(a=[c.createElement(j[1])],e.fn.attr.call(a,d,!0)):a=[k.createElement(j[1])]:(j=e.buildFragment([g[1]],[k]),a=(j.cacheable?e.clone(j.fragment):j.fragment).childNodes);return e.merge(this,a)}h=c.getElementById(g[2]);if(h&&h.parentNode){if(h.id!==g[2])return f.find(a);this.length=1,this[0]=h}this.context=c,this.selector=a;return this}return!d||d.jquery?(d||f).find(a):this.constructor(d).find(a)}if(e.isFunction(a))return f.ready(a);a.selector!==b&&(this.selector=a.selector,this.context=a.context);return e.makeArray(a,this)},selector:"",jquery:"1.6.2",length:0,size:function(){return this.length},toArray:function(){return F.call(this,0)},get:function(a){return a==null?this.toArray():a<0?this[this.length+a]:this[a]},pushStack:function(a,b,c){var d=this.constructor();e.isArray(a)?E.apply(d,a):e.merge(d,a),d.prevObject=this,d.context=this.context,b==="find"?d.selector=this.selector+(this.selector?" ":"")+c:b&&(d.selector=this.selector+"."+b+"("+c+")");return d},each:function(a,b){return e.each(this,a,b)},ready:function(a){e.bindReady(),A.done(a);return this},eq:function(a){return a===-1?this.slice(a):this.slice(a,+a+1)},first:function(){return this.eq(0)},last:function(){return this.eq(-1)},slice:function(){return this.pushStack(F.apply(this,arguments),"slice",F.call(arguments).join(","))},map:function(a){return this.pushStack(e.map(this,function(b,c){return a.call(b,c,b)}))},end:function(){return this.prevObject||this.constructor(null)},push:E,sort:[].sort,splice:[].splice},e.fn.init.prototype=e.fn,e.extend=e.fn.extend=function(){var a,c,d,f,g,h,i=arguments[0]||{},j=1,k=arguments.length,l=!1;typeof i=="boolean"&&(l=i,i=arguments[1]||{},j=2),typeof i!="object"&&!e.isFunction(i)&&(i={}),k===j&&(i=this,--j);for(;j<k;j++)if((a=arguments[j])!=null)for(c in a){d=i[c],f=a[c];if(i===f)continue;l&&f&&(e.isPlainObject(f)||(g=e.isArray(f)))?(g?(g=!1,h=d&&e.isArray(d)?d:[]):h=d&&e.isPlainObject(d)?d:{},i[c]=e.extend(l,h,f)):f!==b&&(i[c]=f)}return i},e.extend({noConflict:function(b){a.$===e&&(a.$=g),b&&a.jQuery===e&&(a.jQuery=f);return e},isReady:!1,readyWait:1,holdReady:function(a){a?e.readyWait++:e.ready(!0)},ready:function(a){if(a===!0&&!--e.readyWait||a!==!0&&!e.isReady){if(!c.body)return setTimeout(e.ready,1);e.isReady=!0;if(a!==!0&&--e.readyWait>0)return;A.resolveWith(c,[e]),e.fn.trigger&&e(c).trigger("ready").unbind("ready")}},bindReady:function(){if(!A){A=e._Deferred();if(c.readyState==="complete")return setTimeout(e.ready,1);if(c.addEventListener)c.addEventListener("DOMContentLoaded",B,!1),a.addEventListener("load",e.ready,!1);else if(c.attachEvent){c.attachEvent("onreadystatechange",B),a.attachEvent("onload",e.ready);var b=!1;try{b=a.frameElement==null}catch(d){}c.documentElement.doScroll&&b&&J()}}},isFunction:function(a){return e.type(a)==="function"},isArray:Array.isArray||function(a){return e.type(a)==="array"},isWindow:function(a){return a&&typeof a=="object"&&"setInterval"in a},isNaN:function(a){return a==null||!m.test(a)||isNaN(a)},type:function(a){return a==null?String(a):I[C.call(a)]||"object"},isPlainObject:function(a){if(!a||e.type(a)!=="object"||a.nodeType||e.isWindow(a))return!1;if(a.constructor&&!D.call(a,"constructor")&&!D.call(a.constructor.prototype,"isPrototypeOf"))return!1;var c;for(c in a);return c===b||D.call(a,c)},isEmptyObject:function(a){for(var b in a)return!1;return!0},error:function(a){throw a},parseJSON:function(b){if(typeof b!="string"||!b)return null;b=e.trim(b);if(a.JSON&&a.JSON.parse)return a.JSON.parse(b);if(o.test(b.replace(p,"@").replace(q,"]").replace(r,"")))return(new Function("return "+b))();e.error("Invalid JSON: "+b)},parseXML:function(b,c,d){a.DOMParser?(d=new DOMParser,c=d.parseFromString(b,"text/xml")):(c=new ActiveXObject("Microsoft.XMLDOM"),c.async="false",c.loadXML(b)),d=c.documentElement,(!d||!d.nodeName||d.nodeName==="parsererror")&&e.error("Invalid XML: "+b);return c},noop:function(){},globalEval:function(b){b&&j.test(b)&&(a.execScript||function(b){a.eval.call(a,b)})(b)},camelCase:function(a){return a.replace(w,x)},nodeName:function(a,b){return a.nodeName&&a.nodeName.toUpperCase()===b.toUpperCase()},each:function(a,c,d){var f,g=0,h=a.length,i=h===b||e.isFunction(a);if(d){if(i){for(f in a)if(c.apply(a[f],d)===!1)break}else for(;g<h;)if(c.apply(a[g++],d)===!1)break}else if(i){for(f in a)if(c.call(a[f],f,a[f])===!1)break}else for(;g<h;)if(c.call(a[g],g,a[g++])===!1)break;return a},trim:G?function(a){return a==null?"":G.call(a)}:function(a){return a==null?"":(a+"").replace(k,"").replace(l,"")},makeArray:function(a,b){var c=b||[];if(a!=null){var d=e.type(a);a.length==null||d==="string"||d==="function"||d==="regexp"||e.isWindow(a)?E.call(c,a):e.merge(c,a)}return c},inArray:function(a,b){if(H)return H.call(b,a);for(var c=0,d=b.length;c<d;c++)if(b[c]===a)return c;return-1},merge:function(a,c){var d=a.length,e=0;if(typeof c.length=="number")for(var f=c.length;e<f;e++)a[d++]=c[e];else while(c[e]!==b)a[d++]=c[e++];a.length=d;return a},grep:function(a,b,c){var d=[],e;c=!!c;for(var f=0,g=a.length;f<g;f++)e=!!b(a[f],f),c!==e&&d.push(a[f]);return d},map:function(a,c,d){var f,g,h=[],i=0,j=a.length,k=a instanceof e||j!==b&&typeof j=="number"&&(j>0&&a[0]&&a[j-1]||j===0||e.isArray(a));if(k)for(;i<j;i++)f=c(a[i],i,d),f!=null&&(h[h.length]=f);else for(g in a)f=c(a[g],g,d),f!=null&&(h[h.length]=f);return h.concat.apply([],h)},guid:1,proxy:function(a,c){if(typeof c=="string"){var d=a[c];c=a,a=d}if(!e.isFunction(a))return b;var f=F.call(arguments,2),g=function(){return a.apply(c,f.concat(F.call(arguments)))};g.guid=a.guid=a.guid||g.guid||e.guid++;return g},access:function(a,c,d,f,g,h){var i=a.length;if(typeof c=="object"){for(var j in c)e.access(a,j,c[j],f,g,d);return a}if(d!==b){f=!h&&f&&e.isFunction(d);for(var k=0;k<i;k++)g(a[k],c,f?d.call(a[k],k,g(a[k],c)):d,h);return a}return i?g(a[0],c):b},now:function(){return(new Date).getTime()},uaMatch:function(a){a=a.toLowerCase();var b=s.exec(a)||t.exec(a)||u.exec(a)||a.indexOf("compatible")<0&&v.exec(a)||[];return{browser:b[1]||"",version:b[2]||"0"}},sub:function(){function a(b,c){return new a.fn.init(b,c)}e.extend(!0,a,this),a.superclass=this,a.fn=a.prototype=this(),a.fn.constructor=a,a.sub=this.sub,a.fn.init=function(d,f){f&&f instanceof e&&!(f instanceof a)&&(f=a(f));return e.fn.init.call(this,d,f,b)},a.fn.init.prototype=a.fn;var b=a(c);return a},browser:{}}),e.each("Boolean Number String Function Array Date RegExp Object".split(" "),function(a,b){I["[object "+b+"]"]=b.toLowerCase()}),z=e.uaMatch(y),z.browser&&(e.browser[z.browser]=!0,e.browser.version=z.version),e.browser.webkit&&(e.browser.safari=!0),j.test(" ")&&(k=/^[\s\xA0]+/,l=/[\s\xA0]+$/),h=e(c),c.addEventListener?B=function(){c.removeEventListener("DOMContentLoaded",B,!1),e.ready()}:c.attachEvent&&(B=function(){c.readyState==="complete"&&(c.detachEvent("onreadystatechange",B),e.ready())});return e}(),g="done fail isResolved isRejected promise then always pipe".split(" "),h=[].slice;f.extend({_Deferred:function(){var a=[],b,c,d,e={done:function(){if(!d){var c=arguments,g,h,i,j,k;b&&(k=b,b=0);for(g=0,h=c.length;g<h;g++)i=c[g],j=f.type(i),j==="array"?e.done.apply(e,i):j==="function"&&a.push(i);k&&e.resolveWith(k[0],k[1])}return this},resolveWith:function(e,f){if(!d&&!b&&!c){f=f||[],c=1;try{while(a[0])a.shift().apply(e,f)}finally{b=[e,f],c=0}}return this},resolve:function(){e.resolveWith(this,arguments);return this},isResolved:function(){return!!c||!!b},cancel:function(){d=1,a=[];return this}};return e},Deferred:function(a){var b=f._Deferred(),c=f._Deferred(),d;f.extend(b,{then:function(a,c){b.done(a).fail(c);return this},always:function(){return b.done.apply(b,arguments).fail.apply(this,arguments)},fail:c.done,rejectWith:c.resolveWith,reject:c.resolve,isRejected:c.isResolved,pipe:function(a,c){return f.Deferred(function(d){f.each({done:[a,"resolve"],fail:[c,"reject"]},function(a,c){var e=c[0],g=c[1],h;f.isFunction(e)?b[a](function(){h=e.apply(this,arguments),h&&f.isFunction(h.promise)?h.promise().then(d.resolve,d.reject):d[g](h)}):b[a](d[g])})}).promise()},promise:function(a){if(a==null){if(d)return d;d=a={}}var c=g.length;while(c--)a[g[c]]=b[g[c]];return a}}),b.done(c.cancel).fail(b.cancel),delete b.cancel,a&&a.call(b,b);return b},when:function(a){function i(a){return function(c){b[a]=arguments.length>1?h.call(arguments,0):c,--e||g.resolveWith(g,h.call(b,0))}}var b=arguments,c=0,d=b.length,e=d,g=d<=1&&a&&f.isFunction(a.promise)?a:f.Deferred();if(d>1){for(;c<d;c++)b[c]&&f.isFunction(b[c].promise)?b[c].promise().then(i(c),g.reject):--e;e||g.resolveWith(g,b)}else g!==a&&g.resolveWith(g,d?[a]:[]);return g.promise()}}),f.support=function(){var a=c.createElement("div"),b=c.documentElement,d,e,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u;a.setAttribute("className","t"),a.innerHTML="   <link/><table></table><a href='/a' style='top:1px;float:left;opacity:.55;'>a</a><input type='checkbox'/>",d=a.getElementsByTagName("*"),e=a.getElementsByTagName("a")[0];if(!d||!d.length||!e)return{};g=c.createElement("select"),h=g.appendChild(c.createElement("option")),i=a.getElementsByTagName("input")[0],k={leadingWhitespace:a.firstChild.nodeType===3,tbody:!a.getElementsByTagName("tbody").length,htmlSerialize:!!a.getElementsByTagName("link").length,style:/top/.test(e.getAttribute("style")),hrefNormalized:e.getAttribute("href")==="/a",opacity:/^0.55$/.test(e.style.opacity),cssFloat:!!e.style.cssFloat,checkOn:i.value==="on",optSelected:h.selected,getSetAttribute:a.className!=="t",submitBubbles:!0,changeBubbles:!0,focusinBubbles:!1,deleteExpando:!0,noCloneEvent:!0,inlineBlockNeedsLayout:!1,shrinkWrapBlocks:!1,reliableMarginRight:!0},i.checked=!0,k.noCloneChecked=i.cloneNode(!0).checked,g.disabled=!0,k.optDisabled=!h.disabled;try{delete a.test}catch(v){k.deleteExpando=!1}!a.addEventListener&&a.attachEvent&&a.fireEvent&&(a.attachEvent("onclick",function(){k.noCloneEvent=!1}),a.cloneNode(!0).fireEvent("onclick")),i=c.createElement("input"),i.value="t",i.setAttribute("type","radio"),k.radioValue=i.value==="t",i.setAttribute("checked","checked"),a.appendChild(i),l=c.createDocumentFragment(),l.appendChild(a.firstChild),k.checkClone=l.cloneNode(!0).cloneNode(!0).lastChild.checked,a.innerHTML="",a.style.width=a.style.paddingLeft="1px",m=c.getElementsByTagName("body")[0],o=c.createElement(m?"div":"body"),p={visibility:"hidden",width:0,height:0,border:0,margin:0},m&&f.extend(p,{position:"absolute",left:-1e3,top:-1e3});for(t in p)o.style[t]=p[t];o.appendChild(a),n=m||b,n.insertBefore(o,n.firstChild),k.appendChecked=i.checked,k.boxModel=a.offsetWidth===2,"zoom"in a.style&&(a.style.display="inline",a.style.zoom=1,k.inlineBlockNeedsLayout=a.offsetWidth===2,a.style.display="",a.innerHTML="<div style='width:4px;'></div>",k.shrinkWrapBlocks=a.offsetWidth!==2),a.innerHTML="<table><tr><td style='padding:0;border:0;display:none'></td><td>t</td></tr></table>",q=a.getElementsByTagName("td"),u=q[0].offsetHeight===0,q[0].style.display="",q[1].style.display="none",k.reliableHiddenOffsets=u&&q[0].offsetHeight===0,a.innerHTML="",c.defaultView&&c.defaultView.getComputedStyle&&(j=c.createElement("div"),j.style.width="0",j.style.marginRight="0",a.appendChild(j),k.reliableMarginRight=(parseInt((c.defaultView.getComputedStyle(j,null)||{marginRight:0}).marginRight,10)||0)===0),o.innerHTML="",n.removeChild(o);if(a.attachEvent)for(t in{submit:1,change:1,focusin:1})s="on"+t,u=s in a,u||(a.setAttribute(s,"return;"),u=typeof a[s]=="function"),k[t+"Bubbles"]=u;o=l=g=h=m=j=a=i=null;return k}(),f.boxModel=f.support.boxModel;var i=/^(?:\{.*\}|\[.*\])$/,j=/([a-z])([A-Z])/g;f.extend({cache:{},uuid:0,expando:"jQuery"+(f.fn.jquery+Math.random()).replace(/\D/g,""),noData:{embed:!0,object:"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000",applet:!0},hasData:function(a){a=a.nodeType?f.cache[a[f.expando]]:a[f.expando];return!!a&&!l(a)},data:function(a,c,d,e){if(!!f.acceptData(a)){var g=f.expando,h=typeof c=="string",i,j=a.nodeType,k=j?f.cache:a,l=j?a[f.expando]:a[f.expando]&&f.expando;if((!l||e&&l&&!k[l][g])&&h&&d===b)return;l||(j?a[f.expando]=l=++f.uuid:l=f.expando),k[l]||(k[l]={},j||(k[l].toJSON=f.noop));if(typeof c=="object"||typeof c=="function")e?k[l][g]=f.extend(k[l][g],c):k[l]=f.extend(k[l],c);i=k[l],e&&(i[g]||(i[g]={}),i=i[g]),d!==b&&(i[f.camelCase(c)]=d);if(c==="events"&&!i[c])return i[g]&&i[g].events;return h?i[f.camelCase(c)]||i[c]:i}},removeData:function(b,c,d){if(!!f.acceptData(b)){var e=f.expando,g=b.nodeType,h=g?f.cache:b,i=g?b[f.expando]:f.expando;if(!h[i])return;if(c){var j=d?h[i][e]:h[i];if(j){delete j[c];if(!l(j))return}}if(d){delete h[i][e];if(!l(h[i]))return}var k=h[i][e];f.support.deleteExpando||h!=a?delete h[i]:h[i]=null,k?(h[i]={},g||(h[i].toJSON=f.noop),h[i][e]=k):g&&(f.support.deleteExpando?delete b[f.expando]:b.removeAttribute?b.removeAttribute(f.expando):b[f.expando]=null)}},_data:function(a,b,c){return f.data(a,b,c,!0)},acceptData:function(a){if(a.nodeName){var b=f.noData[a.nodeName.toLowerCase()];if(b)return b!==!0&&a.getAttribute("classid")===b}return!0}}),f.fn.extend({data:function(a,c){var d=null;if(typeof a=="undefined"){if(this.length){d=f.data(this[0]);if(this[0].nodeType===1){var e=this[0].attributes,g;for(var h=0,i=e.length;h<i;h++)g=e[h].name,g.indexOf("data-")===0&&(g=f.camelCase(g.substring(5)),k(this[0],g,d[g]))}}return d}if(typeof a=="object")return this.each(function(){f.data(this,a)});var j=a.split(".");j[1]=j[1]?"."+j[1]:"";if(c===b){d=this.triggerHandler("getData"+j[1]+"!",[j[0]]),d===b&&this.length&&(d=f.data(this[0],a),d=k(this[0],a,d));return d===b&&j[1]?this.data(j[0]):d}return this.each(function(){var b=f(this),d=[j[0],c];b.triggerHandler("setData"+j[1]+"!",d),f.data(this,a,c),b.triggerHandler("changeData"+j[1]+"!",d)})},removeData:function(a){return this.each(function(){f.removeData(this,a)})}}),f.extend({_mark:function(a,c){a&&(c=(c||"fx")+"mark",f.data(a,c,(f.data(a,c,b,!0)||0)+1,!0))},_unmark:function(a,c,d){a!==!0&&(d=c,c=a,a=!1);if(c){d=d||"fx";var e=d+"mark",g=a?0:(f.data(c,e,b,!0)||1)-1;g?f.data(c,e,g,!0):(f.removeData(c,e,!0),m(c,d,"mark"))}},queue:function(a,c,d){if(a){c=(c||"fx")+"queue";var e=f.data(a,c,b,!0);d&&(!e||f.isArray(d)?e=f.data(a,c,f.makeArray(d),!0):e.push(d));return e||[]}},dequeue:function(a,b){b=b||"fx";var c=f.queue(a,b),d=c.shift(),e;d==="inprogress"&&(d=c.shift()),d&&(b==="fx"&&c.unshift("inprogress"),d.call(a,function(){f.dequeue(a,b)})),c.length||(f.removeData(a,b+"queue",!0),m(a,b,"queue"))}}),f.fn.extend({queue:function(a,c){typeof a!="string"&&(c=a,a="fx");if(c===b)return f.queue(this[0],a);return this.each(function(){var b=f.queue(this,a,c);a==="fx"&&b[0]!=="inprogress"&&f.dequeue(this,a)})},dequeue:function(a){return this.each(function(){f.dequeue(this,a)})},delay:function(a,b){a=f.fx?f.fx.speeds[a]||a:a,b=b||"fx";return this.queue(b,function(){var c=this;setTimeout(function(){f.dequeue(c,b)},a)})},clearQueue:function(a){return this.queue(a||"fx",[])},promise:function(a,c){function m(){--h||d.resolveWith(e,[e])}typeof a!="string"&&(c=a,a=b),a=a||"fx";var d=f.Deferred(),e=this,g=e.length,h=1,i=a+"defer",j=a+"queue",k=a+"mark",l;while(g--)if(l=f.data(e[g],i,b,!0)||(f.data(e[g],j,b,!0)||f.data(e[g],k,b,!0))&&f.data(e[g],i,f._Deferred(),!0))h++,l.done(m);m();return d.promise()}});var n=/[\n\t\r]/g,o=/\s+/,p=/\r/g,q=/^(?:button|input)$/i,r=/^(?:button|input|object|select|textarea)$/i,s=/^a(?:rea)?$/i,t=/^(?:autofocus|autoplay|async|checked|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped|selected)$/i,u=/\:|^on/,v,w;f.fn.extend({attr:function(a,b){return f.access(this,a,b,!0,f.attr)},removeAttr:function(a){return this.each(function(){f.removeAttr(this,a)})},prop:function(a,b){return f.access(this,a,b,!0,f.prop)},removeProp:function(a){a=f.propFix[a]||a;return this.each(function(){try{this[a]=b,delete this[a]}catch(c){}})},addClass:function(a){var b,c,d,e,g,h,i;if(f.isFunction(a))return this.each(function(b){f(this).addClass(a.call(this,b,this.className))});if(a&&typeof a=="string"){b=a.split(o);for(c=0,d=this.length;c<d;c++){e=this[c];if(e.nodeType===1)if(!e.className&&b.length===1)e.className=a;else{g=" "+e.className+" ";for(h=0,i=b.length;h<i;h++)~g.indexOf(" "+b[h]+" ")||(g+=b[h]+" ");e.className=f.trim(g)}}}return this},removeClass:function(a){var c,d,e,g,h,i,j;if(f.isFunction(a))return this.each(function(b){f(this).removeClass(a.call(this,b,this.className))});if(a&&typeof a=="string"||a===b){c=(a||"").split(o);for(d=0,e=this.length;d<e;d++){g=this[d];if(g.nodeType===1&&g.className)if(a){h=(" "+g.className+" ").replace(n," ");for(i=0,j=c.length;i<j;i++)h=h.replace(" "+c[i]+" "," ");g.className=f.trim(h)}else g.className=""}}return this},toggleClass:function(a,b){var c=typeof a,d=typeof b=="boolean";if(f.isFunction(a))return this.each(function(c){f(this).toggleClass(a.call(this,c,this.className,b),b)});return this.each(function(){if(c==="string"){var e,g=0,h=f(this),i=b,j=a.split(o);while(e=j[g++])i=d?i:!h.hasClass(e),h[i?"addClass":"removeClass"](e)}else if(c==="undefined"||c==="boolean")this.className&&f._data(this,"__className__",this.className),this.className=this.className||a===!1?"":f._data(this,"__className__")||""})},hasClass:function(a){var b=" "+a+" ";for(var c=0,d=this.length;c<d;c++)if((" "+this[c].className+" ").replace(n," ").indexOf(b)>-1)return!0;return!1},val:function(a){var c,d,e=this[0];if(!arguments.length){if(e){c=f.valHooks[e.nodeName.toLowerCase()]||f.valHooks[e.type];if(c&&"get"in c&&(d=c.get(e,"value"))!==b)return d;d=e.value;return typeof d=="string"?d.replace(p,""):d==null?"":d}return b}var g=f.isFunction(a);return this.each(function(d){var e=f(this),h;if(this.nodeType===1){g?h=a.call(this,d,e.val()):h=a,h==null?h="":typeof h=="number"?h+="":f.isArray(h)&&(h=f.map(h,function(a){return a==null?"":a+""})),c=f.valHooks[this.nodeName.toLowerCase()]||f.valHooks[this.type];if(!c||!("set"in c)||c.set(this,h,"value")===b)this.value=h}})}}),f.extend({valHooks:{option:{get:function(a){var b=a.attributes.value;return!b||b.specified?a.value:a.text}},select:{get:function(a){var b,c=a.selectedIndex,d=[],e=a.options,g=a.type==="select-one";if(c<0)return null;for(var h=g?c:0,i=g?c+1:e.length;h<i;h++){var j=e[h];if(j.selected&&(f.support.optDisabled?!j.disabled:j.getAttribute("disabled")===null)&&(!j.parentNode.disabled||!f.nodeName(j.parentNode,"optgroup"))){b=f(j).val();if(g)return b;d.push(b)}}if(g&&!d.length&&e.length)return f(e[c]).val();return d},set:function(a,b){var c=f.makeArray(b);f(a).find("option").each(function(){this.selected=f.inArray(f(this).val(),c)>=0}),c.length||(a.selectedIndex=-1);return c}}},attrFn:{val:!0,css:!0,html:!0,text:!0,data:!0,width:!0,height:!0,offset:!0},attrFix:{tabindex:"tabIndex"},attr:function(a,c,d,e){var g=a.nodeType;if(!a||g===3||g===8||g===2)return b;if(e&&c in f.attrFn)return f(a)[c](d);if(!("getAttribute"in a))return f.prop(a,c,d);var h,i,j=g!==1||!f.isXMLDoc(a);j&&(c=f.attrFix[c]||c,i=f.attrHooks[c],i||(t.test(c)?i=w:v&&c!=="className"&&(f.nodeName(a,"form")||u.test(c))&&(i=v)));if(d!==b){if(d===null){f.removeAttr(a,c);return b}if(i&&"set"in i&&j&&(h=i.set(a,d,c))!==b)return h;a.setAttribute(c,""+d);return d}if(i&&"get"in i&&j&&(h=i.get(a,c))!==null)return h;h=a.getAttribute(c);return h===null?b:h},removeAttr:function(a,b){var c;a.nodeType===1&&(b=f.attrFix[b]||b,f.support.getSetAttribute?a.removeAttribute(b):(f.attr(a,b,""),a.removeAttributeNode(a.getAttributeNode(b))),t.test(b)&&(c=f.propFix[b]||b)in a&&(a[c]=!1))},attrHooks:{type:{set:function(a,b){if(q.test(a.nodeName)&&a.parentNode)f.error("type property can't be changed");else if(!f.support.radioValue&&b==="radio"&&f.nodeName(a,"input")){var c=a.value;a.setAttribute("type",b),c&&(a.value=c);return b}}},tabIndex:{get:function(a){var c=a.getAttributeNode("tabIndex");return c&&c.specified?parseInt(c.value,10):r.test(a.nodeName)||s.test(a.nodeName)&&a.href?0:b}},value:{get:function(a,b){if(v&&f.nodeName(a,"button"))return v.get(a,b);return b in a?a.value:null},set:function(a,b,c){if(v&&f.nodeName(a,"button"))return v.set(a,b,c);a.value=b}}},propFix:{tabindex:"tabIndex",readonly:"readOnly","for":"htmlFor","class":"className",maxlength:"maxLength",cellspacing:"cellSpacing",cellpadding:"cellPadding",rowspan:"rowSpan",colspan:"colSpan",usemap:"useMap",frameborder:"frameBorder",contenteditable:"contentEditable"},prop:function(a,c,d){var e=a.nodeType;if(!a||e===3||e===8||e===2)return b;var g,h,i=e!==1||!f.isXMLDoc(a);i&&(c=f.propFix[c]||c,h=f.propHooks[c]);return d!==b?h&&"set"in h&&(g=h.set(a,d,c))!==b?g:a[c]=d:h&&"get"in h&&(g=h.get(a,c))!==b?g:a[c]},propHooks:{}}),w={get:function(a,c){return f.prop(a,c)?c.toLowerCase():b},set:function(a,b,c){var d;b===!1?f.removeAttr(a,c):(d=f.propFix[c]||c,d in a&&(a[d]=!0),a.setAttribute(c,c.toLowerCase()));return c}},f.support.getSetAttribute||(f.attrFix=f.propFix,v=f.attrHooks.name=f.attrHooks.title=f.valHooks.button={get:function(a,c){var d;d=a.getAttributeNode(c);return d&&d.nodeValue!==""?d.nodeValue:b},set:function(a,b,c){var d=a.getAttributeNode(c);if(d){d.nodeValue=b;return b}}},f.each(["width","height"],function(a,b){f.attrHooks[b]=f.extend(f.attrHooks[b],{set:function(a,c){if(c===""){a.setAttribute(b,"auto");return c}}})})),f.support.hrefNormalized||f.each(["href","src","width","height"],function(a,c){f.attrHooks[c]=f.extend(f.attrHooks[c],{get:function(a){var d=a.getAttribute(c,2);return d===null?b:d}})}),f.support.style||(f.attrHooks.style={get:function(a){return a.style.cssText.toLowerCase()||b},set:function(a,b){return a.style.cssText=""+b}}),f.support.optSelected||(f.propHooks.selected=f.extend(f.propHooks.selected,{get:function(a){var b=a.parentNode;b&&(b.selectedIndex,b.parentNode&&b.parentNode.selectedIndex)}})),f.support.checkOn||f.each(["radio","checkbox"],function(){f.valHooks[this]={get:function(a){return a.getAttribute("value")===null?"on":a.value}}}),f.each(["radio","checkbox"],function(){f.valHooks[this]=f.extend(f.valHooks[this],{set:function(a,b){if(f.isArray(b))return a.checked=f.inArray(f(a).val(),b)>=0}})});var x=/\.(.*)$/,y=/^(?:textarea|input|select)$/i,z=/\./g,A=/ /g,B=/[^\w\s.|`]/g,C=function(a){return a.replace(B,"\\$&")};f.event={add:function(a,c,d,e){if(a.nodeType!==3&&a.nodeType!==8){if(d===!1)d=D;else if(!d)return;var g,h;d.handler&&(g=d,d=g.handler),d.guid||(d.guid=f.guid++);var i=f._data(a);if(!i)return;var j=i.events,k=i.handle;j||(i.events=j={}),k||(i.handle=k=function(a){return typeof f!="undefined"&&(!a||f.event.triggered!==a.type)?f.event.handle.apply(k.elem,arguments):b}),k.elem=a,c=c.split(" ");var l,m=0,n;while(l=c[m++]){h=g?f.extend({},g):{handler:d,data:e},l.indexOf(".")>-1?(n=l.split("."),l=n.shift(),h.namespace=n.slice(0).sort().join(".")):(n=[],h.namespace=""),h.type=l,h.guid||(h.guid=d.guid);var o=j[l],p=f.event.special[l]||{};if(!o){o=j[l]=[];if(!p.setup||p.setup.call(a,e,n,k)===!1)a.addEventListener?a.addEventListener(l,k,!1):a.attachEvent&&a.attachEvent("on"+l,k)}p.add&&(p.add.call(a,h),h.handler.guid||(h.handler.guid=d.guid)),o.push(h),f.event.global[l]=!0}a=null}},global:{},remove:function(a,c,d,e){if(a.nodeType!==3&&a.nodeType!==8){d===!1&&(d=D);var g,h,i,j,k=0,l,m,n,o,p,q,r,s=f.hasData(a)&&f._data(a),t=s&&s.events;if(!s||!t)return;c&&c.type&&(d=c.handler,c=c.type);if(!c||typeof c=="string"&&c.charAt(0)==="."){c=c||"";for(h in t)f.event.remove(a,h+c);return}c=c.split(" ");while(h=c[k++]){r=h,q=null,l=h.indexOf(".")<0,m=[],l||(m=h.split("."),h=m.shift(),n=new RegExp("(^|\\.)"+f.map(m.slice(0).sort(),C).join("\\.(?:.*\\.)?")+"(\\.|$)")),p=t[h];if(!p)continue;if(!d){for(j=0;j<p.length;j++){q=p[j];if(l||n.test(q.namespace))f.event.remove(a,r,q.handler,j),p.splice(j--,1)}continue}o=f.event.special[h]||{};for(j=e||0;j<p.length;j++){q=p[j];if(d.guid===q.guid){if(l||n.test(q.namespace))e==null&&p.splice(j--,1),o.remove&&o.remove.call(a,q);if(e!=null)break}}if(p.length===0||e!=null&&p.length===1)(!o.teardown||o.teardown.call(a,m)===!1)&&f.removeEvent(a,h,s.handle),g=null,delete t[h]}if(f.isEmptyObject(t)){var u=s.handle;u&&(u.elem=null),delete s.events,delete s.handle,f.isEmptyObject(s)&&f.removeData(a,b,!0)}}},customEvent:{getData:!0,setData:!0,changeData:!0},trigger:function(c,d,e,g){var h=c.type||c,i=[],j;h.indexOf("!")>=0&&(h=h.slice(0,-1),j=!0),h.indexOf(".")>=0&&(i=h.split("."),h=i.
shift(),i.sort());if(!!e&&!f.event.customEvent[h]||!!f.event.global[h]){c=typeof c=="object"?c[f.expando]?c:new f.Event(h,c):new f.Event(h),c.type=h,c.exclusive=j,c.namespace=i.join("."),c.namespace_re=new RegExp("(^|\\.)"+i.join("\\.(?:.*\\.)?")+"(\\.|$)");if(g||!e)c.preventDefault(),c.stopPropagation();if(!e){f.each(f.cache,function(){var a=f.expando,b=this[a];b&&b.events&&b.events[h]&&f.event.trigger(c,d,b.handle.elem)});return}if(e.nodeType===3||e.nodeType===8)return;c.result=b,c.target=e,d=d!=null?f.makeArray(d):[],d.unshift(c);var k=e,l=h.indexOf(":")<0?"on"+h:"";do{var m=f._data(k,"handle");c.currentTarget=k,m&&m.apply(k,d),l&&f.acceptData(k)&&k[l]&&k[l].apply(k,d)===!1&&(c.result=!1,c.preventDefault()),k=k.parentNode||k.ownerDocument||k===c.target.ownerDocument&&a}while(k&&!c.isPropagationStopped());if(!c.isDefaultPrevented()){var n,o=f.event.special[h]||{};if((!o._default||o._default.call(e.ownerDocument,c)===!1)&&(h!=="click"||!f.nodeName(e,"a"))&&f.acceptData(e)){try{l&&e[h]&&(n=e[l],n&&(e[l]=null),f.event.triggered=h,e[h]())}catch(p){}n&&(e[l]=n),f.event.triggered=b}}return c.result}},handle:function(c){c=f.event.fix(c||a.event);var d=((f._data(this,"events")||{})[c.type]||[]).slice(0),e=!c.exclusive&&!c.namespace,g=Array.prototype.slice.call(arguments,0);g[0]=c,c.currentTarget=this;for(var h=0,i=d.length;h<i;h++){var j=d[h];if(e||c.namespace_re.test(j.namespace)){c.handler=j.handler,c.data=j.data,c.handleObj=j;var k=j.handler.apply(this,g);k!==b&&(c.result=k,k===!1&&(c.preventDefault(),c.stopPropagation()));if(c.isImmediatePropagationStopped())break}}return c.result},props:"altKey attrChange attrName bubbles button cancelable charCode clientX clientY ctrlKey currentTarget data detail eventPhase fromElement handler keyCode layerX layerY metaKey newValue offsetX offsetY pageX pageY prevValue relatedNode relatedTarget screenX screenY shiftKey srcElement target toElement view wheelDelta which".split(" "),fix:function(a){if(a[f.expando])return a;var d=a;a=f.Event(d);for(var e=this.props.length,g;e;)g=this.props[--e],a[g]=d[g];a.target||(a.target=a.srcElement||c),a.target.nodeType===3&&(a.target=a.target.parentNode),!a.relatedTarget&&a.fromElement&&(a.relatedTarget=a.fromElement===a.target?a.toElement:a.fromElement);if(a.pageX==null&&a.clientX!=null){var h=a.target.ownerDocument||c,i=h.documentElement,j=h.body;a.pageX=a.clientX+(i&&i.scrollLeft||j&&j.scrollLeft||0)-(i&&i.clientLeft||j&&j.clientLeft||0),a.pageY=a.clientY+(i&&i.scrollTop||j&&j.scrollTop||0)-(i&&i.clientTop||j&&j.clientTop||0)}a.which==null&&(a.charCode!=null||a.keyCode!=null)&&(a.which=a.charCode!=null?a.charCode:a.keyCode),!a.metaKey&&a.ctrlKey&&(a.metaKey=a.ctrlKey),!a.which&&a.button!==b&&(a.which=a.button&1?1:a.button&2?3:a.button&4?2:0);return a},guid:1e8,proxy:f.proxy,special:{ready:{setup:f.bindReady,teardown:f.noop},live:{add:function(a){f.event.add(this,N(a.origType,a.selector),f.extend({},a,{handler:M,guid:a.handler.guid}))},remove:function(a){f.event.remove(this,N(a.origType,a.selector),a)}},beforeunload:{setup:function(a,b,c){f.isWindow(this)&&(this.onbeforeunload=c)},teardown:function(a,b){this.onbeforeunload===b&&(this.onbeforeunload=null)}}}},f.removeEvent=c.removeEventListener?function(a,b,c){a.removeEventListener&&a.removeEventListener(b,c,!1)}:function(a,b,c){a.detachEvent&&a.detachEvent("on"+b,c)},f.Event=function(a,b){if(!this.preventDefault)return new f.Event(a,b);a&&a.type?(this.originalEvent=a,this.type=a.type,this.isDefaultPrevented=a.defaultPrevented||a.returnValue===!1||a.getPreventDefault&&a.getPreventDefault()?E:D):this.type=a,b&&f.extend(this,b),this.timeStamp=f.now(),this[f.expando]=!0},f.Event.prototype={preventDefault:function(){this.isDefaultPrevented=E;var a=this.originalEvent;!a||(a.preventDefault?a.preventDefault():a.returnValue=!1)},stopPropagation:function(){this.isPropagationStopped=E;var a=this.originalEvent;!a||(a.stopPropagation&&a.stopPropagation(),a.cancelBubble=!0)},stopImmediatePropagation:function(){this.isImmediatePropagationStopped=E,this.stopPropagation()},isDefaultPrevented:D,isPropagationStopped:D,isImmediatePropagationStopped:D};var F=function(a){var b=a.relatedTarget,c=!1,d=a.type;a.type=a.data,b!==this&&(b&&(c=f.contains(this,b)),c||(f.event.handle.apply(this,arguments),a.type=d))},G=function(a){a.type=a.data,f.event.handle.apply(this,arguments)};f.each({mouseenter:"mouseover",mouseleave:"mouseout"},function(a,b){f.event.special[a]={setup:function(c){f.event.add(this,b,c&&c.selector?G:F,a)},teardown:function(a){f.event.remove(this,b,a&&a.selector?G:F)}}}),f.support.submitBubbles||(f.event.special.submit={setup:function(a,b){if(!f.nodeName(this,"form"))f.event.add(this,"click.specialSubmit",function(a){var b=a.target,c=b.type;(c==="submit"||c==="image")&&f(b).closest("form").length&&K("submit",this,arguments)}),f.event.add(this,"keypress.specialSubmit",function(a){var b=a.target,c=b.type;(c==="text"||c==="password")&&f(b).closest("form").length&&a.keyCode===13&&K("submit",this,arguments)});else return!1},teardown:function(a){f.event.remove(this,".specialSubmit")}});if(!f.support.changeBubbles){var H,I=function(a){var b=a.type,c=a.value;b==="radio"||b==="checkbox"?c=a.checked:b==="select-multiple"?c=a.selectedIndex>-1?f.map(a.options,function(a){return a.selected}).join("-"):"":f.nodeName(a,"select")&&(c=a.selectedIndex);return c},J=function(c){var d=c.target,e,g;if(!!y.test(d.nodeName)&&!d.readOnly){e=f._data(d,"_change_data"),g=I(d),(c.type!=="focusout"||d.type!=="radio")&&f._data(d,"_change_data",g);if(e===b||g===e)return;if(e!=null||g)c.type="change",c.liveFired=b,f.event.trigger(c,arguments[1],d)}};f.event.special.change={filters:{focusout:J,beforedeactivate:J,click:function(a){var b=a.target,c=f.nodeName(b,"input")?b.type:"";(c==="radio"||c==="checkbox"||f.nodeName(b,"select"))&&J.call(this,a)},keydown:function(a){var b=a.target,c=f.nodeName(b,"input")?b.type:"";(a.keyCode===13&&!f.nodeName(b,"textarea")||a.keyCode===32&&(c==="checkbox"||c==="radio")||c==="select-multiple")&&J.call(this,a)},beforeactivate:function(a){var b=a.target;f._data(b,"_change_data",I(b))}},setup:function(a,b){if(this.type==="file")return!1;for(var c in H)f.event.add(this,c+".specialChange",H[c]);return y.test(this.nodeName)},teardown:function(a){f.event.remove(this,".specialChange");return y.test(this.nodeName)}},H=f.event.special.change.filters,H.focus=H.beforeactivate}f.support.focusinBubbles||f.each({focus:"focusin",blur:"focusout"},function(a,b){function e(a){var c=f.event.fix(a);c.type=b,c.originalEvent={},f.event.trigger(c,null,c.target),c.isDefaultPrevented()&&a.preventDefault()}var d=0;f.event.special[b]={setup:function(){d++===0&&c.addEventListener(a,e,!0)},teardown:function(){--d===0&&c.removeEventListener(a,e,!0)}}}),f.each(["bind","one"],function(a,c){f.fn[c]=function(a,d,e){var g;if(typeof a=="object"){for(var h in a)this[c](h,d,a[h],e);return this}if(arguments.length===2||d===!1)e=d,d=b;c==="one"?(g=function(a){f(this).unbind(a,g);return e.apply(this,arguments)},g.guid=e.guid||f.guid++):g=e;if(a==="unload"&&c!=="one")this.one(a,d,e);else for(var i=0,j=this.length;i<j;i++)f.event.add(this[i],a,g,d);return this}}),f.fn.extend({unbind:function(a,b){if(typeof a=="object"&&!a.preventDefault)for(var c in a)this.unbind(c,a[c]);else for(var d=0,e=this.length;d<e;d++)f.event.remove(this[d],a,b);return this},delegate:function(a,b,c,d){return this.live(b,c,d,a)},undelegate:function(a,b,c){return arguments.length===0?this.unbind("live"):this.die(b,null,c,a)},trigger:function(a,b){return this.each(function(){f.event.trigger(a,b,this)})},triggerHandler:function(a,b){if(this[0])return f.event.trigger(a,b,this[0],!0)},toggle:function(a){var b=arguments,c=a.guid||f.guid++,d=0,e=function(c){var e=(f.data(this,"lastToggle"+a.guid)||0)%d;f.data(this,"lastToggle"+a.guid,e+1),c.preventDefault();return b[e].apply(this,arguments)||!1};e.guid=c;while(d<b.length)b[d++].guid=c;return this.click(e)},hover:function(a,b){return this.mouseenter(a).mouseleave(b||a)}});var L={focus:"focusin",blur:"focusout",mouseenter:"mouseover",mouseleave:"mouseout"};f.each(["live","die"],function(a,c){f.fn[c]=function(a,d,e,g){var h,i=0,j,k,l,m=g||this.selector,n=g?this:f(this.context);if(typeof a=="object"&&!a.preventDefault){for(var o in a)n[c](o,d,a[o],m);return this}if(c==="die"&&!a&&g&&g.charAt(0)==="."){n.unbind(g);return this}if(d===!1||f.isFunction(d))e=d||D,d=b;a=(a||"").split(" ");while((h=a[i++])!=null){j=x.exec(h),k="",j&&(k=j[0],h=h.replace(x,""));if(h==="hover"){a.push("mouseenter"+k,"mouseleave"+k);continue}l=h,L[h]?(a.push(L[h]+k),h=h+k):h=(L[h]||h)+k;if(c==="live")for(var p=0,q=n.length;p<q;p++)f.event.add(n[p],"live."+N(h,m),{data:d,selector:m,handler:e,origType:h,origHandler:e,preType:l});else n.unbind("live."+N(h,m),e)}return this}}),f.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error".split(" "),function(a,b){f.fn[b]=function(a,c){c==null&&(c=a,a=null);return arguments.length>0?this.bind(b,a,c):this.trigger(b)},f.attrFn&&(f.attrFn[b]=!0)}),function(){function u(a,b,c,d,e,f){for(var g=0,h=d.length;g<h;g++){var i=d[g];if(i){var j=!1;i=i[a];while(i){if(i.sizcache===c){j=d[i.sizset];break}if(i.nodeType===1){f||(i.sizcache=c,i.sizset=g);if(typeof b!="string"){if(i===b){j=!0;break}}else if(k.filter(b,[i]).length>0){j=i;break}}i=i[a]}d[g]=j}}}function t(a,b,c,d,e,f){for(var g=0,h=d.length;g<h;g++){var i=d[g];if(i){var j=!1;i=i[a];while(i){if(i.sizcache===c){j=d[i.sizset];break}i.nodeType===1&&!f&&(i.sizcache=c,i.sizset=g);if(i.nodeName.toLowerCase()===b){j=i;break}i=i[a]}d[g]=j}}}var a=/((?:\((?:\([^()]+\)|[^()]+)+\)|\[(?:\[[^\[\]]*\]|['"][^'"]*['"]|[^\[\]'"]+)+\]|\\.|[^ >+~,(\[\\]+)+|[>+~])(\s*,\s*)?((?:.|\r|\n)*)/g,d=0,e=Object.prototype.toString,g=!1,h=!0,i=/\\/g,j=/\W/;[0,0].sort(function(){h=!1;return 0});var k=function(b,d,f,g){f=f||[],d=d||c;var h=d;if(d.nodeType!==1&&d.nodeType!==9)return[];if(!b||typeof b!="string")return f;var i,j,n,o,q,r,s,t,u=!0,w=k.isXML(d),x=[],y=b;do{a.exec(""),i=a.exec(y);if(i){y=i[3],x.push(i[1]);if(i[2]){o=i[3];break}}}while(i);if(x.length>1&&m.exec(b))if(x.length===2&&l.relative[x[0]])j=v(x[0]+x[1],d);else{j=l.relative[x[0]]?[d]:k(x.shift(),d);while(x.length)b=x.shift(),l.relative[b]&&(b+=x.shift()),j=v(b,j)}else{!g&&x.length>1&&d.nodeType===9&&!w&&l.match.ID.test(x[0])&&!l.match.ID.test(x[x.length-1])&&(q=k.find(x.shift(),d,w),d=q.expr?k.filter(q.expr,q.set)[0]:q.set[0]);if(d){q=g?{expr:x.pop(),set:p(g)}:k.find(x.pop(),x.length===1&&(x[0]==="~"||x[0]==="+")&&d.parentNode?d.parentNode:d,w),j=q.expr?k.filter(q.expr,q.set):q.set,x.length>0?n=p(j):u=!1;while(x.length)r=x.pop(),s=r,l.relative[r]?s=x.pop():r="",s==null&&(s=d),l.relative[r](n,s,w)}else n=x=[]}n||(n=j),n||k.error(r||b);if(e.call(n)==="[object Array]")if(!u)f.push.apply(f,n);else if(d&&d.nodeType===1)for(t=0;n[t]!=null;t++)n[t]&&(n[t]===!0||n[t].nodeType===1&&k.contains(d,n[t]))&&f.push(j[t]);else for(t=0;n[t]!=null;t++)n[t]&&n[t].nodeType===1&&f.push(j[t]);else p(n,f);o&&(k(o,h,f,g),k.uniqueSort(f));return f};k.uniqueSort=function(a){if(r){g=h,a.sort(r);if(g)for(var b=1;b<a.length;b++)a[b]===a[b-1]&&a.splice(b--,1)}return a},k.matches=function(a,b){return k(a,null,null,b)},k.matchesSelector=function(a,b){return k(b,null,null,[a]).length>0},k.find=function(a,b,c){var d;if(!a)return[];for(var e=0,f=l.order.length;e<f;e++){var g,h=l.order[e];if(g=l.leftMatch[h].exec(a)){var j=g[1];g.splice(1,1);if(j.substr(j.length-1)!=="\\"){g[1]=(g[1]||"").replace(i,""),d=l.find[h](g,b,c);if(d!=null){a=a.replace(l.match[h],"");break}}}}d||(d=typeof b.getElementsByTagName!="undefined"?b.getElementsByTagName("*"):[]);return{set:d,expr:a}},k.filter=function(a,c,d,e){var f,g,h=a,i=[],j=c,m=c&&c[0]&&k.isXML(c[0]);while(a&&c.length){for(var n in l.filter)if((f=l.leftMatch[n].exec(a))!=null&&f[2]){var o,p,q=l.filter[n],r=f[1];g=!1,f.splice(1,1);if(r.substr(r.length-1)==="\\")continue;j===i&&(i=[]);if(l.preFilter[n]){f=l.preFilter[n](f,j,d,i,e,m);if(!f)g=o=!0;else if(f===!0)continue}if(f)for(var s=0;(p=j[s])!=null;s++)if(p){o=q(p,f,s,j);var t=e^!!o;d&&o!=null?t?g=!0:j[s]=!1:t&&(i.push(p),g=!0)}if(o!==b){d||(j=i),a=a.replace(l.match[n],"");if(!g)return[];break}}if(a===h)if(g==null)k.error(a);else break;h=a}return j},k.error=function(a){throw"Syntax error, unrecognized expression: "+a};var l=k.selectors={order:["ID","NAME","TAG"],match:{ID:/#((?:[\w\u00c0-\uFFFF\-]|\\.)+)/,CLASS:/\.((?:[\w\u00c0-\uFFFF\-]|\\.)+)/,NAME:/\[name=['"]*((?:[\w\u00c0-\uFFFF\-]|\\.)+)['"]*\]/,ATTR:/\[\s*((?:[\w\u00c0-\uFFFF\-]|\\.)+)\s*(?:(\S?=)\s*(?:(['"])(.*?)\3|(#?(?:[\w\u00c0-\uFFFF\-]|\\.)*)|)|)\s*\]/,TAG:/^((?:[\w\u00c0-\uFFFF\*\-]|\\.)+)/,CHILD:/:(only|nth|last|first)-child(?:\(\s*(even|odd|(?:[+\-]?\d+|(?:[+\-]?\d*)?n\s*(?:[+\-]\s*\d+)?))\s*\))?/,POS:/:(nth|eq|gt|lt|first|last|even|odd)(?:\((\d*)\))?(?=[^\-]|$)/,PSEUDO:/:((?:[\w\u00c0-\uFFFF\-]|\\.)+)(?:\((['"]?)((?:\([^\)]+\)|[^\(\)]*)+)\2\))?/},leftMatch:{},attrMap:{"class":"className","for":"htmlFor"},attrHandle:{href:function(a){return a.getAttribute("href")},type:function(a){return a.getAttribute("type")}},relative:{"+":function(a,b){var c=typeof b=="string",d=c&&!j.test(b),e=c&&!d;d&&(b=b.toLowerCase());for(var f=0,g=a.length,h;f<g;f++)if(h=a[f]){while((h=h.previousSibling)&&h.nodeType!==1);a[f]=e||h&&h.nodeName.toLowerCase()===b?h||!1:h===b}e&&k.filter(b,a,!0)},">":function(a,b){var c,d=typeof b=="string",e=0,f=a.length;if(d&&!j.test(b)){b=b.toLowerCase();for(;e<f;e++){c=a[e];if(c){var g=c.parentNode;a[e]=g.nodeName.toLowerCase()===b?g:!1}}}else{for(;e<f;e++)c=a[e],c&&(a[e]=d?c.parentNode:c.parentNode===b);d&&k.filter(b,a,!0)}},"":function(a,b,c){var e,f=d++,g=u;typeof b=="string"&&!j.test(b)&&(b=b.toLowerCase(),e=b,g=t),g("parentNode",b,f,a,e,c)},"~":function(a,b,c){var e,f=d++,g=u;typeof b=="string"&&!j.test(b)&&(b=b.toLowerCase(),e=b,g=t),g("previousSibling",b,f,a,e,c)}},find:{ID:function(a,b,c){if(typeof b.getElementById!="undefined"&&!c){var d=b.getElementById(a[1]);return d&&d.parentNode?[d]:[]}},NAME:function(a,b){if(typeof b.getElementsByName!="undefined"){var c=[],d=b.getElementsByName(a[1]);for(var e=0,f=d.length;e<f;e++)d[e].getAttribute("name")===a[1]&&c.push(d[e]);return c.length===0?null:c}},TAG:function(a,b){if(typeof b.getElementsByTagName!="undefined")return b.getElementsByTagName(a[1])}},preFilter:{CLASS:function(a,b,c,d,e,f){a=" "+a[1].replace(i,"")+" ";if(f)return a;for(var g=0,h;(h=b[g])!=null;g++)h&&(e^(h.className&&(" "+h.className+" ").replace(/[\t\n\r]/g," ").indexOf(a)>=0)?c||d.push(h):c&&(b[g]=!1));return!1},ID:function(a){return a[1].replace(i,"")},TAG:function(a,b){return a[1].replace(i,"").toLowerCase()},CHILD:function(a){if(a[1]==="nth"){a[2]||k.error(a[0]),a[2]=a[2].replace(/^\+|\s*/g,"");var b=/(-?)(\d*)(?:n([+\-]?\d*))?/.exec(a[2]==="even"&&"2n"||a[2]==="odd"&&"2n+1"||!/\D/.test(a[2])&&"0n+"+a[2]||a[2]);a[2]=b[1]+(b[2]||1)-0,a[3]=b[3]-0}else a[2]&&k.error(a[0]);a[0]=d++;return a},ATTR:function(a,b,c,d,e,f){var g=a[1]=a[1].replace(i,"");!f&&l.attrMap[g]&&(a[1]=l.attrMap[g]),a[4]=(a[4]||a[5]||"").replace(i,""),a[2]==="~="&&(a[4]=" "+a[4]+" ");return a},PSEUDO:function(b,c,d,e,f){if(b[1]==="not")if((a.exec(b[3])||"").length>1||/^\w/.test(b[3]))b[3]=k(b[3],null,null,c);else{var g=k.filter(b[3],c,d,!0^f);d||e.push.apply(e,g);return!1}else if(l.match.POS.test(b[0])||l.match.CHILD.test(b[0]))return!0;return b},POS:function(a){a.unshift(!0);return a}},filters:{enabled:function(a){return a.disabled===!1&&a.type!=="hidden"},disabled:function(a){return a.disabled===!0},checked:function(a){return a.checked===!0},selected:function(a){a.parentNode&&a.parentNode.selectedIndex;return a.selected===!0},parent:function(a){return!!a.firstChild},empty:function(a){return!a.firstChild},has:function(a,b,c){return!!k(c[3],a).length},header:function(a){return/h\d/i.test(a.nodeName)},text:function(a){var b=a.getAttribute("type"),c=a.type;return a.nodeName.toLowerCase()==="input"&&"text"===c&&(b===c||b===null)},radio:function(a){return a.nodeName.toLowerCase()==="input"&&"radio"===a.type},checkbox:function(a){return a.nodeName.toLowerCase()==="input"&&"checkbox"===a.type},file:function(a){return a.nodeName.toLowerCase()==="input"&&"file"===a.type},password:function(a){return a.nodeName.toLowerCase()==="input"&&"password"===a.type},submit:function(a){var b=a.nodeName.toLowerCase();return(b==="input"||b==="button")&&"submit"===a.type},image:function(a){return a.nodeName.toLowerCase()==="input"&&"image"===a.type},reset:function(a){var b=a.nodeName.toLowerCase();return(b==="input"||b==="button")&&"reset"===a.type},button:function(a){var b=a.nodeName.toLowerCase();return b==="input"&&"button"===a.type||b==="button"},input:function(a){return/input|select|textarea|button/i.test(a.nodeName)},focus:function(a){return a===a.ownerDocument.activeElement}},setFilters:{first:function(a,b){return b===0},last:function(a,b,c,d){return b===d.length-1},even:function(a,b){return b%2===0},odd:function(a,b){return b%2===1},lt:function(a,b,c){return b<c[3]-0},gt:function(a,b,c){return b>c[3]-0},nth:function(a,b,c){return c[3]-0===b},eq:function(a,b,c){return c[3]-0===b}},filter:{PSEUDO:function(a,b,c,d){var e=b[1],f=l.filters[e];if(f)return f(a,c,b,d);if(e==="contains")return(a.textContent||a.innerText||k.getText([a])||"").indexOf(b[3])>=0;if(e==="not"){var g=b[3];for(var h=0,i=g.length;h<i;h++)if(g[h]===a)return!1;return!0}k.error(e)},CHILD:function(a,b){var c=b[1],d=a;switch(c){case"only":case"first":while(d=d.previousSibling)if(d.nodeType===1)return!1;if(c==="first")return!0;d=a;case"last":while(d=d.nextSibling)if(d.nodeType===1)return!1;return!0;case"nth":var e=b[2],f=b[3];if(e===1&&f===0)return!0;var g=b[0],h=a.parentNode;if(h&&(h.sizcache!==g||!a.nodeIndex)){var i=0;for(d=h.firstChild;d;d=d.nextSibling)d.nodeType===1&&(d.nodeIndex=++i);h.sizcache=g}var j=a.nodeIndex-f;return e===0?j===0:j%e===0&&j/e>=0}},ID:function(a,b){return a.nodeType===1&&a.getAttribute("id")===b},TAG:function(a,b){return b==="*"&&a.nodeType===1||a.nodeName.toLowerCase()===b},CLASS:function(a,b){return(" "+(a.className||a.getAttribute("class"))+" ").indexOf(b)>-1},ATTR:function(a,b){var c=b[1],d=l.attrHandle[c]?l.attrHandle[c](a):a[c]!=null?a[c]:a.getAttribute(c),e=d+"",f=b[2],g=b[4];return d==null?f==="!=":f==="="?e===g:f==="*="?e.indexOf(g)>=0:f==="~="?(" "+e+" ").indexOf(g)>=0:g?f==="!="?e!==g:f==="^="?e.indexOf(g)===0:f==="$="?e.substr(e.length-g.length)===g:f==="|="?e===g||e.substr(0,g.length+1)===g+"-":!1:e&&d!==!1},POS:function(a,b,c,d){var e=b[2],f=l.setFilters[e];if(f)return f(a,c,b,d)}}},m=l.match.POS,n=function(a,b){return"\\"+(b-0+1)};for(var o in l.match)l.match[o]=new RegExp(l.match[o].source+/(?![^\[]*\])(?![^\(]*\))/.source),l.leftMatch[o]=new RegExp(/(^(?:.|\r|\n)*?)/.source+l.match[o].source.replace(/\\(\d+)/g,n));var p=function(a,b){a=Array.prototype.slice.call(a,0);if(b){b.push.apply(b,a);return b}return a};try{Array.prototype.slice.call(c.documentElement.childNodes,0)[0].nodeType}catch(q){p=function(a,b){var c=0,d=b||[];if(e.call(a)==="[object Array]")Array.prototype.push.apply(d,a);else if(typeof a.length=="number")for(var f=a.length;c<f;c++)d.push(a[c]);else for(;a[c];c++)d.push(a[c]);return d}}var r,s;c.documentElement.compareDocumentPosition?r=function(a,b){if(a===b){g=!0;return 0}if(!a.compareDocumentPosition||!b.compareDocumentPosition)return a.compareDocumentPosition?-1:1;return a.compareDocumentPosition(b)&4?-1:1}:(r=function(a,b){if(a===b){g=!0;return 0}if(a.sourceIndex&&b.sourceIndex)return a.sourceIndex-b.sourceIndex;var c,d,e=[],f=[],h=a.parentNode,i=b.parentNode,j=h;if(h===i)return s(a,b);if(!h)return-1;if(!i)return 1;while(j)e.unshift(j),j=j.parentNode;j=i;while(j)f.unshift(j),j=j.parentNode;c=e.length,d=f.length;for(var k=0;k<c&&k<d;k++)if(e[k]!==f[k])return s(e[k],f[k]);return k===c?s(a,f[k],-1):s(e[k],b,1)},s=function(a,b,c){if(a===b)return c;var d=a.nextSibling;while(d){if(d===b)return-1;d=d.nextSibling}return 1}),k.getText=function(a){var b="",c;for(var d=0;a[d];d++)c=a[d],c.nodeType===3||c.nodeType===4?b+=c.nodeValue:c.nodeType!==8&&(b+=k.getText(c.childNodes));return b},function(){var a=c.createElement("div"),d="script"+(new Date).getTime(),e=c.documentElement;a.innerHTML="<a name='"+d+"'/>",e.insertBefore(a,e.firstChild),c.getElementById(d)&&(l.find.ID=function(a,c,d){if(typeof c.getElementById!="undefined"&&!d){var e=c.getElementById(a[1]);return e?e.id===a[1]||typeof e.getAttributeNode!="undefined"&&e.getAttributeNode("id").nodeValue===a[1]?[e]:b:[]}},l.filter.ID=function(a,b){var c=typeof a.getAttributeNode!="undefined"&&a.getAttributeNode("id");return a.nodeType===1&&c&&c.nodeValue===b}),e.removeChild(a),e=a=null}(),function(){var a=c.createElement("div");a.appendChild(c.createComment("")),a.getElementsByTagName("*").length>0&&(l.find.TAG=function(a,b){var c=b.getElementsByTagName(a[1]);if(a[1]==="*"){var d=[];for(var e=0;c[e];e++)c[e].nodeType===1&&d.push(c[e]);c=d}return c}),a.innerHTML="<a href='#'></a>",a.firstChild&&typeof a.firstChild.getAttribute!="undefined"&&a.firstChild.getAttribute("href")!=="#"&&(l.attrHandle.href=function(a){return a.getAttribute("href",2)}),a=null}(),c.querySelectorAll&&function(){var a=k,b=c.createElement("div"),d="__sizzle__";b.innerHTML="<p class='TEST'></p>";if(!b.querySelectorAll||b.querySelectorAll(".TEST").length!==0){k=function(b,e,f,g){e=e||c;if(!g&&!k.isXML(e)){var h=/^(\w+$)|^\.([\w\-]+$)|^#([\w\-]+$)/.exec(b);if(h&&(e.nodeType===1||e.nodeType===9)){if(h[1])return p(e.getElementsByTagName(b),f);if(h[2]&&l.find.CLASS&&e.getElementsByClassName)return p(e.getElementsByClassName(h[2]),f)}if(e.nodeType===9){if(b==="body"&&e.body)return p([e.body],f);if(h&&h[3]){var i=e.getElementById(h[3]);if(!i||!i.parentNode)return p([],f);if(i.id===h[3])return p([i],f)}try{return p(e.querySelectorAll(b),f)}catch(j){}}else if(e.nodeType===1&&e.nodeName.toLowerCase()!=="object"){var m=e,n=e.getAttribute("id"),o=n||d,q=e.parentNode,r=/^\s*[+~]/.test(b);n?o=o.replace(/'/g,"\\$&"):e.setAttribute("id",o),r&&q&&(e=e.parentNode);try{if(!r||q)return p(e.querySelectorAll("[id='"+o+"'] "+b),f)}catch(s){}finally{n||m.removeAttribute("id")}}}return a(b,e,f,g)};for(var e in a)k[e]=a[e];b=null}}(),function(){var a=c.documentElement,b=a.matchesSelector||a.mozMatchesSelector||a.webkitMatchesSelector||a.msMatchesSelector;if(b){var d=!b.call(c.createElement("div"),"div"),e=!1;try{b.call(c.documentElement,"[test!='']:sizzle")}catch(f){e=!0}k.matchesSelector=function(a,c){c=c.replace(/\=\s*([^'"\]]*)\s*\]/g,"='$1']");if(!k.isXML(a))try{if(e||!l.match.PSEUDO.test(c)&&!/!=/.test(c)){var f=b.call(a,c);if(f||!d||a.document&&a.document.nodeType!==11)return f}}catch(g){}return k(c,null,null,[a]).length>0}}}(),function(){var a=c.createElement("div");a.innerHTML="<div class='test e'></div><div class='test'></div>";if(!!a.getElementsByClassName&&a.getElementsByClassName("e").length!==0){a.lastChild.className="e";if(a.getElementsByClassName("e").length===1)return;l.order.splice(1,0,"CLASS"),l.find.CLASS=function(a,b,c){if(typeof b.getElementsByClassName!="undefined"&&!c)return b.getElementsByClassName(a[1])},a=null}}(),c.documentElement.contains?k.contains=function(a,b){return a!==b&&(a.contains?a.contains(b):!0)}:c.documentElement.compareDocumentPosition?k.contains=function(a,b){return!!(a.compareDocumentPosition(b)&16)}:k.contains=function(){return!1},k.isXML=function(a){var b=(a?a.ownerDocument||a:0).documentElement;return b?b.nodeName!=="HTML":!1};var v=function(a,b){var c,d=[],e="",f=b.nodeType?[b]:b;while(c=l.match.PSEUDO.exec(a))e+=c[0],a=a.replace(l.match.PSEUDO,"");a=l.relative[a]?a+"*":a;for(var g=0,h=f.length;g<h;g++)k(a,f[g],d);return k.filter(e,d)};f.find=k,f.expr=k.selectors,f.expr[":"]=f.expr.filters,f.unique=k.uniqueSort,f.text=k.getText,f.isXMLDoc=k.isXML,f.contains=k.contains}();var O=/Until$/,P=/^(?:parents|prevUntil|prevAll)/,Q=/,/,R=/^.[^:#\[\.,]*$/,S=Array.prototype.slice,T=f.expr.match.POS,U={children:!0,contents:!0,next:!0,prev:!0};f.fn.extend({find:function(a){var b=this,c,d;if(typeof a!="string")return f(a).filter(function(){for(c=0,d=b.length;c<d;c++)if(f.contains(b[c],this))return!0});var e=this.pushStack("","find",a),g,h,i;for(c=0,d=this.length;c<d;c++){g=e.length,f.find(a,this[c],e);if(c>0)for(h=g;h<e.length;h++)for(i=0;i<g;i++)if(e[i]===e[h]){e.splice(h--,1);break}}return e},has:function(a){var b=f(a);return this.filter(function(){for(var a=0,c=b.length;a<c;a++)if(f.contains(this,b[a]))return!0})},not:function(a){return this.pushStack(W(this,a,!1),"not",a)},filter:function(a){return this.pushStack(W(this,a,!0),"filter",a)},is:function(a){return!!a&&(typeof a=="string"?f.filter(a,this).length>0:this.filter(a).length>0)},closest:function(a,b){var c=[],d,e,g=this[0];if(f.isArray(a)){var h,i,j={},k=1;if(g&&a.length){for(d=0,e=a.length;d<e;d++)i=a[d],j[i]||(j[i]=T.test(i)?f(i,b||this.context):i);while(g&&g.ownerDocument&&g!==b){for(i in j)h=j[i],(h.jquery?h.index(g)>-1:f(g).is(h))&&c.push({selector:i,elem:g,level:k});g=g.parentNode,k++}}return c}var l=T.test(a)||typeof a!="string"?f(a,b||this.context):0;for(d=0,e=this.length;d<e;d++){g=this[d];while(g){if(l?l.index(g)>-1:f.find.matchesSelector(g,a)){c.push(g);break}g=g.parentNode;if(!g||!g.ownerDocument||g===b||g.nodeType===11)break}}c=c.length>1?f.unique(c):c;return this.pushStack(c,"closest",a)},index:function(a){if(!a||typeof a=="string")return f.inArray(this[0],a?f(a):this.parent().children());return f.inArray(a.jquery?a[0]:a,this)},add:function(a,b){var c=typeof a=="string"?f(a,b):f.makeArray(a&&a.nodeType?[a]:a),d=f.merge(this.get(),c);return this.pushStack(V(c[0])||V(d[0])?d:f.unique(d))},andSelf:function(){return this.add(this.prevObject)}}),f.each({parent:function(a){var b=a.parentNode;return b&&b.nodeType!==11?b:null},parents:function(a){return f.dir(a,"parentNode")},parentsUntil:function(a,b,c){return f.dir(a,"parentNode",c)},next:function(a){return f.nth(a,2,"nextSibling")},prev:function(a){return f.nth(a,2,"previousSibling")},nextAll:function(a){return f.dir(a,"nextSibling")},prevAll:function(a){return f.dir(a,"previousSibling")},nextUntil:function(a,b,c){return f.dir(a,"nextSibling",c)},prevUntil:function(a,b,c){return f.dir(a,"previousSibling",c)},siblings:function(a){return f.sibling(a.parentNode.firstChild,a)},children:function(a){return f.sibling(a.firstChild)},contents:function(a){return f.nodeName(a,"iframe")?a.contentDocument||a.contentWindow.document:f.makeArray(a.childNodes)}},function(a,b){f.fn[a]=function(c,d){var e=f.map(this,b,c),g=S.call(arguments);O.test(a)||(d=c),d&&typeof d=="string"&&(e=f.filter(d,e)),e=this.length>1&&!U[a]?f.unique(e):e,(this.length>1||Q.test(d))&&P.test(a)&&(e=e.reverse());return this.pushStack(e,a,g.join(","))}}),f.extend({filter:function(a,b,c){c&&(a=":not("+a+")");return b.length===1?f.find.matchesSelector(b[0],a)?[b[0]]:[]:f.find.matches(a,b)},dir:function(a,c,d){var e=[],g=a[c];while(g&&g.nodeType!==9&&(d===b||g.nodeType!==1||!f(g).is(d)))g.nodeType===1&&e.push(g),g=g[c];return e},nth:function(a,b,c,d){b=b||1;var e=0;for(;a;a=a[c])if(a.nodeType===1&&++e===b)break;return a},sibling:function(a,b){var c=[];for(;a;a=a.nextSibling)a.nodeType===1&&a!==b&&c.push(a);return c}});var X=/ jQuery\d+="(?:\d+|null)"/g,Y=/^\s+/,Z=/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/ig,$=/<([\w:]+)/,_=/<tbody/i,ba=/<|&#?\w+;/,bb=/<(?:script|object|embed|option|style)/i,bc=/checked\s*(?:[^=]|=\s*.checked.)/i,bd=/\/(java|ecma)script/i,be=/^\s*<!(?:\[CDATA\[|\-\-)/,bf={option:[1,"<select multiple='multiple'>","</select>"],legend:[1,"<fieldset>","</fieldset>"],thead:[1,"<table>","</table>"],tr:[2,"<table><tbody>","</tbody></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],col:[2,"<table><tbody></tbody><colgroup>","</colgroup></table>"],area:[1,"<map>","</map>"],_default:[0,"",""]};bf.optgroup=bf.option,bf.tbody=bf.tfoot=bf.colgroup=bf.caption=bf.thead,bf.th=bf.td,f.support.htmlSerialize||(bf._default=[1,"div<div>","</div>"]),f.fn.extend({text:function(a){if(f.isFunction(a))return this.each(function(b){var c=f(this);c.text(a.call(this,b,c.text()))});if(typeof a!="object"&&a!==b)return this.empty().append((this[0]&&this[0].ownerDocument||c).createTextNode(a));return f.text(this)},wrapAll:function(a){if(f.isFunction(a))return this.each(function(b){f(this).wrapAll(a.call(this,b))});if(this[0]){var b=f(a,this[0].ownerDocument).eq(0).clone(!0);this[0].parentNode&&b.insertBefore(this[0]),b.map(function(){var a=this;while(a.firstChild&&a.firstChild.nodeType===1)a=a.firstChild;return a}).append(this)}return this},wrapInner:function(a){if(f.isFunction(a))return this.each(function(b){f(this).wrapInner(a.call(this,b))});return this.each(function(){var b=f(this),c=b.contents();c.length?c.wrapAll(a):b.append(a)})},wrap:function(a){return this.each(function(){f(this).wrapAll(a)})},unwrap:function(){return this.parent().each(function(){f.nodeName(this,"body")||f(this).replaceWith(this.childNodes)}).end()},append:function(){return this.domManip(arguments,!0,function(a){this.nodeType===1&&this.appendChild(a)})},prepend:function(){return this.domManip(arguments,!0,function(a){this.nodeType===1&&this.insertBefore(a,this.firstChild)})},before:function(){if(this[0]&&this[0].parentNode)return this.domManip(arguments,!1,function(a){this.parentNode.insertBefore(a,this)});if(arguments.length){var a=f(arguments[0]);a.push.apply(a,this.toArray());return this.pushStack(a,"before",arguments)}},after:function(){if(this[0]&&this[0].parentNode)return this.domManip(arguments,!1,function(a){this.parentNode.insertBefore(a,this.nextSibling)});if(arguments.length){var a=this.pushStack(this,"after",arguments);a.push.apply(a,f(arguments[0]).toArray());return a}},remove:function(a,b){for(var c=0,d;(d=this[c])!=null;c++)if(!a||f.filter(a,[d]).length)!b&&d.nodeType===1&&(f.cleanData(d.getElementsByTagName("*")),f.cleanData([d])),d.parentNode&&d.parentNode.removeChild(d);return this},empty:function(){for(var a=0,b;(b=this[a])!=null;a++){b.nodeType===1&&f.cleanData(b.getElementsByTagName("*"));while(b.firstChild)b.removeChild(b.firstChild)}return this},clone:function(a,b){a=a==null?!1:a,b=b==null?a:b;return this.map(function(){return f.clone(this,a,b)})},html:function(a){if(a===b)return this[0]&&this[0].nodeType===1?this[0].innerHTML.replace(X,""):null;if(typeof a=="string"&&!bb.test(a)&&(f.support.leadingWhitespace||!Y.test(a))&&!bf[($.exec(a)||["",""])[1].toLowerCase()]){a=a.replace(Z,"<$1></$2>");try{for(var c=0,d=this.length;c<d;c++)this[c].nodeType===1&&(f.cleanData(this[c].getElementsByTagName("*")),this[c].innerHTML=a)}catch(e){this.empty().append(a)}}else f.isFunction(a)?this.each(function(b){var c=f(this);c.html(a.call(this,b,c.html()))}):this.empty().append(a);return this},replaceWith:function(a){if(this[0]&&this[0].parentNode){if(f.isFunction(a))return this.each(function(b){var c=f(this),d=c.html();c.replaceWith(a.call(this,b,d))});typeof a!="string"&&(a=f(a).detach());return this.each(function(){var b=this.nextSibling,c=this.parentNode;f(this).remove(),b?f(b).before(a):f(c).append(a)})}return this.length?this.pushStack(f(f.isFunction(a)?a():a),"replaceWith",a):this},detach:function(a){return this.remove(a,!0)},domManip:function(a,c,d){var e,g,h,i,j=a[0],k=[];if(!f.support.checkClone&&arguments.length===3&&typeof j=="string"&&bc.test(j))return this.each(function(){f(this).domManip(a,c,d,!0)});if(f.isFunction(j))return this.each(function(e){var g=f(this);a[0]=j.call(this,e,c?g.html():b),g.domManip(a,c,d)});if(this[0]){i=j&&j.parentNode,f.support.parentNode&&i&&i.nodeType===11&&i.childNodes.length===this.length?e={fragment:i}:e=f.buildFragment(a,this,k),h=e.fragment,h.childNodes.length===1?g=h=h.firstChild:g=h.firstChild;if(g){c=c&&f.nodeName(g,"tr");for(var l=0,m=this.length,n=m-1;l<m;l++)d.call(c?bg(this[l],g):this[l],e.cacheable||m>1&&l<n?f.clone(h,!0,!0):h)}k.length&&f.each(k,bm)}return this}}),f.buildFragment=function(a,b,d){var e,g,h,i;b&&b[0]&&(i=b[0].ownerDocument||b[0]),i.createDocumentFragment||(i=c),a.length===1&&typeof a[0]=="string"&&a[0].length<512&&i===c&&a[0].charAt(0)==="<"&&!bb.test(a[0])&&(f.support.checkClone||!bc.test(a[0]))&&(g=!0,h=f.fragments[a[0]],h&&h!==1&&(e=h)),e||(e=i.createDocumentFragment(),f.clean(a,i,e,d)),g&&(f.fragments[a[0]]=h?e:1);return{fragment:e,cacheable:g}},f.fragments={},f.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(a,b){f.fn[a]=function(c){var d=[],e=f(c),g=this.length===1&&this[0].parentNode;if(g&&g.nodeType===11&&g.childNodes.length===1&&e.length===1){e[b](this[0]);return this}for(var h=0,i=e.length;h<i;h++){var j=(h>0?this.clone(!0):this).get();f(e[h])[b](j),d=d.concat(j
)}return this.pushStack(d,a,e.selector)}}),f.extend({clone:function(a,b,c){var d=a.cloneNode(!0),e,g,h;if((!f.support.noCloneEvent||!f.support.noCloneChecked)&&(a.nodeType===1||a.nodeType===11)&&!f.isXMLDoc(a)){bi(a,d),e=bj(a),g=bj(d);for(h=0;e[h];++h)bi(e[h],g[h])}if(b){bh(a,d);if(c){e=bj(a),g=bj(d);for(h=0;e[h];++h)bh(e[h],g[h])}}e=g=null;return d},clean:function(a,b,d,e){var g;b=b||c,typeof b.createElement=="undefined"&&(b=b.ownerDocument||b[0]&&b[0].ownerDocument||c);var h=[],i;for(var j=0,k;(k=a[j])!=null;j++){typeof k=="number"&&(k+="");if(!k)continue;if(typeof k=="string")if(!ba.test(k))k=b.createTextNode(k);else{k=k.replace(Z,"<$1></$2>");var l=($.exec(k)||["",""])[1].toLowerCase(),m=bf[l]||bf._default,n=m[0],o=b.createElement("div");o.innerHTML=m[1]+k+m[2];while(n--)o=o.lastChild;if(!f.support.tbody){var p=_.test(k),q=l==="table"&&!p?o.firstChild&&o.firstChild.childNodes:m[1]==="<table>"&&!p?o.childNodes:[];for(i=q.length-1;i>=0;--i)f.nodeName(q[i],"tbody")&&!q[i].childNodes.length&&q[i].parentNode.removeChild(q[i])}!f.support.leadingWhitespace&&Y.test(k)&&o.insertBefore(b.createTextNode(Y.exec(k)[0]),o.firstChild),k=o.childNodes}var r;if(!f.support.appendChecked)if(k[0]&&typeof (r=k.length)=="number")for(i=0;i<r;i++)bl(k[i]);else bl(k);k.nodeType?h.push(k):h=f.merge(h,k)}if(d){g=function(a){return!a.type||bd.test(a.type)};for(j=0;h[j];j++)if(e&&f.nodeName(h[j],"script")&&(!h[j].type||h[j].type.toLowerCase()==="text/javascript"))e.push(h[j].parentNode?h[j].parentNode.removeChild(h[j]):h[j]);else{if(h[j].nodeType===1){var s=f.grep(h[j].getElementsByTagName("script"),g);h.splice.apply(h,[j+1,0].concat(s))}d.appendChild(h[j])}}return h},cleanData:function(a){var b,c,d=f.cache,e=f.expando,g=f.event.special,h=f.support.deleteExpando;for(var i=0,j;(j=a[i])!=null;i++){if(j.nodeName&&f.noData[j.nodeName.toLowerCase()])continue;c=j[f.expando];if(c){b=d[c]&&d[c][e];if(b&&b.events){for(var k in b.events)g[k]?f.event.remove(j,k):f.removeEvent(j,k,b.handle);b.handle&&(b.handle.elem=null)}h?delete j[f.expando]:j.removeAttribute&&j.removeAttribute(f.expando),delete d[c]}}}});var bn=/alpha\([^)]*\)/i,bo=/opacity=([^)]*)/,bp=/([A-Z]|^ms)/g,bq=/^-?\d+(?:px)?$/i,br=/^-?\d/,bs=/^[+\-]=/,bt=/[^+\-\.\de]+/g,bu={position:"absolute",visibility:"hidden",display:"block"},bv=["Left","Right"],bw=["Top","Bottom"],bx,by,bz;f.fn.css=function(a,c){if(arguments.length===2&&c===b)return this;return f.access(this,a,c,!0,function(a,c,d){return d!==b?f.style(a,c,d):f.css(a,c)})},f.extend({cssHooks:{opacity:{get:function(a,b){if(b){var c=bx(a,"opacity","opacity");return c===""?"1":c}return a.style.opacity}}},cssNumber:{fillOpacity:!0,fontWeight:!0,lineHeight:!0,opacity:!0,orphans:!0,widows:!0,zIndex:!0,zoom:!0},cssProps:{"float":f.support.cssFloat?"cssFloat":"styleFloat"},style:function(a,c,d,e){if(!!a&&a.nodeType!==3&&a.nodeType!==8&&!!a.style){var g,h,i=f.camelCase(c),j=a.style,k=f.cssHooks[i];c=f.cssProps[i]||i;if(d===b){if(k&&"get"in k&&(g=k.get(a,!1,e))!==b)return g;return j[c]}h=typeof d;if(h==="number"&&isNaN(d)||d==null)return;h==="string"&&bs.test(d)&&(d=+d.replace(bt,"")+parseFloat(f.css(a,c)),h="number"),h==="number"&&!f.cssNumber[i]&&(d+="px");if(!k||!("set"in k)||(d=k.set(a,d))!==b)try{j[c]=d}catch(l){}}},css:function(a,c,d){var e,g;c=f.camelCase(c),g=f.cssHooks[c],c=f.cssProps[c]||c,c==="cssFloat"&&(c="float");if(g&&"get"in g&&(e=g.get(a,!0,d))!==b)return e;if(bx)return bx(a,c)},swap:function(a,b,c){var d={};for(var e in b)d[e]=a.style[e],a.style[e]=b[e];c.call(a);for(e in b)a.style[e]=d[e]}}),f.curCSS=f.css,f.each(["height","width"],function(a,b){f.cssHooks[b]={get:function(a,c,d){var e;if(c){if(a.offsetWidth!==0)return bA(a,b,d);f.swap(a,bu,function(){e=bA(a,b,d)});return e}},set:function(a,b){if(!bq.test(b))return b;b=parseFloat(b);if(b>=0)return b+"px"}}}),f.support.opacity||(f.cssHooks.opacity={get:function(a,b){return bo.test((b&&a.currentStyle?a.currentStyle.filter:a.style.filter)||"")?parseFloat(RegExp.$1)/100+"":b?"1":""},set:function(a,b){var c=a.style,d=a.currentStyle;c.zoom=1;var e=f.isNaN(b)?"":"alpha(opacity="+b*100+")",g=d&&d.filter||c.filter||"";c.filter=bn.test(g)?g.replace(bn,e):g+" "+e}}),f(function(){f.support.reliableMarginRight||(f.cssHooks.marginRight={get:function(a,b){var c;f.swap(a,{display:"inline-block"},function(){b?c=bx(a,"margin-right","marginRight"):c=a.style.marginRight});return c}})}),c.defaultView&&c.defaultView.getComputedStyle&&(by=function(a,c){var d,e,g;c=c.replace(bp,"-$1").toLowerCase();if(!(e=a.ownerDocument.defaultView))return b;if(g=e.getComputedStyle(a,null))d=g.getPropertyValue(c),d===""&&!f.contains(a.ownerDocument.documentElement,a)&&(d=f.style(a,c));return d}),c.documentElement.currentStyle&&(bz=function(a,b){var c,d=a.currentStyle&&a.currentStyle[b],e=a.runtimeStyle&&a.runtimeStyle[b],f=a.style;!bq.test(d)&&br.test(d)&&(c=f.left,e&&(a.runtimeStyle.left=a.currentStyle.left),f.left=b==="fontSize"?"1em":d||0,d=f.pixelLeft+"px",f.left=c,e&&(a.runtimeStyle.left=e));return d===""?"auto":d}),bx=by||bz,f.expr&&f.expr.filters&&(f.expr.filters.hidden=function(a){var b=a.offsetWidth,c=a.offsetHeight;return b===0&&c===0||!f.support.reliableHiddenOffsets&&(a.style.display||f.css(a,"display"))==="none"},f.expr.filters.visible=function(a){return!f.expr.filters.hidden(a)});var bB=/%20/g,bC=/\[\]$/,bD=/\r?\n/g,bE=/#.*$/,bF=/^(.*?):[ \t]*([^\r\n]*)\r?$/mg,bG=/^(?:color|date|datetime|email|hidden|month|number|password|range|search|tel|text|time|url|week)$/i,bH=/^(?:about|app|app\-storage|.+\-extension|file|widget):$/,bI=/^(?:GET|HEAD)$/,bJ=/^\/\//,bK=/\?/,bL=/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,bM=/^(?:select|textarea)/i,bN=/\s+/,bO=/([?&])_=[^&]*/,bP=/^([\w\+\.\-]+:)(?:\/\/([^\/?#:]*)(?::(\d+))?)?/,bQ=f.fn.load,bR={},bS={},bT,bU;try{bT=e.href}catch(bV){bT=c.createElement("a"),bT.href="",bT=bT.href}bU=bP.exec(bT.toLowerCase())||[],f.fn.extend({load:function(a,c,d){if(typeof a!="string"&&bQ)return bQ.apply(this,arguments);if(!this.length)return this;var e=a.indexOf(" ");if(e>=0){var g=a.slice(e,a.length);a=a.slice(0,e)}var h="GET";c&&(f.isFunction(c)?(d=c,c=b):typeof c=="object"&&(c=f.param(c,f.ajaxSettings.traditional),h="POST"));var i=this;f.ajax({url:a,type:h,dataType:"html",data:c,complete:function(a,b,c){c=a.responseText,a.isResolved()&&(a.done(function(a){c=a}),i.html(g?f("<div>").append(c.replace(bL,"")).find(g):c)),d&&i.each(d,[c,b,a])}});return this},serialize:function(){return f.param(this.serializeArray())},serializeArray:function(){return this.map(function(){return this.elements?f.makeArray(this.elements):this}).filter(function(){return this.name&&!this.disabled&&(this.checked||bM.test(this.nodeName)||bG.test(this.type))}).map(function(a,b){var c=f(this).val();return c==null?null:f.isArray(c)?f.map(c,function(a,c){return{name:b.name,value:a.replace(bD,"\r\n")}}):{name:b.name,value:c.replace(bD,"\r\n")}}).get()}}),f.each("ajaxStart ajaxStop ajaxComplete ajaxError ajaxSuccess ajaxSend".split(" "),function(a,b){f.fn[b]=function(a){return this.bind(b,a)}}),f.each(["get","post"],function(a,c){f[c]=function(a,d,e,g){f.isFunction(d)&&(g=g||e,e=d,d=b);return f.ajax({type:c,url:a,data:d,success:e,dataType:g})}}),f.extend({getScript:function(a,c){return f.get(a,b,c,"script")},getJSON:function(a,b,c){return f.get(a,b,c,"json")},ajaxSetup:function(a,b){b?f.extend(!0,a,f.ajaxSettings,b):(b=a,a=f.extend(!0,f.ajaxSettings,b));for(var c in{context:1,url:1})c in b?a[c]=b[c]:c in f.ajaxSettings&&(a[c]=f.ajaxSettings[c]);return a},ajaxSettings:{url:bT,isLocal:bH.test(bU[1]),global:!0,type:"GET",contentType:"application/x-www-form-urlencoded",processData:!0,async:!0,accepts:{xml:"application/xml, text/xml",html:"text/html",text:"text/plain",json:"application/json, text/javascript","*":"*/*"},contents:{xml:/xml/,html:/html/,json:/json/},responseFields:{xml:"responseXML",text:"responseText"},converters:{"* text":a.String,"text html":!0,"text json":f.parseJSON,"text xml":f.parseXML}},ajaxPrefilter:bW(bR),ajaxTransport:bW(bS),ajax:function(a,c){function w(a,c,l,m){if(s!==2){s=2,q&&clearTimeout(q),p=b,n=m||"",v.readyState=a?4:0;var o,r,u,w=l?bZ(d,v,l):b,x,y;if(a>=200&&a<300||a===304){if(d.ifModified){if(x=v.getResponseHeader("Last-Modified"))f.lastModified[k]=x;if(y=v.getResponseHeader("Etag"))f.etag[k]=y}if(a===304)c="notmodified",o=!0;else try{r=b$(d,w),c="success",o=!0}catch(z){c="parsererror",u=z}}else{u=c;if(!c||a)c="error",a<0&&(a=0)}v.status=a,v.statusText=c,o?h.resolveWith(e,[r,c,v]):h.rejectWith(e,[v,c,u]),v.statusCode(j),j=b,t&&g.trigger("ajax"+(o?"Success":"Error"),[v,d,o?r:u]),i.resolveWith(e,[v,c]),t&&(g.trigger("ajaxComplete",[v,d]),--f.active||f.event.trigger("ajaxStop"))}}typeof a=="object"&&(c=a,a=b),c=c||{};var d=f.ajaxSetup({},c),e=d.context||d,g=e!==d&&(e.nodeType||e instanceof f)?f(e):f.event,h=f.Deferred(),i=f._Deferred(),j=d.statusCode||{},k,l={},m={},n,o,p,q,r,s=0,t,u,v={readyState:0,setRequestHeader:function(a,b){if(!s){var c=a.toLowerCase();a=m[c]=m[c]||a,l[a]=b}return this},getAllResponseHeaders:function(){return s===2?n:null},getResponseHeader:function(a){var c;if(s===2){if(!o){o={};while(c=bF.exec(n))o[c[1].toLowerCase()]=c[2]}c=o[a.toLowerCase()]}return c===b?null:c},overrideMimeType:function(a){s||(d.mimeType=a);return this},abort:function(a){a=a||"abort",p&&p.abort(a),w(0,a);return this}};h.promise(v),v.success=v.done,v.error=v.fail,v.complete=i.done,v.statusCode=function(a){if(a){var b;if(s<2)for(b in a)j[b]=[j[b],a[b]];else b=a[v.status],v.then(b,b)}return this},d.url=((a||d.url)+"").replace(bE,"").replace(bJ,bU[1]+"//"),d.dataTypes=f.trim(d.dataType||"*").toLowerCase().split(bN),d.crossDomain==null&&(r=bP.exec(d.url.toLowerCase()),d.crossDomain=!(!r||r[1]==bU[1]&&r[2]==bU[2]&&(r[3]||(r[1]==="http:"?80:443))==(bU[3]||(bU[1]==="http:"?80:443)))),d.data&&d.processData&&typeof d.data!="string"&&(d.data=f.param(d.data,d.traditional)),bX(bR,d,c,v);if(s===2)return!1;t=d.global,d.type=d.type.toUpperCase(),d.hasContent=!bI.test(d.type),t&&f.active++===0&&f.event.trigger("ajaxStart");if(!d.hasContent){d.data&&(d.url+=(bK.test(d.url)?"&":"?")+d.data),k=d.url;if(d.cache===!1){var x=f.now(),y=d.url.replace(bO,"$1_="+x);d.url=y+(y===d.url?(bK.test(d.url)?"&":"?")+"_="+x:"")}}(d.data&&d.hasContent&&d.contentType!==!1||c.contentType)&&v.setRequestHeader("Content-Type",d.contentType),d.ifModified&&(k=k||d.url,f.lastModified[k]&&v.setRequestHeader("If-Modified-Since",f.lastModified[k]),f.etag[k]&&v.setRequestHeader("If-None-Match",f.etag[k])),v.setRequestHeader("Accept",d.dataTypes[0]&&d.accepts[d.dataTypes[0]]?d.accepts[d.dataTypes[0]]+(d.dataTypes[0]!=="*"?", */*; q=0.01":""):d.accepts["*"]);for(u in d.headers)v.setRequestHeader(u,d.headers[u]);if(d.beforeSend&&(d.beforeSend.call(e,v,d)===!1||s===2)){v.abort();return!1}for(u in{success:1,error:1,complete:1})v[u](d[u]);p=bX(bS,d,c,v);if(!p)w(-1,"No Transport");else{v.readyState=1,t&&g.trigger("ajaxSend",[v,d]),d.async&&d.timeout>0&&(q=setTimeout(function(){v.abort("timeout")},d.timeout));try{s=1,p.send(l,w)}catch(z){status<2?w(-1,z):f.error(z)}}return v},param:function(a,c){var d=[],e=function(a,b){b=f.isFunction(b)?b():b,d[d.length]=encodeURIComponent(a)+"="+encodeURIComponent(b)};c===b&&(c=f.ajaxSettings.traditional);if(f.isArray(a)||a.jquery&&!f.isPlainObject(a))f.each(a,function(){e(this.name,this.value)});else for(var g in a)bY(g,a[g],c,e);return d.join("&").replace(bB,"+")}}),f.extend({active:0,lastModified:{},etag:{}});var b_=f.now(),ca=/(\=)\?(&|$)|\?\?/i;f.ajaxSetup({jsonp:"callback",jsonpCallback:function(){return f.expando+"_"+b_++}}),f.ajaxPrefilter("json jsonp",function(b,c,d){var e=b.contentType==="application/x-www-form-urlencoded"&&typeof b.data=="string";if(b.dataTypes[0]==="jsonp"||b.jsonp!==!1&&(ca.test(b.url)||e&&ca.test(b.data))){var g,h=b.jsonpCallback=f.isFunction(b.jsonpCallback)?b.jsonpCallback():b.jsonpCallback,i=a[h],j=b.url,k=b.data,l="$1"+h+"$2";b.jsonp!==!1&&(j=j.replace(ca,l),b.url===j&&(e&&(k=k.replace(ca,l)),b.data===k&&(j+=(/\?/.test(j)?"&":"?")+b.jsonp+"="+h))),b.url=j,b.data=k,a[h]=function(a){g=[a]},d.always(function(){a[h]=i,g&&f.isFunction(i)&&a[h](g[0])}),b.converters["script json"]=function(){g||f.error(h+" was not called");return g[0]},b.dataTypes[0]="json";return"script"}}),f.ajaxSetup({accepts:{script:"text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},contents:{script:/javascript|ecmascript/},converters:{"text script":function(a){f.globalEval(a);return a}}}),f.ajaxPrefilter("script",function(a){a.cache===b&&(a.cache=!1),a.crossDomain&&(a.type="GET",a.global=!1)}),f.ajaxTransport("script",function(a){if(a.crossDomain){var d,e=c.head||c.getElementsByTagName("head")[0]||c.documentElement;return{send:function(f,g){d=c.createElement("script"),d.async="async",a.scriptCharset&&(d.charset=a.scriptCharset),d.src=a.url,d.onload=d.onreadystatechange=function(a,c){if(c||!d.readyState||/loaded|complete/.test(d.readyState))d.onload=d.onreadystatechange=null,e&&d.parentNode&&e.removeChild(d),d=b,c||g(200,"success")},e.insertBefore(d,e.firstChild)},abort:function(){d&&d.onload(0,1)}}}});var cb=a.ActiveXObject?function(){for(var a in cd)cd[a](0,1)}:!1,cc=0,cd;f.ajaxSettings.xhr=a.ActiveXObject?function(){return!this.isLocal&&ce()||cf()}:ce,function(a){f.extend(f.support,{ajax:!!a,cors:!!a&&"withCredentials"in a})}(f.ajaxSettings.xhr()),f.support.ajax&&f.ajaxTransport(function(c){if(!c.crossDomain||f.support.cors){var d;return{send:function(e,g){var h=c.xhr(),i,j;c.username?h.open(c.type,c.url,c.async,c.username,c.password):h.open(c.type,c.url,c.async);if(c.xhrFields)for(j in c.xhrFields)h[j]=c.xhrFields[j];c.mimeType&&h.overrideMimeType&&h.overrideMimeType(c.mimeType),!c.crossDomain&&!e["X-Requested-With"]&&(e["X-Requested-With"]="XMLHttpRequest");try{for(j in e)h.setRequestHeader(j,e[j])}catch(k){}h.send(c.hasContent&&c.data||null),d=function(a,e){var j,k,l,m,n;try{if(d&&(e||h.readyState===4)){d=b,i&&(h.onreadystatechange=f.noop,cb&&delete cd[i]);if(e)h.readyState!==4&&h.abort();else{j=h.status,l=h.getAllResponseHeaders(),m={},n=h.responseXML,n&&n.documentElement&&(m.xml=n),m.text=h.responseText;try{k=h.statusText}catch(o){k=""}!j&&c.isLocal&&!c.crossDomain?j=m.text?200:404:j===1223&&(j=204)}}}catch(p){e||g(-1,p)}m&&g(j,k,m,l)},!c.async||h.readyState===4?d():(i=++cc,cb&&(cd||(cd={},f(a).unload(cb)),cd[i]=d),h.onreadystatechange=d)},abort:function(){d&&d(0,1)}}}});var cg={},ch,ci,cj=/^(?:toggle|show|hide)$/,ck=/^([+\-]=)?([\d+.\-]+)([a-z%]*)$/i,cl,cm=[["height","marginTop","marginBottom","paddingTop","paddingBottom"],["width","marginLeft","marginRight","paddingLeft","paddingRight"],["opacity"]],cn,co=a.webkitRequestAnimationFrame||a.mozRequestAnimationFrame||a.oRequestAnimationFrame;f.fn.extend({show:function(a,b,c){var d,e;if(a||a===0)return this.animate(cr("show",3),a,b,c);for(var g=0,h=this.length;g<h;g++)d=this[g],d.style&&(e=d.style.display,!f._data(d,"olddisplay")&&e==="none"&&(e=d.style.display=""),e===""&&f.css(d,"display")==="none"&&f._data(d,"olddisplay",cs(d.nodeName)));for(g=0;g<h;g++){d=this[g];if(d.style){e=d.style.display;if(e===""||e==="none")d.style.display=f._data(d,"olddisplay")||""}}return this},hide:function(a,b,c){if(a||a===0)return this.animate(cr("hide",3),a,b,c);for(var d=0,e=this.length;d<e;d++)if(this[d].style){var g=f.css(this[d],"display");g!=="none"&&!f._data(this[d],"olddisplay")&&f._data(this[d],"olddisplay",g)}for(d=0;d<e;d++)this[d].style&&(this[d].style.display="none");return this},_toggle:f.fn.toggle,toggle:function(a,b,c){var d=typeof a=="boolean";f.isFunction(a)&&f.isFunction(b)?this._toggle.apply(this,arguments):a==null||d?this.each(function(){var b=d?a:f(this).is(":hidden");f(this)[b?"show":"hide"]()}):this.animate(cr("toggle",3),a,b,c);return this},fadeTo:function(a,b,c,d){return this.filter(":hidden").css("opacity",0).show().end().animate({opacity:b},a,c,d)},animate:function(a,b,c,d){var e=f.speed(b,c,d);if(f.isEmptyObject(a))return this.each(e.complete,[!1]);a=f.extend({},a);return this[e.queue===!1?"each":"queue"](function(){e.queue===!1&&f._mark(this);var b=f.extend({},e),c=this.nodeType===1,d=c&&f(this).is(":hidden"),g,h,i,j,k,l,m,n,o;b.animatedProperties={};for(i in a){g=f.camelCase(i),i!==g&&(a[g]=a[i],delete a[i]),h=a[g],f.isArray(h)?(b.animatedProperties[g]=h[1],h=a[g]=h[0]):b.animatedProperties[g]=b.specialEasing&&b.specialEasing[g]||b.easing||"swing";if(h==="hide"&&d||h==="show"&&!d)return b.complete.call(this);c&&(g==="height"||g==="width")&&(b.overflow=[this.style.overflow,this.style.overflowX,this.style.overflowY],f.css(this,"display")==="inline"&&f.css(this,"float")==="none"&&(f.support.inlineBlockNeedsLayout?(j=cs(this.nodeName),j==="inline"?this.style.display="inline-block":(this.style.display="inline",this.style.zoom=1)):this.style.display="inline-block"))}b.overflow!=null&&(this.style.overflow="hidden");for(i in a)k=new f.fx(this,b,i),h=a[i],cj.test(h)?k[h==="toggle"?d?"show":"hide":h]():(l=ck.exec(h),m=k.cur(),l?(n=parseFloat(l[2]),o=l[3]||(f.cssNumber[i]?"":"px"),o!=="px"&&(f.style(this,i,(n||1)+o),m=(n||1)/k.cur()*m,f.style(this,i,m+o)),l[1]&&(n=(l[1]==="-="?-1:1)*n+m),k.custom(m,n,o)):k.custom(m,h,""));return!0})},stop:function(a,b){a&&this.queue([]),this.each(function(){var a=f.timers,c=a.length;b||f._unmark(!0,this);while(c--)a[c].elem===this&&(b&&a[c](!0),a.splice(c,1))}),b||this.dequeue();return this}}),f.each({slideDown:cr("show",1),slideUp:cr("hide",1),slideToggle:cr("toggle",1),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(a,b){f.fn[a]=function(a,c,d){return this.animate(b,a,c,d)}}),f.extend({speed:function(a,b,c){var d=a&&typeof a=="object"?f.extend({},a):{complete:c||!c&&b||f.isFunction(a)&&a,duration:a,easing:c&&b||b&&!f.isFunction(b)&&b};d.duration=f.fx.off?0:typeof d.duration=="number"?d.duration:d.duration in f.fx.speeds?f.fx.speeds[d.duration]:f.fx.speeds._default,d.old=d.complete,d.complete=function(a){f.isFunction(d.old)&&d.old.call(this),d.queue!==!1?f.dequeue(this):a!==!1&&f._unmark(this)};return d},easing:{linear:function(a,b,c,d){return c+d*a},swing:function(a,b,c,d){return(-Math.cos(a*Math.PI)/2+.5)*d+c}},timers:[],fx:function(a,b,c){this.options=b,this.elem=a,this.prop=c,b.orig=b.orig||{}}}),f.fx.prototype={update:function(){this.options.step&&this.options.step.call(this.elem,this.now,this),(f.fx.step[this.prop]||f.fx.step._default)(this)},cur:function(){if(this.elem[this.prop]!=null&&(!this.elem.style||this.elem.style[this.prop]==null))return this.elem[this.prop];var a,b=f.css(this.elem,this.prop);return isNaN(a=parseFloat(b))?!b||b==="auto"?0:b:a},custom:function(a,b,c){function h(a){return d.step(a)}var d=this,e=f.fx,g;this.startTime=cn||cp(),this.start=a,this.end=b,this.unit=c||this.unit||(f.cssNumber[this.prop]?"":"px"),this.now=this.start,this.pos=this.state=0,h.elem=this.elem,h()&&f.timers.push(h)&&!cl&&(co?(cl=!0,g=function(){cl&&(co(g),e.tick())},co(g)):cl=setInterval(e.tick,e.interval))},show:function(){this.options.orig[this.prop]=f.style(this.elem,this.prop),this.options.show=!0,this.custom(this.prop==="width"||this.prop==="height"?1:0,this.cur()),f(this.elem).show()},hide:function(){this.options.orig[this.prop]=f.style(this.elem,this.prop),this.options.hide=!0,this.custom(this.cur(),0)},step:function(a){var b=cn||cp(),c=!0,d=this.elem,e=this.options,g,h;if(a||b>=e.duration+this.startTime){this.now=this.end,this.pos=this.state=1,this.update(),e.animatedProperties[this.prop]=!0;for(g in e.animatedProperties)e.animatedProperties[g]!==!0&&(c=!1);if(c){e.overflow!=null&&!f.support.shrinkWrapBlocks&&f.each(["","X","Y"],function(a,b){d.style["overflow"+b]=e.overflow[a]}),e.hide&&f(d).hide();if(e.hide||e.show)for(var i in e.animatedProperties)f.style(d,i,e.orig[i]);e.complete.call(d)}return!1}e.duration==Infinity?this.now=b:(h=b-this.startTime,this.state=h/e.duration,this.pos=f.easing[e.animatedProperties[this.prop]](this.state,h,0,1,e.duration),this.now=this.start+(this.end-this.start)*this.pos),this.update();return!0}},f.extend(f.fx,{tick:function(){for(var a=f.timers,b=0;b<a.length;++b)a[b]()||a.splice(b--,1);a.length||f.fx.stop()},interval:13,stop:function(){clearInterval(cl),cl=null},speeds:{slow:600,fast:200,_default:400},step:{opacity:function(a){f.style(a.elem,"opacity",a.now)},_default:function(a){a.elem.style&&a.elem.style[a.prop]!=null?a.elem.style[a.prop]=(a.prop==="width"||a.prop==="height"?Math.max(0,a.now):a.now)+a.unit:a.elem[a.prop]=a.now}}}),f.expr&&f.expr.filters&&(f.expr.filters.animated=function(a){return f.grep(f.timers,function(b){return a===b.elem}).length});var ct=/^t(?:able|d|h)$/i,cu=/^(?:body|html)$/i;"getBoundingClientRect"in c.documentElement?f.fn.offset=function(a){var b=this[0],c;if(a)return this.each(function(b){f.offset.setOffset(this,a,b)});if(!b||!b.ownerDocument)return null;if(b===b.ownerDocument.body)return f.offset.bodyOffset(b);try{c=b.getBoundingClientRect()}catch(d){}var e=b.ownerDocument,g=e.documentElement;if(!c||!f.contains(g,b))return c?{top:c.top,left:c.left}:{top:0,left:0};var h=e.body,i=cv(e),j=g.clientTop||h.clientTop||0,k=g.clientLeft||h.clientLeft||0,l=i.pageYOffset||f.support.boxModel&&g.scrollTop||h.scrollTop,m=i.pageXOffset||f.support.boxModel&&g.scrollLeft||h.scrollLeft,n=c.top+l-j,o=c.left+m-k;return{top:n,left:o}}:f.fn.offset=function(a){var b=this[0];if(a)return this.each(function(b){f.offset.setOffset(this,a,b)});if(!b||!b.ownerDocument)return null;if(b===b.ownerDocument.body)return f.offset.bodyOffset(b);f.offset.initialize();var c,d=b.offsetParent,e=b,g=b.ownerDocument,h=g.documentElement,i=g.body,j=g.defaultView,k=j?j.getComputedStyle(b,null):b.currentStyle,l=b.offsetTop,m=b.offsetLeft;while((b=b.parentNode)&&b!==i&&b!==h){if(f.offset.supportsFixedPosition&&k.position==="fixed")break;c=j?j.getComputedStyle(b,null):b.currentStyle,l-=b.scrollTop,m-=b.scrollLeft,b===d&&(l+=b.offsetTop,m+=b.offsetLeft,f.offset.doesNotAddBorder&&(!f.offset.doesAddBorderForTableAndCells||!ct.test(b.nodeName))&&(l+=parseFloat(c.borderTopWidth)||0,m+=parseFloat(c.borderLeftWidth)||0),e=d,d=b.offsetParent),f.offset.subtractsBorderForOverflowNotVisible&&c.overflow!=="visible"&&(l+=parseFloat(c.borderTopWidth)||0,m+=parseFloat(c.borderLeftWidth)||0),k=c}if(k.position==="relative"||k.position==="static")l+=i.offsetTop,m+=i.offsetLeft;f.offset.supportsFixedPosition&&k.position==="fixed"&&(l+=Math.max(h.scrollTop,i.scrollTop),m+=Math.max(h.scrollLeft,i.scrollLeft));return{top:l,left:m}},f.offset={initialize:function(){var a=c.body,b=c.createElement("div"),d,e,g,h,i=parseFloat(f.css(a,"marginTop"))||0,j="<div style='position:absolute;top:0;left:0;margin:0;border:5px solid #000;padding:0;width:1px;height:1px;'><div></div></div><table style='position:absolute;top:0;left:0;margin:0;border:5px solid #000;padding:0;width:1px;height:1px;' cellpadding='0' cellspacing='0'><tr><td></td></tr></table>";f.extend(b.style,{position:"absolute",top:0,left:0,margin:0,border:0,width:"1px",height:"1px",visibility:"hidden"}),b.innerHTML=j,a.insertBefore(b,a.firstChild),d=b.firstChild,e=d.firstChild,h=d.nextSibling.firstChild.firstChild,this.doesNotAddBorder=e.offsetTop!==5,this.doesAddBorderForTableAndCells=h.offsetTop===5,e.style.position="fixed",e.style.top="20px",this.supportsFixedPosition=e.offsetTop===20||e.offsetTop===15,e.style.position=e.style.top="",d.style.overflow="hidden",d.style.position="relative",this.subtractsBorderForOverflowNotVisible=e.offsetTop===-5,this.doesNotIncludeMarginInBodyOffset=a.offsetTop!==i,a.removeChild(b),f.offset.initialize=f.noop},bodyOffset:function(a){var b=a.offsetTop,c=a.offsetLeft;f.offset.initialize(),f.offset.doesNotIncludeMarginInBodyOffset&&(b+=parseFloat(f.css(a,"marginTop"))||0,c+=parseFloat(f.css(a,"marginLeft"))||0);return{top:b,left:c}},setOffset:function(a,b,c){var d=f.css(a,"position");d==="static"&&(a.style.position="relative");var e=f(a),g=e.offset(),h=f.css(a,"top"),i=f.css(a,"left"),j=(d==="absolute"||d==="fixed")&&f.inArray("auto",[h,i])>-1,k={},l={},m,n;j?(l=e.position(),m=l.top,n=l.left):(m=parseFloat(h)||0,n=parseFloat(i)||0),f.isFunction(b)&&(b=b.call(a,c,g)),b.top!=null&&(k.top=b.top-g.top+m),b.left!=null&&(k.left=b.left-g.left+n),"using"in b?b.using.call(a,k):e.css(k)}},f.fn.extend({position:function(){if(!this[0])return null;var a=this[0],b=this.offsetParent(),c=this.offset(),d=cu.test(b[0].nodeName)?{top:0,left:0}:b.offset();c.top-=parseFloat(f.css(a,"marginTop"))||0,c.left-=parseFloat(f.css(a,"marginLeft"))||0,d.top+=parseFloat(f.css(b[0],"borderTopWidth"))||0,d.left+=parseFloat(f.css(b[0],"borderLeftWidth"))||0;return{top:c.top-d.top,left:c.left-d.left}},offsetParent:function(){return this.map(function(){var a=this.offsetParent||c.body;while(a&&!cu.test(a.nodeName)&&f.css(a,"position")==="static")a=a.offsetParent;return a})}}),f.each(["Left","Top"],function(a,c){var d="scroll"+c;f.fn[d]=function(c){var e,g;if(c===b){e=this[0];if(!e)return null;g=cv(e);return g?"pageXOffset"in g?g[a?"pageYOffset":"pageXOffset"]:f.support.boxModel&&g.document.documentElement[d]||g.document.body[d]:e[d]}return this.each(function(){g=cv(this),g?g.scrollTo(a?f(g).scrollLeft():c,a?c:f(g).scrollTop()):this[d]=c})}}),f.each(["Height","Width"],function(a,c){var d=c.toLowerCase();f.fn["inner"+c]=function(){var a=this[0];return a&&a.style?parseFloat(f.css(a,d,"padding")):null},f.fn["outer"+c]=function(a){var b=this[0];return b&&b.style?parseFloat(f.css(b,d,a?"margin":"border")):null},f.fn[d]=function(a){var e=this[0];if(!e)return a==null?null:this;if(f.isFunction(a))return this.each(function(b){var c=f(this);c[d](a.call(this,b,c[d]()))});if(f.isWindow(e)){var g=e.document.documentElement["client"+c];return e.document.compatMode==="CSS1Compat"&&g||e.document.body["client"+c]||g}if(e.nodeType===9)return Math.max(e.documentElement["client"+c],e.body["scroll"+c],e.documentElement["scroll"+c],e.body["offset"+c],e.documentElement["offset"+c]);if(a===b){var h=f.css(e,d),i=parseFloat(h);return f.isNaN(i)?h:i}return this.css(d,typeof a=="string"?a:a+"px")}}),a.jQuery=a.$=f})(window);
</script>
<script type="text/javascript">
		jQuery.fn.resizeTextarea = function(min) {
		  min = min || 75;
		  return this.bind("keyup change click focus", function() {
			var sh = this.scrollHeight;
			$(this).height(function(i, h) {
			   return h <= sh && sh != 0 ? sh : min;
			});    
		  }).trigger("keyup");
		};

		$(function() {
		  $('.autoGrow').resizeTextarea();
		});
</script>
	  
<input type="hidden" name="closing_id" id="closing_id" value="<?=$closing_id?>">
<input type="hidden" name="status" id="status" value="<?=$status?>">
	<table width="100%" cellpadding="1" cellspacing="0" style="border:0px solid;padding:0 20 0 20;">
		<tr>
			<td width="15%"></td><td width="15%"></td>
			<td width="15%"></td><td width="11%"></td>
			<td width="11%"></td><td width="11%"></td>
			<td width="11%"></td><td width="11%"></td>
		</tr>
		
		<?if(!$export && $chkPageEdit){?>
		<tr height="20">
			<td align="right" colspan="8"><input type="submit" name="save" value="save"></td>
		</tr>
		<?}?>
		
		<tr height="30">
			<td align="right" colspan="8">
				<table width="100%" cellpadding="3" cellspacing="0" border="0">
						<tr>
							<td width="90%"></td><td width="10%"></td>
						</tr>
					<tr bgcolor="#cacaca" height="30">
						<td class="detail" align="center" ><b>Branch: <?=$branch?><b></td>
						<td class="detail" align="right"  style="border-left:none"><?=$begindate?></td>
					<tr>
				</table>
			</td>
		</tr>
		
		<tr height="10">
			<td colspan="8">&nbsp;</td>
		</tr>
		
		<tr height="20" align="left">
			<td colspan="3" valign="top">
				<table width="100%" cellpadding="3" cellspacing="0" style="border:1px solid;">
					<tr style="display:none;">
						<td width="33%"></td><td width="33%"></td>
						<td width="34%"></td>
					</tr>
					<tr bgcolor="#31a7bc" height="20" align="center">
						<td  style="border-bottom:1px solid;" colspan="3"><b>Daily Sales</b></td>
					</tr>
					<tr bgcolor="#bcf9f8" height="20" align="center">
						<td  style="border-bottom:1px solid;border-right:1px solid;"><b>ReceiptID</b></td>
						<td  style="border-bottom:1px solid;border-right:1px solid;"><b>BookID</b></td>
						<td  style="border-bottom:1px solid;"><b>Amount</b></td>
					</tr>
					
					<?
					$total=0;
					$cnt=0;
					$Srddetail=array();
					$bookList=array();
					$pdsList=array();
					$bookListString="";
					$pdsListString="";
					$bookSrdString="";
					for($i=0;$i<$rs["rows"];$i++){
						if($rs[$i]["tb_name"]=="a_bookinginfo"){
							$bookList[$i]=$rs[$i]["book_id"];
						}else{
							$pdsList[$i]=$rs[$i]["book_id"];	
						}
					if($rs[$i]["set_cancel"]!=1 && $rs[$i]["paid_confirm"]==1){		
						$url = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rs[$i]["book_id"]."";
						$pagename = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rs[$i]["book_id"]:"managePds".$rs[$i]["book_id"];
					$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rs[$i]["bpds_id"]."</a>";	
					$total+=$rs[$i]["sr_total"];
					?>
					<tr height="20" align="right">
						<td style="border-bottom:1px solid;border-right:1px solid;"><?=$rs[$i]["salesreceipt_number"]?></td>
						<td style="border-bottom:1px solid;border-right:1px solid;"><?=$id?></td>
						<td style="border-bottom:1px solid;"><?=($rs[$i]["sr_total"])?number_format($rs[$i]["sr_total"],2,".",","):number_format(0,2,".",",")?></td>
					</tr>
					<?
					 $Srddetail[$i] = $rs[$i]["salesreceipt_id"];
					 }
					}

					if($Srddetail){
				    	$bookSrdString = implode(", ", $Srddetail); 
				 	}
					?>
					<tr bgcolor="#ffb9b9" height="20" align="right">
						<td  colspan="2"><b>Total Sales:</b></td>
						<td><b style='color:#ff0000'><?=number_format($total,2,".",",")?></b></td>
					</tr>
					<input type="hidden" id="total_sale" name="total_sale" value="<?=number_format($total,2,".",",")?>">
				</table>
			<br>
<?
$rs_cancel = $obj->getCancelOrNotfinish("cancel",$branch_id,$begin_date);
		$cbook = "";
		for ($i = 0; $i < $rs_cancel["rows"]; $i++) {
			if ($cbook != '') {
				$cbook .= ", ";
			}	
			$curl = ($rs_cancel[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rs_cancel[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rs_cancel[$i]["book_id"]."";
			$cpagename = ($rs_cancel[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rs_cancel[$i]["book_id"]:"managePds".$rs_cancel[$i]["book_id"];
			$cbook .= "<a href='javascript:;;' onClick=\"newwindow('/appt/$curl','$cpagename')\" class=\"menu\">".$rs_cancel[$i]["bpds_id"]."</a>";
		}
?>		
			<b>Booking Cancel</b>
			<br><br><?=($cbook)?$cbook:"-"?>
<?
$rscbook = $obj->getCancelOrNotfinish("not finish",$branch_id,$begin_date);
		$cbook = "";
		for ($i = 0; $i < $rscbook["rows"]; $i++) {
			if ($cbook != '') {
				$cbook .= ", ";
			}	
			
			$curl = ($rscbook[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rscbook[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rscbook[$i]["book_id"]."";
			$cpagename = ($rscbook[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rscbook[$i]["book_id"]:"managePds".$rscbook[$i]["book_id"];

			$cbook .= "<a href='javascript:;;' onClick=\"newwindow('/appt/$curl','$cpagename')\" class=\"menu\">".$rscbook[$i]["bpds_id"]."</a>";
		}
?>
			<br><br>
			<b>Booking Not Finish</b>
			<br><br><?=($cbook)?$cbook:"-"?>
			<?
			if($bookList){
				$bookListString = implode(", ", $bookList); 
			}
			if($pdsList){
				$pdsListString = implode(", ", $pdsList); 
			}
			if($bookListString&&$pdsListString){
				$condition="book_id in ($bookListString) or pds_id in ($pdsListString)";
			}else if($bookListString){
				$condition="book_id in ($bookListString)";
			}else{
				$condition="pds_id in ($pdsListString)";
			}
			$sql_rcancel="select * from log_c_sr where active=0 and " .
						"($condition) and " .
						"salesreceipt_number<>0 " .
						"order by salesreceipt_number";
			$rs_rcancel=$obj->getResult($sql_rcancel);		
			?>
			<br><br>
			<table width="60%" cellpadding="0" cellspacing="0">
				<tr style="display:none;">
						<td width="30%"></td><td width="30%"></td>
				</tr>
				<tr height="20" align="left">
					<td colspan="2"><b>Sale Receipt Canceled<b></td>
				</tr>
				<tr height="20"  align="left">
					<td><u>ReceiptID</u></td>
					<td><u>BookID</u></td>
				</tr>
				<?if($rs_rcancel){?>
				<?for($i=0;$i<$rs_rcancel["rows"];$i++){
				if($rs_rcancel[$i]["book_id"]){
					$bid=$obj->getIdToText($rs_rcancel[$i]["book_id"],"c_bpds_link","bpds_id","tb_id","tb_name='a_bookinginfo'");
					$url="manage_booking.php?chkpage=1&bookid=".$rs_rcancel[$i]["book_id"]."";
					$pagename="manageBooking".$rs_rcancel[$i]["book_id"];
				}else{
					$bid=$obj->getIdToText($rs_rcancel[$i]["pds_id"],"c_bpds_link","bpds_id","tb_id","tb_name='c_saleproduct'");
					$url="manage_pdforsale.php?pdsid=".$rs_rcancel[$i]["pds_id"]."";
					$pagename="managePds".$rs_rcancel[$i]["pds_id"];
				}
				$bid_link[$i]="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$bid."</a>";		
				?>
				<tr height="20"  align="left">
					<td><?=$rs_rcancel[$i]["salesreceipt_number"]?></td>
					<td><?=$bid_link[$i]?></td>
				</tr>
				<?}?>	
				<?}else{?>
				<tr height="20"  align="left">
					<td>-</td>
					<td>-</td>
				</tr>
				<?}?>
			</table>
			
			<?
			//get gift use
			$sql_gift_use="select * from g_gift where book_id in ($bookListString)";
			//get gift sold
			$sql_gift_sold1="select * from g_gift where id_sold in ($bookListString) and tb_name='a_bookinginfo'";
			$sql_gift_sold2="select * from g_gift where id_sold in ($pdsListString) and tb_name='c_saleproduct'";
			
			if($bookListString&&$pdsListString){
			$sql_gift="($sql_gift_use) union ($sql_gift_sold1) union ($sql_gift_sold2) order by gift_number";
			}else if($bookListString&&!$pdsListString){
			$sql_gift="($sql_gift_use) union ($sql_gift_sold1) order by gift_number";
			}else{
			$sql_gift=$sql_gift_sold2;	
			}
			$rs_gift=$obj->getResult($sql_gift);	
			?>
			<br><br>
			<table width="60%" cellpadding="3" cellspacing="0" style="border:1px solid;">
					<tr style="display:none;">
						<td width="34%"></td><td width="33%"></td>
						</td><td width="33%">
					</tr>
					<tr bgcolor="#31a7bc" height="20" align="center">
						<td  style="border-bottom:1px solid;" colspan="3"><b>Gift Certificate</b></td>
					</tr>
					<tr bgcolor="#bcf9f8" height="20" align="center">
						<td  style="border-bottom:1px solid;border-right:1px solid;"><b>Gift Number</b></td>
						<td  style="border-bottom:1px solid;border-right:1px solid;"><b>BookID Use</b></td>
						<td  style="border-bottom:1px solid;"><b>BookID Sold</b></td>
					</tr>
			<?
			$soldid_link=array();
			for($i=0;$i<$rs_gift["rows"];$i++){
				if($rs_gift[$i]["book_id"]){
				$useid=$obj->getIdToText($rs_gift[$i]["book_id"],"c_bpds_link","bpds_id","tb_id","tb_name='a_bookinginfo' and tb_id in ($bookListString)");	
					if($useid){
					$url="manage_booking.php?chkpage=1&bookid=".$rs_gift[$i]["book_id"]."";
					$pagename="manageBooking".$rs_gift[$i]["book_id"];
					$useid_link[$i]="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$useid."</a>";		
					}
				}
				//undefined index
				if(!isset($rs_gift[$i]["pds_id"])){$rs_gift[$i]["pds_id"]="";}
				
				if($rs_gift[$i]["id_sold"]){
					if($rs_gift[$i]["tb_name"]=="a_bookinginfo"){
					$soldid=$obj->getIdToText($rs_gift[$i]["id_sold"],"c_bpds_link","bpds_id","tb_id","tb_name='a_bookinginfo' and tb_id in ($bookListString)");
						if($soldid){
						$url="manage_booking.php?chkpage=1&bookid=".$rs_gift[$i]["id_sold"]."";
						$pagename="manageBooking".$rs_gift[$i]["id_sold"];
						$soldid_link[$i]="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$soldid."</a>";
						}
					}else{
						$soldid=$obj->getIdToText($rs_gift[$i]["id_sold"],"c_bpds_link","bpds_id","tb_id","tb_name='c_saleproduct' and tb_id in ($pdsListString)");
						if($soldid){
						$url="manage_pdforsale.php?pdsid=".$rs_gift[$i]["id_sold"]."";
						$pagename="managePds".$rs_gift[$i]["pds_id"];
						$soldid_link[$i]="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$soldid."</a>";
						}
					}
					
				}
			if(!isset($soldid_link[$i])){$soldid_link[$i]="";}
			if(!isset($useid_link[$i])){$useid_link[$i]="";}
			?>	
					<tr height="20" align="right">
						<td style="border-right:1px solid;"><?=$rs_gift[$i]["gift_number"]?></td>
						<td style="border-right:1px solid;"><?=($useid_link[$i])?$useid_link[$i]:"-"?></td>
						<td><?=($soldid_link[$i])?$soldid_link[$i]:"-"?></td>
					</tr>
			<?}?>
			</table>
			
			<br><br>
			<table width="60%" cellpadding="3" cellspacing="0" style="border:1px solid;">
					<tr style="display:none;">
						<td width="50%"></td><td width="50%"></td>
					</tr>
					<tr bgcolor="#31a7bc" height="20" align="center">
						<td  style="border-bottom:1px solid;" colspan="2"><b>Membership</b></td>
					</tr>
					<tr bgcolor="#bcf9f8" height="20" align="center">
						<td  style="border-bottom:1px solid;border-right:1px solid;"><b>Member Code</b></td>
						<td  style="border-bottom:1px solid;"><b>BookID</b></td>
					</tr>
			<?for($i=0;$i<$rs["rows"];$i++){
			if(!isset($rs[$i+1]["book_id"])){$rs[$i]["book_id"]=0;}
			if(!isset($rs[$i]["book_id"])){$rs[$i]["book_id"]="";}
			if(!isset($rs[$i+1]["book_id"])){$rs[$i+1]["book_id"]="";}
			if(!isset($rs[$i]["set_cancel"])){$rs[$i]["set_cancel"]="";}
			if(!isset($rs[$i]["a_member_code"])){$rs[$i]["a_member_code"]="";}		
						
			if($rs[$i]["set_cancel"]!=1 && $rs[$i]["a_member_code"] && $rs[$i]["book_id"]!=$rs[$i+1]["book_id"]){		
						$url = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rs[$i]["book_id"]."";
						$pagename = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rs[$i]["book_id"]:"managePds".$rs[$i]["book_id"];
					$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rs[$i]["bpds_id"]."</a>";		
			?>
				<tr height="20" align="right">
						<td style="border-right:1px solid;"><?=$rs[$i]["a_member_code"]?></td>
						<td><?=$id?></td>
				</tr>
			<?}
			}?>
			</td>
			</table>
			<br><br>
			
			<td colspan="5" valign="top" rowspan="2">
				<table width="100%" cellpadding="3" cellspacing="0" style="border:1px solid;">
					<tr style="display:none;">
						<td width="20%"></td><td width="20%"></td>
						<td width="20%"></td><td width="20%"></td>
						<td width="20%"></td>
					</tr>
					<tr bgcolor="#31a7bc" height="20" align="center">
						<td style="border-bottom:1px solid;" colspan="5"><b>Total Sales From Receipts</b></td>
					</tr>
					<tr bgcolor="#bcf9f8" height="20">
						<td style="border-bottom:1px solid;" align="left" colspan="5"><b>Cash:</b></td>
					</tr>
					<tr height="20" align="center">
						<td colspan="2">&nbsp</td>
						<td>Quantity</td>
						<td>Value</td>
						<td></td>
					</tr>
					<?
					$sql_coin = "select * from l_coin order by coin_value desc";
					$rs_coin = $obj->getResult($sql_coin);
					$cash_row=$rs_coin["rows"];
					for($i=0;$i<$cash_row;$i++){
					if(!isset($cash_qty[$i])){$cash_qty[$i]="";}	
					if(!isset($cash_value[$i])){$cash_value[$i]="";}		
					?>
					<tr height="20" align="center">
						<td colspan="2" align="right"><input type="hidden" id="coin_id[<?=$i?>]" name="coin_id[<?=$i?>]" value="<?=$rs_coin[$i]["coin_id"]?>"><input type="hidden" id="coin_value[<?=$i?>]" name="coin_value[<?=$i?>]" value="<?=$rs_coin[$i]["coin_value"]?>"><?=$rs_coin[$i]["coin_name"]?>&nbsp;x&nbsp;</td>
						<td><input <?=($chkPageEdit)?"":"style='background-color:#D0D0D0' readonly='readonly' tabindex='-1'"?> type="text" id="cash_qty[<?=$i?>]" name="cash_qty[<?=$i?>]" value="<?=($cash_qty[$i])?$cash_qty[$i]:""?>" onkeypress="return blockNonNumbers(this, event, true, true);" onchange="SumCash('cash_row',<?=$i?>);"></td>
						<td><input style="background-color:#D0D0D0" readonly="readonly" tabindex="-1" type="text" id="cash_value[<?=$i?>]"name="cash_value[]" value="<?=($cash_value[$i])?number_format($cash_value[$i],2,".",","):""?>"></td>
						<td></td>
					</tr>
					<?}?>
					<input type="hidden" id="cash_row" name="cash_row" value="<?=$cash_row?>">
					<tr height="20" align="center">
						<td style="border-bottom:1px solid;" align="right" colspan="4">Total</td>
						<td style="border-bottom:1px solid;" ><input style="background-color:#D0D0D0;color:#04591a" readonly="readonly" tabindex="-1" type="text" id="total_sumcash" name="total_sumcash" value="<?=($total_sumcash)?number_format($total_sumcash,2,".",","):""?>"></td>
					</tr>
					<tr bgcolor="#bcf9f8" height="20">
						<?if($chkPageEdit){?>
						<td style="border-bottom:1px solid;" align="left" colspan="4"><b>Foreign Currency</b></td>
						<td style="border-bottom:1px solid;" align="right"><img style="cursor:pointer;" src="/images/add.gif" width="11px" height="11px" onclick="addRow('currency_row');">&nbsp;<img style="cursor:pointer;" src="/images/del.gif" width="11px" height="11px" onclick="delRow('currency_row');"></td>
						<?}else{?>
						<td style="border-bottom:1px solid;" align="left" colspan="5"><b>Foreign Currency</b></td>	
						<?}?>
					</tr>
					<tr height="20" align="center">
						<td>Currency</td>
						<td>Rate</td>
						<td>Quantity</td>
						<td>Value</td>
						<td>&nbsp;</td>
					</tr>
					<?for($i=0;$i<$currency_row;$i++){
					if(!isset($currency[$i])){$currency[$i]="";}	
					if(!isset($rate[$i])){$rate[$i]="";}	
					if(!isset($quantity_cur[$i])){$quantity_cur[$i]="";}	
					if(!isset($cur_value[$i])){$cur_value[$i]="";}	
					?>
					<tr height="20" align="center">
						<td><input <?=($chkPageEdit)?"":"style='background-color:#D0D0D0' readonly='readonly' tabindex='-1'"?> type="text" value="<?=$currency[$i]?>" id="currency[<?=$i?>]" name="currency[<?=$i?>]"></td>
						<td><input <?=($chkPageEdit)?"":"style='background-color:#D0D0D0' readonly='readonly' tabindex='-1'"?> type="text" value="<?=($rate[$i])?$rate[$i]:""?>" id="rate[<?=$i?>]" name="rate[<?=$i?>]" onkeypress="return blockNonNumbers(this, event, true, true);" onchange="SumCurrency('currency_row',<?=$i?>);"></td>
						<td><input <?=($chkPageEdit)?"":"style='background-color:#D0D0D0' readonly='readonly' tabindex='-1'"?> type="text" value="<?=($quantity_cur[$i])?$quantity_cur[$i]:""?>" id="quantity_cur[<?=$i?>]" name="quantity_cur[<?=$i?>]" onkeypress="return blockNonNumbers(this, event, true, true);" onchange="SumCurrency('currency_row',<?=$i?>);"></td>
						<td><input style="background-color:#D0D0D0" readonly="readonly" tabindex="-1" type="text" value="<?=($cur_value[$i])?number_format($cur_value[$i],2,".",","):""?>" id="cur_value[<?=$i?>]" name="cur_value[<?=$i?>]" onchange="SumCurrency('currency_row',<?=$i?>);"></td>
						<td>&nbsp;</td>
					</tr>
					<?}?>
					<input type="hidden" id="currency_row" name="currency_row" value="<?=$currency_row?>">
					<tr height="20" align="center">
						<td align="right" style="border-bottom:1px solid;" colspan="4">Total</td>
						<td align="center" style="border-bottom:1px solid;"><input style="background-color:#D0D0D0;color:#04591a" readonly="readonly" tabindex="-1" type="text" value="<?=($total_cur)?number_format($total_cur,2,".",","):""?>" id="total_cur" name="total_cur"></td>
					</tr>
					<tr bgcolor="#eaf7cc" height="20">
						<td style="border-bottom:1px solid;" align="left" colspan="4"><b>Total Cash</b></td>
						<td style="border-bottom:1px solid;" align="center" ><input readonly="readonly" tabindex="-1" style="background-color:#D0D0D0;color:#04591a;" type="text" value="<?=($total_cash)?number_format($total_cash,2,".",","):""?>" id= "total_cash" name="total_cash"></td>
					</tr>
					<tr bgcolor="#ffb9b9" height="20">
						<td style="border-bottom:1px solid;" align="left" colspan="4"><b>Starting Money:</b></td>
						<td style="border-bottom:1px solid;" align="center"><input <?=($chkPageEdit)?"style='color:#ff0000'":"style='color:#ff0000;background-color:#D0D0D0' readonly='readonly' tabindex='-1'"?> type="text" value="<?=($start_money)?number_format($start_money,2,".",","):""?>" id="start_money" name="start_money" onkeypress="return blockNonNumbers(this, event, true, true);" onchange="CashDeposit();"></td>
					</tr>
					<tr bgcolor="#ffb9b9" height="20">
						<td style="border-bottom:1px solid;" align="left" colspan="4"><b>Transfer to PC <?=$branch?>:</b></td>
						<td style="border-bottom:1px solid;" align="center"><input <?=($chkPageEdit)?"style='color:#ff0000'":"style='color:#ff0000;background-color:#D0D0D0' readonly='readonly' tabindex='-1'"?> type="text" value="<?=($tranfer_pc)?number_format($tranfer_pc,2,".",","):""?>" id="tranfer_pc" name="tranfer_pc" onkeypress="return blockNonNumbers(this, event, true, true);" onchange="CashDeposit();"></td>
					</tr>
					<tr bgcolor="#1fac4a" height="20">
						<td style="border-bottom:1px solid;" align="left" colspan="4"><b>Total Cash Deposit to Bank:</b></td>
						<td style="border-bottom:1px solid;" align="center"><input readonly="readonly" tabindex="-1" style="background-color:#D0D0D0;color:#04591a" type="text" value="<?=($total_deposit)?number_format($total_deposit,2,".",","):""?>" id="total_deposit" name="total_deposit"></td>
					</tr>
					<tr height="20">
						<td colspan="5">&nbsp;</td>
					</tr>
					
					<!--Auto Get Other Payment Data-->
					<?
					$sql_ptype="SELECT * FROM l_paytype_category where pay_category_id<>'1'";
					$rs_ptype=$obj->getResult($sql_ptype);
					
					$total_cash_charges=0;
					$sum_pay=0;
					for($i=0;$i<$rs_ptype["rows"];$i++){
					if($rs_ptype[$i]["pay_category_id"]=="2"){
							$sql_data="select l_paytype.pay_id,sum(c_srpayment.pay_total) as pay_total,l_paytype.pay_name,pay_category_name " .
									"from c_srpayment,l_paytype,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='".$rs_ptype[$i]["pay_category_id"]."' " .
									"group by l_paytype.pay_id " .
									"order by l_paytype.pay_name";
							$rs_data=$obj->getResult($sql_data);
					}else{
						$sql_data="select c_srpayment.*,l_paytype.pay_id,l_paytype.pay_name,pay_category_name " .
									"from c_srpayment,l_paytype,l_paytype_category where " .
									"c_srpayment.salesreceipt_id in($bookSrdString) and " .
									"c_srpayment.pay_id=l_paytype.pay_id and " .
									"l_paytype.pay_category_id=l_paytype_category.pay_category_id and " .
									"l_paytype_category.pay_category_id='".$rs_ptype[$i]["pay_category_id"]."'";
						$rs_data=$obj->getResult($sql_data);
					}
					
					if($rs_data){
					?>
						<tr bgcolor="#bcf9f8" height="20">
							<td style="border-top:1px solid;border-bottom:1px solid;" align="left" colspan="5"><b><?=$rs_ptype[$i]["pay_category_name"]?></b></td>
						</tr>
						
						<tr height="20">
							<?if($rs_ptype[$i]["pay_category_id"]!="2"){?>
							<td align="center">Book ID#</td>
							<td align="center">Book Company</td>
							<?}else{?>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<?}?>
							<td align="center">Method of Payment</td>
							<td align="center">Value</td>
							<td>&nbsp;</td>
						</tr>
					<?	
					}
					if(!isset($total_pay[$i])){$total_pay[$i]=0;}
						for($k=0;$k<$rs_data["rows"];$k++){
							if($rs_ptype[$i]["pay_category_id"]!="2"){
								if($rs_data[$k]["book_id"]){
									$bid=$obj->getIdToText($rs_data[$k]["book_id"],"c_bpds_link","bpds_id","tb_id","tb_name='a_bookinginfo'");
								}else{
								$bid=$obj->getIdToText($rs_data[$k]["pds_id"],"c_bpds_link","bpds_id","tb_id","tb_name='c_saleproduct'");
								}
							}
							$pay_type[$k]=$rs_data[$k]["pay_name"];
							$pay_value[$k]=$rs_data[$k]["pay_total"];
							$total_pay[$i]+=$pay_value[$k];
						?>
						<tr height="20">
						<?if($rs_ptype[$i]["pay_category_id"]!="2"){?>
							<td align="center"><input style="background-color:#D0D0D0;" readonly="readonly" tabindex="-1" type="text" value="<?=$bid?>" id="pay_bid[<?=$k?>]" name="pay_bid[<?=$k?>]"></td>
							<?
							$bpid=$obj->getIdToText($rs_data[$k]["book_id"],"a_bookinginfo","c_bp_id","book_id");
							$bpname=$obj->getIdToText($bpid,"al_bookparty","bp_name","bp_id");
							?>
							<td align="center"><input style="background-color:#D0D0D0;" readonly="readonly" tabindex="-1" type="text" value="<?=$bpname?>" id="pay_bpname[<?=$k?>]" name="pay_bpname[<?=$k?>]"></td>
						<?}else{?>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<?}?>
						<td align="center"><input style="background-color:#D0D0D0;" readonly="readonly" tabindex="-1" type="text" value="<?=$pay_type[$k]?>" id="pay_type[<?=$k?>]" name="pay_type[<?=$k?>]"></td>
						<td align="center"><input style="background-color:#D0D0D0;" readonly="readonly" tabindex="-1" type="text" value="<?=number_format($pay_value[$k],2,".",",")?>" id="pay_value[<?=$k?>]" name="pay_value[<?=$k?>]"></td>
						<td>&nbsp;</td>
						</tr>
						<?
						}?>
						<?
					if($rs_data){
					?>
					<tr height="20">
						<td  align="right" colspan="4">Total</td>
						<td align="center"><input readonly="readonly" tabindex="-1" style="background-color:#D0D0D0;color:#04591a" type="text" value="<?=number_format($total_pay[$i],2,".",",")?>" id="total_pay[<?=$i?>]" name="total_pay[<?=$i?>]"></td>
					</tr>
					<?
					$total_cash_charges+=$total_pay[$i];
					$sum_pay+=$total_pay[$i];
					}
					}
					$total_cash_charges+=$total_deposit+$tranfer_pc;
					$out_of_balance=$total_cash_charges-$total;
					?>
					<input type="hidden" id="sum_pay" name="sum_pay" value="<?=$sum_pay?>">
					<tr bgcolor="#1fac4a" height="20">
						<td style="border-bottom:1px solid;border-top:1px solid;" align="left" colspan="4"><b>Total</b></td>
						<td style="border-bottom:1px solid;border-top:1px solid;" align="center"><input readonly="readonly" tabindex="-1" style="background-color:#D0D0D0;color:#04591a;" type="text" value="<?=number_format($total_cash_charges,2,".",",")?>" id="total_cash_charges" name="total_cash_charges"></td>
					</tr>
					<tr bgcolor="#da2929" height="20">
						<td style="border-bottom:1px solid;" align="left" colspan="4"><b>Out of Balance</b></td>
						<td style="border-bottom:1px solid;" align="center"><input readonly="readonly" tabindex="-1" style="background-color:#D0D0D0;color:#ff0000" type="text" value="<?=number_format($out_of_balance,2,".",",")?>" id="out_of_balance" name="out_of_balance"></td>
					</tr>
					
					<tr height="20">
						<td style="border-bottom:1px solid;" align="center" colspan="5"><b style='color:#ff0000'>
						<?if($export==false){?><a href="javascript:;" style="text-decoration:none; color:#ff0000;" onClick="openCusDetail(<?=$begin_date.",".$end_date.",".$branch_id?>)" class="pagelink"><?}?>
						<?=($rs_ttcs[0]["qty"])?number_format($rs_ttcs[0]["qty"],0,".",","):"0"?>
						<?if($export==false){?></a><?}?>
						</b><b> # of customers </b><b style='color:#ff0000'><?=($rs_tthour[0]["total"])?number_format(str_replace(".5",".3",$rs_tthour[0]["total"]),2,".",","):"0.00"?></b><b> # of hours </b></td>
					</tr>
					<!--
					<tr height="20">
						<td colspan="5"><textarea class="autoGrow" <?=($chkPageEdit)?"":"style='background-color:#D0D0D0' readonly='readonly' tabindex='-1'"?> name="giftinfo" rows="5" ><?=($giftinfo)?$giftinfo."\n":"Input Gift(s) Sold Detail here"?></textarea></td>
					</tr>
					-->
					<tr height="20">
						<td colspan="5"><textarea class="autoGrow" <?=($chkPageEdit)?"":"style='background-color:#D0D0D0' readonly='readonly' tabindex='-1'"?> name="commentinfo" rows="5" ><?=($commentinfo)?$commentinfo."\n":"Input Comment Detail here"?></textarea></td>
					</tr>
				</table>
			</td>
		</tr>
		
		<tr height="20">
			    	<td colspan="3" valign="bottom">
			    		<table width="100%" cellpadding="3" cellspacing="0" style="border:1px solid;page-break-before: always;">
							<tr style="display:none;">
								<td width="33%"></td><td width="33%"></td>
								<td width="34%"></td>
							</tr>
							<tr bgcolor="#31a7bc" height="20" align="center">
								<td  style="border-bottom:1px solid;" colspan="3"><b>Closing Receptionist signature</b></td>
							</tr>
							<tr height="20" align="center">
								<td colspan="3"><textarea class="autoGrow" <?=($chkPageEdit)?"":"style='background-color:#D0D0D0' readonly='readonly' tabindex='-1'"?> name="signature" rows="3" ><?=$signature."\n"?></textarea></td>
							</tr>
						</table>
			    	</td>
		</tr>
		
		
		<tr height="20">
			    	<td align="left" colspan="4" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
			    	<td align="right" colspan="4" ><br>
			    		<?
			    		if(!isset($save_by)){$save_by="";}
			    		?>
			    		<?if($save_by){?>
			    		<b>Save by: </b><b style="color:#ff0000"><?=$save_by?></b>&nbsp;<?=$save_date?>&nbsp;<?=$save_time?>
			    		<?}?>
			    	</td>
		</tr>
	<table>
	
<?}else{?>
<?
$header = "\t<tr>\n";
$header .= "\t\t<td width=\"100%\" align=\"center\" colspan=\"11\" ><br>\n";
$header .= "\t\t\t<b>Printed: </b>".$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")."\n";
$header .= "\t\t</td>\n";
$header .= "\t</tr>\n";
$header .= "</table></td>\n";
$header .= "\t</tr>\n";
$header .= "</table>\n";
$header .= "<hr style=\"page-break-before:always;border:0;color:#ffffff;\" />\n";
$header .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td valign=\"top\" style=\"padding:10 20 50 20;\" width=\"100%\" align=\"center\">\n";
$header .= "\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td width=\"8%\"></td><td width=\"8%\"></td>\n";
$header .= "\t\t<td width=\"12%\"></td><td width=\"15%\"></td>\n";
$header .= "\t\t<td width=\"7%\"><td width=\"7%\"></td><td width=\"10%\"></td>\n";
$header .= "\t\t<td width=\"10%\"></td><td width=\"10%\"></td>\n";
$header .= "\t\t<td width=\"10%\"></td><td width=\"8%\"></td>\n";
$header .= "\t\t<td width=\"5%\"></td>\n";
$header .= "\t</tr>\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td class=\"reporth\" width=\"100%\" align=\"center\" colspan=\"11\">\n";
$header .= "\t\t\t<b><p>Spa Management System</p>\n";
$header .= "\t\t\t$reportname</b><br>\n";
$header .= "\t\t\t<p><b style='color:#ff0000'>";
$header .= $dateobj->convertdate($begindate,$sdateformat,$ldateformat);
$header .= ($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat);
$header .= "<b><br><br></p>\n";
$header .= "\t\t</td>\n";
$header .= "\t</tr>\n";
$header .= "\t<tr height=\"32\">\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Receipt No.</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Booking ID</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Customer Name</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Room</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Date</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Time</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Method of Payment</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Total</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Cashier</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Reprinted</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>CMS</b></td>\n";
$header .= "\t</tr>\n";
?>

<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="8%"></td><td width="8%"></td>
		<td width="12%"></td><td width="15%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="10%"></td><td width="10%"></td>
		<td width="10%"></td><td width="8%"></td>
		<td width="5%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="11">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Receipt No.</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Time</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Cashier</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Reprinted</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
	</tr>
<?
$all_total=0;		
$rowcnt=0;
$paytype["type"] = array();
$paytype["value"] = array();
$payvalue = array();
$pay_index = 1;
$Srddetail = array ();
$oldSrd = array ();
$Srdold = array ();
$PayId = array ();
$srcound = 0;
$rsunknown_value=0;

$bookSrdString="";
$bookSrdOld="";
$bookPayId="";
for($i=0; $i<$rs["rows"]; $i++) {
// separate page when export
if($export!="Excel"&&$export&&$rowcnt%$chkrow==0&&$i){
	echo $header;	$rowcnt=0;
}	
if($rs[$i]["set_cancel"]==0 && $rs[$i]["paid_confirm"]==1){
// summary each payment's total
$keyword = ($rs[$i]["pay_id"]!=1)?$rs[$i]["pay_name"]:"Unknown";
$key = array_search($keyword, $paytype["type"]);

if(!$key) {	
		$key = $pay_index;
		$pay_index++;
}


if(!isset($paytype["value"][$key])){$paytype["value"][$key]=0;}

if($rs[$i]["pay_id"]!=1){
$paytype["type"][$key] = $keyword;
$paytype["value"][$key] += $rs[$i]["sr_total"];
}

if($rs[$i]["pay_id"]==1){
	$rsunknown = $keyword; 
	$rsunknown_value += $rs[$i]["sr_total"]; 
}
}
if($rs[$i]["set_cancel"]==0){
// each rows' color
$bgcolor = "#eaeaea"; $class = "even";
if($i%2==0){
	$bgcolor = "#d3d3d3"; $class = "odd";
}
if($rs[$i]["paid_confirm"]==0){
	$bgcolor = "#ffb9b9"; $class = "paidconfirm";
}
}

// define booking id links
$url = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rs[$i]["book_id"]."";
$pagename = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rs[$i]["book_id"]:"managePds".$rs[$i]["book_id"];
if($export!=false){
	$id="<b>".$rs[$i]["bpds_id"]."</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rs[$i]["bpds_id"]."</a>";
}	

// define room in each room 
if($rs[$i]["tb_name"]=="a_bookinginfo"){
	$roomname=array();
	$sqlRoom = "select distinct room_name from d_indivi_info,bl_room " .
				"where d_indivi_info.room_id=bl_room.room_id " .
				"and book_id =".$rs[$i]["book_id"] ;
	$rsRoom = $obj->getResult($sqlRoom);
	for($j=0; $j<$rsRoom["rows"]; $j++){
		$roomname[$j]=$rsRoom[$j]["room_name"];
	}
	sort($roomname);
	$rname = implode(", ",array_filter($roomname));
}else{
	$rname = "-";
}

// define another value
$payname = ($rs[$i]["pay_id"]>1)?$rs[$i]["pay_name"]:"-";
if($rs[$i]["set_cancel"]==0 && $rs[$i]["paid_confirm"]==1){
$all_total+=$rs[$i]["sr_total"];
}

$sr_id = $rs[$i]["salesreceipt_id"];
$cashier =($rs[$i]["u"]==null)?"-": $rs[$i]["u"];
$reprint = ($rs[$i]["reprint_times"]==0)?"-":$rs[$i]["reprint_times"];
$cms = ($rs[$i]["cms"])?"<span style='color:#ff0000'>yes</span>":"<span style='color:#ff0000'>no</span>";

$rowcnt++;		

	$sqlcMp = "select salesreceipt_id from c_srpayment where salesreceipt_id=".$rs[$i]["salesreceipt_id"]."";
		$cmpId = $obj->getResult($sqlcMp);
		
	if($cmpId["rows"]>1){
		$bgcolor = "#eaf7cc"; $class = "multipay";
	}	
	
$style="";
if($rs[$i]["set_cancel"]==1){
		
		$bgcolor = "#eaeaea"; $class = "even";
		if($i%2==0){
			$bgcolor = "#d3d3d3"; $class = "odd";
		}
		//$bgcolor = "#707070"; $class="cancel";
?>			
			<tr style="text-decoration:line-through;" bgcolor="<?=$bgcolor?>" class="<?=$class?>" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='<?php echo $bgcolor; ?>'">   			
					<td class="report" align="center"><?=($rs[$i]["salesreceipt_number"])?$rs[$i]["salesreceipt_number"]:"-"?></td>
					<td class="report" align="center"><?=$id?></td>
					<td class="report"><?=$rs[$i]["customer_name"]?></td>
					<td class="report"><?=$rname?></td>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=substr($rs[$i]["hour_name"],0,5)?></td>
					<td class="report" align="center"><?=$payname?></td>
					<td class="report" align="right"><?=number_format($rs[$i]["sr_total"],2,".",",")?></td>
					<td class="report" align="center"><?=$cashier?></td>
					<td class="report" align="center"><?=$reprint?></td>
					<td class="report" align="center"><?=$cms?></td><s>
 			</tr>
 		
 		<?
}else{
?>
			<tr bgcolor="<?=$bgcolor?>" class="<?=$class?>" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='<?php echo $bgcolor; ?>'">   			
					<td class="report" align="center"><?=($rs[$i]["salesreceipt_number"])?$rs[$i]["salesreceipt_number"]:"-"?></td>
					<td class="report" align="center"><?=$id?></td>
					<td class="report"><?=$rs[$i]["customer_name"]?></td>
					<td class="report"><?=$rname?></td>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=substr($rs[$i]["hour_name"],0,5)?></td>
					<td class="report" align="center"><?=$payname?></td>
					<td class="report" align="right"><?=number_format($rs[$i]["sr_total"],2,".",",")?></td>
					<td class="report" align="center"><?=$cashier?></td>
					<td class="report" align="center"><?=$reprint?></td>
					<td class="report" align="center"><?=$cms?></td>
 			</tr>
<?	
}
if($rs[$i]["set_cancel"]==0 && $rs[$i]["paid_confirm"]){				
 	//Get all salesreceipt_id in report	
 	$Srddetail[$i] = $rs[$i]["salesreceipt_id"];			
 	
 	//$sqlMp = "select salesreceipt_id from c_srpayment where salesreceipt_id=".$rs[$i]["salesreceipt_id"]."";
		//$mpId = $obj->getResult($sqlMp);

		//echo $sqlMp."<br>".$mpId["rows"]."<br>";
	if(!$cmpId){			
 	$sqlSr = "select `c_salesreceipt`.`pay_id` ,`c_salesreceipt`.`sr_total` ,`c_salesreceipt`.`salesreceipt_id` , `l_paytype`.`pay_name` from c_salesreceipt, l_paytype where salesreceipt_id=".$rs[$i]["salesreceipt_id"]." AND `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`";
		//echo $sqlSr; 
		$srId = $obj->getResult($sqlSr);	
		for ($k = 0; $k < $srId["rows"]; $k++) {
				$oldSrd[$srcound]["pay_id"] = $srId[$k]["pay_id"];
				$oldSrd[$srcound]["paytype"] = $srId[$k]["pay_name"];
				$oldSrd[$srcound]["pay_price"] = $srId[$k]["sr_total"];
				$Srdold[$srcound] = $srId[$k]["salesreceipt_id"];
			}	
	$srcound++;
	}
 				
 				} 
 
 	if($Srddetail){
    	$bookSrdString = implode(",", $Srddetail); 
 	}
 	
 	if($Srdold){
    	$bookSrdOld = implode(",", $Srdold); 
 	} 
 	
 	$sqlPd = "SELECT `c_srpayment`.`pay_id` FROM c_srpayment WHERE `c_srpayment`.`salesreceipt_id` IN ( ".$bookSrdString." ) group by `c_srpayment`.`pay_id`";
		$srPd = $obj->getResult($sqlPd);
		for ($k = 0; $k < $srPd["rows"]; $k++) {
				$PayId[$k] = $srPd[$k]["pay_id"];

		}	
 	
 	if($PayId){
    	$bookPayId = implode(",", $PayId);
 	} 
}
 		?>

 				<tr height="20">
 					<td colspan="11" height="20">&nbsp;</td>
 				</tr>
<?
 if($export && (count($paytype["type"])+$rowcnt) > $chkrow){
?>
	<tr>
		<td width="100%" align="center" colspan="11" ><br>
			<b>Printed: <?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?></b>
		</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="8%"></td><td width="8%"></td>
		<td width="12%"></td><td width="15%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="10%"></td><td width="10%"></td>
		<td width="10%"></td><td width="8%"></td>
		<td width="5%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="11">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
<?
 }
 ?>
				<tr height="20">
					<td colspan="3" align="left" height="20" style="padding-right:7px;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td width="70%"></td><td width="30%"></td>
					</tr>
<?
	//Get c_srpayment to show result
		
		$newPd = array ();
		$sqlPd = "SELECT `l_paytype`.`pay_name` , SUM( `c_srpayment`.`pay_total` ) AS total_price
FROM c_srpayment, l_paytype
WHERE `c_srpayment`.`pay_id` = `l_paytype`.`pay_id`
AND `c_srpayment`.`salesreceipt_id` IN ( ".$bookSrdString." ) AND `c_srpayment`.`pay_id` != 1 GROUP BY `l_paytype`.`pay_name`";
		//echo $sqlPd; 
		$srPd = $obj->getResult($sqlPd);


		if($srPd){
		for ($i = 0; $i < $srPd["rows"]; $i++) {
			$newPd[$i]["mp_type"] = $srPd[$i]["pay_name"];
			$newPd[$i]["mp_price"] = $srPd[$i]["total_price"];
		
		for ($k = 0; $k < $srcound; $k++) {
				if($newPd[$i]["mp_type"]==$oldSrd[$k]["paytype"]){
					$newPd[$i]["mp_price"]=$newPd[$i]["mp_price"]+$oldSrd[$k]["pay_price"];	
				}
		}
	
?>
					<tr height="20">
						<td align="right"><b><?=$newPd[$i]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newPd[$i]["mp_price"],2,".",",")?></b></td>
					</tr>
<? 
		}
	}else{
		for ($i = 0; $i < $srPd["rows"]; $i++) {
			$newPd[$i]["mp_type"] = $srPd[$i]["pay_name"];
			$newPd[$i]["mp_price"] = $srPd[$i]["total_price"];
		
		for ($k = 0; $k < $srcound; $k++) {
				if($newPd[$i]["mp_type"]==$oldSrd[$k]["paytype"]){
					$newPd[$i]["mp_price"]=$newPd[$i]["mp_price"]+$oldSrd[$k]["pay_price"];	
				}
		}
?>
								
					
					<tr height="20">
						<td align="right"><b><?=$newPd[$i]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newPd[$i]["mp_price"],2,".",",")?></b></td>
					</tr>
<? 
		}
	}
	$newSr = array ();
	$sqlSr = "select `c_salesreceipt`.pay_id , `l_paytype`.`pay_name`, sum(`c_salesreceipt`.`sr_total`) as sr_total from " .
 			"`c_salesreceipt`, `l_paytype` where `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`" .
 			"and `c_salesreceipt`.`salesreceipt_id` in (".$bookSrdOld.") " .
 			"and `c_salesreceipt`.`pay_id` not in (".$bookPayId.") " .
 			//"and `c_srpayment`.`pay_id` != 1 " .
 			"and `c_salesreceipt`.`pay_id` != 1 " .
 			"group by `c_salesreceipt`.`pay_id`";
 	//echo $sqlSr;
		$srSd = $obj->getResult($sqlSr);
		for ($k = 0; $k < $srSd["rows"]; $k++) {	
					$newSr[$k]["mp_type"]=$srSd[$k]["pay_name"];	
					$newSr[$k]["mp_price"]=$srSd[$k]["sr_total"];
					
?>
					<tr height="20">
						<td align="right"><b><?=$newSr[$k]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newSr[$k]["mp_price"],2,".",",")?></b></td>
					</tr>
<? 
		}
?>	
		<?if($rsunknown_value){?>
					<tr height="20">
						<td align="right"><b><?=$rsunknown?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($rsunknown_value,2,".",",")?></b></td>
					</tr>
		<?}?>	
					</table>
					</td>
					<td colspan="8" align="right" height="20" valign="top" style="padding-right:7px;">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
							<td width="75%"></td><td width="25%"></td>
							</tr>
<?
$sql = "select tax_id,servicescharge from bl_branchinfo where branch_active=1 and branch_id=".$branch_id." order by branch_name limit 0,1";
	$rs = $obj->getResult($sql);
	//echo "<br>$sql";
	$servicescharge = $rs[0]["servicescharge"];
	$taxpercent=$obj->getIdToText($rs[0]["tax_id"],"l_tax","tax_percent","tax_id");
$sql_st = "select * from c_srdetail where salesreceipt_id in (".$bookSrdString.")";
	$rs_st = $obj->getResult($sql_st);

$total_sc=0;
$total_tax=0;
for ($s = 0; $s < $rs_st["rows"]; $s++) {
 	
 $sql_p = "select cl_product_category.pos_neg_value from cl_product,cl_product_category " .
 		"where cl_product.pd_id=".$rs_st[$s]["pd_id"]." " .
 		"and cl_product.pd_category_id=cl_product_category.pd_category_id";	
 $rs_p = $obj->getResult($sql_p);
 	
 if($rs_p[0]["pos_neg_value"]){
 	if($rs_st[$s]["set_sc"]){
 		$total_sc+=(($rs_st[$s]["unit_price"]*$servicescharge)/100)*$rs_st[$s]["qty"];
 		if($rs_st[$s]["set_tax"]){
 		$total_tax+=((($rs_st[$s]["unit_price"]+(($rs_st[$s]["unit_price"]*$servicescharge)/100))*$taxpercent)/100)*$rs_st[$s]["qty"];
 		}
 	}else{
 		if($rs_st[$s]["set_tax"]){
 		$total_tax+=((($rs_st[$s]["unit_price"]+(($rs_st[$s]["unit_price"]*$servicescharge)/100))*$taxpercent)/100)*$rs_st[$s]["qty"];
 		}
 	}
 }else{
 		if($rs_st[$s]["set_sc"]){
 		$total_sc-=(($rs_st[$s]["unit_price"]*$servicescharge)/100)*$rs_st[$s]["qty"];
 		if($rs_st[$s]["set_tax"]){
 		$total_tax-=((($rs_st[$s]["unit_price"]+(($rs_st[$s]["unit_price"]*$servicescharge)/100))*$taxpercent)/100)*$rs_st[$s]["qty"];
 		}
 	}else{
 		if($rs_st[$s]["set_tax"]){
 		$total_tax-=((($rs_st[$s]["unit_price"]+(($rs_st[$s]["unit_price"]*$servicescharge)/100))*$taxpercent)/100)*$rs_st[$s]["qty"];
 		}
 	}
 }
}
?>
							<tr height="20">
								<td align="right"><b>Total Service Charges : &nbsp;&nbsp;</b></td>
								<td align="right"><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($total_sc,2,".",",")?></b></td>
							</tr>
							<tr height="20">
								<td align="right"><b>Total Tax : &nbsp;&nbsp;</b></td>
								<td align="right"><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($total_tax,2,".",",")?></b></td>
							</tr>

							<tr height="20">
								<td align="right"><b>Total Revenue : &nbsp;&nbsp;</b></td>
								<td align="right"><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($all_total,2,".",",")?></b></td>
							</tr>
							<tr height="20">
								<td align="right"><b>Total Customers : &nbsp;&nbsp;</b></td>
								<td align="right"><b>&nbsp;&nbsp;<?=number_format($rs_ttcs[0]["qty"],2,".",",")?></b></td>
							</tr>
							<tr height="20">
								<td align="right"><b>Total Therapist Hour : &nbsp;&nbsp;</b></td>
								<td align="right"><b>&nbsp;&nbsp;<?=number_format(str_replace(".5",".3",$rs_tthour[0]["total"]),2,".",",")?></b></td>
							</tr>
						</table>
					</td>
				</tr>
				
				<tr height="50">
			    	<td width="100%" align="center" colspan="11" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
				</tr>
				
<?
$c_sdate = substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2);
		$c_edate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$c_sql1="select c_bpds_link.*,a_bookinginfo.book_id as book_id " .
				"from a_bookinginfo,c_bpds_link " .
				"where a_bookinginfo.book_id=c_bpds_link.tb_id " .
				"and c_bpds_link.tb_name='a_bookinginfo' " .
				"and a_bookinginfo.b_set_cancel<>1 " .
				"";
		$c_sql1.="and a_bookinginfo.book_id not in ( select c_salesreceipt.book_id from c_salesreceipt where c_salesreceipt.book_id=a_bookinginfo.book_id)";
				//"and IFNULL((select count(c_salesreceipt.salesreceipt_id) from c_salesreceipt where c_salesreceipt.book_id=a_bookinginfo.book_id),0)=0 ";
		if($end_date==false){$c_sql1 .= "and a_bookinginfo.b_appt_date=".$c_sdate." ";}
		else{$c_sql1 .= "and a_bookinginfo.b_appt_date>='".$c_sdate."' and a_bookinginfo.b_appt_date<='".$c_edate."' ";}
		if($branch_id){$c_sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		
		$c_sql2="select c_bpds_link.*,c_saleproduct.pds_id as book_id " .
				"from c_saleproduct,c_bpds_link " .
				"where c_saleproduct.pds_id=c_bpds_link.tb_id " .
				"and c_bpds_link.tb_name='c_saleproduct' " .
				"and c_saleproduct.set_cancel<>1 " .
				"";
		$c_sql2.="and c_saleproduct.pds_id not in ( select c_salesreceipt.pds_id from c_salesreceipt where c_salesreceipt.pds_id=c_saleproduct.pds_id)";
				//"and IFNULL((select count(c_salesreceipt.salesreceipt_id) from c_salesreceipt where c_salesreceipt.pds_id=c_saleproduct.pds_id),0)=0 ";
		if($end_date==false){$c_sql2 .= "and c_saleproduct.pds_date =".$c_sdate." ";}
		else{$c_sql2 .= "and c_saleproduct.pds_date >='".$c_sdate."' and c_saleproduct.pds_date <='".$c_edate."' ";}
		if($branch_id){$c_sql2 .= "and c_saleproduct.branch_id =".$branch_id." ";}
		
		$c_sql = "($c_sql1) union ($c_sql2) order by bpds_id ";	
		
		$rscbook = $obj->getResult($c_sql);
		$cbook = "";
		for ($i = 0; $i < $rscbook["rows"]; $i++) {
			if ($cbook != '') {
				$cbook .= ", ";
			}	
			
			$curl = ($rscbook[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rscbook[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rscbook[$i]["book_id"]."";
			$cpagename = ($rscbook[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rscbook[$i]["book_id"]:"managePds".$rscbook[$i]["book_id"];

				if(!$export){
				$cbook .= "<a href='javascript:;;' onClick=\"newwindow('/appt/$curl','$cpagename')\" class=\"menu\">".$rscbook[$i]["bpds_id"]."</a>";
				}else{
				$cbook .= $rscbook[$i]["bpds_id"];
				}
		}
?>
				<tr height="100">
			    	<td width="100%" align="left" colspan="11" ><br>
			    			<br><b>Booking Not Finish : </b><br>
<div></div><br />
<div></div><p><?=($cbook)?$cbook:"No Booking"?></p><br />
<br /> 
<div></div><br />

			    	</td>
				</tr>
				<tr height="100">
			    	<td width="100%" align="left" colspan="11" ><br>
			    			<br><b>Notation : </b><br>
<div></div><br />
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#EAF7CC;"></div> &nbsp;- Green line, Multi method of payment in sale receipt.<br />
<br /> 
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#FFB9B9;"></div> &nbsp;- Red line, This sale receipt is not paid yet.<br />
<br />
<!--<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#707070;"></div> &nbsp;- Gray line, This Cancel Booking.<br />
<br />-->
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#707070;"></div> &nbsp;- Strikethrough Text, This Cancel Booking.<br />
<br />
			    	</td>
				</tr>
		</table></td>
	</tr>
</table>
<?}?>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>