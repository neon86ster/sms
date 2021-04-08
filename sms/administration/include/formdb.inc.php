<?php
/*
 * File name : cms.inc.php
 * Description : Class file which is main controller for cms system
 * Author : art
 * Create date : 22-Oct-2008
 * Modified : natt@chiangmaioasis.com
 */   
require_once("main.inc.php");

class formdb extends main {

/*
 * function to convert special charactors to html entitries ex. < to &lt, > to &gt
 * @param - Input text
 */	
	function encodeText($a) { // use for enctype word to html text
		return htmlspecialchars($a);
	}
	
/*
 * function for get name of namefield by id of idfield form  xml command
 * @param - XML command
 * @patern of $xml command for name() method
 * $xml ="<command>".
 * "<table>tablename you need to get information</table>".
 * "<namefield name='field name need to get information'/>".
 * "<idfield name='field name use to check with key'>key</idfield>".
 * "</command>";
 */	
 	function getNameFormId($xml,$filename='object.xml',$debug=false) {
		$e = simplexml_load_string($xml);
	
		// initial parameter from $xml => string command
		$table = $e->table;		
		$id = $e->idfield;
		$idfield = $e->idfield["name"];
		$namefield = $e->namefield["name"];
		$sql = "select $namefield from $table where $idfield = $id limit 1";
		$xml = "<command>" .
				"<sql>$sql</sql>" .
				"</command>";
		if($debug){
			echo $sql."<br/>";
		}
		$rs = $this->getRsXml($xml,$filename,$debug);
		if(!$rs)
			return false;
		else
			return $rs[0]["$namefield"];
 	}


/*
 * function for Generate tag <select>...</select>
 * can use javascript control the value when select..
 * @param - XML control parameter in selectbox
 * @param - incoming parameter (first value that show in selectbox)
 * @param - Set debug msg form connection class
 * @patern of $xml command for name() method
 * $ff ="<field 
 * 			name="field name of selectbox" 
 * 			table="table name form database" 
 * 			javascript="javascript control the value when select this form" 
 * 			first="first option value in selectbox set this='no' when don't want'"
 * 		/>";
 */ 	
 	function gSelectBox($ff,$filename='object.xml',$value=false,$debug=false) {
 		$f = simplexml_load_file($filename);
 		$tbname = $ff["table"];
 		$element = $f->table->$tbname;
 		$namefield = $element->namefield["name"];
 		$idfield = $element->idfield["name"];
 		$sortby = $element->sortby["name"];
 		
 		foreach($element->field as $field) {
 			if($field["formname"]=="Enable") {$activefield=$field["name"];}
 		}
 		$income = "";
 		if($value) {$income=", incoming parameter: ".$value;}
 		
 		$xml = "<command>".
 				"<table>$tbname</table>";
 		if($sortby!=""){
 			//echo "true<br>";
 			$xml .="<order>".$element->field["name"]."</order>";
 		}else{
 			//echo "false<br>";
			$xml .="<order>$namefield</order>";
 		}
 		
 		if($activefield){$xml.="<where name='$activefield' operator='='>1</where>";}
 		$xml .=	"</command>";
 		$rs = $this->getRsXML($xml,$filename,$debug);
 		
 		$textout = "<select name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" ".$ff["javascript"]."> \n";
 		
 		if($ff["first"]!="no"&&$ff["first"]!=false) {$textout .= "<option value='0'>".$ff["first"]."</option> \n";}
 		
 		for($i=0; $i<$rs["rows"];$i++) {
 			$selected = ($value==$rs[$i]["$idfield"])?'selected':'';
 			$textout .= "<option value=\"".$rs[$i]["$idfield"]."\" $selected >".$rs[$i]["$namefield"]."</option> \n";
 		}
 		$textout .= "</select> \n";
 		
 		if($this->getDebugStatus()) {
 			$this->printDebug("formdb.gSelectBox()","generate list box already..component name: ".$ff["name"]." ".$income);
 		}
 		
 		return $textout." \n";
 	}		
 
/*
 * function for Generate Add form from XML file
 * @param - Tablename form database
 * @param - Set debug msg form connection class
 */ 	
 	function gFormInsert($tbname,$filename='object.xml',$debug=false) {
 		
		$f = simplexml_load_file($filename);
		
		$element = $f->table->$tbname;
		$action = $element["action"];
		$enctype = $element["enctype"];
		$usetable = $element["useTable"];
		
		//$textout = "<form name='$tablename' action='$action' enctype='$enctype' method='post'>\n";
		$textout .= "<table class=\"generalinfo\">";
		foreach($element->field as $field) { // start loop xml
			$name = $field["name"];
			
			if($field["defaultvalue"]=="__get")
				$defaultvalue = $_GET["$name"];
			else if($field["defaultvalue"]=="__post")
				$defaultvalue = $_POST["$name"];
			else if(isset($field["defaultvalue"]))
				$defaultvalue = $field["defaultvalue"];
			else
				$defaultvalue = false;
				 
			if($usetable == "yes") {
				
				if($field["showinformAdd"] != "no" && $field["showinform"] != "no" && $field["formtype"]!="hidden" && $field["formtype"]!="password"){
					
					$textout .= "<tr>\n";
					$textout .= "<td valign='top'>".$field["formname"];
					if($field["prior"]){$textout .= "<font style='color:#ff0000''> ".$field["prior"]."</font> ";}
					$textout .= "</td>\n";
					$textout .= "<td valign='top'>";
					
					if($field["formtype"]=="text" || $field["formtype"]=="button" || $field["formtype"]=="submit" || $field["formtype"]=="reset" || $field["formtype"]=="button"){
						$textout .= "<input id='".$field["name"]."' type='".$field["formtype"]."' name='".$field["name"]."' size='".$field["size"]."' value='$defaultvalue' ".$field["javascript"].">";
					}
					else if($field["formtype"]=="textarea") {
						$textout .= "<textarea id='".$field["name"]."' name='".$field["name"]."' cols='".$field["cols"]."' rows='".$field["rows"]."' ".$field["javascript"].">$defaultvalue</textarea>";
					}
					else if($field["formtype"]=="checkbox") {
						if($field["defaultvalue"]=="__post"&&$_POST["$name"]==1) {$selected = "checked";} else {$selected = "";}
						$textout .= "<input id='".$field["name"]."' type='".$field["formtype"]."' name='".$field["name"]."' value='$defaultvalue' ".$field["javascript"]." $selected>";
					}
					else if($field["formtype"]=="textlink"){
						$textout .= "<a href='".$field["href"]."' ".$field["target"].">".$field["description"]."</a> ";
					}
	 				else if($field["formtype"]=="select") {
	 					$textout .= $this->gSelectBox($field,$filename,$defaultvalue,$debug);
	 				}else if($field["formtype"]=="date"){
	 					$textout .= "<form name'$name'>&nbsp;&nbsp;<input id='$name' name='$name' value='".((isset($_POST["$name"]))?$defaultvalue:date('d-M-Y'))."' style=\"width: 85px;\" readonly=\"1\" class=\"textbox\" type=\"text\">
							            &nbsp;&nbsp;<img src=\"$imgsrc\" onclick=\"showChooser(this, '$name', '".$field["name"]."_showSpan', 1900, 2100, 'd-M-Y', false);\"> 
										<div id=\"".$field["name"]."_showSpan\" class=\"dateChooser\" style=\"display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;\" align=\"center\"></div>" .
									"</form>";
	 				}		
					$textout .= "</td>\n";
					$textout .= "</tr>\n";
				}else if($field["formtype"]=="password"){
	 				$textout .= "<tr> \n";
	 				$textout .= "<td valign='top'>Password";
					$textout .= "</td> \n";
	 				$textout .= "<td valign='top'> \n";
	 				$textout .= "<input name=\"".$field["name"]."\" id=\"pass\" type=\"".$field["formtype"]."\" size=\"".$field["size"]."\" value=\"\" ".$field["javascript"]." > \n";
					$textout .= "</td>\n";
					$textout .= "</tr>\n";
	 				$textout .= "<tr> \n";
	 				$textout .= "<td valign='top'>Retype password";
					$textout .= "</td> \n";
	 				$textout .= "<td valign='top'> \n";
	 				$textout .= "<input name=\"".$field["name"]."\" id=\"rpass\" type=\"".$field["formtype"]."\" size=\"".$field["size"]."\" value=\"\" ".$field["javascript"]." > \n";
					$textout .= "</td>\n";
					$textout .= "</tr>\n";
	 			}else {
					$textout .= "<input id='".$field["name"]."' name='".$field["name"]."' type='".$field["formtype"]."' value='".$defaultvalue."'>\n";
				}
			} 
		} // end loop xml
		
		$textout .= "<input name='formname' id='formname' type='hidden' value='$tbname'>\n";
		$textout .= "</table>\n";
		//$textout .= "</form>\n";
        return $textout;
		
 	}
 	
/*
 * function for Generate edit form from XML file
 * @param - XML command
 * @param - Set debug msg form connection class
 * @patern1 of $xml command for gFormEdit() method
 * $xml ="<command>".
 * "<table>tablename you need to edit the information</table>".
 * "<where name='fieldname' operator='query string operator'>scope value of field name</where>".
 * "</command>";
 * @patern2 of $xml command for gFormEdit() method
 * $xml ="<command>".
 * "<table>tablename you need to edit the information</table>".
 * "<sql>Query string A SQL Query</sql>".
 * "</command>";
 */	
 	function gFormEdit($xml, $filename='object.xml', $debug=false) {
 		$e = simplexml_load_string($xml);
 		$f = simplexml_load_file($filename);
 		
 		$tbname = $e->table;
 		$element = $f->table->$tbname;
 		$method = $this->checkParameter($element["method"],"post");
 		$action = $element["action"];
 		$enctype = $element["enctype"];
 		$usetable = $element["useTable"];
 		
 		$rs = $this->getRsXML($xml,$filename,$debug);
 		
 		//$textout = "<form name='$tbname' action='$action' enctype='$enctype' method='$method'> \n"; 		
 		$textout .= "<table class=\"generalinfo\">\n";
 		foreach($element->field as $ff) {
 			$name = $ff["name"];
 			
 			$defaultvalue = false;
			if($ff["defaultvalue"]=="__get"){
				$defaultvalue = (isset($_GET["$name"]))?$_GET["$name"]:false;
			}else if($ff["defaultvalue"]=="__post"){
				$defaultvalue = (isset($_POST["$name"]))?$_POST["$name"]:false;
			}else if(isset($ff["defaultvalue"])){
				$defaultvalue = $ff["defaultvalue"];
			}
 			if($rs[0]["$name"] && $defaultvalue===false ){
 				$defaultvalue = $rs[0]["$name"];
 			}
				
 			if($ff["formtype"] == "submit" || $ff["formtype"] == "button") {
 				$defaultvalue = " save change ";
 			}
 			
	 			if($ff["showinformEdit"]!="no" && $ff["showinform"]!="no" && $ff["formtype"]!="hidden" && $ff["formtype"]!="password") {
	 				$textout .= "<tr> \n";
	 				$textout .= "<td valign='top'>".$ff["formname"];
					if($ff["prior"]){$textout .= "<font style='color:#ff0000''> ".$ff["prior"]."</font> ";}
					$textout .= "</td> \n";
	 				$textout .= "<td valign='top'> \n";
	 				
	 				if(($ff["formtype"]=="text" || $ff["formtype"]=="file" || $ff["formtype"]=="submit" || $ff["formtype"]=="reset" || $ff["formtype"]=="button")&&$ff["updatein"]=="") {
	 					$textout .= "<input name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" type=\"".$ff["formtype"]."\" size=\"".$ff["size"]."\" value=\"".$defaultvalue."\" ".$ff["javascript"]." > \n";
	 				}
	 				else  if($ff["formtype"]=="text" && $ff["updatein"]=="l_tax") {
	 					$sql="select * from l_tax where tax_id =".$defaultvalue;
	 					$xml = "<command>" .
 	 						"<sql>".$sql."</sql>" .
 							"</command>";
 						$rs2 = $this->getResult($sql,$debug);
	 					$textout .= "<input name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" size=\"".$ff["size"]."\" value=\"".$rs2["0"]["tax_percent"]."\" ".$ff["javascript"]." > \n";
	 				}else if($ff["formtype"]=="textarea") {
	 					$textout .= "<textarea name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" size=\"".$ff["size"]."\" ".$ff["javascript"]." >".$defaultvalue."</textarea> \n";
	 				}
	 				else if($ff["formtype"]=="checkbox") {
	 					$selected = ($rs[0]["$name"] == 1)?"checked":"";     
	 					$textout .= "<input name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" type=\"".$ff["formtype"]."\" value=\"1\" ".$ff["javascript"]." $selected> \n";
	 				}
	 				else if($ff["formtype"]=="textlink") {
	 					$textout .= "<a href=\"".$ff["href"]."?".$ff["target"]."\">".$ff["description"]."</a> \n";
	 				}
	 				else if($ff["formtype"]=="select") {
	 					$textout .= $this->gSelectBox($ff,$filename,$defaultvalue,$debug);
	 				}
	 				else if($ff["formtype"]=="date"){
	 					if($tbname=="l_employee"){$imgsrc="../ufscr/datechooser/calendar.gif";}
	 					$textout .= "<form name'$name'>&nbsp;&nbsp;<input id='$name' name='$name' value='".$this->separate_time($defaultvalue,6)."' style=\"width: 85px;\" readonly=\"1\" class=\"textbox\" type=\"text\">
							            &nbsp;&nbsp;<img src=\"$imgsrc\" onclick=\"showChooser(this, '$name', '".$name."_showSpan', 1900, 2100, 'd-M-Y', false);\"> 
										<div id=\"".$name."_showSpan\" class=\"dateChooser\" style=\"display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;\" align=\"center\"></div>" .
									"</form>";
	 				}	
	 				$textout .= "</td> \n";
	 				$textout .= "</tr> \n";
	 			}else if($ff["formtype"]=="password"){
	 				$textout .= "<tr> \n";
	 				$textout .= "<td valign='top'>New password";
					$textout .= "</td> \n";
	 				$textout .= "<td valign='top'> \n";
	 				$textout .= "<input name=\"".$ff["name"]."\" id=\"newpass\" type=\"".$ff["formtype"]."\" size=\"".$ff["size"]."\" value=\"\" ".$ff["javascript"]." > \n";
					$textout .= "</td>\n";
					$textout .= "</tr>\n";
	 				$textout .= "<tr> \n";
	 				$textout .= "<td valign='top'>Retype New password";
					$textout .= "</td> \n";
	 				$textout .= "<td valign='top'> \n";
	 				$textout .= "<input name=\"".$ff["name"]."\" id=\"rnewpass\" type=\"".$ff["formtype"]."\" size=\"".$ff["size"]."\" value=\"\" ".$ff["javascript"]." > \n";
					$textout .= "</td>\n";
					$textout .= "</tr>\n";
	 			}else {
	 				$textout .= "<input name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" type=\"".$ff["formtype"]."\" value=\"".$defaultvalue."\"> \n";
	 			}	
 		}
 		$textout .= "<input name='formname' type='hidden' value=\"".$tbname."\" > \n";
 		$textout .= "</table> \n";
 		//$textout .= "</form> \n";
 		return $textout;
 	}
	
/*
 * function for read formname for post value
 * @param - Post query value
 * Modified in 3-Dec-2008, natt if cause to check numberic of price and hour in db_package 
 */
 	function readForm($post,$filename='object.xml') {
 		$formname = $post["formname"];
 		$f = simplexml_load_file($filename);
 		$tmp = array();
 		$aform = $f->table->$formname;
 		if($formname=="s_user"){
 			if($post["pass"]!=$post["rpass"]){
	 			$this->setErrorMsg("Please check your password value!!");
	 			return false;
	 		}	
 			if($this->checkUser($post["u"],$post["u_id"])){
	 			$this->setErrorMsg("This system already has this account.Please insert new user account value!!");
	 			return false;
	 		}	
 		}
 		
 		$insertField="";
 		$id="";
 		foreach($aform->field as $ff) {
 			if($ff["showinform"] != "no" && $ff["formtype"] != "submit" && $ff["formtype"]!="button" && $ff["formtype"]!="file" && $ff["formtype"]!="textlink" ) {
 				$t = $ff["name"];
 				$tmp["$t"] = $this->checkHiddenValue(htmlspecialchars($post["$t"]));
 				
 				if(($post["$t"]==""&&$ff["prior"]=="*")
 				||($ff["formtype"]=="select"&&$post["$t"]=="0"&&$ff["prior"]=="*")) {
					$this->setErrorMsgColor("#000000");
 					$this->setErrorMsg("Please insert ".$ff["formname"]."!!");
 					//echo "Error: ".$this->getErrorMsg();
 					return false;
 				}
 			}
 			
	 		if($ff["formtype"]=="date"){
	 			$t = $ff["name"];
	 			$tmp["$t"] = "'".$this->separate_time(htmlspecialchars($post["$t"]),5)."'";
	 		}
 		}
 		return $tmp;
 	}

/*
 * function for get information to insert
 * @param - Post query value
 * @param - Set debug msg form connection class
 */
	function readToInsert($post, $filename='object.xml', $debug=false) {
		$id = false;
		$formname = $post["formname"];
		$tmp = $this->readForm($post,$filename);
		if(!$tmp){return false;}
		$key = array_keys($tmp);
		
		$field="";	// field name after take out form type 'submit,button,file and textlink'
		$value="";	// value from form we need to input
		for($i=0; $i<count($key); $i++) {
			$field .= $key[$i];
 			if($key[$i]=="pass"){$value .= "'".md5(str_replace("'","",htmlspecialchars($tmp[$key[$i]])))."'";}
 			else if($key[$i]=="c_lu_user"){
 				$value .= $_SESSION["__user_id"];}
 			else if($key[$i]=="c_lu_date"){
 				$value .= "Now()";}
 			else if($key[$i]=="c_lu_ip"){
 				$value .= "'".$this->getIp()."'";}
 			else{$value .= htmlspecialchars($tmp[$key[$i]]);}
			if($i < (count($key)-1)) {
				$field .= ",";
				$value .= ",";
			}
		}
		//$this->setErrorMsg($field);
 		$sql = "insert into $formname($field) values($value) ";
 		$xml = "<command>" .
 				"<sql>".$sql."</sql>" .
 				"</command>";
 		if($debug){
 			echo $sql."<br/>";
 		}
 		if($this->getDebugStatus()) {
 			$this->printDebug("formdb.readToInsert()","all array count: ".count($key),$sql);
 		}
		$id = $this->setRsXML($xml,true);
		if(!$id) {
			//$this->setErrorMsg("Cann't insert an information!!'");
			$this->printDebug("formdb.readToInsert()","insert information not complete!!",$sql);
		}
		return $id;
	}

