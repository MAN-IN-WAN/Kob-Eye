		<ul class="accordion">
		    [STORPROC [!Systeme::User::Menus!]|M]
                    <li [IF [!Lien!]~[!M::Url!]]class="k-state-active k-state-selected"[/IF]>
                        <a [IF [!Lien!]~[!M::Url!]]class="k-state-selected"[/IF] href="/[!M::Url!]">[!M::Titre!]<span>[!M::Id!]</span></a>
			[STORPROC [!M::Menus!]|M2]
                        <ul class="sub-menu">
			    [LIMIT 0|100]
                            <li [IF [!Lien!]~[!M2::Url!]]class="k-state-active"[/IF]> <a [IF [!Lien!]~[!M2::Url!]]class="k-state-selected"[/IF] href="/[!M::Url!]/[!M2::Url!]"><em>0[!Pos!]</em>[!M2::Titre!]<span>[!M2::Id!]</span></a>
				[STORPROC [!M2::Menus!]|M3]
				<ul class="sub-menu">
				    [LIMIT 0|100]
	                            <li [IF [!Lien!]~[!M3::Url!]]class="k-state-active"[/IF]> <a [IF [!Lien!]~[!M3::Url!]]class="k-state-selected"[/IF] href="/[!M::Url!]/[!M2::Url!]/[!M3::Url!]">[!M3::Titre!]</a>
				    </li>
				    [/LIMIT]
				</ul>
				[/STORPROC]
			    </li>
			    [/LIMIT]
                        </ul>
			[/STORPROC]
                    </li>
		    [/STORPROC]
                </ul>
		<script type="text/javascript">
                $(document).ready(function() {
		    $('.accordion > li  > a').click(function (event) {
			if ($(this).hasClass("k-state-selected")) {
			    //on replit
			    resetSelected();
			}else{
			    //on déplit
			    var el = $(this).parent('li');
			    //on reinitialise l'element selectionné
			    resetSelected();
			    //on sélectionne celui ci
			    selectItem(el);
			}
			event.preventDefault();
		    });
		    $('.accordion > li.k-state-selected').each(function (index,item){
			    console.log("initialise");
			    $(item).children('ul').css('height','auto');
			    selectItem(item);
		    });
                });
		function resetSelected() {
		    //on reinitialize la hauteur
		    $('.accordion li.k-state-selected ul').css('height','0');
		    //on supprime la classe
		    $('.accordion li.k-state-active').removeClass('k-state-active');
		    $('.accordion li.k-state-selected').removeClass('k-state-selected');
		    $('.accordion li a.k-state-selected').removeClass('k-state-selected');
		}
		function selectItem(el) {
		    var hauteur = 0;
		    //calcul de la hauteur à déplier
		    $(el).children('ul').children('li').each(function (index,item){
			hauteur+=$(item).height();
		    });
		    //on applique la hauteur
		    $(el).children('ul').css('height',hauteur+'px');
		    //on supprime la classe
		    $(el).addClass('k-state-active');
		    $(el).addClass('k-state-selected');
		    $(el).children('a').addClass('k-state-selected');
		}
            </script>