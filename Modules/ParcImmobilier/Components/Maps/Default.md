[INFO [!Lien!]|I]
[STORPROC [!I::Historique!]|N1|2|1][!LeDpt:=[!N1::Value!]!][/STORPROC] // departement
[IF [!TITRE!]!=]<div class="TitreTexteCarte"><h2>[!TITRE!]</h2></div>[/IF]
[!Req:=ParcImmobilier!]

[IF [!RR_Ville!]!=]
	//MODIF SUITE A REFONTE DE LA REFONTE 20 SEPTEMBRE 2013
	[STORPROC ParcImmobilier/Ville/ResidenceVille/[!RR_Ville!]|V|0|1]
		[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D]
			[!ReqDept+=[!Req!]!]
			[!ReqDept+=/Departement/[!D::Id!]/Ville/*!]
		[/STORPROC]
	[/STORPROC]
[ELSE]
	[IF [!N1::Value!]]
		[STORPROC ParcImmobilier/Departement/[!N1::Value!]|D]
			[!Req+=/Departement/[!D::Id!]!]
		[/STORPROC]
	[/IF]
	[!Req+=/Ville/*!]
[/IF]

[!Req+=/Residence/Logement=1&&Reference=0!]
[!ReqDept+=/Residence/Logement=1&&Reference=0!]
[IF [!RR_Projet!]!=]
	[IF [!RR_Projet!]=Habiter]
		[!Req+=&&ProjetHabiter=1!]
	[/IF]
	[IF [!RR_Projet!]=Investir]
		[!Req+=&&ProjetInvestir=1!]
	[/IF]
[/IF]
[IF [!RR_Projet2!]!=]
	[IF [!RR_Projet2!]=STANDING]
		[!Req+=&&Projet2Standing=1!]
	[/IF]
	[IF [!RR_Projet2!]=AIDEE]
		[!Req+=&&Projet2Aidee=1!]
	[/IF]
[/IF]
[IF [!RR_Projet3!]!=]
	[IF [!RR_Projet3!]=DUFLOT]
		[!Req+=&&Projet2Duflot=1!]
	[/IF]
	[IF [!RR_Projet3!]=LMNP]
		[!Req+=&&Projet2LMNP=!]
	[/IF]
[/IF]
////////// Filtres Recherche //////////
[IF [!RR_Type!]!=||[!RR_Prix!]!=]
	[!Req+=/TypeLogement/!]
	[IF [!RR_Type!]!=]
		[!Req+=Titre~[!RR_Type!]!]
	[/IF]
	[IF [!RR_Type!]!=&&[!RR_Prix!]!=]
		[!Req+=&&!]
	[/IF]
	[IF [!RR_Prix!]!=]
		[IF [!RR_Prix!]=T1]
			[!Req+=PrixMin<=120000!]
		[/IF]
		[IF [!RR_Prix!]=T2]
			[!Req+=PrixMin<=160000!]
			[!Req+=(PrixMax>=120000||PrixMax=-)!]
		[/IF]
		[IF [!RR_Prix!]=T3]
			[!Req+=PrixMin<=190000!]
			[!Req+=(PrixMax>=160000||PrixMax=-)!]
		[/IF]
		[IF [!RR_Prix!]=T4]
			[!Req+=PrixMin<=260000!]
			[!Req+=(PrixMax>=190000||PrixMax=-)!]
		[/IF]
		[IF [!RR_Prix!]=T5]
			[!Req+=PrixMin<=350000!]
			[!Req+=(PrixMax>=260000||PrixMax=-)!]
		[/IF]
		[IF [!RR_Prix!]=T6]
			[!Req+=PrixMin>=350000!]
		[/IF]
	[/IF]
	[!Selection:=distinct(j2.Id),j2.*!]
[/IF]

<div class="Carte" id="MapDiv">
	<script type="text/javascript">
		var vLocations = new Array();
	</script>
	
	[!Position:=0!]
//[!Req!]

[IF [!RR_Ville!]!=]
	[!NoResult:=0!]
	// d'abord on recherche les residences de la ville
	[!Req+=&&ResidenceVille=[!RR_Ville!]&Latitude!=&Longitude!=!]
	[COUNT [!Req!]|NbResid]
	[STORPROC [!Req!]|Resid]
        	[STORPROC ParcImmobilier/Ville/Residence/[!Resid::Id!]|V][/STORPROC]
        	[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D][/STORPROC]
		<script type="text/javascript">
			vLocations[[!Position!]] = "[!Resid::Titre!]/[!D::Nom!]/[!Resid::Longitude!]/[!Resid::Latitude!]/[!Resid::Lien!]/[!V::Nom!]/[!D::Code!]/[!V::Lien!]/[!D::Lien!]";
		</script>
		[!Position+=1!]
	[/STORPROC]
	[!Req:=[!ReqDept!]!]
	[!Req+=&&ResidenceVille!=[!RR_Ville!]&Latitude!=&Longitude!=!]
	[STORPROC [!Req!]|Resid]
        	[STORPROC ParcImmobilier/Ville/Residence/[!Resid::Id!]|V][/STORPROC]
        	[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D][/STORPROC]
		<script type="text/javascript">
			vLocations[[!Position!]] = "[!Resid::Titre!]/[!D::Nom!]/[!Resid::Longitude!]/[!Resid::Latitude!]/[!Resid::Lien!]/[!V::Nom!]/[!D::Code!]/[!V::Lien!]/[!D::Lien!]";
		</script>
		[!Position+=1!]
	[/STORPROC]

[ELSE]
	[COUNT [!Req!]|NbResid]
	[STORPROC [!Req!]&Latitude!=&Longitude!=|Resid]
        	[STORPROC ParcImmobilier/Ville/Residence/[!Resid::Id!]|V][/STORPROC]
        	[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D][/STORPROC]
		<script type="text/javascript">
			vLocations[[!Position!]] = "[!Resid::Titre!]/[!D::Nom!]/[!Resid::Longitude!]/[!Resid::Latitude!]/[!Resid::Lien!]/[!V::Nom!]/[!D::Code!]/[!V::Lien!]/[!D::Lien!]";
		</script>
		[!Position+=1!]
	[/STORPROC]

[/IF]
	
	<div id="carte" style="width:[!DIVLARGEUR!]; height:[!DIVHAUTEUR!]"></div>
	
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
		window.addEvent('domready', function() {
			var marqueur_courant = null;
			var optionsCarte = {
				zoom: 7,
				center: new google.maps.LatLng(43.564472,3.449707),
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var maCarte = new google.maps.Map(document.getElementById("carte"), optionsCarte);

			var zoneMarqueurs = new google.maps.LatLngBounds();

			for(var i=0; i<vLocations.length; i++)	{
				var vInformations = vLocations[i].split('/');
				var vTitre = vInformations[0];
				var vDept = vInformations[1];
				var vLongitude = vInformations[2];
				var vLatitude = vInformations[3];
				var vResidId = vInformations[4];
				var vVille = vInformations[5];
				var vCodeDept = vInformations[6];
				var vVilleUrl = vInformations[7];
				var vDeptUrl = vInformations[8];
				if(vLongitude != '' && vLatitude != '') {
					var point = new google.maps.LatLng(vLatitude, vLongitude);
					var optionsMarqueur = {
						position: new google.maps.LatLng(vLatitude,vLongitude),
						map: maCarte,
						title: vVille
						}
					var marqueur = new google.maps.Marker(optionsMarqueur);
					iconFile = '[!Domaine!]/Skins/[!LASKIN!]/Img/punaise.png';
					marqueur.setIcon(iconFile) 
					zoneMarqueurs.extend(marqueur.getPosition());
					var contenuInfoBulle = '<div><h3>' +vTitre+'</h3><br />'+vCodeDept+' '+ vVille+'<br />'+
										'<a href="[!Domaine!]/ParcourirOffre/Departement/'+vDeptUrl+'/Ville/'+vVilleUrl+'/Residence/'+vResidId+'">Voir le programme immobilier</a>' +
										'</div>';
					var infoBulle = new google.maps.InfoWindow({
						content: contenuInfoBulle,
						position: point });
					// Association de l'infobulle au marqueur
					marqueur._infowindow = infoBulle;
				
					// Création de la fonction Clic
					google.maps.event.addListener(marqueur, 'mouseover', function() { 						
						if(marqueur_courant){
							marqueur_courant._infowindow.close();
						}		
						marqueur_courant = this;
						// ! IMPORTANT on utilise this et non pas marqueur
						this._infowindow.open(maCarte, this);
					});
				}
			}
			maCarte.fitBounds(zoneMarqueurs);
			[IF [!NbResid!]=1]maCarte.setZoom(3);[/IF]
		});
		
	</script>
</div>
