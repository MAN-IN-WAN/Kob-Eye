 
 
function fitHeight(){
        var heightTreshold = 300;
        
        var winHeight = $(window).height();
        var winWidth = $(window).width();
        
        var header = $("#header");
        var headerHeight = header.height();
        var footer = $("#footer");
        var footerHeight = footer.height();
        var mainMenu = $("#mainMenu");
        var mainMenuHeight = 0;
        if (mainMenu.length) {
               //mainMenuHeight = winHeight*0.3;
               //mainMenu.height(mainMenuHeight);
               
               mainMenuHeight = mainMenu.height();
        }
        var main = $("#main");
        var leftCol = $("#projectHome");
        var leftColTxt = $("#projectHomeTxt");
        var rightCol = $("#newsHome");
        var colMargin = $("#projectHome").outerHeight(true)-$("#projectHome").outerHeight();
        
        //leftCol.hide();
        //rightCol.hide();
        
        var mainHeight = winHeight - (headerHeight+footerHeight+mainMenuHeight+2);
        main.outerHeight(mainHeight);
        
        if (mainHeight >= heightTreshold) {
                
                colHeight = parseInt(mainHeight - colMargin);
                
                leftCol.outerHeight(colHeight);
                leftColTxt.outerHeight(colHeight);
                rightCol.outerHeight(colHeight);
                
                //leftCol.show();
                //rightCol.show();
                
                //$('.newsContent').textfill({innerTag : 'p'});
                //$('#projectResume').textfill({innerTag : 'p'});
        }
}