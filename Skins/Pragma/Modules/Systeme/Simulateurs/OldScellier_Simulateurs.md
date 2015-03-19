<h1>Les Simulateurs</h1>
<div class="SimulateurComplet">
	<div class="Budget" >
		<form name="formbien" method="post" action="/[!Lien!]" class="formbien">
			<div class="LigneForm"><h3>SIMULATEUR DU BUDGET</h3></div>
			<div class="LigneForm">Calculez le montant de votre emprunt pour l'achat d'un bien, simplement et sans modalité d'inscription.</div>
			<div class="LigneForm">
				<label>Prix de votre bien</label>
				<button type="button" name="Moins"  onclick="modifValeur(document.formbien.prix_bien,-1000);return false;" >-</button>
				<input type="text" size="8"  name="prix_bien" onChange="javascript:calculBien(document.formbien);" value="[!Prix!]" >
				<button type="button" name="Plus"  onclick="modifValeur(document.formbien.prix_bien,+1000);return false;"  >+</button>
			</div>
			<div class="LigneForm">
				<label>Votre apport personnel</label>
				<button type="button" name="MoinsApp"   onclick="modifValeur(document.formbien.apport_perso,-1000);return false;">-</button>
				<input type="text" size="8"  name="apport_perso" onChange="javascript:calculBien(document.formbien);" value="0" >
				<button type="button" name="PlusApp"  onclick="modifValeur(document.formbien.apport_perso,1000);return false;" >+</button>
			</div>
			<div class="LigneForm">
				<label>Somme &agrave; financer</label>
				<input type="text" style="width:127px;"  size="8" name="somme_finance" onChange="javascript:calculBien(document.formbien);" readonly>
			</div>
			<div class="LigneForm">
				<label>Mensualit&eacute;</label>
				<button type="button" name="MoinsMens" onclick="modifValeur(document.formbien.mensualite,-100);return false;">-</button>
				<input type="text" size="8"  name="mensualite" onChange="javascript:calculBien(document.formbien);" value="0" >
				<button type="button" name="PlusMens"  onclick="modifValeur(document.formbien.mensualite,100);return false;" >+</button>
			</div>
			<div class="LigneForm">
				<label>Dur&eacute;e (ans)</label>
				<button type="button" name="MoinsDur" onclick="modifValeur(document.formbien.duree,-1);return false;">-</button>
				<input type="text" size="8"  name="duree" onChange="javascript:calculBien(document.formbien);" value="15" >
				<button type="button" name="PlusDur"  onclick="modifValeur(document.formbien.duree,1);return false;" >+</button>
			</div>
			<div class="LigneForm">
				<label>Taux</label>
				<button type="button" name="MoinsTx"  onclick="modifValeur(document.formbien.taux,-0.1);return false;">-</button>
				<input type="text" size="8"  name="taux" onChange="javascript:calculBien(document.formbien);" value="3.9" >
				<button type="button" name="PlusTx"  onclick="modifValeur(document.formbien.taux,0.1);return false;" >+</button>
			</div>
			<div class="LigneForm">
				<label>Co&ucirc;t de votre emprunt</label>
				<input type="text"  style="width:127px;"  size="8" name="cout_emprunt" onChange="javascript:calculBien(document.formbien);" readonly>
			</div>
		</form>
	</div>
	<div class="LoiScellier" >
		<form id="simulateurForm" action="/[!Lien!]" class="formbien">
			<div class="LigneForm"><h3>SIMULATEUR LOI SCELLIER</h3></div>
			<div class="LigneForm">Choisissez l'investissement idéal grâce à notre moteur de recherche. Cette rubrique vous permet de simuler un investissement pour un achat de bien neuf dans le cadre du dispositif de la loi Scellier.</div>
			<div class="LigneForm">
				<label class="Partie2">Votre revenu imposable annuel (0 à 150000 €)</label>
				<input type="text" size="18"  maxlength="6" id="revenu_value" value="0" onblur="this.value = adjustValue(0, 150000, this.value)" />
			</div>
			<div class="LigneForm">
				<label class="Partie2">Votre revenu foncier annuel (0 à 10700 €)</label>
				<input type="text" size="18"  maxlength="6" id="revenu_foncier_value" value="0" onblur="this.value = adjustValue(0, 10700, this.value)" />
			</div>
			<div class="LigneForm">
				<label class="Partie2">Nombre de parts</label>
				<input type="text" size="8" maxlength="3" id="nb_parts_value" value="1" />
			</div>
			<div class="LigneForm">
				<label class="Partie2">Votre imposition est de</label>
				<input type="text" size="18" readonly="readonly" id="imposition_value" value="0" />	
			</div>
			<div class="LigneForm">
				<label class="Partie2">Votre <abbr title="Taux Marginal d'Imposition">TMI</abbr> est de</label>
				<input type="text" size="18" readonly="readonly" id="tmi_value" value="0" />
			</div>
			<div class="LigneForm"><h3>Vous exercez...</h3></div>
			<div class="LigneForm">
				<div class="BoxCheckSup">
					<label class="Nature">Une profession salariée</label>
					<input type="radio" name="liberal" id="salarie" value="0" />
				</div>
				<div class="BoxCheckSup">
					<label class="Nature">Une profession libérale</label>
					<input type="radio" name="liberal" id="liberal" value="1" checked="checked" />
				</div>
			</div>
			<div class="LigneForm">
				<label class="Partie2">Montant de l'investissement (75000 à 300000 €)</label>
				<input type="text" size="18"   maxlength="6" id="montant_investissement_value" value="75000" onblur="this.value = adjustValue(75000, 300000, this.value)" />
			</div>
			<div class="LigneForm">
				<label class="Partie2">Durée du crédit</label>
				<input type="text"   id="duree_credit_value" value="20" />
			</div>
			<div class="LigneForm">
				<label class="Partie2">Taux du crédit</label>
				<input type="text"   id="taux_credit_value" value="4" />
			</div>
			<div class="LigneForm">
				<label class="Partie2">Durée de la simulation</label>
				<input type="text" id="duree_simulation_value" value="9" onblur="this.value = adjustValue(9, 15, this.value)" />
			</div>
			<div class="LigneForm" style="display:none"><h3>Résultat de la simulation :</h3></div>
			<div class="LigneForm" >
				<p>
					Vous réaliserez une économie d'impôts de <span id="economie_impot" name="economie_impot" style="font-weight:bold"></span> € (dont <span id="texte_csgcrds" name="texte_csgcrds"></span> € de <abbr title="Contribution Sociale Généralisée">CSG</abbr> <abbr title="Contribution au Remboursement de la Dette Sociale">CRDS</abbr>) sur <span id="texte_duree" name="texte_duree"></span> ans.<br />Le capital constitué au bout de cette durée sera de <span id="capital_constitue" name="capital_constitue" style="font-weight:bold;"></span> €.
				</p>
			</div>
			<div class="LigneForm">
				<p style="display:none">
					Votre épargne s'élevera donc à priori à <span id="epargne_moyenne" name="epargne_moyenne"></span> € par mois.*<br /><br />*Ce simulateur est basé sur la rentabilité nette de charges moyenne constatée sur le marché.<br /><strong>L'épargne moyenne placée vous ferait gagner :</strong><br />Sur une assurance vie à 4,5 % : <span id="assurance_vie" name="assurance_vie"></span> €<br />Sur un livret A à 1,25 % : <span id="livret_a" name="livret_a"></span> €<br />Sur un PEL à 2,5 % : <span id="pel" name="pel"></span> €
				</p>
			</div>
			<div class="LigneForm">
				<label class="Partie2"><h3>Qui finance votre projet ?</h3></label>
				<div id="graph"></div>
			</div>
			<div class="LigneForm">
				<div id="messagetxt"></div>
			</div>
		</form>
	</div>
	
</div>