	function checkHiddenValue($value) {
		if($value=="thisuser") {
			return "'".$_SESSION["__user_id"]."'";
		}
		else if($value=="thistime") {
			return "Now()";
		}
		else if($value=="thisip") {
			return "'".$this->ip."'";
		} 
		else {
			return "'".$value."'";
		}
	}
	
	function setActive($post, $filename="object.xml", $debug=false){
		$f = simplexml_load_file($filename);
		$tbname = $post["table"];
		$fid = $post["id"];
		$active = $post["active"];
		
		$element = $f->table->$tbname;
		$idfield = $element->idfield["name"];
		$activefield = $element->activefield["name"];
		$namefield = $element->namefield["name"];
		
		$sql = "update ".$tbname." set ".$activefield."=".$active." where ".$idfield."=".$fid;
		
		if($debug){
			echo $sql."<br/>";
		}
		
		$sid = $this->setResult($sql,$debug);
		
		$chksql = "update ".$tbname." set l_lu_user=".$_SESSION["__user_id"].",l_lu_date=Now(),l_lu_ip='".$this->getIp()."' where ".$idfield."=".$fid;
		$cid=$this->setResult($chksql);
		
		if($sid){
			$chkxml="<command>".
				"<table>".$tbname."</table>".
				"<namefield name='$namefield'></namefield>".
				"<idfield name='$idfield'>".$fid."</idfield>".
				"</command>";
			$name = $this->getNameFormId($chkxml,$filename,$debug);
			return $name;
		}else{return false;}
		
	}
	
/*
 * function for get information to update
 * @param - Post query value
 * @param - Set debug msg form connection class
 */	
	function readToUpdate($post, $filename='object.xml', $debug=false) {
 		$formname = $post["formname"];
		$tmp = $this->readForm($post, $filename);
		if(!$tmp){return false;}
		$key = array_keys($tmp);
 		
 		$field = "";
 		$value = "";
 		$set = false;
 		for($i=0; $i<count($key); $i++) {
 			$field = $key[$i];
 			if(str_replace("'","",htmlspecialchars($tmp[$key[$i]]))!=""&&$key[$i]=="pass"){
 				$value = "'".md5(str_replace("'","",htmlspecialchars($tmp[$key[$i]])))."'";
 			}else if(str_replace("'","",htmlspecialchars($tmp[$key[$i]]))==""&&$key[$i]=="pass"){
 				$value = "'".$this->getPass($tmp[$key[0]])."'";
 			}else{
 				$value = htmlspecialchars($tmp[$key[$i]]);
 			}
 			if($set) {
 				$set.=",";
 			}
 			
 			$set.= $field."=".$value;
 		}
 		
 		$sql = "update ".$formname." set ".$set." where ".$key[0]."=".$tmp[$key[0]];
 		$xml = "<command>" .
 				"<sql>".$sql."</sql>".
 				"</command>";
 		//echo $sql;
 		if($debug){
 			echo $sql."<br/>";
 		}
 		$id = $this->setRsXML($xml,$debug);
 		//echo $id;
 		if(!$id) {
 			$this->printDebug("formdb.readToUpdate()",$this->getErrorMsg(),$sql);
 		}
 		
 		return $id;
 	}
 	
/*
 * function for get information to delete
 * @param - Post query value
 * @param - Set debug msg form connection class
 */		
 	function readToDelete($post,$debug=false) {
		
		$id = $post["id"];
		$idname = $post["idname"];
		$table = $post["table"];
		//echo $id."<br>";
		//echo $idname."<br>";
		
		$sql = "delete from $table where $idname=$id ";
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		$xml = "<command>" .
				"<sql>$sql</sql>" .
				"</command>";
		
		$id = $this->setRsXML($xml,$debug);
 		//echo $id;
 		if(!$id) {
 			$this->printDebug("formdb.readToDelete()",$this->getErrorMsg(),$sql);
 		}
		return $id;
 	}		

