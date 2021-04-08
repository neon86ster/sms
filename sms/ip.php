
<?php
// IP Address & Environment Assessment
// OCT-07-09 m

//GLOBALS OFF WORK ROUND

if (!ini_get('register_globals')) {
$reg_globals = array($_POST, $_GET, $_FILES, $_ENV, $_SERVER, $_COOKIE);
if (isset($_SESSION)) {
array_unshift($reg_globals, $_SESSION);
}
foreach ($reg_globals as $reg_global) {
extract($reg_global, EXTR_SKIP);
}
}

//FIND THE VISITORS IP INFO
    
         $ip = $_SERVER['REMOTE_ADDR'];
		 $port = $_SERVER['REMOTE_PORT']; 
		 $header = $_SERVER['HTTP_USER_AGENT'];
 
		 $host1 = $_SERVER['REMOTE_HOST'];
		 $host2= gethostbyaddr  ( $ip  );
		 $ident=$_SERVER['REMOTE_IDENT'];
		 $ret = apache_getenv("SERVER_ADDR");
			 
		$host=$_SERVER['REMOTE_ADDR'];
		$ident=$_SERVER['REMOTE_IDENT'];
		$auth=$_SERVER['REMOTE_USER'];
		$timeStamp=date("d/M/Y:H:i:s O");
		$reqType=$_SERVER['REQUEST_METHOD'];
		$servProtocol=$_SERVER['SERVER_PROTOCOL'];
		$statusCode="200";

		// Create CLF formatted string
		$clfString=$host." ".$ident." ".$auth." [".$timeStamp."] \"".$reqType." /".$fileName." ".$servProtocol."\" ".$statusCode." ".$fileSize."\r\n";

//DISPLAY THE VISITORS IP INFO
echo "<b>IP Address & Environment Information</b>";
echo "<p>";
echo "Your current IP address is : <b> $ip</b><p> ";
echo "Your current hostname or rDNS entry is : <b> $host2</b><p> ";
echo "You are communicating with the web server on port : <b> $port</b><p>";
echo "Your header information is : <p> <ul><i> $header</i></ul><p>";
echo "Your user name is : <b> $ident </b>"; if ($ident==""){ echo "n/a";}
echo "<p>Remote host : <b> $host1</b>"; if ($host1==""){ echo "n/a";}
echo "<p>You are currently accessing IP : <b> $ret</b><br>";
echo "<p>You loaded this page at exactly : <b> $timeStamp</b><p>";
echo "Your custom log file is : <b> $clfString </b><br>";

?>

