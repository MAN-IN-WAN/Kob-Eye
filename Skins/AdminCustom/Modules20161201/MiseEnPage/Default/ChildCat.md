<div class="childArt">
                <h3>Articles dans cette catégorie</h3>
[STORPROC [!Query!]/Article|Arts]
        
                [LIMIT 0|10000]
                        <p><a href="/MiseEnPage/Article/[!Arts::Id!]" class="artTitle">[!Arts::Titre!]</a></p>
                [/LIMIT]
                
                [NORESULT]
                        <p class="notFound"><span class="glyphicon glyphicon-ban-circle"></span> Aucun article n'est disponible dans cette catégorie.</p>
                [/NORESULT]
[/STORPROC]
        <a href="[!I::LastId!]/AjouterArticle" class="addButton" title="Ajouter un article">Ajouter</a>
</div>  