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
function openDetail(begin,end,branchid,cityid,categoryid){
	var querystr = "&date=2&hidden_begin="+begin+"&hidden_end="+end+"&branchid="+branchid+"&cityid="+cityid+"&categoryid="+categoryid
					+"&acc_func=0";
	gotoURL('/report/sales/item/index.php?pageid=45'+querystr);
} 
function openrDetail(begin,end,branchid,itemid){
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.document.getElementById("pageid").value;
	}
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid+"&itemid="+itemid+pageid;
	window.open("reportdetail.php?"+querystr,"",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 