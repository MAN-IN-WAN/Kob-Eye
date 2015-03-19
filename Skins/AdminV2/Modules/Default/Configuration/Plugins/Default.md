[MODULE Systeme/Configuration/Top]
[!UniqID:=[!TMS::Now!]!]
<div style="width:100%;-moz-border-radius:3px 3px;background:transparent;">
	<div style="background:white;margin-top:10px;-moz-border-radius:3px 3px;padding:3px;margin:5px;">
		<div class="BigTitle">Plugins disponibles et actifs</div>
		<table width="100%">
		[STORPROC [!Module::[!Module::Actuel::Nom!]::getPluginCategories()!]|Val|0|100]
			[!DEBUG::Val!]
			<tr>
				<td colspan="2"><div class="BigTitle" style="background:#343434;margin:0;">[!Val!]
				</div></td>
			</tr>
			<tr>
				<td width="110" valign="top"><img src="/[!Val::getScreen()!]" width="147" height="200" /></td>
				<td valign="top">
					//Tranforme le genericClass en template
					[!UIDTEMPLATE:=Template_[!UniqID!]_[!Val::Id!]!]
					<div id="[!UIDTEMPLATE!]">
						[STORPROC [!Module::[!Module::Actuel::Nom!]::getPlugins([!Val!])!]|Z]
							<div class="BigTitle" style="background:#B5B5B5;margin:0;color:black;">[!Z::Name!]<a href="/Systeme/Configuration/Modeles/addComponent?t=[!Val::Id!]&z=[!Z::Name!]" rel="popup" redirectUrl="/[!Lien!].htm" style="float:right;"><img src="/Skins/AdminV2/Img/add.png" class="ListeMiniImg" style="margin:-1px 2px 2px 0;"/></a></div>
							<div zone="[!Z::Name!]" class="Ct" style="padding-bottom:10px">
								[STORPROC [!Z::getComponents!]|C]
									<div id="[!UIDTEMPLATE!]-[!Z::Name!]-[!Key!]" ordre="[!Key!]" class="BigTitle" style="background:#ddd;color:black;margin:5px 0;position:relative;cursor:move; overflow:hidden">[!C::Name!]
										<a class="deleteLink" href="/Systeme/Configuration/Modeles/removeComponent?t=[!Val::Id!]&z=[!Z::Name!]&c=[!Key!]" rel="confirm" message="Attention! Vous allez supprimer le composant [!C::Name!].Etes vous sur de vouloir le supprimer ?" title="Suppression d'un élément" redirectUrl="/[!Lien!].htm"  style="float:right;"><img src="/Skins/AdminV2/Img/delete.png" class="ListeMiniImg" style="margin:-1px 2px 2px 0;"/></a>
										<a class="editLink" href="/Systeme/Configuration/Modeles/editComponent?t=[!Val::Id!]&z=[!Z::Name!]&c=[!Key!]" rel="popup" redirectUrl="/[!Lien!].htm"  style="float:right;"><img src="/Skins/AdminV2/Img/application_edit.png" class="ListeMiniImg"   style="margin:-1px 2px 2px 0;"/></a>
									</div>
								[/STORPROC]
							</div>
						[/STORPROC]
						// DragAndDrop dans la Zone
						<script type="text/javascript">
							var fromZone[!UIDTEMPLATE!] = '';
							var toZone[!UIDTEMPLATE!] = '';
							st[!UIDTEMPLATE!] = new Sortables('#[!UIDTEMPLATE!] div.Ct', {
								clone:false,
								onStart: function(el) { 
									fromZone[!UIDTEMPLATE!] = el.getParent('div.Ct').get('zone');
									el.setStyle('background','#add8e6');
								},
								onComplete: function(el) {
									// Détache temporairement
									el.setStyle('background','#ddd');
									st[!UIDTEMPLATE!].detach();
									// Params
									var items = $$('#[!UIDTEMPLATE!] div.Ct');
									var from = el.get('ordre');
									// Réorganise
									var to = 0;
									items.each(function(div) {
										var subDiv = div.getElements('div');
										subDiv.each(function(subD, idx) {
											subD.set('ordre',idx);
											var liens = subD.getElements('a');
											liens.each(function(lien) {
												var str = lien.get('href');
												var pos = str.lastIndexOf('=');
												lien = lien.set('href',str.substring(0, pos+1) + idx);
											});
											if(el.get('id') == subD.get('id')) to = idx;
										});
									});
									// Update
									toZone[!UIDTEMPLATE!] = el.getParent('div.Ct').get('zone');
									var req = new Request({
										url:'/Systeme/Configuration/Modeles/setOrder.htm',
										method:'post',
										data:'from=' + from + '&to=' + to + '&t=[!Val::Id!]&fromZone=' + fromZone[!UIDTEMPLATE!] + '&toZone=' + toZone[!UIDTEMPLATE!],
										onRequest: function() {
										},
										onSuccess: function() {
											// Ré-attache
											el.highlight();
											st[!UIDTEMPLATE!].attach();
										}
									}).send();
								}
							});
							st[!UIDTEMPLATE!].attach();
						</script>
					</div>
				</td>
				<td></td>
			</tr>
		[/STORPROC]
		</table>
	</div>
</div>
[MODULE Systeme/Configuration/Bottom]

