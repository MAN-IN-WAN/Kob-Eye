	<div class="row"><div class="col-md-12"><h2>Mon Compte</h2></div></div>
	<div class="row">
		<div class="col-md-6" style="border-right:1px dotted;">
			<h3 class="DejaClient">Déjà Client</h3><span class="petittexte"> (j'ai un compte, je m'identifie)</span>
			[MODULE Systeme/Login?Redirect=[!Redirect!]]
		</div>
		<div class="col-md-6">
			<h3 class="NouveauClient">Nouveau client</h3><span class="petittexteNouveau">(Je crée un compte client)</span>
			<div class="textecreation">Pour créer votre compte cliquez sur le bouton ci-dessous</div>
			
				<input name="C_Creation "type="submit" class="btn btn-red Connexion" value="Je crée mon compte" onclick="$('#NewClient').css('display','block');" />
			
		</div>
	</div>
	<div id="NewClient" [IF [!I_Inscription!]][ELSE]style="display:none;"[/IF]>
		<div class="row">
			<div class="col-md-12">
				[MODULE Systeme/Login/Inscription?Redirect=[!Redirect!]]
			</div>
		</div>
	</div>
