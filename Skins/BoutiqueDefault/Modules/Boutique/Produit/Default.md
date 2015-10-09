// Devise en cours

[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]
[IF [!Qte!]][ELSE]
[IF [!Prod::TypeProduit!]=5]
[!Qte:=25!]
[ELSE]
[!Qte:=1!]
[/IF]

[/IF]
<div class="FicheProduit">
    [STORPROC [!Query!]|Prod|0|1]
    [NORESULT]
    //			[HEADER 404][/HEADER]
    [/NORESULT]
    [STORPROC Boutique/Categorie/Produit/[!Prod::Id!]|Cat|0|1][/STORPROC]
    // gestion des différents modèles de fiche produit
    [SWITCH [!Prod::TypeProduit!]|=]
    [CASE 1]
    [MODULE Boutique/CategorieProduit/ReferenceUnique]
    [/CASE]
    [CASE 2]
    [MODULE Boutique/CategorieProduit/ProduitDecline]
    [/CASE]
    [CASE 3]
    [MODULE Boutique/CategorieProduit/ProduitUnique]
    [/CASE]
    [CASE 4]
    [MODULE Boutique/CategorieProduit/ProduitPackFormule]
    [/CASE]
    [CASE 5]
    [MODULE Boutique/CategorieProduit/ProduitConfigurateur]
    [/CASE]
    [/SWITCH]
    [/STORPROC]
</div>



// Surcouche JS
<script type="text/javascript">

$(document).ready(function () {
    VerifieSelection ();
    $('#FicheProduit').submit(function(e) {
                e.preventDefault();
                console.log('ajout au panier');
                // on vérifie qu'on a sélectionné le produit que l'on voulait acheté
                var sel = $('.CalculPrix');
                var req = {};
                var initI=0;
                //On va chercher tous les combos et radios d'attributs
                $(sel).each(function (index,item){
                    var attribut = $(item).attr('name');
                    var valeurattribut = -1;
                    var attributclass = $(item).attr('class');
                    initI=1;
                    switch ($(item).attr('type')){
                        case "radio":
                            if ($(item).is(":checked")){
                                valeurattribut = $(item).val();
                                //On stocke les informations dans le tableau de requete
                                req[attribut] = valeurattribut;
                            }
                            break;
                        default:
                            valeurattribut = $(item).val();
                            //On stocke les informations dans le tableau de requete
                            req[attribut] = valeurattribut;
                            break;
                    }
                });
                //On boucle sur req si une valeur est égale à -1 on sort
                for (var i in req){
                    if (req[i]==-1) {
                        console.log("Merci de sélectionner les attributs nécessaires avant d'ajouter au panier ");
                        toastr.warning("Merci de sélectionner les attributs nécessaires avant d'ajouter au panier ");
                        e.preventDefault();
                        return;
                    }
                };
                if (!(i)&& initI==1) {
                    console.log("Merci de sélectionner les attributs nécessaires avant d'ajouter au panier ");
                    toastr.warning("Merci de sélectionner les attributs nécessaires avant d'ajouter au panier ");
                    e.preventDefault();
                    return;
                }

                // GESTION DES CHOIX PACKS
                if ( $('#PackType').val()=='4') {
                    var sel = $('.PackChoix');
                    initI=1;
                    //On va chercher tous les choix du pack
                    $(sel).each(function (index,item){
                        if ($(item).val() =="") {
                            initI=0;
                        }
                    });
                    if (initI==0) {
                        console.log("Merci de choisir tous les produits de votre formule");
                        toastr.warning("Merci de choisir tous les produits de votre formule");
                        e.preventDefault();
                        return;

                    }
                    e.preventDefault();
                }
                if ( $('#PackType').val()=='5') {
                    var champ='';initI=1;
                    [STORPROC Boutique/Produit/[!Prod::Id!]/ConfigPack/Options=0&ChoixObligatoire=1|Cpk]
                    Champ='#PackChoix-'+[!Cpk::Id!];
                    if ($(Champ).val() =="") {
                        initI=0;
                    }
                    [/STORPROC]
                    if (initI==0) {
                        console.log("Merci de choisir toutes les parties de votre carte personnalisable");
                        toastr.warning("Merci de choisir toutes les parties de votre carte personnalisable");
                        e.preventDefault();
                        return;

                    }
                    e.preventDefault();
                }
                var LaQte = $('#Qte').val();
                var LaRef= $('[name=Reference]').val();
                $('#myModalLabel').html("");

                console.log('on fait la requete');
                $.ajax({
                    type: "POST",
                    url: "/Boutique/Produit/PopupPanier.htrc",
                    dataType: "html",
                    success: function (msg) {
                        $('#lemodal').find('.modal-body').html(msg);
                        $('#lemodal').modal('show');
                    },
                    data: {
                        Qte:LaQte,
                        Reference:LaRef
                        ,config:{
                                [STORPROC Boutique/Produit/[!Prod::Id!]/ConfigPack|Cpk|||Ordre|ASC]
                [IF [!Pos!]>1],[/IF][!Cpk::Id!]:$('#PackChoix-[!Cpk::Id!]').val()
                        [/STORPROC]
            }
            ,options:{
        [!F:=0!]
        [STORPROC Boutique/Produit/[!Prod::Id!]/ConfigPack|Cpk|||Ordre|ASC]
        [STORPROC Boutique/Produit/[!Prod::Id!]/ConfigPack/[!Cpk::Id!]/Options|Opt|||Ordre|ASC]
        [IF [!F!]],[/IF]
        [SWITCH [!Opt::TypeOptions!]|=]
        [CASE 5]
        '[!Cpk::Id!]_[!Opt::Id!]':$('.OptionsChoix-[!Cpk::Id!]-[!Opt::Id!]').filter(':checked').val()
                [/CASE]
        [CASE 4]
        '[!Cpk::Id!]_[!Opt::Id!]':$('#OptionsChoix-[!Cpk::Id!]-[!Opt::Id!]').val()
                [/CASE]
        [DEFAULT]
        '[!Cpk::Id!]_[!Opt::Id!]':$('#OptionsChoix-[!Cpk::Id!]-[!Opt::Id!]').val()
                [/DEFAULT]
        [/SWITCH]

        [!F+=1!]
                [/STORPROC]
        [/STORPROC]
    }
}
});



});

});


