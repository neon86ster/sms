<?php
/*
 * Created on May 27, 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 require('convert2pdf2.inc.php');
 $pdf = new convert2pdf();
 $pdf->convertFromFile("report.html");
?>
