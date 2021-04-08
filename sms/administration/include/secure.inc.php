<?php
/*
 * File name : secure.inc.php
 * Description : Class file which is Secure controller for cms system
 * Author : art
 * Create date : 22-Oct-2008
 * Modified : natt@chiangmaioasis.com
 */  
require_once("main.inc.php");

class secure extends main {
	
/*
 * function to check user enable from result set
 * @param - result set of user from table s_user
 * 
 */
 	function IsUserEnable($rs) {
		if($rs[0]["active"])
			return true;
		else
			return false;
	}
	
/*
 * function to check username and password when have user login and set username and userid to "__user" and "__user_id" session when logout
 * @param - Username
 * @param - Password
 * @modified - natt 30-Sep-2009 :: add condition for check and update user login with duplicate account or not
 */	
	function login($user=false, $pass=false) {
		$rs = $this->checkUser($user);
		
		if($rs["rows"] < 1) {
			$this->setErrorMsg("User not found...");
			return false;
		}
		$u_id = $rs[0]["u_id"];
		
		if(!$this->IsUserEnable($rs)) {
			$this->setErrorMsg("Your account have disable...");
			return false;
		}
		
		$newpass = md5($pass);
		
		if($rs[0]["pass"] != $newpass) {
			$this->setErrorMsg("The password is incorrect...");
			return false;
		}
		
		if($this->getDebugStatus()) {
			$this->printDebug("user.login()","user: $user, user_id: $u_id");
		}
		
		$_SESSION["__user_id"] = $u_id;
		$_SESSION["__user"] = $user;
		
		$this->setIp($_SERVER["REMOTE_ADDR"]);
		$this->setUserId($u_id);
		
		return true;
	}

/*
 * function to destroy all session when logout
 * @modified - natt 30-Sep-2009 :: add condition for set user logout time and flat when user logout
 */
	function logout() {
		session_destroy();
	}	
	
	
/********************************************************
 * Login and check the login permission
 ********************************************************/ 	
/* 
 * function for check user was login or not from "__user_id" session
 * @modified - natt 30-Sep-2009 :: add condition for check expert admin and check user account is duplicate or not
 */
	function checkLogin() {	
		if(isset($_SESSION["__user_id"])&&$_SESSION["__user_id"]>0){return true;}
		else{return false;}
	}
	
/*
 * function to get user login id from "__user_id" session
 */	
	function getUserIdLogin() {
		//For check debug undefine index : __user_id. By Ruck : 16-05-2009 
		if(isset($_SESSION["__user_id"])){
			return $_SESSION["__user_id"];		
		}else{
			return false;
		}
	}
	
/*
 * function to get user login name from "__user" session
 */		
	function getUserLogin(){
		//For check debug undefine index : __user. By Ruck : 16-05-2009
		if($_SESSION["__user"]){
			return $_SESSION["__user"];		
		}else{
			return false;
		}
	}
	
	
/*
 * function to set and get user login id
 * @param - User id
 */	
	function setUserId($newuserid=false) {
		$this->userid = $newuserid;
		return true;
	}
	
/*
 * function for check ErrorReport status
 * Modify Date : 01-12-2008
 */
	function issetErrorReport(){
		if(isset($_SESSION["__error_reporting"])){
			return true;
		}else{
			return false;
		}
	}
	function resetErrorReport(){
		unset($_SESSION["__error_reporting"]);
	}		
	
/***************************************************************************
 *  Initial all information for new permission table
 ***************************************************************************/
/*
 * function for get all page information that user can access
 * @modified - add this function on 22 June 2009/natt
 */	
 	function get_upage($debug=false){
 		$uid = $this->getUserIdLogin();
 		$sql = "select * from s_upage where user_id=$uid and page_priority>0 order by page_priority asc";
 		$rs = $this->getResult($sql,$debug);
 		return $rs;
 	}
 	
/*
 * function for get all parent id from s_upage resultset
 * @modified - add this function on 22 June 2009/natt
 */
 	function get_parent($pageid,$rs){
 		$parent = array();
 		$priority=0;
 		$menulevel=0;
 		for($i=$rs["rows"]-1;$i>=0;$i--){
 			if($pageid==$rs[$i]["page_id"]){
 				$priority=$rs[$i]["page_priority"];
 				$menulevel=$rs[$i]["menu_level"];
 				$parent[$menulevel-1]=$rs[$i]["parent_id"];
 			}
 			// $priority - prevent undified index
 			if($priority&&$rs[$i]["page_id"]==$parent[$menulevel-1]&&$rs[$i]["menu_level"]<$menulevel){
 				$menulevel=$rs[$i]["menu_level"];
 				$parent[$menulevel-1]=$rs[$i]["parent_id"];
 			}
 			if($menulevel==0&&$priority){
 				break;
 			}
 			
 		}
 		//print_r($parent);
 		
 		return $parent;
 	}
 	
/*
 * function for get all child id from s_upage resultset
 * @modified - add this function on 22 June 2009/natt
 */
 	function get_child($pageid,$rs){
 		$child = array();
 		$cnt=0;$j=0;	// counter
 		
 		// get all parent page that posible in lower of this $pageid tree
 		$parentid = array();
 		$parentid[0] = $pageid;	
 		for($i=0;$i<$rs["rows"];$i++){
 			if(in_array($rs[$i]["parent_id"],$parentid)){
 				$j++;
 				$parentid[$j]=$rs[$i]["page_id"];
 			} 			
 		}
 		
 		// get all child page 
 		for($i=0;$i<$rs["rows"];$i++){
 			if(in_array($rs[$i]["parent_id"],$parentid)){
 				$child[$cnt]=$rs[$i]["page_id"];
 				$cnt++;
 			} 			
 		}
 		return $child;
 	}
 	
/*
 * function for revise s_upage resultset information for use in all page
 * @modified - add this function on 23 June 2009/natt
 */ 	
 	function get_pageinfo($pageid,$rs=false){
 		if($rs==false){
 			$rs = $this->get_upage();
 		}
 		$pageinfo = array();
 		$cnt=0;
 		for($i=0;$i<$rs["rows"];$i++){
 			if($rs[$i]["page_id"]==$pageid){
 					$pageinfo["pageid"] = $rs[$i]["page_id"];
	 				$pageinfo["pagename"] = $rs[$i]["page_name"];
	 				$pageinfo["pageurl"] = "/administration/".$rs[$i]["url"]."?pageid=".$rs[$i]["page_id"];
 			}
 			
 			
 			// 1 level child information
	 		if($rs[$i]["parent_id"]==$pageid){
	 				$pageinfo[$cnt]["page_id"] = $rs[$i]["page_id"];
	 				$pageinfo[$cnt]["page_name"] = $rs[$i]["page_name"];
	 				$pageinfo[$cnt]["url"] = "/administration/".$rs[$i]["url"]."?pageid=".$rs[$i]["page_id"];
	 				$cnt++;
	 		}
 		}
 		$pageinfo["parent_id"]=$this->get_parent($pageid,$rs);
	 	// all parent information
	 	for($k=0;$k<count($pageinfo["parent_id"]);$k++){
 			for($i=0;$i<$rs["rows"];$i++){
	 				if(isset($pageinfo["parent_id"][$k])&&$pageinfo["parent_id"][$k]==$rs[$i]["page_id"]){
	 					$pageinfo["parentid"][$k+1] = $rs[$i]["page_id"];
	 					$pageinfo["parent"][$k+1] = $rs[$i]["page_name"];
	 					$pageinfo["parenturl"][$k+1] = "/administration/".$rs[$i]["url"]."?pageid=".$rs[$i]["page_id"];
	 					break;
	 				}
 			}
	 	}
 		// 1st parent >> Home
 		$pageinfo["parentid"][0] = "";
	 	$pageinfo["parent"][0] = "Home";
	 	$pageinfo["parenturl"][0] = "/administration/mainPage.php?pageid=0";
 		$pageinfo["rows"]=$cnt;
 		return $pageinfo;
 	}

/*
 * function for get part that match with database s_pagename table
 * @modified - add this function on 23 June 2009/natt
 */
 	function get_url($url=false){
 		if($url==false){
 			$url = $_SERVER["PHP_SELF"];
 		}
 		$patharr = explode("/",$url);
 		for($i=0;$i<(count($patharr)-1);$i++){
			if($i==1){
				$path = $patharr[$i];
			}else if($i>1){
				$path.=$patharr[$i];
			}	
		} 
		$path.="index.php";
		
 		return $path;
 	}
 
 	
/***************************************************************************
 *  Check all permission from new permission table
 ***************************************************************************/
/*
 * function for check user permission 
 * set back 3 status - e can edit
 * 					 - v can view
 * 					 - n can't access
 * @modified - add this function on 24 June 2009/natt
 */	
 	function check_permission($pageid,$rs=false){
 		if($pageid==0){		// Home page
 			return "v";
 		}
 		
 		if($rs==false){
 			$rs = $this->get_upage();
 		}
 		$permission = "n";
 		
 		for($i=0;$i<$rs["rows"];$i++){
 			if($pageid==$rs[$i]["page_id"]){
 				if($rs[$i]["view_permission"]){
 					$permission = "v";
 				}
 				if($rs[$i]["edit_permission"]){
 					$permission = "e";
 				}
 				break;
 			}
 		}
 		
 		return $permission;
 	}
}

?>
