//[IF [!SERVER::REMOTE_ADDR!]=178.22.145.106]style="background:url([!Domaine!]/Skins/[!Systeme::Skin!]/Img/carte2014.png) no-repeat 0 0 ;"[/IF]
<div class="EntoureComposantimageArea"   >
	<div class="TitreBloc" ><h2>Situer nos résidences</h2></div>
	<div class="imageArea">
		<div class="BlocCadre">
			<div id="CartePS" >
				<img src="/Skins/[!Systeme::Skin!]/Img/departements_blank.png" alt="" usemap="#Map" style="height: 222px;width:320px;" id="testzone"  onmouseout="VideinteractiveMap()"/>
				<map id="Map" name="Map" >
					[!AreaLien:=/ParcourirOffre/Departement!]
					<area id="area31" onmouseover="interactiveMap(3)"  href="[!AreaLien!]/Haute_Garonne" alt="" title="" shape="poly" coords="20,93,26,92,31,91,36,96,40,93,39,88,52,89,64,119,59,125,59,133,53,134,47,130,39,131,35,141,32,143,28,140,24,144,23,149,10,160,9,164,0,166,0,156,4,150,0,145,0,141,5,136,4,125,19,120,23,122,25,112,29,109,30,105,20,93,22,93,22,96,22,93" />
					<area id="area66" onmouseover="interactiveMap(4)"  href="[!AreaLien!]/Pyrenees_Orientales" alt="" title="" shape="poly" coords="93,160,95,158,91,168,80,169,61,182,74,192,86,191,118,192,135,186,127,173,124,158,115,153,109,155,103,159,96,158,89,161,91,167,89,168,90,168" />
					<area id="area34" onmouseover="interactiveMap(1)"  href="[!AreaLien!]/Herault" alt="" title="" shape="poly" coords="95,118,104,118,105,108,111,107,116,105,143,84,153,88,158,83,168,93,174,92,180,99,178,113,178,118,173,112,152,123,142,135,130,130,124,125,116,124,108,129,104,127,100,127,95,117" />
					<area id="area30" onmouseover="interactiveMap(2)"  href="[!AreaLien!]/Gard" alt="" title="" shape="poly" coords="172,48,183,58,186,53,196,56,198,57,199,64,199,68,203,67,204,71,209,75,211,80,207,81,201,84,199,89,196,95,195,102,190,108,184,116,180,118,178,116,180,99,177,94,174,92,168,92,159,82,151,86,143,84,143,78,137,75,135,66,145,73,153,71,159,66,168,67,167,50,173,47,173,49" />
					<area id="area13" onmouseover="interactiveMap(5)"  href="[!AreaLien!]/Bouches_du_Rhone" alt="" title="" shape="poly" coords="209,81,232,95,235,98,241,97,253,100,256,101,258,113,249,119,248,125,221,120,227,113,217,119,213,121,196,119,182,116,195,104,197,93,202,85,209,81" />
					<area id="area84" onmouseover="interactiveMap(6)"  href="[!AreaLien!]/Vaucluse" alt="" title="83 - Var" shape="poly" coords="199,56,212,56,214,65,227,58,237,64,239,68,246,67,249,67,249,71,252,71,264,90,263,94,256,100,249,98,241,95,237,97,233,97,230,94,208,81,208,77,205,71,203,66,199,67,198,62,198,57" />
					<area id="area83" onmouseover="interactiveMap(7)"  href="[!AreaLien!]/Var" alt="" title="" shape="poly" coords="264,94,272,94,274,96,278,95,284,87,290,90,301,85,303,90,315,99,321,103,312,109,313,113,306,118,295,133,279,140,265,140,255,136,251,125,249,124,253,117,258,113,257,100,260,95,263,95,271,95,276,97,285,88,262,95" />
				</map>
			</div>		
			<a href="/ParcourirOffre"  title="Voir toutes nos résidences" class="ImageAreaLienTout">... Voir toutes nos résidences</a>
		</div>	
		
	</div>	
</div>

