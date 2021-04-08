function toggle(id){
	menu = document.getElementById(id);
	var state = menu.style.display; 
	if (state == 'block') {
		state = 'none';
	}
	else {
		state = 'block';
	}		
	if (document.getElementById) {
		menu.style.display = state;
	}
}
function toggle_rsvnchk(id){
	viewDateSpan = document.getElementById("rsvnviewlimit");
	editDateSpan = document.getElementById("rsvneditlimit");
	viewDateChk = document.getElementById("appt_viewchk");
	editDateChk = document.getElementById("appt_editchk");
	rsvnviewimg = document.getElementById("rsvnviewImg");
	rsvneditimg = document.getElementById("rsvneditImg");
	
	if (id == "appt_editchk" && editDateChk.value==0) {
		editDateChk.value = 1;
		editDateSpan.style.display = 'block';
	}else if(id == "appt_editchk"){
		viewDateChk.value = 0;
		editDateChk.value = 0;
		viewDateSpan.style.display = 'none';
		editDateSpan.style.display = 'none';
	}
	
	if (id == "appt_viewchk" && viewDateChk.value==0) {
		viewDateChk.value = 1;
		editDateChk.value = 1;
		viewDateSpan.style.display = 'block';
		editDateSpan.style.display = 'block';
	}else if(id == "appt_viewchk"){
		viewDateChk.value = 0;
		viewDateSpan.style.display = 'none';
	}
	
	//alert("viewDateChk: "+viewDateChk.value+"\n editDateChk: "+editDateChk.value);
	if(viewDateChk.value==0){
		rsvnviewimg.src = "/images/triState/triState0.gif";
	}else{
		rsvnviewimg.src = "/images/triState/triState4.gif";
	}
	
	if(editDateChk.value==0){
		rsvneditimg.src = "/images/triState/triState0.gif";
	}else{
		rsvneditimg.src = "/images/triState/triState4.gif";
	}
	//For check customize group permission in user page.
	checkCustomizedPermission();
}
/*
 * Function partialCheckBox();
 * For check partial permission in group user.
 * id - page id of check box.
 * state - state of check box ( view/edit ).
 * parentId - page parent id of each check box. 
 */
function partialCheckBox(id,state,parentId){
	var partialState = 0; //0 - uncheck all, 1 - check all, 2 - partial
	
	var nodeValue = document.getElementById(state+"["+id+"]");
	var nodeImg = document.getElementById(state+"Parent"+id+"Img");
	
	//if now state is view. Another node mean is node of edit.
	//if now state is edit. Another node mean is node of view.
	var anotherNodeValue;
	var anotherNodeImg;
	
	var parentValue = document.getElementById(state+"["+parentId+"]");
	var parentImg = document.getElementById(state+"Parent"+parentId+"Img");
	
	var imgDir = "/images/triState/";
	var allPageId = document.getElementById("allPageId");
	
	//split all page id to array().
	allPageId = allPageId.value.split("|");
	
	//Check node is image or not.
	//If is image this node have childs.
	//If node value = 0 or 2 (uncheck all or partial) will set node value = 1 (check all)
	//If node value = 1 (check all) will set node value = 0 (uncheck all)
	if(nodeImg != null){
		if(nodeValue.value == 0 || nodeValue.value ==2){
			nodeValue.value = 1;
		}else if(nodeValue.value == 1){
			nodeValue.value = 0;
		}
		nodeImg.src = imgDir+"triState"+nodeValue.value+".gif";
		
		//Set all child node value like value of this node.
		updateChildNodeValue(id,state,allPageId,imgDir,nodeValue.value,1);
		
		if(state == "view"){
			anotherNodeValue = document.getElementById("edit["+id+"]");
			anotherNodeImg = document.getElementById("editParent"+id+"Img");
			if(nodeValue.value == 0){
				anotherNodeValue.value = 0;
				anotherNodeImg.src = imgDir+"triState"+anotherNodeValue.value+".gif";
				updateChildNodeValue(id,"edit",allPageId,imgDir,anotherNodeValue.value,1);
			}
		}else{
			anotherNodeValue = document.getElementById("view["+id+"]");
			anotherNodeImg = document.getElementById("viewParent"+id+"Img");
			if(nodeValue.value == 1){
				anotherNodeValue.value = 1;
				anotherNodeImg.src = imgDir+"triState"+anotherNodeValue.value+".gif";
				updateChildNodeValue(id,"view",allPageId,imgDir,anotherNodeValue.value,1);
			}
		}
		//For update image when mouse over.
		mouseOver(state,id);
	}else{
		if(state == "view"){
			anotherNodeValue = document.getElementById("edit["+id+"]");
			if(!nodeValue.checked){
				anotherNodeValue.checked = false;
			}
		}else{
			anotherNodeValue = document.getElementById("view["+id+"]");
			if(nodeValue.checked){
				anotherNodeValue.checked = true;
			}
		}
	}
	
	partialState = checkNodeValue(state,allPageId,parentId);
	
	//Set image and value to parent of this node.
	if(parentImg != null){
		parentImg.src = imgDir+"triState"+partialState+".gif";
		parentValue.value = partialState;
	}
	
	//Update image and value of all parent.
	updateParentOfNodeValue(parentId,state,allPageId,imgDir);
	if(state == "view"){
		updateParentOfNodeValue(id,"edit",allPageId,imgDir);
	}else{
		updateParentOfNodeValue(id,"view",allPageId,imgDir);
	}
	
	//For check customize group permission in user page.
	checkCustomizedPermission();
}
/** Function updateParentOfNodeValue()
 * For find all parent of node selected and check value of parent.
 * # recursive function.
 * id - parent id of node selected.<b> 
 * state - state of node selected is view or edit.
 * allPageId - array of all page id.
 * imgDir - image directory of tri - state
 */
