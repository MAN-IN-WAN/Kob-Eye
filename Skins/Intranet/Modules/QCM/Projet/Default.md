[HEADER CSS]Skins/[!Systeme::Skin!]/Css/QCMStyle.css[/HEADER]

//***** mode test
//[STORPROC ProxyCas/Host/Nom=achard@unibio.fr|CasUser|0|1][/STORPROC]
//***********************

[IF [!CasUser!]=]
<div class="alert alert-danger">
Veuillez vous connecter
</div>
[ELSE]
	[!acc:=1!]
	[STORPROC [!Query!]|pr]
	[IF [!pr::TypeProjetId!]==1][!qcm:=1!][/IF]
	[IF [!Test!]=]
		[STORPROC QCM/Projet/[!pr::Id!]/Participation/Host.ParticipationHostId([!CasUser::Id!])|P]
		[NORESULT]
			[IF [!qcm!]]
				[!acc:=0!]
	<div class="alert alert-danger">
	Vous n'avez pas accès à ce questionnaire
	</div>
			[ELSE]
				[OBJ QCM|Participation|P]
				[!P::addParent([!pr!])!]
				[!P::addParent([!CasUser!])!]
				[!P::Save()!]
			[/IF]
		[/NORESULT]
			[IF [!P::Valide!]]
				[!acc:=0!]
	<div class="alert alert-danger">
	Ce questionnaire est déjà validé
	</div>
			[/IF]
		[/STORPROC]
	[/IF]

	[IF [!acc!]]

