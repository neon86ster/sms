// JavaScript Document
function high(obj) { temp=obj.style.background;obj.style.background='#cccccc'; }
function low(obj) { obj.style.background=temp; }
function mouseover(obj){temp=obj.style.textDecoration;obj.style.textDecoration='underline';}
function mouseout(obj){obj.style.textDecoration=temp;}

function chkbutton2() {
   alert("Check");  
}
/*
 * method hidden/show menu list
 */
function showhide(id,url) {
	menu = document.getElementById(id);
	var state = menu.style.display; 
	if (state == 'block') {
		state = 'none';
	}
	else {
		state = 'block';
		gotoURL(url);
	}		
	if (document.getElementById) {
		menu.style.display = state;
	}
}  
function gotoURL(myURL){
    parent.mainFrame.location.href=myURL;
}

function chkDeselectEdit(obj){
	var chk =  document.getElementsByName(obj.name);
	if(eval("chk[1].checked")){
		chk[1].checked =false;
	}
}
function chkSelectView(obj){
	var chk =  document.getElementsByName(obj.name);
	if(!eval("chk[0].checked")){
		chk[0].checked =true;
	}
}
function chkUserSelectGroup(obj){
	var chk =  document.getElementsByName(obj.name);
	if(!eval("chk[1].checked")){
		chk[1].checked =true;
	}
	if(!eval("chk[0].checked")){
		chk[1].checked =false;
		chk[2].checked =false;
	}
}
function chkUserSelectView(obj){
	var chk =  document.getElementsByName(obj.name);
	if(!eval("chk[0].checked")){
		chk[0].checked =true;
	}
	if(!eval("chk[1].checked")){
		chk[0].checked =false;
		chk[2].checked =false;
	}
}
function chkUserSelectEdit(obj){
	var chk = document.getElementsByName(obj.name);
	if(!eval("chk[0].checked")||!eval("chk[1].checked")){
		chk[0].checked =true;
		chk[1].checked =true;
	}
}
function showResvBookingLimit(obj){
	var chk = document.getElementsByName(obj.name);
	var rsvndiv = document.getElementById("resvlimit");
	var resveditlimit = document.getElementById("resveditlimit");
	if(!eval("chk[0].checked")){
		rsvndiv.style.display='none';
	}else{
		rsvndiv.style.display='block';
		if(!eval("chk[2].checked"))
			resveditlimit.style.display = 'none';
		else
			resveditlimit.style.display = 'inline';
	}
}

