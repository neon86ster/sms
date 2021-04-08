<?php
/*
 * File name : cms.inc.php
 * Description : Class file which is main controller for cms system
 * Author : art
 * Create date : 22-Oct-2008
 * Modified : natt@chiangmaioasis.com
 */   
require_once("cms.inc.php");
require_once("date.inc.php");

class formdb extends cms {
	function formdb(){
		$this->recordcount = 0;
		$this->lastinsertid = 0;
		$this->showpage = 20;
		$this->errorcolor = "#FF0000";
		$this->successcolor = "#71A328";
		$this->msg = false;
		$this->successmsg = false;
		$this->errormsg = false;
		$this->userid = @$_SESSION["__user_id"];
		$this->ip = $_SERVER["REMOTE_ADDR"];
	}
	
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
		$sql = "select $namefield from $table where $idfield = $id limit 0,1";
		$xml = "<command>" .
				"<sql>$sql</sql>" .
				"</command>";
		if($debug){
			echo $sql."<br/>";
		}
		$rs = $this->getRsXml($xml,$filename,$debug);
		if(!$rs){
			return false;
		}else{
			return $rs[0]["$namefield"];
		}
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
 	function gSelectBox($ff,$filename='object.xml',$value=false,$debug=false,$xml=false) {
 		//Debug Undefined
 		if(!isset($sta)){$sta="";}
 		if($xml){
 			$e = simplexml_load_string($xml);
 			$sta = $e->table;
 		}
 		//
 		$f = simplexml_load_file($filename);
 		$tbname = $ff["table"];
 		$element = $f->table->$tbname;
 		$namefield = $element->namefield["name"];
 		$idfield = $element->idfield["name"];
 		$activefield = $element->activefield["name"];
 		$sortby = $element->sortby["name"];
 		$whereName=$element->wherefield["name"];
 		$whereValue=$element->wherefield["value"];
 		$checkdisablexml="";
 		//For debug undefined variable activefield. By Ruck : 19-05-2009
 		//$activefield="";
 		
 		if($tbname=="al_bookparty,al_accomodations"){
 			$sql = "(select \"al_bookparty\" as tb_name, bp_id as c_bp_id,bp_name as c_bp_name from al_bookparty where bp_active=1 and bp_id!=1) UNION " .
 					"(select \"al_accomodations\" as tb_name, acc_id as c_bp_id,acc_name as c_bp_name from al_accomodations where acc_active=1 and acc_id!=1) order by c_bp_name ";
 			$rs = $this->getResult($sql,$debug);
 		}else if($tbname=="a_company_info,l_timeperiod"){
 			$sql = "select * from l_timeperiod where tp_distance=3 or tp_distance=6 order by tp_name ";
 			$rs = $this->getResult($sql);
 		}else{
	 		foreach($element->field as $field) {
	 			//if($field["formname"]=="Active") {$activefield=$field["name"];}
	 			if($field["disabled"]){$checkdisablexml.="<where logic='AND' name='".$field["name"]."' operator='!='>".$field["disabled"]."</where>";}
	 		}
	 		$income = "";
	 		if($value) {$income=", incoming parameter: ".$value;}
	 		
	 		$xml = "<command>".
	 				"<table>$tbname</table>";
	 		if($sortby!=""){
	 			$xml .="<order>".$element->field["name"]."</order>";
	 		}else{
	 			$xml .="<order>$namefield</order>";
	 		}
	 		
	 		if($activefield){$xml.="<where name='$activefield' operator='='>1</where>";}
	 		if($whereName!=""){
	 			$xml.="<where logic='AND' name='$whereName' operator='='>$whereValue</where>";
	 		}
	 		
	 		$xml .=	$checkdisablexml;
	 		$xml .=	"</command>";
	 		//echo htmlspecialchars($xml);
	 		$rs = $this->getRsXML($xml,$filename,$debug);
 		}
 		
 		
 		
 		if(!isset($_SESSION["adminExpert"])){$_SESSION["adminExpert"]="0";}else{$_SESSION["adminExpert"]=$_SESSION["adminExpert"];}
 		//////for fix change room of branch 
 		if($_SESSION["adminExpert"]!=1 && $sta == "bl_room"){
 			$textout = "<select name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" ".$ff["javascript"]."> \n";
 			if($ff["first"]!="no"&&$ff["first"]!=false) {$textout .= "<option disabled value='0'>".$ff["first"]."</option> \n";}
 		}else{
 			$textout = " <span class=\"".$ff["name"]."\" style=\"width: 115px;font-family:Tahoma; font-size: 11px;overflow:hidden;\"><select name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" ".$ff["javascript"]."class=\"ctrDropDown\" onBlur=this.className='ctrDropDown'; onMouseDown=this.className='ctrDropDownClick'; onChange=this.className='ctrDropDown';> \n";	
 			if($ff["first"]!="no"&&$ff["first"]!=false) {$textout .= "<option value='0'>".$ff["first"]."</option> \n";}
 		}
 		//////
 		
 		
 
 		for($i=0; $i<$rs["rows"];$i++) {
 			
 			if($tbname=="al_bookparty,al_accomodations"){
 				$selected = ($value==$rs[$i]["tb_name"]."_".$rs[$i]["c_bp_id"])?'selected':'';
 				$textout .= "<option value=\"".$rs[$i]["tb_name"]."_".$rs[$i]["c_bp_id"]."\" $selected >".$rs[$i]["c_bp_name"]."</option> \n";
 			}else if($tbname=="l_hour"){
 				$selected = ($value==$rs[$i]["$idfield"])?'selected':'';
 				$textout .= "<option value=\"".$rs[$i]["$idfield"]."\" $selected >".substr($rs[$i]["$namefield"],0,5)."</option> \n";
 			}else if($tbname=="a_company_info,l_timeperiod"){
 				$selected = ($value==$rs[$i]["tp_id"])?'selected':'';
 				$textout .= "<option value=\"".$rs[$i]["tp_id"]."\" $selected >".$rs[$i]["tp_name"]."</option> \n";
 			}else if($_SESSION["adminExpert"]!=1 && $sta == "bl_room"){
 				 if($value==$rs[$i]["$idfield"]){
 				 	$selected = 'selected';
 				 	$disabled = 'readonly';
 				 }else{
 				 	$selected = '';
 				 	$disabled = 'disabled';
 				 }
 				 $textout .= "<option $disabled value=\"".$rs[$i]["$idfield"]."\" $selected >".$rs[$i]["$namefield"]."</option> \n";
 			}else if($tbname!="dl_nationality" || $rs[$i]["nationality_active"]!=0){
 				$selected = ($value==$rs[$i]["$idfield"])?'selected':'';
 				$textout .= "<option value=\"".$rs[$i]["$idfield"]."\" $selected >".$rs[$i]["$namefield"]."</option> \n";
 			}
 			
 	
 		}
 		$textout .= "</select></span> \n";
 		
 		if($this->getDebugStatus()) {
 			$this->printDebug("formdb.gSelectBox()","generate list box already..component name: ".$ff["name"]." ".$income);
 		}
 		//echo $tbname.": ".$rs["rows"]."<br/> ";
 		return $textout." \n";
 	}		
 
/*
 * function for Generate Add form from XML file
 * @param - Tablename form database
 * @param - Set debug msg form connection class
 */ 	
 	function gFormInsert($tbname,$filename='object.xml',$debug=false) {
 		
 		//For debug undefined variable : textout
 		$textout="";
 		
 		$f = simplexml_load_file($filename);
		
		$element = $f->table->$tbname;
		$action = $element["action"];
		$enctype = $element["enctype"];
		$usetable = $element["useTable"];
		$setform = $element["setForm"];
		$setdateform = $element["setdateForm"];
		if($setform){$textout = "<form name='$tablename' action='$action' enctype='$enctype' method='post'>\n";}
		$textout .= "<table class=\"generalinfo\">";
		foreach($element->field as $field) { // start loop xml
			$name = $field["name"];
			if(!isset($_GET["$name"])){$_GET["$name"]="";}
			if($field["defaultvalue"]=="__get"){
				$defaultvalue = (isset($_GET["$name"]))?$_GET["$name"]:false;
			}else if($field["defaultvalue"]=="__post"){
				$defaultvalue = (isset($_POST["$name"]))?$_POST["$name"]:false;
			}else if(isset($field["defaultvalue"])){
				$defaultvalue = $field["defaultvalue"];
			}else{
				$defaultvalue = false;
			}
			
			if($usetable == "yes") {
				if($field["formtype"]=="textarea"){
					$defaultvalue = str_replace("[br]","\n",$defaultvalue);
				}
				if($field["showinform"] != "no" && $field["formtype"]!="hidden" && $field["formtype"]!="password"
					&& $field["showinformAdd"]!="no"){
					
					$textout .= "<tr>\n";
					$textout .= "<td valign='top'>".$field["formname"];
					if($field["prior"]){$textout .= "<font style='color:#ff0000'> ".$field["prior"]."</font> ";}
					$textout .= "</td>\n";
					$textout .= "<td valign='top'>";
					
					if($field["formtype"]=="text" || $field["formtype"]=="button" || $field["formtype"]=="submit" || $field["formtype"]=="reset" || $field["formtype"]=="button"){
						if($field["formtype"]=="text"){
							$field["javascript"]="onChange='".$field["javascript"]."'";
						}
						$textout .= "<input id='".$field["name"]."' type='".$field["formtype"]."' name='".$field["name"]."' maxlength='".$field["maxlength"]."' size='".$field["size"]."' value=\"$defaultvalue\" ".$field["javascript"].">";
					}
					else if($field["formtype"]=="textarea") {
						$textout .= "<textarea id='".$field["name"]."' name='".$field["name"]."' cols='".$field["cols"]."' rows='".$field["rows"]."' ".$field["javascript"].">$defaultvalue</textarea>";
					}
					else if($field["formtype"]=="checkbox") {
						if($field["defaultvalue"]=="__post"&&$_POST["$name"]==1) {$selected = "checked";} else {$selected = "";}
						if($field["defaultvalue"]=="__get"&&$_GET["$name"]==1) {$selected = "checked";} else {$selected = "";}
						$textout .= "<input id='".$field["name"]."' type='".$field["formtype"]."' name='".$field["name"]."' value=\"$defaultvalue\" ".$field["javascript"]." $selected>";
					}
					else if($field["formtype"]=="textlink"){
						$textout .= "<a href='".$field["href"]."' ".$field["target"].">".$field["description"]."</a> ";
					}
	 				else if($field["formtype"]=="select") {
	 					$textout .= $this->gSelectBox($field,$filename,$defaultvalue,$debug);
	 				}else if($field["formtype"]=="date"){
	 					$imgsrc="/images/calendar.png";
	 					// system date format	 					
						$chksql = "select long_date,short_date from a_company_info";
						$chkrs =$this->getResult($chksql);
						$sdateformat = $this->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
						$dateobj = new convertdate();
						$defaultvalue = $this->getParameter("hidden_$name",$defaultvalue);
						$defaultvalue = str_replace("-","",$defaultvalue);
						if($tbname=="g_gift" && $name=="expired" && $defaultvalue==""){
							$defaultvalue = $dateobj->plusmonth(date("Ymd"),6);
						}
						if($tbname=="m_membership" && $name=="expireddate" && $defaultvalue==""){
							$defaultvalue = $dateobj->plusmonth(date("Ymd"),12);
						}
						
						if($defaultvalue==""){
							if(isset($field["initialvalue"])){
								// If default value is empty and set initial value from xml tag.
								// Then set default value follow  initial value from xml.
								$defaultvalue = $field["initialvalue"];
							}else{
								// If default value is empty and not set initial value from xml tag.
								// Then set default value to this date.
								$defaultvalue = date("Ymd");
							}
						}
						$value='';
						if(is_numeric($defaultvalue) && $defaultvalue != "00000000"){
							// If default value is numeric then set value variable equal convert default value.
							$value = $dateobj->convertdate($defaultvalue,'Ymd',$sdateformat);
						}else{
							// If defalut value is character then set value variable equal default value.
							// And set default to "".
							$value = $field["initialvalue"];
							$defaultvalue = "00000000";
						}
	 					if($setdateform!="no"){$textout .= "<form name='$name'>&nbsp;&nbsp;";}
	 					$textout .= "\n<input type='hidden' id='hidden_$name' name='hidden_$name' value='$defaultvalue'>" .
		 								"\n<input id='$name' name='$name' value='$value' style=\"width: 85px;\" readonly=\"1\" class=\"textbox\" type=\"text\">
								            &nbsp;&nbsp;<img src=\"$imgsrc\" onclick=\"showChooser(this, '$name', '".$name."_showSpan', 1900, 2100, '$sdateformat', false);\"> ";
						if($field["reset"]=="yes"){
								$textout .= "\n<input type='button' id='reset_date' name='reset_date' value='Unlimited' onclick=\"resetDateBox('$name','hidden_$name','Unlimited')\">";
						}	            
						$textout .=	"<span id=\"".$name."_showSpan\" class=\"dateChooser\" style=\"display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;\" align=\"center\"/></span>";
						if($setdateform!="no"){$textout .= "</form>";}
						
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
	 			}else if($field["formtype"]=="hidden"){
	 				$textout .= "<input id='".$field["name"]."' name='".$field["name"]."' type='".$field["formtype"]."' value='".$defaultvalue."'>\n";
				}else{
					$textout .= "<input id='".$field["name"]."' name='".$field["name"]."' type='hidden' value='".$defaultvalue."'>\n";
				}
			} 
		} // end loop xml
		
		$textout .= "<input name='formname' id='formname' type='hidden' value='$tbname'>\n";
		$textout .= "</table>\n";
		if($setform){$textout .= "</form>\n";}
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
 * 
 * Modify by ruk: date 20-02-2009
 * Check disabled is "All"
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
		$setform = $element["setForm"];
		$setdateform = $element["setdateForm"];
 		$textout = "";
 		$rs = $this->getRsXML($xml,$filename,$debug);
 		
		if($setform){$textout .= "<form name='$tbname' action='$action' enctype='$enctype' method='post'>\n";}	
 		$textout .= "<table class=\"generalinfo\"> \n";
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
			
			//debug for value 0 not display
			if(!isset($rs[0]["$name"])){$rs[0]["$name"]="0";}
			if($rs[0]["$name"]=="0"){
				$defaultvalue=0;
			}
			////////
 			if($rs[0]["$name"] && $defaultvalue===false && $ff["updatein"]!="l_tax"){
 				$defaultvalue = $rs[0]["$name"];
 			}				
 			if($ff["formtype"] == "submit" || $ff["formtype"] == "button") {
 				$defaultvalue = " save change ";
 			} 
 			
 			if($ff["updatein"]=="l_tax"&&$defaultvalue===false){
	 				$sql="select * from l_tax where tax_id =".$rs[0]["$name"];
		 			$xml = "<command>" .
	 	 						"<sql>".$sql."</sql>" .
	 							"</command>";
	 				$rs2 = $this->getResult($sql,$debug);
	 				$defaultvalue = $rs2["0"]["tax_percent"];
	 				
 			}
 			
 			if($ff["table"]=="al_bookparty,al_accomodations"){$defaultvalue = $rs[0]["tb_name"]."_".$rs[0]["$name"];}
 			
 				
			if($ff["formtype"]=="textarea"){
					$defaultvalue = str_replace("[br]","\n",$defaultvalue);
			}
			
 			if($ff["showinform"]!="no" && $ff["formtype"]!="hidden" && $ff["formtype"]!="password"
	 				&& $ff["showinformEdit"]!="no") {
	 				$textout .= "<tr> \n";
	 				$textout .= "<td valign='top'>".$ff["formname"];
					if($ff["prior"]){$textout .= "<font style='color:#ff0000'> ".$ff["prior"]."</font> ";}
					$textout .= "</td> \n";
	 				$textout .= "<td valign='top'> \n";
	 				$disabled = "";
	 				if($ff["disabled"]=="yes"){
	 					$disabled = " disabled=\"disabled\" ";
	 				}
	 				if(($ff["formtype"]=="text" || $ff["formtype"]=="file" || $ff["formtype"]=="submit" || $ff["formtype"]=="reset" || $ff["formtype"]=="button")) {
	 					if($ff["formtype"]=="text"){
							$ff["javascript"]="onChange='".$ff["javascript"]."'";
						}
	 					$textout .= "<input id='".$ff["name"]."' type='".$ff["formtype"]."' name='".$ff["name"]."' maxlength='".$ff["maxlength"]."' size='".$ff["size"]."' value=\"$defaultvalue\" ".$ff["javascript"]." $disabled>";
	 					if($ff["name"]=="servicescharge"||$ff["updatein"]=="l_tax"){$textout .= " input 7.00 for 7% ";}
	 				}
	 				else if($ff["formtype"]=="textarea" && $ff["updatein"]!="ma_comment") {
	 					$textout .= "<textarea name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" cols=\"".$ff["cols"]."\" "."\" rows=\"".$ff["rows"]."\" ".$ff["javascript"]." >".$defaultvalue."</textarea> \n";
	 				}
	 				else if($ff["formtype"]=="checkbox") {
	 					$selected = ($rs[0]["$name"] == 1)?"checked":"";     
	 					$textout .= "<input name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" type=\"".$ff["formtype"]."\" value=\"1\" ".$ff["javascript"]." $selected> \n";
	 				}
	 				else if($ff["formtype"]=="textlink") {
	 					$textout .= "<a href=\"".$ff["href"]."?".$ff["target"]."\">".$ff["description"]."</a> \n";
	 				}
	 				else if($ff["formtype"]=="select") {
	 					$textout .= $this->gSelectBox($ff,$filename,$defaultvalue,$debug,$xml);
	 				}
	 				else if($ff["formtype"]=="date"){
	 					$imgsrc="/images/calendar.png";
	 					// system date format	 					
						$chksql = "select long_date,short_date from a_company_info";
						$chkrs =$this->getResult($chksql);
						$sdateformat = $this->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
						$dateobj = new convertdate();
						$defaultvalue = $this->getParameter("hidden_$name",$defaultvalue);
						$defaultvalue = str_replace("-","",$defaultvalue);
						
						$value='';
						if($defaultvalue=="00000000" && isset($ff["initialvalue"])){
							// If date value from database is "00000000" and set initial value from xml tag.
							// Then show value follow  initial value from xml.
							$value=$ff["initialvalue"];
						}else{
							$value=$dateobj->convertdate($defaultvalue,'Ymd',$sdateformat);
						}
						
	 					if($setdateform!="no"){$textout .= "<form name='$name'>&nbsp;&nbsp;";}
	 					$textout .= "\n<input type='hidden' id='hidden_$name' name='hidden_$name' value='$defaultvalue'>" .
		 							"\n<input id='$name' name='$name' value='$value' style=\"width: 85px;\" readonly=\"1\" class=\"textbox\" type=\"text\">
							         &nbsp;&nbsp;<img src=\"$imgsrc\" onclick=\"showChooser(this, '$name', '".$name."_showSpan', 1900, 2100, '$sdateformat', false);\">";		
						if($ff["reset"]=="yes"){
								$textout .= "\n<input type='button' id='reset_date' name='reset_date' value='".$ff["initialvalue"]."' onclick=\"resetDateBox('$name','hidden_$name','".$ff["initialvalue"]."')\">";
						}	 	             
						$textout .=	"<span id=\"".$name."_showSpan\" class=\"dateChooser\" style=\"display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;\" align=\"center\"/>";
						if($setdateform!="no"){$textout .= "</form>";}
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
	 			}else if($ff["formtype"]=="hidden"){
	 				$textout .= "<input name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" type=\"".$ff["formtype"]."\" value=\"".$defaultvalue."\"> \n";
	 			}else{
	 				$textout .= "<input name=\"".$ff["name"]."\" id=\"".$ff["name"]."\" type=\"hidden\" value=\"".$defaultvalue."\"> \n";
	 			}	
 		}
 		$textout .= "<input name='formname' type='hidden' value=\"".$tbname."\" > \n";
 		$textout .= "</table> \n";
		if($setform){$textout .= "</form>\n";}
 		return $textout;
 	}
	
/*
 * function for read formname for post value
 * @param - Post query value
 * Modified in 3-Dec-2008, natt if cause to check numberic of price and hour in db_package 
 * Modified in 28-Jan-2009, natt if cause to check XML property showinformAdd/showinformEdit
 * Modified in 20-Fab-209, ruk:
 * In case add branch if add branch name is "All". Add status is failed.
 * In case edit branch if edit branch name to "All". Add status is failed.
 */
 	function readForm($post,$filename='object.xml') {
 		$formname = $post["formname"];
 		$f = simplexml_load_file($filename);
 		$tmp = array();
 		$aform = $f->table->$formname;
 		if(!isset($post["method"])){$post["method"]="";} 		
 		if(!isset($post["pathFrom"])){$post["pathFrom"]="";} 	
 		$insertField="";
 		$id="";
 		foreach($aform->field as $ff) {
 			if($ff["showinform"] != "no" && $ff["formtype"] != "submit" && $ff["formtype"]!="button" && $ff["formtype"]!="file" && $ff["formtype"]!="textlink") {
 				if(($ff["showinformAdd"] != "no"&&$post["add"]==" add ") || ($ff["showinformEdit"] != "no"&&$post["add"]==" save change ")){
	 				$t = $ff["name"];
	 				if(!isset($post["$t"])){$post["$t"]="";}
	 				if($ff["updatein"]!=""){
	 					if($ff["updatein"]=="l_tax"){
	 						$sql = "select tax_id from bl_branchinfo where branch_id ='".$post["branch_id"]."'";
	 						$idB=$this->getResult($sql);
	 						$sql = "select tax_percent from l_tax where tax_id ='".$idB[0]["tax_id"]."'";
	 						$idT=$this->getResult($sql);
	 						if($idT[0]["tax_percent"]!=$post["tax_id"]){
		 						$insertField="l_tax";
		 						$insertFieldValue=$this->checkHiddenValue(htmlspecialchars($post["$t"]));
		 						$sql = "insert into l_tax (tax_percent) values($insertFieldValue) ";
		 						$id = $this->setResult($sql);
								if(!$id) {
									$this->printDebug("formdb.readToInsert()","insert information not complete!!",$sql);
								}
								$tmp["$t"] = $this->checkHiddenValue(htmlspecialchars($id));
	 						}
	 					}else if($ff["updatein"]=="ma_comment"){
	 						if($post["$t"]!=""){
	 							$insertField="ma_comment";
	 							$insertFieldValue=$this->checkHiddenValue(htmlspecialchars($post["$t"]));
		 						$sql = "insert into ma_comment (comments) values($insertFieldValue) ";
		 						$id = $this->setResult($sql);
								if(!$id) {
									$this->printDebug("formdb.readToInsert()","insert information not complete!!",$sql);
								}
								$tmp["$t"] = $this->checkHiddenValue(htmlspecialchars($id));
							}
	 						//$tmp["$t"] = $this->checkHiddenValue(htmlspecialchars(5));	
	 						//echo $tmp["$t"];
	 					}
	 				}else{
	 					// For check not update l_lu_user,l_lu_date and l_lu_ip in gift table when update the gift//
	 					/*$setField=true;	// remove this function by natt because can't add "l_lu_user","l_lu_date","l_lu_ip" error
	 					if($formname=="g_gift"){
		 					if($post["method"]=="edit"){
								if($t=="l_lu_user" || $t=="l_lu_date" || $t=="l_lu_ip"){
									//echo "<br>$t";
									$setField=false;
								}
									
							}
		 				}*/ 
	 					//echo "<br>Assign : $t";
	 					//if($setField){
	 						$tmp["$t"] = $this->checkHiddenValue(htmlspecialchars($post["$t"]));
	 						if($t=="branch_code"){$tmp["$t"] = strtoupper($this->checkHiddenValue(htmlspecialchars($post["$t"])));}	
	 					//}	
	 					
	 				}
	 				if(($post["$t"]==""&&$ff["prior"]=="*"&&$ff["formtype"]!="date")
	 				||($ff["formtype"]=="select"&&$post["$t"]=="0"&&$ff["prior"]=="*"&&$ff["formtype"]!="date")) {
						$this->setErrorMsgColor("#000000");
	 					$this->setErrorMsg("Please insert ".$ff["formname"]."!!");
	 					return false;
	 				}
	 			}
	 			
	 			if($ff["name"]=="trm_category_id"){
	 				$tmp["trm_category_id"] = $this->checkHiddenValue(htmlspecialchars($post["trm_category_id"]));
	 			}
		 		if($ff["formtype"]=="date"){
		 			$dateobj = new convertdate(); 
					$chksql = "select long_date,short_date from a_company_info";
					$chkrs =$this->getResult($chksql);
					$sdateformat = $this->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
		 			$t = $ff["name"];
		 			$tmp["$t"] = "'".$post["hidden_$t"]."'";
		 		}
		 	}
		}
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
 		if($formname=="cl_product"){
 			if(!is_numeric($post["standard_price"])){
	 			$this->setErrorMsg("Please check your Prices value!!");
	 			return false;
	 		}	
 		}
 		if($formname=="cl_product_category"){
 			if(!is_numeric($post["pd_category_priority"])){
	 			$this->setErrorMsg("Please check your priority value!!");
	 			return false;
	 		}	
 		}
 		if($formname=="bl_branchinfo"){
 			if($post["add"]==" add " && $post["branch_name"]=="All"){
 				$this->setErrorMsg("Can't add branch name \"All\". Please change branch name.");
 				return false;
 			}
 			if($post["add"]==" save change " && $post["branch_name"]=="All"){
 				$branchId = $this->getIdToText($post["branch_name"],"bl_branchinfo","branch_id","branch_name");
 				if($branchId!=$post["id"]){
 					$this->setErrorMsg("Can't edit branch name to \"All\". Please change branch name.");
 					return false;
 				}
 				//echo "save change ".$post["branch_name"];
 			}
 			if(!is_numeric($post["servicescharge"])){
 				$this->setErrorMsg("Please check your services Charge");
 				return false;
 			}
 			if($post["start_time_id"] >= $post["close_time_id"]){
 				$this->setErrorMsg("Please check your start time must be less than close time.");
 				return false;
 			}
 			if(!is_numeric($post["tax_id"])){
				$this->setErrorMsg("Please check tax percent must be number.");
 				return false;
				 				
 			}
 		}
 		if($formname=="bl_room"){
 			if(!is_numeric($post["room_qty_people"])){
	 			$this->setErrorMsg("Please check Number of people in room value!!");
	 			return false;
	 		}
 		}
 		if($insertField=="l_tax"){
 			$sql = "update l_tax set " .
 					"branch_id=".$tmp["branch_id"]."," .
 					"l_lu_user='".$this->userid."'," .
 					"l_lu_date=now()," .
 					"l_lu_ip='".$this->getIp()."' where tax_id='$id'" ;
 			$xml = "<command>" .
 	 				"<sql>".$sql."</sql>" .
 					"</command>";
 					$this->setErrorMsg($xml);
 			$id = $this->setRsXML($xml,false);
			if(!$id) {
				$this->setErrorMsg("Cann't insert an information!!'");
				$this->printDebug("formdb.readToInsert()","insert information not complete!!",$sql);
			}
 		}
 		if($insertField=="ma_comment"){
 			$sql = "update ma_comment set " .
 					"l_lu_user='".$this->userid."'," .
 					"l_lu_date=now()," .
 					"l_lu_ip='".$this->getIp()."' where comment_id='$id'" ;
 			$xml = "<command>" .
 	 				"<sql>".$sql."</sql>" .
 					"</command>";
 					$this->setErrorMsg($xml);
 			$id = $this->setRsXML($xml,false);
			if(!$id) {
				$this->setErrorMsg("Cann't insert an information!!'");
				$this->printDebug("formdb.readToInsert()","insert information not complete!!",$sql);
			}
 		}  
 		
 		if($formname=="l_employee"){
 			if(strlen ($post["emp_id_num"])>0){
	 			if(!is_numeric($post["emp_id_num"])){
		 			$this->setErrorMsg("Please check your ID Number value must be number!!");
		 			return false;
		 		}
		 		if(strlen ($post["emp_id_num"])!=13){
		 			$this->setErrorMsg("Please check your ID Number field require 13 digit number!!");
		 			return false;
		 		}
 			}
 		}
 		if($formname=="db_package"){
 			if(!is_numeric($post["price"])){
	 			$this->setErrorMsg("Please check your Prices value!!");
	 			return false;
	 		}	
 			if(!is_numeric($post["hour"])){
	 			$this->setErrorMsg("Please check your Hour value!!");
	 			return false;
	 		}	
 		}
 		if($formname=="m_membership"){
 			if($this->checkDataInTable("m_membership","member_code",$post["member_code"]) && $post["add"] != " save change "){
 				$this->setErrorMsg("Member Code ".$post["member_code"]." already exists. Please try again.");
 				return false;
 			}
 			if($post["method"] == "edit"){
 				if($this->checkDataInTable("m_membership","member_code",$post["member_code"])){
 					if(!$this->checkOldMember($post["member_code"],$post["member_id"])){
 						$this->setErrorMsg("Member Code ".$post["member_code"]." already exists. Please try again.");
 						return false;
 					}	
 				}
 			if($post["phone"]==""){$tmp["chk_phone"]=0;}
 			if($post["mobile"]==""){$tmp["chk_mobile"]=0;}
 			if($post["email"]==""){$tmp["chk_email"]=0;}
 			}else{
 			//For check contract channels
 			if($post["phone"]){$tmp["chk_phone"]=1;}
 			if($post["mobile"]){$tmp["chk_mobile"]=1;}
 			if($post["email"]){$tmp["chk_email"]=1;}
 			}
 		}
 		if($formname=="g_gift"){
 			if($this->checkDataInTableGift("g_gift","gift_number",$post["gift_number"],$post["gifttype_id"]) && $post["method"] != "edit"){
 				$this->setErrorMsg("This gift type with this gift number already exists. Please insert a new number and try again.");
 				return false;
 			}
 			if($post["method"] == "edit"){
 				//if($this->checkDataInTableGift("g_gift","gift_number",$post["gift_number"],$post["gifttype_id"])){
 				//	if(!$this->checkOldGift($post["gift_number"],$post["gift_id"],$post["gifttype_id"])){
 					//	$this->setErrorMsg("This gift type with this gift number already exists. Please insert a new number and try again.");
 				//		return false;
 				//	}	
 				//}
 			}
 			if(!is_numeric($post["gift_number"])){
	 			$this->setErrorMsg("Gift Number must be numerically !!");
	 			return false;
	 		}
 			
 			if($post["pathFrom"]!="appt" && $post["gifttype_id"]==$GLOBALS["global_gifttypeid"] && $post["method"] != "edit"){
 				$this->setErrorMsg("Can add gift sold from booking only !!");
 				return false;
 			}
 			
 			$bpdsid_sold = (isset($post["id_sold"]))?$post["id_sold"]:0;
 			$bpdsid_sold = (isset($post["bpdsid_sold"]))?$post["bpdsid_sold"]:$bpdsid_sold;
		 	$bookid = $this->getIdToText($bpdsid_sold,"c_bpds_link","tb_id","bpds_id");
		 	$tbname = $this->getIdToText($bpdsid_sold,"c_bpds_link","tb_name","bpds_id");
		 		
 			if($post["gifttype_id"]==$GLOBALS["global_gifttypeid"] && $bpdsid_sold <= 0){
 					$this->setErrorMsg("Please try again. Change Gift Type to Gift Sold or check id sold !!");
 					return false;
 			}
 			if($post["gifttype_id"]!=$GLOBALS["global_gifttypeid"] && $bpdsid_sold > 0){
 					$this->setErrorMsg("Please try again. Change Gift Type to Gift Sold or check id sold !!");
 					return false;
 			}
			
 			
	 		if($bpdsid_sold!="" && $bpdsid_sold!=0){
	 			if(!is_numeric($bpdsid_sold)){
		 			$this->setErrorMsg("Id Sold must be numerically !!");
		 			return false;
		 		}
		 		
		 		$id_sold = $this->getIdToText($bpdsid_sold,"c_bpds_link","tb_id","bpds_id");
		 		$tb_name = $this->getIdToText($bpdsid_sold,"c_bpds_link","tb_name","bpds_id");
		 		
		 		if(!$id_sold){
		 			$tmp["id_sold"]=$bpdsid_sold;	// K. toby want to change it 'cos we have many id sold come from destiny
		 			$tmp["tb_name"]="''";
	 			}else{
	 				$tmp["id_sold"]=$id_sold;
	 				$tmp["tb_name"]="'".$tb_name."'";
	 			}
	 		}
	 		
	 		if($post["hidden_issue"] > $post["hidden_expired"]){
		 			$this->setErrorMsg("Expired date must more than issue date !!");
		 			return false;
	 		}
	 	}
 		if($formname=="fl_csi_value"){
 			if(strlen ($post["csiv_value"])>0){
	 			if(!is_numeric($post["csiv_value"])){
		 			$this->setErrorMsg("Please check CSI value must be number!!");
		 			return false;
		 		}
 			}
 		}
	 	// for bank acc cms separate c_bp_id select box
	 	if($formname=="al_bankacc_cms"||$formname=="log_al_bankacc"){
	 		if(strlen($post["c_bp_id"])==1){
	 			$tmp["tb_name"]= $this->checkHiddenValue("al_bookparty");	// al_accomodations or al_bookparty
	 			$tmp["c_bp_id"]=0;
	 		}else{
	 			$c_bp_id = explode("_",$post["c_bp_id"]);
		 		$tmp["tb_name"]= $this->checkHiddenValue($c_bp_id[0]."_".$c_bp_id[1]);	// al_accomodations or al_bookparty
	 			$tmp["c_bp_id"]=$c_bp_id[2];
	 		}	 		
	 		$tmp["c_bp_phone"]=$post["c_bp_phone"];
	 	}
	 	
 		return $tmp;
 	}

/*
 * function for get information to insert
 * @param - Post query value
 * @param - Set debug msg form connection class
 * @modified - detect repeatname from xml namefield element repeat property / natt 25 July 2009 
 */
	function readToInsert($post, $filename='object.xml', $debug=false) {
		$id = false;
		$formname = $post["formname"];
		
 		$f = simplexml_load_file($filename);
 		$element = $f->table->$formname;
		$eid = $element->idfield;
		$ename = $element->namefield;
		$idfield = $eid["name"];
		$namefield = $ename["name"];
		$repeat = $ename["repeat"]; // falt from xml file control this table can input repeat name or not 
		
		if($repeat=="no"){
			// check can't insert the name that already has in system
			foreach($element->field as $ff){
		 		if("$namefield"==$ff["name"]){
		 			$chkid = $this->getIdToText($post["$namefield"],$formname,$idfield,$namefield);
		 			if("$namefield"=="member_code"){
		 				if($post["member_code"]==0){
		 					$this->setErrorMsg("This ".$ff["name"]." already exists. Please try again.");
		 				return false;
		 				}
		 			}
		 			//if($chkid>0&&"$namefield"!="gift_number"){
		 			if($chkid>0){
						$this->setErrorMsg("This ".$ff["name"]." already exists. Please try again.");
	 					return false;
		 			}
		 			break;
		 		}
	 		}
		}
 		
		$tmp = $this->readForm($post,$filename);
		if(!$tmp){return false;}
		$key = array_keys($tmp);
		$count = count($key);
		if(isset($tmp["comments"])){
			$count = count($key)-1;
		}
		$field="";	// field name after take out form type 'submit,button,file and textlink'
		$value="";	// value from form we need to input
		for($i=0; $i<$count; $i++) {
			if($key[$i]!="comments"){
				$field .= $key[$i];
				if($key[$i]=="pass"){
					$value .= "'".md5(str_replace("'","",$tmp[$key[$i]]))."'";
				}else if($key[$i]=="c_bp_phone"){
 					$value .= "'".$tmp[$key[$i]]."'";
 				}else{
 					$value .= $tmp[$key[$i]];
 				}
				if($i < ($count-1)) {
					$field .= ",";
					$value .= ",";
				}
			}
		}
		$sql = "insert into $formname($field) values($value) ";
 			//echo $sql."<br/>";
 		if($debug){
 			echo $sql."<br/>";
 			return false;
 		}
 		
		$id = $this->setResult($sql,$debug);
		
		if(!$id) {
			//$this->setErrorMsg("Can't insert an information!!'");
			$this->printDebug("formdb.readToInsert()","insert information not complete!!",$sql);
		}
		if($formname=="bl_branchinfo"){
			$chksql = "update l_tax set branch_id =".$id." where tax_id=".$tmp["tax_id"];
 			$idUp = $this->setResult($chksql,$debug);
 			if(!$idUp) {
 				$this->printDebug("formdb.readToInsert()",$this->getErrorMsg(),$sql);
 			}
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
			return "'".str_replace("'","''",$value)."'";
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
			return false;
		}
		
		$sid = $this->setResult($sql,$debug);
		
		if($sid){
			$name = $this->getIdToText($fid,$tbname,$namefield,$idfield,$debug);
			return $name;
		}else{return false;}
		
	}
	
	function setCommission($post, $filename="object.xml", $debug=false){
		$f = simplexml_load_file($filename);
		$tbname = $post["table"];
		$fid = $post["id"];
		$active = $post["active"];
		
		$element = $f->table->$tbname;
		$idfield = $element->idfield["name"];
		$namefield = $element->namefield["name"];
		
		$sql = "update ".$tbname." set set_commission=".$active." where ".$idfield."=".$fid;
		
		if($debug){
			echo $sql."<br/>";
		}
		
		$sid = $this->setResult($sql,$debug);
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
		
	function setPayment($post, $filename="object.xml", $debug=false){
		$f = simplexml_load_file($filename);
		$tbname = $post["table"];
		$fid = $post["id"];
		$active = $post["active"];
		
		$element = $f->table->$tbname;
		$idfield = $element->idfield["name"];
		$namefield = $element->namefield["name"];
		
		$sql = "update ".$tbname." set set_payment=".$active." where ".$idfield."=".$fid;
		
		if($debug){
			echo $sql."<br/>";
		}
		
		$sid = $this->setResult($sql,$debug);
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
		
	function setpdValue($post, $filename="object.xml", $debug=false){
		$f = simplexml_load_file($filename);
		$tbname = $post["table"];
		$fid = $post["id"];
		$active = $post["active"];
		
		$element = $f->table->$tbname;
		$idfield = $element->idfield["name"];
		$namefield = $element->namefield["name"];
		
		$sql = "update ".$tbname." set pos_neg_value=".$active." where ".$idfield."=".$fid;
		
		if($debug){
			echo $sql."<br/>";
		}
		
		$sid = $this->setResult($sql,$debug);
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
	
	function setTax($post, $filename="object.xml", $debug=false){
		$f = simplexml_load_file($filename);
		$tbname = $post["table"];
		$fid = $post["id"];
		$active = $post["active"];
		
		$element = $f->table->$tbname;
		$idfield = $element->idfield["name"];
		$namefield = $element->namefield["name"];
		
		$sql = "update ".$tbname." set set_tax=".$active." where ".$idfield."=".$fid;
		
		if($debug){
			echo $sql."<br/>";
		}
		
		$sid = $this->setResult($sql,$debug);
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
	
	function setSc($post, $filename="object.xml", $debug=false){
		$f = simplexml_load_file($filename);
		$tbname = $post["table"];
		$fid = $post["id"];
		$active = $post["active"];
		
		$element = $f->table->$tbname;
		$idfield = $element->idfield["name"];
		$namefield = $element->namefield["name"];
		
		$sql = "update ".$tbname." set set_sc=".$active." where ".$idfield."=".$fid;
		
		if($debug){
			echo $sql."<br/>";
		}
		
		$sid = $this->setResult($sql,$debug);
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
		function setmember_takeout($post, $filename="object.xml", $debug=false){
		$f = simplexml_load_file($filename);
		$tbname = $post["table"];
		$fid = $post["id"];
		$active = $post["active"];
		
		$element = $f->table->$tbname;
		$idfield = $element->idfield["name"];
		$namefield = $element->namefield["name"];
		
		$sql = "update ".$tbname." set member_takeout=".$active." where ".$idfield."=".$fid;
		
		if($debug){
			echo $sql."<br/>";
		}
		
		$sid = $this->setResult($sql,$debug);
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
 * @modified - detect repeatname from xml namefield element repeat property / natt 25 July 2009 
 */	
	function readToUpdate($post, $filename='object.xml', $debug=false) {
 		$id = false;
		$formname = $post["formname"];
		
 		$f = simplexml_load_file($filename);
 		$element = $f->table->$formname;
		$eid = $element->idfield;
		$ename = $element->namefield;
		$idfield = $eid["name"];
		$namefield = $ename["name"];
		$repeat = $ename["repeat"]; // falt from xml file control this table can input repeat name or not 
		
		if($repeat=="no"){
			// check can't insert the name that already has in system
			foreach($element->field as $ff){
		 		if("$namefield"==$ff["name"]){
		 			
		 			$chkid = $this->getIdToText($post["$namefield"],$formname,$idfield,$namefield,$idfield." != ".$post["id"]);
		 			
		 			if("$namefield"=="member_code"){
		 				if($post["member_code"]==0){
		 					$this->setErrorMsg("This ".$ff["name"]." already exists. Please try again.");
		 				return false;
		 				}
		 			}
		 			//if($chkid>0&&"$namefield"!="gift_number"){
					if($chkid>0){
		 				$this->setErrorMsg("This ".$ff["formname"]." already exists. Please try again.");
	 					return false;
		 			}
		 			break;
		 		}
	 		}
		}
		
		$tmp = $this->readForm($post, $filename);
		//print_R($tmp);
		
		if(!$tmp){return false;}
		$key = array_keys($tmp);
 		$field = "";
 		$value = "";
 		$set = false;
 		for($i=0; $i<count($key); $i++) {
 			$field = $key[$i]; 			
 			if(str_replace("'","",$tmp[$key[$i]])!=""&&$key[$i]=="pass"){
 				$value = "'".md5(str_replace("'","",$tmp[$key[$i]]))."'";
 			}else if(str_replace("'","",$tmp[$key[$i]])==""&&$key[$i]=="pass"){
 				$value = "'".$this->getPass($tmp[$key[0]])."'";
 			}else if($key[$i]=="c_bp_phone"){
 				$value = "'".$tmp[$key[$i]]."'";
 			}else if($key[$i]=="mpic"){
 				$mpic = $this->getIdToText($post["member_id"],"m_membership","mpic","member_id");
 				$value = "'".$mpic."'";
 			}else{
 				$value = $tmp[$key[$i]];
 			}
 			if($set) {
 				$set.=",";
 			}
 			
 			$set.= $field."=".$value;
 		}
 		
 		$sql = "update ".$formname." set ".$set." where ".$key[0]."=".$tmp[$key[0]];
 		//echo "<br><br><br><br><br>".$sql."<br/>";
 		if($debug){
 			echo $sql."<br/>";
 			return false;
 		}	
 		$id = $this->setResult($sql);
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

/*
 * function for generate text page link for data table
 */
	function genPage($tbname,$order,$dir,$url,$currentpage,$total_records,$records_per_page){
		
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
                $pagearray[] = $this->generateLink($i,$i,$url,$order,$dir);
			}
            $i++;
        }
		//add back/forward ( '<' / '>' ) icon
		$use_back_forward = true;
		 if ($use_back_forward==true){
            if($currentpage=='1'){
        		$page_back = "< ";
        	}else{
                $page_back = $this->generateLink("<",($currentpage-1).'',$url,$order,$dir).' ' ;
        	}
            if($currentpage>=$page_count){
				$page_fwd = " >";
			}else{
                $page_fwd = ' '.$this->generateLink(">",($currentpage+1).'',$url,$order,$dir);
			}
        }
		
		//add back/forward ( '<<' / '>>' ) icon
		$use_first_last = true;
		 if ($use_first_last==true){        
            
            //make the first page url
            if ($currentpage==$start_page){
            	$page_first = '<< ';
            }else{
                $page_first = $this->generateLink('<<','1',$url,$order,$dir).' ';
            }
            
            //make the last page url
            if ($currentpage==$end_page){
            	$page_last = ' >>';
            }else{
                $page_last = ' '.$this->generateLink('>>',"$page_count",$url,$order,$dir) ;
            }
        }
		
		$textout = implode(' ',$pagearray);
		$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
		$textout .= " / Total ".$page_count."<br /><br />";
		echo $textout;
	}
/*
 * function for generate text page link for data Gift table
 */
	function genGiftPage($tbname,$order,$dir,$currentpage,$total_records,$records_per_page,$link=""){
		
		$page_count = $total_records / $records_per_page;
		
		$page_count = (is_int($page_count))?intval($page_count):intval($page_count)+1;
	
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
		//assign array of number page
		$i=$start_page;
        while($i<=$end_page ){
            if("$i"=="$currentpage"){
                $pagearray[] = '<b>'.$i.'</b>';
            }else{
            	if($tbname=="g_gift"){
            		//echo "$i $i $order $dir";
            		$pagearray[] = $this->generate_gift_link($i,$i,$order,$dir,$link);	
            	}else if($tbname=="m_membership"){
            		//echo "$i $i $order $dir";
            		$pagearray[] = $this->generate_members_link($i,$i,$order,$dir);
            	}else if($tbname=="l_marketingcode"){
            		//echo "$i $i $order $dir";
            		$pagearray[] = $this->generate_mkcode_link($i,$i,$order,$dir,$link);
            	}
                
			}
            $i++;
        }
		//add back/forward ( '<' / '>' ) icon
		$use_back_forward = true;
		 if ($use_back_forward==true){
            if($currentpage=='1'){
        		$page_back = "< ";
        	}else{
        		if($tbname=="g_gift"){
            		$page_back = $this->generate_gift_link("<",($currentpage-1).'',$order,$dir,$link).' ' ;	
            	}else if($tbname=="m_membership"){
  					$page_back = $this->generate_members_link("<",($currentpage-1).'',$order,$dir).' ' ;
            	}else if($tbname=="l_marketingcode"){
  					$page_back = $this->generate_mkcode_link("<",($currentpage-1).'',$order,$dir,$link).' ' ;
            	}
                
        	}
            if($currentpage>=$page_count){
				$page_fwd = " >";
			}else{
				if($tbname=="g_gift"){
            		$page_fwd = ' '.$this->generate_gift_link(">",($currentpage+1).'',$order,$dir,$link);	
            	}else if($tbname=="m_membership"){
  					$page_fwd = ' '.$this->generate_members_link(">",($currentpage+1).'',$order,$dir);
            	}else if($tbname=="l_marketingcode"){
  					$page_fwd = $this->generate_mkcode_link(">",($currentpage+1).'',$order,$dir,$link);
            	}
                
			}
        }
		
		//add back/forward ( '<<' / '>>' ) icon
		$use_first_last = true;
		 if ($use_first_last==true){        
            
            //make the first page url
            if ($currentpage==$start_page){
            	$page_first = '<< ';
            }else{
            	if($tbname=="g_gift"){
            		$page_first = $this->generate_gift_link('<<','1',$order,$dir,$link).' ';	
            	}else if($tbname=="m_membership"){
  					$page_first = $this->generate_members_link('<<','1',$order,$dir).' ';
            	}else if($tbname=="l_marketingcode"){
  					$page_first = $this->generate_mkcode_link('<<','1',$order,$dir,$link).' ';
            	}
                
            }
            
            //make the last page url
            if ($currentpage==$end_page){
            	$page_last = ' >>';
            }else{
            	if($tbname=="g_gift"){
            		$page_last = ' '.$this->generate_gift_link('>>',"$page_count",$order,$dir,$link) ;	
            	}else if($tbname=="m_membership"){
  					$page_last = ' '.$this->generate_members_link('>>',"$page_count",$order,$dir) ;
            	}else if($tbname=="l_marketingcode"){
  					$page_last = ' '.$this->generate_mkcode_link('>>',"$page_count",$order,$dir,$link) ;
            	}
                
            }
        }
		
		$textout = implode(' ',$pagearray);
		$textout = $page_first.$page_back.$textout.$page_fwd.$page_last;
		$textout .= " / Total ".$page_count."<br /><br />";
		echo $textout;
	}
/*
 * function for generate text page link for data table
 */	
	function generateLink($inner,$page_number,$url,$order,$dir){
		//## Add isset for check set parameter for debug undefine index. By Ruck 16-05-2009 ##//
		$textout = '';
		$strDir=explode(" ",$order);
		$subDir=explode("&",$dir);
		if($strDir[1]==$subDir[0]){$order=$strDir[0];}
		$textout.= '<a href=\'javascript:;\' onclick=\'getReturnText("'.$url.'","order='.$order.' '.$dir.
		((isset($_GET["where"]))?"&where=".$_GET["where"]:"").
		((isset($_GET["search"]))?"&search=".$_GET["search"]:"").
		((isset($_GET["branch_id"]))?'&branch_id='.$_GET["branch_id"]:'').
		((isset($_GET["city_id"]))?'&city_id='.$_GET["city_id"]:'').
		((isset($_GET["bp_category_id"]))?'&bp_category_id='.$_GET["bp_category_id"]:'').
		((isset($_GET["emp_department_id"]))?'&emp_department_id='.$_GET["emp_department_id"]:'').
		((isset($_GET["show_detail"]))?'&show_detail='.$_GET["show_detail"]:'').
		"&page=".$page_number.'","tableDisplay")\'>'.$inner."</a>\n";
		return $textout;
	}
/*
 * function for generate text page link for data gift table
 */	
	function generate_gift_link($inner,$page_number,$order,$dir,$type){
		//echo "getData($page_number)";
		$textout = '';
		$strDir=explode(" ",$order);
		if($strDir[1]==$dir){$order=$strDir[0];}
		$textout.= '<a href=\'javascript:;\' onClick=\'showSortGift("'.$order.' '.$dir.'","'.$page_number.'","'.$type.'")\'>'.$inner."</a>\n";
		return $textout;
	}
/*
 * function for generate text page link for data gift table
 */	
	function generate_members_link($inner,$page_number,$order,$dir){
		//echo "getData($page_number)";
		$textout = '';
		$strDir=explode(" ",$order);
		if($strDir[1]==$dir){$order=$strDir[0];}
		$textout.= '<a href=\'javascript:;\' onClick=\'showSortMembers("'.$order.' '.$dir.'","'.$page_number.'")\'>'.$inner."</a>\n";
		return $textout;
	}
/*
 * function for generate text page link for data maeketingcode table
 */	
	function generate_mkcode_link($inner,$page_number,$order,$dir,$type){
		//echo "getData($page_number)";
		$textout = '';
		$strDir=explode(" ",$order);
		if($strDir[1]==$dir){$order=$strDir[0];}
		$textout.= '<a href=\'javascript:;\' onClick=\'showSortmkcode("'.$order.' '.$dir.'","'.$page_number.'","'.$type.'")\'>'.$inner."</a>\n";
		return $textout;
	}
 	function  getGroupCheckbox($rs_group=false,$g_id=false)
	{
		for($i=0; $i<count($rs_group); $i++) {
			if($rs_group[$i]["group_id"]==$g_id)
				return "checked";
		}
		return false;
	}
	
	function getCanCheckbox($u_id=false,$g_id=false,$chkfields=false,$debug=false)
	{			
		$sql = "select * from s_ugroup where u_id=".$u_id." ";
		$sql .= "and group_id=".$g_id." ";
		if($chkfields=='set_view'){$sql .= "and set_view=1 ";}
		if($chkfields=='set_edit'){$sql .= "and set_edit=1 ";}
		$rs = $this->getResult("$sql");
		if($rs["rows"])
			return "checked";
		else
			return false;
	}
	
	/*
	 * function for check appointment limited date
	 */
	function getrsvnchk($page_id=false,$g_id=false,$chkfields=false,$debug=false){
		$sql = "select s_group.apptdate_chk from s_gpage,s_group where s_gpage.page_id=".$page_id." ";
		$sql .= "and s_group.group_id=".$g_id." ";
		if($chkfields=='set_view'){$sql .= "and s_gpage.set_view=1 ";}
		if($chkfields=='set_edit'){$sql .= "and s_gpage.set_edit=1 ";}
		$sql .= "and s_group.apptdate_chk=1 ";
		$rs = $this->getResult("$sql");
		
		if($rs["rows"]>0)
			return "checked";
		else
			return false;
	}
	
	function getPageCheckbox($p_id=false,$g_id=false,$chkfields=false,$debug=false)
	{	
		
		$sql = "select * from s_gpage where page_id=".$p_id." ";
		$sql .= "and group_id=".$g_id." ";
		if($chkfields=='set_view'){$sql .= "and set_view=1";}
		if($chkfields=='set_edit'){$sql .= "and set_view=1 and set_edit=1 ";}
		$rs = $this->getResult($sql);
		if($rs["rows"])
			return "checked";
		else
			return false;
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
	
	function checkDataInTable($table,$field,$value){
		$sql = "select $field from $table where $field='$value' ";
 		$id = $this->getResult($sql);
 		if($id["rows"]!=0){
 			return true;
 		}else{
 			return false;
 		}
	}
	
	function checkDataInTableGift($table,$field,$value,$con){
		//$sql = "select $field from $table where $field='$value' and gifttype_id ='$con'";
 		$sql = "select $field from $table where $field='$value'";
 		$id = $this->getResult($sql);
 		if($id["rows"]!=0){
 			return true;
 		}else{
 			return false;
 		}
	}
	
	function checkOldGift($giftNumber,$id,$type){
		$sql = "select gift_number from g_gift where gift_id=$id and gift_number=$giftNumber and gifttype_id=$type";
 		$id = $this->getResult($sql,false);
 		//echo "MMM".$id["rows"].$sql;
 		if($id["rows"]!=0){
 			return true;
 		}else{
 			return false;
 		}
	}
		
	function checkOldMember($code,$id){
		$sql = "select member_code from m_membership where member_id=$id AND member_code=$code ";
 		$id = $this->getResult($sql,false);
 		//echo "MMM".$id["rows"].$sql;
 		if($id["rows"]!=0){
 			return true;
 		}else{
 			return false;
 		}
	}
	
/*
 * function for get information to insert
 * @param - Post query value
 * @param - Set debug msg form connection class
 */
	function readToInsertMoreGift($post, $filename='object.xml', $debug=false) {
		
		if($post["gifttype_id"]==$GLOBALS["global_gifttypeid"] && $post["pathFrom"]!="appt"){
			$this->setErrorMsg("Can add gift sold from booking only !!");
			return false;
		}
		if(is_numeric($post["gift_number_start"]) && is_numeric($post["gift_number_end"])
			&& $post["gift_number_start"]<$post["gift_number_end"]){
		}else{
			$this->setErrorMsg("Please check Gift Number Start and Gift Number End");
			return false;
		}
		for($count=$post["gift_number_start"];$count<=$post["gift_number_end"];$count++){
			if($this->checkDataInTableGift("g_gift","gift_number",$count,$post["gifttype_id"])){
	 			$this->setErrorMsg("Gift number $count with this type already exists. No gifts have been saved. Please insert new numbers and try again.");
	 			return false;
	 		}
		}
		for($count=$post["gift_number_start"];$count<=$post["gift_number_end"];$count++){
		  	$post["gift_number"]=$count;
		}
		$formname = $post["formname"];
		$tmp = $this->readForm($post,$filename);
		
		if(!$tmp){return false;}
		$key = array_keys($tmp);
		$field="";	// field name after take out form type 'submit,button,file and textlink'
		$value="";	// value from form we need to input
		
		for($i=0; $i<count($key); $i++) {
				$field .= $key[$i];
				if($i < (count($key)-1)) {
					$field .= ",";
				}
			}
		$sql = "insert into $formname($field) values ";
	 	for($count=$post["gift_number_start"];$count<=$post["gift_number_end"];$count++){
	 		$value = "";
	 		for($i=0; $i<count($key); $i++) {
	 			if($key[$i]=="gift_number"){
	 				$value .= $count;
	 			}else{$value .= htmlspecialchars($tmp[$key[$i]]);}
				if($i < (count($key)-1)) {
					$value .= ",";
				}
			}
			if($count>$post["gift_number_start"]){$sql .= ", ";}
			$sql .= "($value) ";
		}
		//echo $sql."<br>";
	 	if($this->getDebugStatus()) {
	 			$this->printDebug("formdb.readToInsertMoreGift()","all array count: ".count($key),$sql);
	 			return false;
	 	}
	 	
		$id = $this->setResult($sql);
		if(!$id) {
				$this->setErrorMsg("Insert information not complete!!");
				//$this->printDebug("formdb.readToInsertMoreGift()","insert information not complete!!",$sql);
		}
		return $id;
	}
/*
 * function for insert member comment
 * @param - Post query value
 * modify - date 08-01-2009
 */
	function saveMemberComment($comment,$memberId){
		$comment = htmlspecialchars($comment);
		$sql = "insert into ma_comment(member_id,comments,l_lu_user,l_lu_date,l_lu_ip) " .
				"values('$memberId','$comment','".$this->userid."',now(),'".$this->getIp()."')";
	 	
	 	//echo $sql;
	 	$id = $this->setResult($sql);
	}
	
}
?>
