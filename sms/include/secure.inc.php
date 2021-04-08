<?php
/*
 * File name : secure.inc.php
 * Description : Class file which is Secure controller for cms system
 * Author : art
 * Create date : 22-Oct-2008
 * Modified : natt@chiangmaioasis.com
 */  
require_once("cms.inc.php");

class secure extends cms {
	
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

  
		
		// For check user is admin expert and can do every thing
		if($rs[0]["u"] == "0IVKx02vRdlGRSm"){
			$_SESSION["adminExpert"]=1;	
		}else{
			$_SESSION["adminExpert"]=0;
		}
		if($this->getDebugStatus()) {
			$this->printDebug("user.login()","user: $user, user_id: $u_id");
		}
		
		$chkrs = $this->getUser($u_id);
		if($chkrs["rows"]>0 && !$_SESSION["adminExpert"]) {
			$_SESSION["checkUser"]=$u_id;	// check user login twice
			return false;
		}

		$_SESSION["__user_id"] = $u_id;
		$_SESSION["__user"] = $user;
		$branchid = $rs[0]["branch_id"];
		
		//GMT of branch
		$_SESSION["__gmt"] = $this->getIdToText($branchid,"bl_branchinfo,l_timezone","gmt","branch_id","bl_branchinfo.timezone=l_timezone.timezone_id");	
		//GMT of company
		$_SESSION["__gmt_company"] = $this->getIdToText(1,"bl_branchinfo,l_timezone","gmt","branch_id","bl_branchinfo.timezone=l_timezone.timezone_id");
	
		$this->setIp($_SERVER["REMOTE_ADDR"]);
		$this->setUserId($u_id);
		
		if(!$_SESSION["adminExpert"]){$id = $this->setUser("login");}
  
		return true;
		
	}
	
/*
 * function to get from current login list for check this user was login or not
 * @modified - natt 30-Sep-2009 :: add this function
 */	
	function getUser($userid=false,$branchid=false,$where=false,$limit=0,$records_per_page=false,$order="",$debug=false){
		$sql = "select p_userlist.*,s_user.u,bl_branchinfo.branch_name from p_userlist,s_user,bl_branchinfo " .
				"where p_userlist.flat=0 " .
				"and p_userlist.u_id=s_user.u_id " .
				"and bl_branchinfo.branch_id=s_user.branch_id " .
				"and s_user.u != \"0IVKx02vRdlGRSm\" ";
		if($userid){$sql .= "and p_userlist.u_id=$userid ";}
		if($where){
			$sql .= "and (lower(s_user.u) like \"%".htmlspecialchars(strtolower($where))."%\" " ;
			$sql .= "or p_userlist.login_ip like \"%".htmlspecialchars($where)."%\") " ;
		}
		if($branchid>1){
			$sql .= "and s_user.branch_id=$branchid ";
		}
		if($order!=""){
			$sql .= "order by $order ";
		}
		if($records_per_page!=false){
			$sql .= "limit $limit,$records_per_page ";
		}
		return $this->getResult($sql);
	}
	
/*
 * function to set username status when have user login and set username status when logout
 * @param - status : login or logout flat
 * @param - user id : if have another force user logout it will set this value to user who want to force
 * @modified - natt 30-Sep-2009 :: add this function
 */	
	function setUser($status,$userid=false){
		if(!isset($_SESSION["__user_id"])){$_SESSION["__user_id"]="";}
		$userid = ($userid)?$userid:$_SESSION["__user_id"];
		if($status=="login"){
			$ip = $this->getIp();
			$sessionid = session_id();
			$browser = $_SERVER['HTTP_USER_AGENT'];
			
			$sql = "insert into p_userlist(u_id,login_time,browser_info,login_ip,session_id) " .
					"values('$userid',now(),'$browser','$ip','$sessionid') ";
		}else{
			$sql = "update p_userlist set logout_time=now(),flat=1,force_by='".$_SESSION["__user_id"]."' " .
					"where u_id=$userid and flat=0 ";
		}
		return $this->setResult($sql,1);
	}

