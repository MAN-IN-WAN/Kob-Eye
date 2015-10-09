[INFO [!Query!]|I]

//Récupération de la session
[STORPROC [!I::LastDirect!]|Sess|0|1][/STORPROC]

[IF [!I::TypeSearch!]=Child]
    //Nouvelle table
    [OBJ Formation|Equipe|E]
    [!E::Numero:=[!numtable!]!]
    [!E::addParent([!Sess!])!]
    [METHOD E|Save][/METHOD]
[ELSE]
    [STORPROC [!Query!]|E|0|1][/STORPROC]
[/IF]

//enregistrement des réponses
[STORPROC Formation/Session/[!Sess::Id!]/Donnee|D]
    [STORPROC Formation/TypeQuestion/Donnee/[!D::Id!]|TQ|0|1][/STORPROC]
    [STORPROC Formation/Equipe/[!I::LastId!]/Reponse/TypeQuestion.TypeQuestionId([!TQ::Id!])|R]
        [!R::Valeur:=[!donn-[!D::Numero!]!]!]
        [METHOD R|Save][/METHOD]
        [NORESULT]
            //si existe pas alors on créé la reponse
            [OBJ Formation|Reponse|R]
            [!R::Valeur:=[!donn-[!D::Numero!]!]!]
            [!R::addParent([!E!])!]
            [!R::addParent([!TQ!])!]
            [METHOD R|Save][/METHOD]
        [/NORESULT]
    [/STORPROC]
[/STORPROC]
