[INFO [!Query!]|I]
//generation object
[IF [!I::TypeSearch!]=Child]
	//Nouveau
	[OBJ [!I::Module!]|[!I::TypeChild!]|P]
	[!HistoBase:=[!I::Historique!]!]
	[!HistoBase:=[!HistoBase::0!]!]
	[METHOD P|AddParent]
		[PARAM][!HistoBase::Module!]/[!HistoBase::DataSource!]/[!HistoBase::Value!][/PARAM]
	[/METHOD]
	[!TYPE:=NEW!]
[ELSE]
	[STORPROC [!Query!]|P][/STORPROC]
	[!TYPE:=EDIT!]
[/IF]
[!Pref:=POPUP!]


//enregistrement
[IF [!TEST!]]
	//Proprietes
	[STORPROC [!P::Proprietes()!]|Prop]
		[SWITCH [!Prop::type!]|=]
			[CASE date]
				[METHOD P|Set]
					[PARAM][!Prop::Nom!][/PARAM]
					[PARAM][![!Prop::Nom!]Date!] [![!Prop::Nom!]Time!][/PARAM]
				[/METHOD]
			[/CASE]
			[DEFAULT]
				[METHOD P|Set]
					[PARAM][!Prop::Nom!][/PARAM]
					[PARAM][![!Prop::Nom!]!][/PARAM]
				[/METHOD]
			[/DEFAULT]
		[/SWITCH]
	[/STORPROC]
	
	//Parents
	[STORPROC [!P::getParentTypes()!]|Par]
		[IF [![!Par::Nom!]!]>0]
			[METHOD P|AddParent]
				[PARAM][!I::Module!]/[!Par::Titre!]/[![!Par::Nom!]!][/PARAM]
			[/METHOD]
		[/IF]
	[/STORPROC]
	
	//Verification
	[IF [!P::Verify!]]
		//Sauvegarde
		[METHOD P|Save][/METHOD]
		<div class="alert alert-success adjusted">L'élément  a été sauvegardé avec succés</div>
		[CLOSE]1[/CLOSE]
	[ELSE]
		<div class="alert alert-danger adjusted">Des erreurs empêchent la sauvegarde de l'élément:
			<ul>
				[STORPROC [!P::Error!]|E]
				<li>[!E::Message!] [!Error_[!E::Prop!]:=1!]</li>
				[/STORPROC]
			</ul>
		</div>
	[/IF]
[/IF]

<div class="row-fluid">
	<fieldset>
		<input type="hidden" name="TEST" value="asauver" />
		[STORPROC [!P::getElements()!]|E]
			[STORPROC [!E::elements!]/hidden!=1&admin!=1&|El]
				[LIMIT 0|100]
					[MODULE Systeme/Utils/getFormInput?El=[!El!]&P=[!P!]&Pref=[!Pref!]]
				[/LIMIT]
			[/STORPROC]
		[/STORPROC]
	</fieldset>
</div>
<script type="text/javascript">
	$(document).ready(function () {
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
		}).promise);
		$.when($( '.ckeditorfull' ).ckeditor({
	    		toolbar: 'Basic'
		}).promise);
		//init modal and confirm popup
		launch_confirm_popup(this);
		//file upload
		// Change this to the location of your server-side upload handler:
		var url ='/Systeme/FileUpload';
                $('.kefileupload').each(
                    function (index,elem){
                        var id = $(elem).attr('id');
                        $(elem).fileupload({
                            url: url,
                            dataType: 'json',
                            done: function (e, data) {
                                $.each(data.result.files, function (index, file) {
                                    //affiche un paercu de l'image
                                    $('#'+id+'-files').empty();
                                    $('<img src="/'+file.url+'.mini.250x120.jpg" />').appendTo('#'+id+'-files');
                                    //affiche le chemin de l'image
                                     $('#'+id+'-input').val(file.url);
                                });
                            },
                            progressall: function (e, data) {
                                var progress = parseInt(data.loaded / data.total * 100, 10);
                                $('#'+id+'-progress .bar').css(
                                    'width',
                                    progress + '%'
                                );
                            }
                        
                        }).prop('disabled', !$.support.fileInput)
                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                    }
                );
	});
        
 </script>


