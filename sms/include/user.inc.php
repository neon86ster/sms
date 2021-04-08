<?php

/*
 * File name : user.inc.php
 * Description : Class file of program permission setting for cms system
 * Author : natt
 * Create date : 16-Dec-2009
 */
require_once ("secure.inc.php");

class user extends secure {

	/***************************************************************************
	 *  Update new permission table
	 ***************************************************************************/
	/*
	 * function for update group/user permission
	 * @modified - add this function on 24 June 2009/natt
	 * Update function for keep log_viewchk on table s_group and s_userpermission.
	 * log_viewchk variable for check who can view change log on booking page.
	 * @modified - 04-sep-2009/ruck
	 * Re-coding.
	 * @modified - 23-sep-2009/ruck.
	 */
	function update_permission($post, $id, $tablename, $debug = false) {

		/////// For initial all parameter want to use. ///////////////////////
		$permission = array ();
		$permission["add"] = (isset ($post["add"])) ? $post["add"] : 0;
		$permission["cms_update_time"] = (isset ($post["cms_update_time"])) ? $post["cms_update_time"] : 0;
		$permission["uschk"] = (isset ($post["uschk"])) ? $post["uschk"] : 0;
		$permission["srchk"] = (isset ($post["srchk"])) ? $post["srchk"] : 0;
		$permission["logviewchk"] = (isset ($post["logviewchk"])) ? $post["logviewchk"] : 0;
		$permission["apptviewchk"] = (isset ($post["appt_viewchk"])) ? $post["appt_viewchk"] : 0;
		$permission["appteditchk"] = (isset ($post["appt_editchk"])) ? $post["appt_editchk"] : 0;
		$permission["pre_viewdate"] = (isset ($post["previewdate"])) ? $post["previewdate"] : 0;
		$permission["after_viewdate"] = (isset ($post["viewdateafter"])) ? $post["viewdateafter"] : 0;
		$permission["pre_editdate"] = (isset ($post["preeditdate"])) ? $post["preeditdate"] : 0;
		$permission["after_editdate"] = (isset ($post["editdateafter"])) ? $post["editdateafter"] : 0;
		$permission["group_id"] = (isset ($post["group_id"])) ? $post["group_id"] : 0;
		$permission["group_id"] = ($permission["group_id"] == "customized") ? "0" : $permission["group_id"];
		$permission["l_lu_user"] = $_SESSION["__user_id"];
		$permission["l_lu_ip"] = "'" . $_SERVER["REMOTE_ADDR"] . "'";
		$permission["pagepermission"] = explode(",", (isset ($post["pagepermission"])) ? $post["pagepermission"] : "");

		if ($permission["appteditchk"] == 0) {
			$permission["pre_viewdate"] = 0;
			$permission["after_viewdate"] = 0;
			$permission["pre_editdate"] = 0;
			$permission["after_editdate"] = 0;
		}

		// Condition for add or update user and group template.
		if ($tablename == "s_upage") {
			// If user don't have page on table s_upage then insert page again every case.
			$chkPage = $this->getIdToText($id,"s_upage","page_id","user_id");
	 		if($permission["add"] == " add " || !$chkPage){
				if (!$this->insert_user($permission, $id, $debug)) {
					return false;
				}
			} else
				if ($permission["add"] == " save change ") {
					if (!$this->update_user($permission, $id, $debug)) {
						return false;
					}
				}
		} else
			if ($tablename == "s_grouptemplate") {
 			// If user don't have page on table s_upage then insert page again every case.
			$chkPage = $this->getIdToText($id,"s_grouptemplate","page_id","group_id");
	 		if($permission["add"] == " add " || !$chkPage){
					if (!$this->insert_grouptemplate($permission, $id, $debug)) {
						return false;
					}
				} else
					if ($permission["add"] == " save change ") {
						if (!$this->update_grouptemplate($permission, $id, $debug)) {
							return false;
						}
					}
			} else {
				$this->setErrorMsg("Error setting permission table!!");
				return false;
			}

		// Debug status
		if ($debug) {
			return false;
		} else {
			return true;
		}
	}

