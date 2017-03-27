$(document).ready(function(){

	$.each($(".EditorBBCode"),function(i,v){
                
		CKEDITOR.replace($(v).attr('name'), {
			extraPlugins : 'bbcode',
			toolbar :
			[
				['Source', '-', 'Save','NewPage','-','Undo','Redo'],
				['Find','Replace','-','SelectAll','RemoveFormat'],
				['Link', 'Unlink', 'Image'],
				'/',
				[ 'Bold', 'Italic','Underline'],
				['NumberedList','BulletedList','-','Blockquote'],
				['TextColor', '-', 'Smiley','SpecialChar', '-', 'Maximize']
			]
		});
	});
	$.each($(".EditorFull"),function(i,v){
		CKEDITOR.replace($(v).attr('name'), {
	    		toolbar: 'Basic'
		});
	});
	
	
	$.each($(".datePicker"),function(i,v){
		$(v).datetimepicker({
			locale: 'fr',
			sideBySide: true,
			format: 'L LTS'
		});
		$(v).data("DateTimePicker").useCurrent('minute');
	});
	

	var upUrl = '/MiseEnPage/Default/Upload.json'
        var uploadButton = $('<a/>')
				.addClass('btn btn-primary')
				.prop('disabled', true)
				.text('Processing...')
				.on('click', function () {
					var $this = $(this),
					data = $this.data();
					$this
						.off('click')
						.text('Abort')
						.on('click', function () {
							$this.remove();
							data.abort();
					});
					data.submit().always(function () {
						$this.remove();
					});
				});
	$.each($(".fileupload"),function(i,v){		
		$(v).fileupload({
			url: upUrl,
			dataType: 'json',
			autoUpload: false,
			acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
			maxFileSize: 5000000, // 5 MB
			// Enable image resizing, except for Android and Opera,
			// which actually support image resizing, but fail to
			// send Blob objects via XHR requests:
			disableImageResize: /Android(?!.*Chrome)|Opera/
				.test(window.navigator.userAgent),
			previewMaxWidth: 100,
			previewMaxHeight: 100,
			previewCrop: true
		}).on('fileuploadadd', function (e, data) {
			data.context = $('<div/>').appendTo('#files');
			$.each(data.files, function (index, file) {
				var node = $('<p/>').append($('<span/>').text(file.name));   
				if (!index) {
					node.append('<br>').append(uploadButton.clone(true).data(data));
				}
				node.appendTo(data.context);
			});
			$('#progress .progress-bar').css('width','0%');
		}).on('fileuploadprocessalways', function (e, data) {
			var index = data.index, file = data.files[index], node = $(data.context.children()[index]);
			if (file.preview) {
				node.prepend('<br>').prepend(file.preview);
			}
			if (file.error) {
				node.append('<br>').append($('<span class="text-danger"/>').text(file.error));
			}
			if (index + 1 === data.files.length) {
				data.context.find('a').text('Upload').prop('disabled', !!data.files.error);
			}
		}).on('fileuploadprogressall', function (e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			$('#progress .progress-bar').css('width',progress + '%');
		}).on('fileuploaddone', function (e, data) {
			$.each(data.result.files, function (index, file) {
				if (file.url) {
					var link = $('<a>').attr('target', '_blank').prop('href', '/'+file.url);
					$(data.context.children()[index]).wrap(link);
					data.context.parent().prevAll('input').val(file.url);
				} else if (file.error) {
					var error = $('<span class="text-danger"/>').text(file.error);
					$(data.context.children()[index]).append('<br>').append(error);
				}
			});
		}).on('fileuploadfail', function (e, data) {
			$.each(data.files, function (index, file) {
				var error = $('<span class="text-danger"/>').text('File upload failed.');
				$(data.context.children()[index]).append('<br>').append(error);
			});
		}).prop('disabled', !$.support.fileInput)
		    .parent().addClass($.support.fileInput ? undefined : 'disabled');
	});
});


function getTimestamp(dateString) {
	var ret=0;
	if (dateString.length) {
                var dateArr = dateString.split(' ');
		var dateTemp = dateArr[0].split('/');
		var dateClear = dateTemp[2] + '-' + dateTemp[1] + '-' + dateTemp[0] + 'T' + dateArr[1];
		ret = Date.parse(dateClear)/1000;
        } else {
		ret = Date.now()/1000;
		ret = Math.floor(ret);
	}
	return ret;
}

function beforeSubmit(){
	$.each($(".datePicker"),function(i,v){
		var date = $(v).val();
		date = getTimestamp(date);
		$(v).val(date);
	});
	
	return false;
}

function optOutFile() {
        $('#Filedata').prop('name','Filedata_temp');
	return true;
}