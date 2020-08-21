if (getCookie("myCookie")){
    var currPage = window.location.pathname;
    var cookiePage = getCookie("myCookie");
    console.log(cookiePage,currPage );
    if (cookiePage === currPage){
        var dtExpire = new Date();
        dtExpire.setTime(dtExpire.getTime() + 3600 * 24 * 30 * 1000);
        setCookie('myCookie', 'anim', dtExpire, '/' );
        window.location.reload(true);
    }
}
$( document ).ready(function() {
    // $(window).on('mousewheel',function(event){
    //     if (event.originalEvent.wheelDelta < 0){
    //         // downscroll code
    //         animAccueil();
    //         $(window).off('mousewheel');
    //     } else {
    //         // upscroll code
    //     }
    // });

    $('.menuLink a').on('click',function(event){
        $('.menuLink a').off('click');
        var accueil = getCookie("myCookie");
        if (accueil != undefined && accueil !="" )
            return true;
        event.preventDefault();

        var alias = $(this).attr("href");
        var tempAlias = alias;
        if (alias == '/'){
            alias = '/toto';
        }
        $.get(alias+".htm",function(data){
            $( "#mainContent" ).html( data );
            animAccueil();
        })
        if (alias != "/toto"){
            $("#bandoHaut").hide();
            $("#bandoBas").hide();
        }
        var urlcourante = window.location.origin;
        var urlNext = urlcourante + tempAlias;
        var hash = location.hash.replace('#','');
        if(hash != ''){
            // Clear the hash in the URL
            location.hash = '';
        }

        history.replaceState({id:"forcelanding"}, $(this).text(), urlNext);
        $(".Current").removeClass("Current");
        $(this).parent().addClass('Current');

    });
});

function animTeam(event,prenom,nom,poste,description,photo){
    console.log(arguments);
    $('#animBot').addClass('active');
    document.getElementById("detailVeto").innerHTML =
        "   <div class='docHead'> \
                <p class='nomdct'> "+prenom+" "+nom+"</p> \
                "+poste+" \
            </div> \
            <div class='docDecr'>\
                "+ description +" \
            </div>";
    document.getElementById("leftSide").innerHTML = '<img class="imagePers" src="/'+photo+'" alt="'+prenom+' '+nom+'">';
}
function Out() {
    $('#animBot').removeClass('active');
}

function animAccueil() {
    $('.hiddenContenu').removeClass('hiddenContenu');
    $('#Bando').addClass('shrunk');
    $('.Atterissage').removeClass('Atterissage');
    $('.landing').removeClass('landing');

    var ck = $('li.Current a' ).attr('href');
    console.log(ck);
    var dtExpire = new Date();
    dtExpire.setTime(dtExpire.getTime() + 3600 * 24 * 30 * 1000);
    setCookie('myCookie', ck, dtExpire, '/' );

}
function testSlide(nbItem) {
    if ( $( ".item_"+nbItem ).is( ":hidden" ) ) {
        for (let i=0;i<=8;i++){
            if (nbItem != i && $( ".item_"+i ).is( ":visible" )){
                $( ".item_"+i ).prev().css("background-color","");
                $( ".item_"+i ).slideDown().hide("fast");
            }
        }
        $( ".item_"+nbItem ).slideUp().show("fast");
        $( ".item_"+nbItem ).prev().css("background-color","#ffd132");
    }else{
        $( ".item_"+nbItem ).prev().css("background-color","");
        $( ".item_"+nbItem ).slideDown().hide("fast");
    }
}

// Fonction création de cookie
function setCookie(nom, valeur, expire, chemin, domaine, securite){
    document.cookie = nom + ' = ' + escape(valeur) + '  ' +
        ((expire == undefined) ? '' : ('; expires = ' + expire.toGMTString())) +
        ((chemin == undefined) ? '' : ('; path = ' + chemin)) +
        ((domaine == undefined) ? '' : ('; domain = ' + domaine)) +
        ((securite == true) ? '; secure' : '');
}

// Fonction recuperation cookie
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}