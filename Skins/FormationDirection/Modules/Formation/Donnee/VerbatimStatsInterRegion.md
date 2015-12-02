[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H|0|1]
[STORPROC [!H::Module!]/[!H::DataSource!]/[!H::Value!]|P|0|1][/STORPROC]
[/STORPROC]
[STORPROC [!Query!]|CD|0|1][/STORPROC]
[STORPROC [!CD::getParents(TypeQuestion)!]|TQ|0|1][/STORPROC]
[STORPROC [!TQ::getParents(Question)!]|Q|0|1][/STORPROC]

[!FILTER_REGION:=!]
[STORPROC Formation/InterRegion/[!CurrentRegion!]/Region|Reg]
[IF [!Pos!]>1][!FILTER_REGION.=+!][/IF]
[!FILTER_REGION.=Id=[!Reg::Id!]!]
[/STORPROC]

[COUNT Formation/Projet/[!P::Id!]/Session/Region.Region([!FILTER_REGION!])/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]&Valeur!=|NbR]
[STORPROC Formation/Projet/[!P::Id!]/Session/Region.Region([!FILTER_REGION!])/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]&Valeur!=|R|[!Utils::random([!NbR:-10!])!]|12]
    [IF [!Utils::parseInt([!R::Valeur!])!]!=[!R::Valeur!]]
    <div class="well">
        [!R::Valeur!]
    </div>
    [/IF]
[/STORPROC]