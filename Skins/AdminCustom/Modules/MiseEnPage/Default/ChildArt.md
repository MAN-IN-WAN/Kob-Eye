<div class="childArt">
        [STORPROC [!Query!]/Contenu|Cons]
                <h3>Les contenus de cet article</h3>
                [LIMIT 0|10000]
                        <div class="artContent">
                                <div class="contentHead">
                                        <h4>
                                                <span class="conTitre">[!Cons::Titre!]</span>
                                        </h4>
                                        <a href="/MiseEnPage/Contenu/[!Cons::Id!]/Supprimer" class="delButton" title="Supprimer le contenu">Supprimer</a>
                                        <a href="/MiseEnPage/Contenu/[!Cons::Id!]/Modifier" class="modButton" title="Modifier le contenu">Modifier</a>
                                        <div class="clear"></div>
                                </div>
                                <div class="contentBody">
                                        <div class="contentShowcase bloc" data-contentid="[!Cons::Id!]">
                                                [!freeRatio:=100!]
                                        [STORPROC MiseEnPage/Contenu/[!Cons::Id!]/Colonne|Cols|||Ordre|ASC]
                                                [LIMIT 0|10000]
                                                        [!type:=[!Cols::getType()!]!]
                                                        [!tempRatio:=[!Cols::Ratio!]!]
                                                        [IF [!tempRatio!]~=%]
                                                                [!Ratio:=[!tempRatio!]!]
                                                        [ELSE]
                                                                [!Ratio:=[!tempRatio!]%!]
                                                        [/IF]
                                                        <div class="contentCol type[!type!]" style="width:[!Ratio!];" draggable="true" title="Deplacer la colonne [!Cols::Titre!]" data-id="[!Cols::Id!]">
                                                                <div class="colTitre">[!Cols::Titre!]</div>
                                                                <div class="boutons">
                                                                        [STORPROC MiseEnPage/Colonne/[!Cols::Id!]/Bouton|Bout|||Ordre|ASC]
                                                                                <a href="/MiseEnPage/Colonne/[!Cols::Id!]/ModBou" title="modifier le bouton de la colonne"  class="modButton [IF [!Cols::Ratio!]>24]">[!Bout::Label!][ELSE]short">&nbsp;[/IF]</a>
                                                                                [NORESULT]
                                                                                        <a href="/MiseEnPage/Colonne/[!Cols::Id!]/AjouterBouton" title="Ajouter un bouton à la colonne"  class="addButton [IF [!Cols::Ratio!]>24]">Ajouter Bouton[ELSE]short">&nbsp;[/IF]</a>
                                                                                [/NORESULT]
                                                                        [/STORPROC]
                                                                </div>
                                                                <hr/>
                                                                <div class="colButtons">
                                                                        <a href="/MiseEnPage/Colonne/[!Cols::Id!]/Modifier" title="Modifier la colonne" class="modButton [IF [!Cols::Ratio!]>24]">Modifier[ELSE]short">&nbsp;[/IF]</a>
                                                                        <a href="/MiseEnPage/Colonne/[!Cols::Id!]/Supprimer" title="Supprimer la colonne"  class="delButton [IF [!Cols::Ratio!]>24]">Supprimer[ELSE]short">&nbsp;[/IF]</a>
                                                                        <div class="clear"></div>
                                                                </div>                                                       
                                                                <div class="colRatio">Largeur : [!Ratio!] </div>
                                                        </div>
                                                        [!freeRatio:=[!Utils::remainingRatio([!freeRatio!],[!Ratio!])!]!]
                                                [/LIMIT]
                                        [/STORPROC]
                                                [IF [!freeRatio!]>0]
                                                        <a href="/MiseEnPage/Contenu/[!Cons::Id!]/AjouterColonne" class="addColumn [IF [!freeRatio!]<9] short[/IF]">
                                                                [IF [!freeRatio!]>9]
                                                                        <div class="addColTxt">Ajouter</div>
                                                                        <div class="colRatio">Libre: <span class="freeRatio">[!freeRatio!]</span>% </div>
                                                                [ELSE]
                                                                        <div class="addColTxt">+</div>
                                                                        <div class="colRatio"><span class="freeRatio">[!freeRatio!]</span>% </div>
                                                                [/IF]
                                                        </a>
                                                [/IF]
                                        </div>
                                        
                                </div>
                        </div>
                [/LIMIT]
                [NORESULT]
                        <p>Aucun Contenu n'est disponible dans cet Article.</p>
                [/NORESULT]
        [/STORPROC]
        <a href="[!I::LastId!]/AjouterContenu" class="addButton" title="Ajouter du contenu">Ajouter</a>