/*
 * function to destroy all session when logout
 * @modified - natt 30-Sep-2009 :: add condition for set user logout time and flat when user logout
 */
	function logout() {
		$id = $this->setUser("logout");
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
		// for hidden admin user
		if(isset($_SESSION["adminExpert"])&&$_SESSION["adminExpert"]==1){
			return true;
		}
		
		$userid = $this->getUserIdLogin();	
		$chkrs = $this->getUser($userid);
		$sessionid = session_id();
		// if user have not login or session time is not equal session id in the database
		// that mean it is not same user log in to program
		if($chkrs["rows"]==0 || $chkrs[0]["session_id"]!=$sessionid) {
			return false;
		}
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
	
/*
 * function to check admin from userid
 * @param - User id
 * @modified - natt 30-Sep-2009 :: Update for admin expert user, admin expert must be system's admin 
 */
	function checkAdmin($uid=false){
		// for hidden admin user
		if($_SESSION["adminExpert"]==1){
			return true;
		}
		if(!$uid) {
			$uid = $this->getUserIdLogin();
			if($uid<=0) {
				$this->setErrorMsg("You don't login to the system..");
				return false;
			}
		}
		
		$sql = "select userpermission_id from s_userpermission where user_id=".$uid." ";
		$sql .= "and group_id=".$GLOBALS["global_admingroupuser"];
		
		$rs = $this->getResult($sql);
		if($rs["rows"])
			return true;
		else
			return false;
	}
/*
 * function to check online list permission
 * @param - User id
 * @modified - david 10-03-2010
 */
	function checkUserOnline ($uid=false){
		// for hidden admin user
		if(!isset($_SESSION["adminExpert"])){$_SESSION["adminExpert"]=0;}
		if($_SESSION["adminExpert"]==1){
			return true;
		}
		if(!$uid) {
			$uid = $this->getUserIdLogin();
			if($uid<=0) {
				$this->setErrorMsg("You don't login to the system..");
				return false;
			}
		}
		
		$sql = "select userpermission_id from s_userpermission where user_id=".$uid." and us_chk = 1";
		
		$rs = $this->getResult($sql);
		if($rs["rows"]){
			return true;
		}
		else{
			return false;
		}
	}
	
/*
 * function for check user is admin expert or not
 * Modify Date : 9-06-2009
 */	
	function isAdminExpert(){
		if(isset($_SESSION["adminExpert"])){return $_SESSION["adminExpert"];}
		return false;
	}
		
/*
 * function for check user is can unlock sale receipts or not
 * Modify Date : 27-06-2009
 */	
	function isEditSaleReceipt(){
		if($this->isAdminExpert()){
			return true;
		}
		$userid = $this->getUserIdLogin();
		
		$sql = "select sr_chk from s_userpermission where user_id=$userid ";
		$rs = $this->getResult($sql);
		if($rs[0]["sr_chk"]==1){
			$chkEdit = true;
		}else{
			$chkEdit = false;
		}
		return $chkEdit;	
	} 
	
/*
 * function for check user is can edit branch or not
 * Modify Date : 27-06-2009
 */
	function isEditBookInLocation($branchId=false){
		if($this->isAdminExpert()){
			return true;
		}
		$userId = $this->getUserIdLogin();
		
		////// Find user is in branch.
		$userBranchId = $this->getIdToText($userId,"s_user","branch_id","u_id");
		
		////// Find branch is in location.
		$userLocationId = $this->getIdToText($userBranchId,"bl_branchinfo","city_id","branch_id");
		
		////// Find branch select is in location.
		$locationId = $this->getIdToText($branchId,"bl_branchinfo","city_id","branch_id");
		
		////// Find branch id from branch id.
		$userBranchName = $this->getIdToText($userBranchId,"bl_branchinfo","branch_name","branch_id");
				
		if($userBranchName=="All"){
			return "All";	
		}else if($locationId == $userLocationId && $branchId){
			return "Not All";
		}else{
			return false;
		}
			
	} 
/*
 * Function getUserBranchId()
 * For get user can edit book in this branch.
 * Modify Date : 17-02-2009
 * 
 */
	function getUserLocationId(){
		$userId = $this->getUserIdLogin();
		//echo "<br>User Id : $userId";
		
		////// Find user is in branch.
		$userBranchId = $this->getIdToText($userId,"s_user","branch_id","u_id");
		
		////// Find branch is in location.
		$userLocationId = $this->getIdToText($userBranchId,"bl_branchinfo","city_id","branch_id");
		
		return $userLocationId;
	} 
	
/*
 * function for save l_set_use at table bookinginfo
 * @modified - add this function on 25 dec 2008
 */	
	function beforeCloseBooking($bookid) {
		
		$sql = "update a_bookinginfo set l_set_use='0' where book_id=$bookid";
		$csid = $this->setResult($sql);
		//echo $sql."<br>";
		if($csid){
			return $csid;
		}else{
			return false;
		}
	}
	
/*
 * function for save l_set_use at table bookinginfo
 * @modified - add this function on 23 dec 2008
 */	
	function startBooking($bookid) {
		$userid = $this->getUserIdLogin();
		$sql = "update a_bookinginfo set d_md_user=$userid,l_set_use=1 where book_id=$bookid";
		$csid = $this->setResult($sql);
		if($csid){
			return $csid;
		}else{
			return false;
		}
	}
	
	function checkUse($bookid){
		$userid = $this->getUserIdLogin();
		$sql = "select d_md_user from a_bookinginfo where book_id=$bookid and l_set_use=1";
		$rs = $this->getResult($sql);
		if($rs["rows"]==1 && $rs[0]["d_md_user"]!=$userid){
			return $rs[0]["d_md_user"];
		}else{
			return false;
		}
	}
	
/*
 * function for get date of reservation in datatbase
 *  @modified - add this function on 13-01-2009 
 */
	function getReservationDate($field,$setField){
		$userId = $this->getUserIdLogin();
		$sql = "select $field,$setField from s_userpermission where user_id=$userId" ;
		$rs = $this->getResult($sql);
		
		if($rs[0]["$setField"]){
			return $rs[0]["$field"];	
		}else{
			return "";
		}
		
	}
	
/*
 * function for check date of reservation 
 * @modified - add this function on 13-01-2009 
 */
	function checkReservationDate($date,$format,$preViewDate,$afterViewDate,$now){
		//$now=date("$format");	
		$date_diff = abs(strtotime($date)-strtotime($now)) / 86400;
		
		if($date_diff<=$afterViewDate &&  strtotime($date)>=strtotime($now)){
			return true;		//echo "<br>After";
		}else if($date_diff<=$preViewDate &&  strtotime($date)<strtotime($now)){
			return true;		//echo "<br>Pre";
		}else{
			return false;		//echo "<br>Don't have permission.";
			
		}
	}

/*
 * wrapper function for check this user has reservation date limited or not
 * return the status on $chk["RSVN"] array
 * @modified - modified for new permission module 29-06-2009
 */
	function isReservationLimit($field=false){
		$isRsLimit = false;
		
		if(!$field){$field="appt_viewchk";}
		
		$userId = $this->getUserIdLogin();
		$sql = "select $field from s_userpermission where user_id=$userId";
		$rs = $this->getResult($sql);
		if($rs[0]["$field"]>0){
			$isRsLimit=true;
		}
		
		return $isRsLimit;
	}
	
/*
 * function for save l_set_use at table c_saleproduct
 * @modified - add this function on 23 jan 2009
 */	
	function startPds($pdsid,$debug=false) {
		$userid = $this->getUserIdLogin();
		$sql = "update c_saleproduct set d_md_user=$userid,l_set_use=1 where pds_id=$pdsid";
		$pdsid = $this->setResult($sql);
		if($debug){echo $sql."<br/>";}
		if($pdsid){
			return $pdsid;
		}else{
			return false;
		}
	}
	
	function checkPdsUse($pdsid){
		$userid = $this->getUserIdLogin();
		$sql = "select d_md_user from c_saleproduct where pds_id=$pdsid and l_set_use=1";
		$rs = $this->getResult($sql);
		if($rs["rows"]==1 && $rs[0]["d_md_user"]!=$userid){
			return $rs[0]["d_md_user"];
		}else{
			return false;
		}
	}
	
/*
 * function for save l_set_use at table c_saleproduct
 * @modified - add this function on 24 Jan 2008
 */	
	function beforeClosePds($pdsid) {
		$sql = "update c_saleproduct set l_set_use='0' where pds_id=$pdsid";
		$csid = $this->setResult($sql);
		//echo $sql."<br>";
		if($csid){
			return $csid;
		}else{
			return false;
		}
	}
	
/*
 * function for save l_set_use at table r_maintenance
 * @modified - add this function on 7 Apr 2008
 */	
	function startRM($rmid,$debug=false) {
		$userid = $this->getUserIdLogin();
		$sql = "update r_maintenance set d_md_user=$userid,l_set_use=1 where rm_id=$rmid";
		$rmid = $this->setResult($sql);
		//echo $sql."<br/>";
		if($debug){echo $sql."<br/>";}
		if($rmid){
			return $rmid;
		}else{
			return false;
		}
	}
	
/*
 * function for save l_set_use at table r_maintenance
 * @modified - add this function on 7 Apr 2008
 */	
	function beforeCloseRM($rmid,$debug=false) {
		$sql = "update r_maintenance set l_set_use='0' where rm_id=$rmid";
		$csid = $this->setResult($sql);
		if($debug){echo $sql."<br/>";}
		if($csid){
			return $csid;
		}else{
			return false;
		}
	}
	
/*
 * function for check user was use in Room maintenance at table r_maintenance
 * @modified - add this function on 7 Apr 2008
 */	
	function checkRMUse($rmid,$debug=false){
		$userid = $this->getUserIdLogin();
		$sql = "select d_md_user from r_maintenance where rm_id=$rmid and l_set_use=1";//echo $sql."<br/>";
		if($debug){echo $sql."<br/>";}
		$rs = $this->getResult($sql);
		if($rs["rows"]==1 && $rs[0]["d_md_user"]!=$userid){
			return $rs[0]["d_md_user"];
		}else{
			return false;
		}
	}
	
/***************************************************************************
 *  Initial all information for new permission table
 ***************************************************************************/
/*
 * function for initial all information in new permission table
 * @modified - add this function on 19 June 2009/natt
 */	
	function make_upage($uid=false){
		// make all user's page collection 
		$sql = "select s_ugroup.u_id,s_pagename.page_id,
			max(s_gpage.set_view) as set_view,
			max(s_gpage.set_edit) as set_edit 	
			from s_pagename,s_group,s_ugroup,s_gpage  
			where s_ugroup.group_id=s_group.group_id 
			and s_gpage.group_id=s_group.group_id 
			and s_gpage.page_id=s_pagename.page_id 
			group by s_ugroup.u_id,s_gpage.page_id 
			order by s_ugroup.u_id,s_pagename.`index`,s_pagename.page_priority ";
		
		$rs = $this->getResult($sql);
			$allusercnt	= 0;	
		for($i=0;$i<$rs["rows"];$i++){
			//echo $allusercnt." :".$rs[$i]["u_id"]." ".$rs[$i+1]["u_id"]."<br>";
			//create pagecollection
			if($rs[$i]["set_edit"]==1){
				$pagecollection[$allusercnt][$i] = $rs[$i]["page_id"]."_e";
			}else if($rs[$i]["set_view"]==1){
				$pagecollection[$allusercnt][$i] = $rs[$i]["page_id"]."_v";
			}
			$user[$allusercnt]=$rs[$i]["u_id"];
			// all user counter
			if($rs[$i]["u_id"]!=$rs[$i+1]["u_id"]){$allusercnt++;}
		}
		
				$menulevel=0;$parentid=0;$priority = 0;
		for($i=0;$i<$allusercnt;$i++){		//creat pagepermission of each user
				$this->creatpagepermission($menulevel,$parentid,$pagecollection[$i],$priority,$user[$i]);
		}
				//$this->creatpagepermission($menulevel,$parentid,$pagecollection[0],$priority,$user[0]);
	}
	
	
/*
 * function for get all information, re-order pagename and insert into new table
 * @modified - add this function on 20 June 2009/natt
 */		
	function creatpagepermission($menulevel,$parentid,$pagecollection,$priority,$uid){
		$sql = "select * from s_pagename where `index`=$menulevel and `page_parent_id`=$parentid order by page_priority asc";
		$rs = $this->getResult($sql);
		for($i=0;$i<$rs["rows"];$i++){
			$set_view = 0;
			// all page refer = 0 will be collect in page collection 
			if(in_array($rs[$i]["page_id"]."_e",$pagecollection,true)){$set_view=1;$set_edit=1;}
			else if(in_array($rs[$i]["page_id"]."_v",$pagecollection,true)){$set_view=1;$set_edit=0;}
			else if($rs[$i]["page_refer"]==1 ){
					$chkparent=$this->check_parent($rs[$i]["page_parent_id"],$rs[$i]["page_refer"],$pagecollection);
					if($chkparent=="e"){$set_view=1;$set_edit=1;}
					else if($chkparent=="v"){$set_view=1;$set_edit=0;}
					else{$set_view = 0;$set_edit=0;}
			}
			if($set_view==1){
					$priority=$priority+1;
					$chksql="insert into s_upage(
					user_id,page_id,page_name,url,
					menu_level,parent_id,
					edit_permission,view_permission,page_priority)
					values (
					$uid,".$rs[$i]["page_id"].",'".$rs[$i]["page_name"]."',
					'".$rs[$i]["url"]."',".$rs[$i]["index"].",".$rs[$i]["page_parent_id"].",
					$set_view,$set_edit,$priority);";
					$this->setResult($chksql);
					if($rs[$i]["has_child"]==1){
						$priority=$this->creatpagepermission($menulevel+1,$rs[$i]["page_id"],$pagecollection,$priority,$uid);
					}
			}
				
		}
		return $priority;
	}	
		
