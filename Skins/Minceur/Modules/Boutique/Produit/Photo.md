// CALCUL des hauteurs et largeur des blocs
[!FICH_INTERVALLE:=15!]
[!FICH_LARGEURUNEINFO:=63!]
[!FICH_NBINFOS:=4!]

[!FICH_LgUneInfo:=[!FICH_LARGEURUNEINFO!]!]
[!FICH_LgUneInfo+=[!FICH_INTERVALLE!]!]
[!FICH_HgUneInfo:=35!]
[!FICH_HgUneInfo+=0!]
	
[!FICH_LgConteneurVisible:=[!FICH_NBINFOS!]!]
[!FICH_LgConteneurVisible*=[!FICH_LgUneInfo!]!]
[!FICH_LgConteneurVisible-=[!FICH_INTERVALLE!]!]

[COUNT Boutique/Produit/[!Prod::Id!]/Donnee/Type=Image|NbImg]
[IF [!Prod::Image!]!=][!NbImg+=1!][/IF]

[!FICH_LIMITAFFICHAGE:=[!NbImg!]!]
[!FICH_LgConteneurTotal:=[!FICH_LIMITAFFICHAGE!]!]
[!FICH_LgConteneurTotal*=[!FICH_LgUneInfo!]!]
[!FICH_LgConteneurTotal-=[!FICH_INTERVALLE!]!]

[!Promo:=[!Prod::EstenPromo!]!]

<div class="BlocFichImage " style="overflow:hidden;display:block;position:relative;">
	[IF [!Promo!]>0]<div class="PromoProduit"></div>[/IF]
	<div id="FICH_imgEc span5 pull-left" >
		<a class="mb" href="/[IF [!Prod::Image!]!=][!Prod::Image!].limit.560x533.jpg[ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF]" title="[UTIL SANSCOTE][!Prod::Nom!][/UTIL]" >
			<img src="/[IF [!Prod::Image!]!=][!Prod::Image!].limit.400x1000.jpg[ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF]" alt="[!Prod::Nom!]" title="[!Prod::Nom!]"  />
		</a>
	</div>
</div>
[IF [!NbImg!]>1]
	<div class="thumbnail" style="margin-top:10px;">
		[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Image|Img|||Ordre|ASC]
		<a href="javascript:;" >
			<img src="[!Domaine!]/[!Img::Fichier!].mini.100x100.jpg" onclick="return apercu('[!Domaine!]/[!Img::Fichier!].mini.295x281.jpg','[IF [!Img::Valeur!]=][UTIL SANSCOTE][!Prod::Nom!][/UTIL][ELSE][UTIL SANSCOTE][!Img::Valeur!][/UTIL][/IF]','[!Domaine!]/[!Img::Fichier!].limit.560x533.jpg');" style="border:1px solid #747476"/>
		</a>
		[/STORPROC]
		<a href="javascript:;" >
			<img src="[!Domaine!]/[!Prod::Image!].mini.100x100.jpg" onclick="return apercu('[!Domaine!]/[!Prod::Image!].mini.295x281.jpg','[UTIL SANSCOTEESPACE][!Prod::Nom!][/UTIL]','[!Domaine!]/[!Prod::Image!].mini.560x533.jpg');" style="border:1px solid #747476"/>
		</a>
	</div>
[/IF]





// Surcouche JS
<script type="text/javascript">


	var FICH_marginMEA = 0;
	var FICH_indiceMEA = 0;
	var FICH_limitMEA =[IF [!NbImg!]<[!FICH_LIMITAFFICHAGE!]][!NbImg!][ELSE][!FICH_LIMITAFFICHAGE!][/IF];


	function FICH_deplacediv(lechoix,largeurinfo) {
		// fonction pour déplacer quand il y a plusieurs blocks affichés
		if (lechoix=='P' && FICH_indiceMEA>0) {
			FICH_marginMEA += largeurinfo;
			FICH_indiceMEA--;
		}
		if (lechoix=='S' && FICH_indiceMEA<FICH_limitMEA-[!FICH_NBINFOS!] ) {
			FICH_marginMEA -= largeurinfo;
			FICH_indiceMEA++;
		}

		$('FICH_ladivadeplacer').tween('margin-left', FICH_marginMEA+'px'); 
	
	}
	function FICH_afficheimage(limage) {
		$('FICH_imgEc').src=limage;

	}


	// Clic sur miniature
	function apercu(img,legende, zhref) {
		var lien = new Element('a', {
			'href': zhref,
			'title': legende,
			'class': 'mbFull'
		});
		var image = new Element('img', {
			'src': img,
			'alt': legende,
			'title': legende
		}).inject(lien);

		$('FICH_imgEc').empty();

		lien.inject($('FICH_imgEc'));

		var initMultiBox = new multiBox({
				mbClass: '.mbFull',
				container: $(document.body),
				descClassName: 'multiBoxDesc',
				useOverlay: true,
				maxSize: {w:800, h:600},
				addRollover: true
			});
		return false;
	}
	// Clic sur apercu
	function openModal(lien) {
		SqueezeBox.fromElement(lien);
		return false;
	}


</script>