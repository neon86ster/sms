
/***********************************************
 * popup new window
 ***********************************************/
function newwindow(target,nameofwindow){
	window.open(target,nameofwindow,'resizable=0,scrollbars=1');
}
/**************************************************************
 * always check user session - natt/06 June 2009
 **************************************************************/
function checksms() {	
	getReturnText('chksms.php','','chksms');
    setTimeout(checksms, 15000);
}

/***********************************************
 * return change mainframe location
 ***********************************************/
function gotoURL(url){
	parent.mainFrame.location.href = url;
}

function hiddenLeftFrame(path) {
  var fs = window.top.document.getElementsByTagName("frameset")[1];
  var spLine = document.getElementById("spLine");
  if(fs.cols == "220,*"){
  	 fs.cols = "0,*";
	 spLine.src = "/images/bar_open.gif";
  }
  else{
  	 fs.cols = "220,*";
 	 spLine.src = "/images/bar_close.gif";
  }
  /* add for resize mainpage's menuheader ruck/07-05-2009
  try{
  	document.getElementById('mainheader').width=document.body.clientWidth-5;
  }catch(e){}*/
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
	
	//contentDocument
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
					
					img.src = "images/"+theme+"/menu/menu1.png";
					span.style.color = '#ffffff';			// change menu font color	
			}
	}else{
			var parent = parentname.split("|");
			for(i=1;i<parent.length;i++){
					if(parent[i]!="Home0" && parent[i] != "Home"){
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
						img.src = "images/"+theme+"/menu/menu1.png";
						span.style.color = '#ffffff';		
					}
			}
	}		// end check for home page
}

/***********************************************
 * method for All page
 ***********************************************/
function newwindow(target,nameofwindow){
	//window.open(target,nameofwindow,'resizable=0,scrollbars=1');
	window.open(target,nameofwindow,'scrollbars=1top=0, left=0, resizable=no' +',width=' + (screen.width) +',height=' + (screen.height));
}

function EditRoomStatus(bookid,roomid,status){
	document.getElementById("EditBid").value=bookid;
	document.getElementById("EditRoomid").value=roomid;
	document.getElementById("ChangeStatus").value=status;
	if(bookid && roomid && status){
		document.getElementById("appointment").submit();
	}
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
  /*if (window.ActiveXObject) {
    xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
    
  }
  else if (document.implementation.createDocument) {
    xmlDoc = document.implementation.createDocument("","",null);
    
  }
  else {
    return null;
  }

  xmlDoc.async=false;
  xmlDoc.load(dname);*/
  try //Internet Explorer
   {
       xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
   }
   catch(e)
   {
      try //Firefox, Mozilla, Opera, etc.
      {
         xmlDoc=document.implementation.createDocument("","",null);
      }
      catch(e) {alert(e.message)}
   }

   try
   {

       var ua = navigator.userAgent.toLowerCase();
       if (ua.indexOf('safari/') != -1){ //user is reporting as Safari, use XMLHttpRequest instead.
          XmlHTTP = new XMLHttpRequest();
          XmlHTTP.open("get", dname, false);
          XmlHTTP.send("");
          var xDoc = XmlHTTP.responseXML;
          return xDoc;
       }else{

         xmlDoc.async=false;
         xmlDoc.load(dname);
         return(xmlDoc); 

      }

   }
   catch(e) {alert(e.message)}
   return(null);
}

/*******************************************************
 * check date value and set default column in report
 *******************************************************/
function checkDateValue(){
	var selectdate = document.getElementById('date').value;
	
	if(selectdate==1){document.getElementById('column').options[0].selected = true;}	// All
	if(selectdate==3){document.getElementById('column').options[5].selected = true;}	// Last Fiscal Quarter
	if(selectdate==4){document.getElementById('column').options[5].selected = true;}	// Last Fiscal Quarter to date
	if(selectdate==5){document.getElementById('column').options[6].selected = true;}	// Last Fiscal Year
	if(selectdate==6){document.getElementById('column').options[6].selected = true;}	// Last Fiscal Year to date
	if(selectdate==7){document.getElementById('column').options[4].selected = true;}	// Last Month
	if(selectdate==8){document.getElementById('column').options[4].selected = true;}	// Last Month to date
	if(selectdate==9){document.getElementById('column').options[2].selected = true;}	// Last Week
	if(selectdate==10){document.getElementById('column').options[2].selected = true;}	// Last Week to date
	if(selectdate==11){document.getElementById('column').options[5].selected = true;}	// This Fiscal Quarter
	if(selectdate==12){document.getElementById('column').options[5].selected = true;}	// This Fiscal Quarter to date
	if(selectdate==13){document.getElementById('column').options[6].selected = true;}	// This Fiscal Year
	if(selectdate==14){document.getElementById('column').options[6].selected = true;}	// This Fiscal Year to date
	if(selectdate==15){document.getElementById('column').options[4].selected = true;}	// This Month
	if(selectdate==16){document.getElementById('column').options[4].selected = true;}	// This Month to date
	if(selectdate==17){document.getElementById('column').options[1].selected = true;}	// Today
	if(selectdate==18){document.getElementById('column').options[1].selected = true;}	// Today

	//alert(document.getElementById('column').options[0].value);
	//return false;
}

/*******************************************************
 * preference manager validator
 *******************************************************/
//email validator
function checkEmail(inputobject){	
	var inputvalue = inputobject.value;
    var pattern=/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z]){2,4}$/;
    if(!pattern.test(inputvalue)&&inputvalue!=""){           
    	alert("Email must include \"@\" and period \".\" \n    e.g., name@hostname.com");  
		inputobject.value = "";
    }
}
//phone number validator
function checkPhone(inputobject){	
	var inputvalue = inputobject.value;
    var pattern=/^\+[1-9]{1}\d{0,}$/;
    if(!pattern.test(inputvalue)&&inputvalue!=""){           
    	alert("Please input Phone number with this format : \n    + countrycode citycode number \n e.g., +6653, for citycode take off zero.");  
		inputobject.value = "";
    }
}

//phone number validator
function checkMobile(inputobject){	
	var inputvalue = inputobject.value;
    var pattern=/^\+[1-9]{1}\d{0,}$/;
    if(!pattern.test(inputvalue)&&inputvalue!=""){           
    	alert("Please input Mobile number with this format : \n    + countrycode citycode number \n e.g., +6653, for citycode take off zero.");  
		inputobject.value = "";
    }
}
/*******************************************************
 * online users list 
 *******************************************************/
 // unlock user
 function unlockUser(userid,order,page,url){
	var search = "";
	if(document.getElementById('search')!=null){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	 }
	 
	var branch = "";
	if(document.getElementById('branch_id')!=null){
	 	branch = "&branch_id="+document.getElementById("branch_id").value;}
	 	
	var sort = document.getElementById("sort").value;
	
	if(order==""){
		order = "&order="+document.getElementById("order").value;
	}else{
		order = "&order="+order;
		if(sort=="desc"){sort="asc";}else{sort="desc";}
	}
	
	sort = "&sort="+sort;
	var querystr = search+branch+sort+order;
	if(page==""){
		page = 0;
	}
	if(url=="undefined"||url==null){
		url = "index.php";
	}
	
 	querystr = "?"+querystr+"&page="+page+"&userid="+userid;
	location.href=url+querystr;
 }
 
 function loading(){
	 if (document.all) document.body.style.cssText="overflow:hidden;"
 }
 
 