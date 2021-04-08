<?

mysql_connect("localhost", "root", "123456") or die(mysql_error());
echo "Connected to MySQL<br />";

mysql_select_db("sample") or die(mysql_error());
echo "Connected to Database<br />";

//mysql_query("INSERT INTO `sample`.`s_pagename` (`page_id`, `page_name`, `url`, `index`, `page_parent_id`, `page_priority`, `description`, `popup`, `active`, `has_child`, `page_refer`) VALUES ('', 'Hotel Day Spa', 'report/hotel/index.php', '2', '5', '81', '', '', '1', '1', '0')")
//or die(mysql_error()); 
//echo "Data Inserted!<br />";

$result = mysql_query("SELECT * FROM s_group order by group_id")
or die(mysql_error());  

while($row=mysql_fetch_array( $result ))
{

echo "<br/>".$g;
mysql_query("INSERT INTO `sample`.`s_grouptemplate` (`gpage_id`, `group_id`, `page_id`, `parent_id`, `menu_level`, `page_name`, `url`, `edit_permission`, `view_permission`, `page_priority`) VALUES ('', ".$row["group_id"].", '117', '25', '3', 'template', 'spamg/updatefield/temp/index.php', '0' , '0', '24')")
or die(mysql_error()); 
}

echo "Data insert s_grouptemplate!<br />";

?>