function updateParentOfNodeValue(id,state,allPageId,imgDir){
	for(j=0;j<allPageId.length;j++){
	
		//Check this id has parent.
		if(document.getElementById(id+"_"+allPageId[j])){
			
			var partialState = checkNodeValue(state,allPageId,allPageId[j]);
			var parentValue = document.getElementById(state+"["+allPageId[j]+"]");
			var parentImg = document.getElementById(state+"Parent"+allPageId[j]+"Img");
			
			if(parentImg != null){
				parentImg.src = imgDir+"triState"+partialState+".gif";
				parentValue.value = partialState;
			}
			
			updateParentOfNodeValue(allPageId[j],state,allPageId,imgDir);
		}	
	}
}

/* Function checkNodValue()
 * Check all node which have same parent have same value. 
 * state - state of node selected is view or edit.
 * allPageId - array of all page id.
 * Return - partial state ( 0 - uncheck all, 1 - check all or 2 - partial ).
 */
function checkNodeValue(state,allPageId,parentId){
	var partialState = 0; //0 - uncheck all, 1 - check all, 2 - partial
	var nodeState = 0; 
	var checkPartialFirst = true;
	
	for(i=1;i<allPageId.length;i++){
		
		//For check child node check box value. 
		//if childImg = null this child node is check box.
		//if childImg != null this child node is directory.
		var childImg = document.getElementById(state+"Parent"+allPageId[i]+"Img");
		var childNode = document.getElementById(allPageId[i]+"_"+parentId); 
		var childValue = document.getElementById(state+"["+allPageId[i]+"]");
		
		if( childNode != null  && childImg == null){
			var value = ((childValue.checked == true)?1:0);
			
			if(checkPartialFirst){
				nodeState = value;
				checkPartialFirst = false;
			}
			if(nodeState != value){
				partialState = 2;
			}
			
		}else if(childNode != null && childImg != null){
			if(checkPartialFirst){
				nodeState = childValue.value;
				checkPartialFirst = false;
			}
			if(nodeState != childValue.value){
				partialState = 2;
			}
		}
	
	}
	if(partialState == 2){
		partialState = 2;
	}else if(nodeState == 1){
		partialState = 1;
	}else{
		partialState = 0;
	}
	return partialState;

}
/** Function setChildNodeValue()
 * change function name to updateChildNodeValue()
 * For set value to each child node.
 * parentId - id of node selected will be parent id of child node.
 * state - state of node selected is view or edit.
 * allPageId - array of all page id.
 * imgDir - image directory of tri - state
 * value - value of node selected is 0 or 1 (0 - uncheck all, 1 - check all). 
 * nowI - value of i before use recursive function.
 * Return - this function return value of i.
 */
