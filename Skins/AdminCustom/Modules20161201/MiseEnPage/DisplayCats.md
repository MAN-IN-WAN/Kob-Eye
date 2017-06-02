<!--<div class="displayItem row">   
	[STORPROC MiseEnPage/Categorie|Cat]
	        <ul id="catListMenu" class="col-md-12">
	                [LIMIT 0|1000]
	                <li>
   	                        [!onest:=MiseEnPage/Categorie/[!Cat::Id!]!]
	                        <a href="/MiseEnPage/Categorie/[!Cat::Id!]" title="[!Cat::Nom!]" [IF [!onest!]=[!Query!]]class="selected"[/IF] >[!Cat::Nom!]</a>
	                </li>
	                [/LIMIT]
	        </ul>
	        [NORESULT]
	                <p class="col-md-12 notFound"><span class="glyphicon glyphicon-ban-circle">Aucun objet de ce type n'a été trouvé.</p>
	        [/NORESULT]
	[/STORPROC]
</div>-->

<div  class="itemChilds row">
	[MODULE MiseEnPage/Detail/Categorie]
</div>
		