	/*
	 * Function insert_user()
	 * For insert user permission into table s_userpermission and s_upage.
	 * @parame - permission : array keep all permission must be set into table s_userpermission and s_upage.
	 * @parame - user_id : id of user.
	 * @parame - debug : debug status.
	 * @modified - 23-sep-2009/ruck.
	 */
	function insert_user($permission, $user_id, $debug = false) {
		// Insert query for insert data into table s_userpermission.
		$insertUserpermission = "insert into s_userpermission (`user_id`, `group_id` , `cms_update_time` , `appt_viewchk` ," .
								"`appt_editchk` , `pre_viewdate` , `after_viewdate` , `pre_editdate` , `after_editdate` ," .
								"`us_chk` ,`sr_chk` ,`log_viewchk`, `l_lu_user` , `l_lu_date` , `l_lu_ip` , `active`)" .
								"VALUES ( $user_id, " . $permission["group_id"] . ", " . $permission["cms_update_time"] . ", " . $permission["apptviewchk"] . "," .
								"" . $permission["appteditchk"] . ", " . $permission["pre_viewdate"] . ", " . $permission["after_viewdate"] . "," .
								"" . $permission["pre_editdate"] . ", " . $permission["after_editdate"] .", ".$permission["uschk"]. "," .
								"" . $permission["srchk"] . "," . $permission["logviewchk"] . ", " . $permission["l_lu_user"] . ", now()," .
								"" . $permission["l_lu_ip"] . ", 1);";
		
		// Insert query for insert page permission into table s_upage.	
		$insertUpage = "insert into s_upage(user_id,page_id,page_name,url" .
						",menu_level,parent_id,group_id,view_permission" .
						",edit_permission,page_priority) values ";

		$values = array (); // Array for keep all value for insert into table s_upage.

		// Get all page from table s_pagename.
		$sql = "select * from s_pagename order by page_priority";
		$rs = $this->getResult($sql);
		for ($i = 0; $i < $rs["rows"]; $i++) {
			// case has permission in old permission setting but not set in new
			$set_edit = 0;
			$set_view = 0;

			// case has permission in old permission setting and set in new
			if (in_array($rs[$i]["page_id"] . "_e", $permission["pagepermission"], true)) {
				$set_edit = 1;
				$set_view = 1;
			} else
				if (in_array($rs[$i]["page_id"] . "_v", $permission["pagepermission"], true)) {
					$set_edit = 0;
					$set_view = 1;
				}
			$values[$i] = "($user_id," . $rs[$i]["page_id"] . ",'" . $rs[$i]["page_name"] . "'," .
			"'" . $rs[$i]["url"] . "'," . $rs[$i]["index"] . "," . $rs[$i]["page_parent_id"] . "," .
			"" . $permission["group_id"] . ",$set_view,$set_edit," . $rs[$i]["page_priority"] . ")";
		}

		$insertUpage .= implode(",", $values);

		if ($debug) {
			echo "<br> Debug insert user permission sql : $insertUserpermission";
			echo "<br> Debug insert user page permisison sql : $insertUpage";
		} else {
			$chkPermission = $this->getIdToText($user_id, "s_userpermission", "user_id", "user_id");
			// In case add user permission fail. When update user next time.
			// Not insert data into table s_userpermission.
			if (!$chkPermission) {
				if (!$this->setResult($insertUserpermission)) {
					$this->setErrorMsg("Error insert user permission on table s_upage !!");
					return false;
				}
			}
			if (!$this->setResult($insertUpage)) {
				$this->setErrorMsg("Error insert user permission on table s_userpermisison !! $insertUpage");
				return false;
			}
		}
		return true;
	}

	/*
	 * Function update_user()
	 * For update user permission.
	 * @parame - permission : array keep all permission must be set into table s_userpermission and s_upage.
	 * @parame - user_id : id of user.
	 * @parame - debug statuse.
	 * @modified - 23-sep-2009/ruck.
	 */
	function update_user($permission, $user_id, $debug = false) {
		// For update this user on table s_userpermission.
		if (!$this->update_userpermission($permission, $user_id, "s_upage", "update", $debug)) {
			return false;
		}
		// For update active page of this user on table s_upage.
		if (!$this->update_access_page($permission, $user_id, "s_upage", $debug)) {
			return false;
		}
		// For update inactive page this user on table s_upage.		 			
		if (!$this->update_cannotaccess_page($permission, $user_id, "s_upage", $debug)) {
			return false;
		}
		return true;
	}

