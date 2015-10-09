<table id="Onglets">
	<tr>
		<td class="first [IF [!Tab!]=] current [/IF]"><a href="/[!Lien!]?Affichage=Client">Informations</a> </td>
		<td [IF [!Tab!]=Chantier] class="current" [/IF]><a href="/[!Lien!]?Affichage=Client&Tab=Chantier">Photos de chantier</a></td>
	</tr>
</table>
<div class="BlocDetailResidence BlocDetailResidenceClient [IF [!Tab!]!=] nodisplay [/IF]">
	<div class="nodisplay"><h3>Descriptif</h3></div>
	[IF [!R::Accroche!]!=]<div class="accroche">[!R::Accroche!]</div>[/IF]<div class="infosRes">[!R::Texte!]</div>
</div>


<div class="BlocDetailResidence BlocDetailResidenceClient [IF [!Tab!]!=Chantier] nodisplay [/IF]">
	<div class="nodisplay"><h3>Photos de Chantier</h3></div>
	<div class="infosclients">
		[STORPROC ParcImmobilier/Residence/[!R::Id!]/Donnee/Type=Client|Client|0|50|Titre|ASC]
			<div class="Item">
				<a href="/[!Client::URL!]" alt="[!Client::Titre!]" rel="link" target="_blank">[SUBSTR 30][!Client::Titre!][/SUBSTR]</a>
			</div>
		[/STORPROC]
	</div>

</div>