	function getPass($u_id){
		$sql = "select * from s_user where u_id=".$u_id." ";
	
		$rs = $this->getResult("$sql");
		if($rs["rows"]){
			return $rs[0]['pass'];
		}else {
			return false;
		}
	}
	
/*
 * function for generate text page link for data table
 * @modified - move from report.inc.php natt/20-May-2009
 */
	function gen_page($url=false,$currentpage=false,$total_records=false,$records_per_page=false){
		if(!$records_per_page){$records_per_page=$this->getShowpage();}
		$page_count = $total_records / $records_per_page;
		$page_count = (is_int($page_count))?intval($page_count):intval($page_count)+1;
	
		/*echo '$currentpage: '.$currentpage.'<br /> 
		$total_records: '.$total_records.'<br />
		$records_per_page: '.$records_per_page.'<br />
		$page_count: '.$page_count.'<br /><br /><br />';*/
		//show 10 link. like 1-2-3-4-5-6-7-8-9-10
		$align_links_count=10;
		$max_link = ($page_count>$align_links_count)?$align_links_count:$page_count;
		
		$start_page = "$currentpage";
		$end_page = "$currentpage";
		
		//assign endpage and startpage for pagecount
		while($max_link>0){
			$looped = false;
			if(intval($end_page)<$page_count){
				$end_page++;
                $max_link--;
                $looped = true;
			}
			if($start_page>1&&$max_link!='0'){
				$start_page--;
                $max_link--;
                $looped = true;
			}
			if($looped==false){break;}
		}
		/*echo '$currentpage: '.$currentpage.'<br /> 
		$start_page: '.$start_page.'<br />
		$end_page: '.$end_page.'<br />
		$page_count: '.$page_count.'<br />';*/
		//assign array of number page
		$i=$start_page;
        while($i<=$end_page ){
            if("$i"=="$currentpage"){
                $pagearray[] = '<b>'.$i.'</b>';
            }else{
                $pagearray[] = $this->generate_link($i,$i,$url);
			}
            $i++;
        }
		//add back/forward ( '<' / '>' ) icon
		$use_back_forward = true;
		 if ($use_back_forward==true){
            if($currentpage=='1'){
        		$page_back = "< ";
        	}else{
                $page_back = $this->generate_link("<",($currentpage-1).'',$url).' ' ;
        	}
            if($currentpage>=$page_count){
				$page_fwd = " >";
			}else{
                $page_fwd = ' '.$this->generate_link(">",($currentpage+1).'',$url);
			}
        }
		
		//add back/forward ( '<<' / '>>' ) icon
		$use_first_last = true;
		 if ($use_first_last==true){        
            
            //make the first page url
            if ($currentpage==$start_page){
            	$page_first = '<< ';
            }else{
                $page_first = $this->generate_link('<<','1',$url).' ';
            }
            
            //make the last page url
            if ($currentpage==$end_page){
            	$page_last = ' >>';
            }else{
                $page_last = ' '.$this->generate_link('>>',"$page_count",$url) ;
            }
        }
		
		$textout = implode(' ',$pagearray);
		$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
		$textout .= " / Total ".$page_count."<br /><br />";
		echo $textout;
	}
	
	
/*
 * function for generate text page link for data table
 * @modified - move from report.inc.php natt/20-May-2009
 */	
	function generate_link($inner,$page_number,$url){
		$textout = '';
		$textout.= "<a href=\"javascript:;\" onclick=\"sortInfo('',$page_number)\" class=\"pagelink\">$inner</a>\n";
		return $textout;
	}
}
?>
