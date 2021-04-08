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
   if(document.getElementById("again")!=null){
   		t+="&again="+document.getElementById("again").checked;
   }
   if(document.getElementById("id")!=null&&document.getElementById("add").value==" save change "){
  	 	t+="&id="+document.getElementById("id").value;
   }
   return t.replace("+","%2B");
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
	var sort='';
	var category='';
	var gifttype='';
	
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
	// m_membership
	if(document.getElementById('categoryid')!=null && document.getElementById('categoryid').value != ''){
		category= '&categoryid='+document.getElementById('categoryid').value;
	}
	// g_gift
	if(document.getElementById('categoryid')!=null && document.getElementById('categoryid').value != ''){
		gifttype= '&categoryid='+document.getElementById('categoryid').value;
	}
	// al_bankacc_cms
	if(document.getElementById('showinactive')!=null && document.getElementById('showinactive').checked == true){
		showInactive= '&showinactive='+document.getElementById('showinactive').checked;
	}
	if(document.getElementById('showdetail')!=null && document.getElementById('showdetail').checked == true){
		showDetail= '&showdetail='+document.getElementById('showdetail').checked;
	}
	 var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	 d += "table="+table+"&id="+id+"&pageid="+pageid+showInactive+showDetail+search+page+order+sort+category+gifttype;
	 gotoURL("addinfo.php?"+d);
	 
}
function set_editData(table,id){
	var d='';
	var querystr='';
	if (document.getElementById('querystr')!=null&&document.getElementById('querystr').value){
		querystr = document.getElementById('querystr').value;
	}
	d = this.getFormParaValue('../object.xml',table);
	if(table=="g_gift"&&document.getElementById('bpdsid_sold')!= null){
		querystr += "&bpdsid_sold=" + document.getElementById('bpdsid_sold').value;
	}
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	d = d+"&method=edit&pageid="+pageid+querystr;
	
	if(table=='al_bankacc_cms'){
		d = d+"&hidden_c_date="+document.getElementById('hidden_c_date').value;
		if(document.getElementById('add_by_id')!=null){
		d = d+"&add_by_id="+document.getElementById('add_by_id').value;
		}
	}
	//alert(d);
	gotoURL("addinfo.php?"+d);  
	
}
function set_insertData(table){
	var d;
	d = this.getFormParaValue('../object.xml',table);
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	d = d+"&pageid="+pageid;
   	gotoURL("addinfo.php?"+d);  
  
}

function high(obj) { temp=obj.style.background;obj.style.background='#cccccc'; }
function low(obj) { obj.style.background=temp; }
function mouseover(obj){temp=obj.style.textDecoration;obj.style.textDecoration='underline';}
function mouseout(obj){obj.style.textDecoration=temp;}

