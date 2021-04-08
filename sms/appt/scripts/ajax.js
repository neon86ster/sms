/**
 * @author art
 * 
 * 
 */
function getHttpObject() {
	var xmlHttp = null;
	if (window.XMLHttpRequest) {
		xmlHttp = new XMLHttpRequest(); // FireFox, Opera and IE7
	}
	else if (window.ActiveXObject) {
		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP"); // IE5 & IE6
	}
        
	return xmlHttp;
}

function getRandomSession() {
   var randomStr = "rand=" + Math.floor(Math.random()*10000);
   return randomStr;
}

function checkform() {
    //alert("comming!!");    
    var name = document.myform.name.value;
    var lastname = document.myform.lastname.value;
    var email = document.myform.email.value;
    //alert("come function checkform()");
    if(name=="" || lastname=="" || email=="") {
        document.getElementById("area").innerHTML = "<font color='red'><b>Form Error</b></font>";
    }
    else {
        formsubmit(name, lastname, email);
    }
}

function formsubmit(name, lastname, email) {
    var ajaxRequest = getHttpObject();
    //alert("incoming");
    
    ajaxRequest.onreadystatechange = function() {
        var area = document.getElementById("area");
        if(ajaxRequest.readyState == 4 && ajaxRequest.status == 200) {
            area.innerHTML = ajaxRequest.responseText;
        }
    }
    
    var query="";
    query+="name="+name+"&lastname="+lastname+"&email="+email;
    ajaxRequest.open("GET","submit.php?"+query,true);
    ajaxRequest.send(null);
}
// use to Method GET to send information and return text
function getReturnText(url, data, divTag) {
   
	var objRequest = getHttpObject();
	var ranDom = getRandomSession();
	var a = document.getElementById(divTag);
        
        objRequest.onreadystatechange = function(){
			if(objRequest.readyState == 4 && objRequest.status == 200){
				a.innerHTML = objRequest.responseText;
			}
        }
       
        objRequest.open("GET", url+"?"+ranDom+"&"+data);
        objRequest.send(null);
        
}

// use to Method POST to send information and return text
function postReturnText(url, data, divTag) {
	var objRequest = getHttpObject();
	var ranDom = getRandomSession();
        var a = document.getElementById(divTag);
	
        objRequest.onreadystatechange = function(){
			if(objRequest.readyState == 4 && objRequest.status == 200){
                           a.innerHTML = objRequest.responseText;
                           //a.innerHTML = this.createDriver();
                           //return true;
			}
        }
       
    objRequest.open("POST",url);
	objRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	objRequest.send(data+"&"+ranDom);
        
}

function loadXMLDoc(dname) 
{         
  if (window.ActiveXObject) {
    xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
    
  }
  else if (document.implementation.createDocument) {
    xmlDoc = document.implementation.createDocument("","",null);
    
  }
  else {
    return null;
  }

  xmlDoc.async=false;
  xmlDoc.load(dname);
  
  return xmlDoc;
}

function getFormParaValue(dname,tableName) {
  // var f = Array();
   //var para = Array();
  // var f = loadElementName(dname, tableName);
   xmlDoc = this.loadXMLDoc(dname);

    var e = xmlDoc.getElementsByTagName(tableName)[0].getElementsByTagName('field');
   // var a = new Array();
    var i;
   var n,t="";
    //var show = document.getElementById("show");
    
    for(i=0; i<e.length; i++) {
		if(e[i].getAttribute('idfields')!="yes"&&e[i].getAttribute('showinform')!="no"){
			 n = e[i].getAttribute('name');
		       if(i)
		         t+= "&";
		      if(e[i].getAttribute('formtype')!="checkbox"){
		     	 t += n+"="+document.getElementById(n).value;
		      } else if(e[i].getAttribute('formtype')=="checkbox"&&document.getElementById(n).checked){
		      	 t += n+"=1";
		      } else if(e[i].getAttribute('formtype')=="checkbox"&&!document.getElementById(n).checked){
		      	 t += n+"=0";
		      }
		}
    }
   t+="&formname="+tableName;
   t+="&add="+document.getElementById("add").value;
   try{
   		t+="&again="+document.getElementById("again").checked;
   }catch(e){}
   if(document.getElementById("id")!=null&&document.getElementById("add").value==" save change "){t+="&id="+document.getElementById("id").value;}
   //show.innerHTML = t;
   return t;
}

