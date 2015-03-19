[!NbImg:=0!]
<div class="GaleriesReference" style="width:100%">
	<span class="blocProduitPagesTitre " style="text-decoration:underline">Cliquez sur les photos pour zoomer<br/></span>
	[STORPROC [!Query!]/Photo|ImgMini|0|10]
		[!NbImg+=1!]

		<div class="BlocProduitImageMini" style="float:left;">
			<a href="/[!ImgMini::Image!].limit.1000x1000.jpg" title="[!ImgMini::Nom!]" class="mb" id="mb_Diapo[!Pos!]" rel="width:400,height:300">
				<img src="/[!ImgMini::Image!].mini.60x60.jpg" alt="[!ImgMini::Nom!]" style="margin:10px;" />
			</a>
			<div class="multiBoxDesc mb_Diapo[!Pos!]" style="display: none">[!ImgMini::Nom!]</div>
		</div>
		[IF [!NbImg!]=5]<br/>[!NbImg:=0!][/IF]
	[/STORPROC]
</div>
