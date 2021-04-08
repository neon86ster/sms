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

function hiddenLeftFrame(path) {
  if(path!=""){
    var fs = window.top.document.getElementsByTagName("frameset")[1];
    var spLine = document.getElementById("spLine");
    if(fs.cols == "220,*"){
  	   fs.cols = "0,*";
	   spLine.src = path+"/bar_open.gif";
    }
    else{
  	   fs.cols = "220,*";
 	   spLine.src = path+"/bar_close.gif";
    }
  }
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
			if(e[i].getAttribute('formtype')!="checkbox"&&e[i].getAttribute('formtype')!="date"){
				t += n+"="+document.getElementById(n).value;
			} else if(e[i].getAttribute('formtype')=="checkbox"&&document.getElementById(n).checked){
				t += n+"=1";
			} else if(e[i].getAttribute('formtype')=="checkbox"&&!document.getElementById(n).checked){
				t += n+"=0";
			}else if(e[i].getAttribute('formtype')=="date"){
				t += "hidden_"+n+"="+document.getElementById("hidden_"+n).value;
				t += "&"+n+"="+document.getElementById(n).value;
		 	}
		}
    }
   t+="&formname="+tableName;
   t+="&add="+document.getElementById("add").value;
   if(document.getElementById("trmCategoryId")!=null){t+="&trm_category_id="+document.getElementById("trmCategoryId").value;}
   if(document.getElementById("id")!=null&&document.getElementById("add").value==" save change "){t+="&id="+document.getElementById("id").value;}
   //show.innerHTML = t;
   return t;
}
function chkbutton() {
   alert("Check");  
}

function editData(table,id){
	var d = "table="+table+"&id="+id;
	if(table=="s_group"){
		postReturnText("add_guser.php",d,"tableDisplay");
	}else if(table=="s_user"){
		postReturnText("add_user.php",d,"tableDisplay");
	}else if(table=="m_membership"){
		postReturnText("add_membershipinfo.php",d,"tableDisplay");
	}else  if(table=="g_gift"){
		postReturnText("add_giftinfo.php",d,"tableDisplay");  
	}else{
		alert("Please check your information tablename and edit id!!");
	}
}
function set_editData(table,id){
	var d;
	if(table=="a_company_info"){
		d = this.getFormParaValue('../spamg.xml','a_company_info');
		d = d+"&method=edit";
		postReturnText("add_companyinfo.php",d,"tableDisplay");
	}else if(table=="m_membership"){
		d = this.getFormParaValue('../giftinfo/spamg.xml',table);
		d = d+"&method=edit";
		var url = "add_membershipinfo.php?"+d;
		alert(url);
   		window.location.href = url;
	}else if(table=="g_gift"){
		d = this.getFormParaValue('../spamg.xml',table);
		d = d+"&method=edit";
   		postReturnText("add_giftinfo.php",d,"tableDisplay");  
	}else{
		alert("Please check your information tablename and edit id!!");
	}
}
function set_insertData(table){
	var d;
	if(table=="g_gift"){
		d = this.getFormParaValue('spamg.xml',table);
		postReturnText("add_giftinfo.php",d,"tableDisplay");  
	}else{
		alert("Please check your information tablename for insert data!!");
	}
}
function setEnable(table,id,active){
	var d='';
	var showInactive='';
	var showDetail='';
	var search='';
	var page='';
	var order='';
	var category_id='';
	
	//alert(document.getElementById('show_inactive'));
	if(document.getElementById('show_inactive')!=null && document.getElementById('show_inactive').checked == true){
		showInactive= '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if(document.getElementById('search')!=null && document.getElementById('search').value != ''){
	//alert(document.getElementById('search').value);
		search= '&where='+document.getElementById('search').value;
	}
	if(document.getElementById('order')!=null && document.getElementById('order').value != ''){
	//alert(document.getElementById('search').value);
		order= '&order='+document.getElementById('order').value;
	}
	if(document.getElementById('page')!=null && document.getElementById('page').value != ''){
	//alert(document.getElementById('search').value);
		page= '&page='+document.getElementById('page').value;
	}
	if(document.getElementById('category_id')!=null && document.getElementById('category_id').value != ''){
	//alert(document.getElementById('search').value);
		category_id= '&category_id='+document.getElementById('category_id').value;
	}
	
	d+="table="+table+"&id="+id+"&expired="+active+"&method=setactive"+showInactive+search+order+page+category_id;
	
	if(table=="m_membership"){
		location.href="add_membershipinfo.php?"+d;
	}else{
		alert("Please check your information tablename for insert data!!");
	}
}
function gotoSearch(url) {
	var d = 'where='+document.getElementById('search').value;
	getReturnText(url,d,'tableDisplay');
}
function set_insertUserData(table,cnt){
	var d = this.getFormParaValue('../user.xml',"s_user");
	var grs;
  	var nParams = new Array();
	if(table=="s_user"){
		var total=0;
		for(var i=0; i < cnt; i++) {
			grs = document.getElementsByName('grs['+i+']');
			for(var j=0; j < 3; j++){
				if(grs[j].checked) {
					var pParam = grs[j].name+"["+j+"]=";
					pParam+=encodeURIComponent( grs[j].value );
					nParams.push( pParam );
					total++;
				}
			}
		}
		if(total>0){d+="&"}
		// nParams.join( "&" ); //convert Array to String
		 d+=nParams.join( "&" );
		// alert(d);
   		postReturnText("add_user.php",d,"tableDisplay");
	}
}
function updatePagePermission(groupcnt,pagecnt){
	var d="add="+document.getElementById("add").value;
  	var nParams = new Array();
	var total=0;
	var pParam='';
	var debug="";
	for(var i=1;i<=groupcnt;i++) {
		for(var j=1; j<=pagecnt; j++) {
			var prs = new Array();
			prs = document.getElementsByName('prs['+i+']['+j+']');
			pParam='';
			for(var k=0; k < 2; k++){
				if(eval("prs["+k+"]")!=undefined&&eval('prs['+k+'].checked')) {
					if(prs[k].value==0){
						pParam = prs[k].name+"=";
						pParam+=encodeURIComponent(0);
						//debug += "do 0 "+pParam;
					}
					if(prs[k].value==1){
						pParam = prs[k].name+"=";
						pParam+=encodeURIComponent(1);
						//debug += " do 1 "+pParam;
					}
				}
			}
			if(pParam!="") {
				nParams.push( pParam );
				total++;
			}
		}
	}
	if(total>0){d+="&"}
	// nParams.join( "&" ); //convert Array to String
	d+=nParams.join( "&" );
	d+="&last_groupid="+groupcnt+"&last_pageid="+pagecnt;
	d+="&pageindex="+document.getElementById("page_index").value;
	/*var di = document.getElementById("show");
   	di.innerHTML = debug;*/
	//alert(d);
   	postReturnText('manage_gpage.php',d,"tableDisplay");
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
	if(document.getElementById('category_id')!=null){
		d += '&category_id='+document.getElementById('category_id').value;
	} 
	if(document.getElementById('gifttype_id')!=null){
		d += '&gifttype_id='+document.getElementById('gifttype_id').value;
	} 
	getReturnText(url,d,'tableDisplay');
}
/**
 *	Function resetDateBox()
 *	For reset date on interface to default value.
 *	@param - id : id of date box.
 *	@param - hidden_id : id of hidden date.
 *	@param - value : reset value.
 */
function resetDateBox(id,hidden_id,value){
	document.getElementById(id).value = value;
	document.getElementById(hidden_id).value = "00000000";
}