
//[MODULE Systeme/Utils/List?Chemin=[!Query!]&ACols=[!Files!]]


[OBJ AbtelTools|Action|A]

//RECHERCHE
[!fullMails:=[!A::getMailsDiag([!search!],[!domainSearch!])!]!]

[!allMails:=[!fullMails::mails!]!]
[!allDomains:=[!fullMails::domains!]!]

//PAGINATION
[IF [!Page!]=][!Page:=1!][/IF]
[COUNT [!allMails!]|Nb]
[!NbParPage:=30!]
[IF [!Limit!]>0]
        [!NbParPage:=[!Limit!]!]
[/IF]
[!NbNumParPage:=3!]
[!NbPage:=[!Math::Floor([!Nb:/[!NbParPage!]!])!]!]
[IF [!NbPage!]!=[!Nb:/[!NbParPage!]!]][!NbPage+=1!][/IF]


//ACTIONS
[SWITCH [!action!]|=]
        [CASE edit]
                [STORPROC [!I::Module!]/[!I::TypeChild!]/[!id!]|P|0|1]
                        [!P::[!prop!]:=[!value!]!]
                        [IF [!P::Save()!]=]
                                <div class="alert alert-success">L'élément [!P::getDescription()!] [!P::getFirstSearchOrder()!] a bien été mis à jour.</div>
                        [ELSE]
                                <div class="alert alert-danger">Un problème est survenu lors de la mise à jour de l'élément [!P::getDescription()!] [!P::getFirstSearchOrder()!].
                                        <ul>
                                                [STORPROC [!P::Error!]|E]
                                                        <li>[!E::Message!]</li>
                                                [/STORPROC]
                                        </ul>
                                </div>
                        [/IF]
                [/STORPROC]
        [/CASE]
[/SWITCH]

[IF [!Mini!]=]
<div class="row well">
        <form method="GET">
                <div class="col-md-4">
                        <div class="btn-toolbar" role="toolbar">
                                <div class="input-group">
                                        <input name="search" type="text" class="form-control" placeholder="Recherche..." value="[!search!]">
                                        <span class="input-group-btn">
                                                <input class="btn btn-primary" type="submit" value="Recherche" />
                                        </span>
                                </div><!-- /input-group -->
                        </div>
                        <input type="hidden" name="Page" value="[!Page!]">
                </div>
                <div class="col-md-4">
                        <div class="btn-toolbar" role="toolbar">
                                <div class="input-group">
                                        <select name="domainSearch" class="form-control" onchange="$(this).closest('form').submit();">
                                                <option value="" >Domaine...</option>
                                                [STORPROC [!allDomains!]|Dom]
                                                        <option value="[!Dom!]" [IF [!domainSearch!]=[!Dom!]]selected [!domOk:=1!][/IF]>[!Dom!]</option>
                                                [/STORPROC]
                                        </select>
                                </div><!-- /input-group -->
                        </div>
                        <input type="hidden" name="Page" value="[!Page!]">
                
                </div>
        </form>        
        <div class="col-md-4">
                <div class="btn-toolbar pull-right" role="toolbar">
                        <div class="btn-group" role="group">
                                <button class="btn btn-default" disabled="disabled">Page 1 sur [!NbPage!] </button>
                                [IF [!Page!]>1]
                                        <a href="/[!Lien!]?search=[!search!]" class="btn btn-default"><span>&laquo;</span></a>
                                        <a href="[IF [!Page!]=2]/[!Lien!]?search=[!search!][ELSE]?Page=[!Page:-1!]&search=[!search!][/IF]" class="btn btn-default">&lsaquo;</a>
                                        [IF [!Page!]>[!Math::Round([!NbNumParPage:/2!])!]]
                                                <a href="/[!Lien!]?search=[!search!]" class="btn btn-default"><span>1</span></a>
                                                <a href="#" class="btn btn-default" disabled="disabled"><span>...</span></a>
                                        [/IF]
                                [/IF]
                                [!start:=1!]
                                [IF [!Page!]>[!start:+[!NbNumParPage:/[!NbParPage!]!]!]][!start:=[!Math::Round([!Page:-[!NbNumParPage:/2!]!])!]!][/IF]
                                [STORPROC [!NbPage:+1!]|P|[!start!]|[!NbNumParPage!]]
                                        <a href="[IF [!P!]!=1]?Page=[!P!]&search=[!search!][ELSE]/[!Lien!]?search=[!search!][/IF]" class="btn btn-default [IF [!P!]=[!Page!]]active[/IF]">[!P!]</a>
                                [/STORPROC]
                                [IF [!Page!]<[!NbPage!]]
                                        [IF [!Page:+[!NbNumParPage:/2!]!]<[!NbPage!]]
                                                <a href="#" class="btn btn-default"><span>...</span></a>
                                                <a href="?Page=[!NbPage!]&search=[!search!]" class="btn btn-default">[!NbPage!]</a>
                                        [/IF]
                                        <a href="?Page=[!Page:+1!]&search=[!search!]" class="btn btn-default"><span>&rsaquo;</span></a>
                                        <a href="?Page=[!NbPage!]&search=[!search!]" class="btn btn-default">&raquo;</a>
                                [/IF]
                        </div>
                </div>
        </div>
