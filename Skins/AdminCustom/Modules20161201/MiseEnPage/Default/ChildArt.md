<div class="childArt">
        [STORPROC [!Query!]/Contenu|Cons]
                <h3>Les contenus de cet article</h3>
                [LIMIT 0|10000]
                        <div class="tableWrap">
                                [STORPROC MiseEnPage/Contenu/[!Cons::Id!]/Colonne|Cols|||Ordre|ASC]
                                        <table>
                                                <caption><span class="conTitre">[!Cons::Titre!]</span> <a href="/MiseEnPage/Contenu/[!Cons::Id!]/Supprimer" class="delButton" title="Supprimer le contenu">Supprimer</a><a href="/MiseEnPage/Contenu/[!Cons::Id!]/Modifier" class="modButton" title="Modifier le contenu">Modifier</a></caption>
                                                [LIMIT 0|10000]
                                                        <tr>
                                                                <td class="colTitre">[!Cols::Titre!]</td>
                                                                <td class="colRatio">Largeur : [!Cols::Ratio!]% </td>
                                                                <td class="colMod"><a href="/MiseEnPage/Colonne/[!Cols::Id!]/Modifier" title="Modifier la colonne">Modifier</a></td>
                                                                <td class="colDel"><a href="/MiseEnPage/Colonne/[!Cols::Id!]/Supprimer"title="Supprimer la colonne">Supprimer</a></td>
                                                        </tr>
                                                [/LIMIT]
                                                <tr>
                                                        <td colspan="4" class="colAdd"><a href="/MiseEnPage/Contenu/[!Cons::Id!]/AjouterColonne" class="colonneArt">Ajouter une colonne</a></td>
                                                </tr>
                                        </table>
                                        [NORESULT]
                                         <table>
                                                <caption><span class="conTitre">[!Cons::Titre!]</span> <a href="/MiseEnPage/Contenu/[!Cons::Id!]/Supprimer" class="delButton" title="Supprimer le contenu">Supprimer</a><a href="/MiseEnPage/Contenu/[!Cons::Id!]/Modifier" class="modButton" title="Modifier le contenu">Modifier</a></caption>
                                                <tr>
                                                        <td colspan="4" class="colAdd"><a href="/MiseEnPage/Contenu/[!Cons::Id!]/AjouterColonne" class="colonneArt">Ajouter une colonne</a></td>
                                                </tr>
                                        </table>
                                        [/NORESULT]
                                [/STORPROC]
                        </div>
                [/LIMIT]
                [NORESULT]
                        <p>Aucun Contenu n'est disponible dans cet Article.</p>
                [/NORESULT]
        [/STORPROC]
        <a href="[!I::LastId!]/AjouterContenu" class="addButton" title="Ajouter du contenu">Ajouter</a>
</div>