function VerifieSelection () {
    //initialisation
    var sel = $('.CalculPrix');
    var req = {};

    //On va chercher tous les combos et radios d'attributs
    sel.each(function (index,item){
        var attribut = $(item).attr('name');
        var valeurattribut = -1;
        var attributclass = $(item).attr('class');
        switch ($(item).attr('type')){
            case "radio":
                if ($(item).is(":checked")){
                    valeurattribut = $(item).attr('value');
                    //On stocke les informations dans le tableau de requete
                    req[attribut] = valeurattribut;
                }
                break;
            default:
                valeurattribut = $(item).val();
                //On stocke les informations dans le tableau de requete
                req[attribut] = valeurattribut;
                break;
        }
    });
    //On boucle sur req si une valeur est égale à -1 on sort
    for (var i in req){
        if (req[i]==-1)return;
    };

    //On va chercher la quantite
    req.quantite = $('#Qte').val();
    // Desactive le bouton ajouter au panier tant qu'on a pas le retour JSON
    if($('#AchatAjouterPanier') != null){
        $('#AchatAjouterPanier').addClass('Disabled');
        $('#AchatAjouterPanier').attr('disabled','disabled');
    }

    //On execute la requete
    var r = $.getJSON('/Boutique/Produit/[!Prod::Id!]/getTarif.json',req)
            .fail(function (){
                toastr.error('probleme de connexion');
            })
            .done (function(json){
        //mettre à jour le champ tarif
        if (json.price!=0&&json.price!=undefined) {
            $('#tarif').html(json.price+' €');
        }
        // si on est dans configurateur de carte, afficher le prix à l'unité
        if ( $('#PackType').val()=='5') {
            $('#tarifunite').html("soit "+ json.priceUnit+" € l unité");

        }
        if($('#promo')==1) $('#tarifNonPromo').css('display', 'block');
        else {
            if($('#tarifNonPromo') != null) $('#tarifNonPromo').css('display', 'none');
        }
        if($('#tarifvisible') != null) $('#tarifvisible').css('display', 'none');

        $('#Reference').val(json.reference);


        //reactive le bouton ajouter au panier
        if($('#AchatAjouterPanier') != null && parseInt(json.StockAvailable)==1){
            $('#AchatAjouterPanier').unbind('click');
            $('#AchatAjouterPanier').removeClass('Disabled');
            $('#AchatAjouterPanier').removeAttr("disabled");
        }else if ($('#AchatAjouterPanier') != null){
            //on supprime tout evenement de click
            $('#AchatAjouterPanier').unbind('click');
            $('#AchatAjouterPanier').removeAttr("disabled");
            $('#AchatAjouterPanier').click(function (e){
                e.preventDefault();
                toastr.error('stock insuffisant pour ce produit.');
            });

        }
    });
}


function CalculQte2(PlusMoins,Type) {
    var Quantite= parseInt($('#Qte').val());
    var total= Quantite+parseFloat(PlusMoins);
    $('#Qte').val(total);
    if (total < 1) $('#Qte').val(1);
    if (Type==5&&total<25) $('#Qte').val(25);
    VerifieSelection ();
}


