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
	<div class="form-group">
		<label class="col-sm-4 control-label" for="C_Login">Votre e-mail</label>
		<div class="col-sm-8">
			<input type="text" name="C_Login" id="C_Login" value="[!C_Login!]" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label" for="C_Pass">Votre mot de passe</label>
		<div class="col-sm-8">
			<input type="password" name="C_Pass" id="C_Pass" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<input name="C_Connexion" type="submit" class="col-sm-offset-4 btn btn-success Connexion" value="Connexion" class="form-control"/>
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