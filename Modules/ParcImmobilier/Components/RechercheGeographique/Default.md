<div id="EntoureComposantimageArea">
	<div class="TitreBloc">Recherche géographique</div>
	<div class="imageArea">
		<div class="BlocCadre">
			<div id="CartePS">
				<img src="/Skins/[!Systeme::Skin!]/Img/carteEPblank.png" alt="" usemap="#Map" />
				<map id="Map" name="Map">
					[!AreaLien:=/[!Systeme::getMenu(ParcImmobilier/Residence)!]!]
					[OBJ ParcImmobilier|Residence|RModel]
					[STORPROC ParcImmobilier/Departement|Dep]
                        [COUNT [!RModel::getMesResidences([!Dep::Id!])!]|Cpt]
                        [!Cpt[!Dep::Code!]:=[!Cpt!]!]
					[/STORPROC]
					<area id="area31" title="[!Cpt31!] programmes en Haute Garonne" href="[!AreaLien!]?Departement=3" shape="poly" coords="40,94,48,91,55,96,61,90,70,90,82,119,77,128,77,133,59,131,53,144,46,140,43,145,45,150,29,159,27,165,16,165,22,149,18,144,24,135,22,125,40,122,44,112,49,110,50,105,50,106,49,106" href="31" />
					<area id="area66" title="[!Cpt66!] programmes en Pyrénées Orientales" href="[!AreaLien!]?Departement=8" shape="poly" coords="81,185,109,169,109,165,115,160,124,161,130,156,144,160,145,173,154,188,137,193,90,192" href="66" />
					<area id="area34" title="[!Cpt34!] programmes dans l'Hérault" href="[!AreaLien!]?Departement=1" shape="poly" coords="134,107,160,86,172,91,172,87,180,84,187,93,197,97,199,117,195,113,171,125,162,135,159,137,146,129,138,125,131,129,119,128,116,120,123,119,125,109,134,107" href="34" />
					<area id="area30" title="[!Cpt30!] programmes dans le Gard" href="[!AreaLien!]?Departement=2" shape="poly" coords="192,48,186,51,189,66,179,69,172,73,169,74,155,69,153,72,163,78,163,85,170,91,178,85,189,94,199,99,199,117,204,118,215,106,219,88,230,82,230,77,224,72,224,67,220,68,217,59,207,55,202,60" href="30" />
					<area id="area13" title="[!Cpt13!] programmes dans les Bouches du Rhône" href="[!AreaLien!]?Departement=12" shape="poly" coords="230,82,255,97,263,97,277,102,280,114,270,125,204,119,214,104,218,88" href="13" />
					<area id="area84" title="[!Cpt84!] programmes dans le Vaucluse" href="[!AreaLien!]?Departement=7" shape="poly" coords="219,57,231,55,234,56,235,65,248,60,261,68,270,67,283,90,283,95,276,99,254,98,230,83,230,77,224,74,225,66,219,69,219,61" href="84" />
					<area id="area83" title="[!Cpt83!] programmes dans le Var" href="[!AreaLien!]?Departement=10" shape="poly" coords="282,95,297,97,306,87,311,92,325,86,339,102,334,112,335,116,329,118,320,132,301,141,287,142,277,137,270,128,279,113,275,100" href="83" />
				</map>
			</div>
			<div id="ImageAreaTout">
				<a href="[!AreaLien!]?Affichage=Programmes" title="Voir toutes les programmes">Voir tous les programmes</a>
			</div>	
		</div>	
	</div>	
</div>

<script type="text/javascript">
	window.addEvent('domready', function() {
		// Création de la bulle
		new Element('div', {
			'id':'PopupBulle',
			'styles': {
				'display':'none'
			}
		}).inject(document.body);

		// Evenements sur les map area
		$$('area').each(function(a) {
			a.addEvent('mouseover', function(e) {
				// Mise en évidence de la zone
				var id = $(a).get('id');
				var n = 0;
				if(id == 'area31') n = 4;
				if(id == 'area66') n = 3;
				if(id == 'area34') n = 1;
				if(id == 'area30') n = 2;
				if(id == 'area13') n = 5;
				if(id == 'area84') n = 6;
				if(id == 'area83') n = 7;
				interactiveMap(n);
				// Affichage de la bulle
				var text = $(a).get('title');
				var posX = e.page.x - 40;
				var posY = e.page.y - 60;
				$('PopupBulle').setStyles({
					'display':'block',
					'top':posY,
					'left':posX
				});
				//$('PopupBulle').tween('opacity', '1');
				$('PopupBulle').set('html', text);
			});
            a.addEvent('mousemove', function(e) {
                var posX = e.page.x - 40;
                var posY = e.page.y - 60;
                $('PopupBulle').setStyles({
                    'top':posY,
                    'left':posX
                });
            });
			a.addEvent('mouseout', function() {
				interactiveMap(0);
				$('PopupBulle').setStyle('display','none');
			});
		});
	});
	function interactiveMap( n ) {
		if(n == null) n = 0;
		$('CartePS').setStyle('background-position', '100% -' + 212 * n + 'px');
	}
</script>