[TITLE]Admin Kob-Eye | Importation d'un fichier[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<div id="Arbo">
		[BLOC Panneau][/BLOC]
	</div>
	<div id="Data">
		<form enctype="multipart/form-data" action="" method="post" name="frm" >
		[BLOC Panneau]
                    [STORPROC [!Query!]|C|0|1]
                        [!C::setUser()!]
                        [STORPROC [!C::Error!]|E]
                            <div class="alert alert-danger">
                                [!E::Message!]
                            </div>
                            [NORESULT]
                                <div class="alert alert-success">Accès créé</div>
                            [/NORESULT]
                        [/STORPROC]
                    [/STORPROC]
 					<a href="[!Query!]" class="KEBouton" style="width:75px;float:left;margin-left:7px;">Retour</a>
		[/BLOC]
		</form>
	</div>
</div>

