// JavaScript Document
function high(obj) { temp=obj.style.background;obj.style.background='#cccccc'; }
function low(obj) { obj.style.background=temp; }
function mouseover(obj){temp=obj.style.textDecoration;obj.style.textDecoration='underline';}
function mouseout(obj){obj.style.textDecoration=temp;}

function chkbutton2() {
   alert("Check");  
}

function resizeOuterTo(w,h) {
 if (parseInt(navigator.appVersion)>3) {
   if (navigator.appName=="Netscape") {
    top.outerWidth=w;
    top.outerHeight=h;
   }
   else top.resizeTo(w,h);
 }
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
function showHideCheck(div,id){
	var showDiv = document.getElementById(div);
	//alert(document.getElementById(id).checked);
	if(document.getElementById(id).checked){
		showDiv.style.display = "block";
	}else{
		showDiv.style.display = "none";
	}
}
function showhidegift(div,chk){
	var showDiv = document.getElementById(div);
	
	if(document.getElementById('giftChk').value==0){
		showDiv.style.display = "block";
		document.getElementById('giftChk').value=1;
	}else{
		showDiv.style.display = "none";
		document.getElementById('giftChk').value=0;
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
	toggletoolimg.src = (tooldiv.style.display == "none" ) ? "../images/search_show.gif" : "../images/search_hide.gif";
	toggletooltxt.innerHTML = (tooldiv.style.display == "none" ) ? "Show Search" : "Hide Search";

}
function showCC(obj){
	var chk = document.getElementsByName(obj.name);
	var ccdiv = document.getElementById("cc");
	var cctd = document.getElementById("cctd");
	if(!obj.checked){
		ccdiv.style.display='none';
		cctd.style.height=0;
	}else{
		ccdiv.style.display='block';
		cctd.style.height=25;
	}
}
function showTRF(obj){
	var chk = document.getElementsByName(obj.name);
	var trfdiv = document.getElementById("trf");
	var trftd = document.getElementById("trftd");
	if(!obj.checked){
		trfdiv.style.display='none';
		trftd.style.height=0;
	}else{
		trfdiv.style.display='block';
		trftd.style.height=25;
	}
}
function showGIFT(obj){
	var chk = document.getElementsByName(obj.name);
	var giftdiv = document.getElementById("gift");
	var gifttd = document.getElementById("gifttd");
	if(!obj.checked){
		giftdiv.style.display='none';
		gifttd.style.height=0;
	}else{
		giftdiv.style.display='block';
		gifttd.style.height=25;
	}
}
function checkNum(obj){
	if(isNaN(obj.value)) {
			obj.value = 1;
			alert("Please check Total people of booking use number only!!");
	}else if(obj.value==0) {
			obj.value = 1;
			alert("Please check Total people of booking will more than 0!!");
	}else{
		if(document.getElementById("status").value=="add")
			document.getElementById("chkttpp").value++;
	}
	
}
function checkqtyNum(obj){
	if(isNaN(obj.value)) {
			obj.value = 1;
			alert("Please check Qty of Sale Receipt use number only!!");
			return false;
	}
}
function checkpriceNum(obj){
	if(isNaN(obj.value)) {
			obj.value = 0;
			alert("Please check Price of Sale Receipt use number only!!");
			return false;
	}
}
function checkpayNum(obj){

		if(isNaN(obj.value)) {
				obj.value = 0;
				alert("Please check Payment Price use number only!!");
				return false;
		}
		 if(obj.value==""){
			 alert("Please check Payment Price use number only!!");
			 obj.value = 0;
			 return false;
		 }

		 for(var i =0;i<=obj.value.length-1;i++){
			 if(obj.value.charAt(i)==" " || obj.value.charAt(0)=="-"){
				 alert("Please check Payment Price use number only!!");
				 obj.value = 0;
				 return false;
			 }
		 }
		 

}
/*
 * function for check booking party phone number in customer information tab on manage booking page. 
 * @param - input object
 * @modified at 16-Apr-2009 : check phonenumber must be format : + countrycode citycode number
 * @modified at 01-Oct-2009 : change condition for check phone number format prevent input like ++1234568
 */
function checkBPphone(inputobject,oldbpphone){	
	var inputvalue = inputobject.value;
   
	if(pattern.test(inputvalue) && inputvalue != "" && inputvalue == oldbpphone){
		document.getElementById("bpCheck").value="History";
	}else{
		document.getElementById("bpCheck").value="Check";
	}
}
/*
 * function for check customer's phone number in customer information tab on manage booking page. 
 * @param - input object
 * @modified at 16-Apr-2009 : check phonenumber must be format : + countrycode citycode number
 * @modified at 01-Oct-2009 : change condition for check phone number format prevent input like ++1234568
 */
function checkCSphone(inputobject){
	var inputvalue = inputobject.value;
   
}
/*
 * function for check customer's phone number search in treatment information tab on manage booking page. 
 * @param - input object
 * @modified at 16-Apr-2009 : check phonenumber must be format : + countrycode citycode number
 * @modified at 01-Oct-2009 : change condition for check phone number format and numerical prevent input like ++1234568
 */
function checktwphoneSearch(inputobject){
	var inputvalue = inputobject.value;
    var pattern=/^\+[1-9]{1}\d{0,}$/;
    var pattern2=/^\d{0,}$/;
    // if phone number not blank
    // check this input value match with phone number pattern or not
    // and check this input value is number or not 
    if(inputvalue!="" && (!pattern.test(inputvalue) && !pattern2.test(inputvalue))){
    	// if input value not mach with phone number pattern and is not numerical           
    	alert("Please check on Phone Number!!");  
		inputobject.value = "";
    }
    
	if((pattern.test(inputvalue)||pattern2.test(inputvalue))&&inputvalue!=""){
		document.getElementById("twphoneSearch").value="History";
	}else{
		document.getElementById("twphoneSearch").value="Search";
	}
}
/*
 * function for check individual customer's phone number in treatment information tab on manage booking page. 
 * @param - input object
 * @modified at 16-Apr-2009 : check phonenumber must be format : + countrycode citycode number
 * @modified at 01-Oct-2009 : change condition for check phone number format prevent input like ++1234568
 */
function checkTWphone(inputobject){
	var inputvalue = inputobject.value;
    var pattern=/^\+[1-9]{1}\d{0,}$/;
    if(!pattern.test(inputvalue)&&inputvalue!=""){           
    	alert("Please input Phone number with this format : \n    + countrycode citycode number \n e.g., +6653, for citycode take off zero.");  
		inputobject.value = "";
    }
}
function addSr(chk) {
	var srcount = document.getElementById("srcount");
		if(chk != 1) {
			if(srcount.value > 1)
				srcount.value--;		
		}
		else {
			srcount.value++;	
		}
}
function addSrd(i,j,chk) {
	var srdcount = Array();
	srdcount[i] = document.getElementById("srdcount["+i+"]");
	var uprices = document.getElementById("srd["+i+"]["+j+"][unit_price]");
	
	uprices.value=0;
	//alert(srdcount[count].value);
	if(chk!=1) {
		if(srdcount[i].value > 1)
			srdcount[i].value--;
	}
	else {
		if(j == srdcount[i].value-1) {
			srdcount[i].value++;
		}
	}
}
function addTh(i) {
	var thcount = Array();
	thcount[i] = document.getElementById("thcount["+i+"]");
	if(thcount[i].value==5){
		alert("You can't insert therapist more than 5 therapists!!");
		return false;}
	else {
		thcount[i].value++;
	}
}
function addMsg(i) {
	var msgcount = Array();
	msgcount[i] = document.getElementById("msgcount["+i+"]");
	if(msgcount[i].value==5){
		alert("You can't insert massage more than 5 massages!!");
		return false;}
	else {
		msgcount[i].value++;
	}
}
function chkPage(chk){
	var chkpage = document.getElementById("chkpage");
	chkpage.value = chk;
	var custinfo = document.getElementById("custinfo");
	var therainfo = document.getElementById("therainfo");
	var srinfo = document.getElementById("srinfo");
		document.getElementById("tabone").className = "";
		document.getElementById("tabtwo").className = "";
		document.getElementById("tabthree").className = "";
	if(chk==1){
		document.getElementById("tabone").className = "current";
		custinfo.style.display='block';
		therainfo.style.display='none';
		srinfo.style.display='none';
	}else if(chk==2){
		document.getElementById("tabtwo").className = "current";
		custinfo.style.display='none';
		therainfo.style.display='block';
		srinfo.style.display='none';
	}else if(chk==3){
		document.getElementById("tabthree").className = "current";
		//start: add to not submit
		custinfo.style.display='none';
		therainfo.style.display='none';
		srinfo.style.display='block';
		//end
		//document.getElementById("appt").submit();
	}
}
function transferwindow(book_id,time_pu,time_tb){ 
	var url;
	var returnvalue = 0;
	var branch = document.getElementById("cs[branch]");
	var cs_date = document.getElementById("cs[apptdate]");
	var dr_pu = document.getElementById("trf[dr_pu]");	
	var dr_tb = document.getElementById("trf[dr_tb]");	
	var p_pu = document.getElementById("trf[p_pu]");	
	var p_tb = document.getElementById("trf[p_tb]");		
	var cs_name = document.getElementById("cs[name]");
	var cs_hotel = document.getElementById("cs[hotel]");
	var cs_roomnum = document.getElementById("cs[roomnum]");
	var cs_ttpp = document.getElementById("cs[ttpp]");
	var cs_rs = document.getElementById("cs[rs]");
	var cs_cms = document.getElementById("cs[cms]");
	
	if(cs_cms.checked) {
		var cs_bcom = document.getElementById("cs[bcompany]");
		var cs_bp = document.getElementById("cs[bpname]");
		
		cs_bcom = cs_bcom.value;
		cs_bp = cs_bp.value;
	}
	else {
		cs_bcom = returnvalue;
		cs_bp = returnvalue;	
	}
	//alert(test['td']);
	
	cs_name = cs_name.value;
	if(!cs_name) {
		alert("No customer name, Insert customer name please !!");
		return;
	}
	
	
	branch = branch.value;
	cs_date = cs_date.value;
	dr_tb = dr_tb.value;
	
	dr_pu = dr_pu.value;
	if(dr_pu<=1&&dr_tb<=1) {
		alert("Please select driver for pick-up or take-back!!");
		return;
	}
	
	p_pu = p_pu.value;
	p_tb = p_tb.value;
	
	cs_ttpp = cs_ttpp.value;
	cs_roomnum = cs_roomnum.value;
	if(!cs_roomnum)
		cs_roomnum = returnvalue;
		
	cs_hotel = cs_hotel.value;
	cs_rs = cs_rs.value;
	
	
	//alert(time_pu);
	
		url = "transfer_slip.php?book_id="+book_id+"&branch="+branch+"&dr_pu="+dr_pu+"&dr_tb="+dr_tb+"&dr_pu_place="+p_pu+"&dr_tb_place="+p_tb+"&dr_pu_time="+time_pu+"&dr_tb_time="+time_tb+"&cs_name="+cs_name+"&cs_hotel="+cs_hotel+"&cs_roomnum="+cs_roomnum+"&cs_ttpp="+cs_ttpp+"&cs_rs="+cs_rs+"&cs_bcom="+cs_bcom+"&cs_bp="+cs_bp+"&cs_date="+cs_date+"";
	
	
	window.open(url,'trfunit','location=0,toolbar=0,directoris=0,status=0,menubar=1,scrollbars=0,resizable=0,width=250,height=600,left=500,top=100'); 

}

function check_twvalue(i,thcnt) {
	var tw_room = document.getElementById("tw["+i+"][room]");
	var tw_sex = document.getElementById("tw["+i+"][sex]");	
	var tw_national = document.getElementById("tw["+i+"][national]");
	//alert("tw1="+tw_name.value+",tw2="+tw_sd_name.value);
	//alert(tw_fourhand.checked);
	thnum=0;
	for(c=0;c<thcnt;c++) {
		var tw_name = document.getElementById("tw["+i+"]["+c+"][name]");
		if(tw_name.value!=49){
			thnum++;
		}
	}
	
	if(thnum == 0){
		alert("Select therapist name please !!");
		return false;
	}
	
	if(tw_room.value <= 1) {
		alert("Select therapist room please !!");
		return false;
	}
	
	if(tw_sex.value < 1) {
		alert("Select sex of customer please !!");
		return false;
	}
	
	if(tw_national.value <= 1) {
	 	alert("Select nationality of customer please !!");
		return false;
	}
	
	return true;
}

function therapistwindow(book_id,i,thcnt,msgcnt,target) {
	var t;
	var cs_name = document.getElementById("tw["+i+"][csnameinroom]");	
	cs_name = cs_name.value;
	
	if(!cs_name) {
		alert("No customer name, Insert customer name please !!");
		return false;
	}
		
	var branch = document.getElementById("cs[branch]");
	branch = branch.value;
	var apptdate = document.getElementById("cs[apptdate]");
	apptdate = apptdate.value;
	var appttime = document.getElementById("cs[appttime]");
	appttime = appttime.value;
	var tw_room = document.getElementById("tw["+i+"][room]");
	tw_room = tw_room.value;
	var tw_sex = document.getElementById("tw["+i+"][sex]");
	tw_sex = tw_sex.value;
	var tw_national = document.getElementById("tw["+i+"][national]");
	tw_national = tw_national.value;
	
	var tw_name='';
	var tw_hour='';
	var tw_start='';
	var tw_end='';
  	var nParams = new Array();
  	var hParams = new Array();
  	var sParams = new Array();
	var eParams = new Array();
	var total=0;
	for(c=0;c<thcnt;c++) {
		var th_name = document.getElementById("tw["+i+"]["+c+"][name]");
		var th_hour = document.getElementById("tw["+i+"]["+c+"][hour]");
		var thour = document.getElementById("tw["+i+"][tthour]").value;
		var th_start = document.getElementById("tw["+i+"]["+c+"][start_id]");
		var th_end = document.getElementById("tw["+i+"]["+c+"][end_id]");
		if(th_name.value!=1){
			var pParam = "name["+c+"]=";
			var rParam = "hour["+c+"]=";
			var tParam = "twstart["+c+"]=";
			var teParam = "twend["+c+"]=";
			pParam+=encodeURIComponent( th_name.value );
			nParams.push( pParam );
			rParam+=encodeURIComponent( th_hour.value );
			hParams.push( rParam );
			tParam+=encodeURIComponent( th_start.value );
			sParams.push( tParam );
			teParam+=encodeURIComponent( th_end.value );
			eParams.push( teParam );
			total++;
		}
	}
	if(total>0){tw_name+="&";tw_hour+="&";tw_start+="&";tw_end+="&";}
	tw_name+=nParams.join( "&" );
	tw_hour+=hParams.join( "&" );
	tw_start+=sParams.join( "&" );
	tw_end+=eParams.join( "&" );
	
	var tw_msg='';
  	var mParams = new Array();
	var total=0;
	for(c=0;c<msgcnt;c++) {
		var th_msg = document.getElementById("tw["+i+"]["+c+"][msg]");
		if(th_msg.value!=1){
			var sParam = "msg["+c+"]=";
			sParam+=encodeURIComponent( th_msg.value );
			mParams.push( sParam );
			total++;
		}
	}
	if(total>0){tw_msg+="&";}
	tw_msg+=mParams.join( "&" );
	
	var tw_stream = document.getElementById("tw["+i+"][stream]");
	if(tw_stream.checked)
		tw_stream=1;
	else
		tw_stream=0;
		
	var tw_package = document.getElementById("tw["+i+"][package]").value;
//	tw_package = tw_package.value;
	var tw_bath = document.getElementById("tw["+i+"][bath]").value;
//	tw_bath = tw_bath.value;
	var tw_facial = document.getElementById("tw["+i+"][facial]").value;
//	tw_facial = tw_facial.value;
	var tw_wrap = document.getElementById("tw["+i+"][wrap]").value;
//	tw_wrap = tw_wrap.value;
	var tw_strength = document.getElementById("tw["+i+"][strength]").value;
//	tw_strength = tw_strength.value;
	var tw_scrub = document.getElementById("tw["+i+"][scrub]").value;
//	tw_scrub = tw_scrub.value;
	var tw_comment = document.getElementById("tw["+i+"][comments]").value;
//	tw_comment = tw_comment.value;
	
	t = "therapist_ws.php?book_id="+book_id+"&branch="+branch+"&cs_name="+cs_name+"&room="+tw_room+tw_hour+tw_name+tw_start+tw_end+"&sex="+tw_sex+"&national="+tw_national+"&stream="+tw_stream+"&package="+tw_package+"&bath="+tw_bath+"&facial="+tw_facial+"&wrap="+tw_wrap+tw_msg+"&strength="+tw_strength+"&scrub="+tw_scrub+"&comment="+tw_comment+"&appttime="+appttime+"&thour="+thour+"&apptdate="+apptdate+"";	
	
	//alert(t);
	window.open(t,target,'location=0,toolbar=0,directoris=0,status=0,menubar=1,scrollbars=0,resizable=0,width=245,height=770,left=500,top=50');
	
	
}
function saleReceiptWindow(book_id,sr_id,status){
	if(status=="edit"){ 
		var url;
		url = "saleReceipt_slip.php?book_id="+book_id+"&sr_id="+sr_id;
		window.open(url,'salereceipt','menubar=yes,width=250,height=650'); 
	}else{
		alert("Can't see print preview in this page.");
	}
}
function pdsaleReceiptWindow(pds_id,sr_id,status){
	if(status=="edit"){ 
		var url;
		url = "saleReceipt_slip.php?pds_id="+pds_id+"&sr_id="+sr_id;
		window.open(url,'salereceipt','menubar=yes,width=250,height=650'); 
	}else{
		alert("Can't see print preview in this page.");
	}
}
function open_memberdetail(oldmembercode){
		var url;
		var member = document.getElementById("cs[memid]");
		url = "membership/manage_membershipinfo.php?memberId="+member.value+"&oldmemberId="+oldmembercode;
		window.open(url,'memberWindow','location=0,toolbar=0,directoris=0,status=0,menubar=0,scrollbars=1,resizable=0,width=1020,height=700,left=500,top=100'); 
	
}
function changeMemberButton(oldmembercode){
	var member = document.getElementById("cs[memid]");
	if(member.value > 0 && !isNaN(member.value) && oldmembercode==member.value){
		document.getElementById("b_mhistory").value="History";
		document.getElementById("b_mhistory").title="Member History"; 
	}else{
		document.getElementById("b_mhistory").value="Search";
		document.getElementById("b_mhistory").title="Member Search";
		document.getElementById("membercategory").innerHTML="";
	}
}
function setDeleteGift(giftNum){
	var gift = document.getElementById("deleteGift");
	gift.value = giftNum;
	var frm = document.getElementById("appt");
	frm.submit();
}
function newwindow(target,nameofwindow){
	window.open(target,nameofwindow,'height=700,width=1020,resizable=0,scrollbars=1,fullscreen=yes');
}
function miniwindow(target,nameofwindow,height,width,status,left,top,resizeable){ 
	//alert(target+nameofwindow+'location=0,toolbar=0,directoris=0,status='+status+',menubar=0,scrollbars=1,resizable='+resizeable+',width='+width+',height='+height+',left='+left+',top='+top+'');
	window.open(target,nameofwindow,'location=0,toolbar=0,directoris=0,status='+status+',menubar=0,scrollbars=1,resizable='+resizeable+',width='+width+',height='+height+',left='+left+',top='+top+''); 
}
function check_csivalue(thId){
	if(thId>0){return true;}
	else{alert("Please update booking before insert CSI!!");return false; }
}
function editAgent(phone,person,id){
	window.opener.document.getElementById('cs[bpphone]').value=phone;
	window.opener.document.getElementById('cs[bpname]').value=person;
	for (var j = 0; j < window.opener.document.getElementById('cs[bcompany]').length; j++) {
		if (window.opener.document.getElementById('cs[bcompany]').options[j].value == id) {
			window.opener.document.getElementById('cs[bcompany]').options[j].selected = true;
	 	}else{
			window.opener.document.getElementById('cs[bcompany]').options[j].selected = false;
	 	}
	}
	window.opener.document.getElementById('cs[cms]').checked = true;
	window.opener.document.getElementById("bpCheck").value="History";
	window.close();
}
/*
 * update booking information for Customer phone number history page
 * update information in Customer Name, Member Code, Phone Number, B.P. Name, B.P. PH#, Commission Check,
 * Individual Customer Information:  
 * Name, Phone, Email, Birthday, Sex, Nationality, Resident
 * @param - information line that want to update 
 * @modified - 01-Oct-2009 update field birthday
 */
function editCS(id){
	window.opener.document.getElementById('cs[name]').value="";
	if(window.opener.document.getElementById('cs[bpname]').disabled==false) {
		window.opener.document.getElementById('cs[bpname]').value="";
		window.opener.document.getElementById('cs[bpphone]').value="";
		window.opener.document.getElementById('cs[cms]').checked = false;
		window.opener.document.getElementById('cs[bpname]').value=document.getElementById('cs_name'+id).value;
		window.opener.document.getElementById('cs[bpphone]').value=document.getElementById('cs_phone'+id).value;
		if(document.getElementById('set_cms'+id).value=="true"){
			window.opener.document.getElementById('cs[cms]').checked = true;
		}
	}
	window.opener.document.getElementById('cs[memid]').value="";
	window.opener.document.getElementById('cs[phone]').value=document.getElementById('cs_phone'+id).value;
	window.opener.document.getElementById('cs[name]').value=document.getElementById('cs_name'+id).value;
	window.opener.document.getElementById('cs[memid]').value=document.getElementById('member_code'+id).value;
	if(document.getElementById('member_code'+id).value>0){window.opener.document.getElementById('b_mhistory').value="History";window.opener.document.getElementById('b_mhistory').title="Member History";}
	else{window.opener.document.getElementById('b_mhistory').value="Search";window.opener.document.getElementById('b_mhistory').title="Member Search";}
	if(document.getElementById('tbname'+id).value=="d_indivi_info"){
		window.opener.document.getElementById('tw[0][csnameinroom]').value=document.getElementById('cs_name'+id).value;
		window.opener.document.getElementById('tw[0][csphoneinroom]').value=document.getElementById('cs_phone'+id).value;
		window.opener.document.getElementById('tw[0][csbday]').value=document.getElementById('cs_bday'+id).value;
		window.opener.document.getElementById('tw[0][hidden_csbday]').value=document.getElementById('cs_hiddenbday'+id).value;
		window.opener.document.getElementById('tw[0][csemail]').value=document.getElementById('cs_email'+id).value;
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
		for (var j = 0; j < window.opener.document.getElementById('tw[0][resident]').length; j++) {
			if (window.opener.document.getElementById('tw[0][resident]').options[j].value == document.getElementById('resident'+id).value) {
				window.opener.document.getElementById('tw[0][resident]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw[0][resident]').options[j].selected = false;
		 	}
		}
		//window.opener.document.getElementById("cs[searchchk]").value++;
	}else{
		window.opener.document.getElementById('tw[0][csnameinroom]').value=document.getElementById('cs_name'+id).value;
		window.opener.document.getElementById('tw[0][csphoneinroom]').value=document.getElementById('cs_phone'+id).value;
		window.opener.document.getElementById('tw[0][csbday]').value="";
		window.opener.document.getElementById('tw[0][hidden_csbday]').value="0000-00-00";
		window.opener.document.getElementById('tw[0][csemail]').value="";
		window.opener.document.getElementById('tw[0][national]').options[0].selected = true;
		window.opener.document.getElementById('tw[0][sex]').options[0].selected = true;
		window.opener.document.getElementById('tw[0][resident]').options[0].selected = true;
	}
	window.opener.document.getElementById('tw[0][csageinroom]').value="";
	//window.opener.document.getElementById("csphoneSearch").value="History";
	window.close();
}
/*
 * update booking information for Customer phone number history page in individual info. search
 * update information in 
 * Individual Customer Information:  
 * Name, Phone, Email, Birthday, Sex, Nationality, Resident
 * @param - information line that want to update 
 * @modified - 01-Oct-2009 update field birthday
 */
function editTW(id){
	var twid = window.opener.document.getElementById("cs[searchchk]").value;
	var ttpp = window.opener.document.getElementById("cs[ttpp]").value;
	twid=ttpp;
	for(i=0;i < ttpp;i++){
		if(window.opener.document.getElementById('tw['+i+'][csnameinroom]').value==""){twid=i;break;}
	}
	if(twid<ttpp){
		window.opener.document.getElementById('tw['+twid+'][csnameinroom]').value=document.getElementById('cs_name'+id).value;
		window.opener.document.getElementById('tw['+twid+'][csphoneinroom]').value=document.getElementById('cs_phone'+id).value;
		window.opener.document.getElementById('tw['+twid+'][csemail]').value=document.getElementById('cs_email'+id).value;
		window.opener.document.getElementById('tw['+twid+'][csbday]').value=document.getElementById('cs_bday'+id).value;
		window.opener.document.getElementById('tw['+twid+'][hidden_csbday]').value=document.getElementById('cs_hiddenbday'+id).value;
		for (var j = 0; j < window.opener.document.getElementById('tw['+twid+'][national]').length; j++) {
			if (window.opener.document.getElementById('tw['+twid+'][national]').options[j].value == document.getElementById('cs_nationality'+id).value) {
				window.opener.document.getElementById('tw['+twid+'][national]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw['+twid+'][national]').options[j].selected = false;
		 	}
		}
		for (var j = 0; j < window.opener.document.getElementById('tw['+twid+'][sex]').length; j++) {
			if (window.opener.document.getElementById('tw['+twid+'][sex]').options[j].value == document.getElementById('cs_sex'+id).value) {
				window.opener.document.getElementById('tw['+twid+'][sex]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw['+twid+'][sex]').options[j].selected = false;
		 	}
		}
		for (var j = 0; j < window.opener.document.getElementById('tw['+twid+'][resident]').length; j++) {
			if (window.opener.document.getElementById('tw['+twid+'][resident]').options[j].value == document.getElementById('resident'+id).value) {
				window.opener.document.getElementById('tw['+twid+'][resident]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw['+twid+'][resident]').options[j].selected = false;
		 	}
		}
		window.opener.document.getElementById("cs[searchchk]").value++;
	}else{
		twid = ttpp-1;
		window.opener.document.getElementById('tw['+twid+'][csnameinroom]').value=document.getElementById('cs_name'+id).value;
		window.opener.document.getElementById('tw['+twid+'][csphoneinroom]').value=document.getElementById('cs_phone'+id).value;
		window.opener.document.getElementById('tw['+twid+'][csemail]').value=document.getElementById('cs_email'+id).value;
		window.opener.document.getElementById('tw['+twid+'][csbday]').value=document.getElementById('cs_bday'+id).value;
		window.opener.document.getElementById('tw['+twid+'][hidden_csbday]').value=document.getElementById('cs_hiddenbday'+id).value;
		for (var j = 0; j < window.opener.document.getElementById('tw['+twid+'][national]').length; j++) {
			if (window.opener.document.getElementById('tw['+twid+'][national]').options[j].value == document.getElementById('cs_nationality'+id).value) {
				window.opener.document.getElementById('tw['+twid+'][national]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw['+twid+'][national]').options[j].selected = false;
		 	}
		}
		for (var j = 0; j < window.opener.document.getElementById('tw['+twid+'][sex]').length; j++) {
			if (window.opener.document.getElementById('tw['+twid+'][sex]').options[j].value == document.getElementById('cs_sex'+id).value) {
				window.opener.document.getElementById('tw['+twid+'][sex]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw['+twid+'][sex]').options[j].selected = false;
		 	}
		}
		for (var j = 0; j < window.opener.document.getElementById('tw['+twid+'][resident]').length; j++) {
			if (window.opener.document.getElementById('tw['+twid+'][resident]').options[j].value == document.getElementById('resident'+id).value) {
				window.opener.document.getElementById('tw['+twid+'][resident]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw['+twid+'][resident]').options[j].selected = false;
		 	}
		}
	}
	//window.opener.document.getElementById("csphoneSearch").value="History";
	window.close();
}
function setMaxtthour(maxtthour){
	var ttpp = document.getElementById('cs[ttpp]').value;
	var chktthour = document.getElementById('chktthour').value;
	//alert("chktthour="+chktthour);
	if(chktthour==0){
		for (var i = 0; i < ttpp; i++) {
			for (var j = 0; j < document.getElementById("tw["+i+"][0][hour]").length; j++) {
				if (document.getElementById("tw["+i+"][0][hour]").options[j].value == maxtthour) {
					document.getElementById("tw["+i+"][0][hour]").options[j].selected = true;
			 	}else{
					document.getElementById("tw["+i+"][0][hour]").options[j].selected = false;
			 	}
			}
		}
		document.getElementById('chktthour').value++;
	}else{
		return false;
	}
}
function setMaxappttime(maxappttime){
	var ttpp = document.getElementById('cs[appttime]').value;
	var chkappttime = document.getElementById('chkappttime').value;
	if(chkappttime==0){
		document.getElementById('chkappttime').value++;
	}else{
		return false;
	}
}
/////////// For set tax calculate product in sale receipt /////////////////////
function setTaxProduct(taxId,formId){
	var tax = document.getElementById(taxId);
	//alert(formId);
	if(tax.value==0){
		tax.value=1;
	}else{
		tax.value=0;
	}
	document.getElementById(formId).submit();
}
/////////// For set service charge calculate product in sale receipt /////////////////////
function setScProduct(scId,formId){
	var sc = document.getElementById(scId);
	//alert(formId);
	if(sc.value==0){
		sc.value=1;
	}else{
		sc.value=0;
	}
	document.getElementById(formId).submit();
}
/////////// search information in gift certificate,membership information page /////////////////////
function searchInfo(page){
	sortInfo('',page,'');
}
/////////// sort information in current customer,gift certificate,membership information page /////////////////////
function sortInfo(order,page,url){
	var search = "";
	if(document.getElementById('search')!=null){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	 }
	 	
	// membership information
	var categoryid = "";
	if(document.getElementById('categoryid')!=null){
	 	categoryid = document.getElementById("categoryid").value;
		categoryid = "&categoryid="+categoryid;
	 }
	 
	var branch = "";
	if(document.getElementById('branch_id')!=null){
	 	branch = "&branch_id="+document.getElementById("branch_id").value;}
	 	
	var sort = document.getElementById("sort").value;
	
	if(order==""){
		order = "&order="+document.getElementById("order").value;
	}else{
		order = "&order="+order;
		if(sort=="desc"){sort="asc";}else{sort="desc";}
	}
	
	var gifttypeid='';
	if(document.getElementById('gifttypeid')!=null && document.getElementById('gifttypeid').value != ''){
		gifttypeid= '&gifttypeid='+document.getElementById('gifttypeid').value;
	}
	var showInactive = "";
	if(document.getElementById('showinactive')!=null){
		if(document.getElementById('showinactive').checked == true){
			showInactive= '&showinactive='+document.getElementById('showinactive').value;
		}else{showInactive= '&showinactive=0';}
	}
	sort = "&sort="+sort;
	var querystr = search+branch+sort+order+categoryid+gifttypeid+showInactive;
	if(page==""){
		page = 1;
	}
	if(url=="undefined"||url==null){
		url = "index.php";
	}
	querystr = "?"+querystr+"&page="+page+"&pageid=1";
	location.href=url+querystr;
}

function setEnable(table,id,active){
	var d='';
	var showDetail='';
	var search='';
	var page='';
	var order='';
	var category_id='';
	var gifttypeid='';
	
	if(document.getElementById('search')!=null){
		search= '&search='+document.getElementById('search').value;
	}
	if(document.getElementById('order')!=null && document.getElementById('order').value != ''){
		order= '&order='+document.getElementById('order').value;
	}
	if(document.getElementById('page')!=null && document.getElementById('page').value != ''){
		page= '&page='+document.getElementById('page').value;
	}
	if(document.getElementById('categoryid')!=null && document.getElementById('categoryid').value != ''){
		category_id= '&categoryid='+document.getElementById('categoryid').value;
	}
	if(document.getElementById('gifttypeid')!=null && document.getElementById('gifttypeid').value != ''){
		gifttypeid= '&gifttypeid='+document.getElementById('gifttypeid').value;
	}
	
	d+="table="+table+"&id="+id+"&expired="+active+"&method=setactive"+search+order+page+category_id+gifttypeid;

	if(table=="m_membership"){
		location.href="add_membershipinfo.php?"+d;
	}else if(table=="g_gift"){
		location.href="add_giftinfo.php?"+d;
	}else{
		alert("Please check tablename for insert data!!");
	}
}
/*
 * update booking information from Membership information page
 * update information in Customer Name, Member Code, Phone Number, B.P. Name, B.P. PH#, Commission Check,
 * Individual Customer Information:  
 * Name, Phone, Email, Birthday, Sex, Nationality, Resident
 * @param - information line that want to update 
 * @modified - 01-Oct-2009 update field birthday
 */
function editMember(id){
	window.opener.document.getElementById('cs[memid]').value=document.getElementById('member_code'+id).value;
	if(document.getElementById('member_code'+id).value>0){
		window.opener.document.getElementById('b_mhistory').value="History";
		window.opener.document.getElementById('b_mhistory').title="Member History";
	}
	else{
		window.opener.document.getElementById('b_mhistory').value="Directory";
		window.opener.document.getElementById('b_mhistory').title="Member Directory";
	}
	var phone;
	if(document.getElementById('phone'+id).value>0){
		phone=document.getElementById('phone'+id).value;
	}else{
		phone=document.getElementById('mobile'+id).value;
	}
	
	if(window.opener.document.getElementById('cs[bpphone]')==null){window.close();}
	
	window.opener.document.getElementById('cs[bpphone]').value=phone;
	window.opener.document.getElementById('cs[phone]').value=phone;
	var name="";
	name = document.getElementById('fname'+id).value+" "+document.getElementById('mname'+id).value+" "+document.getElementById('lname'+id).value;
	window.opener.document.getElementById('cs[name]').value=name;
	window.opener.document.getElementById('cs[bpname]').value=name;
	window.opener.document.getElementById('tw[0][csageinroom]').value="";
	window.opener.document.getElementById('tw[0][csnameinroom]').value=name;
	window.opener.document.getElementById('tw[0][csbday]').value=document.getElementById('cs_bday'+id).value;
	window.opener.document.getElementById('tw[0][hidden_csbday]').value=document.getElementById('cs_hiddenbday'+id).value;
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
	window.opener.document.getElementById('tw[0][member_use]').checked = true;
	//window.opener.document.getElementById("cs[searchchk]").value++;
	//window.opener.document.getElementById("csphoneSearch").value="History";
	window.close();
}
// email validator
function checkEmail(inputobject){	
	var inputvalue = inputobject.value;
    var pattern=/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z]){2,4}$/;
    if(!pattern.test(inputvalue)&&inputvalue!=""){           
    	alert("Email must include \"@\" and period \".\" \n    e.g., name@hostname.com");  
		inputobject.value = "";
    }
}
// add code free discount to booking 
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
//collapse/expand log tab 
function showHideLog(div){
	var cmsdiv = document.getElementById('cmsdiv');
	var srdiv = document.getElementById('srdiv');
	var thediv = document.getElementById('thediv');
	
	document.getElementById("tabone").className = "";
	document.getElementById("tabtwo").className = "";
	document.getElementById("tabthree").className = "";
	
	if(div=="cmsdiv"){
		document.getElementById("tabone").className = "current";
		cmsdiv.style.display = "block";
		srdiv.style.display = "none";
		thediv.style.display = "none";
	}
	if(div=="srdiv"){
		document.getElementById("tabtwo").className = "current";
		cmsdiv.style.display =  "none";
		srdiv.style.display = "block";
		thediv.style.display = "none";
	}
	if(div=="thediv"){
		document.getElementById("tabthree").className = "current";
		cmsdiv.style.display =  "none";
		srdiv.style.display = "none";
		thediv.style.display = "block";
	}
	
}
//collapse/expand membership tab 
function showHideMember(div){
	var omhDiv = document.getElementById('omhDiv');
	var saleDiv = document.getElementById('saleDiv');
	var treatDiv = document.getElementById('treatDiv');
	
	if(document.getElementById('chkpage')!=null){
		var chkpage = document.getElementById('chkpage');
		if(div=="omhDiv"){
			chkpage.value = 1;
		}
		if(div=="saleDiv"){
			chkpage.value = 2;
		}
		if(div=="treatDiv"){
			chkpage.value = 3;
		}
	}	
	
	if(document.getElementById("tabone")!=null){document.getElementById("tabone").className = "";}
	document.getElementById("tabtwo").className = "";
	document.getElementById("tabthree").className = "";
	
	if(div=="omhDiv"){
		document.getElementById("tabone").className = "current";
		omhDiv.style.display = "block";
		saleDiv.style.display = "none";
		treatDiv.style.display = "none";
	}
	if(div=="saleDiv"){
		document.getElementById("tabtwo").className = "current";
		omhDiv.style.display = "none";
		saleDiv.style.display = "block";
		treatDiv.style.display = "none";
	}
	if(div=="treatDiv"){
		document.getElementById("tabthree").className = "current";
		omhDiv.style.display = "none";
		saleDiv.style.display =  "none";
		treatDiv.style.display = "block";
	}
	document.getElementById("errormsg").innerHTML = "";
	document.getElementById("successmsg").innerHTML = "";
}

