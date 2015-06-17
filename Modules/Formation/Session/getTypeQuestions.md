[OBJ Formation|TypeQuestion|O]
{
    "success": true,
    "data": [
        [STORPROC [!CurrentSession::getTypeQuestions!]|S]
            [IF [!Pos!]>1],[/IF]{
                "id": [!S::Id!]
                [STORPROC [!O::getElementsByAttribute(mobile,1,1)!]|E]
                ,"[!E::name!]": "[JSON][!S::[!E::name!]!][/JSON]"
                [/STORPROC]
            }
        [/STORPROC]
    ]
}
