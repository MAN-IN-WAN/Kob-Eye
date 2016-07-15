
[STORPROC [!Query!]|C]
<div class="titre-product gris-clair">
	<div class="container title-product nopadding-right nopadding-left">
		<div class="row">
			<div class="col-lg-10 col-xs-6">
				<h1 class="title_prod">[!C::Titre!]<span class="title">&nbsp;[!C::SousTitre!]</span></h1>
			</div>
                        <!--
			<div class="col-lg-2 col-xs-6">
				<div class="nav-product">
					<div class="nav-product-btn">
						<a class="left" href="/[!lelienP!]" title="[!NomProdP!]"  onmouseover='$("#Nom-P").css("display","block");' onmouseout='$("#Nom-P").css("display","none");' >
							<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-left.png" class="img-responsive" alt="Fone"/>
						</a>
					</div>
					<div class="nav-product-btn">
						<a class="right" href="/[!lelienS!]" title="[!NomProdS!]"  onmouseover='$("#Nom-S").css("display","block");' onmouseout='$("#Nom-S").css("display","none");' >
							<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-right.png" class="img-responsive" alt="Fone"/>
						</a>
					</div>
				</div>
				
			</div>
                        -->
		</div>
		<div class="row">
			<div class="col-lg-10 col-xs-10">
				[IF [!C::Annee!]>0]<div class="caract">[!C::Annee!]</div>[/IF]
			</div>
			<!--<div class="col-lg-2 col-xs-2" >
				<div class="Nom-Navigation hidden-xs" id="Nom-P"  style="display:none">[!ProdP::Nom!]</div>
				<div class="Nom-Navigation hidden-xs" id="Nom-S"  style="display:none" >[!ProdS::Nom!]</div>
			</div>-->

		</div>
	
	</div>
</div>
<div class="featured">
	<div class="container spare-parts nopadding-right nopadding-left">
            <div class="col-md-8 blanc-ombre" style="padding:0;">
                <img src="/[!C::ImageParts!].limit.900x2000.jpg" class="img-responsive" />
                <!-- Points -->
                    [STORPROC Products/SparePart/[!C::Id!]/Part|P]
                        <div class="spare-point" data-panel="panel-[!P::Id!]" id="point-[!P::Id!]" style="left:[!P::PosX!]%;top:[!P::PosY!]%;">[!Pos!]</div>
                    [/STORPROC]
                    [STORPROC Products/SparePart/[!C::Id!]/Part|P]
                        <div class="BulleInfo vert spare-panel [IF [!P::PosX!]>50] spare-inverse [/IF]" id="panel-[!P::Id!]" style="left:[!P::PosX!]%;top:[!P::PosY!]%;display:none;">
                            <span class="fleche vert"></span>
                            <div class="row-fluid">
                                    <div class="col-lg-12 col-xs-12">
                                            <h3 class="">[!P::Titre!]</h3>
                                    </div>
                                    <div class="col-lg-12 col-xs-12">
                                            <p>
                                                <img class="pull-left" src="/[!P::Image!].limit.150x200.jpg" style="margin:5px;"/>
                                                [!P::Description!]
                                            </p>
                                    </div>
                            </div>
                        </div>
                    [/STORPROC]
            </div>
            <div class="col-md-4 nopadding-right">
                <div class="blanc-ombre">[!C::Titre!]</div>
                <div class="blanc-ombre">
                    <ol>
                    [STORPROC Products/SparePart/[!C::Id!]/Part|P]
                        <li class="spare-list" data-point="point-[!P::Id!]" data-panel="panel-[!P::Id!]">[!P::Titre!]</li>
                    [/STORPROC]
                    </ol>
                </div>
            </div>
	</div>
	<div class="container spare-parts-message">
		<h2>__MESSAGE_SPARE_PARTS_TEL__</h2>
	</div>
</div>
[/STORPROC]


<script type="text/javascript">
    $(document).ready(function () {
        $('.spare-point').mouseover(function (event){
            //on désactive tous les panneaux
            $('.spare-panel').css('display','none');
            //affichage de la bulle info correspondante
            var panel = $('#'+$(this).attr('data-panel'));
            $(panel).css('display','block');
        });
        $('.spare-list').mouseover(function (event){
            //on désactive tous les panneaux
            $('.spare-panel').css('display','none');
            //affichage de la bulle info correspondante
            var panel = $('#'+$(this).attr('data-panel'));
            $(panel).css('display','block');
            //recuperatio du point
            var point = $('#'+$(this).attr('data-point'));
        });
    });
</script>