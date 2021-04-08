<table valign="middle" border="0" cellspacing="0" cellpadding="0" >
<? if($export=="Excel"){ ?>
	<tr>
		<td align="right"><b>Quality</b></td>
		<td align="center"><b>CSI(%)</b></td>
		<td></td>
	</tr>	
<?}else{?>
	<tr>
		<td width="30%"></td><td width="1%"></td><td width="70%"></td>
	</tr>
<? } ?>	
<?
$barcolor = "4892f7";
$http = "../../..";
//print_r($_SERVER);
for($i=0;$i<count($yaxis);$i++){

?>
<? if($export=="Excel"){ ?>
	<tr height="20">
		<td align="right"><?=$yaxis[$i]?></td>
		<td align="center"><?=number_format($dataset[$i],2,".",",")?></td>
		<td></td>
<?}else{?>
	<tr height="20">
		<td height="35" align="right"><?=$yaxis[$i]?></td>
		<td style="background-image: url('<?=$http?>/images/bar/graphline.jpg');background-repeat: repeat-y;">
			<img src="<?=$http?>/images/bar/graphline.jpg" width="3px" height="2">
		</td>
		<!--
		<? if(!$i){ ?>
			<td style="background-image: url('/images/bar/graphline.gif');background-repeat: repeat-y;" rowspan="<?=count($yaxis)?>">
			<img src="/images/bar/graphline.gif" width="3px"></td>
		<? } ?> -->
		<td valign="middle">
		<img src="<?=$http?>/images/bar/<?=$barcolor?>.jpg" alt="" width="<?=$dataset[$i]*4.5?>" height="24">
		<?=number_format($dataset[$i],2,".",",")?></td>
	</tr>
<? } 	
}

?>	<tr height="3">	
			<td align="right"></td>
			<td colspan="2">
				<? if($export!="Excel"){ ?><img src="<?=$http?>/images/bar/graphline.jpg" width="500" height="3" > <? } ?>
			</td>
	</tr>
	<tr height="30">
		<td colspan="3"></td>
	</tr>
	<tr height="10">	
			<td align="center" height="40" colspan="3" style="font-size:13px;">
			 All CSI = <?=number_format($allcsi,2,".",",")?> % 
			</td>
	</tr>
</table>