function CalculQte (PlusMoins,Type) {
    var sel = $('.CalculPrix');
    var req = {};
    var initI=0;
    var Quantite= parseInt($('#Qte').val());
    var total= Quantite+parseFloat(PlusMoins);
    $('#Qte').val(total);
    if (total < 1) $('#Qte').val(1);
    if (Type==5&&total<25) $('#Qte').val(25);

    // GESTION DES CHOIX PACKS
    if ( $('#PackType').val()=='4') {
        var sel = $('.PackChoix');
        initI=1;
        //On va chercher tous les choix du pack
        $(sel).each(function (index,item){
            if ($(item).val() =="") {
                initI=0;
            }
        });
        if (initI==0) {
            toastr.warning("Merci de choisir tous les produits de votre formule");
            return;

        }
    }
    [STORPROC [!Query!]|Prod|0|1][/STORPROC]
    if ( $('#PackType').val()=='5') {
        var champ='';initI=1;
        [STORPROC Boutique/Produit/[!Prod::Id!]/ConfigPack/Options=0&ChoixObligatoire=1|Cpk]
        Champ='#PackChoix-'+[!Cpk::Id!];
        if ($(Champ).val() =="") {
            initI=0;
        }
        [/STORPROC]

    }
    var LaQte = $('#Qte').val();
    var LaRef= $('#Reference').val();
    $('#myModalLabel').html("");


    $.ajax({
        type: "POST",
        url: "/Boutique/Produit/[!Prod::Id!]/getTarif.json",
        dataType: "json",
        success: function (json) {
            //mettre à jour le champ tarif
            if (json.price!=0&&json.price!=undefined) {
                $('#tarif').html(json.price+' €');
            }
            // si on est dans configurateur de carte, afficher le prix à l'unité
            if ( $('#PackType').val()=='5') {
                $('#tarifunite').html("soit "+ json.priceUnit+" € l unité");

            }
            if($('#promo')==1) $('#tarifNonPromo').css('display', 'block');
            else {
                if($('#tarifNonPromo') != null) $('#tarifNonPromo').css('display', 'none');
            }
            if($('#tarifvisible') != null) $('#tarifvisible').css('display', 'none');

            $('#Reference').val(json.reference);


            //reactive le bouton ajouter au panier
            if($('#AchatAjouterPanier') != null && parseInt(json.StockAvailable)==1){
                $('#AchatAjouterPanier').unbind('click');
                $('#AchatAjouterPanier').removeClass('Disabled');
                $('#AchatAjouterPanier').removeAttr("disabled");
            }else if ($('#AchatAjouterPanier') != null){
                //on supprime tout evenement de click
                $('#AchatAjouterPanier').unbind('click');
                $('#AchatAjouterPanier').removeAttr("disabled");
                $('#AchatAjouterPanier').click(function (e){
                    //	e.preventDefault();
                    toastr.error('stock insuffisant pour ce produit.');
                });

            }
        },
        data: {
            quantite:LaQte,
            Reference:LaRef,
            req : req
            ,config:{
                    [STORPROC Boutique/Produit/[!Prod::Id!]/ConfigPack|Cpk|||Ordre|ASC]
    [IF [!Pos!]>1],[/IF][!Cpk::Id!]:$('#PackChoix-[!Cpk::Id!]').val()
            [/STORPROC]
}
,options:{
    [!F:=0!]
    [STORPROC Boutique/Produit/[!Prod::Id!]/ConfigPack|Cpk|||Ordre|ASC]
    [STORPROC Boutique/Produit/[!Prod::Id!]/ConfigPack/[!Cpk::Id!]/Options|Opt|||Ordre|ASC]
    [IF [!F!]],[/IF]
    [SWITCH [!Opt::TypeOptions!]|=]
    [CASE 5]
    '[!Cpk::Id!]_[!Opt::Id!]':$('.OptionsChoix-[!Cpk::Id!]-[!Opt::Id!]').filter(':checked').val()
            [/CASE]
    [CASE 4]
    '[!Cpk::Id!]_[!Opt::Id!]':$('#OptionsChoix-[!Cpk::Id!]-[!Opt::Id!]').val()
            [/CASE]
    [DEFAULT]
    '[!Cpk::Id!]_[!Opt::Id!]':$('#OptionsChoix-[!Cpk::Id!]-[!Opt::Id!]').val()
            [/DEFAULT]
    [/SWITCH]

    [!F+=1!]
            [/STORPROC]
    [/STORPROC]
}
}
});

}






</script>