<?
mysql_connect("localhost", "root", "123456") or die(mysql_error());
echo "Connected to MySQL<br />";

mysql_select_db("sample") or die(mysql_error());
echo "Connected to Database<br />";

mysql_query("INSERT INTO `sample`.`s_pagename` (`page_id`, `page_name`, `url`, `index`, `page_parent_id`, `page_priority`, `description`, `popup`, `active`, `has_child`, `page_refer`) VALUES ('', 'Template', 'spamg/updatefield/temp/index.php', '3', '25', '24', '', '', '1', '0', '0')")
or die(mysql_error()); 
echo "Data Inserted!<br />";
?>