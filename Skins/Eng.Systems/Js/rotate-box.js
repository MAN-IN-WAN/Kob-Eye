var init = function() {
    var box = document.querySelector('.logo-container').children[0],
        logocontainer = document.querySelector('.logo-container'),
        header = document.querySelector('.header'),
        barre = document.querySelector('.barre'),
        body = document.querySelector('.body'),
        footer = document.querySelector('.footer'),
        panelClassName = 'init';

    console.log('intro !!');
    //initialisation
    box.addClassName( panelClassName );
    header.addClassName( panelClassName );
    body.addClassName( panelClassName );
    barre.addClassName( panelClassName );
    footer.addClassName( panelClassName );
    logocontainer.addClassName( panelClassName );
};

window.addEventListener( 'DOMContentLoaded', init, false);