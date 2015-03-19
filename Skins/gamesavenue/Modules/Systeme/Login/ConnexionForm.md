//[IF [!Action!]=][!Action:=/Systeme/Login!][/IF]
<div >
	<form name="login" action="" method="POST">
		<div class="LigneForm ">
			<label>Identifiant</label>
			<input size="36" type="text" name="C_Login" value="[!login!]"/>
		</div>
		<div class="LigneForm ">
			<label>Mot de passe</label>
			<input size="36"  type="password" name="C_Pass" />
		</div>
		<div class="LigneForm ">
			<label>&nbsp;</label>
			<div class="btnRouge">
				<div class="btnRougeGauche"></div>
				<div class="btnRougeCentre">
					<input type="submit" name="C_Valid" value="Connexion" class="btnRougeCentre" />
				</div>
				<div class="btnRougeDroite"></div>
			</div>
		</div>
	</form>
</div>
