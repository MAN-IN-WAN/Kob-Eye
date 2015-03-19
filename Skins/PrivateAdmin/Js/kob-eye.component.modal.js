
/* end window.load functions */
/* ---------------------------------------------------------------------- */
/*	Confirm Popup functions
 /* ---------------------------------------------------------------------- */

function launch_confirm_popup(t) {
	$(".confirm").each(function (index,item){
		$(item).unbind('click');
		$(item).click(function (e){
			//lancement de la popup de confirmation
			e.preventDefault();
			bootbox.confirm($(item).attr('title'), function(result) {
				if (result){
					$.getJSON( $(item).attr('href'), {} )
					.done(function( json ) {
						if (json.success){
							if (t){
								t._fnAjaxUpdate();
							}
							toastr.success(json.message);
						}else
							toastr.error("Une erreur serveur est survenue");
					})
					.fail(function( jqxhr, textStatus, error ) {
						var err = textStatus + ', ' + error;
						toastr.error("Une erreur est survenue: "+err);
					});
				}else 
					toastr.warning("Opération annulée");
			});
		});
	});
}
/* end confirm popup functions */
/* ---------------------------------------------------------------------- */
/*	Confirm Popup functions
 /* ---------------------------------------------------------------------- */

function launch_modal_form_popup(t) {
	var $modal = $('#modalForm');
	$(".ke-form-modal").each(function (index,item){
		$(item).unbind('click');
		$(item).on('click', function(e) {
			e.preventDefault();
			// create the backdrop and wait for next modal to be triggered
			$('body').modalmanager('loading');
			var mode="form"; 
			if ($(item).attr('data-var')){
				//mode appel class
				mode="var";
				var variable = $(item).attr('data-var');
			}else{
				//mode validation form
				$modal.attr('data-source',$(item).attr('href'));
			}
			$modal.find('#modalFormLabel').html($(item).attr('title'));
			$modal.find('.modal-body').load($(item).attr('href'), function() {
				$modal.modal();
			});
			//recuperation du bouton de validation
			var sub = $modal.find('.save');
			sub.unbind('click');
			sub.on('click', function(e) {
				e.preventDefault();
				var b=$modal.find('.modal-body');
				$(b).each(function (index,item2) {
					if (mode=="form"){
						//récupération des valeurs du formulaire
						var a=$modal.children('.modal-form').serialize();
						//envoi du formulaire
						$(item2).load($modal.attr('data-source'),a, function(responseText, textStatus, XMLHttpRequest) {
							if (parseInt(responseText)==1){
								//fermeture du popup
								$modal.modal('hide');
								//rafraichissement de la liste 
								if (t){
									t._fnAjaxUpdate();
								}
								//affichage du toaster
								toastr.success('Modification éxécutée avec succés.');
							}else $modal.modal();
						});
					}else{
						//récupération des valeurs du formulaire
						var a=$modal.children('.modal-form').serializeArray();
						//envoi d'un evenement sur l'élément actionné
						$(item).trigger( "form_valid",[a]);
						$modal.modal('hide');
					}
				});
			});
		});
	});
}
/* end confirm popup functions */
	/* launch confirm popup */
	launch_confirm_popup();
	
	/* launch modal form popup */
	launch_modal_form_popup()