	/*
	 * Function insert_grouptemplate()
	 * For insert template permission into table s_grouptemplate and update table s_group.
	 * @parame - permission : array keep all permission must be set into table s_userpermission and s_upage.
	 * @parame - user_id : id of user.
	 * @parame - debug statuse.
	 * @modified - 23-sep-2009/ruck.
	 */
	function insert_grouptemplate($permission, $group_id, $debug = false) {
		// For update this group on table s_group.
		if (!$this->update_userpermission($permission, $group_id, "s_grouptemplate", "insert", $debug)) {
			return false;
		}

		// Insert query for insert page permission into table s_grouptemplate.	
		$insertTemplate = "insert into s_grouptemplate(group_id,page_id,page_name,url" .
		",menu_level,parent_id,view_permission,edit_permission,page_priority) values ";

		$values = array (); // Array for keep all value for insert into table s_grouptemplate.

		$sql = "select * from s_pagename order by page_priority";
		$rs = $this->getResult($sql);
		for ($i = 0; $i < $rs["rows"]; $i++) {
			// case has permission in old permission setting but not set in new
			$set_edit = 0;
			$set_view = 0;
			// case has permission in old permission setting and set in new
			if (in_array($rs[$i]["page_id"] . "_e", $permission["pagepermission"], true)) {
				$set_edit = 1;
				$set_view = 1;
			} else
				if (in_array($rs[$i]["page_id"] . "_v", $permission["pagepermission"], true)) {
					$set_edit = 0;
					$set_view = 1;
				}
			$values[$i] = "($group_id," . $rs[$i]["page_id"] . ",'" . $rs[$i]["page_name"] . "'," .
			"'" . $rs[$i]["url"] . "'," . $rs[$i]["index"] . "," . $rs[$i]["page_parent_id"] . "," .
			"$set_view,$set_edit," . $rs[$i]["page_priority"] . ")";
		}

		$insertTemplate .= implode(",", $values);

		if ($debug) {
			echo "<br> Debug insert template permisison sql : $insertTemplate";
		} else {
			if (!$this->setResult($insertTemplate)) {
				$this->setErrorMsg("Error insert template permission on table s_grouptemplate !!");
				return false;
			}
		}
		return true;
	}

	/*
	 * Function update_grouptemplate()
	 * For update group template and all user in that group template.
	 * @parame - permission : array keep all permission must be set into table s_userpermission and s_upage.
	 * @parame - user_id : id of user.
	 * @parame - debug statuse.
	 * @modified - 23-sep-2009/ruck.
	 */
	function update_grouptemplate($permission, $group_id, $debug = false) {
		if (!$this->update_userpermission($permission, $group_id, "s_grouptemplate", "update", $debug)) {
			return false;
		}
		if (!$this->update_access_page($permission, $group_id, "s_grouptemplate", $debug)) {
			return false;
		}
		if (!$this->update_cannotaccess_page($permission, $group_id, "s_grouptemplate", $debug)) {
			return false;
		}
		return true;
	}

	/*
	 * Funcction update_userpermisison()
	 * For update table s_group and s_upage when edit grouptemplate and user.
	 * @parame - permission : array keep all permission must be set into table s_userpermission and s_upage.
	 * @parame - user_id : id of user.
	 * @parame - table : table want to update.(s_grouptemplate or s_upage)
	 * @parame - debug statuse.
	 * @modified - 23-sep-2009/ruck.
	 */
	function update_userpermission($permission, $id, $table, $status, $debug = false) {
		// All value must be update on table s_grouptemplate and s_upage.
		$updateValue = "cms_update_time=" . $permission["cms_update_time"] . ", " .
		"appt_viewchk=" . $permission["apptviewchk"] . ", " .
		"appt_editchk=" . $permission["appteditchk"] . ", " .
		"pre_viewdate=" . $permission["pre_viewdate"] . ", " .
		"after_viewdate=" . $permission["after_viewdate"] . ", " .
		"pre_editdate=" . $permission["pre_editdate"] . ", " .
		"after_editdate=" . $permission["after_editdate"] . ", " .
		"us_chk=" . $permission["uschk"] . ", " .
		"sr_chk=" . $permission["srchk"] . ", " .
		"log_viewchk=" . $permission["logviewchk"] . ", " .
		"l_lu_user=" . $permission["l_lu_user"] . ", " .
		"l_lu_date=now(), " .
		"l_lu_ip=" . $permission["l_lu_ip"] . ", " .
		"active=1 ";

		if ($table == "s_grouptemplate") {
			// In case update group template must be update 2 table.
			// Table s_group : for update template permission.
			// Table s_userpermission : for update user permission.
			$updateGroup = "update s_group set $updateValue where group_id=$id ";

			// In case add new group template not update table s_userpermission. 
			if ($status != "insert") {
				$updateUserpermission = "update s_userpermission set $updateValue where group_id=$id ";
			}
		} else
			if ($table == "s_upage") {
				$updateUserpermission = "update s_userpermission set $updateValue,group_id=" . $permission["group_id"] . " where user_id=$id ";
			}

		if ($debug) {
			if ($table == "s_grouptemplate") {
				echo "<br>Debug update template page permission sql : $updateGroup";
			}
			if ($status != "insert" || $table != "s_grouptemplate") {
				echo "<br>Debug update user page permission sql : $updateUserpermission";
			}
		} else {
			// Do this condition only case edit group template.
			if ($table == "s_grouptemplate") {
				if (!$this->setResult($updateGroup)) {
					$this->setErrorMsg("Error update user permission on table s_group !!");
					return false;
				}
			}

			//In case insert new group template not update s_userpermission.
			if ($status != "insert" || $table != "s_grouptemplate") {
				if (!$this->setResult($updateUserpermission)) {
					$this->setErrorMsg("Error update user permission on table s_userpermisison !! $updateUserpermission");
					return false;
				}
			}
		}
		return true;
	}

