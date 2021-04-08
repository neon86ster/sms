<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
$mid = $obj->getParameter("mid");

$uploadsuccess = false;
if(isset( $_REQUEST["Submit"] )) { 	
	
	$sql = "select * from m_membership where member_id=$mid";
	$rs = $obj->getResult($sql);

	if($rs["rows"]==1){
		$ints = "member".$rs[0]["member_id"];
	}else{
		$ints = "tmp";
	}
	
		if($_FILES["uploadedfile"]["type"]=="image/gif")
			$imgsn = $ints.".gif";
		elseif($_FILES["uploadedfile"]["type"]=="image/pjpeg"||$_FILES["uploadedfile"]["type"]=="image/jpeg")
			$imgsn = $ints.".jpg";
		elseif($_FILES["uploadedfile"]["type"]=="image/x-png"||$_FILES["uploadedfile"]["type"]=="image/png")
			$imgsn = $ints.".png";
	
			
		if (!empty($imgsn))
		{
			$_SESSION["images_member"]=$imgsn;
			$target_path=$customize_img ."/images/member/";
 			move_uploaded_file ($_FILES['uploadedfile']['tmp_name'],$target_path.$imgsn) ; 
			resizeImageE($target_path.$imgsn,$target_path,"96","96");
			
			if($mid){$id = $obj->setResult("update m_membership set mpic='$imgsn' where member_id=$mid");}
			$uploadsuccess = true;
?>
<script type="text/javascript">
	function setVisibleC() {
			window.opener.document.getElementById('images_member').src="<?=$customize_part?>/images/member/<?=empty($_SESSION["images_member"]) ? "default.gif" : $_SESSION["images_member"] ?>";
			window.opener.document.getElementById("mpic").value="<?= $_SESSION["images_member"] ?>";
	}
</script>
<img src="<?=$customize_part?>/images/member/<?=empty($_SESSION["images_member"]) ? "default.gif" : $_SESSION["images_member"] ?>">
<?php
		}
		else
			echo "There was an error uploading the file, please try again!";
	}
	
?>
<link href="../../css/style.css" rel="stylesheet" type="text/css">
<form enctype="multipart/form-data" action="" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
Choose a file to upload: <input name="uploadedfile" type="file" /><br />
<input type="hidden" name="mid" value="<?=$mid?>">
<input type="submit" name="Submit" value="Upload File" /> 
<br />(Member picture should be in (PNG, GIF, JPG) format, 63 × 62 Pixel)
</form>
<?if($uploadsuccess){?>
<script type="text/javascript">
	setVisibleC();
	window.close();
</script>
<?}?>
<?
function resizeImageE($filename, $dest, $width, $height, $pictype = "")
{
  $format = strtolower(substr(strrchr($filename,"."),1));
  switch($format)
  {
    case 'gif' :
    $type ="gif";
    $img = imagecreatefromgif($filename);
    break;
    case 'png' :
    $type ="png";
    $img = imagecreatefrompng($filename);
    break;
    case 'jpg' :
    $type ="jpg";
    $img = imagecreatefromjpeg($filename);
    break;
    case 'jpeg' :
    $type ="jpg";
    $img = imagecreatefromjpeg($filename);
    break;
    default :
    die ("ERROR; UNSUPPORTED IMAGE TYPE");
    break;
  }

  list($org_width, $org_height) = getimagesize($filename);
  $xoffset = 0;
  $yoffset = 0;
  if ($pictype == "thumb") // To minimize destortion
  {
    if ($org_width / $width > $org_height/ $height)
    {
      $xtmp = $org_width;
      $xratio = 1-((($org_width/$org_height)-($width/$height))/2);
      $org_width = $org_width * $xratio;
      $xoffset = ($xtmp - $org_width)/2;
    }
    elseif ($org_height/ $height > $org_width / $width)
    {
      $ytmp = $org_height;
      $yratio = 1-((($width/$height)-($org_width/$org_height))/2);
      $org_height = $org_height * $yratio;
      $yoffset = ($ytmp - $org_height)/2;
    }
  //Added this else part -------------
  } else {   
      $xtmp = $org_width/$width;
      $new_width = $width;
      $new_height = $org_height/$xtmp;
      if ($new_height > $height){
        $ytmp = $org_height/$height;
        $new_height = $height;
        $new_width = $org_width/$ytmp;
      }
      $width = round($new_width);
      $height = round($new_height);
  }
 

  $img_n=imagecreatetruecolor ($width, $height);
  imagecopyresampled($img_n, $img, 0, 0, $xoffset, $yoffset, $width, $height, $org_width, $org_height);
	unlink($filename); 
  if($type=="gif")
  {
    imagegif($img_n, $filename);
  }
  elseif($type=="jpg")
  {
    imagejpeg($img_n, $filename);
  }
  elseif($type=="png")
  {
    imagepng($img_n, $filename);
  }
  elseif($type=="bmp")
  {
    imagewbmp($img_n, $filename);
  }
  return true;
}
?>