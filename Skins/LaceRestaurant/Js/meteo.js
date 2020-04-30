$(document).ready(function() {

	function decode(str) {
		var temp = document.createElement("pre");
		temp.innerHTML = str;
		return temp.firstChild.nodeValue;
	}

	// charge ved
	var ved = $("#right .bloc.ved, .center-right .bloc.ved, #sidebar .bloc.ved");

	if (ved.length > 0) {
		if (ved.attr('id') !== undefined && ved.attr('id') !== "") {
			// TODO remettre la bonne URL
			// var urlVED = '/meteo-service/' + ved.attr('id');
			var urlVED = ved.attr('url')+ ved.attr('id');
			$.ajax({
				url : urlVED,
				dataType : "xml",
				beforeSend : function() {
					ved.hide();
				},
				error : function() {
					ved.empty().hide();
					$(".vivreIci").css("display", "none");
					$("#vivreIci").css("display", "none");
				},
				success : function(xml) {
					if (xml !== '') {
						var language = ($("meta[content=en]").length === 1 ? 'en' : 'fr');
						var today_label;
						var new_window_label;

						if (language === 'en') {
							today_label = 'Now in ';
							new_window_label = 'new window';
						} else {
							today_label = 'En direct de ';
							new_window_label = 'nouvelle fenêtre';
						}

						var $meteo = $("#vivreIci");
						var xmlj = $(xml);
						var statutMeteo = xmlj.find("codeRetourMeteo").text();
						if (statutMeteo === "1") {
							/* Get data from xml */
							var tempMin = Math.round(xmlj.find("temperatureMin").text()) + " C&deg; min";
							var tempMax = Math.round(xmlj.find("temperatureMax").text()) + " C&deg; max";
							var ville = xmlj.find("libelleLocalite").text();
							var icon = xmlj.find("icon").text();
							var lienPrev = xmlj.find("lienMeteo").text();
							var lienCinema = xmlj.find("lienCinema").text();
							var lienSortie = xmlj.find("lienSortie").text();
							var lienPlan = xmlj.find("lienPlanItineraireTrafic").text();
							var commentaire = ((xmlj.find("commentaireMeteo").text())  ? decode(xmlj.find("commentaireMeteo").text()) : '');

							/* Set data in template */
							if (tempMin !== "") {
								$meteo.find(".temperatureMin").html(tempMin);
							}
							if (tempMax !== "") {
								$meteo.find(".temperatureMax").html(tempMax);
							}
							if (ville !== "") {
								$meteo.find(".titre").html(today_label + ville);
								$meteo.find(".titre").attr("title", today_label + ville + " (" + new_window_label + ")");
							}
							if (lienPrev !== "") {
								$meteo.find(".previsions").attr("href", lienPrev);
							}

							if (icon !== "" && lienPrev !== "") {
								meteoBloc = $("#meteo");
								var meteoLink = '<a href="' + lienPrev + '" class="href_img lien_meteo" target="_blank" title="' + commentaire + '">';
								meteoLink += '<img class="icone" src="' + icon + '" alt="' + commentaire + '" />';
								meteoLink += '</a>';

								meteoBloc.append(meteoLink);
							}

							// Do not use ternary operator as we do not need va	lue
							if (lienCinema !== "") {
								$meteo.find(".lien_cinema").attr("href", lienCinema);
							} else {
								$meteo.find(".lien_cinema").css("display", "none");
							}

							if (lienPlan !== "") {
								$meteo.find(".lien_plan").attr("href", lienPlan);
							} else {
								$meteo.find(".lien_plan").css("display", "none");
							}

							if (lienSortie !== "") {
								$meteo.find(".lien_sortie").attr("href", lienSortie);
							} else {
								$meteo.find(".lien_sortie").css("display", "none");
							}

							ved.show();
							$meteo.show();
							$("#vivreIci").show();
						} else {
							$(".vivreIci").css("display", "none");
							$("#vivreIci").css("display", "none");
						}
					}

				}
			});

		}
	}
});
