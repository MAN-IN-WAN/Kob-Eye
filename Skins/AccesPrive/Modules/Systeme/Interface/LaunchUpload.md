
function launchUpload( id, module, obj ) {

	var link = $('select-file-' + id);
	var filename = $('selected-file-' + id);
	var image = $('progress-file-' + id);
	// var linkIdle = link.get('html');

	function linkUpdate() {
		if (!swf.uploading) return;
		var size = Swiff.Uploader.formatUnit(swf.size, 'b');
		// link.set('html', swf.percentLoaded + '% sur ' + size);
		image.setStyle('background-position', (100-swf.percentLoaded) + '% 0');
	}

	// Uploader instance
	var swf = new Swiff.Uploader({
		verbose: true,
		appendCookieData: true,
		allowDuplicates: true,
		path: '/Skins/[!Systeme::Skin!]/Swf/Swiff.Uploader.swf',
		url: '/Systeme/Interface/Upload.htm?Module=' + module + '&obj=' + obj,
		target: link,
		instantStart: true,
		onSelectSuccess: function(files) {
			this.setEnabled(false);
			link.setStyle('display', 'none');
			image.setStyle('display', 'inline');
			image.setStyle('background-position', '0% 0');
		},
		onQueue: linkUpdate,		
		onFileComplete: function(file) {
			if (file.response.error) {
				alert('erreur lors du transfert');
			} else {
				filename.set('value', file.response.text);
			}
			this.setEnabled(true);
		},
		onComplete: function() {
			// link.set('html', linkIdle);
			link.setStyle('display', 'inline');
			image.setStyle('display', 'none');
		}
	});

	// Button state
	link.addEvents({
		click: function() {
			return false;
		},
		mouseenter: function() {
			this.addClass('hover');
			swf.reposition();
		},
		mouseleave: function() {
			this.removeClass('hover');
			this.blur();
		},
		mousedown: function() {
			this.focus();
		}
	});

}