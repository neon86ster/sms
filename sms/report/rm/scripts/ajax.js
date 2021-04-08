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
	
	if(document.getElementById('chksortby')!=null){
		querystr += '&chksortby='+document.getElementById('chksortby').value;
	}
	if(document.getElementById('order')!=null){
		querystr += '&order='+document.getElementById('order').value;
	}
	
	querystr += pageid;
	gotoURL('index.php?'+querystr);
}
function changesbValue(obj,categoryid){
	if(obj.value=="A > Z"){document.getElementById('chksortby').value="Z > A";}
	else{document.getElementById('chksortby').value="A > Z";}
	searchCust('','',categoryid);
	//obj.form.submit();
}