[IF [!Test!]=]
<form id="formQCM" action="/QCM/Participation/[!P::Id!]/SaveResultat.json" method="POST">
[/IF]
	<div class="QCMProjet">
		<h2>[IF [!pr::Description!]][!pr::Description!][ELSE][!pr::Nom!][/IF][IF [!Test!]] (Apperçu [!pr::Nom!])[/IF]</h2>
	[STORPROC QCM/Projet/[!pr::Id!]/Page|pg]
		[!pageCount:=[!NbResult!]!]
		[!pageNum:=[!Pos!]!]
		<div id="page[!Pos!]" class="QCMPage">
			<h3>[!pg::Nom!] ([!pageNum!]/[!pageCount!])</h3>
		[STORPROC QCM/Page/[!pg::Id!]/Question|q]
			<div class="QCMquestion">
				[IF [!q::Gras!]]<h4>[ELSE]<h5>[/IF][!q::Question!][IF [!q::Gras!]]</h4>[ELSE]</h5>[/IF]
				[IF [!q::Explication!]]<h6>[!q::Explication!]</h6>[/IF]
				[IF [!q::Image!]]<div class="QCMImageQuestion"><img src="/[!q::Image!]"/></div>[/IF]
				<div class="QCMreponse">
				[SWITCH [!q::Icon!]|=]
					[CASE mx_number]
							<input type="text" name="v-[!q::Id!]" size="6" class="[!q::Icon!]" onkeypress='validate(event)' onchange="checkPage()"
							[IF [!Test!]=1]value="[!q::Reponse!]"[ELSE][IF [!qcm!]][STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]|rs|0|1]value="[!rs::Reponse!]"[/STORPROC][/IF][/IF]
							/>
					[/CASE]
					[CASE mx_textInput]
							<input type="text" name="v-[!q::Id!]" size="52" class="[!q::Icon!]" onchange="checkPage()" 
							[IF [!Test!]=1]value="[!q::Reponse!]"[ELSE][IF [!qcm!]][STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]|rs|0|1]value="[!rs::Reponse!]"[/STORPROC][/IF][/IF]
							/>
					[/CASE]
					[CASE mx_hSlider]
							[IF [!q::Reponse!]>0][!max:=[!q::Reponse!]!][ELSE][!max:=10!][/IF]
							[!val:=0!]
							[IF [!Test!]=][IF [!qcm!]][STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]|rs|0|1][!val:=[!rs::Reponse!]!][/STORPROC][/IF][/IF]
						<div class="QCMReponseLigneHorizontal">
							<input type="range" name="v-[!q::Id!]" size="50" min="0" class="QCMSlider" max="[!max!]" class="[!q::Icon!]" onchange="checkPage()" value="[!val!]"/><input type="text" class="QCMSliderVal" size="6" readonly value="[!val!]/[!max!]"/>
						</div>
					[/CASE]
					[CASE mx_textArea]
							<textarea name="v-[!q::Id!]" rows="3" cols="50" class="[!q::Icon!]" onchange="checkPage()">[IF [!Test!]=][IF [!qcm!]][STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]|rs|0|1][!rs::Reponse!][/STORPROC][/IF][/IF]</textarea>
					[/CASE]
					[CASE mx_radioButton]
						[IF [!q::DictionnaireId!]]
							[STORPROC QCM/Dictionnaire/[!q::DictionnaireId!]/Entree|r]
						<div class="[IF [!q::Horizontal!]]QCMReponseLigneHorizontal[ELSE]QCMReponseLigneVertical[/IF]">
							<input type="radio" name="u-[!q::Id!]" class="[!q::Icon!]" onchange="checkPage()" value="[!r::Id!]" 
							[IF [!Test!]=1][IF [!r::BonneReponse!]]checked[/IF][ELSE][IF [!qcm!]][STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]&ReponseId=[!r::Id!]|rs|0|1]checked[/STORPROC][/IF][/IF]
							/>[IF [!r::Image!]]<div class="QCMImageReponse"><img src="/[!r::Image!]"/></div>[/IF][!r::Reponse!]
						</div>
							[/STORPROC]
						[ELSE]
							[STORPROC QCM/Question/[!q::Id!]/Reponse|r]
						<div class="[IF [!q::Horizontal!]]QCMReponseLigneHorizontal[ELSE]QCMReponseLigneVertical[/IF]">
							<input type="radio" name="u-[!q::Id!]" class="[!q::Icon!]" onchange="checkPage()" value="[!r::Id!]" 
							[IF [!Test!]=1][IF [!r::BonneReponse!]]checked[/IF][ELSE][IF [!qcm!]][STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]&ReponseId=[!r::Id!]|rs|0|1]checked[/STORPROC][/IF][/IF]
							/>[IF [!r::Image!]]<div class="QCMImageReponse"><img src="/[!r::Image!]"/></div>[/IF][!r::Reponse!]
						</div>
							[/STORPROC]
						[/IF]
					[/CASE]
					[CASE mx_comboBox]
						<div class="QCMReponseLigne">
						[!sel:=!]
						[IF [!Test!]=][IF [!qcm!]][STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]|rs|0|1][!sel:=[!rs::ReponseId!]!][/STORPROC][/IF][/IF]
							<select name="u-[!q::Id!]" class="[!q::Icon!]" onchange="checkPage()">
								<option value="">Pas de sélection</option>
						[IF [!q::DictionnaireId!]]
							[STORPROC QCM/Dictionnaire/[!q::DictionnaireId!]/Entree|r]
								<option value="[!r::Id!]" [IF [!Test!]=1][IF [!r::BonneReponse!]]selected[/IF][ELSE][IF [!sel!]==[!r::Id!]]selected[/IF][/IF]>[!r::Reponse!]</option>
							[/STORPROC]
						[ELSE]
							[STORPROC QCM/Question/[!q::Id!]/Reponse|r]
								<option value="[!r::Id!]" [IF [!Test!]=1][IF [!r::BonneReponse!]]selected[/IF][ELSE][IF [!sel!]==[!r::Id!]]selected[/IF][/IF]>[!r::Reponse!]</option>
							[/STORPROC]
						[/IF]
							</select>
						</div>
					[/CASE]
					[CASE mx_checkBox]
						[IF [!q::DictionnaireId!]]
							[STORPROC QCM/Dictionnaire/[!q::DictionnaireId!]/Entree|r]
						<div class="[IF [!q::Horizontal!]]QCMReponseLigneHorizontal[ELSE]QCMReponseLigneVertical[/IF]">
							<input type="checkbox" name="m-[!q::Id!][]" class="[!q::Icon!]" onchange="checkPage()" value="[!r::Id!]" 
							[IF [!Test!]=1][IF [!r::BonneReponse!]]checked[/IF][ELSE][IF [!qcm!]][STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]&ReponseId=[!r::Id!]|rs|0|1]checked[/STORPROC][/IF][/IF]
							/>[IF [!r::Image!]]<div class="QCMImageReponse"><img src="/[!r::Image!]"/></div>[/IF][!r::Reponse!]
						</div>
							[/STORPROC]
						[ELSE]
							[STORPROC QCM/Question/[!q::Id!]/Reponse|r]
						<div class="[IF [!q::Horizontal!]]QCMReponseLigneHorizontal[ELSE]QCMReponseLigneVertical[/IF]">
							<input type="checkbox" name="m-[!q::Id!][]" class="[!q::Icon!]" onchange="checkPage()" value="[!r::Id!]" 
							[IF [!Test!]=1][IF [!r::BonneReponse!]]checked[/IF][ELSE][IF [!qcm!]][STORPROC QCM/Participation/[!P::Id!]/Resultat/QuestionId=[!q::Id!]&ReponseId=[!r::Id!]|rs|0|1]checked[/STORPROC][/IF][/IF]
							/>[IF [!r::Image!]]<div class="QCMImageReponse"><img src="/[!r::Image!]"/></div>[/IF][!r::Reponse!]
						</div>
							[/STORPROC]
						[/IF]
					[/CASE]
					[CASE mx_formHeading]
					[/CASE]
				[/SWITCH]
				</div>
 			</div>
		[/STORPROC]
		</div>
	[/STORPROC]
	</div>
	<div class="QCMBoutons">
		<input type="submit" id="precedent" name="precedent" class="btn btn-primary" value="Précédent"/>
		<input type="submit" id="suivant" name="suivant" class="btn btn-primary" value="Suivant"/>
