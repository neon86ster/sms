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
	}else{
		alert("Please check your information tablename and edit id!!");
	}
}
function set_editData(table,id){
	var d;
	if(table=="s_group"){
		d = this.getFormParaValue('../user.xml','s_group');
		d = d+"&method=edit";
		postReturnText("add_guser.php",d,"tableDisplay");
	}else if(table=="s_user"){
		d = this.getFormParaValue('../user.xml','s_user');
		d = d+"&method=edit";
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
	getReturnText(url,d,'tableDisplay');
}