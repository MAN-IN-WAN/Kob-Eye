$(document).ready(function() {

	// Gestion des onglets pour les formulaires de contacts immo
	var onglets;
	if ($("ul.tabs").length !== 0) {
		// setup ul.tabs to work as tabs for each div directly under div.panes
		onglets = $("ul.tabs").tabs("div.panes > div");
	}

	$(".tabs li a").each(function() {
		$(this).attr("class", "");
	});

	// Si ancre #estimate
	if (location.hash === "#estimate") {
		$(".tabs li.estimate a").attr("class", "estimate current");
		$(".tabs li.estimate").attr("class", "on");
	}
	// Si ancre #manage
	else if (location.hash === "#manage") {
		$(".tabs li.manage a").attr("class", "manage current");
		$(".tabs li.manage").attr("class", "on");
	} else {
		//Activation du 1er onglet  par défaut
		$(".tabs a:first").attr("class", "contact current");
		$(".tabs li:first").attr("class", "on");
	}

});