function showInactive(url) {
	var d='';
	if(document.getElementById('showinactive').checked == true){
		d += '&showinactive='+document.getElementById('showinactive').checked;
	}
	sortInfo('','1');
}
function setEnable(table,id,active){
	var d='';
	var showInactive='';
	var showDetail='';
	var search='';
	var page='';
	var order='';
	var category_id='';
	var gifttypeid='';
	
	if(document.getElementById('showinactive')!=null){
		if(document.getElementById('showinactive').checked == true){
			showInactive= '&showinactive='+document.getElementById('showinactive').value;
		}else{showInactive= '&showinactive=0';}
	}
	if(document.getElementById('search')!=null && document.getElementById('search').value != ''){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	}
	if(document.getElementById('order')!=null && document.getElementById('order').value != ''){
		order= '&order='+document.getElementById('order').value;
	}
	if(document.getElementById('sort')!=null && document.getElementById('sort').value != ''){
		sort= '&sort='+document.getElementById('sort').value;
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
	if(document.getElementById('showdetail')!=null && document.getElementById('showdetail').checked == true){
		showDetail= '&showdetail='+document.getElementById('showdetail').checked;
	}
	
	d+="table="+table+"&id="+id+"&active="+active+"&method=setactive"+showInactive+showDetail+search+order+sort+page+category_id+gifttypeid;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	d = d+"&pageid="+pageid;
	gotoURL("addinfo.php?"+d);
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
	if(document.getElementById('showdetail')!=null && document.getElementById('showdetail').checked == true){
		d = '&showdetail='+document.getElementById('showdetail').checked;
	}
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	d = d+"&pageid="+pageid;
	getReturnText(url,d,'tableDisplay');
}
function make_srnum(num){
	if(isNaN(num)||num==0){
		document.getElementById("errormsg").innerHTML = "Please check value in \"New Sale Receipt Number\" texbox before click on \"Make Number\" button !!";
		return false;
	}
	var branchid = document.getElementById("branchid");
	if(branchid.options[branchid.selectedIndex].value==0){
		document.getElementById("errormsg").innerHTML = "Please select branch before running new sale receipt number !!";
		return false;
	}
	var accfunc = document.getElementById("acc_func");
	if(accfunc.options[accfunc.selectedIndex].value!=1){
		document.getElementById("errormsg").innerHTML = "Please select Change Accounting Function before running new sale receipt number !!";
		return false;
	}
	document.getElementById("errormsg").innerHTML = "";
	var rows = document.getElementById("rows").value;
	var begin = document.getElementById("hidden_begin").value;
	var end = document.getElementById("hidden_end").value;
	var branchid = document.getElementById("branchid").value;
	var q = 'pagenum='+num+'&begin='+begin+'&end='+end+'&branchid='+branchid+'&acc_func=1';
	var sr = "";
	var oldsr = "";
	
	var cnt=0;tmpcnt=0;
	for(i=0;i<rows;i++){
		var srid = document.getElementById("sr_id["+i+"]");
		if(document.getElementById('acc['+i+']').checked){
			if(cnt>0){sr+=",";}
			if(srid!=undefined){sr += srid.value;}
			cnt++;
		}
		if(tmpcnt>0){oldsr+=",";}
		oldsr += srid.value;
		tmpcnt++;
	}
	q += "&srs="+sr+"&oldsrs="+oldsr;
	//alert("&srs="+sr+"&oldsrs="+oldsr);
	getReturnText('chkaccount.php',q,'errormsg');
}
//set therapist was leave
function removethlist(thlistid,cityid){
	var querystr = "leave=1&thlistid="+thlistid+"&cityid="+cityid;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	getReturnText('report.php',querystr,'tableDisplay');

}

function chBranch(thlistid,i,cityid){
	var blid=document.getElementById("branch_name["+i+"]").value;
	var querystr = "add=1&thlistid="+thlistid+"&blid="+blid+"&cityid="+cityid;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	getReturnText('report.php',querystr,'tableDisplay');
}

function searchInfo(page){
	var categoryid = "";
	var showInactive = "";
	var showDetail = "";
	var gifttypeid = "";
	 	
	var search = "";
	if(document.getElementById('search')!=null){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	}
	
	
	if(document.getElementById('categoryid')!=null){
		categoryid = "&categoryid="+document.getElementById("categoryid").value;}
	if(document.getElementById('gifttypeid')!=null){
		gifttypeid = "&gifttypeid="+document.getElementById("gifttypeid").value;}
	if(document.getElementById('showinactive')!=null){
		if(document.getElementById('showinactive').checked == true){
			showInactive= '&showinactive='+document.getElementById('showinactive').value;
		}else{showInactive= '&showinactive=0';}
	}
	if(document.getElementById('showdetail')!=null && document.getElementById('showdetail').checked == true){
		showDetail= '&showdetail='+document.getElementById('showdetail').checked;
	}
		
	var sort = document.getElementById("sort").value;
	var order = document.getElementById("order").value;
	if(sort=="desc"){sort="asc";}else{sort="desc";}
	document.getElementById("sort").value = sort;
	document.getElementById("order").value = order;
	document.getElementById("page").value = page;
	
	var querystr="sort="+sort+"&page="+page+"&order="+order+gifttypeid+categoryid+search+showInactive+showDetail;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	gotoURL('index.php?'+querystr);
}
function sortInfo(order,page){
	var cityid = "";
	var branchid = "";
	var categoryid = "";
	var search = "";
	var gifttypeid = "";
	var showInactive = "";
	var showDetail = "";
	
	if(document.getElementById('cityid')!=null){
	 	cityid = "&cityid="+document.getElementById("cityid").value;
	 }
	if(document.getElementById('branchid')!=null){
	 	branchid = "&branchid="+document.getElementById("branchid").value;
	 }
	if(document.getElementById('categoryid')!=null){
	 	categoryid = document.getElementById("categoryid").value;
		categoryid = "&categoryid="+categoryid;
	 }
	if(document.getElementById('gifttypeid')!=null){
	 	gifttypeid = document.getElementById("gifttypeid").value;
		gifttypeid = "&gifttypeid="+gifttypeid;
	 }
	if(document.getElementById('search')!=null){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	}
	if(document.getElementById('showinactive')!=null){
		if(document.getElementById('showinactive').checked == true){
			showInactive= '&showinactive='+document.getElementById('showinactive').value;
		}else{showInactive= '&showinactive=0';}
	}
	if(document.getElementById('showdetail')!=null && document.getElementById('showdetail').checked == true){
		showDetail= '&showdetail='+document.getElementById('showdetail').checked;
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
	
	var querystr="sort="+sort+"&page="+page+"&order="+order+cityid+categoryid+gifttypeid+search+showInactive+branchid+showDetail;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	gotoURL("index.php?"+querystr);  
}
// set treatment approve
function settreatmentapp(thid,trmtype,trmid,thappid,method,divid){
	var d = 'thid='+thid+'&trmtype='+trmtype+'&trmid='+trmid+'&thappid='+thappid+'&method='+method;
	getReturnText('trmmg.php',d,divid);
}
// load treatment approve
function loadtreatmentapp(thid){
	var d = '';
	d = 'thid='+thid+'&trmtype=0';
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	d = d+"&pageid="+pageid;
	getReturnText('trmmg.php',d,"packagediv");
	d = 'thid='+thid+'&trmtype=3';
	getReturnText('trmmg.php',d,"massagediv");
	d = 'thid='+thid+'&trmtype=2';
	getReturnText('trmmg.php',d,"facialdiv");
	d = 'thid='+thid+'&trmtype=1';
	getReturnText('trmmg.php',d,"bathdiv");
	d = 'thid='+thid+'&trmtype=4';
	getReturnText('trmmg.php',d,"scrubdiv");
	d = 'thid='+thid+'&trmtype=5';
	getReturnText('trmmg.php',d,"wrapdiv");
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
}

//Open windows for print member history report
function printMemberHistory(memberCode){
	var chkpage = document.getElementById('chkpage');
	window.open('print_history.php?memberId='+memberCode+'&chkpage='+chkpage.value+'&export=print','','scrollbars=1, top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height))
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