function test() {
   var d = document.getElementById("show");
   d.innerHTML = "test display OK!!";
}
function chkbutton() {
   alert("Check");  
}
function gotoSearch(url) {
	var d = 'where='+document.getElementById('search').value;
	getReturnText(url,d,'tableDisplay');
}
function selectboxSearch(url,data) {
	var d='';
	d += data;
	if(document.getElementById('branch_id')!=null){
		d += '&branch_id='+document.getElementById('branch_id').value;
	} 
	if(document.getElementById('emp_department_id')!=null){
		d += '&emp_department_id='+document.getElementById('emp_department_id').value;
	} 
	if (document.getElementById('city_id')!=null){
		d += '&city_id='+document.getElementById('city_id').value;
	}
	if (document.getElementById('show_detail')!=null&&document.getElementById('show_detail').checked){
		//alert(document.getElementById('show_detail').value);
		d += '&show_detail='+document.getElementById('show_detail').value;
	}
	getReturnText(url,d,'tableDisplay');
}
function chkvalue(bid,date){
  	document.getElementById("bid").value = bid;
  	var chkPageEdit = document.getElementById("chkPageEdit").value;
  	getReturnText('manage_appointmentinfo.php','page=1&bid='+bid+'&date='+date+"&chkPageEdit="+chkPageEdit,'tableDisplay');
}  
function gotoURL(myURL){
    parent.location.href=myURL;
}
function showMessage() {	
	var bid = document.getElementById("bid").value;
	var date = document.getElementById("date").value;
	var chkPageEdit = document.getElementById("chkPageEdit").value;
	var chkRsView = document.getElementById("chkRsView").value;
	getReturnText('manage_appointmentinfo.php','page=1&bid='+bid+'&date='+date+'&chkPageEdit='+chkPageEdit+'&chkRsView='+chkRsView,'tableDisplay');
    
    setTimeout(showMessage, 10000);
}
function showinfo(id,url,divid){
	document.getElementById(divid).style.display='block';
	id = document.getElementById(id).value;
	if(url=="accinfo.php"){d = "accid="+id;}
	if(url=="bpinfo.php"){d = "bpid="+id;}
	getReturnText("bookingparty/"+url,d,divid);
}
function openthQueue(blockid,url,divid){
	d = "thblock="+blockid;
	if(divid != "thinfo"){
		d += '&branchid='+document.getElementById('cs[branch]').value;
		d += '&date='+document.getElementById('cs[hidden_apptdate]').value;
		var appttime = document.getElementById('cs[appttime]');
		d += '&time='+appttime.options[appttime.selectedIndex].text;
		var hr = document.getElementById(divid);
		d += '&hour='+hr.options[hr.selectedIndex].text;
	}
	
	window.open(url+"?"+d,"TherapistQueue",'location=0,toolbar=0,directoris=0,status=0,menubar=0,scrollbars=1,resizable=0,width=1020,height=700,left=500,top=100'); 
}
function thChoose(blockid,value){
	window.opener.document.getElementById(blockid).value = value;
	var block_index = blockid.replace("thid","name");
	var th_block = window.opener.document.getElementById(block_index);
	for(var i = 0; i < th_block.options.length; i++){
		if(th_block.options[i].value == value){
			th_block.options[i].selected = true;
		} 
	}
}
/////////// search information in current customer information page /////////////////////
function searchcust(){
	var search = "";
	if(document.getElementById('crrcust')!=null){
		search = document.getElementById("crrcust").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	 }
	 
	 newwindow('currentcust.php?search='+search,'currentcustomer');
}

 function loading(){
	 if (document.all) document.body.style.cssText="overflow:hidden;"
 }

/////////// quick search page /////////////////////
 function quicksearch(target,nameofwindow){
	 //var winleft = (screen.width-1020)/2;
	 //var winup = (screen.height-470)/2;
	 var winleft = 100;
	 var winup = 100;
	 window.open(target,nameofwindow,'height=470,width=1020,resizable=0,left='+winleft+',top=' +winup+',scrollbars=1');
}

function Popup(apri) {
	var stile = 'width='+screen.width+', height='+screen.height+', top=0, left=0, resizable=0, scrollbars=1, fullscreen=yes';

	//var stile = 'top=10, left=10, status=no, menubar=no, toolbar=no scrollbar=no';
	window.open(apri, "", stile);
}

