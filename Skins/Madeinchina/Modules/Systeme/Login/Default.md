[IF [!C_Connexion!]!=]
	[CONNEXION [!C_Login!]|[!C_Pass!]]
	[IF [!Systeme::User::Public!]=1]
		[BLOC Erreur|Erreur de connexion]
			<ul class="Error">
				<li>Vos identifiants ne sont pas reconnus</li>
			</ul>
		[/BLOC]
	[ELSE]
		[IF [!Redirect!]=][!Redirect:=[!Lien!]!][/IF]
		[REDIRECT][!Redirect!][/REDIRECT]
	[/IF]
[/IF]

<form action="/[!Lien!]" method="post" id="connexion" class="form-horizontal">
	<div class="control-group">
		<label class="control-label" for="C_Login">Votre e-mail</label>
		<div class="controls">
			<input type="text" name="C_Login" id="C_Login" value="[!C_Login!]" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="C_Pass">Votre mot de passe</label>
		<div class="controls">
			<input type="password" name="C_Pass" id="C_Pass" />
		</div>
	</div>
	<div class="control-group">
		<input name="C_Connexion" type="submit" class="btn btn-kirigami Connexion" value="Connexion" />
		<a  href="/RecupPass" title="Mot de passe oublié" class="oubli">Mot de passe oublié</a>
	</div>
</form>

// Surcharge JS
<script type="text/javascript">
	window.addEvent('domready', function() {
		prepareField($('C_Login'), 'Entrez votre adresse e-mail');
		prepareField($('C_Pass'), '********');
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