[OBJ Distributeur|Shop|ModelC]
[STORPROC [!Query!]|Objet|0|1]
	[IF [!Action!]!=]
		//Maintenant on ouvre le fichier en ecriture
		[!Compte:=0!]
		[!Erreur:=0!]
		[!Existe:=0!]
		[!realname:=[!Utils::getFileName([!Form_Adresse_Upload!])!]!]
		[STORPROC Explorateur/Dossier/Home/[!Systeme::User::Id!]/Distributeur/[!realname!]|File][/STORPROC]

		//On configure le php.ini pour ouvrir une page de plus de 150Mo
		[INI memory_limit]80M[/INI]
		[INI max_execution_time]3600[/INI]
		[!TT:=[![!File::Contenu!]:/%RC%!]!]
		[STORPROC [!TT!]|Ligne|0|10000]
			//Enregistrement du nouveau shop
			[!Ligne:=[![!Ligne!]:/|!]!]
			[OBJ Distributeur|Shop|Con]
			[METHOD Con|Set][PARAM]Name[/PARAM][PARAM][!Ligne::0!][/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]Adress[/PARAM][PARAM][!Ligne::1!][/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]PostalCode[/PARAM][PARAM][!Ligne::2!][/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]City[/PARAM][PARAM][!Ligne::3!][/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]Country[/PARAM][PARAM][!Ligne::4!][/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]Email[/PARAM][PARAM][!Ligne::5!][/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]Website[/PARAM][PARAM][!Ligne::6!][/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]Phone[/PARAM][PARAM][!Ligne::7!][/PARAM][/METHOD]
			[METHOD Con|AddParent]
					[PARAM][!Query!][/PARAM]
			[/METHOD]
			[!Compte+=1!]
			[METHOD Con|Save][/METHOD]
		[/STORPROC]
		<li>[!Compte!] shops ajouts avec succes.</li>
	[ELSE]
		<div class="Propriete" style="margin:10px; border: 5px solid red;padding:20px;">
			<h1>Informations importantes</h1>
			<h3>Liste des champs à utiliser dans cet ordre: Nom | Addresse | Code Postal | Ville | Pays | Email | Website | Tel</h3>
			<ul>
				<li>Il doit y avoir un fichier csv différent par catégorie de shop. (un fichier pour shop, un fichier pour distributeur ....) à importer dans sa catégorie correspondante.</li>
				<li>Les éléments du fichier seront ajoutés, et non modifiés.</li>
				<li>Le fichier csv ne doit pas comporter d'entete avec le nom des colonnes.</li>
				<li>Le séparateur de champs est le caractère |</li>
				<li>Ne pas utiliser de séparateur de texte ex: "</li>
				<li>Surtout ne pas mettre de retour chariot dans les champs (ex les adresses sur une seule ligne)</li>
			</ul>
		</div>
		<div class="form-group group-import row">
			<label for="Form_import" class="col-sm-5 control-label">Fichier CSV</label>
			<div class="col-sm-7 form-value">
				<input type="hidden" class="ImageInput" id="Form_import" name="Form_import" value="[!DF!]"/>
				<input id="input-Image-import" type="file" multiple=false class="file-loading"/>
				<script>
					//$(document).on('ready', function() {
					$("#input-Image-import").fileinput({showCaption: false, showPreview: true, language: 'fr', uploadUrl: '/Systeme/Utils/Form/Upload.htm', dropZoneEnabled: false});
					//});
					$('#input-Image-import').on('fileuploaded', function(event, data, previewId, index) {
						console.log('document upload ', data);
						$('#Form_import').val(data.response.url);
					});

				</script>
			</div>
		</div>
		<input type="hidden" name="Action" value="Importer"/>
	[/IF]
[/STORPROC]
