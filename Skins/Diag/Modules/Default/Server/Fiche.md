<h1>Detail [!O::ObjectType!] [!O::Id!]</h1>
<div id="detailObjet detail[!O::ObjectType!]">
[STORPROC [!O::getProperties()!]|P]
        [IF [!P::category!]!=LDAP]
                //TODO : Gérer category
                <span class="propName">[!P::Nom!] </span>: [IF [!P::Valeur!]][!P::Valeur!][ELSE]Non renseigné[/IF]<br/>
        [/IF]
[/STORPROC]

</div>

[STORPROC [!O::getChildTypes()!]|Child]
        <h2>[!Child::Titre!] associés</h2>
        [STORPROC Parc/[!O::ObjectType!]/[!O::Id!]/[!Child::Titre!]|C]
                <div class="detailRelative">
                        [STORPROC [!C::getProperties()!]|Pc]
                                [IF [!Pc::category!]!=LDAP]
                                        //TODO : Gérer category
                                        <span class="propName">[!Pc::Nom!] </span>: [IF [!Pc::Valeur!]][!Pc::Valeur!][ELSE]Non renseigné[/IF]<br/>
                                [/IF]
                        [/STORPROC]    
                </div>
                <br/>
                [NORESULT]
                        <p class="emptyRelative">Aucun</p>
                [/NORESULT]
        [/STORPROC]
[/STORPROC]

[STORPROC [!O::getParentTypes()!]|Parent]
        <h2>[!Parent::Titre!] associés</h2>
        [STORPROC Parc/[!Parent::Titre!]/[!O::ObjectType!]/[!O::Id!]|P]
                <div class="detailRelative">
                        [STORPROC [!P::getProperties()!]|Pp]
                                [IF [!Pp::category!]!=LDAP]
                                        //TODO : Gérer category
                                        <span class="propName">[!Pp::Nom!] </span>: [IF [!Pp::Valeur!]][!Pp::Valeur!][ELSE]Non renseigné[/IF]<br/>
                                [/IF]
                        [/STORPROC]    
                </div>
                <br/>
                [NORESULT]
                        <p class="emptyRelative">Aucun</p>
                [/NORESULT]
        [/STORPROC]
[/STORPROC]
