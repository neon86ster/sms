
function changeValue(obj){
	if(obj.value=="Collapse"){document.getElementById('chkCollapse').value="Expand";}
	else{document.getElementById('chkCollapse').value="Collapse";}
	document.getElementById('chkCollapse').form.submit();
}
function changesbValue(obj){
	if(obj.value=="A > Z"){document.getElementById('chksortby').value="Z > A";}
	else{document.getElementById('chksortby').value="A > Z";}
	document.getElementById('chksortby').form.submit();
}
function openDetail(begin,end,empid,cityid,branchid){
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid+"&cityid="+cityid+"&empid="+empid
					+"&sortby="+document.getElementById('chksortby').value+"&order="+document.getElementById('order').value;
	querystr += "&pageid="+ window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value; 
	window.open("reportdetail.php?"+querystr,"",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
function openmsgDetail(begin,end,empid,cityid,branchid,msgid){
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid+"&cityid="+cityid+"&msgid="+msgid+"&empid="+empid
					+"&sortby="+document.getElementById('chksortby').value+"&order="+document.getElementById('order').value;
	querystr += "&pageid="+ window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	window.open("reportdetail.php?"+querystr,"",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
function openpackageDetail(begin,end,empid,cityid,branchid,packageid){
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid+"&cityid="+cityid+"&packageid="+packageid+"&empid="+empid
					+"&sortby="+document.getElementById('chksortby').value+"&order="+document.getElementById('order').value;
	querystr += "&pageid="+ window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	window.open("reportdetail.php?"+querystr,"",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
function openthpackageDetail(begin,end,empid,cityid,branchid,packageid){
	document.getElementById("date").value="2";
	document.getElementById("begin").value="";
	document.getElementById("end").value="";
	document.getElementById("hidden_begin").value=begin;
	document.getElementById("hidden_end").value=end;
	document.getElementById("report").value="manage_therapackageinfo1.php";
	document.getElementById("back").style.display='block';
	document.getElementById("branchid").value=branchid;
	document.getElementById("cityid").value=cityid;
	document.getElementById("empid").value=empid;
	document.getElementById("therapackage").submit();
} 
function openthmsgDetail(begin,end,empid,cityid,branchid,messageid){
	document.getElementById("date").value="2";
	document.getElementById("begin").value="";
	document.getElementById("end").value="";
	document.getElementById("hidden_begin").value=begin;
	document.getElementById("hidden_end").value=end;
	document.getElementById("report").value="manage_theramassageinfo1.php";
	document.getElementById("back").style.display='block';
	document.getElementById("branchid").value=branchid;
	document.getElementById("cityid").value=cityid;
	document.getElementById("empid").value=empid;
	document.getElementById("theramassage").submit();
} 
// add in 27-Apr-2009/natt
// modified 10-June-2009/natt add begindate/enddate
function openthmsg(theramassage,begindate,enddate){
	document.getElementById("date").value="2";
	document.getElementById("begin").value="";
	document.getElementById("end").value="";
	document.getElementById("hidden_begin").value=begindate;
	document.getElementById("hidden_end").value=enddate;
	document.getElementById("report").value="manage_theramassageinfo.php";
	document.getElementById("back").style.display='block';
	document.getElementById("msgid").value=theramassage;
	document.getElementById("theramassage").submit();
	//getReturnText('manage_theramassageinfo.php',query,'tableDisplay');
}
// add in 27-Apr-2009/natt 
// modified 10-June-2009/natt add begindate/enddate
function openthpackage(therapackage,begindate,enddate){
	document.getElementById("date").value="2";
	document.getElementById("begin").value="";
	document.getElementById("end").value="";
	document.getElementById("hidden_begin").value=begindate;
	document.getElementById("hidden_end").value=enddate;
	document.getElementById("report").value="manage_therapackageinfo.php";
	document.getElementById("back").style.display='block';
	document.getElementById("packageid").value=therapackage;
	document.getElementById("therapackage").submit();
	//getReturnText('manage_therapackageinfo.php',query,'tableDisplay');
}