	/*
	 * Function update_access_page()
	 * For update all page that user can access on interface.
	 * @parame - permission : array keep all page that user can access.
	 * @parame - id : id of user or group template.
	 * @parame - table : table want to update.(s_grouptemplate or s_upage)
	 * @parame - debug statuse.
	 * @modified - 23-sep-2009/ruck.
	 */
	function update_access_page($permission, $id, $table, $debug = false) {
		// If not set page permission from interface then don't do this function.
		if ($permission["pagepermission"][0] == "") {
			return true;
		}
		for ($i = 0; $i < count($permission["pagepermission"]); $i++) {
			$tmp = explode("_", $permission["pagepermission"][$i]);
			$pageid = $tmp[0];
			$statue = $tmp[1];
			// case has permission in old permission setting but not set in new
			$set_edit = 0;
			$set_view = 0;
			// case has permission in old permission setting and set in new
			if ($statue == "e") {
				$set_view = 1;
				$set_edit = 1;
			} else
				if ($statue == "v") {
					$set_view = 1;
					$set_edit = 0;
				}

			if ($table == "s_grouptemplate") {
				// In case update group template must be update 2 table.
				// Table s_grouptemplate : for update template permission.
				// Table s_upage : for update user permission.
				$updateTemplate = "update s_grouptemplate set " .
				"edit_permission=$set_edit, " .
				"view_permission=$set_view " .
				"where group_id=$id and page_id=$pageid ";

				$updateUpage = "update s_upage set " .
				"edit_permission=$set_edit, " .
				"view_permission=$set_view " .
				"where group_id=$id and page_id=$pageid ";
			} else
				if ($table == "s_upage") {
					$updateUpage = "update s_upage set " .
					"edit_permission=$set_edit, " .
					"view_permission=$set_view, " .
					"group_id=" . $permission["group_id"] . " " .
					"where user_id=$id and page_id=$pageid ";
				}

			if ($debug) {
				if ($table == "s_grouptemplate") {
					echo "<br>Debug update template page permission sql : $updateTemplate";
				}
				echo "<br>Debug update user page permission sql : $updateUpage";
			} else {
				// Do this condition only case edit group template.
				if ($table == "s_grouptemplate") {
					if (!$this->setResult($updateTemplate)) {
						$this->setErrorMsg("Error update user permission on table s_grouptemplate !!");
						return false;
					}
				}
				if (!$this->setResult($updateUpage)) {
					$this->setErrorMsg("Error update user permission on table s_upage !!$updateUpage");
					return false;
				}
			}
		}
		return true;
	}

	/*
	 * Function update_cannotaccess_page()
	 * For update all page that user can't access on interface.
	 * @parame - pageArray : array keep all page that user can access.
	 * @parame - id : id of user or group template.
	 * @parame - table : table want to update.(s_grouptemplate or s_upage)
	 * @parame - debug statuse.
	 * @modified - 23-sep-2009/ruck.
	 */
	function update_cannotaccess_page($permission, $id, $table, $debug = false) {
		$pageActive = implode(",", $permission["pagepermission"]); // Convert array to string.
		$pageActive = str_replace("_e", "", $pageActive); // Replace "_e" with empty character.
		$pageActive = str_replace("_v", "", $pageActive); // Replace "_v" with empty character.
		// Result of variable pageActive is "1,23,3,...". 		

		if ($table == "s_grouptemplate") {
			// In case update group template must be update 2 table.
			// Table s_grouptemplate : for update template permission.
			// Table s_upage : for update user permission.
			$updateTemplate = "update s_grouptemplate set edit_permission=0, view_permission=0 where ";
			$updateUpage = "update s_upage set edit_permission=0, view_permission=0 where ";
			// If page not set page permission from interface then update group template and user can't access all page.
			if ($pageActive != "") {
				$updateTemplate .= "page_id not in($pageActive) and ";
				$updateUpage .= "page_id not in($pageActive) and ";
			}
			$updateTemplate .= "group_id=$id";
			$updateUpage .= "group_id=$id ";
		} else
			if ($table == "s_upage") {
				$updateUpage = "update s_upage set edit_permission=0, view_permission=0, group_id=" . $permission["group_id"] . " where ";
				// If page not set page permission from interface then update user can't access all page.
				if ($pageActive != "") {
					$updateUpage .= "page_id not in($pageActive) and ";
				}
				$updateUpage .= "user_id=$id ";
			}

		if ($debug) {
			if ($table == "s_grouptemplate") {
				echo "<br>Debug update template page permission sql : $updateTemplate";
			}
			echo "<br>Debug update user page permission sql : $updateUpage";
		} else {
			// Do this condition only case edit group template.
			if ($table == "s_grouptemplate") {
				if (!$this->setResult($updateTemplate)) {
					$this->setErrorMsg("Error update user permission on table s_grouptemplate !!");
					return false;
				}
			}
			if (!$this->setResult($updateUpage)) {
				$this->setErrorMsg("Error update user permission on table s_upage !! $updateUpage");
				return false;
			}
		}
		return true;
	}

