<!-- By : Ruck 06-05-2009 -->
<div id="leftBar">
	<img id="line" src="../../../images/body_bg.gif" width="7px"/>
</div>
<div id="hiddenButton">
	<img id="spLine" align="right" src="/images/bar_close.gif" onclick="hiddenLeftFrame('/images')" width="8" height="24">
</div>
<script type="text/javascript">
// By : Ruck 06-05-2009
//configure the below two variables to determine where the static content will initially be positioned when the document loads, in terms of X and Y cooridinates, respectively

//For initial position all div which want to show alway on screen 
var hiddenButton=document.getElementById("hiddenButton"); 
var leftBar=document.getElementById("leftBar"); 
var line=document.getElementById("line");

line.height=document.body.clientHeight-17; 
var w=0;
var h=0;
w+=document.body.scrollLeft;
h+=document.body.scrollTop;
hiddenButton.style.left=w+3;
hiddenButton.style.top=h+(document.body.clientHeight/2);
leftBar.style.left=w;
leftBar.style.top=h;

</script>
