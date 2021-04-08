
function getFormParaValue(dname,tableName) {
   	xmlDoc = this.loadXMLDoc(dname);
    var e = xmlDoc.getElementsByTagName(tableName)[0].getElementsByTagName('field');
    var i;
   	var n,t="";
    var frmvalue = "";
    
    for(i=0; i<e.length; i++) {
		if(e[i].getAttribute('idfields')!="yes"&&e[i].getAttribute('showinform')!="no"){
			n = e[i].getAttribute('name');
			if(i)
				t+= "&";
			if(e[i].getAttribute('formtype')=="textarea"){
		    	frmvalue = document.getElementById(n).value.replace(/\+/g,"%2B");
		    	frmvalue = frmvalue.replace(/\n/g,"[br]");
				t += n+"="+frmvalue.replace(/\&/g,"%26");
			} else if(e[i].getAttribute('formtype')!="checkbox"&&e[i].getAttribute('formtype')!="date"){
				frmvalue = document.getElementById(n).value.replace(/\+/g,"%2B");
				t += n+"="+frmvalue.replace(/\&/g,"%26");
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
   //alert(t);
   return t;
}

function editData(table,id){
	var querystr='';
	var showInactive='';
	var showDetail='';
	var search='';
	var order='';
	var page='';
	var sort='';
	var category='';
	var branch='';
	var city='';
	var cmsid = '';
	
	if(document.getElementById('search')!=null && document.getElementById('search').value != ''){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	}
	if(document.getElementById('order')!=null && document.getElementById('order').value != ''){
		order= '&order='+document.getElementById('order').value;
	}
	if(document.getElementById('page')!=null && document.getElementById('page').value != ''){
		page= '&page='+document.getElementById('page').value;
	}
	if(document.getElementById('sort')!=null && document.getElementById('sort').value != ''){
		sort= '&sort='+document.getElementById('sort').value;
	}
	if(document.getElementById('showinactive')!=null && document.getElementById('showinactive').checked == true){
		showInactive= '&showinactive='+document.getElementById('showinactive').checked;
	}
	if(document.getElementById('showdetail')!=null && document.getElementById('showdetail').checked == true){
		showDetail= '&showdetail='+document.getElementById('showdetail').checked;
	}
	if(document.getElementById('categoryid')!=null && document.getElementById('categoryid').value != ''){
		category= '&categoryid='+document.getElementById('categoryid').value;
	}
	if(document.getElementById('branchid')!=null && document.getElementById('branchid').value != ''){
		branch= '&branchid='+document.getElementById('branchid').value;
	}
	if(document.getElementById('cityid')!=null && document.getElementById('cityid').value != ''){
		city= '&cityid='+document.getElementById('cityid').value;
	}
	if(document.getElementById('cmsid')!=null && document.getElementById('cmsid').value != ''){
		cmsid= '&cmsid='+document.getElementById('cmsid').value;
	 }
	
	 querystr += "table="+table+"&id="+id+showInactive+showDetail+search+page+order+sort+cmsid+category+branch+city;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	 gotoURL("addinfo.php?"+querystr);
}
function set_editData(table,id){
	var d='';
	var querystr='';
	if (document.getElementById('querystr')!=null&&document.getElementById('querystr').value){
		querystr = document.getElementById('querystr').value;
	}
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	d = this.getFormParaValue('../object.xml',table);
	d = d+"&method=edit"+querystr;
	gotoURL("addinfo.php?"+d);  
}
function setedithotelcms(table,id){
	var d;
	var querystr='';
	if (document.getElementById('querystr')!=null&&document.getElementById('querystr').value){
		querystr = document.getElementById('querystr').value;
	}
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	d = this.getFormParaValue('../object.xml',"hotelcms");
	d = d+"&method=edit"+querystr;
	gotoURL("addinfo.php?"+d);  
}
function set_insertData(table){
	var d;
	d = this.getFormParaValue('../object.xml',table);
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	d = d+"&pageid="+pageid;
   	gotoURL("addinfo.php?"+d);  
}
function showInactive(url) {
	var d='';
	if(document.getElementById('showinactive').checked == true){
		d += '&showinactive='+document.getElementById('showinactive').checked;
	}
	sortInfo('','1');
}
function showDetail(url) {
	var d='';
	if(document.getElementById('showdetail').checked == true){
		d += '&showdetail='+document.getElementById('showdetail').checked;
	}
	sortInfo('','1');
}
function setEnable(table,id,active){
	var querystr='';
	var showInactive='';
	var showDetail='';
	var search='';
	var order='';
	var page='';
	var sort='';
	var category='';
	var branch='';
	var city='';
	var cmsid = '';
	
	if(document.getElementById('search')!=null && document.getElementById('search').value != ''){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	}
	if(document.getElementById('order')!=null && document.getElementById('order').value != ''){
		order= '&order='+document.getElementById('order').value;
	}
	if(document.getElementById('page')!=null && document.getElementById('page').value != ''){
		page= '&page='+document.getElementById('page').value;
	}
	if(document.getElementById('sort')!=null && document.getElementById('sort').value != ''){
		sort= '&sort='+document.getElementById('sort').value;
	}
	if(document.getElementById('showinactive')!=null && document.getElementById('showinactive').checked == true){
		showInactive= '&showinactive='+document.getElementById('showinactive').checked;
	}
	if(document.getElementById('showdetail')!=null && document.getElementById('showdetail').checked == true){
		showDetail= '&showdetail='+document.getElementById('showdetail').checked;
	}
	if(document.getElementById('categoryid')!=null && document.getElementById('categoryid').value != ''){
		category= '&categoryid='+document.getElementById('categoryid').value;
	}
	if(document.getElementById('branchid')!=null && document.getElementById('branchid').value != ''){
		branch= '&branchid='+document.getElementById('branchid').value;
	}
	if(document.getElementById('cityid')!=null && document.getElementById('cityid').value != ''){
		city= '&cityid='+document.getElementById('cityid').value;
	}
	if(document.getElementById('cmsid')!=null && document.getElementById('cmsid').value != ''){
		cmsid= '&cmsid='+document.getElementById('cmsid').value;
	 }
	
	 querystr += "method=setactive&active="+active+"&table="+table+"&id="+id+showInactive+showDetail+search+page+order+sort+cmsid+category+branch+city;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	 gotoURL("addinfo.php?"+querystr);
}
//Add by natt in 6-Jan-2009 Auto update servicecharge 
function setSc(table,id,active){
	var querystr='';
	var showInactive='';
	var showDetail='';
	var search='';
	var order='';
	var page='';
	var sort='';
	var category='';
	var branch='';
	var city='';
	
	if(document.getElementById('search')!=null && document.getElementById('search').value != ''){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	}
	if(document.getElementById('order')!=null && document.getElementById('order').value != ''){
		order= '&order='+document.getElementById('order').value;
	}
	if(document.getElementById('page')!=null && document.getElementById('page').value != ''){
		page= '&page='+document.getElementById('page').value;
	}
	if(document.getElementById('sort')!=null && document.getElementById('sort').value != ''){
		sort= '&sort='+document.getElementById('sort').value;
	}
	if(document.getElementById('showinactive')!=null && document.getElementById('showinactive').checked == true){
		showInactive= '&showinactive='+document.getElementById('showinactive').checked;
	}
	if(document.getElementById('showdetail')!=null && document.getElementById('showdetail').checked == true){
		showDetail= '&showdetail='+document.getElementById('showdetail').checked;
	}
	if(document.getElementById('categoryid')!=null && document.getElementById('categoryid').value != ''){
		category= '&categoryid='+document.getElementById('categoryid').value;
	}
	if(document.getElementById('branchid')!=null && document.getElementById('branchid').value != ''){
		branch= '&branchid='+document.getElementById('branchid').value;
	}
	if(document.getElementById('cityid')!=null && document.getElementById('cityid').value != ''){
		city= '&cityid='+document.getElementById('cityid').value;
	}
	
	 querystr += "method=setsc&active="+active+"&table="+table+"&id="+id+showInactive+showDetail+search+page+order+sort+category+branch+city;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	 gotoURL("addinfo.php?"+querystr);
}
function setmember_takeout(table,id,active){
	var querystr='';
	var showInactive='';
	var showDetail='';
	var search='';
	var order='';
	var page='';
	var sort='';
	var category='';
	var branch='';
	var city='';
	
	if(document.getElementById('search')!=null && document.getElementById('search').value != ''){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	}
	if(document.getElementById('order')!=null && document.getElementById('order').value != ''){
		order= '&order='+document.getElementById('order').value;
	}
	if(document.getElementById('page')!=null && document.getElementById('page').value != ''){
		page= '&page='+document.getElementById('page').value;
	}
	if(document.getElementById('sort')!=null && document.getElementById('sort').value != ''){
		sort= '&sort='+document.getElementById('sort').value;
	}
	if(document.getElementById('showinactive')!=null && document.getElementById('showinactive').checked == true){
		showInactive= '&showinactive='+document.getElementById('showinactive').checked;
	}
	if(document.getElementById('showdetail')!=null && document.getElementById('showdetail').checked == true){
		showDetail= '&showdetail='+document.getElementById('showdetail').checked;
	}
	if(document.getElementById('categoryid')!=null && document.getElementById('categoryid').value != ''){
		category= '&categoryid='+document.getElementById('categoryid').value;
	}
	if(document.getElementById('branchid')!=null && document.getElementById('branchid').value != ''){
		branch= '&branchid='+document.getElementById('branchid').value;
	}
	if(document.getElementById('cityid')!=null && document.getElementById('cityid').value != ''){
		city= '&cityid='+document.getElementById('cityid').value;
	}
	
	 querystr += "method=setmember_takeout&active="+active+"&table="+table+"&id="+id+showInactive+showDetail+search+page+order+sort+category+branch+city;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	 gotoURL("addinfo.php?"+querystr);
}
//Add by natt in 6-Jan-2009 Auto update tax 
function setTax(table,id,active){
	var querystr='';
	var showInactive='';
	var showDetail='';
	var search='';
	var order='';
	var page='';
	var sort='';
	var category='';
	var branch='';
	var city='';
	
	if(document.getElementById('search')!=null && document.getElementById('search').value != ''){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	}
	if(document.getElementById('order')!=null && document.getElementById('order').value != ''){
		order= '&order='+document.getElementById('order').value;
	}
	if(document.getElementById('page')!=null && document.getElementById('page').value != ''){
		page= '&page='+document.getElementById('page').value;
	}
	if(document.getElementById('sort')!=null && document.getElementById('sort').value != ''){
		sort= '&sort='+document.getElementById('sort').value;
	}
	if(document.getElementById('showinactive')!=null && document.getElementById('showinactive').checked == true){
		showInactive= '&showinactive='+document.getElementById('showinactive').checked;
	}
	if(document.getElementById('showdetail')!=null && document.getElementById('showdetail').checked == true){
		showDetail= '&showdetail='+document.getElementById('showdetail').checked;
	}
	if(document.getElementById('categoryid')!=null && document.getElementById('categoryid').value != ''){
		category= '&categoryid='+document.getElementById('categoryid').value;
	}
	if(document.getElementById('branchid')!=null && document.getElementById('branchid').value != ''){
		branch= '&branchid='+document.getElementById('branchid').value;
	}
	if(document.getElementById('cityid')!=null && document.getElementById('cityid').value != ''){
		city= '&cityid='+document.getElementById('cityid').value;
	}
	
	 querystr += "method=settax&active="+active+"&table="+table+"&id="+id+showInactive+showDetail+search+page+order+sort+category+branch+city;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	 gotoURL("addinfo.php?"+querystr);
}

function sortInfo(order,page){
	// branch/room information
	var categoryid = "";
	var search = "";
	
	if(document.getElementById('search')!=null){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	
		//for some word
		var patt1=/style/gi;
		if(search.match(patt1)){
			search="styl";
		}
		
		search = "&search="+search;
	}
	
	if(document.getElementById('categoryid')!=null){
	 	categoryid = document.getElementById("categoryid").value
		categoryid = "&categoryid="+categoryid;
	 }
	 // employee information
	var showInactive = "";
	if(document.getElementById('showinactive')!=null){
		if(document.getElementById('showinactive').checked == true){
			showInactive= '&showinactive='+document.getElementById('showinactive').value;
		}else{showInactive= '&showinactive=0';}
	}
	var showDetail = "";
	if(document.getElementById('showdetail')!=null){
		if(document.getElementById('showdetail').checked == true){
			showDetail= '&showdetail='+document.getElementById('showdetail').value;
		}else{showDetail= '&showdetail=0';}
	}
	var branchid = "";
	if(document.getElementById('branchid')!=null){
	 	branchid = document.getElementById("branchid").value
		branchid = "&branchid="+branchid;
	 }
	var cityid = "";
	if(document.getElementById('cityid')!=null){
	 	cityid = document.getElementById("cityid").value
		cityid = "&cityid="+cityid;
	 }
	
	var cmsid = "";
	if(document.getElementById('cmsid')!=null){
		cmsid = document.getElementById("cmsid").value
		cmsid = "&cmsid="+cmsid;
	 }
	 	
	var sort = document.getElementById("sort").value;
	if(order==""){
		order = document.getElementById("order").value;
	}else{
		if(sort=="desc"){sort="asc";}else{sort="desc";}
	}
	document.getElementById("sort").value = sort;
	document.getElementById("order").value = order;
	document.getElementById("page").value = page;
	
	var querystr="sort="+sort+"&page="+page+"&order="+order+branchid+cmsid+cityid+categoryid+search+showInactive+showDetail;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	gotoURL("index.php?"+querystr);  
}
//Create by natt in 4-Dec-2008 function for add append treatement row
function addTrm(){
	var newTrm = document.getElementById('newTrm');
	var package_id = document.getElementById('package_id');
	var d = "id="+package_id.value+"&package_id="+package_id.value+"&newtrm="+encodeURIComponent( newTrm.options[newTrm.selectedIndex].value );
	d += "&tadd=tadd";
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	d = d+"&pageid="+pageid;
   	postReturnText("add_package.php",d,"tableDisplay");
}
//Create by natt in 17-Dec-2009 function for set therapist queue
function set_thQueue(queue_id,old_queue,queue_order){
	var d='';
	var querystr='';
	if (document.getElementById('querystr')!=null&&document.getElementById('querystr').value){
		d += document.getElementById('querystr').value;
	}
	d += "&add="+document.getElementById("add").value; 
	
	var th_cnt = document.getElementById("th_cnt").value;
	var count = 0;
	
	/*if(queue_id!=""){
			d += "&formname=bl_th_queue&queue_order="+queue_order+
				"&th_queue_id="+queue_id;
			gotoURL("addinfo.php?"+d);  
			return;
	}*/
	
	var th_list = new Array();
	var queue = "";
	for(j=0;j<th_cnt;j++){
		th_queue = document.getElementById("th_queue["+j+"]");
		th_list[j] = th_queue.options[th_queue.selectedIndex].value;
		if(array_search(th_list,th_list[j])!=j){
			//alert("Please check therapist queue!!"+j+":"+th_list[j]+","+array_search(th_list,th_list[j])+":"+th_list[array_search(th_list,th_list[j])]);
			alert("Please check therapist queue!!");
			return;
		}
	}
	
	if(queue_id!=""){
		tmp = th_list[queue_order];
		th_list[queue_order] = queue_id;
		th_list[old_queue] = tmp;
	}
	
	queue = th_list.join(',');
	d += "&th_queue="+queue;
	gotoURL("addinfo.php?"+d);  
}
function array_search (array,val) {
	for (var i = 0; i < array.length; i++) {
		if (array[i] == val) {
			return i;
		}
	}
	return i;
}
//Create by natt in 17-Dec-2009 function for check ordering numberfunction checkValue(inputobject){
function checkValue(inputobject,old_number){
	var inputvalue = inputobject.value;
    var pattern=/^[0-9]{1}\d{0,}$/;
    if(!pattern.test(inputvalue)&&inputvalue!=""){           
    	alert("Please check on ordering number!!");  
		inputobject.value = old_number;
    }
}