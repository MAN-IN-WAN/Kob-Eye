var init = function() {
    var box = document.querySelector('.logo-container').children[0],
        logocontainer = document.querySelector('.logo-container'),
        header = document.querySelector('.header'),
        leftColumn = document.querySelector('#left-column'),
        main = document.querySelector('#main'),
        panelClassName = 'init';

    console.log('intro !!');
    //initialisation
    box.addClassName( panelClassName );
    header.addClassName( panelClassName );
    main.addClassName( panelClassName );
    leftColumn.addClassName( panelClassName );
    logocontainer.addClassName( panelClassName );
};

window.addEventListener( 'DOMContentLoaded', init, false);