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
				if(document.getElementById("loading")!=null){
					document.getElementById("loading").style.display="none";
				}
				if(document.getElementById("spLine")!=null){
					document.getElementById("spLine").style.display="block";
				}
			}else{
				if(document.getElementById("loading")!=null){
					document.getElementById("loading").style.display="block";
				}
				if(document.getElementById("spLine")!=null){
					document.getElementById("spLine").style.display="none";
				}
			}
        }
       
        objRequest.open("GET", url+"?"+ranDom+"&"+data+this.getOwnUserId(""));
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
				if(document.getElementById("loading")!=null){
					document.getElementById("loading").style.display="none";
				}
				if(document.getElementById("spLine")!=null){
					document.getElementById("spLine").style.display="block";
				}
			}else{
				if(document.getElementById("loading")!=null){
					document.getElementById("loading").style.display="block";
				}
				if(document.getElementById("spLine")!=null){
					document.getElementById("spLine").style.display="none";
				}
			}
        }
       
       	objRequest.open("POST",url);
	objRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	objRequest.send(data+"&"+ranDom+this.getOwnUserId(""));
        
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
    var i,t="";
   var n="";
    //var show = document.getElementById("show");
    
    for(i=0; i<e.length; i++) {
		if(e[i].getAttribute('idfields')!="yes"&&e[i].getAttribute('showinform')!="no"){
			 n = e[i].getAttribute('name');
			 //alert(n);
		       if(i)
		         t+= "&";
		      if(e[i].getAttribute('formtype')!="checkbox"&&e[i].getAttribute('formtype')!="password"){
		     	 t += n+"="+document.getElementById(n).value;
		      } else if(e[i].getAttribute('formtype')=="checkbox"&&document.getElementById(n).checked){
		      	 t += n+"=1";
		      } else if(e[i].getAttribute('formtype')=="checkbox"&&!document.getElementById(n).checked){
		      	 t += n+"=0";
		      }else if(e[i].getAttribute('formtype')=="password"&&document.getElementById("add").value==" add "){
		      	 t += "pass="+document.getElementById("pass").value;
		      	 t += "&rpass="+document.getElementById("rpass").value;
		      }else if(e[i].getAttribute('formtype')=="password"&&document.getElementById("add").value==" save change "){
		      	 t += "&pass="+document.getElementById("newpass").value;
		      	 t += "&rpass="+document.getElementById("rnewpass").value;
		      }
		}
    }
   t+="&formname="+tableName;
   t+="&add="+document.getElementById("add").value;
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

