$(document).ready(function () {
	
	Shadowbox.init();

	toastr.options = {
		tapToDismiss : true,
		toastClass : 'toast',
		containerId : 'toast-container',
		debug : false,
		fadeIn : 250,
		fadeOut : 200,
		extendedTimeOut : 0,
		iconClasses : {
			error : 'toast-error',
			info : 'toast-info',
			success : 'toast-success',
			warning : 'toast-warning'
		},
		iconClass : 'toast-info',
		positionClass : 'toast-top-right',
		timeOut : 4500, // Set timeOut to 0 to make it sticky
		titleClass : 'toast-title',
		messageClass : 'toast-message'
	};

	

});
/*-- mega menu --*/
function gestioncouleurfond(lacouleur,quoi) {
	
	$(quoi).setStyle('background-color', lacouleur);

}