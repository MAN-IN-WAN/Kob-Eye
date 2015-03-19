// OLD VERSION

[STORPROC [!Query!]|P]
[STORPROC QCM/Projet/Participation/[!P::Id!]|pr|0|1][/STORPROC]
<form id="formQCM" action="/QCM/Participation/[!P::Id!]/SaveResultat.htm" method="POST">
	<h2>[IF [!pr::Description!]][!pr::Description!][ELSE][!pr::Nom!][/IF]</h2>
	<div id="Questionnaire">
	[STORPROC QCM/Projet/[!pr::Id!]/Page|pg]
		[!pageCount:=[!NbResult!]!]
		[!pageNum:=[!Pos!]!]
		<div id="page[!Pos!]" class="QCMpage">
			<h3><br/>[!pg::Nom!]</h3>
		[STORPROC QCM/Page/[!pg::Id!]/Question|q]
			<br/>
			<div class="QCMquestion">
				[IF [!q::Gras!]]<h4>[ELSE]<h5>[/IF][!q::Question!][IF [!q::Gras!]]</h4>[ELSE]</h5>[/IF]
				[IF [!q::Explication!]]<h6>[!q::Explication!]</h6>[/IF]
				[SWITCH [!q::Icon!]|=]
					[CASE mx_number]
						<input type="text" name="v-[!q::Id!]" size="6" class="[!q::Icon!]" onkeypress='validate(event)' onchange="checkPage()" [STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]|rs|0|1]value="[!rs::Reponse!]"[/STORPROC]/>
					[/CASE]
					[CASE mx_textInput]
						<input type="text" name="v-[!q::Id!]" size="52" class="[!q::Icon!]" onchange="checkPage()"  [STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]|rs|0|1]value="[!rs::Reponse!]"[/STORPROC]/>
					[/CASE]
					[CASE mx_hSlider]
						<input type="range" name="v-[!q::Id!]" size="50" min="0" max="[IF [!q::Reponse!]>0][!q::Reponse!][ELSE]10[/IF]" class="[!q::Icon!]" onchange="checkPage()" [STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]|rs|0|1]value="[!rs::Reponse!]"[/STORPROC]/>
					[/CASE]
					[CASE mx_textArea]
						<textarea name="v-[!q::Id!]" rows="3" cols="50" class="[!q::Icon!]" onchange="checkPage()">[STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]|rs|0|1][!rs::Reponse!][/STORPROC]</textarea>
					[/CASE]
					[CASE mx_radioButton]
						<div class="QCMreponse">
						[STORPROC QCM/Question/[!q::Id!]/Reponse|r]
							<input type="radio" name="u-[!q::Id!]" class="[!q::Icon!]" onchange="checkPage()" value="[!r::Id!]" [STORPROC QCM/Participation/[!P::Id!]/Resultat/ReponseId=[!r::Id!]|rs|0|1]checked[/STORPROC]/>[!r::Reponse!][IF [!q::Horizontal!]=0]<br/>[/IF]
						[/STORPROC]
						</div>
					[/CASE]
					[CASE mx_comboBox]
						[!sel:=!]
						[STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]|rs|0|1][!sel:=[!rs::ReponseId!]!][/STORPROC]
						<select name="u-[!q::Id!]" class="[!q::Icon!]" onchange="checkPage()">
							<option value="">Pas de sélection</option>
						[STORPROC QCM/Question/[!q::Id!]/Reponse|r]
							<option value="[!r::Id!]" [IF [!sel!]==[!r::Id!]]selected[/IF]>[!r::Reponse!]</option>
						[/STORPROC]
						</select>
					[/CASE]
					[CASE mx_checkBox]
						<div class="QCMreponse">
						[IF [!q::DictionnaireId!]]
							[STORPROC QCM/Dictionnaire/[!q::DictionnaireId!]/E|r]
								<input type="checkbox" name="m-[!q::Id!][]" class="[!q::Icon!]" onchange="checkPage()" value="[!r::Id!]" [STORPROC QCM/Participation/[!P::Id!]/Resultat/ReponseId=[!r::Id!]|rs|0|1]checked[/STORPROC]/>[!r::Reponse!][IF [!q::Horizontal!]=0]<br/>[/IF]
							[/STORPROC]
						[ELSE]
							[STORPROC QCM/Question/[!q::Id!]/Reponse|r]
								<input type="checkbox" name="m-[!q::Id!][]" class="[!q::Icon!]" onchange="checkPage()" value="[!r::Id!]" [STORPROC QCM/Participation/[!P::Id!]/Resultat/ReponseId=[!r::Id!]|rs|0|1]checked[/STORPROC]/>[!r::Reponse!][IF [!q::Horizontal!]=0]<br/>[/IF]
							[/STORPROC]
						[/IF]
						</div>
					[/CASE]
				[/SWITCH]
				[IF [!q::Icon!]!=mx_formHeading]<br/>[/IF]
 			</div>
		[/STORPROC]
		</div>
	[/STORPROC]
	</div>
	<div class="QCMBoutons" style="margin-top:15px">
		<input type="submit" id="precedent" name="precedent" class="btn btn-primary" value="Précédent"/>
		<input type="submit" id="suivant" name="suivant" class="btn btn-primary" style="margin-left:10px" value="Suivant"/>
		<input type="submit" id="valider" name="valider" class="btn btn-success" style="margin-left:10px" value="Valider"/>
	</div>
</form>
[/STORPROC]


<script type="text/javascript">
var pageCount=[!pageCount!];
var pageNum=1;
var submit="";

$('formQCM').set('send', {
	onSuccess: function (html) {},
	onFailure: function(xhr){}
}).addEvent('submit', function(e){
	e.stop();
	new Request({
		method: this.method,
		url: this.action,
		onSuccess: function(responseText, responseXML) {}
	}).send(this.toQueryString()+"&submit="+submit);
});
$("suivant").addEvent('click', function(e){
	submit=this.name;
	setPage(pageNum+1);
});
$("precedent").addEvent('click', function(e){
	submit=this.name;
	setPage(pageNum-1);
});
$("valider").addEvent('click', function(e){
	submit=this.name;
});

function validate(evt) {
	var theEvent = evt || window.event;
	var key = theEvent.keyCode || theEvent.which;
	key = String.fromCharCode( key );
	var regex = /[0-9]|\./;
	if( !regex.test(key) ) {
		theEvent.returnValue = false;
		if(theEvent.preventDefault) theEvent.preventDefault();
	}
}

function setPage(num) {
	if(submit) $(submit).blur();
	$$(".QCMpage").setStyle("display","none");
	$("page"+num).setStyle("display","block");
	pageNum=num;
	checkPage(pageNum);
}

function checkPage() {
	var ok = true;
	var es=$("page"+pageNum).getElements("[class^=mx_]");
	var l = es.length;
	var r = "";
	for(var i=0; ok && i<l;) {
		var e = es[i];
		var n = e.name;
		switch(e.get("class")) {
			case "mx_number":
			case "mx_textInput":
				ok = e.value != "";
				i += 1;
				break;
			case "mx_radioButton":
			case "mx_checkBox":
				var ok1 = false;
				for(j=i; j<l && es[j].name == n; i++, j++)
					if(es[j].checked) ok1 = true;
				ok = ok1;
				break;
			case "mx_comboBox":
				ok = e.value > 0;
				i++;
				break;
			default:
				i++;
		}
	}
	$("precedent").disabled = pageNum==1;
	$("suivant").disabled = ! ok || pageNum>=pageCount;
	$("valider").disabled = ! ok || pageNum<pageCount;
}

setPage(1);
</script>
