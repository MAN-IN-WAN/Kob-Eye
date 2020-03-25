/**
 * Validation des formulaires de contact & immo
 */

$(document).ready(function() {
	
	// expression régulière pour tester la validité d'un e-mail
    var mail = new RegExp(/^([a-zA-Z0-9._\-]+@[a-zA-Z0-9._\-]{3,}[; ]*)+$/i);
	
	// vérification des champs du formulaire de contact (tmplt 1ere generation + tmplt html5 )
	var form = $("#reqform");
	
	if (form.length === 1) {
	    form.submit(function () {
	        var alerte = "";
	        var oVal = "";
	        
	        var nom = $.trim($("input[name=nom]").val());
	        oVal = $.trim($("input[name=nom]").attr("oVal"));
	        if (nom === "" || nom === oVal) {
	            alerte += _TEXTES.nom + "\n";
	        }
	        
	        var prenom = $.trim($("input[name=prenom]").val());
	        oVal = $.trim($("input[name=prenom]").attr("oVal"));
	        if (prenom === "" || prenom === oVal) {
	            alerte += _TEXTES.prenom + "\n";
	        }
	        var email = $.trim($("input[name=email]").val());
	        oVal = $.trim($("input[name=email]").attr("oVal"));
	        if (email === "" || email === oVal) {
	            alerte += _TEXTES.email + "\n";
	        }
	        if (!mail.test(email) && email !== "" && email !== oVal) {
	            alerte += _TEXTES.email_err + "\n";
	        }
	        var telephone = $.trim($("input[name=telephone]").val());
	        oVal = $.trim($("input[name=telephone]").attr("oVal"));
	        if (telephone === "" || telephone === oVal) {
	            alerte += _TEXTES.telephone + "\n";
	        }
	        var message = $.trim($("textarea[name=message]").val());
	        oVal = $.trim($("textarea[name=message]").attr("oVal"));
	        if (message === "" || message === oVal) {
	            alerte += _TEXTES.message + "\n";
	        }
	        var captcha = $.trim($("input[name=captcha]").val());
	        if (captcha === "") {
	            alerte += _TEXTES.captcha + "\n";
	        }
	        
	        if (alerte !== "") {
	            alert(alerte);
	            return false;
	        } else {
	            var ret = true;
	            $.ajax({
	                url: "/plugins/api/captcha/mail/send",
	                type: "POST",
	                data: {
	                    nom: $.trim($("input[name=nom]").val()),
	                    prenom: $.trim($("input[name=prenom]").val()),
	                    email: $.trim($("input[name=email]").val()),
	                    email_dest: $.trim($("input[name=mailtosend]").val()),
	                    raison: _DATA.corporate_name,
	                    url: _DATA.url,
	                    telephone: $.trim($("input[name=telephone]").val()),
	                    titre: $.trim($("input[name=titre]").val()),
	                    message: $.trim($("textarea[name=message]").val()),
	                    spam: $.trim($("input[name=spam]").val()),
	                    captcha: $.trim($("input[name=captcha]").val()),
	                    captcha_id: $.trim($("input[name=captcha_id]").val())
	                },
	                async: false,
	                dataType: "html",
	                success: function () {
	                    alert(_TEXTES.msg_ok);
	                    ret = true;
	                },
	                error: function (data) {
	                    if (data.responseText === "bad captcha") {
	                        fill_captcha();
	                        alert(_TEXTES.captcha_err);
	                    } else {
	                        alert(_TEXTES.msg_ko);
	                    }
	                    ret = false;
	                }
	
	            });
	            return ret;
	        }
	    });
	}
	
	// vérification des champs du formulaire d'estimation (tmplt 1ere generation + tmplt html5 )
	form = $("#reqestimateform");
	if (form.length === 1) {
	    form.submit(function () {
	        var alerte = "";
	        var oVal = "";
	        var bien_type = $.trim($("#reqestimateform select[name=bien_type]").val());
	        if (bien_type === "") {
	            alerte += _TEXTES.bien_type + "\n";
	        }
	        var bien_pieces = $.trim($("#reqestimateform input[name=bien_pieces]").val());
	        if (bien_pieces === "") {
	            alerte += _TEXTES.bien_pieces + "\n";
	        }
	        var bien_surf = $.trim($("#reqestimateform input[name=bien_surf]").val());
	        if (bien_surf === "") {
	            alerte += _TEXTES.bien_surf + "\n";
	        }
	        var bien_code = $.trim($("#reqestimateform input[name=bien_code]").val());
	        if (bien_code === "") {
	            alerte += _TEXTES.bien_code + "\n";
	        }
	        var bien_ville = $.trim($("#reqestimateform input[name=bien_ville]").val());
	        if (bien_ville === "") {
	            alerte += _TEXTES.bien_ville + "\n";
	        }
	        var bien_name = $.trim($("#reqestimateform input[name=bien_name]").val());
	        if (bien_name === "") {
	            alerte += _TEXTES.bien_name + "\n";
	        }
	        var bien_fname = $.trim($("#reqestimateform input[name=bien_fname]").val());
	        if (bien_fname === "") {
	            alerte += _TEXTES.bien_fname + "\n";
	        }
	        var bien_email = $.trim($("#reqestimateform input[name=bien_email]").val());
	        if (bien_email === "") {
	            alerte += _TEXTES.email + "\n";
	        }
	        if (!mail.test(bien_email) && bien_email !== "") {
	            alerte += _TEXTES.email_err + "\n";
	        }
	        var bien_tel = $.trim($("#reqestimateform input[name=bien_tel]").val());
	        if (bien_tel === "") {
	            alerte += _TEXTES.bien_tel + "\n";
	        }
	        var captcha = $.trim($("#reqestimateform input[name=captcha]").val());
	        if (captcha === "") {
	            alerte += _TEXTES.captcha + "\n";
	        }
	        if (alerte !== "") {
	            alert(alerte);
	            return false;
	        } else {
	            var ret = true;
	            $.ajax({
	                url: "/plugins/api/captcha/mail/sendEstimate",
	                type: "POST",
	                data: {
	                    bien_type: $.trim($("#reqestimateform select[name=bien_type]").val()),
	                    bien_pieces: $.trim($("#reqestimateform input[name=bien_pieces]").val()),
	                    bien_surf: $.trim($("#reqestimateform input[name=bien_surf]").val()),
	                    bien_loc: $.trim($("#reqestimateform input[name=bien_loc]").val()),
	                    bien_code: $.trim($("#reqestimateform input[name=bien_code]").val()),
	                    bien_ville: $.trim($("#reqestimateform input[name=bien_ville]").val()),
	                    bien_comments: $.trim($("#reqestimateform textarea[name=bien_comments]").val()),
	                    bien_name: $.trim($("#reqestimateform input[name=bien_name]").val()),
	                    bien_fname: $.trim($("#reqestimateform input[name=bien_fname]").val()),
	                    email: $.trim($("#reqestimateform input[name=bien_email]").val()),
	                    bien_tel: $.trim($("#reqestimateform input[name=bien_tel]").val()),
	                    email_dest: $.trim($("#reqestimateform input[name=mailtosend]").val()),
	                    url: _DATA.url,
	                    spam: $.trim($("#reqestimateform input[name=spam]").val()),
	                    captcha: $.trim($("#reqestimateform input[name=captcha]").val()),
	                    captcha_id: $.trim($("#reqestimateform input[name=captcha_id]").val())
	                },
	                async: false,
	                dataType: "html",
	                success: function () {
	                    alert(_TEXTES.msg_ok);
	                    ret = true;
	                },
	                error: function (data) {
	                    if (data.responseText === "bad captcha") {
	                        fill_captcha();
	                        alert(_TEXTES.captcha_err);
	                    } else {
	                        alert(_TEXTES.msg_ko);
	                    }
	                    ret = false;
	                }
	
	            });
	            return ret;
	        }
	    });
	}
	
	// vérification des champs du formulaire de gestion de bien (tmplt 1ere generation + tmplt html5 )
	form = $("#reqmanageform");
	if (form.length === 1) {
	    form.submit(function () {
	        var alerte = "";
	        var oVal = "";
	        var bien_manage_type = $.trim($("#reqmanageform #bien_manage_type").val());
	
	        if (bien_manage_type === "") {
	            alerte += _TEXTES.bien_manage_type + "\n";
	        }
	        var bien_type = $.trim($("#reqmanageform select[name=bien_type]").val());
	        if (bien_type === "") {
	            alerte += _TEXTES.bien_type + "\n";
	        }
	        var bien_pieces = $.trim($("#reqmanageform input[name=bien_pieces]").val());
	        if (bien_pieces === "") {
	            alerte += _TEXTES.bien_pieces + "\n";
	        }
	        var bien_surf = $.trim($("#reqmanageform input[name=bien_surf]").val());
	        if (bien_surf === "") {
	            alerte += _TEXTES.bien_surf + "\n";
	        }
	        var bien_code = $.trim($("#reqmanageform input[name=bien_code]").val());
	        if (bien_code === "") {
	            alerte += _TEXTES.bien_code + "\n";
	        }
	        var bien_ville = $.trim($("#reqmanageform input[name=bien_ville]").val());
	        if (bien_ville === "") {
	            alerte += _TEXTES.bien_ville + "\n";
	        }
	        var bien_name = $.trim($("#reqmanageform input[name=bien_name]").val());
	        if (bien_name === "") {
	            alerte += _TEXTES.bien_name + "\n";
	        }
	        var bien_dispo = $.trim($("#reqmanageform input[name=bien_dispo]").val());
	        if (bien_dispo === "") {
	            alerte += _TEXTES.bien_dispo + "\n";
	        }
	        var bien_email = $.trim($("#reqmanageform input[name=bien_email]").val());
	        if (bien_email === "") {
	            alerte += _TEXTES.email + "\n";
	        }
	        if (!mail.test(bien_email) && bien_email !== "") {
	            alerte += _TEXTES.email_err + "\n";
	        }
	        var bien_tel = $.trim($("#reqmanageform input[name=bien_tel]").val());
	        if (bien_tel === "") {
	            alerte += _TEXTES.bien_tel + "\n";
	        }
	        var captcha = $.trim($("#reqmanageform input[name=captcha]").val());
	        if (captcha === "") {
	            alerte += _TEXTES.captcha + "\n";
	        }
	        if (alerte !== "") {
	            alert(alerte);
	            return false;
	        } else {
	            var ret = true;
	            $.ajax({
	                url: "/plugins/api/captcha/mail/sendManage",
	                type: "POST",
	                data: {
	                    bien_manage_type: bien_manage_type,
	                    bien_type: $.trim($("#reqmanageform select[name=bien_type]").val()),
	                    bien_pieces: $.trim($("#reqmanageform input[name=bien_pieces]").val()),
	                    bien_surf: $.trim($("#reqmanageform input[name=bien_surf]").val()),
	                    bien_loc: $.trim($("#reqmanageform input[name=bien_loc]").val()),
	                    bien_code: $.trim($("#reqmanageform input[name=bien_code]").val()),
	                    bien_ville: $.trim($("#reqmanageform input[name=bien_ville]").val()),
	                    bien_price: $.trim($("#reqmanageform input[name=bien_price]").val()),
	                    bien_dispo: $.trim($("#reqmanageform input[name=bien_dispo]").val()),
	                    bien_comments: $.trim($("#reqmanageform textarea[name=bien_comments]").val()),
	                    bien_name: $.trim($("#reqmanageform input[name=bien_name]").val()),
	                    bien_fname: $.trim($("#reqmanageform input[name=bien_fname]").val()),
	                    email: $.trim($("#reqmanageform input[name=bien_email]").val()),
	                    bien_tel: $.trim($("#reqmanageform input[name=bien_tel]").val()),
	                    email_dest: $.trim($("#reqmanageform input[name=mailtosend]").val()),
	                    url: _DATA.url,
	                    spam: $.trim($("#reqmanageform input[name=spam]").val()),
	                    captcha: $.trim($("#reqmanageform input[name=captcha]").val()),
	                    captcha_id: $.trim($("#reqmanageform input[name=captcha_id]").val())
	                },
	                async: false,
	                dataType: "html",
	                success: function () {
	                    alert(_TEXTES.msg_ok);
	                    ret = true;
	                },
	                error: function (data) {
	                    if (data.responseText === "bad captcha") {
	                        fill_captcha();
	                        alert(_TEXTES.captcha_err);
	                    } else {
	                        alert(_TEXTES.msg_ko);
	                    }
	                    ret = false;
	                }
	
	            });
	            return ret;
	        }
	    });
	}
	
});	
