/*
 * This file create for page setup only
 * Create on 17-08-2009
 *
 */

/*
 * Function loadPage()
 * For call report and checkDiv() function.
 *
 */
function loadPage(querystr){
	getReturnText('report.php',querystr,'tableDisplay');
	checkDiv();
}
/*
 * Function checkDive()
 * For check page call file report.php finish or not.
 * If call page finish on page will have element checkDive.
 *
 */	
function checkDiv(){
	if(document.getElementById("checkDiv")==null){
		//If element checkDiv is null call recursive function checkDive() every 1 second
		setTimeout(checkDiv, 1000);
	}else{
		//If have element checkDive call function intialPartialCheckBox()
		initialPartialCheckBox(0,"active",1);
	}
}
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
		
		//For update image when mouse over.
		mouseOver(state,id);
	}
	
	partialState = checkNodeValue(state,allPageId,parentId);
	
	//Set image and value to parent of this node.
	if(parentImg != null){
		parentImg.src = imgDir+"triState"+partialState+".gif";
		parentValue.value = partialState;
	}
	
	//Update image and value of all parent.
	updateParentOfNodeValue(parentId,state,allPageId,imgDir);
	
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

function collapse_all(){
	var arr = new Array(); 
	arr = document.getElementsByTagName("span");
	for(var i=0; i < arr.length; i++){
		if(i>1){document.getElementsByTagName("span").item(i).style.display = 'none';}
	}
}
function expand_all(){
	var arr = new Array(); 
	arr = document.getElementsByTagName("span");
	for(var i=0; i < arr.length; i++){
		if(i>1){document.getElementsByTagName("span").item(i).style.display = 'block';}
	}
}

function set_editData(table){
	var d='';
	var querystr='';
	if (document.getElementById('querystr')!=null&&document.getElementById('querystr').value){
		d += document.getElementById('querystr').value;
	}
	d += "&add="+document.getElementById("add").value; 
	
	var allPageId = document.getElementById("allPageId");
	var count = 0;
	allPageId = allPageId.value.split("|");
	
	var pagepermission = new Array();
	for(j=1;j<allPageId.length;j++){
		var active = document.getElementById("active["+allPageId[j]+"]");
		var nodeImg = document.getElementById("activeParent"+allPageId[j]+"Img");
		
		if(nodeImg == null){
			//This node is check box.
			if(active.checked){
				pagepermission[count]= allPageId[j];
				count++;
			}
		}else{
			//This node is image.
			if(active.value == 1 || active.value == 2){
				pagepermission[count]= allPageId[j];
				count++;
			}
		}
	}
	d += "&pagepermission="+pagepermission;
	gotoURL("addinfo.php?"+d);  
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