function updateChildNodeValue(parentId,state,allPageId,imgDir,value,nowI){
	for(i=1;i<allPageId.length;i++){
		
		//For set child node value which have child.
		//if childImg = null this child node is check box.
		//if childImg != null this child node is directory.
		var childImg = document.getElementById(state+"Parent"+allPageId[i]+"Img");
		var childNode = document.getElementById(allPageId[i]+"_"+parentId);
		var childValue = document.getElementById(state+"["+allPageId[i]+"]");
		
		if(childNode != null && childImg != null){
			childImg.src = imgDir+"triState"+value+".gif";
			childValue.value = value;
			
			i = updateChildNodeValue(allPageId[i],state,allPageId,imgDir,value,i);
		}
		if(childNode != null && childImg == null ){
		//For set child node check box value.
			if(value == 1){
				childValue.checked = true;
			}else{
				childValue.checked = false;
			}
		}
	}
	return nowI;
}
/** Function initaialPartialCheckBox()
 * For initial check box permission interface.
 * Recursive function.
 * parentId - parent id of node want to initial.
 * state - state of node is view or edit.
 * nowJ - value of j before use recursive function.
 */
 function initialPartialCheckBox(parentId,state,nowJ){
 	var imgDir = "/images/triState/";
	var allPageId = document.getElementById("allPageId");
	
	allPageId = allPageId.value.split("|");
	
	for(j=1;j<allPageId.length;j++){
		var childImg = document.getElementById(state+"Parent"+allPageId[j]+"Img");
		var childNode = document.getElementById(allPageId[j]+"_"+parentId);
		
		if(childNode != null && childImg != null){
			j=initialPartialCheckBox(allPageId[j],state,j);
		}
	}
	
	var partialState = checkNodeValue(state,allPageId,parentId);
	var parentValue = document.getElementById(state+"["+parentId+"]");
	var parentImg = document.getElementById(state+"Parent"+parentId+"Img");
			
	if(parentImg != null){
		parentImg.src = imgDir+"triState"+partialState+".gif";
		parentValue.value = partialState;
	}
	
	return nowJ;
 }
/* Function checkCustomizedPermission()
 * Use in usermg/user/addinfo.php
 * For check if user click any box below group line.
 * Status in drop-down box of group will change to Customized. 
 */
function checkCustomizedPermission(){
	//For check customize group permission in user page.
	//Check has object form name user or not. 
	if(document.getElementById("user") != null){
		var objGroup = document.user.group_id;
		var groupIndexBeforeChange = document.getElementById("groupIndexBeforeChange");
		if(objGroup[(objGroup.length-1)].text != 'Customized'){
			
			//For add new element (Customized) in to group drop-down box.
			var newOption = document.createElement("OPTION");
			newOption.text = 'Customized';
			newOption.value = 'customized';
			objGroup.options.add(newOption);
			objGroup.selectedIndex = objGroup.length-1;
			groupIndexBeforeChange.value = objGroup.length-1;
			
		}
	}
}

