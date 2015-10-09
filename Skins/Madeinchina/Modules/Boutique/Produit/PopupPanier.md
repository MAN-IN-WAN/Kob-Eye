[IF [!Systeme::User::Public!]=1]
	[OBJ Boutique|Client|Cli]
[ELSE]
        [!Cli:=[!CurrentClient!]!]
[/IF]


// Ajout au panier
[IF [!Reference!]!=&&[!Qte!]>0]
	[STORPROC Boutique/Produit/Reference/[!Reference!]|Prod|0|1][/STORPROC]

	// en commentaires car sur cete boutique on ne gère pas de colisage 
	//[IF [!Prod::GetColisage()!]][!Qte*=[!Prod::GetColisage()!]!][/IF]
	[!T:=[!Cli::ajouterAuPanier([!Reference!],[!Qte!],[!config!],[!options!])!]!]
	
[/IF]
[IF [!T!]=1]
	//CAS SUCCESS
	
	
	
	[STORPROC Boutique/Reference/[!Reference!]|Re|0|1]
		[!P:=[!Re::getProd!]!]
	//	[!LeTarif:=[!Re::getTarif([!Qte!])!]!]
	//	[!DecliNom:=!]
	//	[STORPROC Boutique/Declinaison/Reference/[!Re::Id!]|Decli||][!DecliNom+=[!Decli::Nom!]!][/STORPROC]
	[/STORPROC]
	//recupération de la dernière ligne commande ajoutée
	[!Panier:=[!Cli::getPanier()!]!]
	[!LC:=[!Panier::getLastOrderLine()!]!]
	[!Limage:=[!Prod::Image!]!]
	<div class="PopupPanier">
		<div class="table-responsive">
			<table class="table">
				<tr><td colspan="2">
					<h3>Vous venez d'ajouter dans votre panier le(s) produit (s) :</h3>
				</td></tr>
				<tr>
					<td style="width:215px;"><img src="/[!Limage!].limit.235x1000.jpg"  alt="[!Utils::noHtml([!P::Description!])!]" class="img-responsive" /></td>
					<td class="BlocPanier">
						<div class="NomTarif">
							<h2>[!P::Nom!]</h2><div class="Tarif">[!Math::PriceV([!LC::MontantTTC!])!] [!CurrentDevise::Sigle!]</div>
						</div>
						<div class="Accro">[!P::Accroche!]</div>
						<div class="Popinfo">
							[IF [!DecliNom!]!=]<div class="Decli">[!Decli::Nom!]</div>[/IF]
							<div class="Qte">Quantité : [!Qte!]</div>
						</div>
						<div class="Descr">
							[UTIL BBCODE][!LC::Description!][/UTIL]
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="border:none;" >
						<a class="btn btn-protector pull-right" id="MonPanier" href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]" >VOIR MON PANIER</a>
					</td>
				</tr>
				<tr>
			<td colspan="2"style="border:none;" > 
			    <a class="btn btn-protector pull-right" id="ContinuAchat" href="[!P::getUrl()!]">CONTINUER MES ACHATS</a>
			</td>
		    </tr>
			</table>
		</div>
	</div>
[ELSE]
	<div class="alert alert-danger">
		<b>Erreur :</b>
		<ul>
            [IF [!Reference!]=]
            <li>Aucune référence</li>
            [/IF]
            [IF [!Qte!]=]
            <li>Aucune Quantité</li>
            [/IF]
            [IF [!Cli!]=]
            <li>Client introuvable</li>
            [/IF]
		[STORPROC [!T!]|E]
			<li>[!E::Message!]</li>
		[/STORPROC]
		</ul>
	</div>
[/IF]