	/*
	* function update_s_pagename()
	* for update active or in active table s_pagename only.
	* Specific for Admin Expert update page to active or inactive.
	* @param - $pageActive : string of all page set to active (Example : $pageActive="2,3,4,5,8,9,10,...")
	* @param - $debug : flag for debug status.
	* @modified - add this function on 17 August 2009/ruck
	* 
	* Include condition for update table s_grouptemplate s_upage when set page to inactive.
	* @Modified - 02 September 2009 / ruck
	*/
	function update_s_pagename($pageActive, $debug = false) {

		if ($pageActive != "") {
			// If set some page to active.
			// Set some page to active. 		
			$sql = "update s_pagename set active=1 where page_id in($pageActive)";
			if ($debug) {
				echo "<br>Debug SQL : $sql";
			} else {
				if (!$this->setResult($sql)) {
					$this->setErrorMsg("Error update active page for page id : $pageActive!!");
					return false;
				}
			}
			// Set some page to inactive.
			$sql = "update s_pagename set active=0 where page_id not in($pageActive)";
			if ($debug) {
				echo "<br>Debug SQL : $sql";
			} else {
				if (!$this->setResult($sql)) {
					$this->setErrorMsg("Error update inactive page for page id not equal : $pageActive!!");
					return false;
				}
			}
			// set all group can't access that page set to inactive. 
			$sql = "update s_grouptemplate set edit_permission=0, view_permission=0 " .
			"where page_id not in($pageActive) ";
			if ($debug) {
				echo "<br>Debug SQL : $sql";
			} else {
				if (!$this->setResult($sql)) {
					$this->setErrorMsg("Error update permission on group template");
					return false;
				}
			}

			// set all user can't access that page set to inactive. Except Expert Admin.
			$expertAdminId = $this->getIdToText("0IVKx02vRdlGRSm", "s_user", "u_id", "u");
			$sql = "update s_upage set edit_permission=0, view_permission=0 " .
			"where page_id not in($pageActive) and user_id!=$expertAdminId ";
			if ($debug) {
				echo "<br>Debug SQL : $sql";
			} else {
				if (!$this->setResult($sql)) {
					$this->setErrorMsg("Error update permission on user page");
					return false;
				}
			}
		} else {
			// If not set any page to active
			// Set all page to inacitve
			$sql = "update s_pagename set active=0 ";
			if ($debug) {
				echo "<br>Debug SQL : $sql";
			} else {
				if (!$this->setResult($sql)) {
					$this->setErrorMsg("Error update inactive page for page id : $pageActive!!");
					return false;
				}
			}
			// set all group can't access that all page. 
			$sql = "update s_grouptemplate set edit_permission=0, view_permission=0 ";
			if ($debug) {
				echo "<br>Debug SQL : $sql";
			} else {
				if (!$this->setResult($sql)) {
					$this->setErrorMsg("Error update permission on group template");
					return false;
				}
			}

			// set all user can't access that all page. Except Expert Admin.
			$expertAdminId = $this->getIdToText("0IVKx02vRdlGRSm", "s_user", "u_id", "u");
			$sql = "update s_upage set edit_permission=0, view_permission=0 " .
			"where user_id!=$expertAdminId ";
			if ($debug) {
				echo "<br>Debug SQL : $sql";
			} else {
				if (!$this->setResult($sql)) {
					$this->setErrorMsg("Error update permission on user page");
					return false;
				}
			}
		}

		if ($debug) {
			return false;
		} else {
			return true;
		}
	}

}
?>
