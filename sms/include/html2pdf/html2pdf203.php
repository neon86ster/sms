<?php
/*
 * Modify on Mar 7, 2009 by Ruk
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 *  html2pdf version 2.0.1
 * This version :
 *  - extends class from fpdf
 * 	- read data from html and write data to pdf
 * 	- change html fomat to array before write to pdf file
 *  - write data on cell not assign position x,y
 *  - enter to new line when text width more than cell width
 *  - can read thai font
 *  - can read file from remote server
 *  - can show bold character of each character set
 *  - can set fill color into cell by tr or td bgcolor
 * Modify on Mar 11, 2009 by Ruk
 *  - debug lenght of row over the table width
 *  - set color to each string in same cell
 * Modifly on Mar 12, 2009 by Ruk 
 *  - debug td color at all
 * Modify on Mar 30, 2009 by Ruk
 *  - this version design for only support to print sale receipt bill
 */


 //define('HTML2FPDF_VERSION','3.0(beta)');
 
if (!defined('RELATIVE_PATH')) define('RELATIVE_PATH','');
if (!defined('FPDF_FONTPATH')) define('FPDF_FONTPATH','html2pdf/font/');
require_once(RELATIVE_PATH.'fpdf.php');
require_once(RELATIVE_PATH.'htmltoolkit.php');

class HTML2FPDF_SR extends FPDF{
	var $enabledtags;
	var $tdbegin; //! bool
	var $table=array(); //! array
	var $col; //! int
	var $row; //! int
	var $nowTable;//!int
	var $align; //!string
	
	var $pgWidth;//int
	var $padding;//array
	var $visualBlock=array();//Array
	var $visualX;//Int
	var $visualY;//Int
	var $isBr;//boolean
	var $style=array();//Array
	var $border;//String
	var $tdWidth;//int
	var $trHeigth;//int
	var $rowSpace;//string
	
	/// for set front
	var $fFamily;//string
	var $fStyle;//string
	var $fSize;//int
	var $cPage=0;//int count paage 
	var $sLMargin=0;
	
	/// For show image ///
	
	
	function HTML2FPDF_SR($orientation='P',$unit='mm',$format='sr'){
	//! @desc Constructor
	//! @return An object (a class instance)
	//Call parent constructor
	
		$this->FPDF($orientation,$unit,$format);
		/////// Set defualt font to AngsanaNew /////////////////
		$this->AddFont('AngsanaNew','','angsa.php');
		$this->AddFont('AngsanaNew','B','angsab.php');
		$this->AddFont('AngsanaNew','I','angsai.php');
		$this->AddFont('AngsanaNew','IB','angsaz.php');
		$this->fFamily="AngsanaNew";
		/////// Set defualt font style to no style /////////////
		$this->fStyle="";
		/////// Set defualt font size to 12 ////////////////////
		$this->fSize=12;
		$this->reSetFont();
		$this->DisableTags();
		/////// Set initial page width /////////////////////////
		//$this->SetMargins(0,20,150);
		$this->SetTopMargin(20);
		$this->pgWidth = $this->w - $this->lMargin - $this->rMargin ;
		$this->nowTable=0;
		
	}
	// Page header
	function header()
	{
	   ////////// Not set header for this system ////////////////
	}

