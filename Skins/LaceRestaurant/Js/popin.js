/**
 * Gestion des popus & popins
 */

// expression régulière pour tester la validité d'un e-mail
var mail = new RegExp(/^([a-zA-Z0-9._\-]+@[a-zA-Z0-9._\-]{3,}[; ]*)+$/i);
var mail1 = new RegExp(/^(("[\w\-\s]+")|([\w\-]+(?:\.[\w\-]+)*)|("[\w\-\s]+")([\w\-]+(?:\.[\w\-]+)*))(@((?:[\w\-]+\.)*\w[\w\-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
var tel_reg = new RegExp(/^0[1-7][0-9]{8}$/i);

// Validation du formulaire de validation de la popin Recommander par mail (tmplt 1ere generation + tmplt HTM5)
function validFormRecommander(link) {
	var alerte = "";

	var nom = $.trim($("input[name=re_nom]").val());
	if (nom === "") {
		alerte += _TEXTES.nom + "\n";
	}

	var email = $.trim($("input[name=re_email]").val());
	if (email === "") {
		alerte += _TEXTES.email + "\n";
	}
	if (!mail.test(email) && email !== "") {
		alerte += _TEXTES.email_err + "\n";
	}

	nom = $.trim($("input[name=re_nomDestinataire]").val());
	if (nom === "") {
		alerte += _TEXTES.destinataireNom + "\n";
	}

	email = $.trim($("input[name=re_emailDestinataire]").val());
	if (email === "") {
		alerte += _TEXTES.destinataireEmail + "\n";
	}
	if (!mail.test(email) && email !== "") {
		alerte += _TEXTES.email_err + "\n";
	}

	if ($("#secondDestinataire").css("display") === "block") {
		nom = $.trim($("input[name=re_nomDestinataire2]").val());
		if (nom === "") {
			alerte += _TEXTES.destinataireNom2 + "\n";
		}

		email = $.trim($("input[name=re_emailDestinataire2]").val());
		if (email === "") {
			alerte += _TEXTES.destinataireEmail2 + "\n";
		}
		if (!mail.test(email) && email !== "") {
			alerte += _TEXTES.email_err + "\n";
		}
	}

	var captcha = $.trim($("input[name=re_captcha]").val());
	if (captcha === "") {
		alerte += _TEXTES.captcha + "\n";
	}

	if (alerte !== "") {
		alert(alerte);
		return false;
	} else {
		var url_recommander = "/plugins/api/captcha/mail/sendRecommandation";
		var data_recommander = {
			nom : $.trim($("input[name=re_nom]").val()),
			email : $.trim($("input[name=re_email]").val()),
			nom_dest : $.trim($("input[name=re_nomDestinataire]").val()),
			email_dest : $.trim($("input[name=re_emailDestinataire]").val()),
			comments : $.trim($("textarea[name=re_commentaires]").val()),
			raison : _DATA.corporate_name,
			url : _DATA.url,
			nom_dest2 : $.trim($("input[name=re_nomDestinataire2]").val()),
			email_dest2 : $.trim($("input[name=re_emailDestinataire2]").val()),
			captcha : $.trim($("input[name=re_captcha]").val()),
			captcha_id : $.trim($("input[name=re_captcha_id]").val())
		};
		// Dans le cas de la recommandation
		// d'un bon plan, on vérifie que le lien recommander
		// possede aussi la classe partager
		// Et on envoie de nouvelles informations en plus
		if (link.hasClass("partager")) {
			url_recommander = "/plugins/api/captcha/mail/sendRecommandationBP";
			data_recommander.bp = JSON.stringify(_DATA.bon_plan_actif);
			data_recommander.current_page = _DATA.bon_plan_actif_currentPage,
			data_recommander.tel = _DATA.tel;
			data_recommander.accroche = _DATA.bon_plan_actif.accroche;
			data_recommander.id_bp = _DATA.bon_plan_actif.id_bp;
			data_recommander.date_fin_validite = _DATA.bon_plan_actif.date_fin_validite;
			data_recommander.address = $('.adresse[itemprop="address"]').html();
		}

		ret = true;
		$.ajax({
			url : url_recommander,
			type : 'POST',
			async : false,
			data : data_recommander,
			success : function() {
				alert(_TEXTES.msg_ok);
			},
			error : function(data) {
				if (data.responseText === "bad captcha") {
					alert(_TEXTES.captcha_err);
					//$(".captcha img").attr("src", "/plugins/api/captcha/captcha.png?" + new Date().getTime());
					refill_captcha();
				} else {
					alert(_TEXTES.msg_ko);
				}
				ret = false;
			}
		});
		return ret;
	}
}

// Validation du formulaire de validation de la popin Recommander par SMS (tmplt 1ere generation + tmplt HTM5)
function validFormRecommanderSms(link) {
	var alerte = "";

	var nom = $.trim($("input[name=sms_nom]").val());
	if (nom === "") {
		alerte += _TEXTES.nom + "\n";
	}

	var telephone = $.trim($("input[name=sms_numero]").val());
	if (telephone === "") {
		alerte += _TEXTES.telephone + "\n";
	}
	if (!tel_reg.test(telephone) && telephone !== "") {
		alerte += _TEXTES.telephone_err + "\n";
	}

	nom = $.trim($("input[name=sms_nomDestinataire]").val());
	if (nom === "") {
		alerte += _TEXTES.destinataireNom + "\n";
	}

	telephone = $.trim($("input[name=sms_numeroDestinataire]").val());
	if (telephone === "") {
		alerte += _TEXTES.telephone + "\n";
	}
	if (!tel_reg.test(telephone) && telephone !== "") {
		alerte += _TEXTES.telephone_err + "\n";
	}

	if ($("#secondDestinataire").css("display") === "block") {
                nom = $.trim($("input[name=sms_nomDestinataire2]").val()) !== "" ? $.trim($("input[name=sms_nomDestinataire2]").val()) : $.trim($("input[name=re_nomDestinataire2]").val());
		if (nom === "") {
			alerte += _TEXTES.destinataireNom2 + "\n";
		}
                
		telephone = $.trim($("input[name=sms_numeroDestinataire2]").val())!=="" ? $.trim($("input[name=sms_numeroDestinataire2]").val()) : $.trim($("input[name=re_emailDestinataire2]").val());
		if (telephone === "") {
			alerte += _TEXTES.telephone + "\n";
		}
		if (!tel_reg.test(telephone) && telephone !== "") {
			alerte += _TEXTES.telephone_err + "\n";
		}
	}

	var captcha = $.trim($("input[name=sms_captcha]").val());
	if (captcha === "") {
		alerte += _TEXTES.captcha + "\n";
	}

	if (alerte !== "") {
		alert(alerte);
		return false;
	} else {
		var url_sms = "/plugins/api/captcha/sms/send";
                
                var nom_dest2 = $.trim($("input[name=sms_nomDestinataire2]").val()) !== "" ? $.trim($("input[name=sms_nomDestinataire2]").val()) : $.trim($("input[name=re_nomDestinataire2]").val());
                var tel_dest2 = $.trim($("input[name=sms_numeroDestinataire2]").val())!=="" ? $.trim($("input[name=sms_numeroDestinataire2]").val()) : $.trim($("input[name=re_emailDestinataire2]").val());
                  
		var data_sms = {
			nom : $.trim($("input[name=sms_nom]").val()),
			tel : $.trim($("input[name=sms_numero]").val()),
			nom_dest : $.trim($("input[name=sms_nomDestinataire]").val()),
			tel_dest : $.trim($("input[name=sms_numeroDestinataire]").val()),
			raison : _DATA.corporate_name,
			url : _DATA.url,
			tel_pvi : _DATA.tel,
			cp : _DATA.cp,
			loc : _DATA.loc,
			nom_dest2 : nom_dest2,
			tel_dest2 : tel_dest2,
			comments : $.trim($("textarea[name=sms_commentaires]").val()),
			captcha : $.trim($("input[name=sms_captcha]").val()),
			captcha_id : $.trim($("input[name=re_captcha_id]").val())
		};
		/**
		 * Dans le cas de partage d'un bon plan par SMS
		 * on vérifie que le lien possede la classe partager
		 * Si c'est le cas, on envoie de nouvelles données
		 * en plus
		 */
		if (link.hasClass("partager")) {
			url_sms = "/plugins/api/captcha/sms/sendBP";
			data_sms.accroche = _DATA.bon_plan_actif.accroche;
			data_sms.id_bp = _DATA.bon_plan_actif.id_bp;
			data_sms.current_page = _DATA.bon_plan_actif_currentPage;
		}
		ret = true;
		$.ajax({
			url : url_sms,
			type : 'POST',
			async : false,
			data : data_sms,
			success : function() {
				xt_click(this, 'C', '', _DATA.corporate_name + '::Envoyer_second_bouton&amp;pjpvi=' + _DATA.pvi_id_oda + '&amp;pjann=' + _DATA.client_id_oda + '&amp;pjconv=3', 'S');
				alert(_TEXTES.sms_ok);
			},
			error : function(data) {
				if (data.responseText === "bad captcha") {
					alert(_TEXTES.captcha_err);
					$(".captcha img").attr("src", "/plugins/api/captcha/captcha.png?" + new Date().getTime());
					refill_captcha();
				} else {
					alert(_TEXTES.sms_ko);
				}
				ret = false;
			}
		});
		return ret;
	}
}

// Validation du formulaire de validation de la popin En-profiter pour Bon Plan (tmplt 1ere generation + tmplt HTM5)
function validFormEnProfiter() {
	var alerte = "";
	var tel_reg = new RegExp(/^0[1-7][0-9]{8}$/i);

	telephone = $.trim($("input[name=tel_dest]").val());
	if (telephone === "") {
		alerte += _TEXTES.telephone + "\n";
	}
	if (!tel_reg.test(telephone) && telephone !== "") {
		alerte += _TEXTES.telephone_err + "\n";
	}

	var captcha = $.trim($("input[name=re_captcha]").val());
	if (captcha === "") {
		alerte += _TEXTES.captcha + "\n";
	}

	if (alerte !== "") {
		alert(alerte);
		return false;
	} else {
		ret = true;
		// On envoie les données au WS permettant d'envoyer un SMS
		$.ajax({
			url : "/plugins/api/captcha/sms/sendEnProfiterBP",
			type : 'POST',
			async : false,
			data : {
				tel_dest : $.trim($("input[name=tel_dest]").val()),
				raison : _DATA.corporate_name,
				url : _DATA.url,
				loc : _DATA.loc,
				accroche : _DATA.bon_plan_actif.accroche,
				current_page : _DATA.bon_plan_actif_currentPage,
				id_bp : _DATA.bon_plan_actif.id_bp,
				captcha : $.trim($("input[name=re_captcha]").val()),
				captcha_id : $.trim($("input[name=re_captcha_id]").val())
			},
			success : function() {
				alert(_TEXTES.sms_ok);
			},
			error : function(data) {
				if (data.responseText === "bad captcha") {
					alert(_TEXTES.captcha_err);
					refill_captcha();
				} else {
					alert(_TEXTES.sms_ko);
				}
				ret = false;
			}
		});
		return ret;
	}
}

//Validation du formulaire d'envoi du plan (tmplt 1ere generation + tmplt HTM5)
function validFormEnvoyerPlan() {

	var alerte = "";
	// expéditeur
	var nomS = $.trim($("input[name=nom]").val());
	if (nomS === "") {
		alerte += _TEXTES.nom + "\n";
	}

	var emailS = $.trim($("input[name=emailSource]").val());
	if (emailS === "") {
		alerte += _TEXTES.email + "\n";
	} else if (!mail1.test(emailS)) {
		alerte += _TEXTES.email_err + "\n";
	}

	// destinataire
	var nom = $.trim($("input[name=nomDest]").val());
	if (nom === "") {
		alerte += _TEXTES.destinataireNom + "\n";
	}

	var email = $.trim($("input[name=emailDest]").val());
	if (email === "") {
		alerte += _TEXTES.destinataireEmail + "\n";
	} else if (!mail1.test(email)) {
		alerte += _TEXTES.email_err + "\n";
	}

	// deuxième destinataire
	var nom2 = "";
	var email2 = "";
	if ($("#secondDestinataire").css("display") === "block") {
		nom2 = $.trim($("input[name=re_nomDestinataire2]").val());
		if (nom2 === "") {
			alerte += _TEXTES.destinataireNom2 + "\n";
		}

		email2 = $.trim($("input[name=re_emailDestinataire2]").val());
		if (email2 === "") {
			alerte += _TEXTES.destinataireEmail2 + "\n";
		} else if (!mail1.test(email2)) {
			alerte += _TEXTES.email_err + "\n";
		}
	}
	// captcha
	var captcha = $.trim($("input[name=re_captcha]").val());
	if (captcha === "") {
		alerte += _TEXTES.captcha + "\n";
	}

	if (alerte !== "") {
		alert(alerte);
		return false;
	} else {
		ret = true;
		$.ajax({
			url : "/plugins/api/captcha/mail/sendPlan",
			type : 'POST',
			async : false,
			data : {
				// expéditeur
				nom : nomS,
				email : emailS,
				// destinataire
				nom_dest : nom,
				email_dest : email,
				// deuxième destinataire
				nom_dest2 : nom2,
				email_dest2 : email2,
				comments : $.trim($("textarea[name=message]").val()),
				// le captcha
				captcha : captcha,
				captcha_id : $.trim($("input[name=re_captcha_id]").val()),
				mapX : _DATA.geoCoordonnees[0].lon,
				mapY : _DATA.geoCoordonnees[0].lat,
				raison : _DATA.corporate_name,
				url : _DATA.url
			},
			success : function() {
				alert(_TEXTES.msg_ok);
			},
			error : function(data) {
				if (data.responseText === "bad captcha") {
					alert(_TEXTES.captcha_err);
					refill_captcha();
				} else {
					alert(_TEXTES.msg_ko);
				}
				ret = false;
			}
		});
		return ret;
	}
}

function popup(url, largeur, hauteur, scroll) {
	var top = (screen.height - hauteur) / 2;
	var left = (screen.width - largeur) / 2;
	var fenetre = window.open(url, "pop_up_" + Math.round(Math.random() * 1000000), "top=" + top + ",left=" + left + ",width=" + largeur + ", height=" + hauteur + ",scrollbars=" + scroll + ",status=no,toolbar=no,resizable=yes,menubar=no,location=no");
	fenetre.focus();
}

function popinBonPlanEnProfiter() {
	// On recupère l'idEpj
	var idEpj = ($("div.ebp").length > 0) ? $("div.ebp").attr("idEpj") : ($("li.ebp").length > 0) ? $("li.ebp").attr("idEpj") : '';

	// Modification des dates de validitée
	$(".en-profiter .debutPeriodeValidite").append(_DATA.bon_plan_actif.date_debut_validite);
	$(".en-profiter .finPeriodeValidite").append(_DATA.bon_plan_actif.date_fin_validite);

	if (isExist(xt_pvi)) {
		// Préparation pour Taggage AT Internet
		var xtatc_print = "return xt_adc(this, 'PUB-[" + xt_pvi + "]-[" + _DATA.bon_plan_actif.accroche + "]-[detail]-[Boutons]-[Imprimer]-[" + _DATA.bon_plan_actif.id_bp + "]-[" + idEpj + "]')";
		var xtatc_send = "return xt_adc(this, 'PUB-[" + xt_pvi + "]-[" + _DATA.bon_plan_actif.accroche + "]-[detail]-[Boutons]-[Envoi_SMS]-[" + _DATA.bon_plan_actif.id_bp + "]-[" + idEpj + "]')";
		// Ajout des evenement onclick sur les boutons Imprimer et Envoyer pour les taggages AT Internet
		$(".en-profiter .imprimerbonplan").attr("onclick", xtatc_print);
		$(".en-profiter .inputEnvoyer").attr("onclick", xtatc_send);
	}

	// Popin en-profiter pour Bon Plan - Envoyer
	$(".en-profiter form").submit(function(e) {
		return validFormEnProfiter();
	});

	$(".en-profiter .imprimerbonplan").click(function() {
		popup(_DATA.printebp_link, 1000, 590, "yes");
		return false;
	});
}

function popinBonPlanPartager() {

	// Code de la toolbar à inserer en haut des popin dans le cas du partage d'un bon plan'
	var popinNav = '<div class="action"><ul><li><a class="recommander partager" href="#" title="recommander à un ami" >Par email</a></li>';
	popinNav += '<li><a class="envoyer partager" href="#" title="envoyer par sms" >Par SMS</a></li>';
	popinNav += '<li><a class="facebook" target="_blank" rel="nofollow" href="#" >Via Facebook</a></li>';
	popinNav += '<li class="last"><a class="twitter" target="_blank" rel="nofollow" href="#" >Via Twitter</a></li></ul></div>';

	$("#popn-inner, #popin").prepend(popinNav);

	// Recupération les infos EPJ depuis la page Bons Plans
	var pageBP = $("div.ebp");
	// Récupération les infos EPJ depuis l'onglet EPJ
	var ongletBP = $("li.ebp");
	// On recupèrel'idEpj
	var idEpj = (pageBP.length > 0) ? pageBP.attr("idEpj") : (ongletBP.length > 0) ? ongletBP.attr("idEpj") : '';

	// Données utilisées pour la mise en place des tags Publisher
	var xtatc_recommander = "return xt_adc(this, 'PUB-[" + xt_pvi + "]-[" + _DATA.bon_plan_actif.accroche + "]-[detail]-[Liens]-[Mail]-[" + _DATA.bon_plan_actif.id_bp + "]-[" + idEpj + "]')";
	var xtatc_send = "return xt_adc(this, 'PUB-[" + xt_pvi + "]-[" + _DATA.bon_plan_actif.accroche + "]-[detail]-[Liens]-[SMS]-[" + _DATA.bon_plan_actif.id_bp + "]-[" + idEpj + "]')";
	var xtatc_facebook = "return xt_adc(this, 'PUB-[" + xt_pvi + "]-[" + _DATA.bon_plan_actif.accroche + "]-[detail]-[Liens]-[Facebook]-[" + _DATA.bon_plan_actif.id_bp + "]-[" + idEpj + "]')";
	var xtatc_twitter = "return xt_adc(this, 'PUB-[" + xt_pvi + "]-[" + _DATA.bon_plan_actif.accroche + "]-[detail]-[Liens]-[Twitter]-[" + _DATA.bon_plan_actif.id_bp + "]-[" + idEpj + "]')";

	// Ajout des evenement onclick sur les boutons de la barre action pour les taggages AT Internet
	$("#popin .action .recommander, #popn .action .recommander").attr("onclick", xtatc_recommander);
	$("#popin .action .envoyer, #popn .action .envoyer").attr("onclick", xtatc_send);
	$("#popin .action .facebook, #popn .action .facebook").attr("onclick", xtatc_facebook);
	$("#popin .action .twitter, #popn .action .twitter").attr("onclick", xtatc_twitter);
}

function fb_share() {
	var lien;
	// Dans le cas ou l'on consulte un bon plan, mais que le lien a été perdu par le clic sur un lien ayant pour cible #
	if (!isExist(_DATA.url_active)) {
		lien = encodeURIComponent(document.location.toString());
	} else {
		lien = encodeURIComponent(_DATA.url_active);
	}
	var titre = encodeURIComponent(document.title);

	popup('http://www.facebook.com/sharer.php?u=' + lien + '&t=' + titre, 630, 440, 'yes');
	return false;
}

function twitter_share() {
	
	if (isExist(_DATA.twitterLien)) {
		
		var twitterLien;
		twitterLien= encodeURIComponent(_DATA.twitterLien);
		
		popup('http://twitter.com/timeline/home?status=' + twitterLien, 795, 440, 'yes');	
		
		
	}else {
		var lien;
		// Dans le cas ou l'on consulte un bon plan, mais que le lien a été perdu par le clic sur un lien ayant pour cible #
		if (!isExist(_DATA.url_active)) {
			lien = document.location.toString();
		} else {
			lien = _DATA.url_active;
		}
		var titre = document.title;
		var publisher = $("meta[name=author]").attr('content');
		var message = publisher + titre + lien;
		if ((publisher.length + titre.length + message.length) > 132) {
			titre = titre.substr(0, 132 - (publisher.length + lien.length));
		}

		popup('http://twitter.com/timeline/home?status=%23' + encodeURIComponent(publisher) + '+:+' + encodeURIComponent(titre) + '+' + encodeURIComponent(lien)+'%23', 795, 440, 'yes');
		
	}

	return false;
}

function google_share() {

	var lien;
	// Dans le cas ou l'on consulte un bon plan, mais que le lien a été perdu par le clic sur un lien ayant pour cible #
	if (!isExist(_DATA.url_active)) {
		lien = document.location.toString();
	} else {
		lien = _DATA.url_active;
	}
	var titre = document.title;
	var publisher = $("meta[name=author]").attr('content');
	var message = publisher + titre + lien;
	if ((publisher.length + titre.length + message.length) > 132) {
		titre = titre.substr(0, 132 - (publisher.length + lien.length));
	}

	popup('https://plus.google.com/share?url=' + encodeURIComponent(lien), 600, 400, 'yes');
	return false;
}
