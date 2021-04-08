<?php
/*
 * Created on May 27, 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 print_r($_SERVER);
 require('convert2pdf.inc.php');
 $pdf = new convert2pdf(false,true);
 $pdf->convertGraphFromFile("csgReport.htm");
?>