	//Page footer
	function footer()
	{
	    //Position at 1.5 cm from bottom
	    //$this->SetY(-15);
	    $this->SetXY(30,-15);
	    //Arial italic 8
	    $this->SetFont($this->fFamily,'',$this->fSize-2);
	    //Page number
	    $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
	}
	
///////////////////
/// HTML parser ///
///////////////////
function writeHTML($html)
{
//! @desc HTML parser
//! @return void
/* $e == content */
	//echo "<br>Write Content";
	$this->sLMargin=$this->lMargin;
  $this->readMetaTags($html);
  $html = AdjustHTML($html,false); //Try to make HTML look more like XHTML
   //Add new supported tags in the DisableTags function
	$html=str_replace('<?','< ',$html); //Fix '<?XML' bug from HTML code generated by MS Word
	$html=strip_tags($html,$this->enabledtags); //remove all unsupported tags, but the ones inside the 'enabledtags' string
  //Explode the string in order to parse the HTML code
	$a=preg_split('/<(.*?)>/ms',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	////////// Count table in page and get colum of each table
	$this->countTable($a);
	
	foreach($a as $i => $e){
		//print_r($this-table);
		//echo "<br>$e";
		if($i%2==0)
		{
			if (strpos($e,"&") !== false) //HTML-ENTITIES decoding
			{
				if (strpos($e,"#") !== false) $e = value_entity_decode($e); // Decode value entities
		        //Avoid crashing the script on PHP 4.0
		        $version = phpversion();
		        $version = str_replace('.','',$version);
		        if ($version >= 430) $e = html_entity_decode($e,ENT_QUOTES,'cp1252'); // changes &nbsp; and the like by their respective char
		        else $e = lesser_entity_decode($e);
		    }
	     	$e = str_replace(chr(160),chr(32),$e); //unify ascii code of spaces (in order to recognize all of them correctly)
			if (strlen($e) == 0) continue;
			if ($this->divrevert) $e = strrev($e);
			if ($this->toupper) $e = strtoupper($e);
			if ($this->tolower) $e = strtolower($e);
		    
		    if($this->tdbegin)
	       	{
	       		/////////////// Keep data to visual block /////////////////
	       		//echo "<br>visual X ".$this->visualX." visual Y : ".$this->visualY." -- Data : $e";
	       		$this->visualBlock[$this->visualX][$this->visualY]["data"]=$e;
	       		if($this->style["bFont"]){
	       			$this->visualBlock[$this->visualX][$this->visualY]["bFont"]=true;
	       		}
		    	///////// Check has enter to new line /////////////////////    		
				if($this->isBr){
				   	$this->visualBlock[$this->visualX][$this->visualY]["br"]++;	
				   	$this->table[$this->nowTable]["tdBr"]++;
				}
			    $this->isBr=false;
	       	}
		}else{
			
			if($e{0}=='/'){
				 $this->CloseTag(strtoupper(substr($e,1)));
			}else{
				/////////////////////// check tag open
			    $regexp = '|=\'(.*?)\'|s'; // eliminate single quotes, if any
		      	$e = preg_replace($regexp,"=\"\$1\"",$e);
		      			$regexp = '| (\\w+?)=([^\\s>"]+)|si'; // changes anykey=anyvalue to anykey="anyvalue" (only do this when this happens inside tags)
		      	$e = preg_replace($regexp," \$1=\"\$2\"",$e);
				//Extract attributes
				$contents=array();
		        preg_match_all('/\\S*=["\'][^"\']*["\']/',$e,$contents);
		        preg_match('/\\S+/',$e,$a2);
		       	///////// Get tag name and change to upper character ////////////
		       	$tag=strtoupper($a2[0]);
		     	$attr=array();
		       	//Ignore content between <table>,<tr> and a <td> tag (this content is usually only a bunch of spaces)
				if (!empty($contents))
				{
		  			foreach($contents[0] as $v)
		  			{
		  				if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
		    			{
		    				//////// Get content name and change to uper character //////////
		    				$attr[strtoupper($a3[1])]=$a3[2];
		     			}
		  			}
				}
				$this->OpenTag($tag,$attr);
			}
		}
	}
}
function OpenTag($tag,$attr)
{
	//! @return void
	// What this gets: < $tag $attr['WIDTH']="90px" > does not get content here </closeTag here>

  	$align = array('left'=>'L','center'=>'C','right'=>'R','top'=>'T','middle'=>'M','bottom'=>'B','justify'=>'J');
	//echo "<br>$tag";
	
	//echo "<br>Tag : $tag";
	//echo "<br>Tag Content : ".$attr["SRC"];
	
	switch($tag){
    
	case 'TABLE': // TABLE-BEGIN
		//echo "<br>Tag : $tag";
		//echo "<br>Tag Content : ".$attr["BORDER"];
		$this->nowTable++;
		$this->align="";
		$this->border="";
		if($attr["BORDER"]){
			$this->table[$this->nowTable]["border"]=1;	
		}else{
			$this->table[$this->nowTable]["border"]=0;
		}
		
    	$this->table[$this->nowTable]["tdWidth"]=array();
    	$this->table[$this->nowTable]["trHeight"]=array();
    	$this->row=0;
    	$this->posX=$this->lMargin;	
		$this->table[$this->nowTable]["width"]=$this->posX;
   	
   		break;
	case 'TR':
		////////// check td bgcolor
		$this->table[$this->nowTable]["trBgColor"][$this->row]=$attr["BGCOLOR"];
		$this->rowSpace="";
		$this->table[$this->nowTable]["trHeight"][$this->row]=$attr["HEIGHT"]/5;
		for($i=1;$i<=$this->nowTable;$i++){
			$this->posX=$this->table[$i]["width"];
		}
		$this->col=0;
		break;
	case 'TD':
		$this->table[$this->nowTable]["tdBr"]=0;
		////// Init visual block
		$this->visualBlock=array();
		$this->visualX=0;
		$this->visualY=0;
		$this->countBr=0;
		
		/////////// Check style of td /////////////
		if($this->table[$this->nowTable]["tdWidth"][$this->col]==null){
			if($attr['WIDTH']){
				$this->table[$this->nowTable]["tdWidth"][$this->col]=ConvertSize($attr['WIDTH'],$this->pgWidth);
				//echo "<br>Td Width : ".$this->table[$this->nowTable]["tdWidth"][$this->col];
			}else{
				$this->table[$this->nowTable]["tdWidth"][$this->col]=getTdSize($this->table[$this->nowTable]["td"],$this->pgWidth);	
			}
		}
		if($attr["ALIGN"]){
			$this->align=$attr['ALIGN'];	
		}
		///// assign td colspan
		if($attr["COLSPAN"]){
			$this->table[$this->nowTable]["tdColSpan"]=$attr["COLSPAN"];	
		}else{
			$this->table[$this->nowTable]["tdColSpan"]=0;	
		}
		////////// assign td class
		$this->table[$this->nowTable]["tdClass"][$this->col]=$attr["CLASS"];
		////////// check td bgcolor
		//echo "<br>Color --> ".$attr["BGCOLOR"]." -- colum -->".$this->col;
		$this->table[$this->nowTable]["tdBgColor"][$this->col]=$attr["BGCOLOR"];
		////////// Set start td
		$this->tdbegin=true;
	
		break;
	
	case 'P':
		$this->visualX++;
		break;
	
	case 'BR':
		$this->isBr=true;
		$this->visualY++;
		break;
	case 'B':
		//echo "<br>Set B font";
		$this->visualX++;
		$this->style["bFont"]=true;
		//$this->visualBlock[$this->visualX][$this->visualY]["bFont"]=true;
	
		break;
	
	case 'IMG':
		$this->Image($attr["SRC"],95,10,20);
		//echo $attr["SRC"];
		break;
	}
	
	//////// Check style of each tag 
	$this->checkStyle($attr["STYLE"]);
	//////// Check class of each tag 
	$this->checkClass($attr["CLASS"]);
	
}
function CloseTag($tag)
{
	switch($tag){
    
	case 'TABLE': // TABLE-BEGIN
		$this->table[$this->nowTable]["border"]=0;
		$this->nowTable--;
    	break;
	case 'TR':
	
		if($this->rowSpace){
			$tmpBoder=explode("BT",$this->rowSpace);
			$tmpBoder2=explode("TB",$this->rowSpace);
			if(count($tmpBoder)>1 || count($tmpBoder2)>1){
				$this->SetY($this->GetY()+0.3);
			}
		}
		$this->trHeigth=0;
		$this->Ln();//$this->rowSpace
		$this->row++;
	
		break;
	case 'TD':
	
		if($this->tdbegin){
			//echo "<br>Write col ".$this->col;
			if($this->table[$this->nowTable]["tdClass"][$this->col]=="reporth"){
				$this->rowSpace=1;
				//echo "<br>Class header ".$this->table[$this->nowTable]["tdClass"][$this->col];
				//echo "<br>Colum : ".$this->col;
				$this->SetFont($this->fFamily,"B",$this->fSize+4);
				$this->trHeigth=20;
			}else if($this->table[$this->nowTable]["tdClass"][$this->col]=="reference"){
			}else{
				$this->trHeigth=$this->table[$this->nowTable]["trHeight"][$this->row];
			}
			if($this->table[$this->nowTable]["tdColSpan"]){
				for($i=0;$i<$this->table[$this->nowTable]["tdColSpan"];$i++){
					//echo "<br>Colum : ".($this->col+$i)."<br> Colum width : ".$this->table[$this->nowTable]["tdWidth"][$this->col+$i];
					$this->tdWidth +=$this->table[$this->nowTable]["tdWidth"][$this->col+$i];	
				}
				$this->col+=$this->table[$this->nowTable]["tdColSpan"]-1;
			}else{
				$this->tdWidth=$this->table[$this->nowTable]["tdWidth"][$this->col];
			}
			$this->writeVisualBlockData($this->tdWidth,$this->trHeigth);
			$this->tdbegin=false;
			$this->col++;
		}
		$this->table[$this->nowTable]["tdClass"][$this->col]="";
		$this->visualBlock[$this->visualX][$this->visualY]["color"]=array();
		$this->visualBlock[$this->visualX][$this->visualY]["bFont"]=false;
	    $this->reSetFont();
		$this->tdWidth=0;
		if(strlen($this->rowSpace)<strlen($this->border)){
			$this->rowSpace=$this->border;	
		}
		$this->border="";
		$this->align="";
		///////// Set empty style all tag///////////
		unset($this->style);
		//$this->style=array();
		$this->table[$this->nowTable]["tdBgColor"][$this->col]="";
		////// Set empty all color td background ///////////////
		//unset($this->table[$this->nowTable]["tdBgColor"]);
		
		break;
	case 'P':
		//echo "<br>Set P Br ";
		$this->isBr=true;
		$this->visualY++;
		break;
	case 'B':
		$this->visualX++;
		$this->style["bFont"]=false;
		//$this->visualBlock[$this->visualX][$this->visualY]["bFont"]=false;
		break;
	}
	
	
}
function readMetaTags($html)
{
//! @return void
//! @desc Pass meta tag info to PDF file properties
	$regexp = '/ (\\w+?)=([^\\s>"]+)/si'; // changes anykey=anyvalue to anykey="anyvalue" (only do this when this happens inside tags)
 	$html = preg_replace($regexp," \$1=\"\$2\"",$html);
  $regexp = '/<meta .*?(name|content)="(.*?)" .*?(name|content)="(.*?)".*?>/si';
  preg_match_all($regexp,$html,$aux);
  
  $firstattr = $aux[1];
  $secondattr = $aux[3];
  for( $i = 0 ; $i < count($aux[0]) ; $i++)
  {

     $name = ( strtoupper($firstattr[$i]) == "NAME" )? strtoupper($aux[2][$i]) : strtoupper($aux[4][$i]);
     $content = ( strtoupper($firstattr[$i]) == "CONTENT" )? $aux[2][$i] : $aux[4][$i];
     switch($name)
     {
       case "KEYWORDS": $this->SetKeywords($content); break;
       case "AUTHOR": $this->SetAuthor($content); break;
       case "DESCRIPTION": $this->SetSubject($content); break;
     }
  }
  //Comercial do Aplicativo usado (no caso um script):
  $this->SetCreator("HTML2FPDF >> http://chiangmaioasis.net");
}
function DisableTags($str='')
{
//! @return void
//! @desc Disable some tags using ',' as separator. Enable all tags calling this function without parameters.
  if ($str == '') //enable all tags
  {
    //Insert new supported tags in the long string below.
    $this->enabledtags = "<tt><kbd><samp><option><outline><span><newpage><page_break><s><strike><del><bdo><big><small><address><ins><cite><font><center><sup><sub><input><select><option><textarea><title><form><ol><ul><li><h1><h2><h3><h4><h5><h6><pre><b><u><i><a><img><p><br><strong><em><code><th><tr><blockquote><hr><td><tr><table><div>";
  }
  else
  {
    $str = explode(",",$str);
    foreach($str as $v) $this->enabledtags = str_replace(trim($v),'',$this->enabledtags);
  }
}
/*
 * Function writeVisualBlockData
 * @parameter $cellWidth - width of each cell
 * @parameter $cellHeight - height of each cell
 * This function : write data in visual block into cell of pdf file
 */
function writeVisualBlockData($cellWidth,$cellHeight){
	$posX=$this->GetX();
	$posY=$this->GetY();
	/////// write cell into pdf file and fill color into cell
	$this->Cell($cellWidth,$cellHeight,'',($this->table[$this->nowTable]["border"])?$this->table[$this->nowTable]["border"]:$this->border,'','',$this->getTdBgColor());
	//$this->Cell($cellWidth,$cellHeight,'',1,'','',$this->getTdBgColor());
	$firstBr=true;
	$posX=$this->GetX();
	$posY=$this->GetY();
	//echo "<br>Get Pos Y : ".$this->GetY();
	$colSpan=0;
	if($this->table[$this->nowTable]["tdColSpan"]){
		$colSpan=$this->table[$this->nowTable]["tdColSpan"];
	}
	////////// Write text in visual block into cell on pdf
	for($y=0;$y<=$this->visualY;$y++){
		$this->SetTextColor(0);
		$strlen=0;
		$extraX=0;
		$bufferWriteData="";
		$checkSpace = "";
		$setBFont = false;
		$dataBlock = array();
		$countData = 0;
		for($x=0;$x<=$this->visualX;$x++){
			/////// Check text is set bold font or not. //////////////////
			if($this->visualBlock[$x][$y]["bFont"] && $this->table[$this->nowTable]["tdClass"][$this->col-$colSpan]!="reporth"){
				$dataBlock["bFont"][$countData]=true;
			}
			/////// Check text is set character or not. //////////////////
			if($this->visualBlock[$x][$y]["color"]["set"]){
				$dataBlock["bColor"]["r"][$countData]=$this->visualBlock[$x][$y]["color"]["r"];
				$dataBlock["bColor"]["b"][$countData]=$this->visualBlock[$x][$y]["color"]["b"];
				$dataBlock["bColor"]["g"][$countData]=$this->visualBlock[$x][$y]["color"]["g"];
				$dataBlock["bColor"]["Set"][$countData]=true;
			}
			///////// Check visual block has data or not. /////////////////
			$checkSpace =str_replace(" ","",$this->visualBlock[$x][$y]["data"]);
			if($checkSpace!=""){
				$bufferWriteData.=$this->visualBlock[$x][$y]["data"];
				$dataBlock["data"][$countData]=	$this->visualBlock[$x][$y]["data"];
				$countData++;
			}
			////////////// Check enter to new line./////////////
			if($this->table[$this->nowTable]["tdBr"] && $firstBr){
				$posY-=$cellHeight-5;
				$firstBr=false;
			}
			if($this->visualBlock[$x][$y]["br"]){
				//echo "<br>BR : ".$this->table[$this->nowTable]["tdBr"];
				$posY+=$cellHeight/($this->table[$this->nowTable]["tdBr"]+1.5);
			}
			
			
		}
		if($bufferWriteData!=""){
			$chkNumb = str_replace(" ","",$bufferWriteData);
			if(is_numeric($chkNumb)){
				$bufferWriteData = $chkNumb;
			}
			///////// convert UTF-8 to ISO-8859-11 for support thai font
			$bufferWriteData = iconv("UTF-8","ISO-8859-11//IGNORE" , $bufferWriteData);
			$strlen = $this->GetStringWidth($bufferWriteData);	
			//echo "<br>Data : ".$bufferWriteData;
			//echo "<br>Data length : ".strlen($bufferWriteData);
			///////// check text width more than cell width or not.
			///////// if text width more than cell width will split text
			if($cellWidth< $strlen){
				$tmpStr=array();
				$subStr = str_split($bufferWriteData,($strlen-$cellWidth)+($strlen/4));
				$tmp = explode(" ",$subStr[0]);
				$strLast="";
				$lenArray=0;
				if(count($tmp)>2){
					for($cs=0;$cs<count($tmp)-1;$cs++){
						if($cs==0){
							$tmpStr[0]=$tmp[0];
						}else if($this->GetStringWidth($tmpStr[0])<$cellWidth-10){
							$tmpStr[0].=" ".$tmp[$cs];
							$lenArray=$cs;	
						}else{
							$strLast.=" ".$tmp[$cs];
						}
						//echo "<br>$cs First Array : ".$this->GetStringWidth($tmpStr[0]);
						//echo "<br>First Array : ".$tmpStr[0];
					}
					$tmpStr[1]=$strLast.$subStr[1];
				}else{
					$tmpStr[0]=$tmp[0];
					$tmpStr[1]=$tmp[1].$subStr[1];	
				}
				//print_r($tmpStr);
				$posY+=$cellHeight/(count($tmpStr)-.5);
				for($s=0;$s<count($tmpStr);$s++){
					$strlen = $this->GetStringWidth($tmpStr[$s]);
					$posY+=$cellHeight/count($tmpStr)*$s;
					
					////////// Now not stable in check new line Bold font
					if($dataBlock["bFont"][0]){
						$this->SetFont($this->fFamily,"B",$this->fSize);
					}else if($this->table[$this->nowTable]["tdClass"][$this->col-$colSpan]=="reporth"){
						
					}else{
						$this->reSetFont();
					}
					$extraX = -$this->checkPadding()+$this->checkAlign($strlen,$cellWidth);
					//////// wirte data into cell
					//echo "<br>Sub string ";
					//echo "<br>Data : ".$tmpStr[$s];
					//echo "<br>Pos Y : ".($posY-($this->table[$this->nowTable]["trHeight"][$this->row]/4));
					$this->Text($posX-$cellWidth+$extraX,$posY-($this->table[$this->nowTable]["trHeight"][$this->row]/4),$tmpStr[$s]);
				}
			}else{
				$messLen="";
				for($w=0;$w<=$countData;$w++){
					if($dataBlock["bColor"]["Set"][$w]){
						$this->SetTextColor($dataBlock["bColor"]["r"][$w],$dataBlock["bColor"]["b"][$w],$dataBlock["bColor"]["g"][$w]);
					}
					if($dataBlock["bFont"][$w]){
						$this->SetFont($this->fFamily,"B",$this->fSize);
					}else{
						$this->reSetFont();
					}
					if($this->table[$this->nowTable]["tdClass"][$this->col-($colSpan-1)]=="reporth"){
						$this->SetFont($this->fFamily,"B",$this->fSize+4);
					}
					///////////// Check string width for assign to align //////////////	
					$strlen = $this->GetStringWidth($bufferWriteData);
					//echo "<br><br>Data : ".$dataBlock["data"][$w];
					//echo "<br>Now Table : ".$this->nowTable." Now Row : ".$this->row;
					//echo "<br>Pos Y : ".($posY+$cellHeight-($this->table[$this->nowTable]["trHeight"][$this->row]/4));
					//////// wirte data into cell
					$extraX = -$this->checkPadding()+$this->checkAlign($strlen,$cellWidth)+$this->GetStringWidth($messLen);
					//echo "<br>Pos Y : ".($posY+$cellHeight-($this->table[$this->nowTable]["trHeight"][$this->row]/4))." -- Pos X : ".($posX-$cellWidth+$extraX);
					$this->Text(($posX-$cellWidth+$extraX),($posY+$cellHeight-($this->table[$this->nowTable]["trHeight"][$this->row]/4)),iconv("UTF-8","ISO-8859-11//IGNORE" , $dataBlock["data"][$w]));					
					$messLen.=$dataBlock["data"][$w];
				}
				//echo "<br>";
			}
		}
	}
}
function checkAlign($strlen,$cellWidth){
	//////////// check align 
	
	switch($this->align){
	   	case 'center': // TABLE-BEGIN
	   		return ($cellWidth/2)-($strlen/2);//-($strlen/2);
	
	   		break;
		
		case 'left':
			///////// statement				
			break;
					
		case 'right':
			return $cellWidth-$strlen;
			break;
						
		default :
								
	}
}
/*
 * fuction checkClass for check to add pdf first page. 
 */
function checkClass($class){
	if($class=="pdffirstpage"){
		$this->cPage++;
	    if($this->table[$this->cPage*2]["tableWidth"]!=null){
	    	$this->lMargin=$this->sLMargin+($this->pgWidth-ConvertSize($this->table[$this->cPage*2]["tableWidth"]))/2;
	    }
	    $this->AddPage();
	}
}
function checkStyle($styleS){
	$contents =explode(";",$styleS);
	if (!empty($contents)){
	  	foreach($contents as $v){
	  		$tmp=explode("padding",$v);
	  		if(count($tmp)>1){
	  			$this->style["P"]="Padding";
	  			$tmp=explode("-",$tmp[1]);
	  			$tmp=explode(":",$tmp[1]);
	  			$this->style["padding"]["name"]=$tmp[0];
	  			$tmp=explode("p",$tmp[1]);
	    		$this->style["padding"]["point"]=$tmp[0];
	     	}
	     	$tmp=explode("text",$v);
	     	if(count($tmp)>1){
	     		$tmp=explode("-",$tmp[1]);
	     		$tmp=explode(":",$tmp[1]);
	     		$this->style["text"]["name"]=$tmp[0];
	     		$this->style["text"]["class"]=str_replace(" ","",$tmp[1]);
	     	}
	     	$tmp=explode("border",$v);
	     	if(count($tmp)>1){
	     		$tmp=explode("-",$tmp[1]);
	     		$tmp=explode(":",$tmp[1]);
	     		$this->style["text"]["name"]=$tmp[0];
	     		$this->style["text"]["class"]=str_replace(" ","",$tmp[1]);
	     	}
	     	///////////// Check enter to new page from key word /////////////////
	     	$tmp=explode("page",$v);
	     	if(count($tmp)>1){
	     		$tmp=explode("-",$tmp[1]);
	     		if($tmp[1]=="break"){
	     			$tmp=explode(":",$tmp[2]);
	     			if($tmp[0]=="before"&&$tmp[1]=="always"){
	     				$this->cPage++;
	     				if($this->table[$this->cPage*2]["tableWidth"]!=null){
	     					$this->lMargin=$this->sLMargin+($this->pgWidth-ConvertSize($this->table[$this->cPage*2]["tableWidth"]))/2;
	     				}
	     				$this->AddPage();
	     			}
	     		}
	     	}
	     	///////// Set text color ///////////
	     	$tmp=explode("color",$v);
	     	if(count($tmp)>1){
	     		$tmp=explode(":",$tmp[1]);
	     		$tmp=explode("#",$tmp[1]);
	     		$tmp=str_split($tmp[1],2);
	     		//echo "<br>Set Color : visual X : ".$this->visualX." -- visual Y : ".$this->visualY;
	     		$this->visualBlock[$this->visualX][$this->visualY]["color"]["set"]=true;
	     		if($tmp[0]!=""){
	     			$this->visualBlock[$this->visualX][$this->visualY]["color"]["r"]=hexdec($tmp[0]);
	     		}
	     		if($tmp[1]!=""){
	     			$this->visualBlock[$this->visualX][$this->visualY]["color"]["b"]=hexdec($tmp[1]);
	     		}
	     		if($tmp[2]!=""){
	     			$this->visualBlock[$this->visualX][$this->visualY]["color"]["g"]=hexdec($tmp[2]);
	     		}
	     	}
	     	$this->checkUnderline();
			$this->checkSetAlign();
			$this->checkBorder();
	     	
	  	}
	}
}
function checkBorder(){
	if($this->style["text"]["name"]=="top"){
		$this->border.="T";
	}else if($this->style["text"]["name"]=="bottom"){
		$this->border.="B";
	}
}
function checkUnderline(){
	if($this->style["text"]["name"]=="decoration"){
		if($this->style["text"]["class"]=="underline"){
			$this->SetFont('','U');
		}else{
			$this->SetFont('','');
		}
	}else{
		$this->SetFont('','');
	}
	
}
function checkSetAlign() {
	if($this->style["text"]["name"]=="align"){
		if($this->style["text"]["class"]!=" "){
			$this->align=$this->style["text"]["class"];
		}
			
	}
	
}
function checkPadding(){
	switch($this->style["padding"]["name"]){
	    case 'right':
	    	
	    	if($this->align=="left"){
	    		return 0;	
	    	}else{
	    		return $this->style["padding"]["point"]/10;
	    	}
			
			break;
		case 'left':
			if($this->align=="right"){
	    		return 0;
			}else{
				return -$this->style["padding"]["point"]/10;
			}
			break;
		
	}
}
function getTdBgColor(){
	$col=$this->col-$this->table[$this->nowTable]["tdColSpan"];
	if($this->table[$this->nowTable]["trBgColor"][$this->row]!=""){
		//echo "<br>Color".$this->table[$this->nowTable]["trBgColor"][$this->row];
		$color=explode("#",$this->table[$this->nowTable]["trBgColor"][$this->row]);
	    $color=str_split($color[1],2);
	    if(strlen($color[2])<2){
	   		$color[2] .="0";
	   	}
	     $color[0]=hexdec($color[0]);
	     $color[1]=hexdec($color[1]);
	     $color[2]=hexdec($color[2]);
		$this->SetFillColor($color[0],$color[1],$color[2]);
		return true;
	}else if($this->table[$this->nowTable]["tdBgColor"][$col]!=""){
		//echo "<br>Color".$this->table[$this->nowTable]["tdBgColor"][$this->col];
		//echo "<br>colum : ".$this->col;
		$color=explode("#",$this->table[$this->nowTable]["tdBgColor"][$col]);
	    $color=str_split($color[1],2);
	    if(strlen($color[2])<2){
	   		$color[2] .="0";
	   	}
	     $color[0]=hexdec($color[0]);
	     $color[1]=hexdec($color[1]);
	     $color[2]=hexdec($color[2]);
		$this->SetFillColor($color[0],$color[1],$color[2]);
		return true;
	}else{
		return false;
	}
}

function reSetFont(){
	$this->SetFont($this->fFamily,$this->fStyle,$this->fSize);
}
function countTable($a){
	$nowTable=0;
	$numTable=0;
	$isTable=false;
	$numTr=0;
	$isContTd=array();
	foreach($a as $i => $e){
		if($i%2==0){
		}else{
			if($e{0}=='/'){
				//Statement
			}else{
				/////////////////////// check tag open
			    $regexp = '|=\'(.*?)\'|s'; // eliminate single quotes, if any
		      	$e = preg_replace($regexp,"=\"\$1\"",$e);
		      			$regexp = '| (\\w+?)=([^\\s>"]+)|si'; // changes anykey=anyvalue to anykey="anyvalue" (only do this when this happens inside tags)
		      	$e = preg_replace($regexp," \$1=\"\$2\"",$e);
				//Extract attributes
				$contents=array();
		        preg_match_all('/\\S*=["\'][^"\']*["\']/',$e,$contents);
		        preg_match('/\\S+/',$e,$a2);
		       	$tag=strtoupper($a2[0]);
		       	$attr=array();
		       	//Ignore content between <table>,<tr> and a <td> tag (this content is usually only a bunch of spaces)
				if (!empty($contents))
				{
		  			foreach($contents[0] as $v)
		  			{
		  				if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
		    			{
		    				$attr[strtoupper($a3[1])]=$a3[2];
		     			}
		  			}
				}
			}
			preg_match('/\\S+/',$e,$a2);
		    $tag=strtoupper($a2[0]);
		    if($tag=="/TABLE"){
				$isTable=false;
		    }
			if($tag=="TABLE"){
				$numTable++;
				$numTr=0;
				$isTable=true;
			}
			if($tag=="TR"){
				$numTr++;	
			}
			if($isTable && $numTr==1 && $tag=="TD"){
				$this->table[$numTable]["td"]++;
				if(stristr($attr["WIDTH"],'%')){
					$this->table[$numTable]["tableWidth"]=null;
				}else{
					$this->table[$numTable]["tableWidth"]+=$attr["WIDTH"];	
				}
				
			}
		}
	}
	//print_r($this->table);
}
/////////////// Create by natt : 5-03-2009
////// This function get web page from url and write content to buffer stream
function get_web_page( $url )
{
    $ch  = curl_init( $url );
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, "deflate");
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 120);
    //curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TRANSFERTEXT, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
    curl_setopt($ch, CURLOPT_CRLF, false);
    $content = curl_exec($ch);
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $info  = curl_getinfo( $ch );
    curl_close( $ch );
    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    $header['info'] = $info;
    return $header;
}
}
?>