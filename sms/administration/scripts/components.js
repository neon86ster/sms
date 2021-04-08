
/***********************************************
 * popup new window
 ***********************************************/
function newwindow(target,nameofwindow){
	window.open(target,nameofwindow,'resizable=0,scrollbars=1');
}

/***********************************************
 * return change mainframe location
 ***********************************************/
function gotoURL(url){
	parent.mainFrame.location.href = url;
}

function hiddenLeftFrame() {
  var fs = window.top.document.getElementsByTagName("frameset")[1];
  var spLine = document.getElementById("spLine");
  if(fs.cols == "220,*"){
  	 fs.cols = "0,*";
	 spLine.src = "images/bar_open.gif";
  }
  else{
  	 fs.cols = "220,*";
 	 spLine.src = "images/bar_close.gif";
  }
  // add for resize mainpage's menuheader ruck/07-05-2009
  try{
  	document.getElementById('mainheader').width=document.body.clientWidth-5;
  }catch(e){}
}

/***********************************************
 * method for All directory page
 ***********************************************/
function changeimg(id,img){
	var imgobj = document.getElementById(id);
	imgobj.src = img;
}

 
/***********************************************
 * method hidden/show menu list
 * pagename - this mainpage's pagename
 * url - this mainpage's url 
 * parent - name of this parent page / 0 - excute this function from left menu
 ***********************************************/
function showhide(pagename,url,parentname,theme,pageid) {
	var allpage = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pagespan");
	var page = (allpage==null)?" ":allpage.value.split("|");
	var menu = ""; var span = "";
	var tdbg = ""; var img = "";
	
	
	// setting this page id in left menu
	if(pageid>0){
		window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value=pageid;
	}
		
	// check if home page
	if(pagename==""){
			// change menu style
			for(i=1;i<page.length;i++){
					var menu = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById(page[i]);
					var span = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById(page[i]+"span");
					var tdbg = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById(page[i]+"bg");
					var img = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById(page[i]+"img");
					if(menu!= null){menu.style.display = "none";}	// check page span
					
					//tdbg.style.background = tdbg.class;
					tdbg.style.background = "url("+tdbg.className+")"; 
					
					img.src = "images/menu1.png";
					span.style.color = '#ffffff';			// change menu font color	
			}
	}else{
			var parent = parentname.split("|");
			for(i=1;i<parent.length;i++){
					if(parent[i]!="Home0"&&parent[i]!="Home"){
						var menu = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById(parent[i]);
						menu.style.display = 'block';
					}
			}
			
			var menu = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById(pagename);
			if(menu!= null){
				var state = menu.style.display;
				if(state == 'block'){
						menu.style.display = 'none';
				}else{
						menu.style.display = 'block';
				}
			}
			
			// change menu style
			for(i=1;i<page.length;i++){
					var span = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById(page[i]+"span");
					var tdbg = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById(page[i]+"bg");
					var img = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById(page[i]+"img");
					
					// reset background image
					//tdbg.style.background = tdbg.class;
					tdbg.style.background = "url("+tdbg.className+")"; 
					
					if(page[i]==pagename){
						
						//tdbg.class = tdbg.style.background;
						img.src = "images/menu2.png";
						span.style.color = '#01399f';			// change menu font color
						tdbg.style.background = "url(images/blank.gif)";
					}else{
						img.src = "images/menu1.png";
						span.style.color = '#ffffff';		
					}
			}
	}		// end check for home page
}

/***********************************************
 * method for All page
 ***********************************************/
function newwindow(target,nameofwindow){
	window.open(target,nameofwindow,'resizable=0,scrollbars=1');
}


function getHttpObject() {
	var xmlHttp = null;
	if (window.XMLHttpRequest) {
		xmlHttp = new XMLHttpRequest(); // FireFox, Opera and IE7
	}
	else if (window.ActiveXObject) {
		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP"); // IE5 & IE6
	}
        
	return xmlHttp;
}

/*******************************************************
 * ajax GET/POST to send information and return text
 *******************************************************/
function getRandomSession() {
   var randomStr = "rand=" + Math.floor(Math.random()*10000);
   return randomStr;
}

// use to Method GET to send information and return text
function getReturnText(url, data, divTag) {
   
	var objRequest = getHttpObject();
	var ranDom = getRandomSession();
	var a = document.getElementById(divTag);
        
        objRequest.onreadystatechange = function(){
			if(objRequest.readyState == 4 && objRequest.status == 200){
				a.innerHTML = objRequest.responseText;
				if(document.getElementById("loading")!=null){
					document.getElementById("loading").style.display="none";
				}
				if(document.getElementById("spLine")!=null){
					document.getElementById("spLine").style.display="block";
				}
			}else{
				if(document.getElementById("loading")!=null){
					document.getElementById("loading").style.display="block";
				}
				if(document.getElementById("spLine")!=null){
					document.getElementById("spLine").style.display="none";
				}
			}
        }
       
        objRequest.open("GET", url+"?"+ranDom+"&"+data);
        objRequest.send(null);
        
}

