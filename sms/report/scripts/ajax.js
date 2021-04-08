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
function openinvDetail(begin,end,categoryid,trmid,branchid){	//Therapist inventory detail
	var querystr = "begin="+begin+"&end="+end+"&categoryid="+categoryid+"&trmid="+trmid+"&branchid="+branchid
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
function openmkDetail(begin,end,branchid,cityid,mkid,mktypeid,tbname,mktype,status){	//recommendation detail
	var querystr = "begin="+begin+"&end="+end+"&mkid="+mkid+"&mktypeid="+mktypeid+"&tbname="+tbname+"&mktype="+mktype+"&status="+status+"&branchid="+branchid+"&cityid="+cityid;
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	querystr=querystr+pageid;
	window.open("reportdetail.php?"+querystr,"tid"+tbname,'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
function openrDetail(begin,end,branchid,itemid,payid,table,cityid,sexid,mkid,mktypeid,tbname,mktype,status){
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.document.getElementById("pageid").value;
	}
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid+"&itemid="+itemid+"&payid="+payid+"&table="+table+"&cityid="+cityid+"&sexid="+sexid+"&mkid="+mkid+"&mktypeid="+mktypeid+"&tbname="+tbname+"&mktype="+mktype+"&status="+status+pageid;
	window.open("reportdetail.php?"+querystr,"",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 