<div id="DivMenu" >
	<div id="Dept1" style="display:none;position:absolute;top:492px;left:487px;" class="BlocMenuContextuel" > 
		//<a href="/ParcourirOffre/Departement/Herault" class="MenuDeptmap">34 - Hérault</a><br/>
		[!Tot:=0!]
		[STORPROC ParcImmobilier/Departement/Code=34/Ville|V|||Nom|DESC]
			[COUNT ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|NbRes]	
			[IF [!NbRes!]][!Tot:=1!][/IF]
			[STORPROC ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|Res]
				- <a href="[!AreaLien!]/Herault/Ville/[!V::Lien!]" class="MenuVillemap">[!V::Nom!]</a> - <a href="[!AreaLien!]/Herault/Ville/[!V::Lien!]/Residence/[!Res::Lien!]" class="MenuResidmap">[!Res::Titre!]</a><br />
			[/STORPROC]
				
			
		[/STORPROC]
		[IF [!Tot!]!=1]<span class="MenuVillemap">Pas de résidence pour le moment</span>[/IF]
	</div>
	<div id="Dept2" style="display:none;position:absolute;top:464px;left:530px;" class="BlocMenuContextuel"  >
		//<a href="/ParcourirOffre/Departement/Gard" class="MenuDeptmap">30 - Gard</a><br/>
		[!Tot:=0!]
		[STORPROC ParcImmobilier/Departement/Code=30/Ville|V]
			[COUNT ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|NbRes]	
			[IF [!NbRes!]][!Tot:=1!][/IF]
			[STORPROC ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|Res]
				- <a href="[!AreaLien!]/Gard/Ville/[!V::Lien!]" class="MenuVillemap">[!V::Nom!]</a> - <a href="[!AreaLien!]/Gard/Ville/[!V::Lien!]/Residence/[!Res::Lien!]" class="MenuResidmap">[!Res::Titre!]</a><br />
			[/STORPROC]
				
			
		[/STORPROC]
		[IF [!Tot!]!=1]<span class="MenuVillemap">Pas de résidence pour le moment</span>[/IF]
	</div>
	<div id="Dept3" style="display:none;position:absolute;top:504px;left:389px;" class="BlocMenuContextuel"  >
		//<a href="/ParcourirOffre/Departement/Haute_Garonne " class="MenuDeptmap">31 - Haute-Garonne</a><br/>
		[!Tot:=0!]
		[STORPROC ParcImmobilier/Departement/Code=31/Ville|V]
			[COUNT ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|NbRes]	
			[IF [!NbRes!]][!Tot:=1!][/IF]
			[STORPROC ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|Res]
				- <a href="[!AreaLien!]/Haute_Garonne/Ville/[!V::Lien!]" class="MenuVillemap">[!V::Nom!]</a> - <a href="[!AreaLien!]/Haute_Garonne/Ville/[!V::Lien!]/Residence/[!Res::Lien!]" class="MenuResidmap">[!Res::Titre!]</a><br />
			[/STORPROC]
				
			
		[/STORPROC]
		[IF [!Tot!]!=1]<span class="MenuVillemap">Pas de résidence pour le moment</span>[/IF]
	</div>
	<div id="Dept4" style="display:none;position:absolute;top:561px;left:447px;" class="BlocMenuContextuel" >
		//<a href="/ParcourirOffre/Departement/Pyrenees_Orientales" class="MenuDeptmap">66 - Pyrénées Orientales</a><br/>
		[!Tot:=0!]
		[STORPROC ParcImmobilier/Departement/Code=66/Ville|V]
			[COUNT ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|NbRes]	
			[IF [!NbRes!]][!Tot:=1!][/IF]
			[STORPROC ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|Res]
				- <a href="[!AreaLien!]/Pyrenees_Orientales/Ville/[!V::Lien!]" class="MenuVillemap">[!V::Nom!]</a> - <a href="[!AreaLien!]/Pyrenees_Orientales/Ville/[!V::Lien!]/Residence/[!Res::Lien!]" class="MenuResidmap">[!Res::Titre!]</a><br />
			[/STORPROC]
				
			
		[/STORPROC]
		[IF [!Tot!]!=1]<span class="MenuVillemap">Pas de résidence pour le moment</span>[/IF]
	</div>
	<div id="Dept5" style="display:none;position:absolute;top:488px;left:561px;" class="BlocMenuContextuel" >
		//<a href="/ParcourirOffre/Departement/Bouches_du_Rhone" class="MenuDeptmap">13 - Bouches du Rhone</a><br/> 
		[!Tot:=0!]
		[STORPROC ParcImmobilier/Departement/Code=13/Ville|V]
			[COUNT ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|NbRes]	
			[IF [!NbRes!]][!Tot:=1!][/IF]
			[STORPROC ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|Res]
				- <a href="[!AreaLien!]/Bouches_du_Rhone/Ville/[!V::Lien!]" class="MenuVillemap">[!V::Nom!]</a> - <a href="[!AreaLien!]/Bouches_du_Rhone/Ville/[!V::Lien!]/Residence/[!Res::Lien!]" class="MenuResidmap">[!Res::Titre!]</a><br />
			[/STORPROC]
		[/STORPROC]
		[IF [!Tot!]!=1]<span class="MenuVillemap">Pas de résidence pour le moment</span>[/IF]
	</div>
	<div id="Dept6" style="display:none;position:absolute;top:465px;left:585px;" class="BlocMenuContextuel" >
		//<a href="/ParcourirOffre/Departement/Vaucluse" class="MenuDeptmap">84 - Vaucluse</a><br/>
		[!Tot:=0!]
		[STORPROC ParcImmobilier/Departement/Code=84/Ville|V]
			[COUNT ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|NbRes]	
			[IF [!NbRes!]][!Tot:=1!][/IF]
			[STORPROC ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|Res]
				- <a href="[!AreaLien!]/Vaucluse/Ville/[!V::Lien!]" class="MenuVillemap">[!V::Nom!]</a> - <a href="[!AreaLien!]/Vaucluse/Ville/[!V::Lien!]/Residence/[!Res::Lien!]" class="MenuResidmap">[!Res::Titre!]</a><br />
			[/STORPROC]
		[/STORPROC]
		[IF [!Tot!]!=1]<span class="MenuVillemap">Pas de résidence pour le moment</span>[/IF]
	</div>
	<div id="Dept7" style="display:none;position:absolute;top:507px;left:628px;" class="BlocMenuContextuel" >
		//<a href="/ParcourirOffre/Departement/Var" class="MenuDeptmap">83 - Var</a><br/>
		[!Tot:=0!]
		[STORPROC ParcImmobilier/Departement/Code=83/Ville|V]
			[COUNT ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|NbRes]	
			[IF [!NbRes!]][!Tot:=1!][/IF]
			[STORPROC ParcImmobilier/Ville/[!V::Id!]/Residence/Logement=1&Reference=0|Res]
				- <a href="[!AreaLien!]/Var/Ville/[!V::Lien!]" class="MenuVillemap">[!V::Nom!]</a> - <a href="[!AreaLien!]/Var/Ville/[!V::Lien!]/Residence/[!Res::Lien!]" class="MenuResidmap">[!Res::Titre!]</a><br />
			[/STORPROC]
		[/STORPROC]
		[IF [!Tot!]!=1]<span class="MenuVillemap">Pas de résidence pour le moment</span>[/IF]
	</div>
</div>
