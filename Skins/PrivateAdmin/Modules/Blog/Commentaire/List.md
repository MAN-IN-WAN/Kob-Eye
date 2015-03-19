<!-- page header -->
<h1 id="page-header">Liste des commentaires</h1>

<div class="fluid-container">

	<!-- widget grid -->
	<section id="widget-grid" class="">

		<!-- row-fluid -->

		<div class="row-fluid">
			<article class="span12">
				<!-- new widget -->
				<div class="jarviswidget" id="widget-id-0">
					<header>
						<h2>Liste des commentaires</h2>
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
							<table class="table table-striped table-bordered responsive has-checkbox " id="list_commentaire"><tr><td>Pas de résultats</td></tr></table>
							<script type="text/javascript">
								$(document).ready(function() {
									$('#list_commentaire').dataTable({
										"bProcessing" : true,
										"bServerSide" : true,
										"bAutoWidth": false,
										"bRetrieve": true,
										"bDestroy": true,
										"sAjaxSource" : "/Blog/Commentaire/getJsonDatatable.json",
										"aoColumns" : [
											{
												"mData" : "TitrePost",
												"sTitle" : "Titre post",
												"sWidth": '15%'
											},{
												"mData" : "Pseudo",
												"sTitle" : "Pseudo",
												"sWidth": '10%'
											},{
												"mData" : "Mail",
												"sTitle" : "Email",
												"sWidth": '10%'
											},{
												"mData" : "Site",
												"sTitle" : "Site",
												"sWidth": '5%',
												"mRender" : function(data, type, full) {
													if (data)
														return '<a href="' + data + '" class="btn btn-warning btn-small" target="_blank">Voir le site</a>';
													else
														return 'Pas de site';
												}
											}, {
												"mData" : "Comment",
												"sTitle" : "Commentaire",
												"sWidth": '15%'
											}, {
												"mData" : "Moderer",
												"sTitle" : "Modéré",
												"sWidth": '5%',
												"mRender" : function(data, type, full) {
													if (data=="1"){
														return '<div  class="badge btn-success special">OK</a>';
													}else{
														return '<div  class="badge btn-warning special">NO</a>';
													}
												}
											}, {
												"mData" : "Publier",
												"sTitle" : "Publié",
												"sWidth": '5%',
												"mRender" : function(data, type, full) {
													if (data=="1"){
														return '<div  class="badge btn-success special">OK</a>';
													}else{
														return '<div  class="badge btn-danger special">NO</a>';
													}
												}
											}, {
												"mData" : "Date",
												"sTitle" : "Date création",
												"sWidth": '15%'
											}, {
												"mData" : "Id",
												"mRender" : function(data, type, full) {
													return '<div class="btn-group"><a href="/[!Lien!]/' + data + '/Publier.json" class="btn btn-success btn-small confirm" title="Etes vous sur de vouloir publier ce commentaire ?">Publier</a><a href="/[!Lien!]/' + data + '/Moderer.json" class="confirm btn btn-danger btn-small"  title="Etes vous sur de vouloir modérer ce commentaire ?">Modérer</a></div>';
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
			</article>

		</div>

		<!-- end row-fluid -->
	</section>
	<!-- end widget grid -->
</div>
