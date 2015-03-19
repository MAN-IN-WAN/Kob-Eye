[STORPROC [!Query!]|S]
	<div class="Ariane">
		Site &gt; [!S::Domaine!]
	</div>
	<table class="Pages">
		<tr>
			<th>Url</th>
			<th>Title</th>
			<th>Description</th>
			<th>Keywords</th>
			<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
		</tr>
		[STORPROC Systeme/Site/[!S::Id!]/Page/Publier=1&Valid=1|P|0|1000|Url|ASC]
			[METHOD S|getRelativePath|Path][PARAM][!P::Url!][/PARAM][/METHOD]
			<tr rel="[!P::Id!]">
				<td><a href="[!P::Url!]" target="_blank">[IF [!Path!]=]/[ELSE][!Path!][/IF]</a></td>
				<td><textarea style="resize:none" class="title">[!P::Title!]</textarea></td>
				<td><textarea style="resize:none" class="description">[!P::Description!]</textarea></td>
				<td><textarea style="resize:none" class="keywords">[!P::Keywords!]</textarea></td>
				<td class="load"></td>
			</tr>
		[/STORPROC]
	</table>
[/STORPROC]

<script type="text/javascript">
	window.addEvent('domready', function() {
		$$('textarea').addEvent('blur', function(e) {
			var line = this.getParent('tr');
			saveLine(line);
		});
		$$('textarea').addEvent('focus', function(e) {
			var line = this.getParent('tr');
			this.getParent('td').setStyle('background-color', '#fff');
			openLine(line);
		});
		$$('textarea').addEvent('blur', function(e) {
			this.getParent('td').setProperty('style', '');
		});
		$$('tr').addEvent('mouseover', function(e) {
			this.addClass('hover');
		});
		$$('tr').addEvent('mouseout', function(e) {
			this.removeClass('hover');
		});
	});

	function saveLine(line) {
		var id = line.get('rel');
		var title = line.getElement('.title');
		var description = line.getElement('.description');
		var keywords = line.getElement('.keywords');
		title.tween('height', '20px');
		description.tween('height', '20px');
		keywords.tween('height', '20px');
		var load = line.getElement('.load');
		load.innerHTML = '<img src="/Skins/[!Systeme::Skin!]/Img/loading.gif" alt="" />';
		new Request({
			url: '/Systeme/Page/UpdateMeta.json',
            data: 'C_Id=' + id + '&C_Title=' + encodeURIComponent(title.value) + '&C_Description=' + encodeURIComponent(description.value) + '&C_Keywords=' + encodeURIComponent(keywords.value),
			onComplete: function(xhr) {
				if(xhr.trim() == 'OK') load.innerHTML = '<img src="/Skins/[!Systeme::Skin!]/Img/tick.gif" alt="" />';
				else load.innerHTML = '<img src="/Skins/[!Systeme::Skin!]/Img/error.gif" alt="" />';
			}
		}).send();
	}

	function openLine(line) {
		var title = line.getElement('.title');
		var description = line.getElement('.description');
		var keywords = line.getElement('.keywords');
		title.tween('height', '100px');
		description.tween('height', '100px');
		keywords.tween('height', '100px');
	}
</script>