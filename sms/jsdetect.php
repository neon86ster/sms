<?php
/*
 * Created on May 13, 2009
 *
 * Detect security and all leftmenu focus
 */

$parent = '';				
for($i=0;$i<count($pageinfo["parent"]);$i++){
	if(!isset($pageinfo["parentid"][$i])){$pageinfo["parentid"][$i]="";}
	$parent.="|".$pageinfo["parent"][$i].$pageinfo["parentid"][$i];
}
if(!isset($pageinfo["pagename"])){$pageinfo["pagename"] = "";}
if(!isset($pageinfo["pageid"])){$pageinfo["pageid"] = "";}
if(!isset($pageinfo["pageurl"])){$pageinfo["pageurl"] = "";}
?>
<script type="text/javascript" src="/scripts/components.js"></script>
<script type="text/javascript">
<? if($pageinfo["parentid"][count($pageinfo["parent"])-1]!=1){?>
		// prevent open main frame on new window
		if(window.parent==null || window.parent.topFrame==null || window.parent.leftFrame==null){
				if(document.getElementById("popupwin")==null){
					document.location.href="/home.php";
				}
		}
<? } ?>
<? if($chkPageView==false){ ?>
			parent.location.href="/home.php";
<? }?>
 		showhide('<?=$pageinfo["pagename"].$pageinfo["pageid"]?>','<?=$pageinfo["pageurl"]?>','<?=($pageinfo["pagename"]=="Home")?"Home0":$parent?>','<?=$theme?>','<?=$pageinfo["pageid"]?>');
</script>
