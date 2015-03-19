<div class="gradient"></div>
[IF [!P::Image!]]
	<img src="[!Domaine!]/[!P::Image!].limit.410x580.jpg?[!TMS::Now!]" original-img="[!Domaine!]/[!P::Image!]" alt="" />
	<a href=[!Domaine!]/[!P::Image!] style="display:none"></a>
	//<img src="[!Domaine!]/[!P::Image!]"  alt="" style="display:none" id="image" />
[ELSE]
	<div class="page-padding">
		<p>[!P::Contenu!]</p>
	</div>
[/IF]