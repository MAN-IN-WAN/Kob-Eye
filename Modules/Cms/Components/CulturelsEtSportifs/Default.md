++[!Nom!]--
<div id="EncartForm" >
	
	<form id="FormRechAvanc" enctype="application/x-www-form-urlencoded"  method="get" action="">
		<div style="background:#EEEFE9;padding-bottom:5px;">
            <h1 class="Titre">Recherche Organisations</h1>
            [OBJ Reservation|Organisation|O]
            <div class="ForSpe">
                <label>Titre</label>    
                <input type="text" name="Nom" id="Nom" value="">
            </div>
            <p>
                <input type="submit" name="Recherche"  value="Rechercher"  class="BtnSearch" />
            </p>
		</div>
		[IF [!C!]>1]
			//Affiche la liste
			[MODULE Reservation/Organisation/Liste?Chemin=[!REQUETE!]&REQUETE=[!REQUETE!]&Filter=[!FILTER!]]
		[ELSE]
			//Affiche un message Pas de resultat
			[MODULE Reservation/Spectacle/Message?Chemin=[!REQUETE!]&Filter=[!FILTER!]]
		[/IF]
	</form>
</div>