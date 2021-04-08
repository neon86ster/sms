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
		gotoUrl(url);
	}		
	if (document.getElementById) {
		menu.style.display = state;
	}
} 
/*
 * By Ruk 21-04-2009
 * Function getOwnUserId
 * return php query string of owner user id
 */  
 function getOwnUserId(myURL){
 	try{
		///// Get onwer user id who is owner left menu. And add to link.
		var ownUserId=window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("ownUserId").value;
	}catch(e){
		var ownUserId="";
	}
	if(myURL.indexOf('?')>=0 || myURL ==""){
		ownUserId="&ownUserId="+ownUserId;	
	}else{
		ownUserId="?ownUserId="+ownUserId;
	}
 	return ownUserId;
 }
function gotoURL(myURL){
	try{
		parent.mainFrame.location.href=myURL+this.getOwnUserId(myURL);
	}catch(e){
		parent.location.href="../../home.php";
	}
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
		chk[1].checked =false;
	}
}
function chkUserSelectEdit(obj){
	var chk = document.getElementsByName(obj.name);
	if(eval("chk[1].checked")){
		chk[0].checked =true;
	}
}
function showResvBookingLimit(obj){
	var chk = document.getElementsByName(obj.name);
	var rsvndiv = document.getElementById("resvlimit");
	var rsvnviewchk = document.getElementById("rsvnviewchk");
	var resveditlimit = document.getElementById("resveditlimit");
	var rsvneditchk = document.getElementById("rsvneditchk");
	//alert(eval("chk[0].checked")+" "+!eval("chk[1].checked"));
	if(!eval("chk[0].checked")){
		rsvnviewchk.value--;
		if(eval("chk[1].checked")){rsvneditchk.value--;}
	}else{
		rsvnviewchk.value++;
	}
	if(rsvneditchk.value<=0){
		resveditlimit.style.display='none';
	}else{
		resveditlimit.style.display='inline';
	}
	if(rsvnviewchk.value<=0){
		rsvndiv.style.display='none';
	}else{
		rsvndiv.style.display='block';
	}
}
function showResvBookingEditLimit(obj){
	var chk = document.getElementsByName(obj.name);
	var rsvndiv = document.getElementById("resvlimit");
	var rsvnviewchk = document.getElementById("rsvnviewchk");
	var resveditlimit = document.getElementById("resveditlimit");
	var rsvneditchk = document.getElementById("rsvneditchk");
	//alert(eval("chk[0].checked")+" "+!eval("chk[1].checked"));
	if(!eval("chk[1].checked")){
			rsvneditchk.value--;
	}else{
		if(!eval("chk[0].checked")){
			rsvnviewchk.value++;
		}
			rsvneditchk.value++;
	}
	if(rsvneditchk.value<=0){
		resveditlimit.style.display='none';
	}else{
		resveditlimit.style.display='inline';
	}
	if(rsvnviewchk.value<=0){
		rsvndiv.style.display='none';
	}else{
		rsvndiv.style.display='block';
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
