function searchCust(cityid,branchid,categoryid){
	var search = document.getElementById('search').value.replace("+","%2B");
		search = search.replace("&","%26");
		
	var querystr = "search="+search;
	if(categoryid!=''){
		categoryid = '&categoryid='+categoryid;
	}
	if(cityid!=''){
		cityid = '&cityid='+cityid;
	}
	if(branchid!=''){
		branchid = '&branchid='+branchid;
	}
	var showInactive = "";
	if(document.getElementById('showinactive')!=null){
		if(document.getElementById('showinactive').checked == true){
			showInactive= '&showinactive='+document.getElementById('showinactive').value;
		}else{showInactive= '&showinactive=0';}
	}
	var hidden_begin = "";
	if(document.getElementById('hidden_begin')!=null){
			hidden_begin= '&hidden_begin='+document.getElementById('hidden_begin').value;
	}
	var hidden_end = "";
	if(document.getElementById('hidden_end')!=null){
			hidden_end = '&hidden_end='+document.getElementById('hidden_end').value;
	}
	var date = "";
	if(document.getElementById('date')!=null){
			date = '&date='+document.getElementById('date').value;
	}
	querystr += categoryid+showInactive+cityid+branchid+hidden_begin+hidden_end+date;
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	querystr += pageid;
	gotoURL('index.php?'+querystr);
}
function openDetail(title,begin,end,branchid,cityid,bpid){
	var querystr = "begin="+begin+"&end="+end+"&branchid="+branchid+"&cityid="+cityid+"&bpid="+bpid;
	var pageid = "";
	if(document.getElementById('pageid')!=null){
		pageid = "&pageid="+document.getElementById('pageid').value;
	}else{
		pageid = "&pageid="+window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	}
	querystr=querystr+pageid;
	window.open("reportdetail.php?"+querystr,title,'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
} 