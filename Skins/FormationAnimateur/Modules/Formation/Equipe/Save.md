[INFO [!Query!]|I]

//Récupération de la session
[STORPROC [!I::LastDirect!]|Sess|0|1][/STORPROC]
[IF [!I::TypeSearch!]=Child]
    //Nouvelle table
    [OBJ Formation|Equipe|E]
    [!E::Numero:=[!numtable!]!]
    [METHOD E|addParent][PARAM][!I::LastDirect!][/PARAM][/METHOD]
    [METHOD E|Save][/METHOD]
[ELSE]
    [STORPROC [!Query!]|E|0|1][/STORPROC]
[/IF]

//enregistrement des réponses
[STORPROC Formation/Session/[!Sess::Id!]/Donnee|D]
    [STORPROC Formation/TypeQuestion/Donnee/[!D::Id!]|TQ|0|1][/STORPROC]
    [STORPROC Formation/Equipe/[!E::Id!]/Reponse/TypeQuestion.TypeQuestionId([!TQ::Id!])|R]
        [!temp:=[!donn-[!D::Numero!]!]!]
        [!R::Valeur:=[!Utils::jsonEncode([!temp!])!]!]
        [METHOD R|Save][/METHOD]
        [NORESULT]
            [OBJ Formation|Reponse|R]
            //si existe pas alors on créé la reponse
            [!temp:=[!donn-[!D::Numero!]!]!]
            [!R::Valeur:=[!Utils::jsonEncode([!temp!])!]!]

            [!R::addParent([!E!])!]
            [!R::addParent([!TQ!])!]
            [METHOD R|Save][/METHOD]
        [/NORESULT]
    [/STORPROC]
[/STORPROC]