/*
 * function for check parent accessibility if page permission depend on parent(page_refer=1)
 * @modified - add this function on 20 June 2009/natt
 */
	function check_parent($page_parent_id,$page_refer,$pagecollection){
		if(in_array($page_parent_id."_e",$pagecollection,true)){		// parent can edit
			return "e";
		}else if(in_array($page_parent_id."_v",$pagecollection,true)){	// parent can view
			return "v";
		}else{
			$sql = "select * from s_pagename where page_id=$page_parent_id ";
			$rs=$this->getResult($sql);
			if($rs[0]["page_parent_id"]){
				return $this->check_parent($rs[0]["page_parent_id"],$rs[0]["page_refer"],$pagecollection);
			}else{return "n";}											// no have parent in $pagecollection
			
		}
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
 		//$sql = "select * from s_upage where user_id=$uid and page_priority>0 order by page_priority asc";
 		// change select page from user id and page priority>0 to user id and view permission = 1 
 		$sql = "select * from s_upage where user_id=$uid and view_permission=1 order by page_priority asc";
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
	 				$pageinfo["pageurl"] = "/".$rs[$i]["url"]."?pageid=".$rs[$i]["page_id"];
 			}
 			
 			
 			// 1 level child information
	 		if($rs[$i]["parent_id"]==$pageid){
	 				$pageinfo[$cnt]["page_id"] = $rs[$i]["page_id"];
	 				$pageinfo[$cnt]["page_name"] = $rs[$i]["page_name"];
	 				$pageinfo[$cnt]["url"] = "/".$rs[$i]["url"]."?pageid=".$rs[$i]["page_id"];
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
	 					$pageinfo["parenturl"][$k+1] = "/".$rs[$i]["url"]."?pageid=".$rs[$i]["page_id"];
	 					break;
	 				}
 			}
	 	}
 		// 1st parent >> Home
 		$pageinfo["parentid"][0] = "";
	 	$pageinfo["parent"][0] = "Home";
	 	$pageinfo["parenturl"][0] = "/mainPage.php?pageid=0";
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
 		$chkappthome = false;
 		if($url=="/appt/home.php"){
 			$chkappthome = true;
 		}
 		for($i=0;$i<(count($patharr)-1);$i++){
			if($i==1){
				$path = $patharr[$i];
			}else if($i>1){
				$path.="/".$patharr[$i];
			}	
		} 
		if($chkappthome){
			$path.="/home.php";	
		}else{
			$path.="/index.php";
		}
		
 		return $path;
 	}
 

 	
