[INFO [!Query!]|I]
//generation object

[IF [!NBCOL!]=][!NBCOL:=2!][/IF]

[IF [!I::TypeSearch!]=Child]
	//Nouveau
	[OBJ [!I::Module!]|[!I::ObjectType!]|P]
	[!TYPE:=NEW!]
	[ELSE]
	[STORPROC [!Query!]|P][/STORPROC]
	[!TYPE:=EDIT!]
[/IF]
 <!--           <div class="navbar">
                <div class="navbar-inner">
                    <a class="brand" href="#">Title</a>
                    <ul class="nav">
                        <li class="active"><a href="#">Home</a></li>
                        <li><a href="#">Link</a></li>
                        <li><a href="#">Link</a></li>
                    </ul>
                </div>
            </div>
-->
<form class="form-horizontal themed" method="post"  id="form[!I::Module!][!I::ObjectType!]" name="form[!I::Module!][!I::ObjectType!]">
	<input type="hidden" name="submitted" value="1" />
	<div class="tabbable custom-tabs [IF [!VERTICAL!]]tabs-left[/IF] tabs-animated  flat flat-all hide-label-980 shadow track-url auto-scroll">
		<ul class="nav nav-tabs">
                        [STORPROC [!CONF::GENERAL::LANGUAGE!]|L]
			<li [IF [!Pos!]=1]class="active"[/IF]>
				<a href="#panel-[!Pos!]" data-toggle="tab" class="active "><i class="icon-lock"></i>&nbsp;<span>[IF [!Pos!]=1]Propriétés[ELSE]Traductions [!Key!][/IF]</span></a>
			</li>
                        [/STORPROC]
                        [!Po:=[!Pos!]!]
			[STORPROC [!P::getChildTypes()!]|So]
			<li >
				<a href="#panel-[!Pos:+[!Po!]!]" data-toggle="tab" class="active "><i class="icon-lock"></i>&nbsp;<span>[IF [!So::Description!]!=][!So::Description!][ELSE][!So::Titre!][/IF]</span></a>
			</li>
                        [/STORPROC]
		</ul>
		<div class="tab-content ">
                        [STORPROC [!CONF::GENERAL::LANGUAGE!]|L]
			<div class="tab-pane active masonry-container js-masonry" data-masonry-options='{ "columnWidth": ".masonry-item", "itemSelector": ".masonry-item" }'  id="panel-[!Pos!]">
				<div class="masonry-item" style="width:[!Math::Floor([!100:/[!NBCOL!]!])!]%;"></div>
				[STORPROC [!P::getElements([!Key!])!]|E]
					[STORPROC [!E::elements!]/hidden!=1&admin!=1&|El]
                                                [!Cat:=[!P::getCategory([!Key!])!]!]
						<div class="masonry-item" style="[IF [!Cat::type!]=large]width:[!Math::Floor([![!100:/[!NBCOL!]!]:*2!])!][ELSE]width:[!Math::Floor([!100:/[!NBCOL!]!])!][/IF]%;">
	
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
											[LIMIT 0|100]
                                                                                            [MODULE Systeme/Utils/getFormInput?El=[!El!]&P=[!P!]]
    											[/LIMIT]
										</fieldset>
									</div>
									<!-- end content-->
								</div>
								<!-- end wrap div -->
							</div>
							<!-- end widget -->
						</div>
					[/STORPROC]
				[/STORPROC]
			</div>
                        [/STORPROC]
                        [!Po:=[!Pos!]!]
			[STORPROC [!P::getChildTypes()!]|So]
			<div class="tab-pane" id="panel-[!Pos:+[!Po!]!]">
                            <div class="" style="width:100%">
                                    <!-- new widget -->
                                    <div class="jarviswidget row-fluid" id="widget-id-[!So::Titre!]">
                                            <!-- wrap div -->
                                            <header>
                                                    <h2>[IF [!So::Description!]!=][!So::Description!][ELSE][!So::Titre!][/IF]</h2>
                                            </header>

                                            <div>
                                                    <div class="inner-spacer">
                                                            <!-- content goes here -->
                                                            [INFO [!P::Module!]/[!So::Titre!]|I]
                                                            [OBJ [!I::Module!]|[!I::ObjectType!]|O]
                                                            <a class="btn btn-large btn-warning pull-right ke-form-modal" href="/[!I::Module!]/[!P::ObjectType!]/[!P::Id!]/[!So::Titre!]/Form.htm" title="Ajouter un [!So::Titre!]" style="margin:10px;">Nouveau [!So::Titre!]</a>
