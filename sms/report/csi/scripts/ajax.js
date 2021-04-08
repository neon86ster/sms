function openrecDetail(begin,end,branchid,recid){	//recommendation detail
	var querystr = "begin="+begin+"&end="+end+"&recid="+recid+"&branchid="+branchid
					+"&sortby="+document.getElementById('chksortby').value+"&order="+document.getElementById('order').value;
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	querystr=querystr+pageid;
	window.open("reportdetail.php?"+querystr,"tid",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
function openthDetail(begin,end,branchid,empid){	//Therapist massage CSI detail
	var querystr = "begin="+begin+"&end="+end+"&empid="+empid+"&branchid="+branchid
					+"&sortby="+document.getElementById('chksortby').value+"&order="+document.getElementById('order').value;
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	querystr=querystr+pageid;
	window.open("reportdetail.php?"+querystr,"thmsgcsi",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
function openDetail(title,begin,end,branchid){
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid;
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	querystr=querystr+pageid;
	window.open("reportdetail.php?"+querystr,title,'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
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