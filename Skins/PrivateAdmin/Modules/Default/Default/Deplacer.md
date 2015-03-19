[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H]
	[SWITCH [!Pos!]|=]
		[CASE 1]
			[STORPROC [!H::Module!]/[!H::DataSource!]/[!H::Value!]|P|0|1][/STORPROC]
		[/CASE]
		[CASE 2]
			[!Module:=[!H::Module!]!]
			[!ObjectClass:=[!H::DataSource!]!]
		[/CASE]
	[/SWITCH]
[/STORPROC]
[OBJ [!Module!]|[!ObjectClass!]|O]
<div class="inner-spacer">
	<table class="table table-striped table-bordered responsive has-checkbox " id="listdep_[!I::ObjectType!]"></table>
	<script type="text/javascript">
			//definition de la globale des parents
			var pars = [
				[STORPROC [!P::getParents([!ObjectClass!])!]|Pars][IF [!Pos!]>1],[/IF][!Pars::Id!][/STORPROC]
			];
			function switchValue(val){
				if (val>0){
					if (pars.indexOf(val)>-1){
						//on le supprime
						pars.splice(pars.indexOf(val),1,pars);
					}else{
						//on l'ajoute
						pars.push(val);
					}
				}
			}
			$('#listdep_[!I::ObjectType!]').dataTable({
				"bProcessing" : true,
				"bServerSide" : true,
				"bAutoWidth": false,
				"bRetrieve": true,
				"bDestroy": true,
				"sAjaxSource" : "/[!Module!]/[!ObjectClass!]/getJsonDatatable.json",
				"aoColumns" : [
				 {
					"mData" : "Id",
					"sTitle" : "",
					"sWidth": '30',
					"mRender" : function(data, type, full) {
							if (pars.indexOf( parseInt(data))>-1)
								return '<input type="checkbox" name="listdep_[!I::ObjectType!]" value="'+data+'" checked="checked" onchange="switchValue('+data+')">';
							else
								return '<input type="checkbox" name="listdep_[!I::ObjectType!]" value="'+data+'" onchange="switchValue('+data+')">';
					}
				},
				[MODULE Systeme/Utils/getListColumns?O=[!O!]]
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
	</script>
</div>