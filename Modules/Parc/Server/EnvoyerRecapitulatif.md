[TITLE]Admin Kob-Eye |Envoyer récapitulatif[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<div id="Arbo">
		[BLOC Panneau][/BLOC]
	</div>
	<div id="Data">
		<form enctype="multipart/form-data" action="" method="post" name="frm" >
		[BLOC Panneau]

[STORPROC [!Query!]|S]
        [STORPROC [!S::getParents(Client)!]|C|0|1][/STORPROC]
	[IF [!MAILSEND!]]
		[LIB PHPMailer|M]
		[METHOD M|setFrom][PARAM]contact@eng.systems[/PARAM][/METHOD]
                [STORPROC [![!C::Email!]:/,!]|Em]
                    [METHOD M|addAddress][PARAM][!Em!][/PARAM][/METHOD]
                [/STORPROC]
		[METHOD M|addAddress][PARAM]contact@eng.systems[/PARAM][/METHOD]
		[METHOD M|MsgHTML][PARAM]
[!CONTENT!]
		[/PARAM][/METHOD]
		[METHOD M|set][PARAM]Subject[/PARAM][PARAM][!SUBJECT!][/PARAM][/METHOD]
		[METHOD M|Send][/METHOD]
		<div class="success">Mail envoyé</div>
					<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Fermer</a>
	[ELSE]
		[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:5px;]
			<div class="Propriete">
				<div class="ProprieteTitre">Sujet</div>
				<div class="ProprieteValeur">&nbsp;
					<input name="SUBJECT" value="[ENG.SYSTEMS] [!C::Nom!] Récapitulatif de serveur dédié" size="100" />
				</div>
			</div>
			<div class="Propriete">
				<div class="ProprieteTitre">Message : </div>
				<div class="ProprieteValeur">&nbsp;
					<textarea name="CONTENT" cols="30" lines="15" style="width:95%;height:200px;">
                                            <h1>DETAILS</h1>
                                            <h3>Configuration du serveur [!S::Nom!]:</h3>
                                                Ip du serveur: <b>[!S::IP!]</b><br />
                                                Nom du serveur: <b>[!S::DNSNom!]</b><br />
                                            <h3>Connexion ssh:</h3>
                                                Nom d'utilisateur: <b>[!S::SshUser!]</b><br />
                                                Mot de passe: <b>[!S::SshPassword!]</b><br />
                                            <h3>Détail  des ressources:</h3>
                                                Nombre de cpu: <b>[!S::NbCpu!]</b><br />
                                                Taille mémoire (RAM): <b>[!S::NbRam!]</b><br />
                                                Espace disque (HDD): <b>[!S::EspaceProvisionne!]</b><br />
                                            <h3>Détail de l'installation:</h3>
                                                [UTIL NL2BR][!S::Commentaire!][/UTIL]<br />
                                        </textarea>
				</div>
			</div>
			<input type="hidden" name="MAILSEND" value="Envoyer"/>
		[/BLOC]
			<div class="JSFormButton" style="overflow:hidden;height:60px;margin-right:6px;">
				<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Annuler</a>
				<input type="submit" class="KEBouton"  value="Enregistrer" name="SaveObject" style="float:right;"/>
			</div>
	[/IF]
[/STORPROC]
		[/BLOC]
		</form>
	</div>
</div>
