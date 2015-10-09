[TITLE]Admin Kob-Eye | Exportation de menus[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<div id="Arbo">
		[BLOC Panneau]
			<a href="/[!Query!].htm" class="KEBouton">Retour au produit</a>
			<a href="/[!Lien!].htm" class="KEBouton">Rafraichir</a>
		[/BLOC]
	</div>
	<div id="Data">
		[BLOC Panneau]
                    [IF [!generer!]!=]
                        <p>
			    [STORPROC [!Query!]|Prod|0|1][/STORPROC]
                            <div style="color:red;font-weight:bold;">Déclinaisons sélectionnées</div>
                            [!TAB:=[!Array::newArray()!]!]
                            [STORPROC [!Declinaisons!]|De]
                                //recherche attribut correspondant
                                [STORPROC Boutique/Attribut/Declinaison/[!De!]|ATT]
                                    [IF [!TAB::[!ATT::Nom!]::Declinaisons!]]
                                        [!TEMPDEC:=[!TAB::[!ATT::Nom!]!]!]
					[!TEMPDEC:=[!TEMPDEC::Declinaisons!]!]
                                    [ELSE]
                                        [!TEMPDEC:=[!Array::newArray()!]!]
                                    [/IF]
                                    [STORPROC Boutique/Declinaison/[!De!]|DEC]
                                        [!TEMPDEC:=[!Array::push([!TEMPDEC!],[!DEC!])!]!]
                                    [/STORPROC]
                                    [!ATT::Declinaisons:=[!TEMPDEC!]!]
                                    [!TAB:=[!Array::push([!TAB!],[!ATT!],[!ATT::Nom!])!]!]
                                [/STORPROC]
                            [/STORPROC]
                            <ul>
                            [STORPROC [!TAB!]|ATT]
                                <li>[!ATT::Nom!]
                                    <ul>
                                    [STORPROC [!ATT::Declinaisons!]|D]
                                        <li>[!D::Nom!]</li>
                                    [/STORPROC]
                                    </ul>
                                </li>
                            [/STORPROC]
                            </ul>
			    //GENERATION DU TABLEAU DES REFERENCES
    			    [!REFS:=[!Array::newArray()!]!]
                            [STORPROC [!TAB!]|ATT]
					//Affection des attributs sur le produit
					[!!]
					[!REF2:=[!Array::newArray()!]!]
					[STORPROC [!REFS!]|R|0|1][NORESULT]
						[!INIT:=1!]
					[/NORESULT]
						[!INIT:=0!]
					[/STORPROC]
					[STORPROC [!ATT::Declinaisons!]|D]
					    [IF [!INIT!]]
						[!R:=[!Array::newArray()!]!]
						//initialisation des premières références
						[!R:=[!Array::push([!R!],[!D!],[!D::Nom!])!]!]
						[!REF2:=[!Array::push([!REF2!],[!R!],[!Pos:-1!])!]!]
					    [ELSE]
						[STORPROC [!REFS!]|R]
						    [!R:=[!Array::push([!R!],[!D!],[!D::Nom!])!]!]
						    [!REF2:=[!Array::push([!REF2!],[!R!])!]!]
						[/STORPROC]
					    [/IF]
					[/STORPROC]
					[!REFS:=[!REF2!]!]
                            [/STORPROC]
			<h4>détail des références à générer</h4>
			<ul>
				[STORPROC [!REFS!]|R]
					<li>
					[STORPROC [!R!]]
						- [!Key!]
						[!DEBUG::R!]
					[/STORPROC]
					</li>
				[/STORPROC]
			</ul>
                        </p>
                    [ELSE]
                    <form method="post">
                        <h1>Sélectionnez les déclinaisons pour ce produit</h1>
                        <div style="overflow:auto;background:white;height:80%;border: 1px solid gray;">
                            <ul style="margin-left:20px;">
                            [STORPROC Boutique/Attribut|At]
                                <li><b>[!At::Nom!]</b>
                                    <ul>
                                    [STORPROC Boutique/Attribut/[!At::Id!]/Declinaison|De]
                                        <li style="line-height:15px;"><input type="checkbox" name="Declinaisons[]" value="[!De::Id!]">[!De::Nom!]</li>
                                    [/STORPROC]
                                    </ul>
                                </li>
                            [/STORPROC]
                            </ul>
                        </div>
                        <input type="submit" class="KEBouton" name="generer" value="Générer les références" style="margin:10px 0; width:100%"/>
                    </form>
                    [/IF]
		[/BLOC]
	</div>
</div>