[IF [!Test!]=]
		<input type="submit" id="valider" name="valider" class="btn btn-success" value="Valider"/>
[/IF]
	</div>
[IF [!Test!]=]
</form>
[/IF]
[/IF]
[/STORPROC]


<script type="text/javascript">
var pageCount=[!pageCount!];
var pageNum=1;
var submit="";


[IF [!Test!]=]
$('formQCM').set('send', {
	onSuccess: function (html) {},
	onFailure: function(xhr){}
}).addEvent('submit', function(e){
	e.stop();
	new Request({
		method: this.method,
		url: this.action,
		onSuccess: function(responseText, responseXML) {
			if(responseText.indexOf('valide') >= 0) {
				alert('Le questionnaire à été validé');
				window.location.href = '/';
			} 
		}
	}).send(this.toQueryString()+"&submit="+submit);
});
[/IF]
$("suivant").addEvent('click', function(e){
	[IF [!Test!]=]submit=this.name;[/IF]
	setPage(pageNum+1);
});
$("precedent").addEvent('click', function(e){
	[IF [!Test!]=]submit=this.name;[/IF]
	setPage(pageNum-1);
});
[IF [!Test!]=]
$("valider").addEvent('click', function(e){
	submit=this.name;
});
[/IF]

var sldr = $$("input.QCMSlider");
sldr.addEvent('change', function(e){
	var inp = this.getNext('input.QCMSliderVal');
	inp.value = this.value+'/'+this.max;
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
	$$(".QCMPage").setStyle("display","none");
	$("page"+num).setStyle("display","block");
	pageNum=num;
	checkPage(pageNum);
}

function checkPage() {
[IF [!Test!]=1]
	$("precedent").disabled = pageNum==1;
	$("suivant").disabled = pageNum>=pageCount;
	return;
[ELSE]
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
[/IF]
}

setPage(1);

</script>
[/IF]