//Open windows for print member history report
function printMemberHistory(memberCode){
	var chkpage = document.getElementById('chkpage');
	window.open('print_history.php?memberId='+memberCode+'&chkpage='+chkpage.value+'&export=print','','scrollbars=1, top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height))
}

//confirm membership balance 
function confirmBalance(memberid,pageid){
	var randomStr = "rand=" + Math.floor(Math.random()*10000);
	var balanceconfirm = confirm("Banlance membership will reset all member's sales receipt that was tick date less than today, Confirm?");
	
	if(balanceconfirm){
			document.getElementById("balance").value = "yes";
			return true ;
	}else{
		return false;
	}
}

function addPayPrice(i,j) {
	
	var totalprice = document.getElementById("srd["+i+"]["+0+"][sr_total]").value;
	var payprice = document.getElementById("srd["+i+"]["+j+"][pay_price]").value;
	var sumprice = parseFloat(0.00);
	var count = document.getElementById("mpdcount["+(i)+"]").value-1;
	var total = Math.round(totalprice*100)/100;

	for(var x=0;x<count;x++){
		sumprice=(parseFloat(sumprice))+(parseFloat(document.getElementById("srd["+i+"]["+(x)+"][pay_price]").value));
		sumprice=Math.round(sumprice*100)/100;
	}
		if(sumprice>total){
			alert("Payment is more than Total");
			document.getElementById("srd["+i+"]["+j+"][pay_price]").value=parseFloat(0.00);
			clearPayPrice(i,j,count,totalprice);
		}else{
			document.getElementById("srd["+i+"]["+(count)+"][pay_price]").value = (Math.round((totalprice-sumprice)*100)/100);
			document.getElementById("srd["+i+"]["+j+"][pay_price]").value = Math.round(payprice*100)/100;
		}
}

function clearPayPrice(i,j,count,totalprice) {
	var sumprice = parseFloat(0.00);
	for(var x=0;x<count;x++){
		if(x!=j){
			sumprice=(parseFloat(sumprice))+(parseFloat(document.getElementById("srd["+i+"]["+(x)+"][pay_price]").value));
		}
	}
	document.getElementById("srd["+i+"]["+(count)+"][pay_price]").value = (Math.round((totalprice-sumprice)*100)/100);
}

function CheckDuplicate(i,j) {
	var count = document.getElementById("mpdcount["+(i)+"]").value-1;
	var chkv = document.getElementById("srd["+i+"]["+j+"][pay_price]").value;
       for(var x=0;x<count;x++){
    	   if(x!=j){
    		 if(document.getElementById("srd["+(i)+"]["+(x)+"][paytype]").value==document.getElementById("srd["+(i)+"]["+(j)+"][paytype]").value){
    			alert("Please Select Method of Payment Again");
    			document.getElementById("srd["+(i)+"]["+(j)+"][paytype]").value=1;
    			document.getElementById("mpdcount["+(i)+"]").form.submit();
    			return false;
    		 }
    	   }   
       }
       if(document.getElementById("srd["+(i)+"]["+j+"][paytype]").value==1){
    	   document.getElementById("mpdcount["+(i)+"]").form.submit();
       }
}

function Chk_paid(i){
	var count = document.getElementById("mpdcount["+(i)+"]").value-1;
	var chk = document.getElementById("srd["+i+"]["+count+"][pay_price]").value;
	var p_chk = document.getElementById("srd["+i+"]["+0+"][pd_id]").value; 

	//var srtotal = parseFloat(0.00);
	//var sumprice = parseFloat(0.00);
	//for(var x=0;x<count;x++){
	//	srtotal=parseFloat(document.getElementById("srd["+i+"][0][sr_total]").value);
	//	sumprice=(parseFloat(sumprice))+(parseFloat(document.getElementById("srd["+i+"]["+(x)+"][pay_price]").value));
	//}
		//if(parseFloat(srtotal)!=parseFloat(sumprice)){
		//	alert("Total Payment Price not equal Total Price");
		//	document.getElementById("srd["+i+"][0][paid]").checked=false;
		//}else{
		
		if(chk!=0){
			alert("Please Fill Payment Price");
			document.getElementById("srd["+i+"][0][paid]").checked=false;
		}
		if(p_chk==1){
			alert("Please Fill Payment Price");
			document.getElementById("srd["+i+"][0][paid]").checked=false;
		}
		//}
}

function findmax(i){
	var count = document.getElementById("mpdcount["+(i)+"]").value;
	var max = document.getElementById("srd["+i+"]["+0+"][pay_price]").value; 
	var chk;
	var getid = document.getElementById("srd["+i+"]["+0+"][paytype]").value;
	for(var x=1;x<count;x++){
		chk = max - document.getElementById("srd["+i+"]["+x+"][pay_price]").value;
	  if(chk<0 && document.getElementById("srd["+i+"]["+x+"][paytype]").value!=1){
		  max = document.getElementById("srd["+i+"]["+x+"][pay_price]").value;
		  getid = document.getElementById("srd["+(i)+"]["+x+"][paytype]").value
	  }
	}
	document.getElementById("srd["+i+"][0][maxpaid]").value=getid;
}

function chkMinus(srcount){
	var ckm=0;
	var count=0;
	for(var i=0;i<=srcount;i++){
			count = document.getElementById("mpdcount["+i+"]").value;
			for(var j=0;j<count;j++){
				if(document.getElementById("srd["+i+"]["+j+"][pay_price]").value<0){
						ckm=1;
				}
			}
			if(ckm==1){
				alert("Please Check Payment Price Not Minus Value");
				document.getElementById("add").type="Button";
				return false;
			}
	}
}

function chkPlus(srcount){
	var ckm=0;
	var count=0;
	for(var i=0;i<=srcount;i++){
			count = document.getElementById("mpdcount["+i+"]").value;
			for(var j=0;j<count;j++){
				if(document.getElementById("srd["+i+"]["+j+"][pay_price]").value>=0){
						ckm=1;
				}
			}
			if(ckm==1){document.getElementById("add").type="Submit";}
	}
}

function checkTime(i,c){
	var start=document.getElementById("tw["+i+"]["+c+"][start_id]").value;
	var end=document.getElementById("tw["+i+"]["+c+"][end_id]").value;
	start=parseFloat(start);
	end=parseFloat(end);
	if(start>end){
		//alert(start+">"+end);
		alert("End Time must be equal or more than Start Time");
		document.getElementById("tw["+i+"]["+c+"][end_id]").value=document.getElementById("tw["+i+"]["+c+"][start_id]").value;
	}
	//else{
		//alert(start+"<"+end);
	//}
}