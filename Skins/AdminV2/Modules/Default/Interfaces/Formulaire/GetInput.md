[IF [!Prefixe!]==RechProp]
    [!DefaultValue:=!]
[ELSE]
    [!DefaultValue:=[!Prop::Default!]!]
[/IF]
[SWITCH [!Prop::Type!]|=]
	[CASE boolean]
		[IF [!Valeur!]=&&[!Valeur!]!=0]
			[IF [!DefaultValue!]=1]
				<input type="radio" name="[!Prefixe!][!Prop::Nom!]" value="1" class="[IF [!DisplayReload!]=True] ChangeOnReload[/IF]" CHECKED>Oui
				<input type="radio" name="[!Prefixe!][!Prop::Nom!]" value="0" class="[IF [!DisplayReload!]=True] ChangeOnReload[/IF]">Non
			[ELSE]
				<input type="radio" name="[!Prefixe!][!Prop::Nom!]" value="1" class="[IF [!DisplayReload!]=True] ChangeOnReload[/IF]">Oui
				<input type="radio" name="[!Prefixe!][!Prop::Nom!]" value="0" class="[IF [!DisplayReload!]=True] ChangeOnReload[/IF]" CHECKED>Non
			[/IF]
		[ELSE]
			[IF [!Valeur!]=1]
				<input type="radio" name="[!Prefixe!][!Prop::Nom!]" value="1" class="[IF [!DisplayReload!]=True] ChangeOnReload[/IF]" CHECKED>Oui
				<input type="radio" class="[IF [!DisplayReload!]=True] ChangeOnReload[/IF]" name="[!Prefixe!][!Prop::Nom!]" value="0">Non
			[ELSE]
				<input type="radio" class="[IF [!DisplayReload!]=True] ChangeOnReload[/IF]" name="[!Prefixe!][!Prop::Nom!]" value="1">Oui
				<input type="radio" class="[IF [!DisplayReload!]=True] ChangeOnReload[/IF]" name="[!Prefixe!][!Prop::Nom!]" value="0" CHECKED>Non
			[/IF]
		[/IF]
	[/CASE]
	[CASE ObjectClass] 
		[!T:=[![!Prefixe!][!Prop::Nom!]!]!]
		[IF [!Utils::isArray([!T!])!]]
			[STORPROC [!T!]|E]
				[!VAL:=[!E!]!]
			[/STORPROC]
		[/IF]
		[IF [!VAL!]=][!VAL:=[!Valeur!]!][/IF]
<a href="" style="display:block;float:right;margin-right:5%;padding-top:5px;" class="makePopup" rel="/Systeme/Interfaces/Explorer/Popup.htm?Prop=[!Prop::Nom!]&Obj=[!ObjectTT!]&Module=[!Module::Actuel::Nom!]&Prefixe=[!Prop::query!]&InputId=[!Prefixe!][!Prop::Nom!]::/[!Query!]::false"><img src="/Skins/AdminV2/Img/folder_explore.png"/></a>
		<input type="text" class="Champ" name="[!Prefixe!][!Prop::Nom!]" id="[!Prefixe!][!Prop::Nom!]" value="[!VAL!]" style="width:90%;">