function editData(table,id){
	var d='';
	var showInactive='';
	var showDetail='';
	var search='';
	var order='';
	var page='';
	
	//alert(document.getElementById('show_inactive'));
	if(document.getElementById('show_inactive')!=null && document.getElementById('show_inactive').checked == true){
		showInactive= '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if(document.getElementById('search')!=null && document.getElementById('search').value != ''){
	//alert(document.getElementById('search').value);
		search= '&where='+document.getElementById('search').value;
	}
	if (document.getElementById('show_detail')!=null&&document.getElementById('show_detail').checked){
		//alert(document.getElementById('show_detail').value);
		showDetail= '&show_detail='+document.getElementById('show_detail').value;
	}
	if(document.getElementById('order')!=null && document.getElementById('order').value != ''){
	//alert(document.getElementById('search').value);
		order= '&order='+document.getElementById('order').value;
	}
	if(document.getElementById('page')!=null && document.getElementById('page').value != ''){
	//alert(document.getElementById('search').value);
		page= '&page='+document.getElementById('page').value;
	}
	d = "table="+table+"&id="+id+showInactive+search+showDetail+order+page;
	
	if(table=="s_group"){
		postReturnText("add_guser.php",d,"tableDisplay");
	}else if(table=="s_user"){
		postReturnText("add_user.php",d,"tableDisplay");
	}else{
		alert("Please check your information tablename and edit id!!");
	}
}
function set_editData(table,id){
	var d='';
	var phpSql='';
	if (document.getElementById('phpSql')!=null&&document.getElementById('phpSql').value){
		//alert(document.getElementById('phpSql').value);
		 phpSql = document.getElementById('phpSql').value;
	}
	if(table=="s_group"){
		d = this.getFormParaValue('../user.xml','s_group');
		d = d+"&method=edit"+phpSql;
		postReturnText("add_guser.php",d,"tableDisplay");
	}else if(table=="s_user"){
		d = this.getFormParaValue('../user.xml','s_user');
		d = d+"&method=edit"+phpSql;
		postReturnText("add_user.php",d,"tableDisplay");
	}else{
		alert("Please check your information tablename and edit id!!");
	}
}
function set_insertData(table){
	var d;
	if(table=="s_group"){
		d = this.getFormParaValue('../user.xml',table);
   		postReturnText("add_guser.php",d,"tableDisplay");  
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
  	var phpSql='';
	if (document.getElementById('phpsql')!=null&&document.getElementById('phpsql').value){
		//alert(document.getElementById('phpSql').value);
		 phpSql = document.getElementById('phpsql').value;
	}
	if(table=="s_user"){
		var total=0;
		for(var i=1; i<=cnt; i++) {
			grs = document.getElementsByName('grs['+i+']');
			for(var j=0; j<2; j++){
				if(eval("grs["+j+"]")!=undefined&&eval("grs["+j+"].checked")) {
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
		 d+="&lastgroupid="+cnt;
		 d+="&previewdate="+document.getElementById('previewdate').value;
		 d+="&viewdateafter="+document.getElementById('viewdateafter').value;
		 d+="&preeditdate="+document.getElementById('preeditdate').value;
		 d+="&editdateafter="+document.getElementById('editdateafter').value;
		d+="&rsvnviewchk="+document.getElementById("rsvnviewchk").value;
		d+="&rsvneditchk="+document.getElementById("rsvneditchk").value;
		 d+=phpSql;
		 //alert(d);
   		 postReturnText("add_user.php",d,"tableDisplay");
	}
}
function updatePagePermission(groupcnt,pagecnt){
	var d="add="+document.getElementById("add").value;
  	var nParams = new Array();
	var total=0;
	var pParam='';
	var debug="";
	if(document.getElementById("page_index").value!="cc"){
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
	}else{
		for(var i=1;i<=groupcnt;i++) {
			var tcms = document.getElementById('utcms['+i+']').value;
			var srchk = document.getElementById('srchk['+i+']');
			var apptdatechk = document.getElementById('apptdatechk['+i+']');
			if(tcms>0)
				d+="&tcms["+i+"]="+tcms;
			if(srchk.checked){d+="&srchk["+i+"]="+1;}
			else{d+="&srchk["+i+"]="+0;}
			if(apptdatechk.checked){d+="&apptdatechk["+i+"]="+1;}
			else{d+="&apptdatechk["+i+"]="+0;}
		}
	}
	d+="&last_groupid="+groupcnt+"&last_pageid="+pagecnt;
	d+="&pageindex="+document.getElementById("page_index").value;
	/*var di = document.getElementById("show");
   	di.innerHTML = debug;*/
	//alert(d);
   	postReturnText('manage_gpage.php',d,"tableDisplay");
}
function selectboxSearch(url,data) {
	var d='';
	var showInactive='';
	var order='';
	var page='';
	
	if(document.getElementById('show_inactive').checked == true){
		showInactive= '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if(document.getElementById('order')!=null && document.getElementById('order').value != ''){
	//alert(document.getElementById('search').value);
		order= '&order='+document.getElementById('order').value;
	}
	if(document.getElementById('page')!=null && document.getElementById('page').value != ''){
	//alert(document.getElementById('search').value);
		page= '&page='+document.getElementById('page').value;
	}
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
	d+=showInactive+order+page;
	getReturnText(url,d,'tableDisplay');
}
function setEnable(table,id,active){
	var d='';
	var showInactive='';
	var showDetail='';
	var search='';
	var page='';
	var order='';
	
	//alert(document.getElementById('show_inactive'));
	if(document.getElementById('show_inactive')!=null && document.getElementById('show_inactive').checked == true){
		showInactive= '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if(document.getElementById('search')!=null && document.getElementById('search').value != ''){
	//alert(document.getElementById('search').value);
		search= '&where='+document.getElementById('search').value;
	}
	if (document.getElementById('show_detail')!=null&&document.getElementById('show_detail').checked){
		//alert(document.getElementById('show_detail').value);
		showDetail= '&show_detail='+document.getElementById('show_detail').value;
	}
	if(document.getElementById('order')!=null && document.getElementById('order').value != ''){
	//alert(document.getElementById('search').value);
		order= '&order='+document.getElementById('order').value;
	}
	if(document.getElementById('page')!=null && document.getElementById('page').value != ''){
	//alert(document.getElementById('search').value);
		page= '&page='+document.getElementById('page').value;
	}
	
	d = "table="+table+"&method=setactive&id="+id+"&active="+active+showInactive+search+showDetail+page+order;
	if(table=="s_user"){
		postReturnText("add_user.php",d,"tableDisplay");
	}else if(table=="s_group"){
		postReturnText("add_guser.php",d,"tableDisplay");
	}else{
		alert("Please check your information tablename for insert data!!");
	}
}
function showInactive(url) {
	var d='';
	
	if(document.getElementById('show_inactive').checked == true){
		d += '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if(document.getElementById('branch_id')!=null){
		d += '&branch_id='+document.getElementById('branch_id').value;
	}
	if (document.getElementById('show_detail')!=null&&document.getElementById('show_detail').checked){
		//alert(document.getElementById('show_detail').value);
		d += '&show_detail='+document.getElementById('show_detail').value;
	}
	
	getReturnText(url,d,'tableDisplay');
}