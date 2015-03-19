<div id="Footer">
<div style=
    <div class="siteWidth">
		<div class="navbar navbar-inverse">
		    <div class="navbar-inner" style="background:transparent;color:#999999">
		        <button data-target=".ke-modules-menu" data-toggle="collapse" class="btn btn-navbar" type="button">
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		        </button>
		        <div id="img_menu" class="pull-left img_menu"></div>
		        <div class="ke-modules-menu nav-collapse collapse">
		            <ul class="nav">
		                [STORPROC [!Systeme::Menus!]/Affiche=1&&MenuBas=1|M1]
		                    <li>
		                        [STORPROC Systeme/Menu/[!M1::Id!]/Menu/Affiche=1|M2]                    
		                            <a class="dropdown" data-toggle="dropdown" href="#">[!M1::Titre!]</a>
		                            <ul class="dropdown-menu">
		                            [LIMIT 0|100]
		                                <li><a href="/[!M1::Url!]/[!M2::Url!]">[!M2::Titre!]</a></li>
		                            [/LIMIT]
		                            </ul>
		                            [NORESULT]
		                                <a href="/[!M1::Url!]">[!M1::Titre!]</a>
		                            [/NORESULT]
		                        [/STORPROC]
		                    </li>
		                [/STORPROC]
		            </ul>
		        </div>
		    </div>
		</div>
    </div>
</div>