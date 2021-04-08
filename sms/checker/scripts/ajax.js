//Commission Envelope Number Report
function addEnv(i,cmsprice){
	var pageid = document.getElementById("pageid").value;
	var envnum = document.getElementById("cmsEnvnumber["+i+"]").value;
	var date = document.getElementById("date").value;
	var begin = document.getElementById("begin").value;
	var end = document.getElementById("end").value;
	var hbegin = document.getElementById("hidden_begin").value;
	var hend = document.getElementById("hidden_end").value;
	var bpid = document.getElementById("bpid").value;
	var cmsid = document.getElementById("cmsid["+i+"]").value;
	document.getElementById("successmsg").innerHTML="";
	if(isNaN(envnum)||envnum==""||envnum==0){
		document.getElementById("errormsg").innerHTML="<img src=\"/images/errormsg.png\" /> Please check envelope number value!!";
	}else{
		window.location ='index.php?insert=add&cmsEnvnumber='+envnum+'&cmsid='+cmsid+'&cmsprice='+cmsprice+'&date='+date+'&begin='+begin+'&end='+end+'&hidden_begin='+hbegin+'&hidden_end='+hend+'&bpid='+bpid+'&pageid='+pageid;
	}
}
//Commission Tracking Record Report
function addallEnv(){
	var envnum;
	var rows = document.getElementById("rows").value;
	var param = new Array();
	var chkparam = new Array();
	document.getElementById("successmsg").innerHTML="";
	for(i=0;i<rows;i++){
		envnum = document.getElementById('cmsEnvnumber['+i+']');
		if(isNaN(envnum.value)&&envnum.value!=""){
			document.getElementById("errormsg").innerHTML="<img src=\"/images/errormsg.png\" /> Please check envelope number must be number value!!";
			return false;
		}
		if(envnum.value!=""){param.push(envnum.value);}
	}
	if(param.length==0){
		document.getElementById("errormsg").innerHTML="<img src=\"/images/errormsg.png\" /> Please check envelope number must be number value!!";
		return false;
	}
	chkparam = param;
	for(i=0;i<param.length;i++){
		for(j=0;j<chkparam.length;j++){
			if(chkparam[j]==param[i]&&j!=i){
				document.getElementById("errormsg").innerHTML="Can't use the same envelope number!!";
				return false;
			}
		}
	}
	document.getElementById("checkaddall").value = "addenv";
	document.getElementById('cen').submit();
}
//Commission Dispersed Report
function addDisp(i,page){
	var pageid = document.getElementById("pageid").value;
	var date = document.getElementById("date").value;
	var begin = document.getElementById("begin").value;
	var end = document.getElementById("end").value;
	var hbegin = document.getElementById("hidden_begin").value;
	var hend = document.getElementById("hidden_end").value;
	var cdfunc = document.getElementById("cdfunc").value;
	var search = document.getElementById("search").value;
	var dispdate = document.getElementById("hidden_pickupdate["+i+"]").value;
	var dispgaveid = document.getElementById("gaveby["+i+"]").options[document.getElementById("gaveby["+i+"]").selectedIndex].value;
	var cmsid = document.getElementById("cmsid["+i+"]").value;
	if(dispdate=="" || dispgaveid==1){
		try{
			document.getElementById("errormsg").innerHTML="Please check on \"Pick Up Date\" and \"Staff Gave\" value !!";
		}catch(e){
			window.location ='index.php?pageid='+pageid;
		}
		return false;
	}else{
		window.location ='index.php?insert=add&dispdate='+dispdate+'&cmsid='+cmsid+'&gaveby='+dispgaveid+'&date='+date+'&begin='+begin+'&end='+end+'&hidden_begin='+hbegin+'&hidden_end='+hend+'&cdfunc='+cdfunc+'&where='+search+'&page='+page+'&pageid='+pageid;
	}
}

// Commission Payment Report
function chkreport(data) {
	var chkdd = document.getElementById("chkddreport").value;
	if(chkdd==0){
		document.getElementById("chkddreport").value='1';
		document.getElementById("chkddreport1").value='0';
	}else{
		data += "&ddreport=1";
		document.getElementById("chkddreport").value=0;
		document.getElementById("chkddreport1").value='1';
	}
	getReturnText('report.php',data,'tableDisplay');
}
function newwindow(target,nameofwindow){
	//window.open(target,nameofwindow,'resizable=0,scrollbars=1');
	window.open(target,nameofwindow,'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
}
// Customers Per Location Report and Therapist Hours Report
function changeValue(obj){
	if(obj.value=="Collapse"){document.getElementById('chkCollapse').value="Expand";}
	else{document.getElementById('chkCollapse').value="Collapse";}
	document.getElementById('chkCollapse').form.submit();
}
// DD report
function changeDDreport(obj){
	if(obj.value=="DD Report"){document.getElementById('chkddreport').value=1;}
	else{document.getElementById('chkddreport').value=0;}
	document.getElementById('ddreport').form.submit();
}
// Customers Per Location Report and Therapist Hours Report
function changesbValue(obj){
	if(obj.value=="A > Z"){document.getElementById('chksortby').value="Z > A";}
	else{document.getElementById('chksortby').value="A > Z";}
	document.getElementById('chksortby').form.submit();
}
// Customers Per Location Report
function openDetail(begin,end,branchid,cityid,categoryid){
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid+"&cityid="+cityid+"&categoryid="+categoryid
					+"&sortby="+document.getElementById('chksortby').value+"&order="+document.getElementById('order').value+pageid;
	window.open("reportdetail.php?"+querystr,"",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
// Branch Report
function openCusDetail(begin,end,branchid){
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid+pageid;
	window.open("reportdetail.php?"+querystr,"",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
// Therapist Hours Report
function opentthourDetail(begin,end,branchid,cityid,categoryid){
	var pageid = "";;
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid+"&cityid="+cityid+"&categoryid="+categoryid
					+"&sortby="+document.getElementById('chksortby').value+"&order="+document.getElementById('order').value+pageid;
	window.open("reportdetail.php?"+querystr,"",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
// Commission Payment Report for paging selected
function sortInfo(column,pagenumber){
	var hidden_begin = document.getElementById("hidden_begin").value;
	var hidden_end = document.getElementById("hidden_end").value;
	var cdfunc = document.getElementById("cdfunc").value;
	var search = document.getElementById("search").value;
	var date = document.getElementById("date").value;
	var data = "begin="+hidden_begin+"&end="+hidden_end+"&where="+search+"&cdfunc="+cdfunc+"&date="+date+"&page="+pagenumber;
	getReturnText('report.php',data,'tableDisplay');
}