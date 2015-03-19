<div class="well">
[IF [!CasUser!]]
	<h1 style="float:none;">Modifier votre mot de passe</h1>
        [IF [!ChangePassword!]=1]
            [!MSG:=[!CasUser::changePassword([**C_Pass**],[**C_ConfirmPass**])!]!]
            [STORPROC [!MSG!]|M]
                <div class="alert alert-[!M::type!]">
                    [!M::message!]
                </div>
            [/STORPROC]
        [/IF]
        <div class="row" >
            <div class="CpContact" style="overflow:hidden:float:none;display:block">
                    <form id="FormPassword" method="post" action="?">
                            <div class="LigneForm">
                                    <label>Votre nouveau mot de passe: <span class="Obligatoire">*</span></label>
                                    <input type="password" name="C_Pass"  style="text-transform:uppercase"  value=""  class="" />
                            </div>
                            
                            
                            
                            <div class="LigneForm">
                                    <label>Re-saisissez votre mot de passe: <span class="Obligatoire">*</span></label>
                                    <input type="password" name="C_ConfirmPass"  value=""  class="" />
                            </div>
                            
                            

                                    <input type="submit" class="btn btn-success" name="Valider" value="Valider"/>
                                    <input type="hidden" name="ChangePassword" value="1" />

                    </form>
                    <p>(*) Champs obligatoires.</p>
                    <p>Le mot de passe doit contenir 8 caractères, au moins une minuscule, une majuscule ainsi qu'un chiffre.</p>
                    
            </div>
	</div>
</div>
<div class="well">
		<h1 style="float:none">Gestion des favoris</h1>
		[IF [!AddFavoris!]=1]
			[OBJ ProxyCas|Favoris|F]
			[METHOD F|Set][PARAM]Url[/PARAM][PARAM][!C_Url!][/PARAM][/METHOD]
			[!T:=[!F::AddParent([!CasUser!])!]!]
			//verification de l'existence
			[STORPROC ProxyCas/Host/[!CasUser::Id!]/Favoris|F2]
				[IF [!F2::Url!]=[!C_Url!]][!ae:=1!][/IF]
			[/STORPROC]	
			
			[IF [!F::Verify()!]&&[!ae!]=]
				[METHOD F|Save][/METHOD]
				<div class="alert alert-success">Favoris ajouté avec succés</div>
			[ELSE]
				<div class="alert alert-danger">
					<ul>
					[IF [!ae!]]
						<li>Le lien existe deja</li>
					[/IF]
					[STORPROC [!F::Error!]|E]
						<li>[!E::message!]</li>	
					[/STORPROC]
					</ul>
				</div>
			[/IF]
		[/IF]
		[IF [!action!]=supprimer&&[!id!]>0]
			[STORPROC ProxyCas/Host/[!CasUser::Id!]/Favoris/[!id!]|F]
				[METHOD F|Delete][/METHOD]
				<div class="alert alert-success">Favoris supprimé avec succés</div>
			[/STORPROC]
		[/IF]
        <div class="row" >
            <div class="CpContact" style="overflow:hidden:float:none;display:block">
                    <form id="FormPassword" method="post" action="?">
                            <div class="LigneForm" style="float:none;">
                                    <label>Lien de votre favoris: <span class="Obligatoire">*</span></label>
                                    <input type="varchar" name="C_Url"  style="text-transform:uppercase"  value=""  class="" />
                            </div>




                                    <input type="submit" class="btn btn-success" name="Valider" value="Valider"/>
                                    <input type="hidden" name="AddFavoris" value="1" />

                    </form>
			<ul>
			[STORPROC ProxyCas/Host/[!CasUser::Id!]/Favoris|F]
				<li style="margin-left:10px;display: block;line-height: 25px;margin: 5px;overflow: hidden;"><a href="?id=[!F::Id!]&action=supprimer" class="btn btn-danger btn-small" style="float:left; height: 11px;line-height: 1;margin: 0 10px;padding: 7px;">supprimer</a>[!F::Url!]</li>
			[/STORPROC]
			</ul>
                    <p>(*) Champs obligatoires.</p>
                    <p>Le mot de passe doit contenir 8 caractères, au moins une minuscule, une majuscule ainsi qu'un chiffre.</p>
                    
            </div>

                    
        </div>
</div>
[ELSE]<div class="alert alert-danger">Veuillez vous connecter <a href="https://proxycas.unibio.fr/login" class="btn btn-success">Se Connecter</a></div>[/IF]
