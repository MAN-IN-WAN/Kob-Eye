<table id="Onglets">
	<tr>
		<td class="first [IF [!Tab!]=] current [/IF]"><a href="/[!Lien!]">Logements</a> </td>
		<td [IF [!Tab!]=Plans] class="current" [/IF]><a href="/[!Lien!]?Tab=Plans">Plans</a></td>
		<td [IF [!Tab!]=Descriptif] class="current" [/IF]><a href="/[!Lien!]?Tab=Descriptif">Descriptif</a></td>
		[COUNT [!Query!]/Donnee/Type=Image|ImgL]
		[IF [!ImgL!]]
		[ELSE]
			[STORPROC ParcImmobilier/Residence/[!R::Id!]/Nouvelle|Ne|0|1]
				[COUNT News/Nouvelle/[!Ne::Id!]/Donnee/Type=Image|ImgL]
			[/STORPROC]
		[/IF]
		[IF [!ImgL!]]
			<td [IF [!Tab!]=Photos] class="current" [/IF]><a href="/[!Lien!]?Tab=Photos">Photos</a></td>
		[/IF]
	</tr>
</table>

<div class="BlocDetailResidence [IF [!Tab!]!=] nodisplay [/IF]">
	<div class="nodisplay"><h3>Logements</h3></div>
	<table class="Logements">
		<tr>
			<th>
				<table>
					<tr>
						<td class="noborder">Type de logement</td>
						<td>Disponibilités</td>
						<td>Superficie</td>
						<td>Prix</td>
					</tr>
				</table>
			</th>
		</tr>
		<tr>
			<td>
				<table>
					[STORPROC [!Query!]/TypeLogement|TL|||Type|ASC]
						<tr>
							<td class="noborder">
								[SWITCH [!TL::Type!]|=]
									[CASE T1]Appartement 1 pièce[/CASE]
									[CASE T2]Appartement 2 pièces[/CASE]
									[CASE T3]Appartement 3 pièces[/CASE]
									[CASE T4]Appartement 4 pièces[/CASE]
									[CASE T5]Appartement 5 pièces[/CASE]
									[CASE T6]Appartement 6 pièces[/CASE]
									[CASE Villa]Villa[/CASE]
									[CASE Studio]Studio[/CASE]
									[DEFAULT][!TL::Titre!][/DEFAULT]
								[/SWITCH]
							</td>
							<td>[!TL::Nombre!]</td>
							<td>
								[IF [!TL::SuperficieMax!]!=-&&[!TL::SuperficieMax!]!=]
									de [!TL::SuperficieMin!]&nbsp;m² à [!TL::SuperficieMax!]&nbsp;m²
								[ELSE]
									[!TL::SuperficieMin!]&nbsp;m²
								[/IF]
							</td>
							<td>
								[IF [!TL::PrixMax!]!=-&&[!TL::PrixMax!]!=]
									de [!TL::PrixMin!]&nbsp;&euro; à [!TL::PrixMax!]&nbsp;&euro;
								[ELSE]
									[!TL::PrixMin!]&nbsp;&euro;
								[/IF]
							</td>
						</tr>
					[/STORPROC]
				</table>
			</td>
		</tr>
	</table>
</div>

<div class="BlocDetailResidence [IF [!Tab!]!=Plans] nodisplay [/IF]">
	<div class="nodisplay"><h3>Plans</h3></div>
	[STORPROC [!Query!]/Donnee/Type=Plan|P]
		[NORESULT]
			Il n'y a pas de plan disponible pour cette résidence.
		[/NORESULT]
	[/STORPROC]
	[MODULE ParcImmobilier/Residence/BlocPlan?R=[!R!]&TypePlan=Studio&H4=Plan type Studio]
	[MODULE ParcImmobilier/Residence/BlocPlan?R=[!R!]&TypePlan=T1&H4=Plan type appartement 1 pièce]
	[MODULE ParcImmobilier/Residence/BlocPlan?R=[!R!]&TypePlan=T2&H4=Plan type appartement 2 pièces]
	[MODULE ParcImmobilier/Residence/BlocPlan?R=[!R!]&TypePlan=T3&H4=Plan type appartement 3 pièces]
	[MODULE ParcImmobilier/Residence/BlocPlan?R=[!R!]&TypePlan=T4&H4=Plan type appartement 4 pièces]
	[MODULE ParcImmobilier/Residence/BlocPlan?R=[!R!]&TypePlan=T5&H4=Plan type appartement 5 pièces]
	[MODULE ParcImmobilier/Residence/BlocPlan?R=[!R!]&TypePlan=T6&H4=Plan type appartement 6 pièces]
	[MODULE ParcImmobilier/Residence/BlocPlan?R=[!R!]&TypePlan=Villa&H4=Plan type Villa]
</div>

<div class="BlocDetailResidence [IF [!Tab!]!=Descriptif] nodisplay [/IF]">
	<div class="nodisplay"><h3>Descriptif</h3></div>
	[!R::Descriptif!] 
	[IF [!R::DescriptifPDF!]!=]
		<a class="TelechargerDV" href="/[!R::DescriptifPDF!]">Télécharger le descriptif de vente</a>
	[/IF]
</div>
[IF [!ImgL!]]
	<div class="BlocDetailResidence [IF [!Tab!]!=Photos] nodisplay [/IF]">
		<div class="nodisplay"><h3>Photos</h3></div>
		<div class="NewsImage" >
			[STORPROC [!Query!]/Donnee/Type=Image|Do1]
				<a href="/[!Do1::URL!].limit.800x600.jpg" class="mb" rel="Img[[!R::Id!]]" style="background:none;" title="[SUBSTR 30][!Utils::NOHTML([!Do1::Titre!])!][/SUBSTR]">
					<img src="/[!Do1::URL!].mini.70x70.jpg" width="70" height="70" alt="[!Do1:Titre!]" title="[!Do1::Titre!]"/>
				</a>
			[/STORPROC]
			[STORPROC ParcImmobilier/Residence/[!R::Id!]/Nouvelle|Ne|0|1]
				[STORPROC News/Nouvelle/[!Ne::Id!]/Donnee/Type=Image|Do]
					<a href="/[!Do::Fichier!].limit.800x600.jpg" class="mb" rel="Img[[!R::Id!]]" style="background:none;" title="[SUBSTR 30][!Utils::NOHTML([!Do::Titre!])!][/SUBSTR]">
						<img src="/[!Do::Fichier!].mini.70x70.jpg" width="70" height="70" alt="[!Do::Titre!]" title="[!Do::Titre!]"/>
					</a>
				[/STORPROC]
			[/STORPROC]

		</div>
	</div>
[/IF]

			
