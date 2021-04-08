<?
mysql_connect("localhost", "root", "123456") or die(mysql_error());
echo "Connected to MySQL<br />";

mysql_select_db("sample") or die(mysql_error());
echo "Connected to Database<br />";


$result = mysql_query("SELECT * FROM s_userpermission order by user_id")
or die(mysql_error());  

while($row=mysql_fetch_array( $result ))
{

echo sdfas;
echo "<br />id: ".$row['user_id'];

mysql_query("INSERT INTO `sample`.`s_upage` (`u_pageid`,`user_id`, `group_id`, `page_id`, `parent_id`, `menu_level`, `page_name`, `url`, `edit_permission`, `view_permission`, `page_priority`) VALUES ('', ".$row["user_id"]." , ".$row["group_id"]." , '110', '5', '1','Hotel Day Spa', 'report/hotel/index.php', '0','0' , '81')")
or die(mysql_error()); 

}

echo "Data insert!<br />";

$result = mysql_query("SELECT * FROM s_userpermission order by user_id")
or die(mysql_error());  
while($row=mysql_fetch_array( $result ))
{

echo sdfas;
echo "<br />id: ".$row['user_id'];

mysql_query("INSERT INTO `sample`.`s_upage` (`u_pageid`,`user_id`, `group_id`, `page_id`, `parent_id`, `menu_level`, `page_name`, `url`, `edit_permission`, `view_permission`, `page_priority`) VALUES ('', ".$row["user_id"]." , ".$row["group_id"]." , '111', '110', '2','Daily Report', 'report/hotel/daily/index.php', '0','0' , '81')")
or die(mysql_error()); 

}

echo "Data insert!<br />";
$result = mysql_query("SELECT * FROM s_userpermission order by user_id")
or die(mysql_error());  
while($row=mysql_fetch_array( $result ))
{

echo sdfas;
echo "<br />id: ".$row['user_id'];

mysql_query("INSERT INTO `sample`.`s_upage` (`u_pageid`,`user_id`, `group_id`, `page_id`, `parent_id`, `menu_level`, `page_name`, `url`, `edit_permission`, `view_permission`, `page_priority`) VALUES ('', ".$row["user_id"]." , ".$row["group_id"]." , '112', '110', '2','Monthly Report', 'report/hotel/monthly/index.php', '0','0' , '81')")
or die(mysql_error()); 

}

echo "Data insert!<br />";
$result = mysql_query("SELECT * FROM s_userpermission order by user_id")
or die(mysql_error());  
while($row=mysql_fetch_array( $result ))
{

echo sdfas;
echo "<br />id: ".$row['user_id'];

mysql_query("INSERT INTO `sample`.`s_upage` (`u_pageid`,`user_id`, `group_id`, `page_id`, `parent_id`, `menu_level`, `page_name`, `url`, `edit_permission`, `view_permission`, `page_priority`) VALUES ('', ".$row["user_id"]." , ".$row["group_id"]." , '113', '4', '1','API', 'spamg/api/index.php', '0','0' , '46')")
or die(mysql_error()); 

}

echo "Data insert!<br />";
$result = mysql_query("SELECT * FROM s_userpermission order by user_id")
or die(mysql_error());  
while($row=mysql_fetch_array( $result ))
{

echo sdfas;
echo "<br />id: ".$row['user_id'];

mysql_query("INSERT INTO `sample`.`s_upage` (`u_pageid`,`user_id`, `group_id`, `page_id`, `parent_id`, `menu_level`, `page_name`, `url`, `edit_permission`, `view_permission`, `page_priority`) VALUES ('', ".$row["user_id"]." , ".$row["group_id"]." , '114', '113', '2','Export Data', 'spamg/api/export/index.php', '0','0' , '46')")
or die(mysql_error()); 

}

echo "Data insert!<br />";
$result = mysql_query("SELECT * FROM s_userpermission order by user_id")
or die(mysql_error());  
while($row=mysql_fetch_array( $result ))
{

echo sdfas;
echo "<br />id: ".$row['user_id'];

mysql_query("INSERT INTO `sample`.`s_upage` (`u_pageid`,`user_id`, `group_id`, `page_id`, `parent_id`, `menu_level`, `page_name`, `url`, `edit_permission`, `view_permission`, `page_priority`) VALUES ('', ".$row["user_id"]." , ".$row["group_id"]." , '115', '113', '2','Interface', 'spamg/api/interface/index.php', '0','0' , '46')")
or die(mysql_error()); 

}

echo "Data insert!<br />";
$result = mysql_query("SELECT * FROM s_userpermission order by user_id")
or die(mysql_error());  
while($row=mysql_fetch_array( $result ))
{

echo sdfas;
echo "<br />id: ".$row['user_id'];

mysql_query("INSERT INTO `sample`.`s_upage` (`u_pageid`,`user_id`, `group_id`, `page_id`, `parent_id`, `menu_level`, `page_name`, `url`, `edit_permission`, `view_permission`, `page_priority`) VALUES ('', ".$row["user_id"]." , ".$row["group_id"]." , '116', '25', '3','Account API Interface', 'spamg/updatefield/accc/index.php', '0','0' , '24')")
or die(mysql_error()); 

}

echo "Data insert!<br />";
$result = mysql_query("SELECT * FROM s_userpermission order by user_id")
or die(mysql_error());  
while($row=mysql_fetch_array( $result ))
{

echo sdfas;
echo "<br />id: ".$row['user_id'];

mysql_query("INSERT INTO `sample`.`s_upage` (`u_pageid`,`user_id`, `group_id`, `page_id`, `parent_id`, `menu_level`, `page_name`, `url`, `edit_permission`, `view_permission`, `page_priority`) VALUES ('', ".$row["user_id"]." , ".$row["group_id"]." , '117', '25', '3','Template', 'spamg/updatefield/temp/index.php', '0','0' , '24')")
or die(mysql_error()); 

}

echo "Data insert!<br />";

?>