//                                                            <a class="btn btn-large btn-warning pull-right" href="/[!Systeme::CurrentMenu::Url!]/[!P::Id!]/[!So::Titre!]/Fiche" title="Ajouter un lien" style="margin:10px;">Nouveau [!So::Titre!]</a>
                                                            <table class="table table-striped table-bordered responsive has-checkbox " id="list_[!So::Titre!]"></table>
                                                            <script type="text/javascript">
                                                                    $(document).ready(function() {
                                                                            $('#list_[!So::Titre!]').dataTable({
                                                                                    "bProcessing" : true,
                                                                                    "bServerSide" : true,
                                                                                    "bAutoWidth": false,
                                                                                    "bRetrieve": true,
                                                                                    "bDestroy": true,
                                                                                    "sAjaxSource" : "/[!I::Module!]/[!P::ObjectType!]/[!P::Id!]/[!So::Titre!]/getJsonDatatable.json",
                                                                                    "aoColumns" : [
                                                                                            [MODULE Systeme/Utils/getListColumns?O=[!O!]]
                                                                                            , {
                                                                                                    "mData" : "Id",
                                                                                                    "mRender" : function(data, type, full) {
                                                                                                            return '<div class="btn-group"><a  class="btn btn-success btn-small ke-form-modal" href="/[!I::Module!]/[!P::ObjectType!]/[!P::Id!]/[!So::Titre!]/'+data+'/Form.htm" title="Modifier un [!So::Titre!]">Editer</a><a href="/[!I::Module!]/[!P::ObjectType!]/[!P::Id!]/[!So::Titre!]/' + data + '/Supprimer.json" class="confirm btn btn-danger btn-small" title="Etes vous sur de vouloir supprimer ce [!So::Titre!] ?">Supprimer</a></div>';
                                                                                                    },
                                                                                                    "sWidth": '10%'
                                                                                            }
                                                                                    ],
                                                                                    sDom : "<'row-fluid dt-header'<'span6'f><'span6 hidden-phone'T>r>t<'row-fluid dt-footer'<'span6 visible-desktop'i><'span6'p>>",
                                                                                    sPaginationType : "bootstrap",
                                                                                    oLanguage : {
                                                                                            sLengthMenu : "Showing: _MENU_",
                                                                                            sSearch : ""
                                                                                    },
                                                                                    "oTableTools": {
                                                                                            "aButtons": [  ]
                                                                                    },
                                                                                    iDisplayLength : 30,
                                                                                    "fnDrawCallback": function () {
                                                                                            launch_confirm_popup(this);
                                                                                            launch_modal_form_popup(this);
                                                                                    }
                                                                            });
                                                                    });
                                                            </script>							
                                                    </div>
                                            </div>
                                    </div>
                            </div>
        		</div>
			[/STORPROC]
		</div>
	</div>
	<div class="form-actions" data-spy="affix" data-offset-bottom="0">
		<a type="reset" class="btn medium btn-danger" href="/[!Systeme::CurrentMenu::Url!]" id="reset-[!I::Module!]-[!I::ObjectType!]">
			Annuler
		</a>
		<button type="submit" class="btn medium btn-primary" id="submit-[!I::Module!]-[!I::ObjectType!]">
			Enregistrer
		</button>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function () {
            $('form').submit(function (e){
                e.preventDefault();
                //alert('#form[!I::Module!][!I::ObjectType!]'+$("form").html('id'));
                $.post( "/[!Query!]/FormSave.json", $('form').serialize() );
            });
        });
</script>
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
        
	function masonryReload() {
		$("#panel-1").masonry();
	}
 </script>