function collapse_all(){
	var arr = new Array(); 
	arr = document.getElementsByTagName("span");
	for(var i=0; i < arr.length; i++){
			if(i>2){document.getElementsByTagName("span").item(i).style.display = 'none';}
		
	}
}
function expand_all(){
	var arr = new Array(); 
	arr = document.getElementsByTagName("span");
	for(var i=0; i < arr.length; i++){
		if(i>2){document.getElementsByTagName("span").item(i).style.display = 'block';}
	}
}
function getFormParaValue(dname,tableName) {
   	xmlDoc = this.loadXMLDoc(dname);
	var e = xmlDoc.getElementsByTagName(tableName)[0].getElementsByTagName('field');
   	var i,t="";
   	var n="";
   	var frmvalue = "";
   	
    for(i=0; i<e.length; i++) {
		if(e[i].getAttribute('idfields')!="yes"&&e[i].getAttribute('showinform')!="no"){
			 n = e[i].getAttribute('name');
		     if(i)
		         t+= "&";
		      
		      if(e[i].getAttribute('formtype')=="textarea"){
		    		frmvalue = document.getElementById(n).value.replace(/\+/g,"%2B");
		    		frmvalue = frmvalue.replace(/\n/g,"[br]");
					t += n+"="+frmvalue.replace(/\&/g,"%26");
			  } else if(e[i].getAttribute('formtype')!="checkbox"&&e[i].getAttribute('formtype')!="password"){
		     	 	frmvalue = document.getElementById(n).value.replace(/\+/g,"%2B");
					t += n+"="+frmvalue.replace(/\&/g,"%26");
		      } else if(e[i].getAttribute('formtype')=="checkbox"&&document.getElementById(n).checked){
		      	 	t += n+"=1";
		      } else if(e[i].getAttribute('formtype')=="checkbox"&&!document.getElementById(n).checked){
		      	 	t += n+"=0";
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
   if(document.getElementById("id")!=null&&document.getElementById("add").value==" save change "){t+="&id="+document.getElementById("id").value;}
   return t;
}

function editData(table,id){
	var querystr='';
	var showInactive='';
	var showDetail='';
	var search='';
	var order='';
	var page='';
	var sort='';
	var category='';
	var branch='';
	var city='';
	var first="&first=1";// For check first time  when access to edit user page.
	
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
	if(document.getElementById('showdetail')!=null && document.getElementById('showdetail').checked == true){
		showDetail= '&showdetail='+document.getElementById('showdetail').checked;
	}
	if(document.getElementById('categoryid')!=null && document.getElementById('categoryid').value != ''){
		category= '&categoryid='+document.getElementById('categoryid').value;
	}
	if(document.getElementById('branchid')!=null && document.getElementById('branchid').value != ''){
		branch= '&branchid='+document.getElementById('branchid').value;
	}
	if(document.getElementById('cityid')!=null && document.getElementById('cityid').value != ''){
		city= '&cityid='+document.getElementById('cityid').value;
	}
	
	 var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	 querystr += "table="+table+"&id="+id+"&pageid="+pageid+showInactive+showDetail+search+page+order+sort+category+branch+city+first;
	 
	 gotoURL("addinfo.php?"+querystr);
}
function set_editData(table){
	var d='';
	var querystr='';
	if (document.getElementById('querystr')!=null&&document.getElementById('querystr').value){
		querystr = document.getElementById('querystr').value;
	}
	d = this.getFormParaValue('../object.xml',table);
	
	// In querystr variable has pageid variable.
	d += "&method=edit"+querystr;
	var uschk = "";
	if (document.getElementById('uschk')!=null&&document.getElementById('uschk').checked){
		uschk = "&uschk="+document.getElementById('uschk').value;
	}
	var srchk = "";
	if (document.getElementById('srchk')!=null&&document.getElementById('srchk').checked){
		srchk = "&srchk="+document.getElementById('srchk').value;
	}
	var logviewchk = "";
	if (document.getElementById('logviewchk')!=null&&document.getElementById('logviewchk').checked){
		logviewchk = "&logviewchk="+document.getElementById('logviewchk').value;
	}
	var appt_viewchk = "";var appt_editchk = "";
	if (document.getElementById('appt_viewchk')!=null&&document.getElementById('appt_viewchk').value>0){
		appt_viewchk = "&appt_viewchk="+document.getElementById('appt_viewchk').value;
		previewdate = "&previewdate="+document.getElementById('previewdate').value;
		viewdateafter = "&viewdateafter="+document.getElementById('afterviewdate').value;
		appt_viewchk = appt_viewchk + previewdate + viewdateafter;
	}
	if (document.getElementById('appt_editchk')!=null&&document.getElementById('appt_editchk').value>0){
		appt_editchk = "&appt_editchk="+document.getElementById('appt_editchk').value;
		preeditdate = "&preeditdate="+document.getElementById('preeditdate').value;
		editdateafter = "&editdateafter="+document.getElementById('aftereditdate').value;
		appt_editchk = appt_editchk + preeditdate + editdateafter;
	}
	
	//Check has object form name user or not. 
	if(document.getElementById("user") != null){
		var objGroup = document.getElementById('group_id');
		d += "&group_id="+objGroup[objGroup.selectedIndex].value;
	}
	
	d += "&cms_update_time="+document.getElementById("cms_update_time").value;
	d += uschk + srchk + appt_viewchk + appt_editchk+logviewchk; 
	d += "&add="+document.getElementById("add").value; 
	
	var allPageId = document.getElementById("allPageId");
	var count = 0;
	allPageId = allPageId.value.split("|");
	
	var pagepermission = new Array();
	for(j=1;j<allPageId.length;j++){
		var view = document.getElementById("view["+allPageId[j]+"]");
		var edit = document.getElementById("edit["+allPageId[j]+"]");
		var nodeImg = document.getElementById("viewParent"+allPageId[j]+"Img");
		
		if(nodeImg == null){
			//This node is check box.
			if(view.checked && edit.checked){
				pagepermission[count]= allPageId[j]+"_e";
				count++; 
			}else if(view.checked){
				pagepermission[count]= allPageId[j]+"_v";
				count++;
			}
		}else{
			//This node is image.
			if( (view.value == 1 && edit.value == 1) 
				  || (view.value == 2 && edit.value == 2)
				  || (view.value == 1 && edit.value == 2)){
				  
				pagepermission[count]= allPageId[j]+"_e";
				count++;
			}else if(view.value == 1 || view.value == 2){
				pagepermission[count]= allPageId[j]+"_v";
				count++;
			}
		}
		
	}
	d += "&pagepermission="+pagepermission;
	//alert(pagepermission);
	//alert(d);
	gotoURL("addinfo.php?"+d+"&first=1");  
}

/* Function confirmDialog()
 * For check user confirm to change group template.
 * objThis - object of drop-down box.
 */
function confirmDialog(objThis){
	var confirmValue = false;
	if(objThis.selectedIndex == 0){
		confirmValue = confirm('You want unset template permission.');
	}else{
		confirmValue = confirm('You want use '+objThis.options[objThis.selectedIndex].text+' template permission.');
	}
	
	if(!confirmValue){
		var groupIndexBeforeChange = document.getElementById("groupIndexBeforeChange");
		objThis.selectedIndex = groupIndexBeforeChange.value;	
	}else{
		xmlDoc = this.loadXMLDoc('../object.xml');

	    var e = xmlDoc.getElementsByTagName("s_user")[0].getElementsByTagName('field');
	    var i,d="";
	    var n="";
	    
	    for(i=0; i<e.length; i++) {
			if(e[i].getAttribute('idfields')!="yes"&&e[i].getAttribute('showinform')!="no"){
				 n = e[i].getAttribute('name');
				 //alert(n);
			    if(i)
			    	d+= "&";
			    if(e[i].getAttribute('formtype')!="checkbox"&&e[i].getAttribute('formtype')!="password"){
			    	d += n+"="+document.getElementById(n).value;
				} 
			}
	    }
	   d+="&formname=s_user";
	   if(document.getElementById("id")!=null&&document.getElementById("add").value==" save change "){d+="&id="+document.getElementById("id").value;}
	   //show.innerHTML = t;
	   d+= "&group_id="+objThis.options[objThis.selectedIndex].value;
	   d+= document.getElementById("querystr").value;
	   gotoURL("addinfo.php?"+d);  
	}
	
}

function checkDateBox(id,state,editView){
	var thisBox = document.getElementById(id);
	if(isNaN(thisBox.value) || thisBox.value==""){
		alert("Please put numeric only.");
		thisBox.value = 0;
	}else{
		thisBox.value = parseInt(thisBox.value,10);
	}
	
	var viewDate = document.getElementById(state+"viewdate");
	var editDate = document.getElementById(state+"editdate");
	
	if(parseInt(editDate.value,10) > parseInt(viewDate.value,10) && editView=="edit"){
		viewDate.value = editDate.value;
	}else if(parseInt(editDate.value,10) > parseInt(viewDate.value,10) && editView=="view"){
		editDate.value = viewDate.value;
	}
	//For check customize group permission in user page.
	checkCustomizedPermission();
}
 /**
  * function mouseOver()
  * For switch icons also preloads the icons if they are not already.
  * state - state of node.
  * id - id of node.
  */
  function mouseOver(state,id) {
    var nodeImg = document.getElementById(state+"Parent"+id+"Img");
    var node = document.getElementById(state+"["+id+"]");
    var imgDir = "/images/triState/";
    var imgFile = '';
    
    if(node.value==1){
    	imgFile = '11';
    }else if(node.value==2){
    	imgFile = '21';
    }else if(node.value==0){
    	imgFile = '01';
    }
    nodeImg.src = imgDir+"triState"+imgFile+".gif";
  }
 
 
 /**
  * function changepic()
  * For switch image picture when some event handler
  * pic - picture path that want to change
  * id - id of image that want to change src
  */
 function changepic(pic,id){
 	var obj = document.getElementById(id);
 	obj.src = pic;
 }
 
 /**
  * function mouseOut()
  * For switch icons also preloads the icons if they are not already.
  * state - state of node.
  * id - id of node.
  */
  function mouseOut(state,id) {
    var nodeImg = document.getElementById(state+"Parent"+id+"Img");
    var node = document.getElementById(state+"["+id+"]");
    var imgDir = "/images/triState/";
    var imgFile = '';
    
    if(node.value==1){
    	imgFile = '1';
    }else if(node.value==2){
    	imgFile = '2';
    }else if(node.value==0){
    	imgFile = '0';
    }
    nodeImg.src = imgDir+"triState"+imgFile+".gif";
  }
  
function showInactive(url) {
	sortInfo('','1');
}
function showDetail(url) {
	sortInfo('','1');
}
function setEnable(table,id,active){
	var querystr='';
	var showInactive='';
	var showDetail='';
	var search='';
	var order='';
	var page='';
	var sort='';
	var category='';
	var branch='';
	var city='';
	
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
	if(document.getElementById('showdetail')!=null && document.getElementById('showdetail').checked == true){
		showDetail= '&showdetail='+document.getElementById('showdetail').checked;
	}
	if(document.getElementById('categoryid')!=null && document.getElementById('categoryid').value != ''){
		category= '&categoryid='+document.getElementById('categoryid').value;
	}
	if(document.getElementById('branchid')!=null && document.getElementById('branchid').value != ''){
		branch= '&branchid='+document.getElementById('branchid').value;
	}
	if(document.getElementById('cityid')!=null && document.getElementById('cityid').value != ''){
		city= '&cityid='+document.getElementById('cityid').value;
	}
	
	 querystr += "method=setactive&active="+active+"&table="+table+"&id="+id+showInactive+showDetail+search+page+order+sort+category+branch+city;
	 var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	 querystr += "&pageid="+pageid;
	 gotoURL("addinfo.php?"+querystr);
}
function sortInfo(order,page){
	// branch/room information
	var categoryid = "";
	var search = "";
	if(document.getElementById('search')!=null){
		search = document.getElementById("search").value.replace("+","%2B");
		search = search.replace("&","%26");
	 	search = "&search="+search;
	}
	
	if(document.getElementById('categoryid')!=null){
	 	categoryid = document.getElementById("categoryid").value
		categoryid = "&categoryid="+categoryid;
	 }
	 // employee information
	var showInactive = "";
	if(document.getElementById('showinactive')!=null){
		if(document.getElementById('showinactive').checked == true){
			showInactive= '&showinactive='+document.getElementById('showinactive').value;
		}else{showInactive= '&showinactive=0';}
	}
	var showDetail = "";
	if(document.getElementById('showdetail')!=null){
		if(document.getElementById('showdetail').checked == true){
			showDetail= '&showdetail='+document.getElementById('showdetail').value;
		}else{showDetail= '&showdetail=0';}
	}
	var categoryid = "";
	if(document.getElementById('categoryid')!=null){
	 	categoryid = document.getElementById("categoryid").value
		categoryid = "&categoryid="+categoryid;
	 }
	var branchid = "";
	if(document.getElementById('branchid')!=null){
	 	branchid = document.getElementById("branchid").value
		branchid = "&branchid="+branchid;
	 }
	var cityid = "";
	if(document.getElementById('cityid')!=null){
	 	cityid = document.getElementById("cityid").value
		cityid = "&cityid="+cityid;
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
	
	var querystr="sort="+sort+"&page="+page+"&order="+order+cityid+categoryid+search+showInactive+showDetail+branchid;
	var pageid = window.top.document.getElementById("leftFrame").contentWindow.document.getElementById("pageid").value;
	querystr = querystr+"&pageid="+pageid;
	gotoURL("index.php?"+querystr);  
}