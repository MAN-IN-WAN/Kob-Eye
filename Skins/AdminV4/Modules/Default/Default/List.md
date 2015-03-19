<!-- page header -->
[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::ObjectType!]|O]
//<a href="/[!Systeme::CurrentMenu::Url!]/Fiche" class="btn btn-large btn-warning pull-right">Ajouter [!O::getDescription!]</a>
//<h1 id="page-header">[!Systeme::CurrentMenu::Titre!]</h1>

	<!-- widget grid -->
	<section id="widget-grid" class="">

				<!-- new widget -->
				<div class="jarviswidget" id="widget-id-0">
					<header>
						<h2>[!Systeme::CurrentMenu::Titre!]</h2>
					</header>
					<!-- wrap div -->
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
							<table class="table table-striped table-bordered responsive has-checkbox " id="list_[!I::ObjectType!]"></table>
							<script type="text/javascript">
								$(document).ready(function() {
									$('#list_[!I::ObjectType!]').dataTable({
										"bProcessing" : true,
										"bServerSide" : true,
										"bAutoWidth": false,
										"bRetrieve": true,
										"bDestroy": true,
										"sAjaxSource" : "/[!I::Module!]/[!I::ObjectType!]/getJsonDatatable.json",
										"aoColumns" : [
											[MODULE Systeme/Utils/getListColumns?O=[!O!]]
											, {
												"mData" : "Id",
												"mRender" : function(data, type, full) {
													return '<div class="btn-group pull-right"><a href="/[!Lien!]/' + data + '" class="btn btn-success btn-small">Editer</a><a href="/[!Lien!]/' + data + '/Supprimer.json" class="confirm btn btn-danger btn-small" title="Etes vous sur de vouloir supprimer ce post ?">Supprimer</a></div>';
												},
												"sWidth": '15%'
											}
										],
										sDom : "<'row-fluid dt-header'<'span6'f><'span6 hidden-phone'T>r>t<'row-fluid dt-footer'<'span6 visible-desktop'i><'span6'p>>",
										sPaginationType : "bootstrap",
										oLanguage : {
											sLengthMenu : "Showing: _MENU_",
											sSearch : ""
										},
										iDisplayLength : 30,
										oTableTools : {
											sSwfPath : "js/include/assets/DT/swf/copy_csv_xls_pdf.swf",
											aButtons : [/*{
												sExtends : "print",
												sButtonText : '<i class="cus-printer oTable-adjust"></i> Print View'
											}, {
												sExtends : "pdf",
												sPdfOrientation : "landscape",
												sPdfMessage : "Your custom message would go here.",
												sButtonText : '<i class="cus-doc-pdf oTable-adjust"></i> Save to PDF'
											}, {
												sExtends : "xls",
												sButtonText : '<i class="cus-doc-excel-table oTable-adjust"></i> Save for Excel'
											}*/]
										},
										"fnDrawCallback": function () {
											launch_confirm_popup(this);
									        }
									});
								});
							</script>
						</div>
						<!-- end content-->
					</div>
					<!-- end wrap div -->
				</div>
				<!-- end widget -->
	</section>
	<!-- end widget grid -->
</div>
