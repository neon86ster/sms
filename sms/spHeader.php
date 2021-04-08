<script type="text/javascript">
// By : Ruck 06-05-2009
//configure the below two variables to determine where the static content will initially be positioned when the document loads, in terms of X and Y cooridinates, respectively

//For initial position all div which want to show alway on screen 
var headerWidth=document.getElementById("headerWidth");
var headerMain=document.getElementById("headerMain");

var subH=15;
var w=0;
var h=0;

try{
	document.getElementById("line").value;
}catch(e){
	w=-6;
}
document.getElementById('headerWidth').width=document.body.clientWidth-15;
w+=document.body.scrollLeft;
h+=document.body.scrollTop;
headerMain.style.left=w+6;
headerMain.style.top=h;

</script>
