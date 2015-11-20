/**
 Intialisation des outils de navigation du wite
 */

var epconsulting = function () {
    console.log('class epconsulting intialisation');
    this.init();
};
$.extend(epconsulting.prototype, {
    map: '',
    content: '',
    loaded: false,
    /** intiilisation de la classe */
    init: function (mapname, contentname) {
        console.log('récupération des éléments html');
        this.map = $(mapname);
        this.content = $(contentname);

        //reinitialisation des éléments
        this.reset();

        //initialisation de la navigation

        if (!this.loaded) {
            //chargement des éléments.
            this.load();
        }else
            //lancement affcihage
            this.display();
    },
    /** reset de l'affichage */
    reset: function () {
        //o vide le contenu
        this.map.clear();
        this.content.clear();
    },
    /** chargement des éléments **/
    load: function () {
        //construction de l'affichage de charement
    },
    /** affichage des éléments de navigation et de la page d'accueil **/
    display: function () {
        //affichage de la navigation
        this.displayNav();

        //affichage du fond et positionnement
        this.displayHomeMap();
    },
    /** affichage de la navigation **/
    displayNav: function () {

    }
});