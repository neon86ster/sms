<? 
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

$bid = $obj->getParameter("bid");
$uploadsuccess = false;
if(isset( $_REQUEST["Submit"] )) { 
	
	$sql = "select branch_id,branch_name from bl_branchinfo where branch_id=$bid";
	$rs = $obj->getResult($sql);	
	
	if($rs["rows"]==1){
		$ints = $rs[0]["branch_name"].$rs[0]["branch_id"];
	}else{
		$ints = "tmp";
	}
	//print_r($_FILES);
		if($_FILES["uploadedfile"]["type"]=="image/gif")
			$imgsn = $ints.".gif";
		elseif($_FILES["uploadedfile"]["type"]=="image/pjpeg"||$_FILES["uploadedfile"]["type"]=="image/jpeg")
			$imgsn = $ints.".jpg";
		elseif($_FILES["uploadedfile"]["type"]=="image/x-png"||$_FILES["uploadedfile"]["type"]=="image/png")
			$imgsn = $ints.".png";
	
			
		if (!empty($imgsn))
		{
			$_SESSION["image_employee"]=$imgsn;
			$target_path=$customize_img ."/images/branch/";
			unlink($target_path.$imgsn);
 			//copy ($_FILES['uploadedfile']['tmp_name'],$_SERVER["DOCUMENT_ROOT"]."/images/user/".$_SESSION["image_employee"])  or die ("There was an error uploading the file, please try again!"); 
			move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path.$imgsn);

			//print_r(getimagesize($_SERVER["DOCUMENT_ROOT"]."/images/user/".$_SESSION["image_employee"]));
			//exit();
			resizeImageE($target_path.$imgsn,$target_path,"87","80");
			//chmod($target_path,777);
			if($bid){$id = $obj->setResult("update bl_branchinfo set bpic='$imgsn' where branch_id=$bid");}
			$uploadsuccess = true;
?>
<script type="text/javascript">
	function setVisibleC() {
		window.opener.document.getElementById('images_branch').src="<?=$customize_part?>/images/branch/<?=empty($_SESSION["image_employee"]) ? "tmp.png" : $_SESSION["image_employee"] ?>";
	}
</script>
<img src="<?=$customize_part?>/images/branch/<?=empty($_SESSION["image_employee"]) ? "tmp.png" : $_SESSION["image_employee"] ?>">
<?php
		}
		else
			echo "There was an error uploading the file, please try again!";
	}
	
?>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<form enctype="multipart/form-data" action="" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
Choose a file to upload: <input name="uploadedfile" type="file" /><br />
<input type="hidden" name="bid" value="<?=$bid?>">
<input type="submit" name="Submit" value="Upload File" /> <br />(Branch logo should be in (PNG, GIF, JPG) format, 87 × 80 Pixel)
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