</div>
[/IF]
<div id='mailResume' class="row">
        <table class="col-md-6">
                <caption>Résumé :</caption>
                <tr><td>Nombre de mails correspondants aux critères: </td><td><strong>[!Nb!]</strong></td></tr>
                <tr><td>Dont Protégés: </td><td><strong>[!fullMails::protected!]</strong></td></tr>
                <tr></tr>
                <tr><td colspan="2"><h3>COS: </h3></td></tr>
                [STORPROC [!fullMails::coses!]|Cos]
                        <tr><td><b>[!Key!]</b> [IF [!Cos::note!]]/ [!Cos::note!][/IF] : </td><td><strong>[!Cos::count!]</strong></td></tr>
                [/STORPROC]
        </table>
        <div id="searchCrits" class="col-md-6">
                <p id="searchCaption">Recherche :</p>
                [IF [!domainSearch!]]
                        <p>Domaine : <strong>[!domainSearch!]</strong></p>
                        [!crit:=1!]
                [/IF]
                [IF [!search!]]
                        <p>Chaîne(s) :
                                <ul>
                                        [STORPROC [!Utils::Explode(;,[!search!])!]|sea]
                                        <li>[!sea!]</li>
                                        [/STORPROC]
                                </ul>        
                        </p>
                        [!crit:=1!]
                [/IF]
                [IF [!crit!]!=1]<p>Aucun critère de recherche particulier</p>[/IF]
        </div>
</div>

<div class="table-responsive">
        <table class="table table-striped">
                <thead>
                        <tr>
                                <th>Adresse</th>
                                <th>Domaine</th>
                                <th>Status</th>
                                <th>COS</th>
                                <th>Quota</th>
                                <th>Protégé</th>
                        </tr>
                </thead>
                <tbody>
                [STORPROC [!allMails!]|C|[!NbParPage:*[!Page:-1!]!]|[!NbParPage!]]
                        <tr id="elem_[!C::zimbra_id!]">    
                                <td class="mailAdresse">
                                        [!Key!]
                                </td>
                                <td class="mailDomaine">
                                        [!C::zimbra_domaine!]
                                </td>
                                <td class="mailStatus">
                                        [!C::zimbra_status!]
                                </td>
                                <td class="mailCos">
                                        [!C::zimbra_cos!]<br>
                                        [!C::zimbra_cosnotes!]
                                </td>
                                <td class="mailQuota">
                                        [!C::zimbra_quota!] Mb
                                </td>
                                <td class="mailMib">
                                        [IF [!C::mib_domaine!]]
                                                <span class="gotCli">Y</span>
                                        [ELSE]
                                                <span class="noCli">N</span>
                                        [/IF]
                                </td>
                        </tr>
                [/STORPROC]
                </tbody>
        </table>
</div>


[IF [!Mini!]=]
<div class="row well">
        <div class="col-md-4">
        </div>
        <div class="col-md-8">
                <div class="btn-toolbar pull-right" role="toolbar">
                        <div class="btn-group" role="group">
                                <button class="btn btn-default" disabled="disabled">Page 1 sur [!NbPage!] </button>
                                [IF [!Page!]>1]
                                        <a href="/[!Lien!]?search=[!search!]" class="btn btn-default"><span>&laquo;</span></a>
                                        <a href="[IF [!Page!]=2]/[!Lien!]?search=[!search!][ELSE]?Page=[!Page:-1!]&search=[!search!][/IF]" class="btn btn-default">&lsaquo;</a>
                                        [IF [!Page!]>[!Math::Round([!NbNumParPage:/2!])!]]
                                                <a href="/[!Lien!]?search=[!search!]" class="btn btn-default"><span>1</span></a>
                                                <a href="#" class="btn btn-default" disabled="disabled"><span>...</span></a>
                                        [/IF]
                                [/IF]
                                [!start:=1!]
                                [IF [!Page!]>[!start:+[!NbNumParPage:/[!NbParPage!]!]!]][!start:=[!Math::Round([!Page:-[!NbNumParPage:/2!]!])!]!][/IF]
                                [STORPROC [!NbPage:+1!]|P|[!start!]|[!NbNumParPage!]]
                                        <a href="[IF [!P!]!=1]?Page=[!P!]&search=[!search!][ELSE]/[!Lien!]?search=[!search!][/IF]" class="btn btn-default [IF [!P!]=[!Page!]]active[/IF]">[!P!]</a>
                                [/STORPROC]
                                [IF [!Page!]<[!NbPage!]]
                                        [IF [!Page:+[!NbNumParPage:/2!]!]<[!NbPage!]]
                                                <a href="#" class="btn btn-default"><span>...</span></a>
                                                <a href="?Page=[!NbPage!]&search=[!search!]" class="btn btn-default">[!NbPage!]</a>
                                        [/IF]
                                        <a href="?Page=[!Page:+1!]&search=[!search!]" class="btn btn-default"><span>&rsaquo;</span></a>
                                        <a href="?Page=[!NbPage!]&search=[!search!]" class="btn btn-default">&raquo;</a>
                                [/IF]
                        </div>
                </div>
        </div>
</div>
[/IF]