function toggleToolDiv(){
	var tooldiv = document.getElementById('tooldiv');
	var toggletooltxt = document.getElementById("toggletooltxt");
	var toggletoolimg = document.getElementById("toggletoolimg");
	tooldiv.style.display = (tooldiv.style.display == "none" ) ? "block" : "none";
	toggletoolimg.src = (tooldiv.style.display == "none" ) ? "../../images/search_show.gif" : "../../images/search_hide.gif";
	toggletooltxt.innerHTML = (tooldiv.style.display == "none" ) ? "Show Search" : "Hide Search";

}
function showHide(div){
	var saleDiv = document.getElementById('saleDiv');
	var treatDiv = document.getElementById('treatDiv');
	
	document.getElementById("tabtwo").className = "";
	document.getElementById("tabthree").className = "";
	//alert(div);
	if(div=="saleDiv"){
		document.getElementById("tabtwo").className = "current";
		saleDiv.style.display = "block";
		treatDiv.style.display = "none";
	}
	if(div=="treatDiv"){
		document.getElementById("tabthree").className = "current";
		saleDiv.style.display =  "none";
		treatDiv.style.display = "block";
	}
}
function showAllGift(allGift){
	var all = document.getElementById("allGift");
	all.value = allGift;
	var frm = document.getElementById("giftinfo");
	frm.submit();
}
function showSortGift(whereGift,pageGift,gifttype){
	var where = document.getElementById("order");
	var page = document.getElementById("page");
	var type = document.getElementById("gifttype_id");
	where.value = whereGift;
	page.value = pageGift;
	type.value = gifttype;
	var frm = document.getElementById("giftinfo");
	frm.submit();
}
function showAllMembers(allMembers){
	var all = document.getElementById("allMembers");
	all.value = allMembers;
	var frm = document.getElementById("membershipinfo");
	frm.submit();
}
function showSortMembers(whereMembers,pageMembers){
	var where = document.getElementById("order");
	var page = document.getElementById("page");
	where.value = whereMembers;
	page.value = pageMembers;
	var frm = document.getElementById("membershipinfo");
	frm.submit();
}
function editMember(id){
	var phone;
	if(document.getElementById('phone'+id).value>0){
		phone=document.getElementById('phone'+id).value;
	}else{
		phone=document.getElementById('mobile'+id).value;
	}
	window.opener.document.getElementById('cs[bpphone]').value=phone;
	window.opener.document.getElementById('cs[phone]').value=phone;
	var name="";
	name = document.getElementById('fname'+id).value+" "+document.getElementById('mname'+id).value+" "+document.getElementById('lname'+id).value;
	window.opener.document.getElementById('cs[name]').value=name;
	window.opener.document.getElementById('cs[bpname]').value=name;
	window.opener.document.getElementById('cs[memid]').value=document.getElementById('member_code'+id).value;
	if(document.getElementById('member_code'+id).value>0){window.opener.document.getElementById('b_mhistory').value="History";window.opener.document.getElementById('b_mhistory').title="Member History";}
	else{window.opener.document.getElementById('b_mhistory').value="Directory";window.opener.document.getElementById('b_mhistory').title="Member Directory";}
	window.opener.document.getElementById('tw[0][csageinroom]').value="";
	window.opener.document.getElementById('tw[0][csnameinroom]').value=name;
	window.opener.document.getElementById('tw[0][csphoneinroom]').value=phone;
	window.opener.document.getElementById('tw[0][csemail]').value=document.getElementById('email'+id).value;
	window.opener.document.getElementById('tw[0][national]').options[0].selected = true;
	for (var j = 0; j < window.opener.document.getElementById('tw[0][national]').length; j++) {
			if (window.opener.document.getElementById('tw[0][national]').options[j].value == document.getElementById('cs_nationality'+id).value) {
				window.opener.document.getElementById('tw[0][national]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw[0][national]').options[j].selected = false;
		 	}
	}
	window.opener.document.getElementById('tw[0][sex]').options[0].selected = true;
	for (var j = 0; j < window.opener.document.getElementById('tw[0][sex]').length; j++) {
			if (window.opener.document.getElementById('tw[0][sex]').options[j].value == document.getElementById('cs_sex'+id).value) {
				window.opener.document.getElementById('tw[0][sex]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw[0][sex]').options[j].selected = false;
		 	}
	}
	window.opener.document.getElementById('tw[0][resident]').options[0].selected = true;
		//window.opener.document.getElementById("cs[searchchk]").value++;
	//window.opener.document.getElementById("csphoneSearch").value="History";
	window.close();
}
function editmkCode(id){
	for (var j = 0; j < window.opener.document.getElementById('cs[inspection]').length; j++) {
			if (window.opener.document.getElementById('cs[inspection]').options[j].value == id) {
				window.opener.document.getElementById('cs[inspection]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('cs[inspection]').options[j].selected = false;
		 	}
	}
	window.close();
}
function selectCategory(){
document.getElementById("search").value="";
document.getElementById('membershipinfo').submit();
}
function selectGifttype(){
document.getElementById("search").value="";
document.getElementById('giftinfo').submit();
}
function selectboxSearch(url) {
	var d='';
	var showInactive='';
	var order='';
	var page='&page=1';
	
	if(document.getElementById('show_inactive')!=null&&document.getElementById('show_inactive').checked == true){
		showInactive= '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if (document.getElementById('show_detail')!=null&&document.getElementById('show_detail').checked){
		//alert(document.getElementById('show_detail').value);
		d += '&show_detail='+document.getElementById('show_detail').value;
	}
	if(document.getElementById('category_id')!=null){
		showInactive= '&category_id='+document.getElementById('category_id').value;
	}
	if (document.getElementById('bp_category_id')!=null){
		d += '&bp_category_id='+document.getElementById('bp_category_id').value;
	}
	if(document.getElementById('order')!=null && document.getElementById('order').value != ''){
	//alert(document.getElementById('search').value);
		order = '&order='+document.getElementById('order').value;
	}
	if (document.getElementById('search')!=null){
		document.getElementById('search').value="";
	}
	/*if(document.getElementById('page')!=null && document.getElementById('page').value != ''){
	//alert(document.getElementById('search').value);
		page = '&page='+document.getElementById('page').value;
	}*/
	document.getElementById('page').value=1;
	//alert(d);
document.getElementById('mkcode').submit();
}
function showInactive(url) {
	var d='';
	if(document.getElementById('branch_id')!=null){
		d += '&branch_id='+document.getElementById('branch_id').value;
	}
	if(document.getElementById('emp_department_id')!=null){
			d += '&emp_department_id='+document.getElementById('emp_department_id').value;
	} 
	if(document.getElementById('category_id')!=null){
		showInactive= '&category_id='+document.getElementById('category_id').value;
	}
	if(document.getElementById('city_id')!=null){
		d += '&city_id='+document.getElementById('city_id').value;
	}  
	if(document.getElementById('show_inactive').checked == true){
		d += '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if (document.getElementById('pd_category_id')!=null){
		d += '&pd_category_id='+document.getElementById('pd_category_id').value;
	}
	if (document.getElementById('show_detail')!=null&&document.getElementById('show_detail').checked){
		//alert(document.getElementById('show_detail').value);
		d += '&show_detail='+document.getElementById('show_detail').value;
	}
	if (document.getElementById('bp_category_id')!=null){
		d += '&bp_category_id='+document.getElementById('bp_category_id').value;
	}
document.getElementById('mkcode').submit();
}
function showSortmkcode(where,pagev,category){
	var order = document.getElementById("order");
	var page = document.getElementById("page");
	var type = document.getElementById("category_id");
	order.value = where;
	page.value = pagev;
	type.value = category;
	var frm = document.getElementById("mkcode");
	frm.submit();
}
function showAll(allCode){
	var all = document.getElementById("allCode");
	all.value = allCode;
	var frm = document.getElementById("mkcode");
	frm.submit();
}