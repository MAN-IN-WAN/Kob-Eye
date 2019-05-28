//--- agenda de la semain
[!DateDebut:=[!Utils::getTms([!Utils::getDate(d/m/Y,[!TMS::Now!])!] 23:59)!]!]
[!DateFin:=[!DateDebut:+2592000!]!]
[!DateStockee:=[!DateDebut!]!]
[STORPROC 7|Jour]
	[!Nb:=[DATE w][!DateStockee!][/DATE]!]!]
	[!TABLEJOUR::[!Nb!]:=[!DateStockee!]!]
	[!DateStockee+=86400!]
[/STORPROC]

//je remplis la table pour l'afficher
[!HgConteneurTotal:=53!]
[!LgConteneurVisible:=106!]
[!HgConteneurVisible:=53!]

<div class="[!NOMDIV!]">
	<div class="TitreAgendaAccueil "><h2>L'Agenda</h2></div>
	<table class="AgendaAccueil" cellspacing="0" cellpadding="0"> 
		[!Ji:=0!]
		[STORPROC TABLEJOUR|Jour]
			[!DateJourlu:=[!Jour!]!]
			<tr class="UnJour">
				<td class="JourSemaine">
					[SWITCH [!Jour!]|=]
						[CASE 1]Lundi[/CASE]
						[CASE 2]Mardi[/CASE]
						[CASE 3]Mercredi[/CASE]
						[CASE 4]Jeudi[/CASE]
						[CASE 5]Vendredi[/CASE]
						[CASE 6]Samedi[/CASE]
						[CASE 7]Dimanche[/CASE]
					[/SWITCH]
				</td>
				<td style="width:[!LgConteneurVisible!]px;height:[!HgConteneurVisible!]px;">
					<div class="ContenuVisible"  style="overflow:hidden;position:relative;width:[!LgConteneurVisible!]px;height:[!HgConteneurVisible!]px;">
						[!NBJ:=0!]
						// compter le nombre de spectacle pour le jour lu
						[COUNT Reservation/Spectacle/DateDebut>=[!DateJourlu!]&DateFin<=[!DateJourlu!]|NBJ]
						[!LgConteneurTotal:=[!LgConteneurVisible!]!]
						[!LgConteneurTotal*=[!NBJ!]!]
						<div class="ContenuTotal" id="ladivadeplacer[!Ji!]" style="height:[!HgConteneurTotal!]px;width:[!LgConteneurTotal!]px;" >
							[STORPROC Reservation/Spectacle/DateDebut>=[!DateJourlu!]&DateFin<=[!DateJourlu!]|Sp]
								<a href="/[!Systeme::getMenu(Reservation/Spectacle)!]/[!Sp::Url!]" >
									<img src="[!Domaine!]/[!Sp::Img!].mini.53x53.jpg" width="53" height="53" alt="[!Sp::Nom!]" title="[!Sp::Nom!]"/>
								</a>
							[/STORPROC]
						</div>
					</div>
				</td>
				<td class="FlecheSuite"><a href="javascript:;" class="suivant"   onclick="deplacediv('ladivadeplacer[!Ji!]',[!NBJ!]);" ><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/Img/AgendaAccueilFleche.jpg" /></a></td>
			</tr>
			[!Ji+=1!]

		[/STORPROC]
	</table>
	<div class="ImprimeAgendaAccueil " ><a href="/Reservation/Agenda/ImprimeSemaine.pdf" alt="impression calendrier" title="impression calendrier" target="_blank"> Imprimer la semaine <span class="IconGraphique">d</span></a></div>

</div>


// Surcouche JS
<script type="text/javascript">
	var Largeurinfo =58;
	var nomdeladiv="";
	
	var tab_div= new Array();
	for (i=0;i<7;i++) {
		nomdeladiv='ladivadeplacer'+i;
		tab_div[nomdeladiv]=0;
		
	}

	function deplacediv(lenomdiv, nbinfos) {
		// fonction pour déplacer quand il y a plusieurs blocks affichés
		totalinfos=-Largeurinfo*nbinfos;
		lemarginSpe = tab_div[lenomdiv] - Largeurinfo ;
		if (totalinfos == lemarginSpe) lemarginSpe = 0 ;
		$(lenomdiv).tween('margin-left', lemarginSpe+'px'); 
		tab_div[lenomdiv]=lemarginSpe;
	
	}

</script>
	

