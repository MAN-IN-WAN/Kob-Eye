[IF [!C_Connexion!]!=]
	[CONNEXION [!C_Login!]|[!C_Pass!]]
	[IF [!Systeme::User::Public!]=1]
		[BLOC Erreur|Erreur de connexion]
			<ul>
				<li style="color:#ff0000;font-weight:bold;padding:10px;font-size:13px;">Identifiant ou mot de passe erronés</li>
			</ul>
		[/BLOC]
	[ELSE]
		[REDIRECT][!Redirect!][/REDIRECT]
	[/IF]
[/IF]

<h2>Accès Prescripteurs</h2>

<form action="/[!Lien!]" method="post" id="connexion" class="FormContact" style="border:none;">
	<div class="LigneForm">
		<label>Identifiant</label>
		<input type="text" name="C_Login" id="C_Login" value="[!C_Login!]" />
	</div>
	<div class="LigneForm">
		<label>Mot de passe</label>
		<input type="password" name="C_Pass" id="C_Pass" />
	</div>
	<div class="lienBtnConx" style=" padding-left: 226px;">
		<input style="width:90px;" name="C_Connexion" type="submit" class="Connexion" value="Connexion" />
	</div>
</form>

// Surcharge JS
<script type="text/javascript">
	window.addEvent('domready', function() {
		prepareField($('C_Login'), '');
		prepareField($('C_Pass'), '');
	});

	function prepareField( field, text ) {
		// init
		if(field.value == '' || field.value == text) {
			field.value = text;
			field.setStyles({
				'font-style': 'italic',
				'color': '#888'
			});
		}
		// click dans le champ
		field.addEvent('focus', function() {
			if(field.value == text) {
				field.value = '';
			}
			field.setStyles({
				'font-style': 'normal',
				'color': '#000'
			});
		});
		// sortie du champ
		field.addEvent('blur', function() {
			if(field.value == '') {
				field.value = text;
				field.setStyles({
					'font-style': 'italic',
					'color': '#888'
				});
			}
		});
	}
</script>