function Qadd(id,type,person,phone){

	if(type=="customer"){
		window.opener.document.getElementById('cs[memid]').value=document.getElementById('cus_code'+id).value;
		if(document.getElementById('cus_code'+id).value>0){
			window.opener.document.getElementById('b_mhistory').value="History";
			window.opener.document.getElementById('b_mhistory').title="Member History";
		}
		else{
			window.opener.document.getElementById('b_mhistory').value="Directory";
			window.opener.document.getElementById('b_mhistory').title="Member Directory";
		}
		
		var name = document.getElementById('cus_name'+id).value;
		var phone = document.getElementById('cus_phone'+id).value;
		window.opener.document.getElementById('cs[name]').value=name;
		window.opener.document.getElementById('cs[phone]').value=phone;
		window.opener.document.getElementById('cs[bpname]').value=name;
		window.opener.document.getElementById('cs[bpphone]').value=phone;
		
		window.opener.document.getElementById('tw[0][csnameinroom]').value=name;
		window.opener.document.getElementById('tw[0][csphoneinroom]').value=phone;
		window.opener.document.getElementById('tw[0][csemail]').value=document.getElementById('cus_email'+id).value;
		window.opener.document.getElementById('tw[0][hidden_csbday]').value=document.getElementById('cus_hidbday'+id).value;
		window.opener.document.getElementById('tw[0][csbday]').value=document.getElementById('cus_bday'+id).value;
		window.opener.document.getElementById('tw[0][csageinroom]').value=document.getElementById('cus_age'+id).value;
		
		for (var j = 0; j < window.opener.document.getElementById('tw[0][sex]').length; j++) {
			if (window.opener.document.getElementById('tw[0][sex]').options[j].value == document.getElementById('cus_sex'+id).value) {
				window.opener.document.getElementById('tw[0][sex]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw[0][sex]').options[j].selected = false;
		 	}
		}
		
		for (var j = 0; j < window.opener.document.getElementById('tw[0][national]').length; j++) {
			if (window.opener.document.getElementById('tw[0][national]').options[j].value == document.getElementById('cus_nationality'+id).value) {
				window.opener.document.getElementById('tw[0][national]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw[0][national]').options[j].selected = false;
		 	}
		}
		
		if(window.opener.document.getElementById('cs[memid]').value){
			window.opener.document.getElementById('tw[0][member_use]').checked = true;
		}
	}
	
	if(type=="member"){
		window.opener.document.getElementById('cs[memid]').value=document.getElementById('member_code'+id).value;
		if(document.getElementById('member_code'+id).value>0){
			window.opener.document.getElementById('b_mhistory').value="History";
			window.opener.document.getElementById('b_mhistory').title="Member History";
		}
		else{
			window.opener.document.getElementById('b_mhistory').value="Directory";
			window.opener.document.getElementById('b_mhistory').title="Member Directory";
		}
		
		var name = document.getElementById('member_name'+id).value;
		var phone = document.getElementById('member_phone'+id).value;
		window.opener.document.getElementById('cs[name]').value=name;
		window.opener.document.getElementById('cs[phone]').value=phone;
		window.opener.document.getElementById('cs[bpname]').value=name;
		window.opener.document.getElementById('cs[bpphone]').value=phone;
		
		window.opener.document.getElementById('tw[0][csnameinroom]').value=name;
		window.opener.document.getElementById('tw[0][csphoneinroom]').value=phone;
		window.opener.document.getElementById('tw[0][csemail]').value=document.getElementById('member_email'+id).value;
		window.opener.document.getElementById('tw[0][hidden_csbday]').value=document.getElementById('member_hidbday'+id).value;
		window.opener.document.getElementById('tw[0][csbday]').value=document.getElementById('member_bday'+id).value;
		window.opener.document.getElementById('tw[0][csageinroom]').value=document.getElementById('member_age'+id).value;
		
		for (var j = 0; j < window.opener.document.getElementById('tw[0][sex]').length; j++) {
			if (window.opener.document.getElementById('tw[0][sex]').options[j].value == document.getElementById('member_sex'+id).value) {
				window.opener.document.getElementById('tw[0][sex]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw[0][sex]').options[j].selected = false;
		 	}
		}
		
		for (var j = 0; j < window.opener.document.getElementById('tw[0][national]').length; j++) {
			if (window.opener.document.getElementById('tw[0][national]').options[j].value == document.getElementById('member_nationality'+id).value) {
				window.opener.document.getElementById('tw[0][national]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('tw[0][national]').options[j].selected = false;
		 	}
		}
		window.opener.document.getElementById('tw[0][member_use]').checked = true;
	}
	
	if(type=="agent"){
		window.opener.document.getElementById('cs[bpphone]').value=phone;
		window.opener.document.getElementById('cs[bpname]').value=person;
		for (var j = 0; j < window.opener.document.getElementById('cs[bcompany]').length; j++) {
			if (window.opener.document.getElementById('cs[bcompany]').options[j].value == id) {
				window.opener.document.getElementById('cs[bcompany]').options[j].selected = true;
		 	}else{
				window.opener.document.getElementById('cs[bcompany]').options[j].selected = false;
		 	}
		}
		//window.opener.document.getElementById('cs[cms]').checked = true;
		window.opener.document.getElementById("bpCheck").value="History";
	}
	if(type=="mkcode"){
		for (var j = 0; j < window.opener.document.getElementById('cs[inspection]').length; j++) {
			if (window.opener.document.getElementById('cs[inspection]').options[j].value == id) {
				window.opener.document.getElementById('cs[inspection]').options[j].selected = true;
			}else{
				window.opener.document.getElementById('cs[inspection]').options[j].selected = false;
			}
		}
	}
	
	if(type=="gift"){
		window.opener.document.getElementById('giftChk').value = true;
		window.opener.document.getElementById('appt').action = "manage_booking.php?giftnumber[]="+id;
		window.opener.document.getElementById('addgift').click();
		
	}
	
	window.close();
}

function focusIt(srd)
{
if(!srd){
	srd = document.getElementById("srcount").value-1;
}
var barcode = document.getElementById("barcode["+srd+"]");
barcode.focus();
}

/* With Reload page
function checkKey(bc,frmname,srd,keyevent){
	
   if ( bc.value.length == 13 && keyevent.keyCode==13) { 
		HttPRequest = false;
		  if (window.XMLHttpRequest) { // Mozilla, Safari,...
			 HttPRequest = new XMLHttpRequest();
			 if (HttPRequest.overrideMimeType) {
				HttPRequest.overrideMimeType('text/html');
			 }
		  } else if (window.ActiveXObject) { // IE
			 try {
				HttPRequest = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
				try {
				   HttPRequest = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {}
			 }
		  } 
		  
		  if (!HttPRequest) {
			 alert('Cannot create XMLHTTP instance');
			 return false;
		  }
	
		    var url = "readbarcode.php";
			var pmeters = "barcode="+bc.value;
			
			HttPRequest.open('POST',url,true);
						
			HttPRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			HttPRequest.setRequestHeader("Content-length", pmeters.length);
			HttPRequest.setRequestHeader("Connection", "close");
			HttPRequest.send(pmeters);
			
			
			HttPRequest.onreadystatechange = function()
			{
				 if(HttPRequest.readyState == 4) // Return Request
				  {		
						var row = (document.getElementById('srdcount['+srd+']').value);
						var insert = true;
						for (var i=0; i<row; i++)
						  {
								if(document.getElementById('srd['+srd+']['+i+'][pd_id]').value==HttPRequest.responseText){
									var qty = document.getElementById('srd['+srd+']['+i+'][quantity]').value;
									document.getElementById('srd['+srd+']['+i+'][quantity]').value = parseInt(qty)+1;
									insert = false;
								}
						  }
						if(insert==true){
						  document.getElementById('srd['+srd+']['+(row-1)+'][pd_id]').value = HttPRequest.responseText;
						}
						document.getElementById('lastscan').value = srd;
						document.forms[frmname].submit();
				  }				
			}
   }
   else if ( bc.value.length >= 13){
		var next_bc = bc.value.substring(13);
		bc.value = next_bc;
   }
}
*/
function checkKey(bc,frmname,srd,keyevent,branch){

   if ( bc.value.length == 13 && keyevent.keyCode==13) { 
		HttPRequest = false;
		  if (window.XMLHttpRequest) { // Mozilla, Safari,...
			 HttPRequest = new XMLHttpRequest();
			 if (HttPRequest.overrideMimeType) {
				HttPRequest.overrideMimeType('text/html');
			 }
		  } else if (window.ActiveXObject) { // IE
			 try {
				HttPRequest = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
				try {
				   HttPRequest = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {}
			 }
		  } 
		  
		  if (!HttPRequest) {
			 alert('Cannot create XMLHTTP instance');
			 return false;
		  }
	
		    var url = "readbarcode.php";
			var pmeters = "barcode="+bc.value+"&branch="+branch;
			
			HttPRequest.open('POST',url,true);
						
			HttPRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			HttPRequest.setRequestHeader("Content-length", pmeters.length);
			HttPRequest.setRequestHeader("Connection", "close");
			HttPRequest.send(pmeters);
			
			
			HttPRequest.onreadystatechange = function()
			{
				 if(HttPRequest.readyState == 4) // Return Request
				  {		
						var objJson = json_parse(HttPRequest.responseText);
					
					if(objJson.pd_id && document.getElementById('srd['+srd+'][0][paid]').checked!=true){
						var row = (document.getElementById('srdcount['+srd+']').value);
						var insert = true;
						for (var i=0; i<row; i++)
						  {
								if(document.getElementById('srd['+srd+']['+i+'][pd_id]').value==objJson.pd_id){
									var qty = document.getElementById('srd['+srd+']['+i+'][quantity]').value;
									document.getElementById('srd['+srd+']['+i+'][quantity]').value = parseInt(qty)+1;
									insert = false;
								}
						  }
						  
						//For no Reload (Not Finished)
						if(insert==true){
						var select = document.getElementById('srd['+srd+']['+(row-1)+'][pd_id]').parentNode.parentNode.parentNode;
						var dupeSelect = select.cloneNode(true);
						
						cleanWhitespace(dupeSelect);
						//for (var i=0; i<dupeSelect.childNodes[0].childNodes.length; i++)
						//  {
						//	alert (i+" : "+dupeSelect.childNodes[0].childNodes[i].id);
						//  }
						dupeSelect.childNodes[0].childNodes[0].childNodes[0].id='srd['+srd+']['+(row)+'][pd_id]';
						dupeSelect.childNodes[0].childNodes[0].childNodes[0].name='srd['+srd+']['+(row)+'][pd_id]';
						dupeSelect.childNodes[0].childNodes[2].id='srd['+srd+']['+(row)+'][quantity]';
						dupeSelect.childNodes[0].childNodes[2].name='srd['+srd+']['+(row)+'][quantity]';
						dupeSelect.childNodes[0].childNodes[4].id='srd['+srd+']['+(row)+'][unit_price]';
						dupeSelect.childNodes[0].childNodes[4].name='srd['+srd+']['+(row)+'][unit_price]';
						dupeSelect.childNodes[0].childNodes[6].id='srd['+srd+']['+(row)+'][plus_sc]';
						dupeSelect.childNodes[0].childNodes[6].name='srd['+srd+']['+(row)+'][plus_sc]';
						dupeSelect.childNodes[0].childNodes[7].id='srd['+srd+']['+(row)+'][plus_tax]';
						dupeSelect.childNodes[0].childNodes[7].name='srd['+srd+']['+(row)+'][plus_tax]';
						dupeSelect.childNodes[0].childNodes[8].id='srd['+srd+']['+(row)+'][srd_id]';
						dupeSelect.childNodes[0].childNodes[8].name='srd['+srd+']['+(row)+'][srd_id]';
						dupeSelect.childNodes[0].childNodes[9].id='srd['+srd+']['+(row)+'][pd_id_tmp]';
						dupeSelect.childNodes[0].childNodes[9].name='srd['+srd+']['+(row)+'][pd_id_tmp]';
						document.getElementById('srdcount['+srd+']').value=parseInt(row)+1;
										
						insertAfter(dupeSelect,select);
						
						var maxRow=0;
						for (var i=0; i<document.getElementById('srcount').value; i++){
							if(parseInt(maxRow)<parseInt(document.getElementById('srdcount['+i+']').value)){
								maxRow = document.getElementById('srdcount['+i+']').value
							}
						}
						
						for (var i=0; i<document.getElementById('srcount').value; i++){
							var cnt=0;
							for(var a=0;a<document.getElementsByTagName('*').length;a++){
								if(document.getElementsByTagName('*')[a].id == 'clone_brow['+i+']'){
									document.getElementsByTagName('*')[a].id = 'delte_brow['+i+']['+cnt+']';
									cnt++
								}
							}
							
							for(var a=0;a<cnt;a++){
								var element = document.getElementById('delte_brow['+i+']['+a+']');
								element.parentNode.removeChild(element);
							}

							for (var j=0; j<(maxRow-document.getElementById('srdcount['+i+']').value); j++){
									var brow = document.getElementById('brow['+i+']');
									var dupebrow = brow.cloneNode(true);
									dupebrow.id='clone_brow['+i+']';
									insertAfter(dupebrow,brow);
							}
						}
						var cnt=0;
						for(var a=0;a<document.getElementsByTagName('*').length;a++){
								if(document.getElementsByTagName('*')[a].id == 'clone_mrow'){
									document.getElementsByTagName('*')[a].id = 'delte_mrow['+cnt+']';
									cnt++
								}
							}
						for(var a=0;a<cnt;a++){
							var element = document.getElementById('delte_mrow['+a+']');
							element.parentNode.removeChild(element);
						}
						
						for (var i=0; i<maxRow-1; i++){
							var mrow = document.getElementById('mrow');
							var dupemrow = mrow.cloneNode(true);
							dupemrow.id="clone_mrow";
							insertAfter(dupemrow,mrow);
						}

						  document.getElementById('srd['+srd+']['+(row-1)+'][pd_id]').value = objJson.pd_id;
						  document.getElementById('srd['+srd+']['+(row-1)+'][unit_price]').value = objJson.standard_price;
						  document.getElementById('srd['+srd+']['+(row-1)+'][plus_tax]').value = objJson.set_tax;
						  document.getElementById('srd['+srd+']['+(row-1)+'][plus_sc]').value = objJson.set_sc;
						}
						
						var row = (document.getElementById('srdcount['+srd+']').value);
							var total = parseFloat(0);
							var sub_total = parseFloat(0);
							for (var i=0; i<row; i++)
							  {
									if(objJson.set_sc==1){
										amount = (parseFloat(document.getElementById('srd['+srd+']['+(i)+'][unit_price]').value)*
												 parseFloat(document.getElementById('srd['+srd+']['+(i)+'][quantity]').value))*
												 (100+parseFloat(objJson.sc_percent))/100;
									}else{
										amount = parseFloat(document.getElementById('srd['+srd+']['+(i)+'][unit_price]').value)*
												 parseFloat(document.getElementById('srd['+srd+']['+(i)+'][quantity]').value);
									}
									
									if(objJson.set_tax==1){
										amount = amount*(100+parseFloat(objJson.tax_percent))/100;
									}
							  sub_total+=parseFloat(document.getElementById('srd['+srd+']['+(i)+'][unit_price]').value);
							  total+=amount;
							  }
		
						document.getElementById('total['+srd+']').innerHTML = "";
						document.getElementById('total['+srd+']').innerHTML = total.toFixed(2);
						document.getElementById('srd['+srd+'][0][subtotal]').value = sub_total.toFixed(2);
						document.getElementById('srd['+srd+'][0][sr_total]').value = total.toFixed(2);
						document.getElementById('srd['+srd+'][0][sr_total1]').value = total.toFixed(2);
						document.getElementById('srd['+srd+'][0][pay_price]').value  = total.toFixed(2);
						document.getElementById('lastscan').value = srd;				
					}
				  }	
			bc.value='';
			}
   }
   else if ( bc.value.length >= 13){
		var next_bc = bc.value.substring(13);
		bc.value = next_bc;
   }
}

function getProduct(){

}
function insertAfter(newElement,targetElement) {
	//target is what you want it to go after. Look for this elements parent.
	var parent = targetElement.parentNode;
	 
	//if the parents lastchild is the targetElement...
	if(parent.lastchild == targetElement) {
	//add the newElement after the target element.
	parent.appendChild(newElement);
	} else {
	// else the target has siblings, insert the new element between the target and it's next sibling.
	parent.insertBefore(newElement, targetElement.nextSibling);
	}
}

var notWhitespace = /\S/;
function cleanWhitespace(node) {
  for (var x = 0; x < node.childNodes.length; x++) {
    var childNode = node.childNodes[x]
    if ((childNode.nodeType == 3)&&(!notWhitespace.test(childNode.nodeValue))) {
// that is, if it's a whitespace text node
      node.removeChild(node.childNodes[x])
      x--
    }
    if (childNode.nodeType == 1) {
// elements can have text child nodes of their own
      cleanWhitespace(childNode)
    }
  }
}
 
