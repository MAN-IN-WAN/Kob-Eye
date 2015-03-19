//affichage d'une catégorie de news  dans un block
[COUNT News/Categorie/[!CATEGAFFICH!]/Nouvelle/Publier=1&ALaUne=1|NbNe]
[STORPROC News/Categorie/[!CATEGAFFICH!]|Cat][/STORPROC]
[!HAUTEURVISIBLE:=0!]
[STORPROC [!NBINFOS!]]
	[!HAUTEURVISIBLE+=[!HAUTEURUNEINFO!]!]
[/STORPROC]
[!HAUTEURVISIBLE+=[!PADDINGINFO!]!]
<div class="[!NOMDIV!]" >
	<div class="EntoureComposantNews ">
		<div class="BlocCadre">
			<div class="TitreBloc">
				<div class="blocleft">[IF [!TITRE!]]<h2>[!TITRE!]</h2>[ELSE]<h2>Les Actus</h2>[/IF]</div>
				[IF [!NbNe!]>[!NBINFOS!]]
					<div id="paginationAccueilActus" class="paginationAccueilActus">
							//<a href="javascript:;" class="precedent" onclick="deplacediv('margin-top','P',[!HAUTEURUNEINFO!]);"  >Précédent</a>
							//<a href="javascript:;" class="suivant"   onclick="deplacediv('margin-top','S',[!HAUTEURUNEINFO!]);" >Suivant</a>
					</div>
				[/IF]
			</div>
			[!MarginTop:=0!]
			<div class="ContenuVisible" id="ladivvisible" style="overflow:auto;display:block;position:relative;[IF [!HAUTEURVISIBLE!]!=0]height:[!HAUTEURVISIBLE!]px;[/IF]">
				<div class="ContenuTotal" id="ladivadeplacer" >
					[OBJ News|Nouvelle|Nouv]
					[!Lesens:=ASC!]
					[IF [!SENS!]=1][!Lesens:=DESC!][/IF]
					[IF [!ORDRE!]=][!ORDRE:=tmsCreate!][/IF]
					[STORPROC News/Categorie/[!CATEGAFFICH!]/Nouvelle/Publier=1&AlaUne=1|Ne|0|[!LIMITAFFICHAGE!]|[!ORDRE!]|[!Lesens!]]
						<div class="AfficheInfo" [IF [!HAUTEURUNEINFO!]!=0&&[!HAUTEURUNEINFO!]!=]style="height:[!HAUTEURUNEINFO!]px;"[/IF]>
							<div class="TitreNews">
								<a href="/[!MENUACTU!]/[!Ne::Url!]" >
									[IF [!CHAMPTITRE!]=tmsEdit||[!CHAMPTITRE!]=tmsCreate||[!CHAMPTITRE!]=Date]
										Le [!Utils::getDate(d/m/Y,[!Ne::[!CHAMPTITRE!]!])!]
									[ELSE]
										[SUBSTR [!NBCARACTTITRE!]][!Ne::[!CHAMPTITRE!]!][/SUBSTR]
									[/IF]
									
								</a>
							</div>
							[IF [!CHAMPACCROCHE!]!=]
								<div class="Accroche" > 
									[IF [!CHAMPACCROCHE!]=tmsEdit||[!CHAMPACCROCHE!]=tmsCreate]
										Le [!Utils::getDate(d/m/Y,[!Ne::[!CHAMPACCROCHE!]!])!]
									[ELSE]
										[SUBSTR [!NBCARACTACCROCHE!]][!Ne::[!CHAMPACCROCHE!]!][/SUBSTR]
									[/IF]
								</div>
							[/IF]
							<div class="PartieInfo">
								[IF [!Ne::Image!]!=]
									<div class="affichimage">
										<a href="/[!MENUACTU!]/[!Ne::Url!]"  ><img src="[!Domaine!]/[!Ne::Image!].limit.[!LARGEURUNEIMG!]x[!HAUTEURUNEIMG!].jpg"   alt="[!Ne::Nom!]" width="[!LARGEURUNEIMG!]" height="[!HAUTEURUNEIMG!]" /></a>
									</div>
								[/IF]
								<div class="Desc" >
								
									[SUBSTR [!NBCARACT!]][!Utils::noHtml([!Ne::[!CHAMPTEXTE!]!])!][/SUBSTR]
								</div>					
							</div>	
							<div class="accueilliennews">
								<a href="/[!MENUACTU!]/[!Ne::Url!]"  >[IF [!TEXTELIENDETAIL!]][!TEXTELIENDETAIL!][ELSE]Lire la suite[/IF]</a>	
							</div>
						</div>
					[/STORPROC]
				</div>	
			</div>	
			[IF [!TEXTELIENTOUTES!]!=]
				<div class="touteslesnews"><a href="/[!MENUACTU!]" >[!TEXTELIENTOUTES!]</a></div>
			[/IF]
		</div>	
	</div>	
</div>	
