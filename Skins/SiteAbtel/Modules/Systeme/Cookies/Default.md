<script type="text/javascript">
        var tagAnalyticsCNIL = {};
        var gaProperty = '[!gaProperty!]';
        
        
        tagAnalyticsCNIL.CookieConsent = function() {
                
                // Désactive le tracking si le cookie d'Opt-out existe déjà .
                var disableStr = 'ga-disable-' + gaProperty;
                var disableStrPiwik = 'piwik_ignore';
                var firstCall = false;
                
                //Cette fonction retourne la date d'expiration du cookie de consentement
                function getCookieExpireDate() {
                        // Le nombre de millisecondes que font 13 mois
                        var cookieTimeout = 33696000000;
                        var date = new Date();
                        date.setTime(date.getTime()+cookieTimeout);
                        var expires = "; expires="+date.toGMTString();
                        return expires;
                }
                
                //Cette fonction vérifie si on a déjà obtenu le consentement de la personne qui visite le site.
                function checkFirstVisit() {
                        var consentCookie = getCookie('hasConsent');
                        if ( !consentCookie ) return true;
                }
                
                //Affiche une bannière d'information en haut de la page
                function showBanner(){
                        var bodytag = document.getElementsByTagName('body')[0];
                        var div = document.createElement('div');
                        div.setAttribute('id','cookie-banner');
                        div.setAttribute('width','70%');
                        // Le code HTML de la demande de consentement
                        div.innerHTML = '<div style="background-color:#fff;text-align:center;padding:5px;font-size:12px;\
                                        border-bottom:1px solid #eeeeee;" id="cookie-banner-message" align="center">\
                                        Nous vous informons que ce site utilise des cookies d\'audience.\
                                        En continuant à naviguer, vous nous autorisez à déposer des cookies de mesure \
                                        d\'audience. <a href="javascript:tagAnalyticsCNIL.CookieConsent.showInform()" \
                                        style="text-decoration:underline;"> En savoir plus ou s\'opposer</a>.</div>';
                        // Vous pouvez modifier le contenu ainsi que le style
                        // Ajoute la bannière juste au début de la page
                        bodytag.insertBefore(div,bodytag.firstChild);
                        document.getElementsByTagName('body')[0].className+=' cookiebanner';
                        createInformAndAskDiv();
                }
                
                // Fonction utile pour récupérer un cookie à partir de son nom
                function getCookie(NameOfCookie) {
                        if (document.cookie.length > 0) {
                                begin = document.cookie.indexOf(NameOfCookie+"=");
                                if (begin != -1) {
                                        begin += NameOfCookie.length+1;
                                        end = document.cookie.indexOf(";", begin);
                                        if (end == -1) end = document.cookie.length;
                                        return unescape(document.cookie.substring(begin, end));
                                }
                        }
                        return null;
                }
                // Fonction utile pour recupérer le nom des cookies piwik complet à partir de son nom
                function searchCookieName(NameOfCookie) {
                        if (document.cookie.length > 0) {
                                var start = document.cookie.indexOf(NameOfCookie);
                                if (start == -1) return null;
                                var stop = document.cookie.indexOf("=", start);
                        
                                return unescape(document.cookie.substring(start, stop));
                        }
                        return null;
                }
                
                //Récupère la version d'Internet Explorer, si c'est un autre navigateur la fonction renvoie -1
                function getInternetExplorerVersion() {
                        var rv = -1;
                        if (navigator.appName == 'Microsoft Internet Explorer') {
                                var ua = navigator.userAgent;
                                var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
                                if (re.exec(ua) != null)
                                        rv = parseFloat( RegExp.$1 );
                        } else if (navigator.appName == 'Netscape') {
                                var ua = navigator.userAgent;
                                var re = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");
                                if (re.exec(ua) != null)
                                        rv = parseFloat( RegExp.$1 );
                        }
                        return rv;
                }
                
                //Effectue une demande de confirmation de DNT pour les utilisateurs d'IE
                function askDNTConfirmation() {
                        var r = confirm("La signal DoNotTrack de votre navigateur est activé, confirmez vous activer \
                                        la fonction DoNotTrack?");
                        return r;
                }
                
                //Vérifie la valeur de navigator.DoNotTrack pour savoir si le signal est activé et est à 1
                function notToTrack() {
                        if ( (navigator.doNotTrack && (navigator.doNotTrack=='yes' || navigator.doNotTrack=='1')) || ( navigator.msDoNotTrack && navigator.msDoNotTrack == '1') ) {
                                var isIE = (getInternetExplorerVersion()!=-1)
                                if (!isIE){ return true; }
                                return false;
                        }
                }
                
                //Si le signal est à 0 on considère que le consentement a déjà été obtenu
                function isToTrack() {
                        if ( navigator.doNotTrack && (navigator.doNotTrack=='no' || navigator.doNotTrack==0 )) {
                                return true;
                        }
                }
                
                // Fonction d'effacement des cookies
                function delCookie(name ) {
                        var path = ";path=" + "/";
                        var hostname = document.location.hostname;
                        var hostnameGA = hostname.substring(hostname.indexOf('.'));        
                        var domain = ";domain=" + "."+hostname;
                        var domainGA = ";domain=" +hostnameGA;
                        var expiration = "Thu, 01-Jan-1970 00:00:01 GMT";
                        
                        document.cookie = name + "=" + path + domain + ";expires=" + expiration;
                        document.cookie = name + "=" + path + domainGA + ";expires=" + expiration;
                        document.cookie = name + "=" + path + ";expires=" + expiration;
                }
                
                // Efface tous les types de cookies utilisés par Google Analytics & Piwik
                function deleteAnalyticsCookies() {
                        var cookieNames = ["__utma","__utmb","__utmc","__utmt","__utmv","__utmz","_ga","_gat","_pk_ref","_pk_id","_pk_cvar","_pk_ses"];
                        
                        for (var i=0; i<cookieNames.length; i++){
                                var basename = cookieNames[i];
                                var fullname = searchCookieName(basename);
                                if (fullname != null) {
                                        delCookie(fullname);
                                }
                        }
                }
                
                //La fonction qui informe et demande le consentement. Il s'agit d'un div qui apparait au centre de la page
                function createInformAndAskDiv() {
                        var bodytag = document.getElementsByTagName('body')[0];
                        var div = document.createElement('div');
                        div.setAttribute('id','inform-and-ask');
                        div.style.width= window.innerWidth+"px" ;
                        div.style.height= window.innerHeight+"px";
                        div.style.display= "none";
                        div.style.position= "fixed";
                        div.style.zIndex= "99999";
                        
                        // Le code HTML de la demande de consentement
                        // Vous pouvez modifier le contenu ainsi que le style
                        div.innerHTML = '<div style="width: 300px; background-color: white; repeat scroll 0% 0% white;\
                                        border: 1px solid #cccccc; padding :10px 10px;text-align:center; position: fixed; top:30px; \
                                        left:50%; margin-top:0px; margin-left:-150px; z-index:100000; opacity:1" id="inform-and-consent">\
                                        <div><span><b>Les cookies d\'audience</b></span></div><br><div>Ce site utilise des cookies d\'audience,\
                                        ces cookies nous aident à identifier le contenu qui vous interesse le plus. \
                                        Ces cookies sont utilisés à des fins de statistiques\
                                         et nous aide aussi à repérer certains dysfonctionnements. </div><div style="padding :10px 10px;text-align:center;"><button\
                                        style="margin-right:50px;text-decoration:underline;" name="S\'opposer" onclick="tagAnalyticsCNIL.CookieConsent.gaOptout();\
                                        tagAnalyticsCNIL.CookieConsent.hideInform();" id="optout-button" >S\'opposer</button>\
                                        <button style="text-decoration:underline;" name="cancel" onclick="tagAnalyticsCNIL.CookieConsent.hideInform()"\
                                        >Accepter</button></div></div>';
                        
                        // Ajoute la bannière juste au début de la page
                        bodytag.insertBefore(div,bodytag.firstChild);
                }
                
                function isClickOnOptOut( evt) {
                        // Si le noeud parent ou le noeud parent du parent est la bannière, on ignore le clic
                        return(evt.target.parentNode.id == 'cookie-banner' || evt.target.parentNode.parentNode.id =='cookie-banner' || evt.target.id == 'optout-button')
                }
                
                function consent(evt) {
                        // On vérifie qu'il ne s'agit pas d'un clic sur la bannière
                        if (!isClickOnOptOut(evt) ) {
                                if ( !clickprocessed) {
                                        evt.preventDefault();
                                        document.cookie = 'hasConsent=true; '+ getCookieExpireDate() +' ; path=/';
                                        callGoogleAnalytics();
                                        clickprocessed = true;
                                        window.setTimeout(function() {evt.target.click();}, 1000)
                                }
                        }
                }
                
                // Tag Google Analytics, cette version est avec le tag Universal Analytics
                function callGoogleAnalytics() {
                        if (firstCall) return;
                        else firstCall = true;
                        // Insà©rez votre tag Google Analytics ou Universal Analytics ici
                }
                
                return {
                        // La fonction d'opt-out
                        gaOptout: function() {
                                document.cookie = disableStr + '=true;'+ getCookieExpireDate() +' ; path=/';
                                document.cookie = disableStrPiwik + '=true;'+ getCookieExpireDate() +' ; path=/';
                                document.cookie = 'hasConsent=false;'+ getCookieExpireDate() +' ; path=/';
                                var div = document.getElementById('cookie-banner');
                                // Ci dessous le code de la bannià¨re affichà©e une fois que l'utilisateur s'est opposà© au dà©pot
                                // Vous pouvez modifier le contenu et le style
                                if ( div!= null ) div.innerHTML = '<div style="background-color:#fff;text-align:center;padding:5px;font-size:12px;\
                                border-bottom:1px solid #eeeeee;" id="cookie-message"> Vous vous êtes opposé au dépôt de cookies de mesures d\'audience\
                                dans votre navigateur </div>'
                                window[disableStr] = true;
                                window['piwikOff'] = true;
                                clickprocessed = true;
                                deleteAnalyticsCookies();
                        },
                        showInform: function() {
                                var div = document.getElementById("inform-and-ask");
                                div.style.display = "";
                        },
                        hideInform: function() {
                                var div = document.getElementById("inform-and-ask");
                                div.style.display = "none";
                                var div = document.getElementById("cookie-banner");
                                div.style.display = "none";
                        },
                        start: function() {
                                //Ce bout de code và©rifie que le consentement n'a pas dà©jà à©tà© obtenu avant d'afficher
                                // la bannià¨re
                                var consentCookie = getCookie('hasConsent');
                                clickprocessed = false;
                                if (!consentCookie) {
                                        //L'utilisateur n'a pas encore de cookie, on affiche la bannià©re.
                                        //Si il clique sur un autre à©là©ment que la bannià©re on enregistre le consentement
                                        if ( notToTrack() ) {
                                                //L'utilisateur a activà© DoNotTrack. Do not ask for consent and just opt him out
                                                tagAnalyticsCNIL.CookieConsent.gaOptout()
                                                alert("Vous avez activé l'option DoNotTrack, nous respectons votre choix.")
                                        } else {
                                                if (isToTrack() ) {
                                                        consent();
                                                } else {
                                                        if (window.addEventListener) {
                                                                window.addEventListener("load", showBanner, false);
                                                                document.addEventListener("click", consent, false);
                                                        } else {
                                                                window.attachEvent("onload", showBanner);
                                                                document.attachEvent("onclick", consent);
                                                        }
                                                }
                                        }
                                } else {
                                        if (document.cookie.indexOf('hasConsent=false') > -1){
                                                window[disableStr] = true;
                                                window['piwikOff'] = true; 
                                        } else {
                                                callGoogleAnalytics();
                                        }
                                }
                        }
                }
        }();
        
        tagAnalyticsCNIL.CookieConsent.start();
        
        
        
        /////////////////////////////////////////////////////////
        //Tag Google Analytics///////////////////////////////////
        /////////////////////////////////////////////////////////
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
      
        ga('create', gaProperty, 'auto');
        ga('send', 'pageview');


        /////////////////////////////////////////////////////////
        //Tag Piwik Analytics///////////////////////////////////
        /////////////////////////////////////////////////////////
        var _paq = _paq || [];
        
        if (window.piwikOff) _paq.push(['disableCookies']);
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
          var u=(("https:" == document.location.protocol) ? "https" : "http") + "://stats.abtel.fr/";
          _paq.push(['setTrackerUrl', u+'piwik.php']);
          _paq.push(['setSiteId', '[!piwikSiteId!]']);
          var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
          g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
        })();
</script>

<noscript><p><img src="http://stats.abtel.fr/piwik.php?idsite=[!piwikSiteId!]" style="border:0;" alt="" /></p></noscript>

