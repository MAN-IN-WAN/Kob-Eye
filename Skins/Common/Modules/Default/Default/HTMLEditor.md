<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body style="margin:0;">
<script type="text/javascript" src="/Skins/Common/Js/tinymce/tinymce.min.js"></script>
//<script src="/Skins/Common/Js/AC_OETags.js" language="javascript"></script>
<script type="text/javascript">

function setDataValue(val) {
alert('set' + val[0]);
	var ed = tinymce.get('content').setContent(val[0]);
}

function getDataValue() {  
	return tinymce.get('content').getContent();
}


function fixHeightOfTheText() {    
	var h = window.innerHeight - 105;
	var resizeHeight = h + "px";
	var textElement = tinymce.DOM.get('content_ifr');
	tinymce.DOM.setStyle(textElement, 'height', resizeHeight);
	return resizeHeight;
}

window.onresize = fixHeightOfTheText;

tinymce.init({
	selector: "textarea",
	theme: "modern",
	width: "100%",
	height: fixHeightOfTheText()
});
</script>
<textarea name="content"></textarea>
</body>
</html>