// use to Method POST to send information and return text
function postReturnText(url, data, divTag) {
	var objRequest = getHttpObject();
	var ranDom = getRandomSession();
        var a = document.getElementById(divTag);
	
        objRequest.onreadystatechange = function(){
			if(objRequest.readyState == 4 && objRequest.status == 200){
				a.innerHTML = objRequest.responseText;
				if(document.getElementById("loading")!=null){
					document.getElementById("loading").style.display="none";
				}
				if(document.getElementById("spLine")!=null){
					document.getElementById("spLine").style.display="block";
				}
			}else{
				if(document.getElementById("loading")!=null){
					document.getElementById("loading").style.display="block";
				}
				if(document.getElementById("spLine")!=null){
					document.getElementById("spLine").style.display="none";
				}
			}
        }
       
    objRequest.open("POST",url);
	objRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	objRequest.send(data+"&"+ranDom);
        
}

function loadXMLDoc(dname) 
{         
  if (window.ActiveXObject) {
    xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
    
  }
  else if (document.implementation.createDocument) {
    xmlDoc = document.implementation.createDocument("","",null);
    
  }
  else {
    return null;
  }

  xmlDoc.async=false;
  xmlDoc.load(dname);
  
  return xmlDoc;
}
// update interface
function sortInfo(order,page){
	var search = "";
	var showInactive = "";
	
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
	 	
	var sort = document.getElementById("sort").value;
	if(order==""){
		order = document.getElementById("order").value;
	}else{
		if(sort=="desc"){sort="asc";}else{sort="desc";}
	}
	document.getElementById("sort").value = sort;
	document.getElementById("order").value = order;
	document.getElementById("page").value = page;
	
	var querystr="sort="+sort+"&page="+page+"&order="+order+search+showInactive;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	gotoURL("index.php?"+querystr);  
}
function setEnable(table,id,active){
	var d='';
	var showInactive='';
	var search='';
	var page='';
	var order='';
	
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
	d+="table="+table+"&id="+id+"&active="+active+"&method=setactive"+showInactive+search+order+sort+page;
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
function getFormParaValue(dname,tableName) {
   xmlDoc = this.loadXMLDoc(dname);
   var e = xmlDoc.getElementsByTagName(tableName)[0].getElementsByTagName('field');
   var i;
   var n,t="";
   var frmvalue = "";
    
    for(i=0; i<e.length; i++) {
		if(e[i].getAttribute('idfields')!="yes"&&e[i].getAttribute('showinform')!="no"){
			 n = e[i].getAttribute('name');
		     if(i) t+= "&";
		     if(e[i].getAttribute('formtype')=="textarea"){
		    	frmvalue = document.getElementById(n).value.replace(/\+/g,"%2B");
		    	frmvalue = frmvalue.replace(/\n/g,"[br]");
				t += n+"="+frmvalue.replace(/\&/g,"%26");
			  } else if(e[i].getAttribute('formtype')!="checkbox"&&e[i].getAttribute('formtype')!="date"
			  &&e[i].getAttribute('formtype')!="password"){
				frmvalue = document.getElementById(n).value.replace(/\+/g,"%2B");
				t += n+"="+frmvalue.replace(/\&/g,"%26");
		      } else if(e[i].getAttribute('formtype')=="checkbox"&&document.getElementById(n).checked){
		      	 t += n+"=1";
		      } else if(e[i].getAttribute('formtype')=="checkbox"&&!document.getElementById(n).checked){
		      	 t += n+"=0";
		      }else if(e[i].getAttribute('formtype')=="date"){
		      	 t += "hidden_"+n+"="+document.getElementById("hidden_"+n).value;
		      	 t += "&"+n+"="+document.getElementById(n).value;
		      }else if(e[i].getAttribute('formtype')=="password"&&document.getElementById("add").value==" add "){
		      	 	t += "pass="+document.getElementById("pass").value;
		      	 	t += "&rpass="+document.getElementById("rpass").value;
		      }else if(e[i].getAttribute('formtype')=="password"&&document.getElementById("add").value==" save change "){
		      		t += "&pass="+document.getElementById("newpass").value;
		      	 	t += "&rpass="+document.getElementById("rnewpass").value;
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
function editData(table,id){
	var d='';
	var showInactive='';
	var search='';
	var order='';
	var page='';
	var sort='';
	
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
	 var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	 d += "table="+table+"&id="+id+"&pageid="+pageid+showInactive+search+page+order+sort;
	 gotoURL("addinfo.php?"+d);
	 
}
function set_editData(table,id){
	var d='';
	var querystr='';
	if (document.getElementById('querystr')!=null&&document.getElementById('querystr').value){
		querystr = document.getElementById('querystr').value;
	}
	d = this.getFormParaValue('../object.xml',table);
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	d = d+"&method=edit&pageid="+pageid+querystr;
	gotoURL("addinfo.php?"+d);  
	
}
function set_insertData(table){
	var d;
	d = this.getFormParaValue('../object.xml',table);
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	d = d+"&pageid="+pageid;
   	gotoURL("addinfo.php?"+d);  
  
}