</div>
<script type="text/javascript">
        function allowDrop(e) {
                e.preventDefault();
        }
        function getPositions(col) {
                var bounds = col.getBoundingClientRect();
                var posX = Math.ceil(bounds.left);
                var width = col.offsetWidth;
                $(col).data('posx1',posX);
                $(col).data('posx2',posX+width);
        }
        function clearPostitions() {
                $('.contentCol').removeData();
        }
        function recalcFreeSpace() {
                var contents = $('.contentShowcase');
                contents.each(function(){
                        var id = $(this).data('contentid');
                        var cols = $(this).children('.contentCol');
                        var free = 100;
                        cols.each(function(){
                                var per = $(this).attr('style').match(/[0-9]+/);
                                free -= parseInt(per[0]);
                        });
                        $(this).children('.addColumn').remove();
                        if (free > 9) {
                                var link ='<a href="/MiseEnPage/Contenu/'+id+'/AjouterColonne" class="addColumn">\
                                        <div class="addColTxt">Ajouter</div> \
                                        <div class="colRatio">Libre: <span class="freeRatio">'+free+'</span>% </div> \
                                        </a>';
                        } else if(free > 0) {
                                var link ='<a href="/MiseEnPage/Contenu/'+id+'/AjouterColonne" class="addColumn short">\
                                        <div class="addColTxt">+</div> \
                                        <div class="colRatio"><span class="freeRatio">'+free+'</span>% </div> \
                                        </a>';
                        }
                        if (link) {
                                $(this).append(link);
                        }
                });
        }

        
        
        var dragging, mouseOffsetX;
        $('.contentCol').on({
                                'dragstart': dragstart,
                                'dragend': dragend,
                                'dragover': dragoverCol,
                                'dragleave': dragleaveCol
                        });
        $('.contentShowcase').on({
                                'dragover dragenter': dragover,
                                'dragleave': dragleave,
                                'drop': drop
                        });

          
        function dragstart(e) {
                e.stopPropagation();
                var dt = e.originalEvent.dataTransfer;
                if (dt) {
                        dt.effectAllowed = 'move';
                        dt.setData('text/html', '');
                        dragging = $(this);
                        //On reinitialise les positions des colones
                        clearPostitions();
                        //On consigne le decalage de la souris par rapport au bord gauche de la div
                        //mouseOffsetX = e.originalEvent.offsetX;
                }
        }
          
        function dragover(e) {
                e.stopPropagation();
                e.preventDefault();
                var dt = e.originalEvent.dataTransfer;
                if (dt && dragging) {
                        dt.dropEffect = 'move';
                        dragging.hide();
                }
                return false;
        }
        function dragoverCol(e) {
                e.stopPropagation();
                e.preventDefault();
                                        
                var event = e.originalEvent;
                var leftPos = event.offsetX;
                var dt = e.originalEvent.dataTransfer;
                if (dt && dragging) {
                        if (!$(this).data('posx1') && !$(this).data('posx2')) {
                                getPositions(this);
                        }
                        var middleX = ($(this).data('posx2') - $(this).data('posx1'))/2;
                        
                        dragging.data('col',$(this));
                        
                        if (leftPos <= middleX) {
                                $(this).css('border-left-width','3px');
                                $(this).css('border-right-width','');
                                dragging.data('relative','before');
                        } else {
                                $(this).css('border-right-width','3px');
                                $(this).css('border-left-width','');
                                dragging.data('relative','after');
                        }
                        
                }
                return false;
        }
          
        function dragleave(e) {
                e.stopPropagation();
        }
        function dragleaveCol(e) {
                e.stopPropagation();
                $(this).css('border-left-width','');
                $(this).css('border-right-width','');
                dragging.data('col','');
                dragging.data('relative','');
        }
          
        function drop(e) {
                e.stopPropagation();
                e.preventDefault();
                if (dragging) {
                        var dropzone = $(this);
                        dragging.data('dropzone', dropzone);
                        dragging.trigger('dragend');
                }
                
                $('.contentCol').css('border-left-width','');
                $('.contentCol').css('border-right-width','');
                return false;
        }
          
        function dragend(e) {
                if (dragging) {
                        var dropzone = dragging.data('dropzone');
                        var parentId = dragging.parent('.contentShowcase').data('contentid');
                        var newParentId = dropzone.data("contentid");
                        var modParent = (parentId != newParentId)? newParentId : false;
                        var modChild = dragging.data('id');
                        
                        if (dropzone) {
                                var repere = dragging.data('col');
                                if (repere) {
                                        if (dragging.data('relative') == 'after') {
                                                dragging.insertAfter(repere);
                                        } else {
                                                dragging.insertBefore(repere);
                                        }
                                } else {
                                        dragging.appendTo(dropzone);
                                }
                                var ordered = new Array();
                                dropzone.children('.contentCol').each(function(key,col){
                                        ordered.push($(col).data('id'));
                                });
                                
                                $.ajax({
                                        url: "/MiseEnPage/Ajax/ReorderCols.json",
                                        data: { order : ordered, modParent : modParent, modChild : modChild},
                                        method: 'POST',
                                        error: function(req,status,error){
                                                        console.log('Oops : '+ error);
                                        },
                                        success: function(data,status,req){
                                                //console.log(data);
                                                if (parentId != newParentId) {
                                                        recalcFreeSpace();
                                                }
                                        }
                                }); 
                        }
                        dragging.show();
                        clearPostitions();
                }
                dragging = undefined;
        }
        
</script>