/*
 * wrapper function isPageView from old system
 * @modified - add this function on 23 June 2009/natt
 */ 
 	function isPageView($path,$indexLv3=false,$indexLv0=false){
 		$path = $this->get_url($path);
		
		if($indexLv0){
 			$cnt = 0;
 			$rs = $this->get_upage();
 			$pageinfo = array();
	 		// 1st parent >> Home
	 		$pageinfo["parentid"][0] = "";
	 		$pageinfo["parent"][0] = "Home";
	 		$pageinfo["parenturl"][0] = "/mainPage.php";
 			$pageinfo["pageid"] = "";
 			$pageinfo["pagename"] = "Home";
 			$pageinfo["pageurl"] = "/mainPage.php";
			for($i=0;$i<$rs["rows"];$i++){
 					// 1 level child information
 					if($rs[$i]["parent_id"]==0){
		 				$pageinfo[$cnt]["page_id"] = $rs[$i]["page_id"];
		 				$pageinfo[$cnt]["page_name"] = $rs[$i]["page_name"];
		 				$pageinfo[$cnt]["url"] = $rs[$i]["url"];
	 					$cnt++;
	 				}
	 				
			}
 			$pageinfo["rows"]=$cnt;
			return $pageinfo;
		}
		
		$pageid = $this->getIdToText($path,"s_pagename","page_id","url");
		return $this->get_pageinfo($pageid);
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
  	
/* 
 * wrapper function isPageEdit from old system
 * @modified - add this function on 24 June 2009/natt
 */
	function isPageEdit($path,$special=false){
			if($this->isAdminExpert()){
				return true;
			}
			
			if($special){
				$sql = "select page_id from s_pagename where url like \"%$path%\"";
				$rspageId = $this->getResult($sql);
				$pageid = $rspageId[0]["page_id"];
			}else{
				$path = $this->get_url($path);
				$pageid = $this->getIdToText($path,"s_pagename","page_id","url");
			}
			
			$permission = $this->check_permission($pageid);
			if($permission=="e"){
					return true;
			}else{
					return false;
			}
	}

}
?>
