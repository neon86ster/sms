<table valign="middle" border="0" cellspacing="0" cellpadding="0" >
<? if($export=="Excel"){ ?>
	<tr>
		<td align="right"><b>Therapist</b></td>
		<td align="center"><b>Total Customer</b></td>
		<td align="center"><b>Total CSI(%)</b></td>
	</tr>	
<?}else{?>
	<tr>
		<td width="40%"></td><td width="1%"></td><td width="60%"></td>
	</tr>	
<? } ?>
<?
$barcolor = "header";
$http = "../../..";
for($i=1;$i<count($yaxis);$i++){
	if($export!="Excel"){
?>
	<tr height="20">
		<td align="right"><?=$yaxis[$i]?></td>
		<? if($export!="Excel"){ ?>
		<td style="background-image: url('<?=$http?>/images/bar/graphline.jpg');background-repeat: repeat-y;"><img src="<?=$http?>/images/bar/graphline.jpg" width="3px" height="2"></td>
		<td valign="middle" style="padding-top:3px;padding-bottom:3px;">
		<img src="<?=$http?>/images/bar/c0ea51.jpg" alt="" width="<?=$csnumset[$i]*4?>" height="24">
		<? } ?>
		<?=number_format($csnumset[$i],2,".",",")?>
		</td>
	</tr>
	<tr height="20">
		<td align="right"></td>
		<td style="background-image: url('<?=$http?>/images/bar/graphline.jpg');background-repeat: repeat-y;"><img src="<?=$http?>/images/bar/graphline.jpg" width="3px" height="2"></td>
		<td valign="middle" style="padding-top:3px;padding-bottom:3px;">
		<? if($export!="Excel"){ ?><img src="<?=$http?>/images/bar/4892f7.jpg" alt="" width="<?=$dataset[$i]*4?>" height="24"> <? } ?>
		<?=number_format($dataset[$i],2,".",",")?>
		</td>
	</tr>
<? 	}else{ ?>
	<tr height="20">
		<td align="right"><?=$yaxis[$i]?></td>
		<td align="center">
		<?=number_format($csnumset[$i],2,".",",")?>
		</td>
		<td align="center">
		<?=number_format($dataset[$i],2,".",",")?>
		</td>
	</tr>
<?	
	} 
}
?>	
	<tr height="3">	
			<td align="right"></td>
			<td colspan="2">
			<?if($export!="Excel"){?><img src="<?=$http?>/images/bar/graphline.jpg" width="500" height="3"><?}?>
			</td>
	</tr>
	<tr height="30">	
			<td align="center" height="40" colspan="3" style="font-size:13px;"> All CSI = <?=number_format($allcsi,2,".",",")?> % </td>
	</tr>
</table>