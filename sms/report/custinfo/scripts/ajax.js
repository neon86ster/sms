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
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid+"&cityid="+cityid+"&categoryid="+categoryid
					+"&sortby="+document.getElementById('chksortby').value+"&order="+document.getElementById('order').value+pageid;
	//window.open("reportdetail.php?"+querystr,"Number of customer detail","resizable=no,menubar=no,toolbar=no,scrollbars=yes");
	window.open("reportdetail.php?"+querystr,"",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
function openNationDetail(begin,end,locationid,nationalityid){
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	var querystr = "begin="+begin+"&end="+end+"&continent="+locationid+"&nationality="+nationalityid
					+"&sortby="+document.getElementById('chksortby').value+"&order="+document.getElementById('order').value+pageid;
	//window.open("reportdetail.php?"+querystr,"Nationality of customer detail","resizable=no,menubar=no,toolbar=no,scrollbars=yes");
	window.open("reportdetail.php?"+querystr,"",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
function openAgeDetail(begin,end,bagerange,eagerange){
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	var pattern = /^([\d]){1,8}$/;
	var branchid = "";
	if(!pattern.test(begin)){ 
		branchid = "&branchid="+end;
		begin = document.getElementById("hidden_begin").value;
		end = document.getElementById("hidden_end").value;
	}
	var querystr = "begin="+begin+"&end="+end+"&beginage="+bagerange+"&endage="+eagerange+branchid
					+"&sortby="+document.getElementById('chksortby').value+"&order="+document.getElementById('order').value+pageid;
	//window.open("reportdetail.php?"+querystr,"Age of customer detail","resizable=no,menubar=no,toolbar=no,scrollbars=yes");
	window.open("reportdetail.php?"+querystr,"",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
function openSexDetail(begin,end,branchid,cityid,categoryid){
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid+"&cityid="+cityid+"&categoryid="+categoryid
					+"&sortby="+document.getElementById('chksortby').value+"&order="+document.getElementById('order').value+pageid;
	//window.open("reportdetail.php?"+querystr,"Sex of customer detail","resizable=no,menubar=no,toolbar=no,scrollbars=yes");
	window.open("reportdetail.php?"+querystr,"",'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 
function searchCust(begin,end,branchid){
	var search = document.getElementById('search').value.replace("+","%2B");
		search = search.replace("&","%26");
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid+"&search="+search;
	getReturnText('report.php',querystr,'tableDisplay');
}