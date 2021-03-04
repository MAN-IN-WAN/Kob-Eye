
addDOMLoadEvent = (function(){
    // create event function stack
    var load_events = [],
        load_timer,
        script,
        done,
        exec,
        old_onload,
        init = function () {
            done = true;

            // kill the timer
            clearInterval(load_timer);

            // execute each function in the stack in the order they were added
            while (exec = load_events.shift())
                exec();

            if (script) script.onreadystatechange = '';
        };

    return function (func) {
        // if the init function was already ran, just run this function now and stop
        if (done) return func();

        if (!load_events[0]) {
            // for Mozilla/Opera9
            if (document.addEventListener)
                document.addEventListener("DOMContentLoaded", init, false);

            // for Internet Explorer
            /*@cc_on @*/
            /*@if (@_win32)
                document.write("<script id=__ie_onload defer src=//0><\/scr"+"ipt>");
                script = document.getElementById("__ie_onload");
                script.onreadystatechange = function() {
                    if (this.readyState == "complete")
                        init(); // call the onload handler
                };
            /*@end @*/

            // for Safari
            if (/WebKit/i.test(navigator.userAgent)) { // sniff
                load_timer = setInterval(function() {
                    if (/loaded|complete/.test(document.readyState))
                        init(); // call the onload handler
                }, 10);
            }

            // for other browsers set the window.onload, but also execute the old window.onload
            old_onload = window.onload;
            window.onload = function() {
                init();
                if (old_onload) old_onload();
            };
        }

        load_events.push(func);
    }
})();


addDOMLoadEvent(function() {
	new abSuivi('add').process();
});


/**
 * Suivi de variable Abtel
 * tristan@abtel-creation.com
 * Utile pour suivre les utilisateurs sur le site s'ils
 * viennent d'une campagne AdWords
	#2010.07.29 MODIF ONDOMREADY (Trop de delai avant execution) By enguer@expressiv.net
	#2010.08.11 MODIF ONDOMREADY(2) (Rattrapage saucisse)
 */

function abSuivi( paramAsuivre ) {

	this.nomParam = paramAsuivre;
	this.valParam = '';
	
	/**
	 * Remplacement de tous les liens de la page pour qu'ils
	 * transmettent le paramètre
	 */
	this.process = function() {
		this.valParam = this.getParamSuivi();
		if(this.valParam != '') {
			var liens = this.getLiens();
			for( var i=0; i<liens.length; i++ ) this.replaceLink(liens[i]);
		}
	};

	/**
	 * Récupère le paramètre que l'on veut suivre
	 *
	 */
	this.getParamSuivi = function() {
		var options = location.search.substring(1).split('&');
		for (var i=0; i<options.length; i++) {
			var param = options[i].split('=');
			if(param[0] == this.nomParam) return param[1];
		}
		return '';
	};

	/**
	 * Retourne tous les liens de la page
	 * 
	 */
	this.getLiens = function() {
		return document.getElementsByTagName('a');
	};

	/**
	 * Remplace le lien avec le paramètre à transmettre
	 * TODO: Faire un check pour savoir si on doit lier avec & ou ? 
	 *		 et uniquement s'il n'existe pas encore dans cette url
	 * 
	 */
	this.replaceLink = function(lien) {
		var separator = this.findSeparator(lien.search);
		if(separator != '') {
			var toAdd = separator + this.nomParam + '=' + this.valParam;
			lien.href += toAdd;
		}
	};

	/**
	 * Trouve le séparateur pour compléter l'URL
	 * ? si aucun parametre pour le moment
	 * & si au moins un parametre pour le moment
	 * Rien si le parametre est déjà dans l'URL
	 */
	this.findSeparator = function(urlParams) {
		if(strpos(urlParams, this.nomParam)) return '';
		return (urlParams == '') ? '?' : '&';
	};

	/**
	 * Equivalent Javascript de cette fonction PHP bien pratique
	 */
	function strpos (haystack, needle, offset) {
		var i = (haystack+'').indexOf(needle, (offset || 0));
		return i === -1 ? false : i;
	};

};




