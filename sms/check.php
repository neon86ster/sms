<?php
/*
 * Created on May 18, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
?>
<script type="text/javascript">
function detect_browser() {
   var mybrowser=navigator.userAgent;
   mybs = "can not check browser';
   if(mybrowser.indexOf('MSIE')>0){
      mybs = "IE";
   }
   if(mybrowser.indexOf('Firefox')>0){
      mybs = "Firefox";
   }   
   if(mybrowser.indexOf('Presto')>0){
       mybs = "Opera";

   }         
   if(mybrowser.indexOf('Chrome')>0){
       mybs = "Chrome";
   }     
 return mybs;
}
alert detect_browser();
</script>