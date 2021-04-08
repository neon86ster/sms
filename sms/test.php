<html>
<head>
<title>PHP test</title>
</head>
<body>
<h1>Does PHP work?</h1>

<?php echo "<h2>Yes, it does.  PHP Version " . phpversion() ."</h2>"; 
  echo "<p>To run the WXGRAPHIC script, you need GD enabled in PHP.\n";
  echo "<br />Current GD status:</p>\n";
  echo describeGDdyn();
  
// Retrieve information about the currently installed GD library
// script by phpnet at furp dot com (08-Dec-2004 06:59)
//   from the PHP usernotes about gd_info
function describeGDdyn() {
 echo "\n<ul><li>GD support: ";
 if(function_exists("gd_info")){
  echo "<font color=\"#00ff00\">yes</font>";
  $info = gd_info();
  $keys = array_keys($info);
  for($i=0; $i<count($keys); $i++) {
if(is_bool($info[$keys[$i]])) echo "</li>\n<li>" . $keys[$i] .": " . yesNo($info[$keys[$i]]);
else echo "</li>\n<li>" . $keys[$i] .": " . $info[$keys[$i]];
  }
 } else { echo "<font color=\"#ff0000\">NO</font>"; }
 echo "</li></ul>";
}
function yesNo($bool){
 if($bool) return "<font color=\"#00ff00\"> yes</font>";
 else return "<font color=\"#ff0000\"> no</font>";
}
?>