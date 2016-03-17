
<form class="form-horizontal themed" method="post">
	<input type="hidden" name="submitted" value="1" />
        [STORPROC [!P::getElements()!]|E]
                <div class="masonry-item">
                        <!-- new widget -->
                        <div class="jarviswidget row-fluid" id="widget-id-[!Pos!]">
                                <!-- wrap div -->
                                <header>
                                        <h2>[!Key!]</h2>
                                </header>
                                <div>
                                        <div class="inner-spacer">
                                                <!-- content goes here -->
                                                <fieldset>
                                                        [STORPROC [!E::elements!]|El]
                                                                <div class="control-group [IF [!Error_[!El::name!]!]]error[/IF]">
                                                                        <label class="control-label" for="[!El::name!]">[!El::description!]</label>
                                                                        <div class="controls">
                                                                                [SWITCH [!El::type!]|=]
                                                                                        [CASE image]
                                                                                        <span class="btn btn-success fileinput-button uploadfile-block">
                                                                                                <i class="glyphicon glyphicon-plus"></i>
                                                                                                <span>Select file...</span>
                                                                                                <!-- The file input field used as target for the file upload widget -->
                                                                                                <input id="kefileupload" type="file" name="files" data-url="/Systeme/FileUpload.json">
                                                                                            </span>
                                                                                            <br>
                                                                                            <br>
                                                                                            <!-- The global progress bar -->
                                                                                            <div id="progress" class="progress">
                                                                                                <div class="progress-bar progress-bar-success"></div>
                                                                                            </div>
                                                                                            <input type="text" name="[!El::name!]" value="[!El::value!]" class="pull-right upload-text span12"/>
                                                                                            <!-- The container for the uploaded files -->
                                                                                            <div id="files" class="files">
                                                                                                [IF [!El::value!]]
                                                                                                        <img src="/[!El::value!].mini.250x120.jpg" />
                                                                                                [/IF]
                                                                                            </div>
                                                                                        [/CASE]
                                                                                        [CASE boolean]
                                                                                                <div class="make-switch switch">
                                                                                                    <input type="checkbox" value="1"  name="[!El::name!]" [IF [!El::value!]] checked="checked"[/IF]>
                                                                                                </div>
                                                                                        [/CASE]
                                                                                        [CASE bbcode]
                                                                                                <textarea class="span12 ckeditorbbcode" rows="2" name="[!El::name!]">[!El::value!]</textarea>
                                                                                        [/CASE]
                                                                                        [CASE html]
                                                                                                <textarea class="span12 ckeditorfull" rows="2" name="[!El::name!]">[!El::value!]</textarea>
                                                                                        [/CASE]
                                                                                        [CASE text]
                                                                                                <textarea class="span12" rows="2" name="[!El::name!]">[!El::value!]</textarea>
                                                                                        [/CASE]
                                                                                        [CASE fkey]
                                                                                                //Affichage des liaisons
                                                                                                [OBJ [!El::objectModule!]|[!El::objectName!]|Pa]
                                                                                                <div class="dataItem" data-src="/[!El::objectModule!]/[!El::objectName!]/[!P::ObjectType!]/[!P::Id!]/getJsonDatatable.json" data-module="[!El::objectModule!]" data-objectclass="[!El::objectName!]" data-interface="getJsonDatatable.json"  data-var="listdep_[!El::objectName!]" data-icon="[!Pa::getIcone()!]" data-title="[!Pa::getDescription()!]" data-form="/[!P::getUrl()!]/[!Pa::ObjectType!]" data-description="[!El::description!]" data-key="[!El::name!]"></div>
                                                                                        [/CASE]
                                                                                        [DEFAULT]
                                                                                                <input type="text" class="span12"  name="[!El::name!]" value="[!El::value!]" />
                                                                                        [/DEFAULT]
                                                                                [/SWITCH]
                                                                        </div>
                                                                </div>
                                                        [/STORPROC]
                                                </fieldset>
                                        </div>
                                        <!-- end content-->
                                </div>
                                <!-- end wrap div -->
                        </div>
                        <!-- end widget -->
                </div>
        [/STORPROC]
	</div>
	<div class="form-actions">
		<a type="reset" class="btn medium btn-danger" href="/[!Systeme::CurrentMenu::Url!]">
			Annuler
		</a>
		<button type="submit" class="btn medium btn-primary">
			Enregistrer
		</button>
	</div>
</form>
[HEADER]
<script type="text/javascript">
/*	$(document).ready(function () {
		$.when($( '.ckeditorbbcode' ).ckeditor({
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
		}).promise).then( function() {
			setTimeout(masonryReload, 1000);
		});
		$.when($( '.ckeditorfull' ).ckeditor({
	    		toolbar: 'Basic'
		}).promise).then( function() {
			setTimeout(masonryReload, 1000);
		});
		// layout Masonry again after all images have loaded
		imagesLoaded($("#panel-1"), function() {
			$("#panel-1").masonry();
		});
		//init modal and confirm popup
		launch_confirm_popup(this);
		launch_modal_form_popup(this);
		//file upload
	    // Change this to the location of your server-side upload handler:
	    var url ='/Systeme/FileUpload';
	     $('#kefileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
            	//affiche un paercu de l'image
            	$('#files').empty();
                $('<img src="/'+file.url+'.mini.250x120.jpg" />').appendTo('#files');
                //affiche le chemin de l'image
                 $('.upload-text').val(file.url);
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
	});
	function masonryReload() {
		$("#panel-1").masonry();
	}*/
 </script>
[/HEADER]

