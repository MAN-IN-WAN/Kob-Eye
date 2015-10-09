<h1>Nos références</h1>

[IF [!View!]!=OldReferences]

[HEADER JS]Skins/[!Systeme::Skin!]/Js/custom-event.js[/HEADER]
[HEADER JS]Skins/[!Systeme::Skin!]/Js/Features.Touch.js[/HEADER]
[HEADER JS]Skins/[!Systeme::Skin!]/Js/Mouse.js[/HEADER]
[HEADER JS]Skins/[!Systeme::Skin!]/Js/Swipe.js[/HEADER]

<div id="PragmaReferencesMain">
    <button id="PragmaReferencesLeftBtn"></button>
    <button id="PragmaReferencesRightBtn"></button>
    <div id="PragmaReferencesContainer">
        <div id="PragmaReferencesContainerDefile">
            [STORPROC ParcImmobilier/Residence/Reference=1|Res|0|1000|DateLivraison|DESC]
                [COUNT ParcImmobilier/Residence/[!Res::Id!]/Donnee/Type=References|NbImg]
                <div class="PragmaReferencesItem">
                    <div class="PragmaReferencesLeft">
                        [STORPROC ParcImmobilier/Residence/[!Res::Id!]/Donnee/Type=References|Img|0|1]
                            <a class="mb" rel="residence[[!Res::Id!]]" href="/[!Img::URL!].limit.800x600.jpg" title="[!Res::Titre!]">
                                <img src="/[!Img::URL!].mini.375x515.jpg" alt="[!Res::Titre!]" />
                            </a>
                        [/STORPROC]
                        [STORPROC ParcImmobilier/Residence/[!Res::Id!]/Donnee/Type=References|Img|3|100]
                            <a class="mb" rel="residence[[!Res::Id!]]" href="/[!Img::URL!].limit.800x600.jpg" title="[!Res::Titre!]">
                                <img src="/[!Img::URL!].mini.375x515.jpg" alt="[!Res::Titre!]" />
                            </a>
                        [/STORPROC]
                    </div>
                    <div class="PragmaReferencesRight">
                        [STORPROC ParcImmobilier/Ville/Residence/[!Res::Id!]|Ville][/STORPROC]
                        [STORPROC ParcImmobilier/Departement/Ville/[!Ville::Id!]|Dep][/STORPROC]
                        <h2>[!Res::Titre!]</h2>
                        <h3>[!Ville::Nom!] ([!Dep::Nom!])</h3>
                        <div class="PragmaReferencesSubInfo">
                            <dt>Livraison</dt>
                            <dd>[!Res::DateLivraison!]</dd>
                        </div>
                        <div class="PragmaReferencesSubInfo">
                            <dt>Architecte</dt>
                            <dd>[!Res::Architecte!]</dd>
                        </div>
                        <div class="PragmaReferencesSubInfo">
                            <dt>Logements</dt>
                            <dd>...</dd>
                        </div>
                        <div class="PragmaReferencesOtherPictures">
                            [STORPROC ParcImmobilier/Residence/[!Res::Id!]/Donnee/Type=References|Img|1|2]
                                <a class="mb" rel="residence[[!Res::Id!]]" href="/[!Img::URL!].limit.800x600.jpg" title="[!Res::Titre!]">
                                    <img src="/[!Img::URL!].mini.175x335.jpg" alt="[!Res::Titre!]" />
                                </a>
                            [/STORPROC]
                        </div>
                    </div>
                </div>
            [/STORPROC]
        </div>
    </div>
</div>

<div id="PragmaReferencesMinis">
    <div id="PragmaReferencesMinisDefile">
        [STORPROC ParcImmobilier/Residence/Reference=1|Res|0|1000|DateLivraison|DESC]
                [!K:=[!Key!]!]
                [!NR:=[!NbResult!]!]
                [!PosX:=[!Key:*93!]!]
                [STORPROC ParcImmobilier/Residence/[!Res::Id!]/Donnee/Type=References|Img|0|1]
                    <img src="/[!Img::URL!].mini.80x80.jpg" alt="[!Res::Titre!]" />
                    [IF [!K!]>0&&[!K!]<[!NR!]]
                        <div class="PragmaReferencesDate" style="left:[!PosX:-48!]px">|<br />[!Res::DateLivraison!]</div>
                    [/IF]
                [/STORPROC]
        [/STORPROC]
    </div>
