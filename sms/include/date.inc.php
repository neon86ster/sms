<?php
require_once("cms.inc.php");
/*
 * File name : cms.inc.php
 * Description : Class file for convert date string from mask
 * Author : Mariusz Stankiewicz http://prettymad.net
 * Create date : 08-Jan-2009
 * Modified : natt@chiangmaioasis.com
 */   
 class convertdate extends cms {
 	
 	/***
		$from_mask can be:
			j	date, non leading zeros
			d	date, with leading zeros
			l	week, full textual representation of the day of the week
			n	month, non leading zeros
			m	month, with leading zeros
			M	month, short textual representation of a month, three letters
			F	month, full textual representation of a month, such as January or March
			y	year, A two digit represenation of a year
			Y	year, A full numeric represenation of a year, 4 digits
	*/
	
	/*
	 * Convert date string to mask format
	 * @param - date string
	 * @param - date string mask format
	 * @param - date string mask format that want to convert
	 * @param - set to true if want to return php Date value
	 * @modified - add this function in 08-Jan-2009
	 */
	function convertdate($string='', $from_mask='', $to_mask='', $return_unix=false){
		
		//if($string==''&&$from_mask==''&&$to_mask==''&&$return_unix==false){return false;}
		if($string=='' || $from_mask=='' || $to_mask==''){return false;} // For debug  Undefined index:  offset:  1,2 --> by : Ruck 11-05-2009
		
		$from_mask = str_replace(', ',",",$from_mask);
		$string = str_replace(', ',",",$string);
		
		if($from_mask!="Ymd"){
			$m1 = ""; $m2 = ""; $m3 = "";
			$s1 = ""; $s2 = ""; $s3 = "";
			list($m1, $m2, $m3) = split('[-./.,. ]', $from_mask);
			list($s1, $s2, $s3) = split('[-./.,. ]', $string);
			
			$smonthNames = array("Jan"=>1,"Feb"=>2,"Mar"=>3,"Apr"=>4,
	    						"May"=>5,"Jun"=>6,"Jul"=>7,"Aug"=>8,
	    						"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);
			$fmonthNames = array("January"=>1,"February"=>2,"March"=>3,"April"=>4,
	    						"May"=>5,"June"=>6,"July"=>7,"August"=>8,
	    						"September"=>9,"October"=>10,"November"=>11,"December"=>12);
	    	$month = "0";
	    	$day = "0";
	    	$year = "0";
			//echo "$string<br/>$m1 $m2 $m3<br/>$s1 $s2 $s3<br/>";
			switch($m1) {
				case 'j' : $day = $s1; break; case 'd' : $day = $s1; break;
				case 'n' : $month = $s1; break; case 'm' : $month = $s1; break; 
				case 'M' : $month = $smonthNames[$s1]; break; case 'F' : $month = $fmonthNames[$s1]; break;
				case 'y' : $year = '20'.$s1; break; case 'Y' : $year = $s1; break;
			}
			switch($m2) {
				case 'j' : $day = $s2; break; case 'd' : $day = $s2; break;
				case 'n' : $month = $s2; break; case 'm' : $month = $s2; break; 
				case 'M' : $month = $smonthNames[$s2]; break; case 'F' : $month = $fmonthNames[$s2]; break;
				case 'y' : $year = '20'.$s2; break; case 'Y' : $year = $s2; break;
			}
			switch($m3) {
				case 'j' : $day = $s3; break; case 'd' : $day = $s3; break;
				case 'n' : $month = $s3; break; case 'm' : $month = $s3; break; 
				case 'M' : $month = $smonthNames[$s3]; break; case 'F' : $month = $fmonthNames[$s3]; break;
				case 'y' : $year = '20'.$s3; break; case 'Y' : $year = $s3; break;
			}
		}
		//specific case for database "Ymd" for example: "20090101" (1-Jan-2009)
		if($from_mask=="Ymd"){
			$day=substr($string,6,2);$month=substr($string,4,2);$year=substr($string,0,4);
		}
		//echo "$day $month $year<br/>";
		$unix_time = mktime(0, 0, 0, (int)$month, (int)$day, (int)$year);
		if($return_unix)
			return $unix_time;

		return date($to_mask, $unix_time);
	}

	/***
		$from_mask can be:
			s	seconds, with leading zeros
			i	minutes, with leading zeros
			h	12-hour format of an hour with leading zeros
			H	24-hour format of an hour with leading zeros
			g	12-hour format of an hour non leading zeros
			G	24-hour format of an hour non leading zeros
	*/
	
	/*
	 * Convert time string to mask format
	 * @param - time string
	 * @param - time string mask format
	 * @param - time string mask format that want to convert
	 * @param - set to true if want to return php Date value
	 * @modified - add this function in 08-Jan-2009
	 */	
	function converttime($string, $from_mask, $to_mask='', $return_unix=false){
		list($m1, $m2, $m3) = split('[/.-.,. .:]', $from_mask);
		list($s1, $s2, $s3) = split('[/.-.,. .:]', $string);
		
		$hours = "0";
    	$minutes = "0";
    	$seconds = "0";
		switch($m1) {
			case 'i' : $minutes = $s1; break; case 's' : $seconds = $s1; break;
			case 'h' : $hours = $s1; break; case 'H' : $hours = $s1; break;
			case 'g' : $hours = $s1; break; case 'G' : $hours = $s1; break;
		}
		switch($m2) {
			case 'i' : $minutes = $s2; break; case 's' : $seconds = $s2; break;
			case 'h' : $hours = $s2; break; case 'H' : $hours = $s2; break;
			case 'g' : $hours = $s2; break; case 'G' : $hours = $s2; break;
		}
		switch($m3) {
			case 'i' : $minutes = $s3; break; case 's' : $seconds = $s3; break;
			case 'h' : $hours = $s3; break; case 'H' : $hours = $s3; break;
			case 'g' : $hours = $s3; break; case 'G' : $hours = $s3; break;
		}
		
		$unix_time = mktime((int)$hours, (int)$minutes, (int)$seconds, 0, 0, 0);
		if($return_unix)
			return $unix_time;

		return date($to_mask, $unix_time);
	}
	
	/*
	 * function for count day between 2 date
	 * return the number of days between the two dates passed in
	 * @param - time string patern: "Ymd"
	 * @param - time string patern: "Ymd"
	 * @modified - add this function in 11-Feb-2009
	 */
		function countdays($a, $b){
			// First we need to break these dates into their constituent parts:
			$aday=substr($a,6,2);$amonth=substr($a,4,2);$ayear=substr($a,0,4);
			$bday=substr($b,6,2);$bmonth=substr($b,4,2);$byear=substr($b,0,4);
			 
			// Now recreate these timestamps, based upon noon on each day
			// The specific time doesn't matter but it must be the same each day
			$a_new = mktime( 12, 0, 0, (int)$amonth, (int)$aday, (int)$ayear);
			$b_new = mktime( 12, 0, 0, (int)$bmonth, (int)$bday, (int)$byear);
			 
			// Subtract these two numbers and divide by the number of seconds in a
			// day. Round the result since crossing over a daylight savings time
			// barrier will cause this time to be off by an hour or two.
			return ceil( abs( $a_new - $b_new ) / 86400 );	// 86400 = 24 * 60 * 60 >> mod second to date 
		}
		
	/*
	 * Plus day from date patern: "Ymd"
	 * @param - time string patern: "Ymd"
	 * @param - day
	 * @param - plus date
	 * @param - time string mask format that want to convert
	 * @modified - add this function in 11-Feb-2009
	 */
		function plusday($date,$plusday=0,$to_mask="Ymd"){
			$day=substr($date,6,2);$month=substr($date,4,2);$year=substr($date,0,4);
			//if($plusday<0){$plusday=13;}
			$unix_time = mktime(0, 0, 0, (int)$month, (int)$day+$plusday, (int)$year);
			return date($to_mask, $unix_time);
		}
		
	/*
	 * Plus month from date patern: "Ymd"
	 * @param - time string patern: "Ymd"
	 * @param - plus month
	 * @param - time string mask format that want to convert
	 * @modified - add this function in 11-Feb-2009
	 */
		function plusmonth($date,$plusmonth=0,$day=32,$to_mask="Ymd"){
			$month=substr($date,4,2);$year=substr($date,0,4);
			if($day!=32){$day=$day;}else{$day=substr($date,6,2);}
			$unix_time = mktime(0, 0, 0, (int)$month+$plusmonth, (int)$day, (int)$year);
			return date($to_mask, $unix_time);
		}
		
	/*
	 * Plus year from date patern: "Ymd"
	 * @param - time string patern: "Ymd"
	 * @param - plus year
	 * @param - time string mask format that want to convert
	 * @modified - add this function in 11-Feb-2009
	 */
		function plusyear($date,$plusyear=0,$day=32,$month=13,$to_mask="Ymd"){
			if($day!=32){$day=$day;}else{$day=substr($date,6,2);}
			if($month!=13){$month=$month;}else{$month=substr($date,4,2);}
			$year=substr($date,0,4);
			$unix_time = mktime(0, 0, 0, (int)$month, (int)$day, (int)$year+$plusyear);
			return date($to_mask, $unix_time);
		}
		
	/*
	 * Plus year from date patern: "Ymd"
	 * @param - time string patern: "Ymd"
	 * @param - plus year
	 * @param - time string mask format that want to convert
	 * @modified - add this function in 11-Feb-2009
	 */
		function plusdate($date,$plusday=0,$plusmonth=0,$plusyear=0,$day=32,$month=13,$to_mask="Ymd"){
			if($day!=32){$day=$day;}else{$day=substr($date,6,2);}
			if($month!=13){$month=$month;}else{$month=substr($date,4,2);}
			$year=substr($date,0,4);
			$unix_time = mktime(0, 0, 0, (int)$month+$plusmonth, (int)$day+$plusday, (int)$year+$plusyear);
			//echo date($date)." ";
			return date($to_mask, $unix_time);
		}
		
	/*
	 * function for count month between 2 date
	 * return the number of days between the two dates passed in
	 * @param - time string patern: "Ymd"
	 * @param - time string patern: "Ymd"
	 * @modified - add this function in 11-Feb-2009
	 */
		function countmonths($a, $b){
			$aday=substr($a,6,2);$amonth=substr($a,4,2);$ayear=substr($a,0,4);
			$bday=substr($b,6,2);$bmonth=substr($b,4,2);$byear=substr($b,0,4);
			 
			$a_new = mktime( 0, 0, 0, (int)$amonth, (int)$aday, (int)$ayear);
			$b_new = mktime( 0, 0, 0, (int)$bmonth, (int)$bday, (int)$byear);
			 
			 return abs($this->total_months($a_new) - $this->total_months($b_new))+1;
		}
		
	/*
	 * Calculate total month
	 */
		function total_months($a){
         $year = date("Y", $a);
         $ymonths= $year * 12;
         
         $month = date("n", $a);
         $total = $ymonths + $month;
                  
         return $total;
      }
		
	/*
	 * convert date time to unix timestamp from database format
	 * this function return 2 Unix timestamp for compare in some report
	 */
		function dbtimetostr($date1,$date2) {
			$timetmp = array();
			list($date,$time) = explode(" ",$date1);
			list($year,$mon,$day) = explode("-",$date);
			list($hour,$min,$sec) = explode(":",$time);
			$timetmp[0] = mktime((int)$hour,(int)$min,(int)$sec,(int)$mon,(int)$day,(int)$year);
			list($date,$time) = explode(" ",$date2);
			list($year,$mon,$day) = explode("-",$date);
			list($hour,$min,$sec) = explode(":",$time);
			$timetmp[1] = mktime((int)$hour,(int)$min,(int)$sec,(int)$mon,(int)$day,(int)$year);
			return $timetmp;
		}
		
	/*
	 * convert time zone from system config.		
	 * @param - date in format Y-m-d
	 * @param - time in format H:i:s
	 */
	 	function timezonefilter($date,$time,$ldateformat){
	 		list($year,$month,$day) = explode("-",$date);
	 		list($hours,$minutes,$seconds) = explode(":",$time);
	 		if(!isset($_SESSION["__gmt"])){$_SESSION["__gmt"]=0;}
	 		$gmt = isset($_SESSION["__gmt"])?$_SESSION["__gmt"]:$_GET["gmt"];
	 		list($hr,$min) = explode(".",number_format($gmt,2,".",","));
	 		
	 		$hours += $hr-$_SESSION["global_timezone"];
	 		$minutes += $min;
	 		
	 		$unix_time = mktime((int)$hours, (int)$minutes, (int)$seconds, (int)$month, (int)$day, (int)$year);
			return date($ldateformat, $unix_time);
	 	}
	 
	 /* conver time zone to global config (Company timezone) */
	 	function timezone_global($date,$time,$ldateformat){
	 		list($year,$month,$day) = explode("-",$date);
	 		list($hours,$minutes,$seconds) = explode(":",$time);
	 		
	 		$gmt = isset($_SESSION["__gmt_company"])?$_SESSION["__gmt_company"]:$_GET["gmt"];
	 			 		
	 		list($hr,$min) = explode(".",number_format($gmt,2,".",","));
	 		$hours += $hr-$_SESSION["global_timezone"];
	 		$minutes += $min;
	 		
	 		$unix_time = mktime((int)$hours, (int)$minutes, (int)$seconds, (int)$month, (int)$day, (int)$year);
			return date($ldateformat, $unix_time);
	 	}
	 	
	 /* conver time zone to global config (Company timezone) */
	 	//function timezone_depend_branch($date=false,$time=false,$ldateformat=false,$branch=false){
	 	function timezone_depend_branch($date,$time,$ldateformat,$branch=false){
	 		$hours="";
	 		$minutes="";
	 		$seconds="";
	 		$unix_time="";
	 		$year="";
	 		$month="";
	 		$day="";
	 		
	 		if($date){
	 		list($year,$month,$day) = explode("-",$date);
	 		}
	 		if($time){
	 		list($hours,$minutes,$seconds) = explode(":",$time);
	 		}
	 		
	 		$sql = "select l_timezone.gmt, l_timezone.timezone_id, bl_branchinfo.timezone from l_timezone, bl_branchinfo where l_timezone.timezone_id = bl_branchinfo.timezone and bl_branchinfo.branch_id=$branch";
			$chkrs = $this->getResult($sql);	
					
				$gmt = $chkrs[0]["gmt"]?$chkrs[0]["gmt"]:$chkrs[0]["gmt"];			
	 			 			
	 		list($hr,$min) = explode(".",number_format($gmt,2,".",","));
	 		$hours += $hr-$_SESSION["global_timezone"];
	 		$minutes += $min;
	 		
	 		$unix_time = mktime((int)$hours, (int)$minutes, (int)$seconds, (int)$month, (int)$day, (int)$year);
			return date($ldateformat, $unix_time);
	 	}
	 	
 }
 
?>
