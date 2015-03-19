<!-- page header -->
<a class="btn btn-large btn-warning pull-right ke-form-modal" href="/Blog/Lien/Form.htm" title="Ajouter un lien">Nouveau lien</a>
<h1 id="page-header">Liste des liens</h1>

<div class="fluid-container">

	<!-- widget grid -->
	<section id="widget-grid" class="">

		<!-- row-fluid -->

		<div class="row-fluid">
			<article class="span12">
				<!-- new widget -->
				<div class="jarviswidget" id="widget-id-0">
					<header>
						<h2>Liste des liens</h2>
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
							<table class="table table-striped table-bordered responsive has-checkbox " id="list_liens">
								<tr>
									<td>Pas de résultats</td>
								</tr>
							</table>
							<script type="text/javascript">
								$(document).ready(function() {
									$('#list_liens').dataTable({
										"bProcessing" : true,
										"bServerSide" : true,
										"bAutoWidth" : false,
										"bRetrieve" : true,
										"bDestroy" : true,
										"sAjaxSource" : "/Blog/Lien/getJsonDatatable.json",
										"aoColumns" : [{
											"mData" : "Titre",
											"sTitle" : "Titre",
											"sWidth" : '20%'
										}, {
											"mData" : "Url",
											"sTitle" : "Url",
											"sWidth" : '23%',
											"mRender" : function(data, type, full) {
												if (data)
													return '<a href="' + data + '" class="btn btn-warning btn-small" target="_blank">Voir le site</a>';
												else
													return 'Pas de site';
											}
										}, {
											"mData" : "Ordre",
											"sTitle" : "Ordre",
											"sWidth" : '10%',
										}, {
											"mData" : "Categorie",
											"sTitle" : "Catégorie",
											"sWidth" : '15%'
										}, {
											"mData" : "Id",
											"mRender" : function(data, type, full) {
												return '<div class="btn-group"><a  class="btn btn-success btn-small ke-form-modal" href="/Blog/Lien/'+data+'/Form.htm" title="Modifier un lien">Editer</a><a href="/[!Lien!]/' + data + '/Supprimer.json" class="confirm btn btn-danger btn-small" title="Etes vous sur de vouloir supprimer ce lien ?">Supprimer</a></div>';
											},
											"sWidth" : '10%'
										}],
										sDom : "<'row-fluid dt-header'<'span6'f><'span6 hidden-phone'T>r>t<'row-fluid dt-footer'<'span6 visible-desktop'i><'span6'p>>",
										sPaginationType : "bootstrap",
										oLanguage : {
											sLengthMenu : "Showing: _MENU_",
											sSearch : ""
										},
										iDisplayLength : 30,
										oTableTools : {
											sSwfPath : "js/include/assets/DT/swf/copy_csv_xls_pdf.swf",
											aButtons : [{
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
											}]
										},
										"fnDrawCallback" : function() {
											launch_confirm_popup(this);
											launch_modal_form_popup(this);
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
			</article>

		</div>

		<!-- end row-fluid -->
	</section>
	<!-- end widget grid -->
</div>
