// IL FAUDRA FAIRE UNE VERSION BOOTSTRAP AVEC UN CAROUSEL ?

[STORPROC [!Query!]|Prod|0|1][/STORPROC]

// CALCUL des hauteurs et largeur des blocs
[!FICH_INTERVALLE:=15!]
[!FICH_LARGEURUNEINFO:=55!]
[!FICH_NBINFOS:=4!]

[!FICH_LgUneInfo:=[!FICH_LARGEURUNEINFO!]!]
[!FICH_LgUneInfo+=[!FICH_INTERVALLE!]!]
[!FICH_HgUneInfo:=45!]
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

[!Promo:=[!Prod::GetPromo!]!]

[IF [!Promo!]!=0]<div class="PromoProduit"></div>[/IF]
<div class="PhotoProduit">
	<a href="[!Domaine!]/[!Prod::Image!]"  class="zoombox" ><img src="/[!Prod::Image!].limit.856x490.jpg" alt="[!Utils::noHtml([!Prod::Description!])!]" class="img-thumbnail image-responsive" /></a>
</div>



<script type="text/javascript">
	$(document).ready(function () {
		$('a.zoombox').zoombox(
			{
				theme : 'darkprettyphoto',
				animation: true
			}
		);


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
	});

</script>