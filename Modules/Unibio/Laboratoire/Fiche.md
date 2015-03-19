<div class="UnibioLesLabo">
	<div class="TitreLabo" style="overflow:hidden;">
		<h1>Fiche détaillée du laboratoire</h1>
	</div>
</div>


[STORPROC [!Query!]|Lab]
	<div class="LaboItem">
		<div class="LaboItemLeft">
			<div class="LaboNom">
				[!Lab::Nom!]
			</div>
			<div class="LaboPersonnes">
				[STORPROC [!Query!]/Professionel|Pro]
					<div class="LaboPers"><div class="LaboPersCivilite">[!Pro::Nom!] [!Pro::Prenom!] </div> <div class="LaboPersProfession">&nbsp;- [!Pro::Profession!]</div></div>
				[/STORPROC]
			</div>
			<div class="LaboPhoto" style="position:relative">
				[IF [!Lab::Photo!]]
					<img src="/[!Lab::Photo!].mini.180x161.jpg" alt="[!Lab::Nom!]" />
				[ELSE]
					<img src="/Skins/[!Systeme::Skin!]/Img/labo-defaut_03.jpg" alt="[!Lab::Nom!]" />
				[/IF]
				//<div class="LaboPhotoFiligrane"></div>
			</div>

		</div>
		<div class="LaboItemRight">
			<div class="LaboItemRightTop">
				<div class="LaboAdresse"><strong>ADRESSE : </strong>[!Lab::Adresse!]</div>
				<div class="LaboNums"><strong>Tél : [!Lab::Tel!]</strong> - Fax : [!Lab::Fax!]</div>
				<div class="LaboHoraires">[!Lab::Horaires!]</div>
			</div>
		</div>
		<div class="LaboItemRightBis">
			<div class="LaboItemRightBottom">
				<div class="LaboDescription"><p>[!Lab::Description!]</p></div>
				<div class="LaboMoreLinks">
				<a class="LaboPlanAcces" href="http://maps.google.fr/maps?f=q&hl=fr&q=[IF [!Lab::GPS!]!=][!Lab::GPS!][ELSE][URL][!Lab::Adresse!][/URL][/IF]" target="_blank">Plan d'accès</a>
 				</div> 
			</div>
		</div>
	</div>
[/STORPROC]

<div class="ButtonsLabo">
	<div class="LigneForm" style="padding:10px;">
		
		<button type="button" onclick="history.go(-1)" class="RetourRecherche">Retour</button>
		<a href="/[!Lien!]/PdfFiche.pdf" class="BtnImprimer" target="_blank" >Imprimer</a>
	</div>

</div>