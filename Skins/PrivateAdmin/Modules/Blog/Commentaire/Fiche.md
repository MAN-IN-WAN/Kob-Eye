<!-- page header -->
[INFO [!Query!]|I]
//generation object
[IF [!I::TypeSearch!]=Child]
	//Nouveau
	[OBJ [!I::Module!]|[!I::TypeChild!]|P]
	[!TYPE:=NEW!]
[ELSE]
	[STORPROC [!Query!]|P][/STORPROC]
	[!TYPE:=EDIT!]
[/IF]

//enregistrement
[IF [!submitted!]]
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
		[IF [!TYPE!]=NEW]
			[REDIRECT][!Systeme::CurrentMenu::Url!]/[!P::Id!]?message=success[/REDIRECT]
		[/IF]
		<div class="alert alert-success adjusted">La catégorie a été sauvegardé avec succés</div>
	[ELSE]
		<div class="alert alert-danger adjusted">Des erreurs empêchent la sauvegarde de la catégorie:
			<ul>
				[STORPROC [!P::Error!]|E]
				<li>[!E::Message!] [!Error_[!E::Prop!]:=1!]</li>
				[/STORPROC]
			</ul>
		</div>
	[/IF]
[/IF]

//Message success apres redirect
[IF [!message!]=success]
		<div class="alert alert-success adjusted">La catégorie a été sauvegardé avec succés</div>
[/IF]


<a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-large btn-warning pull-right">Retour à la liste des catégorie</a>
[IF [!TYPE!]=NEW]
	<h1 id="page-header">Nouvelle catégorie</h1>
[ELSE]
	<h1 id="page-header">Edition de la catégorie [!P::Titre!]</h1>
[/IF]
<form class="form-horizontal themed" method="post">
	<input type="hidden" name="submitted" value="1" />
	<div class="fluid-container">
		<!-- widget grid -->
//		<section id="widget-grid" class="">
			<!-- row-fluid -->
			<div class="row-fluid">
				<article class="span7">
					<!-- new widget -->
					<div class="jarviswidget" id="widget-id-0">
						<!-- wrap div -->
						<header>
							<h2>Edition du contenu</h2>
						</header>
						<div>
							<div class="jarviswidget-editbox">
								<div>
									<label>Title:</label>
									<input type="text" />
								</div>
								<div>
									<label>Styles:</label>
									<span data-widget-setstyle="purple" class="purple-btn"></span>
									<span data-widget-setstyle="navyblue" class="navyblue-btn"></span>
									<span data-widget-setstyle="green" class="green-btn"></span>
									<span data-widget-setstyle="yellow" class="yellow-btn"></span>
									<span data-widget-setstyle="orange" class="orange-btn"></span>
									<span data-widget-setstyle="pink" class="pink-btn"></span>
									<span data-widget-setstyle="red" class="red-btn"></span>
									<span data-widget-setstyle="darkgrey" class="darkgrey-btn"></span>
									<span data-widget-setstyle="black" class="black-btn"></span>
								</div>
							</div>
							<div class="inner-spacer">
								<!-- content goes here -->
								<fieldset>
									<div class="control-group [IF [!Error_Titre!]]error[/IF]">
										<label class="control-label" for="input01">Titre</label>
										<div class="controls">
											<input type="text" class="span12"  name="Titre" value="[!P::Titre!]" />
										</div>
									</div>
									<textarea class="span12 ckeditorfull" rows="10" name="Description">[!P::Description!]</textarea>
								</fieldset>
							</div>
							<!-- end content-->
						</div>
						<!-- end wrap div -->
					</div>
					<!-- end widget -->
				</article>
				<article class="span5">
					<!-- new widget -->
					<div class="jarviswidget" id="widget-id-1">
						<!-- wrap div -->
						<header>
							<h2>Paramètres de publication</h2>
						</header>
						<div>

							<div class="jarviswidget-editbox">
								<div>
									<label>Title:</label>
									<input type="text" />
								</div>
								<div>
									<label>Styles:</label>
									<span data-widget-setstyle="purple" class="purple-btn"></span>
									<span data-widget-setstyle="navyblue" class="navyblue-btn"></span>
									<span data-widget-setstyle="green" class="green-btn"></span>
									<span data-widget-setstyle="yellow" class="yellow-btn"></span>
									<span data-widget-setstyle="orange" class="orange-btn"></span>
									<span data-widget-setstyle="pink" class="pink-btn"></span>
									<span data-widget-setstyle="red" class="red-btn"></span>
									<span data-widget-setstyle="darkgrey" class="darkgrey-btn"></span>
									<span data-widget-setstyle="black" class="black-btn"></span>
								</div>
							</div>
							<div class="inner-spacer">
								<!-- content goes here -->
								<fieldset>
									<div class="control-group">
										<label class="control-label">Visibilité</label>
										<div class="controls">
											<label class="checkbox">
												<input type="checkbox" name="Publier" value="1" [IF [!P::Publier!]]checked="checked"[/IF]>
												Publier </label>
										</div>
									</div>
								</fieldset>
							</div>
							<!-- end content-->
						</div>
						<!-- end wrap div -->
					</div>
					<!-- end widget -->
					<!-- new widget -->
					<div class="jarviswidget" id="widget-id-1">
						<!-- wrap div -->
						<header>
							<h2>Paramètres de référencement</h2>
						</header>
						<div>
							<div class="jarviswidget-editbox">
								<div>
									<label>Title:</label>
									<input type="text" />
								</div>
								<div>
									<label>Styles:</label>
									<span data-widget-setstyle="purple" class="purple-btn"></span>
									<span data-widget-setstyle="navyblue" class="navyblue-btn"></span>
									<span data-widget-setstyle="green" class="green-btn"></span>
									<span data-widget-setstyle="yellow" class="yellow-btn"></span>
									<span data-widget-setstyle="orange" class="orange-btn"></span>
									<span data-widget-setstyle="pink" class="pink-btn"></span>
									<span data-widget-setstyle="red" class="red-btn"></span>
									<span data-widget-setstyle="darkgrey" class="darkgrey-btn"></span>
									<span data-widget-setstyle="black" class="black-btn"></span>
								</div>
							</div>
							<div class="inner-spacer">
								<!-- content goes here -->
								<fieldset>
									<div class="control-group">
										<label class="control-label" for="input01">Meta Title</label>
										<div class="controls">
											<input type="text" class="span12" value="[!P::TitleMeta!]" name="TitleMeta"/>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="textarea">Meta Description</label>
										<div class="controls">
											<textarea class="span12" rows="2" name="DescriptionMeta">[!P::DescriptionMeta!]</textarea>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="textarea">Meta Keywords</label>
										<div class="controls">
											<textarea class="span12" rows="2" name="KeywordsMeta">[!P::KeywordsMeta!]</textarea>
										</div>
									</div>
								</fieldset>
							</div>
							<!-- end content-->
						</div>
						<!-- end wrap div -->
					</div>
					<!-- end widget -->
				</article>
			</div>
			<!-- end row-fluid -->
			<div class="form-actions">
				<a type="reset" class="btn medium btn-danger" href="/[!Systeme::CurrentMenu::Url!]">
					Annuler
				</a>
				<button type="submit" class="btn medium btn-primary">
					Enregistrer
				</button>
			</div>
//		</section>
		<!-- end widget grid -->
	</div>
</form>
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
