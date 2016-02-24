<div class="container">
	<div class="row-fluid">
		<div class="span4 well">
			<a href="http://ansm.sante.fr/" target="_blank">
				<img class="img-responsive" src="/Skins/Ecluse/img/logos/ansm.jpg">
			</a>
		</div>
		<div class="span4 well">
			<a href="http://www.ars.paca.sante.fr/" target="_blank">
				<img class="img-responsive" src="/Skins/Ecluse/img/logos/ars-paca.jpg">
			</a>
		</div>
		<div class="span4 well">
			<a href="http://www.ordre.pharmacien.fr/" target="_blank">
				<img class="img-responsive" src="/Skins/Ecluse/img/logos/ordre-pharmacien.png">
			</a>
		</div>
	</div>
</div>

<footer id="footer" class="omega clearfix">
	<section class="footer">
		<div class="overlay-kb"></div>
		<div class="container">
			<div class="row-fluid">
				<div style="clear:both"></div>
				<div id="lofadvafooterfooter" class="lofadvafooter">
					<div id="lofadva-pos-1" class="lof-position" style="width:100%">
						<div class="lof-position-wrap">
							<div class="lofadva-block-1 lof-block" style="width:100%; float:left;">
								//[MODULE Systeme/Footer/Newsletter]
							</div>
							<div style="clear:both;"></div>
						</div>
					</div>
				</div>
				<div id="lofadva-pos-2" class="row-fluid">
					<div class="lofadva-block-1 lof-block span3">
						[MODULE Systeme/Footer/InfosConnexion]
					</div>
					<div class="lofadva-block-2 lof-block span3">
						<h2><a href="/[!Sys::getMenu(Boutique/Marque)!]">Marques</a></h2>
						<ul>
							[STORPROC Boutique/Marque|M|0|10]
							<li class="item"><a href="/[!Sys::getMenu(Boutique/Marque)!]/[!M::Url!]">[!M::Nom!]</a></li>
							[/STORPROC]
						</ul>
					</div>
					<div class="lofadva-block-3 lof-block span3">
						[MODULE Systeme/Footer/BottomMenu?Num=0]
					</div>
					<div class="lofadva-block-4 lof-block span2">
						[MODULE Systeme/Footer/BottomMenu?Num=1]
					</div>
					<div class="lofadva-block-5 lof-block span1">
						[MODULE Systeme/Footer/BottomMenu?Num=2]
					</div>
					<div style="clear:both;"></div>
				</div>
			</div>
		</div>
	</section>
	<section id="footer-bottom">
		<div class="container">
			<div class="row-fluid">
				<div class="span6">
					<div class="copyright">
						[!Systeme::User::Commentaire!]
					</div>
				</div>
				<div class="span3">
					<div class="footnav">
						<a href="http://driveo.m-w.solutions">DRIVEO M-W SOLUTIONS</a>
					</div>
				</div>
				<div class="span3">
					<div class="footnav">
						<div class="customhtml block " id="leo-customhtml-footnav">
							<div class="block_content">
								<p><img src="/Skins/Paranature/Css/modules/leocustomfootnav/images/icon-social.png" alt="" />
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

</footer>