//		<input type="submit" name="[!Prefixe!][!Prop::Nom!]_explore" value="OK" class="ExplorerBouton" />
		[IF [![!Prefixe!][!Prop::Nom!]_explore!]=OK]
			[INFO [!Prop::query!]|Test]
			[!PrefixeVar:=[!Test::Module!]/[!Test::TypeChild!]/!]
			[MODULE Systeme/Interfaces/Explorer?Prop=[!Prop!]&Prefixe=[!Prefixe!]&PrefixeVar=[!PrefixeVar!]]
		[/IF]
	[/CASE]
	[CASE date]
		<input type="text" id="[!Prefixe!][!Prop::Nom!]" class="ncalendar" name="[!Prefixe!][!Prop::Nom!]" value="[!Valeur!]" />
	[/CASE]
	[CASE color]
		<input type="text" id="[!Prefixe!][!Prop::Nom!]" name="[!Prefixe!][!Prop::Nom!]" value="[!Valeur!]" class="colorP"/>
	[/CASE]
	[CASE ref]
		<input type="text" name="[!Prefixe!][!Prop::Nom!]" value="[!Valeur!]" /><br />
		<a href="/[!Prop::queryRef!]/[!Valeur!]">Accéder</a>
	[/CASE]
	[CASE text]
		[IF [!Prop::length!]]
			[!Nu:=[!Utils::Random(10000)!]!]
			<script type="text/javascript">
			
				function limiteur[!Nu!](){
					var maximum = [!Prop::length!];
					var champ = $('field[!Prefixe!][!Prop::Nom!]');
					var indic = $('field[!Prefixe!][!Prop::Nom!]ind');
				
					if (champ.value.length > maximum){
						champ.value = champ.value.substring(0, maximum);
					}else {
						indic.value = maximum - champ.value.length;
					}
				}
			</script>
			<textarea ROWS="15" class="Champ" style="width:491px;" onKeyDown="limiteur[!Nu!]();" onKeyUp="limiteur[!Nu!]();"  name="[!Prefixe!][!Prop::Nom!]" id="field[!Prefixe!][!Prop::Nom!]">[**Valeur**]</textarea>
			<input readonly type=text name="indicateur" id="field[!Prefixe!][!Prop::Nom!]ind" class="Decompte" value="[!Prop::length!]"> caract&egrave;res restants
		[ELSE]
			<textarea ROWS="15" class="Champ" style="width:491px;" name="[!Prefixe!][!Prop::Nom!]" id="field[!Prefixe!][!Prop::Nom!]">[!Valeur!]</textarea>
		[/IF]
	[/CASE]
	[CASE bbcode]
		<textarea ROWS="20" class="Champ EditorBBCode" style="width:491px;" name="[!Prefixe!][!Prop::Nom!]" id="field[!Prefixe!][!Prop::Nom!]">[!Valeur!]</textarea>
	[/CASE]
	[CASE html]
		<textarea ROWS="30" class="Champ EditorFull" style="width:491px;" name="[!Prefixe!][!Prop::Nom!]" id="field[!Prefixe!][!Prop::Nom!]">[!Valeur!]</textarea>
	[/CASE]
	[CASE textonly]
		<textarea ROWS="15" class="Champ" style="width:491px;" name="[!Prefixe!][!Prop::Nom!]" id="field[!Prefixe!][!Prop::Nom!]">[!Valeur!]</textarea>
	[/CASE]
	[CASE metat]
			<script type="text/javascript">
				function limiteur(){
					var maximum = 150;
					var champ1 = $('frm[!Prefixe!][!Prop::Nom!]');
					var indic1 = $('frm[!Prefixe!][!Prop::Nom!]ind');
				
					if (champ.value.length > maximum){
						champ.value = champ.value.substring(0, maximum);
					}else {
						indic.value = maximum - champ.value.length;
					}
				}
			</script>
		<input type="text"  class="Champ" name="[!Prefixe!][!Prop::Nom!]" onKeyDown="limiteur();" onKeyUp="limiteur();" value="[!Valeur!]" id="frm[!Prefixe!][!Prop::Nom!]"/><br />
		<input readonly type=text name="indicateur" id="frm[!Prefixe!][!Prop::Nom!]ind" class="Decompte" value="150"> caract&egrave;res restants
	[/CASE]
	[CASE metad]
			<script type="text/javascript">
				function limiteur1(){
					var maximum1 = 250;
					var champ1 = $('frm[!Prefixe!][!Prop::Nom!]');
					var indic1 = $('frm[!Prefixe!][!Prop::Nom!]ind');
				
					if (champ1.value.length > maximum1){
						champ1.value = champ1.value.substring(0, maximum1);
					}else {
						indic1.value = maximum1 - champ1.value.length;
					}
				}
			</script>
		<textarea class="Champ" name="[!Prefixe!][!Prop::Nom!]" onKeyDown="limiteur1();" onKeyUp="limiteur1();" id="frm[!Prefixe!][!Prop::Nom!]">[!Valeur!]</textarea><br />
		<input readonly type=text name="indicateur1" id="frm[!Prefixe!][!Prop::Nom!]ind" class="Decompte" value="250"> caract&egrave;res restants
	[/CASE]
	[CASE swf]
	    <script type="text/javascript">
		var setVars = $empty;
		Fl.addToLoad(function(){
		  setVars = function (t) {
		    var d = document.getElementById("[!Prefixe!][!Prop::Nom!]");
		    d.value = t;};
		});
	    </script>
		<input type="text" id="[!Prefixe!][!Prop::Nom!]" name="[!Prefixe!][!Prop::Nom!]" value="[!Valeur!]" style="visibility:hidden;"/>
		<object id="[!Prefixe!][!Prop::Nom!]Swf" width="95%" height="450" type="application/x-shockwave-flash" data="[!Prop::Swf!]" style="visibility: visible;">
			<param name="id" value="[!Prop::Nom!]"/>
			<param name="FLASHVARS" value="Vars=[!Valeur!]"/>
			<param name="Pos" value="[!Valeur!]"/>
		</object>
	[/CASE]
	[CASE metak]
			<script type="text/javascript">
				function limiteur2(){
					var maximum2 = 250;
					var champ1 = $('frm[!Prefixe!][!Prop::Nom!]');
					var indic1 = $('frm[!Prefixe!][!Prop::Nom!]ind');
				
					if (champ2.value.length > maximum2){
						champ2.value = champ2.value.substring(0, maximum2);
					}else {
						indic2.value = maximum2 - champ2.value.length;
					}
				}
			</script>
		<textarea class="Champ" name="[!Prefixe!][!Prop::Nom!]" onKeyDown="limiteur2();" onKeyUp="limiteur2();" id="frm[!Prefixe!][!Prop::Nom!]">[!Valeur!]</textarea><br />
		<input readonly type=text name="indicateur2" id="frm[!Prefixe!][!Prop::Nom!]ind" class="Decompte" value="250"> caract&egrave;res restants
	[/CASE]			
	[CASE file]
		<div id="[!Prefixe!][!Prop::Nom!]_DivUpload" >
		<div class="Content" [IF [!Valeur!]!=]style="display:none"[/IF]>
			<div class="UploadProgress">
			<img src="/Skins/AdminV2/Img/fancy/progress/bar.gif" class="progress current-progress" />
			<div class="current-text"></div>
			</div>
			<a class="Browse" href="#">Attacher un fichier</a>
		</div>
		<div class="Result" [IF [!Valeur!]=]style="display:none"[/IF]>
			<input type="text"  id="[!Prefixe!][!Prop::Nom!]" name="[!Prefixe!][!Prop::Nom!]" value="[!Valeur!]" class="Champ"/>
			<a class="Toggle">
			Changer de fichier
			</a>
			<span class="FileName" style="display:none;">
			[!Valeur!]
			</span>
		</div>
		</div>
		<ul id="[!Prefixe!][!Prop::Nom!]_List" style="display:none"></ul>
		<script type="text/javascript">
			var Cook = Cookie.read('KE_SESSID');
			Fl.makeUpload("[!Prefixe!][!Prop::Nom!]_DivUpload",
			"[!Prefixe!][!Prop::Nom!]_List",Cook,"[!Module::Actuel::Nom!]","[!ObjectTT!]"[IF [!Type!]=Popup],true[/IF]);
		</script>
	[/CASE]
	[CASE image]
		<div id="[!Prefixe!][!Prop::Nom!]_DivUpload" >
		<div class="Content" [IF [!Valeur!]!=]style="display:none"[/IF]>
			<div class="UploadProgress">
			<img src="/Skins/AdminV2/Img/fancy/progress/bar.gif" class="progress current-progress" />
			<div class="current-text"></div>
			</div>
			<a class="Browse" href="#">Attacher un fichier</a>
		</div>
		<div class="Result" [IF [!Valeur!]=]style="display:none"[/IF]>
			<input type="text"  id="[!Prefixe!][!Prop::Nom!]" name="[!Prefixe!][!Prop::Nom!]" value="[!Valeur!]" class="Champ"/>
			<a class="Toggle">
			Changer de fichier
			</a>
			<span class="FileName" style="display:none;">
			[!Valeur!]
			</span>
		</div>
		</div>
		<ul id="[!Prefixe!][!Prop::Nom!]_List" style="display:none"></ul>
		<script type="text/javascript">
			var Cook = Cookie.read('KE_SESSID');
			Fl.makeUpload("[!Prefixe!][!Prop::Nom!]_DivUpload",
			"[!Prefixe!][!Prop::Nom!]_List",Cook,"[!Module::Actuel::Nom!]","[!ObjectTT!]"[IF [!Type!]=Popup],true[/IF]);
		</script>
	[/CASE]
	[CASE template]
		[IF [!ObjectTT!]=ActiveTemplate]
			[!Obcl:=[!O::ObjectClass!]!]
		[ELSE]
			[!Obcl:=[!ObjectTT!]!]
		[/IF]
		<script type="text/javascript">
			function loadTemplateConfig(v){
				if (v=='')return false;
				var myElement = document.moo('[!Prefixe!]TemplateConfig');
				myElement.set('text','TEST');
				var myRequest = new Request({
					url: 'Templates/'+v+'/Template.conf',
					method: 'post',
					onRequest: function(){
						myElement.set('text', 'chargement...');
					},
					onSuccess: function(responseText){
						myElement.set('text', responseText);
					},
					onFailure: function(){
						alert('Fail');
						myElement.set('text', 'Désolé votre requete n\'a put aboutir :(');
					}
				});
				myRequest.send();
			}
		</script>
		//Affichage combobox
		<select class="Champ" name="[!Prefixe!][!Prop::Nom!]" onChange="loadTemplateConfig(this.value);">
			<option value="">...</option>
			[STORPROC [!O::getTemplates!]|Val|0|100]
				<option value="[!Val!]" [IF [!Val!]=[!Valeur!]]selected="selected"[/IF]>[!Val!]</option>
			[/STORPROC]
		</select>
	[/CASE]
	[CASE templateconfig]
		//Config xml
		<textarea ROWS="15" id="[!Prefixe!][!Prop::Nom!]" class="Champ" style="width:491px;" name="[!Prefixe!][!Prop::Nom!]" id="field[!Prefixe!][!Prop::Nom!]">[!Valeur!]</textarea>
	[/CASE]
	[CASE plugin]
		[IF [!ObjectTT!]=ActiveTemplate]
			[!Obcl:=[!O::ObjectClass!]!]
		[ELSE]
			[!Obcl:=[!ObjectTT!]!]
		[/IF]
		<script type="text/javascript">
			function loadTemplateConfig(v){
				if (v=='')return false;
				var myElement = document.moo('[!Prefixe!]PluginConfig');
				myElement.set('text','TEST');
				var myRequest = new Request({
					url: 'Modules/[!O::Module!]/Plugins/[!O::ObjectType!]/'+v+'/Plugin.conf',
					method: 'post',
					onRequest: function(){
						myElement.set('text', 'chargement...');
					},
					onSuccess: function(responseText){
						myElement.set('text', responseText);
					},
					onFailure: function(){
						alert('Fail');
						myElement.set('text', 'Désolé votre requete n\'a put aboutir :(');
					}
				});
				myRequest.send();
			}
		</script>
		//Affichage combobox
		<select class="Champ" name="[!Prefixe!][!Prop::Nom!]" onChange="loadTemplateConfig(this.value);">
			<option value="">...</option>
			[STORPROC [!O::getPlugins!]|Val|0|100]
				<option value="[!Val!]" [IF [!Val!]=[!Valeur!]]selected="selected"[/IF]>[!Val!]</option>
			[/STORPROC]
		</select>
	[/CASE]
	[CASE pluginconfig]
		//Config xml
		<textarea ROWS="15" id="[!Prefixe!][!Prop::Nom!]" class="Champ" style="width:491px;" name="[!Prefixe!][!Prop::Nom!]" id="field[!Prefixe!][!Prop::Nom!]">[!Valeur!]</textarea>
	[/CASE]
	[CASE conf]
		//Affichage combobox
		<select class="Champ" name="[!Prefixe!][!Prop::Nom!]">
			<option value="">...</option>
			[STORPROC [!CONF::[!Prop::query!]!]|Val|0|100|[!Ov!]|ASC]
				<option value="[!Key!]" [IF [!Key!]=[!Valeur!]]selected="selected"[/IF]>[!Key!]</option>
			[/STORPROC]
		</select>
	[/CASE]
	[CASE price]
		<input type="text" class="Champ" name="[!Prefixe!][!Prop::Nom!]" id="[!Prefixe!][!Prop::Nom!]" value="[!Valeur!]" onkeypress="calculHT_TTC('HT');"  onchange="calculHT_TTC('HT');"  style="width:100px;" >
		Choisir la tva pour calculer votre montant : <select class="Champ" name="Tva" id ="[!Prefixe!][!Prop::Nom!]Tva" style="width:100px;" >
			[STORPROC Fiscalite/TauxTva|Tx]
				<option value="[!Tx::Taux!]" >[!Tx::Taux!]</option>
			[/STORPROC]
		</select>
		TTC : <input type="text" class="Champ" name="TarifTTC" id="[!Prefixe!][!Prop::Nom!]TarifTTC" value="[!Prop::Valeur:*1.2!]" onkeypress="calculHT_TTC('TTC');"  onchange="calculHT_TTC('TTC');" style="width:100px;" >
		<script type="text/javascript" >
			function calculHT_TTC (type) {
				var tva =$('[!Prefixe!][!Prop::Nom!]Tva').value;
				if (type=='TTC') {
					var ttc= $('[!Prefixe!][!Prop::Nom!]TarifTTC').value;
					$('[!Prefixe!][!Prop::Nom!]').value = ttc / ((tva/100) +1);
				}else{
					var ht =$('[!Prefixe!][!Prop::Nom!]').value;
					$('[!Prefixe!][!Prop::Nom!]TarifTTC').value = ht * (1 + (tva/100));
				}
			}
			window.onload = function() {
				calculHT_TTC('TTC');
			}
		</script>
	[/CASE]	
	[DEFAULT]
		[IF [!Utils::isArray([!Prop::Values!])!]]
			[STORPROC [!Prop::Values!]|Val]
				<select name="[!Prefixe!][!Prop::Nom!]" class="[IF [!DisplayReload!]=True] ChangeOnReload[/IF] Champ">
					<option value="">...</option>
					[LIMIT 0|100]
						[!T:=[![!Val!]:/::!]!]
						
						[COUNT [!T!]|S]
						[IF [!S!]>1]
							<option value="[!T::0!]" [IF [!Valeur!]=[!T::0!]]selected="selected"[/IF]>[!T::1!]</option>
						[ELSE]
							<option value="[!Val!]" [IF [!Valeur!]=[!Val!]]selected="selected"[/IF]>[!Val!]</option>
						[/IF]
					[/LIMIT]
				</select>
				[NORESULT]
				[/NORESULT]
			[/STORPROC]
		[ELSE]
			[IF [!Prop::query!]]
				[STORPROC [![!Prop::query!]:/::!]|Q|0|1][/STORPROC]
				[STORPROC [![!Prop::query!]:/::!]|Ov|1|1][/STORPROC]
				[STORPROC [![!Prop::query!]:/::!]|Ov2|2|1][/STORPROC]
				[COUNT [!Q!]|Con]
				[IF [!Con!]>100]
					// Auto completion
					[IF [!Valeur!]=][!Val:=-1!][ELSE][!Val:=[!Valeur!]!][/IF]
					<input class="Champ AC" type="text" autocomplete="off" name="[!Prefixe!][!Prop::Nom!]" id="[!Prefixe!][!Prop::Nom!]" />
					[IF [!Ov2!]=]
						<script type="text/javascript">autoCompleteField('[!Prefixe!][!Prop::Nom!]', '[!Q!]', '[!Val!]', '[!Ov!]', '[!Ov!]');</script>
					[ELSE]
						<script type="text/javascript">autoCompleteField('[!Prefixe!][!Prop::Nom!]', '[!Q!]', '[!Val!]', '[!Ov!]', '[!Ov2!]');</script>
					[/IF]
				[ELSE]
					//Affichage combobox
					<select class="Champ" name="[!Prefixe!][!Prop::Nom!]">
						<option value="">...</option>
						[STORPROC [!Prop::query!]|Val|0|100|[!Ov!]|ASC]
							[IF [!Key!]!=[!Pos:-1!]][!Vale:=[!Key!]!][ELSE][!Vale:=[!Val!]!][/IF]
							<option value="[!Vale!]" [IF [!Vale!]=[!Valeur!]]selected="selected"[/IF]>[!Val!]</option>
						[/STORPROC]
					</select>
				[/IF]
			[ELSE]
				[IF [!Prop::method!]]
					//Affichage combobox
					<select class="Champ" name="[!Prefixe!][!Prop::Nom!]">
						<option value="">...</option>
						[STORPROC [!O::[!Prop::method!]()!]|Val]
							[IF [!Key!]!=[!Pos:-1!]][!Vale:=[!Key!]!][ELSE][!Vale:=[!Val!]!][/IF]
							<option value="[!Vale!]" [IF [!Vale!]=[!Valeur!]]selected="selected"[/IF]>[!Val!]</option>
						[/STORPROC]
					</select>
				[ELSE]
					<input type="text" class="Champ" [IF [!Prop::length!]]maxlength="[!Prop::length!]"[/IF] name="[!Prefixe!][!Prop::Nom!]" [IF [!Prop::auto!]=1]readonly="readonly"[/IF] value="[UTIL SPECIALCHARS][!Valeur!][/UTIL]">
				[/IF]
			[/IF]
		[/IF]
	[/DEFAULT]
[/SWITCH]

