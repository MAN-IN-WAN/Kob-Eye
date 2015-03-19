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

<form action="/[!Lien!]" method="post" id="connexion">
	<div class="LigneForm">
		<label>Votre e-mail</label>
		<input type="text" name="C_Login" id="C_Login" value="[!C_Login!]" placeholder="Entrez votre adresse e-mail" style="width:100%"/>
	</div>
	<div class="LigneForm">
		<label>Votre mot de passe</label>
		<input type="password" name="C_Pass" id="C_Pass" style="width:100%"/>
	</div>
	<div class="">
		<input name="C_Connexion" type="submit" class="btn btn-kirigami Connexion" value="Connexion"/>
		<a  href="/RecupPass" title="Mot de passe oublié" class="oubli">Mot de passe oublié</a>
	</div>
</form>

// Surcharge JS
<script type="text/javascript">
	$(document).ready(function() {
//		prepareField($('#C_Login'), 'Entrez votre adresse e-mail');
		prepareField($('#C_Pass'), '********');
	});

	function prepareField( field, text) {
		// init
		if(field.prop('value') == '' || field.prop('value') == text) {
			field.prop('value', text);
			field.css({
				'font-style': 'italic',
				'color': '#888'
			});
		}
		// click dans le champ
		field.on('focus', function() {
			if(field.attr('value') == text) {
				field.attr('value', '');
			}
			field.css({
				'font-style': 'normal',
				'color': '#000'
			});
		});
		// sortie du champ
		field.on('blur', function() {
			if(field.attr('value') == '') {
				field.attr('value', text);
				field.css({
					'font-style': 'italic',
					'color': '#888'
				});
			}
		});
	}
</script>