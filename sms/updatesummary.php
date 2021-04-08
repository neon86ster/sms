<?php
/*
 * Created on Sep 23, 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 include("include.php");

  //$sql = "select * from s_pagename order by page_id";
  //$rs = $object->getResult($sql);
  //$sql = "select u_id from s_user order by u_id";
  //$userRs = $object->getResult($sql);
  $sql = "select group_id from s_group order by group_id";
  $groupRs = $object->getResult($sql);

  for($i=0;$i<$groupRs["rows"];$i++){
  	
  	$sql = "insert into s_grouptemplate (gpage_id, group_id, page_id, parent_id, menu_level," .
  			"page_name, url, edit_permission, view_permission, page_priority) " .
  			"values ('','".$groupRs[$i]["group_id"]."','105','5','1','Summary Report','report/smr/index.php','0','0','81')";
  	//echo $sql."<br>";
  	//$object->setResult($sql);
  }
  
  $sql = "select s_userpermission.user_id, s_userpermission.group_id from s_userpermission order by user_id";
  $userRs = $object->getResult($sql);
  
  for($i=0;$i<$userRs["rows"];$i++){
  	//echo $userRs[$i]["user_id"]."-";
  	//echo $userRs[$i]["group_id"]."<br>";
  	$sql = "insert into s_upage (u_pageid, user_id, group_id, page_id, parent_id, menu_level, page_name" .
  			", url, edit_permission, view_permission, page_priority) " .
  			"values ('','".$userRs[$i]["user_id"]."','".$userRs[$i]["group_id"]."'," .
  					"'105','5','1','Summary Report','report/smr/index.php','0','0','81')";
  	//echo $sql."<br>";
  	//$object->setResult($sql);
  }
  
  
  
  
  
  
  
  	//$sql = "select page_id from s_grouptemplate where group_id=".$groupRs[$i]["group_id"];
  	//$templateRs = $object->getResult($sql);
  	
  	// Keep all page of each grouptemplate.
  	//$oldPageArray = array();
  	//for($j=0;$j<$templateRs["rows"];$j++){
  	//	$oldPageArray[$j] = $templateRs[$j]["page_id"];	
  //	}
  	// This loop for find page difference between.
  	//for($j=0;$j<$rs["rows"];$j++){
  		//if(!in_array($rs[$j]["page_id"],$oldPageArray,true)){
  			// If this page not in table s_grouptemplate keep value for insert into table s_grouptemplate.
	  	//	$values[$countValue] = "('".$groupRs[$i]["group_id"]."',".$rs[$j]["page_id"].",'".$rs[$j]["page_name"]."',
		//							'".$rs[$j]["url"]."','".$rs[$j]["index"]."','".$rs[$j]["page_parent_id"]."'," .
		//							"'0','0','".$rs[$j]["page_priority"]."')";
		//	$countValue++;
  		//}else{
  			// If this page is in table s_grouptemplate already then update page priority like table s_pagename.
  		//	$update = "update s_grouptemplate set page_priority='".$rs[$j]["page_priority"]."' " .
  		//			"where page_id='".$rs[$j]["page_id"]."' and group_id='".$groupRs[$i]["group_id"]."'";
  		//	if(!$object->setResult($update)){
  		//		echo "<br>Error : $update";
  		//	}
  		//}
  	//}
  
  //}
  //$insertTemplate .= implode(",",$values);
 /*  
  $values = array();//Keep all value for insert into table s_upage.
  $countValue = 0;

  for($i=0;$i<$userRs["rows"];$i++){
  	$sql = "select page_id,group_id from s_upage where user_id=".$userRs[$i]["u_id"];
  	$upageRs = $object->getResult($sql);
  	
  	// Keep all page of each user.
  	$oldPageArray = array();
  	for($j=0;$j<$upageRs["rows"];$j++){
  		$oldPageArray[$j] = $upageRs[$j]["page_id"];	
  	}
  	for($j=0;$j<$rs["rows"];$j++){
  		if(!in_array($rs[$j]["page_id"],$oldPageArray,true)){
  			// If this page not in table s_upage keep value for insert into table s_upage.
	  		$values[$countValue] = "('".$userRs[$i]["u_id"]."',".$rs[$j]["page_id"].",'".$rs[$j]["page_name"]."',
									'".$rs[$j]["url"]."','".$rs[$j]["index"]."','".$rs[$j]["page_parent_id"]."'," .
									"'".$upageRs[$j]["group_id"]."','0','0','".$rs[$j]["page_priority"]."')";
			$countValue++;
  		}else{
  			// If this page is in table s_upage already then update page priority like table s_pagename.
  			$update = "update s_upage set page_priority='".$rs[$j]["page_priority"]."' " .
  					"where page_id='".$rs[$j]["page_id"]."' and user_id='".$userRs[$i]["u_id"]."'";
  			if(!$object->setResult($update)){
  				echo "<br>Error : $update";
  			}
  		}
  	}
  }
  $insertUpage .= implode(",",$values);
  if(!$object->setResult($insertTemplate)){
  	echo "<br> Error : $insertTemplate";
  }
  if(!$object->setResult($insertUpage)){
  	echo "<br> Error : $insertUpage";
  }
  
  // ----------- Update table s_userpermission ------------
 $sql = "select user_id from s_userpermission";
 $permissionRs = $object->getResult($sql);
 
 $userArray = array();
  for($j=0;$j<$permissionRs["rows"];$j++){
  	$userArray[$j] = $permissionRs[$j]["user_id"];	
  }
  
  $sql = "select s_user.u_id from s_user where u_id not in(".implode(",",$userArray).") ";
  $userRs = $obj->getResult($sql);
  
  $insertUserpermission = "insert into s_userpermission (`user_id`, `group_id` , `cms_update_time` , `appt_viewchk` ,
								`appt_editchk` , `pre_viewdate` , `after_viewdate` , `pre_editdate` , `after_editdate` ,
								`sr_chk` ,`log_viewchk`, `active`) values ";
  
  $values=array();
  for($i=1;$i<=$userRs["rows"];$i++){
  	if($userRs[$i]["u_id"] != $userRs[$i-1]["u_id"]){
  		$values[$i-1] = "( '".$userRs[$i-1]["u_id"]."','0','0'," .
						"'0','0','0','0','0','0'," .
						"'1','1', '1')";
  	}
  }
  
  $insertUserpermission .= implode(",",$values);
  if(!$object->setResult($insertUserpermission)){
  	echo "<br> Error : $insertUserpermission";
  }
  //$update = "delete from s_upage where page_id = 75";
  //$object->setResult($update);
  echo "<br>Finish";
 */
 
?>
