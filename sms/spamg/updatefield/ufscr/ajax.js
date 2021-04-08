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
			if(i)
				t+= "&";
			if(e[i].getAttribute('formtype')!="checkbox"&&e[i].getAttribute('formtype')!="date"){
				t += n+"="+document.getElementById(n).value.replace("+","%2B");
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
	if(table=="bl_branchinfo"){
		postReturnText("add_branchinfo.php",d,"tableDisplay");
	}else if(table=="bl_room"){
		d += '&branchId='+document.getElementById('branch_id').value;
		postReturnText("add_room.php",d,"tableDisplay");
	}else if(table=="l_employee"){
		if(document.getElementById('branch_id')!=null){
			d += '&branchId='+document.getElementById('branch_id').value;
		}
		
		if(document.getElementById('emp_department_id')!=null){
			d += '&emp_departmentId='+document.getElementById('emp_department_id').value;
		} 
		if(document.getElementById('city_id')!=null){
			d += '&cityId='+document.getElementById('city_id').value;
		} 
		postReturnText("add_emp.php",d,"tableDisplay");
	}else if(table=="cl_product"){
		d += '&pd_categoryId='+document.getElementById('pd_category_id').value;
		postReturnText("add_product.php",d,"tableDisplay");
	}else if(table=="cl_product_category"){
		postReturnText("add_productcategory.php",d,"tableDisplay");
	}else if(table=="db_trm1"){
		var data = "table=db_trim&id="+id+search+showInactive+page+order;
		postReturnText("add_bath.php",data,"tableDisplay");
	}else if(table=="db_trm2"){
		var data = "table=db_trim&id="+id+search+showInactive+page+order;
		postReturnText("add_facial.php",data,"tableDisplay");
	}else if(table=="db_trm3"){
		var data = "table=db_trim&id="+id+search+showInactive+page+order;
		postReturnText("add_massage.php",data,"tableDisplay");
	}else if(table=="db_package"){
		var data = "table=db_package&id="+id+search+showInactive+page+order;
		postReturnText("add_package.php",data,"tableDisplay");
	}else if(table=="db_trm4"){
		var data = "table=db_trim&id="+id+search+showInactive+page+order;
		postReturnText("add_scrub.php",data,"tableDisplay");
	}else if(table=="db_trm5"){
		var data = "table=db_trim&id="+id+search+showInactive+page+order;
		postReturnText("add_wrap.php",data,"tableDisplay");
	}else if(table=="dl_nationality"){
		postReturnText("add_nationality.php",d,"tableDisplay");
	}else if(table=="l_bankname"){
		postReturnText("add_bank.php",d,"tableDisplay");
	}else if(table=="l_paytype"){
		postReturnText("add_paytype.php",d,"tableDisplay");
	}else if(table=="l_marketingcode"){
		postReturnText("add_cfd.php",d,"tableDisplay");
	}else if(table=="al_bookparty"){
		if(document.getElementById('bp_category_id')!=null){
			d += '&bpCategoryId='+document.getElementById('bp_category_id').value;
		} 
		if(document.getElementById('city_id')!=null){
			d += '&cityId='+document.getElementById('city_id').value;
		} 
		postReturnText("add_bp.php",d,"tableDisplay");
	
	}else if(table=="al_accomodations"){
		if(document.getElementById('city_id')!=null){
			d += '&cityId='+document.getElementById('city_id').value;
		} 
		postReturnText("add_acc.php",d,"tableDisplay");
	}else if(table=="al_bookparty_category"){
		postReturnText("add_bpcategory.php",d,"tableDisplay");
	}else if(table=="gl_gifttype"){
		postReturnText("add_gcreason.php",d,"tableDisplay");
	}else if(table=="l_mkcode_category"){
		postReturnText("add_cfdcategory.php",d,"tableDisplay");
	}else{
		alert("Please check your information tablename and edit id!!");
	}
}
function edithotelcms(table,id){
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
	
	if(document.getElementById('city_id')!=null){
			d += '&cityId='+document.getElementById('city_id').value;
	} 
	postReturnText("add_hotelcms.php",d,"tableDisplay");
	
}
function setedithotelcms(table,id){
	var d;
	var phpSql='';
	if (document.getElementById('phpSql')!=null&&document.getElementById('phpSql').value){
		 phpSql = document.getElementById('phpSql').value;
	}
	d = this.getFormParaValue('../object.xml',"hotelcms");
	d = d+"&method=edit"+phpSql;
	postReturnText("add_hotelcms.php",d,"tableDisplay");
}
function set_editData(table,id){
	var d;
	var phpSql='';
	if (document.getElementById('phpSql')!=null&&document.getElementById('phpSql').value){
		//alert(document.getElementById('phpSql').value);
		 phpSql = document.getElementById('phpSql').value;
	}
	if(table=="bl_branchinfo"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
	    //alert(d);
		postReturnText("add_branchinfo.php",d,"tableDisplay");
		//gotoURL('index.php');
	}else if(table=="bl_room"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_room.php",d,"tableDisplay");
	}else if(table=="al_accomodations"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_acc.php",d,"tableDisplay");
	}else if(table=="al_bookparty"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_bp.php",d,"tableDisplay");
	}else if(table=="al_bookparty_category"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_bpcategory.php",d,"tableDisplay");
	}else if(table=="l_employee"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_emp.php",d,"tableDisplay");
	}else if(table=="cl_product"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_product.php",d,"tableDisplay");
	}else if(table=="cl_product_category"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_productcategory.php",d,"tableDisplay");
	}else if(table=="db_trm1"){
	    d = this.getFormParaValue('../object.xml',"db_trm");
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_bath.php",d,"tableDisplay");
	}else if(table=="db_trm2"){
	    d = this.getFormParaValue('../object.xml',"db_trm");
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_facial.php",d,"tableDisplay");
	}else if(table=="db_trm3"){
	    d = this.getFormParaValue('../object.xml',"db_trm");
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_massage.php",d,"tableDisplay");
	}else if(table=="db_trm4"){
	    d = this.getFormParaValue('../object.xml',"db_trm");
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_scrub.php",d,"tableDisplay");
	}else if(table=="db_trm5"){
	    d = this.getFormParaValue('../object.xml',"db_trm");
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_wrap.php",d,"tableDisplay");
	}else if(table=="dl_nationality"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_nationality.php",d,"tableDisplay");
	}else if(table=="l_bankname"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_bank.php",d,"tableDisplay");
	}else if(table=="l_paytype"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_paytype.php",d,"tableDisplay");
	}else if(table=="l_marketingcode"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_cfd.php",d,"tableDisplay");
	}else if(table=="gl_gifttype"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_gcreason.php",d,"tableDisplay");
	}else if(table=="l_mkcode_category"){
	    d = this.getFormParaValue('../object.xml',table);
	    d = d+"&method=edit"+phpSql;
		postReturnText("add_cfdcategory.php",d,"tableDisplay");
	}else{
		alert("Please check your information tablename and edit id!!");
	}
}
function set_insertData(table){
	var d;
	if(table=="bl_branchinfo"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_branchinfo.php",d,"tableDisplay");
	}else if(table=="bl_room"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_room.php",d,"tableDisplay");
	}else if(table=="al_bookparty"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_bp.php",d,"tableDisplay");
	}else if(table=="al_accomodations"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_acc.php",d,"tableDisplay");
	}else if(table=="al_bookparty_category"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_bpcategory.php",d,"tableDisplay");
	}else if(table=="l_employee"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_emp.php",d,"tableDisplay");
	}else if(table=="cl_product"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_product.php",d,"tableDisplay");
	}else if(table=="cl_product_category"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_productcategory.php",d,"tableDisplay");
	}else if(table=="db_trm1"){
	    d = this.getFormParaValue('../object.xml',"db_trm");
	    postReturnText("add_bath.php",d,"tableDisplay");
	}else if(table=="db_trm2"){
	    d = this.getFormParaValue('../object.xml',"db_trm");
	    postReturnText("add_facial.php",d,"tableDisplay");
	}else if(table=="db_trm3"){
	    d = this.getFormParaValue('../object.xml',"db_trm");
	    postReturnText("add_massage.php",d,"tableDisplay");
	}else if(table=="db_trm4"){
	    d = this.getFormParaValue('../object.xml',"db_trm");
	    postReturnText("add_scrub.php",d,"tableDisplay");
	}else if(table=="db_trm5"){
	    d = this.getFormParaValue('../object.xml',"db_trm");
	    postReturnText("add_wrap.php",d,"tableDisplay");
	}else if(table=="dl_nationality"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_nationality.php",d,"tableDisplay");
	}else if(table=="l_bankname"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_bank.php",d,"tableDisplay");
	}else if(table=="l_paytype"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_paytype.php",d,"tableDisplay");
	}else if(table=="l_marketingcode"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_cfd.php",d,"tableDisplay");
	}else if(table=="l_mkcode_category"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_cfdcategory.php",d,"tableDisplay");
	}else if(table=="gl_gifttype"){
	    d = this.getFormParaValue('../object.xml',table);
		postReturnText("add_gcreason.php",d,"tableDisplay");
	}else{
		alert("Please check your information tablename for insert data!!");
	}
}
function gotoSearch(url) {
	var d = 'where='+document.getElementById('search').value;
	getReturnText(url,d,'tableDisplay');
}
function selectboxSearch(url) {
	var d='';
	var showInactive='';
	var order='';
	var page='';
	
	if(document.getElementById('show_inactive')!=null&&document.getElementById('show_inactive').checked == true){
		showInactive= '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if(document.getElementById('branch_id')!=null){
		d += '&branch_id='+document.getElementById('branch_id').value;
	} 
	if(document.getElementById('emp_department_id')!=null){
		d += '&emp_department_id='+document.getElementById('emp_department_id').value;
	} 
	if (document.getElementById('city_id')!=null){
		d += '&city_id='+document.getElementById('city_id').value;
	}
	if (document.getElementById('pd_category_id')!=null){
		d += '&pd_category_id='+document.getElementById('pd_category_id').value;
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
	/*if(document.getElementById('page')!=null && document.getElementById('page').value != ''){
	//alert(document.getElementById('search').value);
		page = '&page='+document.getElementById('page').value;
	}*/
	d+=showInactive+order+page;
	//alert(d);
	getReturnText(url,d,'tableDisplay');
}
function showInactive(url) {
	var d='';
	if(document.getElementById('branch_id')!=null){
		d += '&branch_id='+document.getElementById('branch_id').value;
	}
	if(document.getElementById('emp_department_id')!=null){
			d += '&emp_department_id='+document.getElementById('emp_department_id').value;
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
	getReturnText(url,d,'tableDisplay');
}
function setEnable(table,id,active){
	var d='';
	var showInactive='';
	var showDetail='';
	var search='';
	var order='';
	var page='';
	
	if(document.getElementById('show_inactive').checked == true){
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
	d+="table="+table+"&id="+id+"&active="+active+"&method=setactive"+showInactive+showDetail+search+page+order;
	
	if(table=="bl_branchinfo"){
		postReturnText("add_branchinfo.php",d,'tableDisplay');
	}else if(table=="bl_room"){
		if(document.getElementById('branch_id')!=null){
			d += '&branchId='+document.getElementById('branch_id').value;
		} 
		//alert(d);
		postReturnText("add_room.php",d,'tableDisplay');
	}else if(table=="al_bookparty"){
		if(document.getElementById('bp_category_id')!=null){
			d += '&bpCategoryId='+document.getElementById('bp_category_id').value;
		} 
		if(document.getElementById('city_id')!=null){
			d += '&cityId='+document.getElementById('city_id').value;
		} 
		postReturnText("add_bp.php",d,'tableDisplay');
	}else if(table=="al_accomodations"){
		if(document.getElementById('city_id')!=null){
			d += '&cityId='+document.getElementById('city_id').value;
		} 
		postReturnText("add_acc.php",d,'tableDisplay');
	}else if(table=="l_employee"){
		if(document.getElementById('branch_id')!=null){
			d += '&branchId='+document.getElementById('branch_id').value;
		}
		
		if(document.getElementById('emp_department_id')!=null){
			d += '&emp_departmentId='+document.getElementById('emp_department_id').value;
		} 
		if(document.getElementById('city_id')!=null){
			d += '&cityId='+document.getElementById('city_id').value;
		} 
		postReturnText("add_emp.php",d,'tableDisplay');
	}else if(table=="cl_product"){
		if (document.getElementById('pd_category_id')!=null){
			d += '&pd_categoryId='+document.getElementById('pd_category_id').value;
		}
		
		postReturnText("add_product.php",d,'tableDisplay');
	}else if(table=="cl_product_category"){
		postReturnText("add_productcategory.php",d,'tableDisplay');
	}else if(table=="db_trm1"){
		d="table=db_trm&id="+id+"&active="+active+"&method=setactive"+showInactive+search+page+order;
		
		postReturnText("add_bath.php",d,'tableDisplay');
	}else if(table=="db_trm2"){
		d="table=db_trm&id="+id+"&active="+active+"&method=setactive"+showInactive+search+page+order;
		postReturnText("add_facial.php",d,'tableDisplay');
		
	}else if(table=="db_trm3"){
		d="table=db_trm&id="+id+"&active="+active+"&method=setactive"+showInactive+search+page+order;
		postReturnText("add_massage.php",d,'tableDisplay');
		
	}else if(table=="db_package"){
		d="table=db_package&id="+id+"&active="+active+"&method=setactive"+showInactive+search+page+order;
		postReturnText("add_package.php",d,'tableDisplay');
		
	}else if(table=="db_trm4"){
		d="table=db_trm&id="+id+"&active="+active+"&method=setactive"+showInactive+search+page+order;
		postReturnText("add_scrub.php",d,'tableDisplay');
		
	}else if(table=="db_trm5"){
		d="table=db_trm&id="+id+"&active="+active+"&method=setactive"+showInactive+search+page+order;
		postReturnText("add_wrap.php",d,'tableDisplay');
		
	}else if(table=="dl_nationality"){
		postReturnText("add_nationality.php",d,"tableDisplay");
	}else if(table=="l_bankname"){
		postReturnText("add_bank.php",d,"tableDisplay");
	}else if(table=="l_paytype"){
		postReturnText("add_paytype.php",d,"tableDisplay");
	}else if(table=="al_bookparty_category"){
		postReturnText("add_bpcategory.php",d,'tableDisplay');
	}else if(table=="l_marketingcode"){
		postReturnText("add_cfd.php",d,'tableDisplay');
	}else if(table=="l_mkcode_category"){
		postReturnText("add_cfdcategory.php",d,'tableDisplay');
	}else if(table=="gl_gifttype"){
		postReturnText("add_gcreason.php",d,'tableDisplay');
	}else{
		alert("Please check your information tablename for insert data!!");
	}
}
//Add by natt in 6-Jan-2009 Auto update tax 
function setTax(table,id,active){
	var d='';
	var showInactive='';
	var showDetail='';
	var search='';
	var order='';
	var page='';
	
	if(document.getElementById('search')!=null && document.getElementById('search').value != ''){
	//alert(document.getElementById('search').value);
		search= '&where='+document.getElementById('search').value;
	}
	if(document.getElementById('show_inactive').checked == true){
		showInactive= '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if (document.getElementById('show_detail')!=null&&document.getElementById('show_detail').checked){
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
	d+="table="+table+"&id="+id+"&active="+active+"&method=settax"+showInactive+showDetail+search+page+order;
	if(table=="cl_product"){
		if (document.getElementById('pd_category_id')!=null){
			d += '&pd_categoryId='+document.getElementById('pd_category_id').value;
		}
		
		postReturnText("add_product.php",d,'tableDisplay');
	}else{
		alert("Please check your information tablename for insert data!!");
	}
}
//Add by natt in 17-Jan-2009 Auto update set commission 
function setCommission(table,id,active){
	var d='';
	var showInactive='';
	var showDetail='';
	if(document.getElementById('show_inactive').checked == true){
		showInactive= '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if (document.getElementById('show_detail')!=null&&document.getElementById('show_detail').checked){
		showDetail= '&show_detail='+document.getElementById('show_detail').value;
	}
	d+="table="+table+"&id="+id+"&active="+active+"&method=setcommission"+showInactive+showDetail;
	if(table=="cl_product_category"){
		postReturnText("add_productcategory.php",d,'tableDisplay');
	}else{
		alert("Please check your information tablename for insert data!!");
	}
}
//Add by natt in 26-Jan-2009 Auto update set payment 
function setPayment(table,id,active){
	var d='';
	var showInactive='';
	var showDetail='';
	if(document.getElementById('show_inactive').checked == true){
		showInactive= '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if (document.getElementById('show_detail')!=null&&document.getElementById('show_detail').checked){
		showDetail= '&show_detail='+document.getElementById('show_detail').value;
	}
	d+="table="+table+"&id="+id+"&active="+active+"&method=setpayment"+showInactive+showDetail;
	if(table=="cl_product_category"){
		postReturnText("add_productcategory.php",d,'tableDisplay');
	}else{
		alert("Please check your information tablename for insert data!!");
	}
}
//Add by natt in 26-Jan-2009 Auto update set product positive/negative value
function setpdValue(table,id,active){
	var d='';
	var showInactive='';
	var showDetail='';
	if(document.getElementById('show_inactive').checked == true){
		showInactive= '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if (document.getElementById('show_detail')!=null&&document.getElementById('show_detail').checked){
		showDetail= '&show_detail='+document.getElementById('show_detail').value;
	}
	d+="table="+table+"&id="+id+"&active="+active+"&method=setvalue"+showInactive+showDetail;
	if(table=="cl_product_category"){
		postReturnText("add_productcategory.php",d,'tableDisplay');
	}else{
		alert("Please check your information tablename for insert data!!");
	}
}
//Add by natt in 6-Jan-2009 Auto update servicecharge 
function setSc(table,id,active){
	var d='';
	var showInactive='';
	var showDetail='';
	var search='';
	var order='';
	var page='';
	
	if(document.getElementById('search')!=null && document.getElementById('search').value != ''){
	//alert(document.getElementById('search').value);
		search= '&where='+document.getElementById('search').value;
	}
	if(document.getElementById('show_inactive').checked == true){
		showInactive= '&show_inactive='+document.getElementById('show_inactive').checked;
	}
	if (document.getElementById('show_detail')!=null&&document.getElementById('show_detail').checked){
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
	d+="table="+table+"&id="+id+"&active="+active+"&method=setsc"+showInactive+showDetail+search+page+order;
	if(table=="cl_product"){
		if (document.getElementById('pd_category_id')!=null){
			d += '&pd_categoryId='+document.getElementById('pd_category_id').value;
		}
		postReturnText("add_product.php",d,'tableDisplay');
	}else{
		alert("Please check your information tablename for insert data!!");
	}
}
//Modified by natt in 4-Dec-2008 add query string for append treatement 
function set_insertpackageData(cnt,appenstrmcnt){
	var d = this.getFormParaValue('../object.xml',"db_package");
	var phpSql= document.getElementById('phpSql').value;
  	var nParams = new Array();
	var total=0;
	for(var i=1; i<=cnt; i++) {
		var trmrs = document.getElementById('ctrmrs'+i+'');
		var pParam = 'ctrmrs'+i+'=';
		pParam+=encodeURIComponent( trmrs.options[trmrs.selectedIndex].value );
		nParams.push( pParam );
		total++;
	}
	for(var i=0; i<appenstrmcnt; i++) {
		var trmrs = document.getElementById('appendtrmrs'+i+'');
		pParam = 'appendtrmrs'+i+'=';
		pParam+=encodeURIComponent( trmrs.options[trmrs.selectedIndex].value );
		nParams.push( pParam );
		total++;
	}
	if(total>0){d+="&"}
	// nParams.join( "&" ); //convert Array to String
	d+=nParams.join( "&" );
	d+="&lastgroupid="+cnt+"&appenstrmcnt="+appenstrmcnt+phpSql;
	//alert(d);
   	postReturnText("add_package.php",d,"tableDisplay");
}
//Create by natt in 4-Dec-2008 function for add append treatement row
function addTrm(){
	var newTrm = document.getElementById('newTrm');
	var package_id = document.getElementById('package_id');
	var d = "id="+package_id.value+"&package_id="+package_id.value+"&newtrm="+encodeURIComponent( newTrm.options[newTrm.selectedIndex].value );
	d += "&tadd=tadd";
   	postReturnText("add_package.php",d,"tableDisplay");
}
function checkTest(){
	alert("Check TEst");
}
