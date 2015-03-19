var bbEditor = new Class
({
	initialize : function(to)
	{
		this.to = $(to);
	},

	addTag : function(bTag,eTag)
	{
		if (document.selection) 
		{
			//Cas de IE
			var selectText = document.selection.createRange().text;
			this.to.focus();
			document.selection.createRange().text = bTag + selectText + eTag;
		} else if (this.to.selectionStart || this.to.selectionStart == "0") {
			//FF, Netscape
			var iString = {start: this.to.selectionStart, stop:this.to.selectionEnd};
			iString = $extend({content : this.to.value.substring(iString.start, iString.stop)},iString);
			var lSubst = bTag + iString.content + eTag;
			this.to.value = this.to.value.substring(0, iString.start) + lSubst + this.to.value.substring(iString.stop, this.to.value.length);
			this.to.focus();
			iString.start += bTag.length;
			if (iString.content.length > 0) {
			iString.start += iString.content.length + eTag.length;
			}
			this.to.selectionStart = iString.start;
			this.to.selectionEnd = iString.stop;
		} else {
			this.to.value = this.to.value + bTag + eTag;
			this.to.focus();
		}
	},

	preview: function()
	{
		if (!$(this.to.id + "_preview")) {
			var pDiv = new Element("div");
			pDiv.id = this.to.id + "_preview";
			this.hide();
			pDiv.setStyles(this.to.getCoordinates());
			pDiv.style.position = "absolute";
			pDiv.className = "bbeditorpreview";
			var pFrame = new Element("iframe");
			pFrame.className = "bbcodepreviewframe";
			var aText = this.to.value;
			while (aText.indexOf("\n") > -1) {
			aText = aText.replace("\n", "%0A%0D");
			}
			while (aText.indexOf("#") > -1) {
			aText = aText.replace("#", "%23");
			}
			while (aText.indexOf("&") > -1) {
			aText = aText.replace("&", "%26");
			}
			while (aText.indexOf("+") > -1) {
			aText = aText.replace("+", "%2b");
			}
			pFrame.src = "/Redaction/Affich/BBPreview.htm?Preview=" + aText;
			pDiv.adopt(pFrame);
			document.body.adopt(pDiv);
		} else {
			$(this.to.id + "_preview").remove();
			this.show();
		}
	},
	hide : function () {this.to.style.visibility = "hidden";},
	show : function () {this.to.style.visibility = "visible";},
	clear : function () {this.to.value = "";},
	selectAll : function () {this.to.focus();this.to.select();}
});

