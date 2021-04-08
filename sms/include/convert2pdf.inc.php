<?php
/*
 * Created on Feb 24, 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
define('HTML2FPDF_VERSION','3.0(beta)');
require('html2pdf/html2pdf205.php');
require('html2pdf/html2pdf203.php');
require('html2pdf/html2pdf206.php');
class convert2pdf{
	var $pdf;
	var $pdf_sr;
	var $pdf_graph;
	function convert2pdf($sr=false,$graph=false,$land=false){
		if($sr){
			//##### For initial convert html to sale receipt pdf report ##########//
			//echo "<br>Initail for sale recipt";
			$this->pdf_sr=new HTML2FPDF_SR('P','mm','A4');
			$this->pdf_sr->AddFont('BrowalliaNew','','browa.php');
			$this->pdf_sr->AddFont('BrowalliaNew','B','browab.php');
			$this->pdf_sr->AddFont('BrowalliaNew','I','browai.php');
			$this->pdf_sr->AddFont('BrowalliaNew','IB','browaz.php');
			$this->pdf_sr->lMargin=10;
			$this->pdf_sr->rMargin=30;
			$this->pdf_sr->fFamily="BrowalliaNew";
			$this->pdf_sr->fStyle="";
			$this->pdf_sr->fSize=12;
			//$this->pdf_sr->SetFont($pdf->fFamily,$pdf->fStyle,$pdf->fSize); 
			//##### End initial convert html to sale receipt pdf report ##########//
		}else if($graph){
			$this->pdf_graph=new HTML2FPDF_GRAPH('P','mm','A4');
			$this->pdf_graph->AddFont('BrowalliaNew','','browa.php');
			$this->pdf_graph->AddFont('BrowalliaNew','B','browab.php');
			$this->pdf_graph->AddFont('BrowalliaNew','I','browai.php');
			$this->pdf_graph->AddFont('BrowalliaNew','IB','browaz.php');
			$this->pdf_graph->lMargin=10;
			$this->pdf_graph->rMargin=30;
			$this->pdf_graph->fFamily="BrowalliaNew";
			$this->pdf_graph->fStyle="";
			$this->pdf_graph->fSize=12;
			//$this->pdf_graph->SetFont($pdf->fFamily,$pdf->fStyle,$pdf->fSize); 
		}else if($land){
			//######## For initial convert html to Land pdf report ###################//
			$this->pdf=new HTML2FPDF('L','mm','A4');
			$this->pdf->AddFont('BrowalliaNew','','browa.php');
			$this->pdf->AddFont('BrowalliaNew','B','browab.php');
			$this->pdf->AddFont('BrowalliaNew','I','browai.php');
			$this->pdf->AddFont('BrowalliaNew','IB','browaz.php');
			$this->pdf->lMargin=10;
			$this->pdf->rMargin=30;
			$this->pdf->fFamily="BrowalliaNew";
			$this->pdf->fStyle="";
			$this->pdf->fSize=10;
			//$this->pdf->SetFont($pdf->fFamily,$pdf->fStyle,$pdf->fSize); 
			//######## End initial convert to normal pdf report ##################//	
		}else{
			//######## For initial convert html to normal pdf report ###################//
			$this->pdf=new HTML2FPDF('P','mm','A4');
			$this->pdf->AddFont('BrowalliaNew','','browa.php');
			$this->pdf->AddFont('BrowalliaNew','B','browab.php');
			$this->pdf->AddFont('BrowalliaNew','I','browai.php');
			$this->pdf->AddFont('BrowalliaNew','IB','browaz.php');
			$this->pdf->lMargin=10;
			$this->pdf->rMargin=30;
			$this->pdf->fFamily="BrowalliaNew";
			$this->pdf->fStyle="";
			$this->pdf->fSize=10;
			//$this->pdf->SetFont($pdf->fFamily,$pdf->fStyle,$pdf->fSize); 
			//######## End initial convert to normal pdf report ##################//	
		}
	}
	//############ Function for normal report ##############///
	function convertFromUrl($url){
		$strContent = $this->pdf->get_web_page($url);
		//$this->pdf->AddPage();
		$this->pdf->writeHTML($strContent['content']);
		$this->pdf->Output();
		die();
	}
	function convertFromFile($path){
		$fp = fopen($path,"r");
		$strContent = fread($fp, filesize($path));
		fclose($fp);
		//echo $strContent;
		//$this->pdf->AddPage();
		$this->pdf->writeHTML($strContent);
		$this->pdf->Output();
	}
	//############ End function for normal report ##############///
	
	//########## Function for sale receipt report ##############///
	function convertSRFromUrl($url){
		
		$strContent = $this->pdf_sr->get_web_page($url);
		$this->pdf_sr->writeHTML($strContent['content']);
		$this->pdf_sr->Output();
	}
	function convertSRFromFile($path){
		//echo "<br>Convert sale recipt from file";
		$fp = fopen($path,"r");
		$strContent = fread($fp, filesize($path));
		fclose($fp);
		
		//echo $strContent;
		//$this->pdf->AddPage();
		$this->pdf_sr->writeHTML($strContent);
		$this->pdf_sr->Output();
	}
	//########## End function for sale receipt report ##############///
	
	//########## Function for graph report ##############///
	function convertGraphFromUrl($url){
		
		$strContent = $this->pdf_graph->get_web_page($url);
		$this->pdf_graph->writeHTML($strContent['content']);
		$this->pdf_graph->Output();
	}
	function convertGraphFromFile($path){
		//echo "<br>Convert sale recipt from file";
		$fp = fopen($path,"r");
		$strContent = fread($fp, filesize($path));
		fclose($fp);
		
		//echo $strContent;
		//$this->pdf->AddPage();
		$this->pdf_graph->writeHTML($strContent);
		$this->pdf_graph->Output();
	}
	//########## End function for graph report ##############///
}
	
	 
 
?>
