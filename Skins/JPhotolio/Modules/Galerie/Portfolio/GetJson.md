[STORPROC [!Query!]|P|0|1]
{
"status":1,
"content":"[JSON]<div class="item-detail">
	<div class="item-gallery flexslider">
		<div class="item-download">
			<a data-alt="Download" href="/Galerie/Portfolio/[!P::Url!]/Download.zip">
				<div class="icon-download-alt">&nbsp;</div>
			</a>
		</div>
		<ul class="slides">[/JSON]
			[STORPROC Galerie/Portfolio/[!P::Id!]/Image|Im][JSON]
			<li>
				<a target="_blank" data-alt="Cliquez pour zoomer" class="item-gallery-image" href="#" data-title="[!Im::Titre!]" data="/[!Im::Fichier!]" data-tourl="false">
					<img title="[!Im::Titre!]" alt="[!Im::Titre!]" src="/[!Im::Fichier!].mini.680x680.jpg" />                
				</a>
			</li>[/JSON]
			[/STORPROC][JSON]
		</ul>
	</div>
	<div class="item-description">
		<div class="item-desc-wrapper">
			<h2>[!P::Titre!]</h2>
			<span></span>
			<div class="wrapper-text">
				<p>
					[!P::Description!]
				</p>
				<div class="hideme"></div>
			</div>
		</div>
		<div class="item-navigator">
			<a href="" data-tourl="false">
				<div class="item-prev">
					<div class="icon-chevron-left item-icon-prev">&nbsp;</div>
				</div>
			</a>
			<a href="" data-tourl="false">
				<div class="item-next">
					<div class="icon-chevron-right item-icon-next">&nbsp;</div>
				</div>
			</a>
		</div>
	</div>
</div>
[/JSON]",
"title":"[JSON][!P::Nom!][/JSON]",
"type":"gallery",
"love":"[!P::LikeCompteur!]",
"voted":false
}
[/STORPROC]
