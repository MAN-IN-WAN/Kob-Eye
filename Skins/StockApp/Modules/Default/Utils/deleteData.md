[!All:=[!JsonP::getInput()!]!]
[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|Ob]
{
    "updated": [],
    "added": [],
    "removed": [
        [STORPROC [!All!]|Data]
            [IF [!Data::id!]>0]
                [!Ob::initFromId([!Data::id!])!]
                    {"id":"[!Ob::Id!]"},
                [METHOD Ob|Delete][/METHOD]
            [/IF]
        [/STORPROC]
    ],
    "success": true
}