</div>

<script type="text/javascript">



    var currentRefPragmaReferences = 0;
    var itemWidthPragmaReferences = 0;
    var itemsPragmaReferences = [];
    var allMinisPragmaReferences = [];
    var fxPragmaReferences = null;
    var intervalPragmaReferences = null;
    var divMinisPragmaReferences = null;
    var divDefileMinisPragmaReferences = null; 
    var fxDefileMinisPragmaReferences = null; 
    var poidsPragmaReferences = 0; 
    var swipePragmaReferences = 0;
    var mouseOverPragmaReference = false;
    
    window.addEvent('domready', function() {
        // Affichage boutons
        $('PragmaReferencesRightBtn').setStyle('display', 'block').addEvent('click', function() { changePragmaReference(1); });
        $('PragmaReferencesLeftBtn').setStyle('display', 'block').addEvent('click', function() { changePragmaReference(-1); });
        // Retrait des bordures et mise sur une seule ligne
        itemsPragmaReferences = $$('div.PragmaReferencesItem');
        itemWidthPragmaReferences = itemsPragmaReferences[0].getDimensions().width;
        itemsPragmaReferences.each(function(item) {
            item.setStyles({
                'float':'left',
                'border':'none',
                'box-shadow':'none' 
            });
        })
        // Mise en place du cadre principal
        $('PragmaReferencesContainer').setStyles({
            'width': '840px',
            'padding': '0 10px',
            'overflow':'hidden'
        });
        $('PragmaReferencesContainerDefile').setStyles({
            'height':'600px',
            'width': (itemWidthPragmaReferences * itemsPragmaReferences.length)+'px',
            'overflow':'hidden'
        });
        // Miniatures
        $('PragmaReferencesMinisDefile').setStyles({
            'width': (93 * itemsPragmaReferences.length + (Browser.Features.Touch ? 20 : 0))+'px',
            'overflow':'hidden'
        });
        // Défilement miniatures
        divMinisPragmaReferences = $('PragmaReferencesMinis');
        divMinisPragmaReferences.setStyle('display', 'block');
        divDefileMinisPragmaReferences = $('PragmaReferencesMinisDefile');
        
        if(Browser.Features.Touch) {
        	// TACTILE
			divMinisPragmaReferences.store('swipe:cancelVertical', true);
            divMinisPragmaReferences.addEvent('swipe', function(e) {
                swipePragmaReferences = (e.start.x - e.end.x) / 2;
                intervalSwipePragmaReferences = setInterval(defileSwipePragmaReferences, 20);
            });
            $('PragmaReferencesContainer').addEvent('swipe', function(e) {
                var delta = (e.start.x - e.end.x);
                if(delta > 0) changePragmaReference(1);
                else changePragmaReference(-1);
            });
        }
        else {
        	// MODE POSITION SOURIS
            divMinisPragmaReferences.addEvent('mousemove', function(e) {
	            var fullWidth = this.getDimensions().width.toInt() + 100;
	            var pos = (e.page.x.toInt()-this.getPosition().x) / fullWidth;
	            mouseOverPragmaReference = true;
	            if(pos < 0.4) poidsPragmaReferences = ((0.4-pos)*(0.4-pos+1)*60).toInt();
	            if(pos > 0.6) poidsPragmaReferences = -((pos-0.6)*(pos-0.6+1)*60).toInt();
	        });
	        divMinisPragmaReferences.addEvent('mouseenter', function(e) {
	            if(fxDefileMinisPragmaReferences != null) fxDefileMinisPragmaReferences.stop();
	        });
	        divMinisPragmaReferences.addEvent('mouseleave', function(e) {
	            mouseOverPragmaReference = false;
	        });
	        setInterval(defileMinisPragmaReferences, 20);
        }
        
        
        // Liens miniatures
        allMinisPragmaReferences = divDefileMinisPragmaReferences.getElements('img');
        divDefileMinisPragmaReferences.getElements('img').each(function(img, idx) {
            img.setStyle('opacity', 0.8);
            img.addEvent('click', function() {
                displayPragmaReference(idx);
            }); 
            img.addEvent('mouseenter', function() {
                if(idx != currentRefPragmaReferences) img.setStyle('opacity', 0.8);
            }); 
            img.addEvent('mouseleave', function() {
                if(idx != currentRefPragmaReferences) img.setStyle('opacity', 0.6);
            }); 
        });                
        // FX
        fxDefileMinisPragmaReferences = new Fx.Tween(divDefileMinisPragmaReferences, {duration:1000, property:'margin-left', transition: Fx.Transitions.Quad.easeOut, link:'cancel'});
        fxPragmaReferences = new Fx.Tween($('PragmaReferencesContainerDefile'), {duration:300, property:'margin-left', link:'cancel'});
        fxOpacityPragmaReferences = new Fx.Tween($('PragmaReferencesContainerDefile'), {duration:150, property:'opacity', link:'cancel'});
        // Lancement initial
        displayPragmaReference();
    });
    
    // Déplacement à gauche ou à droite
    function changePragmaReference( move ) {
        var newidx = currentRefPragmaReferences + move;
        if(newidx < 0) newidx = 0;
        if(newidx >= itemsPragmaReferences.length-1) newidx = itemsPragmaReferences.length-1;
        displayPragmaReference(newidx);
    }

    // Défile vers une résidence et lance l'animation de ses images principales
    function displayPragmaReference( idx ) {
        if(idx == null || idx == currentRefPragmaReferences) return;
        clearInterval(intervalPragmaReferences);
        if(idx != null) {
            allMinisPragmaReferences[currentRefPragmaReferences].setStyle('opacity', 0.6);
            currentRefPragmaReferences = idx;
        }
        allMinisPragmaReferences[currentRefPragmaReferences].setStyle('opacity', 1);
        fxOpacityPragmaReferences.start(0.5).chain(function() {
            fxPragmaReferences.start(-860*currentRefPragmaReferences).chain(function() {
                fxOpacityPragmaReferences.start(1);
            });
        });
        intervalPragmaReferences = setInterval(animPragmaReferences, 5000);
    }

    // Anime les images principales pour la résidence en cours
    function animPragmaReferences() {
        var leftCt = itemsPragmaReferences[currentRefPragmaReferences].getElement('div.PragmaReferencesLeft');
        var liens = leftCt.getElements('a');
        if(liens.length < 2) return;
        var firstLien = liens[0];
        var img = firstLien.getElement('img');
        img.setStyle('opacity', 0);
        firstLien.inject(leftCt, 'bottom');
        img.tween('opacity', 1);
    }
    
    // "onEnterFrame" qui fait défiler les miniatures
    function defileMinisPragmaReferences() {
        if(poidsPragmaReferences) {
            var ml = divDefileMinisPragmaReferences.getStyle('margin-left').toInt();
            var limit = divMinisPragmaReferences.getDimensions().width - divDefileMinisPragmaReferences.getDimensions().width;
            if(mouseOverPragmaReference) {
                var dest = ml+poidsPragmaReferences;
                if(dest > 0) dest = 0;
                if(dest < limit) dest = limit;
                divDefileMinisPragmaReferences.setStyle('margin-left', dest);
            }
            else {
                var dest = ml+poidsPragmaReferences*25;
                if(dest > 0) dest = 0;
                if(dest < limit) dest = limit;
                fxDefileMinisPragmaReferences.start(dest+'px');
                poidsPragmaReferences = 0;
            }
        }
    }
    
    function defileSwipePragmaReferences() {
        if(swipePragmaReferences == 0) clearInterval(intervalSwipePragmaReferences);
        divMinisPragmaReferences.scrollLeft += swipePragmaReferences;
        swipePragmaReferences = (swipePragmaReferences * 0.99).toInt();
    }
</script>

[ELSE]
    <div id="Alternatif">
    	[MODULE ParcImmobilier]
    </div>
    
    <script type="text/javascript">
    	var flashvars = {},
    	params = {wmode:"transparent"},
    	attributes = {wmode:"transparent"}; 
    	swfobject.embedSWF("/Skins/[!Systeme::Skin!]/Swf/Reference.swf", "Alternatif", "925", "750", "7.0.0", flashvars, params, attributes);
    </script>
[/IF]